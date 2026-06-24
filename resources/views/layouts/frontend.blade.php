<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', setting_trans('app_name', 'Coop Ait Oumdis'))</title>
    <meta name="description" content="@yield('meta_description', setting_trans('app_description', 'Natural Products Cooperative'))">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    @if(setting('app_logo'))
        <link rel="icon" type="image/png" href="{{ Storage::url(setting('app_logo')) }}">
        <link rel="apple-touch-icon" href="{{ Storage::url(setting('app_logo')) }}">
    @endif

    {{-- SEO / AEO / GEO --}}
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="author" content="{{ setting('app_name', 'Coop Ait Oumdis') }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:title"       content="@yield('meta_title', setting_trans('app_name', 'Coop Ait Oumdis'))">
    <meta property="og:description" content="@yield('meta_description', setting_trans('app_description', 'Natural Products Cooperative'))">
    <meta property="og:image"       content="@yield('meta_image', setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/og-image.jpg'))">
    <meta property="og:site_name"   content="{{ setting('app_name', 'Coop Ait Oumdis') }}">

    {{-- Twitter --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:url"         content="{{ url()->current() }}">
    <meta name="twitter:title"       content="@yield('meta_title', setting_trans('app_name', 'Coop Ait Oumdis'))">
    <meta name="twitter:description" content="@yield('meta_description', setting_trans('app_description', 'Natural Products Cooperative'))">
    <meta name="twitter:image"       content="@yield('meta_image', setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/og-image.jpg'))">

    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "{{ setting('app_name', 'Coop Ait Oumdis') }}",
      "url": "{{ url('/') }}",
      "logo": "{{ setting('app_logo') ? url(Storage::url(setting('app_logo'))) : '' }}",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{{ setting('app_phone', '') }}",
        "contactType": "customer service",
        "email": "{{ setting('app_email', '') }}"
      },
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Azilal",
        "addressCountry": "MA"
      },
      "sameAs": [
        "{{ setting('social_facebook', '#') }}",
        "{{ setting('social_instagram', '#') }}",
        "{{ setting('social_twitter', '#') }}"
      ]
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "url": "{{ url('/') }}",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/shop?search={search_term_string}') }}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    {{-- ── Fonts (preconnect first for performance) ── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    {{-- ── RTL Bootstrap swap (only for Arabic; default LTR handled by our bundle) ── --}}
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @endif

    {{-- ── Vite Bundled CSS (Bootstrap LTR + FA + AOS + Swiper + custom) ── --}}
    @vite(['resources/css/frontend.css'])

    {{-- Legacy static CSS (brand overrides compiled separately) --}}
    @if(file_exists(public_path('css/brand.css')))
        <link rel="stylesheet" href="{{ asset('css/brand.css') }}?v={{ filemtime(public_path('css/brand.css')) }}">
    @endif

    @stack('styles')

</head>
<body>

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <header class="main-header" id="mainHeader">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between py-3">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="text-decoration-none logo-link">
                    <div class="d-flex align-items-center gap-2">
                        @if(setting('app_logo'))
                            <img src="{{ Storage::url(setting('app_logo')) }}"
                                 alt="{{ __('Logo') }}"
                                 style="height:50px;width:auto;flex-shrink:0;filter:drop-shadow(0 2px 8px rgba(0,0,0,.15));">
                        @else
                            <div style="width:44px;height:44px;background:#bf8b43;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-leaf text-white fs-5"></i>
                            </div>
                        @endif
                        <div class="d-flex flex-column text-start align-items-start {{ app()->getLocale() == 'ar' ? 'text-end align-items-end' : '' }}">
                            <span class="fs-5 fw-bold text-white lh-1 logo-text font-tajawal">
                                {{ app()->getLocale() == 'ar' ? 'تعاونية آيت أومديس' : 'Coop Ait Oumdis' }}
                            </span>
                            <span class="x-small text-white opacity-50 mt-1 font-tajawal" style="font-size:0.62rem;">
                                {{ app()->getLocale() == 'ar' ? 'منتجات طبيعية من قلب الأطلس' : 'Produits Naturels du cœur de l\'Atlas' }}
                            </span>
                        </div>
                    </div>
                </a>

                {{-- Desktop Nav --}}
                <nav class="d-none d-lg-flex align-items-center gap-4">
                    <a href="{{ url('/') }}"                                       class="text-decoration-none nav-link-custom {{ Request::is('/') ? 'active' : '' }}">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : (app()->getLocale() == 'fr' ? 'Accueil' : 'Home') }}</a>
                    <a href="{{ route('shop.index', ['category' => 'honey']) }}"   class="text-decoration-none nav-link-custom {{ request('category') == 'honey' ? 'active' : '' }}">{{ app()->getLocale() == 'ar' ? 'العسل' : (app()->getLocale() == 'fr' ? 'Miel' : 'Honey') }}</a>
                    <a href="{{ route('shop.index', ['category' => 'amlou']) }}"  class="text-decoration-none nav-link-custom {{ request('category') == 'amlou' ? 'active' : '' }}">{{ app()->getLocale() == 'ar' ? 'أملو' : (app()->getLocale() == 'fr' ? 'Amlou' : 'Amlou') }}</a>
                    <a href="{{ route('shop.index') }}"                            class="text-decoration-none nav-link-custom {{ Request::routeIs('shop.index') && !request('category') ? 'active' : '' }}">{{ app()->getLocale() == 'ar' ? 'المنتجات الطبيعية' : (app()->getLocale() == 'fr' ? 'Produits Naturels' : 'Natural Products') }}</a>
                    <a href="https://wa.me/{{ str_replace(['+',' '],'',(setting('app_phone','212600000000'))) }}" target="_blank" class="text-decoration-none nav-link-custom">{{ app()->getLocale() == 'ar' ? 'اتصل بنا' : (app()->getLocale() == 'fr' ? 'Contact' : 'Contact Us') }}</a>
                </nav>

                {{-- Actions --}}
                <div class="d-flex align-items-center gap-2 gap-lg-3">

                    {{-- Desktop Search --}}
                    <div class="d-none d-lg-flex align-items-center header-search-container me-2">
                        <i class="fas fa-search text-white opacity-50 me-2 small"></i>
                        <form action="{{ route('shop.index') }}" method="GET" class="m-0 p-0 w-100">
                            <input type="text" name="search"
                                   placeholder="{{ app()->getLocale() == 'ar' ? 'بحث عن منتج...' : (app()->getLocale() == 'fr' ? 'Rechercher...' : 'Search...') }}"
                                   class="header-search-input opacity-75 small"
                                   value="{{ request('search') }}">
                        </form>
                    </div>

                    {{-- Language Switcher --}}
                    <div class="dropdown">
                        <button class="btn btn-link text-white opacity-75 text-decoration-none p-2 dropdown-toggle small fw-bold" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i> {{ strtoupper(app()->getLocale()) }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><a class="dropdown-item small" href="{{ route('lang.switch', 'ar') }}">🇲🇦 العربية</a></li>
                            <li><a class="dropdown-item small" href="{{ route('lang.switch', 'fr') }}">🇫🇷 Français</a></li>
                            <li><a class="dropdown-item small" href="{{ route('lang.switch', 'en') }}">🇬🇧 English</a></li>
                        </ul>
                    </div>

                    {{-- User icon --}}
                    @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-link text-white opacity-75 text-decoration-none p-2 d-none d-lg-block" title="{{ __('Dashboard') }}">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-link text-white opacity-75 text-decoration-none p-2 d-none d-lg-block" title="{{ __('Login') }}">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                    @endauth

                    {{-- Cart Toggle --}}
                    <button class="position-relative p-2 border-0 rounded-3 text-white"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#miniCart"
                            style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;background-color:rgba(255,255,255,0.1)!important;border:1px solid rgba(255,255,255,0.15)!important;">
                        <i class="fa-solid fa-bag-shopping fs-6"></i>
                        {{-- data-cart-count is the canonical badge — JS syncs all [data-cart-count] elements --}}
                        <span data-cart-count
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill border border-white"
                              style="font-size:0.55rem;transform:translate(-60%,20%);background-color:#bf8b43!important;">
                            {{ count(session('cart', [])) }}
                        </span>
                    </button>

                    {{-- Mobile hamburger --}}
                    <button class="d-lg-none p-2 border-0 rounded-3 text-white"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#mobileMenu"
                            style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;background-color:rgba(255,255,255,0.1)!important;border:1px solid rgba(255,255,255,0.15)!important;">
                        <i class="fa-solid fa-bars-staggered fs-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- ══════════════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════════════ --}}
    <footer class="footer-main pt-5 pb-4 mt-0">
        <div class="container">
            <div class="row g-5">

                {{-- Brand Column --}}
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:#2E993B;border-radius:9px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-leaf text-white small"></i>
                        </div>
                        <span class="text-white fw-bold fs-5 font-tajawal">{{ __('Ait') }} <span class="text-green-mid">{{ __('Oumdis') }}</span></span>
                    </div>
                    <p class="small lh-lg mb-4 font-tajawal" style="color:rgba(255,255,255,0.55);">
                        {{ setting_trans('app_description', __('A Moroccan cooperative dedicated to bringing you the finest natural products directly from the Atlas Mountains. Pure honey, argan oil, saffron, and more — harvested with tradition and love.')) }}
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ setting('social_instagram', '#') }}" class="footer-social" target="_blank"><i class="fab fa-instagram small"></i></a>
                        <a href="{{ setting('social_facebook', '#') }}"  class="footer-social" target="_blank"><i class="fab fa-facebook-f small"></i></a>
                        <a href="{{ setting('social_tiktok', '#') }}"    class="footer-social" target="_blank"><i class="fab fa-tiktok small"></i></a>
                        <a href="https://wa.me/{{ str_replace(['+',' '],'',(setting('social_whatsapp', setting('app_phone','212600000000')))) }}" class="footer-social" target="_blank"><i class="fab fa-whatsapp small"></i></a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-4">{{ __('Quick Links') }}</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li><a href="{{ url('/') }}"          class="footer-link">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('shop.index') }}" class="footer-link">{{ __('Shop') }}</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div class="col-6 col-lg-3">
                    <h6 class="text-white fw-bold mb-4">{{ __('Contact') }}</h6>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li class="d-flex align-items-start gap-2 small" style="color:rgba(255,255,255,0.55);">
                            <i class="fas fa-map-marker-alt text-green-mid mt-1"></i>
                            {{ __('Ait Oumdis, Azilal, Morocco') }}
                        </li>
                        <li class="d-flex align-items-center gap-2 small" style="color:rgba(255,255,255,0.55);">
                            <i class="fas fa-phone text-green-mid"></i>
                            {{ setting('app_phone', '+212 600 000 000') }}
                        </li>
                        <li class="d-flex align-items-center gap-2 small" style="color:rgba(255,255,255,0.55);">
                            <i class="fas fa-envelope text-green-mid"></i>
                            {{ setting('app_email', 'contact@aitoumdis.ma') }}
                        </li>
                    </ul>
                </div>

                {{-- Trust Badges — now with icons! --}}
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4">{{ __('Why Trust Us?') }}</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="footer-trust-item">
                            <i class="fas fa-certificate"></i>
                            <span>{{ __('100% Certified Organic') }}</span>
                        </div>
                        <div class="footer-trust-item">
                            <i class="fas fa-truck-fast"></i>
                            <span>{{ __('Delivery to all Morocco') }}</span>
                        </div>
                        <div class="footer-trust-item">
                            <i class="fas fa-hand-holding-dollar"></i>
                            <span>{{ __('Pay on Delivery') }}</span>
                        </div>
                        <div class="footer-trust-item">
                            <i class="fas fa-shield-halved"></i>
                            <span>{{ __('Satisfaction Guaranteed') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5" style="border-color:rgba(255,255,255,0.06);">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <p class="small mb-0" style="color:rgba(255,255,255,0.35);">
                    &copy; {{ date('Y') }} {{ setting_trans('app_name', 'Coop Ait Oumdis') }}. {{ __('All rights reserved.') }}
                </p>
                <div class="d-flex gap-3">
                    <span class="x-small" style="color:rgba(255,255,255,0.25);">{{ __('Crafted with') }} ❤️ {{ __('in Morocco') }}</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- ══════════════════════════════════════════════════
         MINI-CART DRAWER
    ══════════════════════════════════════════════════ --}}
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="miniCart" style="max-width:420px;width:100%;">
        <div class="offcanvas-header" style="background:#f9fafb;border-bottom:1px solid #f1f5f9;">
            <div class="d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;background:#2E993B;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-bag-shopping text-white small"></i>
                </div>
                <h5 class="offcanvas-title fw-bold mb-0">{{ __('My Cart') }}</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <div class="flex-grow-1 overflow-auto p-3" id="mini-cart-items">
                @include('frontend.cart.partials.mini-cart-items')
            </div>
            <div id="mini-cart-footer" class="border-top p-3" style="background:#f9fafb;">
                @include('frontend.cart.partials.mini-cart-footer')
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         MOBILE MENU OFFCANVAS (with language switcher)
    ══════════════════════════════════════════════════ --}}
    <div class="offcanvas offcanvas-{{ app()->getLocale() == 'ar' ? 'end' : 'start' }} border-0 shadow-lg"
         tabindex="-1" id="mobileMenu" style="width:300px;background:#fff;">
        <div class="offcanvas-header border-bottom py-4">
            <div class="d-flex align-items-center gap-2">
                <div style="width:32px;height:32px;background:#2E993B;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-leaf text-white small"></i>
                </div>
                <span class="fw-bold text-dark fs-5 font-tajawal">{{ __('Ait') }} <span class="text-green-mid">{{ __('Oumdis') }}</span></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            {{-- Nav links --}}
            <div class="list-group list-group-flush">
                <a href="{{ url('/') }}"                                       class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3 font-tajawal {{ Request::is('/') ? 'text-green fw-bold' : '' }}">
                    <i class="fas fa-home {{ Request::is('/') ? 'text-green-mid' : 'text-muted' }}"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'الرئيسية' : (app()->getLocale() == 'fr' ? 'Accueil' : 'Home') }}</span>
                </a>
                <a href="{{ route('shop.index') }}"                            class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3 font-tajawal {{ Request::routeIs('shop.*') ? 'text-green fw-bold' : '' }}">
                    <i class="fas fa-shopping-bag {{ Request::routeIs('shop.*') ? 'text-green-mid' : 'text-muted' }}"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'المتجر' : (app()->getLocale() == 'fr' ? 'Boutique' : 'Shop') }}</span>
                </a>
                @auth
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3 font-tajawal">
                    <i class="fa-regular fa-user text-muted"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3 font-tajawal">
                    <i class="fa-regular fa-user text-muted"></i>
                    <span>{{ __('Login') }}</span>
                </a>
                @endauth
            </div>

            {{-- Language Switcher (mobile — this was missing before!) --}}
            <div class="px-4 py-3 border-top mt-auto">
                <p class="x-small fw-bold text-muted text-uppercase mb-2" style="letter-spacing:1px;">{{ __('Language') }}</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('lang.switch', 'ar') }}"
                       class="btn btn-sm rounded-pill fw-bold {{ app()->getLocale() == 'ar' ? 'btn-dark' : 'btn-outline-secondary' }}"
                       style="font-size:0.75rem;">🇲🇦 العربية</a>
                    <a href="{{ route('lang.switch', 'fr') }}"
                       class="btn btn-sm rounded-pill fw-bold {{ app()->getLocale() == 'fr' ? 'btn-dark' : 'btn-outline-secondary' }}"
                       style="font-size:0.75rem;">🇫🇷 Français</a>
                    <a href="{{ route('lang.switch', 'en') }}"
                       class="btn btn-sm rounded-pill fw-bold {{ app()->getLocale() == 'en' ? 'btn-dark' : 'btn-outline-secondary' }}"
                       style="font-size:0.75rem;">🇬🇧 English</a>
                </div>
            </div>

            {{-- WhatsApp CTA --}}
            <div class="p-4">
                <div class="p-4 rounded-4 bg-light border border-light">
                    <div class="fw-bold text-dark mb-1 font-tajawal">{{ __('Contact Support') }}</div>
                    <div class="small text-muted mb-3 font-tajawal">{{ __('Need help with your order?') }}</div>
                    <a href="https://wa.me/{{ str_replace(['+',' '],'',(setting('app_phone','212600000000'))) }}"
                       class="btn btn-brand btn-brand-primary w-100 py-2 rounded-pill text-decoration-none">
                        <i class="fab fa-whatsapp me-1"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>


    {{-- Scroll to Top --}}
    <button id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fas fa-arrow-up small"></i>
    </button>

    {{-- ══════════════════════════════════════════════════
         JS CONFIG — passes PHP values to the frontend bundle
    ══════════════════════════════════════════════════ --}}
    <script>
        window.__csrfToken       = '{{ csrf_token() }}';
        window.__baseUrl         = '{{ url('') }}';
        window.__cartMiniUrl     = '{{ route('cart.mini') }}';
        window.__cartMiniFooterUrl = '{{ route('cart.miniFooter') }}';
        window.__cartUpdateUrl   = '{{ route('cart.update') }}';
        window.__cartRemoveUrl   = '{{ route('cart.remove') }}';
        window.__i18n = {
            addedToCart:  '{{ __('Added to cart!') }}',
            outOfStock:   '{{ __('Insufficient stock') }}',
            removeItem:   '{{ __('Remove this item?') }}',
            keep:         '{{ __('Keep') }}',
            remove:       '{{ __('Remove') }}',
            removedFromWishlist: '{{ __('Removed from wishlist') }}',
            addedToWishlist:     '{{ __('Added to wishlist!') }}',
            error:        '{{ __('Error') }}',
        };
    </script>

    {{-- ── Vite Bundled JS (Bootstrap + AOS + Swiper + SweetAlert2 + cart logic) ── --}}
    @vite(['resources/js/frontend.js'])

    {{-- ── Wishlist toggle (needs Swal, so after bundle) ── --}}
    <script>
        window.toggleWishlist = function (productId) {
            const heart = document.getElementById(`heart-icon-${productId}`);
            if (!heart) return;
            const isHearted = heart.classList.contains('fa-solid');
            if (isHearted) {
                heart.classList.replace('fa-solid', 'fa-regular');
                heart.classList.remove('text-danger');
                heart.classList.add('text-muted');
                Swal.fire({ icon:'success', title: window.__i18n.removedFromWishlist, toast:true, position:'top-end', showConfirmButton:false, timer:2000 });
            } else {
                heart.classList.replace('fa-regular', 'fa-solid');
                heart.classList.remove('text-muted');
                heart.classList.add('text-danger');
                Swal.fire({ icon:'success', title: window.__i18n.addedToWishlist, toast:true, position:'top-end', showConfirmButton:false, timer:2000 });
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
