<!DOCTYPE html>
<html lang="{{ setting('language', 'en') }}" dir="{{ setting('text_direction', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', setting('app_name', 'Speed Platform'))</title>
    <meta name="description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', setting('app_name', 'Speed Platform'))">
    <meta property="og:description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta property="og:image" content="@yield('meta_image', asset('images/og-default.jpg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('meta_title', setting('app_name', 'Speed Platform'))">
    <meta property="twitter:description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta property="twitter:image" content="@yield('meta_image', asset('images/og-default.jpg'))">
    
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
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
                    <a class="navbar-brand me-5" href="{{ url('/') }}">
                        @if(setting('app_logo'))
                            <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="height: 40px;">
                        @else
                            <h3 class="m-0 fw-bold text-uppercase fst-italic position-relative" style="font-family: 'Rajdhani'; letter-spacing: 1px;">
                                Speed<span class="text-primary">Store</span>
                                <i class="fas fa-bolt text-warning position-absolute top-0 start-100 translate-middle ms-2" style="font-size: 0.8em; transform: rotate(15deg);"></i>
                            </h3>
                        @endif
                    </a>

                    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarMain">
                        <!-- Navigation -->
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link-custom {{ request()->routeIs('shop.index') ? 'active' : '' }}" href="{{ route('shop.index') }}">Shop</a>
                            </li>
                        </ul>

                        <!-- Search -->
                        <form action="{{ route('shop.index') }}" method="GET" class="d-flex mx-lg-4 position-relative" style="min-width: 300px;">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                                <input class="form-control bg-light border-start-0 ps-0 text-muted" type="search" name="q" placeholder="Search for products..." aria-label="Search">
                            </div>
                        </form>

                        <!-- Actions -->
                        <div class="d-flex align-items-center gap-3 ms-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="action-btn-circle text-decoration-none" title="My Account">
                                    <i class="far fa-user"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">Login</a>
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
                <i class="fas fa-shopping-bag me-2 text-primary"></i>Shopping Cart
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
                                <p class="mb-2 text-muted small">{{ $details['category_name'] ?? 'Product' }}</p>
                                
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <span class="text-primary fw-bold" style="font-size: 1.1rem;">${{ number_format($details['price'], 2) }}</span>
                                    
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
                        <h5 class="fw-bold text-dark">Your cart is empty</h5>
                        <p class="text-muted small mb-4">Looks like you haven't added anything to your cart yet.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 shadow-sm">Start Shopping</a>
                    </div>
                @endforelse
            </div>
            
            @if(count(session('cart', [])) > 0)
            <div class="border-top p-4 bg-white mt-auto shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <span class="text-muted small text-uppercase fw-bold ls-1">Subtotal</span>
                    <span class="h4 fw-bold text-dark mb-0 ls-tight" id="mini-cart-total">${{ number_format($total, 2) }}</span>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-between align-items-center px-4">
                        <span>Checkout</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="btn btn-light py-2 rounded-pill fw-bold text-muted small">
                        View Cart Details
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <footer class="footer-modern">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <h5 class="fw-bold text-white mb-4 text-uppercase ls-1">About SpeedStore</h5>
                    <p class="small lh-lg mb-4">
                        Your ultimate destination for high-performance gaming gear. We provide the latest hardware to elevate your gaming experience.
                    </p>
                    <div class="d-flex gap-3">
                        @if(setting('social_facebook'))
                        <a href="{{ setting('social_facebook') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(setting('social_twitter'))
                        <a href="{{ setting('social_twitter') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if(setting('social_instagram'))
                        <a href="{{ setting('social_instagram') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(setting('social_discord'))
                        <a href="{{ setting('social_discord') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-discord"></i></a>
                        @endif
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase ls-1">Shop</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('shop.index') }}" class="footer-link small">All Products</a></li>
                        <li><a href="#" class="footer-link small">New Arrivals</a></li>
                        <li><a href="#" class="footer-link small">Featured</a></li>
                        <li><a href="#" class="footer-link small">Deals</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase ls-1">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link small">Help Center</a></li>
                        <li><a href="#" class="footer-link small">Track Order</a></li>
                        <li><a href="#" class="footer-link small">Returns</a></li>
                        <li><a href="#" class="footer-link small">Warranty</a></li>
                    </ul>
                </div>

            </div>
            
            <hr class="border-secondary opacity-25 my-5">
            
            <div class="row align-items-center">
                <div class="col-md-12 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-center mb-0">&copy; {{ date('Y') }} SpeedStore. All Rights Reserved.</p>
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
                // Update cart count badge
                const countEl = document.getElementById('header-cart-count');
                if(countEl && data.cartCount !== undefined) {
                    countEl.textContent = data.cartCount;
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
                    title: 'Error updating cart',
                    showConfirmButton: false,
                    timer: 2500
                });
            });
        }

        function removeItem(id) {
            Swal.fire({
                title: 'Remove from cart?',
                text: "Do you want to remove this item?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!'
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
                        // Update cart count badge
                        const countEl = document.getElementById('header-cart-count');
                        if(countEl && data.cartCount !== undefined) {
                            countEl.textContent = data.cartCount;
                        }
                        // Refresh mini-cart content
                        refreshMiniCart();
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Item removed!',
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
                            title: 'Error removing item',
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
