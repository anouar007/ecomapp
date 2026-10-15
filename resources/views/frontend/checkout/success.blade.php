@extends('layouts.frontend')

@section('meta_title', __('Order confirmed') . ' #' . $order->order_number . ' — ' . setting('app_name', 'WINA SHOP'))

@php
    $isAr = app()->getLocale() === 'ar';
    $waRawPhone = setting('social_whatsapp', setting('contact_phone', '0629035777'));
    $waCleanPhone = preg_replace('/[^0-9]/', '', $waRawPhone);
    if (!str_starts_with($waCleanPhone, '212')) {
        $waCleanPhone = '212' . ltrim($waCleanPhone, '0');
    }
    $waMessage = $isAr 
        ? "مرحبا WINA SHOP، أود الاستفسار بخصوص طلبيتي رقم #" . $order->order_number 
        : "Bonjour WINA SHOP, je vous contacte concernant ma commande #" . $order->order_number;
    $waLink = "https://wa.me/{$waCleanPhone}?text=" . urlencode($waMessage);
@endphp

@section('content')
<div class="order-success-page py-4 py-lg-5">
    <div class="container">
        
        {{-- BREADCRUMB --}}
        <nav aria-label="breadcrumb" class="mb-4 d-none d-md-block">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted"><i class="fas fa-home me-1"></i>{{ __('Accueil') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">{{ __('Boutique') }}</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page">{{ __('Commande confirmée') }}</li>
            </ol>
        </nav>

        {{-- TOP HERO CONFIRMATION CARD --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white order-success-hero">
            <div class="card-body p-4 p-md-5 text-center position-relative">
                <div class="order-hero-bg-accent"></div>
                
                {{-- Celebration Icon with Glowing Pulse --}}
                <div class="order-success-icon-wrap mx-auto mb-3">
                    <div class="order-success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>

                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 mb-2 font-monospace" style="font-size: 0.82rem; font-weight: 500;">
                    <i class="fas fa-circle-check me-1.5"></i>{{ __('Commande enregistrée avec succès') }}
                </span>

                <h1 class="order-hero-title mb-2 text-dark">{{ __('Order confirmed!') }}</h1>
                <p class="order-hero-subtitle text-muted mb-3 mx-auto" style="max-width: 620px;">
                    {{ __('Thank you for your trust,') }} <strong class="text-dark">{{ $order->customer_name }}</strong> ! {{ __('We have received your order and are preparing it right away.') }}
                </p>

                {{-- Order Reference Pill & WhatsApp Button --}}
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2.5 mt-2">
                    <div class="order-ref-pill d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-light border">
                        <span class="text-muted small">{{ __('Order:') }}</span>
                        <span class="text-dark font-monospace order-ref-code" id="orderRefText">#{{ $order->order_number }}</span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1 copy-ref-btn" onclick="copyOrderReference('{{ $order->order_number }}')" title="{{ __('Copy') }}">
                            <i class="far fa-copy" id="copyIcon"></i>
                        </button>
                    </div>

                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 order-hero-wa-btn shadow-xs">
                        <i class="fab fa-whatsapp" style="font-size: 1rem;"></i>
                        <span>{{ __('Assistance WhatsApp rapide') }}</span>
                    </a>
                </div>
            </div>

            {{-- 4-STEP ORDER TIMELINE STEPPER --}}
            <div class="order-stepper-wrap border-top px-3 py-3.5 px-md-5 bg-slate-50">
                <div class="order-stepper d-flex justify-content-between position-relative">
                    <div class="order-step is-complete">
                        <div class="order-step-circle">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title text-dark">{{ __('Validée') }}</span>
                            <span class="order-step-desc text-muted d-none d-sm-block">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'Aujourd\'hui' }}</span>
                        </div>
                    </div>

                    <div class="order-step is-active">
                        <div class="order-step-circle">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title text-dark">{{ __('Préparation') }}</span>
                            <span class="order-step-desc text-muted d-none d-sm-block">{{ __('Vérification & emballage') }}</span>
                        </div>
                    </div>

                    <div class="order-step">
                        <div class="order-step-circle">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title text-dark">{{ __('Expédition') }}</span>
                            <span class="order-step-desc text-muted d-none d-sm-block">{{ __('24h à 48h partout au Maroc') }}</span>
                        </div>
                    </div>

                    <div class="order-step">
                        <div class="order-step-circle">
                            <i class="fas fa-hand-holding-dollar"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title text-dark">{{ __('Livraison') }}</span>
                            <span class="order-step-desc text-muted d-none d-sm-block">{{ __('Règlement à réception') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- LEFT COLUMN: ORDER ITEMS & DELIVERY DETAILS --}}
            <div class="col-12 col-lg-7">
                
                {{-- ORDER ITEMS CARD --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
                    <div class="card-header bg-white px-4 py-3.5 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="order-card-icon-box text-primary bg-primary-subtle rounded-3">
                                <i class="fas fa-boxes-stacked"></i>
                            </span>
                            <div>
                                <h5 class="m-0 fs-6 text-dark" style="font-weight: 500;">{{ __('Articles commandés') }}</h5>
                                <span class="text-muted small" style="font-size: 0.76rem;">{{ $order->items->count() }} {{ __('référence(s) incluse(s)') }}</span>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1.5" style="font-size: 0.78rem;">
                            <i class="fas fa-clock text-warning me-1"></i>{{ ucfirst($order->status ?? 'En attente') }}
                        </span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 order-items-table">
                                <thead class="bg-light small text-muted text-uppercase">
                                    <tr>
                                        <th class="border-0 ps-4 py-2.5">{{ __('Produit') }}</th>
                                        <th class="border-0 text-center py-2.5" style="width: 120px;">{{ __('Prix unitaire') }}</th>
                                        <th class="border-0 text-center py-2.5" style="width: 75px;">{{ __('Qté') }}</th>
                                        <th class="border-0 text-end pe-4 py-2.5" style="width: 130px;">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    @php
                                        $prodImg = $item->product && $item->product->thumbnail ? $item->product->thumbnail : asset('images/camera/cat_cameras.jpg');
                                        $prodUrl = $item->product ? route('shop.show', $item->product->slug ?: $item->product->id) : '#';
                                    @endphp
                                    <tr class="order-item-row">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="{{ $prodUrl }}" class="order-item-thumb-link flex-shrink-0">
                                                    <div class="order-item-thumb-box">
                                                        <img src="{{ $prodImg }}" alt="{{ $item->product_name }}" class="order-item-thumb-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                                    </div>
                                                </a>
                                                <div class="min-w-0">
                                                    <a href="{{ $prodUrl }}" class="order-item-title text-decoration-none text-dark d-block text-truncate" style="max-width: 280px;">
                                                        {{ $item->product_name }}
                                                    </a>
                                                    <div class="d-flex align-items-center gap-2 mt-0.5">
                                                        @if($item->product_sku)
                                                        <span class="badge bg-slate-100 text-muted font-monospace border border-slate-200" style="font-size: 0.70rem; padding: 2px 6px;">
                                                            {{ $item->product_sku }}
                                                        </span>
                                                        @endif
                                                        <span class="text-muted small" style="font-size: 0.72rem;">
                                                            <i class="fas fa-shield-alt text-success me-0.5"></i>{{ __('Garantie 2 ans') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-3 text-secondary small text-nowrap">
                                            {{ currency($item->price) }}
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 0.82rem; font-weight: 500;">
                                                &times;{{ $item->quantity }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 py-3 text-nowrap">
                                            <span class="text-dark font-heading" style="font-size: 0.95rem; font-weight: 500;">
                                                {{ currency($item->price * $item->quantity) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SHIPPING & CONTACT INFO CARD --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-white px-4 py-3.5 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="order-card-icon-box text-danger bg-danger-subtle rounded-3">
                                <i class="fas fa-truck-fast"></i>
                            </span>
                            <div>
                                <h5 class="m-0 fs-6 text-dark" style="font-weight: 500;">{{ __('Informations de livraison & Paiement') }}</h5>
                                <span class="text-muted small" style="font-size: 0.76rem;">{{ __('Coordonnées de réception de votre commande') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            {{-- Client Info --}}
                            <div class="col-sm-6">
                                <div class="order-info-block p-3 rounded-3 bg-light border">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-muted small" style="font-size: 0.78rem;">
                                        <i class="fas fa-user-circle text-primary"></i>
                                        <span>{{ __('Destinataire') }}</span>
                                    </div>
                                    <div class="text-dark fs-6" style="font-weight: 500;">{{ $order->customer_name }}</div>
                                    
                                    <div class="mt-2 text-muted small d-flex align-items-center gap-1.5">
                                        <i class="fas fa-phone text-danger" style="font-size: 0.75rem;"></i>
                                        <a href="tel:{{ $order->customer_phone }}" class="text-decoration-none text-dark">{{ $order->customer_phone }}</a>
                                    </div>

                                    @if($order->customer_email)
                                    <div class="mt-1 text-muted small d-flex align-items-center gap-1.5">
                                        <i class="far fa-envelope text-primary" style="font-size: 0.75rem;"></i>
                                        <span class="text-dark text-truncate" style="max-width: 220px;">{{ $order->customer_email }}</span>
                                    </div>
                                    @endif

                                    @if($order->ice)
                                    <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between">
                                        <span class="text-muted small" style="font-size: 0.74rem;">{{ __('Identifiant Fiscal (ICE):') }}</span>
                                        <span class="badge bg-white text-dark border font-monospace">{{ $order->ice }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Shipping Address --}}
                            <div class="col-sm-6">
                                <div class="order-info-block p-3 rounded-3 bg-light border">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-muted small" style="font-size: 0.78rem;">
                                        <i class="fas fa-location-dot text-danger"></i>
                                        <span>{{ __('Adresse de livraison') }}</span>
                                    </div>
                                    <div class="text-dark" style="font-weight: 500;">
                                        {{ $order->shipping_city }}
                                        @if($order->shipping_state)
                                        <span class="text-muted small" style="font-weight: 400;">({{ $order->shipping_state }})</span>
                                        @endif
                                    </div>
                                    <div class="text-secondary small mt-1" style="line-height: 1.45;">
                                        {{ $order->shipping_address }}
                                    </div>
                                    <div class="text-muted small mt-1" style="font-size: 0.74rem;">
                                        <i class="fas fa-flag me-1 text-danger"></i>Maroc
                                    </div>

                                    <div class="mt-2 pt-2 border-top">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                            <i class="fas fa-bolt me-1"></i>{{ __('Livraison Express 24/48h') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Payment Method Details --}}
                            <div class="col-12">
                                <div class="p-3 rounded-3 border d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" style="background: #f8fafc;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="order-payment-icon-box bg-white border rounded-circle text-success shadow-xs">
                                            <i class="fas fa-hand-holding-dollar"></i>
                                        </div>
                                        <div>
                                            <span class="text-muted small d-block" style="font-size: 0.76rem;">{{ __('Mode de règlement sélectionné') }}</span>
                                            <span class="text-dark" style="font-weight: 500;">{{ __('Paiement à la livraison (Cash on Delivery)') }}</span>
                                            <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.74rem;">
                                                {{ __('Réglez en toute sérénité en espèces ou par chèque directement au livreur lors de la réception.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="badge bg-white text-success border border-success-subtle px-3 py-1.5 rounded-pill flex-shrink-0" style="font-size: 0.78rem;">
                                        <i class="fas fa-lock me-1"></i>{{ __('100% Sécurisé') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: FINANCIAL SUMMARY, ACTIONS & NEXT STEPS --}}
            <div class="col-12 col-lg-5">
                
                {{-- FINANCIAL SUMMARY CARD --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden order-summary-card">
                    <div class="card-header bg-white px-4 py-3.5 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="order-card-icon-box text-success bg-success-subtle rounded-3">
                                <i class="fas fa-receipt"></i>
                            </span>
                            <div>
                                <h5 class="m-0 fs-6 text-dark" style="font-weight: 500;">{{ __('Récapitulatif Financier') }}</h5>
                                <span class="text-muted small" style="font-size: 0.76rem;">{{ __('Détail des montants facturés') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2.5 text-secondary small">
                            <span>{{ __('Sous-total HT') }}</span>
                            <span class="text-dark font-monospace" style="font-weight: 500;">{{ currency($order->subtotal) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2.5 text-secondary small">
                            <span>{{ setting('tax_label', 'TVA') }} ({{ floatval(setting('tax_rate', 20)) }}%)</span>
                            <span class="text-dark font-monospace" style="font-weight: 500;">{{ currency($order->tax) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 text-secondary small">
                            <span>{{ __('Frais de livraison') }}</span>
                            @if($order->shipping_cost > 0)
                                <span class="text-dark font-monospace" style="font-weight: 500;">{{ currency($order->shipping_cost) }}</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5">
                                    {{ __('Gratuite') }}
                                </span>
                            @endif
                        </div>

                        @if($order->discount > 0)
                        <div class="d-flex justify-content-between mb-3 text-secondary small">
                            <span class="text-danger">{{ __('Remise commerciale') }}</span>
                            <span class="text-danger font-monospace" style="font-weight: 500;">-{{ currency($order->discount) }}</span>
                        </div>
                        @endif

                        <div class="pt-3 border-top d-flex justify-content-between align-items-baseline mb-4">
                            <div>
                                <span class="fs-6 text-dark mb-0 d-block" style="font-weight: 500;">{{ __('Total TTC') }}</span>
                                <span class="text-muted small" style="font-size: 0.74rem;">
                                    <i class="fas fa-check-double text-success me-1"></i>{{ __('Montant net à payer à réception') }}
                                </span>
                            </div>
                            <span class="order-final-total text-danger font-heading">
                                {{ $order->formatted_total }}
                            </span>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="d-grid gap-2.5">
                            <a href="{{ route('shop.index') }}" class="btn btn-dark py-2.5 rounded-pill d-flex align-items-center justify-content-center gap-2 order-primary-action-btn shadow-xs">
                                <i class="fas fa-shopping-bag"></i>
                                <span>{{ __('Continuer vos achats') }}</span>
                            </a>

                            <button type="button" class="btn btn-outline-secondary py-2.5 rounded-pill d-flex align-items-center justify-content-center gap-2 order-print-action-btn" onclick="window.print()">
                                <i class="fas fa-print"></i>
                                <span>{{ __('Imprimer le reçu / Bon de commande') }}</span>
                            </button>

                            @if(auth()->check())
                            <a href="{{ route('customer.orders') }}" class="btn btn-link text-muted small py-1 text-decoration-none text-center">
                                <i class="fas fa-history me-1.5"></i>{{ __('Consulter l\'historique de mes commandes') }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- NEXT STEPS INFO CARD --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h6 class="text-dark mb-3 d-flex align-items-center gap-2" style="font-weight: 500;">
                        <span class="order-card-icon-box text-info bg-info-subtle rounded-3" style="width: 28px; height: 28px; font-size: 0.85rem;">
                            <i class="fas fa-circle-info"></i>
                        </span>
                        <span>{{ __('Prochaines étapes de votre commande') }}</span>
                    </h6>

                    <div class="d-flex flex-column gap-3 small text-secondary">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="order-step-num-badge">1</div>
                            <div>
                                <strong class="text-dark d-block" style="font-weight: 500;">{{ __('Validation téléphonique') }}</strong>
                                <span class="text-muted" style="font-size: 0.78rem;">
                                    {{ __('Un conseiller WINA SHOP vous contactera sous peu pour confirmer les détails de livraison.') }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-start">
                            <div class="order-step-num-badge">2</div>
                            <div>
                                <strong class="text-dark d-block" style="font-weight: 500;">{{ __('Expédition & Suivi transporteur') }}</strong>
                                <span class="text-muted" style="font-size: 0.78rem;">
                                    {{ __('Votre colis est préparé avec emballage antichoc renforcé et pris en charge par notre transporteur express.') }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-start">
                            <div class="order-step-num-badge">3</div>
                            <div>
                                <strong class="text-dark d-block" style="font-weight: 500;">{{ __('Paiement à la livraison') }}</strong>
                                <span class="text-muted" style="font-size: 0.78rem;">
                                    {{ __('Vérifiez votre colis et réglez le livreur en espèces ou chèque avec reçu tamponné.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TRUST GUARANTEE STRIP --}}
                <div class="card border-0 shadow-xs rounded-4 bg-light p-3">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <i class="fas fa-shield-halved text-success mb-1" style="font-size: 1.1rem;"></i>
                                <span class="d-block text-dark small" style="font-size: 0.74rem; font-weight: 500;">{{ __('Garantie 2 ans') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <i class="fas fa-file-invoice text-primary mb-1" style="font-size: 1.1rem;"></i>
                                <span class="d-block text-dark small" style="font-size: 0.74rem; font-weight: 500;">{{ __('Facture avec ICE') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <i class="fas fa-truck-fast text-danger mb-1" style="font-size: 1.1rem;"></i>
                                <span class="d-block text-dark small" style="font-size: 0.74rem; font-weight: 500;">{{ __('Livraison Express') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <i class="fas fa-store text-warning mb-1" style="font-size: 1.1rem;"></i>
                                <span class="d-block text-dark small" style="font-size: 0.74rem; font-weight: 500;">{{ __('Showroom Casablanca') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- MODERN COMPONENT STYLES & PRINT MEDIA QUERY --}}
<style>
    .order-success-page {
        background-color: #f8fafc;
        min-height: 85vh;
        font-family: "Roboto", sans-serif !important;
    }

    .order-success-page * {
        font-weight: 400 !important;
    }

    .order-success-page .fa,
    .order-success-page .fas,
    .order-success-page .far,
    .order-success-page .fab {
        font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        font-weight: 900 !important;
    }
    .order-success-page .far {
        font-weight: 400 !important;
    }
    .order-success-page .fab {
        font-family: "Font Awesome 6 Brands" !important;
        font-weight: 400 !important;
    }

    .order-success-hero {
        position: relative;
        border: 1px solid #e2e8f0 !important;
    }

    .order-hero-bg-accent {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #10b981 0%, #059669 50%, #dc2626 100%);
    }

    .order-success-icon-wrap {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 0 10px rgba(16, 185, 129, 0.06);
    }

    .order-success-icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
    }

    .order-hero-title {
        font-size: 1.85rem;
        letter-spacing: -0.02em;
    }

    .order-hero-subtitle {
        font-size: 0.95rem;
        line-height: 1.55;
    }

    .order-ref-pill {
        border-color: #e2e8f0 !important;
    }

    .order-ref-code {
        color: #0f172a !important;
        letter-spacing: 0.5px;
    }

    .copy-ref-btn {
        transition: color 0.2s ease, transform 0.2s ease;
    }
    .copy-ref-btn:hover {
        color: #0f172a !important;
        transform: scale(1.1);
    }

    .order-hero-wa-btn {
        background: #25d366 !important;
        border-color: #25d366 !important;
        color: #ffffff !important;
        transition: all 0.25s ease;
    }
    .order-hero-wa-btn:hover {
        background: #1eb956 !important;
        border-color: #1eb956 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.35);
    }

    /* STEPPER TIMELINE */
    .order-stepper-wrap {
        background: #f8fafc;
        border-color: #e2e8f0 !important;
    }

    .order-stepper::before {
        content: "";
        position: absolute;
        top: 18px;
        left: 30px;
        right: 30px;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
    }

    .order-step {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }

    .order-step-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 0.85rem;
        transition: all 0.25s ease;
    }

    .order-step.is-complete .order-step-circle {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }

    .order-step.is-active .order-step-circle {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.3);
    }

    .order-step-title {
        display: block;
        font-size: 0.82rem;
        margin-bottom: 2px;
    }

    .order-step-desc {
        display: block;
        font-size: 0.72rem;
    }

    /* ICONS & BADGES */
    .order-card-icon-box {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .order-item-thumb-box {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .order-item-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.25s ease;
    }

    .order-item-row:hover .order-item-thumb-img {
        transform: scale(1.08);
    }

    .order-item-title {
        font-size: 0.92rem;
        transition: color 0.2s ease;
    }
    .order-item-title:hover {
        color: #dc2626 !important;
    }

    .order-payment-icon-box {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .order-step-num-badge {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(14, 165, 233, 0.12);
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .order-final-total {
        font-size: 1.65rem;
        color: #dc2626 !important;
    }

    .order-primary-action-btn {
        background: #0f172a !important;
        border-color: #0f172a !important;
        color: #ffffff !important;
        transition: all 0.25s ease;
    }
    .order-primary-action-btn:hover {
        background: #1e293b !important;
        border-color: #1e293b !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.25);
    }

    .order-print-action-btn {
        border-color: #cbd5e1 !important;
        color: #334155 !important;
        transition: all 0.25s ease;
    }
    .order-print-action-btn:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #94a3b8 !important;
    }

    /* PRINT SPECIFIC STYLES */
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        nav, header, footer, .whatsapp-float, .breadcrumb, .order-hero-wa-btn, .order-primary-action-btn, .order-print-action-btn, #mobileNavDrawer, #miniCart {
            display: none !important;
        }
        .order-success-page {
            background: #ffffff !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #000000 !important;
        }
        .order-hero-bg-accent {
            display: none !important;
        }
        .col-lg-7, .col-lg-5 {
            width: 100% !important;
        }
    }
</style>

<script>
function copyOrderReference(refNumber) {
    if (navigator && navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(refNumber).then(showCopiedFeedback).catch(() => fallbackCopy(refNumber));
    } else {
        fallbackCopy(refNumber);
    }
}

function fallbackCopy(refNumber) {
    const tempInput = document.createElement('input');
    tempInput.value = refNumber;
    document.body.appendChild(tempInput);
    tempInput.select();
    try {
        document.execCommand('copy');
        showCopiedFeedback();
    } catch (e) {
        alert(refNumber);
    }
    document.body.removeChild(tempInput);
}

function showCopiedFeedback() {
    const icon = document.getElementById('copyIcon');
    if (icon) {
        icon.className = 'fas fa-check text-success';
        setTimeout(() => {
            icon.className = 'far fa-copy';
        }, 2000);
    }
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ __("Order number copied!") }}',
            showConfirmButton: false,
            timer: 2000,
            background: '#1a1a2e',
            color: '#ffffff'
        });
    }
}
</script>
@endsection
