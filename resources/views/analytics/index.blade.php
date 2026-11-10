@extends('layouts.app')

@section('title', 'Statistiques Avancées')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête de page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h1 class="brand-title">Statistiques & Analyse des Ventes</h1>
                <p class="brand-subtitle">Analysez les flux de commandes, performances des produits et chiffre d'affaires</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('analytics.export', ['period' => $period]) }}" class="btn-brand-outline">
                <i class="fas fa-download me-1.5 text-success"></i> <span>Exporter CSV</span>
            </a>
            <div class="d-inline-flex gap-1 bg-white border p-1 rounded-pill shadow-xs">
                <a href="{{ route('analytics.index', ['period' => 'week']) }}" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ $period == 'week' ? 'btn-danger text-white' : 'btn-light text-muted' }}">Semaine</a>
                <a href="{{ route('analytics.index', ['period' => 'month']) }}" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ $period == 'month' ? 'btn-danger text-white' : 'btn-light text-muted' }}">Mois</a>
                <a href="{{ route('analytics.index', ['period' => 'year']) }}" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ $period == 'year' ? 'btn-danger text-white' : 'btn-light text-muted' }}">Année</a>
            </div>
        </div>
    </div>

    <!-- Metric KPI Stat Cards (Product Section Standard) -->
    <div class="row g-3 mb-4">
        <!-- Chiffre d'Affaires -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Chiffre d'Affaires</div>
                        <div class="stat-kpi-value text-dark">{{ currency($analytics['sales']['total_revenue']) }}</div>
                        <div class="stat-kpi-sub text-muted">Période sélectionnée</div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commandes Totales -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Commandes Totales</div>
                        <div class="stat-kpi-value text-success">{{ number_format($analytics['sales']['total_orders']) }}</div>
                        <div class="stat-kpi-sub text-muted">Validées & traitées</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Analytics Sections (Product Standard Table Card) -->
    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <div class="brand-table-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-fire text-danger"></i>
                        <span>Produits les Plus Vendus</span>
                    </h5>
                    <span class="badge bg-light text-secondary border px-2.5 py-1">Top ventes</span>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($analytics['sales']['top_selling_products'] as $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
                            <span class="fw-semibold text-dark">{{ $product->name }}</span>
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 fw-bold">{{ $product->total_sold }} vendus</span>
                        </li>
                    @empty
                        <li class="list-group-item px-0 py-3 text-muted">Aucune donnée de vente pour cette période.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
