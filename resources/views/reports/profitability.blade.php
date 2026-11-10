@extends('layouts.app')

@section('title', 'Rapport de Rentabilité, Bénéfices & Marges (Admin)')

@push('styles')
<style>
/* ==========================================================================
   PROFITABILITY & BENEFIT REPORT STYLES - PRO EXECUTIVE ALIGNMENT
   ========================================================================== */
.profit-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.35rem 1.4rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.profit-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}
.kpi-icon-pill {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.chips-scroll-period {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 3px;
}
.chips-scroll-period::-webkit-scrollbar {
    display: none;
}
.profit-mobile-card {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.profit-mobile-card:active {
    transform: scale(0.995);
}
.period-pill-btn {
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
    flex-shrink: 0;
}
.period-pill-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.period-pill-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}
.nav-tabs-profit .nav-link {
    font-weight: 600;
    color: #64748b;
    border: none;
    border-bottom: 2.5px solid transparent;
    padding: 0.85rem 1.4rem;
    font-size: 0.88rem;
    transition: all 0.15s ease;
}
.nav-tabs-profit .nav-link:hover {
    color: #0f172a;
}
.nav-tabs-profit .nav-link.active {
    color: #dc2626;
    border-bottom-color: #dc2626;
    background: transparent;
}
.prod-thumb-profit-lg {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    flex-shrink: 0;
}
.fixed-profit-table {
    table-layout: fixed !important;
    width: 100% !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Top Executive Header -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-25 border border-danger border-opacity-50 text-white d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.35rem;">
                        <i class="fas fa-chart-pie text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-danger rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                <i class="fas fa-lock me-1"></i> STRICTEMENT ADMIN
                            </span>
                            <span class="text-white-50 small">• Analyse Financière & Rentabilité Réelle</span>
                        </div>
                        <h1 class="h3 fw-bold text-white mb-0">Rapport de Rentabilité, Bénéfices & Marges</h1>
                        <p class="text-white-50 small mb-0 mt-1">Calcul précis du bénéfice net réel (Ventes - Coûts d'achat - Dépenses d'exploitation) par produit, marque, catégorie et période.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('expenses.index', ['period' => $period]) }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold shadow-xs" style="font-size: 0.85rem;">
                        <i class="fas fa-file-invoice-dollar text-warning me-1.5"></i> Dépenses ({{ currency($overall['total_expenses']) }})
                    </a>
                    <a href="{{ route('reports.profitability.costs') }}" class="btn btn-danger rounded-pill px-3.5 py-2 fw-semibold shadow-sm" style="font-size: 0.85rem;">
                        <i class="fas fa-coins me-1.5"></i> Coûts d'Achat
                        @if($missingCostCount > 0)
                            <span class="badge bg-white text-danger rounded-pill ms-1">{{ $missingCostCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('reports.profitability.export', ['period' => $period, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                       class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold shadow-xs" style="font-size: 0.85rem;">
                        <i class="fas fa-file-excel text-success me-1.5"></i> Exporter CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert / Flash message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    @if($missingCostCount > 0)
    <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4 p-3" role="alert">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <i class="fas fa-triangle-exclamation text-warning fs-4"></i>
                <div>
                    <strong>{{ $missingCostCount }} produit(s) n'ont pas encore de prix de revient (coût d'achat) renseigné.</strong>
                    <div class="small text-muted">Pour ces articles, le coût d'achat est comptabilisé à 0 DH. Définissez leurs prix de revient pour des marges exactes.</div>
                </div>
            </div>
            <a href="{{ route('reports.profitability.costs', ['status' => 'missing']) }}" class="btn btn-sm btn-dark rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                <i class="fas fa-coins me-1 text-warning"></i> Gérer les {{ $missingCostCount }} coûts manquants <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <!-- Period Selector Filter Bar (Mobile-First) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <!-- Preset Horizontal Swipeable Chips -->
                <div class="chips-scroll-period">
                    <a href="{{ route('reports.profitability', ['period' => 'today']) }}" class="period-pill-btn {{ $period === 'today' ? 'active' : '' }}">
                        <i class="far fa-calendar-day"></i> Aujourd'hui
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'yesterday']) }}" class="period-pill-btn {{ $period === 'yesterday' ? 'active' : '' }}">
                        Hier
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'week']) }}" class="period-pill-btn {{ $period === 'week' ? 'active' : '' }}">
                        7 Derniers Jours
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'month']) }}" class="period-pill-btn {{ $period === 'month' ? 'active' : '' }}">
                        Ce Mois-ci
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'last_month']) }}" class="period-pill-btn {{ $period === 'last_month' ? 'active' : '' }}">
                        Mois Dernier
                    </a>
                    <a href="{{ route('reports.profitability', ['period' => 'year']) }}" class="period-pill-btn {{ $period === 'year' ? 'active' : '' }}">
                        Cette Année ({{ date('Y') }})
                    </a>
                </div>

                <!-- Custom Date Range Form -->
                <form method="GET" action="{{ route('reports.profitability') }}" class="d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap">
                    <input type="hidden" name="period" value="custom">
                    <div class="d-flex align-items-center gap-1.5 flex-grow-1">
                        <span class="text-muted small fw-semibold">Du:</span>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control form-control-sm rounded-3">
                        <span class="text-muted small fw-semibold">Au:</span>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control form-control-sm rounded-3">
                    </div>
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 fw-semibold flex-shrink-0">
                        Appliquer
                    </button>
                </form>
            </div>
            <div class="mt-2 text-muted small" style="font-size: 0.76rem;">
                <i class="far fa-calendar-check text-primary me-1"></i> Données analysées du <strong>{{ $startDate->translatedFormat('d F Y') }}</strong> au <strong>{{ $endDate->translatedFormat('d F Y') }}</strong>.
            </div>
        </div>
    </div>

    <!-- 4 Perfectly Aligned Financial KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Chiffre d'Affaires -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="profit-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Chiffre d'Affaires</span>
                        <div class="kpi-icon-pill bg-primary-subtle text-primary">
                            <i class="fas fa-cart-shopping"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ currency($overall['total_revenue']) }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    <span>{{ $overall['orders_count'] }} commande(s) • {{ $overall['items_sold'] }} article(s)</span>
                </div>
            </div>
        </div>

        <!-- 2. Coût d'Achat Marchandises (COGS) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="profit-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Coût d'Achat Ventes</span>
                        <div class="kpi-icon-pill bg-warning-subtle text-warning">
                            <i class="fas fa-truck-ramp-box"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ currency($overall['total_cost']) }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small d-flex justify-content-between" style="font-size: 0.74rem;">
                    <span>Marge Brute Ventes :</span>
                    <strong class="text-dark">{{ currency($overall['gross_profit']) }}</strong>
                </div>
            </div>
        </div>

        <!-- 3. Charges & Dépenses d'Exploitation -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="profit-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Charges & Dépenses</span>
                        <div class="kpi-icon-pill bg-danger-subtle text-danger">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-danger mb-1">{{ currency($overall['total_expenses']) }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small d-flex justify-content-between align-items-center" style="font-size: 0.74rem;">
                    <span>Frais d'exploitation période</span>
                    <a href="{{ route('expenses.index', ['period' => $period]) }}" class="text-danger fw-semibold text-decoration-none">
                        Gérer <i class="fas fa-arrow-right ms-0.5"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. Bénéfice Net Réel Global -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="profit-kpi-card" style="background: {{ $overall['net_profit'] >= 0 ? 'linear-gradient(135deg, #064e3b 0%, #065f46 100%)' : 'linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%)' }}; color: #ffffff;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Bénéfice Net Réel Global</span>
                        <div class="kpi-icon-pill bg-white {{ $overall['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fas fa-sack-dollar"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-white mb-1">{{ currency($overall['net_profit']) }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-white-50 small d-flex justify-content-between align-items-center" style="font-size: 0.74rem;">
                    <span>Taux Net : <strong>{{ $overall['margin_percentage'] }}%</strong></span>
                    <span style="font-size: 0.68rem;">(Ventes - Coûts - Dépenses)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Break-Even & Burn Rate Analysis Banner -->
    @if(isset($overall['monthly_burn_rate']) && $overall['monthly_burn_rate'] > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
        <div class="card-body p-3.5 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-lg-5">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
                            <i class="fas fa-scale-balanced"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.68rem;">SEUIL DE RENTABILITÉ & POINT MORT</span>
                                <span class="text-white-50 small">• Charges Fixes</span>
                            </div>
                            <h5 class="fw-bold text-white mb-0 fs-6">Couverture des Frais Généraux</h5>
                            <div class="text-white-50 small mt-0.5" style="font-size: 0.74rem;">
                                Vos charges fixes mensuelles (loyer, salaires, abonnements) s'élèvent à <strong>{{ currency($overall['monthly_burn_rate']) }}/mois</strong>.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="p-2.5 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between mb-1.5" style="font-size: 0.76rem;">
                            <span class="text-white-50">Couverture du point mort :</span>
                            <span class="fw-bold {{ $overall['break_even_achieved'] ? 'text-success' : 'text-warning' }}">
                                {{ $overall['break_even_progress'] }}%
                                @if($overall['break_even_achieved'])
                                    (Couvert ✓)
                                @else
                                    (En cours)
                                @endif
                            </span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 99px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar {{ $overall['break_even_achieved'] ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $overall['break_even_progress'] }}%;"></div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-1.5 text-white-50" style="font-size: 0.7rem;">
                            <span>Marge brute : <strong class="text-white">{{ currency($overall['gross_profit']) }}</strong></span>
                            @if(!$overall['break_even_achieved'])
                                <span>Reste à couvrir : <strong class="text-warning">{{ currency($overall['remaining_margin_to_break_even']) }}</strong></span>
                            @else
                                <span class="text-success fw-semibold">Frais fixes amortis !</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3 text-lg-end">
                    <div class="text-white-50 small mb-1" style="font-size: 0.74rem;">CA Point Mort Estimé :</div>
                    <div class="fs-5 fw-bold text-white mb-2">{{ currency($overall['break_even_revenue']) }}</div>
                    <a href="{{ route('expenses.index', ['tab' => 'recurring']) }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1.5 fw-semibold shadow-xs" style="font-size: 0.78rem;">
                        <i class="fas fa-repeat me-1.5 text-warning"></i> Gérer les charges fixes
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

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
            <ul class="nav nav-tabs nav-tabs-profit border-0" id="profitTabs" role="tablist">
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
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#tab-expenses" type="button" role="tab">
                        <i class="fas fa-file-invoice-dollar me-1.5 text-danger"></i> Charges & Dépenses ({{ currency($overall['total_expenses']) }})
                    </button>
                </li>
                <li class="nav-item ms-auto" role="presentation">
                    <a href="{{ route('reports.profitability.costs') }}" class="nav-link text-danger fw-bold d-flex align-items-center">
                        <i class="fas fa-coins me-1.5"></i> Gestion Complète des Coûts
                        @if($missingCostCount > 0)
                            <span class="badge bg-danger text-white rounded-pill ms-1.5" style="font-size: 0.68rem;">{{ $missingCostCount }}</span>
                        @endif
                        <i class="fas fa-arrow-right ms-1.5 small"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="profitTabsContent">
            
            <!-- TAB 1: BÉNÉFICE PAR PRODUIT -->
            <div class="tab-pane fade show active" id="tab-products" role="tabpanel">
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table align-middle mb-0 fixed-profit-table">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 30%;">Article & Marque</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 10%;">Qté Vendue</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Prix Vente Moy.</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Prix Revient</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Total Ventes</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Bénéfice Net</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Marge %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($productBenefits as $p)
                            <tr class="hover-bg-light">
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        @if($p->product_image)
                                            <img src="{{ asset('storage/' . $p->product_image) }}" alt="{{ $p->product_name }}" class="prod-thumb-profit-lg shadow-xs"
                                                 onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                        @else
                                            <div class="prod-thumb-profit-lg d-flex align-items-center justify-content-center text-muted">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark text-truncate" title="{{ $p->product_name }}">
                                                {{ $p->product_name }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">
                                                <span class="font-monospace text-secondary">{{ $p->product_sku }}</span>
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
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Aucune vente enregistrée sur cette période.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View for Products -->
                <div class="d-block d-lg-none p-3 space-y-3">
                    @forelse($productBenefits as $p)
                    <div class="profit-mobile-card p-3">
                        <div class="d-flex align-items-start gap-2.5 mb-2.5">
                            @if($p->product_image)
                                <img src="{{ asset('storage/' . $p->product_image) }}" alt="{{ $p->product_name }}" class="prod-thumb-profit-lg shadow-xs"
                                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                            @else
                                <div class="prod-thumb-profit-lg d-flex align-items-center justify-content-center text-muted">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                            <div class="min-w-0 flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5 font-monospace" style="font-size: 0.68rem;">
                                        {{ $p->quantity_sold }} vendu(s)
                                    </span>
                                    <span class="badge {{ $p->margin_percentage >= 30 ? 'bg-success-subtle text-success border border-success-subtle' : ($p->margin_percentage >= 15 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle') }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                        Marge: {{ $p->margin_percentage }}%
                                    </span>
                                </div>
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem;" title="{{ $p->product_name }}">{{ $p->product_name }}</div>
                                <div class="text-muted small" style="font-size: 0.72rem;">
                                    <span class="font-monospace">SKU: {{ $p->product_sku }}</span>
                                    @if($p->brand_name) • {{ $p->brand_name }} @endif
                                </div>
                            </div>
                        </div>

                        <!-- Financial breakdown rows -->
                        <div class="p-2.5 rounded-3 bg-light border space-y-1.5 mb-2" style="font-size: 0.76rem;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">Chiffre d'Affaires :</span>
                                <strong class="text-dark">{{ currency($p->total_revenue) }}</strong>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">Prix de Revient :</span>
                                <div class="d-flex align-items-center gap-1.5">
                                    <strong class="{{ $p->cost_price <= 0 ? 'text-warning' : 'text-secondary' }}">{{ currency($p->cost_price) }}</strong>
                                    <button type="button" class="btn btn-xs btn-link text-primary p-0" 
                                            onclick="openQuickCostModal({{ $p->product_id }}, '{{ addslashes($p->product_name) }}', {{ (float)$p->cost_price }})"
                                            title="Modifier coût">
                                        <i class="fas fa-pencil" style="font-size: 0.7rem;"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-1.5 border-top">
                                <span class="fw-semibold text-dark">Bénéfice Net :</span>
                                <span class="fw-bold fs-6 {{ $p->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ currency($p->net_profit) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted small">
                        <i class="fas fa-chart-pie fa-2x mb-2 text-secondary opacity-25"></i>
                        <p class="mb-0">Aucune vente enregistrée sur cette période.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB 2: BÉNÉFICE PAR MARQUE -->
            <div class="tab-pane fade" id="tab-brands" role="tabpanel">
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table align-middle mb-0 fixed-profit-table">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 28%;">Marque</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 12%;">Articles Vendus</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 15%;">Chiffre d'Affaires</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 15%;">Coût d'Achat</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 15%;">Bénéfice Net</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 15%;">Marge & Part</th>
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
                                <td class="pe-3 py-3 text-end">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 mb-1 d-inline-block">
                                        {{ $b->margin_percentage }}%
                                    </span>
                                    <div class="text-muted small" style="font-size: 0.68rem;">{{ $b->profit_share }}% du total</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Aucune vente par marque disponible.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View for Brands -->
                <div class="d-block d-lg-none p-3 space-y-3">
                    @forelse($brandBenefits as $b)
                    <div class="profit-mobile-card p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $b->brand_name }}</h6>
                                <span class="text-muted small" style="font-size: 0.72rem;">{{ $b->products_count }} réf. • {{ $b->items_sold }} vendus</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.74rem;">
                                {{ $b->margin_percentage }}% marge
                            </span>
                        </div>

                        <div class="p-2.5 rounded-3 bg-light border space-y-1.5 mb-2" style="font-size: 0.76rem;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">Chiffre d'Affaires :</span>
                                <strong class="text-dark">{{ currency($b->total_revenue) }}</strong>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">Coût d'Achat :</span>
                                <span class="text-muted">{{ currency($b->total_cost) }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-1.5 border-top">
                                <span class="fw-semibold text-dark">Bénéfice Net :</span>
                                <span class="fw-bold fs-6 text-success">{{ currency($b->net_profit) }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between small text-muted" style="font-size: 0.72rem;">
                            <span>Part du gain total :</span>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress" style="width: 80px; height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ min(100, $b->profit_share) }}%"></div>
                                </div>
                                <strong>{{ $b->profit_share }}%</strong>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted small">
                        <p class="mb-0">Aucune donnée par marque disponible.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB 3: BÉNÉFICE PAR CATÉGORIE -->
            <div class="tab-pane fade" id="tab-categories" role="tabpanel">
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table align-middle mb-0 fixed-profit-table">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 28%;">Catégorie</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 12%;">Articles Vendus</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 15%;">Chiffre d'Affaires</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 15%;">Coût d'Achat</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 15%;">Bénéfice Net</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 15%;">Marge & Part</th>
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
                                <td class="pe-3 py-3 text-end">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 mb-1 d-inline-block">
                                        {{ $c->margin_percentage }}%
                                    </span>
                                    <div class="text-muted small" style="font-size: 0.68rem;">{{ $c->profit_share }}% du total</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Aucune donnée par catégorie disponible.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View for Categories -->
                <div class="d-block d-lg-none p-3 space-y-3">
                    @forelse($categoryBenefits as $c)
                    <div class="profit-mobile-card p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $c->category_name }}</h6>
                                <span class="text-muted small" style="font-size: 0.72rem;">{{ $c->products_count }} réf. • {{ $c->items_sold }} vendus</span>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.74rem;">
                                {{ $c->margin_percentage }}% marge
                            </span>
                        </div>

                        <div class="p-2.5 rounded-3 bg-light border space-y-1.5 mb-2" style="font-size: 0.76rem;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">Chiffre d'Affaires :</span>
                                <strong class="text-dark">{{ currency($c->total_revenue) }}</strong>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">Coût d'Achat :</span>
                                <span class="text-muted">{{ currency($c->total_cost) }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-1.5 border-top">
                                <span class="fw-semibold text-dark">Bénéfice Net :</span>
                                <span class="fw-bold fs-6 text-success">{{ currency($c->net_profit) }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between small text-muted" style="font-size: 0.72rem;">
                            <span>Part du gain total :</span>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress" style="width: 80px; height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ min(100, $c->profit_share) }}%"></div>
                                </div>
                                <strong>{{ $c->profit_share }}%</strong>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted small">
                        <p class="mb-0">Aucune donnée par catégorie disponible.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB 4: CHARGES & DÉPENSES D'EXPLOITATION -->
            <div class="tab-pane fade" id="tab-expenses" role="tabpanel">
                <div class="p-3 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
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
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 35%;">Poste de Dépense</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 15%;">Nombre d'Écritures</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 25%;">Montant Total</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 25%;">Part des Charges</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($expenseBenefits as $eb)
                            <tr class="hover-bg-light">
                                <td class="ps-3 py-3">
                                    <span class="badge {{ $eb->badge }} rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="{{ $eb->icon }} me-1"></i> {{ $eb->label }}
                                    </span>
                                </td>
                                <td class="py-3 text-center fw-semibold text-dark">{{ $eb->count }}</td>
                                <td class="py-3 text-end fw-bold text-danger">-{{ currency($eb->total_amount) }}</td>
                                <td class="pe-3 py-3 text-end">
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
                label: 'Bénéfice Net Réel (DH)',
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
                label: 'Coût Marchandises (DH)',
                data: trendsData.costs,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2,
                borderDash: [5, 5],
                tension: 0.3,
                fill: false,
                order: 2
            },
            {
                type: 'line',
                label: 'Dépenses & Charges (DH)',
                data: trendsData.expenses || [],
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                borderDash: [3, 3],
                tension: 0.3,
                fill: false,
                order: 3
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
