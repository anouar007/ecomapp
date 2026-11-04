@extends('layouts.frontend')

@section('meta_title', 'cameralik — Camera. Create. Capture.')
@section('meta_description', __('Camera, lenses, accessories and creative gear for photographers and filmmakers.'))

@push('styles')
<style>
/* ==========================================================================
   CAMERALIK DESIGN SYSTEM (Scoped for Homepage & Modern Components)
   ========================================================================== */
:root {
    --clk-ink: #101010;
    --clk-muted: #6d6d6d;
    --clk-line: #e8e8e8;
    --clk-soft: #f6f6f4;
    --clk-accent: #ef2727;
    --clk-white: #ffffff;
    --clk-radius: 24px;
    --clk-max: 1240px;
}

body {
    background-color: #ffffff !important;
    color: var(--clk-ink) !important;
    font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
}

.clk-wrap {
    max-width: var(--clk-max);
    margin: 0 auto;
    padding: 0 28px;
}

@media (max-width: 620px) {
    .clk-wrap {
        padding: 0 18px;
    }
}

/* ── Typography & Helpers ── */
.clk-kicker {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--clk-accent);
}

.clk-h2 {
    font-size: clamp(32px, 4vw, 52px);
    letter-spacing: -.045em;
    line-height: 1.05;
    margin: 8px 0 0;
    color: var(--clk-ink);
    font-weight: 800;
}

.clk-see {
    font-size: 14px;
    font-weight: 750;
    border-bottom: 1.5px solid var(--clk-ink);
    padding-bottom: 4px;
    color: var(--clk-ink);
    text-decoration: none !important;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.clk-see:hover {
    color: var(--clk-accent);
    border-color: var(--clk-accent);
}

.clk-btn {
    padding: 13px 22px;
    border-radius: 999px;
    border: 1px solid var(--clk-ink);
    font-size: 14px;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none !important;
    transition: all 0.25s ease;
    cursor: pointer;
}

.clk-btn.primary {
    background: var(--clk-ink);
    color: #fff !important;
}

.clk-btn.primary:hover {
    background: #292929;
    border-color: #292929;
    color: #fff !important;
    transform: translateY(-2px);
}

.clk-btn.secondary {
    background: #fff;
    color: var(--clk-ink) !important;
}

.clk-btn.secondary:hover {
    border-color: var(--clk-accent);
    color: var(--clk-accent) !important;
    transform: translateY(-2px);
}

/* ── Hero Section ── */
.clk-hero {
    padding: 56px 0 28px;
}

.clk-hero-grid {
    display: grid;
    grid-template-columns: 1.05fr .95fr;
    gap: 42px;
    align-items: center;
}

.clk-eyebrow {
    display: inline-flex;
    gap: 8px;
    align-items: center;
    padding: 7px 14px;
    border: 1px solid #ddd;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: #333;
}

.clk-eyebrow i {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--clk-accent);
    display: inline-block;
}

.clk-hero h1 {
    font-size: clamp(46px, 6.5vw, 86px);
    line-height: .94;
    letter-spacing: -.065em;
    margin: 20px 0 22px;
    max-width: 760px;
    font-weight: 900;
    color: var(--clk-ink);
}

.clk-hero p {
    font-size: 18px;
    line-height: 1.65;
    color: var(--clk-muted);
    max-width: 580px;
    margin: 0;
}

.clk-cta {
    display: flex;
    gap: 12px;
    margin-top: 28px;
    flex-wrap: wrap;
}

.clk-hero-art {
    min-height: 460px;
    border-radius: 32px;
    background: #111;
    position: relative;
    overflow: hidden;
    display: grid;
    place-items: center;
    box-shadow: 0 20px 50px rgba(0,0,0,0.18);
}

.clk-hero-art:before {
    content: "";
    position: absolute;
    width: 520px;
    height: 520px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,.14);
    box-shadow: 0 0 0 70px rgba(255,255,255,.025), 0 0 0 140px rgba(255,255,255,.018);
}

.clk-hero-art:after {
    content: "";
    position: absolute;
    width: 190px;
    height: 190px;
    border-radius: 50%;
    background: radial-gradient(circle at 34% 28%, #fff 0 3%, #e8e8e8 4% 8%, #242424 9% 48%, #0b0b0b 49% 64%, #222 65% 70%, #ef2727 71% 73%, #111 74%);
    box-shadow: 0 24px 80px rgba(0,0,0,.45);
}

.clk-hero-mark {
    position: absolute;
    bottom: 26px;
    left: 28px;
    color: #fff;
    font-size: 12px;
    letter-spacing: .15em;
    text-transform: uppercase;
    font-weight: 700;
    z-index: 2;
}

.clk-hero-mark span {
    color: var(--clk-accent);
}

/* ── Four-Point Value Strip ── */
.clk-strip {
    border-top: 1px solid var(--clk-line);
    border-bottom: 1px solid var(--clk-line);
    padding: 18px 0;
    margin-top: 28px;
}

.clk-strip-row {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
    color: #666;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .12em;
}

.clk-strip-row b {
    color: #111;
}

/* ── Section Structure ── */
.clk-section {
    padding: 76px 0;
}

.clk-section-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 24px;
}

/* ── Category Swiper Carousel ── */
.clk-cat-slider-wrap {
    position: relative;
    width: 100%;
}

.clk-cat-swiper {
    width: 100%;
    padding: 8px 4px 16px;
    overflow: hidden;
}

.clk-cat {
    height: 240px;
    background: var(--clk-soft);
    border-radius: 22px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    position: relative;
    text-decoration: none !important;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    border: 1px solid transparent;
    user-select: none;
}

.clk-cat:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(0,0,0,0.06);
    border-color: #e5e5e0;
}

.clk-cat h3 {
    font-size: 21px;
    letter-spacing: -.03em;
    margin: 0;
    position: relative;
    z-index: 2;
    color: var(--clk-ink);
    font-weight: 800;
    line-height: 1.15;
}

.clk-cat p {
    font-size: 13px;
    color: var(--clk-muted);
    max-width: 170px;
    position: relative;
    z-index: 2;
    margin: 6px 0 0;
    line-height: 1.45;
}

.clk-cat .shape {
    position: absolute;
    right: -28px;
    bottom: -35px;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 26px solid #111;
    transition: transform 0.3s ease;
}

.clk-cat:hover .shape {
    transform: scale(1.06);
}

.clk-cat.shape-square .shape {
    border-radius: 30px;
    border-width: 18px;
    transform: rotate(25deg);
}
.clk-cat.shape-square:hover .shape {
    transform: rotate(32deg) scale(1.06);
}

.clk-cat.shape-disc .shape {
    border: 0;
    background: #111;
    width: 120px;
    height: 120px;
}

.clk-cat.shape-ring .shape {
    border-width: 14px;
    width: 125px;
    height: 125px;
}

.clk-cat.shape-aperture .shape {
    border-radius: 50%;
    border: 18px double var(--clk-accent);
    width: 135px;
    height: 135px;
}

/* Category Slider Controls */
.clk-slider-nav {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.clk-nav-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid var(--clk-line);
    background: #fff;
    color: var(--clk-ink);
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.clk-nav-btn:hover {
    background: var(--clk-ink);
    color: #fff;
    border-color: var(--clk-ink);
}

.clk-nav-btn:disabled,
.clk-nav-btn.swiper-button-disabled {
    opacity: 0.35;
    cursor: not-allowed;
    pointer-events: none;
}

.clk-cat-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    margin-top: 14px;
}

.clk-cat-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ddd;
    transition: all 0.25s ease;
    cursor: pointer;
}

.clk-cat-dot-active {
    width: 24px;
    border-radius: 999px;
    background: var(--clk-ink);
}

/* ── Product Grid ── */
.clk-products {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

@media (max-width: 1100px) {
    .clk-products {
        grid-template-columns: repeat(3, 1fr);
    }
}

.clk-product {
    border: 1px solid var(--clk-line);
    border-radius: 24px;
    overflow: hidden;
    background: #fff;
    transition: transform .25s ease, box-shadow .25s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
}

.clk-product:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 45px rgba(0,0,0,.08);
}

.clk-product-visual {
    height: 250px;
    background: #f5f5f3;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 16px;
    overflow: hidden;
}

.clk-product-visual img {
    max-width: 85%;
    max-height: 85%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.clk-product:hover .clk-product-visual img {
    transform: scale(1.05);
}

.clk-product-info {
    padding: 18px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
}

.clk-tag {
    font-size: 11px;
    color: var(--clk-accent);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    margin-bottom: 6px;
    display: block;
}

.clk-product h3 {
    font-size: 16px;
    margin: 4px 0 14px;
    font-weight: 700;
    color: var(--clk-ink);
    line-height: 1.35;
    height: 2.7em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.clk-product h3 a {
    color: inherit !important;
    text-decoration: none !important;
}

.clk-product h3 a:hover {
    color: var(--clk-accent) !important;
}

.clk-price {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 800;
    font-size: 15px;
    color: var(--clk-ink);
    margin-top: auto;
}

.clk-price-old {
    text-decoration: line-through;
    color: var(--clk-muted);
    font-size: 12px;
    font-weight: 500;
    margin-left: 6px;
}

.clk-add-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #111;
    color: #fff !important;
    display: grid;
    place-items: center;
    border: none;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.clk-add-btn:hover {
    background: var(--clk-accent);
    transform: scale(1.08);
}

.clk-add-btn.is-in-cart {
    background: #166534;
}

/* ── Dark Service Banner ── */
.clk-banner {
    background: #111;
    color: #fff;
    border-radius: 32px;
    padding: 56px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 30px;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.clk-banner:before {
    content: "";
    position: absolute;
    right: -100px;
    top: -100px;
    width: 380px;
    height: 380px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.06);
    pointer-events: none;
}

.clk-banner h2 {
    max-width: 720px;
    color: #fff;
    margin: 8px 0 12px;
    font-size: clamp(28px, 3.5vw, 44px);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.04em;
}

.clk-banner p {
    color: #bcbcbc;
    max-width: 650px;
    line-height: 1.6;
    margin: 0;
    font-size: 15px;
}

.clk-banner .clk-btn {
    border-color: #fff;
    color: #fff !important;
    background: transparent;
}

.clk-banner .clk-btn:hover {
    background: #fff;
    color: #111 !important;
    transform: translateY(-2px);
}

/* ── 3-Column Value Features ── */
.clk-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-top: 1px solid var(--clk-line);
    border-bottom: 1px solid var(--clk-line);
}

.clk-feature {
    padding: 34px 26px;
    border-right: 1px solid var(--clk-line);
}

.clk-feature:last-child {
    border-right: 0;
}

.clk-feature .num {
    font-size: 12px;
    color: var(--clk-accent);
    font-weight: 800;
    letter-spacing: 0.1em;
}

.clk-feature h3 {
    font-size: 20px;
    margin: 16px 0 8px;
    font-weight: 800;
    color: var(--clk-ink);
    letter-spacing: -0.02em;
}

.clk-feature p {
    font-size: 14px;
    color: var(--clk-muted);
    line-height: 1.6;
    margin: 0;
}

/* ── Mobile & Responsive ── */
@media (max-width: 980px) {
    .clk-hero-grid {
        grid-template-columns: 1fr;
    }
    .clk-hero-art {
        min-height: 380px;
    }
    .clk-products {
        grid-template-columns: repeat(2, 1fr);
    }
    .clk-banner {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 620px) {
    .clk-hero {
        padding-top: 36px;
    }
    .clk-hero-art {
        min-height: 280px;
    }
    .clk-products,
    .clk-features {
        grid-template-columns: 1fr;
    }
    .clk-feature {
        border-right: 0;
        border-bottom: 1px solid var(--clk-line);
    }
    .clk-feature:last-child {
        border-bottom: 0;
    }
    .clk-section {
        padding: 52px 0;
    }
    .clk-banner {
        padding: 34px 24px;
    }
    .clk-strip-row {
        justify-content: flex-start;
    }
    .clk-hero p {
        font-size: 16px;
    }
}
</style>
@endpush

@section('content')

{{-- =========================================================================
     1. CAMERALIK HERO SECTION
     ========================================================================= --}}
<section class="clk-hero">
    <div class="clk-wrap clk-hero-grid">
        <div>
            <div class="clk-eyebrow">
                <i></i> {{ __('Camera gear for creators') }}
            </div>
            <h1>
                {{ __('See it.') }}<br>
                <span style="color: var(--clk-accent);">{{ __('Capture it.') }}</span>
            </h1>
            <p>
                {{ __('Premium cameras, lenses and creative gear selected for photographers, filmmakers and creators who care about the shot.') }}
            </p>
            <div class="clk-cta">
                <a class="clk-btn primary" href="{{ route('shop.index') }}">
                    <span>{{ __('Explore gear') }}</span>
                    <span>↗</span>
                </a>
                <a class="clk-btn secondary" href="#categories">
                    <span>{{ __('Browse categories') }}</span>
                </a>
            </div>
        </div>

        {{-- Hero Visual Shutter Art --}}
        <div class="clk-hero-art">
            <div class="clk-hero-mark">
                cameralik <span>/</span> {{ __('create without limits') }}
            </div>
        </div>
    </div>

    {{-- Value Proposition Strip --}}
    <div class="clk-wrap clk-strip">
        <div class="clk-strip-row">
            <span><b>01</b> {{ __('Curated gear') }}</span>
            <span><b>02</b> {{ __('Expert advice') }}</span>
            <span><b>03</b> {{ __('Fast delivery') }}</span>
            <span><b>04</b> {{ __('Creator support') }}</span>
        </div>
    </div>
</section>

{{-- =========================================================================
     2. SHOP BY CATEGORY (Cameralik Smooth Swiper Slider)
     ========================================================================= --}}
<section class="clk-section" id="categories">
    <div class="clk-wrap">
        <div class="clk-section-head">
            <div>
                <div class="clk-kicker">{{ __('Shop by category') }}</div>
                <h2 class="clk-h2">{{ __('Everything behind') }}<br>{{ __('the perfect shot.') }}</h2>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="clk-slider-nav">
                    <button type="button" class="clk-nav-btn" id="clkCatPrev" aria-label="{{ __('Previous') }}" title="{{ __('Précédent') }}">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="clk-nav-btn" id="clkCatNext" aria-label="{{ __('Next') }}" title="{{ __('Suivant') }}">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <a class="clk-see d-none d-sm-inline-flex" href="{{ route('shop.index') }}">
                    <span>{{ __('View all') }}</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        {{-- Swiper Slider with all Active Categories --}}
        <div class="clk-cat-slider-wrap">
            <div class="swiper clk-cat-swiper">
                <div class="swiper-wrapper">
                    @php
                        // Filter categories (exclude general/test-cat if needed or show all valid ones)
                        $sliderCats = $allCategories->filter(function($c) {
                            return !in_array($c->slug, ['general', 'test-cat']);
                        });

                        // Array of shape classes to cycle through Cameralik styling
                        $shapeClasses = ['', 'shape-square', 'shape-disc', 'shape-ring', 'shape-aperture'];

                        // Helpful category micro-descriptions matching the cameralik vibe
                        $catDescriptions = [
                            'camera' => __('Mirrorless, full-frame & creator bodies.'),
                            'objectifs' => __('Prime, zoom and specialty glass.'),
                            'stabilisateurs' => __('Gimbals, rigs & cinema stabilization.'),
                            'lumieres-materiel-de-studio' => __('Continuous LED, flashes & softboxes.'),
                            'son' => __('Wireless mics, shotguns & 32-bit audio.'),
                            'sacs-de-camera' => __('Protective backpacks, hard cases & bags.'),
                            'trepieds' => __('Carbon fiber tripods & fluid heads.'),
                            'batterie-chargeur' => __('V-mounts, batteries & fast chargers.'),
                            'carte-memoire-lecteur' => __('High-speed CFexpress & SD cards.'),
                            'accessoires' => __('Cables, cages, filters and studio tools.'),
                            'materiel-de-podcast' => __('All-in-one broadcast & audio setups.'),
                            'filtres-nd-cpl-polarise' => __('Variable ND, polarizing & mist filters.'),
                            'matte-box' => __('Mini matte boxes & carbon fiber hoods.'),
                            'accessoires-insta360' => __('Action mounts, invisible poles & cases.'),
                            'kit-de-nettoyage' => __('Sensor cleaning kits, blowers & swabs.'),
                        ];
                    @endphp

                    @foreach($sliderCats as $idx => $cat)
                        @php
                            $shapeClass = $shapeClasses[$idx % count($shapeClasses)];
                            $catDesc = $catDescriptions[$cat->slug] ?? ($cat->description ?: __('Explore professional equipment.'));
                        @endphp
                        <div class="swiper-slide h-auto">
                            <a class="clk-cat {{ $shapeClass }}" href="{{ route('shop.index', ['category' => $cat->slug]) }}">
                                <div>
                                    <h3>{{ $cat->name }}</h3>
                                    <p>{{ \Illuminate\Support\Str::limit($catDesc, 65) }}</p>
                                </div>
                                <div class="shape"></div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Swiper Pagination Dots --}}
            <div class="clk-cat-pagination"></div>
        </div>
    </div>
</section>

{{-- =========================================================================
     3. FEATURED GEAR (Dynamic Products from Catalog)
     ========================================================================= --}}
<section class="clk-section" id="shop" style="padding-top: 10px;">
    <div class="clk-wrap">
        <div class="clk-section-head">
            <div>
                <div class="clk-kicker">{{ __('Featured gear') }}</div>
                <h2 class="clk-h2">{{ __('Built for the next frame.') }}</h2>
            </div>
            <a class="clk-see" href="{{ route('shop.index') }}">
                <span>{{ __('Shop all') }}</span>
                <span>→</span>
            </a>
        </div>

        <div class="clk-products">
            @foreach($featuredProducts->take(8) as $product)
                @php
                    $isInCart = isset(session('cart', [])[$product->id]);
                    $categoryName = $product->productCategory?->name ?? __('Creator essentials');
                @endphp
                <article class="clk-product">
                    <div class="clk-product-visual">
                        <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                            <img src="{{ $product->thumbnail }}" 
                                 alt="{{ $product->name }}" 
                                 loading="lazy" 
                                 onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        </a>
                    </div>
                    <div class="clk-product-info">
                        <div>
                            <span class="clk-tag">{{ $categoryName }}</span>
                            <h3>
                                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                        </div>

                        <div class="clk-price">
                            <div>
                                @if($product->isOnSale())
                                    <span>{{ $product->formatted_sale_price }}</span>
                                    <span class="clk-price-old">{{ $product->formatted_price }}</span>
                                @elseif($product->price && $product->price > 0)
                                    <span>{{ $product->formatted_price }}</span>
                                @else
                                    <span>{{ __('Sur devis') }}</span>
                                @endif
                            </div>

                            @if($product->isInStock())
                                <button type="button" 
                                        class="clk-add-btn {{ $isInCart ? 'is-in-cart' : '' }}" 
                                        onclick="addToCart({{ $product->id }}, 1, this)"
                                        data-product-id="{{ $product->id }}"
                                        aria-label="{{ $isInCart ? __('Dans le panier') : __('Ajouter au panier') }}"
                                        title="{{ $isInCart ? __('Dans le panier') : __('Ajouter au panier') }}">
                                    @if($isInCart)
                                        <i class="fas fa-check" style="font-size: 13px;"></i>
                                    @else
                                        <span>+</span>
                                    @endif
                                </button>
                            @else
                                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" 
                                   class="clk-add-btn" 
                                   style="background: #e2e8f0; color: #64748b !important;" 
                                   title="{{ __('Voir le produit') }}">
                                    <i class="fas fa-eye" style="font-size: 12px;"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- =========================================================================
     4. NEW ARRIVALS (Curated Stream)
     ========================================================================= --}}
@if(isset($newArrivalProducts) && $newArrivalProducts->count() > 0)
<section class="clk-section" style="padding-top: 10px;">
    <div class="clk-wrap">
        <div class="clk-section-head">
            <div>
                <div class="clk-kicker">{{ __('Fresh in studio') }}</div>
                <h2 class="clk-h2">{{ __('New Arrivals') }}</h2>
            </div>
            <a class="clk-see" href="{{ route('shop.index') }}">
                <span>{{ __('See all new') }}</span>
                <span>→</span>
            </a>
        </div>

        <div class="clk-products">
            @foreach($newArrivalProducts->take(4) as $product)
                @php
                    $isInCart = isset(session('cart', [])[$product->id]);
                    $categoryName = $product->productCategory?->name ?? __('New release');
                @endphp
                <article class="clk-product">
                    <div class="clk-product-visual">
                        <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                            <img src="{{ $product->thumbnail }}" 
                                 alt="{{ $product->name }}" 
                                 loading="lazy" 
                                 onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        </a>
                    </div>
                    <div class="clk-product-info">
                        <div>
                            <span class="clk-tag">{{ $categoryName }}</span>
                            <h3>
                                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                        </div>

                        <div class="clk-price">
                            <div>
                                @if($product->isOnSale())
                                    <span>{{ $product->formatted_sale_price }}</span>
                                    <span class="clk-price-old">{{ $product->formatted_price }}</span>
                                @elseif($product->price && $product->price > 0)
                                    <span>{{ $product->formatted_price }}</span>
                                @else
                                    <span>{{ __('Sur devis') }}</span>
                                @endif
                            </div>

                            @if($product->isInStock())
                                <button type="button" 
                                        class="clk-add-btn {{ $isInCart ? 'is-in-cart' : '' }}" 
                                        onclick="addToCart({{ $product->id }}, 1, this)"
                                        data-product-id="{{ $product->id }}"
                                        aria-label="{{ $isInCart ? __('Dans le panier') : __('Ajouter au panier') }}"
                                        title="{{ $isInCart ? __('Dans le panier') : __('Ajouter au panier') }}">
                                    @if($isInCart)
                                        <i class="fas fa-check" style="font-size: 13px;"></i>
                                    @else
                                        <span>+</span>
                                    @endif
                                </button>
                            @else
                                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" 
                                   class="clk-add-btn" 
                                   style="background: #e2e8f0; color: #64748b !important;" 
                                   title="{{ __('Voir le produit') }}">
                                    <i class="fas fa-eye" style="font-size: 12px;"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- =========================================================================
     5. SERVICES BANNER (Advice & Pro Workflow)
     ========================================================================= --}}
<section class="clk-section" id="services" style="padding-top: 10px;">
    <div class="clk-wrap">
        <div class="clk-banner">
            <div>
                <div class="clk-kicker" style="color: var(--clk-accent);">{{ __('More than a store') }}</div>
                <h2>{{ __('Get the gear. Get the right advice.') }}</h2>
                <p>
                    {{ __('Not sure what to buy? Our team helps you choose the right setup for your photography, video, studio or content workflow.') }}
                </p>
            </div>
            <a class="clk-btn" href="{{ route('contact') }}">
                <span>{{ __('Talk to an expert') }}</span>
                <span>↗</span>
            </a>
        </div>
    </div>
</section>

{{-- =========================================================================
     6. ABOUT / THREE CORE VALUES
     ========================================================================= --}}
<section class="clk-section" id="about" style="padding-top: 10px; padding-bottom: 60px;">
    <div class="clk-wrap">
        <div class="clk-features">
            <div class="clk-feature">
                <div class="num">01 / {{ __('TRUST') }}</div>
                <h3>{{ __('Curated, not crowded.') }}</h3>
                <p>{{ __('Focused selection of reliable gear instead of endless options.') }}</p>
            </div>
            <div class="clk-feature">
                <div class="num">02 / {{ __('SUPPORT') }}</div>
                <h3>{{ __('Advice that makes sense.') }}</h3>
                <p>{{ __('Real-world guidance for your exact camera and creative goals.') }}</p>
            </div>
            <div class="clk-feature">
                <div class="num">03 / {{ __('COMMUNITY') }}</div>
                <h3>{{ __('Made for creators.') }}</h3>
                <p>{{ __('A store built around people who shoot, edit, publish and create.') }}</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        const catSwiper = new Swiper('.clk-cat-swiper', {
            slidesPerView: 1.2,
            slidesPerGroup: 1,
            spaceBetween: 14,
            grabCursor: true,
            loop: false,
            speed: 550,
            navigation: {
                prevEl: '#clkCatPrev',
                nextEl: '#clkCatNext',
            },
            pagination: {
                el: '.clk-cat-pagination',
                clickable: true,
                bulletClass: 'clk-cat-dot',
                bulletActiveClass: 'clk-cat-dot-active',
            },
            breakpoints: {
                480: {
                    slidesPerView: 2,
                    slidesPerGroup: 2,
                    spaceBetween: 14,
                },
                768: {
                    slidesPerView: 3,
                    slidesPerGroup: 3,
                    spaceBetween: 16,
                },
                1024: {
                    slidesPerView: 4,
                    slidesPerGroup: 4,
                    spaceBetween: 18,
                }
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
