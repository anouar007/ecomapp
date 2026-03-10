<!DOCTYPE html>
<html lang="{{ setting('language', 'fr') }}" dir="{{ setting('text_direction', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', 'Coopérative Ait Oumdis' . ' — ' . 'Produits du terroir & Santé naturelle')</title>
    <meta name="description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta name="keywords" content="@yield('meta_keywords', setting('app_name', 'boutique') . ', e-commerce, Maroc, acheter en ligne, livraison Maroc')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="author" content="{{ setting('app_name', 'Cooperative Ait Oumdis') }}">
    <meta name="theme-color" content="#00b878">
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
    <meta property="og:site_name" content="Coopérative Ait Oumdis">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', 'Coopérative Ait Oumdis')">
    <meta property="og:description" content="@yield('meta_description', 'Découvrez les trésors de la province d\'Azilal : miel pur, huile d\'argan, amlou artisanal et recettes naturelles.')">
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
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
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
    <div class="header-main sticky-top shadow-sm w-100 z-50">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-2">
                <div class="container-fluid px-0">
                    <!-- Logo -->
                    <a class="navbar-brand me-3 me-lg-5" href="{{ url('/') }}">
                        {{-- Force Text Logo for Ait Oumdis Branding --}}
                        <h3 class="m-0 fw-bold text-uppercase position-relative" style="font-family: 'Nunito'; letter-spacing: 0.5px; color: var(--primary);">
                            Ait<span class="text-accent">Oumdis</span>
                            <i class="fas fa-leaf text-primary position-absolute top-0 start-100 translate-middle ms-2" style="font-size: 0.8em;"></i>
                        </h3>
                    </a>

                    <!-- Mobile: always-visible actions (cart + user) + toggler -->
                    <div class="d-flex align-items-center gap-2 ms-auto d-lg-none">
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
                        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-expanded="false" aria-label="Menu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>

                    <!-- Collapsible section -->
                    <div class="collapse navbar-collapse" id="navbarMain">
                        <!-- Navigation links -->
                        <ul class="navbar-nav me-auto mb-0 gap-1 mb-3 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('shop.index') ? 'active' : '' }}" href="{{ route('shop.index') }}">Boutique</a>
                            </li>
                        </ul>

                        <!-- Search -->
                        <form action="{{ route('shop.index') }}" method="GET" class="d-flex mx-lg-4 flex-grow-1 flex-lg-grow-0 mb-3 mb-lg-0" style="max-width: 380px;">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                                <input class="form-control bg-light border-start-0 ps-0 text-muted" type="search" name="q" placeholder="Rechercher des produits..." aria-label="Rechercher" value="{{ request('q') }}">
                            </div>
                        </form>

                        <!-- Desktop-only actions -->
                        <div class="d-none d-lg-flex align-items-center gap-3 ms-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="action-btn-circle text-decoration-none" title="Mon compte">
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

    <footer class="footer-modern bg-white pt-5 pb-4 border-top">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="footer-brand mb-4">
                        <img src="{{ Storage::url(setting('site_logo')) }}" alt="Ait Oumdis" height="40" class="mb-4">
                        <p class="text-muted small lh-lg">Plongez au cœur de l'Atlas marocain avec les produits authentiques de la Coopérative Ait Oumdis. Miel pur, huile d'Argan, et remèdes naturels d'Azilal.</p>
                    </div>
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
                        <a href="{{ $sfb }}" target="_blank" rel="noopener" class="footer-social-btn text-muted hover-primary transition-all" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        @endif
                        @if($validUrl($stw))
                        <a href="{{ $stw }}" target="_blank" rel="noopener" class="footer-social-btn text-muted hover-primary transition-all" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        @endif
                        @if($validUrl($sig))
                        <a href="{{ $sig }}" target="_blank" rel="noopener" class="footer-social-btn text-muted hover-primary transition-all" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if($validUrl($sli))
                        <a href="{{ $sli }}" target="_blank" rel="noopener" class="footer-social-btn text-muted hover-primary transition-all" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        @endif
                        @if($validUrl($swa))
                        <a href="{{ $swa }}" target="_blank" rel="noopener" class="footer-social-btn text-muted hover-primary transition-all" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        @endif
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <h6 class="footer-title fw-bold text-dark mb-4">Boutique</h6>
                    <ul class="footer-links list-unstyled">
                        <li class="mb-2"><a href="{{ route('shop.index') }}" class="text-muted text-decoration-none hover-primary transition-all small">Tous les produits</a></li>
                        <li class="mb-2"><a href="{{ route('shop.index', ['category' => 'miel-pur']) }}" class="text-muted text-decoration-none hover-primary transition-all small">Miel Pur</a></li>
                        <li class="mb-2"><a href="{{ route('shop.index', ['category' => 'huile-dargan']) }}" class="text-muted text-decoration-none hover-primary transition-all small">Huile d'Argan</a></li>
                        <li class="mb-2"><a href="{{ route('shop.index', ['category' => 'plantes-medicinales']) }}" class="text-muted text-decoration-none hover-primary transition-all small">Plantes</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3">
                    <h6 class="footer-title fw-bold text-dark mb-4">Informations</h6>
                    <ul class="footer-links list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary transition-all small">À propos de nous</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary transition-all small">Notre coopérative</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary transition-all small">Expédition & Livraison</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary transition-all small">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h6 class="footer-title fw-bold text-dark mb-4">Contact</h6>
                    <ul class="footer-links footer-contact list-unstyled">
                        <li class="mb-3 d-flex align-items-start text-muted small">
                            <i class="fas fa-map-marker-alt mt-1 me-2 text-primary"></i>
                            <span>Ait Oumdis, Province d'Azilal<br>Maroc</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start text-muted small">
                            <i class="fas fa-envelope mt-1 me-2 text-primary"></i>
                            <a href="mailto:{{ setting('company_email', 'contact@aitoumdis.com') }}" class="text-muted text-decoration-none hover-primary transition-all">{{ setting('company_email', 'contact@aitoumdis.com') }}</a>
                        </li>
                        <li class="mb-3 d-flex align-items-start text-muted small">
                            <i class="fas fa-phone mt-1 me-2 text-primary"></i>
                            <a href="tel:{{ setting('company_phone', '+212600000000') }}" class="text-muted text-decoration-none hover-primary transition-all">{{ setting('company_phone', '+212600000000') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 text-muted opacity-25">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="text-muted small mb-0">&copy; {{ date('Y') }} Coopérative Ait Oumdis. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <img src="{{ asset('images/payments.png') }}" alt="Paiement Sécurisé" height="24" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
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
