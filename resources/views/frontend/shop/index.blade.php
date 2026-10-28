@extends('layouts.frontend')

@php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->name . ' — ' . setting('app_name', 'WINA SHOP'))
        : (request('q') ? 'Résultats pour "' . request('q') . '" — ' . setting('app_name', 'WINA SHOP') : 'Catalogue Matériel Photo & Vidéo — ' . setting('app_name', 'WINA SHOP'));
    $pageDescription = $activeCategory
        ? ('Découvrez notre gamme de ' . $activeCategory->name . '. Garantie constructeur, showroom à Casablanca et livraison express partout au Maroc.')
        : 'Parcourez notre catalogue complet de matériel photo et vidéo, caméras, drones DJI, objectifs, stabilisateurs et éclairage studio au Maroc.';
    $pageKeywords = $activeCategory
        ? ($activeCategory->name . ', ' . setting('app_name', 'WINA SHOP') . ', acheter ' . $activeCategory->name . ' Maroc, prix ' . $activeCategory->name)
        : setting('app_name', 'WINA SHOP') . ', caméras cinéma, objectifs photo, stabilisateurs DJI, éclairage vidéo, Casablanca, Maroc';
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_keywords', $pageKeywords)

@section('json_ld')
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Accueil",
        "item": "{{ url('/') }}"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Boutique",
        "item": "{{ route('shop.index') }}"
      }
      @if($activeCategory)
      ,{
        "@type": "ListItem",
        "position": 3,
        "name": "{{ addslashes($activeCategory->name) }}",
        "item": "{{ route('shop.index', ['category' => $activeCategory->slug]) }}"
      }
      @endif
    ]
  },
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "{{ addslashes($pageTitle) }}",
    "url": "{{ url()->current() }}",
    "numberOfItems": {{ $products->total() }},
    "itemListElement": [
      @foreach($products as $i => $prod)
      {
        "@type": "ListItem",
        "position": {{ $i + 1 }},
        "url": "{{ route('shop.show', $prod->slug ?: $prod->id) }}",
        "name": "{{ addslashes($prod->name) }}"
      }{{ !$loop->last ? ',' : '' }}
      @endforeach
    ]
  }
]
</script>
@endsection

@section('content')

{{-- =============================================
     SHOP HERO STRIP (Cinema Viewfinder Theme)
     ============================================= --}}
<section class="shop-hero">
    <div class="shop-hero-backdrop"></div>
    <div class="container position-relative">
        <div class="shop-hero-content" data-aos="fade-up">
            {{-- Breadcrumb --}}
            <nav class="shop-breadcrumb mb-3" aria-label="breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i> {{ __('Accueil') }}</a>
                <span class="shop-bc-sep mx-2 opacity-50">/</span>
                <a href="{{ route('shop.index') }}">{{ __('Boutique') }}</a>
                @if(request('category'))
                    <span class="shop-bc-sep mx-2 opacity-50">/</span>
                    <span class="fw-bold hero-bc-category" id="heroBcCategory">{{ $categories->where('slug', request('category'))->first()->name ?? __('Rayon') }}</span>
                @endif
            </nav>

            <h1 class="shop-hero-title mb-2">
                @if(request('q'))
                    {{ __('Résultats pour') }} <span class="text-brand-red">&laquo; {{ request('q') }} &raquo;</span>
                @elseif(request('category'))
                    <span class="text-brand-red">{{ $categories->where('slug', request('category'))->first()->name ?? __('Produits') }}</span>
                @else
                    {{ __('Caméras & Optiques Cinéma') }}
                @endif
            </h1>
            <p class="shop-hero-sub d-none d-md-block">
                {{ __('Explorez les caméras cinéma 4K/8K, objectifs pro, drones DJI, stabilisateurs et éclairage studio avec garantie constructeur au Maroc.') }}
            </p>
        </div>
    </div>
</section>

{{-- =============================================
     MAIN SHOP LAYOUT
     ============================================= --}}
<section class="shop-body">
    <div class="container-shop px-2 px-md-4">
        {{-- ── MOBILE CATEGORY SELECTOR CARD (Prominent Full-width Dropdown) ── --}}
        @php
            $catIcons = [
                'cameras-hybrides'            => 'fa-camera',
                'camera'                      => 'fa-camera',
                'objectifs-optiques'          => 'fa-circle-notch',
                'objectifs'                   => 'fa-circle-notch',
                'stabilisateurs-gimbals'      => 'fa-video',
                'stabilisateurs'              => 'fa-video',
                'eclairage-studio'            => 'fa-lightbulb',
                'lumieres-materiel-de-studio' => 'fa-lightbulb',
                'audio-micros-sans-fil'       => 'fa-microphone',
                'son'                         => 'fa-microphone',
                'drones-cine'                 => 'fa-helicopter',
                'trepieds'                    => 'fa-expand-alt',
                'sacs-de-camera'              => 'fa-suitcase',
                'batterie-chargeur'           => 'fa-battery-full',
                'carte-memoire-lecteur'       => 'fa-sd-card',
                'materiel-de-podcast'         => 'fa-podcast',
                'filtres-nd-cpl-polarise'     => 'fa-adjust',
                'matte-box'                   => 'fa-film',
                'accessoires-insta360'        => 'fa-cube',
                'kit-de-nettoyage'            => 'fa-spray-can',
            ];
            $currentMobileCat = $categories->where('slug', request('category'))->first();
            $currentMobileCatImg = $currentMobileCat && !empty($currentMobileCat->image) && file_exists(public_path($currentMobileCat->image))
                ? asset($currentMobileCat->image)
                : null;
            $currentMobileCatIcon = $currentMobileCat ? ($catIcons[$currentMobileCat->slug] ?? 'fa-tag') : 'fa-layer-group';
        @endphp

        {{-- ── MOBILE HORIZONTAL CATEGORY SCROLLER STRIP (Direct Category Pills) ── --}}
        <button type="button" class="d-none" id="mobileCategoryDropdownBtn" aria-hidden="true"></button>
        <div class="shop-mobile-categories-wrap d-none mb-2">
            <div class="shop-mobile-cat-scroll">
                <button type="button" class="mobile-cat-pill {{ !request('category') ? 'active' : '' }}" data-slug="" data-name="{{ __('Tout le catalogue') }}">
                    <i class="fas fa-th-large"></i>
                    <span>{{ __('Tous') }}</span>
                    <span class="mobile-cat-badge">{{ \App\Models\Product::where('status','active')->count() }}</span>
                </button>
                @foreach($categories as $cat)
                @php
                    $cIcon = !empty($cat->icon) ? (str_starts_with($cat->icon, 'fa') ? $cat->icon : 'fas fa-' . $cat->icon) : ($catIcons[$cat->slug] ?? 'fas fa-camera');
                    $cImg = $cat->image_url;
                @endphp
                <button type="button" class="mobile-cat-pill {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}" data-name="{{ $cat->name }}">
                    @if($cImg)
                        <img src="{{ $cImg }}" alt="{{ $cat->name }}" width="20" height="20" class="mobile-cat-pill-img" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                        <i class="{{ $cIcon }}" style="display: none;"></i>
                    @else
                        <i class="{{ $cIcon }}"></i>
                    @endif
                    <span>{{ $cat->name }}</span>
                    <span class="mobile-cat-badge">{{ $cat->products_count }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- ── MOBILE STICKY CONTROL TOOLBAR (Filters, Brands Dropdown, Sort) ── --}}
        @php
            $toolbarBrands = $smartBrands ?? collect();
            $activeBrandName = isset($activeBrand) && $activeBrand ? $activeBrand->name : '';
        @endphp
        <div class="shop-mobile-toolbar d-lg-none">
            <div class="shop-mobile-toolbar-row d-flex">
                {{-- 1. Filter trigger button --}}
                <button type="button" class="btn-mobile-tool m-auto" data-bs-toggle="offcanvas" data-bs-target="#shopFilterSheet" id="mobileFilterTrigger">
                    <i class="fas fa-sliders-h text-danger flex-shrink-0"></i>
                    <span class="text-truncate">{{ __('Filtres') }}</span>
                    <span class="badge rounded-pill bg-danger d-none ms-0.5" id="mobileFilterBadge">0</span>
                </button>

                {{-- 2. Brand Dropdown --}}
                <div class="dropdown m-auto" id="mobileBrandDropdownContainer" style="{{ $toolbarBrands->isEmpty() ? 'display: none;' : '' }}">
                    <button class="btn-mobile-tool justify-content-between {{ $activeBrandName ? 'has-brand' : '' }}" 
                            type="button" 
                            id="mobileBrandDropdownBtn" 
                            data-bs-toggle="dropdown" 
                            data-bs-auto-close="outside"
                            aria-expanded="false">
                        <span class="d-flex align-items-center gap-1 text-truncate">
                            <i class="fas fa-tags text-danger flex-shrink-0" style="font-size: 0.8rem;"></i>
                            <span class="text-truncate" id="mobileBrandLabel">{{ $activeBrandName ? $activeBrandName : __('Marque') }}</span>
                        </span>
                        <i class="fas fa-chevron-down opacity-50 ms-0.5 flex-shrink-0" style="font-size: 0.6rem;"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-start shadow-lg border-0 rounded-4 p-2 mt-2" 
                         aria-labelledby="mobileBrandDropdownBtn" 
                         id="mobileBrandDropdownMenu"
                         style="min-width: 230px; max-width: 290px; z-index: 1095;">
                        <div class="d-flex align-items-center justify-content-between px-2 py-1 mb-1.5 border-bottom">
                            <span class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">
                                <i class="fas fa-tags text-danger me-1"></i>{{ __('Marques') }}
                            </span>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none text-danger p-0 shop-brand-link" 
                                    id="mobileBrandResetLink" 
                                    data-slug="" 
                                    data-name="{{ __('Toutes les marques') }}" 
                                    style="font-size: 0.72rem; {{ !request('brand') ? 'display: none;' : '' }}">
                                <i class="fas fa-times me-0.5"></i>{{ __('Effacer') }}
                            </button>
                        </div>
                        <div class="toolbar-brand-list-wrap" style="max-height: 250px; overflow-y: auto;">
                            {{-- Option: Toutes les marques --}}
                            <button type="button" class="dropdown-item rounded-3 py-1.5 px-2 d-flex align-items-center justify-content-between shop-brand-link mb-1 {{ !request('brand') ? 'active' : '' }}" 
                                    data-slug="" 
                                    data-name="{{ __('Toutes les marques') }}">
                                <span class="d-flex align-items-center gap-2">
                                    <span class="cat-slot-box" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px;">
                                        <i class="fas fa-th-large text-muted" style="font-size: 0.7rem;"></i>
                                    </span>
                                    <span class="fw-medium" style="font-size: 0.82rem;">{{ __('Toutes les marques') }}</span>
                                </span>
                            </button>

                            {{-- Individual Brands --}}
                            <div id="mobileBrandItemsContainer">
                                @foreach($toolbarBrands as $b)
                                <button type="button" class="dropdown-item rounded-3 py-1.5 px-2 d-flex align-items-center justify-content-between shop-brand-link mb-0.5 {{ request('brand') == $b->slug ? 'active' : '' }}" 
                                        data-slug="{{ $b->slug }}" 
                                        data-name="{{ $b->name }}">
                                    <span class="d-flex align-items-center gap-2 text-truncate me-2">
                                        <span class="cat-slot-box" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; flex-shrink: 0;">
                                            @if($b->logo)
                                                <img src="{{ $b->logo_url }}" alt="{{ $b->name }}" width="16" height="16" class="cat-slot-media" style="object-fit: contain;" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                                <i class="fas fa-tag text-muted" style="display: none; font-size: 0.7rem;"></i>
                                            @else
                                                <i class="fas fa-tag text-muted" style="font-size: 0.7rem;"></i>
                                            @endif
                                        </span>
                                        <span class="fw-medium text-truncate" style="font-size: 0.82rem;">{{ $b->name }}</span>
                                    </span>
                                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        <span class="badge cat-mega-count rounded-pill" style="font-size: 0.68rem;">{{ $b->products_count }}</span>
                                    </div>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Sort Dropdown --}}
                <div class="dropdown m-auto">
                    <button class="btn-mobile-tool justify-content-between" type="button" id="mobileSortDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-flex align-items-center gap-1 text-truncate">
                            <i class="fas fa-sort-amount-down text-muted flex-shrink-0" style="font-size: 0.8rem;"></i>
                            <span class="text-truncate" id="mobileSortLabel">{{ __('Nouveautés') }}</span>
                        </span>
                        <i class="fas fa-chevron-down opacity-50 ms-0.5 flex-shrink-0" style="font-size: 0.6rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2" aria-labelledby="mobileSortDropdownBtn" style="min-width: 190px; z-index: 1095;">
                        <li><button class="dropdown-item rounded-3 py-2 mobile-sort-item {{ request('sort') == 'newest' || !request('sort') ? 'active' : '' }}" type="button" data-sort="newest"><i class="fas fa-clock me-2"></i>{{ __('Nouveautés') }}</button></li>
                        <li><button class="dropdown-item rounded-3 py-2 mobile-sort-item {{ request('sort') == 'price_asc' ? 'active' : '' }}" type="button" data-sort="price_asc"><i class="fas fa-arrow-up-1-9 me-2"></i>{{ __('Prix : Croissant') }}</button></li>
                        <li><button class="dropdown-item rounded-3 py-2 mobile-sort-item {{ request('sort') == 'price_desc' ? 'active' : '' }}" type="button" data-sort="price_desc"><i class="fas fa-arrow-down-9-1 me-2"></i>{{ __('Prix : Décroissant') }}</button></li>
                    </ul>
                </div>

                {{-- View Mode Toggle (hidden on mobile as requested) --}}
                <button type="button" class="btn-mobile-tool btn-mobile-view-toggle d-none" id="mobileViewToggle" title="{{ __('Changer la disposition') }}" aria-label="{{ __('Changer la disposition') }}" style="display: none !important;">
                    <i class="fas fa-th-large d-none" id="viewToggleIcon"></i>
                </button>
            </div>

            {{-- Result Count & Removable Filter Chips --}}
            <div class="mobile-active-strip d-flex align-items-center justify-content-between pt-2">
                <div class="mobile-results-count text-muted">
                    <span id="mobileProductTotal" class="fw-bold text-dark">{{ $products->total() }}</span> {{ __('produits') }}
                </div>
                <div class="mobile-chips-scroll" id="mobileActiveChipsContainer"></div>
            </div>
        </div>

        <div class="row gx-0 gx-lg-3 gx-xl-4 gy-3 gy-lg-4 align-items-start">
            {{-- ── SIDEBAR (Desktop) ── --}}
            <div class="col-lg-3 col-xl-3 d-none d-lg-block shop-sidebar-col">
                <div class="shop-sidebar sticky-top" style="top: 24px;">
                    @include('frontend.shop.partials.sidebar-content')
                </div>
            </div>

            {{-- ── PRODUCT GRID ── --}}
            <div class="col-lg-9 col-xl-9 flex-grow-1 shop-main-col">
                {{-- Toolbar (Desktop only) --}}
                <div class="shop-toolbar mb-4 d-none d-lg-flex align-items-center justify-content-between gap-3">
                    <div class="shop-toolbar-left flex-shrink-0 d-flex align-items-center gap-2.5">
                        <h2 class="shop-toolbar-title" id="categoryTitle">
                            @if(request('category'))
                                {{ $categories->where('slug', request('category'))->first()->name ?? __('Produits') }}
                            @else
                                {{ __('Tout le catalogue') }}
                            @endif
                        </h2>
                        <span class="shop-toolbar-count" id="desktopProductTotalBadge">{{ $products->total() }} {{ __('produits') }}</span>
                    </div>

                    <div class="shop-toolbar-right d-flex align-items-center gap-2.5 flex-grow-1 justify-content-end">
                        {{-- Search in Top Bar --}}
                        <form id="searchForm" class="shop-toolbar-search flex-grow-1">
                            <div class="shop-search-wrap position-relative">
                                <i class="fas fa-search shop-search-icon-inline position-absolute text-muted" style="left: 13px; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 0.82rem;"></i>
                                <input type="text" name="q" class="shop-search-input w-100" style="padding-left: 34px; padding-right: 38px;"
                                       placeholder="{{ __('Rechercher un modèle, marque...') }}" value="{{ request('q') }}" autocomplete="off">
                                <button type="submit" class="shop-search-btn position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%);" aria-label="{{ __('Lancer la recherche') }}" title="{{ __('Rechercher') }}">
                                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                                </button>
                            </div>
                        </form>

                        {{-- Brand Dropdown in Toolbar --}}
                        @php
                            $toolbarBrands = $smartBrands ?? collect();
                            $activeBrandName = isset($activeBrand) && $activeBrand ? $activeBrand->name : '';
                        @endphp
                        <div class="shop-toolbar-brand position-relative flex-shrink-0" id="toolbarBrandDropdownContainer" style="{{ $toolbarBrands->isEmpty() ? 'display: none;' : '' }}">
                            <div class="dropdown">
                                <button class="shop-brand-dropdown-btn d-flex align-items-center gap-2 border bg-white rounded-3 px-3 py-1.5 shadow-none {{ $activeBrandName ? 'has-brand' : '' }}" 
                                        type="button" 
                                        id="toolbarBrandDropdownBtn" 
                                        data-bs-toggle="dropdown" 
                                        data-bs-auto-close="outside"
                                        aria-expanded="false" 
                                        style="height: 38px; cursor: pointer; border-color: #e5e7eb !important; transition: all 0.2s ease;">
                                    <i class="fas fa-tags text-danger" style="font-size: 0.85rem;"></i>
                                    <span class="fw-semibold text-dark text-truncate" id="toolbarBrandLabel" style="max-width: 130px; font-size: 0.84rem;">
                                        {{ $activeBrandName ? $activeBrandName : __('Marque') }}
                                    </span>
                                    <span class="badge rounded-pill bg-light text-muted border font-monospace px-1.5 py-0.5" id="toolbarBrandCountBadge" style="font-size: 0.7rem;">
                                        {{ $toolbarBrands->count() }}
                                    </span>
                                    <i class="fas fa-chevron-down text-muted ms-0.5" style="font-size: 0.65rem;"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2.5 mt-2" 
                                     aria-labelledby="toolbarBrandDropdownBtn" 
                                     id="toolbarBrandDropdownMenu"
                                     style="min-width: 260px; max-width: 300px; z-index: 1050;">
                                    
                                    {{-- Header inside dropdown --}}
                                    <div class="d-flex align-items-center justify-content-between px-2 py-1 mb-2 border-bottom">
                                        <span class="fw-bold text-dark text-uppercase fs-xs" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                            <i class="fas fa-tags text-danger me-1"></i>{{ __('Marques') }}
                                        </span>
                                        <button type="button" class="btn btn-link btn-sm text-decoration-none text-danger p-0 shop-brand-link" 
                                                id="toolbarBrandResetLink" 
                                                data-slug="" 
                                                data-name="{{ __('Toutes les marques') }}" 
                                                style="font-size: 0.75rem; {{ !request('brand') ? 'display: none;' : '' }}">
                                            <i class="fas fa-times me-0.5"></i>{{ __('Effacer') }}
                                        </button>
                                    </div>

                                    {{-- Live Search box if more than 5 brands --}}
                                    <!--div class="cat-pro-search-box mb-2 px-1 d-none" id="toolbarBrandSearchBox" style="{{ $toolbarBrands->count() <= 5 ? 'display: none;' : '' }}">
                                        <div class="position-relative">
                                            <i class="fas fa-search position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.75rem; pointer-events: none;"></i>
                                            <input type="text" 
                                                   class="form-control form-control-sm border rounded-pill" 
                                                   id="toolbarBrandSearchInput" 
                                                   placeholder="{{ __('Filtrer les marques...') }}" 
                                                   autocomplete="off" 
                                                   style="padding-left: 28px; font-size: 0.8rem;">
                                        </div>
                                    </div-->

                                    {{-- Brand Items Scrollable List --}}
                                    <div class="toolbar-brand-list-wrap" id="toolbarBrandListWrap" style="max-height: 250px; overflow-y: auto;">
                                        {{-- Option: Toutes les marques --}}
                                        <button type="button" class="dropdown-item rounded-3 py-1.5 px-2.5 d-flex align-items-center justify-content-between shop-brand-link mb-1 {{ !request('brand') ? 'active' : '' }}" 
                                                data-slug="" 
                                                data-name="{{ __('Toutes les marques') }}">
                                            <span class="d-flex align-items-center gap-2">
                                                <span class="cat-slot-box" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px;">
                                                    <i class="fas fa-th-large text-muted" style="font-size: 0.75rem;"></i>
                                                </span>
                                                <span class="fw-medium" style="font-size: 0.84rem;">{{ __('Toutes les marques') }}</span>
                                            </span>
                                            <i class="fas fa-check cat-item-check" style="font-size: 0.75rem;"></i>
                                        </button>

                                        {{-- Individual Brands --}}
                                        <div id="toolbarBrandItemsContainer">
                                            @foreach($toolbarBrands as $b)
                                            <button type="button" class="dropdown-item rounded-3 py-1.5 px-2.5 d-flex align-items-center justify-content-between shop-brand-link mb-0.5 {{ request('brand') == $b->slug ? 'active' : '' }}" 
                                                    data-slug="{{ $b->slug }}" 
                                                    data-name="{{ $b->name }}">
                                                <span class="d-flex align-items-center gap-2 text-truncate me-2">
                                                    <span class="cat-slot-box" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; flex-shrink: 0;">
                                                        @if($b->logo)
                                                            <img src="{{ $b->logo_url }}" alt="{{ $b->name }}" width="18" height="18" class="cat-slot-media" style="object-fit: contain;" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                                            <i class="fas fa-tag text-muted" style="display: none; font-size: 0.75rem;"></i>
                                                        @else
                                                            <i class="fas fa-tag text-muted" style="font-size: 0.75rem;"></i>
                                                        @endif
                                                    </span>
                                                    <span class="fw-medium text-truncate" style="font-size: 0.84rem;">{{ $b->name }}</span>
                                                </span>
                                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                    <span class="badge cat-mega-count rounded-pill" style="font-size: 0.7rem;">{{ $b->products_count }}</span>
                                                </div>
                                            </button>
                                            @endforeach
                                        </div>

                                        <div class="text-center py-2 text-muted small" id="toolbarBrandNoResults" style="display: none; font-size: 0.78rem;">
                                            <i class="fas fa-search me-1 opacity-50"></i>{{ __('Aucune marque trouvée') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sort Dropdown in Toolbar --}}
                        <div class="shop-toolbar-sort d-flex align-items-center gap-2 flex-shrink-0">
                            <label class="shop-sort-label"><i class="fas fa-sort-amount-down text-danger me-1"></i>{{ __('Trier par :') }}</label>
                            <select class="shop-sort-select" id="sortSelect">
                                <option value="newest"  {{ request('sort') == 'newest'     ? 'selected' : '' }}>{{ __('Nouveautés') }}</option>
                                <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>{{ __('Prix : Croissant') }}</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Prix : Décroissant') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Active Filters (Desktop) --}}
                <div class="shop-active-filters mb-4 d-none d-lg-flex {{ (request('q') || request('category') || request('brand') || request('min_price') || request('max_price')) ? '' : 'd-none' }}" id="desktopActiveFilters">
                    <span class="shop-active-label">{{ __('Filtres actifs :') }}</span>
                    <span id="desktopActiveFilterChips">
                        @if(request('q'))
                            <span class="shop-filter-tag cursor-pointer" onclick="removeFilter('q')">{{ __('Recherche :') }} « {{ request('q') }} » <i class="fas fa-times ms-1 small"></i></span>
                        @endif
                        @if(request('category'))
                            <span class="shop-filter-tag cursor-pointer" onclick="removeFilter('category')">{{ __('Rayon :') }} {{ $categories->where('slug', request('category'))->first()->name ?? request('category') }} <i class="fas fa-times ms-1 small"></i></span>
                        @endif
                        @if(request('brand'))
                            <span class="shop-filter-tag cursor-pointer" onclick="removeFilter('brand')">{{ __('Marque :') }} {{ $activeBrand->name ?? request('brand') }} <i class="fas fa-times ms-1 small"></i></span>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <span class="shop-filter-tag cursor-pointer" onclick="removeFilter('price')">{{ __('Budget :') }} {{ request('min_price', '0') }} — {{ request('max_price', '∞') }} DH <i class="fas fa-times ms-1 small"></i></span>
                        @endif
                    </span>
                    <a href="#" class="shop-clear-link" id="desktopResetFiltersBtn">
                        <i class="fas fa-times me-1"></i>{{ __('Effacer tout') }}
                    </a>
                </div>

                {{-- Skeleton Loader Grid (Shimmer during AJAX) --}}
                <div id="shopSkeletonGrid" class="row g-2 g-md-3 d-none mb-4">
                    @for($s = 0; $s < 6; $s++)
                    <div class="col-6 col-sm-6 col-md-4 col-xl-4 col-xxl-3 product-col">
                        <div class="shop-skeleton-card">
                            <div class="shop-skeleton-img"></div>
                            <div class="shop-skeleton-body">
                                <div class="shop-skeleton-line short"></div>
                                <div class="shop-skeleton-line full"></div>
                                <div class="shop-skeleton-line medium"></div>
                                <div class="shop-skeleton-line price"></div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- Product Grid (AJAX-swapped partial) --}}
                <div id="productGridContainer">
                    @include('frontend.shop.partials.product-grid')
                </div>

                {{-- Fallback Loader Spinner --}}
                <div id="loader" class="d-none text-center py-5">
                    <div class="shop-loader-spinner"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── MOBILE DEDICATED BOTTOM SHEET FILTER DRAWER ── --}}
<div class="offcanvas offcanvas-bottom shop-bottom-sheet d-lg-none" tabindex="-1" id="shopFilterSheet" aria-labelledby="shopFilterSheetLabel">
    <div class="sheet-drag-handle"></div>
    <div class="offcanvas-header border-bottom py-2.5 px-3 px-sm-4">
        <div class="d-flex align-items-center gap-2.5">
            <span class="sheet-header-icon-box">
                <i class="fas fa-sliders-h"></i>
            </span>
            <div>
                <div class="offcanvas-title h5 fw-bold mb-0" id="shopFilterSheetLabel" style="font-size: 0.98rem; letter-spacing: -0.2px;">{{ __('Filtres & Préférences') }}</div>
                <div class="sheet-header-sub text-muted" id="sheetActiveFiltersSub">{{ __('Affinez selon votre équipement et budget') }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-sheet-reset" id="sheetResetBtn" title="{{ __('Réinitialiser les filtres') }}">
                <i class="fas fa-undo-alt me-1"></i><span>{{ __('Réinitialiser') }}</span>
            </button>
            <button type="button" class="btn-sheet-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Fermer') }}">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="offcanvas-body px-3 py-3 px-sm-4">
        {{-- Section 1: Search inside shop --}}
        <div class="filter-section">
            <label class="filter-section-title mb-2">
                <span class="filter-title-icon"><i class="fas fa-search"></i></span>
                <span>{{ __('Rechercher par mot-clé') }}</span>
            </label>
            <div class="sheet-input-wrap">
                <i class="fas fa-search sheet-input-icon"></i>
                <input type="text" class="sheet-form-control" id="sheetSearchInput" placeholder="{{ __('Modèle, boîtier, marque, optique...') }}" value="{{ request('q') }}" autocomplete="off">
                <button type="button" class="sheet-input-clear {{ request('q') ? '' : 'd-none' }}" id="sheetSearchClearBtn" aria-label="{{ __('Effacer') }}">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
        </div>

        {{-- Section 2: Budget (MAD) --}}
        <div class="filter-section">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="filter-section-title mb-0">
                    <span class="filter-title-icon"><i class="fas fa-coins"></i></span>
                    <span>{{ __('Budget (MAD)') }}</span>
                </label>
                <button type="button" class="sheet-clear-text-btn {{ (request('min_price') || request('max_price')) ? '' : 'd-none' }}" id="sheetPriceResetBtn">
                    <i class="fas fa-undo-alt me-1"></i>{{ __('Effacer le prix') }}
                </button>
            </div>
            <div class="shop-price-inputs-row mb-2">
                <div class="shop-price-col">
                    <div class="price-field-micro-label">{{ __('Minimum') }}</div>
                    <label for="sheetMinPrice" class="price-input-box mb-0">
                        <input type="number" class="price-number-input shop-price-input" id="sheetMinPrice" placeholder="0" value="{{ request('min_price') }}" min="0" max="65000" step="50" inputmode="numeric">
                    </label>
                </div>
                <div class="price-range-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <div class="shop-price-col">
                    <div class="price-field-micro-label">{{ __('Maximum') }}</div>
                    <label for="sheetMaxPrice" class="price-input-box mb-0">
                        <input type="number" class="price-number-input shop-price-input" id="sheetMaxPrice" placeholder="Max" value="{{ request('max_price') }}" min="0" max="65000" step="50" inputmode="numeric">
                    </label>
                </div>
            </div>
            <div class="sidebar-budget-grid">
                <button type="button" class="sheet-budget-chip sidebar-budget-chip" data-min="0" data-max="1000">&lt; 1 000 DH</button>
                <button type="button" class="sheet-budget-chip sidebar-budget-chip" data-min="1000" data-max="5000">1 000 – 5 000 DH</button>
                <button type="button" class="sheet-budget-chip sidebar-budget-chip" data-min="5000" data-max="15000">5 000 – 15 000 DH</button>
                <button type="button" class="sheet-budget-chip sidebar-budget-chip" data-min="15000" data-max="">+ 15 000 DH</button>
            </div>
        </div>

        {{-- Section 3: Rayons & Catégories --}}
        <div class="filter-section">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="filter-section-title mb-0">
                    <span class="filter-title-icon"><i class="fas fa-layer-group"></i></span>
                    <span>{{ __('Rayons Pro') }}</span>
                </label>
                <span class="sheet-section-badge">{{ $categories->count() }} {{ __('rayons') }}</span>
            </div>

            {{-- Live Search for Rayons --}}
            <div class="sheet-cat-search-box mb-2">
                <i class="fas fa-search sheet-cat-search-icon"></i>
                <input type="text" class="sheet-cat-search-input" id="sheetCatSearchInput" placeholder="{{ __('Filtrer les rayons (ex: Caméra, Son...)') }}" autocomplete="off">
            </div>

            {{-- Structured 2-column grid --}}
            <div class="sheet-cat-scroll-box" id="sheetCatScrollBox">
                <div class="sheet-cat-grid" id="sheetCategoryChips">
                    <button type="button" class="sheet-cat-card sheet-cat-chip {{ !request('category') ? 'active' : '' }}" data-slug="" data-name="{{ __('Tout le catalogue') }}">
                        <span class="sheet-cat-card-icon">
                            <i class="fas fa-th-large"></i>
                        </span>
                        <span class="sheet-cat-card-name">{{ __('Tout le catalogue') }}</span>
                        <span class="sheet-chip-count sheet-cat-card-count">{{ \App\Models\Product::where('status','active')->count() }}</span>
                    </button>
                    @foreach($categories as $cat)
                    @php
                        $cIcon = !empty($cat->icon) ? (str_starts_with($cat->icon, 'fa') ? $cat->icon : 'fas fa-' . $cat->icon) : ($catIcons[$cat->slug] ?? 'fas fa-camera');
                        $cImg = $cat->image_url;
                    @endphp
                    <button type="button" class="sheet-cat-card sheet-cat-chip {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}" data-name="{{ $cat->name }}">
                        <span class="sheet-cat-card-icon">
                            @if($cImg)
                                <img src="{{ $cImg }}" alt="{{ $cat->name }}" width="26" height="26" class="sheet-cat-card-img" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                <i class="{{ $cIcon }}" style="display: none;"></i>
                            @else
                                <i class="{{ $cIcon }}"></i>
                            @endif
                        </span>
                        <span class="sheet-cat-card-name">{{ $cat->name }}</span>
                        <span class="sheet-chip-count sheet-cat-card-count">{{ $cat->products_count }}</span>
                    </button>
                    @endforeach
                </div>
                <div class="sheet-cat-no-results text-center py-3 text-muted small d-none" id="sheetCatNoResults">
                    <i class="fas fa-info-circle me-1"></i>{{ __('Aucun rayon correspondant') }}
                </div>
            </div>
        </div>

        {{-- Section 4: Marques (Smart Brand Filter) --}}
        @php
            $sheetBrands = $smartBrands ?? collect();
            $activeBrandSlug = request('brand') ?? ($brandSlug ?? '');
        @endphp
        <div class="filter-section mt-3" id="sheetBrandFilterSection" style="{{ $sheetBrands->isEmpty() ? 'display: none;' : '' }}">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="filter-section-title mb-0">
                    <span class="filter-title-icon"><i class="fas fa-tags"></i></span>
                    <span>{{ __('Marques') }}</span>
                </label>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="sheet-clear-text-btn {{ $activeBrandSlug ? '' : 'd-none' }}" id="sheetBrandResetBtn">
                        <i class="fas fa-undo-alt me-1"></i>{{ __('Effacer') }}
                    </button>
                    <span class="sheet-section-badge" id="sheetBrandCountBadge">{{ $sheetBrands->count() }} {{ __('marques') }}</span>
                </div>
            </div>

            {{-- Live Search for Brands in Sheet --}}
            <div class="sheet-cat-search-box mb-2" id="sheetBrandSearchBox" style="{{ $sheetBrands->count() <= 6 ? 'display: none;' : '' }}">
                <i class="fas fa-search sheet-cat-search-icon"></i>
                <input type="text" class="sheet-cat-search-input" id="sheetBrandSearchInput" placeholder="{{ __('Filtrer les marques...') }}" autocomplete="off">
            </div>

            <div class="sheet-cat-scroll-box" id="sheetBrandScrollBox">
                <div class="sheet-cat-grid" id="sheetBrandChips">
                    @foreach($sheetBrands as $b)
                    <button type="button" class="sheet-cat-card sheet-brand-chip {{ $activeBrandSlug == $b->slug ? 'active' : '' }}" data-slug="{{ $b->slug }}" data-name="{{ $b->name }}">
                        <span class="sheet-cat-card-icon" style="width: 28px; height: 28px;">
                            @if($b->logo)
                                <img src="{{ $b->logo_url }}" alt="{{ $b->name }}" width="22" height="22" class="sheet-cat-card-img" style="object-fit: contain;" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                <i class="fas fa-tag" style="display: none; font-size: 0.85rem;"></i>
                            @else
                                <i class="fas fa-tag" style="font-size: 0.85rem;"></i>
                            @endif
                        </span>
                        <span class="sheet-cat-card-name">{{ $b->name }}</span>
                        <span class="sheet-chip-count sheet-cat-card-count">{{ $b->products_count }}</span>
                    </button>
                    @endforeach
                </div>
                <div class="sheet-cat-no-results text-center py-2 text-muted small d-none" id="sheetBrandNoResults">
                    <i class="fas fa-info-circle me-1"></i>{{ __('Aucune marque correspondante') }}
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer p-2.5 px-sm-4 bg-white border-top">
        <button class="btn btn-danger w-100 py-2.5 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-between px-3.5 sheet-apply-btn" id="sheetApplyBtn">
            <span class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-6"></i>
                <span>{{ __('Appliquer les filtres') }}</span>
            </span>
            <span class="badge rounded-pill bg-white text-danger font-monospace px-2.5 py-1" id="sheetApplyCountBadge">
                {{ $products->total() }} {{ __('produits') }}
            </span>
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Shop State Manager
const shopState = {
    category: "{{ request('category', '') }}",
    brand: "{{ request('brand', '') }}",
    sort: "{{ request('sort', 'newest') }}",
    q: "{{ request('q', '') }}",
    min_price: "{{ request('min_price', '') }}",
    max_price: "{{ request('max_price', '') }}",
    viewMode: localStorage.getItem('camera_shop_view') || (window.innerWidth < 768 ? 'grid-2' : 'grid-2')
};

// Brand Names Map
let brandNamesMap = {
    @if(isset($smartBrands))
        @foreach($smartBrands as $b)
            '{{ $b->slug }}': '{{ addslashes($b->name) }}',
        @endforeach
    @endif
    @if(isset($activeBrand) && $activeBrand)
        '{{ $activeBrand->slug }}': '{{ addslashes($activeBrand->name) }}',
    @endif
};

// Category Titles Map
const categoryNamesMap = {
    '': '{{ addslashes(__('Tout le catalogue')) }}',
    @foreach($categories as $cat)
        '{{ $cat->slug }}': '{{ addslashes($cat->name) }}',
    @endforeach
};

// Category Counts Map
const categoryCountsMap = {
    '': '{{ \App\Models\Product::where('status','active')->count() }}',
    @foreach($categories as $cat)
        '{{ $cat->slug }}': '{{ $cat->products_count }}',
    @endforeach
};

// Category Images Map
const categoryImagesMap = {
    '': '',
    @foreach($categories as $cat)
        @php
            $cImg = !empty($cat->image) && file_exists(public_path($cat->image)) ? asset($cat->image) : '';
        @endphp
        '{{ $cat->slug }}': '{{ $cImg }}',
    @endforeach
};

// Category Icons Map
const categoryIconsMap = {
    '': 'fa-layer-group',
    @foreach($categories as $cat)
        '{{ $cat->slug }}': '{{ $catIcons[$cat->slug] ?? 'fa-tag' }}',
    @endforeach
};

// Sort Labels Map
const sortLabelsMap = {
    'newest': '{{ __('Nouveautés') }}',
    'price_asc': '{{ __('Prix : Croissant') }}',
    'price_desc': '{{ __('Prix : Décroissant') }}'
};

function getParams() {
    const p = new URLSearchParams();
    if (shopState.category) p.append('category', shopState.category);
    if (shopState.brand) p.append('brand', shopState.brand);
    if (shopState.sort && shopState.sort !== 'newest') p.append('sort', shopState.sort);
    if (shopState.q) p.append('q', shopState.q);
    if (shopState.min_price) p.append('min_price', shopState.min_price);
    if (shopState.max_price) p.append('max_price', shopState.max_price);
    return p;
}

function parseUrlIntoState() {
    const p = new URLSearchParams(window.location.search);
    shopState.category = p.get('category') || '';
    shopState.brand = p.get('brand') || '';
    shopState.sort = p.get('sort') || 'newest';
    shopState.q = p.get('q') || '';
    shopState.min_price = p.get('min_price') || '';
    shopState.max_price = p.get('max_price') || '';
}

function updateUIState() {
    // 1. Category scroller pills & sheet chips
    document.querySelectorAll('.mobile-cat-pill, .sheet-cat-chip, .shop-cat-link').forEach(el => {
        const slug = el.getAttribute('data-slug');
        if (slug === shopState.category) {
            el.classList.add('active');
        } else {
            el.classList.remove('active');
        }
    });

    // 1b. Update active category labels & avatars across all dropdowns
    const activeName = categoryNamesMap[shopState.category] || '{{ __('Tout le catalogue') }}';
    const activeCount = categoryCountsMap[shopState.category] || categoryCountsMap[''];
    const imgUrl = categoryImagesMap[shopState.category] || '';
    const iconClass = categoryIconsMap[shopState.category] || 'fa-layer-group';

    function setSlotMedia(el, img, icon, extraClass = '') {
        if (!el) return;
        if (img) {
            el.innerHTML = `<img src="${img}" alt="" width="30" height="30" class="cat-slot-media" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';"><i class="fas ${icon} cat-slot-icon ${extraClass}" style="display: none;"></i>`;
        } else {
            el.innerHTML = `<i class="fas ${icon} cat-slot-icon ${extraClass}"></i>`;
        }
    }

    // Sidebar
    const sidebarLabel = document.getElementById('sidebarCatDropdownLabel');
    if (sidebarLabel) sidebarLabel.textContent = activeName;
    const sidebarCountBadge = document.getElementById('sidebarCatCountBadge');
    if (sidebarCountBadge) sidebarCountBadge.textContent = activeCount;
    setSlotMedia(document.getElementById('sidebarCatAvatar'), imgUrl, iconClass);
    const sidebarCatReset = document.getElementById('sidebarCatResetBtn');
    if (sidebarCatReset) sidebarCatReset.style.display = shopState.category ? 'inline-flex' : 'none';
    const sidebarPriceReset = document.getElementById('sidebarPriceResetBtn');
    if (sidebarPriceReset) sidebarPriceReset.style.display = (shopState.min_price || shopState.max_price) ? 'inline-flex' : 'none';

    // Mobile Card
    const mobileLabel = document.getElementById('mobileCatLabel');
    if (mobileLabel) mobileLabel.textContent = activeName;
    const mobileCountBadge = document.getElementById('mobileCatCountBadge');
    if (mobileCountBadge) mobileCountBadge.textContent = activeCount;
    setSlotMedia(document.getElementById('mobileCatAvatar'), imgUrl, iconClass);

    // Toolbar title update
    const catTitleEl = document.getElementById('categoryTitle');
    if (catTitleEl) {
        catTitleEl.textContent = activeName;
    }

    // 2. Sort Selects and Mobile Label
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) sortSelect.value = shopState.sort;

    const mobileSortLabel = document.getElementById('mobileSortLabel');
    if (mobileSortLabel) mobileSortLabel.textContent = sortLabelsMap[shopState.sort] || '{{ __('Nouveautés') }}';

    document.querySelectorAll('.mobile-sort-item').forEach(el => {
        if (el.getAttribute('data-sort') === shopState.sort) {
            el.classList.add('active');
        } else {
            el.classList.remove('active');
        }
    });

    // 3. Search and Price Inputs Sync
    const desktopSearch = document.querySelector('input[name="q"].shop-search-input');
    if (desktopSearch) desktopSearch.value = shopState.q;
    const sheetSearch = document.getElementById('sheetSearchInput');
    if (sheetSearch) sheetSearch.value = shopState.q;

    const desktopMin = document.querySelector('input[name="min_price"].shop-price-input');
    if (desktopMin) desktopMin.value = shopState.min_price;
    const sheetMin = document.getElementById('sheetMinPrice');
    if (sheetMin) sheetMin.value = shopState.min_price;

    const desktopMax = document.querySelector('input[name="max_price"].shop-price-input');
    if (desktopMax) desktopMax.value = shopState.max_price;
    const sheetMax = document.getElementById('sheetMaxPrice');
    if (sheetMax) sheetMax.value = shopState.max_price;

    // Synchronize price slider UI
    if (typeof syncPriceSliderUI === 'function') {
        syncPriceSliderUI();
    }

    // Synchronize Budget Chips active highlights
    const hasBudget = (shopState.min_price !== '' || shopState.max_price !== '');
    document.querySelectorAll('.sheet-budget-chip, .sidebar-budget-chip').forEach(chip => {
        const cMin = chip.getAttribute('data-min') || '';
        const cMax = chip.getAttribute('data-max') || '';
        const curMin = shopState.min_price || '';
        const curMax = shopState.max_price || '';
        const matchMin = (curMin == cMin || (curMin === '' && (cMin === '' || cMin === '0')));
        const matchMax = (curMax == cMax);
        if (hasBudget && matchMin && matchMax) {
            chip.classList.add('active');
        } else {
            chip.classList.remove('active');
        }
    });

    // 3b. Brand active highlights
    document.querySelectorAll('.shop-brand-link, .sheet-brand-chip').forEach(el => {
        const slug = el.getAttribute('data-slug');
        if (slug === shopState.brand) {
            el.classList.add('active');
        } else {
            el.classList.remove('active');
        }
    });

    const toolbarBrandBtn = document.getElementById('toolbarBrandDropdownBtn');
    const toolbarBrandLabel = document.getElementById('toolbarBrandLabel');
    const toolbarBrandResetLink = document.getElementById('toolbarBrandResetLink');

    const mobileBrandBtn = document.getElementById('mobileBrandDropdownBtn');
    const mobileBrandLabel = document.getElementById('mobileBrandLabel');
    const mobileBrandResetLink = document.getElementById('mobileBrandResetLink');

    const activeBrandDisplay = shopState.brand ? (brandNamesMap[shopState.brand] || shopState.brand) : '';

    if (toolbarBrandLabel) {
        toolbarBrandLabel.textContent = activeBrandDisplay || "{{ __('Marque') }}";
    }
    if (toolbarBrandBtn) {
        toolbarBrandBtn.classList.toggle('has-brand', Boolean(shopState.brand));
    }
    if (toolbarBrandResetLink) {
        toolbarBrandResetLink.style.display = shopState.brand ? 'inline-flex' : 'none';
    }

    if (mobileBrandLabel) {
        mobileBrandLabel.textContent = activeBrandDisplay || "{{ __('Marque') }}";
    }
    if (mobileBrandBtn) {
        mobileBrandBtn.classList.toggle('has-brand', Boolean(shopState.brand));
    }
    if (mobileBrandResetLink) {
        mobileBrandResetLink.style.display = shopState.brand ? 'inline-flex' : 'none';
    }

    const sheetBrandReset = document.getElementById('sheetBrandResetBtn');
    if (sheetBrandReset) sheetBrandReset.classList.toggle('d-none', !shopState.brand);

    // 4. Active filters count badge on mobile & drawer subtitle
    let activeFilterCount = 0;
    if (shopState.category) activeFilterCount++;
    if (shopState.brand) activeFilterCount++;
    if (shopState.q) activeFilterCount++;
    if (shopState.min_price || shopState.max_price) activeFilterCount++;

    const filterBadge = document.getElementById('mobileFilterBadge');
    if (filterBadge) {
        if (activeFilterCount > 0) {
            filterBadge.textContent = activeFilterCount;
            filterBadge.classList.remove('d-none');
        } else {
            filterBadge.classList.add('d-none');
        }
    }

    const sheetSub = document.getElementById('sheetActiveFiltersSub');
    if (sheetSub) {
        if (activeFilterCount > 0) {
            sheetSub.textContent = activeFilterCount === 1 
                ? '1 {{ __('filtre actif') }}' 
                : `${activeFilterCount} {{ __('filtres actifs') }}`;
            sheetSub.classList.add('text-danger', 'fw-bold');
            sheetSub.classList.remove('text-muted');
        } else {
            sheetSub.textContent = "{{ __('Affinez selon votre équipement et budget') }}";
            sheetSub.classList.remove('text-danger', 'fw-bold');
            sheetSub.classList.add('text-muted');
        }
    }

    const sheetSearchClear = document.getElementById('sheetSearchClearBtn');
    if (sheetSearchClear) {
        sheetSearchClear.classList.toggle('d-none', !shopState.q);
    }

    const sheetPriceResetBtn = document.getElementById('sheetPriceResetBtn');
    if (sheetPriceResetBtn) {
        sheetPriceResetBtn.classList.toggle('d-none', !(shopState.min_price || shopState.max_price));
    }

    // 5. Render active filter chips on mobile and desktop
    renderActiveFilterChips();

    // 6. View Mode
    applyViewMode(shopState.viewMode);
}

function renderActiveFilterChips() {
    const mobileContainer = document.getElementById('mobileActiveChipsContainer');
    const desktopContainer = document.getElementById('desktopActiveFilterChips');
    const desktopWrap = document.getElementById('desktopActiveFilters');

    let chipsHtml = '';
    let desktopHtml = '';

    if (shopState.category) {
        const catName = categoryNamesMap[shopState.category] || shopState.category;
        chipsHtml += `<span class="mobile-chip" onclick="removeFilter('category')">${catName} <i class="fas fa-times ms-1"></i></span>`;
        desktopHtml += `<span class="shop-filter-tag cursor-pointer" onclick="removeFilter('category')">{{ __('Rayon :') }} ${catName} <i class="fas fa-times ms-1 small"></i></span>`;
    }
    if (shopState.brand) {
        const brandName = brandNamesMap[shopState.brand] || shopState.brand;
        chipsHtml += `<span class="mobile-chip" onclick="removeFilter('brand')">{{ __('Marque :') }} ${brandName} <i class="fas fa-times ms-1"></i></span>`;
        desktopHtml += `<span class="shop-filter-tag cursor-pointer" onclick="removeFilter('brand')">{{ __('Marque :') }} ${brandName} <i class="fas fa-times ms-1 small"></i></span>`;
    }
    if (shopState.q) {
        chipsHtml += `<span class="mobile-chip" onclick="removeFilter('q')">« ${shopState.q} » <i class="fas fa-times ms-1"></i></span>`;
        desktopHtml += `<span class="shop-filter-tag cursor-pointer" onclick="removeFilter('q')">{{ __('Recherche :') }} « ${shopState.q} » <i class="fas fa-times ms-1 small"></i></span>`;
    }
    if (shopState.min_price || shopState.max_price) {
        const min = shopState.min_price || '0';
        const max = shopState.max_price || '∞';
        chipsHtml += `<span class="mobile-chip" onclick="removeFilter('price')">${min}–${max} DH <i class="fas fa-times ms-1"></i></span>`;
        desktopHtml += `<span class="shop-filter-tag cursor-pointer" onclick="removeFilter('price')">{{ __('Budget :') }} ${min} — ${max} DH <i class="fas fa-times ms-1 small"></i></span>`;
    }

    if (chipsHtml) {
        chipsHtml += `<span class="mobile-chip-clear" onclick="resetAllFilters()">{{ __('Effacer tout') }}</span>`;
    }

    if (mobileContainer) mobileContainer.innerHTML = chipsHtml;
    if (desktopContainer) desktopContainer.innerHTML = desktopHtml;
    if (desktopWrap) {
        if (desktopHtml) {
            desktopWrap.classList.remove('d-none');
        } else {
            desktopWrap.classList.add('d-none');
        }
    }
}

window.removeFilter = function(type) {
    if (type === 'category') shopState.category = '';
    if (type === 'brand') shopState.brand = '';
    if (type === 'q') shopState.q = '';
    if (type === 'price') { 
        shopState.min_price = ''; 
        shopState.max_price = ''; 
        const dMin = document.querySelector('input[name="min_price"].shop-price-input');
        const dMax = document.querySelector('input[name="max_price"].shop-price-input');
        if (dMin) dMin.value = '';
        if (dMax) dMax.value = '';
        const sMin = document.getElementById('sheetMinPrice');
        const sMax = document.getElementById('sheetMaxPrice');
        if (sMin) sMin.value = '';
        if (sMax) sMax.value = '';
        if (typeof syncPriceSliderUI === 'function') syncPriceSliderUI();
    }
    fetchProducts();
};

window.resetAllFilters = function() {
    shopState.category = '';
    shopState.brand = '';
    shopState.q = '';
    shopState.min_price = '';
    shopState.max_price = '';
    shopState.sort = 'newest';
    fetchProducts();
};

window.applyCategoryFilter = function(slug) {
    shopState.category = slug || '';
    fetchProducts();
};

window.applyBrandFilter = function(slug) {
    if (shopState.brand === slug) {
        shopState.brand = '';
    } else {
        shopState.brand = slug || '';
    }
    fetchProducts();
};

function applyViewMode(mode) {
    const gridContainer = document.getElementById('productGridContainer');
    const skeleton = document.getElementById('shopSkeletonGrid');
    const toggleIcon = document.getElementById('viewToggleIcon');
    const toggleBtn = document.getElementById('mobileViewToggle');
    if (!gridContainer) return;

    if (mode === 'grid-1') {
        gridContainer.classList.add('shop-grid-1col');
        if (skeleton) skeleton.classList.add('shop-grid-1col');
        if (toggleIcon) toggleIcon.className = 'fas fa-th-large';
        if (toggleBtn) {
            toggleBtn.setAttribute('title', 'Passer en grille (2 colonnes)');
            toggleBtn.setAttribute('aria-label', 'Passer en grille (2 colonnes)');
        }
    } else {
        gridContainer.classList.remove('shop-grid-1col');
        if (skeleton) skeleton.classList.remove('shop-grid-1col');
        if (toggleIcon) toggleIcon.className = 'fas fa-square';
        if (toggleBtn) {
            toggleBtn.setAttribute('title', 'Passer en grand format (1 colonne)');
            toggleBtn.setAttribute('aria-label', 'Passer en grand format (1 colonne)');
        }
    }
}

function fetchProducts(url = "{{ route('shop.index') }}", pushHistory = true) {
    const grid = document.getElementById('productGridContainer');
    const skeleton = document.getElementById('shopSkeletonGrid');
    const loader = document.getElementById('loader');
    if (!grid) return;

    // Show skeleton and fade grid
    if (skeleton) skeleton.classList.remove('d-none');
    grid.style.opacity = '0.25';

    const params = getParams();
    let fetchUrl = url;
    if (!url.includes('?')) {
        const queryStr = params.toString();
        fetchUrl = queryStr ? `${url}?${queryStr}` : url;
    }

    if (pushHistory) {
        window.history.pushState(null, '', fetchUrl);
    }

    fetch(fetchUrl, { 
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html, application/json'
        } 
    })
    .then(r => r.text())
    .then(html => {
        grid.innerHTML = html;
        grid.style.opacity = '1';
        if (skeleton) skeleton.classList.add('d-none');
        if (loader) loader.classList.add('d-none');

        // Extract total & title from Meta Bridge
        const metaBridge = grid.querySelector('#shopMetaBridge');
        if (metaBridge) {
            const total = metaBridge.getAttribute('data-total') || '0';
            const catTitle = metaBridge.getAttribute('data-category-title') || '';

            const desktopTotalBadge = document.getElementById('desktopProductTotalBadge');
            if (desktopTotalBadge) desktopTotalBadge.textContent = `${total} {{ __('produits') }}`;

            const mobileTotal = document.getElementById('mobileProductTotal');
            if (mobileTotal) mobileTotal.textContent = total;

            const sheetApplyBadge = document.getElementById('sheetApplyCountBadge');
            if (sheetApplyBadge) sheetApplyBadge.textContent = `${total} {{ __('produits') }}`;

            const categoryTitleEl = document.getElementById('categoryTitle');
            if (categoryTitleEl && catTitle) categoryTitleEl.textContent = catTitle;

            // Extract smart brands payload
            const brandsPayloadEl = grid.querySelector('#shopMetaBrandsPayload');
            if (brandsPayloadEl) {
                try {
                    const brandsData = JSON.parse(brandsPayloadEl.textContent);
                    updateSmartBrandsUI(brandsData);
                } catch(e) {
                    console.error('Brands parse error:', e);
                }
            }
        }

        // Re-apply view mode on new content
        applyViewMode(shopState.viewMode);
        updateUIState();

        // Smooth scroll on mobile if triggered from sheet or filters
        if (window.innerWidth < 992) {
            const mobileToolbar = document.querySelector('.shop-mobile-toolbar');
            if (mobileToolbar) {
                mobileToolbar.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    })
    .catch(err => {
        console.error('Fetch products error:', err);
        grid.style.opacity = '1';
        if (skeleton) skeleton.classList.add('d-none');
        if (loader) loader.classList.add('d-none');
    });
}

function updateSmartBrandsUI(brandsData) {
    if (!Array.isArray(brandsData)) return;

    brandsData.forEach(b => {
        brandNamesMap[b.slug] = b.name;
    });

    const toolbarContainer = document.getElementById('toolbarBrandDropdownContainer');
    const toolbarItemsContainer = document.getElementById('toolbarBrandItemsContainer');
    const toolbarBadge = document.getElementById('toolbarBrandCountBadge');
    const toolbarSearchBox = document.getElementById('toolbarBrandSearchBox');

    const mobileContainer = document.getElementById('mobileBrandDropdownContainer');
    const mobileItemsContainer = document.getElementById('mobileBrandItemsContainer');

    const sheetSection = document.getElementById('sheetBrandFilterSection');
    const sheetContainer = document.getElementById('sheetBrandChips');
    const sheetBadge = document.getElementById('sheetBrandCountBadge');
    const sheetSearchBox = document.getElementById('sheetBrandSearchBox');

    if (brandsData.length === 0) {
        if (toolbarContainer) toolbarContainer.style.display = 'none';
        if (mobileContainer) mobileContainer.style.display = 'none';
        if (sheetSection) sheetSection.style.display = 'none';
        return;
    }

    if (toolbarContainer) toolbarContainer.style.display = '';
    if (mobileContainer) mobileContainer.style.display = '';
    if (sheetSection) sheetSection.style.display = '';

    if (toolbarBadge) toolbarBadge.textContent = brandsData.length;
    if (sheetBadge) sheetBadge.textContent = `${brandsData.length} {{ __('marques') }}`;

    if (toolbarSearchBox) toolbarSearchBox.style.display = brandsData.length <= 5 ? 'none' : '';
    if (sheetSearchBox) sheetSearchBox.style.display = brandsData.length <= 6 ? 'none' : '';

    if (toolbarItemsContainer) {
        let toolbarHtml = '';
        brandsData.forEach(b => {
            const isActive = (shopState.brand === b.slug);
            const mediaHtml = b.logo_url
                ? `<img src="${b.logo_url}" alt="${b.name}" width="18" height="18" class="cat-slot-media" style="object-fit: contain;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';"><i class="fas fa-tag text-muted" style="display: none; font-size: 0.75rem;"></i>`
                : `<i class="fas fa-tag text-muted" style="font-size: 0.75rem;"></i>`;

            toolbarHtml += `
            <button type="button" class="dropdown-item rounded-3 py-1.5 px-2.5 d-flex align-items-center justify-content-between shop-brand-link mb-0.5 ${isActive ? 'active' : ''}" 
                    data-slug="${b.slug}" 
                    data-name="${b.name}">
                <span class="d-flex align-items-center gap-2 text-truncate me-2">
                    <span class="cat-slot-box" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; flex-shrink: 0;">
                        ${mediaHtml}
                    </span>
                    <span class="fw-medium text-truncate" style="font-size: 0.84rem;">${b.name}</span>
                </span>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <span class="badge cat-mega-count rounded-pill" style="font-size: 0.7rem;">${b.count}</span>
                    <i class="fas fa-check cat-item-check" style="font-size: 0.75rem;"></i>
                </div>
            </button>`;
        });
        toolbarItemsContainer.innerHTML = toolbarHtml;
    }

    if (mobileItemsContainer) {
        let mobileHtml = '';
        brandsData.forEach(b => {
            const isActive = (shopState.brand === b.slug);
            const mediaHtml = b.logo_url
                ? `<img src="${b.logo_url}" alt="${b.name}" width="16" height="16" class="cat-slot-media" style="object-fit: contain;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';"><i class="fas fa-tag text-muted" style="display: none; font-size: 0.7rem;"></i>`
                : `<i class="fas fa-tag text-muted" style="font-size: 0.7rem;"></i>`;

            mobileHtml += `
            <button type="button" class="dropdown-item rounded-3 py-1.5 px-2 d-flex align-items-center justify-content-between shop-brand-link mb-0.5 ${isActive ? 'active' : ''}" 
                    data-slug="${b.slug}" 
                    data-name="${b.name}">
                <span class="d-flex align-items-center gap-2 text-truncate me-2">
                    <span class="cat-slot-box" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; flex-shrink: 0;">
                        ${mediaHtml}
                    </span>
                    <span class="fw-medium text-truncate" style="font-size: 0.82rem;">${b.name}</span>
                </span>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <span class="badge cat-mega-count rounded-pill" style="font-size: 0.68rem;">${b.count}</span>
                </div>
            </button>`;
        });
        mobileItemsContainer.innerHTML = mobileHtml;
    }

    if (sheetContainer) {
        let sheetHtml = '';
        brandsData.forEach(b => {
            const isActive = (shopState.brand === b.slug);
            const mediaHtml = b.logo_url
                ? `<img src="${b.logo_url}" alt="${b.name}" width="22" height="22" class="sheet-cat-card-img" style="object-fit: contain;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';"><i class="fas fa-tag" style="display: none; font-size: 0.85rem;"></i>`
                : `<i class="fas fa-tag" style="font-size: 0.85rem;"></i>`;

            sheetHtml += `
            <button type="button" class="sheet-cat-card sheet-brand-chip ${isActive ? 'active' : ''}" data-slug="${b.slug}" data-name="${b.name}">
                <span class="sheet-cat-card-icon" style="width: 28px; height: 28px;">
                    ${mediaHtml}
                </span>
                <span class="sheet-cat-card-name">${b.name}</span>
                <span class="sheet-chip-count sheet-cat-card-count">${b.count}</span>
            </button>`;
        });
        sheetContainer.innerHTML = sheetHtml;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateUIState();

    // Browser Popstate (Back & Forward buttons support)
    window.addEventListener('popstate', function() {
        parseUrlIntoState();
        fetchProducts(window.location.href, false);
    });

    // Delegated handler on product grid for pagination and card category links
    const gridContainer = document.getElementById('productGridContainer');
    if (gridContainer) {
        gridContainer.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.shop-pagination a');
            if (pageLink) {
                e.preventDefault();
                fetchProducts(pageLink.href);
                window.scrollTo({ top: 150, behavior: 'smooth' });
                return;
            }
            const cardCat = e.target.closest('.shop-filter-cat-link');
            if (cardCat) {
                e.preventDefault();
                const slug = cardCat.getAttribute('data-slug') || '';
                window.applyCategoryFilter(slug);
                return;
            }
        });
    }

    // Consolidated Category Clicks (Mobile Scroller, Sidebar, Dropdown)
    document.addEventListener('click', function(e) {
        const catBtn = e.target.closest('.mobile-cat-pill, .shop-cat-link:not(.mobile-category-item)');
        if (catBtn) {
            e.preventDefault();
            const slug = catBtn.getAttribute('data-slug') || '';
            window.applyCategoryFilter(slug);
            if (catBtn.classList.contains('mobile-cat-pill')) {
                catBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }
    });

    // Consolidated Brand Clicks (Toolbar, Sidebar & Sheet)
    document.addEventListener('click', function(e) {
        const brandBtn = e.target.closest('.shop-brand-link');
        if (brandBtn) {
            e.preventDefault();
            const slug = brandBtn.getAttribute('data-slug') || '';
            window.applyBrandFilter(slug);

            // Close toolbar & mobile dropdowns if open
            ['toolbarBrandDropdownBtn', 'mobileBrandDropdownBtn'].forEach(btnId => {
                const btn = document.getElementById(btnId);
                if (btn) {
                    const bsDropdown = bootstrap.Dropdown.getInstance(btn);
                    if (bsDropdown) bsDropdown.hide();
                }
            });
        }

        const sheetBrandChip = e.target.closest('.sheet-brand-chip');
        if (sheetBrandChip) {
            e.preventDefault();
            const slug = sheetBrandChip.getAttribute('data-slug') || '';
            window.applyBrandFilter(slug);
        }
    });

    const sheetBrandReset = document.getElementById('sheetBrandResetBtn');
    if (sheetBrandReset) {
        sheetBrandReset.addEventListener('click', function(e) {
            e.preventDefault();
            window.removeFilter('brand');
        });
    }

    // Live brand search filters (Toolbar & Sheet)
    ['toolbarBrandSearchInput', 'sheetBrandSearchInput'].forEach(inputId => {
        const inputEl = document.getElementById(inputId);
        if (inputEl) {
            inputEl.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const targetBox = (inputId === 'toolbarBrandSearchInput')
                    ? document.getElementById('toolbarBrandItemsContainer')
                    : document.getElementById('sheetBrandChips');
                const noRes = (inputId === 'toolbarBrandSearchInput')
                    ? document.getElementById('toolbarBrandNoResults')
                    : document.getElementById('sheetBrandNoResults');

                if (targetBox) {
                    let matchCount = 0;
                    targetBox.querySelectorAll('.shop-brand-link, .sheet-brand-chip').forEach(item => {
                        const name = (item.getAttribute('data-name') || item.textContent).toLowerCase();
                        const match = name.includes(query);
                        item.style.display = match ? '' : 'none';
                        if (match) matchCount++;
                    });
                    if (noRes) noRes.style.display = matchCount === 0 ? 'block' : 'none';
                }
            });
        }
    });

    // Live category search filters for dropdown panels & sidebar cards
    ['mobileCatSearchInput', 'sidebarCatSearchInput'].forEach(inputId => {
        const inputEl = document.getElementById(inputId);
        if (inputEl) {
            inputEl.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const container = this.closest('.shop-cat-pro-menu') || this.closest('.shop-filter-card');
                if (container) {
                    let matchCount = 0;
                    container.querySelectorAll('.cat-mega-item, .cat-pro-item, .shop-cat-link').forEach(item => {
                        const name = (item.getAttribute('data-name') || item.textContent).toLowerCase();
                        const match = name.includes(query);
                        item.style.display = match ? '' : 'none';
                        if (match) matchCount++;
                    });
                    const noRes = container.querySelector('.sidebar-cat-no-results');
                    if (noRes) noRes.style.display = matchCount === 0 ? 'block' : 'none';
                }
            });
        }
    });

    // 3. Desktop Sort Select
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            shopState.sort = this.value;
            fetchProducts();
        });
    }

    // 4. Mobile Sort Dropdown Items & Sidebar Quick Sort Links
    document.querySelectorAll('.mobile-sort-item, .shop-quick-link[data-sort]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            shopState.sort = this.getAttribute('data-sort');
            fetchProducts();
        });
    });

    // 5. Mobile View Mode Toggle (2-cols vs 1-col)
    const viewToggleBtn = document.getElementById('mobileViewToggle');
    if (viewToggleBtn) {
        viewToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            shopState.viewMode = (shopState.viewMode === 'grid-2') ? 'grid-1' : 'grid-2';
            localStorage.setItem('camera_shop_view', shopState.viewMode);
            applyViewMode(shopState.viewMode);
        });
    }

    // 6. Mobile Sheet Category Chips
    document.querySelectorAll('.sheet-cat-chip').forEach(chip => {
        chip.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sheet-cat-chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            shopState.category = this.getAttribute('data-slug') || '';
        });
    });

    // 7. Interactive Budget Dual Slider & Inputs
    window.syncPriceSliderUI = function() {
        const rangeMin = document.getElementById('priceRangeMin');
        const rangeMax = document.getElementById('priceRangeMax');
        const sliderFill = document.getElementById('priceSliderFill');
        if (!rangeMin || !rangeMax || !sliderFill) return;

        const minVal = shopState.min_price ? parseInt(shopState.min_price) || 0 : 0;
        const maxVal = shopState.max_price ? parseInt(shopState.max_price) || 65000 : 65000;

        rangeMin.value = minVal;
        rangeMax.value = maxVal;

        const minPct = Math.min(100, Math.max(0, (minVal / 65000) * 100));
        const maxPct = Math.min(100, Math.max(0, (maxVal / 65000) * 100));

        sliderFill.style.left = minPct + '%';
        sliderFill.style.width = Math.max(0, maxPct - minPct) + '%';
    };

    const rangeMin = document.getElementById('priceRangeMin');
    const rangeMax = document.getElementById('priceRangeMax');
    const sliderFill = document.getElementById('priceSliderFill');
    const dMin = document.querySelector('input[name="min_price"].shop-price-input');
    const dMax = document.querySelector('input[name="max_price"].shop-price-input');

    function updateSliderFromInputs() {
        const minVal = dMin && dMin.value ? parseInt(dMin.value) || 0 : 0;
        const maxVal = dMax && dMax.value ? parseInt(dMax.value) || 65000 : 65000;
        if (rangeMin) rangeMin.value = minVal;
        if (rangeMax) rangeMax.value = maxVal;
        if (sliderFill) {
            const minPct = Math.min(100, Math.max(0, (minVal / 65000) * 100));
            const maxPct = Math.min(100, Math.max(0, (maxVal / 65000) * 100));
            sliderFill.style.left = minPct + '%';
            sliderFill.style.width = Math.max(0, maxPct - minPct) + '%';
        }
    }

    let priceDebounceTimer = null;
    function triggerPriceFilterDebounced() {
        clearTimeout(priceDebounceTimer);
        priceDebounceTimer = setTimeout(() => {
            shopState.min_price = dMin ? dMin.value.trim() : '';
            shopState.max_price = dMax ? dMax.value.trim() : '';
            fetchProducts();
        }, 550);
    }

    if (dMin) {
        dMin.addEventListener('input', function() {
            updateSliderFromInputs();
            triggerPriceFilterDebounced();
        });
        dMin.addEventListener('change', function() {
            clearTimeout(priceDebounceTimer);
            shopState.min_price = dMin ? dMin.value.trim() : '';
            shopState.max_price = dMax ? dMax.value.trim() : '';
            fetchProducts();
        });
    }

    if (dMax) {
        dMax.addEventListener('input', function() {
            updateSliderFromInputs();
            triggerPriceFilterDebounced();
        });
        dMax.addEventListener('change', function() {
            clearTimeout(priceDebounceTimer);
            shopState.min_price = dMin ? dMin.value.trim() : '';
            shopState.max_price = dMax ? dMax.value.trim() : '';
            fetchProducts();
        });
    }

    if (rangeMin && rangeMax && sliderFill) {
        const minGap = 500;
        function onRangeSlide(e) {
            let val1 = parseInt(rangeMin.value) || 0;
            let val2 = parseInt(rangeMax.value) || 65000;
            if (val2 - val1 < minGap) {
                if (e.target === rangeMin) {
                    rangeMin.value = val2 - minGap;
                    val1 = val2 - minGap;
                } else {
                    rangeMax.value = val1 + minGap;
                    val2 = val1 + minGap;
                }
            }
            const minPct = (val1 / 65000) * 100;
            const maxPct = (val2 / 65000) * 100;
            sliderFill.style.left = minPct + '%';
            sliderFill.style.width = (maxPct - minPct) + '%';

            if (dMin) dMin.value = val1 > 0 ? val1 : '';
            if (dMax) dMax.value = val2 < 65000 ? val2 : '';
        }

        rangeMin.addEventListener('input', onRangeSlide);
        rangeMax.addEventListener('input', onRangeSlide);

        rangeMin.addEventListener('mousedown', () => { rangeMin.style.zIndex = '5'; rangeMax.style.zIndex = '4'; });
        rangeMin.addEventListener('touchstart', () => { rangeMin.style.zIndex = '5'; rangeMax.style.zIndex = '4'; });
        rangeMax.addEventListener('mousedown', () => { rangeMax.style.zIndex = '5'; rangeMin.style.zIndex = '4'; });
        rangeMax.addEventListener('touchstart', () => { rangeMax.style.zIndex = '5'; rangeMin.style.zIndex = '4'; });

        rangeMin.addEventListener('change', function() {
            shopState.min_price = dMin ? dMin.value.trim() : '';
            shopState.max_price = dMax ? dMax.value.trim() : '';
            fetchProducts();
        });
        rangeMax.addEventListener('change', function() {
            shopState.min_price = dMin ? dMin.value.trim() : '';
            shopState.max_price = dMax ? dMax.value.trim() : '';
            fetchProducts();
        });
    }

    updateSliderFromInputs();

    // 8. Mobile Sheet Budget Chips & Sidebar Budget Chips
    document.querySelectorAll('.sheet-budget-chip, .sidebar-budget-chip').forEach(chip => {
        chip.addEventListener('click', function(e) {
            e.preventDefault();
            const min = this.getAttribute('data-min') || '';
            const max = this.getAttribute('data-max') || '';
            shopState.min_price = min;
            shopState.max_price = max;

            if (this.classList.contains('sidebar-budget-chip')) {
                const dMin = document.querySelector('input[name="min_price"].shop-price-input');
                const dMax = document.querySelector('input[name="max_price"].shop-price-input');
                if (dMin) dMin.value = min;
                if (dMax) dMax.value = max;
                updateSliderFromInputs();
                fetchProducts();
            } else {
                const sheetMin = document.getElementById('sheetMinPrice');
                const sheetMax = document.getElementById('sheetMaxPrice');
                if (sheetMin) sheetMin.value = min;
                if (sheetMax) sheetMax.value = max;
                document.querySelectorAll('.sheet-budget-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // 10. Mobile Sheet Apply Button
    const sheetApplyBtn = document.getElementById('sheetApplyBtn');
    if (sheetApplyBtn) {
        sheetApplyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            shopState.q = document.getElementById('sheetSearchInput').value.trim();
            shopState.min_price = document.getElementById('sheetMinPrice').value.trim();
            shopState.max_price = document.getElementById('sheetMaxPrice').value.trim();

            // Close offcanvas
            const sheetEl = document.getElementById('shopFilterSheet');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(sheetEl);
            if (bsOffcanvas) bsOffcanvas.hide();

            fetchProducts();
        });
    }

    // 11. Mobile Sheet Reset Button
    const sheetResetBtn = document.getElementById('sheetResetBtn');
    if (sheetResetBtn) {
        sheetResetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sheetSearchInput').value = '';
            document.getElementById('sheetMinPrice').value = '';
            document.getElementById('sheetMaxPrice').value = '';
            shopState.category = '';
            shopState.brand = '';
            document.querySelectorAll('.sheet-cat-chip').forEach(c => {
                if (c.getAttribute('data-slug') === '') c.classList.add('active');
                else c.classList.remove('active');
            });
            document.querySelectorAll('.sheet-brand-chip').forEach(c => c.classList.remove('active'));
            resetAllFilters();
            const sheetEl = document.getElementById('shopFilterSheet');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(sheetEl);
            if (bsOffcanvas) bsOffcanvas.hide();
        });
    }

    // 11b. Mobile Sheet Live Category Search
    const sheetCatSearchInput = document.getElementById('sheetCatSearchInput');
    if (sheetCatSearchInput) {
        sheetCatSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const grid = document.getElementById('sheetCategoryChips');
            if (grid) {
                let matchCount = 0;
                grid.querySelectorAll('.sheet-cat-card').forEach(item => {
                    const name = (item.getAttribute('data-name') || item.textContent).toLowerCase();
                    const match = name.includes(query);
                    item.style.display = match ? '' : 'none';
                    if (match) matchCount++;
                });
                const noRes = document.getElementById('sheetCatNoResults');
                if (noRes) noRes.classList.toggle('d-none', matchCount > 0);
            }
        });
    }

    // 11c. Mobile Sheet Search Clear Button
    const sheetSearchInp = document.getElementById('sheetSearchInput');
    const sheetSearchClr = document.getElementById('sheetSearchClearBtn');
    if (sheetSearchInp && sheetSearchClr) {
        sheetSearchInp.addEventListener('input', function() {
            sheetSearchClr.classList.toggle('d-none', !this.value.trim());
        });
        sheetSearchClr.addEventListener('click', function() {
            sheetSearchInp.value = '';
            sheetSearchClr.classList.add('d-none');
            sheetSearchInp.focus();
        });
    }

    // 11d. Mobile Sheet Price Clear Button
    const sheetPriceClr = document.getElementById('sheetPriceResetBtn');
    if (sheetPriceClr) {
        sheetPriceClr.addEventListener('click', function(e) {
            e.preventDefault();
            const sMin = document.getElementById('sheetMinPrice');
            const sMax = document.getElementById('sheetMaxPrice');
            if (sMin) sMin.value = '';
            if (sMax) sMax.value = '';
            document.querySelectorAll('.sheet-budget-chip').forEach(c => c.classList.remove('active'));
            sheetPriceClr.classList.add('d-none');
        });
    }

    ['sheetMinPrice', 'sheetMaxPrice'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                const sMin = document.getElementById('sheetMinPrice');
                const sMax = document.getElementById('sheetMaxPrice');
                const hasVal = (sMin && sMin.value.trim()) || (sMax && sMax.value.trim());
                if (sheetPriceClr) sheetPriceClr.classList.toggle('d-none', !hasVal);
            });
        }
    });

    // 12. Desktop Reset Filters
    const desktopResetBtn = document.getElementById('desktopResetFiltersBtn');
    if (desktopResetBtn) {
        desktopResetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            resetAllFilters();
        });
    }

    // 13. Desktop Forms
    const desktopPriceForm = document.getElementById('priceFilterForm');
    if (desktopPriceForm) {
        desktopPriceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const minInp = this.querySelector('input[name="min_price"]');
            const maxInp = this.querySelector('input[name="max_price"]');
            shopState.min_price = minInp ? minInp.value.trim() : '';
            shopState.max_price = maxInp ? maxInp.value.trim() : '';
            fetchProducts();
        });
    }

    // Direct label/box click handler to guarantee focus
    document.querySelectorAll('.price-input-box').forEach(box => {
        box.addEventListener('click', function(e) {
            const inp = this.querySelector('input');
            if (inp && e.target !== inp && !e.target.closest('.price-input-clear-btn')) {
                inp.focus();
            }
        });
    });

    const desktopSearchForm = document.getElementById('searchForm');
    if (desktopSearchForm) {
        desktopSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            shopState.q = this.querySelector('input[name="q"]').value.trim();
            fetchProducts();
        });
    }
});
</script>
@endpush
