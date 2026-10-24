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
            'badge' => __('Commande en cours de préparation'),
            'badge_class' => 'bg-info-subtle text-info border border-info-subtle',
            'badge_icon' => 'fas fa-box-open',
            'title' => __('Commande en préparation !'),
            'subtitle' => __('Votre commande est actuellement en cours de préparation et de vérification par notre équipe logistique.'),
            'icon' => 'fas fa-box-open',
            'accent' => 'linear-gradient(90deg, #0284c7 0%, #0369a1 50%, #10b981 100%)',
            'icon_bg' => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
            'icon_glow' => 'rgba(2, 132, 199, 0.35)',
        ],
        'shipped' => [
            'badge' => __('Commande expédiée — En cours de livraison'),
            'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'badge_icon' => 'fas fa-truck-fast',
            'title' => __('Votre commande est en route !'),
            'subtitle' => __('Votre colis a été remis à notre transporteur express et sera livré sous 24h à 48h.'),
            'icon' => 'fas fa-truck-fast',
            'accent' => 'linear-gradient(90deg, #3b82f6 0%, #1d4ed8 50%, #059669 100%)',
            'icon_bg' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
            'icon_glow' => 'rgba(59, 130, 246, 0.35)',
        ],
        'delivered' => [
            'badge' => __('Commande livrée avec succès'),
            'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
            'badge_icon' => 'fas fa-circle-check',
            'title' => __('Commande livrée !'),
            'subtitle' => __('Votre commande a été livrée avec succès. Merci d\'avoir choisi WINA SHOP !'),
            'icon' => 'fas fa-check-double',
            'accent' => 'linear-gradient(90deg, #10b981 0%, #059669 100%)',
            'icon_bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'icon_glow' => 'rgba(16, 185, 129, 0.35)',
        ],
        'cancelled' => [
            'badge' => __('Commande annulée'),
            'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'badge_icon' => 'fas fa-circle-xmark',
            'title' => __('Commande annulée'),
            'subtitle' => __('Cette commande a été annulée. Contactez notre support WhatsApp si vous avez la moindre question.'),
            'icon' => 'fas fa-xmark',
            'accent' => 'linear-gradient(90deg, #ef4444 0%, #b91c1c 100%)',
            'icon_bg' => 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)',
            'icon_glow' => 'rgba(239, 68, 68, 0.35)',
        ],
        default => [
            'badge' => __('Commande enregistrée avec succès'),
            'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
            'badge_icon' => 'fas fa-circle-check',
            'title' => __('Commande confirmée !'),
            'subtitle' => $isAr 
                ? "شكراً لثقتكم، {$order->customer_name} ! لقد تم استلام طلبكم وجاري تحضيره فوراً." 
                : "Merci pour votre confiance, {$order->customer_name} ! Nous avons bien reçu votre commande et nous la préparons dès maintenant.",
            'icon' => 'fas fa-check',
            'accent' => 'linear-gradient(90deg, #10b981 0%, #059669 50%, #dc2626 100%)',
            'icon_bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'icon_glow' => 'rgba(16, 185, 129, 0.35)',
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
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white order-success-hero" id="orderHeroCard">
            <div class="card-body p-4 p-md-5 text-center position-relative">
                <div class="order-hero-bg-accent" id="heroAccentBar" style="background: {{ $heroConfig['accent'] }};"></div>
                
                {{-- Celebration Icon with Glowing Pulse --}}
                <div class="order-success-icon-wrap mx-auto mb-3" id="heroIconWrap">
                    <div class="order-success-icon" id="heroIcon" style="background: {{ $heroConfig['icon_bg'] }}; box-shadow: 0 6px 16px {{ $heroConfig['icon_glow'] }};">
                        <i class="{{ $heroConfig['icon'] }}" id="heroIconInner"></i>
                    </div>
                </div>

                <span class="badge {{ $heroConfig['badge_class'] }} rounded-pill px-3 py-1.5 mb-2 font-monospace" style="font-size: 0.82rem; font-weight: 500;" id="heroBadge">
                    <i class="{{ $heroConfig['badge_icon'] }} me-1.5"></i>{{ $heroConfig['badge'] }}
                </span>

                <h1 class="order-hero-title mb-2 text-dark" id="heroTitle">{{ $heroConfig['title'] }}</h1>
                <p class="order-hero-subtitle text-muted mb-3 mx-auto" style="max-width: 620px;" id="heroSubtitle">
                    {{ $heroConfig['subtitle'] }}
                </p>

                {{-- Order Reference Pill & Live Tracker & WhatsApp Button --}}
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mt-2">
                    <div class="order-ref-pill d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill bg-light border">
                        <span class="text-muted small">{{ __('Commande :') }}</span>
                        <span class="text-dark font-monospace fw-bold order-ref-code" id="orderRefText">#{{ $order->order_number }}</span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1 copy-ref-btn" onclick="copyOrderReference('{{ $order->order_number }}')" title="{{ __('Copy') }}">
                            <i class="far fa-copy" id="copyIcon"></i>
                        </button>
                    </div>

                    {{-- LIVE TRACKING BADGE --}}
                    <div class="order-live-tracker d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill bg-white border shadow-xs" id="liveTrackerBadge" title="{{ __('Mise à jour en direct automatique') }}">
                        <span class="live-dot-pulse" id="liveDotPulse"></span>
                        <span class="text-dark small" style="font-size: 0.80rem;" id="liveSyncStatus">{{ __('Suivi en direct actif') }}</span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1" id="manualRefreshBtn" onclick="refreshOrderStatus(true)" title="{{ __('Actualiser le statut maintenant') }}">
                            <i class="fas fa-arrows-rotate" id="refreshIcon"></i>
                        </button>
                    </div>

                    <div class="w-100 d-sm-none"></div>

                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-2 order-hero-wa-btn shadow-xs">
                        <i class="fab fa-whatsapp" style="font-size: 1.05rem;"></i>
                        <span>{{ __('Assistance WhatsApp rapide') }}</span>
                    </a>
                </div>
            </div>

            {{-- 4-STEP ORDER TIMELINE STEPPER --}}
            <div class="order-stepper-wrap border-top px-3 py-3.5 px-md-5 bg-slate-50" id="orderStepperWrap">
                <div class="order-stepper d-flex justify-content-between position-relative">
                    <div class="order-stepper-line-track">
                        <div class="order-stepper-line-fill" id="stepperLineFill" style="width: {{ $stepperProgressPercent }}%;"></div>
                    </div>

                    <div class="order-step {{ $step1Class }}" id="step1">
                        <div class="order-step-circle">
                            <i class="fas fa-check" id="step1Icon"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title text-dark">{{ __('Validée') }}</span>
                            <span class="order-step-desc text-muted d-none d-sm-block">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'Aujourd\'hui' }}</span>
                        </div>
                    </div>

                    <div class="order-step {{ $step2Class }}" id="step2">
                        <div class="order-step-circle">
                            <i class="fas fa-box-open" id="step2Icon"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title text-dark">{{ __('Préparation') }}</span>
                            <span class="order-step-desc text-muted d-none d-sm-block">{{ __('Vérification & emballage') }}</span>
                        </div>
                    </div>

                    <div class="order-step {{ $step3Class }}" id="step3">
                        <div class="order-step-circle">
                            <i class="fas fa-truck-fast" id="step3Icon"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title text-dark">{{ __('Expédition') }}</span>
                            <span class="order-step-desc text-muted d-none d-sm-block">{{ __('24h à 48h partout au Maroc') }}</span>
                        </div>
                    </div>

                    <div class="order-step {{ $step4Class }}" id="step4">
                        <div class="order-step-circle">
                            <i class="fas fa-hand-holding-dollar" id="step4Icon"></i>
                        </div>
                        <div class="order-step-label">
                            <span class="order-step-title text-dark">{{ __('Livraison') }}</span>
                            <span class="order-step-desc text-muted d-none d-sm-block">{{ __('Règlement à réception') }}</span>
                        </div>
                    </div>
                </div>

                {{-- CANCELLED ALERT (if cancelled) --}}
                <div id="cancelledAlert" class="alert alert-danger border-0 rounded-3 mb-0 mt-3 {{ $order->status === 'cancelled' ? '' : 'd-none' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-circle-xmark fs-5"></i>
                        <div>
                            <strong class="d-block">{{ __('Cette commande a été annulée.') }}</strong>
                            <span class="small">{{ __('Si vous avez des questions ou souhaitez modifier votre commande, veuillez contacter notre support WhatsApp.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- LEFT COLUMN: ORDER ITEMS & DELIVERY DETAILS --}}
            <div class="col-12 col-lg-7">
                
                {{-- ORDER NOTES / CARRIER TRACKING ALERT --}}
                <div id="orderNotesAlert" class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden {{ empty($order->notes) ? 'd-none' : '' }}" style="border-left: 4px solid #0284c7 !important;">
                    <div class="card-body p-3.5 d-flex align-items-start gap-3">
                        <div class="order-card-icon-box text-primary bg-primary-subtle rounded-3 flex-shrink-0">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="m-0 text-dark fs-6" style="font-weight: 500;">{{ __('Note de suivi / Informations de livraison') }}</div>
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.70rem;">{{ __('Mise à jour en direct') }}</span>
                            </div>
                            <p class="text-secondary small mb-0" id="orderNotesContent" style="white-space: pre-line;">{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>

                {{-- ORDER ITEMS CARD --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
                    <div class="card-header bg-white px-4 py-3.5 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="order-card-icon-box text-primary bg-primary-subtle rounded-3">
                                <i class="fas fa-boxes-stacked"></i>
                            </span>
                            <div>
                                <h2 class="m-0 fs-6 text-dark" style="font-weight: 500;">{{ __('Articles commandés') }}</h2>
                                <span class="text-muted small" style="font-size: 0.76rem;">{{ $order->items->count() }} {{ __('référence(s) incluse(s)') }}</span>
                            </div>
                        </div>
                        <span class="badge {{ $statusBadgeClass }} rounded-pill px-3 py-1.5" style="font-size: 0.78rem;" id="orderStatusBadge">
                            <i class="{{ $statusBadgeIcon }} me-1" id="orderStatusBadgeIcon"></i><span id="orderStatusBadgeText">{{ $statusLabel }}</span>
                        </span>
                    </div>

                    <div class="card-body p-0">
                        {{-- Desktop Table View (>= 768px) --}}
                        <div class="table-responsive d-none d-md-block">
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
                                                        <img src="{{ $prodImg }}" alt="{{ $item->product_name }}" width="52" height="52" class="order-item-thumb-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
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

                        {{-- Mobile Product List (< 768px) --}}
                        <div class="d-md-none p-3">
                            @foreach($order->items as $item)
                            @php
                                $prodImg = $item->product && $item->product->thumbnail ? $item->product->thumbnail : asset('images/camera/cat_cameras.jpg');
                                $prodUrl = $item->product ? route('shop.show', $item->product->slug ?: $item->product->id) : '#';
                            @endphp
                            <div class="order-mobile-item {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <a href="{{ $prodUrl }}" class="flex-shrink-0">
                                        <div class="order-item-thumb-box" style="width: 60px; height: 60px; border-radius: 10px;">
                                            <img src="{{ $prodImg }}" alt="{{ $item->product_name }}" width="60" height="60" class="order-item-thumb-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                        </div>
                                    </a>
                                    <div class="flex-grow-1 min-w-0">
                                        <a href="{{ $prodUrl }}" class="text-dark text-decoration-none d-block mb-1 fw-semibold" style="font-size: 0.92rem; line-height: 1.35;">
                                            {{ $item->product_name }}
                                        </a>
                                        <div class="d-flex flex-wrap align-items-center gap-1.5 mb-2">
                                            @if($item->product_sku)
                                            <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.68rem; padding: 2px 6px;">
                                                {{ $item->product_sku }}
                                            </span>
                                            @endif
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem; padding: 2px 6px;">
                                                <i class="fas fa-shield-alt me-0.5"></i>{{ __('Garantie 2 ans') }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-1 border-top border-light">
                                            <div class="text-muted small">
                                                <span>{{ currency($item->price) }}</span>
                                                <span class="mx-1">&times;</span>
                                                <span class="badge bg-light text-dark border px-2 py-0.5 fw-bold" style="font-size: 0.76rem;">{{ $item->quantity }}</span>
                                            </div>
                                            <div class="text-danger font-heading fw-bold" style="font-size: 0.95rem;">
                                                {{ currency($item->price * $item->quantity) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
                                <h2 class="m-0 fs-6 text-dark" style="font-weight: 500;">{{ __('Informations de livraison & Paiement') }}</h2>
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
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <span class="badge {{ $paymentBadgeClass }} px-3 py-1.5 rounded-pill" id="orderPaymentStatusBadge" style="font-size: 0.78rem;">
                                            <i class="{{ $order->payment_status === 'paid' ? 'fas fa-circle-check me-1' : 'fas fa-clock me-1' }}"></i>
                                            <span id="orderPaymentStatusText">{{ $paymentLabel }}</span>
                                        </span>
                                        <span class="badge bg-white text-muted border px-2.5 py-1.5 rounded-pill d-none d-sm-inline-block" style="font-size: 0.74rem;">
                                            <i class="fas fa-lock me-1 text-success"></i>{{ __('100% Sécurisé') }}
                                        </span>
                                    </div>
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
                                <h2 class="m-0 fs-6 text-dark" style="font-weight: 500;">{{ __('Récapitulatif Financier') }}</h2>
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

                        <div class="pt-3 border-top d-flex justify-content-between align-items-center mb-4 gap-2">
                            <div class="flex-grow-1 min-w-0">
                                <span class="fs-6 text-dark mb-0 d-block fw-bold">{{ __('Total TTC') }}</span>
                                <span class="text-muted small" style="font-size: 0.74rem;">
                                    <i class="fas fa-check-double text-success me-1"></i>{{ __('Montant net à payer à réception') }}
                                </span>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="order-final-total text-danger font-heading text-nowrap fw-bold">
                                    {{ $order->formatted_total }}
                                </span>
                            </div>
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
                    <div class="text-dark mb-3 d-flex align-items-center gap-2" style="font-weight: 500;">
                        <span class="order-card-icon-box text-info bg-info-subtle rounded-3" style="width: 28px; height: 28px; font-size: 0.85rem;">
                            <i class="fas fa-circle-info"></i>
                        </span>
                        <span>{{ __('Prochaines étapes de votre commande') }}</span>
                    </div>

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
        position: relative;
    }

    .order-stepper-line-track {
        position: absolute;
        top: 18px;
        left: 12.5%;
        right: 12.5%;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
        border-radius: 4px;
        overflow: hidden;
    }

    .order-stepper-line-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        border-radius: 4px;
        transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);
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
        transition: all 0.35s ease;
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
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.12), 0 4px 10px rgba(15, 23, 42, 0.3);
        animation: activeStepPulse 2.2s infinite ease-in-out;
    }

    .order-step.is-cancelled .order-step-circle {
        background: #ef4444;
        border-color: #ef4444;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }

    @keyframes activeStepPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }

    /* LIVE PULSE DOT */
    .live-dot-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        position: relative;
        display: inline-block;
        flex-shrink: 0;
    }
    .live-dot-pulse::after {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        border-radius: 50%;
        background-color: #10b981;
        animation: pulseLive 2s infinite ease-out;
    }
    @keyframes pulseLive {
        0% { transform: scale(1); opacity: 0.9; }
        100% { transform: scale(2.8); opacity: 0; }
    }

    /* HIGHLIGHT FLASH ON ORDER UPDATED */
    @keyframes orderHighlightFlash {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        50% { box-shadow: 0 0 0 12px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .order-updated-highlight {
        animation: orderHighlightFlash 1.6s ease-out 2;
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
        font-size: 1.55rem;
        color: #dc2626 !important;
        white-space: nowrap !important;
        font-weight: 700 !important;
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

    /* ================================================================
       MOBILE RESPONSIVE PERFECTION (< 768px & < 576px)
       ================================================================ */
    @media (max-width: 767.98px) {
        .order-success-page {
            padding-top: 12px !important;
            padding-bottom: 24px !important;
        }

        .order-success-page > .container {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        .order-success-page .row.g-4 {
            --bs-gutter-x: 10px !important;
            --bs-gutter-y: 12px !important;
        }

        /* Hero Card */
        .order-success-hero {
            border-radius: 14px !important;
            margin-bottom: 12px !important;
        }

        .order-success-hero .card-body {
            padding: 20px 12px 16px !important;
        }

        .order-hero-title {
            font-size: 1.4rem !important;
            margin-bottom: 6px !important;
        }

        .order-hero-subtitle {
            font-size: 0.88rem !important;
            line-height: 1.45 !important;
            margin-bottom: 12px !important;
        }

        .order-success-icon-wrap {
            width: 60px !important;
            height: 60px !important;
            margin-bottom: 10px !important;
        }

        .order-success-icon {
            width: 44px !important;
            height: 44px !important;
            font-size: 1.25rem !important;
        }

        /* Hero Buttons & Pills */
        .order-ref-pill {
            padding: 5px 12px !important;
            font-size: 0.82rem !important;
        }

        .order-live-tracker {
            padding: 5px 12px !important;
            font-size: 0.82rem !important;
        }

        .order-hero-wa-btn {
            width: 100% !important;
            justify-content: center !important;
            padding: 9px 16px !important;
            font-size: 0.88rem !important;
            margin-top: 4px;
        }

        /* Stepper */
        .order-stepper-wrap {
            padding: 12px 6px !important;
        }

        .order-stepper-line-track {
            top: 15px !important;
            left: 12.5% !important;
            right: 12.5% !important;
            height: 2.5px !important;
        }

        .order-step-circle {
            width: 30px !important;
            height: 30px !important;
            font-size: 0.72rem !important;
            margin-bottom: 4px !important;
            border-width: 1.5px !important;
        }

        .order-step-title {
            font-size: 0.70rem !important;
            font-weight: 600 !important;
            letter-spacing: -0.2px !important;
        }

        /* Section Cards */
        .order-success-page .card {
            border-radius: 14px !important;
            margin-bottom: 12px !important;
        }

        .order-success-page .card-header {
            padding: 12px 14px !important;
        }

        .order-success-page .card-body {
            padding: 14px 12px !important;
        }

        /* Sub-cards / info blocks */
        .order-info-block {
            padding: 12px 10px !important;
        }

        /* Total TTC */
        .order-final-total {
            font-size: 1.45rem !important;
            white-space: nowrap !important;
        }

        /* Action Buttons */
        .order-primary-action-btn,
        .order-print-action-btn {
            font-size: 0.90rem !important;
            padding: 11px 14px !important;
        }

        /* WhatsApp floating button positioning on mobile */
        .whatsapp-float {
            bottom: calc(14px + env(safe-area-inset-bottom, 0px)) !important;
            right: 12px !important;
            width: 48px !important;
            height: 48px !important;
        }

        .whatsapp-float-svg {
            width: 26px !important;
            height: 26px !important;
        }
    }

    @media (max-width: 420px) {
        .order-hero-title {
            font-size: 1.25rem !important;
        }

        .order-ref-pill,
        .order-live-tracker {
            font-size: 0.78rem !important;
            padding: 5px 10px !important;
        }

        .order-step-title {
            font-size: 0.66rem !important;
        }

        .order-final-total {
            font-size: 1.35rem !important;
        }
    }

    /* PRINT SPECIFIC STYLES */
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        nav, header, footer, .whatsapp-float, .breadcrumb, .order-hero-wa-btn, .order-primary-action-btn, .order-print-action-btn, #mobileNavDrawer, #miniCart, .order-live-tracker {
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
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            liveStatusText.textContent = '{{ __("Suivi en direct actif") }} (' + timeStr + ')';
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
    // 1. Toast notification for the customer
    if (flags.status) {
        showUpdateToast('{{ __("Mise à jour de votre commande") }}', '{{ __("Nouveau statut :") }} ' + data.status_label, 'success');
    } else if (flags.payment) {
        showUpdateToast('{{ __("Statut de paiement mis à jour") }}', data.payment_status_label, 'info');
    } else if (flags.notes) {
        showUpdateToast('{{ __("Nouvelle note de suivi reçue") }}', data.notes, 'info');
    }

    // 2. Pulse visual highlight on Hero & Stepper
    const heroCard = document.getElementById('orderHeroCard');
    const stepperWrap = document.getElementById('orderStepperWrap');
    if (heroCard) {
        heroCard.classList.remove('order-updated-highlight');
        void heroCard.offsetWidth; // reflow
        heroCard.classList.add('order-updated-highlight');
    }
    if (stepperWrap) {
        stepperWrap.classList.remove('order-updated-highlight');
        void stepperWrap.offsetWidth;
        stepperWrap.classList.add('order-updated-highlight');
    }

    // 3. Update Hero Card
    if (data.hero) {
        const heroBadge = document.getElementById('heroBadge');
        if (heroBadge) {
            heroBadge.className = 'badge ' + data.hero.badge_class + ' rounded-pill px-3 py-1.5 mb-2 font-monospace';
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

        const heroAccentBar = document.getElementById('heroAccentBar');
        if (heroAccentBar && data.hero.accent) {
            heroAccentBar.style.background = data.hero.accent;
        }

        const heroIcon = document.getElementById('heroIcon');
        if (heroIcon && data.hero.icon_bg) {
            heroIcon.style.background = data.hero.icon_bg;
            heroIcon.style.boxShadow = '0 6px 16px ' + data.hero.icon_glow;
        }
    }

    // 4. Update Stepper classes & progress line
    updateStepperClasses(data.status);

    // 5. Update Order Items Card status badge
    const orderStatusBadge = document.getElementById('orderStatusBadge');
    if (orderStatusBadge) {
        orderStatusBadge.className = 'badge rounded-pill px-3 py-1.5 ' + data.status_badge_class;
        orderStatusBadge.innerHTML = '<i class="' + data.status_badge_icon + ' me-1" id="orderStatusBadgeIcon"></i><span id="orderStatusBadgeText">' + data.status_label + '</span>';
    }

    // 6. Update Payment status badge
    const paymentStatusBadge = document.getElementById('orderPaymentStatusBadge');
    if (paymentStatusBadge) {
        paymentStatusBadge.className = 'badge rounded-pill px-3 py-1.5 flex-shrink-0 ' + data.payment_badge_class;
        const iconClass = (data.payment_status === 'paid') ? 'fas fa-circle-check me-1 text-success' : 'fas fa-clock me-1';
        paymentStatusBadge.innerHTML = '<i class="' + iconClass + '"></i><span id="orderPaymentStatusText">' + data.payment_status_label + '</span>';
    }

    // 7. Update Notes Alert
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

    // 8. Cancelled notice
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

    // Clear existing classes
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

// Lifecycle listeners
document.addEventListener('DOMContentLoaded', () => {
    // Start active polling every 4 seconds
    startPolling(4000);

    // Initial check after 2 seconds
    setTimeout(refreshOrderStatus, 2000);

    // Slower polling in background tab, resume instant on focus
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            startPolling(18000);
        } else {
            startPolling(4000);
            refreshOrderStatus();
        }
    });

    window.addEventListener('focus', () => {
        refreshOrderStatus();
    });
});
</script>
@endsection
