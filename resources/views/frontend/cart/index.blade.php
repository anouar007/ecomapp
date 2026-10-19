@extends('layouts.frontend')

@section('meta_title', __('My Cart') . ' — ' . setting('app_name', 'WINA SHOP'))

@section('content')
<div class="bg-light py-3 py-md-5 cart-page">
    <div class="container">
        {{-- Page Header with Badge & Helper Text --}}
        <div class="d-flex align-items-center justify-content-between mb-3 mb-md-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h1 class="fw-bold mb-0 font-heading cart-page-title">{{ __('My Cart') }}</h1>
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="cart-header-badge" id="cart-header-badge">
                            {{ count(session('cart')) }} {{ count(session('cart')) > 1 ? __('articles') : __('article') }}
                        </span>
                    @endif
                </div>
                <p class="text-muted small mb-0">{{ __('Vérifiez vos articles avant de finaliser votre commande.') }}</p>
            </div>
            @if(session('cart') && count(session('cart')) > 0)
                <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1.5 d-none d-sm-inline-flex align-items-center gap-2">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }} small"></i>
                    <span>{{ __('Continue Shopping') }}</span>
                </a>
            @endif
        </div>

        @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-3 g-lg-4 align-items-start">
            <div class="col-lg-8">
                {{-- ── MOBILE VIEW: Compact & Elegant Item Cards (d-md-none) ── --}}
                <div class="cart-mobile-items d-md-none mb-3">
                    @foreach(session('cart') as $id => $details)
                    @php
                        $cartImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/')))
                            ? asset(ltrim($details['image'], '/'))
                            : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                    @endphp
                    <div class="cart-mobile-card cart-row-{{ $id }}" id="cart-row-mobile-{{ $id }}">
                        <div class="d-flex gap-3 align-items-center">
                            {{-- Thumbnail Image --}}
                            <a href="{{ route('shop.show', $id) }}" class="cart-mobile-thumb-link flex-shrink-0 text-decoration-none">
                                <img src="{{ $cartImg }}" alt="{{ $details['name'] }}" width="78" height="78" class="cart-mobile-thumb rounded-3 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                            </a>

                            {{-- Content Column Beside Image --}}
                            <div class="flex-grow-1 min-w-0">
                                {{-- Row 1: Category Tag & Delete Button --}}
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="cart-cat-badge">
                                        {{ !empty($details['category_name']) ? $details['category_name'] : __('Accessoires') }}
                                    </span>
                                    <button type="button" class="btn-cart-delete-mini" onclick="removeItem({{ $id }})" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>

                                {{-- Row 2: Product Title --}}
                                <h6 class="cart-mobile-title fw-bold mb-2">
                                    <a href="{{ route('shop.show', $id) }}">{{ $details['name'] }}</a>
                                </h6>

                                {{-- Row 3: Stepper on Left & Total Price on Right --}}
                                <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                                    {{-- Quantity Stepper --}}
                                    <div class="cart-stepper-pill">
                                        <button class="cart-stepper-btn" type="button" onclick="changeCartQty({{ $id }}, -1)" aria-label="-">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" class="cart-stepper-val cart-item-qty-{{ $id }}" id="cart-item-qty-mobile-{{ $id }}" value="{{ $details['quantity'] }}" readonly>
                                        <button class="cart-stepper-btn" type="button" onclick="changeCartQty({{ $id }}, 1)" aria-label="+">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    {{-- Line Total Price --}}
                                    <div class="text-end">
                                        <div class="cart-mobile-price cart-item-total-{{ $id }}" id="cart-item-total-mobile-{{ $id }}">
                                            {{ currency($details['price'] * $details['quantity']) }}
                                        </div>
                                        <div class="cart-unit-hint {{ $details['quantity'] > 1 ? '' : 'd-none' }}" id="cart-item-unit-hint-mobile-{{ $id }}">
                                            {{ currency($details['price']) }} / u
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- ── DESKTOP VIEW: Spacious Table (d-none d-md-block) ── --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden d-none d-md-block cart-desktop-card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light border-bottom">
                                    <tr>
                                        <th scope="col" class="py-3.5 px-4 text-muted small text-uppercase fw-bold ls-1">{{ __('Product') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-muted small text-uppercase text-center fw-bold ls-1" style="width: 140px;">{{ __('Price') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-muted small text-uppercase text-center fw-bold ls-1" style="width: 140px;">{{ __('Quantity') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-muted small text-uppercase {{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }} fw-bold ls-1" style="width: 140px;">{{ __('Total') }}</th>
                                        <th scope="col" class="py-3.5 px-4 {{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }}" style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach(session('cart') as $id => $details)
                                    @php
                                        $total += $details['price'] * $details['quantity'];
                                        $cartImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/')))
                                            ? asset(ltrim($details['image'], '/'))
                                            : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                                    @endphp
                                    <tr class="border-bottom transition-all hover-bg-light cart-row-{{ $id }}" id="cart-row-{{ $id }}">
                                        <td class="py-4 px-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ $cartImg }}" alt="{{ $details['name'] }}" width="72" height="72" class="rounded-3 shadow-xs object-fit-cover border" style="width: 72px; height: 72px;" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                                </div>
                                                <div class="min-w-0">
                                                    <h6 class="fw-bold mb-1 lh-sm">
                                                        <a href="{{ route('shop.show', $id) }}" class="text-decoration-none text-dark">{{ $details['name'] }}</a>
                                                    </h6>
                                                    <p class="text-muted small mb-0">{{ $details['category_name'] ?? __('Product') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-4 px-4 fw-bold text-dark">{{ currency($details['price']) }}</td>
                                        <td class="text-center py-4 px-4">
                                            <div class="cart-stepper-pill mx-auto">
                                                <button class="cart-stepper-btn" type="button" onclick="changeCartQty({{ $id }}, -1)" aria-label="-">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="text" class="cart-stepper-val cart-item-qty-{{ $id }}" id="cart-item-qty-{{ $id }}" value="{{ $details['quantity'] }}" readonly>
                                                <button class="cart-stepper-btn" type="button" onclick="changeCartQty({{ $id }}, 1)" aria-label="+">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="{{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }} py-4 px-4 fw-bold text-danger fs-6 mb-0 cart-item-total-{{ $id }}" id="cart-item-total-{{ $id }}">{{ currency($details['price'] * $details['quantity']) }}</td>
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
            </div>

            {{-- ── ORDER SUMMARY ── --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top cart-summary-card" style="top: 100px; z-index: 1;">
                    <div class="card-body p-3.5 p-sm-4">
                        <h5 class="fw-bold mb-3.5 font-heading fs-6 fs-sm-5">{{ __('Order Summary') }}</h5>
                        <div class="d-flex justify-content-between mb-2.5 text-muted small">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="fw-bold text-dark" id="cart-summary-subtotal">{{ currency($total) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted small">
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
                        <div class="d-flex justify-content-between mb-3.5 align-items-center">
                            <span class="h5 fw-bold mb-0 text-dark">{{ __('Total') }}</span>
                            <span class="h4 fw-bold text-danger mb-0 font-heading" id="cart-summary-total">{{ currency($total) }}</span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100 py-3 rounded-pill fw-bold mb-2.5 shadow-sm hover-scale-sm transition-transform d-flex align-items-center justify-content-center gap-2 text-decoration-none">
                            <span>{{ __('Place Order') }}</span>
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </a>
                        <a href="{{ route('shop.index') }}" class="btn btn-link text-muted w-100 text-decoration-none small text-center d-flex align-items-center justify-content-center gap-1.5 py-1">
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }} small"></i>
                            <span>{{ __('Continue Shopping') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5 mt-4">
            <div class="mb-4 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm border" style="width: 120px; height: 120px;">
                <i class="fas fa-shopping-basket fa-4x text-muted opacity-25"></i>
            </div>
            <h2 class="fw-bold text-dark mb-3 font-heading fs-3">{{ __('Your cart is empty') }}</h2>
            <p class="text-muted mb-4">{{ __("You haven't added anything yet.") }}</p>
            <a href="{{ route('shop.index') }}" class="btn btn-danger rounded-pill px-5 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center gap-2">
                <i class="fas fa-shopping-bag"></i>
                <span>{{ __('Start Shopping') }}</span>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
