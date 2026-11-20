@extends('layouts.frontend')

@section('meta_title', __('Checkout') . ' — ' . setting('app_name', 'WINA SHOP'))

@section('content')
<div id="checkout-progress-bar" class="checkout-progress-bar" style="display: none;"></div>
<div class="bg-light checkout-page-wrapper py-3 py-md-5">
    <div class="container checkout-container">
        <h1 class="h3 fw-bold mb-3 mb-md-4 font-heading checkout-title">{{ __('Finaliser votre commande') }}</h1>
        <div class="row g-3 g-md-4">
            <div class="col-lg-7">
                <div class="card checkout-card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
                    <div class="card-body p-3 p-sm-4">
                        <h2 class="h4 fw-bold mb-3 mb-md-4 checkout-section-title">{{ __('Shipping Information') }}</h2>

                        @if(isset($errors) && $errors->any())
                            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
                                <div class="d-flex align-items-start gap-2.5">
                                    <i class="fas fa-exclamation-triangle text-danger fs-5 mt-1"></i>
                                    <div>
                                        <strong class="d-block mb-1">{{ __('Please correct the following errors:') }}</strong>
                                        <ul class="mb-0 ps-3 small">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
                                <i class="fas fa-exclamation-circle me-1.5"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <input type="hidden" name="coupon_code" id="checkout_coupon_code_hidden" value="{{ $appliedCoupon['code'] ?? '' }}">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="customer_name" class="form-label fw-semibold text-dark mb-1.5">
                                        {{ __('FULL NAME') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group checkout-input-group">
                                        <span class="input-group-text"><i class="far fa-user text-muted"></i></span>
                                        <input type="text" 
                                               id="customer_name" 
                                               name="customer_name" 
                                               class="form-control checkout-input" 
                                               required 
                                               value="{{ old('customer_name', auth()->user()->name ?? '') }}" 
                                               placeholder="....">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="customer_phone" class="form-label fw-semibold text-dark mb-1.5">
                                        {{ __('PHONE NUMBER') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group checkout-input-group">
                                        <span class="input-group-text"><i class="fas fa-phone-alt text-muted"></i></span>
                                        <input type="tel" 
                                               id="customer_phone" 
                                               name="customer_phone" 
                                               inputmode="tel"
                                               autocomplete="tel"
                                               class="form-control checkout-input @error('customer_phone') is-invalid @enderror" 
                                               placeholder="Ex: 06 12 34 56 78" 
                                               required
                                               value="{{ old('customer_phone', auth()->user()->phone ?? '') }}">
                                    </div>
                                    <div id="phone-validation-feedback" class="small mt-1 @error('customer_phone') text-danger fw-semibold @else text-muted @enderror" style="font-size: 0.78rem;">
                                        @error('customer_phone')
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        @else
                                            Format accepté : 06 / 07 / 05 XX XX XX XX ou +212...
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="customer_phone_confirmation" class="form-label fw-semibold text-dark mb-1.5">
                                        {{ __('CONFIRM PHONE NUMBER') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group checkout-input-group">
                                        <span class="input-group-text"><i class="fas fa-check-double text-muted"></i></span>
                                        <input type="tel"
                                               id="customer_phone_confirmation"
                                               name="customer_phone_confirmation"
                                               inputmode="tel"
                                               autocomplete="off"
                                               class="form-control checkout-input @error('customer_phone_confirmation') is-invalid @enderror"
                                               placeholder="Ex: 06 12 34 56 78"
                                               required
                                               value="{{ old('customer_phone_confirmation', '') }}">
                                    </div>
                                    <div id="phone-confirm-validation-feedback" class="small mt-1 @error('customer_phone_confirmation') text-danger fw-semibold @else text-muted @enderror" style="font-size: 0.78rem;">
                                        @error('customer_phone_confirmation')
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        @else
                                            {{ __('Retype your phone number to confirm') }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="customer_email" class="form-label fw-semibold text-dark mb-1.5">
                                        E-MAIL <span class="text-muted fw-normal small">({{ __('optional') }})</span>
                                    </label>
                                    <div class="input-group checkout-input-group">
                                        <span class="input-group-text"><i class="far fa-envelope text-muted"></i></span>
                                        <input type="email" 
                                               id="customer_email" 
                                               name="customer_email" 
                                               class="form-control checkout-input" 
                                               placeholder="...." 
                                               value="{{ old('customer_email', auth()->user()->email ?? '') }}">
                                    </div>
                                </div>

                                @php
                                    $selectedDeliveryMethod = old('delivery_method', 'delivery');
                                    $isPickupSelected = $selectedDeliveryMethod === 'pickup_casablanca';
                                @endphp
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark mb-2">
                                        {{ __('DELIVERY METHOD') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-6">
                                            <label class="delivery-option-card {{ !$isPickupSelected ? 'is-selected' : '' }}" for="delivery_method_home">
                                                <input class="delivery-option-radio" type="radio" name="delivery_method" id="delivery_method_home" value="delivery" {{ !$isPickupSelected ? 'checked' : '' }}>
                                                <span class="delivery-card-icon">
                                                    <i class="fas fa-truck"></i>
                                                </span>
                                                <span class="delivery-card-body">
                                                    <span class="delivery-card-title">{{ __('Home Delivery') }}</span>
                                                    <span class="delivery-card-subtitle">{{ __('Delivery across Morocco') }}</span>
                                                </span>
                                                <span class="delivery-check-indicator" aria-hidden="true">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="delivery-option-card is-pickup {{ $isPickupSelected ? 'is-selected' : '' }}" for="delivery_method_pickup">
                                                <input class="delivery-option-radio" type="radio" name="delivery_method" id="delivery_method_pickup" value="pickup_casablanca" {{ $isPickupSelected ? 'checked' : '' }}>
                                                <span class="delivery-card-icon is-pickup">
                                                    <i class="fas fa-store"></i>
                                                </span>
                                                <span class="delivery-card-body">
                                                    <span class="delivery-card-title">{{ __('Pickup at Casablanca Store') }}</span>
                                                    <span class="delivery-card-subtitle">{{ $pickupStore->name ?? 'Magasin Principal Casablanca' }}</span>
                                                    <span class="delivery-free-badge">{{ __('Gratuit') }} • 0 {{ setting('currency_symbol', 'DH') }}</span>
                                                </span>
                                                <span class="delivery-check-indicator" aria-hidden="true">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 {{ $isPickupSelected ? '' : 'd-none' }}" id="pickup-store-info-box">
                                    @php
                                        $displayPickupPhone = $pickupPhone ?? ($pickupStore?->phone ?: '06 29 03 57 77');
                                        $pickupTelHref = 'tel:' . preg_replace('/[^\d\+]/', '', $displayPickupPhone);
                                        $displayPickupMapsUrl = $pickupMapsUrl ?? 'https://maps.app.goo.gl/NfQmJQ1xqXSydiUk6';
                                    @endphp
                                    <div class="pickup-store-info-card">
                                        <div class="pickup-store-badge">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <div class="pickup-store-content">
                                            <div class="pickup-store-header">
                                                <div class="pickup-store-title">
                                                    {{ $pickupStore->name ?? 'Magasin Principal Casablanca' }} — {{ $pickupStore->city ?? 'Casablanca' }}
                                                </div>
                                                <span class="pickup-zero-fee-pill">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>{{ __('Store pickup — zero delivery fee') }}</span>
                                                </span>
                                            </div>
                                            @if(!empty($pickupStore?->address))
                                                <div class="pickup-store-address">
                                                    <i class="fas fa-map-pin"></i>
                                                    <span>{{ $pickupStore->address }}</span>
                                                </div>
                                            @endif
                                            <div class="pickup-store-actions">
                                                <a href="{{ $pickupTelHref }}" class="btn-pickup-phone">
                                                    <i class="fas fa-phone-alt"></i>
                                                    <span>{{ $displayPickupPhone }}</span>
                                                </a>
                                                <a href="{{ $displayPickupMapsUrl }}" target="_blank" rel="noopener noreferrer" class="btn-pickup-maps">
                                                    <i class="fas fa-map-location-dot"></i>
                                                    <span>{{ __('View location on Google Maps') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 home-delivery-field {{ $isPickupSelected ? 'd-none' : '' }}">
                                    <label for="shipping_address" class="form-label fw-semibold text-dark">
                                        {{ __('ADDRESS') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group checkout-input-group checkout-address-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                        <input type="text" 
                                               id="shipping_address" 
                                               name="shipping_address" 
                                               class="form-control checkout-input" 
                                               {{ $isPickupSelected ? '' : 'required' }}
                                               value="{{ old('shipping_address', auth()->user()->address ?? '') }}" 
                                               placeholder="....">
                                        <button type="button"
                                                id="btn-get-location"
                                                class="btn-geo-locate"
                                                title="{{ __('Use my current location') }}">
                                            <i class="fas fa-location-crosshairs" id="geo-btn-icon"></i>
                                            <span id="geo-btn-text" class="geo-btn-label">{{ __('Use my location') }}</span>
                                        </button>
                                    </div>
                                    <div id="geo-location-feedback" class="mt-2 d-none" aria-live="polite"></div>
                                </div>

                                <div class="col-12 home-delivery-field {{ $isPickupSelected ? 'd-none' : '' }}">
                                    <label for="shipping_city_input" class="form-label fw-semibold text-dark mb-1.5">
                                        {{ __('SHIPPING CITY') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="city-autocomplete-wrapper">
                                        <div class="input-group checkout-input-group">
                                            <span class="input-group-text"><i class="fas fa-city text-muted"></i></span>
                                            <input type="text" 
                                                   name="shipping_city" 
                                                   id="shipping_city_input" 
                                                   class="form-control checkout-input fw-semibold" 
                                                   placeholder="...." 
                                                   autocomplete="off" 
                                                   {{ $isPickupSelected ? '' : 'required' }}
                                                   value="{{ old('shipping_city', auth()->user()->city ?? '') }}">
                                            <button class="btn btn-light border-0 text-muted px-3" type="button" id="city-clear-btn" style="display: none;" title="{{ __('Clear city') }}">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </div>

                                        {{-- Dropdown Suggestion List --}}
                                        <div id="city-suggestions-dropdown" class="city-suggestions-dropdown" style="display: none;"></div>
                                    </div>

                                    {{-- Live status indicator --}}
                                    <div id="city-status-container" class="mt-2" style="display: none;"></div>
                                </div>

                                {{-- Hidden fields: ICE and Région hidden per request --}}
                                <input type="hidden" name="ice" value="{{ old('ice', auth()->user()->ice ?? '') }}">
                                <input type="hidden" name="shipping_state" value="{{ old('shipping_state', auth()->user()->state ?? '') }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card checkout-card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white p-3 p-sm-4 border-bottom-0 pb-0">
                        <h2 class="h5 fw-bold m-0">{{ __('Order Summary') }}</h2>
                    </div>
                    <div class="card-body p-3 p-sm-4 pt-0">
                        @foreach($cart as $id => $details)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 position-relative">
                                @php
                                    $chkImg = \App\Models\Product::resolveImageUrl($details['image'] ?? null);
                                @endphp
                                <img src="{{ $chkImg }}" alt="{{ $details['name'] }}" width="60" height="60" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary tiny-badge">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold mb-0 text-truncate fs-6" style="max-width: 170px;">{{ $details['name'] }}</div>
                                @if(!empty($details['variant_name']))
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;">
                                        <i class="fas fa-layer-group me-1"></i>{{ $details['variant_name'] }}
                                    </span>
                                @endif
                            </div>
                            <div class="fw-bold">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach
                        
                        <hr class="my-3 opacity-10">
                        
                        <!-- Promo Code Section -->
                        <div class="checkout-promo-box mb-3.5">
                            <div class="promo-box-header d-flex align-items-center justify-content-between mb-2.5">
                                <label for="checkout_promo_input" class="promo-label mb-0 d-inline-flex align-items-center gap-2">
                                    <span class="promo-icon-badge">
                                        <i class="fas fa-ticket-alt"></i>
                                    </span>
                                    <span class="promo-title-text">{{ __('Code Promo / Réduction') }}</span>
                                </label>
                                <span class="promo-status-badge {{ !empty($appliedCoupon) ? '' : 'd-none' }}" id="checkout-coupon-active-badge">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('Actif') }}
                                </span>
                            </div>

                            <!-- Input State -->
                            <div id="promo-input-container" class="{{ !empty($appliedCoupon) ? 'd-none' : '' }}">
                                <div class="promo-input-wrapper">
                                    <div class="promo-input-affix">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <input type="text" 
                                           id="checkout_promo_input" 
                                           class="form-control promo-field text-uppercase font-monospace fw-bold" 
                                           placeholder="{{ __('EX: PROMO10') }}" 
                                           autocomplete="off"
                                           spellcheck="false">
                                    <button class="btn btn-apply-promo" type="button" id="btn-apply-promo">
                                        <span>{{ __('Appliquer') }}</span>
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                                <div id="promo-error-msg" class="promo-feedback-error d-none mt-1.5 small text-danger"></div>
                            </div>

                            <!-- Applied State -->
                            <div id="promo-applied-container" class="{{ !empty($appliedCoupon) ? '' : 'd-none' }}">
                                <div class="promo-applied-card">
                                    <div class="promo-applied-left">
                                        <div class="promo-applied-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="promo-applied-info">
                                            <div class="promo-code-display">
                                                <span class="promo-code-name" id="applied-code-text">{{ $appliedCoupon['code'] ?? '' }}</span>
                                            </div>
                                            <div class="promo-saving-hint" id="applied-discount-hint">
                                                @if(!empty($appliedCoupon))
                                                    @if($appliedCoupon['is_free_shipping'])
                                                        <i class="fas fa-truck-moving text-success me-1"></i> {{ __('Livraison offerte') }}
                                                    @else
                                                        -{{ currency($appliedCoupon['discount']) }}
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-remove-promo" id="btn-remove-promo" title="{{ __('Retirer le code') }}">
                                        <i class="fas fa-times me-1"></i>
                                        <span>{{ __('Retirer') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3 opacity-10">

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('Articles') }}</span>
                            <span class="fw-bold" id="checkout-subtotal" 
                                  data-subtotal="{{ $subtotal }}"
                                  data-discount="{{ $discount }}"
                                  data-is-free-shipping="{{ !empty($appliedCoupon['is_free_shipping']) ? '1' : '0' }}"
                                  data-currency-symbol="{{ setting('currency_symbol', 'DH') }}"
                                  data-currency-decimals="{{ setting('currency_decimals', 2) }}"
                                  data-decimal-separator="{{ setting('decimal_separator', '.') }}"
                                  data-thousands-separator="{{ setting('thousands_separator', ' ') }}"
                                  data-currency-position="{{ setting('currency_position', 'after') }}">
                                {{ currency($subtotal) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 align-items-center {{ $discount > 0 ? '' : 'd-none' }}" id="checkout-discount-row">
                            <span class="text-success fw-semibold">
                                <i class="fas fa-tag me-1"></i> {{ __('Réduction') }}
                            </span>
                            <span class="fw-bold text-success" id="checkout-discount-cost">-{{ currency($discount) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <span class="text-muted">{{ __('Shipping Fee') }}</span>
                            <span class="fw-bold" id="checkout-shipping-cost">
                                <span class="text-muted small">{{ __('Select a city') }}</span>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-top">
                            <div>
                                <span class="h5 fw-bold mb-0 d-block">{{ __('Total') }}</span>
                            </div>
                            <span class="h4 fw-bold text-danger mb-0 font-heading" id="checkout-total">{{ currency($total) }}</span>
                        </div>

                        <button type="submit" form="checkout-form" id="checkout-submit-btn" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow">
                            <i class="fas fa-lock me-2 small"></i>{{ __('Place Order') }} (<span id="btn-total-label">{{ currency($total) }}</span>)
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none small">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right me-1' : 'fa-arrow-left me-1' }}"></i> {{ __('Return to cart') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cityInput = document.getElementById('shipping_city_input');
    const dropdown = document.getElementById('city-suggestions-dropdown');
    const clearBtn = document.getElementById('city-clear-btn');
    const statusContainer = document.getElementById('city-status-container');

    const subtotalEl = document.getElementById('checkout-subtotal');
    const shippingEl = document.getElementById('checkout-shipping-cost');
    const totalEl = document.getElementById('checkout-total');
    const btnTotalEl = document.getElementById('btn-total-label');

    if (!cityInput || !subtotalEl) return;

    const shippingAddressInput = document.getElementById('shipping_address');
    const pickupInfoBox = document.getElementById('pickup-store-info-box');
    const homeDeliveryFields = document.querySelectorAll('.home-delivery-field');
    const deliveryRadios = document.querySelectorAll('input[name="delivery_method"]');

    const allCities = @json($cities);
    const subtotal = parseFloat(subtotalEl.getAttribute('data-subtotal')) || 0;
    let currentDiscount = parseFloat(subtotalEl.getAttribute('data-discount')) || 0;
    let isFreeShipping = subtotalEl.getAttribute('data-is-free-shipping') === '1';
    let isStorePickup = document.querySelector('input[name="delivery_method"]:checked')?.value === 'pickup_casablanca';
    let currentShipping = null;
    const symbol = subtotalEl.getAttribute('data-currency-symbol') || 'DH';
    const decimals = parseInt(subtotalEl.getAttribute('data-currency-decimals')) || 2;
    const decSep = subtotalEl.getAttribute('data-decimal-separator') || '.';
    const thoSep = subtotalEl.getAttribute('data-thousands-separator') || ' ';
    const position = subtotalEl.getAttribute('data-currency-position') || 'after';

    let activeIndex = -1;
    let visibleItems = [];

    function formatCurrency(amount) {
        const fixed = amount.toFixed(decimals);
        const parts = fixed.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thoSep);
        const formatted = parts.join(decSep);
        return position === 'before' ? symbol + ' ' + formatted : formatted + ' ' + symbol;
    }

    function updateSummary(price) {
        if (price !== undefined) currentShipping = price;
        const effectiveShipping = (isStorePickup || isFreeShipping)
            ? 0
            : (currentShipping !== null && !isNaN(currentShipping) ? currentShipping : null);

        if (isStorePickup) {
            shippingEl.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-store me-1"></i>{{ __('Gratuit') }} (${formatCurrency(0)})</span>`;
        } else if (isFreeShipping) {
            shippingEl.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-gift me-1"></i>{{ __("Gratuit") }}</span>';
        } else if (effectiveShipping !== null) {
            shippingEl.innerHTML = '<span class="text-danger fw-bold">' + formatCurrency(effectiveShipping) + '</span>';
        } else {
            shippingEl.innerHTML = '<span class="text-muted small">{{ __('Type your city') }}</span>';
        }

        const grandTotal = Math.max(0, subtotal + (effectiveShipping || 0) - currentDiscount);
        totalEl.textContent = formatCurrency(grandTotal);
        if (btnTotalEl) btnTotalEl.textContent = formatCurrency(grandTotal);
    }

    function syncDeliveryMethodUI(triggerSummary = true) {
        isStorePickup = document.querySelector('input[name="delivery_method"]:checked')?.value === 'pickup_casablanca';

        deliveryRadios.forEach(radio => {
            const card = radio.closest('.delivery-option-card');
            if (card) card.classList.toggle('is-selected', radio.checked);
        });

        homeDeliveryFields.forEach(el => el.classList.toggle('d-none', isStorePickup));
        if (pickupInfoBox) pickupInfoBox.classList.toggle('d-none', !isStorePickup);

        if (shippingAddressInput) shippingAddressInput.required = !isStorePickup;
        if (cityInput) cityInput.required = !isStorePickup;

        if (!triggerSummary) return;

        if (isStorePickup) {
            updateSummary(0);
        } else {
            const val = cityInput.value.trim();
            if (val) {
                selectManual(val);
            } else {
                hideStatus();
                updateSummary(null);
            }
        }
    }

    deliveryRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            syncDeliveryMethodUI(true);
        });
    });

    // --- PROMO CODE LOGIC ---
    const btnApplyPromo = document.getElementById('btn-apply-promo');
    const promoInput = document.getElementById('checkout_promo_input');
    const promoError = document.getElementById('promo-error-msg');
    const promoInputContainer = document.getElementById('promo-input-container');
    const promoAppliedContainer = document.getElementById('promo-applied-container');
    const appliedCodeText = document.getElementById('applied-code-text');
    const appliedDiscountHint = document.getElementById('applied-discount-hint');
    const btnRemovePromo = document.getElementById('btn-remove-promo');
    const hiddenCouponInput = document.getElementById('checkout_coupon_code_hidden');
    const discountRow = document.getElementById('checkout-discount-row');
    const discountCost = document.getElementById('checkout-discount-cost');
    const couponBadge = document.getElementById('checkout-coupon-active-badge');

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
                btnApplyPromo.textContent = '';

                if (data.success) {
                    currentDiscount = data.coupon.discount || 0;
                    isFreeShipping = data.coupon.is_free_shipping || false;

                    if (hiddenCouponInput) hiddenCouponInput.value = data.coupon.code;
                    if (appliedCodeText) appliedCodeText.textContent = data.coupon.code;
                    if (appliedDiscountHint) {
                        appliedDiscountHint.innerHTML = isFreeShipping 
                            ? '<i class="fas fa-truck-moving text-success me-1"></i> {{ __("Livraison offerte") }}' 
                            : ' -' + data.coupon.formatted_discount + ' {{ __("appliqués") }}';
                    }

                    if (promoInputContainer) promoInputContainer.classList.add('d-none');
                    if (promoAppliedContainer) promoAppliedContainer.classList.remove('d-none');
                    if (couponBadge) couponBadge.classList.remove('d-none');

                    if (discountRow) {
                        if (currentDiscount > 0) {
                            discountRow.classList.remove('d-none');
                            if (discountCost) discountCost.textContent = '-' + data.coupon.formatted_discount;
                        } else {
                            discountRow.classList.add('d-none');
                        }
                    }

                    updateSummary();
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
                currentDiscount = 0;
                isFreeShipping = false;

                if (hiddenCouponInput) hiddenCouponInput.value = '';
                if (promoInput) promoInput.value = '';
                if (promoInputContainer) promoInputContainer.classList.remove('d-none');
                if (promoAppliedContainer) promoAppliedContainer.classList.add('d-none');
                if (couponBadge) couponBadge.classList.add('d-none');
                if (discountRow) discountRow.classList.add('d-none');

                updateSummary();
            })
            .catch(() => {
                btnRemovePromo.disabled = false;
            });
        });
    }

    function normalizeText(str) {
        return (str || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function findMatch(query) {
        if (!query) return null;
        const normQ = normalizeText(query);
        const rawQ = query.trim().toLowerCase();

        return allCities.find(c => {
            const en = normalizeText(c.name_en);
            const ar = (c.name_ar || '').trim().toLowerCase();
            return en === normQ || ar === rawQ;
        });
    }

    function showStatus(type, html) {
        statusContainer.style.display = 'block';
        statusContainer.innerHTML = `<div class="city-status-pill ${type}">${html}</div>`;
    }

    function hideStatus() {
        statusContainer.style.display = 'none';
        statusContainer.innerHTML = '';
    }

    function selectCity(city) {
        cityInput.value = city.name_en;
        clearBtn.style.display = 'block';
        dropdown.style.display = 'none';
        updateSummary(parseFloat(city.price));
        showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> {{ __('Selected city:') }} <strong>${escapeHtml(city.name_en)}</strong> (${escapeHtml(city.name_ar)}) — {{ __('Shipping:') }} ${formatCurrency(city.price)}`);
    }

    function selectManual(cityName) {
        const trimmed = (cityName || '').trim();
        if (!trimmed) {
            hideStatus();
            updateSummary(null);
            return;
        }

        cityInput.value = trimmed;
        clearBtn.style.display = 'block';
        dropdown.style.display = 'none';

        // Check if manual entry matches a known city name (Latin or Arabic)
        const matched = findMatch(trimmed);
        if (matched) {
            updateSummary(parseFloat(matched.price));
            showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> {{ __('Recognized city:') }} <strong>${escapeHtml(matched.name_en)}</strong> (${escapeHtml(matched.name_ar)}) — {{ __('Shipping:') }} ${formatCurrency(matched.price)}`);
        } else {
            // Unlisted city: apply standard 40 DH fee
            updateSummary(40);
            showStatus('manual', `<i class="fas fa-check-circle text-primary me-1"></i> {{ __('Validated city:') }} <strong>${escapeHtml(trimmed)}</strong> — {{ __('Standard rate:') }} ${formatCurrency(40)}`);
        }
    }

    function renderDropdown(matches, query, exact) {
        dropdown.innerHTML = '';
        visibleItems = [];
        activeIndex = -1;

        if (matches.length > 0) {
            const header = document.createElement('div');
            header.className = 'city-dropdown-header d-flex justify-content-between align-items-center';
            header.innerHTML = `
                <span><i class="fas fa-list-ul me-1"></i> {{ __('Suggested cities') }} (${matches.length})</span>
                <span class="small fw-normal text-muted">{{ __('Click to select') }}</span>
            `;
            dropdown.appendChild(header);

            matches.forEach((c) => {
                const item = document.createElement('div');
                const isExact = exact && (exact.name_en.toLowerCase() === c.name_en.toLowerCase() || (exact.name_ar && exact.name_ar === c.name_ar));
                item.className = 'city-suggestion-item' + (isExact ? ' active' : '');
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark">${escapeHtml(c.name_en)}</span>
                            <span class="text-muted ms-1">(${escapeHtml(c.name_ar)})</span>
                        </div>
                        <span class="badge bg-danger-subtle text-danger fw-bold">${formatCurrency(c.price)}</span>
                    </div>
                `;
                item.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    selectCity(c);
                });
                dropdown.appendChild(item);
                visibleItems.push({ type: 'city', data: c, element: item });
            });

            // Option to continue typing / keep typed text as custom city
            const manualItem = document.createElement('div');
            manualItem.className = 'city-suggestion-item city-manual-choice';
            manualItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-pen text-warning me-1"></i>
                        <span>{{ __('Continue with') }} "<strong>${escapeHtml(query)}</strong>" ({{ __('Custom city') }})</span>
                    </div>
                    <span class="badge bg-secondary-subtle text-dark fw-bold">${formatCurrency(40)}</span>
                </div>
            `;
            manualItem.addEventListener('mousedown', function(e) {
                e.preventDefault();
                selectManual(query);
            });
            dropdown.appendChild(manualItem);
            visibleItems.push({ type: 'manual', data: query, element: manualItem });
        } else {
            // No matching cities found in the database
            const noMatch = document.createElement('div');
            noMatch.className = 'city-no-match-box';
            noMatch.innerHTML = `
                <div class="fw-bold text-dark mb-1">
                    <i class="fas fa-info-circle text-primary me-1"></i> {{ __('No city suggested for') }} "${escapeHtml(query)}"
                </div>
                <div class="small text-muted mb-2">
                    {{ __('Continue typing: it is automatically validated as a custom city') }} ({{ __('Shipping:') }} ${formatCurrency(40)}).
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                    <i class="fas fa-check me-1"></i> {{ __('Validate') }} "${escapeHtml(query)}"
                </button>
            `;
            noMatch.addEventListener('mousedown', function(e) {
                e.preventDefault();
                selectManual(query);
            });
            dropdown.appendChild(noMatch);
            visibleItems.push({ type: 'manual', data: query, element: noMatch });
        }

        dropdown.style.display = 'block';
    }

    function onSearch() {
        const raw = cityInput.value;
        const normQ = normalizeText(raw);

        if (!normQ) {
            dropdown.style.display = 'none';
            clearBtn.style.display = 'none';
            hideStatus();
            updateSummary(null);
            return;
        }

        clearBtn.style.display = 'block';

        // Check if query is already an exact match
        const exact = findMatch(raw);
        if (exact) {
            updateSummary(parseFloat(exact.price));
            showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> {{ __('Recognized city:') }} <strong>${escapeHtml(exact.name_en)}</strong> (${escapeHtml(exact.name_ar)}) — {{ __('Shipping:') }} ${formatCurrency(exact.price)}`);
        } else {
            // Live validation as manual custom city while typing
            updateSummary(40);
            showStatus('manual', `<i class="fas fa-pen text-primary me-1"></i> {{ __('Custom entry:') }} <strong>${escapeHtml(raw.trim())}</strong> ({{ __('Standard rate:') }} ${formatCurrency(40)})`);
        }

        // Search in allCities
        const rawTrim = raw.trim().toLowerCase();
        const matches = allCities.filter(c => {
            const en = normalizeText(c.name_en);
            const ar = (c.name_ar || '').trim().toLowerCase();
            return en.includes(normQ) || ar.includes(rawTrim);
        }).sort((a, b) => {
            const aStarts = normalizeText(a.name_en).startsWith(normQ);
            const bStarts = normalizeText(b.name_en).startsWith(normQ);
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            return 0;
        }).slice(0, 10);

        renderDropdown(matches, raw.trim(), exact);
    }

    cityInput.addEventListener('input', onSearch);
    cityInput.addEventListener('focus', function() {
        if (cityInput.value.trim()) {
            onSearch();
        }
    });

    cityInput.addEventListener('blur', function() {
        setTimeout(function() {
            dropdown.style.display = 'none';
            const val = cityInput.value.trim();
            if (val) {
                selectManual(val);
            } else {
                hideStatus();
                updateSummary(null);
            }
        }, 200);
    });

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const wrapper = cityInput.closest('.city-autocomplete-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Keyboard navigation: ArrowDown, ArrowUp, Enter, Escape
    cityInput.addEventListener('keydown', function(e) {
        if (dropdown.style.display === 'none' || visibleItems.length === 0) {
            if (e.key === 'Enter') {
                const val = cityInput.value.trim();
                if (val) {
                    selectManual(val);
                }
            }
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % visibleItems.length;
            highlightActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + visibleItems.length) % visibleItems.length;
            highlightActive();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && activeIndex < visibleItems.length) {
                const item = visibleItems[activeIndex];
                if (item.type === 'city') selectCity(item.data);
                else selectManual(item.data);
            } else {
                selectManual(cityInput.value);
            }
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    });

    function highlightActive() {
        visibleItems.forEach((it, idx) => {
            if (idx === activeIndex) {
                it.element.classList.add('active');
                it.element.scrollIntoView({ block: 'nearest' });
            } else {
                it.element.classList.remove('active');
            }
        });
    }

    clearBtn.addEventListener('click', function() {
        cityInput.value = '';
        clearBtn.style.display = 'none';
        dropdown.style.display = 'none';
        hideStatus();
        updateSummary(null);
        cityInput.focus();
    });

    // Client-side phone & phone-confirmation validation before checkout submit
    const phoneInput = document.getElementById('customer_phone');
    const phoneFeedback = document.getElementById('phone-validation-feedback');
    const phoneConfirmInput = document.getElementById('customer_phone_confirmation');
    const phoneConfirmFeedback = document.getElementById('phone-confirm-validation-feedback');

    function validatePhoneValue(raw) {
        const trimmed = (raw || '').trim();
        if (!trimmed) return { valid: false, message: 'Veuillez saisir votre numéro de téléphone.' };
        if (/[a-zA-Z]/.test(trimmed) || /[^\d\s\+\-\.\(\)]/.test(trimmed)) {
            return { valid: false, message: 'Le numéro ne doit contenir que des chiffres (ex: 06 12 34 56 78).' };
        }

        let compact = trimmed.replace(/[\s\-\.\(\)]+/g, '');
        if (compact.startsWith('00')) {
            compact = '+' + compact.slice(2);
        }

        const maIntl = compact.match(/^(?:\+?212)0?([5-8]\d{8})$/);
        if (maIntl) {
            return { valid: true, normalized: '0' + maIntl[1] };
        }
        if (/^\+?212/.test(compact)) {
            return { valid: false, message: 'Numéro marocain invalide. Exemple : +212 6 12 34 56 78.' };
        }

        const maLocal = compact.match(/^0([5-8]\d{8})$/);
        if (maLocal) {
            return { valid: true, normalized: '0' + maLocal[1] };
        }
        const maNine = compact.match(/^([5-8]\d{8})$/);
        if (maNine) {
            return { valid: true, normalized: '0' + maNine[1] };
        }
        if (compact.startsWith('0')) {
            return { valid: false, message: 'Le numéro doit contenir 10 chiffres (ex: 06 12 34 56 78).' };
        }

        if (/^\+[1-9]\d{7,14}$/.test(compact) && !/^(\d)\1+$/.test(compact.slice(1))) {
            return { valid: true, normalized: compact };
        }

        return { valid: false, message: 'Numéro invalide. Exemple : 06 12 34 56 78 ou +212 6 12 34 56 78.' };
    }

    function setPhoneFieldState(inputEl, feedbackEl, isValid, showError, msg, defaultHint) {
        if (!inputEl) return true;
        if (isValid) {
            inputEl.setCustomValidity('');
            inputEl.classList.remove('is-invalid');
            inputEl.classList.add('is-valid');
            if (feedbackEl) {
                feedbackEl.className = 'small mt-1 text-success fw-semibold';
                feedbackEl.innerHTML = '<i class="fas fa-check-circle me-1"></i>' + msg;
            }
            return true;
        }

        inputEl.setCustomValidity(msg);
        inputEl.classList.remove('is-valid');
        if (showError) {
            inputEl.classList.add('is-invalid');
            if (feedbackEl) {
                feedbackEl.className = 'small mt-1 text-danger fw-semibold';
                feedbackEl.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>' + msg;
            }
        } else {
            inputEl.classList.remove('is-invalid');
            if (feedbackEl) {
                feedbackEl.className = 'small mt-1 text-muted';
                feedbackEl.textContent = defaultHint;
            }
        }
        return false;
    }

    function applyPhoneValidationUI(showError) {
        if (!phoneInput) return true;
        const result = validatePhoneValue(phoneInput.value);
        return setPhoneFieldState(
            phoneInput,
            phoneFeedback,
            result.valid,
            showError,
            result.valid ? '{{ __("Valid number") }}' : result.message,
            'Format accepté : 06 / 07 / 05 XX XX XX XX ou +212...'
        );
    }

    function applyPhoneConfirmValidationUI(showError) {
        if (!phoneConfirmInput) return true;
        const defaultHint = '{{ __("Retype your phone number to confirm") }}';
        const primary = validatePhoneValue(phoneInput ? phoneInput.value : '');
        const confirm = validatePhoneValue(phoneConfirmInput.value);

        if (!phoneConfirmInput.value.trim()) {
            return setPhoneFieldState(
                phoneConfirmInput,
                phoneConfirmFeedback,
                false,
                showError,
                'Veuillez confirmer votre numéro de téléphone.',
                defaultHint
            );
        }

        if (!confirm.valid) {
            return setPhoneFieldState(
                phoneConfirmInput,
                phoneConfirmFeedback,
                false,
                showError,
                confirm.message,
                defaultHint
            );
        }

        const matched = primary.valid && primary.normalized === confirm.normalized;
        return setPhoneFieldState(
            phoneConfirmInput,
            phoneConfirmFeedback,
            matched,
            showError,
            matched ? '{{ __("Valid number") }}' : '{{ __("Phone numbers do not match.") }}',
            defaultHint
        );
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            const digitsCount = (this.value || '').replace(/\D/g, '').length;
            applyPhoneValidationUI(digitsCount >= 10);
            if (phoneConfirmInput && phoneConfirmInput.value.trim() !== '') {
                applyPhoneConfirmValidationUI(digitsCount >= 10);
            }
        });
        phoneInput.addEventListener('blur', function () {
            if (this.value.trim() !== '') {
                applyPhoneValidationUI(true);
            }
            if (phoneConfirmInput && phoneConfirmInput.value.trim() !== '') {
                applyPhoneConfirmValidationUI(true);
            }
        });
        if (phoneInput.value.trim() !== '') {
            applyPhoneValidationUI(false);
        }
    }

    if (phoneConfirmInput) {
        phoneConfirmInput.addEventListener('input', function () {
            const digitsCount = (this.value || '').replace(/\D/g, '').length;
            applyPhoneConfirmValidationUI(digitsCount >= 10);
        });
        phoneConfirmInput.addEventListener('blur', function () {
            if (this.value.trim() !== '') {
                applyPhoneConfirmValidationUI(true);
            }
        });
        if (phoneConfirmInput.value.trim() !== '') {
            applyPhoneConfirmValidationUI(false);
        }
    }

    // Ensure delivery mode, manual city & both phone fields are validated on form submit
    const checkoutForm = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('checkout-submit-btn');
    const progressBar = document.getElementById('checkout-progress-bar');
    let isSubmitting = false;

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            if (!applyPhoneValidationUI(true)) {
                e.preventDefault();
                if (phoneInput) {
                    phoneInput.focus();
                    phoneInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            if (!applyPhoneConfirmValidationUI(true)) {
                e.preventDefault();
                if (phoneConfirmInput) {
                    phoneConfirmInput.focus();
                    phoneConfirmInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            if (!isStorePickup) {
                const val = cityInput.value.trim();
                if (val) {
                    selectManual(val);
                }
            }

            if (checkoutForm.checkValidity()) {
                isSubmitting = true;
                if (progressBar) {
                    progressBar.style.display = 'block';
                }
                if (submitBtn) {
                    submitBtn.classList.add('btn-checkout-submitting');
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> {{ __('Confirmation en cours...') }}';
                    setTimeout(function() {
                        submitBtn.disabled = true;
                    }, 20);
                }
            }
        });
    }

    // Current location button for street address & city detection
    const geoBtn = document.getElementById('btn-get-location');
    const geoBtnIcon = document.getElementById('geo-btn-icon');
    const geoBtnText = document.getElementById('geo-btn-text');
    const geoFeedback = document.getElementById('geo-location-feedback');
    const geoMessages = {
        useLocation: @json(__('Use my location')),
        locating: @json(__('Locating...')),
        detected: @json(__('Location detected successfully')),
        unsupported: @json(__('Geolocation is not supported by your browser.')),
        denied: @json(__('Location permission denied. Please allow access or enter your address manually.')),
        unavailable: @json(__('Unable to retrieve your location. Please enter your address manually.'))
    };

    function setGeoLoading(loading) {
        if (!geoBtn) return;
        geoBtn.disabled = loading;
        geoBtn.classList.toggle('is-loading', loading);
        if (loading) {
            geoBtn.classList.remove('is-located');
        }
        if (geoBtnIcon) {
            geoBtnIcon.className = loading ? 'fas fa-spinner fa-spin' : 'fas fa-location-crosshairs';
        }
        if (geoBtnText) {
            geoBtnText.textContent = loading ? geoMessages.locating : geoMessages.useLocation;
        }
    }

    function showGeoFeedback(type, htmlContent) {
        if (geoBtn) {
            geoBtn.classList.toggle('is-located', type === 'success');
        }
        if (!geoFeedback) return;
        const statusClass = type === 'error' ? 'is-error' : 'is-success';
        geoFeedback.className = 'mt-2';
        geoFeedback.innerHTML = `<div class="geo-status-pill ${statusClass}">${htmlContent}</div>`;
    }

    if (geoBtn) {
        geoBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                showGeoFeedback('error', `<i class="fas fa-exclamation-circle"></i><span>${escapeHtml(geoMessages.unsupported)}</span>`);
                return;
            }

            setGeoLoading(true);
            if (geoFeedback) geoFeedback.classList.add('d-none');

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const lat = Number(pos.coords.latitude).toFixed(6);
                    const lon = Number(pos.coords.longitude).toFixed(6);
                    const fallbackMaps = `https://www.google.com/maps?q=${lat},${lon}`;

                    fetch(`{{ route('checkout.reverseGeocode') }}?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status)))
                    .then(data => {
                        if (shippingAddressInput && data?.address) {
                            shippingAddressInput.value = data.address;
                        } else if (shippingAddressInput) {
                            shippingAddressInput.value = `Position GPS (${lat}, ${lon})`;
                        }
                        if (data?.city && !isStorePickup) {
                            selectManual(data.city);
                        }
                        const safeMapsUrl = (data?.maps_url && data.maps_url.startsWith('https://')) ? data.maps_url : fallbackMaps;
                        showGeoFeedback(
                            'success',
                            `<i class="fas fa-check-circle"></i><span>${escapeHtml(geoMessages.detected)}</span><a href="${escapeHtml(safeMapsUrl)}" target="_blank" rel="noopener noreferrer" class="geo-maps-link">Google Maps <i class="fas fa-arrow-up-right-from-square" style="font-size: 0.65rem;"></i></a>`
                        );
                    })
                    .catch(() => {
                        if (shippingAddressInput) {
                            shippingAddressInput.value = `Position GPS (${lat}, ${lon})`;
                        }
                        showGeoFeedback(
                            'success',
                            `<i class="fas fa-check-circle"></i><span>${escapeHtml(geoMessages.detected)}</span><a href="${escapeHtml(fallbackMaps)}" target="_blank" rel="noopener noreferrer" class="geo-maps-link">Google Maps <i class="fas fa-arrow-up-right-from-square" style="font-size: 0.65rem;"></i></a>`
                        );
                    })
                    .finally(() => {
                        setGeoLoading(false);
                    });
                },
                function(err) {
                    setGeoLoading(false);
                    const msg = (err && err.code === 1) ? geoMessages.denied : geoMessages.unavailable;
                    showGeoFeedback('error', `<i class="fas fa-exclamation-circle"></i><span>${escapeHtml(msg)}</span>`);
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 }
            );
        });
    }

    // Sync initial delivery mode & pre-filled city on page load
    syncDeliveryMethodUI(true);
});
</script>
@endpush

<style>
/* ================================================================
   CHECKOUT FORM INPUT CLARITY & MODERN POLISH
   ================================================================ */
.checkout-input-group {
    border-radius: 10px;
    background: #ffffff;
    transition: all 0.2s ease-in-out;
    border: 1.5px solid #cbd5e1;
    overflow: hidden;
}

.checkout-input-group:hover {
    border-color: #94a3b8;
}

.checkout-input-group:focus-within {
    border-color: #dc2626 !important;
    box-shadow: 0 0 0 3.5px rgba(220, 38, 38, 0.15) !important;
}

.checkout-input-group .input-group-text {
    background-color: #f8fafc !important;
    border: none !important;
    color: #64748b !important;
    padding-left: 14px;
    padding-right: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.checkout-input-group:focus-within .input-group-text {
    background-color: #fef2f2 !important;
    color: #dc2626 !important;
}

.checkout-input-group .form-control.checkout-input {
    background-color: #ffffff !important;
    border: none !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    padding: 11px 14px !important;
    color: #0f172a !important;
    box-shadow: none !important;
}

.checkout-input-group .form-control.checkout-input:focus {
    box-shadow: none !important;
    outline: none !important;
}

.checkout-input::placeholder {
    color: #94a3b8 !important;
    letter-spacing: 2.5px !important;
    font-weight: 400 !important;
    opacity: 0.8 !important;
}

#city-clear-btn {
    border: none !important;
    background: transparent !important;
    transition: color 0.15s ease;
}

#city-clear-btn:hover {
    color: #ef4444 !important;
}

/* ================================================================
   MOBILE VIEWPORT SPACING OPTIMIZATION
   Reduces wasted left/right padding so inputs have maximum width
   ================================================================ */
@media (max-width: 575.98px) {
    .checkout-page-wrapper {
        padding-top: 12px !important;
        padding-bottom: 24px !important;
    }

    .checkout-container {
        padding-left: 6px !important;
        padding-right: 6px !important;
    }

    .checkout-card {
        border-radius: 12px !important;
        margin-bottom: 12px !important;
    }

    .checkout-card .card-body {
        padding: 14px 10px !important;
    }

    .checkout-card .card-header {
        padding: 14px 10px 0 !important;
    }

    .checkout-title {
        font-size: 1.25rem !important;
        margin-bottom: 10px !important;
        padding-left: 4px !important;
    }

    .checkout-section-title {
        font-size: 1.05rem !important;
        margin-bottom: 12px !important;
    }

    .checkout-input-group {
        border-radius: 8px !important;
    }

    .checkout-input-group .input-group-text {
        padding-left: 10px !important;
        padding-right: 8px !important;
        font-size: 0.88rem !important;
    }

    .checkout-input-group .form-control.checkout-input {
        padding: 9px 10px !important;
        font-size: 0.92rem !important;
    }

    .checkout-input::placeholder {
        letter-spacing: 2px !important;
    }
}

/* ================================================================
   PROMO CODE / DISCOUNT BOX PRO STYLING
   Aligned with WINA SHOP Camera Brand Aesthetic
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

/* Promo Input Group */
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
    padding: 8px 6px !important;
    font-size: 0.88rem !important;
    font-weight: 700 !important;
    letter-spacing: 1.5px !important;
    color: #0f172a !important;
    box-shadow: none !important;
    outline: none !important;
}

.promo-field::placeholder {
    font-size: 0.8rem;
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

/* Applied Voucher Card */
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

    /* Promo Applied Card on Mobile */
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

    /* Input mode on Mobile */
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

/* ================================================================
   DELIVERY METHOD SELECTOR, STORE PICKUP CARD & GEOLOCATION UI
   ================================================================ */
.delivery-option-card {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 14px;
    padding-inline-end: 36px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.22, 1, 0.36, 1);
    height: 100%;
    margin: 0;
}

.delivery-option-card:hover {
    border-color: #94a3b8;
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
}

.delivery-option-card .delivery-option-radio {
    position: absolute;
    opacity: 0;
    width: 1px;
    height: 1px;
    pointer-events: none;
}

.delivery-option-card:has(.delivery-option-radio:focus-visible) {
    outline: 2px solid #dc2626;
    outline-offset: 2px;
}

.delivery-option-card.is-pickup:has(.delivery-option-radio:focus-visible) {
    outline-color: #16a34a;
}

.delivery-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.98rem;
    background: #fef2f2;
    color: #dc2626;
    transition: all 0.2s ease;
}

.delivery-card-icon.is-pickup {
    background: #f0fdf4;
    color: #16a34a;
}

.delivery-card-body {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
    min-width: 0;
    flex: 1;
}

.delivery-card-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
}

.delivery-card-subtitle {
    font-size: 0.77rem;
    font-weight: 500;
    color: #64748b;
    line-height: 1.35;
}

.delivery-free-badge {
    display: inline-flex;
    align-items: center;
    margin-top: 4px;
    padding: 2px 9px;
    border-radius: 999px;
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.01em;
}

.delivery-check-indicator {
    position: absolute;
    top: 11px;
    inset-inline-end: 11px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.62rem;
    transition: all 0.2s ease;
}

.delivery-check-indicator i {
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.18s ease;
}

.delivery-option-card.is-selected {
    border-color: #dc2626;
    background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
    box-shadow: 0 4px 14px rgba(220, 38, 38, 0.12), 0 0 0 3px rgba(220, 38, 38, 0.08);
}

.delivery-option-card.is-selected .delivery-card-icon {
    background: #dc2626;
    color: #ffffff;
}

.delivery-option-card.is-selected .delivery-check-indicator {
    background: #dc2626;
    border-color: #dc2626;
}

.delivery-option-card.is-selected .delivery-check-indicator i {
    opacity: 1;
    transform: scale(1);
}

.delivery-option-card.is-pickup.is-selected {
    border-color: #16a34a;
    background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
    box-shadow: 0 4px 14px rgba(22, 163, 74, 0.12), 0 0 0 3px rgba(22, 163, 74, 0.09);
}

.delivery-option-card.is-pickup.is-selected .delivery-card-icon {
    background: #16a34a;
    color: #ffffff;
}

.delivery-option-card.is-pickup.is-selected .delivery-check-indicator {
    background: #16a34a;
    border-color: #16a34a;
}

.pickup-store-info-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
    border: 1.5px solid #bbf7d0;
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 2px 10px rgba(22, 163, 74, 0.06);
}

.pickup-store-badge {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #16a34a;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.92rem;
}

.pickup-store-content {
    flex: 1;
    min-width: 0;
}

.pickup-store-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 6px 10px;
}

.pickup-store-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
}

.pickup-zero-fee-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 999px;
    background: #dcfce7;
    color: #15803d;
    font-size: 0.72rem;
    font-weight: 700;
}

.pickup-store-address {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    margin-top: 6px;
    font-size: 0.82rem;
    font-weight: 500;
    color: #475569;
    line-height: 1.45;
}

.pickup-store-address i {
    color: #16a34a;
    margin-top: 3px;
    font-size: 0.78rem;
}

.pickup-store-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
}

.btn-pickup-phone {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    font-size: 0.78rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.18s ease;
}

.btn-pickup-phone:hover {
    border-color: #0f172a;
    background: #f8fafc;
    color: #0f172a;
}

.btn-pickup-maps {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1.5px solid #16a34a;
    background: #16a34a;
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.22);
    transition: all 0.18s ease;
}

.btn-pickup-maps:hover {
    background: #15803d;
    border-color: #15803d;
    color: #ffffff;
    transform: translateY(-1px);
}

.checkout-address-group {
    align-items: center;
    padding-inline-end: 6px;
}

.checkout-address-group .input-group-text {
    align-self: stretch;
    display: flex;
    align-items: center;
}

.checkout-address-group .form-control.checkout-input {
    min-width: 0;
    border-radius: 0 !important;
}

.btn-geo-locate {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    margin: 0 !important;
    border-radius: 7px !important;
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #dc2626;
    font-size: 0.77rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
    cursor: pointer;
    transition: all 0.18s ease;
}

.btn-geo-locate:hover:not(:disabled) {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
}

.btn-geo-locate:disabled,
.btn-geo-locate.is-loading {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
    cursor: wait;
    opacity: 0.9;
}

.btn-geo-locate.is-located {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #15803d;
}

.btn-geo-locate.is-located:hover:not(:disabled) {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(22, 163, 74, 0.22);
}

.btn-geo-locate:focus-visible {
    outline: 2px solid #dc2626;
    outline-offset: 2px;
}

.geo-status-pill {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.79rem;
    font-weight: 600;
    line-height: 1.35;
}

.geo-status-pill.is-success {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.geo-status-pill.is-error {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

.geo-maps-link {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid #86efac;
    color: #15803d;
    font-size: 0.73rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.16s ease;
}

.geo-maps-link:hover {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
}

@media (max-width: 575.98px) {
    .delivery-option-card {
        padding: 11px 12px;
        padding-inline-end: 34px;
        gap: 10px;
    }

    .delivery-card-icon {
        width: 34px;
        height: 34px;
        font-size: 0.9rem;
    }

    .pickup-store-info-card {
        padding: 12px 13px;
        gap: 10px;
    }

    .checkout-address-group .btn-geo-locate {
        padding: 6px 9px;
        font-size: 0.73rem;
    }
}
</style>
@endsection
