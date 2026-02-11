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
        if ($request->has('category')) {
            $query->whereHas('productCategory', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
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

        $categories = Category::withCount('products')->get();

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
        
        // Related products
        $relatedProducts = Product::where('status', 'active')
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
            'main_image_url' => $product->main_image ? \Storage::url($product->main_image) : null,
            'url' => route('shop.show', $product->id)
        ]);
    }
}
