@extends('layouts.app')

@section('title', 'Rentabilité & Marges Réelles (Admin)')

@push('styles')
<style>
/* ==========================================================================
   PROFITABILITY & MARGINS EXECUTIVE DASHBOARD - PRO ERP STANDARD
   ========================================================================== */
.prof-filter-pill {
    white-space: nowrap;
    border-radius: 9999px;
    padding: 0.42rem 0.95rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    flex-shrink: 0;
}
.prof-filter-pill:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.prof-filter-pill.active {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.22);
}

.chips-scroll-prof {
    display: flex;
    gap: 0.45rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.chips-scroll-prof::-webkit-scrollbar {
    display: none;
}

/* P&L Waterfall Equation Card */
.pnl-waterfall-grid {
    display: grid;
    grid-template-columns: 1fr auto 1fr auto 1fr auto 1fr auto 1fr;
    gap: 0.65rem;
    align-items: center;
}
.pnl-step-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.85rem 1rem;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.pnl-step-box:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
}
.pnl-step-box.is-gross {
    background: #eff6ff;
    border-color: #bfdbfe;
}
.pnl-step-box.is-net-pos {
    background: #ecfdf5;
    border-color: #a7f3d0;
}
.pnl-step-box.is-net-neg {
    background: #fef2f2;
    border-color: #fecaca;
}
.pnl-operator {
    width: 28px;
    height: 28px;
    border-radius: 9999px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    font-weight: 800;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

@media (max-width: 1199.98px) {
    .pnl-waterfall-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    .pnl-operator {
        margin: -0.15rem auto;
    }
}

.prof-tabs-bar {
    display: flex;
    gap: 0.45rem;
    overflow-x: auto;
    scrollbar-width: none;
    padding: 0.35rem;
    background: #f1f5f9;
    border-radius: 14px;
}
.prof-tabs-bar::-webkit-scrollbar {
    display: none;
}
.prof-tabs-bar .nav-link {
    border: none;
    border-radius: 10px;
    color: #475569;
    font-weight: 600;
    font-size: 0.84rem;
    padding: 0.52rem 0.95rem;
    white-space: nowrap;
    transition: all 0.15s ease;
}
.prof-tabs-bar .nav-link:hover {
    color: #0f172a;
    background: rgba(255, 255, 255, 0.65);
}
.prof-tabs-bar .nav-link.active {
    background: #ffffff;
    color: #dc2626;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
}

.prod-thumb-prof {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    flex-shrink: 0;
}
.prod-thumb-placeholder {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cost-edit-chip {
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    color: #334155;
    border-radius: 9999px;
    padding: 0.25rem 0.65rem;
    font-size: 0.78rem;
    font-weight: 600;
    transition: all 0.15s ease;
    cursor: pointer;
}
.cost-edit-chip:hover {
    border-color: #dc2626;
    background: #fef2f2;
    color: #b91c1c;
}
.cost-edit-chip.is-missing {
    border-color: #f59e0b;
    background: #fffbeb;
    color: #b45309;
}

.chart-mode-btn {
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.28rem 0.75rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    transition: all 0.15s ease;
}
.chart-mode-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}

.prof-mobile-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.prof-table-scroll {
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
.prof-pagination-bar {
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 0.8rem 1.25rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}
.prof-page-btn {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    min-width: 32px;
    height: 32px;
    padding: 0 0.55rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}
.prof-page-btn:hover:not(:disabled):not(.active) {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}
.prof-page-btn.active {
    background: #0f172a;
    border-color: #0f172a;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.18);
}
.prof-page-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}
.prof-table-scroll::-webkit-scrollbar {
    width: 7px;
    height: 7px;
}
.prof-table-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}
.prof-table-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
.prof-table-pro {
    border-collapse: separate;
    border-spacing: 0;
}
.prof-table-pro thead th {
    position: sticky;
    top: 0;
    z-index: 6;
    background: #f8fafc !important;
    color: #475569;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding-top: 0.85rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}
.prof-table-pro thead th.is-sortable {
    cursor: pointer;
    user-select: none;
    transition: color 0.15s ease, background-color 0.15s ease;
}
.prof-table-pro thead th.is-sortable:hover {
    color: #0f172a;
    background: #f1f5f9 !important;
}
.prof-table-pro thead th.is-sortable.sort-active {
    color: #dc2626;
}
.prof-table-pro tbody tr.prof-product-row {
    transition: background-color 0.12s ease;
}
.prof-table-pro tbody tr.prof-product-row td {
    border-bottom: 1px solid #f1f5f9;
    padding-top: 0.72rem;
    padding-bottom: 0.72rem;
    vertical-align: middle;
}
.prof-table-pro tbody tr.prof-product-row:hover td {
    background-color: #f8fafc;
}
.prof-table-pro tbody tr.prof-product-row.row-tone-sold td:first-child {
    box-shadow: inset 3px 0 0 #10b981;
}
.prof-table-pro tbody tr.prof-product-row.row-tone-neg td:first-child {
    box-shadow: inset 3px 0 0 #ef4444;
}
.prof-table-pro tbody tr.prof-product-row.row-tone-missing td:first-child {
    box-shadow: inset 3px 0 0 #f59e0b;
}
.prof-table-pro tbody tr.prof-product-row.row-tone-idle td:first-child {
    box-shadow: inset 3px 0 0 #e2e8f0;
}
.prof-num {
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
}
.prof-sku-tag {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.67rem;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.1rem 0.4rem;
}
.prof-margin-bar-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.45rem;
}
.prof-margin-track {
    width: 48px;
    height: 5px;
    border-radius: 9999px;
    background: #e2e8f0;
    overflow: hidden;
    flex-shrink: 0;
}
.prof-margin-fill {
    height: 100%;
    border-radius: 9999px;
}
.prof-table-footer-bar {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 0.7rem 1.5rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    font-size: 0.8rem;
}
.prof-quick-chip {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    border-radius: 9999px;
    font-size: 0.76rem;
    font-weight: 600;
    padding: 0.25rem 0.72rem;
    cursor: pointer;
    transition: all 0.15s ease;
}
.prof-quick-chip:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.prof-quick-chip.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}
</style>
@endpush

@section('content')
@php
    $baseParams = array_filter([
        'period' => $period,
        'start_date' => $period === 'custom' ? $startDate->format('Y-m-d') : null,
        'end_date' => $period === 'custom' ? $endDate->format('Y-m-d') : null,
        'store_id' => $storeId,
        'sort' => $sortBy !== 'profit_desc' ? $sortBy : null,
        'search' => $search !== '' ? $search : null,
        'margin_filter' => ($marginFilter ?? 'all') !== 'all' ? $marginFilter : null,
    ], fn($v) => $v !== null && $v !== '');

    $rev = (float) ($overall['total_revenue'] ?? 0);
    $cogs = (float) ($overall['total_cost'] ?? 0);
    $gross = (float) ($overall['gross_profit'] ?? 0);
    $exp = (float) ($overall['total_expenses'] ?? 0);
    $net = (float) ($overall['net_profit'] ?? 0);

    $cogsShare = $rev > 0 ? round(min(100, max(0, ($cogs / $rev) * 100)), 1) : 0;
    $expShare = $rev > 0 ? round(min(100, max(0, ($exp / $rev) * 100)), 1) : 0;
    $netShare = $rev > 0 ? round(max(0, ($net / $rev) * 100), 1) : 0;

    $periodsList = [
        'today' => ['label' => "Aujourd'hui", 'icon' => 'fas fa-bolt'],
        'yesterday' => ['label' => 'Hier', 'icon' => 'fas fa-clock-rotate-left'],
        'week' => ['label' => '7 jours', 'icon' => 'fas fa-calendar-week'],
        'month' => ['label' => 'Ce mois', 'icon' => 'fas fa-calendar-days'],
        'last_month' => ['label' => 'Mois dernier', 'icon' => 'fas fa-arrow-left'],
        'year' => ['label' => 'Cette année', 'icon' => 'fas fa-calendar'],
    ];

    $uncostedRevenue = (float) ($overall['uncosted_revenue'] ?? 0);
    $uncostedSold = (int) ($overall['uncosted_products_sold'] ?? 0);
@endphp

<div class="container-fluid py-4">

    {{-- Page Header (Product / Executive Standard) --}}
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="badge bg-danger rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                        <i class="fas fa-shield-halved me-1"></i> CONFIDENTIEL ADMIN
                    </span>
                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                        <i class="fas fa-calendar-check me-1 text-danger"></i> {{ $startDate->format('d/m/Y') }} &rarr; {{ $endDate->format('d/m/Y') }}
                    </span>
                    @if($selectedStore)
                    <span class="badge bg-dark text-white rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                        <i class="fas fa-store me-1"></i> {{ $selectedStore->name }}
                    </span>
                    @else
                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                        <i class="fas fa-globe me-1"></i> Tous les magasins &amp; dépôts
                    </span>
                    @endif
                </div>
                <h1 class="brand-title">Rentabilité &amp; Marges Réelles</h1>
                <p class="brand-subtitle">Lecture claire du chiffre d'affaires, coût d'achat, charges d'exploitation et bénéfice net par produit, marque et magasin.</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('reports.profitability.costs') }}" class="btn-brand-outline position-relative">
                <i class="fas fa-coins me-1.5" style="color: var(--primary-color)"></i> Gérer les Coûts d'Achat
                @if($missingCostCount > 0)
                <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $missingCostCount }}</span>
                @endif
            </a>
            <a href="{{ route('reports.profitability.export', $baseParams) }}" class="btn-brand-primary">
                <i class="fas fa-file-csv me-1.5"></i> Exporter CSV
            </a>
        </div>
    </div>

    {{-- Cross-Module Finance Subnav --}}
    @include('partials.subnav.finance')

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    {{-- Data Quality Alert: Missing Cost Prices --}}
    @if($uncostedSold > 0 || $missingCostCount > 0)
    <div class="alert alert-warning border border-warning-subtle rounded-4 shadow-xs mb-4 p-3 px-md-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div class="d-flex align-items-start align-items-lg-center gap-3">
            <div class="rounded-3 bg-warning bg-opacity-25 text-warning-emphasis p-2.5 flex-shrink-0">
                <i class="fas fa-triangle-exclamation fs-5"></i>
            </div>
            <div>
                <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                    @if($uncostedSold > 0)
                        {{ $uncostedSold }} produit(s) vendu(s) sur cette période n'ont pas de coût d'achat ({{ currency($uncostedRevenue) }} de CA à coût 0 DH)
                    @else
                        {{ $missingCostCount }} produit(s) du catalogue n'ont pas encore de prix d'achat configuré
                    @endif
                </div>
                <div class="small text-muted">
                    Complétez les coûts d'achat pour obtenir une marge nette exacte et éviter des bénéfices surestimés.
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 flex-shrink-0">
            @if($uncostedSold > 0)
            <a href="{{ route('reports.profitability', array_merge($baseParams, ['margin_filter' => 'missing_cost'])) }}#tab-products-pane"
               class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold">
                <i class="fas fa-filter me-1"></i> Voir les ventes sans coût
            </a>
            @endif
            <a href="{{ route('reports.profitability.costs', ['status' => 'missing']) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold">
                <i class="fas fa-pen-to-square me-1"></i> Compléter les Coûts ({{ $missingCostCount }})
            </a>
        </div>
    </div>
    @endif

    {{-- Unified Period & Store Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 px-md-4">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3">
                {{-- Quick Period Pills --}}
                <div class="chips-scroll-prof">
                    @foreach($periodsList as $pKey => $pMeta)
                    <a href="{{ route('reports.profitability', array_filter([
                        'period' => $pKey,
                        'store_id' => $storeId,
                        'sort' => $sortBy !== 'profit_desc' ? $sortBy : null,
                        'search' => $search !== '' ? $search : null,
                        'margin_filter' => ($marginFilter ?? 'all') !== 'all' ? $marginFilter : null,
                    ])) }}" class="prof-filter-pill {{ $period === $pKey ? 'active' : '' }}">
                        <i class="{{ $pMeta['icon'] }} opacity-75"></i>
                        <span>{{ $pMeta['label'] }}</span>
                    </a>
                    @endforeach
                    <button type="button"
                            class="prof-filter-pill {{ $period === 'custom' ? 'active' : '' }}"
                            onclick="document.getElementById('customRangeForm').classList.toggle('d-none')">
                        <i class="fas fa-sliders"></i>
                        <span>Personnalisé</span>
                    </button>
                </div>

                {{-- Store Filter + Custom Date Range Trigger --}}
                <form method="GET" action="{{ route('reports.profitability') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="hidden" name="period" value="{{ $period }}">
                    @if($period === 'custom')
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    @endif
                    @if($sortBy !== 'profit_desc')
                    <input type="hidden" name="sort" value="{{ $sortBy }}">
                    @endif
                    @if($search !== '')
                    <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    @if(($marginFilter ?? 'all') !== 'all')
                    <input type="hidden" name="margin_filter" value="{{ $marginFilter }}">
                    @endif

                    <div class="input-group input-group-sm" style="min-width: 245px; max-width: 310px;">
                        <span class="input-group-text bg-light border-end-0 text-danger fw-semibold rounded-start-pill ps-3">
                            <i class="fas fa-store"></i>
                        </span>
                        <select name="store_id" class="form-select border-start-0 rounded-end-pill fw-semibold text-dark" onchange="this.form.submit()">
                            <option value="">Tous les magasins &amp; dépôts</option>
                            @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ (int)($storeId ?? 0) === (int)$st->id ? 'selected' : '' }}>
                                {{ $st->name }} ({{ $st->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @if($storeId)
                    <a href="{{ route('reports.profitability', array_filter([
                        'period' => $period,
                        'start_date' => $period === 'custom' ? $startDate->format('Y-m-d') : null,
                        'end_date' => $period === 'custom' ? $endDate->format('Y-m-d') : null,
                    ])) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-secondary" title="Voir tous les magasins">
                        <i class="fas fa-times me-1"></i> Tout voir
                    </a>
                    @endif
                </form>
            </div>

            {{-- Custom Date Range Inline Panel --}}
            <form id="customRangeForm"
                  method="GET"
                  action="{{ route('reports.profitability') }}"
                  class="mt-3 pt-3 border-top {{ $period === 'custom' ? '' : 'd-none' }}">
                <input type="hidden" name="period" value="custom">
                @if($storeId)
                <input type="hidden" name="store_id" value="{{ $storeId }}">
                @endif
                @if($sortBy !== 'profit_desc')
                <input type="hidden" name="sort" value="{{ $sortBy }}">
                @endif
                @if($search !== '')
                <input type="hidden" name="search" value="{{ $search }}">
                @endif
                @if(($marginFilter ?? 'all') !== 'all')
                <input type="hidden" name="margin_filter" value="{{ $marginFilter }}">
                @endif
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Date de début</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control form-control-sm rounded-3" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Date de fin</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control form-control-sm rounded-3" required>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-semibold">
                            <i class="fas fa-check me-1"></i> Appliquer la période
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 4 Executive KPI Cards (ERP Product Standard) --}}
    <div class="row g-3 mb-4">
        {{-- 1. Chiffre d'Affaires --}}
        <div class="col-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="stat-kpi-label">Chiffre d'Affaires</div>
                        <div class="stat-kpi-value text-dark">{{ currency($rev) }}</div>
                        <div class="stat-kpi-sub text-muted mt-1">
                            <span class="badge bg-light text-dark border rounded-pill">{{ $overall['orders_count'] }} cmd</span>
                            <span class="ms-1">{{ number_format($overall['items_sold'], 0, ',', ' ') }} art. vendus</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-cash-register"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Marge Brute Produits --}}
        <div class="col-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="stat-kpi-label">Marge Brute Ventes</div>
                        <div class="stat-kpi-value {{ $gross >= 0 ? 'text-primary' : 'text-danger' }}">{{ currency($gross) }}</div>
                        <div class="stat-kpi-sub text-muted mt-1">
                            <span class="badge bg-primary-subtle text-primary rounded-pill fw-bold">{{ $overall['gross_margin'] }}% marge</span>
                            <span class="ms-1">Coût: {{ currency($cogs) }}</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-percent"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Dépenses & Charges --}}
        <div class="col-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="stat-kpi-label">Dépenses &amp; Charges</div>
                        <div class="stat-kpi-value text-danger">-{{ currency($exp) }}</div>
                        <div class="stat-kpi-sub text-muted mt-1">
                            <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $expShare }}% du CA</span>
                            <a href="{{ route('expenses.index', ['period' => $period]) }}" class="text-decoration-none text-secondary ms-1 fw-semibold">Détails &rarr;</a>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Bénéfice Net Réel Global --}}
        <div class="col-6 col-xl-3">
            <div class="stat-kpi-card border-{{ $net >= 0 ? 'success' : 'danger' }} border-opacity-25">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="stat-kpi-label">Bénéfice Net Réel Global</div>
                        <div class="stat-kpi-value {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $net >= 0 ? '+' : '' }}{{ currency($net) }}
                        </div>
                        <div class="stat-kpi-sub text-muted mt-1">
                            <span class="badge {{ $net >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill fw-bold">
                                {{ $overall['margin_percentage'] }}% net
                            </span>
                            <span class="ms-1">{{ currency($overall['avg_profit_per_order']) }}/cmd</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon {{ $net >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Clear Visual P&L Waterfall Equation Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 px-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fas fa-diagram-project text-danger me-2"></i>Équation P&amp;L en Direct (Comment est calculé votre bénéfice)
                    </h6>
                    <span class="text-muted small">Chaque dirham encaissé réparti entre coût marchandise, frais d'exploitation et profit net.</span>
                </div>
                <div class="d-flex align-items-center gap-2 small">
                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                        Coeff. Markup moyen : <strong>+{{ $overall['markup_percentage'] }}%</strong>
                    </span>
                </div>
            </div>

            <div class="pnl-waterfall-grid mb-3">
                <div class="pnl-step-box">
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.68rem;">1. Chiffre d'Affaires</div>
                    <div class="fs-5 fw-bold text-dark mt-1">{{ currency($rev) }}</div>
                    <div class="small text-muted">100% des ventes</div>
                </div>

                <div class="pnl-operator">&minus;</div>

                <div class="pnl-step-box">
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.68rem;">2. Coût Marchandises</div>
                    <div class="fs-5 fw-bold text-warning-emphasis mt-1">{{ currency($cogs) }}</div>
                    <div class="small text-muted">{{ $cogsShare }}% du CA</div>
                </div>

                <div class="pnl-operator">=</div>

                <div class="pnl-step-box is-gross">
                    <div class="text-uppercase text-primary fw-bold" style="font-size: 0.68rem;">3. Marge Brute</div>
                    <div class="fs-5 fw-bold text-primary mt-1">{{ currency($gross) }}</div>
                    <div class="small text-primary">{{ $overall['gross_margin'] }}% de marge brute</div>
                </div>

                <div class="pnl-operator">&minus;</div>

                <div class="pnl-step-box">
                    <div class="text-uppercase text-danger fw-bold" style="font-size: 0.68rem;">4. Charges &amp; Dépenses</div>
                    <div class="fs-5 fw-bold text-danger mt-1">{{ currency($exp) }}</div>
                    <div class="small text-muted">{{ $expShare }}% du CA</div>
                </div>

                <div class="pnl-operator">=</div>

                <div class="pnl-step-box {{ $net >= 0 ? 'is-net-pos' : 'is-net-neg' }}">
                    <div class="text-uppercase {{ $net >= 0 ? 'text-success' : 'text-danger' }} fw-bold" style="font-size: 0.68rem;">5. Bénéfice Net Réel</div>
                    <div class="fs-5 fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }} mt-1">
                        {{ $net >= 0 ? '+' : '' }}{{ currency($net) }}
                    </div>
                    <div class="small {{ $net >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">{{ $overall['margin_percentage'] }}% de rentabilité nette</div>
                </div>
            </div>

            @if($rev > 0)
            <div>
                <div class="progress rounded-pill" style="height: 10px; background: #e2e8f0;">
                    <div class="progress-bar bg-warning" style="width: {{ $cogsShare }}%" title="Coût d'achat : {{ $cogsShare }}%"></div>
                    <div class="progress-bar bg-danger" style="width: {{ min($expShare, max(0, 100 - $cogsShare)) }}%" title="Charges : {{ $expShare }}%"></div>
                    @if($net > 0)
                    <div class="progress-bar bg-success" style="width: {{ min($netShare, max(0, 100 - $cogsShare - $expShare)) }}%" title="Bénéfice Net : {{ $netShare }}%"></div>
                    @endif
                </div>
                <div class="d-flex flex-wrap gap-3 mt-2 small text-muted">
                    <span><i class="fas fa-square text-warning me-1"></i> Coût achat ({{ $cogsShare }}%)</span>
                    <span><i class="fas fa-square text-danger me-1"></i> Charges ({{ $expShare }}%)</span>
                    <span><i class="fas fa-square text-success me-1"></i> Profit net ({{ $overall['margin_percentage'] }}%)</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Break-Even & Recurring Burn Rate Banner --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 px-md-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                            <i class="fas fa-bullseye me-1 text-warning"></i> SEUIL DE RENTABILITÉ
                        </span>
                        <span class="text-muted small fw-semibold">Couverture des Frais Généraux</span>
                    </div>
                    @if(($overall['monthly_burn_rate'] ?? 0) > 0)
                    <div class="small text-secondary mt-1">
                        Charges fixes mensuelles : <strong class="text-dark">{{ currency($overall['monthly_burn_rate']) }}</strong>
                        &middot; Point mort estimé : <strong class="text-primary">{{ currency($overall['break_even_revenue']) }} de CA</strong>
                    </div>
                    @else
                    <div class="small text-muted mt-1">
                        Aucune charge fixe mensuelle configurée pour calculer votre point mort automatique.
                        <a href="{{ route('expenses.index', ['tab' => 'recurring']) }}" class="text-danger fw-semibold text-decoration-none ms-1">Configurer &rarr;</a>
                    </div>
                    @endif
                </div>
                <div class="col-lg-7">
                    @if(($overall['monthly_burn_rate'] ?? 0) > 0)
                    <div class="d-flex justify-content-between align-items-center small mb-1.5">
                        <span class="fw-semibold text-dark">
                            Marge brute générée : {{ currency($gross) }} / {{ currency($overall['monthly_burn_rate']) }}
                        </span>
                        @if($overall['break_even_achieved'])
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold">
                            <i class="fas fa-check-circle me-1"></i> Seuil atteint (+{{ currency($gross - $overall['monthly_burn_rate']) }})
                        </span>
                        @else
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill fw-bold">
                            Reste {{ currency($overall['remaining_margin_to_break_even']) }} de marge à générer ({{ $overall['break_even_progress'] }}%)
                        </span>
                        @endif
                    </div>
                    <div class="progress rounded-pill" style="height: 10px; background: #f1f5f9;">
                        <div class="progress-bar {{ $overall['break_even_achieved'] ? 'bg-success' : 'bg-warning' }}"
                             style="width: {{ $overall['break_even_progress'] }}%"></div>
                    </div>
                    @else
                    <div class="d-flex justify-content-lg-end">
                        <a href="{{ route('expenses.index', ['tab' => 'recurring']) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                            <i class="fas fa-repeat me-1"></i> Définir mes charges fixes (Loyer, Salaires...)
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Timeline Chart --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fas fa-chart-area text-danger me-2"></i>Évolution Quotidienne / Mensuelle
                </h6>
                <span class="text-muted small">Comparez le chiffre d'affaires, les coûts d'achat, les dépenses et le bénéfice net</span>
            </div>
            <div class="d-flex flex-wrap gap-1.5" id="chartModeGroup">
                <button type="button" class="chart-mode-btn active" data-chart-mode="all">Tout voir</button>
                <button type="button" class="chart-mode-btn" data-chart-mode="ca_profit">CA vs Bénéfice Net</button>
                <button type="button" class="chart-mode-btn" data-chart-mode="costs">Coûts &amp; Charges</button>
            </div>
        </div>
        <div class="card-body p-3 px-md-4">
            <div style="position: relative; height: 295px;">
                <canvas id="profitTimelineChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Detailed Drilldown Tabs --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3 px-md-4">
            <ul class="nav prof-tabs-bar" id="profitabilityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-products-btn" data-bs-toggle="tab" data-bs-target="#tab-products-pane" type="button" role="tab">
                        <i class="fas fa-box-open me-1.5"></i> Produits <span class="badge bg-light text-dark border ms-1" id="profProductsCountBadge">{{ $productBenefits->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-brands-btn" data-bs-toggle="tab" data-bs-target="#tab-brands-pane" type="button" role="tab">
                        <i class="fas fa-tags me-1.5"></i> Par Marque <span class="badge bg-light text-dark border ms-1">{{ $brandBenefits->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-categories-btn" data-bs-toggle="tab" data-bs-target="#tab-categories-pane" type="button" role="tab">
                        <i class="fas fa-layer-group me-1.5"></i> Par Catégorie <span class="badge bg-light text-dark border ms-1">{{ $categoryBenefits->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-expenses-btn" data-bs-toggle="tab" data-bs-target="#tab-expenses-pane" type="button" role="tab">
                        <i class="fas fa-receipt me-1.5"></i> Dépenses &amp; Charges <span class="badge bg-light text-dark border ms-1">{{ $expenseBenefits->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="profitabilityTabsContent">

            {{-- TAB 1: ALL PRODUCTS WITH INSTANT ZERO-REFRESH SEARCH, FILTER & SORT --}}
            <div class="tab-pane fade show active" id="tab-products-pane" role="tabpanel">
                @php
                    $soldProductsCount = $productBenefits->where('is_sold', true)->count();
                    $missingCostProdCount = $productBenefits->where('has_cost', false)->count();
                    $renderRows = $pagedProducts ?? $productBenefits;
                    $curPg = $currentPage ?? 1;
                    $ppVal = $perPage ?? 25;
                    $totCount = $productBenefits->count();
                    $lastPg = $lastPage ?? max(1, (int) ceil($totCount / max(1, $ppVal)));
                    $rangeStart = $totCount > 0 ? (($curPg - 1) * $ppVal) + 1 : 0;
                    $rangeEnd = min($totCount, $curPg * $ppVal);
                @endphp
                <div class="p-3 px-md-4 bg-light bg-opacity-50 border-bottom">
                    <form id="profLiveFilterForm" method="GET" action="{{ route('reports.profitability') }}" class="row g-2 align-items-center" onsubmit="return false;">
                        <div class="col-12 col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text"
                                       id="profProductSearch"
                                       name="search"
                                       value="{{ $search }}"
                                       autocomplete="off"
                                       class="form-control border-start-0 border-end-0"
                                       placeholder="Recherche instantanée : nom, SKU, marque, catégorie...">
                                <button type="button"
                                        id="profClearSearchBtn"
                                        class="btn btn-white bg-white border border-start-0 text-muted {{ $search === '' ? 'd-none' : '' }}"
                                        title="Effacer la recherche">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <select id="profMarginFilter" name="margin_filter" class="form-select form-select-sm">
                                <option value="all" {{ ($marginFilter ?? 'all') === 'all' ? 'selected' : '' }}>Tous les produits ({{ $productBenefits->count() }})</option>
                                <option value="sold_only" {{ ($marginFilter ?? 'all') === 'sold_only' ? 'selected' : '' }}>Vendus sur la période ({{ $soldProductsCount }})</option>
                                <option value="missing_cost" {{ ($marginFilter ?? 'all') === 'missing_cost' ? 'selected' : '' }}>Coût manquant - 0 DH ({{ $missingCostProdCount }})</option>
                                <option value="negative" {{ ($marginFilter ?? 'all') === 'negative' ? 'selected' : '' }}>Marge négative (À perte)</option>
                                <option value="low" {{ ($marginFilter ?? 'all') === 'low' ? 'selected' : '' }}>Marge faible (&lt; 15%)</option>
                                <option value="healthy" {{ ($marginFilter ?? 'all') === 'healthy' ? 'selected' : '' }}>Marge saine (&ge; 15%)</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <select id="profSortSelect" name="sort" class="form-select form-select-sm">
                                <option value="profit_desc" {{ $sortBy === 'profit_desc' ? 'selected' : '' }}>Trier : Plus rentables ↓</option>
                                <option value="profit_asc" {{ $sortBy === 'profit_asc' ? 'selected' : '' }}>Trier : Moins rentables ↑</option>
                                <option value="margin_desc" {{ $sortBy === 'margin_desc' ? 'selected' : '' }}>Trier : Marge % la plus haute ↓</option>
                                <option value="margin_asc" {{ $sortBy === 'margin_asc' ? 'selected' : '' }}>Trier : Marge % la plus basse ↑</option>
                                <option value="revenue_desc" {{ $sortBy === 'revenue_desc' ? 'selected' : '' }}>Trier : Chiffre d'affaires ↓</option>
                                <option value="quantity_desc" {{ $sortBy === 'quantity_desc' ? 'selected' : '' }}>Trier : Quantités vendues ↓</option>
                                <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Trier : Nom A &rarr; Z</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-1 d-flex justify-content-end">
                            <button type="button"
                                    id="profResetFilterBtn"
                                    class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 w-100 {{ ($search === '' && ($marginFilter ?? 'all') === 'all' && $sortBy === 'profit_desc') ? 'd-none' : '' }}"
                                    title="Réinitialiser les filtres">
                                <i class="fas fa-rotate-left"></i>
                            </button>
                        </div>
                    </form>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2.5">
                        <div class="d-flex flex-wrap align-items-center gap-1.5">
                            <button type="button" class="prof-quick-chip {{ ($marginFilter ?? 'all') === 'all' ? 'active' : '' }}" data-quick-margin="all">
                                Tous ({{ $productBenefits->count() }})
                            </button>
                            <button type="button" class="prof-quick-chip {{ ($marginFilter ?? 'all') === 'sold_only' ? 'active' : '' }}" data-quick-margin="sold_only">
                                Vendus ({{ $soldProductsCount }})
                            </button>
                            <button type="button" class="prof-quick-chip {{ ($marginFilter ?? 'all') === 'missing_cost' ? 'active' : '' }}" data-quick-margin="missing_cost">
                                Coût manquant ({{ $missingCostProdCount }})
                            </button>
                        </div>
                        <div class="small text-muted" id="profFilterStatusText">
                            Affichage de <strong>{{ $rangeStart }}&ndash;{{ $rangeEnd }}</strong> sur <strong>{{ number_format($totCount, 0, ',', ' ') }}</strong> produit(s)
                        </div>
                    </div>
                </div>

                @if($totCount > 0)
                {{-- Desktop Pro Paginated Table --}}
                <div class="table-responsive prof-table-scroll d-none d-md-block">
                    <table class="table prof-table-pro align-middle mb-0" id="profProductsTable">
                        <thead>
                            <tr>
                                <th class="ps-4 is-sortable {{ $sortBy === 'name_asc' ? 'sort-active' : '' }}" data-sort-col="name_asc" style="min-width: 280px;">
                                    Produit <i class="fas fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="text-center is-sortable {{ $sortBy === 'quantity_desc' ? 'sort-active' : '' }}" data-sort-col="quantity_desc">
                                    Qté <i class="fas fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="text-end">PV Unitaire</th>
                                <th class="text-end">Coût d'Achat</th>
                                <th class="text-end is-sortable {{ $sortBy === 'revenue_desc' ? 'sort-active' : '' }}" data-sort-col="revenue_desc">
                                    CA Total <i class="fas fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="text-end">Coût Total</th>
                                <th class="text-end is-sortable {{ in_array($sortBy, ['profit_desc', 'profit_asc']) ? 'sort-active' : '' }}" data-sort-col="profit_toggle">
                                    Bénéfice Net <i class="fas fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="pe-4 text-end is-sortable {{ in_array($sortBy, ['margin_desc', 'margin_asc']) ? 'sort-active' : '' }}" data-sort-col="margin_toggle">
                                    Marge Brute <i class="fas fa-sort ms-1 opacity-50"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="profProductsTbody">
                            @foreach($renderRows as $item)
                            @php
                                $imgSrc = null;
                                if ($item->product_image) {
                                    $imgSrc = \Illuminate\Support\Str::startsWith($item->product_image, ['http://', 'https://', 'images/', '/'])
                                        ? asset(ltrim($item->product_image, '/'))
                                        : asset('storage/' . $item->product_image);
                                }
                                $isSold = (bool) ($item->is_sold ?? ($item->quantity_sold > 0));
                                $isNeg = $item->has_cost && ($isSold ? $item->net_profit < 0 : $item->unit_margin < 0);
                                $isLow = $item->has_cost && !$isNeg && $item->margin_percentage < 15;
                                $rowTone = !$item->has_cost ? 'row-tone-missing' : ($isNeg ? 'row-tone-neg' : ($isSold ? 'row-tone-sold' : 'row-tone-idle'));
                                $barWidth = max(0, min(100, (float) $item->margin_percentage));
                                $barColor = $isNeg ? 'bg-danger' : ($isLow ? 'bg-warning' : 'bg-success');
                            @endphp
                            <tr class="prof-product-row {{ $rowTone }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2.5">
                                        @if($imgSrc)
                                        <img src="{{ $imgSrc }}"
                                             alt="{{ $item->product_name }}"
                                             loading="lazy"
                                             class="prod-thumb-prof shadow-xs"
                                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                        <div class="prod-thumb-placeholder d-none"><i class="fas fa-box"></i></div>
                                        @else
                                        <div class="prod-thumb-placeholder"><i class="fas fa-box"></i></div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 280px;" title="{{ $item->product_name }}">
                                                {{ $item->product_name }}
                                            </div>
                                            <div class="small text-muted d-flex flex-wrap align-items-center gap-1.5 mt-0.5">
                                                @if($item->product_sku)
                                                <span class="prof-sku-tag">{{ $item->product_sku }}</span>
                                                @endif
                                                @if($item->brand_name)
                                                <span class="fw-medium text-secondary">{{ $item->brand_name }}</span>
                                                @endif
                                                @if($item->category_name)
                                                <span>&middot; {{ $item->category_name }}</span>
                                                @endif
                                                @if(isset($item->catalog_stock))
                                                <span class="badge rounded-pill {{ $item->catalog_stock > 0 ? 'bg-light text-secondary border' : 'bg-danger-subtle text-danger' }}" style="font-size: 0.65rem;">
                                                    Stock: {{ $item->catalog_stock }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center prof-num">
                                    @if($isSold)
                                    <span class="badge bg-dark text-white rounded-pill fw-bold px-2.5 py-1">{{ $item->quantity_sold }}</span>
                                    @else
                                    <span class="text-muted small fw-semibold">0</span>
                                    @endif
                                </td>
                                <td class="text-end prof-num text-dark fw-semibold">{{ currency($item->avg_selling_price) }}</td>
                                <td class="text-end prof-num">
                                    <button type="button"
                                            class="cost-edit-chip js-prof-edit-cost {{ !$item->has_cost ? 'is-missing' : '' }}"
                                            data-id="{{ $item->product_id }}"
                                            data-name="{{ $item->product_name }}"
                                            data-cost="{{ (float)$item->cost_price }}"
                                            data-sell="{{ round((float)$item->avg_selling_price, 2) }}"
                                            title="Modifier le coût d'achat">
                                        @if($item->has_cost)
                                            {{ currency($item->cost_price) }} <i class="fas fa-pen ms-1 opacity-75" style="font-size: 0.66rem;"></i>
                                        @else
                                            <i class="fas fa-plus-circle me-1"></i> Définir
                                        @endif
                                    </button>
                                </td>
                                <td class="text-end prof-num fw-semibold {{ $isSold ? 'text-dark' : 'text-muted' }}">{{ currency($item->total_revenue) }}</td>
                                <td class="text-end prof-num text-muted">{{ currency($item->total_cost) }}</td>
                                <td class="text-end prof-num">
                                    @if($isSold)
                                    <span class="fw-bold {{ $isNeg ? 'text-danger' : 'text-success' }}">
                                        {{ $item->net_profit >= 0 ? '+' : '' }}{{ currency($item->net_profit) }}
                                    </span>
                                    @if($item->profit_share > 0)
                                    <div class="text-muted" style="font-size: 0.68rem;">{{ $item->profit_share }}% du total</div>
                                    @endif
                                    @elseif($item->has_cost)
                                    <span class="small fw-semibold {{ $isNeg ? 'text-danger' : 'text-secondary' }}">
                                        {{ $item->unit_margin >= 0 ? '+' : '' }}{{ currency($item->unit_margin) }}/u.
                                    </span>
                                    <div class="text-muted" style="font-size: 0.68rem;">Invendu</div>
                                    @else
                                    <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end prof-num">
                                    @if(!$item->has_cost)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5">Coût manquant</span>
                                    @else
                                    <div class="prof-margin-bar-wrap">
                                        <div class="prof-margin-track d-none d-xl-block">
                                            <div class="prof-margin-fill {{ $barColor }}" style="width: {{ $barWidth }}%"></div>
                                        </div>
                                        <span class="badge rounded-pill fw-bold {{ $isNeg ? 'bg-danger-subtle text-danger' : ($isLow ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success') }}">
                                            {{ $item->margin_percentage }}%
                                        </span>
                                    </div>
                                    @if(!$isSold)
                                    <div class="text-muted" style="font-size: 0.67rem;">catalogue</div>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards (Paginated) --}}
                <div class="d-md-none p-3 d-flex flex-column gap-2.5" id="profMobileList">
                    @foreach($renderRows as $item)
                    @php
                        $isSold = (bool) ($item->is_sold ?? ($item->quantity_sold > 0));
                        $isNeg = $item->has_cost && ($isSold ? $item->net_profit < 0 : $item->unit_margin < 0);
                    @endphp
                    <div class="prof-mobile-card prof-product-card p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                <div class="small text-muted">{{ $item->product_sku ?: 'Sans SKU' }} &middot; {{ $item->quantity_sold }} vendu(s)</div>
                            </div>
                            <span class="badge rounded-pill {{ !$item->has_cost ? 'bg-warning-subtle text-warning-emphasis' : ($isNeg ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success') }} fw-bold">
                                {{ !$item->has_cost ? 'Coût manquant' : ($item->margin_percentage . '%') }}
                            </span>
                        </div>
                        <div class="row g-2 small mb-2">
                            <div class="col-4">
                                <div class="text-muted">CA</div>
                                <div class="fw-bold text-dark">{{ currency($item->total_revenue) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted">Coût</div>
                                <div class="fw-semibold text-secondary">{{ currency($item->total_cost) }}</div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="text-muted">{{ $isSold ? 'Bénéfice' : 'Marge u.' }}</div>
                                <div class="fw-bold {{ $isNeg ? 'text-danger' : 'text-success' }}">
                                    {{ $isSold ? currency($item->net_profit) : ($item->has_cost ? currency($item->unit_margin) : '—') }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="small text-muted">PV: {{ currency($item->avg_selling_price) }}</span>
                            <button type="button"
                                    class="cost-edit-chip js-prof-edit-cost {{ !$item->has_cost ? 'is-missing' : '' }}"
                                    data-id="{{ $item->product_id }}"
                                    data-name="{{ $item->product_name }}"
                                    data-cost="{{ (float)$item->cost_price }}"
                                    data-sell="{{ round((float)$item->avg_selling_price, 2) }}">
                                <i class="fas fa-pen me-1"></i> {{ $item->has_cost ? currency($item->cost_price) : 'Définir coût' }}
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Live Filtered Summary Footer --}}
                <div class="prof-table-footer-bar" id="profTableFooterSummary">
                    <div class="text-muted">
                        <span class="fw-bold text-dark" id="profFootCount">{{ $totCount }}</span> référence(s) filtrée(s)
                        <span class="ms-2 small d-none d-lg-inline">&middot; <span class="text-success fw-semibold">Vert</span> = vendu rentable, <span class="text-warning-emphasis fw-semibold">Ambre</span> = coût manquant, <span class="text-danger fw-semibold">Rouge</span> = perte</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 prof-num">
                        <span>Qté vendue : <strong class="text-dark" id="profFootQty">{{ number_format($productBenefits->sum('quantity_sold'), 0, ',', ' ') }}</strong></span>
                        <span>CA filtré : <strong class="text-dark" id="profFootRev">{{ currency($productBenefits->sum('total_revenue')) }}</strong></span>
                        <span>Bénéfice filtré : <strong class="text-success" id="profFootProfit">{{ currency($productBenefits->sum('net_profit')) }}</strong></span>
                    </div>
                </div>

                {{-- Pro Pagination Bar (Zero-Refresh Instant Page Switching + Per-Page Selector) --}}
                <div class="prof-pagination-bar" id="profPaginationBar">
                    <div class="d-flex flex-wrap align-items-center gap-2 small text-muted">
                        <span id="profPageRangeInfo">Page <strong>{{ $curPg }}</strong> / <strong>{{ $lastPg }}</strong> ({{ $rangeStart }}&ndash;{{ $rangeEnd }} sur {{ number_format($totCount, 0, ',', ' ') }})</span>
                        <div class="d-inline-flex align-items-center gap-1.5 ms-md-2">
                            <label for="profPerPageSelect" class="small text-muted mb-0">Lignes :</label>
                            <select id="profPerPageSelect" class="form-select form-select-sm py-0.5 ps-2 pe-4" style="width: auto; font-size: 0.78rem;">
                                @foreach([15, 25, 50, 100] as $ppOpt)
                                    <option value="{{ $ppOpt }}" {{ $ppVal === $ppOpt ? 'selected' : '' }}>{{ $ppOpt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <nav aria-label="Pagination des produits" id="profPaginationNav" class="d-flex flex-wrap align-items-center gap-1">
                        <button type="button" class="prof-page-btn" data-prof-page="{{ max(1, $curPg - 1) }}" {{ $curPg <= 1 ? 'disabled' : '' }} aria-label="Précédent">&laquo;</button>
                        @php
                            $winStart = max(1, $curPg - 2);
                            $winEnd = min($lastPg, $curPg + 2);
                        @endphp
                        @if($winStart > 1)
                            <button type="button" class="prof-page-btn" data-prof-page="1">1</button>
                            @if($winStart > 2)<span class="px-1 text-muted small">&hellip;</span>@endif
                        @endif
                        @for($pg = $winStart; $pg <= $winEnd; $pg++)
                            <button type="button" class="prof-page-btn {{ $pg === $curPg ? 'active' : '' }}" data-prof-page="{{ $pg }}">{{ $pg }}</button>
                        @endfor
                        @if($winEnd < $lastPg)
                            @if($winEnd < $lastPg - 1)<span class="px-1 text-muted small">&hellip;</span>@endif
                            <button type="button" class="prof-page-btn" data-prof-page="{{ $lastPg }}">{{ $lastPg }}</button>
                        @endif
                        <button type="button" class="prof-page-btn" data-prof-page="{{ min($lastPg, $curPg + 1) }}" {{ $curPg >= $lastPg ? 'disabled' : '' }} aria-label="Suivant">&raquo;</button>
                    </nav>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-box-open fa-3x mb-2 opacity-25"></i>
                    <p class="fw-semibold mb-1">Aucun produit trouvé pour ces critères.</p>
                    <div class="small">Modifiez la période, le magasin ou les filtres de recherche.</div>
                </div>
                @endif

                {{-- Compact In-Memory Catalog JSON (~290 KB vs 9.6 MB DOM) powering zero-refresh search, sort & pagination --}}
                <script type="application/json" id="profProductsDataset">{!! json_encode($productDataset ?? [], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
                <script type="application/json" id="profPaginationInit">{!! json_encode([
                    'page' => $curPg,
                    'perPage' => $ppVal,
                    'currency' => [
                        'symbol' => setting('currency_symbol', 'DH'),
                        'decimals' => (int) (setting('currency_decimals', 2) ?? 2),
                        'decSep' => setting('decimal_separator', '.') ?? '.',
                        'thouSep' => setting('thousands_separator', ',') ?? ' ',
                        'pos' => setting('currency_position', 'after'),
                    ],
                ]) !!}</script>
            </div>

            {{-- TAB 2: BRANDS --}}
            <div class="tab-pane fade" id="tab-brands-pane" role="tabpanel">
                @if($brandBenefits->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom text-secondary text-uppercase" style="font-size: 0.71rem;">
                            <tr>
                                <th class="ps-4 py-3">Marque</th>
                                <th class="py-3 text-center">Produits</th>
                                <th class="py-3 text-center">Unités</th>
                                <th class="py-3 text-end">Chiffre d'Affaires</th>
                                <th class="py-3 text-end">Coût d'Achat</th>
                                <th class="py-3 text-end">Marge Brute</th>
                                <th class="pe-4 py-3 text-end">Taux &amp; Part</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brandBenefits as $b)
                            <tr>
                                <td class="ps-4 py-3 fw-bold text-dark">{{ $b->brand_name }}</td>
                                <td class="py-3 text-center">{{ $b->products_count }}</td>
                                <td class="py-3 text-center"><span class="badge bg-light text-dark border rounded-pill">{{ $b->items_sold }}</span></td>
                                <td class="py-3 text-end fw-semibold text-dark">{{ currency($b->total_revenue) }}</td>
                                <td class="py-3 text-end text-muted">{{ currency($b->total_cost) }}</td>
                                <td class="py-3 text-end fw-bold {{ $b->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $b->net_profit >= 0 ? '+' : '' }}{{ currency($b->net_profit) }}
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <span class="badge {{ $b->margin_percentage >= 15 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }} rounded-pill fw-bold">
                                        {{ $b->margin_percentage }}%
                                    </span>
                                    <span class="small text-muted ms-1">({{ $b->profit_share }}% part)</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted small">Aucune donnée marque sur cette période.</div>
                @endif
            </div>

            {{-- TAB 3: CATEGORIES --}}
            <div class="tab-pane fade" id="tab-categories-pane" role="tabpanel">
                @if($categoryBenefits->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom text-secondary text-uppercase" style="font-size: 0.71rem;">
                            <tr>
                                <th class="ps-4 py-3">Catégorie</th>
                                <th class="py-3 text-center">Produits</th>
                                <th class="py-3 text-center">Unités</th>
                                <th class="py-3 text-end">Chiffre d'Affaires</th>
                                <th class="py-3 text-end">Coût d'Achat</th>
                                <th class="py-3 text-end">Marge Brute</th>
                                <th class="pe-4 py-3 text-end">Taux &amp; Part</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryBenefits as $c)
                            <tr>
                                <td class="ps-4 py-3 fw-bold text-dark">{{ $c->category_name }}</td>
                                <td class="py-3 text-center">{{ $c->products_count }}</td>
                                <td class="py-3 text-center"><span class="badge bg-light text-dark border rounded-pill">{{ $c->items_sold }}</span></td>
                                <td class="py-3 text-end fw-semibold text-dark">{{ currency($c->total_revenue) }}</td>
                                <td class="py-3 text-end text-muted">{{ currency($c->total_cost) }}</td>
                                <td class="py-3 text-end fw-bold {{ $c->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $c->net_profit >= 0 ? '+' : '' }}{{ currency($c->net_profit) }}
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <span class="badge {{ $c->margin_percentage >= 15 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }} rounded-pill fw-bold">
                                        {{ $c->margin_percentage }}%
                                    </span>
                                    <span class="small text-muted ms-1">({{ $c->profit_share }}% part)</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted small">Aucune donnée catégorie sur cette période.</div>
                @endif
            </div>

            {{-- TAB 4: EXPENSES --}}
            <div class="tab-pane fade" id="tab-expenses-pane" role="tabpanel">
                <div class="p-3 px-md-4 bg-light bg-opacity-50 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Répartition des Charges d'Exploitation sur la Période</h6>
                        <span class="text-muted small">Déduites de votre marge brute pour obtenir le bénéfice net réel global</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-danger fs-6">{{ currency($overall['total_expenses']) }}</span>
                        <a href="{{ route('expenses.index', ['period' => $period]) }}" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs">
                            <i class="fas fa-receipt me-1"></i> Gérer les Dépenses
                        </a>
                    </div>
                </div>

                @if($expenseBenefits->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 35%;">Poste de Dépense</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 15%;">Écritures</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 25%;">Montant Total</th>
                                <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 25%;">Part des Charges</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenseBenefits as $eb)
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="badge {{ $eb->badge }} rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="{{ $eb->icon }} me-1"></i> {{ $eb->label }}
                                    </span>
                                </td>
                                <td class="py-3 text-center fw-semibold text-dark">{{ $eb->count }}</td>
                                <td class="py-3 text-end fw-bold text-danger">-{{ currency($eb->total_amount) }}</td>
                                <td class="pe-4 py-3 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="progress" style="width: 80px; height: 6px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $eb->share_percentage }}%"></div>
                                        </div>
                                        <span class="badge bg-light text-dark border rounded-pill fw-bold">{{ $eb->share_percentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted small">
                    <i class="fas fa-file-invoice-dollar fa-3x mb-2 opacity-25"></i>
                    <p class="mb-2 fw-semibold">Aucune dépense enregistrée sur cette période.</p>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> Enregistrer une dépense
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Consolidated Store / Warehouse Overview --}}
    @if(isset($storeOverview) && $storeOverview->isNotEmpty())
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3 px-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0 fw-bold fs-6 text-dark">
                    <i class="fas fa-warehouse text-danger me-2"></i>Synthèse Consolidée par Point de Vente &amp; Dépôt
                </h5>
                <div class="text-muted small">Cliquez sur un magasin pour filtrer tout le tableau de bord P&amp;L sur ce point de vente</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('stores.index') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-store me-1"></i> Gérer les dépôts
                </a>
                <a href="{{ route('stores.stock') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                    <i class="fas fa-cubes me-1"></i> Stock par rayon
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase" style="font-size: 0.71rem;">
                    <tr>
                        <th class="ps-4 py-3">Dépôt / Point de vente</th>
                        <th class="text-end py-3">CA Ventes</th>
                        <th class="text-end py-3">Marge Brute</th>
                        <th class="text-end py-3">Dépenses</th>
                        <th class="text-end py-3">Bénéfice Net</th>
                        <th class="text-end py-3">Stock (Coût)</th>
                        <th class="text-end py-3">Encaissements</th>
                        <th class="text-end pe-4 py-3">Arrivages</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($storeOverview as $st)
                    @php
                        $isSelectedStore = (int)($storeId ?? 0) === (int)$st->id;
                    @endphp
                    <tr class="{{ $isSelectedStore ? 'table-danger bg-opacity-10' : '' }}">
                        <td class="ps-4 py-3">
                            <a href="{{ route('reports.profitability', array_filter([
                                'period' => $period,
                                'store_id' => $isSelectedStore ? null : $st->id,
                                'start_date' => $period === 'custom' ? $startDate->format('Y-m-d') : null,
                                'end_date' => $period === 'custom' ? $endDate->format('Y-m-d') : null,
                            ])) }}" class="text-decoration-none fw-bold text-dark d-inline-flex align-items-center gap-1.5">
                                <span>{{ $st->name }}</span>
                                @if($isSelectedStore)
                                <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">Actif</span>
                                @endif
                            </a>
                            <div class="small text-muted">{{ $st->code }} &middot; {{ ucfirst($st->type) }} &middot; {{ number_format($st->stock_units, 0, ',', ' ') }} u. en stock</div>
                        </td>
                        <td class="text-end fw-semibold text-dark">{{ currency($st->period_revenue) }}</td>
                        <td class="text-end">
                            <span class="fw-semibold text-primary">{{ currency($st->period_gross_profit) }}</span>
                            <div class="small text-muted">{{ $st->margin_percentage }}%</div>
                        </td>
                        <td class="text-end text-danger fw-semibold">-{{ currency($st->period_expenses) }}</td>
                        <td class="text-end fw-bold {{ $st->period_net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $st->period_net_profit >= 0 ? '+' : '' }}{{ currency($st->period_net_profit) }}
                        </td>
                        <td class="text-end text-muted">{{ currency($st->cost_valuation) }}</td>
                        <td class="text-end text-success fw-semibold">+{{ currency($st->period_collections) }}</td>
                        <td class="text-end pe-4">
                            <span class="badge bg-light text-dark border rounded-pill">{{ $st->arrivals_count }}</span>
                            <span class="small text-muted ms-1">{{ currency($st->arrivals_landed_cost) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Quick Cost Price Modal with Live Margin Preview --}}
<div class="modal fade" id="quickCostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="quickCostForm" method="POST" action="" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header bg-dark text-white py-2.5 px-3 border-0">
                <h6 class="modal-title fw-bold mb-0 fs-6"><i class="fas fa-coins me-1.5 text-warning"></i>Prix de Revient (Coût)</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-3">
                <div class="fw-semibold text-dark small mb-2 text-truncate" id="quickCostProdName">—</div>
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Prix vente moyen :</span>
                    <strong class="text-dark" id="quickCostAvgPrice">—</strong>
                </div>
                <label class="form-label small fw-bold text-dark mb-1">Prix d'Achat Unitaire (DH)</label>
                <div class="input-group mb-2.5">
                    <input type="number" step="0.01" min="0" name="cost_price" id="quickCostInput" class="form-control rounded-start-3 fw-bold" required>
                    <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                </div>
                <div class="rounded-3 bg-light border p-2 small d-flex justify-content-between align-items-center" id="quickCostPreviewBox">
                    <span class="text-muted">Marge unitaire estimée :</span>
                    <strong id="quickCostMarginPreview" class="text-dark">—</strong>
                </div>
                <div class="form-text mt-2" style="font-size: 0.72rem;">Strictement confidentiel (Admin uniquement).</div>
            </div>
            <div class="modal-footer bg-light px-3 py-2 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold" id="quickCostSubmitBtn">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const trendsData = @json($trends);
    let currentSellingPrice = 0;

    function initProfitabilityChart(mode = 'all') {
        const canvas = document.getElementById('profitTimelineChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();

        const datasets = [];
        if (mode === 'all' || mode === 'ca_profit') {
            datasets.push({
                label: 'Bénéfice Net Réel (DH)',
                data: trendsData.profits || [],
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                borderColor: '#10b981',
                borderWidth: 1,
                borderRadius: 6,
                order: 1
            });
            datasets.push({
                type: 'line',
                label: "Chiffre d'Affaires (DH)",
                data: trendsData.revenues || [],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 2.5,
                tension: 0.3,
                fill: false,
                order: 0
            });
        }
        if (mode === 'all' || mode === 'costs') {
            datasets.push({
                type: 'line',
                label: 'Coût Marchandises (DH)',
                data: trendsData.costs || [],
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2,
                borderDash: [5, 5],
                tension: 0.3,
                fill: false,
                order: 2
            });
            datasets.push({
                type: 'line',
                label: 'Dépenses & Charges (DH)',
                data: trendsData.expenses || [],
                borderColor: '#dc2626',
                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                borderWidth: 2,
                borderDash: [3, 3],
                tension: 0.3,
                fill: false,
                order: 3
            });
        }

        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: trendsData.labels || [],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, boxWidth: 8 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + Number(context.raw).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DH';
                            },
                            afterBody: function (items) {
                                if (!items.length || !trendsData.margins) return '';
                                const idx = items[0].dataIndex;
                                return 'Marge nette du jour: ' + (trendsData.margins[idx] ?? 0) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function (value) {
                                return Number(value).toLocaleString('fr-FR') + ' DH';
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    function updateQuickMarginPreview() {
        const input = document.getElementById('quickCostInput');
        const previewEl = document.getElementById('quickCostMarginPreview');
        if (!input || !previewEl) return;

        const cost = parseFloat(input.value);
        if (isNaN(cost) || cost < 0 || currentSellingPrice <= 0) {
            previewEl.textContent = '—';
            previewEl.className = 'text-dark';
            return;
        }
        const unitProfit = currentSellingPrice - cost;
        const pct = ((unitProfit / currentSellingPrice) * 100).toFixed(1);
        previewEl.textContent = (unitProfit >= 0 ? '+' : '') + unitProfit.toFixed(2) + ' DH (' + pct + '%)';
        previewEl.className = unitProfit >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
    }

    window.openQuickCostModal = function (productId, productName, currentCost, avgSellingPrice) {
        currentSellingPrice = Number(avgSellingPrice || 0);
        document.getElementById('quickCostForm').action = '/reports/profitability/products/' + productId + '/cost';
        document.getElementById('quickCostProdName').textContent = productName;
        document.getElementById('quickCostAvgPrice').textContent = currentSellingPrice.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' DH';
        document.getElementById('quickCostInput').value = currentCost > 0 ? currentCost : '';
        updateQuickMarginPreview();
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('quickCostModal'));
        modal.show();
        setTimeout(() => document.getElementById('quickCostInput')?.focus(), 180);
    };

    function normalizeText(str) {
        return (str || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function escapeHtml(str) {
        return (str == null ? '' : String(str))
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    let profDataset = [];
    let profState = {
        page: 1,
        perPage: 25,
        currency: { symbol: 'DH', decimals: 2, decSep: '.', thouSep: ',', pos: 'after' }
    };

    function fmtCurrency(val) {
        const cfg = profState.currency || {};
        const decimals = Number.isFinite(Number(cfg.decimals)) ? Number(cfg.decimals) : 2;
        const decSep = cfg.decSep != null ? String(cfg.decSep) : '.';
        const thouSep = cfg.thouSep != null ? String(cfg.thouSep) : ',';
        const symbol = cfg.symbol || 'DH';
        const num = Number(val || 0);
        const isNeg = num < 0;
        const absFixed = Math.abs(num).toFixed(decimals);
        const parts = absFixed.split('.');
        const intFormatted = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thouSep);
        const formatted = (isNeg ? '-' : '') + intFormatted + (parts.length > 1 ? decSep + parts[1] : '');
        return cfg.pos === 'before' ? (symbol + ' ' + formatted) : (formatted + ' ' + symbol);
    }

    function buildDesktopRowHtml(item) {
        const rowTone = !item.hc ? 'row-tone-missing' : (item.ng ? 'row-tone-neg' : (item.so ? 'row-tone-sold' : 'row-tone-idle'));
        const safeName = escapeHtml(item.n);
        const safeSku = escapeHtml(item.sk);
        const safeBrand = escapeHtml(item.br);
        const safeCat = escapeHtml(item.ct);
        const safeImg = escapeHtml(item.im);

        const thumbHtml = item.im
            ? `<img src="${safeImg}" alt="${safeName}" loading="lazy" class="prod-thumb-prof shadow-xs" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');"><div class="prod-thumb-placeholder d-none"><i class="fas fa-box"></i></div>`
            : `<div class="prod-thumb-placeholder"><i class="fas fa-box"></i></div>`;

        const qtyHtml = item.so
            ? `<span class="badge bg-dark text-white rounded-pill fw-bold px-2.5 py-1">${item.q}</span>`
            : `<span class="text-muted small fw-semibold">0</span>`;

        const costChipHtml = item.hc
            ? `${escapeHtml(fmtCurrency(item.cp))} <i class="fas fa-pen ms-1 opacity-75" style="font-size: 0.66rem;"></i>`
            : `<i class="fas fa-plus-circle me-1"></i> Définir`;

        let netProfitHtml = `<span class="text-muted small">&mdash;</span>`;
        if (item.so) {
            netProfitHtml = `<span class="fw-bold ${item.ng ? 'text-danger' : 'text-success'}">${item.np >= 0 ? '+' : ''}${escapeHtml(fmtCurrency(item.np))}</span>`
                + (item.sh > 0 ? `<div class="text-muted" style="font-size: 0.68rem;">${item.sh}% du total</div>` : '');
        } else if (item.hc) {
            netProfitHtml = `<span class="small fw-semibold ${item.ng ? 'text-danger' : 'text-secondary'}">${item.um >= 0 ? '+' : ''}${escapeHtml(fmtCurrency(item.um))}/u.</span><div class="text-muted" style="font-size: 0.68rem;">Invendu</div>`;
        }

        let marginHtml = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5">Coût manquant</span>`;
        if (item.hc) {
            const barWidth = Math.max(0, Math.min(100, Number(item.m || 0)));
            const barColor = item.ng ? 'bg-danger' : (item.lw ? 'bg-warning' : 'bg-success');
            const badgeTone = item.ng ? 'bg-danger-subtle text-danger' : (item.lw ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success');
            marginHtml = `<div class="prof-margin-bar-wrap"><div class="prof-margin-track d-none d-xl-block"><div class="prof-margin-fill ${barColor}" style="width: ${barWidth}%"></div></div><span class="badge rounded-pill fw-bold ${badgeTone}">${item.m}%</span></div>`
                + (!item.so ? `<div class="text-muted" style="font-size: 0.67rem;">catalogue</div>` : '');
        }

        const stockClass = item.st > 0 ? 'bg-light text-secondary border' : 'bg-danger-subtle text-danger';

        return `<tr class="prof-product-row ${rowTone}">
            <td class="ps-4">
                <div class="d-flex align-items-center gap-2.5">
                    ${thumbHtml}
                    <div class="min-w-0">
                        <div class="fw-bold text-dark text-truncate" style="max-width: 280px;" title="${safeName}">${safeName}</div>
                        <div class="small text-muted d-flex flex-wrap align-items-center gap-1.5 mt-0.5">
                            ${item.sk ? `<span class="prof-sku-tag">${safeSku}</span>` : ''}
                            ${item.br ? `<span class="fw-medium text-secondary">${safeBrand}</span>` : ''}
                            ${item.ct ? `<span>&middot; ${safeCat}</span>` : ''}
                            <span class="badge rounded-pill ${stockClass}" style="font-size: 0.65rem;">Stock: ${item.st}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="text-center prof-num">${qtyHtml}</td>
            <td class="text-end prof-num text-dark fw-semibold">${escapeHtml(fmtCurrency(item.p))}</td>
            <td class="text-end prof-num">
                <button type="button" class="cost-edit-chip js-prof-edit-cost ${!item.hc ? 'is-missing' : ''}"
                        data-id="${item.id}" data-name="${safeName}" data-cost="${item.cp}" data-sell="${item.p}" title="Modifier le coût d'achat">
                    ${costChipHtml}
                </button>
            </td>
            <td class="text-end prof-num fw-semibold ${item.so ? 'text-dark' : 'text-muted'}">${escapeHtml(fmtCurrency(item.r))}</td>
            <td class="text-end prof-num text-muted">${escapeHtml(fmtCurrency(item.tc))}</td>
            <td class="text-end prof-num">${netProfitHtml}</td>
            <td class="pe-4 text-end prof-num">${marginHtml}</td>
        </tr>`;
    }

    function buildMobileCardHtml(item) {
        const safeName = escapeHtml(item.n);
        const badgeCls = !item.hc ? 'bg-warning-subtle text-warning-emphasis' : (item.ng ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success');
        const badgeTxt = !item.hc ? 'Coût manquant' : `${item.m}%`;
        const profitTxt = item.so ? fmtCurrency(item.np) : (item.hc ? fmtCurrency(item.um) : '—');
        return `<div class="prof-mobile-card prof-product-card p-3">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div>
                    <div class="fw-bold text-dark">${safeName}</div>
                    <div class="small text-muted">${item.sk ? escapeHtml(item.sk) : 'Sans SKU'} &middot; ${item.q} vendu(s)</div>
                </div>
                <span class="badge rounded-pill ${badgeCls} fw-bold">${badgeTxt}</span>
            </div>
            <div class="row g-2 small mb-2">
                <div class="col-4"><div class="text-muted">CA</div><div class="fw-bold text-dark">${escapeHtml(fmtCurrency(item.r))}</div></div>
                <div class="col-4"><div class="text-muted">Coût</div><div class="fw-semibold text-secondary">${escapeHtml(fmtCurrency(item.tc))}</div></div>
                <div class="col-4 text-end"><div class="text-muted">${item.so ? 'Bénéfice' : 'Marge u.'}</div><div class="fw-bold ${item.ng ? 'text-danger' : 'text-success'}">${escapeHtml(profitTxt)}</div></div>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <span class="small text-muted">PV: ${escapeHtml(fmtCurrency(item.p))}</span>
                <button type="button" class="cost-edit-chip js-prof-edit-cost ${!item.hc ? 'is-missing' : ''}"
                        data-id="${item.id}" data-name="${safeName}" data-cost="${item.cp}" data-sell="${item.p}">
                    <i class="fas fa-pen me-1"></i> ${item.hc ? escapeHtml(fmtCurrency(item.cp)) : 'Définir coût'}
                </button>
            </div>
        </div>`;
    }

    function renderPaginationControls(curPage, lastPage) {
        const nav = document.getElementById('profPaginationNav');
        if (!nav) return;

        let html = `<button type="button" class="prof-page-btn" data-prof-page="${Math.max(1, curPage - 1)}" ${curPage <= 1 ? 'disabled' : ''} aria-label="Précédent">&laquo;</button>`;
        const winStart = Math.max(1, curPage - 2);
        const winEnd = Math.min(lastPage, curPage + 2);

        if (winStart > 1) {
            html += `<button type="button" class="prof-page-btn" data-prof-page="1">1</button>`;
            if (winStart > 2) html += `<span class="px-1 text-muted small">&hellip;</span>`;
        }
        for (let pg = winStart; pg <= winEnd; pg++) {
            html += `<button type="button" class="prof-page-btn ${pg === curPage ? 'active' : ''}" data-prof-page="${pg}">${pg}</button>`;
        }
        if (winEnd < lastPage) {
            if (winEnd < lastPage - 1) html += `<span class="px-1 text-muted small">&hellip;</span>`;
            html += `<button type="button" class="prof-page-btn" data-prof-page="${lastPage}">${lastPage}</button>`;
        }
        html += `<button type="button" class="prof-page-btn" data-prof-page="${Math.min(lastPage, curPage + 1)}" ${curPage >= lastPage ? 'disabled' : ''} aria-label="Suivant">&raquo;</button>`;
        nav.innerHTML = html;
    }

    function applyLiveProductFilterAndSort(options = {}) {
        const resetPage = options === true || (options && options.resetPage === true);
        const scrollToTable = Boolean(options && options.scrollToTable === true);

        const searchEl = document.getElementById('profProductSearch');
        const filterEl = document.getElementById('profMarginFilter');
        const sortEl = document.getElementById('profSortSelect');
        const perPageEl = document.getElementById('profPerPageSelect');
        const clearBtn = document.getElementById('profClearSearchBtn');
        const resetBtn = document.getElementById('profResetFilterBtn');
        const countBadge = document.getElementById('profProductsCountBadge');
        const statusText = document.getElementById('profFilterStatusText');
        const pageRangeInfo = document.getElementById('profPageRangeInfo');
        const tbody = document.getElementById('profProductsTbody');
        const mobileList = document.getElementById('profMobileList');
        const paginationBar = document.getElementById('profPaginationBar');

        if (!searchEl || !filterEl || !sortEl) return;

        const query = normalizeText(searchEl.value.trim());
        const marginMode = filterEl.value || 'all';
        const sortMode = sortEl.value || 'profit_desc';
        if (perPageEl) {
            const parsedPP = parseInt(perPageEl.value, 10);
            if ([15, 25, 50, 100].includes(parsedPP)) profState.perPage = parsedPP;
        }
        if (resetPage) profState.page = 1;

        if (clearBtn) clearBtn.classList.toggle('d-none', query === '');
        if (resetBtn) {
            resetBtn.classList.toggle('d-none', query === '' && marginMode === 'all' && sortMode === 'profit_desc');
        }

        document.querySelectorAll('.prof-quick-chip[data-quick-margin]').forEach(chip => {
            chip.classList.toggle('active', chip.dataset.quickMargin === marginMode);
        });

        const filtered = [];
        let sumQty = 0;
        let sumRev = 0;
        let sumProfit = 0;

        for (let i = 0; i < profDataset.length; i++) {
            const item = profDataset[i];
            if (query !== '' && !item._searchNorm.includes(query)) continue;
            if (marginMode === 'sold_only' && !item.so) continue;
            if (marginMode === 'missing_cost' && item.hc) continue;
            if (marginMode === 'negative' && !item.ng) continue;
            if (marginMode === 'low' && !item.lw) continue;
            if (marginMode === 'healthy' && !item.hl) continue;

            filtered.push(item);
            sumQty += item.q;
            sumRev += item.r;
            sumProfit += item.np;
        }

        filtered.sort((a, b) => {
            switch (sortMode) {
                case 'profit_asc':
                    return (a.np - b.np) || (a.um - b.um) || a._nameLower.localeCompare(b._nameLower);
                case 'margin_desc':
                    return (b.m - a.m) || (b.np - a.np) || a._nameLower.localeCompare(b._nameLower);
                case 'margin_asc':
                    return (a.m - b.m) || (a.np - b.np) || a._nameLower.localeCompare(b._nameLower);
                case 'revenue_desc':
                    return (b.r - a.r) || (b.q - a.q) || a._nameLower.localeCompare(b._nameLower);
                case 'quantity_desc':
                    return (b.q - a.q) || (b.r - a.r) || a._nameLower.localeCompare(b._nameLower);
                case 'name_asc':
                    return a._nameLower.localeCompare(b._nameLower);
                default:
                    return (b.np - a.np) || (b.q - a.q) || a._nameLower.localeCompare(b._nameLower);
            }
        });

        const totalFiltered = filtered.length;
        const lastPage = Math.max(1, Math.ceil(totalFiltered / profState.perPage));
        profState.page = Math.max(1, Math.min(profState.page, lastPage));
        const startIdx = (profState.page - 1) * profState.perPage;
        const endIdx = Math.min(totalFiltered, startIdx + profState.perPage);
        const pageItems = filtered.slice(startIdx, endIdx);

        if (tbody) {
            if (pageItems.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-search fa-2x mb-2 opacity-25"></i><div class="fw-semibold">Aucun produit ne correspond à votre recherche.</div></td></tr>';
            } else {
                tbody.innerHTML = pageItems.map(buildDesktopRowHtml).join('');
            }
        }

        if (mobileList) {
            if (pageItems.length === 0) {
                mobileList.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-search fa-2x mb-2 opacity-25"></i><div class="fw-semibold">Aucun produit ne correspond à votre recherche.</div></div>';
            } else {
                mobileList.innerHTML = pageItems.map(buildMobileCardHtml).join('');
            }
        }

        const rangeStartDisplay = totalFiltered > 0 ? startIdx + 1 : 0;
        if (countBadge) countBadge.textContent = totalFiltered;
        if (statusText) {
            statusText.innerHTML = totalFiltered > 0
                ? 'Affichage de <strong>' + rangeStartDisplay + '&ndash;' + endIdx + '</strong> sur <strong>' + totalFiltered.toLocaleString('fr-FR') + '</strong> produit(s)'
                : 'Affichage de <strong>0</strong> produit(s)';
        }
        if (pageRangeInfo) {
            pageRangeInfo.innerHTML = 'Page <strong>' + profState.page + '</strong> / <strong>' + lastPage + '</strong> (' + rangeStartDisplay + '&ndash;' + endIdx + ' sur ' + totalFiltered.toLocaleString('fr-FR') + ')';
        }
        if (paginationBar) {
            paginationBar.classList.toggle('d-none', totalFiltered === 0);
        }
        renderPaginationControls(profState.page, lastPage);

        const footCount = document.getElementById('profFootCount');
        const footQty = document.getElementById('profFootQty');
        const footRev = document.getElementById('profFootRev');
        const footProfit = document.getElementById('profFootProfit');

        if (footCount) footCount.textContent = totalFiltered;
        if (footQty) footQty.textContent = sumQty.toLocaleString('fr-FR');
        if (footRev) footRev.textContent = fmtCurrency(sumRev);
        if (footProfit) {
            footProfit.textContent = (sumProfit >= 0 ? '+' : '') + fmtCurrency(sumProfit);
            footProfit.className = sumProfit >= 0 ? 'text-success' : 'text-danger';
        }

        document.querySelectorAll('#profProductsTable thead th.is-sortable').forEach(th => {
            const col = th.dataset.sortCol;
            const active = col === sortMode
                || (col === 'profit_toggle' && (sortMode === 'profit_desc' || sortMode === 'profit_asc'))
                || (col === 'margin_toggle' && (sortMode === 'margin_desc' || sortMode === 'margin_asc'));
            th.classList.toggle('sort-active', active);
        });

        try {
            const url = new URL(window.location.href);
            if (searchEl.value.trim() !== '') url.searchParams.set('search', searchEl.value.trim());
            else url.searchParams.delete('search');

            if (marginMode !== 'all') url.searchParams.set('margin_filter', marginMode);
            else url.searchParams.delete('margin_filter');

            if (sortMode !== 'profit_desc') url.searchParams.set('sort', sortMode);
            else url.searchParams.delete('sort');

            if (profState.page > 1) url.searchParams.set('page', String(profState.page));
            else url.searchParams.delete('page');

            if (profState.perPage !== 25) url.searchParams.set('per_page', String(profState.perPage));
            else url.searchParams.delete('per_page');

            window.history.replaceState({}, '', url.toString());
        } catch (_) {}

        if (scrollToTable) {
            document.getElementById('tab-products-pane')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function bindEvents() {
        const datasetEl = document.getElementById('profProductsDataset');
        const initEl = document.getElementById('profPaginationInit');
        if (initEl) {
            try {
                const parsedInit = JSON.parse(initEl.textContent || '{}');
                if (parsedInit.page) profState.page = parseInt(parsedInit.page, 10) || 1;
                if (parsedInit.perPage) profState.perPage = parseInt(parsedInit.perPage, 10) || 25;
                if (parsedInit.currency) profState.currency = parsedInit.currency;
            } catch (_) {}
        }
        if (datasetEl) {
            try {
                const rawArr = JSON.parse(datasetEl.textContent || '[]');
                profDataset = Array.isArray(rawArr) ? rawArr.map(item => {
                    item._searchNorm = normalizeText((item.n || '') + ' ' + (item.sk || '') + ' ' + (item.br || '') + ' ' + (item.ct || ''));
                    item._nameLower = (item.n || '').toLowerCase();
                    return item;
                }) : [];
            } catch (_) {
                profDataset = [];
            }
        }

        const searchEl = document.getElementById('profProductSearch');
        const filterEl = document.getElementById('profMarginFilter');
        const sortEl = document.getElementById('profSortSelect');
        const perPageEl = document.getElementById('profPerPageSelect');
        const clearBtn = document.getElementById('profClearSearchBtn');
        const resetBtn = document.getElementById('profResetFilterBtn');
        const productsPane = document.getElementById('tab-products-pane');

        if (searchEl) {
            searchEl.addEventListener('input', () => applyLiveProductFilterAndSort({ resetPage: true }));
        }
        if (filterEl) {
            filterEl.addEventListener('change', () => applyLiveProductFilterAndSort({ resetPage: true }));
        }
        if (sortEl) {
            sortEl.addEventListener('change', () => applyLiveProductFilterAndSort({ resetPage: true }));
        }
        if (perPageEl) {
            perPageEl.addEventListener('change', () => applyLiveProductFilterAndSort({ resetPage: true }));
        }
        if (clearBtn && searchEl) {
            clearBtn.addEventListener('click', function () {
                searchEl.value = '';
                applyLiveProductFilterAndSort({ resetPage: true });
                searchEl.focus();
            });
        }
        if (resetBtn && searchEl && filterEl && sortEl) {
            resetBtn.addEventListener('click', function () {
                searchEl.value = '';
                filterEl.value = 'all';
                sortEl.value = 'profit_desc';
                applyLiveProductFilterAndSort({ resetPage: true });
            });
        }
        document.querySelectorAll('.prof-quick-chip[data-quick-margin]').forEach(chip => {
            chip.addEventListener('click', function () {
                if (filterEl) {
                    filterEl.value = this.dataset.quickMargin;
                    applyLiveProductFilterAndSort({ resetPage: true });
                }
            });
        });

        document.querySelectorAll('#profProductsTable thead th.is-sortable').forEach(th => {
            th.addEventListener('click', function () {
                if (!sortEl) return;
                const col = this.dataset.sortCol;
                if (col === 'profit_toggle') {
                    sortEl.value = sortEl.value === 'profit_desc' ? 'profit_asc' : 'profit_desc';
                } else if (col === 'margin_toggle') {
                    sortEl.value = sortEl.value === 'margin_desc' ? 'margin_asc' : 'margin_desc';
                } else {
                    sortEl.value = col;
                }
                applyLiveProductFilterAndSort({ resetPage: true });
            });
        });

        if (productsPane) {
            productsPane.addEventListener('click', function (e) {
                const pageBtn = e.target.closest('[data-prof-page]');
                if (pageBtn && !pageBtn.disabled) {
                    const targetPage = parseInt(pageBtn.dataset.profPage, 10);
                    if (targetPage && targetPage !== profState.page) {
                        profState.page = targetPage;
                        applyLiveProductFilterAndSort({ resetPage: false, scrollToTable: true });
                    }
                    return;
                }
                const editBtn = e.target.closest('.js-prof-edit-cost');
                if (editBtn) {
                    window.openQuickCostModal(
                        parseInt(editBtn.dataset.id, 10),
                        editBtn.dataset.name || '',
                        parseFloat(editBtn.dataset.cost || '0'),
                        parseFloat(editBtn.dataset.sell || '0')
                    );
                }
            });
        }

        document.querySelectorAll('#chartModeGroup .chart-mode-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#chartModeGroup .chart-mode-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                initProfitabilityChart(this.dataset.chartMode);
            });
        });

        const costInput = document.getElementById('quickCostInput');
        if (costInput) {
            costInput.addEventListener('input', updateQuickMarginPreview);
        }

        const costForm = document.getElementById('quickCostForm');
        if (costForm) {
            costForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const btn = document.getElementById('quickCostSubmitBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Enregistrement...';
                }
                try {
                    const res = await fetch(costForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': costForm.querySelector('input[name="_token"]').value
                        },
                        body: new FormData(costForm)
                    });
                    if (res.ok) {
                        const modalEl = document.getElementById('quickCostModal');
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                        if (window.SPARouter && typeof window.SPARouter.visit === 'function') {
                            window.SPARouter.visit(window.location.href, false);
                        } else {
                            window.location.reload();
                        }
                        return;
                    }
                    costForm.submit();
                } catch (_) {
                    costForm.submit();
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = 'Enregistrer';
                    }
                }
            });
        }

        initProfitabilityChart('all');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindEvents);
    } else {
        bindEvents();
    }
})();
</script>
@endpush
@endsection
