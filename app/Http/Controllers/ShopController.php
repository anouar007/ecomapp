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

        // Save the search in the client's history (logged-in clients, first page only)
        if ($request->user() && is_string($search) && trim($search) !== '' && (int) $request->get('page', 1) <= 1) {
            try {
                \App\Models\SearchHistory::record($request->user()->id, $search);
            } catch (\Throwable $e) {
                report($e); // never break the shop page because of history
            }
        }

        $categorySlug = $request->get('category');
        $brandSlug = $request->get('brand');
        $minPrice = $request->filled('min_price') ? (float) $request->min_price : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->max_price : null;
        $sort = $request->get('sort');

        $fileCache = Cache::store('file');
        $cacheVer = \App\Http\Middleware\FastGuestPageCache::getVersion();
        $pageNum = (int) $request->get('page', 1);
        $productsCacheKey = 'shop_grid_v2_' . $cacheVer . '_' . md5(json_encode([
            $search, $categorySlug, $brandSlug, $minPrice, $maxPrice, $sort, $pageNum,
        ]));

        $products = $fileCache->remember($productsCacheKey, 600, function () use ($search, $categorySlug, $brandSlug, $minPrice, $maxPrice, $sort) {
            $query = ProductSearchService::buildQuery(
                search: $search,
                categorySlug: $categorySlug,
                brandSlug: $brandSlug,
                minPrice: $minPrice,
                maxPrice: $maxPrice,
                sort: $sort
            );

            return $query->with(['images', 'productCategory', 'brand', 'activeTags'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->paginate(32)->withQueryString();
        });

        $categories = $fileCache->remember("shop_catalog_categories_{$cacheVer}", 3600, function () {
            return Category::withCount('products')->get();
        });

        $totalActiveProducts = (int) $fileCache->remember("total_active_products_count_{$cacheVer}", 1800, function () {
            return Product::where('status', 'active')->count();
        });

        // Smart brands contextually calculated based on active category & search
        $smartBrands = ProductSearchService::getSmartBrands($categorySlug, $search);
        $activeBrand = !empty($brandSlug)
            ? $fileCache->remember("active_brand_{$brandSlug}_{$cacheVer}", 3600, fn() => Brand::where('slug', $brandSlug)->first())
            : null;

        // Get matching categories for search chips if searching
        $matchingCategories = $request->filled('q')
            ? ProductSearchService::getMatchingCategories($search, 4)
            : collect();

        $popularSearches = ProductSearchService::getPopularSearches();

        $activeCategory = $categories->where('slug', $categorySlug)->first();
        $activeCategoryTitle = $activeCategory 
            ? $activeCategory->name 
            : ($request->filled('q') ? __('Résultats pour ":q"', ['q' => $request->q]) : __('Tout le matériel'));

        // Track Category View and Search Query
        if ($activeCategory && (int) $request->get('page', 1) <= 1) {
            \App\Services\ViewTrackingService::trackCategoryView($activeCategory, $request);
        }
        if (is_string($search) && trim($search) !== '' && (int) $request->get('page', 1) <= 1) {
            \App\Services\ViewTrackingService::trackSearch($search, $products->total(), $request);
        }

        $viewData = compact(
            'products',
            'categories',
            'totalActiveProducts',
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
     * Display the specified product (Ultra-Fast Cached Data & Zero ORDER BY RAND()).
     */
    public function show(Request $request, $id)
    {
        $fileCache = Cache::store('file');
        $cacheVer = \App\Http\Middleware\FastGuestPageCache::getVersion();
        $pdpCacheKey = 'pdp_bundle_v2_' . $cacheVer . '_' . md5((string) $id);

        $bundle = $fileCache->remember($pdpCacheKey, 1800, function () use ($id, $fileCache, $cacheVer) {
            $product = is_numeric($id)
                ? Product::with(['images', 'productCategory', 'categories', 'variants', 'brand', 'activeTags'])->find($id)
                : Product::with(['images', 'productCategory', 'categories', 'variants', 'brand', 'activeTags'])->where('slug', $id)->first();

            if (!$product) {
                return null;
            }

            // 1. Related products — find items in stock sharing any assigned category (10 products on slider)
            $categoryIds = $product->category_ids;
            $relatedProducts = Product::with(['images', 'activeTags', 'productCategory', 'brand'])
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->where('id', '!=', $product->id)
                ->where(function ($q) use ($categoryIds) {
                    if (!empty($categoryIds)) {
                        $q->whereIn('category_id', $categoryIds)
                          ->orWhereHas('categories', function ($sub) use ($categoryIds) {
                              $sub->whereIn('categories.id', $categoryIds);
                          });
                    }
                })
                ->take(10)
                ->get();

            // If fewer than 10 related in same category, supplement with same brand or latest active in-stock products
            if ($relatedProducts->count() < 10) {
                $excludeIds = $relatedProducts->pluck('id')->push($product->id)->all();
                $fillQuery = Product::with(['images', 'activeTags', 'productCategory', 'brand'])
                    ->where('status', 'active')
                    ->where('stock', '>', 0)
                    ->whereNotIn('id', $excludeIds);

                if ($product->brand_id) {
                    $fillQuery->orderByRaw("CASE WHEN brand_id = " . (int) $product->brand_id . " THEN 0 ELSE 1 END");
                }

                $moreRelated = $fillQuery->latest()->take(10 - $relatedProducts->count())->get();
                $relatedProducts = $relatedProducts->concat($moreRelated);
            }

            // 2. Suggested products ("Suggestions pour vous") — Fast In-Memory Pool (0ms, no ORDER BY RAND() table scan!)
            $inStockPool = $fileCache->remember("shop_instock_pool_v1_{$cacheVer}", 1800, function () {
                return Product::with(['images', 'activeTags', 'productCategory', 'brand'])
                    ->where('status', 'active')
                    ->where('stock', '>', 0)
                    ->latest('updated_at')
                    ->take(80)
                    ->get();
            });

            $excludeIdsForRandom = $relatedProducts->pluck('id')->push($product->id)->all();
            $filteredPool = $inStockPool->whereNotIn('id', $excludeIdsForRandom)->values();
            if ($filteredPool->count() < 10) {
                $filteredPool = $inStockPool->where('id', '!=', $product->id)->values();
            }
            $randomProducts = $filteredPool->count() > 10
                ? $filteredPool->random(10)->values()
                : $filteredPool;

            // 3. Pre-calculate review stats so Blade doesn't run AVG() and COUNT() on every page view
            $reviewStats = ProductReview::where('product_id', $product->id)
                ->where('status', 'approved')
                ->selectRaw('COUNT(*) as total_count, COALESCE(AVG(rating), 0) as avg_rating')
                ->first();

            return [
                'product' => $product,
                'relatedProducts' => $relatedProducts,
                'randomProducts' => $randomProducts,
                'avgRating' => (float) ($reviewStats->avg_rating ?? 0),
                'reviewCount' => (int) ($reviewStats->total_count ?? 0),
            ];
        });

        if (!$bundle || empty($bundle['product'])) {
            abort(404);
        }

        $product = $bundle['product'];
        $relatedProducts = $bundle['relatedProducts'];
        $randomProducts = $bundle['randomProducts'];
        $avgRating = $bundle['avgRating'];
        $reviewCount = $bundle['reviewCount'];

        // Track product view asynchronously after response is sent
        \App\Services\ViewTrackingService::trackProductView($product, $request);

        // Paginate approved reviews (cached for page 1 when reviewCount === 0 to skip DB query completely!)
        $reviewPage = (int) $request->get('page', 1);
        if ($reviewCount === 0 && $reviewPage <= 1) {
            $reviews = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 5, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        } else {
            $reviews = ProductReview::where('product_id', $product->id)
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        }

        return view('frontend.shop.show', compact(
            'product',
            'relatedProducts',
            'randomProducts',
            'reviews',
            'avgRating',
            'reviewCount'
        ));
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
        $data = ProductSearchService::liveSearch($q, 8);
        if (mb_strlen($q) >= 2) {
            \App\Services\ViewTrackingService::trackSearch($q, (int)($data['count'] ?? count($data['products'] ?? [])), $request);
        }
        return response()->json($data);
    }
}
