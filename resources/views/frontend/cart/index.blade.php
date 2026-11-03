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
                        <div class="checkout-promo-box my-3">
                            <div class="promo-box-header d-flex align-items-center justify-content-between mb-2.5">
                                <label for="cart_promo_input" class="promo-label mb-0 d-inline-flex align-items-center gap-2">
                                    <span class="promo-icon-badge">
                                        <i class="fas fa-ticket-alt"></i>
                                    </span>
                                    <span class="promo-title-text">{{ __('Code Promo / Réduction') }}</span>
                                </label>
                                <span class="promo-status-badge {{ !empty($appliedCoupon) ? '' : 'd-none' }}" id="cart-coupon-badge">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('Actif') }}
                                </span>
                            </div>

                            <div id="cart-promo-input-box" class="{{ !empty($appliedCoupon) ? 'd-none' : '' }}">
                                <div class="promo-input-wrapper">
                                    <div class="promo-input-affix">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <input type="text" 
                                           id="cart_promo_input" 
                                           class="form-control promo-field text-uppercase font-monospace fw-bold" 
                                           placeholder="{{ __('EX: PROMO10') }}"
                                           autocomplete="off"
                                           spellcheck="false">
                                    <button class="btn btn-apply-promo" type="button" id="btn-cart-apply-promo">
                                        <span>{{ __('Appliquer') }}</span>
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                                <div id="cart-promo-error" class="promo-feedback-error d-none mt-1.5 small text-danger"></div>
                            </div>

                            <div id="cart-promo-applied-box" class="{{ !empty($appliedCoupon) ? '' : 'd-none' }}">
                                <div class="promo-applied-card">
                                    <div class="promo-applied-left">
                                        <div class="promo-applied-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="promo-applied-info">
                                            <div class="promo-code-display">
                                                <span class="promo-code-name" id="cart-applied-code">{{ $appliedCoupon['code'] ?? '' }}</span>
                                            </div>
                                            <div class="promo-saving-hint" id="cart-applied-hint">
                                                @if(!empty($appliedCoupon))
                                                    @if($appliedCoupon['is_free_shipping'])
                                                        <i class="fas fa-truck-moving text-success me-1"></i> {{ __('Livraison offerte') }}
                                                    @else
                                                        <i class="fas fa-arrow-down text-success me-1"></i> -{{ currency($appliedCoupon['discount']) }} {{ __('appliqués') }}
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-remove-promo" id="btn-cart-remove-promo" title="{{ __('Retirer le code') }}">
                                        <i class="fas fa-trash-alt me-1"></i>
                                        <span>{{ __('Retirer') }}</span>
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

@push('styles')
<style>
/* ================================================================
   PROMO CODE / DISCOUNT BOX PRO STYLING
   ================================================================ */
.checkout-promo-box {
    background: #e2e8f0;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: all 0.2s ease;
}

.checkout-promo-box:hover {
    border-color: #cbd5e1;
}

.promo-box-header {
    padding-bottom: 8px;
}

.promo-label {
    cursor: default;
}

.promo-icon-badge {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: #fef2f2;
    color: #dc2626;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.promo-title-text {
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    color: #334155;
}

.promo-status-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.72rem;
    font-weight: 600;
    color: #059669;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    padding: 2px 8px;
    border-radius: 9999px;
}

.promo-input-wrapper {
    position: relative;
    display: flex;
    align-items: stretch;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 3px;
    transition: all 0.2s ease;
}

.promo-input-wrapper:focus-within {
    border-color: #dc2626;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.promo-input-affix {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 10px;
    color: #94a3b8;
    font-size: 0.85rem;
}

.promo-input-wrapper:focus-within .promo-input-affix {
    color: #dc2626;
}

.promo-field {
    border: none !important;
    background: transparent !important;
    padding: 7px 6px !important;
    font-size: 0.85rem !important;
    font-weight: 700 !important;
    letter-spacing: 1.5px !important;
    color: #0f172a !important;
    box-shadow: none !important;
    outline: none !important;
}

.promo-field::placeholder {
    font-size: 0.78rem;
    font-weight: 400;
    letter-spacing: 0;
    color: #94a3b8 !important;
    text-transform: none;
}

.btn-apply-promo {
    background: #006b9f !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
}

.btn-apply-promo:hover:not(:disabled) {
    background: #dc2626 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(220, 38, 38, 0.25);
}

.btn-apply-promo:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.promo-feedback-error {
    font-size: 0.78rem;
    color: #dc2626;
    font-weight: 500;
}

.promo-applied-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
    border: 1.5px dashed #86efac;
    border-radius: 10px;
    gap: 8px;
}

.promo-applied-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.promo-applied-check {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #22c55e;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
    box-shadow: 0 2px 5px rgba(34, 197, 94, 0.3);
}

.promo-applied-info {
    min-width: 0;
}

.promo-code-display {
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.2;
}

.promo-code-name {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.92rem;
    font-weight: 800;
    letter-spacing: 1.2px;
    color: #15803d;
}

.promo-applied-pill {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    background: #dcfce7;
    color: #166534;
    padding: 1px 6px;
    border-radius: 4px;
    border: 1px solid #bbf7d0;
}

.promo-saving-hint {
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
    margin-top: 2px;
}

.btn-remove-promo {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    flex-shrink: 0;
}

.btn-remove-promo:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
}

.btn-remove-promo:active {
    transform: translateY(0);
}

/* ================================================================
   MOBILE-SPECIFIC REFINEMENTS (< 576px / iPhone SE 375px)
   ================================================================ */
@media (max-width: 575.98px) {
    .checkout-promo-box {
        padding: 10px 12px;
        border-radius: 12px;
    }

    .promo-title-text {
        font-size: 0.72rem;
        letter-spacing: 0.4px;
    }

    .promo-status-badge {
        font-size: 0.68rem;
        padding: 1px 7px;
    }

    .promo-applied-card {
        padding: 8px 10px;
        gap: 6px;
    }

    .promo-applied-left {
        gap: 8px;
        flex: 1 1 auto;
        min-width: 0;
    }

    .promo-applied-check {
        width: 24px;
        height: 24px;
        font-size: 0.7rem;
    }

    .promo-code-name {
        font-size: 0.82rem;
        letter-spacing: 0.8px;
    }

    .promo-applied-pill {
        font-size: 0.6rem;
        padding: 1px 4px;
    }

    .promo-saving-hint {
        font-size: 0.7rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .btn-remove-promo {
        padding: 4px 8px;
        font-size: 0.7rem;
        border-radius: 6px;
    }

    .btn-remove-promo i {
        font-size: 0.68rem;
    }

    .promo-input-wrapper {
        border-radius: 8px;
        padding: 2px;
    }

    .promo-input-affix {
        padding: 0 8px;
        font-size: 0.8rem;
    }

    .promo-field {
        font-size: 0.82rem !important;
        letter-spacing: 1px !important;
        padding: 7px 4px !important;
    }

    .promo-field::placeholder {
        font-size: 0.75rem !important;
    }

    .btn-apply-promo {
        padding: 6px 10px !important;
        font-size: 0.78rem !important;
        border-radius: 6px !important;
    }
}
</style>
@endpush

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
                        appliedHint.innerHTML = data.coupon.is_free_shipping 
                            ? '<i class="fas fa-truck-moving text-success me-1"></i> {{ __("Livraison offerte") }}' 
                            : '<i class="fas fa-arrow-down text-success me-1"></i> -' + data.coupon.formatted_discount + ' {{ __("appliqués") }}';
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
