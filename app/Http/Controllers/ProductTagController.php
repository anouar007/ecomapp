<?php

namespace App\Http\Controllers;

use App\Models\ProductTag;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductTagController extends Controller
{
    /**
     * Display a listing of product tags.
     */
    public function index(Request $request)
    {
        $query = ProductTag::withCount('products');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $tags = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(20)
            ->withQueryString();

        $totalTags = ProductTag::count();
        $activeTags = ProductTag::where('status', 'active')->count();
        $inactiveTags = max(0, $totalTags - $activeTags);
        $tagsWithProducts = ProductTag::has('products')->count();
        $taggedProductsCount = DB::table('product_product_tag')->distinct('product_id')->count('product_id');

        return view('product-tags.index', compact(
            'tags',
            'totalTags',
            'activeTags',
            'inactiveTags',
            'tagsWithProducts',
            'taggedProductsCount'
        ));
    }

    /**
     * Show the form for creating a new product tag.
     */
    public function create()
    {
        $products = Product::with(['images', 'productCategory'])
            ->select('id', 'name', 'sku', 'price', 'sale_price', 'stock', 'category_id', 'status')
            ->orderBy('name', 'asc')
            ->get();
        $selectedProductIds = [];

        return view('product-tags.create', compact('products', 'selectedProductIds'));
    }

    /**
     * Store a newly created product tag in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'icon' => 'nullable|string|max:80',
            'style_type' => 'required|in:solid,gradient,outline,soft,glass',
            'bg_color' => 'required|string|max:30',
            'bg_gradient' => 'nullable|string|max:30',
            'text_color' => 'required|string|max:30',
            'border_color' => 'nullable|string|max:30',
            'shape' => 'required|in:rounded,pill,square,leaf,ribbon',
            'position' => 'required|in:top-left,top-right,bottom-left,bottom-right',
            'size_scale' => 'required|integer|min:50|max:180',
            'font_weight' => 'required|in:600,700,800,900',
            'uppercase' => 'nullable|boolean',
            'pulse_animation' => 'nullable|boolean',
            'has_shadow' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']) ?: 'tag-' . time();
        $validated['uppercase'] = $request->boolean('uppercase');
        $validated['pulse_animation'] = $request->boolean('pulse_animation');
        $validated['has_shadow'] = $request->boolean('has_shadow');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $tag = ProductTag::create($validated);

        if ($request->has('product_ids')) {
            $tag->products()->sync($request->input('product_ids', []));
        }

        $this->flushProductTagCaches();

        return redirect()->route('product-tags.index')
            ->with('success', "Le tag « {$tag->name} » a été créé avec succès.");
    }

    /**
     * Show the form for editing the specified product tag.
     */
    public function edit(ProductTag $productTag)
    {
        $products = Product::with(['images', 'productCategory'])
            ->select('id', 'name', 'sku', 'price', 'sale_price', 'stock', 'category_id', 'status')
            ->orderBy('name', 'asc')
            ->get();
        $selectedProductIds = $productTag->products()->pluck('products.id')->map(fn($id) => (int) $id)->toArray();

        return view('product-tags.edit', compact('productTag', 'products', 'selectedProductIds'));
    }

    /**
     * Update the specified product tag in storage.
     */
    public function update(Request $request, ProductTag $productTag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'icon' => 'nullable|string|max:80',
            'style_type' => 'required|in:solid,gradient,outline,soft,glass',
            'bg_color' => 'required|string|max:30',
            'bg_gradient' => 'nullable|string|max:30',
            'text_color' => 'required|string|max:30',
            'border_color' => 'nullable|string|max:30',
            'shape' => 'required|in:rounded,pill,square,leaf,ribbon',
            'position' => 'required|in:top-left,top-right,bottom-left,bottom-right',
            'size_scale' => 'required|integer|min:50|max:180',
            'font_weight' => 'required|in:600,700,800,900',
            'uppercase' => 'nullable|boolean',
            'pulse_animation' => 'nullable|boolean',
            'has_shadow' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $validated['uppercase'] = $request->boolean('uppercase');
        $validated['pulse_animation'] = $request->boolean('pulse_animation');
        $validated['has_shadow'] = $request->boolean('has_shadow');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $productTag->update($validated);

        if ($request->has('sync_products')) {
            $productTag->products()->sync($request->input('product_ids', []));
        }

        $this->flushProductTagCaches();

        return redirect()->route('product-tags.index')
            ->with('success', "Le tag « {$productTag->name} » a été mis à jour.");
    }

    /**
     * Remove the specified product tag from storage.
     */
    public function destroy(ProductTag $productTag)
    {
        $name = $productTag->name;
        $productTag->products()->detach();
        $productTag->delete();

        $this->flushProductTagCaches();

        return redirect()->route('product-tags.index')
            ->with('success', "Le tag « {$name} » a été supprimé.");
    }

    /**
     * Flush product and home caches when tags change.
     */
    private function flushProductTagCaches(): void
    {
        Cache::forget('frontend_home_data');
        Cache::forget('home_featured_products_v1');
        Cache::forget('home_new_arrival_products_v1');
        Cache::forget('home_occasion_products_v1');
        Cache::forget('active_product_tags_v1');
    }
}

