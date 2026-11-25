@push('styles')
<style>
    /* Professional Order KPI Cards Design */
    .order-kpi-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--kpi-bg, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        border: 1.5px solid var(--kpi-border, #e2e8f0);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        overflow: hidden;
        min-height: 74px;
    }
    .order-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--kpi-accent, #0f172a);
        opacity: 0.9;
        transition: height 0.2s ease, opacity 0.2s ease;
    }
    .order-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.1), 0 4px 8px -4px rgba(15, 23, 42, 0.04);
        border-color: var(--kpi-accent, #0f172a);
    }
    .order-kpi-card:hover::before {
        height: 4px;
        opacity: 1;
    }
    .order-kpi-card.active-filter {
        border: 2px solid var(--kpi-active-border, #0f172a) !important;
        background: var(--kpi-accent, #0f172a) !important;
        box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px var(--kpi-accent, #0f172a), 0 6px 14px -2px var(--kpi-ring, rgba(15, 23, 42, 0.28)) !important;
        transform: translateY(-1px);
    }
    .order-kpi-card.active-filter::before {
        height: 0;
        opacity: 0;
    }
    .order-kpi-top {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 4px;
    }
    .order-kpi-card .order-kpi-label {
        font-size: 0.76rem;
        font-weight: 700 !important;
        letter-spacing: 0.02em;
        color: var(--kpi-text, #475569);
        text-transform: capitalize;
        line-height: 1.2;
    }
    .order-kpi-badge {
        font-size: 0.82rem;
        font-weight: 800 !important;
        padding: 3px 8px;
        border-radius: 9999px;
        line-height: 1.2;
        letter-spacing: 0.02em;
        flex-shrink: 0;
        font-variant-numeric: tabular-nums;
    }
    .order-kpi-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        width: 100%;
    }
    .order-kpi-card .order-kpi-value {
        font-family: var(--font-heading, 'Outfit', sans-serif);
        font-size: 1.55rem;
        font-weight: 800 !important;
        line-height: 1.15;
        letter-spacing: -0.02em;
        color: var(--kpi-text, #0f172a);
        font-variant-numeric: tabular-nums;
    }
    .order-kpi-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .order-kpi-card:hover .order-kpi-icon {
        transform: scale(1.08);
    }
    .order-kpi-footer {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.7rem;
        font-weight: 500;
        color: #64748b;
        margin-top: 6px;
        line-height: 1.2;
    }
    .order-kpi-card.active-filter .order-kpi-label,
    .order-kpi-card.active-filter .order-kpi-value,
    .order-kpi-card.active-filter .order-kpi-footer {
        color: #ffffff !important;
    }
    .order-kpi-card.active-filter .order-kpi-badge {
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
    }
    .order-kpi-card.active-filter .order-kpi-icon {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
    }

    /* Status Color Themes (matched to status badges & mobile order cards with lighter tint) */
    .kpi-theme-all {
        --kpi-accent: #64748b;
        --kpi-active-border: #475569;
        --kpi-bg: rgba(100, 116, 139, 0.12);
        --kpi-border: #94a3b8;
        --kpi-text: #475569;
        --kpi-ring: rgba(100, 116, 139, 0.25);
    }
    .kpi-theme-all .order-kpi-icon {
        background: rgba(255, 255, 255, 0.88);
        color: #475569;
    }
    .kpi-theme-all .order-kpi-badge {
        background: rgba(255, 255, 255, 0.88);
        color: #475569;
    }

    .kpi-theme-pending {
        --kpi-accent: #d97706;
        --kpi-active-border: #b45309;
        --kpi-bg: rgba(217, 119, 6, 0.12);
        --kpi-border: #f59e0b;
        --kpi-text: #b45309;
        --kpi-ring: rgba(217, 119, 6, 0.28);
    }
    .kpi-theme-pending .order-kpi-icon {
        background: rgba(255, 255, 255, 0.78);
        color: #d97706;
    }
    .kpi-theme-pending .order-kpi-badge {
        background: rgba(255, 255, 255, 0.78);
        color: #b45309;
    }

    .kpi-theme-en-demande {
        --kpi-accent: #c61aff;
        --kpi-active-border: #a800e0;
        --kpi-bg: rgba(198, 26, 255, 0.12);
        --kpi-border: #d966ff;
        --kpi-text: #a800e0;
        --kpi-ring: rgba(198, 26, 255, 0.28);
    }
    .kpi-theme-en-demande .order-kpi-icon {
        background: rgba(255, 255, 255, 0.78);
        color: #c61aff;
    }
    .kpi-theme-en-demande .order-kpi-badge {
        background: rgba(255, 255, 255, 0.78);
        color: #a800e0;
    }

    .kpi-theme-reserved {
        --kpi-accent: #7c3aed;
        --kpi-active-border: #6d28d9;
        --kpi-bg: rgba(124, 58, 237, 0.12);
        --kpi-border: #a78bfa;
        --kpi-text: #6d28d9;
        --kpi-ring: rgba(124, 58, 237, 0.28);
    }
    .kpi-theme-reserved .order-kpi-icon {
        background: rgba(255, 255, 255, 0.78);
        color: #7c3aed;
    }
    .kpi-theme-reserved .order-kpi-badge {
        background: rgba(255, 255, 255, 0.78);
        color: #6d28d9;
    }

    .kpi-theme-processing {
        --kpi-accent: #2563eb;
        --kpi-active-border: #1d4ed8;
        --kpi-bg: rgba(37, 99, 235, 0.12);
        --kpi-border: #60a5fa;
        --kpi-text: #1d4ed8;
        --kpi-ring: rgba(37, 99, 235, 0.28);
    }
    .kpi-theme-processing .order-kpi-icon {
        background: rgba(255, 255, 255, 0.78);
        color: #2563eb;
    }
    .kpi-theme-processing .order-kpi-badge {
        background: rgba(255, 255, 255, 0.78);
        color: #1d4ed8;
    }

    .kpi-theme-shipped {
        --kpi-accent: #0093ac;
        --kpi-active-border: #0e7490;
        --kpi-bg: rgba(0, 147, 172, 0.12);
        --kpi-border: #38bdf8;
        --kpi-text: #0e7490;
        --kpi-ring: rgba(0, 147, 172, 0.28);
    }
    .kpi-theme-shipped .order-kpi-icon {
        background: rgba(255, 255, 255, 0.78);
        color: #0093ac;
    }
    .kpi-theme-shipped .order-kpi-badge {
        background: rgba(255, 255, 255, 0.78);
        color: #0e7490;
    }

    .kpi-theme-delivered {
        --kpi-accent: #059669;
        --kpi-active-border: #047857;
        --kpi-bg: rgba(5, 150, 105, 0.12);
        --kpi-border: #34d399;
        --kpi-text: #047857;
        --kpi-ring: rgba(5, 150, 105, 0.28);
    }
    .kpi-theme-delivered .order-kpi-icon {
        background: rgba(255, 255, 255, 0.78);
        color: #059669;
    }
    .kpi-theme-delivered .order-kpi-badge {
        background: rgba(255, 255, 255, 0.78);
        color: #047857;
    }

    .kpi-theme-cancelled {
        --kpi-accent: #dc2626;
        --kpi-active-border: #b91c1c;
        --kpi-bg: rgba(220, 38, 38, 0.12);
        --kpi-border: #f87171;
        --kpi-text: #b91c1c;
        --kpi-ring: rgba(220, 38, 38, 0.28);
    }
    .kpi-theme-cancelled .order-kpi-icon {
        background: rgba(255, 255, 255, 0.78);
        color: #dc2626;
    }
    .kpi-theme-cancelled .order-kpi-badge {
        background: rgba(255, 255, 255, 0.78);
        color: #b91c1c;
    }

    @media (max-width: 767.98px) {
        .order-kpi-card {
            padding: 6px 7px;
            min-height: 52px;
            border-radius: 10px;
            align-items: stretch;
            justify-content: center;
            text-align: left;
        }
        .order-kpi-card > div {
            width: 100%;
            display: block;
        }
        .order-kpi-top {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 3px;
        }
        .order-kpi-card .order-kpi-label {
            font-size: 0.61rem;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .order-kpi-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 3px;
            width: 100%;
        }
        .order-kpi-card .order-kpi-value {
            font-size: 1.02rem;
            line-height: 1.05;
        }
        .order-kpi-card .order-kpi-badge {
            display: inline-block !important;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 9999px;
            line-height: 1.15;
            flex-shrink: 0;
        }
    }
</style>
@endpush

@extends('layouts.app')

@php
    $isPosChannel = ($channel ?? 'website') === 'pos';
    $indexRoute = $indexRoute ?? ($isPosChannel ? 'orders.pos' : 'orders.index');
    $exportParams = array_merge(request()->all(), ['channel' => $isPosChannel ? 'pos' : 'website']);
@endphp

@section('title', $isPosChannel ? 'Commandes POS (Caisse)' : 'Commandes Site Web')

@section('content')
<div class="orders-index-page">
    <!-- En-tête Exécutif Mobile-First -->
    <div class="orders-hero-card {{ $isPosChannel ? 'is-pos' : 'is-web' }}">
        <div class="orders-hero-main">
            <div class="d-flex align-items-center gap-2.5 min-w-0">
                <div class="orders-hero-icon">
                    <i class="fas {{ $isPosChannel ? 'fa-cash-register' : 'fa-shopping-bag' }}"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="orders-hero-title text-truncate">{{ $isPosChannel ? 'Commandes POS (Caisse)' : 'Commandes Site Web' }}</h1>
                    <p class="text-muted small mb-0 d-none d-md-block">
                        {{ $isPosChannel ? 'Ventes effectuées en boutique depuis la caisse POS' : 'Suivi complet des commandes clients provenant du site web' }}
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-auto">
                @if(request('status') || request('payment_status') || request('tax_mode') || request('delivery_type') || request('staff_id') || request('store_id') || request('search'))
                    <a href="{{ route($indexRoute) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 d-none d-md-inline-flex align-items-center gap-1.5 fw-semibold">
                        <i class="fas fa-rotate-left"></i> <span>Toutes ({{ number_format($stats['total'] ?? 0, 0, ',', ' ') }})</span>
                    </a>
                @endif
                <a href="{{ route('export.orders.pdf', $exportParams) }}" data-no-spa="true" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 d-none d-md-inline-flex align-items-center gap-1.5 fw-semibold">
                    <i class="fas fa-file-pdf text-danger"></i> <span>Exporter PDF</span>
                </a>
                <a href="{{ route('export.orders', $exportParams) }}" data-no-spa="true" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-none d-md-inline-flex align-items-center gap-1.5 fw-semibold">
                    <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
                </a>
                @if($isPosChannel)
                    <a href="{{ route('pos.index') }}" class="btn-brand-primary justify-content-center text-nowrap px-3 py-2" style="font-size: 0.82rem;">
                        <i class="fas fa-cash-register me-1"></i> <span class="d-none d-sm-inline">Ouvrir la caisse POS</span><span class="d-sm-none">Caisse</span>
                    </a>
                @else
                    <a href="{{ route('orders.create') }}" class="btn-brand-primary justify-content-center text-nowrap px-3 py-2" style="font-size: 0.82rem;">
                        <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">Nouvelle commande</span><span class="d-sm-none">Créer</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="orders-hero-subbar">
            <div class="orders-channel-tabs" role="navigation" aria-label="Navigation Commandes">
                <a href="{{ route('orders.index') }}" class="orders-channel-tab {{ !$isPosChannel ? 'active' : '' }}">
                    <i class="fas fa-globe {{ !$isPosChannel ? 'text-primary' : '' }}"></i>
                    <span>Site Web</span>
                </a>
                <a href="{{ route('orders.pos') }}" class="orders-channel-tab {{ $isPosChannel ? 'active' : '' }}">
                    <i class="fas fa-cash-register {{ $isPosChannel ? 'text-danger' : '' }}"></i>
                    <span>Commandes POS</span>
                </a>
                <a href="{{ route('orders.staff-stats') }}" class="orders-channel-tab">
                    <i class="fas fa-user-check text-success"></i>
                    <span>Stats Équipe</span>
                </a>
            </div>

            <div class="orders-hero-metrics tabular-nums">
                <span class="orders-metric-chip">
                    <i class="fas fa-receipt text-primary"></i>
                    <strong class="orders-strong text-dark" id="kpi-total-orders">{{ number_format($stats['total'] ?? $orders->total(), 0, ',', ' ') }}</strong>
                    <span>total</span>
                </span>
                <span class="orders-metric-chip is-revenue">
                    <i class="fas fa-coins"></i>
                    <strong class="orders-strong" id="kpi-revenue-orders">{{ currency($stats['revenue'] ?? 0) }}</strong>
                    <span class="d-none d-sm-inline">encaissés</span>
                </span>
            </div>
        </div>
    </div>

    @php
        $totalOrdersCount = $stats['total'] ?? 0;
        $pendingPct = $totalOrdersCount > 0 ? round(($stats['pending'] / $totalOrdersCount) * 100, 1) : 0;
        $enDemandePct = $totalOrdersCount > 0 ? round((($stats['en_demande'] ?? 0) / $totalOrdersCount) * 100, 1) : 0;
        $reservedPct = $totalOrdersCount > 0 ? round((($stats['reserved'] ?? 0) / $totalOrdersCount) * 100, 1) : 0;
        $processingPct = $totalOrdersCount > 0 ? round(($stats['processing'] / $totalOrdersCount) * 100, 1) : 0;
        $shippedPct = $totalOrdersCount > 0 ? round(($stats['shipped'] / $totalOrdersCount) * 100, 1) : 0;
        $deliveredPct = $totalOrdersCount > 0 ? round(($stats['delivered'] / $totalOrdersCount) * 100, 1) : 0;
        $cancelledPct = $totalOrdersCount > 0 ? round(($stats['cancelled'] / $totalOrdersCount) * 100, 1) : 0;
    @endphp

    <!-- 8 Delivery Status Cards (4 per row on Mobile & Desktop) -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2.5 g-md-3 mb-3 mb-md-4 orders-kpi-grid">
        <!-- 0. Toutes les commandes (All orders) -->
        <div class="col mb-2">
            <a href="{{ route($indexRoute, request()->except(['status', 'page'])) }}" 
               class="order-kpi-card kpi-theme-all h-100 {{ !request('status') ? 'active-filter' : '' }}"
               title="Voir toutes les commandes">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">Total</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-all-orders-card">{{ number_format($stats['total'] ?? 0, 0, ',', ' ') }}</div>
                        <span class="order-kpi-badge" id="kpi-all-orders-pct">100%</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 1. En attente (pending) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'pending' ? route($indexRoute, request()->except(['status', 'page'])) : route($indexRoute, array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
               class="order-kpi-card kpi-theme-pending h-100 {{ request('status') === 'pending' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes en attente">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">En attente</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-pending-orders">{{ number_format($stats['pending'] ?? 0, 0, ',', ' ') }}</div>
                        <span class="order-kpi-badge" id="kpi-pending-pct">{{ $pendingPct }}%</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 1b. Sur Commande (en_demande) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'en_demande' ? route($indexRoute, request()->except(['status', 'page'])) : route($indexRoute, array_merge(request()->except('page'), ['status' => 'en_demande'])) }}" 
               class="order-kpi-card kpi-theme-en-demande h-100 {{ request('status') === 'en_demande' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes sur commande">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">Sur Commande</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-en-demande-orders">{{ number_format($stats['en_demande'] ?? 0, 0, ',', ' ') }}</div>
                        <span class="order-kpi-badge" id="kpi-en-demande-pct">{{ $enDemandePct }}%</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 1c. Réservé (reserved) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'reserved' ? route($indexRoute, request()->except(['status', 'page'])) : route($indexRoute, array_merge(request()->except('page'), ['status' => 'reserved'])) }}"
               class="order-kpi-card kpi-theme-reserved h-100 {{ request('status') === 'reserved' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes réservées">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">Réservé</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-reserved-orders">{{ number_format($stats['reserved'] ?? 0, 0, ',', ' ') }}</div>
                        <span class="order-kpi-badge" id="kpi-reserved-pct">{{ $reservedPct }}%</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. En cours (processing) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'processing' ? route($indexRoute, request()->except(['status', 'page'])) : route($indexRoute, array_merge(request()->except('page'), ['status' => 'processing'])) }}" 
               class="order-kpi-card kpi-theme-processing h-100 {{ request('status') === 'processing' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes en cours de préparation">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">En cours</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-processing-orders">{{ number_format($stats['processing'] ?? 0, 0, ',', ' ') }}</div>
                        <span class="order-kpi-badge" id="kpi-processing-pct">{{ $processingPct }}%</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Expédiée (shipped) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'shipped' ? route($indexRoute, request()->except(['status', 'page'])) : route($indexRoute, array_merge(request()->except('page'), ['status' => 'shipped'])) }}" 
               class="order-kpi-card kpi-theme-shipped h-100 {{ request('status') === 'shipped' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes expédiées en acheminement">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">Expédiée</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-shipped-orders">{{ number_format($stats['shipped'] ?? 0, 0, ',', ' ') }}</div>
                        <span class="order-kpi-badge" id="kpi-shipped-pct">{{ $shippedPct }}%</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Livrée (delivered) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'delivered' ? route($indexRoute, request()->except(['status', 'page'])) : route($indexRoute, array_merge(request()->except('page'), ['status' => 'delivered'])) }}" 
               class="order-kpi-card kpi-theme-delivered h-100 {{ request('status') === 'delivered' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes livrées avec succès">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">Livrée</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-delivered-orders">{{ number_format($stats['delivered'] ?? 0, 0, ',', ' ') }}</div>
                        <span class="order-kpi-badge" id="kpi-delivered-pct">{{ $deliveredPct }}%</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 5. Annulée (cancelled) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'cancelled' ? route($indexRoute, request()->except(['status', 'page'])) : route($indexRoute, array_merge(request()->except('page'), ['status' => 'cancelled'])) }}" 
               class="order-kpi-card kpi-theme-cancelled h-100 {{ request('status') === 'cancelled' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes annulées ou rejetées">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">Annulée</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-cancelled-orders">{{ number_format($stats['cancelled'] ?? 0, 0, ',', ' ') }}</div>
                        <span class="order-kpi-badge" id="kpi-cancelled-pct">{{ $cancelledPct }}%</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre de filtres unifiée + Raccourcis tactiles (Desktop & Mobile) -->
    <div class="orders-toolbar-card">
        <form method="GET" action="{{ route($indexRoute) }}" id="orderFilterForm">
            <!-- Row 1 : Champ Recherche + Bouton Toggle Mobile + Actions Desktop -->
            <div class="orders-toolbar-top d-flex align-items-center gap-2 flex-nowrap flex-md-wrap">
                <div class="brand-search-wrapper position-relative flex-grow-1 min-w-0" style="min-width: 140px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control {{ request('search') ? 'pe-5' : '' }}"
                           placeholder="Client, téléphone, ville, email, N° commande..."
                           value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="orders-search-clear" title="Effacer la recherche">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>

                @php
                    $hasDateFilter = request('date_preset') || request('date_from') || request('date_to') || request('date');
                    $hasActiveFilters = request('status') || request('payment_status') || request('tax_mode') || request('delivery_type') || request('staff_id') || request('store_id') || $hasDateFilter;
                    $activeFilterCount = collect(['status', 'payment_status', 'tax_mode', 'delivery_type', 'staff_id', 'store_id', 'date_preset', 'date_from', 'date_to', 'date'])->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile (d-md-none) -->
                <button type="button"
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5 flex-shrink-0"
                        data-bs-toggle="collapse"
                        data-bs-target="#orderFiltersCollapse"
                        aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filtres</span>
                    @if($activeFilterCount > 0)
                        <span class="badge bg-white text-dark px-1.5" style="font-size: 0.7rem;">{{ $activeFilterCount }}</span>
                    @endif
                </button>

                <!-- Compteur résultats & quick reset Desktop (d-none d-md-flex) -->
                <div class="ms-md-auto d-none d-md-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border px-2.5 py-2">
                        {{ $orders->total() }} résultat{{ $orders->total() > 1 ? 's' : '' }}
                    </span>
                    @if(request('search') || $hasActiveFilters)
                        <a href="{{ route($indexRoute) }}" class="btn btn-sm btn-light border text-danger fw-semibold" title="Réinitialiser tous les filtres">
                            <i class="fas fa-times me-1"></i>Réinitialiser
                        </a>
                    @endif
                </div>
            </div>

            <!-- Row 1b : Raccourcis filtres rapides en 1 clic (Swipeable sur Mobile) -->
            @php
                $isTodayPreset = request('date_preset') === 'today';
                $isUnconfirmedQuick = request('staff_id') === 'unconfirmed';
                $quickTax = strtolower((string) request('tax_mode'));
                $quickDeliv = strtolower((string) request('delivery_type'));
            @endphp
            <div class="orders-quick-chips" aria-label="Filtres rapides">
                <a href="{{ $isTodayPreset ? request()->fullUrlWithQuery(['date_preset' => null, 'date_from' => null, 'date_to' => null, 'page' => null]) : request()->fullUrlWithQuery(['date_preset' => 'today', 'date_from' => null, 'date_to' => null, 'page' => null]) }}"
                   class="orders-quick-chip {{ $isTodayPreset ? 'active' : '' }}">
                    <i class="far fa-calendar-check"></i><span>Aujourd'hui</span>
                </a>
                @if(!$isPosChannel)
                    <a href="{{ $isUnconfirmedQuick ? request()->fullUrlWithQuery(['staff_id' => null, 'page' => null]) : request()->fullUrlWithQuery(['staff_id' => 'unconfirmed', 'page' => null]) }}"
                       class="orders-quick-chip {{ $isUnconfirmedQuick ? 'active' : '' }}">
                        <i class="fas fa-clock"></i><span>Non confirmées</span>
                    </a>
                @endif
                <a href="{{ $quickTax === 'ht' ? request()->fullUrlWithQuery(['tax_mode' => null, 'page' => null]) : request()->fullUrlWithQuery(['tax_mode' => 'ht', 'page' => null]) }}"
                   class="orders-quick-chip is-ht {{ $quickTax === 'ht' ? 'active' : '' }}">
                    <span class="status-dot tax-dot-ht"></span><span>HT</span>
                </a>
                <a href="{{ $quickTax === 'ttc' ? request()->fullUrlWithQuery(['tax_mode' => null, 'page' => null]) : request()->fullUrlWithQuery(['tax_mode' => 'ttc', 'page' => null]) }}"
                   class="orders-quick-chip is-ttc {{ $quickTax === 'ttc' ? 'active' : '' }}">
                    <span class="status-dot tax-dot-ttc"></span><span>TTC</span>
                </a>
                <a href="{{ $quickDeliv === 'ozon' ? request()->fullUrlWithQuery(['delivery_type' => null, 'page' => null]) : request()->fullUrlWithQuery(['delivery_type' => 'ozon', 'page' => null]) }}"
                   class="orders-quick-chip {{ $quickDeliv === 'ozon' ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i><span>Ozon</span>
                </a>
                <a href="{{ $quickDeliv === 'our_team' ? request()->fullUrlWithQuery(['delivery_type' => null, 'page' => null]) : request()->fullUrlWithQuery(['delivery_type' => 'our_team', 'page' => null]) }}"
                   class="orders-quick-chip {{ $quickDeliv === 'our_team' ? 'active' : '' }}">
                    <i class="fas fa-people-carry-box"></i><span>Livreur</span>
                </a>
                <a href="{{ $quickDeliv === 'store' ? request()->fullUrlWithQuery(['delivery_type' => null, 'page' => null]) : request()->fullUrlWithQuery(['delivery_type' => 'store', 'page' => null]) }}"
                   class="orders-quick-chip {{ $quickDeliv === 'store' ? 'active' : '' }}">
                    <i class="fas fa-store"></i><span>Store</span>
                </a>
                <a href="{{ $quickDeliv === 'autre' ? request()->fullUrlWithQuery(['delivery_type' => null, 'page' => null]) : request()->fullUrlWithQuery(['delivery_type' => 'autre', 'page' => null]) }}"
                   class="orders-quick-chip {{ $quickDeliv === 'autre' ? 'active' : '' }}">
                    <i class="fas fa-truck-arrow-right"></i><span>Autre</span>
                </a>
            </div>

            <!-- Row 2 : Filtres Déroulants Structurés (Toujours visibles sur Desktop, repliables sur Mobile) -->
            <div class="collapse d-md-block {{ $hasActiveFilters ? 'show' : '' }} mt-2.5 pt-2.5 border-top" id="orderFiltersCollapse">
                <div class="row g-2 align-items-end">
                    <!-- Statut de livraison -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">Statut</span>
                            <select name="status" id="orderStatusFilterSelect" class="form-select form-select-sm fw-semibold">
                                <option value="">Tous les statuts</option>
                                @php
                                    $statusNames = [
                                        'pending' => '⏳ En attente',
                                        'en_demande' => '📋 Sur Commande',
                                        'reserved' => '🔖 Réservé',
                                        'processing' => '⚙️ En cours',
                                        'shipped' => '🚚 Expédiée',
                                        'delivered' => '✅ Livrée',
                                        'cancelled' => '❌ Annulée'
                                    ];
                                @endphp
                                @foreach($statusNames as $stKey => $stLabel)
                                    <option value="{{ $stKey }}" {{ request('status') == $stKey ? 'selected' : '' }}>{{ $stLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Statut de paiement -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">Paiement</span>
                            <select name="payment_status" class="form-select form-select-sm">
                                <option value="">Tous paiements</option>
                                @php
                                    $paymentStatusNames = [
                                        'pending' => '⏳ En attente',
                                        'partial' => '⏳ Partiel',
                                        'paid' => '💳 Payé',
                                        'failed' => '⚠️ Échoué',
                                        'refunded' => '↩️ Remboursé'
                                    ];
                                @endphp
                                @foreach($paymentStatusNames as $pstKey => $pstLabel)
                                    <option value="{{ $pstKey }}" {{ request('payment_status') == $pstKey ? 'selected' : '' }}>{{ $pstLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Régime Fiscal (TTC / HT) -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">Régime</span>
                            <select name="tax_mode" class="form-select form-select-sm fw-semibold">
                                <option value="">Tous (TTC / HT)</option>
                                <option value="ttc" {{ strtolower((string) request('tax_mode')) === 'ttc' ? 'selected' : '' }}>TTC</option>
                                <option value="ht" {{ strtolower((string) request('tax_mode')) === 'ht' ? 'selected' : '' }}>HT</option>
                            </select>
                        </div>
                    </div>

                    <!-- Type de livraison (Store / Livreur / Ozon / Autre) -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">Livraison</span>
                            <select name="delivery_type" class="form-select form-select-sm fw-semibold">
                                <option value="">Toutes livraisons</option>
                                <option value="store" {{ strtolower((string) request('delivery_type')) === 'store' ? 'selected' : '' }}>Store</option>
                                <option value="our_team" {{ in_array(strtolower((string) request('delivery_type')), ['our_team', 'livreur'], true) ? 'selected' : '' }}>Livreur</option>
                                <option value="ozon" {{ strtolower((string) request('delivery_type')) === 'ozon' ? 'selected' : '' }}>Ozon</option>
                                <option value="autre" {{ strtolower((string) request('delivery_type')) === 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>

                    <!-- Utilisateur (Confirmée par / Créée par) -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">{{ $isPosChannel ? 'Créée par' : 'Confirmée par' }}</span>
                            <select name="staff_id" class="form-select form-select-sm fw-semibold">
                                <option value="">Tous utilisateurs</option>
                                @if(!$isPosChannel)
                                    <option value="unconfirmed" {{ request('staff_id') === 'unconfirmed' ? 'selected' : '' }}>⏳ Non confirmées</option>
                                @endif
                                @foreach($staffUsers ?? [] as $su)
                                    <option value="{{ $su->id }}" {{ (string) request('staff_id') === (string) $su->id ? 'selected' : '' }}>
                                        {{ $su->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Magasin / Dépôt source -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">Dépôt</span>
                            <select name="store_id" class="form-select form-select-sm">
                                <option value="">Tous les dépôts</option>
                                <option value="unassigned" {{ request('store_id') === 'unassigned' ? 'selected' : '' }}>⚠️ Dépôt à affecter</option>
                                @foreach($stores ?? [] as $st)
                                    <option value="{{ $st->id }}" {{ (string) request('store_id') === (string) $st->id ? 'selected' : '' }}>
                                        {{ $st->name }} ({{ $st->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filtre de Date (Preset) -->
                    <div class="col-6 col-md-3 col-xl-3">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">Période</span>
                            <select name="date_preset" id="orderDatePreset" class="form-select form-select-sm">
                                <option value="">📅 Toutes les dates</option>
                                <option value="today" {{ request('date_preset') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                                <option value="yesterday" {{ request('date_preset') == 'yesterday' ? 'selected' : '' }}>Hier</option>
                                <option value="this_week" {{ request('date_preset') == 'this_week' ? 'selected' : '' }}>Cette semaine</option>
                                <option value="this_month" {{ request('date_preset') == 'this_month' ? 'selected' : '' }}>Ce mois-ci</option>
                                <option value="last_month" {{ request('date_preset') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                                <option value="custom" {{ request('date_preset') == 'custom' || (!request('date_preset') && (request('date_from') || request('date_to'))) ? 'selected' : '' }}>Période personnalisée...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date de début (Du) -->
                    <div class="col-6 col-md-3 col-xl-3">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">Du</span>
                            <input type="date" name="date_from" id="orderDateFrom" class="form-control form-control-sm"
                                   value="{{ request('date_from', request('date_preset') ? ($stats['date_from'] ?? '') : '') }}"
                                   title="Date de début">
                        </div>
                    </div>

                    <!-- Date de fin (Au) -->
                    <div class="col-6 col-md-3 col-xl-3">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label">Au</span>
                            <input type="date" name="date_to" id="orderDateTo" class="form-control form-control-sm"
                                   value="{{ request('date_to', request('date_preset') ? ($stats['date_to'] ?? '') : '') }}"
                                   title="Date de fin">
                        </div>
                    </div>

                    <!-- Boutons Filtrer / Réinitialiser -->
                    <div class="col-6 col-md-3 col-xl-3">
                        <div class="orders-filter-field">
                            <span class="orders-filter-label" aria-hidden="true">&nbsp;</span>
                            <div class="orders-filter-actions">
                                <button type="submit" class="btn-brand-primary btn-sm flex-grow-1 justify-content-center" title="Appliquer les filtres">
                                    <i class="fas fa-filter me-1"></i> <span>Appliquer</span>
                                </button>
                                <a href="{{ route($indexRoute) }}" class="btn-brand-light btn-sm flex-shrink-0" title="Réinitialiser les filtres">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Exporter sur Mobile (d-md-none) -->
                    <div class="col-12 d-md-none mt-2 pt-2 border-top d-flex gap-2">
                        <a href="{{ route('export.orders.pdf', $exportParams) }}" data-no-spa="true" class="btn btn-sm btn-outline-danger flex-fill justify-content-center">
                            <i class="fas fa-file-pdf me-1"></i> Exporter PDF
                        </a>
                        <a href="{{ route('export.orders', $exportParams) }}" data-no-spa="true" class="btn btn-sm btn-outline-secondary flex-fill justify-content-center">
                            <i class="fas fa-file-csv me-1 text-success"></i> Exporter CSV
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Badges with Quick Removal Pills -->
    @if(request('search') || $hasActiveFilters)
        <div class="d-flex align-items-center gap-2 flex-wrap mb-3 px-1">
            <span class="text-muted small fw-semibold">
                <i class="fas fa-filter text-primary me-1"></i> Filtres appliqués :
            </span>

            @if(request('search'))
                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Recherche : "{{ request('search') }}" <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('status'))
                @php
                    $statusBadgeLabels = [
                        'pending' => 'En attente',
                        'en_demande' => 'Sur Commande',
                        'reserved' => 'Réservé',
                        'processing' => 'En cours',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée'
                    ];
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Livraison : {{ $statusBadgeLabels[request('status')] ?? request('status') }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('payment_status'))
                @php
                    $payBadgeLabels = [
                        'pending' => 'En attente',
                        'partial' => 'Partiel',
                        'paid' => 'Payé',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé'
                    ];
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['payment_status' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Paiement : {{ $payBadgeLabels[request('payment_status')] ?? request('payment_status') }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('tax_mode'))
                <a href="{{ request()->fullUrlWithQuery(['tax_mode' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    <i class="fas fa-tag {{ strtolower((string) request('tax_mode')) === 'ht' ? 'text-warning' : 'text-primary' }} me-0.5"></i> Régime : {{ strtoupper((string) request('tax_mode')) }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('delivery_type'))
                @php
                    $deliveryTypeKey = strtolower((string) request('delivery_type'));
                    $deliveryTypeLabel = \App\Models\Order::DELIVERY_TYPES[$deliveryTypeKey] ?? ucfirst($deliveryTypeKey);
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['delivery_type' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    <i class="fas fa-shipping-fast text-danger me-0.5"></i> Livraison : {{ $deliveryTypeLabel }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('staff_id'))
                @php
                    $selStaffObj = isset($staffUsers) && is_numeric(request('staff_id'))
                        ? $staffUsers->firstWhere('id', (int) request('staff_id'))
                        : null;
                    $staffFilterLabel = request('staff_id') === 'unconfirmed'
                        ? 'Non confirmées'
                        : ($selStaffObj?->name ?? '#' . request('staff_id'));
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['staff_id' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    <i class="fas fa-user-check text-success me-0.5"></i> {{ $isPosChannel ? 'Créée par' : 'Confirmée par' }} : {{ $staffFilterLabel }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('store_id'))
                @php
                    $selectedStoreObj = isset($stores) && is_numeric(request('store_id'))
                        ? $stores->firstWhere('id', (int) request('store_id'))
                        : null;
                    $storeFilterLabel = request('store_id') === 'unassigned'
                        ? 'À affecter'
                        : ($selectedStoreObj?->name ?? '#' . request('store_id'));
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['store_id' => null]) }}"
                   class="badge {{ $selectedStoreObj ? 'store-badge-themed' : 'bg-white text-dark border' }} rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs"
                   style="{{ $selectedStoreObj ? $selectedStoreObj->badge_style : '' }}"
                   title="Supprimer ce filtre">
                    <i class="fas fa-warehouse {{ $selectedStoreObj ? '' : 'text-primary' }} me-0.5"></i> Dépôt : {{ $storeFilterLabel }} <i class="fas fa-times {{ $selectedStoreObj ? '' : 'text-muted' }} ms-0.5"></i>
                </a>
            @endif

            @if($hasDateFilter)
                @php
                    $presetLabels = [
                        'today' => "Aujourd'hui",
                        'yesterday' => 'Hier',
                        'this_week' => 'Cette semaine',
                        'this_month' => 'Ce mois-ci',
                        'last_month' => 'Mois dernier',
                        'custom' => 'Personnalisé'
                    ];
                    $presetName = $presetLabels[request('date_preset')] ?? '';
                    $dateRangeFormatted = '';
                    if (!empty($stats['date_from']) && !empty($stats['date_to'])) {
                        if ($stats['date_from'] === $stats['date_to']) {
                            $dateRangeFormatted = \Carbon\Carbon::parse($stats['date_from'])->format('d/m/Y');
                        } else {
                            $dateRangeFormatted = \Carbon\Carbon::parse($stats['date_from'])->format('d/m/Y') . ' → ' . \Carbon\Carbon::parse($stats['date_to'])->format('d/m/Y');
                        }
                    } elseif (!empty($stats['date_from'])) {
                        $dateRangeFormatted = 'Depuis le ' . \Carbon\Carbon::parse($stats['date_from'])->format('d/m/Y');
                    } elseif (!empty($stats['date_to'])) {
                        $dateRangeFormatted = "Jusqu'au " . \Carbon\Carbon::parse($stats['date_to'])->format('d/m/Y');
                    }
                    $dateBadgeTitle = $presetName ? ($presetName . ($dateRangeFormatted ? " ({$dateRangeFormatted})" : '')) : $dateRangeFormatted;
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['date_preset' => null, 'date_from' => null, 'date_to' => null, 'date' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer le filtre de date">
                    <i class="far fa-calendar-alt text-primary me-0.5"></i> Date : {{ $dateBadgeTitle }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            <a href="{{ route($indexRoute) }}" class="small text-danger fw-semibold text-decoration-none ms-1">
                Effacer tout
            </a>
        </div>
    @endif

    <!-- Mobile Orders Container (Touch First, d-block d-md-none) -->
    <div class="mobile-orders-container d-block d-md-none">
        <div class="d-flex align-items-center justify-content-between px-1 mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark" style="font-size: 0.92rem; letter-spacing: -0.01em;">
                    <i class="fas fa-receipt text-danger me-1.5 opacity-75"></i>Commandes récentes
                </span>
                <span class="badge rounded-pill bg-danger-subtle text-danger fw-bold px-2 py-0.5" style="font-size: 0.72rem;">
                    {{ $orders->total() }}
                </span>
            </div>
            @if(request('status') || request('payment_status') || request('tax_mode') || request('delivery_type') || request('staff_id') || request('store_id') || request('search'))
                <a href="{{ route($indexRoute) }}" class="small text-danger fw-semibold text-decoration-none" style="font-size: 0.75rem;">
                    <i class="fas fa-times me-0.5"></i>Effacer filtres
                </a>
            @endif
        </div>

        @forelse($orders as $order)
            @include('orders.partials.mobile-order-card', ['order' => $order])
        @empty
            <div class="text-center py-5 bg-white rounded-4 border p-4 shadow-sm">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 20px;">
                    <i class="fas fa-shopping-bag text-muted"></i>
                </div>
                <h6 class="fw-bold text-dark">Aucune commande trouvée</h6>
                <p class="text-muted small mb-0">Aucun enregistrement ne correspond à vos critères de recherche.</p>
                @if(request('search') || $hasActiveFilters)
                    <a href="{{ route($indexRoute) }}" class="btn-brand-primary btn-sm mt-3">
                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                    </a>
                @endif
            </div>
        @endforelse

        <!-- Mobile Pagination Footer -->
        @if($orders->hasPages())
            <div class="bg-white p-3 rounded-4 border shadow-sm mt-3 mb-4">
                <div class="text-muted small text-center mb-2">
                    Affichage de <span class="fw-semibold text-dark">{{ $orders->firstItem() ?? 0 }}</span> à <span class="fw-semibold text-dark">{{ $orders->lastItem() ?? 0 }}</span> sur <span class="fw-semibold text-dark">{{ $orders->total() }}</span>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Tableau des commandes Desktop (d-none d-md-block) -->
    <div class="brand-table-card orders-table-card d-none d-md-block">
        <div class="table-responsive">
            <table class="brand-table align-middle" style="width: 100%; min-width: 1020px;">
                <thead>
                    <tr>
                        <th style="min-width: 130px; padding-left: 1.25rem;">N° Commande</th>
                        <th style="min-width: 220px;">Client</th>
                        <th style="min-width: 180px;">Articles</th>
                        <th class="text-end" style="min-width: 120px;">Total</th>
                        <th class="text-center" style="min-width: 130px;">Livraison</th>
                        <th class="text-center" style="min-width: 130px;">Paiement</th>
                        <th style="min-width: 100px;">Date</th>
                        <th class="text-end" style="min-width: 110px; padding-right: 1.25rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td style="padding-left: 1.25rem;">
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                    #{{ $order->order_number }}
                                </a>
                                <button type="button"
                                        class="order-copy-inline-btn"
                                        onclick="copyOrderNumber('{{ $order->order_number }}', this)"
                                        title="Copier le N° de commande">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            @php $forcedOosItems = $order->forcedOutOfStockItems(); @endphp
                            @if($forcedOosItems->isNotEmpty())
                                <div class="mt-1">
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5"
                                          style="font-size: 0.66rem;"
                                          title="Contient un produit hors stock (forcé en stock) : {{ $forcedOosItems->pluck('display_name')->implode(', ') }}">
                                        <i class="fas fa-triangle-exclamation text-warning me-1"></i>Rupture ({{ $forcedOosItems->count() }})
                                    </span>
                                </div>
                            @endif
                            <div class="d-flex flex-column align-items-start gap-1 mt-1">
                                @if($order->createdByUser)
                                    <span class="order-staff-chip is-creator" title="Commande créée par {{ $order->createdByUser->name }} le {{ $order->created_at->format('d/m/Y à H:i') }}">
                                        <i class="fas fa-user-tie"></i>
                                        <span class="order-staff-chip-name">{{ $order->createdByUser->name }}</span>
                                    </span>
                                @endif
                                @if(!$order->isPosOrder() || ($order->confirmedByUser && (int) $order->confirmed_by !== (int) $order->created_by))
                                    <span class="order-staff-chip is-confirmed {{ $order->confirmedByUser ? '' : 'd-none' }}"
                                          id="dt-confirmed-by-{{ $order->id }}"
                                          title="{{ $order->confirmedByUser ? 'Confirmée par ' . $order->confirmedByUser->name . ($order->confirmed_at ? ' le ' . $order->confirmed_at->format('d/m/Y à H:i') : '') : '' }}">
                                        <i class="fas fa-user-check"></i>
                                        <span class="order-staff-chip-name" id="dt-confirmed-name-{{ $order->id }}">{{ $order->confirmedByUser?->name }}</span>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="order-table-avatar {{ $order->is_blacklisted ? 'is-blacklisted' : '' }}">
                                    @if($order->is_blacklisted)
                                        <i class="fas fa-ban"></i>
                                    @else
                                        {{ strtoupper(substr($order->customer_name ?: 'C', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="fw-bold {{ $order->is_blacklisted ? 'text-danger' : 'text-dark' }}" style="font-size: 0.92rem; {{ $order->is_blacklisted ? 'color: #dc2626 !important;' : '' }}">{{ $order->customer_name ?: 'Client invité' }}</span>
                                        @if($order->is_blacklisted)
                                            <span class="badge bg-danger text-white border border-danger-subtle fw-bold d-none" style="font-size: 0.68rem; padding: 2px 7px;" title="Client sur liste noire : {{ $order->blacklist_reason ?: 'Sans motif spécifié' }}">
                                                <i class="fas fa-ban me-1"></i>Liste Noire
                                            </span>
                                        @endif
                                        @php $tier = $order->customer_tier; @endphp
                                        <span class="badge {{ $tier['badge_class'] }} d-none" style="font-size: 0.68rem; padding: 2px 6px;" title="{{ $tier['description'] }}">
                                            <i class="fas {{ $tier['icon'] }} me-0.5"></i>{{ $tier['short'] }}
                                        </span>
                                    </div>
                                    <div class="text-muted small d-flex align-items-center gap-2">
                                        <span class="text-truncate" style="max-width: 190px;">{{ $order->customer_email ?: ($order->customer_phone ?: 'Aucun contact') }}</span>
                                        @if($order->customer_record)
                                            <a href="{{ route('customers.show', $order->customer_record) }}" class="text-primary text-decoration-none" title="Consulter la fiche client et ses statistiques">
                                                <i class="fas fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php $firstItem = $order->items->first(); @endphp
                            @if($firstItem)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="position-relative flex-shrink-0" style="width: 40px; height: 40px; border-radius: 8px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; padding: 2px; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ $firstItem->product_image }}" 
                                             alt="{{ $firstItem->clean_product_name }}" 
                                             style="width: 100%; height: 100%; object-fit: contain;" 
                                             onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                                             loading="lazy">
                                        @if($order->items->count() > 1)
                                            <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-dark text-white fw-bold" style="font-size: 0.58rem; padding: 1px 4px; transform: translate(15%, 15%); box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                                                +{{ $order->items->count() - 1 }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0" style="max-width: 150px;">
                                        <div class="text-truncate fw-semibold text-dark small" title="{{ $firstItem->displayName }}">
                                            {{ $firstItem->clean_product_name }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.70rem;">
                                            @if($firstItem->variant_name)
                                                <span class="badge bg-light text-secondary border px-1 py-0 me-1" style="font-size: 0.65rem;">{{ $firstItem->variant_name }}</span>
                                            @endif
                                            <span>x{{ $firstItem->quantity }}</span>
                                            @if($order->items->count() > 1)
                                                <span class="text-muted ms-1">({{ $order->items->count() }} art.)</span>
                                            @endif
                                        </div>
                                        @php
                                            $orderStoreBadges = $order->items->pluck('store')->filter()->unique('id');
                                            $hasUnassignedItem = $order->items->whereNull('store_id')->isNotEmpty();
                                        @endphp
                                        <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                            @foreach($orderStoreBadges as $stBadge)
                                                <span class="badge store-badge-themed px-1.5 py-0" style="font-size: 0.64rem; {{ $stBadge->badge_style }}" title="{{ $stBadge->name }}">
                                                    <i class="fas fa-warehouse me-0.5"></i>{{ $stBadge->name }}
                                                </span>
                                            @endforeach
                                            @if($hasUnassignedItem)
                                                <a href="{{ route('orders.show', $order) }}" class="badge bg-warning-subtle text-warning border border-warning-subtle px-1.5 py-0 text-decoration-none" style="font-size: 0.64rem;" title="Cliquer pour affecter un magasin / dépôt">
                                                    <i class="fas fa-store-slash me-0.5"></i>À affecter
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-light text-secondary px-2.5 py-1 border" style="border-radius: 6px;">0 article</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark fs-6 font-heading tabular-nums">
                            <div class="orders-strong" id="dt-order-total-{{ $order->id }}">{{ $order->formatted_total }}</div>
                            @php $orderTaxMode = $order->normalized_tax_mode; @endphp
                            <div class="dropdown d-inline-block mt-1">
                                <button type="button"
                                        class="tax-mode-pill tax-mode-{{ $orderTaxMode }} dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        data-bs-auto-close="true"
                                        data-bs-boundary="viewport"
                                        data-bs-popper-config='{"strategy":"fixed"}'
                                        aria-expanded="false"
                                        id="dt-tax-pill-{{ $order->id }}"
                                        title="Changer le régime TTC / HT">
                                    <span id="dt-tax-label-{{ $order->id }}">{{ $order->tax_mode_label }}</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu text-start" aria-labelledby="dt-tax-pill-{{ $order->id }}">
                                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Régime fiscal</li>
                                    <li>
                                        <button type="button" data-tax-mode="ttc" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $orderTaxMode === 'ttc' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'tax_mode', 'ttc')">
                                            <span><span class="status-dot tax-dot-ttc me-1.5"></span> TTC</span>
                                            @if($orderTaxMode === 'ttc') <i class="fas fa-check text-primary small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-tax-mode="ht" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $orderTaxMode === 'ht' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'tax_mode', 'ht')">
                                            <span><span class="status-dot tax-dot-ht me-1.5"></span> HT</span>
                                            @if($orderTaxMode === 'ht') <i class="fas fa-check text-warning small"></i> @endif
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-1 {{ (float) ($order->remaining_amount ?? 0) > 0 ? '' : 'd-none' }}" id="dt-order-remaining-wrap-{{ $order->id }}">
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" id="dt-order-remaining-{{ $order->id }}">
                                    Reste : {{ currency($order->remaining_amount ?? 0) }}
                                </span>
                                <div class="text-muted {{ $order->payment_due_date ? '' : 'd-none' }}" style="font-size: 0.64rem;" id="dt-order-due-{{ $order->id }}">
                                    {{ $order->payment_due_date ? 'Éch. ' . $order->payment_due_date->format('d/m/Y') : '' }}
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $stLower = strtolower($order->status);
                                $normalizedStatus = match($stLower) {
                                    'delivered', 'livrée' => 'delivered',
                                    'shipped', 'expédiée' => 'shipped',
                                    'processing', 'en cours' => 'processing',
                                    'en_demande' => 'en_demande',
                                    'reserved', 'réservé' => 'reserved',
                                    'cancelled', 'annulée' => 'cancelled',
                                    default => 'pending'
                                };
                                $statusDot = match($normalizedStatus) {
                                    'delivered' => 'green',
                                    'shipped' => 'teal',
                                    'processing' => 'blue',
                                    'en_demande' => 'indigo',
                                    'reserved' => 'purple',
                                    'cancelled' => 'red',
                                    default => 'amber'
                                };
                                $statusClass = match($normalizedStatus) {
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    'shipped' => 'info',
                                    'processing' => 'primary',
                                    'en_demande' => 'indigo',
                                    'reserved' => 'purple',
                                    default => 'warning'
                                };
                                $statusText = match($normalizedStatus) {
                                    'delivered' => 'Livrée',
                                    'shipped' => 'Expédiée',
                                    'processing' => 'En cours',
                                    'en_demande' => 'Sur Commande',
                                    'reserved' => 'Réservé',
                                    'cancelled' => 'Annulée',
                                    default => 'En attente'
                                };
                            @endphp
                            <div class="dropdown d-inline-block">
                                <button type="button" 
                                        class="brand-badge brand-badge-btn {{ $statusClass }} status-pill-{{ $normalizedStatus }} dropdown-toggle" 
                                        data-bs-toggle="dropdown" 
                                        data-bs-auto-close="true"
                                        data-bs-boundary="viewport"
                                        data-bs-popper-config='{"strategy":"fixed"}'
                                        data-cancellation-reason="{{ $order->cancellation_reason ?? '' }}"
                                        aria-expanded="false"
                                        id="dt-status-pill-{{ $order->id }}"
                                        title="Changer le statut de livraison">
                                    <span class="status-dot dot-{{ $statusDot }}" id="dt-status-dot-{{ $order->id }}"></span>
                                    <span id="dt-status-label-{{ $order->id }}">{{ $statusText }}</span>
                                    <i class="fas fa-chevron-down ms-0.5" style="font-size: 0.6rem;"></i>
                                </button>
                                <ul class="dropdown-menu shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="dt-status-pill-{{ $order->id }}">
                                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de livraison</li>
                                    <li>
                                        <button type="button" data-status="pending" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'pending' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'pending')">
                                            <span><span class="status-dot dot-amber me-1.5"></span> En attente</span>
                                            @if($normalizedStatus === 'pending') <i class="fas fa-check text-warning small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-status="en_demande" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'en_demande' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'en_demande')">
                                            <span><span class="status-dot dot-blue me-1.5"></span> Sur Commande</span>
                                            @if($normalizedStatus === 'en_demande') <i class="fas fa-check text-primary small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-status="reserved" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'reserved' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'reserved')">
                                            <span><span class="status-dot dot-purple me-1.5"></span> Réservé</span>
                                            @if($normalizedStatus === 'reserved') <i class="fas fa-check text-purple small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-status="processing" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'processing' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'processing')">
                                            <span><span class="status-dot dot-blue me-1.5"></span> En cours</span>
                                            @if($normalizedStatus === 'processing') <i class="fas fa-check text-primary small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-status="shipped" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'shipped' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'shipped')">
                                            <span><span class="status-dot dot-teal me-1.5"></span> Expédiée</span>
                                            @if($normalizedStatus === 'shipped') <i class="fas fa-check text-info small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-status="delivered" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'delivered' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'delivered')">
                                            <span><span class="status-dot dot-green me-1.5"></span> Livrée</span>
                                            @if($normalizedStatus === 'delivered') <i class="fas fa-check text-success small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-status="cancelled" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between text-danger {{ $normalizedStatus === 'cancelled' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'cancelled')">
                                            <span><span class="status-dot dot-red me-1.5"></span> Annulée</span>
                                            @if($normalizedStatus === 'cancelled') <i class="fas fa-check text-danger small"></i> @endif
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            @php
                                $orderDeliveryType = $order->effective_delivery_type;
                                $orderDeliveryIcon = match ($orderDeliveryType) {
                                    'our_team' => 'fa-people-carry-box',
                                    'ozon' => 'fa-box-open',
                                    'autre' => 'fa-truck-arrow-right',
                                    default => 'fa-store',
                                };
                            @endphp
                            <div class="mt-1">
                                <div class="dropdown d-inline-block">
                                    <button type="button"
                                            class="delivery-type-pill delivery-type-{{ $orderDeliveryType }} dropdown-toggle"
                                            data-bs-toggle="dropdown"
                                            data-bs-auto-close="true"
                                            data-bs-boundary="viewport"
                                            data-bs-popper-config='{"strategy":"fixed"}'
                                            data-shipping-cost="{{ round((float) ($order->shipping_cost ?? 0), 2) }}"
                                            data-suggested-shipping="{{ \App\Services\DeliveryService::getDeliveryCost($order->shipping_city, 40.0) }}"
                                            data-shipping-city="{{ $order->shipping_city ?? '' }}"
                                            aria-expanded="false"
                                            id="dt-delivery-pill-{{ $order->id }}"
                                            title="Changer le type de livraison">
                                        <i class="fas {{ $orderDeliveryIcon }}" id="dt-delivery-icon-{{ $order->id }}"></i>
                                        <span id="dt-delivery-label-{{ $order->id }}">{{ $order->effective_delivery_type_label }}</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu text-start" aria-labelledby="dt-delivery-pill-{{ $order->id }}">
                                        <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Type de livraison</li>
                                        <li>
                                            <button type="button" data-delivery-type="store" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $orderDeliveryType === 'store' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'delivery_type', 'store')">
                                                <span><span class="status-dot delivery-dot-store me-1.5"></span> Store</span>
                                                @if($orderDeliveryType === 'store') <i class="fas fa-check text-danger small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" data-delivery-type="our_team" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $orderDeliveryType === 'our_team' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'delivery_type', 'our_team')">
                                                <span><span class="status-dot delivery-dot-our_team me-1.5"></span> Livreur</span>
                                                @if($orderDeliveryType === 'our_team') <i class="fas fa-check text-primary small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" data-delivery-type="ozon" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $orderDeliveryType === 'ozon' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'delivery_type', 'ozon')">
                                                <span><span class="status-dot delivery-dot-ozon me-1.5"></span> Ozon</span>
                                                @if($orderDeliveryType === 'ozon') <i class="fas fa-check text-warning small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" data-delivery-type="autre" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $orderDeliveryType === 'autre' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'delivery_type', 'autre')">
                                                <span><span class="status-dot delivery-dot-autre me-1.5"></span> Autre</span>
                                                @if($orderDeliveryType === 'autre') <i class="fas fa-check text-purple small"></i> @endif
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-1 {{ ($normalizedStatus === 'cancelled' && $order->cancellation_reason) ? '' : 'd-none' }}" id="dt-cancel-reason-wrap-{{ $order->id }}">
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle text-wrap text-start px-2 py-1"
                                      style="font-size: 0.64rem; max-width: 170px; line-height: 1.25;"
                                      id="dt-cancel-reason-text-{{ $order->id }}"
                                      title="{{ $order->cancellation_reason }}">
                                    <i class="fas fa-comment-slash me-1"></i>{{ \Illuminate\Support\Str::limit($order->cancellation_reason, 55) }}
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $payStatus = strtolower($order->payment_status);
                                $isPaid = in_array($payStatus, ['paid', 'payé']);
                                $pClass = match($payStatus) {
                                    'paid', 'payé' => 'paid',
                                    'partial', 'partiel' => 'partial',
                                    'failed', 'échoué' => 'failed',
                                    'refunded', 'remboursé' => 'refunded',
                                    default => 'pending'
                                };
                                $pText = match($payStatus) {
                                    'paid', 'payé' => 'Payé',
                                    'partial', 'partiel' => 'Partiel',
                                    'failed', 'échoué' => 'Échoué',
                                    'refunded', 'remboursé' => 'Remboursé',
                                    default => 'En attente'
                                };
                                $pIcon = match($payStatus) {
                                    'paid', 'payé' => 'fa-check-circle',
                                    'partial', 'partiel' => 'fa-circle-half-stroke',
                                    'failed', 'échoué' => 'fa-exclamation-circle',
                                    'refunded', 'remboursé' => 'fa-undo',
                                    default => 'fa-clock'
                                };
                                $dtPayMethodLabel = $order->payment_method ? match(strtolower((string) $order->payment_method)) {
                                    'cod', 'cash_on_delivery' => 'COD',
                                    'cash' => 'Espèces',
                                    'card' => 'Carte',
                                    'virement', 'bank_transfer' => 'Virement',
                                    'cheque', 'chèque' => 'Chèque',
                                    'credit' => 'Crédit',
                                    'other' => 'Autre',
                                    default => ucfirst(str_replace('_', ' ', (string) $order->payment_method)),
                                } : null;
                            @endphp
                            @php
                                $normalizedPayStatus = match($payStatus) {
                                    'paid', 'payé' => 'paid',
                                    'partial', 'partiel' => 'partial',
                                    'failed', 'échoué' => 'failed',
                                    'refunded', 'remboursé' => 'refunded',
                                    default => 'pending'
                                };
                            @endphp
                            <div class="dropdown d-inline-block">
                                <button type="button"
                                        class="brand-badge brand-badge-btn pay-badge-{{ $pClass }} py-1 px-2.5 dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        data-bs-auto-close="true"
                                        data-bs-boundary="viewport"
                                        data-bs-popper-config='{"strategy":"fixed"}'
                                        data-total="{{ round((float) $order->total, 2) }}"
                                        data-paid-amount="{{ round((float) ($order->paid_amount ?? 0), 2) }}"
                                        data-due-date="{{ $order->payment_due_date?->format('Y-m-d') ?? '' }}"
                                        aria-expanded="false"
                                        id="dt-pay-pill-{{ $order->id }}"
                                        title="Changer le statut de paiement">
                                    <i class="fas {{ $pIcon }}" id="dt-pay-icon-{{ $order->id }}"></i>
                                    <span id="dt-pay-label-{{ $order->id }}">{{ $pText }}</span>
                                    <i class="fas fa-chevron-down ms-0.5" style="font-size: 0.55rem;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu text-start" aria-labelledby="dt-pay-pill-{{ $order->id }}">
                                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de paiement</li>
                                    <li>
                                        <button type="button" data-payment-status="pending" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedPayStatus === 'pending' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'pending')">
                                            <span><span class="status-dot dot-amber me-1.5"></span> En attente</span>
                                            @if($normalizedPayStatus === 'pending') <i class="fas fa-check text-warning small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-payment-status="partial" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedPayStatus === 'partial' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'partial')">
                                            <span><span class="status-dot dot-blue me-1.5"></span> Partiel</span>
                                            @if($normalizedPayStatus === 'partial') <i class="fas fa-check text-primary small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-payment-status="paid" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedPayStatus === 'paid' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'paid')">
                                            <span><span class="status-dot dot-green me-1.5"></span> Payé</span>
                                            @if($normalizedPayStatus === 'paid') <i class="fas fa-check text-success small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-payment-status="failed" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedPayStatus === 'failed' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'failed')">
                                            <span><span class="status-dot dot-red me-1.5"></span> Échoué</span>
                                            @if($normalizedPayStatus === 'failed') <i class="fas fa-check text-danger small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-payment-status="refunded" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedPayStatus === 'refunded' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'refunded')">
                                            <span><span class="status-dot dot-gray me-1.5"></span> Remboursé</span>
                                            @if($normalizedPayStatus === 'refunded') <i class="fas fa-check text-secondary small"></i> @endif
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            @if($dtPayMethodLabel)
                                <div class="text-muted mt-1" style="font-size: 0.68rem;">
                                    <i class="fas fa-wallet opacity-75 me-0.5"></i>{{ $dtPayMethodLabel }}
                                </div>
                            @endif
                        </td>
                        <td class="tabular-nums">
                            <div class="text-dark fw-semibold small">{{ $order->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-end" style="padding-right: 1.25rem;">
                            <div class="action-buttons-group justify-content-end">
                                <a href="{{ route('orders.show', $order) }}" class="action-btn action-btn-view" title="Détails de la commande">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="action-btn action-btn-edit" title="Modifier la commande">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @if($order->invoice)
                                    <a href="{{ route('invoices.show', $order->invoice) }}" class="action-btn action-btn-success" title="Voir la facture">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                @endif
                                @if(in_array($order->status, ['pending', 'cancelled']))
                                    <form method="POST" 
                                          action="{{ route('orders.destroy', $order->id) }}" 
                                          class="m-0 p-0 d-inline"
                                          data-confirm-delete="true"
                                          data-item-type="order"
                                          data-item-name="Commande #{{ $order->order_number }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-delete" title="Supprimer la commande">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-shopping-bag text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucune commande trouvée</h5>
                                <p class="text-muted">Aucun enregistrement ne correspond à vos critères de recherche.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Affichage de {{ $orders->firstItem() ?? 0 }} à {{ $orders->lastItem() ?? 0 }} sur {{ $orders->total() }} commandes
            </span>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyOrderNumber(orderNum, btn) {
        if (!orderNum) return;
        const textToCopy = orderNum.toString().trim();
        
        const showSuccess = function() {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(function() {
                btn.innerHTML = originalHtml;
            }, 1500);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(showSuccess).catch(function() {
                fallbackCopy(textToCopy, showSuccess);
            });
        } else {
            fallbackCopy(textToCopy, showSuccess);
        }
    }

    function fallbackCopy(text, callback) {
        const temp = document.createElement('textarea');
        temp.value = text;
        temp.style.position = 'fixed';
        temp.style.left = '-9999px';
        document.body.appendChild(temp);
        temp.focus();
        temp.select();
        try {
            document.execCommand('copy');
            if (callback) callback();
        } catch (e) {
            console.error('Copy failed:', e);
        }
        document.body.removeChild(temp);
    }

    /**
     * Helper to immediately close any dropdown for an order
     */
    function closeOrderDropdowns(orderId) {
        const toggles = [
            document.getElementById(`dt-status-pill-${orderId}`),
            document.getElementById(`dt-pay-pill-${orderId}`),
            document.getElementById(`dt-tax-pill-${orderId}`),
            document.getElementById(`dt-delivery-pill-${orderId}`),
            document.getElementById(`mob-status-pill-${orderId}`),
            document.getElementById(`mob-pay-pill-${orderId}`),
            document.getElementById(`mob-tax-pill-${orderId}`),
            document.getElementById(`mob-delivery-pill-${orderId}`)
        ].filter(Boolean);

        toggles.forEach(btn => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                const inst = bootstrap.Dropdown.getInstance(btn);
                if (inst) inst.hide();
            }
            btn.classList.remove('show');
            btn.setAttribute('aria-expanded', 'false');
            const parent = btn.closest('.dropdown');
            if (parent) {
                parent.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
            }
        });

        // Close any other open status dropdown menu immediately
        document.querySelectorAll('.status-dropdown-menu.show').forEach(m => {
            m.classList.remove('show');
            const toggle = m.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]');
            if (toggle) {
                toggle.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
                if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                    const inst = bootstrap.Dropdown.getInstance(toggle);
                    if (inst) inst.hide();
                }
            }
        });
    }

    /**
     * Quick In-Place Status & Payment Status Updater
     */
    async function promptDeliveryShippingPrice(deliveryType, currentShipping, suggestedShipping, cityName) {
        const labels = { our_team: 'Livreur', ozon: 'Ozon', autre: 'Autre', store: 'Store' };
        const typeLabel = labels[deliveryType] || deliveryType;
        const defaultPrice = currentShipping > 0 ? currentShipping : (suggestedShipping > 0 ? suggestedShipping : 40);
        const presets = Array.from(new Set([0, 20, 30, 40, suggestedShipping].filter(v => !Number.isNaN(v) && v >= 0))).sort((a, b) => a - b);

        if (typeof Swal === 'undefined') {
            const raw = window.prompt(`Prix de livraison (${typeLabel}) en DH :`, String(defaultPrice));
            if (raw === null) return null;
            const parsed = parseFloat(String(raw).replace(',', '.'));
            return Number.isNaN(parsed) || parsed < 0 ? null : parsed;
        }

        const presetHtml = presets.map(p =>
            `<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 fw-semibold swal-ship-preset" data-price="${p}" style="font-size:0.78rem;">${p} DH</button>`
        ).join('');

        const cityHint = cityName ? `<div class="text-muted small mb-2"><i class="fas fa-map-marker-alt text-danger me-1"></i>Ville : <strong>${cityName}</strong></div>` : '';

        const result = await Swal.fire({
            title: `Frais de livraison — ${typeLabel}`,
            html: `
                ${cityHint}
                <div class="input-group input-group-lg mb-2.5">
                    <input type="number" id="swal-shipping-cost-input" class="form-control fw-bold text-center" step="0.01" min="0" value="${defaultPrice}" placeholder="0.00">
                    <span class="input-group-text fw-bold bg-light">DH</span>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-1.5 mt-2">${presetHtml}</div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check me-1"></i> Enregistrer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#dc2626',
            focusConfirm: false,
            didOpen: () => {
                const inp = document.getElementById('swal-shipping-cost-input');
                if (inp) {
                    inp.focus();
                    inp.select();
                    inp.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            Swal.clickConfirm();
                        }
                    });
                }
                document.querySelectorAll('.swal-ship-preset').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (inp) {
                            inp.value = btn.dataset.price;
                            inp.focus();
                        }
                    });
                });
            },
            preConfirm: () => {
                const inp = document.getElementById('swal-shipping-cost-input');
                const val = parseFloat(String(inp ? inp.value : '').replace(',', '.'));
                if (Number.isNaN(val) || val < 0) {
                    Swal.showValidationMessage('Veuillez saisir un prix de livraison valide (≥ 0 DH).');
                    return false;
                }
                return Math.round(val * 100) / 100;
            }
        });

        return result.isConfirmed ? result.value : null;
    }

    const ORDER_CANCELLATION_PRESETS = @json(\App\Models\Order::CANCELLATION_REASONS);

    async function promptCancellationReason(existingReason = '') {
        if (typeof Swal === 'undefined') {
            while (true) {
                const raw = window.prompt("Raison d'annulation (obligatoire) :", existingReason || '');
                if (raw === null) return null;
                const clean = String(raw).trim();
                if (clean !== '') return clean;
                alert("La raison d'annulation est obligatoire.");
            }
        }

        const chipsHtml = ORDER_CANCELLATION_PRESETS.map(r => {
            const safe = String(r).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return `<button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold swal-cancel-preset" data-reason="${safe}" style="font-size:0.74rem;">${safe}</button>`;
        }).join('');

        const res = await Swal.fire({
            title: "Raison d'annulation (obligatoire)",
            html: `
                <div class="text-muted small mb-2.5">Indiquez pourquoi cette commande est annulée :</div>
                <div class="d-flex flex-wrap justify-content-center gap-1.5 mb-3">${chipsHtml}</div>
                <textarea id="swal-cancel-reason-input" class="form-control text-start" rows="3" maxlength="1000" placeholder="Saisissez la raison d'annulation..."></textarea>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-times-circle me-1"></i> Confirmer l\'annulation',
            cancelButtonText: 'Retour',
            confirmButtonColor: '#dc2626',
            focusConfirm: false,
            didOpen: () => {
                const inp = document.getElementById('swal-cancel-reason-input');
                if (inp) {
                    inp.value = existingReason || '';
                    inp.focus();
                }
                document.querySelectorAll('.swal-cancel-preset').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (inp) {
                            inp.value = btn.dataset.reason || '';
                            inp.focus();
                        }
                    });
                });
            },
            preConfirm: () => {
                const inp = document.getElementById('swal-cancel-reason-input');
                const val = String(inp ? inp.value : '').trim();
                if (!val) {
                    Swal.showValidationMessage("Veuillez indiquer la raison d'annulation.");
                    return false;
                }
                return val;
            }
        });

        return res.isConfirmed ? res.value : null;
    }

    async function promptPartialPaymentDetails(orderTotal, currentPaid = 0, currentDueDate = '') {
        const total = Math.max(0, Math.round((parseFloat(orderTotal) || 0) * 100) / 100);
        const existingPaid = Math.min(total, Math.max(0, Math.round((parseFloat(currentPaid) || 0) * 100) / 100));
        const defaultPaid = existingPaid > 0 && existingPaid < total
            ? existingPaid
            : Math.round((total * 0.5) * 100) / 100;

        if (typeof Swal === 'undefined') {
            const raw = window.prompt(`Montant déjà payé en DH (Total : ${total.toFixed(2)} DH) :`, String(defaultPaid));
            if (raw === null) return null;
            const parsed = parseFloat(String(raw).replace(',', '.'));
            if (Number.isNaN(parsed) || parsed < 0 || parsed > total) return null;
            return { paid_amount: Math.round(parsed * 100) / 100, payment_due_date: currentDueDate || '' };
        }

        const pctButtons = [25, 50, 75].map(pct => {
            const amt = Math.round((total * (pct / 100)) * 100) / 100;
            return `<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold swal-partial-pct" data-amount="${amt}" style="font-size:0.75rem;">${pct}% (${amt.toFixed(2)} DH)</button>`;
        }).join('');

        const res = await Swal.fire({
            title: 'Paiement Partiel',
            html: `
                <div class="text-muted small mb-2">Total commande : <strong class="text-dark">${total.toFixed(2)} DH</strong></div>
                <div class="mb-2 text-start">
                    <label class="form-label small fw-semibold mb-1">Montant payé (DH)</label>
                    <div class="input-group">
                        <input type="number" id="swal-partial-paid-input" class="form-control fw-bold" step="0.01" min="0" max="${total}" value="${defaultPaid.toFixed(2)}">
                        <span class="input-group-text fw-bold bg-light">DH</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-1.5 mb-2.5">${pctButtons}</div>
                <div class="small fw-semibold mb-3 text-danger" id="swal-partial-remaining-preview">
                    Reste à payer : ${Math.max(0, total - defaultPaid).toFixed(2)} DH
                </div>
                <div class="text-start">
                    <label class="form-label small fw-semibold mb-1">Date d'échéance (optionnel)</label>
                    <input type="date" id="swal-partial-due-input" class="form-control form-control-sm" value="${currentDueDate || ''}">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check me-1"></i> Enregistrer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#2563eb',
            focusConfirm: false,
            didOpen: () => {
                const inp = document.getElementById('swal-partial-paid-input');
                const remEl = document.getElementById('swal-partial-remaining-preview');
                const updateRem = () => {
                    const v = parseFloat(String(inp?.value || '0').replace(',', '.')) || 0;
                    const rem = Math.max(0, total - v);
                    if (remEl) remEl.textContent = `Reste à payer : ${rem.toFixed(2)} DH`;
                };
                if (inp) {
                    inp.focus();
                    inp.select();
                    inp.addEventListener('input', updateRem);
                }
                document.querySelectorAll('.swal-partial-pct').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (inp) {
                            inp.value = btn.dataset.amount || '0';
                            updateRem();
                            inp.focus();
                        }
                    });
                });
            },
            preConfirm: () => {
                const inp = document.getElementById('swal-partial-paid-input');
                const dueInp = document.getElementById('swal-partial-due-input');
                const val = parseFloat(String(inp ? inp.value : '').replace(',', '.'));
                if (Number.isNaN(val) || val < 0 || val > total) {
                    Swal.showValidationMessage(`Veuillez saisir un montant entre 0 et ${total.toFixed(2)} DH.`);
                    return false;
                }
                if (total > 0 && val >= total) {
                    Swal.showValidationMessage("Pour un paiement total, choisissez plutôt « Payé ».");
                    return false;
                }
                return {
                    paid_amount: Math.round(val * 100) / 100,
                    payment_due_date: dueInp ? dueInp.value : ''
                };
            }
        });

        return res.isConfirmed ? res.value : null;
    }

    /**
     * Quick Update Order Status, Payment Status, Tax Mode or Delivery Type via AJAX
     */
    async function quickUpdateOrder(orderId, field, value) {
        if (!orderId || !field || !value) return;

        // Auto close dropdown immediately on click
        closeOrderDropdowns(orderId);

        let shippingCostPayload = undefined;
        let cancellationReasonPayload = undefined;
        let partialPaidPayload = undefined;
        let partialDueDatePayload = undefined;
        if (field === 'status' && value === 'cancelled') {
            const sourceStatusPill = document.getElementById(`dt-status-pill-${orderId}`) || document.getElementById(`mob-status-pill-${orderId}`);
            const existingReason = sourceStatusPill?.dataset.cancellationReason || '';
            const chosenReason = await promptCancellationReason(existingReason);
            if (chosenReason === null) {
                return;
            }
            cancellationReasonPayload = chosenReason;
        } else if (field === 'payment_status' && value === 'partial') {
            const sourcePayPill = document.getElementById(`dt-pay-pill-${orderId}`) || document.getElementById(`mob-pay-pill-${orderId}`);
            const total = parseFloat(sourcePayPill?.dataset.total ?? '0') || 0;
            const curPaid = parseFloat(sourcePayPill?.dataset.paidAmount ?? '0') || 0;
            const curDue = sourcePayPill?.dataset.dueDate || '';
            const chosenPartial = await promptPartialPaymentDetails(total, curPaid, curDue);
            if (chosenPartial === null) {
                return;
            }
            partialPaidPayload = chosenPartial.paid_amount;
            partialDueDatePayload = chosenPartial.payment_due_date;
        } else if (field === 'delivery_type') {
            if (value === 'store') {
                shippingCostPayload = 0;
            } else {
                const sourcePill = document.getElementById(`dt-delivery-pill-${orderId}`) || document.getElementById(`mob-delivery-pill-${orderId}`);
                const curShip = parseFloat(sourcePill?.dataset.shippingCost ?? '0') || 0;
                const sugShip = parseFloat(sourcePill?.dataset.suggestedShipping ?? '40') || 40;
                const cityName = sourcePill?.dataset.shippingCity || '';
                const chosenPrice = await promptDeliveryShippingPrice(value, curShip, sugShip, cityName);
                if (chosenPrice === null) {
                    return;
                }
                shippingCostPayload = chosenPrice;
            }
        }

        // Visual loading indicator on triggering pill (using pointerEvents instead of disabled)
        const dtPillId = field === 'status'
            ? `dt-status-pill-${orderId}`
            : (field === 'tax_mode' ? `dt-tax-pill-${orderId}` : (field === 'delivery_type' ? `dt-delivery-pill-${orderId}` : `dt-pay-pill-${orderId}`));
        const mobPillId = field === 'status'
            ? `mob-status-pill-${orderId}`
            : (field === 'tax_mode' ? `mob-tax-pill-${orderId}` : (field === 'delivery_type' ? `mob-delivery-pill-${orderId}` : `mob-pay-pill-${orderId}`));
        const dtPill = document.getElementById(dtPillId);
        const mobPill = document.getElementById(mobPillId);
        
        if (dtPill) {
            dtPill.style.opacity = '0.6';
            dtPill.style.pointerEvents = 'none';
        }
        if (mobPill) {
            mobPill.style.opacity = '0.6';
            mobPill.style.pointerEvents = 'none';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            const urlParams = new URLSearchParams(window.location.search);
            const bodyPayload = {
                [field]: value,
                channel: '{{ $channel ?? 'website' }}',
                filter_tax_mode: urlParams.get('tax_mode') || '',
                filter_delivery_type: urlParams.get('delivery_type') || '',
                filter_staff_id: urlParams.get('staff_id') || '',
                date_from: urlParams.get('date_from') || '{{ $stats['date_from'] ?? '' }}',
                date_to: urlParams.get('date_to') || '{{ $stats['date_to'] ?? '' }}',
                date_preset: urlParams.get('date_preset') || ''
            };
            if (shippingCostPayload !== undefined) {
                bodyPayload.shipping_cost = shippingCostPayload;
            }
            if (cancellationReasonPayload !== undefined) {
                bodyPayload.cancellation_reason = cancellationReasonPayload;
            }
            if (partialPaidPayload !== undefined) {
                bodyPayload.paid_amount = partialPaidPayload;
                bodyPayload.payment_due_date = partialDueDatePayload || null;
            }

            fetch(`/orders/${orderId}/quick-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(bodyPayload)
            })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.order) {
                // Update Order row and mobile card UI
                updateOrderUI(orderId, data.order);

                // Update KPI metrics counters live
                if (data.stats) {
                    updateKPIStats(data.stats);
                }

                // Modern Toast Notification
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Mise à jour effectuée avec succès'
                    });
                }
            } else {
                throw new Error(data.message || 'Erreur lors de la mise à jour');
            }
        })
        .catch(error => {
            console.error('Quick update error:', error);
            const msg = error.message || (error.errors ? Object.values(error.errors).flat().join('<br>') : 'Impossible de modifier le statut');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    html: msg,
                    confirmButtonColor: '#dc2626'
                });
            } else {
                alert(msg);
            }
        })
        .finally(() => {
            if (dtPill) {
                dtPill.style.opacity = '1';
                dtPill.style.pointerEvents = '';
            }
            if (mobPill) {
                mobPill.style.opacity = '1';
                mobPill.style.pointerEvents = '';
            }
        });
    }

    /**
     * Dynamically update badges, icons, labels, and dropdown states for both desktop and mobile
     */
    function updateOrderUI(orderId, order) {
        // --- Delivery Status ---
        const st = (order.status || '').toLowerCase();
        const stMap = {
            'pending': { label: 'En attente', dot: 'amber', badgeClass: 'warning status-pill-pending', textClass: 'text-warning', mobBg: '#d97706', mobColor: '#ffffff', mobBorder: '#b45309' },
            'en_demande': { label: 'Sur Commande', dot: 'indigo', badgeClass: 'indigo status-pill-en_demande', textClass: 'text-primary', mobBg: '#c61aff', mobColor: '#ffffff', mobBorder: '#a800e0' },
            'reserved': { label: 'Réservé', dot: 'purple', badgeClass: 'purple status-pill-reserved', textClass: 'text-purple', mobBg: '#7c3aed', mobColor: '#ffffff', mobBorder: '#6d28d9' },
            'processing': { label: 'En cours', dot: 'blue', badgeClass: 'primary status-pill-processing', textClass: 'text-primary', mobBg: '#2563eb', mobColor: '#ffffff', mobBorder: '#1d4ed8' },
            'shipped': { label: 'Expédiée', dot: 'teal', badgeClass: 'info status-pill-shipped', textClass: 'text-info', mobBg: '#0093ac', mobColor: '#ffffff', mobBorder: '#0e7490' },
            'delivered': { label: 'Livrée', dot: 'green', badgeClass: 'success status-pill-delivered', textClass: 'text-success', mobBg: '#059669', mobColor: '#ffffff', mobBorder: '#047857' },
            'cancelled': { label: 'Annulée', dot: 'red', badgeClass: 'danger status-pill-cancelled', textClass: 'text-danger', mobBg: '#dc2626', mobColor: '#ffffff', mobBorder: '#b91c1c' }
        };
        const sInfo = stMap[st] || { label: order.status_label || st, dot: 'amber', badgeClass: 'warning status-pill-pending', textClass: 'text-warning', mobBg: '#d97706', mobColor: '#ffffff', mobBorder: '#b45309' };

        // Desktop Delivery Badge
        const dtStatusPill = document.getElementById(`dt-status-pill-${orderId}`);
        if (dtStatusPill) {
            dtStatusPill.dataset.cancellationReason = order.cancellation_reason || '';
            dtStatusPill.className = `brand-badge brand-badge-btn ${sInfo.badgeClass} dropdown-toggle`;
            const dtLabel = document.getElementById(`dt-status-label-${orderId}`);
            if (dtLabel) dtLabel.textContent = sInfo.label;
            const dtDot = document.getElementById(`dt-status-dot-${orderId}`);
            if (dtDot) dtDot.className = `status-dot dot-${sInfo.dot}`;

            const dtMenu = dtStatusPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (dtMenu) {
                dtMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const fn = item.getAttribute('onclick') || '';
                    const isSelected = fn.includes(`'${st}'`);
                    item.classList.toggle('active', isSelected);
                    const check = item.querySelector('.fa-check');
                    if (check) check.remove();
                    if (isSelected) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${sInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // Mobile Order Card Border Color
        const mobCard = document.getElementById(`mobile-order-card-${orderId}`);
        if (mobCard) {
            const normalizedStMap = {
                'pending': 'pending',
                'en_demande': 'en_demande',
                'reserved': 'reserved',
                'réservé': 'reserved',
                'processing': 'processing',
                'en cours': 'processing',
                'shipped': 'shipped',
                'expédiée': 'shipped',
                'delivered': 'delivered',
                'livrée': 'delivered',
                'cancelled': 'cancelled',
                'annulée': 'cancelled'
            };
            const normalizedSt = normalizedStMap[st] || 'pending';
            mobCard.classList.remove(
                'mobile-order-status-pending',
                'mobile-order-status-en_demande',
                'mobile-order-status-reserved',
                'mobile-order-status-processing',
                'mobile-order-status-shipped',
                'mobile-order-status-delivered',
                'mobile-order-status-cancelled'
            );
            mobCard.classList.add(`mobile-order-status-${normalizedSt}`);
        }

        // Mobile Delivery Badge
        const mobStatusPill = document.getElementById(`mob-status-pill-${orderId}`);
        if (mobStatusPill) {
            mobStatusPill.dataset.cancellationReason = order.cancellation_reason || '';
            mobStatusPill.style.background = sInfo.mobBg;
            mobStatusPill.style.color = sInfo.mobColor;
            mobStatusPill.style.setProperty('border', `1px solid ${sInfo.mobBorder}`, 'important');
            const mobLabel = document.getElementById(`mob-status-label-${orderId}`);
            if (mobLabel) mobLabel.textContent = sInfo.label;
            const mobDot = document.getElementById(`mob-status-dot-${orderId}`);
            if (mobDot) mobDot.className = `status-dot dot-${sInfo.dot}`;

            const mobMenu = mobStatusPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (mobMenu) {
                mobMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const fn = item.getAttribute('onclick') || '';
                    const isSelected = fn.includes(`'${st}'`);
                    item.classList.toggle('active', isSelected);
                    const check = item.querySelector('.fa-check');
                    if (check) check.remove();
                    if (isSelected) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${sInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // --- Cancellation Reason Badge / Banner ---
        const cancelReason = (order.cancellation_reason || '').trim();
        const showCancelReason = st === 'cancelled' && cancelReason !== '';

        const dtCancelWrap = document.getElementById(`dt-cancel-reason-wrap-${orderId}`);
        const dtCancelText = document.getElementById(`dt-cancel-reason-text-${orderId}`);
        if (dtCancelWrap && dtCancelText) {
            dtCancelWrap.classList.toggle('d-none', !showCancelReason);
            if (showCancelReason) {
                const truncated = cancelReason.length > 55 ? `${cancelReason.slice(0, 52)}...` : cancelReason;
                dtCancelText.title = cancelReason;
                dtCancelText.innerHTML = '';
                const ic = document.createElement('i');
                ic.className = 'fas fa-comment-slash me-1';
                dtCancelText.appendChild(ic);
                dtCancelText.appendChild(document.createTextNode(truncated));
            }
        }

        const mobCancelWrap = document.getElementById(`mob-cancel-reason-wrap-${orderId}`);
        const mobCancelText = document.getElementById(`mob-cancel-reason-text-${orderId}`);
        if (mobCancelWrap && mobCancelText) {
            mobCancelWrap.classList.toggle('d-none', !showCancelReason);
            if (showCancelReason) {
                mobCancelText.textContent = cancelReason;
            }
        }

        // --- Payment Status ---
        const pst = (order.payment_status || '').toLowerCase();
        const pstMap = {
            'paid': { label: 'Payé', icon: 'fa-check-circle', badgeClass: 'paid', textClass: 'text-success', mobClass: 'paid' },
            'partial': { label: 'Partiel', icon: 'fa-circle-half-stroke', badgeClass: 'partial', textClass: 'text-primary', mobClass: 'partial' },
            'pending': { label: 'En attente', icon: 'fa-clock', badgeClass: 'pending', textClass: 'text-warning', mobClass: 'pending' },
            'failed': { label: 'Échoué', icon: 'fa-exclamation-circle', badgeClass: 'failed', textClass: 'text-danger', mobClass: 'failed' },
            'refunded': { label: 'Remboursé', icon: 'fa-undo', badgeClass: 'refunded', textClass: 'text-secondary', mobClass: 'refunded' }
        };
        const pInfo = pstMap[pst] || { label: order.payment_status_label || pst, icon: 'fa-clock', badgeClass: 'pending', textClass: 'text-warning', mobClass: 'pending' };

        // Desktop Payment Badge (Interactive dropdown pill)
        const dtPayPill = document.getElementById(`dt-pay-pill-${orderId}`);
        if (dtPayPill) {
            dtPayPill.className = `brand-badge brand-badge-btn pay-badge-${pInfo.badgeClass} py-1 px-2.5 dropdown-toggle`;
            if (order.total !== undefined) dtPayPill.dataset.total = String(order.total);
            if (order.paid_amount !== undefined) dtPayPill.dataset.paidAmount = String(order.paid_amount);
            dtPayPill.dataset.dueDate = order.payment_due_date || '';
            const dtPayLabel = document.getElementById(`dt-pay-label-${orderId}`);
            if (dtPayLabel) dtPayLabel.textContent = pInfo.label;
            const dtPayIcon = document.getElementById(`dt-pay-icon-${orderId}`);
            if (dtPayIcon) dtPayIcon.className = `fas ${pInfo.icon}`;

            const dtPayMenu = dtPayPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (dtPayMenu) {
                dtPayMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const isSel = item.dataset.paymentStatus === pst;
                    item.classList.toggle('active', isSel);
                    const chk = item.querySelector('.fa-check');
                    if (chk) chk.remove();
                    if (isSel) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${pInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // Mobile Payment Pill (Interactive dropdown pill)
        const mobPayPill = document.getElementById(`mob-pay-pill-${orderId}`);
        if (mobPayPill) {
            mobPayPill.className = `mobile-order-pay-pill ${pInfo.mobClass} dropdown-toggle`;
            if (order.total !== undefined) mobPayPill.dataset.total = String(order.total);
            if (order.paid_amount !== undefined) mobPayPill.dataset.paidAmount = String(order.paid_amount);
            mobPayPill.dataset.dueDate = order.payment_due_date || '';
            const mobPayLabel = document.getElementById(`mob-pay-label-${orderId}`);
            if (mobPayLabel) mobPayLabel.textContent = pInfo.label;
            const mobPayIcon = document.getElementById(`mob-pay-icon-${orderId}`);
            if (mobPayIcon) mobPayIcon.className = `fas ${pInfo.icon}`;

            const mobPayMenu = mobPayPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (mobPayMenu) {
                mobPayMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const isSel = item.dataset.paymentStatus === pst;
                    item.classList.toggle('active', isSel);
                    const chk = item.querySelector('.fa-check');
                    if (chk) chk.remove();
                    if (isSel) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${pInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // --- TTC / HT Fiscal Tag ---
        if (order.tax_mode) {
            const tm = (order.tax_mode || 'ht').toLowerCase() === 'ttc' ? 'ttc' : 'ht';
            const tmLabel = tm === 'ht' ? 'HT' : 'TTC';
            const tmCheckClass = tm === 'ht' ? 'text-warning' : 'text-primary';

            const dtTaxPill = document.getElementById(`dt-tax-pill-${orderId}`);
            if (dtTaxPill) {
                dtTaxPill.className = `tax-mode-pill tax-mode-${tm} dropdown-toggle`;
                const dtTaxLabel = document.getElementById(`dt-tax-label-${orderId}`);
                if (dtTaxLabel) dtTaxLabel.textContent = tmLabel;
                const dtTaxMenu = dtTaxPill.closest('.dropdown')?.querySelector('.dropdown-menu');
                if (dtTaxMenu) {
                    dtTaxMenu.querySelectorAll('.dropdown-item').forEach(item => {
                        const isSel = item.dataset.taxMode === tm;
                        item.classList.toggle('active', isSel);
                        const chk = item.querySelector('.fa-check');
                        if (chk) chk.remove();
                        if (isSel) {
                            item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${tmCheckClass} small"></i>`);
                        }
                    });
                }
            }

            const mobTaxPill = document.getElementById(`mob-tax-pill-${orderId}`);
            if (mobTaxPill) {
                mobTaxPill.className = `tax-mode-pill tax-mode-${tm} dropdown-toggle`;
                const mobTaxLabel = document.getElementById(`mob-tax-label-${orderId}`);
                if (mobTaxLabel) mobTaxLabel.textContent = tmLabel;
                const mobTaxMenu = mobTaxPill.closest('.dropdown')?.querySelector('.dropdown-menu');
                if (mobTaxMenu) {
                    mobTaxMenu.querySelectorAll('.dropdown-item').forEach(item => {
                        const isSel = item.dataset.taxMode === tm;
                        item.classList.toggle('active', isSel);
                        const chk = item.querySelector('.fa-check');
                        if (chk) chk.remove();
                        if (isSel) {
                            item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${tmCheckClass} small"></i>`);
                        }
                    });
                }
            }
        }

        // --- Delivery Type Tag (Store / Livreur / Ozon / Autre) ---
        if (order.delivery_type) {
            const delivMetaMap = {
                store: { label: 'Store', icon: 'fa-store', checkClass: 'text-danger' },
                our_team: { label: 'Livreur', icon: 'fa-people-carry-box', checkClass: 'text-primary' },
                ozon: { label: 'Ozon', icon: 'fa-box-open', checkClass: 'text-warning' },
                autre: { label: 'Autre', icon: 'fa-truck-arrow-right', checkClass: 'text-purple' }
            };
            const dt = delivMetaMap[order.delivery_type] ? order.delivery_type : 'ozon';
            const dInfo = delivMetaMap[dt];

            [
                { pill: `dt-delivery-pill-${orderId}`, icon: `dt-delivery-icon-${orderId}`, label: `dt-delivery-label-${orderId}` },
                { pill: `mob-delivery-pill-${orderId}`, icon: `mob-delivery-icon-${orderId}`, label: `mob-delivery-label-${orderId}` }
            ].forEach(cfg => {
                const pillEl = document.getElementById(cfg.pill);
                if (!pillEl) return;
                pillEl.className = `delivery-type-pill delivery-type-${dt} dropdown-toggle`;
                if (order.shipping_cost !== undefined) {
                    pillEl.dataset.shippingCost = String(order.shipping_cost);
                }
                const iconEl = document.getElementById(cfg.icon);
                if (iconEl) iconEl.className = `fas ${dInfo.icon}`;
                const lblEl = document.getElementById(cfg.label);
                if (lblEl) lblEl.textContent = order.delivery_type_label || dInfo.label;
                const menuEl = pillEl.closest('.dropdown')?.querySelector('.dropdown-menu');
                if (menuEl) {
                    menuEl.querySelectorAll('.dropdown-item').forEach(item => {
                        const isSel = item.dataset.deliveryType === dt;
                        item.classList.toggle('active', isSel);
                        const chk = item.querySelector('.fa-check');
                        if (chk) chk.remove();
                        if (isSel) {
                            item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${dInfo.checkClass} small"></i>`);
                        }
                    });
                }
            });
        }

        // --- Order Total & Remaining Amount (updated when shipping_cost changes) ---
        if (order.total_formatted) {
            const dtTotalEl = document.getElementById(`dt-order-total-${orderId}`);
            if (dtTotalEl) dtTotalEl.textContent = order.total_formatted;
            const mobTotalEl = document.getElementById(`mob-order-total-${orderId}`);
            if (mobTotalEl) mobTotalEl.textContent = order.total_formatted;
        }
        if (order.remaining_amount !== undefined) {
            const dtRemWrap = document.getElementById(`dt-order-remaining-wrap-${orderId}`);
            const dtRemEl = document.getElementById(`dt-order-remaining-${orderId}`);
            if (dtRemEl && order.remaining_amount_formatted) {
                dtRemEl.textContent = `Reste : ${order.remaining_amount_formatted}`;
            }
            if (dtRemWrap) {
                dtRemWrap.classList.toggle('d-none', Number(order.remaining_amount) <= 0);
            }
            const dtDueEl = document.getElementById(`dt-order-due-${orderId}`);
            if (dtDueEl) {
                if (order.payment_due_date_formatted && Number(order.remaining_amount) > 0) {
                    dtDueEl.textContent = `Éch. ${order.payment_due_date_formatted}`;
                    dtDueEl.classList.remove('d-none');
                } else {
                    dtDueEl.textContent = '';
                    dtDueEl.classList.add('d-none');
                }
            }
            const mobRemWrap = document.getElementById(`mob-order-remaining-wrap-${orderId}`);
            const mobRemEl = document.getElementById(`mob-order-remaining-${orderId}`);
            if (mobRemEl && order.remaining_amount_formatted) {
                mobRemEl.textContent = `Reste : ${order.remaining_amount_formatted}`;
            }
            if (mobRemWrap) {
                mobRemWrap.classList.toggle('d-none', Number(order.remaining_amount) <= 0);
            }
        }

        // --- Staff Confirmation Attribution (First user moving off pending) ---
        if (order.confirmed_by_name) {
            const dtConf = document.getElementById(`dt-confirmed-by-${orderId}`);
            const dtConfName = document.getElementById(`dt-confirmed-name-${orderId}`);
            if (dtConf && dtConfName) {
                dtConfName.textContent = order.confirmed_by_name;
                dtConf.title = `Confirmée par ${order.confirmed_by_name}${order.confirmed_at_formatted ? ' le ' + order.confirmed_at_formatted : ''}`;
                dtConf.classList.remove('d-none');
            }
            const mobStaffRow = document.getElementById(`mob-staff-row-${orderId}`);
            if (mobStaffRow) {
                mobStaffRow.classList.remove('d-none');
            }
            const mobConf = document.getElementById(`mob-confirmed-by-${orderId}`);
            const mobConfName = document.getElementById(`mob-confirmed-name-${orderId}`);
            if (mobConf && mobConfName) {
                mobConfName.textContent = `Confirmée : ${order.confirmed_by_name}`;
                mobConf.classList.remove('d-none');
            }
        }
    }

    /**
     * Update real-time KPI metrics for all delivery statuses
     */
    function updateKPIStats(stats) {
        if (!stats) return;
        const total = stats.total !== undefined ? Number(stats.total) : null;

        if (total !== null) {
            const el = document.getElementById('kpi-total-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(total);
            const elCard = document.getElementById('kpi-all-orders-card');
            if (elCard) elCard.textContent = new Intl.NumberFormat('fr-FR').format(total);
        }

        const calcPct = (val) => {
            if (!total || total <= 0 || val === undefined || val === null) return '0%';
            const pct = Math.round((Number(val) / total) * 100 * 10) / 10;
            return pct + '%';
        };

        if (stats.pending !== undefined) {
            const el = document.getElementById('kpi-pending-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.pending);
            const pctEl = document.getElementById('kpi-pending-pct');
            if (pctEl) pctEl.textContent = calcPct(stats.pending);
        }
        if (stats.en_demande !== undefined) {
            const el = document.getElementById('kpi-en-demande-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.en_demande);
            const pctEl = document.getElementById('kpi-en-demande-pct');
            if (pctEl) pctEl.textContent = calcPct(stats.en_demande);
        }
        if (stats.reserved !== undefined) {
            const el = document.getElementById('kpi-reserved-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.reserved);
            const pctEl = document.getElementById('kpi-reserved-pct');
            if (pctEl) pctEl.textContent = calcPct(stats.reserved);
        }
        if (stats.processing !== undefined) {
            const el = document.getElementById('kpi-processing-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.processing);
            const pctEl = document.getElementById('kpi-processing-pct');
            if (pctEl) pctEl.textContent = calcPct(stats.processing);
        }
        if (stats.shipped !== undefined) {
            const el = document.getElementById('kpi-shipped-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.shipped);
            const pctEl = document.getElementById('kpi-shipped-pct');
            if (pctEl) pctEl.textContent = calcPct(stats.shipped);
        }
        if (stats.delivered !== undefined) {
            const el = document.getElementById('kpi-delivered-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.delivered);
            const pctEl = document.getElementById('kpi-delivered-pct');
            if (pctEl) pctEl.textContent = calcPct(stats.delivered);
        }
        if (stats.cancelled !== undefined) {
            const el = document.getElementById('kpi-cancelled-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.cancelled);
            const pctEl = document.getElementById('kpi-cancelled-pct');
            if (pctEl) pctEl.textContent = calcPct(stats.cancelled);
        }
        if (stats.revenue_formatted !== undefined) {
            const el = document.getElementById('kpi-revenue-orders');
            if (el) el.textContent = stats.revenue_formatted;
        }
    }

    /**
     * Initialize dropdowns with fixed Popper strategy so they never get clipped by table bounds
     */
    function initStatusDropdowns() {
        if (typeof bootstrap === 'undefined' || !bootstrap.Dropdown) return;
        const triggers = document.querySelectorAll('.brand-badge-btn[data-bs-toggle="dropdown"], .tax-mode-pill[data-bs-toggle="dropdown"], .mobile-order-badge[data-bs-toggle="dropdown"], .mobile-order-pay-pill[data-bs-toggle="dropdown"]');
        triggers.forEach(el => {
            bootstrap.Dropdown.getOrCreateInstance(el, {
                autoClose: true,
                popperConfig: function(defaultBsPopperConfig) {
                    return {
                        ...defaultBsPopperConfig,
                        strategy: 'fixed',
                        modifiers: [
                            ...(defaultBsPopperConfig?.modifiers || []),
                            {
                                name: 'preventOverflow',
                                options: {
                                    boundary: 'viewport',
                                    padding: 8
                                }
                            },
                            {
                                name: 'flip',
                                options: {
                                    fallbackPlacements: ['top', 'bottom']
                                }
                            }
                        ]
                    };
                }
            });
        });
    }

    // Auto-close any status dropdown as soon as an item inside is clicked
    document.addEventListener('click', function(e) {
        const item = e.target.closest('.status-dropdown-menu .dropdown-item');
        if (item) {
            const menu = item.closest('.dropdown-menu');
            if (menu) {
                menu.classList.remove('show');
                const toggle = menu.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]');
                if (toggle) {
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                        const inst = bootstrap.Dropdown.getInstance(toggle);
                        if (inst) inst.hide();
                    }
                }
            }
        }
    });

    function syncOrderStatusFilterColor() {
        const sel = document.getElementById('orderStatusFilterSelect');
        if (!sel) return;
        const themeMap = {
            'pending':    { bg: '#d97706', border: '#b45309', color: '#ffffff' },
            'en_demande': { bg: '#c61aff', border: '#a800e0', color: '#ffffff' },
            'reserved':   { bg: '#7c3aed', border: '#6d28d9', color: '#ffffff' },
            'processing': { bg: '#2563eb', border: '#1d4ed8', color: '#ffffff' },
            'shipped':    { bg: '#0093ac', border: '#0e7490', color: '#ffffff' },
            'delivered':  { bg: '#059669', border: '#047857', color: '#ffffff' },
            'cancelled':  { bg: '#dc2626', border: '#b91c1c', color: '#ffffff' }
        };
        const theme = themeMap[sel.value] || null;
        if (theme) {
            sel.style.backgroundColor = theme.bg;
            sel.style.borderColor = theme.border;
            sel.style.color = theme.color;
        } else {
            sel.style.removeProperty('background-color');
            sel.style.removeProperty('border-color');
            sel.style.removeProperty('color');
        }
    }

    (function () {
        function initOrdersIndexView() {
            initStatusDropdowns();
            const statusFilterSel = document.getElementById('orderStatusFilterSelect');
            if (statusFilterSel) {
                syncOrderStatusFilterColor();
                statusFilterSel.addEventListener('change', syncOrderStatusFilterColor);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initOrdersIndexView);
        } else {
            initOrdersIndexView();
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        initStatusDropdowns();
        syncOrderStatusFilterColor();

        // Auto-handle Date Preset Selection
        const datePresetSelect = document.getElementById('orderDatePreset');
        const fromInput = document.getElementById('orderDateFrom');
        const toInput = document.getElementById('orderDateTo');
        const form = document.getElementById('orderFilterForm');

        if (datePresetSelect && form) {
            datePresetSelect.addEventListener('change', function() {
                const val = this.value;
                const today = new Date();
                const pad = (n) => String(n).padStart(2, '0');
                const formatDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

                if (val === 'today') {
                    const todayStr = formatDate(today);
                    if (fromInput) fromInput.value = todayStr;
                    if (toInput) toInput.value = todayStr;
                    form.submit();
                } else if (val === 'yesterday') {
                    const y = new Date(today);
                    y.setDate(y.getDate() - 1);
                    const yStr = formatDate(y);
                    if (fromInput) fromInput.value = yStr;
                    if (toInput) toInput.value = yStr;
                    form.submit();
                } else if (val === 'this_week') {
                    const curr = new Date(today);
                    const day = curr.getDay();
                    const diff = curr.getDate() - (day === 0 ? 6 : day - 1); // Monday
                    const firstDay = new Date(curr.setDate(diff));
                    const lastDay = new Date(firstDay);
                    lastDay.setDate(lastDay.getDate() + 6);
                    if (fromInput) fromInput.value = formatDate(firstDay);
                    if (toInput) toInput.value = formatDate(lastDay);
                    form.submit();
                } else if (val === 'this_month') {
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    if (fromInput) fromInput.value = formatDate(firstDay);
                    if (toInput) toInput.value = formatDate(lastDay);
                    form.submit();
                } else if (val === 'last_month') {
                    const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                    if (fromInput) fromInput.value = formatDate(firstDay);
                    if (toInput) toInput.value = formatDate(lastDay);
                    form.submit();
                } else if (val === '') {
                    if (fromInput) fromInput.value = '';
                    if (toInput) toInput.value = '';
                    form.submit();
                }
            });
        }
    });
</script>
@endpush
