<!DOCTYPE html>
<html lang="{{ setting('language', 'fr') }}" dir="{{ setting('text_direction', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', setting('app_name', 'Coopérative Aït Oumdis'))</title>
    <meta name="description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta name="keywords" content="@yield('meta_keywords', setting('app_name', 'boutique') . ', e-commerce, Maroc, acheter en ligne, livraison Maroc')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="author" content="{{ setting('app_name', 'Coopérative Aït Oumdis') }}">
    <meta name="developer" content="Elegant Boost (https://elegantboost.com/)">
    <meta name="designer" content="Elegant Boost">
    <meta name="theme-color" content="#0c261e">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Preconnect to external resources for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Favicon -->
    <link rel="icon" href="{{ app_favicon_url() }}">
    <link rel="apple-touch-icon" href="{{ app_favicon_url() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('meta_type', 'website')">
    <meta property="og:site_name" content="{{ setting('app_name', 'Coopérative Aït Oumdis') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', setting('app_name', 'Coopérative Aït Oumdis'))">
    <meta property="og:description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta property="og:image" content="@yield('meta_image', app_logo_url('images/og-default.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ setting('language', 'fr') === 'ar' ? 'ar_MA' : 'fr_MA' }}">
    <meta property="og:updated_time" content="{{ now()->toIso8601String() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('meta_title', setting('app_name', 'Coopérative Aït Oumdis'))">
    <meta name="twitter:description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta name="twitter:image" content="@yield('meta_image', app_logo_url('images/og-default.jpg'))">    
    <meta name="twitter:site" content="@yield('twitter_site', '@' . str_replace(' ', '', setting('app_name', 'Coopérative Aït Oumdis')))">
    
    <!-- JSON-LD Structured Data Schema -->
    @yield('json_ld')
    
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600;1,700&family=Cinzel:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sunpure-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
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
<body class="{{ request()->routeIs('checkout.*') ? 'checkout-page' : '' }}">
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

    <!-- ==========================================================================
       SUNPURE LUXURY HEADER
       ========================================================================== -->
  <header class="sp-header {{ !request()->routeIs('home') ? 'sp-header-solid' : '' }}" id="spHeader">
    <div class="sp-nav-container">
      <a href="{{ route('home') }}" class="sp-logo-link" aria-label="Accueil Coopérative Aït Oumdis">
        <img src="{{ app_logo_url() }}" alt="Logo {{ setting('app_name', 'Aït Oumdis') }}" class="sp-logo-symbol">
        <div class="sp-logo-text-wrap">
          <span class="sp-logo-main">AIT OUMDIS</span>
          <span class="sp-logo-sub">COOPERATIVE</span>
        </div>
      </a>

      <ul class="sp-nav-menu" id="spNavMenu">
        <li><a href="{{ route('home') }}" class="sp-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
        <li><a href="{{ route('shop.index') }}" class="sp-nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">Boutique</a></li>
        <li><a href="{{ route('home') }}#a-propos" class="sp-nav-link">À propos</a></li>
        <li><a href="{{ route('home') }}#engagements-bio" class="sp-nav-link">Nos engagements</a></li>
      </ul>

      <div class="sp-nav-actions">
        <!-- Bouton Panier -->
        <button class="sp-nav-action-btn cart-toggle-btn {{ request()->routeIs('cart.*') ? 'active' : '' }}" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart" title="Voir mon panier" aria-label="Ouvrir le panier" style="background: transparent; border: none; cursor: pointer; padding: 0;">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="sp-cart-count" id="header-cart-count">{{ count(session('cart', [])) }}</span>
        </button>

        <!-- Bouton Menu Mobile -->
        <button class="sp-mobile-menu-btn" id="spMobileMenuBtn" aria-label="Menu mobile" type="button">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
  </header>

  <main>
        @yield('content')
    </main>

    <!-- Offcanvas Mini Cart -->
    <!-- ====== MINI CART OFFCANVAS (redesigned) ====== -->
    <style>
    #miniCart {
        width: 420px;
        max-width: 100vw;
        background: #faf9f5;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    @media (max-width: 480px) {
        #miniCart { width: 100vw; }
    }
    .mc-header {
        background: #0c261e;
        color: #ffffff;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .mc-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
    }
    .mc-header-title svg { color: #e2ad50; }
    .mc-close-btn {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: #ffffff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .mc-close-btn:hover { background: rgba(255,255,255,0.2); }
    .mc-items-wrap {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .mc-item {
        background: #ffffff;
        border-radius: 16px;
        padding: 14px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        position: relative;
        border: 1px solid rgba(12,38,30,0.06);
        box-shadow: 0 2px 12px rgba(12,38,30,0.04);
    }
    .mc-item-img-wrap {
        position: relative;
        flex-shrink: 0;
        width: 72px;
        height: 72px;
    }
    .mc-item-img {
        width: 72px;
        height: 72px;
        border-radius: 10px;
        object-fit: cover;
        background: #fbf9f4;
        display: block;
    }
    .mc-item-img-placeholder {
        width: 72px;
        height: 72px;
        border-radius: 10px;
        background: linear-gradient(135deg, #fbf9f4 0%, #f0ebe0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c28d32;
        flex-shrink: 0;
    }
    .mc-qty-badge {
        position: absolute;
        top: -6px; left: -6px;
        width: 22px; height: 22px;
        border-radius: 50%;
        background: #0c261e;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #faf9f5;
    }
    .mc-item-body {
        flex: 1;
        min-width: 0;
    }
    .mc-item-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0c261e;
        line-height: 1.3;
        margin-bottom: 3px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        padding-right: 24px;
    }
    .mc-item-cat {
        font-size: 0.72rem;
        color: #8a9e97;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .mc-item-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .mc-item-price {
        font-size: 1rem;
        font-weight: 800;
        color: #c28d32;
        white-space: nowrap;
    }
    .mc-qty-ctrl {
        display: flex;
        align-items: center;
        gap: 4px;
        background: #f4f0e8;
        border-radius: 9999px;
        padding: 3px 6px;
        border: 1px solid rgba(12,38,30,0.08);
    }
    .mc-qty-btn {
        width: 26px; height: 26px;
        border: none;
        background: transparent;
        color: #0c261e;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 0.75rem;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .mc-qty-btn:hover { background: rgba(12,38,30,0.08); }
    .mc-qty-val {
        width: 26px;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 700;
        color: #0c261e;
        border: none;
        background: transparent;
        pointer-events: none;
    }
    .mc-remove-btn {
        position: absolute;
        top: 10px; right: 10px;
        width: 22px; height: 22px;
        border-radius: 50%;
        background: rgba(220,38,38,0.08);
        border: none;
        color: #dc2626;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 0.65rem;
        transition: all 0.2s;
    }
    .mc-remove-btn:hover { background: #dc2626; color: #ffffff; }
    .mc-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 48px 24px;
        gap: 12px;
    }
    .mc-empty-icon {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(12,38,30,0.06);
        display: flex; align-items: center; justify-content: center;
        color: #c28d32;
        margin-bottom: 8px;
    }
    .mc-empty-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0c261e;
    }
    .mc-empty-sub {
        font-size: 0.84rem;
        color: #8a9e97;
        line-height: 1.5;
    }
    .mc-footer {
        flex-shrink: 0;
        background: #ffffff;
        border-top: 1px solid rgba(12,38,30,0.07);
        padding: 18px 20px 22px;
        margin-bottom: env(safe-area-inset-bottom, 0px);
    }
    .mc-subtotal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px dashed rgba(12,38,30,0.1);
    }
    .mc-subtotal-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #8a9e97;
    }
    .mc-subtotal-value {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.3rem;
        font-weight: 700;
        color: #0c261e;
    }
    .mc-btn-checkout {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 14px 22px;
        background: #0c261e;
        color: #ffffff;
        border: none;
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-bottom: 10px;
    }
    .mc-btn-checkout:hover {
        background: #153a2e;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(12,38,30,0.25);
    }
    .mc-btn-checkout svg { flex-shrink: 0; }
    .mc-btn-view-cart {
        display: block;
        width: 100%;
        text-align: center;
        padding: 11px;
        background: transparent;
        border: 1.5px solid rgba(12,38,30,0.15);
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #4b5563;
        text-decoration: none;
        transition: all 0.2s;
    }
    .mc-btn-view-cart:hover {
        border-color: #0c261e;
        color: #0c261e;
        background: rgba(12,38,30,0.03);
    }
    </style>

    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="miniCart" aria-labelledby="miniCartLabel">
        <!-- Header -->
        <div class="mc-header">
            <div class="mc-header-title" id="miniCartLabel">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Mon Panier
            </div>
            <button class="mc-close-btn" data-bs-dismiss="offcanvas" aria-label="Fermer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Body -->
        <div class="offcanvas-body p-0 d-flex flex-column" style="overflow: hidden;">
            <div class="mc-items-wrap" id="mini-cart-items">
                @php $total = 0; @endphp
                @forelse(session('cart', []) as $id => $details)
                    @php $total += $details['price'] * $details['quantity']; @endphp
                    <div class="mc-item" id="cart-item-{{ $id }}">
                        <!-- Image -->
                        <div class="mc-item-img-wrap">
                            @if(!empty($details['image']))
                                <img src="{{ Storage::url($details['image']) }}"
                                     alt="{{ $details['name'] }}"
                                     class="mc-item-img"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                <div class="mc-item-img-placeholder" style="display:none;">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                </div>
                            @else
                                <div class="mc-item-img-placeholder">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                </div>
                            @endif
                            <span class="mc-qty-badge">×{{ $details['quantity'] }}</span>
                        </div>

                        <!-- Info -->
                        <div class="mc-item-body">
                            <div class="mc-item-name" title="{{ $details['name'] }}">{{ $details['name'] }}</div>
                            <div class="mc-item-cat">{{ $details['category_name'] ?? 'Soin naturel' }}</div>
                            <div class="mc-item-footer">
                                <span class="mc-item-price">{{ currency($details['price']) }}</span>
                                <div class="mc-qty-ctrl">
                                    <button class="mc-qty-btn" onclick="updateQty({{ $id }}, {{ $details['quantity'] - 1 }})">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    </button>
                                    <input class="mc-qty-val" value="{{ $details['quantity'] }}" readonly>
                                    <button class="mc-qty-btn" onclick="updateQty({{ $id }}, {{ $details['quantity'] + 1 }})">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Remove -->
                        <button class="mc-remove-btn" onclick="removeItem({{ $id }})" title="Retirer">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                @empty
                    <div class="mc-empty">
                        <div class="mc-empty-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        </div>
                        <div class="mc-empty-title">Votre panier est vide</div>
                        <div class="mc-empty-sub">Découvrez nos soins 100% naturels<br>du Haut Atlas marocain.</div>
                        <a href="{{ route('shop.index') }}" class="mc-btn-checkout" style="margin-top:12px; width:auto; padding: 12px 28px;" data-bs-dismiss="offcanvas">
                            <span>Voir la boutique</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endforelse
            </div>

            @if(count(session('cart', [])) > 0)
            <div class="mc-footer">
                <div class="mc-subtotal-row">
                    <span class="mc-subtotal-label">Sous-total</span>
                    <span class="mc-subtotal-value" id="mini-cart-total">{{ currency($total) }}</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="mc-btn-checkout">
                    <span>Commander</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('cart.index') }}" class="mc-btn-view-cart" onclick="navigateFromCart(event, '{{ route('cart.index') }}')">
                    Voir le panier complet
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- ==========================================================================
       SUNPURE LUXURY FOOTER
       ========================================================================== -->
  <footer class="sp-footer" id="contact">
    <div class="sp-footer-wave-top">
      <svg viewBox="0 0 1440 40" preserveAspectRatio="none" fill="var(--sp-footer-green)">
        <path d="M0,0 C380,35 920,40 1440,5 L1440,40 L0,40 Z"></path>
      </svg>
    </div>
    <div class="sp-footer-container">
      <div class="sp-footer-grid">
        
        <!-- Col 1 : Logo & Signature Cursive -->
        <div class="sp-footer-brand-col">
          <a href="{{ route('home') }}" class="sp-logo-link" style="gap: 12px; text-decoration: none;">
            <img src="{{ app_logo_url() }}" alt="Logo {{ setting('app_name', 'Aït Oumdis') }}" class="sp-logo-symbol">
            <div class="sp-logo-text-wrap">
              <span class="sp-logo-main">AIT OUMDIS</span>
              <span class="sp-logo-sub">COOPERATIVE</span>
            </div>
          </a>
          <div class="sp-footer-signature-wrap">
            <div class="sp-footer-signature">Naturellement proche de vous</div>
            <span class="sp-sig-underline"></span>
          </div>
          <p class="sp-footer-desc">
            @if(($currentLocale ?? app()->getLocale() ?? 'fr') === 'ar')
              تعاونية فلاحية وحرفية أصيلة في قلب الأطلس الكبير على ارتفاع 1800 متر. نصنع مستحضرات تجميل طبيعية وعضوية 100% بيو: زيوت نباتية نفيسة، عناية طبيعية بالشعر وواقي الشمس SunPure من كنوز الطبيعة المغربية المستدامة.
            @elseif(($currentLocale ?? app()->getLocale() ?? 'fr') === 'en')
              Authentic artisanal cooperative nestled at 1,800m in Morocco's High Atlas. We craft 100% organic and natural botanical skincare, pure oils, and SunPure suncare born from ancestral Moroccan heritage.
            @else
              Coopérative artisanale nichée à 1 800 m d'altitude dans le Haut Atlas marocain. Nous formulons des soins cosmétiques 100% biologiques et naturels : huiles végétales pures, rituels capillaires et protecteurs solaires SunPure, issus d'un savoir-faire ancestral respectueux de la terre.
            @endif
          </p>
        </div>

        <!-- Col 2 : Liens utiles (Titre Doré) -->
        <div>
          <h4 class="sp-footer-col-title sp-col-gold">Liens utiles</h4>
          <ul class="sp-footer-links-list">
            <li><a href="{{ route('home') }}" class="sp-footer-link">Accueil</a></li>
            <li><a href="{{ route('shop.index') }}" class="sp-footer-link">Boutique</a></li>
            <li><a href="{{ route('home') }}#a-propos" class="sp-footer-link">À propos</a></li>
            <li><a href="{{ route('home') }}#contact" class="sp-footer-link">Contact</a></li>
          </ul>
        </div>

        <!-- Col 3 : Service client -->
        <div>
          <h4 class="sp-footer-col-title">Service client</h4>
          <ul class="sp-footer-links-list">
            <li><a href="{{ route('cart.index') }}" class="sp-footer-link">Mon Panier</a></li>
            <li><a href="{{ route('checkout.index') }}" class="sp-footer-link">Livraison & Commande</a></li>
            <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('social_whatsapp', '212661000000')) }}" target="_blank" rel="noopener" class="sp-footer-link">Assistance WhatsApp</a></li>
            <li><a href="{{ route('home') }}#pourquoi-sunpure" class="sp-footer-link">Nos Garanties</a></li>
          </ul>
        </div>

        <!-- Col 4 : Restez connectés -->
        <div>
          <h4 class="sp-footer-col-title">Restez connectés</h4>
          <p class="sp-newsletter-desc">Recevez nos nouveautés et offres spéciales</p>
          <form class="sp-newsletter-form" onsubmit="event.preventDefault(); alert('Merci ! Vous êtes bien inscrit(e) à nos offres.'); this.reset();">
            <input type="email" placeholder="Votre adresse e-mail" required class="sp-newsletter-input">
            <button type="submit" class="sp-newsletter-btn">S'inscrire</button>
          </form>
          <div class="sp-social-links">
            <a href="{{ setting('social_instagram', 'https://www.instagram.com/aitoumdiscooperative/') }}" target="_blank" rel="noopener" class="sp-social-icon" aria-label="Instagram" title="Suivez-nous sur Instagram">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('social_whatsapp', '212661000000')) }}" target="_blank" rel="noopener" class="sp-social-icon" aria-label="WhatsApp" title="Écrivez-nous sur WhatsApp">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/></svg>
            </a>
          </div>
        </div>

        <!-- Col 5 : Fabriqué au Maroc -->
        <div class="sp-morocco-badge-wrap">
          <div class="sp-morocco-vsep"></div>
          <div class="sp-morocco-inner">
            <div class="sp-morocco-badge-text">
              Fabriqué<br>au Maroc
              <span class="sp-morocco-gold-line"></span>
            </div>
          </div>
        </div>

      </div>

      <!-- Bas de page légal -->
      <div class="sp-footer-bottom">
        <div>
          &copy; {{ date('Y') }} {{ setting('app_name', 'Coopérative Aït Oumdis') }}. Tous droits réservés.
        </div>
        <div class="sp-footer-legal-links">
          <a href="{{ route('shop.index') }}">Boutique</a>
          <span class="sep">|</span>
          <a href="{{ route('home') }}#engagements-bio">Nos Engagements</a>
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
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        @endif

        // Update all cart counters and the floating checkout button
        function updateGlobalCartCount(count) {
            const num = parseInt(count) || 0;
            document.querySelectorAll('#header-cart-count, #header-cart-count-mobile, .sp-cart-count').forEach(el => {
                el.textContent = num;
            });

            let floatingBtn = document.getElementById('spFloatingCheckout');
            if (!floatingBtn && num > 0 && !window.location.pathname.includes('/checkout')) {
                floatingBtn = document.createElement('a');
                floatingBtn.href = '{{ route('checkout.index') }}';
                floatingBtn.id = 'spFloatingCheckout';
                floatingBtn.className = 'sp-floating-checkout is-visible';
                floatingBtn.setAttribute('title', 'Confirmer la commande');
                floatingBtn.setAttribute('aria-label', 'Confirmer la commande');
                floatingBtn.innerHTML = `
                    <span class="sp-fc-icon-box">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        <span class="sp-fc-badge" id="spFloatingCartBadge">${num}</span>
                    </span>
                    <span class="sp-fc-text">Confirmer la commande</span>
                    <span class="sp-fc-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </span>
                `;
                document.body.appendChild(floatingBtn);
            }

            if (floatingBtn) {
                const badge = document.getElementById('spFloatingCartBadge');
                if (badge) badge.textContent = num;

                if (num > 0 && !window.location.pathname.includes('/checkout')) {
                    floatingBtn.classList.remove('is-hidden');
                    floatingBtn.classList.add('is-visible');
                    floatingBtn.style.setProperty('display', 'inline-flex', 'important');
                    floatingBtn.style.setProperty('opacity', '1', 'important');
                    floatingBtn.style.setProperty('pointer-events', 'auto', 'important');
                    floatingBtn.style.setProperty('visibility', 'visible', 'important');

                    floatingBtn.classList.remove('sp-pulse');
                    void floatingBtn.offsetWidth;
                    floatingBtn.classList.add('sp-pulse');
                    setTimeout(() => floatingBtn.classList.remove('sp-pulse'), 700);
                } else {
                    floatingBtn.classList.remove('is-visible');
                    floatingBtn.classList.add('is-hidden');
                    floatingBtn.style.setProperty('display', 'none', 'important');
                    floatingBtn.style.setProperty('opacity', '0', 'important');
                    floatingBtn.style.setProperty('pointer-events', 'none', 'important');
                }
            }
        }
        window.updateGlobalCartCount = updateGlobalCartCount;

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
                if (data.cartCount !== undefined) updateGlobalCartCount(data.cartCount);
                refreshMiniCart(false);
                if (window.location.pathname.endsWith('/cart') || window.location.pathname === '/cart') {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Erreur lors de la mise à jour du panier',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            });
        }

        function removeItem(id) {
            const doRemove = () => {
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
                    if (data.cartCount !== undefined) updateGlobalCartCount(data.cartCount);
                    refreshMiniCart(false);
                    if (window.location.pathname.endsWith('/cart') || window.location.pathname === '/cart') {
                        window.location.reload();
                    } else if (typeof Swal !== 'undefined') {
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
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            };

            if (typeof Swal !== 'undefined') {
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
                        doRemove();
                    }
                });
            } else {
                if (confirm('Voulez-vous supprimer cet article du panier ?')) {
                    doRemove();
                }
            }
        }

        // Global Add To Cart function
        function addToCart(id, qty = 1, btn = null) {
            if (!btn && window.event && window.event.currentTarget) {
                btn = window.event.currentTarget;
            }
            if (!btn) {
                btn = document.querySelector('button[onclick*="addToCart(' + id + '"]');
            }
            let originalHtml = '';
            if (btn) {
                originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const token = tokenEl ? tokenEl.getAttribute('content') : '{{ csrf_token() }}';

            fetch('/cart/add/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: qty })
            })
            .then(r => r.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                if (data.success) {
                    if (data.cartCount !== undefined) updateGlobalCartCount(data.cartCount);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Soin ajouté au panier !',
                            showConfirmButton: false,
                            timer: 2000,
                            background: '#0c261e',
                            color: '#fff'
                        });
                    }

                    refreshMiniCart(false);

                    if (window.location.pathname.endsWith('/cart') || window.location.pathname === '/cart') {
                        window.location.reload();
                    }
                } else {
                    const msg = data.message || "Erreur lors de l'ajout au panier";
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Erreur', text: msg });
                    } else {
                        alert(msg);
                    }
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                console.error('Erreur addToCart:', err);
            });
        }

        // Refresh mini-cart content dynamically
        function refreshMiniCart(openDrawer = false) {
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
                const cartOffcanvas = document.getElementById('miniCart');
                if(cartOffcanvas) {
                    fetch('{{ route('cart.miniFooter') }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.text())
                    .then(footerHtml => {
                        const existingFooter = cartOffcanvas.querySelector('.mc-footer');
                        if(existingFooter && footerHtml.trim()) {
                            existingFooter.outerHTML = footerHtml;
                        } else if(!existingFooter && footerHtml.trim()) {
                            cartOffcanvas.querySelector('.offcanvas-body').insertAdjacentHTML('beforeend', footerHtml);
                        } else if(existingFooter && !footerHtml.trim()) {
                            existingFooter.remove();
                        }
                    })
                    .catch(console.error);

                    if (openDrawer && typeof bootstrap !== 'undefined') {
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(cartOffcanvas) || new bootstrap.Offcanvas(cartOffcanvas);
                        bsOffcanvas.show();
                    }
                }
            })
            .catch(console.error);
        }
    </script>

    <script>
    // Navigate from mini cart — close offcanvas first then redirect
    function navigateFromCart(e, url) {
        e.preventDefault();
        var cartEl = document.getElementById('miniCart');
        if (cartEl && typeof bootstrap !== 'undefined') {
            var instance = bootstrap.Offcanvas.getInstance(cartEl);
            if (instance) {
                cartEl.addEventListener('hidden.bs.offcanvas', function handler() {
                    cartEl.removeEventListener('hidden.bs.offcanvas', handler);
                    window.location.href = url;
                }, { once: true });
                instance.hide();
                return;
            }
        }
        window.location.href = url;
    }
    </script>

    <script>
    // Hide WhatsApp widget whenever any offcanvas opens; restore on close
    (function () {
        function waWidget() { return document.querySelector('.sp-wa-widget'); }

        document.addEventListener('show.bs.offcanvas', function () {
            var wa = waWidget();
            if (wa) {
                wa.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                wa.style.opacity   = '0';
                wa.style.transform = 'scale(0.85)';
                wa.style.pointerEvents = 'none';
            }
            var fc = document.getElementById('spFloatingCheckout');
            if (fc) {
                fc.style.setProperty('display', 'none', 'important');
            }
        });

        document.addEventListener('hidden.bs.offcanvas', function () {
            var wa = waWidget();
            if (wa) {
                wa.style.opacity   = '1';
                wa.style.transform = 'scale(1)';
                wa.style.pointerEvents = '';
            }
            var fc = document.getElementById('spFloatingCheckout');
            if (fc && fc.classList.contains('is-visible')) {
                fc.style.setProperty('display', 'inline-flex', 'important');
                fc.style.setProperty('opacity', '1', 'important');
                fc.style.setProperty('pointer-events', 'auto', 'important');
            }
        });
    })();
    </script>
    
    <script>
    // Toggle Mobile Navbar Menu
    document.addEventListener('DOMContentLoaded', function() {
        var menuBtn = document.getElementById('spMobileMenuBtn');
        var navMenu = document.getElementById('spNavMenu');
        
        if (menuBtn && navMenu) {
            menuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                navMenu.classList.toggle('mobile-open');
                
                // Toggle active class on button for hamburger animation if needed
                this.classList.toggle('active');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!navMenu.contains(event.target) && !menuBtn.contains(event.target)) {
                    navMenu.classList.remove('mobile-open');
                    menuBtn.classList.remove('active');
                }
            });
        }
    });
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
    @include('partials.floating-checkout')
    @include('partials.whatsapp-popup')
</body>
</html>
