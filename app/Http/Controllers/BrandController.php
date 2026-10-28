<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of brands with KPI metrics and filtering.
     */
    public function index(Request $request)
    {
        $query = Brand::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('website', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $brands = $query->paginate(20)->withQueryString();

        $kpiStats = [
            'total' => Brand::count(),
            'active' => Brand::where('status', 'active')->count(),
            'inactive' => Brand::where('status', 'inactive')->count(),
            'with_products' => Brand::has('products')->count(),
            'unassigned_products' => Product::whereNull('brand_id')->count(),
        ];

        return view('brands.index', compact('brands', 'kpiStats'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:brands,slug'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand = Brand::create($validated);

        return redirect()->route('brands.index')
            ->with('success', "La marque « {$brand->name} » a été créée avec succès.");
    }

    /**
     * Display the specified brand (redirects to edit).
     */
    public function show(Brand $brand)
    {
        return redirect()->route('brands.edit', $brand);
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        $brand->loadCount('products');
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:brands,slug,' . $brand->id],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->boolean('remove_logo')) {
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        unset($validated['remove_logo']);
        $brand->update($validated);

        return redirect()->route('brands.index')
            ->with('success', "La marque « {$brand->name} » a été mise à jour avec succès.");
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand)
    {
        // Unlink associated products
        Product::where('brand_id', $brand->id)->update(['brand_id' => null]);

        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brandName = $brand->name;
        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', "La marque « {$brandName} » a été supprimée avec succès.");
    }

    /**
     * Auto-detect and link unassigned products to matching brands based on product name.
     */
    public function autoDetect()
    {
        $brands = Brand::where('status', 'active')->get();
        $unassignedProducts = Product::whereNull('brand_id')->get();
        $linkedCount = 0;

        foreach ($unassignedProducts as $product) {
            foreach ($brands as $brand) {
                $brandName = preg_quote($brand->name, '/');
                if (preg_match('/(?:\b|^)' . $brandName . '(?:\b|$)/i', $product->name)) {
                    $product->update(['brand_id' => $brand->id]);
                    $linkedCount++;
                    break;
                }
            }
        }

        return redirect()->route('brands.index')
            ->with('success', "{$linkedCount} produit(s) ont été automatiquement associés à leurs marques.");
    }
}
