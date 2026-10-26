@extends('layouts.frontend')

@section('meta_title', setting('app_name', 'WINA SHOP') . ' — ' . __('Matériel Photo, Vidéo & Caméras au Maroc'))
@section('meta_description', __('Votre référence au Maroc pour le matériel photo & vidéo professionnel — Caméras Sony, Canon, drones & stabilisateurs DJI, optiques et éclairage studio.'))

@section('json_ld')
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "Où acheter du matériel photo, vidéo et caméras professionnelles au Maroc ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "WINA SHOP est le magasin de référence au Maroc pour l'équipement audiovisuel professionnel (Sony, Canon, Nikon, DJI, Godox, RØDE). Notre showroom est situé à Casablanca (Al Oulfa), avec livraison express 24h à 48h partout au Maroc et paiement à la livraison (Cash on Delivery)."
        }
      },
      {
        "@type": "Question",
        "name": "Comment obtenir un devis proforma INDH ou une facture avec ICE pour du matériel vidéo ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "WINA SHOP délivre immédiatement des devis proforma certifiés conformes pour les porteurs de projets INDH, coopératives et créateurs d'entreprises, ainsi que des factures officielles avec identifiant fiscal (ICE) pour toutes les sociétés et professionnels."
        }
      },
      {
        "@type": "Question",
        "name": "Les caméras, drones et objectifs vendus chez WINA SHOP sont-ils originaux et garantis ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "100% des produits neufs vendus chez WINA SHOP sont neufs sous emballage d'origine scellé, authentiques et accompagnés d'une garantie constructeur officielle avec SAV dédié."
        }
      },
      {
        "@type": "Question",
        "name": "Quels sont les délais et les frais de livraison au Maroc ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Nous livrons sous 24h à 48h dans toutes les villes du Maroc (Casablanca, Rabat, Marrakech, Tanger, Fès, Agadir, Oujda, Meknès, etc.). Le paiement s'effectue en espèces à la livraison après inspection de votre colis."
        }
      },
      {
        "@type": "Question",
        "name": "أين يمكنني شراء كاميرات ومعدات تصوير احترافية في المغرب بالضمان والدفع عند الاستلام؟",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "متجر WINA SHOP بالدار البيضاء هو خيارك الأول لشراء كاميرات سوني، كانون، نيكون، درون دي جي اي، ومعدات إضاءة غودوكس بالمغرب. نوفر ضماناً رسمياً، فواتير قانونية مع ICE، وفواتير شكلية INDH، مع التوصيل السريع والدفع نقداً عند الاستلام بكافة مدن المملكة."
        }
      }
    ]
  }
]
</script>
@endsection

@section('content')

{{-- =========================================================================
     1. HERO SECTION (DJI Osmo 360 + Osmo Pocket 4 + Godox AD800Pro)
     ========================================================================= --}}
<section class="hero-winashop-section py-3">
    <div class="container">
        <h1 class="visually-hidden">{{ setting('app_name', 'WINA SHOP') }} — {{ __('Matériel Photo, Vidéo, Caméras & Drones au Maroc') }}</h1>
        <div class="row g-3">
            {{-- Big Hero Banner Slider on Left --}}
            <div class="col-lg-8">
                <div class="hero-main-card position-relative overflow-hidden">
                    <div class="swiper hero-main-swiper">
                        <div class="swiper-wrapper">
                            @forelse($heroSlides as $slide)
                            <div class="swiper-slide hero-slide-item" style="background-color: #0b0f19;">
                                <div class="hero-main-bg" style="background-image: url('{{ $slide->image_url }}');"></div>
                                <div class="hero-main-overlay"></div>
                                <a href="{{ $slide->link ?: route('shop.index') }}" class="stretched-link" aria-label="{{ $slide->title }}"></a>
                                
                                @if($slide->badge)
                                <div class="hero-glass-badge" style="color: #ffffff !important;">
                                    <span class="hero-live-dot"></span>
                                    <span style="color: #ffffff !important;">{{ $slide->badge }}</span>
                                </div>
                                @endif

                                <div class="hero-slide-content">
                                    <h2 class="hero-slide-title" style="color: #ffffff !important; text-shadow: 0 2px 14px rgba(0, 0, 0, 0.9);">{{ $slide->title }}</h2>
                                    @if($slide->description)
                                    <p class="hero-slide-desc d-none d-md-block">{{ $slide->description }}</p>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="swiper-slide hero-slide-item" style="background-color: #0b0f19;">
                                <div class="hero-main-bg" style="background-image: url('{{ asset('images/camera/hero_winashop.jpg') }}');"></div>
                                <div class="hero-main-overlay"></div>
                                <a href="{{ route('shop.index', ['q' => 'DJI Osmo 360']) }}" class="stretched-link" aria-label="DJI Osmo 360 All in One"></a>
                                
                                <div class="hero-glass-badge" style="color: #ffffff !important;">
                                    <span class="hero-live-dot"></span>
                                    <span style="color: #ffffff !important;">NOUVEAUTÉ EXCLUSIVE • DJI OSMO 360</span>
                                </div>

                                <div class="hero-slide-content">
                                    <h2 class="hero-slide-title" style="color: #ffffff !important; text-shadow: 0 2px 14px rgba(0, 0, 0, 0.9);">DJI Osmo 360 All-In-One</h2>
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
                           class="cat-slide-item text-decoration-none d-flex flex-column align-items-center w-100 {{ !empty($cat['highlight']) ? 'cat-item-highlight' : '' }}">
                            
                            {{-- Category Card (Full Image Background) --}}
                            <div class="cat-item-card bg-white position-relative overflow-hidden w-100">
                                {{-- Category Image as Card Background --}}
                                <img src="{{ $cat['image'] ?? $cat['icon'] }}" 
                                     alt="{{ $cat['title'] }}" 
                                     width="120" 
                                     height="120" 
                                     class="cat-card-bg-img" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">

                                {{-- Item Count Badge at Top Right --}}
                                <span class="cat-badge-counter badge text-white fw-bold">
                                    {{ $cat['count'] }}
                                </span>
                            </div>

                            {{-- Category Name OUTSIDE the Card --}}
                            <div class="cat-caption-outside text-center w-100 mt-2">
                                <span class="cat-label text-uppercase text-center d-block">
                                    {{ $cat['title'] }}
                                </span>
                            </div>
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
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 home-products-grid">
                @foreach($featuredProducts->take(10) as $product)
                    <div class="col">
                        @include('frontend.partials.product_card_slider', ['product' => $product])
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

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 home-products-grid">
                @foreach($newArrivalProducts->take(6) as $product)
                    <div class="col">
                        @include('frontend.partials.product_card_slider', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Row 2: Coin Occasion (Moved under Nouvelle Arrivage - Full Row) --}}
        <div>
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: clamp(1.2rem, 2.5vw, 1.45rem); letter-spacing: -0.3px;">
                        Coin Occasion (Reconditionné & Certifié)
                    </h2>
                </div>
                <a href="{{ route('shop.index', ['category' => 'occasion']) }}" class="text-secondary text-decoration-none small fw-semibold hover-red">
                    <span>Voir tout</span>
                    <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                </a>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 home-products-grid">
                @foreach($occasionProducts as $product)
                    <div class="col">
                        @include('frontend.partials.product_card_slider', ['product' => $product])
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
<section class="state-funding-section">
    <div class="container">
        <div class="state-funding-wrapper">

            {{-- 1. Hero Block --}}
            <div class="row align-items-center g-4 mb-4 mb-lg-5 pb-3 pb-lg-4 border-bottom">
                <div class="col-12 col-lg-6">
                    <div class="indh-hero-badge mb-3">
                        <i class="fas fa-landmark text-danger" style="font-size: 0.75rem;"></i>
                        <span class="indh-hero-badge-text">{{ __('INDH & PROJETS SOUTENUS') }}</span>
                    </div>

                    <h2 class="indh-hero-title mb-3">
                        {{ __('Bénéficiaires de') }}<br>
                        <span class="text-danger">{{ __('Financements Publics') }}</span>
                    </h2>

                    <p class="indh-hero-sub mb-0">
                        {{ __('Composez votre panier de matériel photo/vidéo, validez sans paiement et recevez votre devis officiel cacheté avec ICE et TVA 20%.') }}
                    </p>
                </div>

                <div class="col-12 col-lg-6 text-center text-lg-end">
                    <img src="{{ asset('images/indh/hero_gear.jpg') }}" 
                         alt="{{ __('Équipements photo vidéo cinéma pro subventionnés') }}" 
                         class="indh-hero-gear-img img-fluid">
                </div>
            </div>

            {{-- 2. Three Connected Step Cards --}}
            <div class="row g-3 g-xl-4 mb-4 align-items-stretch position-relative">
                {{-- Step 1 --}}
                <div class="col-12 col-lg-4 position-relative">
                    <div class="indh-step-card-pro p-0">
                        <div class="row g-0 h-100">
                            <div class="col-7 p-3 p-xl-3.5 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="indh-step-badge-num">01</div>
                                    </div>
                                    <h3 class="indh-step-title mb-1.5">
                                        {{ __('Composez votre panier') }}
                                    </h3>
                                    <p class="indh-step-desc mb-2">
                                        {{ __('Choisissez votre matériel photo/vidéo parmi notre large sélection de marques et d\'équipements professionnels.') }}
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <a href="{{ route('shop.index') }}" class="indh-step-btn-pro text-decoration-none">
                                        <i class="fas fa-cart-shopping indh-btn-icon"></i>
                                        <span>{{ __('Découvrir le matériel') }}</span>
                                        <i class="fas fa-chevron-right indh-btn-arrow"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-5 indh-step-img-col">
                                <img src="{{ asset('images/indh/step1_laptop.jpg') }}" alt="{{ __('Composez votre panier') }}" class="indh-step-card-img">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="col-12 col-lg-4 position-relative">
                    <div class="indh-step-card-pro p-0">
                        <div class="row g-0 h-100">
                            <div class="col-7 p-3 p-xl-3.5 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="indh-step-badge-num">02</div>
                                    </div>
                                    <h3 class="indh-step-title mb-1.5">
                                        {{ __('Validez sans paiement') }}
                                    </h3>
                                    <p class="indh-step-desc mb-2">
                                        {{ __('Passez votre commande sans effectuer de paiement en ligne. Simple et rapide.') }}
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <a href="{{ route('cart.index') }}" class="indh-step-btn-pro text-decoration-none">
                                        <i class="fas fa-file-invoice indh-btn-icon"></i>
                                        <span>{{ __('Valider ma commande') }}</span>
                                        <i class="fas fa-chevron-right indh-btn-arrow"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-5 indh-step-img-col">
                                <img src="{{ asset('images/indh/step2_phone.jpg') }}" alt="{{ __('Validez sans paiement') }}" class="indh-step-card-img">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="col-12 col-lg-4 position-relative">
                    <div class="indh-step-card-pro p-0">
                        <div class="row g-0 h-100">
                            <div class="col-7 p-3 p-xl-3.5 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="indh-step-badge-num">03</div>
                                    </div>
                                    <h3 class="indh-step-title mb-1.5">
                                        {{ __('Recevez votre devis cacheté') }}
                                    </h3>
                                    <p class="indh-step-desc mb-2">
                                        {{ __('Recevez par WhatsApp votre devis officiel cacheté avec ICE et TVA 20%, sous 2h.') }}
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <a href="https://wa.me/212629035777?text={{ urlencode('Bonjour, j\'ai sélectionné mon matériel pour mon projet INDH / Financement Public. Je souhaite recevoir mon devis officiel cacheté.') }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="indh-step-btn-pro btn-whatsapp-step text-decoration-none">
                                        <i class="fab fa-whatsapp indh-btn-icon"></i>
                                        <span>{{ __('Recevoir mon devis') }}</span>
                                        <i class="fas fa-chevron-right indh-btn-arrow"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-5 indh-step-img-col">
                                <img src="{{ asset('images/indh/step3_devis.jpg') }}" alt="{{ __('Recevez votre devis cacheté') }}" class="indh-step-card-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Dark Bottom Banner --}}
            <div class="indh-banner-cta">
                <div class="row align-items-center g-3 g-lg-4">
                    <div class="col-12 col-lg-7">
                        <span class="indh-banner-badge d-inline-block mb-2">{{ __('DEMANDE URGENTE ?') }}</span>
                        <h3 class="indh-banner-title mb-2">
                            {{ __('Besoin d\'un devis urgent pour votre dossier de subvention ?') }}
                        </h3>
                        <p class="indh-banner-desc mb-0">
                            {{ __('Notre équipe est à votre disposition pour vous accompagner et vous envoyer votre devis sous 2h.') }}
                        </p>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="indh-banner-cta-actions">
                            <a href="{{ route('shop.index') }}" class="indh-btn-choose w-100 w-sm-auto">
                                <i class="fas fa-camera" style="font-size: 0.85rem;"></i>
                                <span>{{ __('Choisir mon matériel') }}</span>
                                <i class="fas fa-chevron-right" style="font-size: 0.65rem;"></i>
                            </a>
                            <a href="https://wa.me/212629035777?text={{ urlencode('Bonjour, je suis bénéficiaire d\'un financement (INDH / Programme État) et je souhaite obtenir un devis proforma pour mon projet.') }}" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="indh-btn-devis w-100 w-sm-auto">
                                <i class="fab fa-whatsapp text-success" style="font-size: 1.05rem;"></i>
                                <span>{{ __('Demander mon devis') }}</span>
                                <i class="fas fa-chevron-right" style="font-size: 0.65rem;"></i>
                            </a>
                        </div>
                    </div>
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
                        <span class="d-block fw-bold text-dark mb-1" style="font-size: 0.95rem;">Livraison Express 24-48h</span>
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
                        <span class="d-block fw-bold text-dark mb-1" style="font-size: 0.95rem;">100% Neuf & Scellé</span>
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
                        <span class="d-block fw-bold text-dark mb-1" style="font-size: 0.95rem;">Conseil Technique Pro</span>
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
                        <span class="d-block fw-bold text-dark mb-1" style="font-size: 0.95rem;">Paiement à la Livraison</span>
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
                    <h2 class="fw-bold text-dark mb-1" style="font-size: clamp(1.2rem, 2.8vw, 1.45rem); letter-spacing: -0.3px;">
                        La Confiance des Professionnels de l'Image
                    </h2>
                    <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                        Découvrez les retours d'expérience de nos clients photographes, vidéastes et créateurs au Maroc.
                    </p>
                </div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 bg-light rounded-pill border flex-shrink-0 text-nowrap">
                    <div class="d-inline-flex align-items-center gap-1 text-warning stars-row text-nowrap">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <span class="fw-bold text-dark text-nowrap" style="font-size: 0.95rem;">4.9 / 5</span>
                    <span class="text-muted small text-nowrap d-none d-sm-inline">(Plus de 200 avis vérifiés)</span>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-3">
                <div class="col">
                    <div class="client-review-box">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-inline-flex align-items-center gap-1 text-warning stars-row text-nowrap">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <span class="badge px-2 py-1" style="background: #dcfce7; color: #166534; border-radius: 999px; font-size: 0.68rem; font-weight: 600;">
                                    <i class="fas fa-check-circle me-1"></i> Achat Vérifié
                                </span>
                            </div>
                            <p class="client-review-text mb-0" style="font-size: 0.85rem; line-height: 1.55; color: #334155;">
                                "Livraison en 24h chrono à Marrakech pour ma Sony FX3A. Matériel impeccable, scellé d'origine. Les conseillers Wina Shop m'ont même guidé pour le choix de la poignée XLR."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
                            <div class="client-avatar-initials text-white" style="background: #0f172a; color: #ffffff !important;">YB</div>
                            <div>
                                <h3 class="fw-bold text-dark mb-0 fs-6">Youssef B.</h3>
                                <span class="text-muted small" style="font-size: 0.74rem;">Réalisateur & Cadreur • Marrakech</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="client-review-box">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-inline-flex align-items-center gap-1 text-warning stars-row text-nowrap">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <span class="badge px-2 py-1" style="background: #dcfce7; color: #166534; border-radius: 999px; font-size: 0.68rem; font-weight: 600;">
                                    <i class="fas fa-check-circle me-1"></i> Achat Vérifié
                                </span>
                            </div>
                            <p class="client-review-text mb-0" style="font-size: 0.85rem; line-height: 1.55; color: #334155;">
                                "Pack DJI Mic 2 reçu à Casablanca le jour même. Une réactivité exemplaire au Maroc pour du matériel de son professionnel. Très satisfaite du service !"
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
                            <div class="client-avatar-initials text-white" style="background: #991b1b; color: #ffffff !important;">SM</div>
                            <div>
                                <h3 class="fw-bold text-dark mb-0 fs-6">Sara M.</h3>
                                <span class="text-muted small" style="font-size: 0.74rem;">Créatrice de Contenu • Casablanca</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="client-review-box">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-inline-flex align-items-center gap-1 text-warning stars-row text-nowrap">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <span class="badge px-2 py-1" style="background: #dcfce7; color: #166534; border-radius: 999px; font-size: 0.68rem; font-weight: 600;">
                                    <i class="fas fa-check-circle me-1"></i> Achat Vérifié
                                </span>
                            </div>
                            <p class="client-review-text mb-0" style="font-size: 0.85rem; line-height: 1.55; color: #334155;">
                                "Le plus grand choix de flashs Godox et d'accessoires SmallRig avec des tarifs justes et la disponibilité immédiate. Le support technique est toujours au rendez-vous."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
                            <div class="client-avatar-initials text-white" style="background: #0369a1; color: #ffffff !important;">KT</div>
                            <div>
                                <h3 class="fw-bold text-dark mb-0 fs-6">Karim T.</h3>
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
                    <h2 class="fw-bold text-white mb-2" style="font-size: clamp(1.3rem, 3vw, 1.75rem); letter-spacing: -0.4px;">
                        Rejoignez le Cercle Privilège Wina Shop
                    </h2>
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
        const heroSlideCount = document.querySelectorAll('.hero-main-swiper .swiper-slide').length;
        const heroSwiper = new Swiper('.hero-main-swiper', {
            slidesPerView: 1,
            loop: heroSlideCount > 1,
            speed: 750,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            autoplay: heroSlideCount > 1 ? {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            } : false,
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
            loop: true,
            loopAddBlankSlides: true,
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
