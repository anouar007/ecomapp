@extends('layouts.app')

@section('title', 'Rapport de Rentabilité, Bénéfices & Marges (Admin)')

@push('styles')
<style>
.stat-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.35rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.06);
}
.kpi-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.period-pill {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.4rem 0.95rem;
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
}
.period-pill:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.period-pill.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}
.nav-tabs-custom .nav-link {
    font-weight: 600;
    color: #64748b;
    border: none;
    border-bottom: 2.5px solid transparent;
    padding: 0.75rem 1.25rem;
    font-size: 0.88rem;
}
.nav-tabs-custom .nav-link.active {
    color: #dc2626;
    border-bottom-color: #dc2626;
    background: transparent;
}
.prod-thumb-profit {
    width: 42px;
    height: 42px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Top Admin Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                    <i class="fas fa-lock me-1"></i> Réservé aux Administrateurs
                </span>
                <span class="text-muted small">• Analyse Financière & Rentabilité Réelle</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0">Rapport de Rentabilité, Bénéfices & Marges</h1>
            <p class="text-muted small mb-0">Calcul exact du bénéfice net (Ventes - Coûts d'achat) par produit, marque, catégorie et période de temps.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.profitability.export', ['period' => $period, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
               class="btn btn-outline-success rounded-pill px-3.5 py-2 fw-semibold shadow-xs">
                <i class="fas fa-file-excel me-1.5"></i> Exporter en Excel / CSV
            </a>
        </div>
    </div>

    <!-- Alert / Flash message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-check-circle fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($missingCostCount > 0)
    <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4 p-3" role="alert">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <i class="fas fa-triangle-exclamation text-warning fs-4"></i>
                <div>
                    <strong>{{ $missingCostCount }} produit(s) n'ont pas encore de prix de revient (coût d'achat) renseigné.</strong>
                    <div class="small text-muted">Pour ces articles, le coût d'achat est comptabilisé à 0 DH. Vous pouvez mettre à jour le coût directement depuis le tableau ci-dessous.</div>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Period Selector Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Preset Pills -->
                <div class="d-flex flex-wrap gap-1.5 align-items-center">
                    <a href="{{ route('reports.profitability', ['period' => 'today']) }}" class="period-pill {{ $period === 'today' ? 'active' : '' }}">
                        Aujourd'hui
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'yesterday']) }}" class="period-pill {{ $period === 'yesterday' ? 'active' : '' }}">
                        Hier
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'week']) }}" class="period-pill {{ $period === 'week' ? 'active' : '' }}">
                        7 Derniers Jours
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'month']) }}" class="period-pill {{ $period === 'month' ? 'active' : '' }}">
                        Ce Mois-ci
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'last_month']) }}" class="period-pill {{ $period === 'last_month' ? 'active' : '' }}">
                        Mois Dernier
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'year']) }}" class="period-pill {{ $period === 'year' ? 'active' : '' }}">
                        Cette Année ({{ date('Y') }})
                    </a>
                </div>

                <!-- Custom Date Range Form -->
                <form method="GET" action="{{ route('reports.profitability') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="period" value="custom">
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="text-muted small fw-semibold">Du :</span>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control form-control-sm rounded-3" style="width: 140px;">
                        <span class="text-muted small fw-semibold">Au :</span>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control form-control-sm rounded-3" style="width: 140px;">
                    </div>
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 fw-semibold">
                        Appliquer
                    </button>
                </form>
            </div>
            <div class="mt-2 text-muted small" style="font-size: 0.76rem;">
                <i class="far fa-calendar-check me-1"></i> Données analysées du <strong>{{ $startDate->translatedFormat('d F Y') }}</strong> au <strong>{{ $endDate->translatedFormat('d F Y') }}</strong>.
            </div>
        </div>
    </div>

    <!-- 4 Main Financial KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Chiffre d'Affaires -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Chiffre d'Affaires</span>
                    <div class="kpi-icon-wrap bg-primary-subtle text-primary">
                        <i class="fas fa-cart-shopping"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-dark mb-1">{{ currency($overall['total_revenue']) }}</div>
                <div class="small text-muted">
                    <span>{{ $overall['orders_count'] }} commande(s) • {{ $overall['items_sold'] }} article(s)</span>
                </div>
            </div>
        </div>

        <!-- 2. Coût d'Achat Total -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Coût Total des Ventes (Achats)</span>
                    <div class="kpi-icon-wrap bg-warning-subtle text-warning">
                        <i class="fas fa-truck-ramp-box"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-dark mb-1">{{ currency($overall['total_cost']) }}</div>
                <div class="small text-muted">
                    Prix de revient fournisseurs des articles vendus
                </div>
            </div>
        </div>

        <!-- 3. Bénéfice Net Réalisé (Gain) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-kpi-card" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); color: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-white-50 small fw-semibold text-uppercase" style="font-size: 0.72rem;">Bénéfice Net Réalisé</span>
                    <div class="kpi-icon-wrap bg-white text-success">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-white mb-1">{{ currency($overall['net_profit']) }}</div>
                <div class="small text-white-50">
                    Gain net réel (Chiffre d'affaires - Coûts)
                </div>
            </div>
        </div>

        <!-- 4. Taux de Marge Moyenne -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Taux de Marge Moyenne</span>
                    <div class="kpi-icon-wrap bg-success-subtle text-success">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-success mb-1">{{ $overall['margin_percentage'] }}%</div>
                <div class="small text-muted">
                    Markup : +{{ $overall['markup_percentage'] }}% sur coût d'achat
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Chart Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Évolution Temporelle des Ventes & Bénéfices</h5>
                <span class="text-muted small">Comparaison Chiffre d'Affaires vs Coût d'Achat vs Gain Net</span>
            </div>
        </div>
        <div style="height: 280px; position: relative;">
            <canvas id="profitTimelineChart"></canvas>
        </div>
    </div>

    <!-- 3 Detailed Analytical Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="border-bottom bg-white px-3 pt-2">
            <ul class="nav nav-tabs nav-tabs-custom border-0" id="profitTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#tab-products" type="button" role="tab">
                        <i class="fas fa-box me-1.5"></i> Bénéfice par Produit ({{ $productBenefits->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="brands-tab" data-bs-toggle="tab" data-bs-target="#tab-brands" type="button" role="tab">
                        <i class="fas fa-copyright me-1.5"></i> Bénéfice par Marque ({{ $brandBenefits->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#tab-categories" type="button" role="tab">
                        <i class="fas fa-tags me-1.5"></i> Bénéfice par Catégorie ({{ $categoryBenefits->count() }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="profitTabsContent">
            
            <!-- TAB 1: BÉNÉFICE PAR PRODUIT -->
            <div class="tab-pane fade show active" id="tab-products" role="tabpanel">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 30%;">Produit & Marque</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem;">Qté Vendue</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Prix Vente Moy.</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Prix de Revient (Coût)</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Total Ventes</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Total Coût</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Bénéfice Net</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Marge %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($productBenefits as $p)
                            <tr class="hover-bg-light">
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        @if($p->product_image)
                                            <img src="{{ asset('storage/' . $p->product_image) }}" alt="{{ $p->product_name }}" class="prod-thumb-profit"
                                                 onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                                        @else
                                            <div class="prod-thumb-profit bg-light d-flex align-items-center justify-content-center text-muted">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 260px;" title="{{ $p->product_name }}">
                                                {{ $p->product_name }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">
                                                <span class="font-monospace">{{ $p->product_sku }}</span>
                                                @if($p->brand_name)
                                                    <span>• {{ $p->brand_name }}</span>
                                                @endif
                                                @if($p->category_name)
                                                    <span>• {{ $p->category_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center fw-bold">{{ $p->quantity_sold }}</td>
                                <td class="py-3 text-end fw-medium small">{{ currency($p->avg_selling_price) }}</td>
                                <td class="py-3 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1.5">
                                        <span class="fw-medium small {{ $p->cost_price <= 0 ? 'text-warning' : 'text-secondary' }}">
                                            {{ currency($p->cost_price) }}
                                        </span>
                                        <button type="button" class="btn btn-sm btn-link text-primary p-0" 
                                                onclick="openQuickCostModal({{ $p->product_id }}, '{{ addslashes($p->product_name) }}', {{ (float)$p->cost_price }})"
                                                title="Modifier le prix de revient">
                                            <i class="fas fa-pencil" style="font-size: 0.7rem;"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-3 text-end fw-semibold small">{{ currency($p->total_revenue) }}</td>
                                <td class="py-3 text-end text-muted small">{{ currency($p->total_cost) }}</td>
                                <td class="py-3 text-end">
                                    <span class="fw-bold fs-6 {{ $p->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ currency($p->net_profit) }}
                                    </span>
                                </td>
                                <td class="pe-3 py-3 text-end">
                                    <span class="badge {{ $p->margin_percentage >= 30 ? 'bg-success-subtle text-success' : ($p->margin_percentage >= 15 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning') }} rounded-pill px-2.5 py-1">
                                        {{ $p->margin_percentage }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    Aucune vente enregistrée sur cette période.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: BÉNÉFICE PAR MARQUE -->
            <div class="tab-pane fade" id="tab-brands" role="tabpanel">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Marque</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem;">Articles Vendus</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Chiffre d'Affaires</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Coût d'Achat</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Bénéfice Net</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Taux Marge %</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Part du Bénéfice Global</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($brandBenefits as $b)
                            <tr class="hover-bg-light">
                                <td class="ps-3 py-3">
                                    <div class="fw-bold text-dark">{{ $b->brand_name }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">{{ $b->products_count }} référence(s) vendue(s)</div>
                                </td>
                                <td class="py-3 text-center fw-bold">{{ $b->items_sold }}</td>
                                <td class="py-3 text-end fw-semibold small">{{ currency($b->total_revenue) }}</td>
                                <td class="py-3 text-end text-muted small">{{ currency($b->total_cost) }}</td>
                                <td class="py-3 text-end">
                                    <span class="fw-bold fs-6 text-success">{{ currency($b->net_profit) }}</span>
                                </td>
                                <td class="py-3 text-end">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">
                                        {{ $b->margin_percentage }}%
                                    </span>
                                </td>
                                <td class="pe-3 py-3 text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <div class="progress" style="width: 60px; height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ min(100, $b->profit_share) }}%;"></div>
                                        </div>
                                        <span class="small fw-semibold text-dark">{{ $b->profit_share }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Aucune vente par marque disponible.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: BÉNÉFICE PAR CATÉGORIE -->
            <div class="tab-pane fade" id="tab-categories" role="tabpanel">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Catégorie</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem;">Articles Vendus</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Chiffre d'Affaires</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Coût d'Achat</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Bénéfice Net</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Marge %</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Part du Bénéfice</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($categoryBenefits as $c)
                            <tr class="hover-bg-light">
                                <td class="ps-3 py-3">
                                    <div class="fw-bold text-dark">{{ $c->category_name }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">{{ $c->products_count }} référence(s)</div>
                                </td>
                                <td class="py-3 text-center fw-bold">{{ $c->items_sold }}</td>
                                <td class="py-3 text-end fw-semibold small">{{ currency($c->total_revenue) }}</td>
                                <td class="py-3 text-end text-muted small">{{ currency($c->total_cost) }}</td>
                                <td class="py-3 text-end">
                                    <span class="fw-bold fs-6 text-success">{{ currency($c->net_profit) }}</span>
                                </td>
                                <td class="py-3 text-end">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">
                                        {{ $c->margin_percentage }}%
                                    </span>
                                </td>
                                <td class="pe-3 py-3 text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <div class="progress" style="width: 60px; height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ min(100, $c->profit_share) }}%;"></div>
                                        </div>
                                        <span class="small fw-semibold text-dark">{{ $c->profit_share }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Aucune donnée par catégorie disponible.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Quick Modal Update Cost Price -->
<div class="modal fade" id="quickCostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="quickCostForm" method="POST" action="" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-2.5 px-3 border-0">
                <h6 class="modal-title fw-bold mb-0 fs-6">Prix de Revient (Coût d'Achat)</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-3">
                <div class="text-muted small mb-2 text-truncate" id="quickCostProdName">—</div>
                <label class="form-label small fw-bold text-dark mb-1">Prix d'Achat Unitaire (DH)</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" name="cost_price" id="quickCostInput" class="form-control rounded-start-3 fw-bold" required>
                    <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                </div>
                <div class="form-text" style="font-size: 0.72rem;">Strictement confidentiel et visible par l'Admin uniquement.</div>
            </div>
            <div class="modal-footer bg-light px-3 py-2 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const trendsData = @json($trends);

const ctx = document.getElementById('profitTimelineChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: trendsData.labels,
        datasets: [
            {
                label: 'Bénéfice Net (DH)',
                data: trendsData.profits,
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                borderColor: '#10b981',
                borderWidth: 1,
                borderRadius: 6,
                order: 1
            },
            {
                type: 'line',
                label: 'Chiffre d\'Affaires (DH)',
                data: trendsData.revenues,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 2.5,
                tension: 0.3,
                fill: false,
                order: 0
            },
            {
                type: 'line',
                label: 'Coût d\'Achat (DH)',
                data: trendsData.costs,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2,
                borderDash: [5, 5],
                tension: 0.3,
                fill: false,
                order: 2
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    boxWidth: 8
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + Number(context.raw).toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' DH';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f1f5f9'
                },
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('fr-FR') + ' DH';
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

function openQuickCostModal(productId, productName, currentCost) {
    document.getElementById('quickCostForm').action = `/reports/profitability/products/${productId}/cost`;
    document.getElementById('quickCostProdName').textContent = productName;
    document.getElementById('quickCostInput').value = currentCost > 0 ? currentCost : '';
    const modal = new bootstrap.Modal(document.getElementById('quickCostModal'));
    modal.show();
}
</script>
@endpush
@endsection

