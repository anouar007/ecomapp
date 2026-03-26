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
    public function index()
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

        // 2. Categories with Products for Horizontal Scrolls (Cached for 1 hour)
        $categoriesWithProducts = Cache::remember('home_categories_products', 3600, function () {
            return Category::where('status', 'active')
                ->has('products')
                ->with(['products' => function($query) {
                    $query->where('status', 'active')
                        ->with(['images', 'primaryImage', 'productCategory', 'variants'])
                        ->latest()
                        ->take(10);
                }])
                ->get();
        });

        // 3. Simple Category List (Cached for 1 hour)
        $allCategories = Cache::remember('home_all_categories', 3600, function () {
            return Category::where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->get();
        });

        return view('frontend.home', compact(
            'heroSlides', 
            'allCategories', 
            'categoriesWithProducts'
        ));
    }
}
