<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Show the application home page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // 1. Hero Content (Cached for 1 hour)
        $heroSlides = Cache::remember('home_hero_slides', 3600, function () {
            $slides = Banner::where('position', 'main_hero')
                ->where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->get();
                
            if($slides->isEmpty()) {
                 return Product::where('status', 'active')
                    ->where(function($q) {
                        $q->whereNotNull('image')->orWhereHas('images');
                    })
                    ->with(['images', 'primaryImage', 'variants'])
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            }
            return $slides;
        });

        // 2. Product Catalog Query (Merged from ShopController)
        $query = Product::where('status', 'active');

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('productCategory', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
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

        $products = $query->with(['images', 'productCategory', 'variants'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get();

        // 3. Simple Category List - FRESH LOAD
        $allCategories = Category::where('status', 'active')
            ->with(['products' => function($q) {
                $q->where('status', 'active')
                  ->orderBy('created_at', 'desc')
                  ->with(['images', 'variants']);
            }])
            ->orderBy('sort_order', 'asc')
            ->get();

        // 4. Categories with Products - FRESH LOAD
        $categoriesWithProducts = $allCategories->filter(function($cat) {
            return $cat->products->count() > 0;
        });

        if ($request->ajax() && ($request->filled('search') || $request->filled('sort'))) {
            return response()->json([
                'html' => view('frontend.partials.catalog-content', compact(
                    'products', 
                    'allCategories', 
                    'categoriesWithProducts'
                ))->render()
            ]);
        }

        return view('frontend.home', compact(
            'heroSlides', 
            'allCategories', 
            'categoriesWithProducts',
            'products'
        ));
    }
}
