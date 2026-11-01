<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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
        // 1. Hero Data & Banners (Single cached query)
        $banners = Cache::remember('home_active_banners_v1', 3600, function () {
            return Banner::where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->get();
        });

        $heroSlides = $banners->where('position', 'main_hero')->values();
        $heroSideTop = $banners->firstWhere('position', 'side_top');
        $heroSideBottom = $banners->firstWhere('position', 'side_bottom');
        $promoMiddleBanner = $banners->firstWhere('position', 'wide_middle');

        // 2. Popular Categories loaded dynamically from DB with dashboard-uploaded images
        $defaultIcons = [
            'camera' => asset('images/camera/icons/cat_camera.svg'),
            'objectifs' => asset('images/camera/icons/cat_lens.svg'),
            'carte-memoire-lecteur' => asset('images/camera/icons/cat_sdcard.svg'),
            'accessoires' => asset('images/camera/icons/cat_accessories.svg'),
            'materiel-de-podcast' => asset('images/camera/icons/cat_podcast.svg'),
            'filtres-nd-cpl-polarise' => asset('images/camera/icons/cat_filter.svg'),
            'matte-box' => asset('images/camera/icons/cat_mattebox.svg'),
            'accessoires-insta360' => asset('images/camera/icons/cat_insta360.svg'),
            'kit-de-nettoyage' => asset('images/camera/icons/cat_cleaning.svg'),
            'trepieds' => asset('images/camera/icons/cat_tripod.svg'),
            'batterie-chargeur' => asset('images/camera/icons/cat_battery.svg'),
            'sacs-de-camera' => asset('images/camera/icons/cat_bag.svg'),
            'lumieres-materiel-de-studio' => asset('images/camera/icons/cat_lighting.svg'),
            'son' => asset('images/camera/icons/cat_audio.svg'),
            'stabilisateurs' => asset('images/camera/icons/cat_gimbals.svg'),
            'occasion' => asset('images/camera/icons/cat_occasion.svg'),
        ];

        $popularCategories = Cache::remember('home_popular_categories_v2', 1800, function () use ($defaultIcons) {
            $categories = Category::where('status', 'active')
                ->whereNull('parent_id')
                ->where('slug', '!=', 'test-cat')
                ->withCount('products')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            return $categories->map(function ($cat) use ($defaultIcons) {
                // Determine image: dashboard uploaded image takes top priority
                $imageUrl = $cat->image_url;
                if (empty($imageUrl)) {
                    $imageUrl = $defaultIcons[$cat->slug] ?? asset('images/camera/cat_cameras.jpg');
                }

                return [
                    'id' => $cat->id,
                    'title' => $cat->name,
                    'count' => $cat->products_count,
                    'image' => $imageUrl,
                    'icon' => $imageUrl, // backwards compatibility for any view referencing icon
                    'url' => route('shop.index', ['category' => $cat->slug]),
                    'highlight' => stripos($cat->name, 'occasion') !== false,
                ];
            })->toArray();
        });

        // 3. Featured Flagship Products ("Notre sélection de produits")
        $defaultFeaturedIds = [594, 45, 374, 36, 18, 2, 7, 14];
        $savedFeatured = setting('home_section_featured_products');
        if (is_string($savedFeatured)) {
            $savedFeatured = json_decode($savedFeatured, true);
        }
        $featuredIds = (!empty($savedFeatured) && is_array($savedFeatured)) ? $savedFeatured : $defaultFeaturedIds;

        $featuredProducts = Cache::remember('home_featured_products_v1', 1800, function () use ($featuredIds) {
            $prods = Product::where('status', 'active')
                ->whereIn('id', $featuredIds)
                ->with(['images', 'productCategory', 'activeTags', 'brand'])
                ->get();

            // Preserve exact configured order
            $ordered = collect($featuredIds)
                ->map(fn($id) => $prods->firstWhere('id', $id))
                ->filter()
                ->values();

            if ($ordered->count() < 8) {
                $extra = Product::where('status', 'active')
                    ->whereNotNull('price')
                    ->where('price', '>', 0)
                    ->whereNotIn('id', $ordered->pluck('id'))
                    ->with(['images', 'productCategory', 'activeTags', 'brand'])
                    ->take(8 - $ordered->count())
                    ->get();
                $ordered = $ordered->merge($extra);
            }
            return $ordered;
        });

        // 4. New Arrivals ("Nouvelle Arrivage")
        $defaultNewArrivalIds = [251, 239, 129, 130, 126, 127];
        $savedNewArrival = setting('home_section_new_arrivals_products');
        if (is_string($savedNewArrival)) {
            $savedNewArrival = json_decode($savedNewArrival, true);
        }
        $newArrivalIds = (!empty($savedNewArrival) && is_array($savedNewArrival)) ? $savedNewArrival : $defaultNewArrivalIds;

        $newArrivalProducts = Cache::remember('home_new_arrival_products_v1', 1800, function () use ($newArrivalIds) {
            $prods = Product::where('status', 'active')
                ->whereIn('id', $newArrivalIds)
                ->with(['images', 'productCategory', 'activeTags', 'brand'])
                ->get();

            $ordered = collect($newArrivalIds)
                ->map(fn($id) => $prods->firstWhere('id', $id))
                ->filter()
                ->values();

            if ($ordered->count() < 6) {
                $extraNew = Product::where('status', 'active')
                    ->whereNotIn('id', $ordered->pluck('id'))
                    ->with(['images', 'productCategory', 'activeTags', 'brand'])
                    ->latest()
                    ->take(6 - $ordered->count())
                    ->get();
                $ordered = $ordered->merge($extraNew);
            }
            return $ordered;
        });

        // 5. Occasion Products ("Coin Occasion") - ONLY products of this category or custom selected
        $savedOccasion = setting('home_section_occasion_products');
        if (is_string($savedOccasion)) {
            $savedOccasion = json_decode($savedOccasion, true);
        }
        $occasionIds = (!empty($savedOccasion) && is_array($savedOccasion)) ? $savedOccasion : null;

        $occasionProducts = Cache::remember('home_occasion_products_v1', 1800, function () use ($occasionIds) {
            if (!empty($occasionIds)) {
                $prods = Product::where('status', 'active')
                    ->whereIn('id', $occasionIds)
                    ->with(['images', 'productCategory', 'categories', 'activeTags', 'brand'])
                    ->get();

                return collect($occasionIds)
                    ->map(fn($id) => $prods->firstWhere('id', $id))
                    ->filter()
                    ->values();
            }

            return Product::where('status', 'active')
                ->where(function ($q) {
                    $q->whereHas('categories', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhereHas('productCategory', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhere('name', 'like', '%(occasion)%')
                      ->orWhere('name', 'like', '%occasion%');
                })
                ->with(['images', 'productCategory', 'categories', 'activeTags', 'brand'])
                ->orderByRaw('FIELD(id, 71, 31, 9, 12, 84) DESC')
                ->get();
        });

        // General categories for navbar / footer (cached)
        $allCategories = Cache::remember('all_active_categories_sorted_v1', 3600, function () {
            return Category::where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->get();
        });

        return view('frontend.home', compact(
            'heroSlides',
            'heroSideTop',
            'heroSideBottom',
            'promoMiddleBanner',
            'popularCategories',
            'featuredProducts',
            'newArrivalProducts',
            'occasionProducts',
            'allCategories'
        ));
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
