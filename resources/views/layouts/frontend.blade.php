<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" style="scroll-behavior: smooth;">
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
    <meta name="theme-color" content="#1964D6">
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
    <meta property="og:site_name" content="{{ setting('app_name', 'Speed Platform') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', setting('app_name', 'Speed Platform'))">
    <meta property="og:description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta property="og:image" content="@yield('meta_image', setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/og-default.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ setting('language', 'fr') === 'ar' ? 'ar_MA' : 'fr_MA' }}">
    <meta property="og:updated_time" content="{{ now()->toIso8601String() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('meta_title', setting('app_name', 'Speed Platform'))">
    <meta name="twitter:description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta name="twitter:image" content="@yield('meta_image', setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/og-default.jpg'))">    
    <meta name="twitter:site" content="@yield('twitter_site', '@' . str_replace(' ', '', setting('app_name', 'SpeedPlatform')))">
    
    <!-- JSON-LD Structured Data Schema -->
    @yield('json_ld')
    
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&family=Noto+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dynamic Theme Overrides -->
    <style>
        :root {
            --primary: {{ setting('primary_color', '#475927') }};
            --primary-mid: {{ setting('primary_color', '#475927') }}cc;
            --secondary: #b93126;
            --gold: #d4af37;
            --bg-warm: #fcfaf8;
            --text-dark: #000000;
            --accent: {{ setting('primary_color', '#475927') }};
            --accent-hover: {{ setting('primary_color', '#475927') }}b3;
            --font-heading: {{ setting('font_family', "'Noto Kufi Arabic', 'Noto Sans', system-ui, sans-serif") }};
            --font-body: {{ setting('font_family', "'Noto Kufi Arabic', 'Noto Sans', system-ui, sans-serif") }};
        }
        body, h1, h2, h3, h4, h5, h6, .navbar-brand, .nav-link-custom {
            font-family: var(--font-body) !important;
        }
    </style>
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
    <!-- Preloader -->
    <div id="preloader" class="preloader-wrapper">
        <div class="preloader-content">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="spinner-grow text-danger" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4 class="mt-3" style="font-family: var(--font-heading); color: var(--primary);">ديوان الفن</h4>
        </div>
    </div>
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

    <!-- Gold accent line -->
    <div style="height:3px; background: linear-gradient(90deg, #d4a843 0%, #c8922a 100%); width:100%;"></div>

    <!-- Main Header -->
    <div class="header-main w-100 position-sticky top-0 transition-all duration-300" id="mainHeader" style="background-color: rgba(252, 250, 248, 0.9); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 1040; border-bottom: 1px solid rgba(0,0,0,0.06); box-shadow: 0 4px 30px rgba(0,0,0,0.03);">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <div class="container-fluid px-0">
                    
                    <!-- Language Switcher (Left Side in mockup) -->
                    <div class="d-none d-lg-flex align-items-center me-auto order-lg-1">
                        <div class="dropdown">
                            <button class="btn btn-link text-dark text-decoration-none fw-bold px-0 dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-family: var(--font-heading); font-size: 0.9rem; letter-spacing: 1px;">
                                EN / FR
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start shadow-sm border-0" aria-labelledby="languageDropdown">
                                <li><a class="dropdown-item fw-bold text-end" href="{{ route('lang.switch', 'ar') }}" style="font-family: 'Cairo', sans-serif;">العربية (AR)</a></li>
                                <li><a class="dropdown-item fw-bold" href="{{ route('lang.switch', 'fr') }}">Français (FR)</a></li>
                                <li><a class="dropdown-item fw-bold" href="{{ route('lang.switch', 'en') }}">English (EN)</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Navigation links (Middle) -->
                    <div class="collapse navbar-collapse order-lg-2" id="navbarMain">
                        @php
                            $headerMenu = \App\Models\Menu::where('location', 'header')->first();
                        @endphp
                        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3 gap-lg-4">
                            @if($headerMenu && $headerMenu->items->count() > 0)
                                @foreach($headerMenu->items->sortBy('order') as $item)
                                    <li class="nav-item">
                                        <a class="nav-link text-dark fw-bold {{ request()->is(ltrim($item->link, '/')) || (request()->routeIs('home') && $item->link === '/') ? 'active text-primary' : '' }}" href="{{ url($item->link) }}" style="font-size: 0.95rem; transition: color 0.3s ease;">
                                            {{ $item->label }}
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="nav-item"><a class="nav-link text-dark fw-bold {{ request()->routeIs('home') ? 'active text-primary' : '' }}" href="{{ url('/') }}">{{ __('الرئيسية') }}</a></li>
                                <li class="nav-item"><a class="nav-link text-dark fw-bold" href="{{ url('/about') }}">{{ __('عن الديوان') }}</a></li>
                                <li class="nav-item"><a class="nav-link text-dark fw-bold" href="{{ url('/workshops') }}">{{ __('الورش') }}</a></li>
                                <li class="nav-item"><a class="nav-link text-dark fw-bold" href="{{ url('/news') }}">{{ __('الأخبار والجديد') }}</a></li>
                                <li class="nav-item"><a class="nav-link text-dark fw-bold" href="{{ url('/events') }}">{{ __('الفعاليات') }}</a></li>
                                <li class="nav-item"><a class="nav-link text-dark fw-bold" href="{{ url('/research') }}">{{ __('البحوث والأساتذة') }}</a></li>
                                <li class="nav-item"><a class="nav-link text-dark fw-bold" href="{{ url('/shop') }}">{{ __('المتجر الفني') }}</a></li>
                                <li class="nav-item"><a class="nav-link text-dark fw-bold" href="{{ url('/gallery') }}">{{ __('المعرض') }}</a></li>
                                <li class="nav-item"><a class="nav-link text-dark fw-bold" href="{{ url('/contact') }}">{{ __('تواصل معنا') }}</a></li>
                            @endif
                        </ul>
                    </div>

                    <!-- Logo (Right Side in mockup) -->
                    <a class="navbar-brand ms-auto ms-lg-0 order-lg-3" href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ setting('app_name', 'Diwane') }}" class="navbar-logo-img" style="max-height: 45px;">
                    </a>

                    <!-- Mobile Toggler -->
                    <button class="navbar-toggler border-0 p-1 ms-2 order-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-expanded="false" aria-label="Menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>
        </div>
    </div>


    <main>
        @yield('content')
    </main>

    <!-- Offcanvas Mini Cart -->
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="miniCart" aria-labelledby="miniCartLabel" style="width: 450px; background: #f8fafc;">
        <div class="offcanvas-header bg-white border-bottom py-3">
            <h5 class="offcanvas-title fw-bold font-heading" id="miniCartLabel" style="font-family: 'Cairo', sans-serif; color: var(--primary);">
                <i class="fas fa-shopping-bag me-2" style="color: var(--primary);"></i>سلة المشتريات
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
                                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover" style="width: 80px; height: 80px;">
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

    <footer class="footer-modern pt-5 pb-4" style="background-color: #1c2410; color: rgba(255,255,255,0.85); border-top: none;">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ setting('app_name', 'Diwane') }} | ديوان الفن" class="mb-4" style="max-height: 60px;">
                    <p class="small lh-lg mb-4" style="color: rgba(255,255,255,0.65);">
                        {{ setting('app_description', 'أكاديمية ديوان الفن: فضاء فني وثقافي لتعليم الخط العربي والبحوث الأكاديمية الأصيلة وصيانة التراث الجمالي الإسلامي بالمملكة المغربية.') }}
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
                    <div class="d-flex gap-3 flex-wrap mb-4">
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
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold mb-4" style="font-family: 'Cairo', sans-serif; color: #d4a843;">{{ __('روابط سريعة') }}</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}" class="footer-link small d-block mb-2 text-decoration-none" style="color: rgba(255,255,255,0.6);">الرئيسية</a></li>
                        <li><a href="{{ url('/about') }}" class="footer-link small d-block mb-2 text-decoration-none" style="color: rgba(255,255,255,0.6);">عن الديوان</a></li>
                        <li><a href="{{ url('/workshops') }}" class="footer-link small d-block mb-2 text-decoration-none" style="color: rgba(255,255,255,0.6);">الورش الفنية</a></li>
                        <li><a href="{{ url('/news') }}" class="footer-link small d-block mb-2 text-decoration-none" style="color: rgba(255,255,255,0.6);">الأخبار والجديد</a></li>
                        <li><a href="{{ url('/events') }}" class="footer-link small d-block mb-2 text-decoration-none" style="color: rgba(255,255,255,0.6);">الفعاليات والمعارض</a></li>
                        <li><a href="{{ url('/research') }}" class="footer-link small d-block mb-2 text-decoration-none" style="color: rgba(255,255,255,0.6);">البحوث والأساتذة</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-12">
                    <h6 class="fw-bold mb-4" style="font-family: 'Cairo', sans-serif; color: #d4a843;">{{ __('النشرة البريدية') }}</h6>
                    <p class="small mb-3" style="color: rgba(255,255,255,0.6);">اشترك معنا ليصلك أحدث مستجدات المعارض والدورات التخصصية بالديوان.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="email" name="email" class="form-control form-control-sm border py-2" placeholder="البريد الإلكتروني..." required>
                        <button type="submit" class="btn btn-primary btn-sm px-3" style="background-color: var(--primary); border: none;">اشترك</button>
                    </form>
                </div>

            </div>
            
            <hr class="border-secondary opacity-25 my-4">
            
            <div class="row align-items-center">
                <div class="col-md-12 text-center">
                    <p class="small mb-0" style="color: rgba(255,255,255,0.5);">
                        &copy; {{ date('Y') }} {{ setting('app_name', 'Diwane') }}. جميع الحقوق محفوظة. 
                        صنع بكل شغف وفخر في المغرب <i class="fas fa-heart text-danger mx-1"></i> Made with passion in Morocco.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        @if(setting('frontend_enable_animations', true))
        AOS.init({
            duration: 1000,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50,
            delay: 50
        });
        @endif

        // Preloader Logic
        const hidePreloader = function() {
            const preloader = document.getElementById('preloader');
            if (preloader && preloader.style.display !== 'none') {
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            }
        };
        
        window.addEventListener('load', hidePreloader);
        // Fallback: hide preloader after 2 seconds max even if images are still loading
        setTimeout(hidePreloader, 2000);

        // Sticky Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('mainHeader');
            if (window.scrollY > 50) {
                header.style.backgroundColor = 'rgba(252, 250, 248, 0.95)';
                header.style.boxShadow = '0 10px 40px rgba(0,0,0,0.08)';
                header.style.borderBottom = '1px solid rgba(212, 168, 67, 0.2)';
            } else {
                header.style.backgroundColor = 'rgba(252, 250, 248, 0.9)';
                header.style.boxShadow = '0 4px 30px rgba(0,0,0,0.03)';
                header.style.borderBottom = '1px solid rgba(0,0,0,0.06)';
            }
        });

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
