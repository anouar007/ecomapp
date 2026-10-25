<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;
use App\Services\ProductSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $search = $request->get('q', '');
        $categorySlug = $request->get('category');
        $minPrice = $request->filled('min_price') ? (float) $request->min_price : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->max_price : null;
        $sort = $request->get('sort');

        // Build intelligent search query with relevance ranking
        $query = ProductSearchService::buildQuery(
            search: $search,
            categorySlug: $categorySlug,
            minPrice: $minPrice,
            maxPrice: $maxPrice,
            sort: $sort
        );

        $products = $query->with(['images', 'productCategory'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->paginate(20)->withQueryString();

        $categories = Cache::remember('shop_catalog_categories', 3600, function () {
            return Category::withCount('products')->get();
        });

        // Get matching categories for search chips if searching
        $matchingCategories = $request->filled('q')
            ? ProductSearchService::getMatchingCategories($search, 4)
            : collect();

        $popularSearches = ProductSearchService::getPopularSearches();

        $activeCategory = $categories->where('slug', $categorySlug)->first();
        $activeCategoryTitle = $activeCategory 
            ? $activeCategory->name 
            : ($request->filled('q') ? __('Résultats pour ":q"', ['q' => $request->q]) : __('Tout le matériel'));

        if ($request->ajax() || $request->wantsJson()) {
            if ($request->header('Accept') === 'application/json' || $request->get('format') === 'json') {
                return response()->json([
                    'html' => view('frontend.shop.partials.product-grid', compact('products', 'categories', 'activeCategory', 'activeCategoryTitle', 'matchingCategories', 'popularSearches'))->render(),
                    'total' => $products->total(),
                    'category_title' => $activeCategoryTitle,
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'matching_categories' => $matchingCategories,
                ]);
            }

            return view('frontend.shop.partials.product-grid', compact('products', 'categories', 'activeCategory', 'activeCategoryTitle', 'matchingCategories', 'popularSearches'))->render();
        }

        return view('frontend.shop.index', compact('products', 'categories', 'activeCategory', 'activeCategoryTitle', 'matchingCategories', 'popularSearches'));
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = is_numeric($id)
            ? Product::with(['images', 'productCategory', 'inventoryMovements'])->findOrFail($id)
            : Product::with(['images', 'productCategory', 'inventoryMovements'])->where('slug', $id)->firstOrFail();
        
        // Paginate approved reviews separately - 5 per page
        $reviews = ProductReview::where('product_id', $product->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        // Related products — eager-load images so main_image accessor works in view
        $relatedProducts = Product::with('images')
            ->where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('frontend.shop.show', compact('product', 'relatedProducts', 'reviews'));
    }

    /**
     * Return product data as JSON for Quick View.
     */
    public function json($id)
    {
        $product = Product::with('productCategory', 'primaryImage')->findOrFail($id);
        
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => \Str::limit($product->description, 150),
            'formatted_price' => $product->formatted_price,
            'sale_price' => $product->sale_price,
            'formatted_sale_price' => $product->formatted_sale_price,
            'is_on_sale' => $product->isOnSale(),
            'discount_percentage' => $product->discount_percentage,
            'category_name' => $product->category_name,
            'main_image_url' => $product->thumbnail,
            'url' => route('shop.show', $product->id)
        ]);
    }

    /**
     * Return dynamic search results as JSON for navbar live search.
     */
    public function liveSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        return response()->json(ProductSearchService::liveSearch($q, 8));
    }
}
