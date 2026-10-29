<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', setting('app_name', 'WINA SHOP') . ' — ' . __('Matériel Photo, Vidéo, Caméras & Drones au Maroc'))</title>
    <meta name="description" content="@yield('meta_description', setting('app_description', 'WINA SHOP Casablanca : Votre référence au Maroc pour le matériel photo & vidéo professionnel. Caméras Sony, Canon, drones & stabilisateurs DJI, optiques, éclairage studio Godox. Garantie, devis proforma INDH et livraison partout au Maroc.'))">
    <meta name="keywords" content="@yield('meta_keywords', setting('app_name', 'WINA SHOP') . ', Winashop, matériel photo Maroc, vente caméras Casablanca, caméras cinéma, Sony Alpha Maroc, Canon EOS, DJI Osmo, drones DJI, stabilisateurs gimbals, éclairage Godox, objectifs photo, devis proforma INDH, facture avec ICE, matériel audiovisuel, كاميرات المغرب, بيع معدات التصوير الدار البيضاء, درون دي جي اي, إضاءة استوديو, شراء كاميرا كانون سوني')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    <meta name="author" content="{{ setting('app_name', 'WINA SHOP') }}">
    <meta name="developer" content="Elegant Boost (https://elegantboost.com/)">
    <meta name="designer" content="Elegant Boost">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Search Console & Webmaster Verification -->
    <meta name="google-site-verification" content="{{ setting('google_site_verification', 'mu1RdcICWNrWA2JcMpphobUwRmzCX1_Z9pAl51hs-Jg') }}">
    @if(setting('bing_site_verification'))
    <meta name="msvalidate.01" content="{{ setting('bing_site_verification') }}">
    @endif
    @if(setting('google_analytics_id'))
    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('google_analytics_id') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ setting('google_analytics_id') }}');
    </script>
    @endif

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Multi-Language Hreflang Tags (French, Arabic & English + x-default) -->
    <link rel="alternate" hreflang="fr" href="{{ request()->fullUrlWithQuery(['lang' => 'fr']) }}">
    <link rel="alternate" hreflang="fr-MA" href="{{ request()->fullUrlWithQuery(['lang' => 'fr']) }}">
    <link rel="alternate" hreflang="ar" href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}">
    <link rel="alternate" hreflang="ar-MA" href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}">
    <link rel="alternate" hreflang="en" href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">
    <link rel="alternate" hreflang="en-MA" href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <!-- AI Search & LLM Discovery Endpoints -->
    <link rel="alternate" type="text/plain" href="{{ url('/llms.txt') }}" title="LLMs Context">
    <link rel="alternate" type="text/plain" href="{{ url('/llms-full.txt') }}" title="Complete Product Catalog for AI">

    <!-- Geographic & Local Search Meta Tags (Morocco) -->
    <meta name="geo.region" content="MA-CAS">
    <meta name="geo.placename" content="Casablanca, Maroc">
    <meta name="geo.position" content="33.5574;-7.6744">
    <meta name="ICBM" content="33.5574, -7.6744">

    <!-- Preconnect to external resources for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Favicon -->
    <link rel="icon" href="{{ app_logo_url() }}" type="image/png">
    <link rel="icon" href="{{ app_logo_url() }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ app_logo_url() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ app_logo_url() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ app_logo_url() }}">
    <link rel="shortcut icon" href="{{ app_logo_url() }}">

    @php
        $siteTitle = setting('app_name', 'WINA SHOP');
        $defaultMetaImage = app_logo_url();
        
        $metaImageRaw = trim($__env->yieldContent('meta_image'));
        $resolvedOgImage = !empty($metaImageRaw) ? $metaImageRaw : $defaultMetaImage;
        
        // Ensure fully qualified URL
        if (str_starts_with($resolvedOgImage, '//')) {
            $resolvedOgImage = (request()->isSecure() ? 'https:' : 'http:') . $resolvedOgImage;
        } elseif (str_starts_with($resolvedOgImage, '/')) {
            $resolvedOgImage = url($resolvedOgImage);
        } elseif (!str_starts_with($resolvedOgImage, 'http://') && !str_starts_with($resolvedOgImage, 'https://')) {
            $resolvedOgImage = url($resolvedOgImage);
        }
        
        $resolvedOgSecure = str_replace('http://', 'https://', $resolvedOgImage);
        
        // Detect MIME type
        $imgExt = strtolower(pathinfo(parse_url($resolvedOgImage, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        $ogMime = match($imgExt) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/jpeg',
        };
        
        $metaType = trim($__env->yieldContent('meta_type', 'website'));
        $isProductType = $metaType === 'product';
        $metaUrl = trim($__env->yieldContent('meta_url', url()->current()));
        $metaTitle = trim($__env->yieldContent('meta_title', $siteTitle . ' — Matériel Photo, Vidéo & Caméras au Maroc'));
        $metaDesc = trim($__env->yieldContent('meta_description', setting('app_description', 'Votre référence au Maroc pour le matériel photo & vidéo professionnel — Caméras Sony, Canon, drones & stabilisateurs DJI, optiques et éclairage studio.')));
        $cleanMetaTitle = htmlspecialchars(strip_tags(html_entity_decode($metaTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8')), ENT_QUOTES, 'UTF-8');
        $cleanMetaDesc = htmlspecialchars(strip_tags(html_entity_decode($metaDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8')), ENT_QUOTES, 'UTF-8');
    @endphp

    <!-- Open Graph / Facebook / WhatsApp / iMessage / Telegram Link Previews -->
    <meta property="og:type" content="{{ $metaType }}">
    <meta property="og:site_name" content="{{ $siteTitle }}">
    <meta property="og:url" content="{{ $metaUrl }}">
    <meta property="og:title" content="{!! $cleanMetaTitle !!}">
    <meta property="og:description" content="{!! $cleanMetaDesc !!}">
    <meta property="og:image" content="{{ $resolvedOgImage }}">
    <meta property="og:image:secure_url" content="{{ $resolvedOgSecure }}">
    <meta property="og:image:type" content="{{ $ogMime }}">
    <meta property="og:image:width" content="{{ $isProductType ? '800' : '1200' }}">
    <meta property="og:image:height" content="{{ $isProductType ? '800' : '630' }}">
    <meta property="og:image:alt" content="{!! $cleanMetaTitle !!}">
    <link rel="image_src" href="{{ $resolvedOgImage }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_MA' : 'fr_MA' }}">
    <meta property="og:locale:alternate" content="{{ app()->getLocale() === 'ar' ? 'fr_MA' : 'ar_MA' }}">
    <meta property="og:updated_time" content="{{ now()->toIso8601String() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $metaUrl }}">
    <meta name="twitter:title" content="{!! $cleanMetaTitle !!}">
    <meta name="twitter:description" content="{!! $cleanMetaDesc !!}">
    <meta name="twitter:image" content="{{ $resolvedOgImage }}">
    <meta name="twitter:image:alt" content="{!! $cleanMetaTitle !!}">
    <meta name="twitter:site" content="@yield('twitter_site', '@winashop.ma')">

    <!-- Additional Page-Specific SEO Meta (e.g. product:price) -->
    @yield('extra_meta')
    
    <!-- JSON-LD Structured Data Schema -->
    @yield('json_ld')

    {{-- Global CameraStore & WebSite Knowledge Graph Schema --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": ["CameraStore", "Store", "LocalBusiness"],
      "@id": "{{ url('/') }}#organization",
      "name": "{{ setting('company_name', setting('app_name', 'WINA SHOP')) }}",
      "alternateName": ["Winashop", "Wina Shop Maroc", "وينا شوب"],
      "url": "{{ url('/') }}",
      "logo": "{{ app_logo_url() }}",
      "image": "{{ asset('images/camera/hero_winashop.jpg') }}",
      "description": "{{ addslashes(setting('app_description', 'Votre référence au Maroc pour le matériel photo & vidéo professionnel — Caméras Sony, Canon, drones DJI, stabilisateurs et éclairage studio.')) }}",
      "priceRange": "MAD 100 - MAD 150000",
      "currenciesAccepted": "MAD",
      "paymentAccepted": "Cash on Delivery, Bank Transfer, Credit Card",
      "openingHours": "Mo,Tu,We,Th,Fr,Sa 09:30-19:30",
      @if(setting('company_phone'))
      "telephone": "{{ setting('company_phone') }}",
      @else
      "telephone": "+212629035777",
      @endif
      @if(setting('company_email'))
      "email": "{{ setting('company_email') }}",
      @else
      "email": "contact@winashop.com",
      @endif
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ setting('company_address', '01 Rue 102, Hay Wiam, Al Oulfa') }}",
        "addressLocality": "{{ setting('company_city', 'Casablanca') }}",
        "addressRegion": "Grand Casablanca",
        "postalCode": "20220",
        "addressCountry": "MA"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 33.5574,
        "longitude": -7.6744
      },
      "areaServed": [
        { "@type": "AdministrativeArea", "name": "Maroc" },
        { "@type": "City", "name": "Casablanca" },
        { "@type": "City", "name": "Rabat" },
        { "@type": "City", "name": "Marrakech" },
        { "@type": "City", "name": "Tanger" },
        { "@type": "City", "name": "Agadir" },
        { "@type": "City", "name": "Fès" }
      ],
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
      "alternateName": ["Winashop", "Wina Shop Maroc", "وينا شوب"],
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
    <!-- Optimized Typography System (Consolidated Google Fonts with display=swap) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Cairo:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Cairo:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Cairo:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    </noscript>

    <!-- Core Layout & Theme CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ asset('css/frontend.min.css') }}?v={{ file_exists(public_path('css/frontend.min.css')) ? filemtime(public_path('css/frontend.min.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('css/camera-theme.min.css') }}?v={{ file_exists(public_path('css/camera-theme.min.css')) ? filemtime(public_path('css/camera-theme.min.css')) : time() }}">

    <!-- Non-Critical Deferred Plugins (FontAwesome, AOS, SweetAlert, Flags) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" media="print" onload="this.media='all'">
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/js/app.js'])
    @endif
    
    <!-- Global Roboto & Normal Weight Typography System with font-display: swap -->
    <style>
        @font-face {
            font-family: 'Font Awesome 6 Free';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-regular-400.woff2") format("woff2");
        }
        @font-face {
            font-family: 'Font Awesome 6 Free';
            font-style: normal;
            font-weight: 900;
            font-display: swap;
            src: url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-solid-900.woff2") format("woff2");
        }
        @font-face {
            font-family: 'Font Awesome 6 Brands';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-brands-400.woff2") format("woff2");
        }
        /* Prevent automatic zoom on mobile when focusing inputs */
        @media screen and (max-width: 991.98px) {
            .header-search-input,
            #headerSearchInput,
            .pro-search-input,
            #proMobileSearchInput,
            input[type="text"],
            input[type="search"],
            input[type="tel"],
            input[type="email"],
            input[type="number"],
            input[type="password"],
            select,
            textarea {
                font-size: 16px !important;
                touch-action: manipulation;
            }
            .brand-logo-img {
                height: 80px !important;
                max-height: 80px !important;
                width: auto !important;
                object-fit: contain;
            }
        }
        /* Keep header search results above the shop sticky filter bar (z-index 1050) */
        body.header-search-open .header-main,
        .header-main:has(#headerSearchDropdown:not(.d-none)) {
            z-index: 1065 !important;
        }
        :root {
            --font-heading: "Roboto", sans-serif !important;
            --font-body: "Roboto", sans-serif !important;
            --font-family: "Roboto", sans-serif !important;
        }
        body, input, button, select, textarea, p, span:not([class*="fa"]):not(.fas):not(.far):not(.fab), a, li, label, table, th, td {
            font-family: "Roboto", sans-serif !important;
        }
        h1, h2, h3, h4, h5, h6, .font-heading, .brand-title, .page-title, .hero-title, .section-title, .product-title, .product-card-title {
            font-family: "Roboto", sans-serif !important;
            font-weight: 500 !important;
        }
        b, strong, .pdp-description b, .pdp-description strong, .pdp-enrich-body b, .pdp-enrich-body strong, .entry-content b, .entry-content strong {
            font-weight: 700 !important;
        }
        .fw-bolder, .fw-extrabold, th, dt, .product-price, .nav-link-item, .price-display, .badge, .btn {
            font-weight: 400 !important;
        }
        /* Protect FontAwesome icon font and solid weights */
        .fas, .fa-solid {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
        }
        .far, .fa-regular {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 400 !important;
        }
        .fab, .fa-brands {
            font-family: "Font Awesome 6 Brands" !important;
            font-weight: 400 !important;
        }

        /* Category Button & Link Heartbeat Animation */
        @-webkit-keyframes heartbeat {
            from, to, 100% {
                -webkit-transform: scale(1);
                transform: scale(1);
                -webkit-transform-origin: center center;
                transform-origin: center center;
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out;
            }
            10% {
                -webkit-transform: scale(.91);
                transform: scale(.91);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in;
            }
            17% {
                -webkit-transform: scale(.98);
                transform: scale(.98);
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out;
            }
            33% {
                -webkit-transform: scale(.87);
                transform: scale(.87);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in;
            }
            45% {
                -webkit-transform: scale(1);
                transform: scale(1);
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out;
            }
        }

        @keyframes heartbeat {
            from, to, 100% {
                -webkit-transform: scale(1);
                transform: scale(1);
                -webkit-transform-origin: center center;
                transform-origin: center center;
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out;
            }
            10% {
                -webkit-transform: scale(.91);
                transform: scale(.91);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in;
            }
            17% {
                -webkit-transform: scale(.98);
                transform: scale(.98);
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out;
            }
            33% {
                -webkit-transform: scale(.87);
                transform: scale(.87);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in;
            }
            45% {
                -webkit-transform: scale(1);
                transform: scale(1);
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out;
            }
        }

        #allCategoriesLinkNavbar {
            -webkit-animation: heartbeat 1.5s ease-in-out infinite both !important;
            animation: heartbeat 1.5s ease-in-out infinite both !important;
            -webkit-transform-origin: center center !important;
            transform-origin: center center !important;
            will-change: transform;
        }

        /* ================================================================
           UNIVERSAL 8PX BORDER RADIUS SYSTEM
           ================================================================ */
        :root {
            --bs-border-radius: 8px !important;
            --bs-border-radius-sm: 8px !important;
            --bs-border-radius-lg: 8px !important;
            --bs-border-radius-xl: 8px !important;
            --bs-border-radius-xxl: 8px !important;
            --bs-border-radius-pill: 8px !important;
            --radius-sm: 8px !important;
            --radius-md: 8px !important;
            --radius-lg: 8px !important;
            --radius-xl: 8px !important;
            --border-radius: 8px !important;
            --brand-radius: 8px !important;
        }

        *,
        *::before,
        *::after {
            --bs-border-radius: 8px !important;
            --bs-border-radius-pill: 8px !important;
        }

        [class*="rounded"],
        .rounded,
        .rounded-1,
        .rounded-2,
        .rounded-3,
        .rounded-4,
        .rounded-5,
        .rounded-pill,
        .rounded-top,
        .rounded-bottom,
        .rounded-start,
        .rounded-end,
        .btn,
        .btn-group,
        .btn-group > .btn,
        button:not([class*="pulse"]):not([class*="dot"]),
        input,
        select,
        textarea,
        .form-control,
        .form-select,
        .input-group,
        .input-group-text,
        .badge,
        .card,
        .card-header,
        .card-footer,
        .card-body,
        .pcard,
        .pcard-img,
        .pcard-img img,
        .featured-pro-card,
        .featured-pro-img-box,
        .selection-showcase-box,
        .pdp-card,
        .pdp-section-card,
        .pdp-main-image-wrap,
        .pdp-thumb,
        .pdp-thumb img,
        .pdp-qty-wrap,
        .pdp-add-btn,
        .pdp-trust-pill,
        .popular-category-card,
        .popular-cat-img-wrap,
        .popular-cat-img-wrap img,
        .client-review-box,
        .indh-step-card-pro,
        .indh-guarantee-pill,
        .indh-feature-pill,
        .indh-banner-cta,
        .indh-step-icon-slot,
        .indh-step-btn,
        .shop-filter-card,
        .shop-toolbar,
        .product-card-v2,
        .cat-card-v2,
        .service-card,
        .app-card,
        .process-step,
        .usp-mini-card,
        .review-card,
        .faq-item,
        .newsletter-banner,
        .newsletter-v2-inner,
        .hero-slide,
        .hero-badge,
        .order-card,
        .modal-content,
        .dropdown-menu,
        .dropdown-item,
        .offcanvas,
        .alert,
        .page-link,
        .header-search-wrap,
        .header-search-box,
        .header-search-dropdown,
        .action-btn-circle,
        .btn-cat-swiper-arrow,
        .brand-chip,
        .filter-pill,
        .trust-pill,
        .badge-v2,
        .featured-brand-chip,
        .pcard-badge,
        .about-hero-pill,
        .category-row-card {
            border-radius: 8px !important;
        }

        /* Pulse dots retain circular status */
        .stock-pulse-dot,
        .tally-dot,
        .about-pulse-dot,
        .nav-live-pulse,
        .badge-dot {
            border-radius: 50% !important;
        }

        /* ================================================================
           PRODUCT CARD UNIFORM SIZE & EXACT ELEMENT POSITIONING
           ================================================================ */
        .featured-pro-card.selection-card {
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            padding: 8px !important;
            box-sizing: border-box !important;
        }

        .featured-pro-card .featured-pro-img-box,
        .selection-card .featured-pro-img-box {
            height: 215px !important;
            min-height: 215px !important;
            max-height: 215px !important;
            margin-bottom: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #ffffff !important;
            padding: 4px !important;
            position: relative !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .featured-pro-card .featured-pro-img-box img,
        .selection-card .featured-pro-img-box img {
            width: 100% !important;
            height: 100% !important;
            max-height: 100% !important;
            max-width: 100% !important;
            object-fit: contain !important;
            transition: transform 0.3s ease;
        }

        .featured-pro-card .featured-pro-title,
        .selection-card .featured-pro-title,
        .featured-pro-card .featured-pro-title a,
        .selection-card .featured-pro-title a,
        .featured-pro-title,
        .featured-pro-title a {
            color: #000000 !important;
            font-size: 0.84rem !important;
            font-weight: 450 !important;
            line-height: 1.35 !important;
            height: 2.7em !important;
            min-height: 2.7em !important;
            max-height: 2.7em !important;
            overflow: hidden !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            margin-bottom: 8px !important;
        }

        .featured-pro-card .pcard-price,
        .selection-card .pcard-price {
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            align-items: flex-start !important;
            height: 42px !important;
            min-height: 42px !important;
            max-height: 42px !important;
            margin-bottom: 0px !important;
            overflow: hidden !important;
            line-height: 1.1 !important;
            gap: 0px !important;
        }

        .featured-pro-card .pcard-price-main-line,
        .selection-card .pcard-price-main-line {
            display: flex !important;
            align-items: center !important;
            justify-content: left !important;
            width: 100% !important;
            height: 24px !important;
            min-height: 24px !important;
            max-height: 24px !important;
            line-height: 1.15 !important;
            margin-bottom: 2px !important;
            gap: 4px !important;
        }

        .featured-pro-card .pcard-price-current,
        .selection-card .pcard-price-current {
            font-size: 1.21rem !important;
            font-weight: 700 !important;
            line-height: 1.15 !important;
            color: #000000 !important;
            height: 24px !important;
            min-height: 24px !important;
            max-height: 24px !important;
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
            margin-bottom: 0px !important;
        }

        .featured-pro-card .pcard-price-old,
        .selection-card .pcard-price-old {
            font-size: 0.83rem !important;
            color: #c10f2b !important;
            text-decoration: line-through !important;
            line-height: 1.1 !important;
            height: 16px !important;
            min-height: 16px !important;
            max-height: 16px !important;
            display: block !important;
            white-space: nowrap !important;
            margin-top: 0px !important;
        }

        .selection-card .featured-pro-body,
        .featured-pro-card .featured-pro-body {
            margin-bottom: 4px !important;
        }

        .selection-card .featured-pro-action,
        .featured-pro-card .featured-pro-action {
            margin-top: 2px !important;
            padding-top: 0px !important;
        }

        /* 100% Unified PDP & Product Card Stock Badges System */
        .pdp-stock-badge,
        .featured-stock-pill {
            display: inline-flex !important;
            align-items: center !important;
            line-height: 1 !important;
            border-radius: 20px !important;
            white-space: nowrap !important;
            font-weight: 700 !important;
            letter-spacing: normal !important;
            text-transform: none !important;
        }

        .pdp-stock-badge--in,
        .featured-stock-pill:not(.featured-stock-pill--out),
        .selection-card .featured-stock-pill:not(.featured-stock-pill--out),
        .featured-pro-card .featured-stock-pill:not(.featured-stock-pill--out) {
            background: rgba(21, 128, 61, 0.08) !important;
            border: 0.1em solid rgb(23 128 61 / 43%) !important;
            color: #15803d !important;
            font-weight: 700 !important;
            border-radius: 20px !important;
            display: inline-flex !important;
            align-items: center !important;
            line-height: 1 !important;
        }

        .pdp-stock-badge--in i {
            color: #15803d !important;
            font-size: 0.74rem !important;
        }

        .featured-stock-pill:not(.featured-stock-pill--out) i {
            color: #15803d !important;
            font-size: 0.67rem !important;
        }

        .pcard-price .featured-stock-pill,
        .selection-card .featured-stock-pill,
        .featured-pro-card .featured-stock-pill,
        .featured-stock-pill {
            font-size: 0.78rem !important;
            padding: 2.2px 6.5px !important;
            gap: 2.5px !important;
            margin: 0 !important;
            margin-left: 5px !important;
            flex-shrink: 0 !important;
            transform: none !important;
            align-self: center !important;
        }

        .pdp-price-group .pdp-stock-badge,
        .pdp-price-main-line .pdp-stock-badge {
            font-size: 0.71rem !important;
            padding: 4.5px 11px !important;
            transform: none !important;
            align-self: center !important;
        }

        .pcard-price .featured-stock-pill--out,
        .selection-card .featured-stock-pill--out,
        .featured-pro-card .featured-stock-pill--out,
        .featured-stock-pill--out {
            background: rgba(220, 38, 38, 0.09) !important;
            border: 0.1em solid rgb(200 16 47 / 43%) !important;
            color: #dc2626 !important;
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            border-radius: 20px !important;
            display: inline-flex !important;
            align-items: center !important;
            line-height: 1 !important;
            transform: none !important;
            align-self: center !important;
        }

        .pdp-stock-badge--out,
        .pdp-price-main-line .pdp-stock-badge--out {
            background: rgba(220, 38, 38, 0.09) !important;
            border: 0.1em solid rgb(200 16 47 / 43%) !important;
            color: #dc2626 !important;
            font-weight: 700 !important;
            border-radius: 20px !important;
            display: inline-flex !important;
            align-items: center !important;
            line-height: 1 !important;
            font-size: 0.71rem !important;
            padding: 4.5px 11px !important;
            transform: none !important;
            align-self: center !important;
        }

        .featured-stock-pill--out i {
            color: #dc2626 !important;
            font-size: 0.67rem !important;
        }

        .pdp-stock-badge--out i {
            color: #dc2626 !important;
            font-size: 0.72rem !important;
        }

        .stock-pulse-dot {
            width: 4px !important;
            height: 4px !important;
            background: #02a826 !important;
        }

        /* Tighter spacing between Add to Cart row and Commander button */
        .pdp-cart-row {
            margin-bottom: 0px !important;
        }

        .pdp-commander-wrap {
            margin-top: 8px !important;
        }

        .pdp-cat-label {
            color: var(--brand-red, #dc2626) !important;
        }

        .pdp-cat-label:hover {
            color: #b91c1c !important;
        }

        .pdp-top-meta {
            margin-bottom: 8px !important;
        }

        .pdp-sku-trust-box {
            display: flex;
            align-items: center;
        }

        .pdp-sku-badge {
            position: relative;
            background: #f8fafc;
            border: 1px solid var(--light-border, #e2e8f0);
            color: #475569;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: all;
            outline: none;
            line-height: 1.3;
        }

        .pdp-sku-badge:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        }

        .pdp-sku-badge:active {
            transform: translateY(0);
        }

        .pdp-sku-icon {
            color: var(--brand-red, #dc2626);
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }

        .pdp-sku-badge:hover .pdp-sku-icon {
            color: var(--brand-red, #dc2626);
        }

        .pdp-sku-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.78rem;
        }

        .pdp-sku-value {
            color: #0f172a;
            font-family: var(--font-mono, monospace);
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        .pdp-sku-copy-icon {
            color: #94a3b8;
            font-size: 0.78rem;
            transition: all 0.2s ease;
        }

        .pdp-sku-badge:hover .pdp-sku-copy-icon {
            color: var(--brand-red, #dc2626);
        }

        .pdp-sku-copied-tooltip {
            display: none;
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10;
            pointer-events: none;
        }

        .pdp-sku-copied-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 4px;
            border-style: solid;
            border-color: #0f172a transparent transparent transparent;
        }

        .pdp-sku-badge.copied .pdp-sku-copied-tooltip {
            display: block;
        }

        .pdp-sku-badge.copied {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .pdp-sku-badge.copied .pdp-sku-copy-icon {
            color: #16a34a;
        }

        .pdp-price-main-line {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }

        .pdp-price-main {
            display: inline-flex !important;
            align-items: center !important;
            line-height: 1 !important;
        }

        .pdp-thumbs {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 10px !important;
            padding: 12px 24px !important;
            border-top: 1px solid var(--light-border-sub, #f1f5f9) !important;
            background: #f8fafc !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .pdp-thumb {
            flex: 1 1 0 !important;
            max-width: 76px !important;
            min-width: 0 !important;
            aspect-ratio: 1 / 1 !important;
            height: auto !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            border: 2px solid var(--light-border, #e2e8f0) !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            background: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .pdp-thumb img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            display: block !important;
        }

        .pdp-thumb:hover,
        .pdp-thumb.active {
            border-color: var(--brand-red, #dc2626) !important;
            transform: translateY(-2px);
            box-shadow: var(--brand-red-glow, 0 4px 14px rgba(220, 38, 38, 0.25));
        }

        @media (max-width: 576px) {
            .pdp-thumbs {
                gap: 8px !important;
                padding: 10px 14px !important;
            }
            .pdp-thumb {
                border-radius: 10px !important;
            }
        }

        @media (max-width: 360px) {
            .pdp-thumbs {
                gap: 6px !important;
                padding: 8px 10px !important;
            }
            .pdp-thumb {
                border-radius: 8px !important;
            }
        }

        .search-result-meta {
            display: flex !important;
            align-items: flex-start !important;
        }

        .search-result-price-wrap,
        .pro-result-price-wrap {
            display: inline-flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            line-height: 1 !important;
        }

        .search-result-price-main-line,
        .pro-result-price-main-line {
            display: inline-flex !important;
            align-items: baseline !important;
            gap: 8px !important;
            line-height: 1 !important;
        }

        .search-result-price,
        .pro-result-price {
            font-family: var(--font-mono, monospace) !important;
            font-weight: 800 !important;
            font-size: 0.85rem !important;
            color: #0f172a !important;
            line-height: 1 !important;
            margin-bottom: 0px !important;
        }

        .search-result-price-old,
        .pro-result-price-old {
            font-family: var(--font-mono, monospace) !important;
            font-size: 0.72rem !important;
            color: #dc2626 !important;
            text-decoration: line-through !important;
            display: block !important;
            line-height: 1 !important;
            margin-top: 3px !important;
        }

        .search-result-stock {
            font-family: var(--font-mono, monospace) !important;
            font-size: 0.68rem !important;
            font-weight: 600 !important;
            color: #02a826 !important;
            display: inline-flex !important;
            align-items: baseline !important;
            gap: 4.5px !important;
            line-height: 1 !important;
            margin-bottom: 0px !important;
        }

        .search-result-stock.out {
            color: #94a3b8 !important;
        }

        .search-result-stock::before {
            content: '' !important;
            display: inline-block !important;
            width: 5.5px !important;
            height: 5.5px !important;
            border-radius: 50% !important;
            background: currentColor !important;
        }

        .pro-result-stock {
            font-size: 0.71rem !important;
            font-weight: 600 !important;
            padding: 2.2px 7.5px !important;
            border-radius: 50px !important;
            background: #ecfdf5 !important;
            color: #02a826 !important;
            line-height: 1 !important;
            margin-bottom: 0px !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        mark.search-highlight,
        .search-highlight {
            background: #e11d481a !important;
            color: #e11d48 !important;
            font-weight: 800 !important;
            padding: 0 2px !important;
            border-radius: 3px !important;
        }

        .featured-stock-badge-corner {
            position: static !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
            z-index: auto !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-flex !important;
            align-items: center !important;
            box-shadow: none !important;
            pointer-events: auto !important;
            margin: 0 !important;
            margin-left: 5px !important;
            flex-shrink: 0 !important;
        }

        .featured-pro-card:hover .featured-stock-badge-corner,
        .selection-card:hover .featured-stock-badge-corner {
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-flex !important;
        }

        [dir="rtl"] .featured-stock-badge-corner,
        html[dir="rtl"] .featured-stock-badge-corner {
            left: auto !important;
            right: auto !important;
            top: auto !important;
        }

        .featured-pro-card .featured-pro-btn,
        .selection-card .featured-pro-btn {
            height: 38px !important;
            min-height: 38px !important;
            max-height: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 10px !important;
            font-size: 0.80rem !important;
            width: 100% !important;
            white-space: nowrap !important;
        }

        @media (max-width: 575px) {
            .featured-pro-card.selection-card {
                padding: 6px !important;
            }

            .featured-pro-card .featured-pro-img-box,
            .selection-card .featured-pro-img-box {
                height: 165px !important;
                min-height: 165px !important;
                max-height: 165px !important;
                margin-bottom: 5px !important;
                padding: 3px !important;
            }

            .featured-pro-card .featured-pro-title,
            .selection-card .featured-pro-title,
            .featured-pro-card .featured-pro-title a,
            .selection-card .featured-pro-title a,
            .featured-pro-title,
            .featured-pro-title a {
                color: #000000 !important;
                font-size: 0.78rem !important;
                font-weight: 450 !important;
                line-height: 1.25 !important;
                height: 2.5em !important;
                min-height: 2.5em !important;
                max-height: 2.5em !important;
                margin-bottom: 8px !important;
            }

            .featured-pro-card .pcard-price,
            .selection-card .pcard-price {
                height: 38px !important;
                min-height: 38px !important;
                max-height: 38px !important;
                margin-bottom: 0px !important;
                line-height: 1.1 !important;
                gap: 0px !important;
            }

            .featured-pro-card .pcard-price-main-line,
            .selection-card .pcard-price-main-line {
                display: flex !important;
                align-items: center !important;
                justify-content: left !important;
                width: 100% !important;
                height: 22px !important;
                min-height: 22px !important;
                max-height: 22px !important;
                line-height: 1.15 !important;
                margin-bottom: 1px !important;
                gap: 3px !important;
            }

            .featured-pro-card .pcard-price-current,
            .selection-card .pcard-price-current {
                font-size: 1.12rem !important;
                height: 22px !important;
                min-height: 22px !important;
                max-height: 22px !important;
                display: inline-flex !important;
                align-items: center !important;
                margin-bottom: 0px !important;
                color: #000000 !important;
            }

            .featured-pro-card .pcard-price-old,
            .selection-card .pcard-price-old {
                font-size: 0.78rem !important;
                height: 15px !important;
                min-height: 15px !important;
                max-height: 15px !important;
                margin-top: 0px !important;
            }

            .selection-card .featured-pro-body,
            .featured-pro-card .featured-pro-body {
                margin-bottom: 3px !important;
            }

            .selection-card .featured-pro-action,
            .featured-pro-card .featured-pro-action {
                margin-top: 2px !important;
                padding-top: 0px !important;
            }

            .selection-card .featured-stock-pill,
            .featured-pro-card .featured-stock-pill,
            .featured-stock-pill,
            .featured-stock-pill--out {
                font-size: 0.65rem !important;
                padding: 1.6px 5.5px !important;
                border-radius: 20px !important;
                margin: 0 !important;
                margin-left: 4px !important;
                flex-shrink: 0 !important;
                transform: none !important;
                align-self: center !important;
            }
            .featured-stock-pill i,
            .featured-stock-pill--out i {
                font-size: 0.59rem !important;
            }

            .featured-stock-badge-corner {
                position: static !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                bottom: auto !important;
                z-index: auto !important;
            }

            [dir="rtl"] .featured-stock-badge-corner,
            html[dir="rtl"] .featured-stock-badge-corner {
                left: auto !important;
                right: auto !important;
                top: auto !important;
            }

            .featured-pro-card .featured-pro-btn,
            .selection-card .featured-pro-btn {
                height: 34px !important;
                min-height: 34px !important;
                max-height: 34px !important;
                font-size: 0.74rem !important;
                padding: 0 6px !important;
            }
        }

        /* ===== Proportional stock badge system: Card badge matches PDP scale and alignment ===== */
        .featured-pro-card .pcard-price-main-line,
        .selection-card .pcard-price-main-line {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 5px !important;
            line-height: 1 !important;
        }

        .featured-pro-card .pcard-price-main-line > .pcard-price-current,
        .selection-card .pcard-price-main-line > .pcard-price-current {
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            align-self: center !important;
        }

        /* Card stock badge: delicately sized (10% smaller) and optically aligned with current price digits baseline */
        .featured-pro-card .pcard-price-main-line > .featured-stock-pill,
        .selection-card .pcard-price-main-line > .featured-stock-pill,
        .pcard-price .featured-stock-pill,
        .pcard-price-main-line > .featured-stock-pill {
            position: static !important;
            inset: auto !important;
            display: inline-flex !important;
            align-items: center !important;
            align-self: center !important;
            gap: 2.5px !important;
            height: 16px !important;
            min-height: 16px !important;
            max-height: 16px !important;
            padding: 0 6px !important;
            font-size: 0.585rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.02em !important;
            line-height: 1 !important;
            box-sizing: border-box !important;
            border-radius: 20px !important;
            margin: 0 !important;
            margin-left: 4px !important;
            flex-shrink: 0 !important;
            transform: translateY(-1.5px) !important;
        }

        .featured-pro-card .pcard-price-main-line > .featured-stock-pill:not(.featured-stock-pill--out),
        .selection-card .pcard-price-main-line > .featured-stock-pill:not(.featured-stock-pill--out),
        .pcard-price .featured-stock-pill:not(.featured-stock-pill--out),
        .pcard-price-main-line > .featured-stock-pill:not(.featured-stock-pill--out),
        .shop-body .featured-stock-pill:not(.featured-stock-pill--out),
        .shop-body .featured-stock-pill--in,
        .home-products-grid .featured-stock-pill--in,
        .featured-stock-pill--in {
            border: 0.1em solid rgb(23 128 61 / 43%) !important;
            background: rgba(21, 128, 61, 0.09) !important;
            color: #15803d !important;
        }

        .featured-pro-card .pcard-price-main-line > .featured-stock-pill--out,
        .selection-card .pcard-price-main-line > .featured-stock-pill--out,
        .pcard-price .featured-stock-pill--out,
        .pcard-price-main-line > .featured-stock-pill--out,
        .shop-body .featured-stock-pill--out,
        .home-products-grid .featured-stock-pill--out,
        .featured-stock-pill--out {
            border: 0.1em solid rgb(200 16 47 / 43%) !important;
            background: rgba(220, 38, 38, 0.09) !important;
            color: #dc2626 !important;
        }

        .featured-pro-card .pcard-price-main-line > .featured-stock-pill i,
        .selection-card .pcard-price-main-line > .featured-stock-pill i,
        .pcard-price .featured-stock-pill i,
        .pcard-price-main-line > .featured-stock-pill i {
            display: block !important;
            font-size: 0.52rem !important;
            line-height: 1 !important;
            margin: 0 !important;
        }

        .featured-pro-card .pcard-price-main-line > .featured-stock-pill--out i,
        .selection-card .pcard-price-main-line > .featured-stock-pill--out i,
        .pcard-price .featured-stock-pill--out i,
        .pcard-price-main-line > .featured-stock-pill--out i {
            color: #dc2626 !important;
        }

        .featured-pro-card .featured-stock-label,
        .selection-card .featured-stock-label,
        .pcard-price .featured-stock-label,
        .pcard-price-main-line .featured-stock-label {
            display: block !important;
            font-size: 0.585rem !important;
            line-height: 1 !important;
            font-weight: 700 !important;
            letter-spacing: 0.01em !important;
        }

        [dir="rtl"] .featured-pro-card .pcard-price-main-line > .featured-stock-pill,
        [dir="rtl"] .selection-card .pcard-price-main-line > .featured-stock-pill,
        [dir="rtl"] .pcard-price .featured-stock-pill,
        [dir="rtl"] .pcard-price-main-line > .featured-stock-pill,
        html[dir="rtl"] .featured-pro-card .pcard-price-main-line > .featured-stock-pill,
        html[dir="rtl"] .selection-card .pcard-price-main-line > .featured-stock-pill,
        html[dir="rtl"] .pcard-price .featured-stock-pill,
        html[dir="rtl"] .pcard-price-main-line > .featured-stock-pill {
            margin-left: 0 !important;
            margin-right: 4px !important;
        }

        /* ===== Product page (PDP): stock badge and price alignment ===== */
        .pdp-price-row .pdp-price-main-line,
        .pdp-price-row .pdp-price-main-line.d-flex {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 10px !important;
            line-height: 1 !important;
        }

        .pdp-price-row .pdp-price-main-line > .pdp-price-main {
            line-height: 1 !important;
            display: inline-flex !important;
            align-items: center !important;
            align-self: center !important;
        }

        .pdp-price-row .pdp-price-main-line > .pdp-stock-badge {
            position: static !important;
            transform: none !important;
            align-self: center !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            height: 22px !important;
            min-height: 22px !important;
            max-height: 22px !important;
            padding: 0 10px !important;
            font-size: 0.71rem !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            box-sizing: border-box !important;
            border: none !important;
            border-radius: 20px !important;
            margin: 0 !important;
            flex-shrink: 0 !important;
        }

        .pdp-price-row .pdp-price-main-line > .pdp-stock-badge--in {
            border: 0.1em solid rgb(23 128 61 / 43%) !important;
            background: rgba(21, 128, 61, 0.09) !important;
            color: #15803d !important;
        }

        .pdp-price-row .pdp-price-main-line > .pdp-stock-badge--out {
            border: 0.1em solid rgb(200 16 47 / 43%) !important;
            background: rgba(220, 38, 38, 0.09) !important;
            color: #dc2626 !important;
        }

        .pdp-price-row .pdp-stock-badge > i {
            display: block !important;
            font-size: 0.70rem !important;
            line-height: 1 !important;
            margin: 0 !important;
        }

        .pdp-price-row .pdp-stock-badge--out > i {
            color: #dc2626 !important;
        }

        .pdp-price-row .pdp-stock-label {
            display: block !important;
            font-size: 0.71rem !important;
            line-height: 1 !important;
            font-weight: 700 !important;
        }

        /* PDP Variation Options Section */
        .pdp-variants-wrapper {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 14px 16px !important;
            margin: 16px 0 !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }

        .pdp-variant-pill {
            border-radius: 10px !important;
            padding: 7px 16px !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            background: #ffffff !important;
            color: #000000 !important;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
            line-height: 1.35 !important;
            cursor: pointer !important;
            border: 1px solid #e2e8f0;
        }

        .pdp-variant-pill:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 6px rgba(15, 23, 42, 0.08) !important;
        }

        .pdp-variant-pill.active {
            background: #021962 !important;
            color: #ffffff !important;
        }

        .pdp-variant-pill.is-out-of-stock,
        .pdp-variant-pill:disabled {
            opacity: 0.7 !important;
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            box-shadow: none !important;
            transform: none !important;
        }

        @media (max-width: 575px) {
            .featured-pro-card .pcard-price-main-line > .featured-stock-pill,
            .selection-card .pcard-price-main-line > .featured-stock-pill {
                height: 15px !important;
                min-height: 15px !important;
                max-height: 15px !important;
                padding: 0 5px !important;
                font-size: 0.50rem !important;
            }

            .featured-pro-card .featured-stock-label,
            .selection-card .featured-stock-label {
                font-size: 0.50rem !important;
            }

            .featured-pro-card .pcard-price-main-line > .featured-stock-pill i,
            .selection-card .pcard-price-main-line > .featured-stock-pill i {
                font-size: 0.46rem !important;
            }
        }

        /* ===== Product Name / Title: Black (#000000) on all places ===== */
        .featured-pro-title,
        .featured-pro-title a,
        .featured-pro-card .featured-pro-title,
        .featured-pro-card .featured-pro-title a,
        .selection-card .featured-pro-title,
        .selection-card .featured-pro-title a,
        .pcard-name,
        .pcard-name a,
        .product-v2-name,
        .product-v2-name a,
        .popular-pro-title,
        .popular-pro-title a,
        .pdp-title,
        .pdp-info-panel .pdp-title,
        h1.pdp-title,
        .pro-result-title,
        .pro-result-title a,
        .search-result-name,
        .search-result-name a,
        .search-result-title,
        .search-result-title a,
        .cart-mobile-title,
        .cart-mobile-title a,
        .cart-product-title,
        .cart-product-title a,
        .cart-table a.text-dark,
        .mini-cart-item h6,
        .mini-cart-item h6 a,
        #miniCartItems h6,
        #miniCartItems h6 a,
        .checkout-product-title,
        .checkout-product-name,
        .checkout-item-title {
            color: #000000 !important;
        }

        .featured-pro-title a:hover,
        .pcard-name a:hover,
        .product-v2-name a:hover,
        .popular-pro-title a:hover,
        .pro-result-title a:hover,
        .cart-mobile-title a:hover,
        #miniCartItems h6 a:hover {
            color: #000000 !important;
            opacity: 0.8;
        }

        /* Current product price pure black on all product cards and product detail page */
        .pcard-price-current,
        .featured-pro-card .pcard-price-current,
        .selection-card .pcard-price-current,
        .pdp-price-main,
        .pdp-price-row .pdp-price-main,
        .pdp-price-group .pdp-price-main,
        .pdp-price-main-line .pdp-price-main {
            color: #000000 !important;
        }

        /* Navbar navigation links bold */
        .nav-link-item,
        nav .nav-link-item,
        .navbar .nav-link-item,
        #mobileNavDrawer .nav-link.fw-bold {
            font-weight: 700 !important;
        }

        /* Shop page hero background */
        .shop-hero {
            background: #000000 !important;
            background: linear-gradient(135deg, #000000, #910000) !important;

        }

        /* ================================================================
           INDH & PROJETS SOUTENUS - 100% REDESIGN
           ================================================================ */
        .state-funding-section {
            background: #ffffff !important;
            padding: 40px 0 !important;
        }

        .state-funding-wrapper {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .indh-hero-badge {
            background: rgba(220, 38, 38, 0.08);
            border: 1px solid rgba(220, 38, 38, 0.16);
            border-radius: 6px !important;
            padding: 4px 10px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .indh-hero-badge-text {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #dc2626;
        }

        .indh-hero-title {
            font-size: clamp(2rem, 3.6vw, 3rem) !important;
            font-weight: 900 !important;
            line-height: 1.12 !important;
            letter-spacing: -0.03em !important;
            color: #0f172a !important;
        }

        .indh-hero-sub {
            font-size: clamp(0.95rem, 1.35vw, 1.05rem) !important;
            line-height: 1.6 !important;
            color: #4b5563 !important;
            max-width: 520px;
        }

        .indh-hero-gear-img {
            max-height: 250px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 14px 28px rgba(15, 23, 42, 0.08));
            transition: transform 0.35s ease;
        }
        .indh-hero-gear-img:hover {
            transform: scale(1.02);
        }

        /* 3 Step Cards */
        .indh-step-card-pro {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 18px !important;
            padding: 0 !important;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
            height: 100% !important;
            position: relative !important;
            overflow: hidden !important;
            display: block !important;
        }
        .indh-step-card-pro:hover {
            transform: translateY(-4px) !important;
            border-color: rgba(220, 38, 38, 0.35) !important;
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08) !important;
        }

        .indh-step-badge-num {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            background: #dc2626 !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            font-size: 0.85rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3) !important;
            flex-shrink: 0 !important;
        }

        .indh-step-corner-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px !important;
            background: #dc2626;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .indh-step-title {
            font-size: 1.12rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            min-height: auto !important;
            margin-bottom: 0.4rem !important;
            display: block !important;
        }

        .indh-step-desc {
            font-size: 0.80rem !important;
            color: #64748b !important;
            line-height: 1.48 !important;
            min-height: auto !important;
            margin-bottom: 0.5rem !important;
        }

        .indh-step-btn-pro {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 7px 16px !important;
            border-radius: 999px !important;
            font-size: 0.80rem !important;
            font-weight: 750 !important;
            text-decoration: none !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            background: rgba(220, 38, 38, 0.06) !important;
            color: #dc2626 !important;
            border: 1px solid rgba(220, 38, 38, 0.22) !important;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.06) !important;
            cursor: pointer;
        }
        .indh-step-btn-pro .indh-btn-icon {
            font-size: 0.78rem !important;
            transition: transform 0.2s ease !important;
        }
        .indh-step-btn-pro .indh-btn-arrow {
            font-size: 0.65rem !important;
            transition: transform 0.2s ease !important;
        }
        .indh-step-btn-pro:hover {
            background: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 18px rgba(220, 38, 38, 0.28) !important;
        }
        .indh-step-btn-pro:hover .indh-btn-icon {
            transform: scale(1.1) !important;
        }
        .indh-step-btn-pro:hover .indh-btn-arrow {
            transform: translateX(3px) !important;
        }

        /* Step 3 WhatsApp Pro Button */
        .indh-step-btn-pro.btn-whatsapp-step {
            background: rgba(34, 197, 94, 0.08) !important;
            color: #15803d !important;
            border: 1px solid rgba(34, 197, 94, 0.3) !important;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.08) !important;
        }
        .indh-step-btn-pro.btn-whatsapp-step:hover {
            background: #16a34a !important;
            color: #ffffff !important;
            border-color: #16a34a !important;
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.3) !important;
        }

        .indh-step-img-col {
            position: relative;
            overflow: hidden;
        }
        .indh-step-img-col::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 28px;
            background: linear-gradient(to right, #ffffff, rgba(255, 255, 255, 0));
            z-index: 2;
            pointer-events: none;
        }

        .indh-step-card-img {
            width: 100%;
            height: 100%;
            min-height: 165px;
            object-fit: cover;
            object-position: center;
            display: block;
            transition: transform 0.35s ease;
        }
        .indh-step-card-pro:hover .indh-step-card-img {
            transform: scale(1.05);
        }

        .indh-step-arrow {
            position: absolute;
            right: -13px;
            top: 50%;
            transform: translateY(-50%);
            width: 26px;
            height: 26px;
            border-radius: 50% !important;
            background: #ffffff;
            border: 1px solid #fecaca;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.15);
            z-index: 3;
        }

        /* Guarantees Strip */
        .indh-guarantees-strip {
            background: #ffffff !important;
            border: 1px solid #eef2f6 !important;
            border-radius: 16px !important;
            padding: 14px 20px !important;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02) !important;
        }

        .indh-guarantee-pill {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            min-height: auto !important;
            padding: 6px 12px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        .indh-guarantee-pill:hover {
            transform: none !important;
            box-shadow: none !important;
            background: transparent !important;
            border-color: transparent !important;
        }

        .indh-guarantee-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 10px !important;
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .indh-guarantee-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }

        .indh-guarantee-desc {
            font-size: 0.76rem;
            color: #64748b;
            line-height: 1.3;
        }

        .indh-guarantee-divider {
            border-right: 1px solid #f1f5f9;
        }
        @media (max-width: 1199px) {
            .indh-guarantee-divider {
                border-right: none;
            }
        }

        /* Dark Bottom Banner */
        .indh-banner-cta {
            background: #0d131f !important;
            background: radial-gradient(circle at 95% 15%, rgba(220, 38, 38, 0.22) 0%, transparent 45%),
                        radial-gradient(circle at 10% 85%, rgba(34, 197, 94, 0.1) 0%, transparent 40%),
                        linear-gradient(135deg, #090e17 0%, #0d131f 50%, #131b2e 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 18px !important;
            padding: clamp(22px, 3.2vw, 32px) clamp(20px, 3.5vw, 38px) !important;
            position: relative !important;
            overflow: hidden !important;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
        }
        .indh-banner-cta::before {
            display: none !important;
        }

        .indh-banner-gear-img {
            max-height: 95px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.5));
        }

        .indh-banner-badge {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%) !important;
            color: #ffffff !important;
            font-size: 0.68rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.08em !important;
            padding: 4px 12px !important;
            border-radius: 50px !important;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.4) !important;
            text-transform: uppercase;
        }

        .indh-banner-title {
            font-size: clamp(1.18rem, 2.1vw, 1.45rem) !important;
            font-weight: 800 !important;
            color: #ffffff !important;
            line-height: 1.28 !important;
            letter-spacing: -0.02em !important;
        }

        .indh-banner-desc {
            font-size: clamp(0.82rem, 1.1vw, 0.88rem) !important;
            color: rgba(255, 255, 255, 0.8) !important;
            line-height: 1.55 !important;
            max-width: 620px;
        }

        .indh-wa-banner-btn {
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.16) !important;
            border-radius: 14px !important;
            padding: 9px 16px !important;
            color: #ffffff !important;
            text-decoration: none !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
            width: 100%;
        }
        .indh-wa-banner-btn:hover {
            background: rgba(34, 197, 94, 0.18) !important;
            border-color: rgba(34, 197, 94, 0.5) !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.28) !important;
        }
        .indh-wa-banner-btn:hover .indh-wa-btn-arrow {
            transform: translateX(3px) scale(1.1) !important;
            color: #22c55e !important;
        }
        .indh-wa-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50% !important;
            background: #22c55e;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 2px 10px rgba(34, 197, 94, 0.4);
            flex-shrink: 0;
            position: relative;
        }
        .indh-wa-pulse-ring {
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 50%;
            border: 2px solid rgba(34, 197, 94, 0.7);
            animation: indh-wa-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes indh-wa-pulse {
            0% { transform: scale(0.95); opacity: 0.9; }
            50% { transform: scale(1.28); opacity: 0; }
            100% { transform: scale(0.95); opacity: 0; }
        }
        .indh-wa-btn-label {
            font-size: 0.88rem !important;
            font-weight: 750 !important;
            line-height: 1.25 !important;
            color: #ffffff !important;
        }
        .indh-wa-btn-status {
            font-size: 0.72rem !important;
            color: rgba(255, 255, 255, 0.75) !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .indh-wa-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 8px #22c55e;
            display: inline-block;
        }
        .indh-wa-btn-arrow {
            font-size: 0.8rem !important;
            color: rgba(255, 255, 255, 0.5) !important;
            transition: transform 0.2s ease, color 0.2s ease !important;
            flex-shrink: 0;
        }

        .indh-banner-cta-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        @media (min-width: 576px) {
            .indh-banner-cta-actions {
                flex-direction: row;
                justify-content: flex-start;
                gap: 12px;
            }
        }
        @media (min-width: 992px) {
            .indh-banner-cta-actions {
                justify-content: flex-end;
            }
        }

        .indh-btn-choose,
        .indh-btn-devis {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 9px !important;
            white-space: nowrap !important;
            border-radius: 12px !important;
            padding: 12px 22px !important;
            font-weight: 750 !important;
            font-size: 0.85rem !important;
            text-decoration: none !important;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .indh-btn-choose {
            background: #dc2626 !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35) !important;
        }
        .indh-btn-choose:hover {
            background: #b91c1c !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 22px rgba(220, 38, 38, 0.45) !important;
        }
        .indh-btn-choose:hover .fa-chevron-right,
        .indh-btn-devis:hover .fa-chevron-right {
            transform: translateX(3px) !important;
        }
        .indh-btn-choose .fa-chevron-right,
        .indh-btn-devis .fa-chevron-right {
            transition: transform 0.2s ease !important;
        }

        .indh-btn-devis {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #ffffff !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
        }
        .indh-btn-devis:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 22px rgba(255, 255, 255, 0.35) !important;
        }

        @media (max-width: 575px) {
            .indh-step-img-col {
                display: none !important;
            }
            .indh-step-card-pro .col-7 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
            .indh-banner-cta {
                padding: 20px 16px !important;
            }
            .indh-banner-cta .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .indh-banner-cta [class*="col-"] {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }

        /* RTL adjustments */
        html[dir="rtl"] .indh-step-arrow {
            right: auto;
            left: -13px;
            transform: translateY(-50%) rotate(180deg);
        }
        html[dir="rtl"] .indh-step-img-col::before {
            left: auto;
            right: 0;
            background: linear-gradient(to left, #ffffff, rgba(255, 255, 255, 0));
        }
        html[dir="rtl"] .indh-guarantee-divider {
            border-right: none;
            border-left: 1px solid #f1f5f9;
        }
        @media (max-width: 1199px) {
            html[dir="rtl"] .indh-guarantee-divider {
                border-left: none;
            }
        }
        html[dir="rtl"] .indh-step-btn-pro .indh-btn-arrow,
        html[dir="rtl"] .indh-wa-banner-btn .indh-wa-btn-arrow,
        html[dir="rtl"] .indh-btn-choose i.fa-chevron-right,
        html[dir="rtl"] .indh-btn-devis i.fa-chevron-right {
            transform: rotate(180deg);
        }
    </style>
    <!-- Custom Head & Body Codes (Cached) -->
    @php
        $activeCustomCodes = \Illuminate\Support\Facades\Cache::remember('active_custom_codes_v1', 86400, function() {
            return \App\Models\CustomCode::where('is_active', true)->orderBy('priority', 'desc')->get();
        });
        $headCodes = $activeCustomCodes->where('position', 'head');
        $bodyStartCodes = $activeCustomCodes->where('position', 'body_start');
        $bodyEndCodes = $activeCustomCodes->where('position', 'body_end');
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
    @stack('styles')
</head>
<body class="{{ request()->routeIs('cart.*') ? 'cart-page' : '' }} {{ request()->routeIs('checkout.*') ? 'checkout-page' : '' }}">
    <!-- Custom Body Start Codes -->
    @foreach($bodyStartCodes as $code)
        {!! $code->content !!}
    @endforeach



    <!-- Main Header / Navbar (Single Clean Row) -->
    <div class="header-main sticky-top bg-white shadow-sm w-100" style="position: sticky !important; top: 0 !important; z-index: 1040; border-bottom: 1px solid #f1f3f5;">
        <div class="container py-0 py-lg-0">
            <div class="d-flex align-items-center justify-content-between gap-3 gap-xl-4">
                
                <!-- Left: Logo + Categories Button + Navigation Links on its right -->
                <div class="d-flex align-items-center gap-2 gap-lg-3 gap-xl-4 flex-nowrap">
                    <!-- Brand Logo (Enlarged) -->
                    <a class="navbar-brand m-0 p-0 d-flex align-items-center me-1 me-lg-2" href="{{ url('/') }}" style="flex-shrink: 0;" title="{{ setting('app_name', 'WINA SHOP') }}" aria-label="{{ setting('app_name', 'WINA SHOP') }} - {{ __('Accueil') }}">
                        <img src="{{ app_logo_url() }}" alt="{{ setting('app_name', 'WINA SHOP') }}" width="96" height="96" class="brand-logo-img" style="height: 96px; max-height: 105px; width: auto; max-width: 280px; object-fit: contain;">
                        <span class="visually-hidden">{{ setting('app_name', 'WINA SHOP') }}</span>
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
                                <a href="{{ route('shop.index') }}" class="btn btn-categories-red py-1 px-2 text-white fw-bold text-decoration-none d-flex align-items-center gap-1 hover-underline" style="font-size: 12px;" id="allCategoriesLinkNavbar">
                                    <span>{{ __('Tout le produits') }}</span>
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
                                                                    <img src="{{ $catImg }}" alt="{{ $cat->name }}" width="30" height="30" loading="lazy" decoding="async" class="cat-slot-media" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                                                    <i class="fas fa-folder cat-slot-icon" style="display: none;"></i>
                                                                @else
                                                                    <i class="{{ $catIconClass }} cat-slot-icon"></i>
                                                                @endif
                                                            </span>
                                                            <span class="cat-mega-name">{{ $cat->name }}</span>
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
                <div class="d-flex align-items-center gap-2 gap-md-3 gap-xl-4 flex-grow-1 justify-content-end header-actions-wrap" style="max-width: 580px;">
                    
                    <!-- Mobile Menu Toggler -->
                    <button class="navbar-toggler border-0 p-1 d-lg-none text-dark ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavDrawer" aria-controls="mobileNavDrawer" aria-label="Menu">
                        <i class="fas fa-bars" style="font-size: 20px;"></i>
                    </button>    
                
                    <!-- Search Input (Pill shape #f1f3f5) -->
                    <form action="{{ route('shop.index') }}" method="GET" class="header-search-wrap w-100 position-relative" style="max-width: 380px;" id="headerSearchForm" autocomplete="off">
                        <div class="position-relative d-flex align-items-center header-search-box" style="background: #f1f3f5; border-radius: 9999px; padding: 0 14px; height: 42px;">
                            <i class="fas fa-search text-muted me-2" style="font-size: 13px;"></i>
                            <input class="form-control border-0 bg-transparent shadow-none p-0 header-search-input" 
                                   type="search" 
                                   name="q" 
                                   id="headerSearchInput" 
                                   placeholder="{{ __('Rechercher...') }}" 
                                   aria-label="{{ __('Search') }}" 
                                   value="{{ request('q') }}" 
                                   autocomplete="off" 
                                   spellcheck="false"
                                   style="font-size: 16px; color: #334155;">
                            <button class="btn btn-search-clear d-none p-0 border-0 bg-transparent text-muted" type="button" id="headerSearchClear" aria-label="{{ __('Effacer la recherche') }}">
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

                    <!-- Dummy button to preserve any JS bindings -->
                    <button class="d-none" type="button" id="mobileSearchTriggerBtn" aria-hidden="true"></button>

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
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Offcanvas Drawer -->
    <div class="offcanvas offcanvas-start border-0 shadow-lg d-lg-none" tabindex="-1" id="mobileNavDrawer" aria-labelledby="mobileNavDrawerLabel" style="width: 310px;">
        <div class="offcanvas-header border-bottom py-3">
            <a href="{{ url('/') }}" class="navbar-brand m-0" aria-label="{{ setting('app_name', 'WINA SHOP') }} - {{ __('Accueil') }}" title="{{ setting('app_name', 'WINA SHOP') }}">
                <img src="{{ app_logo_url() }}" alt="{{ setting('app_name', 'WINA SHOP') }}" width="70" height="70" style="height: 70px; width: auto; object-fit: contain;">
                <span class="visually-hidden">{{ setting('app_name', 'WINA SHOP') }}</span>
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
                                        <img src="{{ $catImg }}" alt="{{ $cat->name }}" width="30" height="30" loading="lazy" decoding="async" class="cat-slot-media" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                        <i class="fas fa-folder cat-slot-icon" style="display: none;"></i>
                                    @else
                                        <i class="{{ $catIconClass }} cat-slot-icon"></i>
                                    @endif
                                </span>
                                <span class="cat-mega-name">{{ $cat->name }}</span>
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
                                        <img src="{{ $cat->thumbnail }}" alt="" width="36" height="36" loading="lazy" decoding="async" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
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
                <p class="pro-empty-title h6 mb-1 fw-bold">{{ __('No product found') }}</p>
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
            <div class="offcanvas-title fw-bold font-heading h5 mb-0" id="miniCartLabel">
                <i class="fas fa-shopping-bag me-2 text-primary"></i>{{ __('My Cart') }}
            </div>
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
                                <img src="{{ $miniImg }}" alt="{{ $details['name'] }}" width="80" height="80" class="rounded-3 object-fit-cover" style="width: 80px; height: 80px;">
                                <span class="position-absolute top-0 {{ app()->getLocale() === 'ar' ? 'end-0' : 'start-0' }} translate-middle badge rounded-pill bg-light text-dark border shadow-sm" style="font-size: 0.7rem;">x{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0 overflow-hidden">
                                <div class="fw-bold mb-1 text-truncate h6 {{ app()->getLocale() === 'ar' ? 'ps-4' : 'pe-4' }}" title="{{ $details['name'] }}">{{ $details['name'] }}</div>
                                <p class="mb-2 text-muted small">{{ $details['category_name'] ?? __('Product') }}</p>
                                
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <span class="text-primary fw-bold" style="font-size: 1.1rem;">{{ currency($details['price']) }}</span>
                                    
                                    <div class="quantity-control bg-light rounded-pill d-flex align-items-center px-1 border">
                                        <button class="btn btn-sm btn-link text-dark text-decoration-none p-1 border-0" onclick="updateQty({{ $id }}, {{ $details['quantity'] - 1 }})" aria-label="{{ __('Diminuer la quantité') }}">
                                            <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="{{ $details['quantity'] }}" readonly style="width: 30px;" aria-label="{{ __('Quantité') }}">
                                        <button class="btn btn-sm btn-link text-dark text-decoration-none p-1 border-0" onclick="updateQty({{ $id }}, {{ $details['quantity'] + 1 }})" aria-label="{{ __('Augmenter la quantité') }}">
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
                        <div class="fw-bold text-dark h5 mb-2">{{ __('Your cart is empty') }}</div>
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
    @if(!request()->routeIs('checkout.*') && !request()->routeIs('cart.*'))
    @php
        $cartSession = session('cart', []);
        $cartInitialCount = array_sum(array_column($cartSession, 'quantity'));
    @endphp
    <style>
        .floating-checkout-wrap,
        #floatingCheckoutWrap,
        .floating-checkout-wrap.is-visible,
        .floating-checkout-btn,
        #floatingCheckoutBtn {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
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

        /* Ensure SweetAlert2 confirmation modals and popups render above #miniCart & offcanvas */
        .swal2-container {
            z-index: 2000000 !important;
        }

        /* Completely hide floating checkout and WhatsApp when miniCart or offcanvas is open */
        body.mini-cart-open .floating-checkout-wrap,
        body.mini-cart-open .whatsapp-float,
        body:has(#miniCart.show) .floating-checkout-wrap,
        body:has(#miniCart.show) .whatsapp-float,
        body:has(#miniCart.showing) .floating-checkout-wrap,
        body:has(#miniCart.showing) .whatsapp-float,
        body:has(.offcanvas.show) .floating-checkout-wrap,
        body:has(.offcanvas.show) .whatsapp-float,
        body.cart-page .floating-checkout-wrap,
        body.checkout-page .floating-checkout-wrap,
        body:has(.cart-page) .floating-checkout-wrap {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            display: none !important;
            transform: translateY(20px) scale(0.85) !important;
        }
        body.has-whatsapp-float .floating-checkout-wrap.is-visible,
        body:has(.whatsapp-float) .floating-checkout-wrap.is-visible {
            bottom: 96px !important;
            right: 24px !important;
            left: auto !important;
        }
        html[dir="rtl"] .floating-checkout-wrap {
            right: auto !important;
            left: 24px !important;
        }
        html[dir="rtl"] body.has-whatsapp-float .floating-checkout-wrap.is-visible,
        html[dir="rtl"] body:has(.whatsapp-float) .floating-checkout-wrap.is-visible {
            bottom: 96px !important;
            left: 24px !important;
            right: auto !important;
        }
        @media (max-width: 768px) {
            .floating-checkout-wrap,
            #floatingCheckoutWrap,
            .floating-checkout-wrap.is-visible,
            .floating-checkout-btn,
            #floatingCheckoutBtn {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }
            body.has-whatsapp-float .floating-checkout-wrap.is-visible,
            body:has(.whatsapp-float) .floating-checkout-wrap.is-visible {
                bottom: calc(82px + env(safe-area-inset-bottom, 0px)) !important;
                right: 16px !important;
                left: auto !important;
            }
            html[dir="rtl"] .floating-checkout-wrap {
                left: 16px !important;
                right: auto !important;
            }
            html[dir="rtl"] body.has-whatsapp-float .floating-checkout-wrap.is-visible,
            html[dir="rtl"] body:has(.whatsapp-float) .floating-checkout-wrap.is-visible {
                bottom: calc(82px + env(safe-area-inset-bottom, 0px)) !important;
                left: 16px !important;
                right: auto !important;
            }
            .floating-checkout-btn {
                padding: 10px 16px 10px 12px !important;
                gap: 10px !important;
                box-shadow: 0 10px 28px rgba(0, 0, 0, 0.55), 0 0 18px rgba(220, 38, 38, 0.45) !important;
            }
            .floating-checkout-label {
                font-size: 0.88rem !important;
                font-weight: 400 !important;
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

        $appName = setting('app_name', 'WINA SHOP');
        $currentLocale = app()->getLocale();
        $isProductPage = (request()->routeIs('shop.show') || trim($__env->yieldContent('meta_type')) === 'product') && isset($product) && $product instanceof \App\Models\Product;

        if ($isProductPage) {
            $productUrl = route('shop.show', $product->slug ?: $product->id);
            if ($currentLocale === 'ar') {
                $waMessage = "مرحبا {$appName}، أود الاستفسار عن هذا المنتج:\n{$product->name}\n{$productUrl}";
            } elseif ($currentLocale === 'en') {
                $waMessage = "Hello {$appName}, I would like more information about this product:\n{$product->name}\n{$productUrl}";
            } else {
                $waMessage = "Bonjour {$appName}, je souhaite me renseigner sur ce produit :\n{$product->name}\n{$productUrl}";
            }
        } else {
            if ($currentLocale === 'ar') {
                $waMessage = "مرحبا {$appName}، أود الاستفسار عن منتجاتكم.";
            } elseif ($currentLocale === 'en') {
                $waMessage = "Hello {$appName}, I would like to inquire about your products.";
            } else {
                $waMessage = 'Bonjour Wina Shop, je souhaite me renseigner sur vos produits.';
            }
        }
    @endphp
    <a href="https://wa.me/{{ $waCleanPhone }}?text={{ rawurlencode($waMessage) }}" 
       class="whatsapp-float" 
       target="_blank" 
       rel="noopener noreferrer" 
       aria-label="WhatsApp - Service Client Wina Shop" 
       title="WhatsApp - Service Client Wina Shop">
        <span class="whatsapp-float-pulse"></span>
        <span class="whatsapp-online-dot"></span>
        <svg viewBox="0 0 32 32" class="whatsapp-float-svg" width="30" height="30" fill="currentColor" aria-hidden="true">
            <path d="M16.002 0.007C7.168 0.007 0 7.175 0 16.008c0 2.825 0.738 5.578 2.141 7.999L0.086 31.914l8.13-2.052c2.342 1.282 4.978 1.956 7.786 1.956 8.834 0 16.002-7.168 16.002-16.01S24.836 0.007 16.002 0.007zm0 29.317c-2.484 0-4.912-0.669-7.037-1.936l-0.505-0.3-5.234 1.321 1.398-5.093-0.33-0.526C3.003 20.672 2.302 18.384 2.302 16.008c0-7.555 6.145-13.7 13.7-13.7 7.555 0 13.7 6.145 13.7 13.7 0 7.555-6.145 13.7-13.7 13.7zm7.51-10.252c-0.412-0.206-2.438-1.203-2.816-1.34-0.378-0.137-0.653-0.206-0.927 0.206s-1.065 1.34-1.305 1.615c-0.24 0.275-0.481 0.309-0.893 0.103-0.412-0.206-1.741-0.642-3.316-2.046-1.226-1.093-2.054-2.443-2.294-2.855-0.24-0.412-0.026-0.635 0.18-0.84 0.186-0.185 0.412-0.481 0.618-0.721 0.206-0.24 0.275-0.412 0.412-0.687 0.137-0.275 0.069-0.515-0.034-0.721s-0.927-2.233-1.27-3.057c-0.335-0.803-0.675-0.694-0.927-0.707l-0.79-0.014c-0.275 0-0.721 0.103-1.099 0.515s-1.443 1.409-1.443 3.435 1.477 3.985 1.683 4.26c0.206 0.275 2.907 4.439 7.042 6.225 0.984 0.425 1.753 0.679 2.352 0.869 0.988 0.314 1.888 0.27 2.6 0.164 0.794-0.119 2.438-0.996 2.781-1.958 0.344-0.962 0.344-1.786 0.24-1.958-0.103-0.172-0.378-0.275-0.79-0.481z"/>
        </svg>
        <span class="whatsapp-float-tooltip" aria-hidden="true" style="display: none;">
            <span style="color: #ffffff;">WhatsApp</span>
            <small style="color: #4ade80;">En ligne</small>
        </span>
    </a>

    <footer class="footer-modern">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <a href="{{ url('/') }}" class="d-inline-block" title="{{ setting('app_name', 'WINA SHOP') }}" aria-label="{{ setting('app_name', 'WINA SHOP') }}">
                            <img src="{{ app_logo_url() }}" alt="{{ setting('app_name', 'WINA SHOP') }}" width="56" height="56" style="height: 56px; width: auto; object-fit: contain;">
                        </a>
                        <p class="fw-bold text-white mb-0 text-uppercase ls-1 d-flex align-items-center gap-2 h5">
                            <span class="tally-dot"></span> {{ setting('app_name', 'WINA SHOP') }}
                        </p>
                    </div>
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
                        <a href="{{ $sfb }}" target="_blank" rel="noopener" class="footer-social-btn" title="Facebook" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        @endif
                        @if($validUrl($stw))
                        <a href="{{ $stw }}" target="_blank" rel="noopener" class="footer-social-btn" title="Twitter / X" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        @endif
                        @if($validUrl($sig))
                        <a href="{{ $sig }}" target="_blank" rel="noopener" class="footer-social-btn" title="Instagram" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if($validUrl($sli))
                        <a href="{{ $sli }}" target="_blank" rel="noopener" class="footer-social-btn" title="LinkedIn" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        @endif
                        @if($validUrl($swa))
                        @php
                            $waFooterLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $swa);
                        @endphp
                        <a href="{{ $waFooterLink }}" target="_blank" rel="noopener" class="footer-social-btn footer-social-btn--wa" title="WhatsApp" aria-label="WhatsApp">
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
                    <p class="fw-bold text-white mb-4 text-uppercase ls-1 h6">{{ __('Shop') }}</p>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('shop.index') }}" class="footer-link small">{{ __('Featured Products') }}</a></li>
                        <li><a href="{{ route('about') }}" class="footer-link small">{{ __('About') }} {{ setting('app_name', 'WINA SHOP') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="footer-link small">{{ __('Contact & Showroom') }}</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-6">
                    <p class="fw-bold text-white mb-4 text-uppercase ls-1 h6">{{ __('Customer Service') }}</p>
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
                    <p class="small text-center mb-0">&copy; {{ date('Y') }} {{ setting('app_name', 'WINA SHOP') }}. {{ __('All rights reserved') }}.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                once: true,
                offset: 40
            });
        }

        // Mini Cart & Cart Page Functions
        window.updateFloatingCheckout = function(count, total) {
            const wrap = document.getElementById('floatingCheckoutWrap');
            const badge = document.getElementById('floatingCheckoutCount');
            const numCount = parseInt(count) || 0;

            if (badge) {
                badge.textContent = numCount;
            }

            if (wrap) {
                wrap.classList.remove('is-visible');
                wrap.style.display = 'none';
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
                wrap.classList.remove('is-visible');
                wrap.style.display = 'none';
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
                    floatingCheckout.classList.remove('is-visible');
                    floatingCheckout.style.setProperty('display', 'none', 'important');
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
            const buttons = document.querySelectorAll(`button[data-product-id="${numId}"]:not(.pcard-quick-cart-btn):not(.pcard-overlay-btn):not(.pdp-add-btn)`);
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
                    b.innerHTML = '<span class="pcard-quick-cart-label">Dans le panier</span>';
                    b.setAttribute('title', inCartLabel);
                    b.setAttribute('aria-label', inCartLabel);
                } else {
                    b.classList.remove('is-in-cart');
                    b.innerHTML = '<span class="pcard-quick-cart-label">Ajouter</span>';
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

            // 4. Product Details Page (PDP) Add to Cart Button
            const pdpButtons = document.querySelectorAll(`.pdp-add-btn[data-product-id="${numId}"], #addToCartBtn[data-product-id="${numId}"]`);
            pdpButtons.forEach(b => {
                b.disabled = false;
                if (inCart) {
                    b.classList.add('is-in-cart');
                    b.innerHTML = `<i class="fas fa-check me-2"></i> <span id="addToCartText">${inCartLabel}</span>`;
                    b.setAttribute('title', inCartLabel);
                } else {
                    b.classList.remove('is-in-cart');
                    b.innerHTML = `<i class="fas fa-cart-plus me-2"></i> <span id="addToCartText">${notInCartLabel}</span>`;
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

        window.addToCart = addToCart = function(productId, quantity = 1, triggerEl = null, force = false, variantId = null) {
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

            if (isAlreadyInCart && !force && !variantId) {
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
                            window.addToCart(productId, quantity, btn, true, variantId);
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
                    window.addToCart(productId, quantity, btn, true, variantId);
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

            const requestPayload = { quantity: parseInt(quantity) || 1 };
            if (variantId) {
                requestPayload.variant_id = variantId;
            }

            return fetch(`{{ url('/cart/add') }}/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestPayload)
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
            const inputs = document.querySelectorAll(`input[id="cart-item-qty-${id}"], input[id="cart-item-qty-mobile-${id}"], .cart-item-qty-${id}`);
            let currentQty = 1;
            if (inputs.length > 0) {
                currentQty = parseInt(inputs[0].value) || 1;
            } else {
                const fallback = document.getElementById('cart-item-qty-' + id);
                if (fallback) currentQty = parseInt(fallback.value) || 1;
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

                // Update /cart page elements if present (both desktop and mobile)
                document.querySelectorAll(`input[id="cart-item-qty-${id}"], input[id="cart-item-qty-mobile-${id}"], .cart-item-qty-${id}`).forEach(inp => {
                    inp.value = data.quantity;
                });
                document.querySelectorAll(`[id="cart-item-total-${id}"], [id="cart-item-total-mobile-${id}"], .cart-item-total-${id}`).forEach(el => {
                    if (data.itemTotal) el.textContent = data.itemTotal;
                });
                const unitHint = document.getElementById(`cart-item-unit-hint-mobile-${id}`);
                if (unitHint) {
                    if (data.quantity > 1) {
                        unitHint.classList.remove('d-none');
                    } else {
                        unitHint.classList.add('d-none');
                    }
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

                        // Update /cart header badge if present
                        const headerBadge = document.getElementById('cart-header-badge');
                        if (headerBadge && data.cartCount !== undefined) {
                            headerBadge.textContent = `${data.cartCount} ${data.cartCount > 1 ? '{{ __('articles') }}' : '{{ __('article') }}'}`;
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

                        // If on /cart page, remove the rows (both desktop & mobile)
                        const rows = document.querySelectorAll(`[id="cart-row-${id}"], [id="cart-row-mobile-${id}"], .cart-row-${id}`);
                        if (rows.length > 0) {
                            rows.forEach(r => {
                                r.style.transition = 'opacity 0.25s, transform 0.25s';
                                r.style.opacity = '0';
                                r.style.transform = 'scale(0.95)';
                                setTimeout(() => r.remove(), 250);
                            });
                            setTimeout(() => {
                                const remainingDesktop = document.querySelectorAll('tr[id^="cart-row-"]').length;
                                const remainingMobile = document.querySelectorAll('div[id^="cart-row-mobile-"]').length;
                                if (data.isEmpty || (remainingDesktop === 0 && remainingMobile === 0)) {
                                    window.location.reload();
                                }
                            }, 280);
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
                const escaped = escapeHtml(text);
                const expanded = query.replace(/(\d)([a-zA-Z])/g, '$1 $2').replace(/([a-zA-Z]+)(\d+)/g, '$1 $2');
                let words = (query + ' ' + expanded)
                    .split(/\s+/)
                    .map(w => w.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&'))
                    .filter(w => w.length > 0);
                words = Array.from(new Set(words));
                if (words.length === 0) return escaped;
                words.sort((a, b) => b.length - a.length);
                const regex = new RegExp('(' + words.join('|') + ')', 'gi');
                return escaped.replace(regex, '<mark class="search-highlight">$1</mark>');
            }

            function openDropdown() {
                dropdown.classList.remove('d-none');
                document.body.classList.add('header-search-open');
            }

            function closeDropdown() {
                dropdown.classList.add('d-none');
                document.body.classList.remove('header-search-open');
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
                        const currentPrice = p.is_sale ? p.sale_price : p.price;
                        const stockHtml = p.in_stock 
                            ? `<span class="search-result-stock">{{ __('In stock') }}</span>`
                            : `<span class="search-result-stock out">{{ __('Out of stock') }}</span>`;

                        const oldPriceHtml = p.is_sale 
                            ? `<span class="search-result-price-old">${escapeHtml(p.price)}</span>`
                            : '';

                        const priceHtml = `
                            <div class="search-result-price-wrap">
                                <div class="search-result-price-main-line">
                                    <span class="search-result-price">${escapeHtml(currentPrice)}</span>
                                    ${stockHtml}
                                </div>
                                ${oldPriceHtml}
                            </div>
                        `;

                        html += `
                            <a href="${p.url}" class="search-result-item" data-index="${idx}">
                                <img src="${p.image}" alt="${escapeHtml(p.name)}" width="44" height="44" class="search-result-thumb" loading="lazy">
                                <div class="search-result-info">
                                    <div class="search-result-cat">${escapeHtml(p.category)}</div>
                                    <div class="search-result-name">${highlightText(p.name, query)}</div>
                                    <div class="search-result-meta">
                                        ${priceHtml}
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
                        const currentPrice = p.is_sale ? p.sale_price : p.price;
                        const stockHtml = p.in_stock 
                            ? `<span class="pro-result-stock">{{ __('In stock') }}</span>`
                            : `<span class="pro-result-stock out">{{ __('Out of stock') }}</span>`;

                        const oldPriceHtml = p.is_sale 
                            ? `<span class="pro-result-price-old">${escapeHtml(p.price)}</span>`
                            : '';

                        const priceHtml = `
                            <div class="pro-result-price-wrap">
                                <div class="pro-result-price-main-line">
                                    <span class="pro-result-price">${escapeHtml(currentPrice)}</span>
                                    ${stockHtml}
                                </div>
                                ${oldPriceHtml}
                            </div>
                        `;

                        html += `
                            <a href="${p.url}" class="pro-result-card">
                                <img src="${p.image}" alt="${escapeHtml(p.name)}" width="62" height="62" class="pro-result-thumb" loading="lazy">
                                <div class="pro-result-info">
                                    <span class="pro-result-cat">${escapeHtml(p.category)}</span>
                                    <div class="pro-result-title">${highlightText(p.name, query)}</div>
                                    <div class="pro-result-price-row">
                                        ${priceHtml}
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
    @if(isset($bodyEndCodes))
    @foreach($bodyEndCodes as $code)
        @if($code->type == 'css')
            <style>{!! $code->content !!}</style>
        @elseif($code->type == 'js')
            <script>{!! $code->content !!}</script>
        @else
            {!! $code->content !!}
        @endif
    @endforeach
    @endif
</body>
</html>
