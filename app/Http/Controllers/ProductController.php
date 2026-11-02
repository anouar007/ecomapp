<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['images', 'productCategory']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Stock Status Filter
        if ($request->filled('stock_status')) {
            $ss = $request->stock_status;
            if ($ss === 'in_stock') {
                $query->where('stock', '>', 5);
            } elseif ($ss === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 5);
            } elseif ($ss === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($ss === 'alerts') {
                $query->where('stock', '<=', 5);
            }
        }

        // Image Quality Filter
        if ($request->filled('quality')) {
            $q = $request->quality;
            if ($q === 'needs_upgrade') {
                $query->where(function($sub) {
                    $sub->whereIn('image_quality', ['sd', 'low', 'placeholder', 'missing', 'unknown'])
                        ->orWhereNull('image_quality');
                });
            } elseif ($q === '4k') {
                $query->where('image_quality', '4k');
            } elseif ($q === 'fhd') {
                $query->where('image_quality', 'fhd');
            } elseif ($q === 'sd') {
                $query->where('image_quality', 'sd');
            } elseif ($q === 'missing') {
                $query->where(function($sub) {
                    $sub->whereIn('image_quality', ['placeholder', 'missing'])
                        ->orWhereNull('image');
                });
            }
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest('id'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'stock_asc' => $query->orderBy('stock', 'asc'),
            'stock_desc' => $query->orderBy('stock', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->latest('id'),
        };

        $products = $query->paginate(20)->withQueryString();
        
        $categories = \App\Models\Category::where('status', 'active')->orderBy('name')->get();

        $totalCount = Product::count();
        $fourKCount = Product::where('image_quality', '4k')->count();
        $qualityStats = [
            'total' => $totalCount,
            'four_k' => $fourKCount,
            'fhd' => Product::where('image_quality', 'fhd')->count(),
            'needs_upgrade' => Product::whereIn('image_quality', ['sd', 'low', 'placeholder', 'missing', 'unknown'])->orWhereNull('image_quality')->count(),
        ];

        $stats = [
            'total' => $totalCount,
            'active' => Product::where('status', 'active')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
            'low_stock' => Product::where('stock', '>', 0)->where('stock', '<=', 5)->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->count(),
            'total_stock' => (int) Product::sum('stock'),
            'inventory_value' => (float) (Product::sum(DB::raw('price * stock')) ?? 0),
            'four_k' => $fourKCount,
            'fhd' => $qualityStats['fhd'],
            'needs_upgrade' => $qualityStats['needs_upgrade'],
            'four_k_percentage' => $totalCount > 0 ? (int) round(($fourKCount / $totalCount) * 100) : 0,
        ];

        return view('products.index', compact('products', 'categories', 'qualityStats', 'stats'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = \App\Models\Category::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'images.*' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create($validated);

            // Handle multiple image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $index,
                        'is_primary' => $index === 0, // First image is primary
                    ]);
                }
            } else {
                // Automatically search & download official product image from brand website
                try {
                    app(\App\Services\OfficialProductImageService::class)->fetchForProduct($product);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not auto-fetch official image for product #{$product->id}: " . $e->getMessage());
                }
            }

            DB::commit();
            
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $product->load('images', 'productCategory');
        $categories = \App\Models\Category::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'images.*' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['exists:product_images,id'],
        ]);

        DB::beginTransaction();
        try {
            $product->update($validated);

            // Handle image removal
            if ($request->has('remove_images')) {
                $imagesToRemove = ProductImage::whereIn('id', $request->remove_images)->get();
                foreach ($imagesToRemove as $img) {
                    if ($product->image === $img->image_path) {
                        $product->image = null;
                    }
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
                if (empty($product->image) || !file_exists(storage_path('app/public/' . $product->image))) {
                    $nextPrimary = $product->images()->first();
                    if ($nextPrimary) {
                        $nextPrimary->update(['is_primary' => true]);
                        $product->image = $nextPrimary->image_path;
                        $product->save();
                    }
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $currentMaxOrder = $product->images()->max('sort_order') ?? -1;
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $currentMaxOrder + $index + 1,
                        'is_primary' => $product->images()->count() === 0 && $index === 0,
                    ]);
                }
            }

            DB::commit();
            
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Delete all product images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Delete old single image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            DB::commit();
            
            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Import products from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'], // 10MB max
        ]);

        try {
            $import = new \App\Imports\ProductsImport();
            $import->import($request->file('file'));

            $errors = $import->errors();
            $importedCount = $import->getImportedCount();

            if ($errors->isNotEmpty()) {
                $errorMessages = $errors->take(5)->map(function ($failure) {
                    return "Row {$failure->row()}: " . implode(', ', $failure->errors());
                })->implode('; ');

                return redirect()->route('products.index')
                    ->with('warning', "Imported {$importedCount} products with some errors: {$errorMessages}");
            }

            return redirect()->route('products.index')
                ->with('success', "Successfully imported {$importedCount} products.");

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = collect($failures)->take(5)->map(function ($failure) {
                return "Row {$failure->row()}: " . implode(', ', $failure->errors());
            })->implode('; ');

            return redirect()->route('products.index')
                ->with('error', "Import failed: {$errorMessages}");

        } catch (\Exception $e) {
            return redirect()->route('products.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel template for product import.
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ProductTemplateExport(),
            'products_import_template.xlsx'
        );
    }

    /**
     * Handle bulk actions on multiple products.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:delete,duplicate,increase_stock,decrease_stock,activate,deactivate,generate_descriptions',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'stock_amount' => 'nullable|integer|min:1',
        ]);

        $productIds = $request->product_ids;
        $action = $request->action;
        $count = count($productIds);

        try {
            switch ($action) {
                case 'delete':
                    Product::whereIn('id', $productIds)->delete();
                    return response()->json([
                        'success' => true,
                        'message' => "{$count} product(s) deleted successfully."
                    ]);

                case 'duplicate':
                    $duplicated = 0;
                    foreach (Product::whereIn('id', $productIds)->with('images')->get() as $product) {
                        $newProduct = $product->replicate();
                        $newProduct->name = $product->name . ' (Copy)';
                        $newProduct->sku = $product->sku . '-COPY-' . time() . rand(100, 999);
                        $newProduct->save();
                        
                        // Duplicate the product images
                        foreach ($product->images as $image) {
                            $newProduct->images()->create([
                                'image_path' => $image->image_path,
                                'is_primary' => $image->is_primary,
                                'sort_order' => $image->sort_order,
                            ]);
                        }
                        
                        $duplicated++;
                    }
                    return response()->json([
                        'success' => true,
                        'message' => "{$duplicated} product(s) duplicated successfully."
                    ]);

                case 'increase_stock':
                    $amount = $request->stock_amount ?? 10;
                    Product::whereIn('id', $productIds)->increment('stock', $amount);
                    return response()->json([
                        'success' => true,
                        'message' => "Stock increased by {$amount} for {$count} product(s)."
                    ]);

                case 'decrease_stock':
                    $amount = $request->stock_amount ?? 10;
                    Product::whereIn('id', $productIds)->decrement('stock', $amount);
                    // Ensure stock doesn't go negative
                    Product::whereIn('id', $productIds)->where('stock', '<', 0)->update(['stock' => 0]);
                    return response()->json([
                        'success' => true,
                        'message' => "Stock decreased by {$amount} for {$count} product(s)."
                    ]);

                case 'activate':
                    Product::whereIn('id', $productIds)->update(['status' => 'active']);
                    return response()->json([
                        'success' => true,
                        'message' => "{$count} product(s) activated successfully."
                    ]);

                case 'deactivate':
                    Product::whereIn('id', $productIds)->update(['status' => 'inactive']);
                    return response()->json([
                        'success' => true,
                        'message' => "{$count} product(s) deactivated successfully."
                    ]);

                case 'generate_descriptions':
                    $service = app(\App\Services\ProductDescriptionService::class);
                    $result = $service->bulkGenerateDescriptions($productIds, [
                        'overwrite_mode' => $request->overwrite_mode ?? 'empty_only',
                        'style' => $request->style ?? 'pro_ecommerce',
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => "Descriptions générées avec succès : {$result['updated']} produit(s) enrichi(s), {$result['skipped']} ignoré(s).",
                        'data' => $result,
                    ]);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action specified.'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search 4K high-resolution candidates for a product.
     */
    public function search4kImages(Request $request, Product $product)
    {
        $query = $request->input('q', $product->name);
        $limit = (int)$request->input('limit', 48);
        $limit = max(12, min(80, $limit));

        $service = app(\App\Services\OfficialProductImageService::class);
        $candidates = $service->find4kCandidates($query, $product->category_name, 800, $limit);

        return response()->json([
            'success' => true,
            'query' => $query,
            'count' => count($candidates),
            'candidates' => $candidates,
        ]);
    }

    /**
     * Apply selected 4K image(s) directly to a product (as primary or gallery).
     */
    public function apply4kImage(Request $request, Product $product)
    {
        $request->validate([
            'image_url' => ['nullable', 'url'],
            'image_urls' => ['nullable', 'array'],
            'image_urls.*' => ['url'],
            'as_primary' => ['nullable'],
        ]);

        $service = app(\App\Services\OfficialProductImageService::class);
        $asPrimaryExplicit = $request->has('as_primary') ? filter_var($request->input('as_primary'), FILTER_VALIDATE_BOOLEAN) : null;

        // Bulk import multiple 4K images
        if ($request->has('image_urls') && is_array($request->input('image_urls'))) {
            $urls = array_values(array_filter($request->input('image_urls')));
            $imported = [];
            $failed = 0;
            $firstAsPrimary = ($asPrimaryExplicit === true);

            foreach ($urls as $idx => $url) {
                $isPrimaryForThis = ($idx === 0 && $firstAsPrimary);
                $res = $service->apply4kImage($product, $url, $isPrimaryForThis);
                if ($res) {
                    $imported[] = $res;
                } else {
                    $failed++;
                }
            }

            $product->refresh();

            return response()->json([
                'success' => count($imported) > 0,
                'count' => count($imported),
                'failed' => $failed,
                'images' => $imported,
                'message' => count($imported) . ' image(s) 4K importée(s) avec succès !',
                'badge_html' => $product->image_quality_badge,
            ]);
        }

        // Single image import
        $imageUrl = $request->input('image_url');
        if (!$imageUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune image spécifiée.',
            ], 422);
        }

        // Default to true for single image if not specified (backward compatibility)
        $isPrimary = ($asPrimaryExplicit !== null) ? $asPrimaryExplicit : true;

        $res = $service->apply4kImage($product, $imageUrl, $isPrimary);

        if (!$res) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de télécharger ou d\'appliquer l\'image 4K. Veuillez essayer une autre image.',
            ], 422);
        }

        $product->refresh();

        return response()->json([
            'success' => true,
            'message' => $isPrimary ? 'Image 4K principale définie avec succès !' : 'Image 4K ajoutée à la galerie du produit !',
            'image_path' => $res['path'],
            'image_url' => $res['image_url'],
            'image_id' => $res['image_id'],
            'is_primary' => $res['is_primary'],
            'sort_order' => $res['sort_order'],
            'width' => $res['width'],
            'height' => $res['height'],
            'quality' => $res['quality'],
            'badge_html' => $product->image_quality_badge,
        ]);
    }

    /**
     * Delete a specific product image via AJAX.
     */
    public function deleteImage(Product $product, \App\Models\ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return response()->json(['success' => false, 'message' => 'Image non trouvée pour ce produit.'], 404);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary || $product->image === $image->image_path) {
            $next = $product->images()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
                $product->update([
                    'image' => $next->image_path,
                ]);
            } else {
                $product->update([
                    'image' => null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image supprimée avec succès.',
        ]);
    }

    /**
     * Upload and apply a high-resolution image directly from user computer.
     */
    public function upload4kImage(Request $request, Product $product)
    {
        $request->validate([
            'image_file' => ['required', 'image', 'max:25600'], // up to 25MB high-res
        ]);

        $file = $request->file('image_file');
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $product->id . '_' . \Illuminate\Support\Str::slug(substr($product->name, 0, 40)) . '_' . time() . '.' . $ext;
        $relativeDir = 'products/official';
        $fullStorageDir = storage_path('app/public/' . $relativeDir);

        if (!file_exists($fullStorageDir)) {
            mkdir($fullStorageDir, 0755, true);
        }

        $file->move($fullStorageDir, $filename);
        $relativePath = $relativeDir . '/' . $filename;
        $fullSavedPath = storage_path('app/public/' . $relativePath);

        $info = @getimagesize($fullSavedPath);
        $width = $info[0] ?? null;
        $height = $info[1] ?? null;
        $quality = 'sd';
        if ($width >= 2000 || $height >= 2000) {
            $quality = '4k';
        } elseif ($width >= 1200 || $height >= 1200) {
            $quality = 'fhd';
        }

        $product->update([
            'image' => $relativePath,
            'image_width' => $width,
            'image_height' => $height,
            'image_quality' => $quality,
        ]);

        \App\Models\ProductImage::updateOrCreate(
            ['product_id' => $product->id, 'is_primary' => true],
            ['image_path' => $relativePath, 'sort_order' => 0]
        );

        \Illuminate\Support\Facades\Cache::forget('frontend_home_data');
        \Illuminate\Support\Facades\Cache::forget('frontend_nav_categories');
        \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');

        return response()->json([
            'success' => true,
            'message' => 'Image 4K téléversée et appliquée avec succès !',
            'image_path' => $relativePath,
            'image_url' => asset('storage/' . $relativePath),
            'width' => $width,
            'height' => $height,
            'quality' => $quality,
            'badge_html' => $product->image_quality_badge,
        ]);
    }

    /**
     * Studio Quality Hub view for managing and upgrading product images to 4K.
     */
    public function imageQualityHub(Request $request)
    {
        $query = Product::with(['productCategory'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('quality')) {
            $q = $request->quality;
            if ($q === 'needs_upgrade') {
                $query->where(function($sub) {
                    $sub->whereIn('image_quality', ['sd', 'low', 'placeholder', 'missing', 'unknown'])
                        ->orWhereNull('image_quality');
                });
            } elseif ($q === '4k') {
                $query->where('image_quality', '4k');
            } elseif ($q === 'fhd') {
                $query->where('image_quality', 'fhd');
            } elseif ($q === 'sd') {
                $query->where('image_quality', 'sd');
            } elseif ($q === 'missing') {
                $query->whereIn('image_quality', ['placeholder', 'missing'])->orWhereNull('image');
            }
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(24)->withQueryString();
        $categories = \App\Models\Category::where('status', 'active')->orderBy('name')->get();

        $qualityStats = [
            'total' => Product::count(),
            'four_k' => Product::where('image_quality', '4k')->count(),
            'fhd' => Product::where('image_quality', 'fhd')->count(),
            'needs_upgrade' => Product::whereIn('image_quality', ['sd', 'low', 'placeholder', 'missing', 'unknown'])->orWhereNull('image_quality')->count(),
            'missing' => Product::whereIn('image_quality', ['placeholder', 'missing'])->orWhereNull('image')->count(),
        ];

        return view('products.quality_hub', compact('products', 'categories', 'qualityStats'));
    }

    /**
     * Batch upgrade products to 4K studio quality.
     */
    public function bulkUpgrade4k(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        if (empty($productIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No products selected for 4K upgrade.',
            ], 422);
        }

        $service = app(\App\Services\OfficialProductImageService::class);
        $result = $service->bulkUpgrade4k($productIds, 1200);

        return response()->json([
            'success' => true,
            'message' => "Mise à niveau terminée : {$result['upgraded']} produit(s) mis à niveau en qualité Studio 4K !",
            'data' => $result,
        ]);
    }

    /**
     * Toggle product status (active/inactive) via AJAX.
     */
    public function toggleStatus(Product $product)
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'status_label' => $newStatus === 'active' ? 'Actif' : 'Inactif',
            'message' => "Le statut du produit '{$product->name}' a été mis à jour en " . ($newStatus === 'active' ? 'Actif' : 'Inactif'),
        ]);
    }

    /**
     * Generate the best description for a single product.
     */
    public function generateDescription(Request $request, Product $product)
    {
        $style = $request->input('style', 'pro_ecommerce');
        $service = app(\App\Services\ProductDescriptionService::class);
        $description = $service->generateBestDescription($product, ['style' => $style]);

        return response()->json([
            'success' => true,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'description' => $description,
            'length' => mb_strlen($description),
        ]);
    }

    /**
     * Apply generated description to a product.
     */
    public function applyDescription(Request $request, Product $product)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $product->update([
            'description' => $request->description,
        ]);

        \Illuminate\Support\Facades\Cache::forget('frontend_home_data');
        \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');

        return response()->json([
            'success' => true,
            'message' => "Description mise à jour avec succès pour '{$product->name}' !",
            'description' => $product->description,
        ]);
    }

    /**
     * Bulk generate descriptions for multiple products.
     */
    public function bulkGenerateDescriptions(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        if (empty($productIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez sélectionner au moins un produit.',
            ], 422);
        }

        $overwriteMode = $request->input('overwrite_mode', 'empty_only');
        $style = $request->input('style', 'pro_ecommerce');

        $service = app(\App\Services\ProductDescriptionService::class);
        $result = $service->bulkGenerateDescriptions($productIds, [
            'overwrite_mode' => $overwriteMode,
            'style' => $style,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Génération terminée : {$result['updated']} produit(s) mis à jour, {$result['skipped']} ignoré(s).",
            'data' => $result,
        ]);
    }
}

