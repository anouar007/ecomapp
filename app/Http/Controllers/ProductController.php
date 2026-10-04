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
        $query = Product::with(['images', 'productCategory'])->latest();

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
                $query->whereIn('image_quality', ['placeholder', 'missing'])->orWhereNull('image');
            }
        }

        $products = $query->paginate(20)->withQueryString();
        
        $categories = \App\Models\Category::where('status', 'active')->orderBy('name')->get();

        $qualityStats = [
            'total' => Product::count(),
            'four_k' => Product::where('image_quality', '4k')->count(),
            'fhd' => Product::where('image_quality', 'fhd')->count(),
            'needs_upgrade' => Product::whereIn('image_quality', ['sd', 'low', 'placeholder', 'missing', 'unknown'])->orWhereNull('image_quality')->count(),
        ];

        return view('products.index', compact('products', 'categories', 'qualityStats'));
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
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
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
            'action' => 'required|string|in:delete,duplicate,increase_stock,decrease_stock,activate,deactivate',
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
        $service = app(\App\Services\OfficialProductImageService::class);
        $candidates = $service->find4kCandidates($query, $product->category_name);

        return response()->json([
            'success' => true,
            'query' => $query,
            'count' => count($candidates),
            'candidates' => $candidates,
        ]);
    }

    /**
     * Apply a selected 4K image directly to a product.
     */
    public function apply4kImage(Request $request, Product $product)
    {
        $request->validate([
            'image_url' => ['required', 'url'],
        ]);

        $service = app(\App\Services\OfficialProductImageService::class);
        $path = $service->apply4kImage($product, $request->input('image_url'));

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download or apply 4K image. Please select another image.',
            ], 422);
        }

        $product->refresh();
        $fullUrl = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'message' => 'Image 4K Ultra-HD appliquée avec succès !',
            'image_path' => $path,
            'image_url' => $fullUrl,
            'width' => $product->image_width,
            'height' => $product->image_height,
            'quality' => $product->image_quality,
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
}

