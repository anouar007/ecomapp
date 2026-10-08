<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', setting('app_name', 'Speed Platform'))</title>
    <meta name="description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta name="keywords" content="@yield('meta_keywords', setting('app_name', 'boutique') . ', e-commerce, Maroc, acheter en ligne, livraison Maroc')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="author" content="{{ setting('app_name', 'Speed Platform') }}">
    <meta name="developer" content="Elegant Boost (https://elegantboost.com/)">
    <meta name="designer" content="Elegant Boost">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Preconnect to external resources for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Favicon -->
    @if(setting('app_logo'))
        <link rel="icon" href="{{ asset('storage/' . setting('app_logo')) }}" type="image/x-icon">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . setting('app_logo')) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('meta_type', 'website')">
    <meta property="og:site_name" content="{{ setting('app_name', 'WINA SHOP') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', setting('app_name', 'WINA SHOP') . ' — Matériel Photo, Vidéo & Caméras au Maroc')">
    <meta property="og:description" content="@yield('meta_description', setting('app_description', 'Votre référence au Maroc pour le matériel photo & vidéo professionnel — Caméras Sony, Canon, drones & stabilisateurs DJI, optiques et éclairage studio.'))">
    <meta property="og:image" content="@yield('meta_image', setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/hero_cinema_rig.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ setting('language', 'fr') === 'ar' ? 'ar_MA' : 'fr_MA' }}">
    <meta property="og:updated_time" content="{{ now()->toIso8601String() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('meta_title', setting('app_name', 'WINA SHOP') . ' — Matériel Photo, Vidéo & Caméras au Maroc')">
    <meta name="twitter:description" content="@yield('meta_description', setting('app_description', 'Votre référence au Maroc pour le matériel photo & vidéo professionnel — Caméras Sony, Canon, drones & stabilisateurs DJI, optiques et éclairage studio.'))">
    <meta name="twitter:image" content="@yield('meta_image', setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/hero_cinema_rig.jpg'))">    
    <meta name="twitter:site" content="@yield('twitter_site', '@winashop.ma')">

    <!-- Additional Page-Specific SEO Meta (e.g. product:price) -->
    @yield('extra_meta')
    
    <!-- JSON-LD Structured Data Schema -->
    @yield('json_ld')
    @hasSection('json_ld')
    @else
    {{-- Global Default Organization & WebSite Schema --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "{{ setting('company_name', setting('app_name', 'WINA SHOP')) }}",
      "url": "{{ url('/') }}",
      "logo": "{{ setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/logo.png') }}",
      "description": "{{ addslashes(setting('app_description', 'Votre référence au Maroc pour le matériel photo & vidéo professionnel — Caméras Sony, Canon, drones DJI, stabilisateurs et éclairage studio.')) }}",
      @if(setting('company_phone'))
      "telephone": "{{ setting('company_phone') }}",
      @endif
      @if(setting('company_email'))
      "email": "{{ setting('company_email') }}",
      @endif
      @if(setting('company_address'))
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ setting('company_address') }}",
        "addressLocality": "{{ setting('company_city', 'Casablanca') }}",
        "addressCountry": "MA"
      },
      @endif
      "sameAs": [
        @php
          $socials = array_values(array_filter([
            setting('social_facebook', 'https://www.facebook.com/WinaShop.0629035777'),
            setting('social_instagram', 'https://www.instagram.com/winashop.ma/'),
            setting('social_twitter'),
            setting('social_linkedin'),
            setting('social_whatsapp') ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', setting('social_whatsapp')) : 'https://wa.me/212629035777',
          ]));
        @endphp
        @foreach($socials as $idx => $soc)
          "{{ $soc }}"@if(!$loop->last),@endif
        @endforeach
      ]
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "{{ setting('app_name', 'WINA SHOP') }}",
      "url": "{{ url('/') }}",
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "{{ route('shop.index') }}?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    @endif
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}?v={{ file_exists(public_path('css/frontend.css')) ? filemtime(public_path('css/frontend.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('css/camera-theme.css') }}?v={{ file_exists(public_path('css/camera-theme.css')) ? filemtime(public_path('css/camera-theme.css')) : time() }}">
    <!-- Custom Head Codes -->
    @php
        $headCodes = \App\Models\CustomCode::where('is_active', true)
            ->where('position', 'head')
            ->orderBy('priority', 'desc')
            ->get();
    @endphp
    @foreach($headCodes as $code)
        @if($code->type == 'css')
            <style>{!! $code->content !!}</style>
        @elseif($code->type == 'js')
            <script>{!! $code->content !!}</script>
        @else
            {!! $code->content !!}
        @endif
    @endforeach
</head>
<body>
    <!-- Custom Body Start Codes -->
    @php
        $bodyStartCodes = \App\Models\CustomCode::where('is_active', true)
            ->where('position', 'body_start')
            ->orderBy('priority', 'desc')
            ->get();
    @endphp
    @foreach($bodyStartCodes as $code)
        {!! $code->content !!}
    @endforeach

    <!-- Top Announcement Bar -->
    <div class="header-top-bar py-1 d-none d-md-block" style="background: #ffffff; color: #1e293b; font-size: 0.75rem; border-bottom: 1px solid #eef2f6;">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('shop.index') }}" class="text-secondary text-decoration-none fw-semibold" style="letter-spacing: 0.5px; font-size: 0.72rem;">ADD A MENU</a>
            </div>
            <div class="fw-bold text-dark mx-auto" style="letter-spacing: 0.8px; font-size: 0.75rem;">
                LIVRAISON RAPIDE PARTOUT AU MAROC
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('lang.switch', 'fr') }}" class="text-secondary text-decoration-none fw-semibold {{ app()->getLocale() === 'fr' ? 'text-danger' : '' }}" style="font-size: 0.72rem;">FR</a>
                <span class="text-secondary opacity-50">|</span>
                <a href="{{ route('lang.switch', 'ar') }}" class="text-secondary text-decoration-none fw-semibold {{ app()->getLocale() === 'ar' ? 'text-danger' : '' }}" style="font-size: 0.72rem;">AR</a>
            </div>
        </div>
    </div>

    <!-- Main Header / Navbar (Single Clean Row) -->
    <div class="header-main bg-white shadow-sm w-100" style="z-index: 1040; border-bottom: 1px solid #f1f3f5;">
        <div class="container py-2 py-lg-3">
            <div class="d-flex align-items-center justify-content-between gap-3 gap-xl-4">
                
                <!-- Left: Logo + Categories Button + Navigation Links on its right -->
                <div class="d-flex align-items-center gap-2 gap-lg-3 gap-xl-4 flex-nowrap">
                    <!-- Brand Logo (Enlarged) -->
                    <a class="navbar-brand m-0 p-0 d-flex align-items-center me-1 me-lg-2" href="{{ url('/') }}" style="flex-shrink: 0;" title="{{ setting('app_name', 'WINA SHOP') }}">
                        <img src="{{ asset('images/camera/logo.png') }}" alt="{{ setting('app_name', 'WINA SHOP') }}" class="brand-logo-img" style="height: 78px; max-height: 85px; width: auto; max-width: 220px; object-fit: contain;">
                    </a>

                    @php
                        $navCategories = \Illuminate\Support\Facades\Cache::remember('frontend_nav_categories', 3600, function() {
                            return \App\Models\Category::where('status', 'active')
                                ->where('slug', '!=', 'general')
                                ->withCount(['products' => function($q) {
                                    $q->where('status', 'active');
                                }])
                                ->orderBy('sort_order', 'asc')
                                ->get();
                        });

                        $catIcons = [
                            'camera'                      => 'fas fa-camera',
                            'cameras-hybrides'            => 'fas fa-video',
                            'objectifs'                   => 'fas fa-circle-notch',
                            'objectifs-optiques'          => 'fas fa-circle-notch',
                            'lumieres-materiel-de-studio' => 'fas fa-lightbulb',
                            'eclairage-studio'            => 'fas fa-lightbulb',
                            'son'                         => 'fas fa-microphone-lines',
                            'audio-micros-sans-fil'       => 'fas fa-microphone',
                            'stabilisateurs'              => 'fas fa-arrows-to-dot',
                            'stabilisateurs-gimbals'      => 'fas fa-arrows-to-dot',
                            'drones-cine'                 => 'fas fa-helicopter',
                            'sacs-de-camera'              => 'fas fa-bag-shopping',
                            'trepieds'                    => 'fas fa-braille',
                            'batterie-chargeur'           => 'fas fa-bolt',
                            'carte-memoire-lecteur'       => 'fas fa-sd-card',
                            'accessoires'                 => 'fas fa-sliders',
                            'materiel-de-podcast'         => 'fas fa-podcast',
                            'filtres-nd-cpl-polarise'     => 'fas fa-circle-half-stroke',
                            'matte-box'                   => 'fas fa-cube',
                            'accessoires-insta360'        => 'fas fa-camera-rotate',
                            'kit-de-nettoyage'            => 'fas fa-spray-can-sparkles',
                            'occasion'                    => 'fas fa-tag',
                        ];
                    @endphp

                    <!-- Red Categories Button (Immediately after logo) -->
                    <div class="dropdown header-categories-dropdown d-none d-lg-inline-block">
                        <button class="btn btn-categories-red text-white fw-bold d-flex align-items-center gap-2" 
                                type="button" 
                                id="headerCategoriesBtn" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fas fa-bars"></i>
                            <span>CATÉGORIES</span>
                            <i class="fas fa-chevron-down ms-1" style="font-size: 10px;"></i>
                        </button>

                        <!-- Pro Categories Mega Dropdown -->
                        <div class="dropdown-menu categories-mega-menu shadow-lg border-0 p-0" aria-labelledby="headerCategoriesBtn">
                            <!-- Mega Menu Header -->
                            <div class="categories-menu-header d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                <span class="d-flex align-items-center gap-2 text-dark fw-bold text-uppercase" style="font-size: 11.5px; letter-spacing: 0.8px;">
                                    <i class="fas fa-layer-group text-danger"></i>
                                    <span>{{ __('Rayons & Matériel Pro') }}</span>
                                </span>
                                <a href="{{ route('shop.index') }}" class="text-danger fw-bold text-decoration-none d-flex align-items-center gap-1 hover-underline" style="font-size: 12px;">
                                    <span>{{ __('Tout le catalogue') }}</span>
                                    <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                </a>
                            </div>

                            <!-- Mega Menu Body: 3 Clean Balanced Columns Grid (No scroll) -->
                            <div class="categories-menu-grid p-3">
                                <div class="row g-2">
                                    @php
                                        $perColumn = ceil($navCategories->count() / 3);
                                        $chunks = $navCategories->chunk($perColumn);
                                    @endphp
                                    @foreach($chunks as $columnCategories)
                                        <div class="col-4">
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($columnCategories as $cat)
                                                    @php
                                                        $catImg = null;
                                                        $catIconClass = null;

                                                        // Check if user uploaded a custom image from dashboard
                                                        if (!empty($cat->image) && (str_starts_with($cat->image, 'categories/') || str_starts_with($cat->image, 'storage/') || str_starts_with($cat->image, 'http'))) {
                                                            $catImg = $cat->image_url;
                                                        }
                                                        // Check if icon field points to an image/svg file
                                                        elseif (!empty($cat->icon) && (str_ends_with($cat->icon, '.svg') || str_ends_with($cat->icon, '.png') || str_ends_with($cat->icon, '.webp') || str_contains($cat->icon, '/'))) {
                                                            $catImg = asset($cat->icon);
                                                        }
                                                        // Check if icon field is a FontAwesome class from dashboard
                                                        elseif (!empty($cat->icon)) {
                                                            $rawIcon = trim($cat->icon);
                                                            $catIconClass = str_starts_with($rawIcon, 'fa') ? $rawIcon : 'fas fa-' . $rawIcon;
                                                        }
                                                        // Fallback to image if set
                                                        elseif (!empty($cat->image_url)) {
                                                            $catImg = $cat->image_url;
                                                        }
                                                        // Fallback to preset or default icon
                                                        else {
                                                            $catIconClass = $catIcons[$cat->slug] ?? 'fas fa-folder';
                                                        }
                                                    @endphp
                                                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" 
                                                       class="cat-mega-item d-flex align-items-center justify-content-between text-decoration-none">
                                                        <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                            <span class="cat-slot-box">
                                                                @if($catImg)
                                                                    <img src="{{ $catImg }}" alt="{{ $cat->name }}" class="cat-slot-media" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                                                    <i class="fas fa-folder cat-slot-icon" style="display: none;"></i>
                                                                @else
                                                                    <i class="{{ $catIconClass }} cat-slot-icon"></i>
                                                                @endif
                                                            </span>
                                                            <span class="cat-mega-name text-truncate">{{ $cat->name }}</span>
                                                        </div>
                                                        <span class="badge cat-mega-count rounded-pill">{{ $cat->products_count }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Links directly on the right of the red categories button -->
                    <nav class="d-none d-lg-flex align-items-center gap-3 gap-xl-4 ms-1 flex-nowrap">
                        <a href="{{ route('home') }}" class="nav-link-item text-decoration-none fw-bold {{ request()->routeIs('home') ? 'text-dark active' : 'text-secondary' }}" style="font-size: 13.5px; letter-spacing: 0.5px; white-space: nowrap;">ACCUEIL</a>
                        <a href="{{ route('shop.index') }}" class="nav-link-item text-decoration-none fw-bold {{ request()->routeIs('shop.*') && !request('category') ? 'text-dark active' : 'text-secondary' }}" style="font-size: 13.5px; letter-spacing: 0.5px; white-space: nowrap;">BOUTIQUE</a>
                        <a href="{{ route('about') }}" class="nav-link-item text-decoration-none fw-bold {{ request()->routeIs('about') ? 'text-dark active' : 'text-secondary' }}" style="font-size: 13.5px; letter-spacing: 0.5px; white-space: nowrap;">À PROPOS</a>
                        <a href="{{ route('contact') }}" class="nav-link-item text-decoration-none fw-bold {{ request()->routeIs('contact') ? 'text-dark active' : 'text-secondary' }}" style="font-size: 13.5px; letter-spacing: 0.5px; white-space: nowrap;">CONTACT</a>
                    </nav>
                </div>

                <!-- Right: Search Bar + Cart Icon -->
                <div class="d-flex align-items-center gap-3 gap-xl-4 flex-grow-1 justify-content-end" style="max-width: 480px;">
                    <!-- Search Input (Pill shape #f1f3f5) -->
                    <form action="{{ route('shop.index') }}" method="GET" class="header-search-wrap w-100 d-none d-md-block position-relative" style="max-width: 380px;" id="headerSearchForm" autocomplete="off">
                        <div class="position-relative d-flex align-items-center" style="background: #f1f3f5; border-radius: 9999px; padding: 0 16px; height: 44px;">
                            <i class="fas fa-search text-muted me-2" style="font-size: 13px;"></i>
                            <input class="form-control border-0 bg-transparent shadow-none p-0 header-search-input" 
                                   type="search" 
                                   name="q" 
                                   id="headerSearchInput" 
                                   placeholder="Rechercher des produits..." 
                                   aria-label="{{ __('Search') }}" 
                                   value="{{ request('q') }}" 
                                   autocomplete="off" 
                                   spellcheck="false"
                                   style="font-size: 13px; color: #334155;">
                            <button class="btn btn-search-clear d-none p-0 border-0 bg-transparent text-muted" type="button" id="headerSearchClear">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Live Search Floating Dropdown -->
                        <div class="header-search-dropdown shadow-lg d-none" id="headerSearchDropdown" role="region" aria-label="{{ __('Search Results') }}">
                            <div class="search-quick-tags p-3 border-bottom" id="headerSearchTags">
                                <div class="search-section-label">
                                    <i class="fas fa-fire me-1 text-danger"></i> {{ strtoupper(__('Frequent Searches')) }}
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <button type="button" class="search-tag-chip" data-search="Sony Alpha">Sony Alpha</button>
                                    <button type="button" class="search-tag-chip" data-search="DJI Osmo">DJI Osmo</button>
                                    <button type="button" class="search-tag-chip" data-search="Godox">Godox</button>
                                    <button type="button" class="search-tag-chip" data-search="Rode">Røde</button>
                                    <button type="button" class="search-tag-chip" data-search="Insta360">Insta360</button>
                                </div>
                            </div>
                            <div class="search-results-list" id="headerSearchResultsList"></div>
                            <div class="search-empty-state text-center py-4 px-3 d-none" id="headerSearchEmpty">
                                <div class="search-empty-icon mb-2">
                                    <i class="fas fa-search-minus fa-2x text-muted opacity-50"></i>
                                </div>
                                <div class="fw-bold text-dark mb-1">{{ __('No product found') }}</div>
                            </div>
                            <div class="search-dropdown-footer p-2 text-center border-top d-none" id="headerSearchFooter">
                                <a href="#" class="search-view-all-link" id="headerSearchViewAll">
                                    <span>{{ __('View all results') }}</span>
                                    <span class="search-count-pill badge bg-danger ms-1" id="headerSearchCountBadge">0</span>
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Mobile search icon button -->
                    <button class="bg-transparent border-0 p-1 text-dark d-md-none" type="button" id="mobileSearchTriggerBtn" title="{{ __('Search') }}" aria-label="{{ __('Search') }}">
                        <i class="fas fa-search" style="font-size: 19px;"></i>
                    </button>

                    <!-- Cart Icon with Red Badge -->
                    <div class="position-relative ms-1">
                        <button class="bg-transparent border-0 p-1 text-dark d-flex align-items-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart" title="{{ __('My Cart') }}" style="cursor: pointer;">
                            <i class="fas fa-shopping-bag" style="font-size: 22px;"></i>
                        </button>
                        @php
                            $headerInitialCartCount = array_sum(array_column(session('cart', []), 'quantity'));
                        @endphp
                        <span id="header-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white {{ $headerInitialCartCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem; padding: 2px 6px;">
                            {{ $headerInitialCartCount }}
                        </span>
                    </div>

                    <!-- Mobile Menu Toggler -->
                    <button class="navbar-toggler border-0 p-1 d-lg-none text-dark ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavDrawer" aria-controls="mobileNavDrawer" aria-label="Menu">
                        <i class="fas fa-bars" style="font-size: 20px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Offcanvas Drawer -->
    <div class="offcanvas offcanvas-start border-0 shadow-lg d-lg-none" tabindex="-1" id="mobileNavDrawer" aria-labelledby="mobileNavDrawerLabel" style="width: 310px;">
        <div class="offcanvas-header border-bottom py-3">
            <a href="{{ url('/') }}" class="navbar-brand m-0">
                <img src="{{ asset('images/camera/logo.png') }}" alt="{{ setting('app_name', 'WINA SHOP') }}" style="height: 48px; width: auto;">
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            <!-- Mobile Categories Red Button -->
            <div class="mb-3">
                <a href="{{ route('shop.index') }}" class="btn btn-categories-red text-white w-100 fw-bold d-flex align-items-center justify-content-between py-2 px-3">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fas fa-bars"></i>
                        <span>CATÉGORIES</span>
                    </span>
                    <i class="fas fa-chevron-right" style="font-size: 11px;"></i>
                </a>
            </div>

            <nav class="d-flex flex-column gap-2">
                <a href="{{ route('home') }}" class="nav-link py-2 px-3 rounded fw-bold text-dark text-decoration-none {{ request()->routeIs('home') ? 'bg-light text-danger' : '' }}">ACCUEIL</a>
                <a href="{{ route('shop.index') }}" class="nav-link py-2 px-3 rounded fw-bold text-dark text-decoration-none {{ request()->routeIs('shop.*') && !request('category') ? 'bg-light text-danger' : '' }}">BOUTIQUE</a>
                <a href="{{ route('about') }}" class="nav-link py-2 px-3 rounded fw-bold text-dark text-decoration-none {{ request()->routeIs('about') ? 'bg-light text-danger' : '' }}">À PROPOS</a>
                <a href="{{ route('contact') }}" class="nav-link py-2 px-3 rounded fw-bold text-dark text-decoration-none {{ request()->routeIs('contact') ? 'bg-light text-danger' : '' }}">CONTACT</a>
            </nav>

            <div class="mt-4 pt-3 border-top">
                <div class="small fw-bold text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.5px;">Rayons & Catégories</div>
                <div class="d-flex flex-column gap-1">
                    @foreach($navCategories as $cat)
                        @php
                            $catImg = null;
                            $catIconClass = null;

                            if (!empty($cat->image) && (str_starts_with($cat->image, 'categories/') || str_starts_with($cat->image, 'storage/') || str_starts_with($cat->image, 'http'))) {
                                $catImg = $cat->image_url;
                            }
                            elseif (!empty($cat->icon) && (str_ends_with($cat->icon, '.svg') || str_ends_with($cat->icon, '.png') || str_ends_with($cat->icon, '.webp') || str_contains($cat->icon, '/'))) {
                                $catImg = asset($cat->icon);
                            }
                            elseif (!empty($cat->icon)) {
                                $rawIcon = trim($cat->icon);
                                $catIconClass = str_starts_with($rawIcon, 'fa') ? $rawIcon : 'fas fa-' . $rawIcon;
                            }
                            elseif (!empty($cat->image_url)) {
                                $catImg = $cat->image_url;
                            }
                            else {
                                $catIconClass = $catIcons[$cat->slug] ?? 'fas fa-folder';
                            }
                        @endphp
                        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="cat-mega-item d-flex align-items-center justify-content-between py-1 px-2 text-decoration-none rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                <span class="cat-slot-box">
                                    @if($catImg)
                                        <img src="{{ $catImg }}" alt="{{ $cat->name }}" class="cat-slot-media" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                        <i class="fas fa-folder cat-slot-icon" style="display: none;"></i>
                                    @else
                                        <i class="{{ $catIconClass }} cat-slot-icon"></i>
                                    @endif
                                </span>
                                <span class="cat-mega-name text-truncate">{{ $cat->name }}</span>
                            </div>
                            <span class="badge cat-mega-count rounded-pill">{{ $cat->products_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <main>
        @yield('content')
    </main>

    <!-- Pro Full-Screen Mobile Search Overlay -->
    <div class="pro-mobile-search-overlay" id="proMobileSearchOverlay" role="dialog" aria-modal="true" aria-label="{{ __('Search') }}">
        <!-- Top Sticky Header -->
        <div class="pro-mobile-search-header">
            <form action="{{ route('shop.index') }}" method="GET" class="pro-mobile-search-form" id="proMobileSearchForm" autocomplete="off">
                <div class="pro-search-bar">
                    <span class="pro-search-icon">
                        <i class="fas fa-search pro-icon-default"></i>
                        <span class="spinner-border spinner-border-sm text-danger pro-icon-spinner d-none" role="status" aria-hidden="true"></span>
                    </span>
                    <input type="search" 
                           name="q" 
                           id="proMobileSearchInput" 
                           class="pro-search-input" 
                           placeholder="{{ __('Search cameras, lenses, mics...') }}" 
                           aria-label="{{ __('Search') }}"
                           autocomplete="off" 
                           autocapitalize="off" 
                           spellcheck="false">
                    <button type="button" class="pro-search-clear d-none" id="proMobileSearchClear" aria-label="{{ __('Clear') }}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </form>
            <button type="button" class="pro-mobile-search-close" id="proMobileSearchCloseBtn" aria-label="{{ __('Cancel') }}">
                {{ __('Cancel') }}
            </button>
        </div>

        <!-- Scrollable Content Body -->
        <div class="pro-mobile-search-body" id="proMobileSearchBody">
            <!-- 1. Default Discovery State (When query is empty) -->
            <div class="pro-search-suggestions" id="proMobileSearchSuggestions">
                <div class="pro-search-section">
                    <div class="pro-section-title">
                        <i class="fas fa-fire text-danger me-1"></i> {{ strtoupper(__('Frequent Searches')) }}
                    </div>
                    <div class="pro-tags-grid">
                        <button type="button" class="pro-tag-chip" data-query="DJI Mic 2">
                            <i class="fas fa-search"></i> DJI Mic 2
                        </button>
                        <button type="button" class="pro-tag-chip" data-query="Sony Alpha 7">
                            <i class="fas fa-search"></i> Sony Alpha 7
                        </button>
                        <button type="button" class="pro-tag-chip" data-query="Blackmagic 6K">
                            <i class="fas fa-search"></i> Blackmagic 6K
                        </button>
                        <button type="button" class="pro-tag-chip" data-query="Objectif 24-70">
                            <i class="fas fa-search"></i> {{ app()->getLocale() === 'ar' ? 'عدسة 24-70' : 'Objectif 24-70' }}
                        </button>
                        <button type="button" class="pro-tag-chip" data-query="Stabilisateur RS 4">
                            <i class="fas fa-search"></i> {{ app()->getLocale() === 'ar' ? 'مانع اهتزاز RS 4' : 'Stabilisateur RS 4' }}
                        </button>
                        <button type="button" class="pro-tag-chip" data-query="Mavic 3 Cine">
                            <i class="fas fa-search"></i> Mavic 3 Cine
                        </button>
                    </div>
                </div>

                <div class="pro-search-section mt-4">
                    <div class="pro-section-title">
                        <i class="fas fa-th-large text-danger me-1"></i> {{ strtoupper(__('Sections & Equipment')) }}
                    </div>
                    <div class="pro-cats-grid">
                        @foreach($navCategories as $cat)
                            @php $icon = $catIcons[$cat->slug] ?? 'fa-camera'; @endphp
                            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="pro-cat-card">
                                <span class="pro-cat-card-icon" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                    @if($cat->image)
                                        <img src="{{ $cat->thumbnail }}" alt="{{ $cat->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                        <i class="fas {{ $icon }}" style="display: none;"></i>
                                    @else
                                        <i class="fas {{ $icon }}"></i>
                                    @endif
                                </span>
                                <div class="pro-cat-card-info">
                                    <span class="pro-cat-card-name">{{ $cat->name }}</span>
                                    <span class="pro-cat-card-count">{{ $cat->products_count }} {{ __('refs.') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 2. Dynamic Live Results List -->
            <div class="pro-search-results d-none" id="proMobileSearchResults"></div>

            <!-- 3. Empty State -->
            <div class="pro-search-empty d-none" id="proMobileSearchEmpty">
                <div class="pro-empty-icon">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h6 class="pro-empty-title">{{ __('No product found') }}</h6>
                <p class="pro-empty-subtitle">
                    {{ __('No results match') }} « <span class="pro-empty-query text-danger fw-bold"></span> »
                </p>
                <div class="pro-empty-tags-hint">
                    <span class="text-muted small d-block mb-2">{{ __('Popular suggestions:') }}</span>
                    <div class="pro-tags-grid justify-content-center">
                        <button type="button" class="pro-tag-chip" data-query="Sony">Sony</button>
                        <button type="button" class="pro-tag-chip" data-query="Mic">{{ app()->getLocale() === 'ar' ? 'ميكروفونات' : 'Micros' }}</button>
                        <button type="button" class="pro-tag-chip" data-query="Objectif">{{ app()->getLocale() === 'ar' ? 'عدسات' : 'Objectifs' }}</button>
                        <button type="button" class="pro-tag-chip" data-query="Drone">{{ app()->getLocale() === 'ar' ? 'طائرات' : 'Drones' }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom View All Bar -->
        <div class="pro-mobile-search-footer d-none" id="proMobileSearchFooter">
            <a href="#" class="pro-search-view-all-btn" id="proMobileSearchViewAll">
                <span>{{ __('View all results') }}</span>
                <span class="badge bg-white text-danger rounded-pill ms-2 fw-bold" id="proMobileSearchCount">0</span>
                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left ms-2' : 'fa-arrow-right ms-2' }}"></i>
            </a>
        </div>
    </div>

    <!-- Offcanvas Mini Cart -->
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="miniCart" aria-labelledby="miniCartLabel" style="width: 450px; background: #f8fafc;">
        <div class="offcanvas-header bg-white border-bottom py-3">
            <h5 class="offcanvas-title fw-bold font-heading" id="miniCartLabel">
                <i class="fas fa-shopping-bag me-2 text-primary"></i>{{ __('My Cart') }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column h-100">
            <div class="flex-grow-1 overflow-auto p-4" id="mini-cart-items">
                @php $total = 0; @endphp
                @forelse(session('cart', []) as $id => $details)
                    @php $total += $details['price'] * $details['quantity']; @endphp
                    <div class="cart-item bg-white p-3 rounded-4 shadow-sm mb-3 position-relative border border-light" id="cart-item-{{ $id }}">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 {{ app()->getLocale() === 'ar' ? 'ms-3' : 'me-3' }} position-relative">
                                @php
                                    $miniImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/'))) 
                                        ? asset(ltrim($details['image'], '/')) 
                                        : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                                @endphp
                                <img src="{{ $miniImg }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover" style="width: 80px; height: 80px;">
                                <span class="position-absolute top-0 {{ app()->getLocale() === 'ar' ? 'end-0' : 'start-0' }} translate-middle badge rounded-pill bg-light text-dark border shadow-sm" style="font-size: 0.7rem;">x{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold mb-1 text-truncate {{ app()->getLocale() === 'ar' ? 'ps-4' : 'pe-4' }}" title="{{ $details['name'] }}">{{ $details['name'] }}</h6>
                                <p class="mb-2 text-muted small">{{ $details['category_name'] ?? __('Product') }}</p>
                                
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <span class="text-primary fw-bold" style="font-size: 1.1rem;">{{ currency($details['price']) }}</span>
                                    
                                    <div class="quantity-control bg-light rounded-pill d-flex align-items-center px-1 border">
                                        <button class="btn btn-sm btn-link text-dark text-decoration-none p-1 border-0" onclick="updateQty({{ $id }}, {{ $details['quantity'] - 1 }})">
                                            <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="{{ $details['quantity'] }}" readonly style="width: 30px;">
                                        <button class="btn btn-sm btn-link text-dark text-decoration-none p-1 border-0" onclick="updateQty({{ $id }}, {{ $details['quantity'] + 1 }})">
                                            <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="mini-cart-item-remove position-absolute {{ app()->getLocale() === 'ar' ? 'start-0' : 'end-0' }}" onclick="removeItem({{ $id }})" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @empty
                    <div class="text-center py-5 mt-5">
                        <div class="mb-4 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
                            <i class="fas fa-shopping-basket fa-3x text-muted opacity-25"></i>
                        </div>
                        <h5 class="fw-bold text-dark">{{ __('Your cart is empty') }}</h5>
                        <p class="text-muted small mb-4">{{ __("You haven't added anything yet.") }}</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 shadow-sm">{{ __('Start Shopping') }}</a>
                    </div>
                @endforelse
            </div>
            
            @if(count(session('cart', [])) > 0)
            <div class="border-top p-4 bg-white mt-auto shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <span class="text-muted small text-uppercase fw-bold ls-1">{{ __('Subtotal') }}</span>
                    <span class="h4 fw-bold text-dark mb-0 ls-tight" id="mini-cart-total">{{ currency($total) }}</span>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-between align-items-center px-4">
                        <span>{{ __('Checkout') }}</span>
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="btn btn-light py-2 rounded-pill fw-bold text-muted small">
                        {{ __('View cart') }}
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Floating Checkout Button (appears on cart addition and stays on screen) --}}
    @if(!request()->routeIs('checkout.*'))
    @php
        $cartSession = session('cart', []);
        $cartInitialCount = array_sum(array_column($cartSession, 'quantity'));
    @endphp
    <style>
        .floating-checkout-wrap {
            position: fixed !important;
            bottom: 24px;
            right: 24px;
            z-index: 99999 !important;
            opacity: 0 !important;
            visibility: hidden !important;
            display: none !important;
            transform: translateY(20px) scale(0.92);
            transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                        visibility 0.35s;
            pointer-events: none !important;
        }
        .floating-checkout-wrap.is-visible {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) scale(1) !important;
            pointer-events: auto !important;
            display: inline-flex !important;
        }

        /* Elevate #miniCart above all floating buttons */
        #miniCart.offcanvas,
        .offcanvas.show {
            z-index: 1000000 !important;
        }
        .offcanvas-backdrop,
        .offcanvas-backdrop.show {
            z-index: 999995 !important;
        }

        /* Completely hide floating checkout and WhatsApp when miniCart or offcanvas is open */
        body.mini-cart-open .floating-checkout-wrap,
        body.mini-cart-open .whatsapp-float,
        body:has(#miniCart.show) .floating-checkout-wrap,
        body:has(#miniCart.show) .whatsapp-float,
        body:has(#miniCart.showing) .floating-checkout-wrap,
        body:has(#miniCart.showing) .whatsapp-float,
        body:has(.offcanvas.show) .floating-checkout-wrap,
        body:has(.offcanvas.show) .whatsapp-float {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            display: none !important;
            transform: translateY(20px) scale(0.85) !important;
        }
        body.has-whatsapp-float .floating-checkout-wrap.is-visible,
        body:has(.whatsapp-float) .floating-checkout-wrap.is-visible {
            bottom: 96px !important;
        }
        html[dir="rtl"] .floating-checkout-wrap {
            right: auto !important;
            left: 24px !important;
        }
        html[dir="rtl"] body.has-whatsapp-float .floating-checkout-wrap.is-visible,
        html[dir="rtl"] body:has(.whatsapp-float) .floating-checkout-wrap.is-visible {
            bottom: 24px !important;
            left: 24px !important;
        }
        @media (max-width: 768px) {
            .floating-checkout-wrap {
                bottom: calc(22px + env(safe-area-inset-bottom, 0px)) !important;
                right: 14px !important;
                left: auto !important;
                z-index: 99999 !important;
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }
            .floating-checkout-wrap.is-visible {
                display: inline-flex !important;
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
                transform: translateY(0) scale(1) !important;
            }
            body.has-whatsapp-float .floating-checkout-wrap.is-visible,
            body:has(.whatsapp-float) .floating-checkout-wrap.is-visible {
                bottom: calc(84px + env(safe-area-inset-bottom, 0px)) !important;
            }
            html[dir="rtl"] .floating-checkout-wrap {
                left: 14px !important;
                right: auto !important;
            }
            html[dir="rtl"] body.has-whatsapp-float .floating-checkout-wrap.is-visible,
            html[dir="rtl"] body:has(.whatsapp-float) .floating-checkout-wrap.is-visible {
                bottom: calc(22px + env(safe-area-inset-bottom, 0px)) !important;
                left: 14px !important;
            }
            .floating-checkout-btn {
                padding: 10px 16px 10px 12px !important;
                gap: 10px !important;
                box-shadow: 0 10px 28px rgba(0, 0, 0, 0.55), 0 0 18px rgba(220, 38, 38, 0.45) !important;
            }
            .floating-checkout-label {
                font-size: 0.88rem !important;
                font-weight: 700 !important;
            }
        }
    </style>
    <div id="floatingCheckoutWrap" class="floating-checkout-wrap {{ $cartInitialCount > 0 ? 'is-visible' : '' }}" aria-live="polite">
        <a href="{{ route('checkout.index') }}" class="floating-checkout-btn" id="floatingCheckoutBtn" title="{{ __('Checkout') }}">
            <div class="floating-checkout-icon-box">
                <i class="fas fa-shopping-bag"></i>
                <span class="floating-checkout-badge" id="floatingCheckoutCount">{{ $cartInitialCount }}</span>
            </div>
            <span class="floating-checkout-label">{{ __('Checkout') }}</span>
            <span class="floating-checkout-arrow-box">
                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
            </span>
        </a>
    </div>
    @endif

    {{-- Always-On Floating WhatsApp Button (All Devices & Pages) --}}
    @php
        $waRawPhone = setting('social_whatsapp', '0629035777');
        $waCleanPhone = preg_replace('/[^0-9]/', '', $waRawPhone);
        if (str_starts_with($waCleanPhone, '0')) {
            $waCleanPhone = '212' . substr($waCleanPhone, 1);
        } elseif (!str_starts_with($waCleanPhone, '212')) {
            $waCleanPhone = '212' . $waCleanPhone;
        }
    @endphp
    <a href="https://wa.me/{{ $waCleanPhone }}?text={{ urlencode('Bonjour Wina Shop, je souhaite me renseigner sur vos produits.') }}" 
       class="whatsapp-float" 
       target="_blank" 
       rel="noopener noreferrer" 
       aria-label="Contacter sur WhatsApp" 
       title="Contacter sur WhatsApp">
        <span class="whatsapp-float-pulse"></span>
        <span class="whatsapp-online-dot"></span>
        <svg viewBox="0 0 32 32" class="whatsapp-float-svg" width="30" height="30" fill="currentColor" aria-hidden="true">
            <path d="M16.002 0.007C7.168 0.007 0 7.175 0 16.008c0 2.825 0.738 5.578 2.141 7.999L0.086 31.914l8.13-2.052c2.342 1.282 4.978 1.956 7.786 1.956 8.834 0 16.002-7.168 16.002-16.01S24.836 0.007 16.002 0.007zm0 29.317c-2.484 0-4.912-0.669-7.037-1.936l-0.505-0.3-5.234 1.321 1.398-5.093-0.33-0.526C3.003 20.672 2.302 18.384 2.302 16.008c0-7.555 6.145-13.7 13.7-13.7 7.555 0 13.7 6.145 13.7 13.7 0 7.555-6.145 13.7-13.7 13.7zm7.51-10.252c-0.412-0.206-2.438-1.203-2.816-1.34-0.378-0.137-0.653-0.206-0.927 0.206s-1.065 1.34-1.305 1.615c-0.24 0.275-0.481 0.309-0.893 0.103-0.412-0.206-1.741-0.642-3.316-2.046-1.226-1.093-2.054-2.443-2.294-2.855-0.24-0.412-0.026-0.635 0.18-0.84 0.186-0.185 0.412-0.481 0.618-0.721 0.206-0.24 0.275-0.412 0.412-0.687 0.137-0.275 0.069-0.515-0.034-0.721s-0.927-2.233-1.27-3.057c-0.335-0.803-0.675-0.694-0.927-0.707l-0.79-0.014c-0.275 0-0.721 0.103-1.099 0.515s-1.443 1.409-1.443 3.435 1.477 3.985 1.683 4.26c0.206 0.275 2.907 4.439 7.042 6.225 0.984 0.425 1.753 0.679 2.352 0.869 0.988 0.314 1.888 0.27 2.6 0.164 0.794-0.119 2.438-0.996 2.781-1.958 0.344-0.962 0.344-1.786 0.24-1.958-0.103-0.172-0.378-0.275-0.79-0.481z"/>
        </svg>
        <span class="whatsapp-float-tooltip">
            <span>WhatsApp</span>
            <small>En ligne</small>
        </span>
    </a>

    <footer class="footer-modern">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <h5 class="fw-bold text-white mb-3 text-uppercase ls-1 d-flex align-items-center gap-2">
                        <span class="tally-dot"></span> {{ setting('app_name', 'WINA SHOP') }}
                    </h5>
                    <p class="small lh-lg mb-4 text-slate-400">
                        @if(app()->getLocale() === 'ar')
                            متجرك المرجعي في المغرب لمعدات التصوير الفوتوغرافي والفيديو الاحترافية: كاميرات، عدسات، طائرات درون ومثبتات DJI، إضاءة وصوتيات الاستوديو. مقرنا بالدار البيضاء وتوصيل سريع وموثوق في جميع أنحاء المغرب.
                        @else
                            Votre référence au Maroc pour le matériel photo & vidéo professionnel : appareils photo, caméras, objectifs, drones et stabilisateurs DJI, micros HF et éclairage studio. Showroom à Casablanca et livraison express partout au Maroc.
                        @endif
                    </p>
                    @php
                        $sfb  = setting('social_facebook',  'https://www.facebook.com/WinaShop.0629035777');
                        $stw  = setting('social_twitter',   '');
                        $sig  = setting('social_instagram', 'https://www.instagram.com/winashop.ma/');
                        $sli  = setting('social_linkedin',  '');
                        $swa  = setting('social_whatsapp',  '+212629035777');
                        // Only treat as valid if it's a real URL (not empty or bare '#')
                        $validUrl = fn($v) => $v && $v !== '#' && $v !== '/#';
                    @endphp
                    <div class="d-flex gap-3 flex-wrap">
                        @if($validUrl($sfb))
                        <a href="{{ $sfb }}" target="_blank" rel="noopener" class="footer-social-btn" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        @endif
                        @if($validUrl($stw))
                        <a href="{{ $stw }}" target="_blank" rel="noopener" class="footer-social-btn" title="Twitter / X">
                            <i class="fab fa-twitter"></i>
                        </a>
                        @endif
                        @if($validUrl($sig))
                        <a href="{{ $sig }}" target="_blank" rel="noopener" class="footer-social-btn" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if($validUrl($sli))
                        <a href="{{ $sli }}" target="_blank" rel="noopener" class="footer-social-btn" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        @endif
                        @if($validUrl($swa))
                        @php
                            $waFooterLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $swa);
                        @endphp
                        <a href="{{ $waFooterLink }}" target="_blank" rel="noopener" class="footer-social-btn footer-social-btn--wa" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        @endif
                        {{-- If none configured, show placeholder text --}}
                        @if(!$validUrl($sfb) && !$validUrl($stw) && !$validUrl($sig) && !$validUrl($sli) && !$validUrl($swa))
                        <span class="text-muted small fst-italic">{{ __('Follow us') }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase ls-1">{{ __('Shop') }}</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('shop.index') }}" class="footer-link small">{{ __('Featured Products') }}</a></li>
                        <li><a href="{{ route('about') }}" class="footer-link small">{{ __('About') }} {{ setting('app_name', 'WINA SHOP') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="footer-link small">{{ __('Contact & Showroom') }}</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase ls-1">{{ __('Customer Service') }}</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('contact') }}" class="footer-link small">{{ __('Request a Quote') }}</a></li>
                        <li><a href="{{ route('about') }}#garantie" class="footer-link small">{{ __('Warranty') }}</a></li>
                        <li><a href="{{ route('contact') }}#faq" class="footer-link small">{{ __('FAQ') }}</a></li>
                    </ul>
                </div>

            </div>
            
            <hr class="border-secondary opacity-25 my-5">
            
            <div class="row align-items-center">
                <div class="col-md-12 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-center mb-0">&copy; {{ date('Y') }} {{ setting('app_name', 'WINA SHOP') }}. {{ __('All rights reserved') }}. {{ app()->getLocale() === 'ar' ? 'تطوير' : 'Développé par' }} <a href="https://elegantboost.com/" target="_blank" class="text-white text-decoration-none fw-bold hover-primary transition-all">Elegant Boost</a>.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        @if(setting('frontend_enable_animations'))
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100
            });
        }
        @endif

        // Mini Cart & Cart Page Functions
        window.updateFloatingCheckout = function(count, total) {
            const wrap = document.getElementById('floatingCheckoutWrap');
            const badge = document.getElementById('floatingCheckoutCount');
            const numCount = parseInt(count) || 0;

            if (badge) {
                badge.textContent = numCount;
            }

            if (wrap) {
                if (numCount > 0) {
                    wrap.classList.add('is-visible');
                    wrap.style.display = 'inline-flex';
                    wrap.classList.add('pulse-anim');
                    setTimeout(() => wrap.classList.remove('pulse-anim'), 600);
                } else {
                    wrap.classList.remove('is-visible');
                    wrap.style.display = 'none';
                }
            }

            // Also keep top navigation cart badge updated
            if (typeof window.updateCartBadgeCount === 'function') {
                window.updateCartBadgeCount(numCount);
            }
        };

        window.updateCartBadgeCount = function(count) {
            const num = parseInt(count) || 0;
            ['header-cart-count', 'header-cart-count-mobile'].forEach(bId => {
                const el = document.getElementById(bId);
                if (el) {
                    el.textContent = num;
                    if (num > 0) {
                        el.classList.remove('d-none');
                    } else {
                        el.classList.add('d-none');
                    }
                }
            });
        };

        function initFloatingCheckoutState() {
            const wrap = document.getElementById('floatingCheckoutWrap');
            const badge = document.getElementById('floatingCheckoutCount');
            const count = badge ? (parseInt(badge.textContent) || 0) : 0;
            if (wrap) {
                if (count > 0) {
                    wrap.classList.add('is-visible');
                    wrap.style.display = 'inline-flex';
                } else {
                    wrap.classList.remove('is-visible');
                    wrap.style.display = 'none';
                }
            }
            if (typeof window.updateCartBadgeCount === 'function') {
                window.updateCartBadgeCount(count);
            }
        }

        // Detect floating WhatsApp to prevent overlap on mobile
        function checkFloatingOffsets() {
            if (document.querySelector('.whatsapp-float')) {
                document.body.classList.add('has-whatsapp-float');
            }
        }

        function initFloatingCheckoutClick() {
            const checkoutBtn = document.getElementById('floatingCheckoutBtn');
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', function(e) {
                    const countEl = document.getElementById('floatingCheckoutCount');
                    const count = countEl ? (parseInt(countEl.textContent) || 0) : 0;
                    if (count === 0) {
                        e.preventDefault();
                        const miniCartEl = document.getElementById('miniCart');
                        if (miniCartEl && typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
                            const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(miniCartEl);
                            bsOffcanvas.show();
                        } else {
                            window.location.href = "{{ route('cart.index') }}";
                        }
                    }
                });
            }
        }
        function initOffcanvasFloatingButtonsHandler() {
            const miniCartEl = document.getElementById('miniCart');
            const floatingCheckout = document.getElementById('floatingCheckoutWrap');
            const whatsappBtn = document.querySelector('.whatsapp-float');

            function hideButtons() {
                document.body.classList.add('mini-cart-open');
                if (floatingCheckout) {
                    floatingCheckout.style.setProperty('display', 'none', 'important');
                    floatingCheckout.style.setProperty('opacity', '0', 'important');
                    floatingCheckout.style.setProperty('pointer-events', 'none', 'important');
                }
                if (whatsappBtn) {
                    whatsappBtn.style.setProperty('display', 'none', 'important');
                    whatsappBtn.style.setProperty('opacity', '0', 'important');
                    whatsappBtn.style.setProperty('pointer-events', 'none', 'important');
                }
            }

            function restoreButtons() {
                document.body.classList.remove('mini-cart-open');
                if (whatsappBtn) {
                    whatsappBtn.style.removeProperty('display');
                    whatsappBtn.style.removeProperty('opacity');
                    whatsappBtn.style.removeProperty('pointer-events');
                }
                if (floatingCheckout) {
                    const countEl = document.getElementById('floatingCheckoutCount');
                    const count = countEl ? (parseInt(countEl.textContent) || 0) : 0;
                    if (count > 0) {
                        floatingCheckout.style.removeProperty('display');
                        floatingCheckout.style.removeProperty('opacity');
                        floatingCheckout.style.removeProperty('pointer-events');
                        floatingCheckout.classList.add('is-visible');
                    } else {
                        floatingCheckout.classList.remove('is-visible');
                        floatingCheckout.style.setProperty('display', 'none', 'important');
                    }
                }
            }

            if (miniCartEl) {
                miniCartEl.addEventListener('show.bs.offcanvas', hideButtons);
                miniCartEl.addEventListener('shown.bs.offcanvas', hideButtons);
                miniCartEl.addEventListener('hide.bs.offcanvas', restoreButtons);
                miniCartEl.addEventListener('hidden.bs.offcanvas', restoreButtons);
            }

            const mobileNav = document.getElementById('mobileNavDrawer');
            if (mobileNav) {
                mobileNav.addEventListener('show.bs.offcanvas', hideButtons);
                mobileNav.addEventListener('shown.bs.offcanvas', hideButtons);
                mobileNav.addEventListener('hide.bs.offcanvas', restoreButtons);
                mobileNav.addEventListener('hidden.bs.offcanvas', restoreButtons);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                checkFloatingOffsets();
                initFloatingCheckoutState();
                initFloatingCheckoutClick();
                initOffcanvasFloatingButtonsHandler();
            });
        } else {
            checkFloatingOffsets();
            initFloatingCheckoutState();
            initFloatingCheckoutClick();
            initOffcanvasFloatingButtonsHandler();
        }

        window.updateProductCartState = function(productId, inCart) {
            const numId = parseInt(productId);
            if (!numId) return;

            const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
            const inCartLabel = isAr ? 'في السلة' : 'Dans le panier';
            const notInCartLabel = isAr ? 'أضف إلى السلة' : 'Ajouter au panier';
            const inCartOverlayLabel = isAr ? 'في السلة' : 'Dans le panier';
            const notInCartOverlayLabel = isAr ? 'أضف' : 'Ajouter';

            // 1. Featured cards on Homepage and elsewhere with data-product-id
            const buttons = document.querySelectorAll(`button[data-product-id="${numId}"]:not(.pcard-quick-cart-btn):not(.pcard-overlay-btn)`);
            buttons.forEach(b => {
                b.disabled = false;
                if (inCart) {
                    b.classList.add('is-in-cart');
                    b.innerHTML = `<i class="fas fa-check" style="font-size: 11px;"></i> <span>${inCartLabel}</span>`;
                    b.setAttribute('title', inCartLabel);
                } else {
                    b.classList.remove('is-in-cart');
                    b.innerHTML = `<i class="fas fa-cart-plus" style="font-size: 11px;"></i> <span>${notInCartLabel}</span>`;
                    b.setAttribute('title', notInCartLabel);
                }
            });

            // 2. Shop catalog quick-cart buttons (round icon only)
            const quickButtons = document.querySelectorAll(`.pcard-quick-cart-btn[data-product-id="${numId}"], .pcard-quick-cart-btn[onclick*="addToCart(${numId},"], .pcard-quick-cart-btn[onclick*="addToCart(${numId})"]`);
            quickButtons.forEach(b => {
                b.disabled = false;
                if (inCart) {
                    b.classList.add('is-in-cart');
                    b.innerHTML = '<i class="fas fa-check"></i>';
                    b.setAttribute('title', inCartLabel);
                    b.setAttribute('aria-label', inCartLabel);
                } else {
                    b.classList.remove('is-in-cart');
                    b.innerHTML = '<i class="fas fa-shopping-bag"></i>';
                    b.setAttribute('title', notInCartLabel);
                    b.setAttribute('aria-label', notInCartLabel);
                }
            });

            // 3. Shop catalog hover overlay buttons
            const overlayButtons = document.querySelectorAll(`.pcard-overlay-btn[data-product-id="${numId}"], .pcard-overlay-btn[onclick*="addToCart(${numId},"], .pcard-overlay-btn[onclick*="addToCart(${numId})"]`);
            overlayButtons.forEach(b => {
                b.disabled = false;
                if (inCart) {
                    b.classList.add('is-in-cart');
                    b.innerHTML = `<i class="fas fa-check"></i> ${inCartOverlayLabel}`;
                    b.setAttribute('title', inCartLabel);
                } else {
                    b.classList.remove('is-in-cart');
                    b.innerHTML = `<i class="fas fa-cart-plus"></i> ${notInCartOverlayLabel}`;
                    b.setAttribute('title', notInCartLabel);
                }
            });
        };

        window.syncAllProductCartButtons = function(cartProductIds) {
            const ids = Array.isArray(cartProductIds) ? cartProductIds.map(Number) : [];
            const allProductBtns = document.querySelectorAll('button[data-product-id]');
            allProductBtns.forEach(btn => {
                const pId = parseInt(btn.dataset.productId);
                if (pId) {
                    const inCart = ids.includes(pId);
                    window.updateProductCartState(pId, inCart);
                }
            });
        };

        window.addToCart = addToCart = function(productId, quantity = 1, triggerEl = null, force = false) {
            let originalHtml = '';
            let btn = triggerEl;
            if (!btn && typeof event !== 'undefined' && event && event.currentTarget) {
                btn = event.currentTarget;
            }
            if (!btn) {
                btn = document.querySelector(`button[data-product-id="${productId}"]`) || 
                      document.querySelector(`button[onclick*="addToCart(${productId},"]`) ||
                      document.querySelector(`button[onclick*="addToCart(${productId})"]`);
            }

            // Check if product is already in cart and button is in green state
            const isAlreadyInCart = (btn && btn.classList.contains('is-in-cart')) || 
                                    (document.querySelector(`button[data-product-id="${productId}"].is-in-cart`) !== null);

            if (isAlreadyInCart && !force) {
                const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: isAr ? 'المنتج موجود في السلة' : 'Article déjà dans le panier',
                        text: isAr ? 'هذا المنتج موجود بالفعل في سلتك. هل ترغب في إضافة كمية إضافية ؟' : 'Ce produit est déjà dans votre panier. Souhaitez-vous ajouter un exemplaire supplémentaire ?',
                        icon: 'question',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonColor: '#16a34a',
                        denyButtonColor: '#0f172a',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: isAr ? '<i class="fas fa-plus ms-1"></i> إضافة كمية (+1)' : '<i class="fas fa-plus me-1"></i> Ajouter une quantité (+1)',
                        denyButtonText: isAr ? '<i class="fas fa-shopping-bag ms-1"></i> عرض السلة' : '<i class="fas fa-shopping-bag me-1"></i> Voir mon panier',
                        cancelButtonText: isAr ? 'إلغاء' : 'Annuler',
                        reverseButtons: !isAr
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.addToCart(productId, quantity, btn, true);
                        } else if (result.isDenied) {
                            const miniCartEl = document.getElementById('miniCart');
                            if (miniCartEl && typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
                                bootstrap.Offcanvas.getOrCreateInstance(miniCartEl).show();
                            } else {
                                window.location.href = "{{ route('cart.index') }}";
                            }
                        }
                    });
                    return;
                } else if (confirm('Ce produit est déjà dans votre panier. Souhaitez-vous en ajouter un exemplaire supplémentaire ?')) {
                    window.addToCart(productId, quantity, btn, true);
                    return;
                } else {
                    return;
                }
            }
            if (btn) {
                originalHtml = btn.innerHTML;
                btn.disabled = true;
                const hasSpan = btn.querySelector('span');
                if (hasSpan) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>{{ __('Adding...') }}</span>';
                } else {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }
            }

            return fetch(`{{ url('/cart/add') }}/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ quantity: parseInt(quantity) || 1 })
            })
            .then(async response => {
                const data = await response.json();
                if (btn) {
                    btn.disabled = false;
                }
                if (!response.ok || !data.success) {
                    if (btn) btn.innerHTML = originalHtml;
                    throw new Error(data.message || '{{ __('Error adding to cart') }}');
                }

                // Keep button green with "Dans le panier"
                if (typeof window.updateProductCartState === 'function') {
                    window.updateProductCartState(productId, true);
                }
                if (data.cartProductIds && typeof window.syncAllProductCartButtons === 'function') {
                    window.syncAllProductCartButtons(data.cartProductIds);
                }

                // Update both desktop and mobile cart count badges
                if (typeof window.updateCartBadgeCount === 'function') {
                    window.updateCartBadgeCount(data.cartCount);
                }

                // Update floating checkout button
                if (typeof window.updateFloatingCheckout === 'function') {
                    window.updateFloatingCheckout(data.cartCount, data.cartTotal);
                }

                // Refresh mini-cart content
                if (typeof refreshMiniCart === 'function') {
                    refreshMiniCart();
                }

                // Feedback toast
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || '{{ __('Equipment added to cart!') }}',
                        showConfirmButton: false,
                        timer: 2200,
                        background: '#1a1a2e',
                        color: '#ffffff'
                    });
                }

                return data;
            })
            .catch(err => {
                console.error(err);
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: err.message || '{{ __('Cannot add to cart.') }}',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        };

        // Global safeguard: Intercept any form submitting to /cart/add to prevent page refreshes
        document.addEventListener('submit', function(e) {
            const form = e.target.closest('form');
            if (!form) return;
            const action = form.getAttribute('action') || '';
            if (action.includes('/cart/add') || form.classList.contains('add-to-cart-form')) {
                e.preventDefault();
                e.stopPropagation();
                const match = action.match(/\/cart\/add\/(\d+)/);
                const productId = form.dataset.productId || (match ? match[1] : null);
                const qtyInput = form.querySelector('input[name="quantity"]');
                const quantity = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
                const btn = form.querySelector('button[type="submit"]') || form.querySelector('button');
                if (productId && typeof window.addToCart === 'function') {
                    window.addToCart(productId, quantity, btn);
                }
            }
        }, true);

        function changeCartQty(id, delta) {
            const input = document.getElementById('cart-item-qty-' + id);
            let currentQty = 1;
            if (input) {
                currentQty = parseInt(input.value) || 1;
            }
            const newQty = currentQty + delta;
            updateQty(id, newQty);
        }

        function updateQty(id, qty) {
            if(qty < 1) {
                removeItem(id);
                return;
            }
            
            fetch('{{ route('cart.update', [], false) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id, quantity: qty })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Erreur mise à jour'); });
                }
                return response.json();
            })
            .then(data => {
                // Update both desktop and mobile cart count badges
                if (typeof window.updateCartBadgeCount === 'function') {
                    window.updateCartBadgeCount(data.cartCount);
                }

                // Update floating checkout button
                if (typeof window.updateFloatingCheckout === 'function') {
                    window.updateFloatingCheckout(data.cartCount, data.cartTotal);
                }

                // Update /cart page elements if present
                const qtyInput = document.getElementById('cart-item-qty-' + id);
                if (qtyInput) {
                    qtyInput.value = data.quantity;
                }
                const rowTotal = document.getElementById('cart-item-total-' + id);
                if (rowTotal && data.itemTotal) {
                    rowTotal.textContent = data.itemTotal;
                }
                const subtotal = document.getElementById('cart-summary-subtotal');
                if (subtotal && data.cartTotal) {
                    subtotal.textContent = data.cartTotal;
                }
                const total = document.getElementById('cart-summary-total');
                if (total && data.cartTotal) {
                    total.textContent = data.cartTotal;
                }

                // Refresh mini-cart content
                refreshMiniCart();
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: error.message || '{{ __('Error updating cart') }}',
                    showConfirmButton: false,
                    timer: 2500
                });
            });
        }

        function removeItem(id) {
            Swal.fire({
                title: '{{ __('Remove from cart?') }}',
                text: "{{ __('Do you want to remove this item?') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __('Yes, remove!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('cart.remove', [], false) }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update both desktop and mobile cart count badges
                        if (typeof window.updateCartBadgeCount === 'function') {
                            window.updateCartBadgeCount(data.cartCount);
                        }

                        // Update floating checkout button
                        if (typeof window.updateFloatingCheckout === 'function') {
                            window.updateFloatingCheckout(data.cartCount, data.cartTotal);
                        }

                        // Revert product button to default "Ajouter au panier"
                        if (typeof window.updateProductCartState === 'function') {
                            window.updateProductCartState(id, false);
                        }
                        if (data.cartProductIds && typeof window.syncAllProductCartButtons === 'function') {
                            window.syncAllProductCartButtons(data.cartProductIds);
                        }

                        // If on /cart page, remove the row or show empty cart notice without reloading
                        const row = document.getElementById('cart-row-' + id);
                        if (row) {
                            row.style.transition = 'opacity 0.25s, transform 0.25s';
                            row.style.opacity = '0';
                            row.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                row.remove();
                                if (data.isEmpty || document.querySelectorAll('[id^="cart-row-"]').length === 0) {
                                    const cartTableCol = document.querySelector('.col-lg-8');
                                    const cartSummaryCol = document.querySelector('.col-lg-4');
                                    const rowWrapper = cartTableCol ? cartTableCol.closest('.row') : null;
                                    if (rowWrapper) {
                                        rowWrapper.innerHTML = `
                                            <div class="col-12 text-center py-5 mt-4" id="cartEmptyNotice">
                                                <div class="mb-4 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 120px; height: 120px;">
                                                    <i class="fas fa-shopping-basket fa-4x text-muted opacity-25"></i>
                                                </div>
                                                <h3 class="fw-bold text-dark mb-3">{{ __('Your cart is empty') }}</h3>
                                                <p class="text-muted mb-4">{{ __("You haven't added anything yet.") }}</p>
                                                <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm hover-scale-sm transition-transform">{{ __('Start Shopping') }}</a>
                                            </div>
                                        `;
                                    }
                                }
                            }, 250);
                        }
                        const subtotal = document.getElementById('cart-summary-subtotal');
                        if (subtotal && data.cartTotal) {
                            subtotal.textContent = data.cartTotal;
                        }
                        const total = document.getElementById('cart-summary-total');
                        if (total && data.cartTotal) {
                            total.textContent = data.cartTotal;
                        }

                        // Refresh mini-cart content
                        refreshMiniCart();
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '{{ __('Item removed!') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            background: '#1a1a2e',
                            color: '#fff'
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: '{{ __('Error removing item') }}',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    });
                }
            });
        }

        // Refresh mini-cart content dynamically
        function refreshMiniCart() {
            fetch('{{ route('cart.mini', [], false) }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const miniCartContainer = document.getElementById('mini-cart-items');
                if(miniCartContainer) {
                    miniCartContainer.innerHTML = html;
                }
                // Also update the footer section if cart has items
                const cartOffcanvas = document.getElementById('miniCart');
                if(cartOffcanvas) {
                    // Fetch full mini-cart to get updated footer
                    fetch('{{ route('cart.miniFooter', [], false) }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.text())
                    .then(footerHtml => {
                        const existingFooter = cartOffcanvas.querySelector('.border-top.p-4.bg-white');
                        if(existingFooter && footerHtml.trim()) {
                            existingFooter.outerHTML = footerHtml;
                        } else if(!existingFooter && footerHtml.trim()) {
                            // Append footer if it didn't exist before
                            cartOffcanvas.querySelector('.offcanvas-body').insertAdjacentHTML('beforeend', footerHtml);
                        } else if(existingFooter && !footerHtml.trim()) {
                            existingFooter.remove();
                        }
                    })
                    .catch(console.error);
                }
            })
            .catch(console.error);
        }

        // Mobile category dropdown toggle
        function initMobileCategoryToggle() {
            const dropdownItem = document.querySelector('.nav-item-dropdown');
            const chevron = document.querySelector('.nav-chevron-icon');
            if (dropdownItem && chevron) {
                chevron.addEventListener('click', function(e) {
                    if (window.innerWidth < 992) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropdownItem.classList.toggle('mobile-open');
                    }
                });
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMobileCategoryToggle);
        } else {
            initMobileCategoryToggle();
        }

        // Dynamic Navbar Live Search
        function initNavbarLiveSearch() {
            const form = document.getElementById('headerSearchForm');
            const input = document.getElementById('headerSearchInput');
            const clearBtn = document.getElementById('headerSearchClear');
            const dropdown = document.getElementById('headerSearchDropdown');
            const tagsBox = document.getElementById('headerSearchTags');
            const resultsList = document.getElementById('headerSearchResultsList');
            const emptyBox = document.getElementById('headerSearchEmpty');
            const footerBox = document.getElementById('headerSearchFooter');
            const viewAllLink = document.getElementById('headerSearchViewAll');
            const countBadge = document.getElementById('headerSearchCountBadge');
            const spinner = document.querySelector('.search-spinner');
            const defaultIcon = document.querySelector('.search-icon-default');

            if (!form || !input || !dropdown) return;

            let debounceTimer = null;
            let currentAbortController = null;
            const searchCache = new Map();
            let selectedIndex = -1;

            function showSpinner(show) {
                if (spinner && defaultIcon) {
                    if (show) {
                        spinner.classList.remove('d-none');
                        defaultIcon.classList.add('d-none');
                    } else {
                        spinner.classList.add('d-none');
                        defaultIcon.classList.remove('d-none');
                    }
                }
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/[&<>"']/g, function(m) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
                });
            }

            function highlightText(text, query) {
                if (!query || !text) return escapeHtml(text);
                const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp('(' + escapedQuery + ')', 'gi');
                return escapeHtml(text).replace(regex, '<mark class="search-highlight">$1</mark>');
            }

            function openDropdown() {
                dropdown.classList.remove('d-none');
            }

            function closeDropdown() {
                dropdown.classList.add('d-none');
                selectedIndex = -1;
            }

            function updateClearButton() {
                if (clearBtn) {
                    if (input.value.trim().length > 0) {
                        clearBtn.classList.remove('d-none');
                    } else {
                        clearBtn.classList.add('d-none');
                    }
                }
            }

            function renderResults(data, query) {
                showSpinner(false);
                tagsBox.classList.add('d-none');
                selectedIndex = -1;

                const hasCategories = data.categories && data.categories.length > 0;
                const hasProducts = data.products && data.products.length > 0;

                if (!hasCategories && !hasProducts) {
                    resultsList.innerHTML = '';
                    resultsList.classList.add('d-none');
                    emptyBox.classList.remove('d-none');
                    const emptyQueryText = emptyBox.querySelector('.empty-query-text');
                    if (emptyQueryText) emptyQueryText.textContent = query;
                    footerBox.classList.add('d-none');
                    openDropdown();
                    return;
                }

                emptyBox.classList.add('d-none');
                resultsList.classList.remove('d-none');

                let html = '';

                // Category proposals if any
                if (hasCategories) {
                    html += `
                        <div class="search-section-label px-3 pt-3 pb-1">
                            <i class="fas fa-th-large me-1 text-danger"></i> {{ strtoupper(__('Matching Categories')) }}
                        </div>
                        <div class="search-category-suggestions px-2 pb-2">
                    `;
                    data.categories.forEach(cat => {
                        html += `
                            <a href="${cat.url}" class="search-cat-item">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="search-cat-icon"><i class="fas fa-arrow-right text-danger"></i></span>
                                    <span class="search-cat-text">{{ __('In') }} <strong>${highlightText(cat.name, query)}</strong></span>
                                </div>
                                <span class="badge bg-light text-muted border">${cat.count} {{ __('refs.') }}</span>
                            </a>
                        `;
                    });
                    html += `</div>`;
                }

                // Products proposals
                if (hasProducts) {
                    html += `
                        <div class="search-section-label px-3 pt-2 pb-1 ${hasCategories ? 'border-top' : ''}">
                            <i class="fas fa-camera me-1 text-danger"></i> {{ strtoupper(__('Equipment & Models')) }} (${data.count || data.products.length})
                        </div>
                    `;
                    data.products.forEach((p, idx) => {
                        const priceHtml = p.is_sale 
                            ? `<span class="search-result-price">${escapeHtml(p.sale_price)}</span> <span class="search-result-price-old">${escapeHtml(p.price)}</span>`
                            : `<span class="search-result-price">${escapeHtml(p.price)}</span>`;

                        const stockHtml = p.in_stock 
                            ? `<span class="search-result-stock">{{ __('In stock') }}</span>`
                            : `<span class="search-result-stock out">{{ __('Out of stock') }}</span>`;

                        html += `
                            <a href="${p.url}" class="search-result-item" data-index="${idx}">
                                <img src="${p.image}" alt="${escapeHtml(p.name)}" class="search-result-thumb" loading="lazy">
                                <div class="search-result-info">
                                    <div class="search-result-cat">${escapeHtml(p.category)}</div>
                                    <div class="search-result-name">${highlightText(p.name, query)}</div>
                                    <div class="search-result-meta">
                                        ${priceHtml}
                                        ${stockHtml}
                                    </div>
                                </div>
                                <div class="search-result-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                        `;
                    });
                }

                resultsList.innerHTML = html;

                if (footerBox && viewAllLink) {
                    viewAllLink.href = data.all_url || `{{ route('shop.index', [], false) }}?q=${encodeURIComponent(query)}`;
                    if (countBadge) countBadge.textContent = data.count || (data.products ? data.products.length : 0);
                    footerBox.classList.remove('d-none');
                }

                openDropdown();
            }

            function fetchLiveSearch(query) {
                if (searchCache.has(query)) {
                    renderResults(searchCache.get(query), query);
                    return;
                }

                if (currentAbortController) {
                    currentAbortController.abort();
                }
                currentAbortController = new AbortController();

                showSpinner(true);

                fetch(`{{ route('shop.search.live', [], false) }}?q=${encodeURIComponent(query)}`, {
                    signal: currentAbortController.signal,
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    searchCache.set(query, data);
                    renderResults(data, query);
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        console.error('Search error:', err);
                        showSpinner(false);
                    }
                });
            }

            // Input typing listener with debounce
            input.addEventListener('input', function() {
                const query = input.value.trim();
                updateClearButton();

                if (debounceTimer) clearTimeout(debounceTimer);

                if (query.length === 0) {
                    showSpinner(false);
                    resultsList.innerHTML = '';
                    resultsList.classList.add('d-none');
                    emptyBox.classList.add('d-none');
                    footerBox.classList.add('d-none');
                    tagsBox.classList.remove('d-none');
                    openDropdown();
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetchLiveSearch(query);
                }, 150);
            });

            // Focus event
            input.addEventListener('focus', function() {
                updateClearButton();
                const query = input.value.trim();
                if (query.length === 0) {
                    tagsBox.classList.remove('d-none');
                    resultsList.classList.add('d-none');
                    emptyBox.classList.add('d-none');
                    footerBox.classList.add('d-none');
                    openDropdown();
                } else {
                    fetchLiveSearch(query);
                }
            });

            // Clear button click
            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    input.value = '';
                    updateClearButton();
                    resultsList.innerHTML = '';
                    resultsList.classList.add('d-none');
                    emptyBox.classList.add('d-none');
                    footerBox.classList.add('d-none');
                    tagsBox.classList.remove('d-none');
                    openDropdown();
                    input.focus();
                });
            }

            // Quick suggestion tags click
            dropdown.addEventListener('click', function(e) {
                const chip = e.target.closest('.search-tag-chip');
                if (chip) {
                    e.preventDefault();
                    const term = chip.getAttribute('data-search');
                    if (term) {
                        input.value = term;
                        updateClearButton();
                        input.focus();
                        fetchLiveSearch(term);
                    }
                }
            });

            // Keyboard navigation (Arrow keys, Escape, Enter)
            input.addEventListener('keydown', function(e) {
                if (dropdown.classList.contains('d-none')) return;

                const items = resultsList.querySelectorAll('.search-cat-item, .search-result-item');
                if (e.key === 'ArrowDown') {
                    if (items.length > 0) {
                        e.preventDefault();
                        selectedIndex = (selectedIndex + 1) % items.length;
                        highlightSelectedItem(items);
                    }
                } else if (e.key === 'ArrowUp') {
                    if (items.length > 0) {
                        e.preventDefault();
                        selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                        highlightSelectedItem(items);
                    }
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        e.preventDefault();
                        window.location.href = items[selectedIndex].href;
                    }
                } else if (e.key === 'Escape') {
                    closeDropdown();
                }
            });

            function highlightSelectedItem(items) {
                items.forEach((item, idx) => {
                    if (idx === selectedIndex) {
                        item.classList.add('selected');
                        item.scrollIntoView({ block: 'nearest' });
                    } else {
                        item.classList.remove('selected');
                    }
                });
            }

            // Close on click outside
            document.addEventListener('click', function(e) {
                if (!form.contains(e.target)) {
                    closeDropdown();
                }
            });
        }

        // Pro Mobile Full-Screen Search Handler
        function initProMobileSearch() {
            const overlay = document.getElementById('proMobileSearchOverlay');
            const triggerHeader = document.getElementById('mobileSearchTriggerBtn');
            const triggerInMenu = document.getElementById('inMenuSearchTrigger');
            const closeBtn = document.getElementById('proMobileSearchCloseBtn');
            const input = document.getElementById('proMobileSearchInput');
            const clearBtn = document.getElementById('proMobileSearchClear');
            const suggestionsBox = document.getElementById('proMobileSearchSuggestions');
            const resultsBox = document.getElementById('proMobileSearchResults');
            const emptyBox = document.getElementById('proMobileSearchEmpty');
            const footerBox = document.getElementById('proMobileSearchFooter');
            const viewAllLink = document.getElementById('proMobileSearchViewAll');
            const countBadge = document.getElementById('proMobileSearchCount');
            const spinner = overlay ? overlay.querySelector('.pro-icon-spinner') : null;
            const defaultIcon = overlay ? overlay.querySelector('.pro-icon-default') : null;

            if (!overlay || !input) return;

            let debounceTimer = null;
            let currentAbort = null;
            const mobileCache = new Map();

            function showSpinner(show) {
                if (spinner && defaultIcon) {
                    if (show) {
                        spinner.classList.remove('d-none');
                        defaultIcon.classList.add('d-none');
                    } else {
                        spinner.classList.add('d-none');
                        defaultIcon.classList.remove('d-none');
                    }
                }
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/[&<>"']/g, function(m) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
                });
            }

            function highlightText(text, query) {
                if (!query || !text) return escapeHtml(text);
                const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp('(' + escapedQuery + ')', 'gi');
                return escapeHtml(text).replace(regex, '<mark class="search-highlight">$1</mark>');
            }

            function openOverlay(initialQuery = '') {
                // If navbar is open on mobile, collapse it
                const navbarCollapse = document.getElementById('navbarMain');
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) bsCollapse.hide();
                }

                document.body.classList.add('pro-mobile-search-open');
                overlay.classList.add('active');

                if (initialQuery) {
                    input.value = initialQuery;
                    updateClear();
                    fetchMobileSearch(initialQuery);
                } else if (input.value.trim().length > 0) {
                    updateClear();
                    fetchMobileSearch(input.value.trim());
                } else {
                    showSuggestions();
                }

                setTimeout(() => {
                    input.focus();
                }, 180);
            }

            function closeOverlay() {
                overlay.classList.remove('active');
                document.body.classList.remove('pro-mobile-search-open');
            }

            function updateClear() {
                if (clearBtn) {
                    if (input.value.trim().length > 0) {
                        clearBtn.classList.remove('d-none');
                    } else {
                        clearBtn.classList.add('d-none');
                    }
                }
            }

            function showSuggestions() {
                showSpinner(false);
                suggestionsBox.classList.remove('d-none');
                resultsBox.classList.add('d-none');
                emptyBox.classList.add('d-none');
                footerBox.classList.add('d-none');
                resultsBox.innerHTML = '';
            }

            function renderMobileResults(data, query) {
                showSpinner(false);
                suggestionsBox.classList.add('d-none');

                const hasCats = data.categories && data.categories.length > 0;
                const hasProds = data.products && data.products.length > 0;

                if (!hasCats && !hasProds) {
                    resultsBox.innerHTML = '';
                    resultsBox.classList.add('d-none');
                    emptyBox.classList.remove('d-none');
                    const emptyQ = emptyBox.querySelector('.pro-empty-query');
                    if (emptyQ) emptyQ.textContent = query;
                    footerBox.classList.add('d-none');
                    return;
                }

                emptyBox.classList.add('d-none');
                resultsBox.classList.remove('d-none');

                let html = '';

                // Matching Category Proposals
                if (hasCats) {
                    html += `
                        <div class="pro-section-title mb-2">
                            <i class="fas fa-th-large text-danger me-1"></i> {{ strtoupper(__('Matching Categories')) }}
                        </div>
                    `;
                    data.categories.forEach(cat => {
                        html += `
                            <a href="${cat.url}" class="pro-cat-match-card">
                                <div class="pro-cat-match-left">
                                    <span class="pro-cat-match-icon"><i class="fas fa-arrow-right"></i></span>
                                    <span class="pro-cat-match-title">{{ __('In') }} <strong>${highlightText(cat.name, query)}</strong></span>
                                </div>
                                <span class="badge bg-light text-muted border">${cat.count} {{ __('refs.') }}</span>
                            </a>
                        `;
                    });
                }

                // Matching Products
                if (hasProds) {
                    html += `
                        <div class="pro-section-title mt-3 mb-2">
                            <i class="fas fa-camera text-danger me-1"></i> {{ strtoupper(__('Equipment & Models')) }} (${data.count || data.products.length})
                        </div>
                    `;
                    data.products.forEach(p => {
                        const priceHtml = p.is_sale 
                            ? `<span class="pro-result-price">${escapeHtml(p.sale_price)}</span> <span class="pro-result-price-old">${escapeHtml(p.price)}</span>`
                            : `<span class="pro-result-price">${escapeHtml(p.price)}</span>`;

                        const stockHtml = p.in_stock 
                            ? `<span class="pro-result-stock">{{ __('In stock') }}</span>`
                            : `<span class="pro-result-stock out">{{ __('Out of stock') }}</span>`;

                        html += `
                            <a href="${p.url}" class="pro-result-card">
                                <img src="${p.image}" alt="${escapeHtml(p.name)}" class="pro-result-thumb" loading="lazy">
                                <div class="pro-result-info">
                                    <span class="pro-result-cat">${escapeHtml(p.category)}</span>
                                    <div class="pro-result-title">${highlightText(p.name, query)}</div>
                                    <div class="pro-result-price-row">
                                        ${priceHtml}
                                        ${stockHtml}
                                    </div>
                                </div>
                                <div class="pro-result-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                        `;
                    });
                }

                resultsBox.innerHTML = html;

                if (footerBox && viewAllLink) {
                    viewAllLink.href = data.all_url || `{{ route('shop.index', [], false) }}?q=${encodeURIComponent(query)}`;
                    if (countBadge) countBadge.textContent = data.count || (data.products ? data.products.length : 0);
                    footerBox.classList.remove('d-none');
                }
            }

            function fetchMobileSearch(query) {
                if (mobileCache.has(query)) {
                    renderMobileResults(mobileCache.get(query), query);
                    return;
                }

                if (currentAbort) currentAbort.abort();
                currentAbort = new AbortController();

                showSpinner(true);

                fetch(`{{ route('shop.search.live', [], false) }}?q=${encodeURIComponent(query)}`, {
                    signal: currentAbort.signal,
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    mobileCache.set(query, data);
                    renderMobileResults(data, query);
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        console.error('Search error:', err);
                        showSpinner(false);
                    }
                });
            }

            // Input event listener
            input.addEventListener('input', function() {
                const query = input.value.trim();
                updateClear();

                if (debounceTimer) clearTimeout(debounceTimer);

                if (query.length === 0) {
                    showSuggestions();
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetchMobileSearch(query);
                }, 150);
            });

            // Clear button
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    input.value = '';
                    updateClear();
                    showSuggestions();
                    input.focus();
                });
            }

            // Chip click listeners inside overlay (popular tags + empty state tags)
            overlay.addEventListener('click', function(e) {
                const chip = e.target.closest('.pro-tag-chip');
                if (chip) {
                    e.preventDefault();
                    const q = chip.getAttribute('data-query');
                    if (q) {
                        input.value = q;
                        updateClear();
                        fetchMobileSearch(q);
                    }
                }
            });

            // Close button
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeOverlay();
                });
            }

            // Open triggers
            if (triggerHeader) {
                triggerHeader.addEventListener('click', function(e) {
                    e.preventDefault();
                    openOverlay();
                });
            }
            if (triggerInMenu) {
                triggerInMenu.addEventListener('click', function(e) {
                    e.preventDefault();
                    openOverlay();
                });
            }

            // Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && overlay.classList.contains('active')) {
                    closeOverlay();
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initNavbarLiveSearch();
                initProMobileSearch();
            });
        } else {
            initNavbarLiveSearch();
            initProMobileSearch();
        }
    </script>
    @stack('scripts')

    <!-- Custom Body End Codes -->
    @php
        $bodyEndCodes = \App\Models\CustomCode::where('is_active', true)
            ->where('position', 'body_end')
            ->orderBy('priority', 'desc')
            ->get();
    @endphp
    @foreach($bodyEndCodes as $code)
        @if($code->type == 'css')
            <style>{!! $code->content !!}</style>
        @elseif($code->type == 'js')
            <script>{!! $code->content !!}</script>
        @else
            {!! $code->content !!}
        @endif
    @endforeach
</body>
</html>
