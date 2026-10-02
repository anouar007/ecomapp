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
    <meta property="og:site_name" content="{{ setting('app_name', 'Full Frame House') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', setting('app_name', 'Full Frame House') . ' — Matériel Cinéma, Caméras & Optiques au Maroc')">
    <meta property="og:description" content="@yield('meta_description', setting('app_description', 'Votre référence au Maroc pour les caméras cinéma, boîtiers hybrides, optiques pro, stabilisateurs et éclairage studio.'))">
    <meta property="og:image" content="@yield('meta_image', setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/hero_cinema_rig.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ setting('language', 'fr') === 'ar' ? 'ar_MA' : 'fr_MA' }}">
    <meta property="og:updated_time" content="{{ now()->toIso8601String() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('meta_title', setting('app_name', 'Full Frame House') . ' — Matériel Cinéma, Caméras & Optiques au Maroc')">
    <meta name="twitter:description" content="@yield('meta_description', setting('app_description', 'Votre référence au Maroc pour les caméras cinéma, boîtiers hybrides, optiques pro, stabilisateurs et éclairage studio.'))">
    <meta name="twitter:image" content="@yield('meta_image', setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/hero_cinema_rig.jpg'))">    
    <meta name="twitter:site" content="@yield('twitter_site', '@' . str_replace(' ', '', setting('app_name', 'FullFrameHouse')))">

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
      "name": "{{ setting('company_name', setting('app_name', 'Full Frame House')) }}",
      "url": "{{ url('/') }}",
      "logo": "{{ setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/logo.png') }}",
      "description": "{{ addslashes(setting('app_description', 'Votre référence au Maroc pour les caméras cinéma, boîtiers hybrides, optiques et éclairage studio.')) }}",
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
            setting('social_facebook'),
            setting('social_instagram'),
            setting('social_twitter'),
            setting('social_linkedin'),
            setting('social_whatsapp') ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', setting('social_whatsapp')) : null,
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
      "name": "{{ setting('app_name', 'Full Frame House') }}",
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
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    <link rel="stylesheet" href="{{ asset('css/camera-theme.css') }}">
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

    <!-- Main Header -->
    <div class="header-main shadow-sm w-100" style="z-index: 1040;">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-2">
                <div class="container-fluid px-0">
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
                            'cameras-hybrides'       => 'fa-camera',
                            'objectifs-optiques'     => 'fa-circle-notch',
                            'stabilisateurs-gimbals' => 'fa-video',
                            'eclairage-studio'       => 'fa-lightbulb',
                            'audio-micros-sans-fil'  => 'fa-microphone',
                            'drones-cine'            => 'fa-helicopter',
                        ];
                    @endphp

                    <!-- Logo -->
                    <a class="navbar-brand me-3 me-lg-5" href="{{ url('/') }}">
                        @php
                            $appName = setting('app_name', 'LUMINA Optics');
                            $logoSetting = setting('app_logo');
                            $logoSrc = ($logoSetting && (str_starts_with($logoSetting, 'http') || str_starts_with($logoSetting, 'images/'))) 
                                ? asset($logoSetting) 
                                : ($logoSetting ? asset('storage/' . $logoSetting) : asset('images/camera/logo.png'));

                            $words = explode(' ', trim($appName));
                            if (count($words) > 1) {
                                $lastWord = array_pop($words);
                                $firstPart = implode(' ', $words);
                            } else {
                                $firstPart = $appName;
                                $lastWord = '';
                            }
                        @endphp
                        <div class="brand-logo-wrap">
                            <img src="{{ $logoSrc }}" alt="{{ $appName }}" class="brand-logo-img">
                        </div>
                        <div class="brand-text-wrap d-none d-sm-flex">
                            <span class="brand-title">
                                {{ $firstPart }} @if($lastWord)<span class="glow-red">{{ $lastWord }}</span>@endif
                            </span>
                            <span class="brand-subtitle"><span class="tally-dot"></span> {{ setting('app_tagline', 'CINE & CAMÉRAS MAROC') }}</span>
                        </div>
                    </a>

                    <!-- Mobile: always-visible actions (search + cart + user) + toggler -->
                    <div class="d-flex align-items-center gap-2 ms-auto d-lg-none">
                        <button class="action-btn-circle bg-transparent" type="button" id="mobileSearchTriggerBtn" title="Rechercher" aria-label="Rechercher">
                            <i class="fas fa-search"></i>
                        </button>
                        @auth
                            <a href="{{ route('dashboard') }}" class="action-btn-circle text-decoration-none" title="Mon compte">
                                <i class="far fa-user"></i>
                            </a>
                        @endauth
                        <div class="position-relative">
                            <button class="action-btn-circle bg-transparent" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                            <span id="header-cart-count-mobile" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem;">
                                {{ count(session('cart', [])) }}
                            </span>
                        </div>
                        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-expanded="false" aria-label="Menu" id="navbarMainToggler">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>

                    <!-- Collapsible section -->
                    <div class="collapse navbar-collapse" id="navbarMain">
                        <!-- Navigation links -->
                        <ul class="navbar-nav me-auto mb-0 gap-1 mb-3 mb-lg-0 align-items-lg-center">
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('Home') }}</a>
                            </li>
                            {{-- Boutique with Categories Hover Dropdown --}}
                            <li class="nav-item nav-item-dropdown position-relative">
                                <a class="nav-link-custom {{ request()->routeIs('shop.*') ? 'active' : '' }}" href="{{ route('shop.index') }}" id="boutiqueNavLink">
                                    <span>{{ __('Shop') }}</span>
                                    <i class="fas fa-chevron-down nav-chevron-icon"></i>
                                </a>

                                {{-- Category Hover Dropdown --}}
                                <div class="nav-categories-dropdown">
                                    <div class="nav-dropdown-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="tally-dot"></span>
                                            <span class="nav-dropdown-title">{{ strtoupper(__('Sections & Equipment')) }}</span>
                                        </div>
                                        <a href="{{ route('shop.index') }}" class="nav-dropdown-viewall">{{ __('View All') }}</a>
                                    </div>

                                    <div class="nav-dropdown-grid">
                                        @foreach($navCategories as $navCat)
                                            @php
                                                $icon = $catIcons[$navCat->slug] ?? 'fa-camera';
                                                $isCurrentCat = request('category') === $navCat->slug;
                                            @endphp
                                            <a href="{{ route('shop.index', ['category' => $navCat->slug]) }}" class="nav-cat-item {{ $isCurrentCat ? 'active' : '' }}">
                                                <div class="nav-cat-icon-box" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                    @if($navCat->image)
                                                        <img src="{{ $navCat->thumbnail }}" alt="{{ $navCat->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                                        <i class="fas {{ $icon }}" style="display: none;"></i>
                                                    @else
                                                        <i class="fas {{ $icon }}"></i>
                                                    @endif
                                                </div>
                                                <div class="nav-cat-text">
                                                    <span class="nav-cat-name">{{ $navCat->name }}</span>
                                                    <span class="nav-cat-count">{{ $navCat->products_count }} {{ __('refs.') }}</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>

                                    <div class="nav-dropdown-footer">
                                        <a href="{{ route('shop.index') }}" class="nav-dropdown-footer-link">
                                            <span>{{ __('Browse all categories') }}</span>
                                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">{{ __('About') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                            </li>
                        </ul>

                        <!-- Mobile in-menu search button + language switcher -->
                        <div class="d-lg-none mt-2 mb-3">
                            <button type="button" class="btn-search-mobile-bar w-100" id="inMenuSearchTrigger">
                                <i class="fas fa-search text-danger"></i>
                                <span>{{ __('Search equipment...') }}</span>
                            </button>
                            {{-- Mobile Language Switcher --}}
                            <div class="d-flex justify-content-center gap-2 mt-3">
                                <a href="{{ route('lang.switch', 'fr') }}"
                                   class="lang-btn {{ app()->getLocale() === 'fr' ? 'lang-btn-active' : '' }}"
                                   style="padding: 6px 16px; font-size: 0.82rem;">
                                    🇫🇷 Français
                                </a>
                                <a href="{{ route('lang.switch', 'ar') }}"
                                   class="lang-btn {{ app()->getLocale() === 'ar' ? 'lang-btn-active' : '' }}"
                                   style="padding: 6px 16px; font-size: 0.82rem;">
                                    🇲🇦 العربية
                                </a>
                            </div>
                        </div>

                        <!-- Search Form with Dynamic Live Search (Desktop) -->
                        <form action="{{ route('shop.index') }}" method="GET" class="header-search-wrap mx-lg-4 flex-grow-1 flex-lg-grow-0 mb-3 mb-lg-0 d-none d-lg-block" style="max-width: 420px;" id="headerSearchForm" autocomplete="off">
                            <div class="input-group search-input-group">
                                <span class="input-group-text ps-3 search-icon-slot">
                                    <i class="fas fa-search search-icon-default"></i>
                                    <span class="spinner-border spinner-border-sm text-danger search-spinner d-none" role="status" aria-hidden="true"></span>
                                </span>
                                <input class="form-control ps-2 header-search-input" 
                                       type="search" 
                                       name="q" 
                                       id="headerSearchInput" 
                                       placeholder="Rechercher boîtier, objectif, micro..." 
                                       aria-label="Rechercher" 
                                       value="{{ request('q') }}" 
                                       autocomplete="off"
                                       spellcheck="false">
                                <button class="btn btn-search-clear d-none" type="button" id="headerSearchClear" aria-label="Effacer">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-search-submit d-none d-sm-inline-flex" type="submit" aria-label="Lancer la recherche" title="Rechercher">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>

                            <!-- Live Search Floating Dropdown -->
                            <div class="header-search-dropdown shadow-lg d-none" id="headerSearchDropdown" role="region" aria-label="Résultats de recherche">
                                <!-- Frequent Searches Chips (Shown on focus when input is empty) -->
                                <div class="search-quick-tags p-3 border-bottom" id="headerSearchTags">
                                    <div class="search-section-label">
                                        <i class="fas fa-fire me-1 text-danger"></i> {{ strtoupper(__('Frequent Searches')) }}
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <button type="button" class="search-tag-chip" data-search="Sony Alpha">Sony Alpha</button>
                                        <button type="button" class="search-tag-chip" data-search="Blackmagic">Blackmagic</button>
                                        <button type="button" class="search-tag-chip" data-search="{{ app()->getLocale() === 'ar' ? 'عدسات' : 'Objectif' }}">{{ app()->getLocale() === 'ar' ? 'عدسات' : 'Objectifs' }}</button>
                                        <button type="button" class="search-tag-chip" data-search="{{ app()->getLocale() === 'ar' ? 'أجهزة الاستقرار' : 'Stabilisateur' }}">{{ app()->getLocale() === 'ar' ? 'أجهزة الاستقرار' : 'Stabilisateurs' }}</button>
                                        <button type="button" class="search-tag-chip" data-search="{{ app()->getLocale() === 'ar' ? 'ميكروفون' : 'Micro sans fil' }}">{{ app()->getLocale() === 'ar' ? 'ميكروفونات' : 'Micros sans fil' }}</button>
                                        <button type="button" class="search-tag-chip" data-search="Drone">{{ app()->getLocale() === 'ar' ? 'طائرات' : 'Drones' }}</button>
                                    </div>
                                </div>

                                <!-- Dynamic Results List -->
                                <div class="search-results-list" id="headerSearchResultsList"></div>

                                <!-- Empty State -->
                                <div class="search-empty-state text-center py-4 px-3 d-none" id="headerSearchEmpty">
                                    <div class="search-empty-icon mb-2">
                                        <i class="fas fa-search-minus fa-2x text-muted opacity-50"></i>
                                    </div>
                                    <div class="fw-bold text-dark mb-1">{{ __('No product found') }}</div>
                                    <div class="small text-muted mb-2">{{ __('No results match') }} « <span class="empty-query-text text-danger fw-semibold"></span> »</div>
                                    <div class="small text-muted">{{ __('Try model name, brand or category') }}</div>
                                </div>

                                <!-- View All Footer -->
                                <div class="search-dropdown-footer p-2 text-center border-top d-none" id="headerSearchFooter">
                                    <a href="#" class="search-view-all-link" id="headerSearchViewAll">
                                        <span>{{ __('View all results') }}</span>
                                        <span class="search-count-pill badge bg-danger ms-1" id="headerSearchCountBadge">0</span>
                                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Desktop-only actions -->
                        <div class="d-none d-lg-flex align-items-center gap-3 ms-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="action-btn-circle text-decoration-none" title="{{ __('My Account') }}">
                                    <i class="far fa-user"></i>
                                </a>
                            @endauth

                            <div class="position-relative">
                                <button class="action-btn-circle bg-transparent" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart">
                                    <i class="fas fa-shopping-bag"></i>
                                </button>
                                <span id="header-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem;">
                                    {{ count(session('cart', [])) }}
                                </span>
                            </div>

                            {{-- Language Switcher --}}
                            <div class="lang-switcher d-flex align-items-center gap-1">
                                <a href="{{ route('lang.switch', 'fr') }}"
                                   class="lang-btn {{ app()->getLocale() === 'fr' ? 'lang-btn-active' : '' }}"
                                   title="Français">
                                    <span class="fi fi-fr"></span>
                                    <span class="lang-label">FR</span>
                                </a>
                                <span class="lang-divider">|</span>
                                <a href="{{ route('lang.switch', 'ar') }}"
                                   class="lang-btn {{ app()->getLocale() === 'ar' ? 'lang-btn-active' : '' }}"
                                   title="العربية">
                                    <span class="fi fi-ma"></span>
                                    <span class="lang-label">AR</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>


    <main>
        @yield('content')
    </main>

    <!-- Pro Full-Screen Mobile Search Overlay -->
    <div class="pro-mobile-search-overlay" id="proMobileSearchOverlay" role="dialog" aria-modal="true" aria-label="Recherche mobile">
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
                           placeholder="Rechercher boîtier, optique, micro..." 
                           aria-label="Rechercher un produit ou un rayon"
                           autocomplete="off" 
                           autocapitalize="off" 
                           spellcheck="false">
                    <button type="button" class="pro-search-clear d-none" id="proMobileSearchClear" aria-label="Effacer la recherche">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </form>
            <button type="button" class="pro-mobile-search-close" id="proMobileSearchCloseBtn" aria-label="Fermer la recherche">
                Annuler
            </button>
        </div>

        <!-- Scrollable Content Body -->
        <div class="pro-mobile-search-body" id="proMobileSearchBody">
            <!-- 1. Default Discovery State (When query is empty) -->
            <div class="pro-search-suggestions" id="proMobileSearchSuggestions">
                <div class="pro-search-section">
                    <div class="pro-section-title">
                        <i class="fas fa-fire text-danger me-1"></i> RECHERCHES FRÉQUENTES
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
                            <i class="fas fa-search"></i> Objectif 24-70
                        </button>
                        <button type="button" class="pro-tag-chip" data-query="Stabilisateur RS 4">
                            <i class="fas fa-search"></i> Stabilisateur RS 4
                        </button>
                        <button type="button" class="pro-tag-chip" data-query="Mavic 3 Cine">
                            <i class="fas fa-search"></i> Mavic 3 Cine
                        </button>
                    </div>
                </div>

                <div class="pro-search-section mt-4">
                    <div class="pro-section-title">
                        <i class="fas fa-th-large text-danger me-1"></i> EXPLORER PAR RAYON
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
                                    <span class="pro-cat-card-count">{{ $cat->products_count }} réf.</span>
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
                <h6 class="pro-empty-title">Aucun produit trouvé</h6>
                <p class="pro-empty-subtitle">
                    Aucun résultat ne correspond à « <span class="pro-empty-query text-danger fw-bold"></span> »
                </p>
                <div class="pro-empty-tags-hint">
                    <span class="text-muted small d-block mb-2">Suggestions populaires :</span>
                    <div class="pro-tags-grid justify-content-center">
                        <button type="button" class="pro-tag-chip" data-query="Sony">Sony</button>
                        <button type="button" class="pro-tag-chip" data-query="Mic">Microphones</button>
                        <button type="button" class="pro-tag-chip" data-query="Objectif">Objectifs</button>
                        <button type="button" class="pro-tag-chip" data-query="Drone">Drones</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom View All Bar -->
        <div class="pro-mobile-search-footer d-none" id="proMobileSearchFooter">
            <a href="#" class="pro-search-view-all-btn" id="proMobileSearchViewAll">
                <span>Voir tous les résultats</span>
                <span class="badge bg-white text-danger rounded-pill ms-2 fw-bold" id="proMobileSearchCount">0</span>
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>

    <!-- Offcanvas Mini Cart -->
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="miniCart" aria-labelledby="miniCartLabel" style="width: 450px; background: #f8fafc;">
        <div class="offcanvas-header bg-white border-bottom py-3">
            <h5 class="offcanvas-title fw-bold font-heading" id="miniCartLabel">
                <i class="fas fa-shopping-bag me-2 text-primary"></i>Mon Panier
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
                            <div class="flex-shrink-0 me-3 position-relative">
                                @php
                                    $miniImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/'))) 
                                        ? asset(ltrim($details['image'], '/')) 
                                        : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                                @endphp
                                <img src="{{ $miniImg }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover" style="width: 80px; height: 80px;">
                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-light text-dark border shadow-sm" style="font-size: 0.7rem;">x{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold mb-1 text-truncate pe-4" title="{{ $details['name'] }}">{{ $details['name'] }}</h6>
                                <p class="mb-2 text-muted small">{{ $details['category_name'] ?? 'Produit' }}</p>
                                
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
                        <button class="btn btn-sm text-danger position-absolute top-0 end-0 mt-2 me-2 opacity-50 hover-opacity-100 transition-all" onclick="removeItem({{ $id }})" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @empty
                    <div class="text-center py-5 mt-5">
                        <div class="mb-4 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
                            <i class="fas fa-shopping-basket fa-3x text-muted opacity-25"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Votre panier est vide</h5>
                        <p class="text-muted small mb-4">Vous n'avez encore rien ajouté à votre panier.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 shadow-sm">Commencer les achats</a>
                    </div>
                @endforelse
            </div>
            
            @if(count(session('cart', [])) > 0)
            <div class="border-top p-4 bg-white mt-auto shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <span class="text-muted small text-uppercase fw-bold ls-1">Sous-total</span>
                    <span class="h4 fw-bold text-dark mb-0 ls-tight" id="mini-cart-total">{{ currency($total) }}</span>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-between align-items-center px-4">
                        <span>Commander</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="btn btn-light py-2 rounded-pill fw-bold text-muted small">
                        Voir le panier complet
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <footer class="footer-modern">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <h5 class="fw-bold text-white mb-3 text-uppercase ls-1 d-flex align-items-center gap-2">
                        <span class="tally-dot"></span> {{ setting('app_name', 'LUMINA Cine & Optics') }}
                    </h5>
                    <p class="small lh-lg mb-4 text-slate-400">
                        @if(app()->getLocale() === 'ar')
                            شريكك المرجعي في المعدات الصوتية البصرية، كاميرات السينما، عدسات البث، أجهزة الاستقرار وإضاءة الاستوديو في المغرب. معرض، عروض توضيحية وتوصيل آمن في جميع أنحاء المملكة.
                        @else
                            Votre partenaire de référence en équipement audiovisuel, caméras de cinéma, objectifs broadcast, stabilisateurs et éclairage studio au Maroc. Showroom, démonstrations et livraison sécurisée dans tout le Royaume.
                        @endif
                    </p>
                    @php
                        $sfb  = setting('social_facebook',  '');
                        $stw  = setting('social_twitter',   '');
                        $sig  = setting('social_instagram', '');
                        $sli  = setting('social_linkedin',  '');
                        $swa  = setting('social_whatsapp',  '');
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
                        <li><a href="{{ route('about') }}" class="footer-link small">{{ __('About') }} {{ setting('app_name', 'LUMINA') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="footer-link small">Showroom & {{ __('Contact') }}</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase ls-1">{{ __('Customer Service') }}</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('contact') }}" class="footer-link small">{{ __('Request a Quote') }}</a></li>
                        <li><a href="{{ route('customer.orders') }}" class="footer-link small">{{ __('Track Order') }}</a></li>
                        <li><a href="{{ route('about') }}#garantie" class="footer-link small">{{ __('Warranty') }}</a></li>
                        <li><a href="{{ route('contact') }}#faq" class="footer-link small">{{ __('FAQ') }}</a></li>
                    </ul>
                </div>

            </div>
            
            <hr class="border-secondary opacity-25 my-5">
            
            <div class="row align-items-center">
                <div class="col-md-12 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-center mb-0">&copy; {{ date('Y') }} {{ setting('app_name', 'LUMINA Cine & Optics') }}. {{ __('All rights reserved') }}. {{ app()->getLocale() === 'ar' ? 'تطوير' : 'Développé par' }} <a href="https://elegantboost.com/" target="_blank" class="text-white text-decoration-none fw-bold hover-primary transition-all">Elegant Boost</a>.</p>
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
                const updateCartBadges = (count) => {
                    ['header-cart-count', 'header-cart-count-mobile'].forEach(bId => {
                        const el = document.getElementById(bId);
                        if (el && count !== undefined) el.textContent = count;
                    });
                };
                updateCartBadges(data.cartCount);

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
                    title: error.message || 'Erreur lors de la mise à jour du panier',
                    showConfirmButton: false,
                    timer: 2500
                });
            });
        }

        function removeItem(id) {
            Swal.fire({
                title: 'Retirer du panier ?',
                text: "Voulez-vous supprimer cet article ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
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
                        ['header-cart-count', 'header-cart-count-mobile'].forEach(bId => {
                            const el = document.getElementById(bId);
                            if (el && data.cartCount !== undefined) el.textContent = data.cartCount;
                        });

                        // If on /cart page, remove the row or reload if empty
                        const row = document.getElementById('cart-row-' + id);
                        if (row) {
                            row.style.transition = 'opacity 0.25s, transform 0.25s';
                            row.style.opacity = '0';
                            row.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                row.remove();
                                if (data.isEmpty || document.querySelectorAll('[id^="cart-row-"]').length === 0) {
                                    window.location.reload();
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
                            title: 'Article supprimé !',
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
                            title: 'Erreur lors de la suppression',
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
                    const footerSection = cartOffcanvas.querySelector('.border-top.p-4');
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
                            <i class="fas fa-th-large me-1 text-danger"></i> RAYONS CORRESPONDANTS
                        </div>
                        <div class="search-category-suggestions px-2 pb-2">
                    `;
                    data.categories.forEach(cat => {
                        html += `
                            <a href="${cat.url}" class="search-cat-item">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="search-cat-icon"><i class="fas fa-arrow-right text-danger"></i></span>
                                    <span class="search-cat-text">Dans <strong>${highlightText(cat.name, query)}</strong></span>
                                </div>
                                <span class="badge bg-light text-muted border">${cat.count} réf.</span>
                            </a>
                        `;
                    });
                    html += `</div>`;
                }

                // Products proposals
                if (hasProducts) {
                    html += `
                        <div class="search-section-label px-3 pt-2 pb-1 ${hasCategories ? 'border-top' : ''}">
                            <i class="fas fa-camera me-1 text-danger"></i> ÉQUIPEMENTS & MODÈLES (${data.count || data.products.length})
                        </div>
                    `;
                    data.products.forEach((p, idx) => {
                        const priceHtml = p.is_sale 
                            ? `<span class="search-result-price">${escapeHtml(p.sale_price)}</span> <span class="search-result-price-old">${escapeHtml(p.price)}</span>`
                            : `<span class="search-result-price">${escapeHtml(p.price)}</span>`;

                        const stockHtml = p.in_stock 
                            ? `<span class="search-result-stock">En stock</span>`
                            : `<span class="search-result-stock out">Sur commande</span>`;

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
                            <i class="fas fa-th-large text-danger me-1"></i> RAYONS CORRESPONDANTS
                        </div>
                    `;
                    data.categories.forEach(cat => {
                        html += `
                            <a href="${cat.url}" class="pro-cat-match-card">
                                <div class="pro-cat-match-left">
                                    <span class="pro-cat-match-icon"><i class="fas fa-arrow-right"></i></span>
                                    <span class="pro-cat-match-title">Dans <strong>${highlightText(cat.name, query)}</strong></span>
                                </div>
                                <span class="badge bg-light text-muted border">${cat.count} réf.</span>
                            </a>
                        `;
                    });
                }

                // Matching Products
                if (hasProds) {
                    html += `
                        <div class="pro-section-title mt-3 mb-2">
                            <i class="fas fa-camera text-danger me-1"></i> ÉQUIPEMENTS & MODÈLES (${data.count || data.products.length})
                        </div>
                    `;
                    data.products.forEach(p => {
                        const priceHtml = p.is_sale 
                            ? `<span class="pro-result-price">${escapeHtml(p.sale_price)}</span> <span class="pro-result-price-old">${escapeHtml(p.price)}</span>`
                            : `<span class="pro-result-price">${escapeHtml(p.price)}</span>`;

                        const stockHtml = p.in_stock 
                            ? `<span class="pro-result-stock">En stock</span>`
                            : `<span class="pro-result-stock out">Sur commande</span>`;

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
