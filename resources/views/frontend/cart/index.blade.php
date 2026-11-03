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
                            @php
                                $baseProdId = $details['product_id'] ?? explode('_', (string)$id)[0];
                            @endphp
                            <a href="{{ route('shop.show', $baseProdId) }}" class="cart-mobile-thumb-link flex-shrink-0 text-decoration-none">
                                <img src="{{ $cartImg }}" alt="{{ $details['name'] }}" width="78" height="78" class="cart-mobile-thumb rounded-3 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                            </a>

                            {{-- Content Column Beside Image --}}
                            <div class="flex-grow-1 min-w-0">
                                {{-- Row 1: Category Tag & Delete Button --}}
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <span class="cart-cat-badge">
                                            {{ !empty($details['category_name']) ? $details['category_name'] : __('Accessoires') }}
                                        </span>
                                        @if(!empty($details['variant_name']))
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;">
                                                <i class="fas fa-layer-group me-1"></i>{{ $details['variant_name'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <button type="button" class="btn-cart-delete-mini" onclick="removeItem('{{ $id }}')" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>

                                {{-- Row 2: Product Title --}}
                                <h6 class="cart-mobile-title fw-bold mb-2">
                                    <a href="{{ route('shop.show', $baseProdId) }}">{{ $details['name'] }}</a>
                                </h6>

                                {{-- Row 3: Stepper on Left & Total Price on Right --}}
                                <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                                    {{-- Quantity Stepper --}}
                                    <div class="cart-stepper-pill">
                                        <button class="cart-stepper-btn" type="button" onclick="changeCartQty('{{ $id }}', -1)" aria-label="-">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" class="cart-stepper-val cart-item-qty-{{ $id }}" id="cart-item-qty-mobile-{{ $id }}" value="{{ $details['quantity'] }}" readonly>
                                        <button class="cart-stepper-btn" type="button" onclick="changeCartQty('{{ $id }}', 1)" aria-label="+">
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
                                        $baseProdId = $details['product_id'] ?? explode('_', (string)$id)[0];
                                    @endphp
                                    <tr class="border-bottom transition-all hover-bg-light cart-row-{{ $id }}" id="cart-row-{{ $id }}">
                                        <td class="py-4 px-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ $cartImg }}" alt="{{ $details['name'] }}" width="72" height="72" class="rounded-3 shadow-xs object-fit-cover border" style="width: 72px; height: 72px;" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                                </div>
                                                <div class="min-w-0">
                                                    <h6 class="fw-bold mb-1 lh-sm d-flex align-items-center flex-wrap gap-1.5">
                                                        <a href="{{ route('shop.show', $baseProdId) }}" class="text-decoration-none text-dark">{{ $details['name'] }}</a>
                                                        @if(!empty($details['variant_name']))
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">
                                                                <i class="fas fa-layer-group me-1"></i>{{ $details['variant_name'] }}
                                                            </span>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted small mb-0">{{ $details['category_name'] ?? __('Product') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-4 px-4 fw-bold text-dark">{{ currency($details['price']) }}</td>
                                        <td class="text-center py-4 px-4">
                                            <div class="cart-stepper-pill mx-auto">
                                                <button class="cart-stepper-btn" type="button" onclick="changeCartQty('{{ $id }}', -1)" aria-label="-">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="text" class="cart-stepper-val cart-item-qty-{{ $id }}" id="cart-item-qty-{{ $id }}" value="{{ $details['quantity'] }}" readonly>
                                                <button class="cart-stepper-btn" type="button" onclick="changeCartQty('{{ $id }}', 1)" aria-label="+">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="{{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }} py-4 px-4 fw-bold text-danger fs-6 mb-0 cart-item-total-{{ $id }}" id="cart-item-total-{{ $id }}">{{ currency($details['price'] * $details['quantity']) }}</td>
                                        <td class="{{ app()->getLocale() === 'ar' ? 'text-start' : 'text-end' }} py-4 px-4">
                                            <button type="button" class="btn-cart-delete" onclick="removeItem('{{ $id }}')" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
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

                        {{-- Promo Code Section --}}
                        <div class="my-3 p-3 bg-light rounded-4 border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold small text-uppercase text-muted">
                                    <i class="fas fa-ticket-alt text-primary me-1"></i> {{ __('Code Promo') }}
                                </span>
                                <span class="badge bg-success-subtle text-success small {{ !empty($appliedCoupon) ? '' : 'd-none' }}" id="cart-coupon-badge">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('Actif') }}
                                </span>
                            </div>

                            <div id="cart-promo-input-box" class="{{ !empty($appliedCoupon) ? 'd-none' : '' }}">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="cart_promo_input" class="form-control text-uppercase font-monospace fw-bold" placeholder="{{ __('EX: PROMO10') }}">
                                    <button class="btn btn-dark fw-bold px-3" type="button" id="btn-cart-apply-promo">
                                        {{ __('Appliquer') }}
                                    </button>
                                </div>
                                <div id="cart-promo-error" class="text-danger small mt-1.5 d-none"></div>
                            </div>

                            <div id="cart-promo-applied-box" class="{{ !empty($appliedCoupon) ? '' : 'd-none' }}">
                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border border-success-subtle">
                                    <div>
                                        <div class="fw-bold font-monospace text-success small" id="cart-applied-code">{{ $appliedCoupon['code'] ?? '' }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;" id="cart-applied-hint">
                                            @if(!empty($appliedCoupon))
                                                @if($appliedCoupon['is_free_shipping'])
                                                    {{ __('Livraison offerte') }}
                                                @else
                                                    -{{ currency($appliedCoupon['discount']) }}
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none small" id="btn-cart-remove-promo">
                                        <i class="fas fa-times me-1"></i>{{ __('Retirer') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Discount Line --}}
                        <div class="d-flex justify-content-between mb-2.5 text-success small {{ ($discount ?? 0) > 0 ? '' : 'd-none' }}" id="cart-discount-row">
                            <span class="fw-semibold"><i class="fas fa-tag me-1"></i> {{ __('Réduction') }}</span>
                            <span class="fw-bold" id="cart-summary-discount">-{{ currency($discount ?? 0) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 text-muted small">
                            <span>{{ __('Shipping') }}</span>
                            <span class="text-dark fw-bold" id="cart-shipping-text">
                                @if(!empty($appliedCoupon['is_free_shipping']))
                                    <span class="text-success fw-bold"><i class="fas fa-gift me-1"></i>{{ __('Gratuit') }}</span>
                                @else
                                    @if(app()->getLocale() === 'ar')
                                        من 20 درهم <small class="text-muted fw-normal">(حسب المدينة)</small>
                                    @else
                                        Dès 20 DH <small class="text-muted fw-normal">(selon ville)</small>
                                    @endif
                                @endif
                            </span>
                        </div>
                        <hr class="my-3 opacity-10">
                        <div class="d-flex justify-content-between mb-3.5 align-items-center">
                            <span class="h5 fw-bold mb-0 text-dark">{{ __('Total') }}</span>
                            <span class="h4 fw-bold text-danger mb-0 font-heading" id="cart-summary-total">
                                {{ currency(max(0, $total - ($discount ?? 0))) }}
                            </span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnApplyPromo = document.getElementById('btn-cart-apply-promo');
    const promoInput = document.getElementById('cart_promo_input');
    const promoError = document.getElementById('cart-promo-error');
    const inputBox = document.getElementById('cart-promo-input-box');
    const appliedBox = document.getElementById('cart-promo-applied-box');
    const appliedCode = document.getElementById('cart-applied-code');
    const appliedHint = document.getElementById('cart-applied-hint');
    const btnRemovePromo = document.getElementById('btn-cart-remove-promo');
    const discountRow = document.getElementById('cart-discount-row');
    const summaryDiscount = document.getElementById('cart-summary-discount');
    const summaryTotal = document.getElementById('cart-summary-total');
    const couponBadge = document.getElementById('cart-coupon-badge');
    const shippingText = document.getElementById('cart-shipping-text');

    if (btnApplyPromo && promoInput) {
        btnApplyPromo.addEventListener('click', function() {
            const code = promoInput.value.trim();
            if (!code) return;

            promoError.classList.add('d-none');
            btnApplyPromo.disabled = true;
            btnApplyPromo.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch("{{ route('cart.applyCoupon') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json())
            .then(data => {
                btnApplyPromo.disabled = false;
                btnApplyPromo.textContent = '{{ __("Appliquer") }}';

                if (data.success) {
                    if (appliedCode) appliedCode.textContent = data.coupon.code;
                    if (appliedHint) {
                        appliedHint.textContent = data.coupon.is_free_shipping 
                            ? '{{ __("Livraison offerte") }}' 
                            : '-' + data.coupon.formatted_discount;
                    }

                    if (inputBox) inputBox.classList.add('d-none');
                    if (appliedBox) appliedBox.classList.remove('d-none');
                    if (couponBadge) couponBadge.classList.remove('d-none');

                    if (discountRow) {
                        if (data.coupon.discount > 0) {
                            discountRow.classList.remove('d-none');
                            if (summaryDiscount) summaryDiscount.textContent = '-' + data.coupon.formatted_discount;
                        } else {
                            discountRow.classList.add('d-none');
                        }
                    }

                    if (summaryTotal) summaryTotal.textContent = data.newTotal;

                    if (data.coupon.is_free_shipping && shippingText) {
                        shippingText.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-gift me-1"></i>{{ __("Gratuit") }}</span>';
                    }
                } else {
                    promoError.textContent = data.message || '{{ __("Code promo invalide.") }}';
                    promoError.classList.remove('d-none');
                }
            })
            .catch(() => {
                btnApplyPromo.disabled = false;
                btnApplyPromo.textContent = '{{ __("Appliquer") }}';
                promoError.textContent = '{{ __("Erreur lors de la validation du code promo.") }}';
                promoError.classList.remove('d-none');
            });
        });

        promoInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnApplyPromo.click();
            }
        });
    }

    if (btnRemovePromo) {
        btnRemovePromo.addEventListener('click', function() {
            btnRemovePromo.disabled = true;
            fetch("{{ route('cart.removeCoupon') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                btnRemovePromo.disabled = false;
                if (promoInput) promoInput.value = '';
                if (inputBox) inputBox.classList.remove('d-none');
                if (appliedBox) appliedBox.classList.add('d-none');
                if (couponBadge) couponBadge.classList.add('d-none');
                if (discountRow) discountRow.classList.add('d-none');
                if (summaryTotal) summaryTotal.textContent = data.cartTotal;
                if (shippingText) {
                    shippingText.innerHTML = '{{ app()->getLocale() === "ar" ? "من 20 درهم" : "Dès 20 DH" }} <small class="text-muted fw-normal">({{ __("selon ville") }})</small>';
                }
            })
            .catch(() => {
                btnRemovePromo.disabled = false;
            });
        });
    }
});
</script>
@endpush
