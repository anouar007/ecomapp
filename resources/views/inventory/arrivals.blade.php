@extends('layouts.app')

@section('title', 'Suivi des Arrivages & Lots FIFO')

@push('styles')
<style>
.fifo-arrivals-page .fifo-kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px !important;
    padding: 1rem 1.15rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: transform 0.16s ease, box-shadow 0.16s ease;
}
.fifo-arrivals-page .fifo-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}
.fifo-arrivals-page .fifo-kpi-label {
    font-size: 0.7rem;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
}
.fifo-arrivals-page .fifo-kpi-value {
    font-size: clamp(1.05rem, 2.2vw, 1.45rem);
    font-weight: 800 !important;
    line-height: 1.2;
    margin-top: 4px;
    font-variant-numeric: tabular-nums;
}
.fifo-arrivals-page .fifo-kpi-sub {
    font-size: 0.72rem;
    color: #64748b;
    margin-top: 6px;
}
.fifo-arrivals-page .fifo-kpi-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.fifo-arrivals-page .fifo-pills-scroll {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.fifo-arrivals-page .fifo-pills-scroll::-webkit-scrollbar {
    display: none;
}
.fifo-arrivals-page .fifo-pill-tab {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.42rem 0.9rem;
    font-size: 0.78rem;
    font-weight: 600 !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    flex-shrink: 0;
    transition: all 0.15s ease;
}
.fifo-arrivals-page .fifo-pill-tab:hover {
    background: #f8fafc;
    color: #0f172a;
}
.fifo-arrivals-page .fifo-pill-tab.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}
.fifo-arrivals-page .fifo-pill-count {
    padding: 1px 7px;
    border-radius: 9999px !important;
    font-size: 0.68rem;
    font-weight: 700 !important;
    background: #f1f5f9;
    color: #334155;
}
.fifo-arrivals-page .fifo-pill-tab.active .fifo-pill-count {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

/* Status Badges */
.fifo-arrivals-page .fifo-status-badge,
.fifo-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 9999px !important;
    font-size: 0.72rem;
    font-weight: 700 !important;
    white-space: nowrap;
}
.fifo-badge-selling {
    background: #dbeafe;
    color: #1d4ed8;
    border: 1px solid #93c5fd;
}
.fifo-badge-active {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #6ee7b7;
}
.fifo-badge-waiting {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fcd34d;
}
.fifo-badge-sold-out {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.fifo-pulse-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
    animation: fifoPulseAnim 1.5s infinite;
}
@keyframes fifoPulseAnim {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.45; transform: scale(1.25); }
    100% { opacity: 1; transform: scale(1); }
}

/* Progress track */
.fifo-arrivals-page .fifo-progress-track {
    width: 100%;
    height: 7px;
    background: #e2e8f0;
    border-radius: 9999px !important;
    overflow: hidden;
}
.fifo-arrivals-page .fifo-progress-fill {
    height: 100%;
    border-radius: 9999px !important;
    transition: width 0.3s ease;
}

/* Product FIFO Pipeline Card */
.fifo-arrivals-page .fifo-pipeline-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 0.75rem;
}
.fifo-arrivals-page .fifo-pipeline-step {
    position: relative;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px !important;
    padding: 0.85rem;
    background: #ffffff;
}
.fifo-arrivals-page .fifo-pipeline-step.is-selling {
    border-color: #3b82f6;
    background: #f8fbff;
}
.fifo-arrivals-page .fifo-pipeline-step.is-active {
    border-color: #10b981;
    background: #f6fef9;
}
.fifo-arrivals-page .fifo-pipeline-step.is-sold_out {
    background: #f8fafc;
    border-color: #cbd5e1;
}
.fifo-arrivals-page .fifo-step-rank {
    font-size: 0.7rem;
    font-weight: 800 !important;
    padding: 2px 8px;
    border-radius: 9999px !important;
    background: #0f172a;
    color: #ffffff;
}

/* Mobile Batch Card */
.fifo-arrivals-page .fifo-mobile-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px !important;
    padding: 0.95rem;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
}
.fifo-arrivals-page .fifo-mobile-metrics {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.5rem;
}
.fifo-arrivals-page .fifo-mobile-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px !important;
    padding: 0.55rem 0.65rem;
}
.fifo-arrivals-page .fifo-mobile-tile-lbl {
    font-size: 0.63rem;
    font-weight: 700 !important;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.03em;
}
.fifo-arrivals-page .fifo-mobile-tile-val {
    font-size: 0.84rem;
    font-weight: 800 !important;
    margin-top: 2px;
    font-variant-numeric: tabular-nums;
}
.fifo-arrivals-page .fifo-thumb {
    width: 44px;
    height: 44px;
    border-radius: 10px !important;
    object-fit: contain;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 2px;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4 fifo-arrivals-page">

    <!-- Header -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="badge bg-danger rounded-pill px-2.5 py-0.5" style="font-size: 0.7rem; font-weight: 700 !important;">
                        <i class="fas fa-lock me-1"></i> FIFO STRICT · ACCÈS ADMIN
                    </span>
                    <span class="text-muted small">Du plus ancien arrivage jusqu'à épuisement avant le suivant</span>
                </div>
                <h1 class="brand-title">Suivi des Arrivages &amp; Lots FIFO</h1>
                <p class="brand-subtitle">Consultez la date d'arrivée, le coût d'achat, le statut d'écoulement, le bénéfice réalisé et la durée pour vendre chaque arrivage.</p>
            </div>
        </div>

        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('inventory.index') }}" class="btn-brand-outline">
                <i class="fas fa-boxes me-1.5"></i> Inventaire
            </a>
            <a href="{{ route('shipments.create') }}" class="btn-brand-primary">
                <i class="fas fa-truck-ramp-box me-1.5"></i> Nouvel Arrivage
            </a>
        </div>
    </div>

    @include('partials.subnav.inventory')

    <!-- 5 KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl">
            <div class="fifo-kpi-card">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="fifo-kpi-label">Arrivages / Lots</div>
                        <div class="fifo-kpi-value text-dark">{{ number_format($kpis['total_batches'], 0, ',', ' ') }}</div>
                    </div>
                    <div class="fifo-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                </div>
                <div class="fifo-kpi-sub">
                    <strong>{{ number_format($kpis['total_sold_units'], 0, ',', ' ') }}</strong> vendues / {{ number_format($kpis['total_initial_units'], 0, ',', ' ') }} u.
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl">
            <div class="fifo-kpi-card">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="fifo-kpi-label">Coût Total Arrivages</div>
                        <div class="fifo-kpi-value text-dark">{{ currency($kpis['total_arrival_cost']) }}</div>
                    </div>
                    <div class="fifo-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
                <div class="fifo-kpi-sub">
                    Coût écoulé : <strong>{{ currency($kpis['total_realized_cost']) }}</strong>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl">
            <div class="fifo-kpi-card" style="border-left: 4px solid #10b981;">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="fifo-kpi-label">Bénéfice Réalisé</div>
                        <div class="fifo-kpi-value {{ $kpis['total_realized_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $kpis['total_realized_profit'] >= 0 ? '+' : '' }}{{ currency($kpis['total_realized_profit']) }}
                        </div>
                    </div>
                    <div class="fifo-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="fifo-kpi-sub">
                    CA : <strong>{{ currency($kpis['total_realized_revenue']) }}</strong> · Marge <strong>{{ $kpis['avg_realized_margin_pct'] }}%</strong>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-xl">
            <div class="fifo-kpi-card">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="fifo-kpi-label">Restant &amp; Potentiel</div>
                        <div class="fifo-kpi-value text-primary">{{ number_format($kpis['total_remaining_units'], 0, ',', ' ') }} u.</div>
                    </div>
                    <div class="fifo-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
                <div class="fifo-kpi-sub">
                    Bén. potentiel : <strong class="{{ $kpis['estimated_remaining_profit'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $kpis['estimated_remaining_profit'] >= 0 ? '+' : '' }}{{ currency($kpis['estimated_remaining_profit']) }}</strong>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl">
            <div class="fifo-kpi-card">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="fifo-kpi-label">Temps Moyen d'Écoulement</div>
                        <div class="fifo-kpi-value text-dark">{{ $kpis['avg_sell_duration_label'] }}</div>
                    </div>
                    <div class="fifo-kpi-icon bg-secondary-subtle text-secondary">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                </div>
                <div class="fifo-kpi-sub">
                    Calculé sur <strong>{{ $pillCounts['sold_out'] }}</strong> lot(s) vendus à 100%
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Pills & Search / Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            @php
                $preserveFilters = array_filter([
                    'source_type' => $sourceType !== 'all' ? $sourceType : null,
                    'product_id' => $productId,
                    'store_id' => $storeId,
                    'search' => $search !== '' ? $search : null,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'sort' => $sort !== 'arrived_desc' ? $sort : null,
                ], fn ($v) => $v !== null && $v !== '');
            @endphp

            <div class="fifo-pills-scroll mb-3">
                <a href="{{ route('inventory.arrivals', $preserveFilters) }}"
                   class="fifo-pill-tab {{ $status === 'all' ? 'active' : '' }}">
                    <span>Tous les lots</span>
                    <span class="fifo-pill-count">{{ $pillCounts['all'] }}</span>
                </a>
                <a href="{{ route('inventory.arrivals', array_merge($preserveFilters, ['status' => 'selling'])) }}"
                   class="fifo-pill-tab {{ $status === 'selling' ? 'active' : '' }}">
                    <i class="fas fa-fire text-primary"></i>
                    <span>En cours de vente</span>
                    <span class="fifo-pill-count">{{ $pillCounts['selling'] }}</span>
                </a>
                <a href="{{ route('inventory.arrivals', array_merge($preserveFilters, ['status' => 'active'])) }}"
                   class="fifo-pill-tab {{ $status === 'active' ? 'active' : '' }}">
                    <i class="fas fa-play text-success"></i>
                    <span>Actif · Prochain FIFO</span>
                    <span class="fifo-pill-count">{{ $pillCounts['active'] }}</span>
                </a>
                <a href="{{ route('inventory.arrivals', array_merge($preserveFilters, ['status' => 'waiting'])) }}"
                   class="fifo-pill-tab {{ $status === 'waiting' ? 'active' : '' }}">
                    <i class="fas fa-hourglass-half text-warning"></i>
                    <span>En file d'attente</span>
                    <span class="fifo-pill-count">{{ $pillCounts['waiting'] }}</span>
                </a>
                <a href="{{ route('inventory.arrivals', array_merge($preserveFilters, ['status' => 'sold_out'])) }}"
                   class="fifo-pill-tab {{ $status === 'sold_out' ? 'active' : '' }}">
                    <i class="fas fa-check-circle text-secondary"></i>
                    <span>Vendu / Épuisé</span>
                    <span class="fifo-pill-count">{{ $pillCounts['sold_out'] }}</span>
                </a>
            </div>

            <form method="GET" action="{{ route('inventory.arrivals') }}" class="row g-2 align-items-end">
                @if($status !== 'all')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif

                <div class="col-12 col-md-3">
                    <label class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Recherche</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Produit, SKU, réf., fournisseur...">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Article</label>
                    <select name="product_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous les produits</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ (int) $productId === (int) $p->id ? 'selected' : '' }}>
                                {{ $p->name }} {{ $p->sku ? "({$p->sku})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Dépôt / Magasin</label>
                    <select name="store_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous sites</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ (int) $storeId === (int) $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Origine</label>
                    <select name="source_type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $sourceType === 'all' ? 'selected' : '' }}>Toutes sources</option>
                        <option value="shipment" {{ $sourceType === 'shipment' ? 'selected' : '' }}>Arrivage fournisseur</option>
                        <option value="inventory_in" {{ $sourceType === 'inventory_in' ? 'selected' : '' }}>Entrée inventaire</option>
                        <option value="opening_stock" {{ $sourceType === 'opening_stock' ? 'selected' : '' }}>Stock initial</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Tri</label>
                    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="arrived_desc" {{ $sort === 'arrived_desc' ? 'selected' : '' }}>Arrivée récente</option>
                        <option value="arrived_asc" {{ $sort === 'arrived_asc' ? 'selected' : '' }}>Ordre FIFO (ancien → récent)</option>
                        <option value="profit_desc" {{ $sort === 'profit_desc' ? 'selected' : '' }}>Plus rentable</option>
                        <option value="duration_asc" {{ $sort === 'duration_asc' ? 'selected' : '' }}>Vendu le plus vite</option>
                        <option value="duration_desc" {{ $sort === 'duration_desc' ? 'selected' : '' }}>Plus longue durée</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Arrivé du</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Au</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm">
                </div>

                <div class="col-12 col-md-auto d-flex gap-1.5 ms-md-auto">
                    <button type="submit" class="btn btn-sm btn-dark px-3" style="font-weight: 600 !important;">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    @if($status !== 'all' || $sourceType !== 'all' || $productId || $storeId || $search !== '' || $dateFrom || $dateTo || $sort !== 'arrived_desc')
                        <a href="{{ route('inventory.arrivals') }}" class="btn btn-sm btn-outline-secondary" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Chronological FIFO Queue Pipeline when scoped to a single product -->
    @if($selectedProduct && $productFifoQueue->isNotEmpty())
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <img src="{{ $selectedProduct->thumbnail }}" alt="{{ $selectedProduct->name }}" class="fifo-thumb"
                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                <div>
                    <div class="small text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700 !important;">
                        File FIFO chronologique · {{ $productFifoQueue->count() }} arrivage(s)
                    </div>
                    <h6 class="mb-0 text-dark" style="font-weight: 800 !important;">{{ $selectedProduct->name }}</h6>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border">Stock catalogue : {{ (int) ($selectedProduct->stock ?? 0) }} u.</span>
                <a href="{{ route('inventory.adjust', $selectedProduct) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Ajouter arrivage
                </a>
            </div>
        </div>
        <div class="card-body p-3 bg-light bg-opacity-50">
            <div class="fifo-pipeline-grid">
                @foreach($productFifoQueue as $idx => $qBatch)
                    @php
                        $pBarColor = match($qBatch->status) {
                            'sold_out' => '#64748b',
                            'selling' => '#2563eb',
                            'active' => '#10b981',
                            default => '#f59e0b',
                        };
                    @endphp
                    <div class="fifo-pipeline-step is-{{ $qBatch->status }}">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                            <span class="fifo-step-rank">#{{ $idx + 1 }} FIFO</span>
                            <span class="fifo-status-badge {{ $qBatch->status_badge_class }}" style="font-size: 0.65rem; padding: 2px 8px;">
                                @if($qBatch->status === 'selling')<span class="fifo-pulse-dot"></span>@endif
                                {{ $qBatch->status_label }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between small mb-1">
                            <span class="font-monospace text-dark" style="font-weight: 700 !important;">{{ $qBatch->reference ?? ('LOT #' . $qBatch->id) }}</span>
                            <span class="text-muted">{{ $qBatch->arrived_at ? $qBatch->arrived_at->format('d/m/Y') : '—' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between small mb-1">
                            <span class="text-muted">Coût : <strong class="text-dark">{{ currency($qBatch->total_cost) }}</strong> ({{ currency($qBatch->unit_cost) }}/u)</span>
                        </div>
                        <div class="fifo-progress-track my-1.5">
                            <div class="fifo-progress-fill" style="width: {{ min(100, $qBatch->consumed_percent) }}%; background: {{ $pBarColor }};"></div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                            <span><strong>{{ $qBatch->sold_quantity }}</strong>/{{ $qBatch->initial_quantity }} vendu(s)</span>
                            <span class="text-muted">Reste : <strong>{{ $qBatch->remaining_quantity }}</strong></span>
                        </div>
                        <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                            <span class="{{ $qBatch->realized_profit >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: 700 !important;">
                                Bén. {{ $qBatch->realized_profit >= 0 ? '+' : '' }}{{ currency($qBatch->realized_profit) }}
                            </span>
                            <span class="text-muted"><i class="far fa-clock me-1"></i>{{ $qBatch->sell_duration_label }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Main List Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="mb-0 text-dark" style="font-weight: 800 !important;">Historique des Lots &amp; Arrivages ({{ $batches->total() }})</h6>
                <span class="text-muted small">Chaque vente prélève automatiquement sur le lot le plus ancien jusqu'à épuisement complet.</span>
            </div>
        </div>

        <!-- DESKTOP TABLE -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase" style="font-size: 0.7rem; font-weight: 700 !important;">Produit &amp; Arrivage</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.7rem; font-weight: 700 !important;">Date d'arrivée</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.7rem; font-weight: 700 !important;">Statut FIFO</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.7rem; font-weight: 700 !important; min-width: 155px;">Écoulement</th>
                        <th class="py-3 text-secondary text-uppercase text-end" style="font-size: 0.7rem; font-weight: 700 !important;">Coût Arrivage</th>
                        <th class="py-3 text-secondary text-uppercase text-end" style="font-size: 0.7rem; font-weight: 700 !important;">Bénéfice Arrivage</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.7rem; font-weight: 700 !important;">Temps pour vendre</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase text-end" style="font-size: 0.7rem; font-weight: 700 !important;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($batches as $batch)
                        @php
                            $barColor = match($batch->status) {
                                'sold_out' => '#64748b',
                                'selling' => '#2563eb',
                                'active' => '#10b981',
                                default => '#f59e0b',
                            };
                        @endphp
                        <tr>
                            <td class="ps-3 py-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="{{ $batch->product ? $batch->product->thumbnail : asset('images/camera/cat_cameras.jpg') }}"
                                         alt="{{ $batch->product?->name ?? 'Produit' }}"
                                         class="fifo-thumb"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    <div class="min-w-0">
                                        @if($batch->product)
                                            <a href="{{ route('inventory.arrivals', ['product_id' => $batch->product_id]) }}"
                                               class="text-dark text-decoration-none d-block text-truncate"
                                               style="max-width: 240px; font-weight: 700 !important; font-size: 0.86rem;"
                                               title="Voir la file FIFO de {{ $batch->product->name }}">
                                                {{ $batch->product->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Produit supprimé</span>
                                        @endif
                                        <div class="d-flex align-items-center flex-wrap gap-1 mt-0.5" style="font-size: 0.71rem;">
                                            <span class="badge bg-light text-dark border font-monospace">{{ $batch->reference ?? ('LOT #' . $batch->id) }}</span>
                                            <span class="badge bg-light text-secondary border">{{ $batch->source_label }}</span>
                                            @if($batch->variant)
                                                <span class="badge bg-primary-subtle text-primary">{{ $batch->variant->name }}</span>
                                            @endif
                                            @if($batch->store)
                                                <span class="badge store-badge-themed border" style="{{ $batch->store->badge_style }}">{{ $batch->store->name }}</span>
                                            @endif
                                            @if($batch->supplier_name)
                                                <span class="text-muted">· {{ $batch->supplier_name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="text-dark" style="font-weight: 700 !important; font-size: 0.84rem;">
                                    {{ $batch->arrived_at ? $batch->arrived_at->format('d/m/Y') : '—' }}
                                </div>
                                <div class="text-muted" style="font-size: 0.71rem;">
                                    {{ $batch->arrived_at ? $batch->arrived_at->format('H:i') : '' }}
                                    @if($batch->arrived_at)
                                        · {{ $batch->arrived_at->diffForHumans() }}
                                    @endif
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="fifo-status-badge {{ $batch->status_badge_class }}">
                                    @if($batch->status === 'selling')
                                        <span class="fifo-pulse-dot"></span>
                                    @elseif($batch->status === 'active')
                                        <i class="fas fa-bolt"></i>
                                    @elseif($batch->status === 'sold_out')
                                        <i class="fas fa-check-circle"></i>
                                    @else
                                        <i class="fas fa-hourglass-half"></i>
                                    @endif
                                    <span>{{ $batch->status_label }}</span>
                                </span>
                            </td>

                            <td class="py-3">
                                <div class="d-flex align-items-center justify-content-between mb-1" style="font-size: 0.76rem;">
                                    <span><strong>{{ $batch->sold_quantity }}</strong> / {{ $batch->initial_quantity }} vendu(s)</span>
                                    <span class="text-muted">{{ $batch->sold_percent }}%</span>
                                </div>
                                <div class="fifo-progress-track">
                                    <div class="fifo-progress-fill" style="width: {{ min(100, $batch->consumed_percent) }}%; background: {{ $barColor }};"></div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1" style="font-size: 0.7rem;">
                                    <span class="{{ $batch->remaining_quantity > 0 ? 'text-dark' : 'text-muted' }}">
                                        Reste : <strong>{{ $batch->remaining_quantity }}</strong> u.
                                    </span>
                                    @if($batch->manual_out_quantity > 0)
                                        <span class="text-warning">(-{{ $batch->manual_out_quantity }} ajuste.)</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3 text-end">
                                <div class="text-dark" style="font-weight: 800 !important; font-size: 0.88rem;">
                                    {{ currency($batch->total_cost) }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ currency($batch->unit_cost) }} / u.
                                </div>
                            </td>

                            <td class="py-3 text-end">
                                @if($batch->sold_quantity > 0)
                                    <div class="{{ $batch->realized_profit >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: 800 !important; font-size: 0.88rem;">
                                        {{ $batch->realized_profit >= 0 ? '+' : '' }}{{ currency($batch->realized_profit) }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.71rem;">
                                        CA {{ currency($batch->realized_revenue) }} · <strong>{{ $batch->realized_margin_percentage }}%</strong>
                                    </div>
                                @else
                                    <div class="text-muted" style="font-weight: 600 !important; font-size: 0.82rem;">0,00 DH réalisé</div>
                                @endif
                                @if($batch->remaining_quantity > 0 && $batch->current_selling_price > 0)
                                    <div class="{{ $batch->estimated_remaining_profit >= 0 ? 'text-primary' : 'text-danger' }}" style="font-size: 0.69rem; font-weight: 600 !important;">
                                        Potentiel rest. : {{ $batch->estimated_remaining_profit >= 0 ? '+' : '' }}{{ currency($batch->estimated_remaining_profit) }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3">
                                <div class="text-dark" style="font-weight: 700 !important; font-size: 0.84rem;">
                                    <i class="far fa-clock me-1 text-muted"></i>{{ $batch->sell_duration_label }}
                                </div>
                                @if($batch->status === 'sold_out' && $batch->sold_out_at)
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        Terminé le {{ $batch->sold_out_at->format('d/m/Y H:i') }}
                                    </div>
                                @elseif($batch->first_sold_at)
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        1ère vente {{ $batch->first_sold_at->format('d/m/Y') }}
                                    </div>
                                @else
                                    <div class="text-muted" style="font-size: 0.7rem;">Aucune vente sur ce lot</div>
                                @endif
                            </td>

                            <td class="pe-3 py-3 text-end text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2.5 py-1"
                                            style="font-size: 0.74rem; font-weight: 600 !important;"
                                            onclick="openFifoAllocationsModal({{ $batch->id }})">
                                        <i class="fas fa-receipt me-1"></i>Ventes ({{ $batch->allocations_count }})
                                    </button>
                                    @if($batch->shipment_id)
                                        <a href="{{ route('shipments.show', $batch->shipment_id) }}"
                                           class="btn btn-sm btn-light border rounded-circle"
                                           title="Voir bon d'arrivage {{ $batch->reference }}">
                                            <i class="fas fa-truck-ramp-box text-primary"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-layer-group fa-3x mb-3 opacity-25"></i>
                                <p class="fw-semibold mb-1">Aucun arrivage FIFO enregistré pour ces critères.</p>
                                <p class="small mb-3">Les arrivages créés via <strong>Arrivages</strong> ou <strong>Entrée de stock</strong> apparaîtront ici avec leur ordre FIFO.</p>
                                <a href="{{ route('shipments.create') }}" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5">
                                    <i class="fas fa-plus me-1"></i> Créer un arrivage
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE-FIRST CARDS -->
        <div class="d-block d-lg-none p-3 bg-light bg-opacity-50">
            <div class="d-flex flex-column gap-3">
                @forelse($batches as $batch)
                    @php
                        $barColor = match($batch->status) {
                            'sold_out' => '#64748b',
                            'selling' => '#2563eb',
                            'active' => '#10b981',
                            default => '#f59e0b',
                        };
                    @endphp
                    <div class="fifo-mobile-card">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <img src="{{ $batch->product ? $batch->product->thumbnail : asset('images/camera/cat_cameras.jpg') }}"
                                     alt="{{ $batch->product?->name ?? 'Produit' }}"
                                     class="fifo-thumb"
                                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                <div class="min-w-0">
                                    @if($batch->product)
                                        <a href="{{ route('inventory.arrivals', ['product_id' => $batch->product_id]) }}"
                                           class="text-dark text-decoration-none d-block text-truncate"
                                           style="font-weight: 800 !important; font-size: 0.88rem;">
                                            {{ $batch->product->name }}
                                        </a>
                                    @endif
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        <span class="font-monospace fw-semibold">{{ $batch->reference ?? ('LOT #' . $batch->id) }}</span>
                                        · Arrivé le {{ $batch->arrived_at ? $batch->arrived_at->format('d/m/Y') : '—' }}
                                    </div>
                                </div>
                            </div>
                            <span class="fifo-status-badge {{ $batch->status_badge_class }}" style="font-size: 0.65rem; padding: 3px 8px;">
                                @if($batch->status === 'selling')<span class="fifo-pulse-dot"></span>@endif
                                {{ $batch->status_label }}
                            </span>
                        </div>

                        <div class="d-flex flex-wrap gap-1 mb-2" style="font-size: 0.68rem;">
                            <span class="badge bg-light text-secondary border">{{ $batch->source_label }}</span>
                            @if($batch->variant)<span class="badge bg-primary-subtle text-primary">{{ $batch->variant->name }}</span>@endif
                            @if($batch->store)<span class="badge store-badge-themed border" style="{{ $batch->store->badge_style }}">{{ $batch->store->name }}</span>@endif
                        </div>

                        <div class="mb-2.5">
                            <div class="d-flex align-items-center justify-content-between mb-1" style="font-size: 0.74rem;">
                                <span>Écoulement : <strong>{{ $batch->sold_quantity }}</strong> / {{ $batch->initial_quantity }} vendus</span>
                                <span class="text-muted">Reste <strong>{{ $batch->remaining_quantity }}</strong> u.</span>
                            </div>
                            <div class="fifo-progress-track">
                                <div class="fifo-progress-fill" style="width: {{ min(100, $batch->consumed_percent) }}%; background: {{ $barColor }};"></div>
                            </div>
                        </div>

                        <div class="fifo-mobile-metrics mb-2.5">
                            <div class="fifo-mobile-tile">
                                <div class="fifo-mobile-tile-lbl">Coût arrivage</div>
                                <div class="fifo-mobile-tile-val text-dark">{{ currency($batch->total_cost) }}</div>
                                <div class="text-muted" style="font-size: 0.67rem;">{{ currency($batch->unit_cost) }} / u.</div>
                            </div>
                            <div class="fifo-mobile-tile" style="background: #ecfdf5; border-color: #a7f3d0;">
                                <div class="fifo-mobile-tile-lbl">Bénéfice réalisé</div>
                                <div class="fifo-mobile-tile-val {{ $batch->realized_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $batch->realized_profit >= 0 ? '+' : '' }}{{ currency($batch->realized_profit) }}
                                </div>
                                <div class="text-muted" style="font-size: 0.67rem;">Marge {{ $batch->realized_margin_percentage }}%</div>
                            </div>
                            <div class="fifo-mobile-tile">
                                <div class="fifo-mobile-tile-lbl">Temps pour vendre</div>
                                <div class="fifo-mobile-tile-val text-dark">{{ $batch->sell_duration_label }}</div>
                            </div>
                            <div class="fifo-mobile-tile">
                                <div class="fifo-mobile-tile-lbl">Potentiel restant</div>
                                <div class="fifo-mobile-tile-val {{ $batch->estimated_remaining_profit >= 0 ? 'text-primary' : 'text-danger' }}">{{ $batch->estimated_remaining_profit >= 0 ? '+' : '' }}{{ currency($batch->estimated_remaining_profit) }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-dark rounded-pill px-3 py-1 flex-grow-1"
                                    style="font-size: 0.76rem; font-weight: 600 !important;"
                                    onclick="openFifoAllocationsModal({{ $batch->id }})">
                                <i class="fas fa-receipt me-1"></i> Détail ventes ({{ $batch->allocations_count }})
                            </button>
                            @if($batch->shipment_id)
                                <a href="{{ route('shipments.show', $batch->shipment_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.76rem;">
                                    Bon arrivage
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <p class="fw-semibold mb-0">Aucun lot FIFO trouvé.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if($batches->hasPages())
            <div class="p-3 border-top bg-light d-flex justify-content-center">
                {{ $batches->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Sales Allocations Drawn From This Arrival -->
<div class="modal fade" id="fifoAllocationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                <div>
                    <div class="small text-white-50" id="fifoModalRef">—</div>
                    <h6 class="modal-title fw-bold mb-0" id="fifoModalProduct">Détail FIFO de l'arrivage</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-2 mb-3" id="fifoModalSummary">
                    <div class="col-6 col-md-3">
                        <div class="p-2.5 rounded-3 bg-light border">
                            <div class="text-muted small" style="font-size: 0.68rem;">Arrivé le</div>
                            <div class="fw-bold text-dark small" id="fifoModalArrived">—</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2.5 rounded-3 bg-light border">
                            <div class="text-muted small" style="font-size: 0.68rem;">Coût unitaire / Total</div>
                            <div class="fw-bold text-dark small" id="fifoModalCost">—</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2.5 rounded-3 border" style="background: #ecfdf5; border-color: #a7f3d0 !important;">
                            <div class="text-muted small" style="font-size: 0.68rem;">Bénéfice réalisé</div>
                            <div class="fw-bold text-success small" id="fifoModalProfit">—</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2.5 rounded-3 bg-light border">
                            <div class="text-muted small" style="font-size: 0.68rem;">Temps de vente</div>
                            <div class="fw-bold text-dark small" id="fifoModalDuration">—</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.04em;">
                    Ventes prélevées sur cet arrivage
                </h6>
                <div class="table-responsive border rounded-3">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-2 small text-secondary">Date</th>
                                <th class="py-2 small text-secondary">Document</th>
                                <th class="py-2 small text-secondary">Client</th>
                                <th class="py-2 small text-secondary text-center">Qté</th>
                                <th class="py-2 small text-secondary text-end">Prix vente</th>
                                <th class="py-2 small text-secondary text-end">Coût FIFO</th>
                                <th class="pe-3 py-2 small text-secondary text-end">Bénéfice</th>
                            </tr>
                        </thead>
                        <tbody id="fifoModalAllocBody">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Chargement...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2.5 px-4">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function escapeFifoHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function openFifoAllocationsModal(batchId) {
    const modalEl = document.getElementById('fifoAllocationsModal');
    const tbody = document.getElementById('fifoModalAllocBody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Chargement des ventes FIFO...</td></tr>';
    bootstrap.Modal.getOrCreateInstance(modalEl).show();

    const url = @json(route('inventory.arrivals.allocations', ['stockBatch' => '__BATCH__'])).replace('__BATCH__', batchId);
    fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            const b = data.batch;
            document.getElementById('fifoModalRef').textContent = `${b.reference} · ${b.source_label} (${b.status_label})`;
            document.getElementById('fifoModalProduct').textContent = b.product_name + (b.variant_name ? ` (${b.variant_name})` : '');
            document.getElementById('fifoModalArrived').textContent = b.arrived_at_formatted;
            document.getElementById('fifoModalCost').textContent = `${b.unit_cost_formatted}/u · ${b.total_cost_formatted}`;
            document.getElementById('fifoModalProfit').textContent = `${b.realized_profit_formatted} (${b.realized_margin_pct}%)`;
            document.getElementById('fifoModalDuration').textContent = b.sell_duration_label;

            const rows = data.allocations || [];
            if (rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Aucune unité vendue sur cet arrivage pour le moment.</td></tr>';
                return;
            }

            tbody.innerHTML = rows.map(r => {
                let docHtml = '<span class="text-muted">Vente directe</span>';
                if (r.order_url && r.order_number) {
                    docHtml = `<a href="${escapeFifoHtml(r.order_url)}" class="fw-semibold text-primary text-decoration-none font-monospace">#${escapeFifoHtml(r.order_number)}</a>`;
                } else if (r.invoice_url && r.invoice_number) {
                    docHtml = `<a href="${escapeFifoHtml(r.invoice_url)}" class="fw-semibold text-info text-decoration-none font-monospace">${escapeFifoHtml(r.invoice_number)}</a>`;
                }
                const profClass = r.profit >= 0 ? 'text-success' : 'text-danger';
                return `
                    <tr>
                        <td class="ps-3 py-2 small text-muted">${escapeFifoHtml(r.allocated_at_formatted)}</td>
                        <td class="py-2 small">${docHtml}</td>
                        <td class="py-2 small">${escapeFifoHtml(r.customer_name)}</td>
                        <td class="py-2 text-center fw-bold">${r.quantity}</td>
                        <td class="py-2 text-end small">${escapeFifoHtml(r.unit_sell_price_formatted)}</td>
                        <td class="py-2 text-end small text-muted">${escapeFifoHtml(r.total_cost_formatted)}</td>
                        <td class="pe-3 py-2 text-end fw-bold ${profClass}">${escapeFifoHtml(r.profit_formatted)}</td>
                    </tr>
                `;
            }).join('');
        })
        .catch(() => {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Impossible de charger les ventes de ce lot.</td></tr>';
        });
}
</script>
@endpush
