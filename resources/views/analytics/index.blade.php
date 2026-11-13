@extends('layouts.app')

@section('title', 'Analytique Ventes & Performance')

@push('styles')
<style>
    .sales-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.15rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .sales-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .sales-kpi-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--kpi-accent, #dc2626);
    }
    .kpi-accent-red { --kpi-accent: #dc2626; }
    .kpi-accent-emerald { --kpi-accent: #10b981; }
    .kpi-accent-indigo { --kpi-accent: #6366f1; }
    .kpi-accent-sky { --kpi-accent: #0ea5e9; }
    .kpi-accent-amber { --kpi-accent: #f59e0b; }
    .kpi-accent-violet { --kpi-accent: #8b5cf6; }

    .sales-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .sales-kpi-value {
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #0f172a;
        line-height: 1.2;
        margin-top: 0.25rem;
    }
    .sales-kpi-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .analytics-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }
    .growth-pill {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.18rem 0.48rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }
    .growth-up { background: #ecfdf5; color: #059669; }
    .growth-down { background: #fef2f2; color: #dc2626; }
    .growth-flat { background: #f1f5f9; color: #64748b; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête de page -->
    <div class="brand-header mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h1 class="brand-title">Analytique Ventes &amp; Performance</h1>
                <p class="brand-subtitle mb-0">
                    Période : <strong class="text-dark">{{ $periodLabel }}</strong>
                    <span class="text-muted">({{ \Carbon\Carbon::parse($startDateStr)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDateStr)->format('d/m/Y') }})</span>
                </p>
            </div>
        </div>
        <div class="brand-header-actions d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('analytics.export', ['period' => $period, 'start_date' => $startDateStr, 'end_date' => $endDateStr]) }}"
               class="btn-brand-outline" data-no-spa="true">
                <i class="fas fa-download me-1.5 text-success"></i> <span>Exporter CSV</span>
            </a>
        </div>
    </div>

    @include('partials.subnav.analytics')

    <!-- Barre de filtres période & dates personnalisées -->
    <div class="analytics-panel p-3 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-1.5">
                @php
                    $periods = [
                        'today' => "Aujourd'hui",
                        'week' => '7 jours',
                        'month' => '30 jours',
                        'quarter' => '3 mois',
                        'year' => '12 mois',
                    ];
                @endphp
                @foreach($periods as $pKey => $pLabel)
                    <a href="{{ route('analytics.index', ['period' => $pKey]) }}"
                       class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $period === $pKey ? 'btn-danger text-white shadow-sm' : 'btn-light text-secondary border' }}">
                        {{ $pLabel }}
                    </a>
                @endforeach
            </div>

            <form action="{{ route('analytics.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                <input type="hidden" name="period" value="custom">
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light text-muted fw-semibold">Du</span>
                    <input type="date" name="start_date" value="{{ $startDateStr }}" class="form-control form-control-sm">
                </div>
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light text-muted fw-semibold">Au</span>
                    <input type="date" name="end_date" value="{{ $endDateStr }}" class="form-control form-control-sm">
                </div>
                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 fw-semibold">
                    <i class="fas fa-filter me-1"></i> Appliquer
                </button>
            </form>
        </div>
    </div>

    @php
        $sales = $analytics['sales'];
        $customers = $analytics['customers'];
        $products = $analytics['products'];
        $inventory = $analytics['inventory'];
        $reviews = $analytics['reviews'];
        $returns = $analytics['returns'];
        $coupons = $analytics['coupons'];
    @endphp

    <!-- 6 KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Chiffre d'Affaires -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="sales-kpi-card kpi-accent-red">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="sales-kpi-label">Chiffre d'Affaires</div>
                    <div class="sales-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="sales-kpi-value">{{ currency($sales['total_revenue']) }}</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-2">
                    @if($sales['revenue_growth'] !== null)
                        <span class="growth-pill {{ $sales['revenue_growth'] >= 0 ? 'growth-up' : 'growth-down' }}">
                            <i class="fas fa-arrow-{{ $sales['revenue_growth'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ $sales['revenue_growth'] >= 0 ? '+' : '' }}{{ $sales['revenue_growth'] }}%
                        </span>
                    @else
                        <span class="growth-pill growth-flat">—</span>
                    @endif
                    <span class="text-muted" style="font-size: 0.7rem;">{{ $sales['valid_orders'] }} valides</span>
                </div>
            </div>
        </div>

        <!-- 2. Commandes Totales -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="sales-kpi-card kpi-accent-emerald">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="sales-kpi-label">Commandes</div>
                    <div class="sales-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
                <div class="sales-kpi-value text-success">{{ number_format($sales['total_orders'], 0, ',', ' ') }}</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-2">
                    @if($sales['orders_growth'] !== null)
                        <span class="growth-pill {{ $sales['orders_growth'] >= 0 ? 'growth-up' : 'growth-down' }}">
                            <i class="fas fa-arrow-{{ $sales['orders_growth'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ $sales['orders_growth'] >= 0 ? '+' : '' }}{{ $sales['orders_growth'] }}%
                        </span>
                    @else
                        <span class="growth-pill growth-flat">—</span>
                    @endif
                    <span class="text-muted" style="font-size: 0.7rem;">{{ $sales['completed_orders'] }} livrées</span>
                </div>
            </div>
        </div>

        <!-- 3. Panier Moyen -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="sales-kpi-card kpi-accent-indigo">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="sales-kpi-label">Panier Moyen</div>
                    <div class="sales-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <div class="sales-kpi-value">{{ currency($sales['average_order_value']) }}</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-2">
                    @if($sales['aov_growth'] !== null)
                        <span class="growth-pill {{ $sales['aov_growth'] >= 0 ? 'growth-up' : 'growth-down' }}">
                            <i class="fas fa-arrow-{{ $sales['aov_growth'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ $sales['aov_growth'] >= 0 ? '+' : '' }}{{ $sales['aov_growth'] }}%
                        </span>
                    @else
                        <span class="growth-pill growth-flat">—</span>
                    @endif
                    <span class="text-muted" style="font-size: 0.7rem;">{{ $sales['completion_rate'] }}% livré</span>
                </div>
            </div>
        </div>

        <!-- 4. Nouveaux Clients -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="sales-kpi-card kpi-accent-sky">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="sales-kpi-label">Nouveaux Clients</div>
                    <div class="sales-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
                <div class="sales-kpi-value">{{ number_format($customers['new_customers'], 0, ',', ' ') }}</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-2">
                    @if($customers['customers_growth_pct'] !== null)
                        <span class="growth-pill {{ $customers['customers_growth_pct'] >= 0 ? 'growth-up' : 'growth-down' }}">
                            {{ $customers['customers_growth_pct'] >= 0 ? '+' : '' }}{{ $customers['customers_growth_pct'] }}%
                        </span>
                    @else
                        <span class="growth-pill growth-flat">—</span>
                    @endif
                    <span class="text-muted" style="font-size: 0.7rem;">Total : {{ number_format($customers['total_customers'], 0, ',', ' ') }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Valeur du Stock -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="sales-kpi-card kpi-accent-amber">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="sales-kpi-label">Valeur Stock</div>
                    <div class="sales-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                </div>
                <div class="sales-kpi-value">{{ currency($inventory['total_stock_value']) }}</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-2">
                    <span class="text-muted" style="font-size: 0.7rem;">{{ number_format($inventory['total_items_in_stock'], 0, ',', ' ') }} pcs</span>
                    @if($inventory['stock_alerts'] > 0)
                        <a href="{{ route('inventory.alerts') }}" class="badge bg-warning-subtle text-warning border text-decoration-none" style="font-size: 0.68rem;">
                            {{ $inventory['stock_alerts'] }} alerte(s)
                        </a>
                    @else
                        <span class="badge bg-success-subtle text-success" style="font-size: 0.68rem;">Stock OK</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 6. Retours & Coupons -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="sales-kpi-card kpi-accent-violet">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="sales-kpi-label">Taux Retour</div>
                    <div class="sales-kpi-icon bg-light text-dark">
                        <i class="fas fa-rotate-left"></i>
                    </div>
                </div>
                <div class="sales-kpi-value">{{ $returns['return_rate'] }}%</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-2">
                    <span class="text-muted" style="font-size: 0.7rem;">{{ $returns['total_returns'] }} retour(s)</span>
                    <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                        {{ $coupons['total_usage'] }} coupon(s)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Principaux -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <div class="analytics-panel p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-chart-area text-danger"></i>
                            <span>Évolution du Chiffre d'Affaires &amp; Commandes</span>
                        </h5>
                        <small class="text-muted">Courbe quotidienne des ventes validées et volume de commandes</small>
                    </div>
                    <span class="badge bg-light text-secondary border px-2.5 py-1">{{ $sales['daily_sales']->count() }} jours</span>
                </div>
                <div style="height: 290px; position: relative;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="analytics-panel p-3 p-md-4 h-100 d-flex flex-column">
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-chart-pie text-primary"></i>
                        <span>Statuts des Commandes</span>
                    </h5>
                    <small class="text-muted">Répartition sur la période sélectionnée</small>
                </div>
                <div style="height: 210px; position: relative;" class="my-auto">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                <div class="row g-2 mt-3 pt-2 border-top text-center small">
                    <div class="col-4">
                        <div class="fw-bold text-success">{{ $sales['completed_orders'] }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Livrées</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-warning">{{ $sales['pending_orders'] }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">En cours</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-danger">{{ $sales['cancelled_orders'] }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Annulées</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Produits & Top Clients -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="analytics-panel p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-fire text-danger"></i>
                        <span>Produits les Plus Vendus</span>
                    </h5>
                    <span class="badge bg-danger-subtle text-danger border px-2.5 py-1">Top 10</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Produit</th>
                                <th class="text-center">Stock</th>
                                <th class="text-end">Vendus</th>
                                <th class="text-end">CA Généré</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $maxSold = max(1, (int) ($sales['top_selling_products']->max('total_sold') ?? 1));
                            @endphp
                            @forelse($sales['top_selling_products'] as $idx => $product)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                        @if(!empty($product->sku))
                                            <small class="text-muted font-monospace">{{ $product->sku }}</small>
                                        @endif
                                        <div class="progress mt-1" style="height: 4px; max-width: 160px;">
                                            <div class="progress-bar bg-danger" style="width: {{ round(($product->total_sold / $maxSold) * 100) }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if((int) $product->stock <= 0)
                                            <span class="badge bg-danger-subtle text-danger">Rupture</span>
                                        @elseif((int) $product->stock <= 5)
                                            <span class="badge bg-warning-subtle text-warning">{{ $product->stock }}</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">{{ $product->stock }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 fw-bold">
                                            {{ number_format($product->total_sold, 0, ',', ' ') }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">{{ currency((float) $product->total_revenue) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Aucune vente enregistrée sur cette période.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="analytics-panel p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-crown text-warning"></i>
                        <span>Meilleurs Clients</span>
                    </h5>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                        Voir tout <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($customers['top_customers'] as $customer)
                        <div class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between gap-2 border-bottom">
                            <div class="d-flex align-items-center gap-2.5 min-w-0">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 34px; height: 34px; font-size: 0.8rem;">
                                    {{ strtoupper(mb_substr($customer->name ?? 'C', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 0.88rem;">{{ $customer->name }}</div>
                                    <div class="text-muted" style="font-size: 0.73rem;">
                                        {{ $customer->orders_count ?? 0 }} cmd &bull; {{ number_format((int) ($customer->loyalty_points ?? 0)) }} pts
                                    </div>
                                </div>
                            </div>
                            <span class="fw-bold text-dark flex-shrink-0" style="font-size: 0.88rem;">
                                {{ currency((float) ($customer->total_spent ?? 0)) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-4 text-center text-muted">Aucun client enregistré.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Santé Catalogue, Avis Clients & Promotions/Retours -->
    <div class="row g-3">
        <!-- Santé Catalogue & Stock -->
        <div class="col-12 col-md-4">
            <div class="analytics-panel p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0"><i class="fas fa-warehouse text-warning me-2"></i>Santé Catalogue &amp; Stock</h6>
                    <a href="{{ route('inventory.index') }}" class="text-decoration-none small fw-semibold">Inventaire &rarr;</a>
                </div>
                <div class="d-flex flex-column gap-2.5 small">
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="text-muted">Produits actifs / Total</span>
                        <strong class="text-dark">{{ $products['active_products'] }} / {{ $products['total_products'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="text-muted">Stock faible</span>
                        <span class="badge bg-warning-subtle text-warning fw-bold">{{ $products['low_stock_products'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="text-muted">Rupture de stock</span>
                        <span class="badge bg-danger-subtle text-danger fw-bold">{{ $products['out_of_stock'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="text-muted">Unités en stock</span>
                        <strong class="text-dark">{{ number_format($inventory['total_items_in_stock'], 0, ',', ' ') }} pcs</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avis Clients -->
        <div class="col-12 col-md-4">
            <div class="analytics-panel p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0"><i class="fas fa-star text-warning me-2"></i>Avis Clients</h6>
                    <span class="badge bg-warning-subtle text-warning fw-bold">{{ $reviews['average_rating'] }} / 5 ({{ $reviews['approved_reviews'] }})</span>
                </div>
                @php
                    $approvedTotal = max(1, (int) $reviews['approved_reviews']);
                    $stars = [
                        5 => $reviews['five_star'],
                        4 => $reviews['four_star'],
                        3 => $reviews['three_star'],
                        2 => $reviews['two_star'],
                        1 => $reviews['one_star'],
                    ];
                @endphp
                <div class="d-flex flex-column gap-2">
                    @foreach($stars as $star => $count)
                        <div class="d-flex align-items-center gap-2 small">
                            <span class="text-muted fw-semibold" style="width: 28px;">{{ $star }}★</span>
                            <div class="progress flex-grow-1" style="height: 7px;">
                                <div class="progress-bar bg-warning" style="width: {{ round(($count / $approvedTotal) * 100) }}%"></div>
                            </div>
                            <span class="text-dark fw-semibold text-end" style="width: 28px;">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
                @if($reviews['pending_reviews'] > 0)
                    <div class="mt-3 pt-2 border-top small text-warning fw-semibold">
                        <i class="fas fa-clock me-1"></i> {{ $reviews['pending_reviews'] }} avis en attente de modération
                    </div>
                @endif
            </div>
        </div>

        <!-- Coupons & Retours -->
        <div class="col-12 col-md-4">
            <div class="analytics-panel p-3 p-md-4 h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-tags text-primary me-2"></i>Coupons &amp; Retours</h6>
                <div class="d-flex flex-column gap-2.5 small">
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="text-muted">Coupons actifs</span>
                        <strong class="text-dark">{{ $coupons['active_coupons'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="text-muted">Remises accordées</span>
                        <strong class="text-danger">-{{ currency($coupons['total_discount_given']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="text-muted">Top coupon</span>
                        <span class="badge bg-primary-subtle text-primary font-monospace">
                            {{ $coupons['most_used_coupon']->code ?? '—' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="text-muted">Remboursements effectués</span>
                        <strong class="text-dark">{{ currency($returns['total_refunded']) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trendCanvas = document.getElementById('salesTrendChart');
    if (trendCanvas && typeof Chart !== 'undefined') {
        const labels = @json($sales['daily_sales']->pluck('label')->values());
        const revenues = @json($sales['daily_sales']->pluck('total')->values());
        const orders = @json($sales['daily_sales']->pluck('orders')->values());

        new Chart(trendCanvas, {
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'CA (DH)',
                        data: revenues,
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'yRevenue',
                        pointRadius: 2.5,
                        pointHoverRadius: 5,
                    },
                    {
                        type: 'bar',
                        label: 'Commandes',
                        data: orders,
                        backgroundColor: 'rgba(99, 102, 241, 0.35)',
                        borderColor: '#6366f1',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'yOrders',
                        maxBarThickness: 18,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 11 } } },
                    yRevenue: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        ticks: { font: { size: 11 } }
                    },
                    yOrders: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: { stepSize: 1, font: { size: 11 } }
                    }
                }
            }
        });
    }

    const statusCanvas = document.getElementById('orderStatusChart');
    if (statusCanvas && typeof Chart !== 'undefined') {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Livrées / Terminées', 'En cours / Attente', 'Annulées'],
                datasets: [{
                    data: [
                        {{ (int) $sales['completed_orders'] }},
                        {{ (int) $sales['pending_orders'] }},
                        {{ (int) $sales['cancelled_orders'] }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14, font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
@endpush
