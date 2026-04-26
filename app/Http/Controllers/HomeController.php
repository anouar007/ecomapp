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

        // 2. Product Catalog Query
        $query = Product::where('status', 'active');

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('productCategory', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Sort - Default to Newest
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            default: $query->orderBy('created_at', 'desc'); break;
        }

        // 3. Custom Pagination: 30 then 10
        $page = (int)$request->get('page', 1);
        $perPage = ($page === 1) ? 30 : 10;
        $offset = ($page === 1) ? 0 : 30 + (($page - 2) * 10);
        
        $products = $query->with(['images', 'productCategory', 'variants'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Check for more products
        $totalInQuery = (clone $query)->count();
        $hasMore = ($offset + $perPage) < $totalInQuery;

        $allCategories = Category::where('status', 'active')->orderBy('sort_order', 'asc')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.partials.catalog-content', compact('products'))->render(),
                'hasMore' => $hasMore,
                'nextPage' => $page + 1
            ]);
        }

        return view('frontend.home', compact(
            'heroSlides',
            'allCategories',
            'products',
            'hasMore'
        ));
    }

    /**
     * Show the maintenance page.
     *
     * @return \Illuminate\View\View
     */
    public function maintenance()
    {
        if (!setting('maintenance_mode', false)) {
            return redirect()->route('home');
        }

        return view('frontend.maintenance');
    }
}
