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
        // 1. Hero Data & Banners
        $heroSlides = Banner::where('position', 'main_hero')
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get();

        $heroSideTop = Banner::where('position', 'side_top')
            ->where('status', 'active')
            ->first();

        $heroSideBottom = Banner::where('position', 'side_bottom')
            ->where('status', 'active')
            ->first();

        $promoMiddleBanner = Banner::where('position', 'wide_middle')
            ->where('status', 'active')
            ->first();

        // 2. Popular Categories matching screenshot 2 + additional store categories
        $popularCategories = [
            [
                'title' => 'CARTE MÉMOIRE / LECTEUR',
                'count' => 35,
                'icon' => asset('images/camera/icons/cat_sdcard.svg'),
                'url' => route('shop.index', ['category' => 'carte-memoire-lecteur']),
                'highlight' => false,
            ],
            [
                'title' => 'ACCESSOIRES',
                'count' => 564,
                'icon' => asset('images/camera/icons/cat_accessories.svg'),
                'url' => route('shop.index', ['category' => 'accessoires']),
                'highlight' => false,
            ],
            [
                'title' => 'MATÉRIEL DE PODCAST',
                'count' => 15,
                'icon' => asset('images/camera/icons/cat_podcast.svg'),
                'url' => route('shop.index', ['category' => 'materiel-de-podcast']),
                'highlight' => false,
            ],
            [
                'title' => 'FILTRES ND / CPL POLARISÉ',
                'count' => 65,
                'icon' => asset('images/camera/icons/cat_filter.svg'),
                'url' => route('shop.index', ['category' => 'filtres-nd-cpl-polarise']),
                'highlight' => false,
            ],
            [
                'title' => 'MATTE BOX',
                'count' => 88,
                'icon' => asset('images/camera/icons/cat_mattebox.svg'),
                'url' => route('shop.index', ['category' => 'matte-box']),
                'highlight' => false,
            ],
            [
                'title' => 'ACCESSOIRES INSTA360',
                'count' => 87,
                'icon' => asset('images/camera/icons/cat_insta360.svg'),
                'url' => route('shop.index', ['category' => 'accessoires-insta360']),
                'highlight' => false,
            ],
            [
                'title' => 'KIT DE NETTOYAGE',
                'count' => 7,
                'icon' => asset('images/camera/icons/cat_cleaning.svg'),
                'url' => route('shop.index', ['category' => 'kit-de-nettoyage']),
                'highlight' => false,
            ],
            [
                'title' => 'OCCASION',
                'count' => 5,
                'icon' => asset('images/camera/icons/cat_occasion.svg'),
                'url' => route('shop.index', ['category' => 'occasion']),
                'highlight' => true,
            ],
            [
                'title' => 'TRÉPIEDS',
                'count' => 97,
                'icon' => asset('images/camera/icons/cat_tripod.svg'),
                'url' => route('shop.index', ['category' => 'trepieds']),
                'highlight' => false,
            ],
            [
                'title' => 'BATTERIE / CHARGEUR',
                'count' => 110,
                'icon' => asset('images/camera/icons/cat_battery.svg'),
                'url' => route('shop.index', ['category' => 'batterie-chargeur']),
                'highlight' => false,
            ],
            [
                'title' => 'SACS DE CAMÉRA',
                'count' => 36,
                'icon' => asset('images/camera/icons/cat_bag.svg'),
                'url' => route('shop.index', ['category' => 'sacs-de-camera']),
                'highlight' => false,
            ],
            [
                'title' => 'OBJECTIFS',
                'count' => 34,
                'icon' => asset('images/camera/icons/cat_lens.svg'),
                'url' => route('shop.index', ['category' => 'objectifs']),
                'highlight' => false,
            ],
            [
                'title' => 'CAMÉRAS & HYBRIDES',
                'count' => 81,
                'icon' => asset('images/camera/icons/cat_camera.svg'),
                'url' => route('shop.index', ['category' => 'camera']),
                'highlight' => false,
            ],
        ];

        // 3. Featured Flagship Products ("Notre sélection de produits" matching Screenshot 3)
        $featuredIds = [594, 45, 374, 36, 18, 2, 7, 14];
        $featuredProducts = Product::where('status', 'active')
            ->whereIn('id', $featuredIds)
            ->orderByRaw('FIELD(id, 594, 45, 374, 36, 18, 2, 7, 14)')
            ->get();

        if ($featuredProducts->count() < 8) {
            $extra = Product::where('status', 'active')
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->whereNotIn('id', $featuredProducts->pluck('id'))
                ->take(8 - $featuredProducts->count())
                ->get();
            $featuredProducts = $featuredProducts->merge($extra);
        }

        // 4. New Arrivals ("Nouvelle Arrivage")
        $newArrivalIds = [251, 239, 129, 130, 126, 127];
        $newArrivalProducts = Product::where('status', 'active')
            ->whereIn('id', $newArrivalIds)
            ->orderByRaw('FIELD(id, 251, 239, 129, 130, 126, 127)')
            ->get();

        if ($newArrivalProducts->count() < 6) {
            $extraNew = Product::where('status', 'active')
                ->whereNotIn('id', $newArrivalProducts->pluck('id'))
                ->latest()
                ->take(6 - $newArrivalProducts->count())
                ->get();
            $newArrivalProducts = $newArrivalProducts->merge($extraNew);
        }

        // 5. Occasion Products ("Coin Occasion") - ONLY products of this category
        $occasionProducts = Product::where('status', 'active')
            ->where(function ($q) {
                $q->whereHas('productCategory', function ($c) {
                    $c->where('slug', 'occasion');
                })->orWhere('name', 'like', '%(occasion)%')
                  ->orWhere('name', 'like', '%occasion%');
            })
            ->orderByRaw('FIELD(id, 71, 31, 9, 12, 84) DESC')
            ->get();

        // General categories for navbar / footer
        $allCategories = Category::where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get();

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
