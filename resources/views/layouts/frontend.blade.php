<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <title>@yield('meta_title', setting('app_name', 'Moubdi3oun'))</title>
    
    <!-- Multi-Platform Tracking -->
    @include('frontend.partials.tracking')
    <meta name="description" content="@yield('meta_description', setting('app_description', 'Moubdi3oun | Handcrafted Home Furniture. Menuiserie, Tapisserie, et Métallurgie de luxe.'))">
    <meta name="keywords" content="@yield('meta_keywords', setting('app_name', 'Moubdi3oun') . ', mobilier, sur mesure, artisanat, ameublement, Maroc, luxe')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="author" content="{{ setting('app_name', 'Moubdi3oun') }}">
    <meta name="theme-color" content="#e94560">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Hreflang for Multi-language SEO (EN, FR, AR) -->
    <link rel="alternate" hreflang="en" href="{{ url()->current() . (strpos(url()->current(), '?') !== false ? '&' : '?') . 'hl=en' }}">
    <link rel="alternate" hreflang="fr" href="{{ url()->current() . (strpos(url()->current(), '?') !== false ? '&' : '?') . 'hl=fr' }}">
    <link rel="alternate" hreflang="ar" href="{{ url()->current() . (strpos(url()->current(), '?') !== false ? '&' : '?') . 'hl=ar' }}">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

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
    <meta property="og:site_name" content="{{ setting('app_name', 'Moubdi3oun') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', setting('app_name', 'Moubdi3oun'))">
    <meta property="og:description" content="@yield('meta_description', setting('app_description', 'Moubdi3oun | Handcrafted Home Furniture. Menuiserie, Tapisserie, et Métallurgie de luxe.'))">
    <meta property="og:image" content="@yield('meta_image', setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/og-default.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ app()->getLocale() == 'ar' ? 'ar_MA' : (app()->getLocale() == 'fr' ? 'fr_MA' : 'en_US') }}">
    <meta property="og:updated_time" content="{{ now()->toIso8601String() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('meta_title', setting('app_name', 'Moubdi3oun'))">
    <meta name="twitter:description" content="@yield('meta_description', setting('app_description', 'Moubdi3oun | Handcrafted Home Furniture.'))">
    <meta name="twitter:image" content="@yield('meta_image', setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/og-default.jpg'))">    
    <meta name="twitter:site" content="@yield('twitter_site', '@' . str_replace(' ', '', setting('app_name', 'Moubdi3oun')))">

    <!-- Organization Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "{{ setting('app_name', 'Moubdi3oun') }}",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('storage/' . setting('app_logo')) }}",
      "sameAs": [
        "{{ setting('social_facebook', '#') }}",
        "{{ setting('social_instagram', '#') }}",
        "{{ setting('social_whatsapp', '#') }}"
      ],
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{{ setting('contact_phone', '') }}",
        "contactType": "customer service"
      }
    }
    </script>
    
    @yield('json_ld')

    <!-- commerce tags for catalog ads -->
    @yield('commerce_meta')
    
    <!-- JSON-LD Structured Data Schema -->
    @yield('json_ld')
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
        <style>
            body, h1, h2, h3, h4, h5, h6, .nav-link, .btn, .pcard-name { font-family: 'Cairo', sans-serif !important; }
        </style>
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    <!-- 1. ANNOUNCEMENT BAR (MARQUEE) -->
    <div class="announcement-bar">
        <div class="promo-marquee">
            <div class="promo-item">✨ Livraison gratuite partout au Maroc dès 5000 DH</div>
            <div class="promo-item">🎨 Création sur-mesure : Bois, Métal & Tapisserie</div>
            <div class="promo-item">⏳ -10% sur votre première commande avec le code MOUBDI10</div>
            <!-- Duplicate for seamless loop -->
            <div class="promo-item">✨ Livraison gratuite partout au Maroc dès 5000 DH</div>
            <div class="promo-item">🎨 Création sur-mesure : Bois, Métal & Tapisserie</div>
            <div class="promo-item">⏳ -10% sur votre première commande avec le code MOUBDI10</div>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @if(setting('social_whatsapp'))
    <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('social_whatsapp')) }}" 
       class="whatsapp-float shadow" 
       target="_blank" 
       title="{{ __('Chat with us on WhatsApp') }}">
        <i class="fab fa-whatsapp"></i>
    </a>
    <style>
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            color: white;
            background-color: #128c7e;
        }
        [dir="rtl"] .whatsapp-float {
            right: auto;
            left: 40px;
        }
        @media screen and (max-width: 767px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
                font-size: 25px;
            }
            [dir="rtl"] .whatsapp-float {
                right: auto;
                left: 20px;
            }
        }
    </style>
    @endif

    <!-- Main Header -->
    <div class="header-main">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <div class="container-fluid px-0">
                    <!-- Logo -->
                    <a class="navbar-brand me-3 me-lg-5" href="{{ url('/') }}">
                        <h3 class="m-0 fw-black text-uppercase" style="font-family: 'Inter', sans-serif; letter-spacing: -1.5px; color: var(--accent);">
                            Moubdi3<span style="color: var(--text-dark);">{{ app()->getLocale() == 'ar' ? 'ون' : 'oun' }}</span>
                        </h3>
                    </a>

                    <!-- Mobile Toggler -->
                    <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Collapsible section -->
                    <div class="collapse navbar-collapse" id="navbarMain">
                        <ul class="navbar-nav ms-auto mb-0 gap-4">
                            <li class="nav-item">
                                <a class="nav-link fw-bold text-uppercase small ls-1" href="{{ url('/') }}">{{ __('Home') }}</a>
                            </li>
                            <!-- MEGA MENU TRIGGER -->
                            <li class="nav-item has-megamenu">
                                <a class="nav-link fw-bold text-uppercase small ls-1 d-flex align-items-center" href="{{ route('shop.index') }}">
                                    {{ __('Shop') }} <i class="fas fa-chevron-down ms-1" style="font-size: 0.6rem;"></i>
                                </a>
                                <div class="megamenu">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <span class="megamenu-title">Salon & Séjour</span>
                                                <a href="{{ route('shop.index', ['category' => 'salons']) }}" class="megamenu-link">Canapés sur mesure</a>
                                                <a href="{{ route('shop.index', ['category' => 'tables']) }}" class="megamenu-link">Tables basses</a>
                                                <a href="{{ route('shop.index', ['category' => 'meubles-tv']) }}" class="megamenu-link">Meubles TV</a>
                                                <a href="{{ route('shop.index', ['category' => 'fauteuils']) }}" class="megamenu-link">Fauteuils</a>
                                            </div>
                                            <div class="col-lg-3">
                                                <span class="megamenu-title">Chambre à Coucher</span>
                                                <a href="{{ route('shop.index', ['category' => 'lits']) }}" class="megamenu-link">Lits design</a>
                                                <a href="{{ route('shop.index', ['category' => 'armoires']) }}" class="megamenu-link">Dressings & Armoires</a>
                                                <a href="{{ route('shop.index', ['category' => 'chevets']) }}" class="megamenu-link">Tables de chevet</a>
                                                <a href="{{ route('shop.index', ['category' => 'commodes']) }}" class="megamenu-link">Commodes</a>
                                            </div>
                                            <div class="col-lg-3">
                                                <span class="megamenu-title">L'Atelier Moubdi3oun</span>
                                                <a href="{{ url('/about') }}" class="megamenu-link">Notre Savoir-faire</a>
                                                <a href="{{ url('/process') }}" class="megamenu-link">Le Processus Créatif</a>
                                                <a href="{{ url('/materials') }}" class="megamenu-link">Nos Matériaux</a>
                                                <a href="{{ url('/contact') }}" class="megamenu-link">Demande de Devis</a>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="megamenu-featured-card">
                                                    <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b48ec?auto=format&fit=crop&q=80" alt="New Collection">
                                                    <div class="megamenu-featured-content">
                                                        <span class="badge-new mb-2 d-inline-block">Nouveau</span>
                                                        <h6 class="fw-black text-uppercase ls-1">Collection Atlas 2024</h6>
                                                        <a href="{{ route('shop.index') }}" class="btn btn-sm btn-outline-light rounded-0 text-uppercase fw-bold" style="font-size: 0.6rem;">Découvrir</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold text-uppercase small ls-1" href="{{ url('/about') }}">{{ __('About') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold text-uppercase small ls-1" href="{{ url('/portfolio') }}">{{ __('Portfolio') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold text-uppercase small ls-1" href="{{ url('/contact') }}">{{ __('Contact') }}</a>
                            </li>
                        </ul>
                        
                        <div class="d-flex align-items-center gap-3 gap-lg-4 ms-lg-5 mt-3 mt-lg-0">
                            <!-- LANGUAGE SWITCHER -->
                            <div class="dropdown locale-dropdown">
                                <button class="btn btn-link text-dark p-0 fw-bold small text-uppercase ls-1 dropdown-toggle text-decoration-none" type="button" data-bs-toggle="dropdown">
                                    {{ app()->getLocale() }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                    <li><a class="dropdown-item small fw-bold" href="{{ route('locale.set', 'fr') }}">FR - Français</a></li>
                                    <li><a class="dropdown-item small fw-bold" href="{{ route('locale.set', 'en') }}">EN - English</a></li>
                                    <li><a class="dropdown-item small fw-bold" href="{{ route('locale.set', 'ar') }}">AR - العربية</a></li>
                                </ul>
                            </div>

                            <button class="btn btn-link text-dark p-0 search-trigger">
                                <i class="fas fa-search fs-5"></i>
                            </button>
                             <div class="position-relative">
                                <button class="btn btn-link text-dark p-0 position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart">
                                    <i class="fas fa-shopping-bag fs-5"></i>
                                    <span id="header-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.55rem; padding: 0.35em 0.5em;">
                                        {{ count(session('cart', [])) }}
                                    </span>
                                </button>
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

    <!-- Offcanvas Mini Cart -->
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="miniCart" aria-labelledby="miniCartLabel" style="width: 450px; background: #fff;">
        <div class="offcanvas-header border-bottom py-3">
            <h5 class="offcanvas-title fw-black text-uppercase ls-1" id="miniCartLabel" style="font-size: 1.1rem;">
                <i class="fas fa-shopping-bag me-2" style="color: var(--accent);"></i>{{ __('Cart') }}
            </h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column h-100">
            <div class="flex-grow-1 overflow-auto p-4" id="mini-cart-items">
                @php $total = 0; @endphp
                @forelse(session('cart', []) as $id => $details)
                    @php $total += $details['price'] * $details['quantity']; @endphp
                    <div class="cart-item bg-white p-3 rounded-4 shadow-sm mb-3 position-relative border border-light" id="cart-item-{{ $id }}">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3 position-relative">
                                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover" style="width: 80px; height: 80px;">
                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-light text-dark border shadow-sm" style="font-size: 0.7rem;">x{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold mb-1 text-truncate pe-4" title="{{ $details['name'] }}">{{ $details['name'] }}</h6>
                                <p class="mb-2 text-muted small">{{ $details['category_name'] ?? 'Produit' }}</p>
                                
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <span class="fw-black" style="font-size: 1.1rem; color: var(--accent);">{{ currency($details['price']) }}</span>
                                    
                                    <div class="qty-selector p-1" style="transform: scale(0.85); transform-origin: right;">
                                        <button class="btn btn-link link-dark p-1 text-decoration-none border-0" onclick="updateQty({{ $id }}, {{ $details['quantity'] - 1 }})">
                                            <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="{{ $details['quantity'] }}" readonly style="width: 25px;">
                                        <button class="btn btn-link link-dark p-1 text-decoration-none border-0" onclick="updateQty({{ $id }}, {{ $details['quantity'] + 1 }})">
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
                        <div class="mb-4 bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-shopping-bag fa-3x text-muted opacity-25"></i>
                        </div>
                        <h5 class="fw-black text-uppercase ls-1">Panier vide</h5>
                        <p class="text-muted small mb-4">Votre sélection est actuellement vide.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-4 btn-sm text-uppercase fw-bold">Découvrir</a>
                    </div>
                @endforelse
            </div>
            
            @if(count(session('cart', [])) > 0)
            <div class="border-top p-4 bg-white mt-auto shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-muted small text-uppercase fw-black ls-1">TOTAL</span>
                    <span class="h4 fw-black text-dark mb-0" style="color: var(--accent) !important;">{{ currency($total) }}</span>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-dark py-3 rounded-pill fw-black shadow-sm d-flex justify-content-between align-items-center px-4 text-uppercase ls-1">
                        <span>Commander</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="btn btn-link py-2 fw-bold text-muted small text-decoration-none">
                        Vérifier le panier
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <footer class="footer-modern">
        <div class="container">
            <div class="row g-5">
                <!-- Brand & Social -->
                <div class="col-lg-4">
                    <img src="{{ setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/logo.png') }}" alt="{{ setting('app_name') }}" class="footer-logo">
                    <p class="small lh-lg mb-4 opacity-75">
                        Moubdi3oun incarne l'excellence de l'artisanat marocain. Nous transformons les matériaux les plus nobles en pièces uniques pour sublimer votre intérieur. Un héritage de passion et de précision.
                    </p>
                    @php
                        $sfb  = setting('social_facebook',  '');
                        $stw  = setting('social_twitter',   '');
                        $sig  = setting('social_instagram', '');
                        $sli  = setting('social_linkedin',  '');
                        $swa  = setting('social_whatsapp',  '');
                        $validUrl = fn($v) => $v && $v !== '#' && $v !== '/#';
                    @endphp
                    <div class="d-flex gap-2">
                        @if($validUrl($sfb)) <a href="{{ $sfb }}" target="_blank" class="footer-social-btn"><i class="fab fa-facebook-f"></i></a> @endif
                        @if($validUrl($sig)) <a href="{{ $sig }}" target="_blank" class="footer-social-btn"><i class="fab fa-instagram"></i></a> @endif
                        @if($validUrl($stw)) <a href="{{ $stw }}" target="_blank" class="footer-social-btn"><i class="fab fa-twitter"></i></a> @endif
                        @if($validUrl($swa)) <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $swa) }}" target="_blank" class="footer-social-btn"><i class="fab fa-whatsapp"></i></a> @endif
                    </div>
                </div>

                <!-- Maison & Ateliers -->
                <div class="col-lg-2 col-6">
                    <h6 class="footer-column-title">Maison</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/about') }}" class="footer-link">L'Esprit</a></li>
                        <li><a href="{{ url('/portfolio') }}" class="footer-link">Réalisations</a></li>
                        <li><a href="{{ url('/contact') }}" class="footer-link">L'Atelier</a></li>
                        <li><a href="{{ route('shop.index') }}" class="footer-link">Collections</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="col-lg-2 col-6">
                    <h6 class="footer-column-title">Assistance</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Suivre ma commande</a></li>
                        <li><a href="#" class="footer-link">FAQs</a></li>
                        <li><a href="#" class="footer-link">Livraison & Pose</a></li>
                        <li><a href="#" class="footer-link">Garantie</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-lg-4">
                    <h6 class="footer-column-title">Contact</h6>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="small">
                            {{ setting('company_address', 'Casablanca, Maroc') }}<br>
                            <span class="opacity-50">Siège social & Showroom</span>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="small">
                            {{ setting('company_phone', '+212 6XX XX XX XX') }}<br>
                            <span class="opacity-50">Lun-Sam: 09h00 - 19h00</span>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon"><i class="fas fa-envelope"></i></div>
                        <div class="small">
                            {{ setting('company_email', 'contact@moubdi3oun.com') }}<br>
                            <span class="opacity-50">Support Client & Devis</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <p class="small mb-0 opacity-50">&copy; {{ date('Y') }} {{ setting('app_name', 'Moubdi3oun') }}. Tous droits réservés.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex align-items-center justify-content-md-end gap-3 opacity-50">
                            <i class="fab fa-cc-visa fs-4"></i>
                            <i class="fab fa-cc-mastercard fs-4"></i>
                            <i class="fab fa-cc-apple-pay fs-4"></i>
                            <span class="small fw-bold border-start ps-3 border-secondary">PAIEMENT SÉCURISÉ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
// Shop UI Enhancements
document.addEventListener('DOMContentLoaded', function() {
    const searchTriggers = document.querySelectorAll('.search-trigger');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose = document.querySelector('.search-close');
    
    searchTriggers.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            searchOverlay.classList.add('active');
            setTimeout(() => searchOverlay.querySelector('input').focus(), 300);
        });
    });
    
    searchClose.addEventListener('click', () => {
        searchOverlay.classList.remove('active');
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape' && searchOverlay.classList.contains('active')) {
            searchOverlay.classList.remove('active');
        }
    });
});
</script>
<script>
        @if(setting('frontend_enable_animations'))
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        @endif

        // Mini Cart Functions
        function updateQty(id, qty) {
            if(qty < 1) {
                removeItem(id);
                return;
            }
            
            fetch('{{ route('cart.update') }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id, quantity: qty })
            })
            .then(response => response.json())
            .then(data => {
                // Update both desktop and mobile cart count badges
                const updateCartBadges = (count) => {
                    ['header-cart-count', 'header-cart-count-mobile'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el && count !== undefined) el.textContent = count;
                    });
                };
                updateCartBadges(data.cartCount);
                // Refresh mini-cart content
                refreshMiniCart();
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Erreur lors de la mise à jour du panier',
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
                confirmButtonText: 'Oui, supprimer !'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('cart.remove') }}', {
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
                        ['header-cart-count', 'header-cart-count-mobile'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el && data.cartCount !== undefined) el.textContent = data.cartCount;
                        });
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
            fetch('{{ route('cart.mini') }}', {
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
                    fetch('{{ route('cart.miniFooter') }}', {
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

        // Global Variation Selection for Product Cards
        function selectCardVariant(productId, type, value, el, isSlider = false) {
            const cardId = isSlider ? `slider-${productId}` : productId;
            const cardEl = el.closest(isSlider ? '.product-card' : '.pcard');
            
            // Initialize storage if needed
            if (!window.selectedCardOptions) window.selectedCardOptions = {};
            if (!window.selectedCardOptions[cardId]) window.selectedCardOptions[cardId] = {};
            const selection = window.selectedCardOptions[cardId];

            // Update active class for the specific row
            const row = el.closest('.pcard-variant-row');
            if (el.classList.contains('active')) {
                el.classList.remove('active');
                selection[type] = null;
            } else {
                const siblings = row.querySelectorAll(el.classList.contains('pcard-color-dot') ? '.pcard-color-dot' : '.pcard-size-pill');
                siblings.forEach(s => s.classList.remove('active'));
                el.classList.add('active');
                selection[type] = value;
            }

            // Find matching variant
            const variants = window.cardVariants[productId];
            
            // Check if both are needed
            const hasColor = cardEl.querySelector('.pcard-color-dot');
            const hasSize = cardEl.querySelector('.pcard-size-pill');
            
            let match = null;
            if (hasColor && hasSize) {
                if (selection.color && selection.size) {
                    match = variants.find(v => v.color == selection.color && v.size == selection.size);
                }
            } else if (hasColor) {
                if (selection.color) {
                    match = variants.find(v => v.color == selection.color);
                }
            } else if (hasSize) {
                if (selection.size) {
                    match = variants.find(v => v.size == selection.size);
                }
            }

            // Update UI based on match or lack thereof
            const priceEl = document.getElementById(isSlider ? `pcard-price-slider-${productId}` : `pcard-price-${productId}`);
            const inputId = isSlider ? `card-selected-variant-slider-${productId}` : `card-selected-variant-${productId}`;
            const input = document.getElementById(inputId);

            if (match) {
                if (priceEl) priceEl.innerText = match.formatted_price;
                if (input) input.value = match.id;
            } else {
                // If no match or partially selected, revert price to default if we can find it
                // For simplicity, we keep the price or reset if the product price is in data
                if (input) input.value = "";
            }

            // Update availability of other pills in this card
            updateCardAvailability(productId, cardEl, selection);
        }

        function updateCardAvailability(productId, cardEl, selection) {
            const variants = window.cardVariants[productId];
            if (!variants) return;

            // Update Size Pills
            cardEl.querySelectorAll('.pcard-size-pill').forEach(pill => {
                const size = pill.innerText.trim();
                let isAvailable = false;
                if (selection.color) {
                    isAvailable = variants.some(v => v.color == selection.color && v.size == size && v.stock > 0);
                } else {
                    isAvailable = variants.some(v => v.size == size && v.stock > 0);
                }
                pill.classList.toggle('disabled', !isAvailable);
            });

            // Update Color Dots
            cardEl.querySelectorAll('.pcard-color-dot').forEach(dot => {
                const color = dot.title;
                let isAvailable = false;
                if (selection.size) {
                    isAvailable = variants.some(v => v.size == selection.size && v.color == color && v.stock > 0);
                } else {
                    isAvailable = variants.some(v => v.color == color && v.stock > 0);
                }
                dot.classList.toggle('disabled', !isAvailable);
            });
        }

        // Initialize all cards availability on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.pcard, .product-card, .product-card-v2').forEach(cardEl => {
                const productIdMatch = cardEl.innerHTML.match(/window\.cardVariants\[(\d+)\]/);
                if (productIdMatch) {
                    const productId = productIdMatch[1];
                    updateCardAvailability(productId, cardEl, {});
                }
            });
        });

        // Global Add to Cart with Variant Support
        function addToCart(productId, isSlider = false) {
            const cardId = isSlider ? `slider-${productId}` : productId;
            const inputId = isSlider ? `card-selected-variant-slider-${productId}` : `card-selected-variant-${productId}`;
            const variantInput = document.getElementById(inputId);
            let variantId = (variantInput && variantInput.value) ? variantInput.value : null;

            // If product has variants but none selected, alert user
            if (!variantId && window.cardVariants && window.cardVariants[productId] && window.cardVariants[productId].length > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sélection Requise',
                    text: 'Veuillez choisir une finition et des dimensions avant d\'ajouter au panier.',
                    confirmButtonText: 'D\'accord',
                    confirmButtonColor: '#1a1a1a'
                });
                return;
            }

            // Find button to show loading
            const btn = event ? event.currentTarget : null;
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn && btn.tagName === 'BUTTON') {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    quantity: 1,
                    variant_id: variantId 
                })
            })
            .then(r => r.json())
            .then(data => {
                if (btn && btn.tagName === 'BUTTON') {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Ajouté au panier !',
                        showConfirmButton: false, timer: 2500, background: '#1a1a1a', color: '#fff'
                    });
                    ['header-cart-count', 'header-cart-count-mobile'].forEach(id => {
                        const badge = document.getElementById(id);
                        if (badge && data.cartCount !== undefined) badge.innerText = data.cartCount;
                    });
                    if (typeof refreshMiniCart === 'function') refreshMiniCart();
                } else {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: data.message || 'Une erreur est survenue' });
                }
            })
            .catch(err => {
                console.error(err);
                if (btn && btn.tagName === 'BUTTON') {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        }
    </script>
    <!-- Mobile Navigation Dock -->
    <div class="mobile-nav-dock d-lg-none">
        <a href="{{ url('/') }}" class="mobile-nav-item {{ Request::is('/') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
        </a>
        <a href="{{ route('shop.index') }}" class="mobile-nav-item {{ Request::is('shop*') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
        </a>
        <a href="#" class="mobile-nav-item search-trigger">
            <i class="fas fa-search"></i>
        </a>
        <a href="#" class="mobile-nav-item position-relative" data-bs-toggle="offcanvas" data-bs-target="#miniCart">
            <i class="fas fa-shopping-bag"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
                {{ count(session('cart', [])) }}
            </span>
        </a>
        <a href="{{ route('customer.dashboard') }}" class="mobile-nav-item {{ Request::is('my-account*') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i>
        </a>
    </div>

    <!-- Search Overlay -->
    <div class="search-overlay" id="searchOverlay">
        <button class="btn btn-link text-dark position-absolute top-0 end-0 m-4 fs-3 search-close">
            <i class="fas fa-times"></i>
        </button>
        <form action="{{ route('shop.index') }}" method="GET" class="w-100 d-flex flex-column align-items-center">
            <input type="text" name="query" class="search-input-full" placeholder="Que recherchez-vous ?" autocomplete="off">
            <p class="mt-4 text-muted small text-uppercase ls-2">Entrée pour lancer la recherche</p>
        </form>
    </div>

    <!-- Newsletter Popup -->
    @include('frontend.partials.newsletter-popup')

    @stack('scripts')

    <!-- Newsletter Popup Modal -->
    <div class="modal fade modal-newsletter" id="newsletterPopup" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 overflow-hidden rounded-5">
                <div class="row g-0">
                    <div class="col-md-5 d-none d-md-block newsletter-img"></div>
                    <div class="col-md-7">
                        <div class="modal-body p-5 text-center position-relative">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
                            <span class="badge-new mb-3 d-inline-block">Offre Exclusive</span>
                            <h3 class="fw-black text-uppercase ls-1 mb-4">Rejoignez <br><span style="color: var(--accent);">Le Cercle</span></h3>
                            <p class="text-muted small mb-4">Inscrivez-vous à notre newsletter et profitez de **-10%** sur votre première commande de mobilier artisanal.</p>
                            
                            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                                @csrf
                                <input type="email" name="email" class="form-control py-3 rounded-pill bg-light border-0 mb-3 text-center" placeholder="votre@email.com" required>
                                <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-black text-uppercase ls-1">Je m'inscris</button>
                            </form>
                            
                            <p class="mt-4 mb-0 text-muted" style="font-size: 0.65rem;">En vous inscrivant, vous acceptez nos conditions générales d'utilisation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Popovers (for Lookbook)
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Back to Top Button
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Newsletter Popup Delay
        if (!localStorage.getItem('newsletter_popup_shown')) {
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('newsletterPopup'));
                modal.show();
                localStorage.setItem('newsletter_popup_shown', 'true');
            }, 10000); // 10 seconds delay
        }
    });
    </script>

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
