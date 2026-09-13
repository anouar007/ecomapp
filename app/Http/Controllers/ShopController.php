<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 'active');

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('productCategory', function ($q) use ($request) {
                $q->whereIn('slug', (array) $request->input('category'));
            });
        }

        if ($request->filled('size')) {
            $query->whereHas('variants', fn ($q) => $q
                ->where('status', 'active')->whereIn('size', (array) $request->input('size')));
        }

        if ($request->filled('rating') && is_numeric($request->input('rating'))) {
            $query->whereHas('reviews', fn ($q) => $q->where('status', 'approved')
                ->selectRaw('product_id')->groupBy('product_id')
                ->havingRaw('AVG(rating) >= CAST(? AS DECIMAL(3,2))', [(float) $request->input('rating')]));
        }

        // Search
        if ($request->has('q')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%' . $request->q . '%')
                ->orWhere('name_ar', 'like', '%' . $request->q . '%'));
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
            case 'popular':
            case null:
                $query->withSum('orderItems', 'quantity')->orderByDesc('order_items_sum_quantity');
                break;
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

        $products = $query->with(['images', 'productCategory', 'variants'])
            ->withCount(['reviews' => fn ($q) => $q->where('status', 'approved')])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('storefront.partials.product-grid', compact('products'))->render();
        }

        $categories = Category::where('status', 'active')
            ->withCount(['products' => fn ($q) => $q->where('status', 'active')])->get();
        $sizes = \App\Models\ProductVariant::where('status', 'active')
            ->whereHas('product', fn ($q) => $q->where('status', 'active'))
            ->whereNotNull('size')->distinct()->orderBy('size')->pluck('size');
        $priceCeiling = max(500, (int) ceil(Product::where('status', 'active')->max('price') ?? 0));

        return view('storefront.shop', compact('products', 'categories', 'sizes', 'priceCeiling'));
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        // For now using ID, later can switch to slug if added
        $product = Product::where('status', 'active')->with(['images', 'productCategory', 'variants'])
            ->withCount(['reviews' => fn ($q) => $q->where('status', 'approved')])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->findOrFail($id);
        
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

        return view('storefront.product', compact('product', 'relatedProducts', 'reviews'));
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
            'main_image_url' => $product->main_image ? \Storage::url($product->main_image) : null,
            'url' => route('shop.show', $product->id)
        ]);
    }
}
