@extends('layouts.frontend')

@section('meta_title', __('Commande confirmée') . ' #' . $order->order_number . ' — ' . setting('app_name', 'WINA SHOP'))

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

    $status = $order->status ?? 'pending';
    $paymentStatus = $order->payment_status ?? 'pending';

    $heroConfig = match($status) {
        'processing' => [
            'badge' => __('Commande en préparation'),
            'badge_class' => 'bg-info-subtle text-info border border-info-subtle',
            'badge_icon' => 'fas fa-box-open',
            'title' => __('Commande en préparation !'),
            'subtitle' => __('Votre commande est actuellement en cours de préparation et de vérification par notre équipe logistique.'),
            'icon' => 'fas fa-box-open',
        ],
        'shipped' => [
            'badge' => __('Commande expédiée — En route'),
            'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'badge_icon' => 'fas fa-truck-fast',
            'title' => __('Votre commande est en route !'),
            'subtitle' => __('Votre colis a été remis à notre transporteur express et sera livré sous 24h à 48h partout au Maroc.'),
            'icon' => 'fas fa-truck-fast',
        ],
        'delivered' => [
            'badge' => __('Commande livrée avec succès'),
            'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
            'badge_icon' => 'fas fa-circle-check',
            'title' => __('Commande livrée !'),
            'subtitle' => __('Votre commande a été livrée avec succès. Merci d\'avoir choisi WINA SHOP pour votre équipement !'),
            'icon' => 'fas fa-check-double',
        ],
        'cancelled' => [
            'badge' => __('Commande annulée'),
            'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'badge_icon' => 'fas fa-circle-xmark',
            'title' => __('Commande annulée'),
            'subtitle' => __('Cette commande a été annulée. Contactez notre support WhatsApp si vous avez la moindre question.'),
            'icon' => 'fas fa-xmark',
        ],
        default => [
            'badge' => __('Commande validée avec succès'),
            'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
            'badge_icon' => 'fas fa-circle-check',
            'title' => __('Merci pour votre commande !'),
            'subtitle' => $isAr 
                ? "شكراً لثقتكم، {$order->customer_name} ! لقد تم استلام طلبكم وجاري تحضيره فوراً من طرف فريق WINA SHOP." 
                : "Merci pour votre confiance, {$order->customer_name} ! Nous avons bien reçu votre commande et nous la préparons dès maintenant.",
            'icon' => 'fas fa-check',
        ],
    };

    $isStep1Complete = true;
    $isStep2Complete = in_array($status, ['shipped', 'delivered']);
    $isStep2Active = in_array($status, ['pending', 'processing']);
    $isStep3Complete = ($status === 'delivered');
    $isStep3Active = ($status === 'shipped');
    $isStep4Complete = ($status === 'delivered');

    $step1Class = ($status === 'cancelled') ? 'is-cancelled' : 'is-complete';
    $step2Class = ($status === 'cancelled') ? '' : ($isStep2Complete ? 'is-complete' : ($isStep2Active ? 'is-active' : ''));
    $step3Class = ($status === 'cancelled') ? '' : ($isStep3Complete ? 'is-complete' : ($isStep3Active ? 'is-active' : ''));
    $step4Class = ($status === 'cancelled') ? '' : ($isStep4Complete ? 'is-active is-complete' : '');

    $stepperProgressPercent = match($status) {
        'pending' => 20,
        'processing' => 45,
        'shipped' => 75,
        'delivered' => 100,
        'cancelled' => 0,
        default => 20,
    };

    $statusLabel = match($status) {
        'pending' => __('Validée'),
        'processing' => __('En préparation'),
        'shipped' => __('Expédiée'),
        'delivered' => __('Livrée'),
        'cancelled' => __('Annulée'),
        default => ucfirst($status),
    };

    $statusBadgeClass = match($status) {
        'pending' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'processing' => 'bg-info-subtle text-info border border-info-subtle',
        'shipped' => 'bg-primary-subtle text-primary border border-primary-subtle',
        'delivered' => 'bg-success-subtle text-success border border-success-subtle',
        'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
        default => 'bg-light text-dark border',
    };

    $statusBadgeIcon = match($status) {
        'pending' => 'fas fa-clock text-warning',
        'processing' => 'fas fa-box-open text-info',
        'shipped' => 'fas fa-truck-fast text-primary',
        'delivered' => 'fas fa-circle-check text-success',
        'cancelled' => 'fas fa-circle-xmark text-danger',
        default => 'fas fa-clock text-secondary',
    };

    $paymentLabel = match($paymentStatus) {
        'paid' => __('Payé'),
        'failed' => __('Échoué'),
        'refunded' => __('Remboursé'),
        default => __('En attente (Paiement à la livraison)'),
    };

    $paymentBadgeClass = match($paymentStatus) {
        'paid' => 'bg-success-subtle text-success border border-success-subtle',
        'failed' => 'bg-danger-subtle text-danger border border-danger-subtle',
        'refunded' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        default => 'bg-white text-success border border-success-subtle',
    };
@endphp

@section('content')
<div class="bg-light checkout-page-wrapper py-3 py-md-5 order-success-page">
    <div class="container checkout-container">
        
        {{-- BREADCRUMB --}}
        <nav aria-label="breadcrumb" class="mb-3 d-none d-md-block">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted"><i class="fas fa-home me-1"></i>{{ __('Accueil') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">{{ __('Boutique') }}</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ __('Confirmation de commande') }}</li>
            </ol>
        </nav>

        {{-- TOP ORDER CONFIRMATION & TIMELINE CARD --}}
        <div class="card checkout-card border-0 shadow-sm rounded-4 overflow-hidden mb-3 mb-md-4 order-success-hero" id="orderHeroCard">
            <div class="card-body p-3 p-sm-4 text-center position-relative">
                <div class="order-hero-bg-accent" id="heroAccentBar" style="display: none;"></div>
                
                {{-- Clean Confirmation Badge --}}
                <div class="order-success-icon-wrap" id="heroIconWrap">
                    <i class="{{ $heroConfig['icon'] }}" id="heroIconInner"></i>
                </div>

                <div class="mb-2">
                    <span class="badge {{ $heroConfig['badge_class'] }} rounded-pill px-3 py-1 font-monospace fw-semibold" style="font-size: 0.78rem;" id="heroBadge">
                        <i class="{{ $heroConfig['badge_icon'] }} me-1.5"></i>{{ $heroConfig['badge'] }}
                    </span>
                </div>

                <h1 class="h3 fw-bold font-heading text-dark mb-1" id="heroTitle">{{ $heroConfig['title'] }}</h1>
                <p class="text-muted small mb-3 mx-auto" style="max-width: 580px; line-height: 1.55;" id="heroSubtitle">
                    {{ $heroConfig['subtitle'] }}
                </p>

                {{-- Order Meta Info Pill Row --}}
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                    {{-- Order Reference Pill --}}
                    <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill bg-light border small">
                        <span class="text-muted">{{ __('Commande :') }}</span>
                        <strong class="font-monospace text-dark" id="orderRefText">#{{ $order->order_number }}</strong>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1 copy-ref-btn" onclick="copyOrderReference('{{ $order->order_number }}')" title="{{ __('Copier') }}">
                            <i class="far fa-copy" id="copyIcon"></i>
                        </button>
                    </div>

                    {{-- Live Tracker Pill --}}
                    <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill bg-white border small shadow-2xs" id="liveTrackerBadge" title="{{ __('Suivi automatique en temps réel') }}">
                        <span class="live-dot-pulse" id="liveDotPulse"></span>
                        <span class="text-dark small" id="liveSyncStatus">{{ __('Suivi en direct') }}</span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1" id="manualRefreshBtn" onclick="refreshOrderStatus(true)" title="{{ __('Actualiser') }}">
                            <i class="fas fa-arrows-rotate" id="refreshIcon"></i>
                        </button>
                    </div>

                    {{-- WhatsApp Support Button --}}
                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 order-hero-wa-btn">
                        <i class="fab fa-whatsapp"></i>
                        <span class="fw-semibold">{{ __('Support WhatsApp') }}</span>
                    </a>
                </div>
            </div>

            {{-- 4-STEP TIMELINE STEPPER --}}
            <div class="order-stepper-wrap" id="orderStepperWrap">
                <div class="order-stepper d-flex justify-content-between position-relative">
                    <div class="order-stepper-line-track">
                        <div class="order-stepper-line-fill" id="stepperLineFill" style="width: {{ $stepperProgressPercent }}%;"></div>
                    </div>

                    <div class="order-step {{ $step1Class }}" id="step1">
                        <div class="order-step-circle">
                            <i class="fas fa-check" id="step1Icon"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title">{{ __('Validée') }}</span>
                            <span class="order-step-desc d-none d-sm-block">{{ $order->created_at ? $order->created_at->format('d/m/Y') : 'Aujourd\'hui' }}</span>
                        </div>
                    </div>

                    <div class="order-step {{ $step2Class }}" id="step2">
                        <div class="order-step-circle">
                            <i class="fas fa-box-open" id="step2Icon"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title">{{ __('Préparation') }}</span>
                            <span class="order-step-desc d-none d-sm-block">{{ __('Emballage soigné') }}</span>
                        </div>
                    </div>

                    <div class="order-step {{ $step3Class }}" id="step3">
                        <div class="order-step-circle">
                            <i class="fas fa-truck-fast" id="step3Icon"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title">{{ __('Expédition') }}</span>
                            <span class="order-step-desc d-none d-sm-block">{{ __('24h à 48h au Maroc') }}</span>
                        </div>
                    </div>

                    <div class="order-step {{ $step4Class }}" id="step4">
                        <div class="order-step-circle">
                            <i class="fas fa-hand-holding-dollar" id="step4Icon"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title">{{ __('Livraison') }}</span>
                            <span class="order-step-desc d-none d-sm-block">{{ __('Paiement à réception') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Cancelled alert if order is cancelled --}}
                <div id="cancelledAlert" class="alert alert-danger border-0 rounded-3 mb-0 mt-3 {{ $order->status === 'cancelled' ? '' : 'd-none' }}">
                    <div class="d-flex align-items-center gap-2 small">
                        <i class="fas fa-circle-xmark fs-5 text-danger"></i>
                        <div>
                            <strong>{{ __('Cette commande a été annulée.') }}</strong> {{ __('Contactez notre support WhatsApp pour toute information.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            {{-- LEFT COLUMN: ORDERED ITEMS & SHIPPING INFO --}}
            <div class="col-lg-7">
                
                {{-- Order Notes Alert (if present) --}}
                <div id="orderNotesAlert" class="card checkout-card border-0 shadow-sm rounded-4 mb-3 mb-md-4 bg-white overflow-hidden {{ empty($order->notes) ? 'd-none' : '' }}" style="border-left: 3.5px solid #0284c7 !important;">
                    <div class="card-body p-3 d-flex align-items-start gap-2.5">
                        <i class="fas fa-bullhorn text-primary fs-5 mt-0.5"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-0.5">
                                <strong class="text-dark small fw-bold">{{ __('Note de suivi logistique') }}</strong>
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">{{ __('Direct') }}</span>
                            </div>
                            <p class="text-secondary small mb-0" id="orderNotesContent" style="white-space: pre-line;">{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>

                {{-- CARD: ARTICLES COMMANDES --}}
                <div class="card checkout-card border-0 shadow-sm rounded-4 mb-3 mb-md-4 order-items-table">
                    <div class="card-header bg-white p-3 p-sm-4 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-boxes-stacked text-danger"></i>
                            <h2 class="h5 fw-bold m-0 font-heading text-dark">{{ __('Articles commandés') }}</h2>
                        </div>
                        <span class="badge {{ $statusBadgeClass }} rounded-pill px-2.5 py-1" id="orderStatusBadge">
                            <i class="{{ $statusBadgeIcon }} me-1" id="orderStatusBadgeIcon"></i><span id="orderStatusBadgeText">{{ $statusLabel }}</span>
                        </span>
                    </div>

                    <div class="card-body p-3 p-sm-4">
                        @foreach($order->items as $item)
                        @php
                            $prodImg = $item->product_image;
                            $prodUrl = $item->product_url;
                        @endphp
                        <div class="d-flex align-items-center py-2.5 {{ !$loop->last ? 'border-bottom' : '' }}">
                            {{-- Thumbnail with Quantity Badge --}}
                            <div class="me-3 position-relative flex-shrink-0">
                                <a href="{{ $prodUrl }}" target="_blank" rel="noopener">
                                    <img src="{{ $prodImg }}" 
                                         alt="{{ $item->product_name }}" 
                                         width="60" 
                                         height="60" 
                                         class="order-item-thumb-box"
                                         style="width: 60px; height: 60px; object-fit: contain; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2px;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';" 
                                         loading="lazy">
                                </a>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark font-monospace" style="font-size: 0.68rem; padding: 3px 6px;">
                                    {{ $item->quantity }}
                                </span>
                            </div>

                            {{-- Product Name & Metadata --}}
                            <div class="flex-grow-1 min-w-0 me-3">
                                <a href="{{ $prodUrl }}" class="fw-bold text-dark text-decoration-none d-block text-truncate mb-1" style="font-size: 0.94rem;" target="_blank" rel="noopener">
                                    {{ $item->product_name }}
                                </a>
                                <div class="d-flex flex-wrap align-items-center gap-1.5">
                                    @if(!empty($item->variant_name))
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.70rem;">
                                            <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                        </span>
                                    @endif
                                    @if(!empty($item->product_sku))
                                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.68rem;">
                                            {{ $item->product_sku }}
                                        </span>
                                    @endif
                                    <span class="text-muted small" style="font-size: 0.74rem;">
                                        {{ currency($item->price) }} × {{ $item->quantity }}
                                    </span>
                                </div>
                            </div>

                            {{-- Line Subtotal --}}
                            <div class="text-end flex-shrink-0">
                                <div class="fw-bold text-dark font-heading" style="font-size: 0.98rem;">
                                    {{ currency($item->price * $item->quantity) }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- CARD: INFORMATIONS DE LIVRAISON & PAIEMENT --}}
                <div class="card checkout-card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
                    <div class="card-header bg-white p-3 p-sm-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-truck-fast text-danger"></i>
                            <h2 class="h5 fw-bold m-0 font-heading text-dark">{{ __('Informations de livraison & Paiement') }}</h2>
                        </div>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        <div class="row g-3">
                            {{-- Destinataire --}}
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light border h-100">
                                    <div class="text-muted small fw-semibold mb-1.5 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-user text-primary me-1"></i>{{ __('Destinataire') }}
                                    </div>
                                    <div class="fw-bold text-dark mb-1">{{ $order->customer_name }}</div>
                                    <div class="small mb-1">
                                        <i class="fas fa-phone-alt text-danger me-1.5 small"></i>
                                        <a href="tel:{{ $order->customer_phone }}" class="text-dark text-decoration-none fw-medium">{{ $order->customer_phone }}</a>
                                    </div>
                                    @if($order->customer_email)
                                    <div class="small text-muted text-truncate" style="max-width: 230px;">
                                        <i class="far fa-envelope text-muted me-1.5 small"></i>{{ $order->customer_email }}
                                    </div>
                                    @endif
                                    @if($order->ice)
                                    <div class="mt-2 pt-2 border-top small d-flex justify-content-between align-items-center">
                                        <span class="text-muted">{{ __('ICE :') }}</span>
                                        <span class="badge bg-white text-dark border font-monospace">{{ $order->ice }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Adresse de livraison --}}
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light border h-100">
                                    <div class="text-muted small fw-semibold mb-1.5 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ __('Adresse de livraison') }}
                                    </div>
                                    <div class="fw-bold text-dark mb-1">
                                        {{ $order->shipping_city }}
                                        @if($order->shipping_state)
                                        <span class="text-muted small fw-normal">({{ $order->shipping_state }})</span>
                                        @endif
                                    </div>
                                    <div class="small text-secondary mb-2" style="line-height: 1.4;">
                                        {{ $order->shipping_address }}
                                    </div>
                                    <div class="pt-2 border-top">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.70rem;">
                                            <i class="fas fa-bolt me-1"></i>{{ __('Livraison Express 24/48h partout au Maroc') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Mode de paiement --}}
                            <div class="col-12">
                                <div class="p-3 rounded-3 border bg-light d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2.5">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle bg-white border p-2 text-success shadow-2xs flex-shrink-0" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-hand-holding-dollar fs-5"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark small fw-bold">{{ __('Paiement à la livraison (Cash on Delivery)') }}</strong>
                                            <span class="text-muted small" style="font-size: 0.76rem;">{{ __('Réglez en toute sérénité en espèces ou par chèque directement au livreur lors de la réception.') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge {{ $paymentBadgeClass }} px-2.5 py-1 rounded-pill" id="orderPaymentStatusBadge" style="font-size: 0.76rem;">
                                            <i class="{{ $order->payment_status === 'paid' ? 'fas fa-circle-check me-1' : 'fas fa-clock me-1' }}"></i>
                                            <span id="orderPaymentStatusText">{{ $paymentLabel }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: FINANCIAL SUMMARY, ACTIONS & NEXT STEPS --}}
            <div class="col-lg-5">
                
                {{-- FINANCIAL SUMMARY CARD --}}
                <div class="card checkout-card border-0 shadow-sm rounded-4 mb-3 mb-md-4 order-summary-card">
                    <div class="card-header bg-white p-3 p-sm-4 border-bottom pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-receipt text-danger"></i>
                            <h2 class="h5 fw-bold m-0 font-heading text-dark">{{ __('Récapitulatif') }}</h2>
                        </div>
                    </div>

                    <div class="card-body p-3 p-sm-4">
                        @php
                            $itemsTotal = $order->items && $order->items->count() > 0 
                                ? $order->items->sum(fn($i) => (float)$i->price * (int)$i->quantity) 
                                : max(0, (float)$order->total - (float)$order->shipping_cost + (float)$order->discount);
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">{{ __('Articles') }}</span>
                            <span class="fw-bold text-dark font-monospace">{{ currency($itemsTotal) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <span class="text-muted small">{{ __('Frais de livraison') }}</span>
                            @if($order->shipping_cost > 0)
                                <span class="fw-bold text-dark font-monospace">{{ currency($order->shipping_cost) }}</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                    {{ __('Gratuite') }}
                                </span>
                            @endif
                        </div>

                        @if($order->discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span class="small">{{ __('Remise') }}</span>
                            <span class="fw-bold font-monospace">-{{ currency($order->discount) }}</span>
                        </div>
                        @endif

                        <hr class="my-3 opacity-10">

                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="h5 fw-bold mb-0 text-dark font-heading">{{ __('Total') }}</span>
                            <span class="h4 fw-bold text-danger mb-0 font-heading order-final-total" id="orderFinalTotal">
                                {{ $order->formatted_total }}
                            </span>
                        </div>

                        <small class="text-muted d-block text-end mb-3" style="font-size: 0.74rem;">
                            <i class="fas fa-shield-check text-success me-1"></i>
                        </small>

                        {{-- ACTION BUTTONS --}}
                        <div class="d-grid gap-2">
                            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm order-primary-action-btn">
                                <i class="fas fa-shopping-bag me-2"></i>{{ __('Continuer vos achats') }}
                            </a>

                            <button type="button" class="btn btn-outline-dark w-100 rounded-pill fw-semibold py-2 order-print-action-btn" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>{{ __('Imprimer le reçu') }}
                            </button>

                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success w-100 rounded-pill fw-semibold py-2">
                                <i class="fab fa-whatsapp me-2"></i>{{ __('Besoin d\'aide ? WhatsApp') }}
                            </a>

                            @if(auth()->check())
                            <a href="{{ route('customer.orders') }}" class="btn btn-link text-muted small py-1 text-decoration-none text-center">
                                <i class="fas fa-clock-rotate-left me-1"></i>{{ __('Mes commandes') }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- NEXT STEPS INFO CARD --}}
                <div class="card checkout-card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
                    <div class="card-header bg-white p-3 p-sm-4 border-bottom">
                        <h3 class="h6 fw-bold m-0 font-heading text-dark">
                            <i class="fas fa-circle-info text-info me-2"></i>{{ __('Prochaines étapes') }}
                        </h3>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        <div class="d-flex flex-column gap-3 small text-secondary">
                            <div class="d-flex gap-2.5 align-items-start">
                                <span class="badge bg-light text-dark border rounded-circle flex-shrink-0" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 0.72rem;">1</span>
                                <div>
                                    <strong class="text-dark d-block fw-bold mb-0.5">{{ __('Confirmation') }}</strong>
                                    <span class="text-muted" style="font-size: 0.76rem; line-height: 1.4;">{{ __('Un conseiller vous contactera par téléphone pour valider l\'adresse.') }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2.5 align-items-start">
                                <span class="badge bg-light text-dark border rounded-circle flex-shrink-0" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 0.72rem;">2</span>
                                <div>
                                    <strong class="text-dark d-block fw-bold mb-0.5">{{ __('Expédition rapide') }}</strong>
                                    <span class="text-muted" style="font-size: 0.76rem; line-height: 1.4;">{{ __('Colis sécurisé et remis au transporteur express sous 24h.') }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2.5 align-items-start">
                                <span class="badge bg-light text-dark border rounded-circle flex-shrink-0" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 0.72rem;">3</span>
                                <div>
                                    <strong class="text-dark d-block fw-bold mb-0.5">{{ __('Paiement à la livraison') }}</strong>
                                    <span class="text-muted" style="font-size: 0.76rem; line-height: 1.4;">{{ __('Vérifiez le colis et réglez le livreur en espèces ou par chèque.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- REASSURANCE BADGES --}}
                <div class="card checkout-card border-0 shadow-sm rounded-4 p-2.5 bg-light">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <i class="fas fa-shield-halved text-success mb-1 small d-block"></i>
                                <span class="d-block text-dark small fw-bold" style="font-size: 0.72rem;">{{ __('Garantie officielle') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <i class="fas fa-file-invoice text-primary mb-1 small d-block"></i>
                                <span class="d-block text-dark small fw-bold" style="font-size: 0.72rem;">{{ __('Facture avec ICE') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <i class="fas fa-truck-fast text-danger mb-1 small d-block"></i>
                                <span class="d-block text-dark small fw-bold" style="font-size: 0.72rem;">{{ __('Livraison Express') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <i class="fas fa-store text-warning mb-1 small d-block"></i>
                                <span class="d-block text-dark small fw-bold" style="font-size: 0.72rem;">{{ __('Showroom Casablanca') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- PRINT SPECIFIC STYLES --}}
<style>
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        nav, header, footer, .whatsapp-float, .breadcrumb, .order-hero-wa-btn, .order-primary-action-btn, .order-print-action-btn, #mobileNavDrawer, #miniCart, #liveTrackerBadge {
            display: none !important;
        }
        .checkout-page-wrapper {
            background: #ffffff !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #000000 !important;
            border-radius: 6px !important;
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
            title: '{{ __("Numéro de commande copié !") }}',
            showConfirmButton: false,
            timer: 2000,
            background: '#0f172a',
            color: '#ffffff'
        });
    }
}

/* REAL-TIME LIVE ORDER TRACKING SCRIPT */
let currentOrderStatus = '{{ $order->status }}';
let currentOrderPaymentStatus = '{{ $order->payment_status }}';
let currentOrderNotes = {!! json_encode($order->notes ?? '') !!};
let pollingTimer = null;
let isCheckingStatus = false;

function refreshOrderStatus(isManual = false) {
    if (isCheckingStatus) return;
    isCheckingStatus = true;

    const refreshIcon = document.getElementById('refreshIcon');
    if (refreshIcon) {
        refreshIcon.classList.add('fa-spin');
    }

    const liveStatusText = document.getElementById('liveSyncStatus');

    fetch('{{ route('checkout.liveStatus', $order->id) }}', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        cache: 'no-store'
    })
    .then(response => {
        if (!response.ok) throw new Error('Live status fetch failed');
        return response.json();
    })
    .then(data => {
        if (!data || !data.success) return;

        const hasStatusChanged = (data.status !== currentOrderStatus);
        const hasPaymentChanged = (data.payment_status !== currentOrderPaymentStatus);
        const hasNotesChanged = ((data.notes ?? '') !== (currentOrderNotes ?? ''));

        if (hasStatusChanged || hasPaymentChanged || hasNotesChanged) {
            applyOrderUpdates(data, {
                status: hasStatusChanged,
                payment: hasPaymentChanged,
                notes: hasNotesChanged
            });

            currentOrderStatus = data.status;
            currentOrderPaymentStatus = data.payment_status;
            currentOrderNotes = data.notes ?? '';
        } else if (isManual) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: '{{ __("Votre commande est à jour") }}',
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#0f172a',
                    color: '#ffffff'
                });
            }
        }

        if (liveStatusText) {
            liveStatusText.textContent = '{{ __("Statut à jour") }}';
        }
    })
    .catch(err => {
        console.warn('Live order status check notice:', err);
    })
    .finally(() => {
        isCheckingStatus = false;
        if (refreshIcon) {
            setTimeout(() => refreshIcon.classList.remove('fa-spin'), 350);
        }
    });
}

function applyOrderUpdates(data, flags) {
    if (flags.status) {
        showUpdateToast('{{ __("Mise à jour de votre commande") }}', '{{ __("Nouveau statut :") }} ' + data.status_label, 'success');
    } else if (flags.payment) {
        showUpdateToast('{{ __("Statut de paiement mis à jour") }}', data.payment_status_label, 'info');
    } else if (flags.notes) {
        showUpdateToast('{{ __("Nouvelle note de suivi reçue") }}', data.notes, 'info');
    }

    if (data.hero) {
        const heroBadge = document.getElementById('heroBadge');
        if (heroBadge) {
            heroBadge.className = 'badge ' + data.hero.badge_class + ' rounded-pill px-3 py-1 font-monospace fw-semibold';
            heroBadge.innerHTML = '<i class="' + data.hero.badge_icon + ' me-1.5"></i>' + data.hero.badge_text;
        }

        const heroTitle = document.getElementById('heroTitle');
        if (heroTitle) {
            heroTitle.textContent = data.hero.title;
        }

        const heroSubtitle = document.getElementById('heroSubtitle');
        if (heroSubtitle) {
            heroSubtitle.textContent = data.hero.subtitle;
        }

        const heroIconInner = document.getElementById('heroIconInner');
        if (heroIconInner) {
            heroIconInner.className = data.hero.icon;
        }
    }

    updateStepperClasses(data.status);

    const orderStatusBadge = document.getElementById('orderStatusBadge');
    if (orderStatusBadge) {
        orderStatusBadge.className = 'badge rounded-pill px-2.5 py-1 ' + data.status_badge_class;
        orderStatusBadge.innerHTML = '<i class="' + data.status_badge_icon + ' me-1" id="orderStatusBadgeIcon"></i><span id="orderStatusBadgeText">' + data.status_label + '</span>';
    }

    const paymentStatusBadge = document.getElementById('orderPaymentStatusBadge');
    if (paymentStatusBadge) {
        paymentStatusBadge.className = 'badge rounded-pill px-2.5 py-1 flex-shrink-0 ' + data.payment_badge_class;
        const iconClass = (data.payment_status === 'paid') ? 'fas fa-circle-check me-1 text-success' : 'fas fa-clock me-1';
        paymentStatusBadge.innerHTML = '<i class="' + iconClass + '"></i><span id="orderPaymentStatusText">' + data.payment_status_label + '</span>';
    }

    const orderNotesAlert = document.getElementById('orderNotesAlert');
    const orderNotesContent = document.getElementById('orderNotesContent');
    if (orderNotesAlert && orderNotesContent) {
        if (data.notes && data.notes.trim() !== '') {
            orderNotesContent.textContent = data.notes;
            orderNotesAlert.classList.remove('d-none');
        } else {
            orderNotesAlert.classList.add('d-none');
        }
    }

    const cancelledAlert = document.getElementById('cancelledAlert');
    if (cancelledAlert) {
        if (data.status === 'cancelled') {
            cancelledAlert.classList.remove('d-none');
        } else {
            cancelledAlert.classList.add('d-none');
        }
    }
}

function updateStepperClasses(status) {
    const s1 = document.getElementById('step1');
    const s2 = document.getElementById('step2');
    const s3 = document.getElementById('step3');
    const s4 = document.getElementById('step4');
    const lineFill = document.getElementById('stepperLineFill');

    if (!s1 || !s2 || !s3 || !s4) return;

    [s1, s2, s3, s4].forEach(el => el.classList.remove('is-complete', 'is-active', 'is-cancelled'));

    if (status === 'cancelled') {
        s1.classList.add('is-cancelled');
        if (lineFill) lineFill.style.width = '0%';
        return;
    }

    if (status === 'pending') {
        s1.classList.add('is-complete');
        s2.classList.add('is-active');
        if (lineFill) lineFill.style.width = '20%';
    } else if (status === 'processing') {
        s1.classList.add('is-complete');
        s2.classList.add('is-active');
        if (lineFill) lineFill.style.width = '45%';
    } else if (status === 'shipped') {
        s1.classList.add('is-complete');
        s2.classList.add('is-complete');
        s3.classList.add('is-active');
        if (lineFill) lineFill.style.width = '75%';
    } else if (status === 'delivered') {
        s1.classList.add('is-complete');
        s2.classList.add('is-complete');
        s3.classList.add('is-complete');
        s4.classList.add('is-complete', 'is-active');
        if (lineFill) lineFill.style.width = '100%';
    }
}

function showUpdateToast(title, text, icon) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon || 'info',
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 4500,
            timerProgressBar: true,
            background: '#0f172a',
            color: '#ffffff'
        });
    }
}

function startPolling(interval) {
    stopPolling();
    pollingTimer = setInterval(refreshOrderStatus, interval);
}

function stopPolling() {
    if (pollingTimer) {
        clearInterval(pollingTimer);
        pollingTimer = null;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    startPolling(5000);
    setTimeout(refreshOrderStatus, 2500);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            startPolling(20000);
        } else {
            startPolling(5000);
            refreshOrderStatus();
        }
    });

    window.addEventListener('focus', () => {
        refreshOrderStatus();
    });
});
</script>
@endsection
