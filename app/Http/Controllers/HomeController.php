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
        $data = \Illuminate\Support\Facades\Cache::remember('frontend_home_data', 1800, function() {
            // 1. Hero Section
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

            // 2. All Categories List with products (eager load images)
            $allCategories = Category::where('status', 'active')
                ->with(['products' => function($query) {
                    $query->where('status', 'active')
                        ->with(['primaryImage', 'images'])
                        ->latest()
                        ->take(4);
                }])
                ->orderBy('sort_order', 'asc')
                ->get();

            // 3. Featured Products - Show only one row (4 items)
            $featuredProducts = Product::where('status', 'active')
                ->with(['productCategory', 'primaryImage', 'images'])
                ->latest() 
                ->take(4)
                ->get()
                ->map(function($product) {
                    $product->is_new = $product->created_at->diffInDays(now()) < 30;
                    $product->short_description = Str::limit($product->description, 60);
                    $product->rating = rand(3, 5);
                    $product->review_count = rand(10, 500);
                    $product->in_wishlist = false;
                    return $product;
                });

            // Ensure category products also have these properties for the view
            foreach($allCategories as $category) {
                $category->products->each(function($product) {
                    $product->rating = rand(3, 5);
                    $product->review_count = rand(10, 500);
                });
            }

            // 4. Testimonials
            $testimonials = \App\Models\Testimonial::where('is_active', true)
                ->latest()
                ->take(6)
                ->get();

            return compact('heroSlides', 'allCategories', 'featuredProducts', 'testimonials');
        });

        return view('frontend.home', $data);
    }

    /**
     * Display the About page.
     */
    public function about()
    {
        return view('frontend.about');
    }

    /**
     * Display the Contact page.
     */
    public function contact()
    {
        return view('frontend.contact');
    }

    /**
     * Process contact form submission.
     */
    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:2500',
        ]);

        \App\Models\ContactMessage::create($validated);

        return redirect()->route('contact')->with('success', 'Merci ! Votre message a été transmis à nos conseillers techniques. Nous vous répondrons dans les plus brefs délais.');
    }
}
