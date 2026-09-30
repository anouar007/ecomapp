<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 'active');

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('productCategory', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search across name, sku, description, and category
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($b) use ($search) {
                $b->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('productCategory', function ($cat) use ($search) {
                      $cat->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Price Filter
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->with(['images', 'productCategory'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('frontend.shop.partials.product-grid', compact('products'))->render();
        }

        $categories = Cache::remember('shop_catalog_categories', 3600, function () {
            return Category::withCount('products')->get();
        });

        return view('frontend.shop.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        // For now using ID, later can switch to slug if added
        $product = Product::with(['images', 'productCategory', 'inventoryMovements'])->findOrFail($id);
        
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
        if (mb_strlen($q) < 1) {
            return response()->json([
                'success' => true,
                'query' => '',
                'count' => 0,
                'categories' => [],
                'products' => [],
                'all_url' => route('shop.index', [], false)
            ]);
        }

        $cacheKey = 'live_search_' . md5(mb_strtolower($q));
        $data = Cache::remember($cacheKey, 300, function () use ($q) {
            // 1. Matching Categories Suggestions
            $matchingCategories = Category::where('status', 'active')
                ->where(function($cq) use ($q) {
                    $cq->where('name', 'like', '%' . $q . '%')
                       ->orWhere('slug', 'like', '%' . $q . '%');
                })
                ->withCount(['products' => function($pq) {
                    $pq->where('status', 'active');
                }])
                ->take(3)
                ->get()
                ->map(function($cat) {
                    return [
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                        'count' => $cat->products_count,
                        'url' => route('shop.index', ['category' => $cat->slug], false),
                    ];
                });

            // 2. Multi-word Product Matching
            $terms = array_values(array_filter(explode(' ', $q), fn($t) => mb_strlen(trim($t)) > 0));

            $query = Product::where('status', 'active')
                ->where(function ($builder) use ($q, $terms) {
                    // Direct full phrase match
                    $builder->where('name', 'like', '%' . $q . '%')
                        ->orWhere('sku', 'like', '%' . $q . '%')
                        ->orWhere('description', 'like', '%' . $q . '%')
                        ->orWhereHas('productCategory', function ($catQuery) use ($q) {
                            $catQuery->where('name', 'like', '%' . $q . '%');
                        });

                    // Or all individual keywords match
                    if (count($terms) > 1) {
                        $builder->orWhere(function($sub) use ($terms) {
                            foreach ($terms as $term) {
                                $sub->where(function($termSub) use ($term) {
                                    $termSub->where('name', 'like', '%' . $term . '%')
                                            ->orWhere('sku', 'like', '%' . $term . '%')
                                            ->orWhereHas('productCategory', function ($cq) use ($term) {
                                                $cq->where('name', 'like', '%' . $term . '%');
                                            });
                                });
                            }
                        });
                    }
                })
                ->with(['productCategory', 'images']);

            $totalCount = $query->count();
            $products = $query->take(6)->get();

            $results = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category_name ?? 'Matériel Caméra',
                    'price' => $product->formatted_price,
                    'is_sale' => $product->isOnSale(),
                    'sale_price' => $product->isOnSale() ? $product->formatted_sale_price : null,
                    'discount' => $product->discount_percentage,
                    'in_stock' => $product->isInStock(),
                    'image' => $product->thumbnail,
                    'url' => route('shop.show', $product->id, false),
                ];
            });

            return [
                'success' => true,
                'query' => $q,
                'count' => $totalCount,
                'categories' => $matchingCategories,
                'products' => $results,
                'all_url' => route('shop.index', ['q' => $q], false)
            ];
        });

        return response()->json($data);
    }
}
