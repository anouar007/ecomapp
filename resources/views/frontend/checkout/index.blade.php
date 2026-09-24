@extends('layouts.frontend')

@section('meta_title', 'Finaliser ma commande — Coopérative Aït Oumdis')

@section('content')
<style>
/* ==========================================================================
   EXCLUSIVE FULL-SCREEN PRO MOBILE & DESKTOP CHECKOUT EXPERIENCE
   ========================================================================== */
.sp-checkout-page {
    background-color: #faf8f4;
    background-image: radial-gradient(rgba(194, 141, 50, 0.08) 1px, transparent 1px);
    background-size: 22px 22px;
    min-height: 100vh;
    padding: 30px 0 140px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0c261e;
}

/* Header & Breadcrumb */
.sp-checkout-header {
    margin-bottom: 24px;
}
.sp-checkout-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.84rem;
    margin-bottom: 10px;
    color: #6b7a72;
}
.sp-checkout-breadcrumb a {
    color: #6b7a72;
    text-decoration: none;
    transition: color 0.2s ease;
}
.sp-checkout-breadcrumb a:hover {
    color: #c28d32;
}
.sp-checkout-breadcrumb .sep {
    color: #c28d32;
    opacity: 0.7;
}
.sp-checkout-breadcrumb .current {
    color: #0c261e;
    font-weight: 600;
}

.sp-checkout-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(1.65rem, 3.2vw, 2.35rem);
    font-weight: 700;
    color: #0c261e;
    margin: 0 0 10px;
    letter-spacing: -0.01em;
    line-height: 1.2;
}
.sp-checkout-pill-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.sp-reassurance-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid rgba(226, 173, 80, 0.4);
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #0c261e;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.sp-reassurance-pill .dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
}

/* Checkout Container & Grid */
.sp-checkout-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Cards System */
.sp-checkout-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(12, 38, 30, 0.07);
    box-shadow: 0 4px 20px rgba(12, 38, 30, 0.04);
    padding: 28px 24px;
    margin-bottom: 20px;
}
.sp-card-step-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(12, 38, 30, 0.06);
}
.sp-step-badge {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0c261e;
    color: #e2ad50;
    font-weight: 800;
    font-size: 0.90rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1.5px solid rgba(226, 173, 80, 0.4);
}
.sp-step-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: #0c261e;
    margin: 0;
}
.sp-step-sub {
    font-size: 0.82rem;
    color: #6b7a72;
    margin-left: auto;
}

/* Form Groups & Inputs */
.sp-fg {
    margin-bottom: 18px;
}
.sp-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #3b4d44;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 7px;
}
.sp-label .opt {
    text-transform: none;
    font-weight: 500;
    color: #8c9c94;
    font-size: 0.76rem;
    letter-spacing: normal;
}
.sp-input, .sp-select {
    width: 100%;
    height: 52px;
    font-size: 16px !important; /* Critical: Prevents iOS auto-zoom */
    padding: 12px 16px;
    background-color: #fcfbf9;
    border: 1.5px solid rgba(12, 38, 30, 0.12);
    border-radius: 12px;
    color: #0c261e;
    font-weight: 500;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: inherit;
    box-sizing: border-box;
}
.sp-input::placeholder {
    color: #9baaa2;
    font-size: 0.92rem;
}
.sp-input:focus, .sp-select:focus {
    background-color: #ffffff;
    border-color: #0c261e;
    box-shadow: 0 0 0 3.5px rgba(226, 173, 80, 0.25);
    outline: none;
}
.sp-input.is-invalid, .sp-select.is-invalid {
    border-color: #ef4444;
    background-color: #fff9f9;
}

/* Payment Method Card */
.sp-payment-radio-card {
    border: 2px solid #0c261e;
    background: linear-gradient(180deg, #fbfbf9 0%, #ffffff 100%);
    border-radius: 14px;
    padding: 18px 18px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    box-shadow: 0 4px 14px rgba(12, 38, 30, 0.06);
    position: relative;
    user-select: none;
}
.sp-payment-radio-circle {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid #0c261e;
    background: #0c261e;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
    flex-shrink: 0;
}
.sp-payment-radio-circle::after {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #e2ad50;
}
.sp-payment-card-body {
    flex-grow: 1;
}
.sp-payment-card-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0c261e;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 5px;
}
.sp-badge-recommended {
    background: rgba(226, 173, 80, 0.18);
    color: #9e6f1a;
    border: 1px solid rgba(226, 173, 80, 0.4);
    font-size: 0.70rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 9999px;
    text-transform: uppercase;
}
.sp-payment-card-sub {
    font-size: 0.86rem;
    color: #6b7a72;
    line-height: 1.45;
    margin-bottom: 12px;
}
.sp-payment-tags {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.sp-payment-tag {
    font-size: 0.76rem;
    font-weight: 600;
    color: #0c261e;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f4efe6;
    padding: 4px 10px;
    border-radius: 6px;
}

/* Order Summary Items */
.sp-summary-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(12, 38, 30, 0.07);
    box-shadow: 0 4px 20px rgba(12, 38, 30, 0.04);
    padding: 26px 24px;
}
.sp-order-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid rgba(12, 38, 30, 0.06);
}
.sp-order-item:first-child {
    padding-top: 0;
}
.sp-item-img-wrap {
    position: relative;
    width: 62px;
    height: 62px;
    border-radius: 12px;
    background: #fbfbf9;
    border: 1px solid rgba(12, 38, 30, 0.08);
    flex-shrink: 0;
    overflow: visible;
}
.sp-item-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 12px;
    padding: 3px;
}
.sp-item-qty-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #0c261e;
    color: #e2ad50;
    border: 2px solid #ffffff;
    font-weight: 800;
    font-size: 0.70rem;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.sp-item-info {
    flex-grow: 1;
    min-width: 0;
}
.sp-item-title {
    font-size: 0.94rem;
    font-weight: 700;
    color: #0c261e;
    margin: 0 0 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sp-item-meta {
    font-size: 0.78rem;
    color: #6b7a72;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sp-item-price {
    font-weight: 700;
    font-size: 1rem;
    color: #0c261e;
    flex-shrink: 0;
}

/* Pricing Breakdown */
.sp-pricing-box {
    background: #fbfbf9;
    border-radius: 12px;
    padding: 14px 16px;
    margin: 18px 0;
    border: 1px dashed rgba(226, 173, 80, 0.45);
}
.sp-price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.90rem;
    margin-bottom: 8px;
    color: #6b7a72;
}
.sp-price-row:last-child {
    margin-bottom: 0;
}
.sp-price-val {
    font-weight: 700;
    color: #0c261e;
}
.sp-free-tag {
    background: #ecfdf5;
    color: #059669;
    font-weight: 800;
    font-size: 0.78rem;
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px solid rgba(5, 150, 105, 0.2);
}
.sp-total-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding-top: 14px;
    margin-top: 14px;
    border-top: 1.5px solid rgba(12, 38, 30, 0.08);
}
.sp-total-label-wrap {
    display: flex;
    flex-direction: column;
}
.sp-total-main-label {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0c261e;
}
.sp-total-sub-label {
    font-size: 0.78rem;
    color: #6b7a72;
}
.sp-total-main-val {
    font-size: 1.7rem;
    font-weight: 800;
    color: #0c261e;
    letter-spacing: -0.02em;
    line-height: 1;
}

/* Desktop Submit Button */
.sp-desktop-submit-btn {
    width: 100%;
    height: 56px;
    background: linear-gradient(135deg, #0c261e 0%, #164638 100%);
    color: #ffffff;
    border: 1.5px solid rgba(226, 173, 80, 0.5);
    border-radius: 9999px;
    font-size: 1.05rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 8px 24px rgba(12, 38, 30, 0.35);
    cursor: pointer;
    transition: all 0.22s ease;
    margin-top: 18px;
}
.sp-desktop-submit-btn:hover {
    background: linear-gradient(135deg, #0f3026 0%, #1c5243 100%);
    border-color: #e2ad50;
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(12, 38, 30, 0.42), 0 0 16px rgba(226, 173, 80, 0.25);
}

/* Reassurance Strip */
.sp-trust-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-top: 18px;
    text-align: center;
}
.sp-trust-item {
    background: #fbfbf9;
    border-radius: 10px;
    padding: 10px 6px;
    border: 1px solid rgba(12, 38, 30, 0.05);
}
.sp-trust-icon {
    font-size: 1.15rem;
    color: #c28d32;
    margin-bottom: 4px;
}
.sp-trust-label {
    font-size: 0.70rem;
    font-weight: 700;
    color: #0c261e;
    line-height: 1.2;
}

/* Mobile Sticky Bottom Bar */
.sp-mobile-sticky-bar {
    display: none;
}

/* ==========================================================================
   MOBILE FULL-SCREEN REFINEMENTS (@media max-width: 768px)
   ========================================================================== */
@media (max-width: 768px) {
    .sp-checkout-page {
        padding: 10px 0 120px 0 !important;
        background-size: 16px 16px;
    }
    
    /* Maximum edge-to-edge width with safe 10px borders */
    .sp-checkout-container {
        padding-left: 10px !important;
        padding-right: 10px !important;
        max-width: 100% !important;
    }
    .sp-checkout-row {
        --bs-gutter-x: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .sp-checkout-col {
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 100% !important;
    }
    .sp-checkout-card {
        padding: 18px 14px !important;
        border-radius: 16px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 2px 10px rgba(12, 38, 30, 0.04) !important;
    }
    .sp-checkout-header {
        margin-bottom: 14px !important;
        padding: 0 4px !important;
    }
    .sp-checkout-title {
        font-size: 1.45rem !important;
        margin: 2px 0 6px !important;
    }
    .sp-reassurance-pill {
        font-size: 0.74rem !important;
        padding: 5px 12px !important;
    }

    /* Inputs take full mobile comfort */
    .sp-input, .sp-select {
        height: 50px !important;
        padding: 10px 14px !important;
        border-radius: 10px !important;
    }
    .sp-fg {
        margin-bottom: 14px !important;
    }

    /* Hide desktop submit in card on mobile */
    .sp-desktop-submit-wrap {
        display: none !important;
    }

    /* Mobile Sticky Checkout Bar (Fixed at bottom) */
    .sp-mobile-sticky-bar {
        display: flex !important;
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        padding: 10px 14px calc(10px + env(safe-area-inset-bottom, 0px)) !important;
        border-top: 1.5px solid rgba(12, 38, 30, 0.08) !important;
        box-shadow: 0 -8px 25px rgba(12, 38, 30, 0.12) !important;
        z-index: 10000 !important;
        align-items: center !important;
        gap: 12px !important;
    }
    .sp-sticky-total-block {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
    }
    .sp-sticky-total-label {
        font-size: 0.68rem;
        color: #6b7a72;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.04em;
        line-height: 1;
    }
    .sp-sticky-total-val {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0c261e;
        line-height: 1.2;
    }
    .sp-sticky-submit-btn {
        flex-grow: 1;
        height: 50px;
        background: linear-gradient(135deg, #0c261e 0%, #174236 100%);
        color: #ffffff !important;
        border: 1.5px solid rgba(226, 173, 80, 0.45);
        border-radius: 9999px;
        font-weight: 800;
        font-size: 0.95rem;
        letter-spacing: 0.01em;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(12, 38, 30, 0.25);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .sp-sticky-submit-btn:active {
        transform: scale(0.98);
    }

    /* Distraction-free: hide heavy marketing footer on mobile checkout */
    body.checkout-page .sp-footer {
        display: none !important;
    }
    #spFloatingCheckout {
        display: none !important;
    }
}
</style>

<div class="sp-checkout-page">
    <div class="sp-checkout-container">
        
        <!-- Header -->
        <div class="sp-checkout-header">
            <nav class="sp-checkout-breadcrumb" aria-label="Fil d'Ariane">
                <a href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Accueil</a>
                <span class="sep">/</span>
                <a href="{{ route('cart.index') }}">Panier</a>
                <span class="sep">/</span>
                <span class="current">Commande</span>
            </nav>
            <h1 class="sp-checkout-title">Finaliser ma Commande</h1>
            <div class="sp-checkout-pill-bar">
                <div class="sp-reassurance-pill">
                    <span class="dot"></span>
                    <span>Paiement en espèces à la livraison (COD)</span>
                </div>
                <div class="sp-reassurance-pill d-none d-sm-inline-flex">
                    <i class="fas fa-shield-alt text-success"></i>
                    <span>Commande 100% Sécurisée</span>
                </div>
            </div>
        </div>

        @if (isset($errors) && $errors->any())
        <div class="alert alert-danger rounded-4 mb-4 border-0 shadow-sm" style="background: #fff5f5; border-left: 4px solid #ef4444 !important; color: #991b1b;">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fas fa-exclamation-circle text-danger"></i>
                <strong style="font-size: 0.92rem;">Veuillez corriger les informations suivantes :</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row sp-checkout-row g-lg-4">
            
            <!-- Left Column: Delivery & Payment Forms -->
            <div class="col-lg-7 sp-checkout-col">
                
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf

                    <!-- 1. Delivery Details -->
                    <div class="sp-checkout-card">
                        <div class="sp-card-step-header">
                            <span class="sp-step-badge">1</span>
                            <h2 class="sp-step-title">Informations de livraison</h2>
                            <span class="sp-step-sub d-none d-sm-inline">Étape 1 sur 2</span>
                        </div>

                        <div class="row g-3">
                            <!-- Full Name -->
                            <div class="col-12 sp-fg">
                                <label class="sp-label" for="customer_name">
                                    <i class="fas fa-user-circle" style="color: #c28d32;"></i>
                                    <span>Nom & Prénom <span class="text-danger">*</span></span>
                                </label>
                                <input type="text" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       class="sp-input @error('customer_name') is-invalid @enderror" 
                                       placeholder="Ex: Mohamed Alami" 
                                       value="{{ old('customer_name', auth()->user()->name ?? '') }}" 
                                       required 
                                       autocomplete="name">
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6 sp-fg">
                                <label class="sp-label" for="customer_phone">
                                    <i class="fas fa-phone-alt" style="color: #c28d32;"></i>
                                    <span>Téléphone (WhatsApp) <span class="text-danger">*</span></span>
                                </label>
                                <input type="tel" 
                                       id="customer_phone" 
                                       name="customer_phone" 
                                       class="sp-input @error('customer_phone') is-invalid @enderror" 
                                       placeholder="06 12 34 56 78" 
                                       value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" 
                                       required 
                                       autocomplete="tel">
                            </div>

                            <!-- Email (Optional) -->
                            <div class="col-md-6 sp-fg">
                                <label class="sp-label" for="customer_email">
                                    <i class="fas fa-envelope" style="color: #c28d32;"></i>
                                    <span>E-mail <span class="opt">(Optionnel)</span></span>
                                </label>
                                <input type="email" 
                                       id="customer_email" 
                                       name="customer_email" 
                                       class="sp-input @error('customer_email') is-invalid @enderror" 
                                       placeholder="Pour le suivi de votre colis" 
                                       value="{{ old('customer_email', auth()->user()->email ?? '') }}" 
                                       autocomplete="email">
                            </div>

                            <!-- City -->
                            <div class="col-md-6 sp-fg">
                                <label class="sp-label" for="shipping_city">
                                    <i class="fas fa-city" style="color: #c28d32;"></i>
                                    <span>Ville <span class="text-danger">*</span></span>
                                </label>
                                <select id="shipping_city" 
                                        name="shipping_city" 
                                        class="sp-select @error('shipping_city') is-invalid @enderror" 
                                        required 
                                        autocomplete="address-level2">
                                    <option value="" disabled {{ old('shipping_city') ? '' : 'selected' }}>Sélectionnez votre ville</option>
                                    @php
                                        $cities = [
                                            'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger', 'Agadir', 
                                            'Meknès', 'Oujda', 'Kenitra', 'Tétouan', 'Salé', 'Temara', 
                                            'Safi', 'Mohammedia', 'Khouribga', 'El Jadida', 'Béni Mellal', 
                                            'Aït Melloul', 'Nador', 'Laâyoune', 'Dakhla', 'Al Hoceïma', 
                                            'Settat', 'Berrechid', 'Khemisset', 'Guelmim', 'Berkane', 
                                            'Taourirt', 'Taroudant', 'Ouarzazate', 'Taza', 'Essaouira', 
                                            'Larache', 'Ksar El Kebir', 'Tiznit', 'Azilal', 'Aït Oumdis'
                                        ];
                                    @endphp
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}" {{ old('shipping_city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                    <option value="Autre ville" {{ old('shipping_city') == 'Autre ville' ? 'selected' : '' }}>Autre ville au Maroc</option>
                                </select>
                            </div>

                            <!-- State / Region -->
                            <div class="col-md-6 sp-fg">
                                <label class="sp-label" for="shipping_state">
                                    <i class="fas fa-map-marked-alt" style="color: #c28d32;"></i>
                                    <span>Région / Quartier <span class="opt">(Optionnel)</span></span>
                                </label>
                                <input type="text" 
                                       id="shipping_state" 
                                       name="shipping_state" 
                                       class="sp-input @error('shipping_state') is-invalid @enderror" 
                                       placeholder="Ex: Maârif, Guéliz, Agdal..." 
                                       value="{{ old('shipping_state') }}">
                            </div>

                            <!-- Exact Address -->
                            <div class="col-12 sp-fg">
                                <label class="sp-label" for="shipping_address">
                                    <i class="fas fa-map-marker-alt" style="color: #c28d32;"></i>
                                    <span>Adresse exacte de livraison <span class="text-danger">*</span></span>
                                </label>
                                <input type="text" 
                                       id="shipping_address" 
                                       name="shipping_address" 
                                       class="sp-input @error('shipping_address') is-invalid @enderror" 
                                       placeholder="Quartier, N° rue, bâtiment, étage..." 
                                       value="{{ old('shipping_address') }}" 
                                       required 
                                       autocomplete="street-address">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Payment Method -->
                    <div class="sp-checkout-card">
                        <div class="sp-card-step-header">
                            <span class="sp-step-badge">2</span>
                            <h2 class="sp-step-title">Mode de paiement</h2>
                            <span class="sp-step-sub d-none d-sm-inline">Étape 2 sur 2</span>
                        </div>

                        <div class="sp-payment-radio-card">
                            <input type="radio" name="payment_method" value="cod" checked style="display: none;">
                            <div class="sp-payment-radio-circle"></div>
                            <div class="sp-payment-card-body">
                                <div class="sp-payment-card-title">
                                    <span>Paiement en espèces à la livraison</span>
                                    <span class="sp-badge-recommended">Recommandé</span>
                                </div>
                                <div class="sp-payment-card-sub">
                                    Vous ne payez <strong>aucun centime à l'avance</strong>. Le règlement se fait en espèces directement au livreur après réception de votre commande.
                                </div>
                                <div class="sp-payment-tags">
                                    <span class="sp-payment-tag"><i class="fas fa-check-circle text-success"></i> Sans prépaiement</span>
                                    <span class="sp-payment-tag"><i class="fas fa-box-open text-primary"></i> Colis vérifié</span>
                                    <span class="sp-payment-tag"><i class="fas fa-shipping-fast" style="color: #c28d32;"></i> Livraison 24-48h</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-5 sp-checkout-col">
                <div class="sp-summary-card sticky-top" style="top: 100px;">
                    
                    <div class="sp-card-step-header" style="margin-bottom: 16px;">
                        <span class="sp-step-badge" style="background: rgba(226,173,80,0.2); color: #0c261e; border-color: #c28d32;">
                            <i class="fas fa-shopping-bag" style="font-size: 0.85rem; color: #c28d32;"></i>
                        </span>
                        <h2 class="sp-step-title" style="font-size: 1.18rem;">Récapitulatif de commande</h2>
                        <span class="sp-step-sub">{{ count($cart) }} soin{{ count($cart) > 1 ? 's' : '' }}</span>
                    </div>

                    <!-- Items List -->
                    <div class="sp-order-items-list mb-3">
                        @foreach($cart as $id => $details)
                        <div class="sp-order-item">
                            <div class="sp-item-img-wrap">
                                @if(!empty($details['image']))
                                    <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="sp-item-img">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100" style="color: #c28d32;">
                                        <i class="fas fa-leaf"></i>
                                    </div>
                                @endif
                                <span class="sp-item-qty-badge">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="sp-item-info">
                                <h3 class="sp-item-title" title="{{ $details['name'] }}">{{ $details['name'] }}</h3>
                                <div class="sp-item-meta">
                                    <span>{{ $details['category_name'] ?? 'Soin Botanique Bio' }}</span>
                                </div>
                            </div>
                            <div class="sp-item-price">
                                {{ currency($details['price'] * $details['quantity']) }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Price Calculation -->
                    <div class="sp-pricing-box">
                        <div class="sp-price-row">
                            <span>Sous-total</span>
                            <span class="sp-price-val">{{ currency($total) }}</span>
                        </div>
                        <div class="sp-price-row">
                            <span>Frais de livraison</span>
                            <span class="sp-free-tag">GRATUITE</span>
                        </div>
                    </div>

                    <!-- Total Row -->
                    <div class="sp-total-row">
                        <div class="sp-total-label-wrap">
                            <span class="sp-total-main-label">Total à payer</span>
                            <span class="sp-total-sub-label">TVA comprise • Paiement à la réception</span>
                        </div>
                        <div class="sp-total-main-val">
                            {{ currency($total) }}
                        </div>
                    </div>

                    <!-- Desktop Submit Button -->
                    <div class="sp-desktop-submit-wrap">
                        <button type="submit" form="checkout-form" class="sp-desktop-submit-btn" id="desktopSubmitBtn">
                            <i class="fas fa-lock"></i>
                            <span>Confirmer la commande ({{ currency($total) }})</span>
                            <i class="fas fa-arrow-right ms-auto"></i>
                        </button>
                    </div>

                    <!-- Trust Strip -->
                    <div class="sp-trust-strip">
                        <div class="sp-trust-item">
                            <div class="sp-trust-icon"><i class="fas fa-leaf"></i></div>
                            <div class="sp-trust-label">100% Bio & Terroir</div>
                        </div>
                        <div class="sp-trust-item">
                            <div class="sp-trust-icon"><i class="fas fa-truck"></i></div>
                            <div class="sp-trust-label">Partout au Maroc</div>
                        </div>
                        <div class="sp-trust-item">
                            <div class="sp-trust-icon"><i class="fas fa-headset"></i></div>
                            <div class="sp-trust-label">Support WhatsApp</div>
                        </div>
                    </div>

                    <!-- Back to Cart Link -->
                    <div class="text-center mt-3">
                        <a href="{{ route('cart.index') }}" class="text-decoration-none" style="font-size: 0.82rem; color: #6b7a72; font-weight: 600;">
                            <i class="fas fa-arrow-left me-1"></i> Modifier mon panier
                        </a>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<!-- ==========================================================================
     MOBILE STICKY BOTTOM CONFIRMATION BAR
     ========================================================================== -->
<div class="sp-mobile-sticky-bar">
    <div class="sp-sticky-total-block">
        <span class="sp-sticky-total-label">Total net</span>
        <span class="sp-sticky-total-val">{{ currency($total) }}</span>
    </div>
    <button type="submit" form="checkout-form" class="sp-sticky-submit-btn" id="mobileSubmitBtn">
        <i class="fas fa-lock"></i>
        <span>Confirmer la commande</span>
        <i class="fas fa-arrow-right"></i>
    </button>
</div>

@push('scripts')
<script>
    // Ensure checkout body class is active
    document.body.classList.add('checkout-page');

    // Prevent duplicate submissions and provide interactive feedback
    var checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            var nameField = document.getElementById('customer_name');
            var phoneField = document.getElementById('customer_phone');
            var cityField = document.getElementById('shipping_city');
            var addressField = document.getElementById('shipping_address');

            var invalid = null;
            if (!nameField.value.trim()) invalid = nameField;
            else if (!phoneField.value.trim()) invalid = phoneField;
            else if (!cityField.value) invalid = cityField;
            else if (!addressField.value.trim()) invalid = addressField;

            if (invalid) {
                e.preventDefault();
                invalid.focus();
                invalid.classList.add('is-invalid');
                invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }

            var submitBtns = [
                document.getElementById('desktopSubmitBtn'),
                document.getElementById('mobileSubmitBtn')
            ];

            submitBtns.forEach(function (btn) {
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Validation en cours…</span>';
                }
            });
        });

        // Clear invalid red border on input
        checkoutForm.querySelectorAll('input, select').forEach(function (el) {
            el.addEventListener('input', function () {
                if (this.value.trim()) this.classList.remove('is-invalid');
            });
            el.addEventListener('change', function () {
                if (this.value.trim()) this.classList.remove('is-invalid');
            });
        });
    }
</script>
@endpush
@endsection
