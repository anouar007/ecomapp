<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
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
        $brandSlug = $request->get('brand');
        $minPrice = $request->filled('min_price') ? (float) $request->min_price : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->max_price : null;
        $sort = $request->get('sort');

        // Build intelligent search query with relevance ranking
        $query = ProductSearchService::buildQuery(
            search: $search,
            categorySlug: $categorySlug,
            brandSlug: $brandSlug,
            minPrice: $minPrice,
            maxPrice: $maxPrice,
            sort: $sort
        );

        $products = $query->with(['images', 'productCategory', 'brand', 'activeTags'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->paginate(20)->withQueryString();

        $categories = Cache::remember('shop_catalog_categories', 3600, function () {
            return Category::withCount('products')->get();
        });

        // Smart brands contextually calculated based on active category & search
        $smartBrands = ProductSearchService::getSmartBrands($categorySlug, $search);
        $activeBrand = !empty($brandSlug) ? Brand::where('slug', $brandSlug)->first() : null;

        // Get matching categories for search chips if searching
        $matchingCategories = $request->filled('q')
            ? ProductSearchService::getMatchingCategories($search, 4)
            : collect();

        $popularSearches = ProductSearchService::getPopularSearches();

        $activeCategory = $categories->where('slug', $categorySlug)->first();
        $activeCategoryTitle = $activeCategory 
            ? $activeCategory->name 
            : ($request->filled('q') ? __('Résultats pour ":q"', ['q' => $request->q]) : __('Tout le matériel'));

        $viewData = compact(
            'products',
            'categories',
            'activeCategory',
            'activeCategoryTitle',
            'matchingCategories',
            'popularSearches',
            'smartBrands',
            'activeBrand',
            'brandSlug'
        );

        if ($request->ajax() || $request->wantsJson()) {
            if ($request->header('Accept') === 'application/json' || $request->get('format') === 'json') {
                return response()->json([
                    'html' => view('frontend.shop.partials.product-grid', $viewData)->render(),
                    'total' => $products->total(),
                    'category_title' => $activeCategoryTitle,
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'matching_categories' => $matchingCategories,
                    'brands' => $smartBrands,
                    'active_brand' => $activeBrand ? ['id' => $activeBrand->id, 'name' => $activeBrand->name, 'slug' => $activeBrand->slug] : null,
                ]);
            }

            return view('frontend.shop.partials.product-grid', $viewData)->render();
        }

        return view('frontend.shop.index', $viewData);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = is_numeric($id)
            ? Product::with(['images', 'productCategory', 'categories', 'inventoryMovements', 'variants', 'brand', 'activeTags'])->findOrFail($id)
            : Product::with(['images', 'productCategory', 'categories', 'inventoryMovements', 'variants', 'brand', 'activeTags'])->where('slug', $id)->firstOrFail();
        
        // Paginate approved reviews separately - 5 per page
        $reviews = ProductReview::where('product_id', $product->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        // Related products — find items sharing any assigned category
        $categoryIds = $product->category_ids;
        $relatedProducts = Product::with(['images', 'activeTags'])
            ->where('status', 'active')
            ->where('id', '!=', $product->id)
            ->where(function($q) use ($categoryIds) {
                if (!empty($categoryIds)) {
                    $q->whereIn('category_id', $categoryIds)
                      ->orWhereHas('categories', function($sub) use ($categoryIds) {
                          $sub->whereIn('categories.id', $categoryIds);
                      });
                }
            })
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
