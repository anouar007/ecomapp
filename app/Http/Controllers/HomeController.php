<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner; // Added import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Show the application home page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // 1. Hero Content
        $heroSlides = Banner::where('position', 'main_hero')
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get();
            
        if($heroSlides->isEmpty()) {
             $heroSlides = Product::where('status', 'active')
                ->where('image', '!=', null)
                ->inRandomOrder()
                ->take(3)
                ->get();
        }

        // 2. Categories with Products for Horizontal Scrolls
        $categoriesWithProducts = Category::where('status', 'active')
            ->has('products')
            ->with(['products' => function($query) {
                $query->where('status', 'active')->latest()->take(10);
            }])
            ->get();

        // 3. Simple Category List (for circle navigation)
        $allCategories = Category::where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('frontend.home', compact(
            'heroSlides', 
            'allCategories', 
            'categoriesWithProducts'
        ));
    }
}
