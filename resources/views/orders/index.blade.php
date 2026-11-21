@push('styles')
<style>
    /* Professional Order KPI Cards Design */
    .order-kpi-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #ffffff;
        border-radius: 14px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        overflow: hidden;
        min-height: 98px;
    }
    .order-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--kpi-accent, #3b82f6);
        opacity: 0.85;
        transition: height 0.2s ease, opacity 0.2s ease;
    }
    .order-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        border-color: var(--kpi-border, #cbd5e1);
    }
    .order-kpi-card:hover::before {
        height: 4px;
        opacity: 1;
    }
    .order-kpi-card.active-filter {
        border-color: var(--kpi-accent, #2563eb) !important;
        background: var(--kpi-active-bg, #f8fafc) !important;
        box-shadow: 0 0 0 2px var(--kpi-ring, rgba(37, 99, 235, 0.18)), 0 6px 16px -2px rgba(15, 23, 42, 0.08) !important;
    }
    .order-kpi-card.active-filter::before {
        height: 4px;
        opacity: 1;
    }
    .order-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .order-kpi-label {
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        color: #64748b;
        text-transform: capitalize;
        line-height: 1.2;
    }
    .order-kpi-badge {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 9999px;
        line-height: 1.2;
        letter-spacing: 0.02em;
    }
    .order-kpi-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .order-kpi-value {
        font-family: var(--font-heading, 'Outfit', sans-serif);
        font-size: 1.55rem;
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.02em;
        color: #0f172a;
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
        color: #94a3b8;
        margin-top: 6px;
        line-height: 1.2;
    }

    /* Status Color Themes */
    .kpi-theme-all {
        --kpi-accent: #2563eb;
        --kpi-border: #bfdbfe;
        --kpi-active-bg: #eff6ff;
        --kpi-ring: rgba(37, 99, 235, 0.25);
    }
    .kpi-theme-all .order-kpi-icon {
        background: #eff6ff;
        color: #2563eb;
    }
    .kpi-theme-all .order-kpi-badge {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .kpi-theme-pending {
        --kpi-accent: #d97706;
        --kpi-border: #fde68a;
        --kpi-active-bg: #fffbeb;
        --kpi-ring: rgba(217, 119, 6, 0.25);
    }
    .kpi-theme-pending .order-kpi-icon {
        background: #fef3c7;
        color: #d97706;
    }
    .kpi-theme-pending .order-kpi-badge {
        background: #fef3c7;
        color: #b45309;
    }
    .kpi-theme-pending .order-kpi-value {
        color: #b45309;
    }

    .kpi-theme-processing {
        --kpi-accent: #0284c7;
        --kpi-border: #bae6fd;
        --kpi-active-bg: #f0f9ff;
        --kpi-ring: rgba(2, 132, 199, 0.25);
    }
    .kpi-theme-processing .order-kpi-icon {
        background: #e0f2fe;
        color: #0284c7;
    }
    .kpi-theme-processing .order-kpi-badge {
        background: #e0f2fe;
        color: #0369a1;
    }
    .kpi-theme-processing .order-kpi-value {
        color: #0369a1;
    }

    .kpi-theme-shipped {
        --kpi-accent: #6366f1;
        --kpi-border: #ddd6fe;
        --kpi-active-bg: #f5f3ff;
        --kpi-ring: rgba(99, 102, 241, 0.25);
    }
    .kpi-theme-shipped .order-kpi-icon {
        background: #ede9fe;
        color: #6366f1;
    }
    .kpi-theme-shipped .order-kpi-badge {
        background: #ede9fe;
        color: #4f46e5;
    }
    .kpi-theme-shipped .order-kpi-value {
        color: #4f46e5;
    }

    .kpi-theme-delivered {
        --kpi-accent: #10b981;
        --kpi-border: #a7f3d0;
        --kpi-active-bg: #ecfdf5;
        --kpi-ring: rgba(16, 185, 129, 0.25);
    }
    .kpi-theme-delivered .order-kpi-icon {
        background: #d1fae5;
        color: #059669;
    }
    .kpi-theme-delivered .order-kpi-badge {
        background: #d1fae5;
        color: #047857;
    }
    .kpi-theme-delivered .order-kpi-value {
        color: #047857;
    }

    .kpi-theme-cancelled {
        --kpi-accent: #ef4444;
        --kpi-border: #fecaca;
        --kpi-active-bg: #fef2f2;
        --kpi-ring: rgba(239, 68, 68, 0.25);
    }
    .kpi-theme-cancelled .order-kpi-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .kpi-theme-en-demande {
        --kpi-accent: #8b5cf6;
        --kpi-border: #ddd6fe;
        --kpi-active-bg: #f5f3ff;
        --kpi-ring: rgba(139, 92, 246, 0.25);
    }
    .kpi-theme-en-demande .order-kpi-icon {
        background: #ede9fe;
        color: #7c3aed;
    }
    .kpi-theme-en-demande .order-kpi-badge {
        background: #ede9fe;
        color: #6d28d9;
    }
    .kpi-theme-en-demande .order-kpi-value {
        color: #6d28d9;
    }

    .kpi-theme-reserved {
        --kpi-accent: #9333ea;
        --kpi-border: #e9d5ff;
        --kpi-active-bg: #faf5ff;
        --kpi-ring: rgba(147, 51, 234, 0.25);
    }
    .kpi-theme-reserved .order-kpi-icon {
        background: #f3e8ff;
        color: #9333ea;
    }
    .kpi-theme-reserved .order-kpi-badge {
        background: #f3e8ff;
        color: #7e22ce;
    }
    .kpi-theme-reserved .order-kpi-value {
        color: #7e22ce;
    }
    .kpi-theme-cancelled .order-kpi-badge {
        background: #fee2e2;
        color: #b91c1c;
    }
    .kpi-theme-cancelled .order-kpi-value {
        color: #dc2626;
    }

    @media (max-width: 767.98px) {
        .order-kpi-card {
            padding: 9px 11px;
            min-height: 76px;
            border-radius: 12px;
        }
        .order-kpi-value {
            font-size: 1.2rem;
        }
        .order-kpi-icon {
            width: 28px;
            height: 28px;
            font-size: 0.8rem;
            border-radius: 8px;
        }
        .order-kpi-footer {
            display: none;
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
    <!-- En-tête de page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas {{ $isPosChannel ? 'fa-cash-register' : 'fa-shopping-bag' }}"></i>
            </div>
            <div>
                <h1 class="brand-title">{{ $isPosChannel ? 'Commandes POS (Caisse)' : 'Commandes Site Web' }}</h1>
                <p class="brand-subtitle d-flex align-items-center gap-2 flex-wrap mb-0">
                    <span class="d-none d-md-inline">{{ $isPosChannel ? 'Consultez et gérez les ventes effectuées en boutique depuis la caisse POS' : 'Suivez et gérez vos commandes provenant du site web selon leur statut de livraison' }}</span>
                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-receipt text-primary me-1"></i> <strong id="kpi-total-orders">{{ number_format($stats['total'] ?? $orders->total(), 0, ',', ' ') }}</strong> total</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-coins me-1"></i> <strong id="kpi-revenue-orders">{{ currency($stats['revenue'] ?? 0) }}</strong> encaissés</span>
                </p>
            </div>
        </div>
        <div class="brand-header-actions">
            @if($isPosChannel)
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-globe text-primary"></i> <span class="d-none d-sm-inline">Commandes </span><span>Site Web</span>
                </a>
            @else
                <a href="{{ route('orders.pos') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-cash-register text-danger"></i> <span class="d-none d-sm-inline">Commandes </span><span>POS</span>
                </a>
            @endif
            @if(request('status') || request('payment_status') || request('tax_mode') || request('store_id') || request('search'))
                <a href="{{ route($indexRoute) }}" class="btn btn-outline-danger shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-rotate-left"></i> <span>Toutes ({{ number_format($stats['total'] ?? 0, 0, ',', ' ') }})</span>
                </a>
            @endif
            <a href="{{ route('export.orders.pdf', $exportParams) }}" data-no-spa="true" class="btn btn-outline-danger shadow-sm rounded-pill px-3 py-2 d-none d-md-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-pdf text-danger"></i> <span>Exporter PDF</span>
            </a>
            <a href="{{ route('export.orders', $exportParams) }}" data-no-spa="true" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-none d-md-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
            </a>
            @if($isPosChannel)
                <a href="{{ route('pos.index') }}" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3 py-2" style="font-size: 0.84rem;">
                    <i class="fas fa-cash-register me-1"></i> <span class="d-none d-sm-inline">Ouvrir la caisse POS</span><span class="d-sm-none">Caisse</span>
                </a>
            @else
                <a href="{{ route('orders.create') }}" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3 py-2" style="font-size: 0.84rem;">
                    <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">Nouvelle commande</span><span class="d-sm-none">Créer</span>
                </a>
            @endif
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

    <!-- 8 Delivery Status Cards (Single-row swipe rail on Mobile, 4-col KPI grid on Desktop) -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2.5 g-md-3 mb-3 mb-md-4 orders-kpi-grid">
        <!-- 0. Toutes les commandes (All orders) -->
        <div class="col mb-2">
            <a href="{{ route($indexRoute, request()->except(['status', 'page'])) }}" 
               class="order-kpi-card kpi-theme-all h-100 {{ !request('status') ? 'active-filter' : '' }}"
               title="Voir toutes les commandes">
                <div>
                    <div class="order-kpi-top">
                        <span class="order-kpi-label">Total</span>
                        <span class="order-kpi-badge" id="kpi-all-orders-pct">100%</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-all-orders-card">{{ number_format($stats['total'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="order-kpi-icon">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                    </div>
                </div>
                <div class="order-kpi-footer">
                    <i class="fas fa-layer-group"></i> <span>Toutes les commandes</span>
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
                        <span class="order-kpi-label" style="color: #d97706;">En attente</span>
                        <span class="order-kpi-badge" id="kpi-pending-pct">{{ $pendingPct }}%</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-pending-orders">{{ number_format($stats['pending'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="order-kpi-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="order-kpi-footer">
                    <i class="fas fa-hourglass-half"></i> <span>À traiter</span>
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
                        <span class="order-kpi-label" style="color: #6d28d9;">Sur Commande</span>
                        <span class="order-kpi-badge" id="kpi-en-demande-pct">{{ $enDemandePct }}%</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-en-demande-orders">{{ number_format($stats['en_demande'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="order-kpi-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="order-kpi-footer">
                    <i class="fas fa-clock-rotate-left"></i> <span>Approvisionnement</span>
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
                        <span class="order-kpi-label" style="color: #7e22ce;">Réservé</span>
                        <span class="order-kpi-badge" id="kpi-reserved-pct">{{ $reservedPct }}%</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-reserved-orders">{{ number_format($stats['reserved'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="order-kpi-icon">
                            <i class="fas fa-bookmark"></i>
                        </div>
                    </div>
                </div>
                <div class="order-kpi-footer">
                    <i class="fas fa-bookmark"></i> <span>Mise de côté</span>
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
                        <span class="order-kpi-label" style="color: #0284c7;">En cours</span>
                        <span class="order-kpi-badge" id="kpi-processing-pct">{{ $processingPct }}%</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-processing-orders">{{ number_format($stats['processing'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="order-kpi-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                </div>
                <div class="order-kpi-footer">
                    <i class="fas fa-dolly"></i> <span>En préparation</span>
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
                        <span class="order-kpi-label" style="color: #6366f1;">Expédiée</span>
                        <span class="order-kpi-badge" id="kpi-shipped-pct">{{ $shippedPct }}%</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-shipped-orders">{{ number_format($stats['shipped'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="order-kpi-icon">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                    </div>
                </div>
                <div class="order-kpi-footer">
                    <i class="fas fa-route"></i> <span>En livraison</span>
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
                        <span class="order-kpi-label" style="color: #059669;">Livrée</span>
                        <span class="order-kpi-badge" id="kpi-delivered-pct">{{ $deliveredPct }}%</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-delivered-orders">{{ number_format($stats['delivered'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="order-kpi-icon">
                            <i class="fas fa-circle-check"></i>
                        </div>
                    </div>
                </div>
                <div class="order-kpi-footer">
                    <i class="fas fa-clipboard-check"></i> <span>Finalisée</span>
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
                        <span class="order-kpi-label" style="color: #dc2626;">Annulée</span>
                        <span class="order-kpi-badge" id="kpi-cancelled-pct">{{ $cancelledPct }}%</span>
                    </div>
                    <div class="order-kpi-body">
                        <div class="order-kpi-value" id="kpi-cancelled-orders">{{ number_format($stats['cancelled'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="order-kpi-icon">
                            <i class="fas fa-circle-xmark"></i>
                        </div>
                    </div>
                </div>
                <div class="order-kpi-footer">
                    <i class="fas fa-ban"></i> <span>Non aboutie</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route($indexRoute) }}" id="orderFilterForm">
            <!-- Row 1 : Champ Recherche + Bouton Toggle Mobile + Actions Desktop -->
            <div class="d-flex align-items-center gap-2 flex-nowrap flex-md-wrap">
                <div class="brand-search-wrapper flex-grow-1 min-w-0" style="min-width: 140px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Client, téléphone, ville, email, N° commande..."
                           value="{{ request('search') }}">
                </div>

                @php
                    $hasDateFilter = request('date_preset') || request('date_from') || request('date_to') || request('date');
                    $hasActiveFilters = request('status') || request('payment_status') || request('tax_mode') || request('store_id') || $hasDateFilter;
                    $activeFilterCount = collect(['status', 'payment_status', 'tax_mode', 'store_id', 'date_preset', 'date_from', 'date_to', 'date'])->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile (d-md-none) -->
                <button type="button" 
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="collapse" 
                        data-bs-target="#orderFiltersCollapse" 
                        aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filtres</span>
                    @if($activeFilterCount > 0)
                        <span class="badge bg-white text-dark rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $activeFilterCount }}</span>
                    @endif
                </button>

                <!-- Actions Desktop (Compteur de résultats) (d-none d-md-flex) -->
                <div class="ms-md-auto d-none d-md-flex align-items-center gap-2">
                    @if(request('search') || $hasActiveFilters)
                        <span class="badge bg-light text-secondary border px-2.5 py-2">
                            {{ $orders->total() }} résultat(s)
                        </span>
                    @endif
                    <a href="{{ route('export.orders.pdf', $exportParams) }}" data-no-spa="true" class="btn-brand-outline border-danger text-danger">
                        <i class="fas fa-file-pdf me-1.5"></i>Exporter PDF
                    </a>
                    <a href="{{ route('export.orders', $exportParams) }}" data-no-spa="true" class="btn-brand-outline">
                        <i class="fas fa-file-csv me-1.5" style="color: var(--success-color)"></i>Exporter CSV
                    </a>
                </div>
            </div>

            <!-- Row 2 : Filtres Déroulants Uniques (Toujours visibles sur Desktop, repliables sur Mobile) -->
            <div class="collapse d-md-block {{ $hasActiveFilters ? 'show' : '' }} mt-3 pt-3 pt-md-2 border-top border-top-md-0" id="orderFiltersCollapse">
                <div class="row g-2 align-items-center">
                    <!-- Statut de livraison -->
                    <div class="col-6 col-md-auto">
                        <select name="status" id="orderStatusFilterSelect" class="form-select form-select-sm fw-semibold" style="min-width: 160px;">
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

                    <!-- Statut de paiement -->
                    <div class="col-6 col-md-auto">
                        <select name="payment_status" class="form-select form-select-sm" style="min-width: 155px;">
                            <option value="">Tous les paiements</option>
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

                    <!-- Régime Fiscal (TTC / HT) -->
                    <div class="col-6 col-md-auto">
                        <select name="tax_mode" class="form-select form-select-sm fw-semibold" style="min-width: 145px;">
                            <option value="">🏷️ Tous (TTC / HT)</option>
                            <option value="ttc" {{ strtolower((string) request('tax_mode')) === 'ttc' ? 'selected' : '' }}>TTC</option>
                            <option value="ht" {{ strtolower((string) request('tax_mode')) === 'ht' ? 'selected' : '' }}>HT</option>
                        </select>
                    </div>

                    <!-- Magasin / Dépôt source -->
                    <div class="col-12 col-md-auto">
                        <select name="store_id" class="form-select form-select-sm" style="min-width: 170px;">
                            <option value="">🏪 Tous les dépôts</option>
                            <option value="unassigned" {{ request('store_id') === 'unassigned' ? 'selected' : '' }}>⚠️ Dépôt à affecter</option>
                            @foreach($stores ?? [] as $st)
                                <option value="{{ $st->id }}" {{ (string) request('store_id') === (string) $st->id ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre de Date (Preset) -->
                    <div class="col-12 col-md-auto">
                        <select name="date_preset" id="orderDatePreset" class="form-select form-select-sm" style="min-width: 165px;">
                            <option value="">📅 Toutes les dates</option>
                            <option value="today" {{ request('date_preset') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="yesterday" {{ request('date_preset') == 'yesterday' ? 'selected' : '' }}>Hier</option>
                            <option value="this_week" {{ request('date_preset') == 'this_week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="this_month" {{ request('date_preset') == 'this_month' ? 'selected' : '' }}>Ce mois-ci</option>
                            <option value="last_month" {{ request('date_preset') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                            <option value="custom" {{ request('date_preset') == 'custom' || (!request('date_preset') && (request('date_from') || request('date_to'))) ? 'selected' : '' }}>Période personnalisée...</option>
                        </select>
                    </div>

                    <!-- Date de début (Du) -->
                    <div class="col-6 col-md-auto">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted px-2" style="font-size: 0.72rem;">Du</span>
                            <input type="date" name="date_from" id="orderDateFrom" class="form-control form-control-sm" 
                                   value="{{ request('date_from', request('date_preset') ? ($stats['date_from'] ?? '') : '') }}" 
                                   title="Date de début">
                        </div>
                    </div>

                    <!-- Date de fin (Au) -->
                    <div class="col-6 col-md-auto">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted px-2" style="font-size: 0.72rem;">Au</span>
                            <input type="date" name="date_to" id="orderDateTo" class="form-control form-control-sm" 
                                   value="{{ request('date_to', request('date_preset') ? ($stats['date_to'] ?? '') : '') }}" 
                                   title="Date de fin">
                        </div>
                    </div>

                    <!-- Boutons Filtrer / Réinitialiser -->
                    <div class="col-12 col-md-auto d-flex gap-2">
                        <button type="submit" class="btn-brand-primary btn-sm flex-grow-1 flex-md-grow-0 justify-content-center" title="Appliquer les filtres">
                            <i class="fas fa-filter me-1"></i> <span class="d-none d-md-inline">Filtrer</span><span class="d-md-none">Appliquer les filtres</span>
                        </button>
                        <a href="{{ route($indexRoute) }}" class="btn-brand-light btn-sm flex-shrink-0" title="Réinitialiser les filtres">
                            <i class="fas fa-redo"></i>
                        </a>
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

            @if(request('store_id'))
                @php
                    $selectedStoreObj = isset($stores) && is_numeric(request('store_id'))
                        ? $stores->firstWhere('id', (int) request('store_id'))
                        : null;
                    $storeFilterLabel = request('store_id') === 'unassigned'
                        ? 'À affecter'
                        : ($selectedStoreObj?->name ?? '#' . request('store_id'));
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['store_id' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    <i class="fas fa-warehouse text-primary me-0.5"></i> Dépôt : {{ $storeFilterLabel }} <i class="fas fa-times text-muted ms-0.5"></i>
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
            @if(request('status') || request('payment_status') || request('tax_mode') || request('store_id') || request('search'))
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
                        <th class="text-center d-none" style="min-width: 130px;">Paiement</th>
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
                                             alt="{{ $firstItem->product_name }}" 
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
                                            {{ $firstItem->product_name }}
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
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0" style="font-size: 0.64rem;" title="{{ $stBadge->name }}">
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
                            <div>{{ $order->formatted_total }}</div>
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
                            @if((float) ($order->remaining_amount ?? 0) > 0)
                                <div class="mt-1">
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                        Reste : {{ currency($order->remaining_amount) }}
                                    </span>
                                    @if($order->payment_due_date)
                                        <div class="text-muted" style="font-size: 0.64rem;">
                                            Éch. {{ $order->payment_due_date->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>
                            @endif
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
                        </td>
                        <td class="text-center d-none">
                            @php
                                $payStatus = strtolower($order->payment_status);
                                $isPaid = in_array($payStatus, ['paid', 'payé']);
                                $pClass = match($payStatus) {
                                    'paid', 'payé' => 'paid',
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
                                    'failed', 'échoué' => 'fa-exclamation-circle',
                                    'refunded', 'remboursé' => 'fa-undo',
                                    default => 'fa-clock'
                                };
                            @endphp
                            <div class="d-inline-block">
                                <span class="brand-badge pay-badge-{{ $pClass }} py-1 px-2.5" 
                                      id="dt-pay-pill-{{ $order->id }}"
                                      title="Statut de paiement : {{ $pText }}">
                                    <i class="fas {{ $pIcon }}" id="dt-pay-icon-{{ $order->id }}"></i>
                                    <span id="dt-pay-label-{{ $order->id }}">{{ $pText }}</span>
                                </span>
                            </div>
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
            document.getElementById(`mob-status-pill-${orderId}`),
            document.getElementById(`mob-pay-pill-${orderId}`),
            document.getElementById(`mob-tax-pill-${orderId}`)
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
    function quickUpdateOrder(orderId, field, value) {
        if (!orderId || !field || !value) return;

        // Auto close dropdown immediately on click
        closeOrderDropdowns(orderId);

        // Visual loading indicator on triggering pill (using pointerEvents instead of disabled)
        const dtPillId = field === 'status'
            ? `dt-status-pill-${orderId}`
            : (field === 'tax_mode' ? `dt-tax-pill-${orderId}` : `dt-pay-pill-${orderId}`);
        const mobPillId = field === 'status'
            ? `mob-status-pill-${orderId}`
            : (field === 'tax_mode' ? `mob-tax-pill-${orderId}` : `mob-pay-pill-${orderId}`);
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
                date_from: urlParams.get('date_from') || '{{ $stats['date_from'] ?? '' }}',
                date_to: urlParams.get('date_to') || '{{ $stats['date_to'] ?? '' }}',
                date_preset: urlParams.get('date_preset') || ''
            };

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
            'en_demande': { label: 'Sur Commande', dot: 'indigo', badgeClass: 'indigo status-pill-en_demande', textClass: 'text-primary', mobBg: '#4338ca', mobColor: '#ffffff', mobBorder: '#3730a3' },
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

        // --- Payment Status ---
        const pst = (order.payment_status || '').toLowerCase();
        const pstMap = {
            'paid': { label: 'Payé', icon: 'fa-check-circle', badgeClass: 'paid', textClass: 'text-success', mobClass: 'paid' },
            'partial': { label: 'Partiel', icon: 'fa-clock', badgeClass: 'pending', textClass: 'text-warning', mobClass: 'pending' },
            'pending': { label: 'En attente', icon: 'fa-clock', badgeClass: 'pending', textClass: 'text-warning', mobClass: 'pending' },
            'failed': { label: 'Échoué', icon: 'fa-exclamation-circle', badgeClass: 'failed', textClass: 'text-danger', mobClass: 'failed' },
            'refunded': { label: 'Remboursé', icon: 'fa-undo', badgeClass: 'refunded', textClass: 'text-secondary', mobClass: 'refunded' }
        };
        const pInfo = pstMap[pst] || { label: order.payment_status_label || pst, icon: 'fa-clock', badgeClass: 'pending', textClass: 'text-warning', mobClass: 'pending' };

        // Desktop Payment Badge (Static read-only pill)
        const dtPayPill = document.getElementById(`dt-pay-pill-${orderId}`);
        if (dtPayPill) {
            dtPayPill.className = `brand-badge pay-badge-${pInfo.badgeClass} py-1 px-2.5`;
            const dtPayLabel = document.getElementById(`dt-pay-label-${orderId}`);
            if (dtPayLabel) dtPayLabel.textContent = pInfo.label;
            const dtPayIcon = document.getElementById(`dt-pay-icon-${orderId}`);
            if (dtPayIcon) dtPayIcon.className = `fas ${pInfo.icon}`;
        }

        // Mobile Payment Pill (Static read-only pill)
        const mobPayPill = document.getElementById(`mob-pay-pill-${orderId}`);
        if (mobPayPill) {
            mobPayPill.className = `mobile-order-pay-pill ${pInfo.mobClass} border-0`;
            const mobPayLabel = document.getElementById(`mob-pay-label-${orderId}`);
            if (mobPayLabel) mobPayLabel.textContent = pInfo.label;
            const mobPayIcon = document.getElementById(`mob-pay-icon-${orderId}`);
            if (mobPayIcon) mobPayIcon.className = `fas ${pInfo.icon}`;
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
                mobTaxPill.className = `tax-mode-pill tax-mode-${tm} dropdown-toggle mt-1`;
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
            'en_demande': { bg: '#4338ca', border: '#3730a3', color: '#ffffff' },
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
            if (window.innerWidth < 768) {
                const activeKpiCard = document.querySelector('.orders-kpi-grid .order-kpi-card.active-filter');
                if (activeKpiCard) {
                    activeKpiCard.scrollIntoView({ behavior: 'auto', inline: 'center', block: 'nearest' });
                }
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
