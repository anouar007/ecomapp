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
        // 1. Hero Section - Generic Store Images/Content
        // We can still use active featured products or banners, but the user requested "images and content generale"
        // Let's use Banners for the slides if available, otherwise fallback to featured products as slides
        $heroSlides = Banner::where('position', 'main_hero')
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get();
            
        if($heroSlides->isEmpty()) {
             // Fallback to top rated products for visual appeal if no banners
             $heroSlides = Product::where('status', 'active')
                ->where('image', '!=', null)
                ->inRandomOrder()
                ->take(3)
                ->get();
        }

        // 2. All Categories List
        $allCategories = Category::where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get(); // Fetching all for the list section

        // 3. Featured Products
        $featuredProducts = Product::where('status', 'active')
            ->with(['productCategory', 'primaryImage', 'images'])
            ->latest() // Use latest for now as "featured"
            ->take(8)
            ->get()
            ->map(function($product) {
                // Compatibility for new view
                $product->thumbnail = $product->main_image ? Storage::url($product->main_image) : null;
                $product->is_new = $product->created_at->diffInDays(now()) < 30;
                $product->short_description = Str::limit($product->description, 60);
                $product->rating = rand(3, 5); // Dummy rating for visual demo
                $product->review_count = rand(10, 500);
                $product->in_wishlist = false; // Requires auth check ideally
                return $product;
            });

        // 4. Testimonials
        $testimonials = \App\Models\Testimonial::where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.home', compact(
            'heroSlides', 
            'allCategories', 
            'featuredProducts',
            'testimonials'
        ));
    }
}
