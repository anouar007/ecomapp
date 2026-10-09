@extends('layouts.frontend')

@section('meta_title', __('My Cart') . ' — ' . setting('app_name', 'Full Frame House'))

@section('content')
<style>
    /* ====================================================
       MOBILE & DESKTOP CART PAGE STYLES — FULL FRAME HOUSE
       ==================================================== */
    .cart-page-wrapper {
        min-height: 65vh;
        background-color: #f8fafc;
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Mobile Cart Cards */
    .cart-mobile-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 18px !important;
        padding: 16px;
        position: relative;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .cart-mobile-card:hover {
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07) !important;
    }

    /* Delete Button on Mobile Card */
    .btn-cart-delete-card {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #94a3b8;
        font-size: 0.82rem;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 2;
    }
    html[dir="rtl"] .btn-cart-delete-card {
        right: auto;
        left: 14px;
    }
    .btn-cart-delete-card:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #dc2626;
        transform: scale(1.06);
    }
    .btn-cart-delete-card:active {
        transform: scale(0.92);
    }

    /* Quantity Stepper Pill */
    .cart-qty-stepper {
        width: 104px;
        height: 36px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        padding: 2px 6px;
    }
    .cart-qty-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 0.85rem;
        font-weight: 700;
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.1s ease;
    }
    .cart-qty-btn:active {
        transform: scale(0.9);
        background: #e2e8f0;
    }
    .cart-qty-input {
        width: 34px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
        padding: 0;
        outline: none;
    }

    /* Mobile Sticky Checkout Bottom Bar */
    .cart-mobile-sticky-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1025;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        background: rgba(255, 255, 255, 0.96) !important;
        border-top: 1px solid #e2e8f0;
        box-shadow: 0 -6px 22px rgba(0, 0, 0, 0.08) !important;
        padding: 12px 18px calc(14px + env(safe-area-inset-bottom, 0px)) 18px;
    }

    /* Responsive container paddings */
    @media (max-width: 767.98px) {
        .cart-page-wrapper {
            padding-bottom: calc(110px + env(safe-area-inset-bottom, 0px)) !important;
        }
        .cart-header-title {
            font-size: 1.55rem !important;
        }
    }

    /* Trust items */
    .cart-trust-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 16px 20px;
    }
</style>

<div class="cart-page-wrapper py-3 py-md-5">
    <div class="container">
        
        <!-- Breadcrumb Navigation -->
        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">{{ __('Catalogue') }}</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ __('My Cart') }}</li>
                </ol>
            </nav>
        </div>

        @php
            $cartItems = session('cart', []);
            $totalQty = is_array($cartItems) ? array_sum(array_column($cartItems, 'quantity')) : 0;
        @endphp

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-2">
                <h1 class="fw-bold mb-0 font-heading cart-header-title text-dark">
                    {{ __('My Cart') }}
                </h1>
                @if(count($cartItems) > 0)
                    <span class="badge bg-dark text-white rounded-pill px-3 py-1 fs-6 fw-bold">
                        {{ $totalQty }} {{ $totalQty > 1 ? __('articles') : __('article') }}
                    </span>
                @endif
            </div>

            <!-- Desktop Continue Shopping Button (Hidden on Mobile to keep clean header) -->
            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill d-none d-md-inline-flex align-items-center gap-2">
                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                <span>{{ __('Continue Shopping') }}</span>
            </a>
        </div>

        @if($cartItems && count($cartItems) > 0)
        <div class="row g-4">
            
            {{-- ══════════════════════════════════════════════
                 PRODUCTS LIST (COL-LG-8)
                 ══════════════════════════════════════════════ --}}
            <div class="col-lg-8">
                
                {{-- ── 1. DESKTOP VIEW (Visible on tablet & desktop >= 768px) ── --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden d-none d-md-block bg-white" style="border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light border-bottom">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 text-muted small text-uppercase fw-bold ls-1">{{ __('Product') }}</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-uppercase text-center fw-bold ls-1">{{ __('Price') }}</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-uppercase text-center fw-bold ls-1" style="width: 150px;">{{ __('Quantity') }}</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-uppercase {{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }} fw-bold ls-1">{{ __('Total') }}</th>
                                        <th scope="col" class="py-3 px-4 {{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }}" style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $calcTotal = 0; @endphp
                                    @foreach($cartItems as $id => $details)
                                    @php 
                                        $calcTotal += $details['price'] * $details['quantity'];
                                        $cartImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/')))
                                            ? asset(ltrim($details['image'], '/'))
                                            : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                                    @endphp
                                    <tr class="border-bottom transition-all hover-bg-light" id="cart-row-{{ $id }}">
                                        <td class="py-4 px-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    <a href="{{ route('shop.show', $id) }}">
                                                        <img src="{{ $cartImg }}" alt="{{ $details['name'] }}" class="rounded-3 shadow-xs object-fit-cover border" style="width: 72px; height: 72px;">
                                                    </a>
                                                </div>
                                                <div class="min-w-0">
                                                    <h6 class="fw-bold mb-1">
                                                        <a href="{{ route('shop.show', $id) }}" class="text-decoration-none text-dark hover-text-primary text-truncate-2">{{ $details['name'] }}</a>
                                                    </h6>
                                                    <span class="badge bg-light text-muted border small fw-normal">{{ $details['category_name'] ?? __('Product') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-4 px-4 fw-bold text-dark">{{ currency($details['price']) }}</td>
                                        <td class="text-center py-4 px-4">
                                            <div class="cart-qty-stepper mx-auto">
                                                <button class="cart-qty-btn" type="button" onclick="changeCartQty({{ $id }}, -1)" aria-label="-">
                                                    <i class="fas fa-minus small"></i>
                                                </button>
                                                <input type="text" class="cart-qty-input" id="cart-item-qty-{{ $id }}" value="{{ $details['quantity'] }}" readonly>
                                                <button class="cart-qty-btn" type="button" onclick="changeCartQty({{ $id }}, 1)" aria-label="+">
                                                    <i class="fas fa-plus small"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="{{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }} py-4 px-4 fw-bold text-dark h5 mb-0" id="cart-item-total-{{ $id }}">
                                            {{ currency($details['price'] * $details['quantity']) }}
                                        </td>
                                        <td class="{{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }} py-4 px-4">
                                            <button type="button" class="btn-cart-delete" onclick="removeItem({{ $id }})" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ── 2. MOBILE CARD VIEW (Visible only on mobile < 768px) ── --}}
                <div class="d-md-none mb-4">
                    <div class="d-flex flex-column gap-3">
                        @foreach($cartItems as $id => $details)
                        @php
                            $cartImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/')))
                                ? asset(ltrim($details['image'], '/'))
                                : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                        @endphp
                        <div class="cart-mobile-card" data-cart-row="{{ $id }}">
                            <!-- Delete Button (Top Right) -->
                            <button type="button" class="btn-cart-delete-card" onclick="removeItem({{ $id }})" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                <i class="far fa-trash-alt"></i>
                            </button>

                            <div class="d-flex align-items-start gap-3">
                                <!-- Product Image -->
                                <a href="{{ route('shop.show', $id) }}" class="flex-shrink-0 text-decoration-none">
                                    <img src="{{ $cartImg }}" alt="{{ $details['name'] }}" class="rounded-3 border object-fit-cover shadow-xs" style="width: 78px; height: 78px; border-color: #f1f5f9 !important;">
                                </a>

                                <!-- Product Info (Padded on the right to leave space for delete button) -->
                                <div class="flex-grow-1 min-w-0 {{ app()->getLocale() === 'ar' ? 'ps-4' : 'pe-4' }}" style="{{ app()->getLocale() === 'ar' ? 'padding-left: 36px;' : 'padding-right: 36px;' }}">
                                    <a href="{{ route('shop.show', $id) }}" class="text-decoration-none text-dark d-block">
                                        <h6 class="fw-bold mb-1 fs-6 text-truncate-2 lh-sm text-dark">{{ $details['name'] }}</h6>
                                    </a>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge bg-light text-muted border small fw-normal">{{ $details['category_name'] ?? __('Product') }}</span>
                                        <span class="text-muted small fw-semibold">{{ currency($details['price']) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Row: Quantity Stepper & Bold Line Total -->
                            <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top">
                                <!-- Stepper -->
                                <div class="cart-qty-stepper">
                                    <button class="cart-qty-btn" type="button" onclick="changeCartQty({{ $id }}, -1)" aria-label="-">
                                        <i class="fas fa-minus small"></i>
                                    </button>
                                    <input type="text" class="cart-qty-input" data-cart-qty="{{ $id }}" value="{{ $details['quantity'] }}" readonly>
                                    <button class="cart-qty-btn" type="button" onclick="changeCartQty({{ $id }}, 1)" aria-label="+">
                                        <i class="fas fa-plus small"></i>
                                    </button>
                                </div>

                                <!-- Line Total (Clean, bold, dark) -->
                                <div class="text-end">
                                    <span class="fw-bold text-dark fs-5" data-cart-total="{{ $id }}">
                                        {{ currency($details['price'] * $details['quantity']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- ══════════════════════════════════════════════
                 ORDER SUMMARY (COL-LG-4)
                 ══════════════════════════════════════════════ --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top bg-white mb-4" style="top: 100px; z-index: 10; border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 font-heading d-flex align-items-center gap-2 text-dark">
                            <i class="fas fa-receipt text-primary"></i>
                            <span>{{ __('Order Summary') }}</span>
                        </h5>

                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="fw-bold text-dark cart-summary-subtotal" id="cart-summary-subtotal">{{ currency($total) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>{{ __('Shipping') }}</span>
                            <span class="text-dark fw-bold">
                                @if(app()->getLocale() === 'ar')
                                    من 20 درهم <small class="text-muted fw-normal">(حسب المدينة)</small>
                                @else
                                    Dès 20 DH <small class="text-muted fw-normal">(selon ville)</small>
                                @endif
                            </span>
                        </div>

                        <hr class="my-3 opacity-10">

                        <div class="d-flex justify-content-between mb-4 align-items-center">
                            <span class="h6 fw-bold mb-0 text-dark">{{ __('Total TTC') }}</span>
                            <span class="h4 fw-bold text-dark mb-0 cart-summary-total" id="cart-summary-total">{{ currency($total) }}</span>
                        </div>

                        <!-- On Desktop only: Primary Checkout Button -->
                        <button class="btn btn-dark w-100 py-3 rounded-pill fw-bold mb-3 shadow-md hover-scale-sm transition-transform d-none d-md-flex align-items-center justify-content-center gap-2" onclick="location.href='{{ route('checkout.index') }}'">
                            <span>{{ __('Place Order') }}</span>
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </button>

                        <!-- Continue shopping link -->
                        <a href="{{ route('shop.index') }}" class="btn btn-link text-muted w-100 text-decoration-none small text-center d-flex align-items-center justify-content-center gap-1">
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                            <span>{{ __('Continue Shopping') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Trust Badges Strip (Neatly positioned Under Order Summary) --}}
                <div class="cart-trust-box shadow-xs">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">{{ __('Garantie 2 Ans') }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ __('Matériel 100% officiel certifié') }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                <i class="fas fa-truck-fast"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">{{ __('Livraison Sécurisée') }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ __('Expédition express 24/48h au Maroc') }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">{{ __('Facturation & ICE') }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ __('Conforme CGI & productions') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ── 3. MOBILE STICKY CHECKOUT BOTTOM BAR (Visible only on mobile < 768px) ── --}}
        <div class="cart-mobile-sticky-bar d-md-none">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">{{ __('Total TTC') }}</div>
                    <div class="fw-bold text-dark fs-5 cart-bottom-bar-total" style="line-height: 1.1;">{{ currency($total) }}</div>
                </div>
                <button class="btn btn-dark fw-bold rounded-pill px-4 py-2.5 d-inline-flex align-items-center justify-content-center gap-2 flex-grow-1 shadow-sm" onclick="location.href='{{ route('checkout.index') }}'">
                    <span>{{ __('Place Order') }}</span>
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                </button>
            </div>
        </div>

        @else
        {{-- ══════════════════════════════════════════════
             EMPTY CART STATE
             ══════════════════════════════════════════════ --}}
        <div class="text-center py-5 my-4">
            <div class="mb-4 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 120px; height: 120px; border: 1px solid #e2e8f0;">
                <i class="fas fa-shopping-bag fa-4x text-muted opacity-25"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">{{ __('Your cart is empty') }}</h3>
            <p class="text-muted mb-4 fs-6">{{ __("You haven't added anything yet.") }}</p>
            <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center gap-2">
                <i class="fas fa-film"></i>
                <span>{{ __('Start Shopping') }}</span>
            </a>
        </div>
        @endif

    </div>
</div>
@endsection
