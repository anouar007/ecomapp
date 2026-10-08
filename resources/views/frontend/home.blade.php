@extends('layouts.frontend')

@section('meta_title', setting('app_name', 'WINA SHOP') . ' — ' . __('Matériel Photo, Vidéo & Caméras au Maroc'))
@section('meta_description', __('Votre référence au Maroc pour le matériel photo & vidéo professionnel — Caméras Sony, Canon, drones & stabilisateurs DJI, optiques et éclairage studio.'))

@section('content')

{{-- =========================================================================
     1. HERO SECTION (DJI Osmo 360 + Osmo Pocket 4 + Godox AD800Pro)
     ========================================================================= --}}
<section class="hero-winashop-section py-3">
    <div class="container">
        <div class="row g-3">
            {{-- Big Hero Banner Slider on Left --}}
            <div class="col-lg-8">
                <div class="hero-main-card position-relative overflow-hidden">
                    <div class="swiper hero-main-swiper">
                        <div class="swiper-wrapper">
                            @forelse($heroSlides as $slide)
                            <div class="swiper-slide hero-slide-item">
                                <div class="hero-main-bg" style="background-image: url('{{ $slide->image_url }}');"></div>
                                <div class="hero-main-overlay"></div>
                                <a href="{{ $slide->link ?: route('shop.index') }}" class="stretched-link" aria-label="{{ $slide->title }}"></a>
                                
                                @if($slide->badge)
                                <div class="hero-glass-badge">
                                    <span class="hero-live-dot"></span>
                                    <span>{{ $slide->badge }}</span>
                                </div>
                                @endif

                                <div class="hero-slide-content">
                                    <h2 class="hero-slide-title">{{ $slide->title }}</h2>
                                    @if($slide->description)
                                    <p class="hero-slide-desc d-none d-md-block">{{ $slide->description }}</p>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="swiper-slide hero-slide-item">
                                <div class="hero-main-bg" style="background-image: url('{{ asset('images/camera/hero_winashop.jpg') }}');"></div>
                                <div class="hero-main-overlay"></div>
                                <a href="{{ route('shop.index', ['q' => 'DJI Osmo 360']) }}" class="stretched-link" aria-label="DJI Osmo 360 All in One"></a>
                                
                                <div class="hero-glass-badge">
                                    <span class="hero-live-dot"></span>
                                    <span>NOUVEAUTÉ EXCLUSIVE • DJI OSMO 360</span>
                                </div>

                                <div class="hero-slide-content">
                                    <h2 class="hero-slide-title">DJI Osmo 360 All-In-One</h2>
                                    <p class="hero-slide-desc d-none d-md-block">Capteur 1 pouce, double optique 8K et stabilisation RockSteady 3.0.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>

                        {{-- Subtle Frosted Glass Navigation Arrows --}}
                        <button type="button" class="hero-swiper-nav hero-swiper-prev" aria-label="Diapositive précédente">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="hero-swiper-nav hero-swiper-next" aria-label="Diapositive suivante">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        {{-- Dash Pagination (Custom Swiper Bullets) --}}
                        <div class="hero-dash-pagination"></div>
                    </div>
                </div>
            </div>

            {{-- 2 Stacked Cards on Right (Side-by-side on tablet, stacked on desktop) --}}
            <div class="col-lg-4">
                <div class="d-flex flex-column flex-sm-row flex-lg-column gap-3 h-100">
                    {{-- Top Card: e.g. DJI Osmo Pocket 4 --}}
                    @php 
                        $topImg = $heroSideTop?->image_url ?? asset('images/camera/banner_osmo_pocket.jpg');
                        $topTag = $heroSideTop?->badge ?? 'GIMBAL 4K COMPACT';
                        $topLink = $heroSideTop?->link ?? route('shop.index', ['q' => 'Osmo Pocket']);
                        $topBtn = $heroSideTop?->button_text ?? 'Acheter';
                    @endphp
                    <div class="hero-side-card position-relative overflow-hidden flex-fill" title="{{ $heroSideTop?->title ?? '' }}">
                        <div class="hero-side-bg" style="background-image: url('{{ $topImg }}');"></div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0.15) 0%, rgba(15,23,42,0.6) 100%); pointer-events: none;"></div>
                        <span class="hero-side-tag">{{ $topTag }}</span>
                        @if(!empty($heroSideTop?->title))
                            <span class="visually-hidden">{{ $heroSideTop->title }}</span>
                        @endif
                        <div class="position-absolute bottom-0 start-0 m-3 z-2">
                            <a href="{{ $topLink }}" class="btn hero-action-btn" aria-label="{{ $heroSideTop?->title ?? $topBtn }}">
                                <span>{{ $topBtn }}</span>
                                <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Bottom Card: e.g. Godox AD800Pro --}}
                    @php 
                        $botImg = $heroSideBottom?->image_url ?? asset('images/camera/banner_godox_ad800.jpg');
                        $botTag = $heroSideBottom?->badge ?? 'OUTDOOR FLASH 800W';
                        $botLink = $heroSideBottom?->link ?? route('shop.index', ['q' => 'Godox AD800']);
                        $botBtn = $heroSideBottom?->button_text ?? 'Découvrez la GODOX AD800 PRO';
                    @endphp
                    <div class="hero-side-card position-relative overflow-hidden flex-fill" title="{{ $heroSideBottom?->title ?? '' }}">
                        <div class="hero-side-bg" style="background-image: url('{{ $botImg }}');"></div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0.15) 0%, rgba(15,23,42,0.6) 100%); pointer-events: none;"></div>
                        <span class="hero-side-tag">{{ $botTag }}</span>
                        @if(!empty($heroSideBottom?->title))
                            <span class="visually-hidden">{{ $heroSideBottom->title }}</span>
                        @endif
                        <div class="position-absolute bottom-0 start-0 m-3 z-2">
                            <a href="{{ $botLink }}" class="btn hero-action-btn" aria-label="{{ $heroSideBottom?->title ?? $botBtn }}">
                                <span>{{ $botBtn }}</span>
                                <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- =========================================================================
     2. CATÉGORIES POPULAIRES (Modern Smooth Swiper Slider)
     ========================================================================= --}}
<section class="popular-categories-section py-3 py-md-4">
    <div class="container position-relative">
        <div class="d-flex align-items-center justify-content-between mb-3 mb-md-4">
            <div>
                <span class="badge text-white fw-bold px-2 px-md-3 py-1 mb-1 mb-md-2 d-inline-block" style="background: #0f172a; border-radius: 999px; font-size: 0.70rem; letter-spacing: 0.5px;">
                    <i class="fas fa-layer-group me-1 text-danger"></i> RAYONS PHARES
                </span>
                <h2 class="fw-bold text-dark mb-0 mb-md-1" style="font-size: clamp(1.25rem, 3.5vw, 1.65rem); letter-spacing: -0.4px;">Catégories Populaires</h2>
                <p class="text-secondary small mb-0 d-none d-md-block" style="font-size: 0.88rem;">Explorez nos rayons phares et équipements audiovisuels certifiés au Maroc</p>
            </div>

            {{-- Slider Controls --}}
            <div class="cat-slider-nav-wrap d-flex align-items-center gap-1 gap-md-2">
                <button type="button" 
                        class="btn btn-cat-swiper-arrow d-flex align-items-center justify-content-center" 
                        id="catSwiperPrev" 
                        aria-label="Précédent" 
                        title="Catégories précédentes">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" 
                        class="btn btn-cat-swiper-arrow d-flex align-items-center justify-content-center" 
                        id="catSwiperNext" 
                        aria-label="Suivant" 
                        title="Catégories suivantes">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        {{-- Swiper Container --}}
        <div class="swiper popular-cats-swiper pb-2">
            <div class="swiper-wrapper align-items-stretch">
                @foreach($popularCategories as $cat)
                    <div class="swiper-slide h-auto">
                        <a href="{{ $cat['url'] }}" 
                           class="cat-item-card h-100 p-2 p-md-3 bg-white text-decoration-none d-flex flex-column align-items-center justify-content-center position-relative {{ !empty($cat['highlight']) ? 'cat-item-highlight' : '' }}">
                            
                            {{-- Item Count Badge at Top Right --}}
                            <span class="cat-badge-counter badge text-white fw-bold">
                                {{ $cat['count'] }}
                            </span>

                            {{-- SVG Icon --}}
                            <div class="cat-icon-slot mb-2 mb-md-3 d-flex align-items-center justify-content-center">
                                <img src="{{ $cat['icon'] }}" alt="{{ $cat['title'] }}" class="cat-icon-svg" loading="lazy">
                            </div>

                            {{-- Title --}}
                            <span class="cat-label text-uppercase fw-bold text-center">
                                {{ $cat['title'] }}
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Dots pagination below categories --}}
        <div class="popular-cats-pagination text-center mt-2 mt-md-3 d-flex justify-content-center align-items-center gap-2"></div>
    </div>
</section>

{{-- =========================================================================
     3. NOTRE SÉLECTION DE PRODUITS (Signature Red Box Showcase - Screenshot 3)
     ========================================================================= --}}
<section class="featured-products-section py-3 py-md-4">
    <div class="container">
        <div class="selection-showcase-box">
            {{-- Header Bar --}}
            <div class="selection-showcase-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 mb-md-4 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="selection-accent-badge"></span>
                    <h2 class="fw-bold text-dark mb-0 selection-title" style="font-size: clamp(1.25rem, 3vw, 1.55rem); letter-spacing: -0.4px;">
                        Notre sélection de produits
                    </h2>
                </div>
                <div>
                    <a href="{{ route('shop.index') }}" 
                       class="btn btn-outline-danger fw-bold px-3 py-1 text-nowrap d-inline-flex align-items-center gap-2" 
                       style="border-radius: 8px; font-size: 0.82rem; border-color: #c8102e;">
                        <span>Voir tout le catalogue</span>
                        <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                    </a>
                </div>
            </div>

            {{-- Products Grid (5-column on desktop matching Screenshot 3, 2-column on mobile) --}}
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3">
                @foreach($featuredProducts->take(10) as $product)
                    @php
                        $brand = 'PRO';
                        if (stripos($product->name, 'Sony') !== false) $brand = 'SONY';
                        elseif (stripos($product->name, 'Canon') !== false) $brand = 'CANON';
                        elseif (stripos($product->name, 'DJI') !== false) $brand = 'DJI';
                        elseif (stripos($product->name, 'Godox') !== false) $brand = 'GODOX';
                        elseif (stripos($product->name, 'Blackmagic') !== false) $brand = 'BLACKMAGIC';
                        elseif (stripos($product->name, 'Rode') !== false || stripos($product->name, 'RØDE') !== false) $brand = 'RØDE';
                        elseif (stripos($product->name, 'Insta360') !== false) $brand = 'INSTA360';
                        elseif (stripos($product->name, 'Lexar') !== false) $brand = 'LEXAR';
                        elseif (stripos($product->name, 'SmallRig') !== false) $brand = 'SMALLRIG';
                        
                        $sessionCart = session('cart', []);
                        $isInCart = isset($sessionCart[$product->id]);
                        $isAr = app()->getLocale() === 'ar';
                    @endphp
                    <div class="col">
                        <div class="featured-pro-card selection-card h-100 d-flex flex-column justify-content-between">
                            {{-- Product Image with clean white background --}}
                            <div class="featured-pro-img-box mb-2">
                                @if(!$product->isInStock())
                                    <span class="featured-card-oos-badge">{{ $isAr ? 'نفذت الكمية' : 'Épuisé' }}</span>
                                @endif
                                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                            </div>

                            {{-- Product Info --}}
                            <div class="mb-2 flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="featured-brand-chip d-none">{{ $brand }}</span>
                                    <h3 class="featured-pro-title">
                                        <a href="{{ route('shop.show', $product->slug ?: $product->id) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                </div>

                                <div>
                                    {{-- Price in vivid green --}}
                                    <div class="d-flex align-items-baseline gap-2 mb-1">
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <span class="fw-bold price-tag-green" style="font-size: 1.05rem;">
                                                MAD {{ number_format($product->sale_price, 2, ',', '.') }}
                                            </span>
                                            <span class="text-danger text-decoration-line-through small" style="font-size: 0.78rem;">
                                                MAD {{ number_format($product->price, 2, ',', '.') }}
                                            </span>
                                        @elseif($product->price && $product->price > 0)
                                            <span class="fw-bold price-tag-green" style="font-size: 1.05rem;">
                                                MAD {{ number_format($product->price, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                Sur devis
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Stock badge --}}
                                    <div class="d-flex align-items-center gap-1 mb-2">
                                        @if($product->isInStock())
                                            <span class="featured-stock-pill">
                                                <span class="stock-pulse-dot"></span>
                                                <span>{{ $isAr ? 'متوفر' : 'EN STOCK' }}</span>
                                            </span>
                                        @else
                                            <span class="featured-stock-pill featured-stock-pill--out">
                                                <i class="fas fa-times-circle text-danger" style="font-size: 10px;"></i>
                                                <span>{{ $isAr ? 'نفذت الكمية' : 'RUPTURE DE STOCK' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Full-width Action Button --}}
                            <div class="mt-auto pt-1">
                                @if($product->isInStock())
                                    <button type="button" 
                                            class="btn w-100 featured-pro-btn {{ $isInCart ? 'is-in-cart' : '' }}" 
                                            onclick="addToCart({{ $product->id }}, 1, this)"
                                            data-product-id="{{ $product->id }}"
                                            title="{{ $isInCart ? ($isAr ? 'في السلة' : 'Dans le panier') : ($isAr ? 'أضف إلى السلة' : 'Ajouter au panier') }}">
                                        @if($isInCart)
                                            <i class="fas fa-check" style="font-size: 11px;"></i>
                                            <span>{{ $isAr ? 'في السلة' : 'Dans le panier' }}</span>
                                        @else
                                            <span>{{ $isAr ? 'أضف إلى السلة' : 'Ajouter au panier' }}</span>
                                        @endif
                                    </button>
                                @else
                                    <button type="button" 
                                            class="btn w-100 featured-pro-btn featured-pro-btn--out" 
                                            disabled 
                                            title="{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}">
                                        <i class="fas fa-ban" style="font-size: 11px;"></i>
                                        <span>{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


{{-- =========================================================================
     4. NOUVELLE ARRIVAGE & COIN OCCASION (Matching Screenshot 4)
     ========================================================================= --}}
<section class="new-arrivals-and-occasion-section py-3 py-md-4">
    <div class="container">
        {{-- High-Tech Promotional Banner (Insta360 Exclusive Promo) --}}
        @php
            $promoImg = $promoMiddleBanner?->image_url ?? asset('images/camera/banner_insta360_promo.jpg');
            $promoTag1 = $promoMiddleBanner?->badge ?? 'PRODUIT TENDANCE';
            $promoTag2 = $promoMiddleBanner?->subtitle ?? 'PROMOTION EXCLUSIVE';
            $promoTitle = $promoMiddleBanner?->title ?? 'Promotion Exclusive sur Insta360';
            $promoDesc = $promoMiddleBanner?->description ?? "Capturez l'impossible avec les caméras d'action et 360° les plus innovantes du marché. Remises jusqu'à 25% disponibles sur le showroom.";
            $promoBtn = $promoMiddleBanner?->button_text ?? 'Voir la Promotion';
            $promoLink = $promoMiddleBanner?->link ?? route('shop.index', ['category' => 'accessoires-insta360']);
            $promoFeatures = !empty($promoMiddleBanner?->features_list) ? $promoMiddleBanner->features_list : ['VIDÉO 360° IMMERSIVE', 'STABILISATION AVANCÉE', 'RÉSOLUTION 5.7K ULTRA HD'];
        @endphp
        <div class="insta360-promo-banner position-relative mb-4 mb-md-5" style="background-image: url('{{ $promoImg }}');">
            <div class="insta360-promo-overlay"></div>
            <div class="position-relative z-2 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($promoTag1)<span class="promo-tag-red">{{ $promoTag1 }}</span>@endif
                        @if($promoTag2)<span class="promo-tag-dark">{{ $promoTag2 }}</span>@endif
                    </div>
                    <h3 class="fw-bold text-white mb-2" style="font-size: 1.85rem; letter-spacing: -0.4px;">
                        {{ $promoTitle }}
                    </h3>
                    @if($promoDesc)
                    <p class="text-white-50 mb-0 d-none d-md-block" style="max-width: 600px; font-size: 0.95rem;">
                        {{ $promoDesc }}
                    </p>
                    @endif
                    @if(!empty($promoFeatures))
                    <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4 mt-2">
                        @foreach($promoFeatures as $feature)
                        <span class="promo-bullet"><i class="fas fa-check text-danger"></i> {{ $feature }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ $promoLink }}" class="promo-cta-btn">
                        <span>{{ $promoBtn }}</span>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size: 12px;"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Row 1: Nouvelle Arrivage (Full Row) --}}
        <div class="mb-4 mb-md-5">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="selection-accent-badge"></span>
                    <h2 class="fw-bold text-dark mb-0" style="font-size: clamp(1.2rem, 2.5vw, 1.45rem); letter-spacing: -0.3px;">
                        Nouvelle Arrivage
                    </h2>
                </div>
                <a href="{{ route('shop.index') }}" class="text-secondary text-decoration-none small fw-semibold hover-red">
                    <span>Voir tout</span>
                    <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                </a>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3">
                @foreach($newArrivalProducts->take(6) as $product)
                    @php
                        $brand = 'PRO';
                        if (stripos($product->name, 'Sony') !== false) $brand = 'SONY';
                        elseif (stripos($product->name, 'Canon') !== false) $brand = 'CANON';
                        elseif (stripos($product->name, 'Godox') !== false) $brand = 'GODOX';
                        
                        $sessionCart = session('cart', []);
                        $isInCart = isset($sessionCart[$product->id]);
                        $isAr = app()->getLocale() === 'ar';
                    @endphp
                    <div class="col">
                        <div class="featured-pro-card selection-card workflow-card h-100 d-flex flex-column justify-content-between">
                            {{-- Product Image with clean white background --}}
                            <div class="featured-pro-img-box mb-2">
                                @if(!$product->isInStock())
                                    <span class="featured-card-oos-badge">{{ $isAr ? 'نفذت الكمية' : 'Épuisé' }}</span>
                                @endif
                                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                            </div>

                            {{-- Product Info --}}
                            <div class="mb-2 flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="featured-brand-chip d-none">{{ $brand }}</span>
                                    <h3 class="featured-pro-title">
                                        <a href="{{ route('shop.show', $product->slug ?: $product->id) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                </div>

                                <div>
                                    {{-- Price in vivid green --}}
                                    <div class="d-flex align-items-baseline gap-2 mb-1">
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <span class="fw-bold price-tag-green" style="font-size: 1.05rem;">
                                                MAD {{ number_format($product->sale_price, 2, ',', '.') }}
                                            </span>
                                            <span class="text-danger text-decoration-line-through small" style="font-size: 0.78rem;">
                                                MAD {{ number_format($product->price, 2, ',', '.') }}
                                            </span>
                                        @elseif($product->price && $product->price > 0)
                                            <span class="fw-bold price-tag-green" style="font-size: 1.05rem;">
                                                MAD {{ number_format($product->price, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                Sur devis
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Stock badge --}}
                                    <div class="d-flex align-items-center gap-1 mb-2">
                                        @if($product->isInStock())
                                            <span class="featured-stock-pill workflow-meta-pill">
                                                <span class="stock-pulse-dot"></span>
                                                <span>{{ $isAr ? 'متوفر' : 'EN STOCK' }}</span>
                                            </span>
                                        @else
                                            <span class="featured-stock-pill workflow-meta-pill featured-stock-pill--out">
                                                <i class="fas fa-times-circle text-danger" style="font-size: 10px;"></i>
                                                <span>{{ $isAr ? 'نفذت الكمية' : 'RUPTURE DE STOCK' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Full-width Action Button --}}
                            <div class="mt-auto pt-1">
                                @if($product->isInStock())
                                    <button type="button" 
                                            class="btn w-100 featured-pro-btn {{ $isInCart ? 'is-in-cart' : '' }}" 
                                            onclick="addToCart({{ $product->id }}, 1, this)"
                                            data-product-id="{{ $product->id }}"
                                            title="{{ $isInCart ? ($isAr ? 'في السلة' : 'Dans le panier') : ($isAr ? 'أضف إلى السلة' : 'Ajouter au panier') }}">
                                        @if($isInCart)
                                            <i class="fas fa-check" style="font-size: 11px;"></i>
                                            <span>{{ $isAr ? 'في السلة' : 'Dans le panier' }}</span>
                                        @else
                                            <span>{{ $isAr ? 'أضف إلى السلة' : 'Ajouter au panier' }}</span>
                                        @endif
                                    </button>
                                @else
                                    <button type="button" 
                                            class="btn w-100 featured-pro-btn featured-pro-btn--out" 
                                            disabled 
                                            title="{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}">
                                        <i class="fas fa-ban" style="font-size: 11px;"></i>
                                        <span>{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Row 2: Coin Occasion (Moved under Nouvelle Arrivage - Full Row) --}}
        <div>
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white fw-bold px-2 py-1" style="font-size: 0.7rem; border-radius: 4px;">PRO</span>
                    <h2 class="fw-bold text-dark mb-0" style="font-size: clamp(1.2rem, 2.5vw, 1.45rem); letter-spacing: -0.3px;">
                        Coin Occasion (Reconditionné & Certifié)
                    </h2>
                </div>
                <a href="{{ route('shop.index', ['category' => 'occasion']) }}" class="text-secondary text-decoration-none small fw-semibold hover-red">
                    <span>Voir tout</span>
                    <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                </a>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-md-3">
                @foreach($occasionProducts as $product)
                    @php
                        $brand = 'PRO';
                        if (stripos($product->name, 'Sony') !== false) $brand = 'SONY';
                        elseif (stripos($product->name, 'Fujifilm') !== false) $brand = 'FUJIFILM';
                        elseif (stripos($product->name, 'Canon') !== false) $brand = 'CANON';
                        elseif (stripos($product->name, 'Olympus') !== false) $brand = 'OLYMPUS';
                        
                        $sessionCart = session('cart', []);
                        $isInCart = isset($sessionCart[$product->id]);
                        $isAr = app()->getLocale() === 'ar';
                    @endphp
                    <div class="col">
                        <div class="featured-pro-card selection-card workflow-card h-100 d-flex flex-column justify-content-between position-relative">
                            {{-- Blue OCCASION badge at top left --}}
                            <div class="position-absolute top-0 start-0 m-2 z-2">
                                <span class="badge bg-primary text-white fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.5px; border-radius: 4px;">
                                    OCCASION
                                </span>
                            </div>

                            {{-- Product Image with clean white background --}}
                            <div class="featured-pro-img-box mb-2">
                                @if(!$product->isInStock())
                                    <span class="featured-card-oos-badge">{{ $isAr ? 'نفذت الكمية' : 'Épuisé' }}</span>
                                @endif
                                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                            </div>

                            {{-- Product Info --}}
                            <div class="mb-2 flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="featured-brand-chip d-none">{{ $brand }}</span>
                                    <h3 class="featured-pro-title">
                                        <a href="{{ route('shop.show', $product->slug ?: $product->id) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                </div>

                                <div>
                                    {{-- Price in vivid green --}}
                                    <div class="d-flex align-items-baseline gap-2 mb-1">
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <span class="fw-bold price-tag-green" style="font-size: 1.05rem;">
                                                MAD {{ number_format($product->sale_price, 2, ',', '.') }}
                                            </span>
                                            <span class="text-danger text-decoration-line-through small" style="font-size: 0.78rem;">
                                                MAD {{ number_format($product->price, 2, ',', '.') }}
                                            </span>
                                        @elseif($product->price && $product->price > 0)
                                            <span class="fw-bold price-tag-green" style="font-size: 1.05rem;">
                                                MAD {{ number_format($product->price, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                Sur devis
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Guarantee / Occasion badge --}}
                                    <div class="d-flex align-items-center gap-1 mb-2">
                                        @if($product->isInStock())
                                            <span class="featured-stock-pill workflow-meta-pill" style="background: #f0fdf4; border: 1px solid #dcfce7; color: #16a34a;">
                                                <i class="fas fa-shield-check text-success" style="font-size: 10px;"></i>
                                                <span>GARANTI 6 MOIS</span>
                                            </span>
                                        @else
                                            <span class="featured-stock-pill workflow-meta-pill featured-stock-pill--out">
                                                <i class="fas fa-times-circle text-danger" style="font-size: 10px;"></i>
                                                <span>{{ $isAr ? 'نفذت الكمية' : 'RUPTURE DE STOCK' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Full-width Action Button --}}
                            <div class="mt-auto pt-1">
                                @if($product->isInStock())
                                    <button type="button" 
                                            class="btn w-100 featured-pro-btn {{ $isInCart ? 'is-in-cart' : '' }}" 
                                            onclick="addToCart({{ $product->id }}, 1, this)"
                                            data-product-id="{{ $product->id }}"
                                            title="{{ $isInCart ? ($isAr ? 'في السلة' : 'Dans le panier') : ($isAr ? 'أضف إلى السلة' : 'Ajouter au panier') }}">
                                        @if($isInCart)
                                            <i class="fas fa-check" style="font-size: 11px;"></i>
                                            <span>{{ $isAr ? 'في السلة' : 'Dans le panier' }}</span>
                                        @else
                                            <span>{{ $isAr ? 'أضف إلى السلة' : 'Ajouter au panier' }}</span>
                                        @endif
                                    </button>
                                @else
                                    <button type="button" 
                                            class="btn w-100 featured-pro-btn featured-pro-btn--out" 
                                            disabled 
                                            title="{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}">
                                        <i class="fas fa-ban" style="font-size: 11px;"></i>
                                        <span>{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


{{-- =========================================================================
     5. DOUBLE BANNERS (Microphones Sound + Studio Lighting)
     ========================================================================= --}}
<section class="double-banners-section py-4">
    <div class="container">
        <div class="row g-4">
            {{-- Left Banner: Microphones --}}
            <div class="col-md-6">
                <div class="banner-box position-relative p-4 p-md-5 d-flex flex-column justify-content-center align-items-start" 
                     style="background: #0b0f19 url('{{ asset('images/camera/banner_mic_sound.jpg') }}') center/cover no-repeat;">
                    <div class="banner-box-overlay banner-mic-overlay"></div>
                    <div class="position-relative z-2">
                        <span class="badge text-white px-2 py-1 mb-2 d-inline-block" style="background: rgba(255,255,255,0.14); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); font-size: 0.68rem; letter-spacing: 0.8px;">
                            PRO AUDIO & PODCAST
                        </span>
                        <h3 class="fw-bold text-white mb-2" style="font-size: 1.45rem; line-height: 1.35; max-width: 360px;">
                            Élevez Votre Son : Microphones de Qualité pour Tous Vos Besoins
                        </h3>
                        <p class="text-white-50 small mb-3 d-none d-sm-block" style="max-width: 340px; font-size: 0.82rem;">
                            Micros canon, HF cravate & enregistreurs 32-bit float pour tournages exigeants.
                        </p>
                        <span class="banner-pill-btn">
                            <span>Explorer la Gamme Audio</span>
                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                        </span>
                    </div>
                    <a href="{{ route('shop.index', ['category' => 'materiel-de-podcast']) }}" class="stretched-link" aria-label="Microphones"></a>
                </div>
            </div>

            {{-- Right Banner: Lighting --}}
            <div class="col-md-6">
                <div class="banner-box position-relative p-4 p-md-5 d-flex flex-column justify-content-center align-items-start" 
                     style="background: #0b0f19 url('{{ asset('images/camera/banner_studio_lighting.jpg') }}') center/cover no-repeat;">
                    <div class="banner-box-overlay banner-light-overlay"></div>
                    <div class="position-relative z-2">
                        <span class="badge text-white px-2 py-1 mb-2 d-inline-block" style="background: rgba(255,255,255,0.14); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); font-size: 0.68rem; letter-spacing: 0.8px;">
                            BEST PRODUCTS • STUDIO LIGHTING
                        </span>
                        <h3 class="fw-bold text-white mb-2" style="font-size: 1.45rem; line-height: 1.35; max-width: 360px;">
                            Tout le matériel nécessaire pour votre éclairage.
                        </h3>
                        <p class="text-white-50 small mb-3 d-none d-sm-block" style="max-width: 340px; font-size: 0.82rem;">
                            Flashes haute puissance Godox AD, projecteurs continus LED et softboxes paraboliques.
                        </p>
                        <span class="banner-pill-btn">
                            <span>Explorer l'Éclairage Studio</span>
                            <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                        </span>
                    </div>
                    <a href="{{ route('shop.index', ['category' => 'eclairage-studio']) }}" class="stretched-link" aria-label="Éclairage Studio"></a>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- =========================================================================
     6. FINANCEMENTS PUBLICS & PROGRAMMES DE L'ÉTAT (INDH, FORSA, COOPÉRATIVES)
     ========================================================================= --}}
<section class="state-funding-section py-4">
    <div class="container">
        <div class="state-funding-wrapper">
            {{-- Header --}}
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="selection-accent-badge"></span>
                        <span class="badge text-white fw-bold px-2 py-1" style="background: #0f172a; font-size: 0.70rem; border-radius: 4px; letter-spacing: 0.4px;">
                            <i class="fas fa-landmark text-danger me-1"></i>PROGRAMMES ÉTAT & SUBVENTIONS
                        </span>
                        <span class="badge text-white fw-bold px-2 py-1" style="background: #16a34a; font-size: 0.70rem; border-radius: 4px;">
                            <i class="fas fa-check-circle me-1"></i>FOURNISSEUR AGRÉÉ MAROC
                        </span>
                    </div>

                    <h2 class="fw-bold text-dark mb-1" style="font-size: clamp(1.25rem, 2.8vw, 1.55rem); letter-spacing: -0.4px;">
                        Bénéficiaires de Financements Publics (INDH & Projets Soutenus)
                    </h2>
                    <p class="text-secondary mb-0" style="font-size: 0.90rem;">
                        Obtenez votre <strong>devis proforma officiel conforme</strong> (avec cachet, ICE et TVA 20%) en <strong>3 étapes simples, sans aucun paiement en ligne</strong> :
                    </p>
                </div>

                {{-- Direct WhatsApp Button in Header --}}
                <div class="flex-shrink-0">
                    <a href="https://wa.me/212629035777?text=Bonjour%2C%20je%20suis%20b%C3%A9n%C3%A9ficiaire%20d%27un%20financement%20%28INDH%20%2F%20Programme%20%C3%89tat%29%20et%20je%20souhaite%20obtenir%20un%20devis%20proforma%20pour%20mon%20projet." 
                       target="_blank" 
                       class="btn text-white fw-bold px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm" 
                       style="background: #25d366; border-radius: 8px; font-size: 0.84rem;">
                        <i class="fab fa-whatsapp" style="font-size: 16px;"></i>
                        <span>Conseiller INDH WhatsApp</span>
                    </a>
                </div>
            </div>

            {{-- 3-Step Connected Visual Cards --}}
            <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
                {{-- Step 1 : Sélection du matériel --}}
                <div class="col">
                    <div class="indh-step-card-pro">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="indh-step-badge-num">01</div>
                            <span class="badge fw-bold" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-size: 0.70rem; letter-spacing: 0.3px; border-radius: 4px; padding: 4px 8px;">
                                ÉTAPE 1 • SÉLECTION
                            </span>
                        </div>
                        
                        <div class="indh-step-icon-slot">
                            <i class="fas fa-cart-shopping"></i>
                        </div>
                        
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 1.12rem; letter-spacing: -0.2px;">
                            1. Composez Votre Panier
                        </h3>
                        
                        <p class="text-secondary small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                            Ajoutez librement au panier les caméras, micros, drones ou éclairages selon le budget alloué à votre subvention.
                        </p>

                        <div class="indh-feature-pill">
                            <i class="fas fa-check text-danger me-1"></i>Matériel 100% Neuf & Garanti constructeur
                        </div>

                        <div class="pt-2 border-top mt-auto">
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                <i class="fas fa-tag text-danger me-1"></i>Prix affichés en TTC (TVA 20% incluse)
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Step 2 : Commande sans paiement --}}
                <div class="col">
                    <div class="indh-step-card-pro">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="indh-step-badge-num">02</div>
                            <span class="badge fw-bold" style="background: #fef9c3; color: #854d0e; border: 1px solid #fde047; font-size: 0.70rem; letter-spacing: 0.3px; border-radius: 4px; padding: 4px 8px;">
                                ÉTAPE 2 • AUCUN PAIEMENT (0 DH)
                            </span>
                        </div>
                        
                        <div class="indh-step-icon-slot">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 1.12rem; letter-spacing: -0.2px;">
                            2. Validez sans Payer (0 DH)
                        </h3>
                        
                        <p class="text-secondary small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                            Validez votre commande sur le site. <strong>Aucun paiement bancaire n'est requis</strong> pour enregistrer votre liste et obtenir un N° de dossier.
                        </p>

                        <div class="indh-feature-pill highlight-zero">
                            <i class="fas fa-shield-halved text-success me-1"></i>0,00 DH en ligne • N° de dossier immédiat
                        </div>

                        <div class="pt-2 border-top mt-auto">
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                <i class="fas fa-bolt text-warning me-1"></i>Réservation immédiate du matériel
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Step 3 : Support & Devis proforma (Destination Step) --}}
                <div class="col">
                    <div class="indh-step-card-pro step-destination">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="indh-step-badge-num">03</div>
                            <span class="badge fw-bold" style="background: #fef2f2; color: #c8102e; border: 1px solid #fee2e2; font-size: 0.70rem; letter-spacing: 0.3px; border-radius: 4px; padding: 4px 8px;">
                                ÉTAPE 3 • DEVIS SOUS 2H CHRONO
                            </span>
                        </div>
                        
                        <div class="indh-step-icon-slot">
                            <i class="fas fa-stamp"></i>
                        </div>
                        
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 1.12rem; letter-spacing: -0.2px;">
                            3. Recevez le Devis Cacheté
                        </h3>
                        
                        <p class="text-secondary small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                            Envoyez votre N° de dossier par WhatsApp. Nous vous délivrons votre <strong>devis proforma officiel avec cachet et TVA 20%</strong> sous 2h.
                        </p>

                        <div class="indh-feature-pill highlight-proforma">
                            <i class="fas fa-certificate text-danger me-1"></i>Cachet Officiel Wina Shop • ICE • RC
                        </div>

                        <div class="pt-2 border-top mt-auto">
                            <a href="https://wa.me/212629035777?text=Bonjour%2C%20j%27ai%20enregistr%C3%A9%20mon%20panier%20sur%20le%20site%20pour%20mon%20projet%20INDH%20%2F%20Financement%20Public.%20Voici%20mon%20num%C3%A9ro%20de%20dossier%20pour%20recevoir%20le%20devis%20proforma%20officiel%20cachet%C3%A9." 
                               target="_blank" 
                               class="btn text-white fw-bold w-100 py-2 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" 
                               style="background: #25d366; border-radius: 8px; font-size: 0.80rem; box-shadow: 0 4px 12px rgba(37,211,102,0.3);">
                                <i class="fab fa-whatsapp" style="font-size: 16px;"></i>
                                <span>Envoyer mon N° par WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4 Legal & Institutional Compliance Guarantees (2x2 on mobile, 4 in row on desktop) --}}
            <div class="row row-cols-2 row-cols-lg-4 g-2 mb-4">
                <div class="col">
                    <div class="indh-guarantee-pill">
                        <div class="indh-guarantee-icon">
                            <i class="fas fa-file-invoice text-danger"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0" style="font-size: 0.84rem;">Mentions Légales 100%</h4>
                            <span class="text-muted small" style="font-size: 0.72rem;">ICE, RC, IF, Patente & TVA 20%</span>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="indh-guarantee-pill">
                        <div class="indh-guarantee-icon">
                            <i class="fas fa-clock text-primary"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0" style="font-size: 0.84rem;">Édition sous 2 Heures</h4>
                            <span class="text-muted small" style="font-size: 0.72rem;">Respect des délais de commission</span>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="indh-guarantee-pill">
                        <div class="indh-guarantee-icon">
                            <i class="fas fa-shield-halved text-success"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0" style="font-size: 0.84rem;">Matériel 100% Neuf</h4>
                            <span class="text-muted small" style="font-size: 0.72rem;">Garantie constructeur & SAV Maroc</span>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="indh-guarantee-pill">
                        <div class="indh-guarantee-icon">
                            <i class="fas fa-user-tie text-warning"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0" style="font-size: 0.84rem;">Conseiller Pro Dédié</h4>
                            <span class="text-muted small" style="font-size: 0.72rem;">Aide au calibrage de votre budget</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pro Action & Support Bar --}}
            <div class="indh-banner-cta d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div class="position-relative z-2">
                    <h3 class="fw-bold text-white mb-1" style="font-size: 1.25rem;">
                        Besoin d'un devis urgent pour votre dossier de subvention ?
                    </h3>
                    <p class="text-white-50 mb-0 small" style="font-size: 0.84rem;">
                        Cellule INDH & Projets publics : assistance directe par téléphone au <strong class="text-white">06 29 03 57 77</strong> ou par WhatsApp.
                    </p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2 position-relative z-2 flex-shrink-0">
                    <a href="{{ route('shop.index') }}" 
                       class="btn btn-light fw-bold px-3 py-2 text-nowrap d-inline-flex align-items-center gap-2 shadow-sm" 
                       style="border-radius: 10px; font-size: 0.84rem; min-height: 40px;">
                        <i class="fas fa-cart-plus text-danger" style="font-size: 12px;"></i>
                        <span>Sélectionner mon matériel</span>
                    </a>
                    <a href="https://wa.me/212629035777?text=Bonjour%2C%20je%20suis%20b%C3%A9n%C3%A9ficiaire%20d%27un%20financement%20%28INDH%20%2F%20Programme%20%C3%89tat%29%20et%20je%20souhaite%20obtenir%20un%20devis%20proforma%20pour%20mon%20projet." 
                       target="_blank" 
                       class="btn text-white fw-bold px-3 py-2 text-nowrap d-inline-flex align-items-center gap-2 shadow-sm" 
                       style="background: #25d366; border-radius: 10px; font-size: 0.84rem; min-height: 40px; box-shadow: 0 4px 14px rgba(37,211,102,0.4);">
                        <i class="fab fa-whatsapp" style="font-size: 16px;"></i>
                        <span>Demander mon Devis Proforma</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================================
     7. RÉASSURANCE & AVIS CLIENTS VÉRIFIÉS
     ========================================================================= --}}
<section class="trust-and-reviews-section py-4">
    <div class="container">
        {{-- 4 Pillars of Reassurance (2x2 on mobile, 4 in row on desktop) --}}
        <div class="row row-cols-2 row-cols-lg-4 g-2 g-md-3 mb-4">
            <div class="col">
                <div class="reassurance-box">
                    <div class="reassurance-icon icon-red">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Livraison Express 24-48h</h4>
                        <p class="text-secondary mb-0" style="font-size: 0.8rem; line-height: 1.45;">
                            Expédition rapide et sécurisée avec emballage renforcé anti-chocs dans tout le Maroc.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="reassurance-box">
                    <div class="reassurance-icon icon-blue">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">100% Neuf & Scellé</h4>
                        <p class="text-secondary mb-0" style="font-size: 0.8rem; line-height: 1.45;">
                            Matériel d'origine constructeur sous garantie officielle avec SAV réactif à Casablanca.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="reassurance-box">
                    <div class="reassurance-icon icon-amber">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Conseil Technique Pro</h4>
                        <p class="text-secondary mb-0" style="font-size: 0.8rem; line-height: 1.45;">
                            Une équipe de vidéastes et photographes vous aide à choisir la configuration idéale.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="reassurance-box">
                    <div class="reassurance-icon icon-green">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Paiement à la Livraison</h4>
                        <p class="text-secondary mb-0" style="font-size: 0.8rem; line-height: 1.45;">
                            Payez en toute sérénité à la réception de votre commande ou en ligne par carte bancaire.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verified Customer Reviews --}}
        <div class="bg-white p-3 p-md-5 rounded-4 border shadow-sm">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1" style="font-size: clamp(1.2rem, 2.8vw, 1.45rem); letter-spacing: -0.3px;">
                        La Confiance des Professionnels de l'Image
                    </h3>
                    <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                        Découvrez les retours d'expérience de nos clients photographes, vidéastes et créateurs au Maroc.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 px-3 py-2 bg-light rounded-pill border">
                    <div class="text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">4.9 / 5</span>
                    <span class="text-muted small">(Plus de 200 avis vérifiés)</span>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-3">
                <div class="col">
                    <div class="client-review-box">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="text-warning small">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <span class="badge text-success px-2 py-1" style="background: #dcfce7; border-radius: 999px; font-size: 0.68rem; font-weight: 700;">
                                    <i class="fas fa-check-circle me-1"></i> Achat Vérifié
                                </span>
                            </div>
                            <p class="client-review-text mb-0" style="font-size: 0.85rem; line-height: 1.55; color: #334155;">
                                "Livraison en 24h chrono à Marrakech pour ma Sony FX3A. Matériel impeccable, scellé d'origine. Les conseillers Wina Shop m'ont même guidé pour le choix de la poignée XLR."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
                            <div class="client-avatar-initials">YB</div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Youssef B.</h5>
                                <span class="text-muted small" style="font-size: 0.74rem;">Réalisateur & Cadreur • Marrakech</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="client-review-box">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="text-warning small">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <span class="badge text-success px-2 py-1" style="background: #dcfce7; border-radius: 999px; font-size: 0.68rem; font-weight: 700;">
                                    <i class="fas fa-check-circle me-1"></i> Achat Vérifié
                                </span>
                            </div>
                            <p class="client-review-text mb-0" style="font-size: 0.85rem; line-height: 1.55; color: #334155;">
                                "Pack DJI Mic 2 reçu à Casablanca le jour même. Une réactivité exemplaire au Maroc pour du matériel de son professionnel. Très satisfaite du service !"
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
                            <div class="client-avatar-initials" style="background: #c8102e;">SM</div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Sara M.</h5>
                                <span class="text-muted small" style="font-size: 0.74rem;">Créatrice de Contenu • Casablanca</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="client-review-box">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="text-warning small">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <span class="badge text-success px-2 py-1" style="background: #dcfce7; border-radius: 999px; font-size: 0.68rem; font-weight: 700;">
                                    <i class="fas fa-check-circle me-1"></i> Achat Vérifié
                                </span>
                            </div>
                            <p class="client-review-text mb-0" style="font-size: 0.85rem; line-height: 1.55; color: #334155;">
                                "Le plus grand choix de flashs Godox et d'accessoires SmallRig avec des tarifs justes et la disponibilité immédiate. Le support technique est toujours au rendez-vous."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
                            <div class="client-avatar-initials" style="background: #0284c7;">KT</div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Karim T.</h5>
                                <span class="text-muted small" style="font-size: 0.74rem;">Photographe Studio • Rabat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- =========================================================================
     8. NEWSLETTER PRIVILÈGE & CONSEILS D'EXPERTS
     ========================================================================= --}}
<section class="newsletter-pro-section py-4 mb-5">
    <div class="container">
        <div class="newsletter-banner position-relative">
            <div class="newsletter-glow"></div>
            <div class="newsletter-glow-secondary"></div>
            <div class="row align-items-center position-relative z-2 g-4">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge text-white fw-bold px-3 py-1" 
                              style="background: #c8102e; border-radius: 999px; font-size: 0.72rem; letter-spacing: 0.6px;">
                            COMMUNAUTÉ PRO
                        </span>
                        <span class="text-white-50 small">Restez à la pointe de l'audiovisuel</span>
                    </div>
                    <h3 class="fw-bold text-white mb-2" style="font-size: clamp(1.3rem, 3vw, 1.75rem); letter-spacing: -0.4px;">
                        Rejoignez le Cercle Privilège Wina Shop
                    </h3>
                    <p class="text-white-50 mb-0" style="font-size: 0.9rem; max-width: 540px; line-height: 1.55;">
                        Recevez nos alertes arrivages, bancs d'essai exclusifs, tutoriels d'étalonnage et offres privées réservées aux professionnels au Maroc.
                    </p>
                </div>
                <div class="col-lg-5">
                    <form action="{{ route('contact') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2">
                        <div class="position-relative flex-grow-1">
                            <input type="email" 
                                   name="newsletter_email" 
                                   placeholder="Votre adresse email professionnelle..." 
                                   class="form-control px-3 py-2 border-0 bg-white" 
                                   style="border-radius: 10px; font-size: 0.88rem; min-height: 48px;" 
                                   required>
                        </div>
                        <button type="submit" 
                                class="btn text-white fw-bold px-4 py-2 text-nowrap d-inline-flex align-items-center justify-content-center gap-2 shadow" 
                                style="background: linear-gradient(135deg, #c8102e, #a80c26); border-radius: 10px; font-size: 0.88rem; min-height: 48px;">
                            <span>S'inscrire</span>
                            <i class="fas fa-paper-plane" style="font-size: 11px;"></i>
                        </button>
                    </form>
                    <div class="text-white-50 small mt-2 d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                        <i class="fas fa-lock text-white-50"></i>
                        <span>Pas de spam. Désinscription possible à tout moment en 1 clic.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        // 1. Hero Main Banner Swiper Slider (Left Box)
        const heroSwiper = new Swiper('.hero-main-swiper', {
            slidesPerView: 1,
            loop: true,
            speed: 750,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            grabCursor: true,
            pagination: {
                el: '.hero-dash-pagination',
                clickable: true,
                bulletClass: 'hero-dash-item',
                bulletActiveClass: 'active',
                renderBullet: function(index, className) {
                    return '<button type="button" class="' + className + '" aria-label="Diapositive ' + (index + 1) + '"></button>';
                }
            },
            navigation: {
                prevEl: '.hero-swiper-prev',
                nextEl: '.hero-swiper-next',
            },
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
        });

        // 2. Popular Categories Swiper Slider
        const catSwiper = new Swiper('.popular-cats-swiper', {
            slidesPerView: 3,
            slidesPerGroup: 3,
            spaceBetween: 8,
            watchSlidesProgress: true,
            grabCursor: true,
            rewind: true,
            speed: 500,
            navigation: {
                prevEl: '#catSwiperPrev',
                nextEl: '#catSwiperNext',
            },
            pagination: {
                el: '.popular-cats-pagination',
                clickable: true,
                bulletClass: 'cat-swiper-dot',
                bulletActiveClass: 'cat-swiper-dot-active',
            },
            breakpoints: {
                480: {
                    slidesPerView: 3,
                    slidesPerGroup: 3,
                    spaceBetween: 10,
                },
                576: {
                    slidesPerView: 4,
                    slidesPerGroup: 4,
                    spaceBetween: 10,
                },
                768: {
                    slidesPerView: 5,
                    slidesPerGroup: 5,
                    spaceBetween: 12,
                },
                992: {
                    slidesPerView: 6,
                    slidesPerGroup: 6,
                    spaceBetween: 14,
                },
                1200: {
                    slidesPerView: 8,
                    slidesPerGroup: 8,
                    spaceBetween: 16,
                },
            },
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
        });
    }
});
</script>
@endpush
