@extends('layouts.app')

@section('title', 'Vues & Recherches Clients - Analytics')

@push('styles')
<style>
    .analytics-page-container {
        padding-bottom: 3rem;
    }

    /* KPI Cards */
    .views-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.15rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .views-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }
    .views-kpi-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--kpi-accent, #6366f1);
    }
    .kpi-accent-purple { --kpi-accent: #6366f1; }
    .kpi-accent-blue   { --kpi-accent: #0ea5e9; }
    .kpi-accent-teal   { --kpi-accent: #14b8a6; }
    .kpi-accent-rose   { --kpi-accent: #f43f5e; }

    .views-kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .views-kpi-value {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .views-kpi-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 0.2rem;
    }

    /* Horizontal Scrollable Periods */
    .period-filter-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        white-space: nowrap;
        scrollbar-width: none;
        max-width: 100%;
    }
    .period-filter-scroll::-webkit-scrollbar {
        display: none;
    }

    .period-filter-group {
        display: inline-flex;
        flex-wrap: nowrap;
    }
    .period-filter-group .btn {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 0.75rem;
        border-radius: 7px;
        white-space: nowrap;
    }

    /* Horizontal Scrollable Tabs */
    .views-tabs-scroller {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .views-tabs-scroller::-webkit-scrollbar {
        display: none;
    }
    .views-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 0.25rem;
        flex-wrap: nowrap !important;
        white-space: nowrap !important;
        min-width: max-content;
    }
    .views-nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.75rem 1rem;
        border-radius: 10px 10px 0 0;
        position: relative;
        background: transparent;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    .views-nav-tabs .nav-link:hover {
        color: #0f172a;
        background: #f8fafc;
    }
    .views-nav-tabs .nav-link.active {
        color: #4f46e5;
        background: #ffffff;
    }
    .views-nav-tabs .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: #4f46e5;
        border-radius: 3px 3px 0 0;
    }

    /* Rank Badges */
    .rank-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.75rem;
    }
    .rank-1 { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .rank-2 { background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; }
    .rank-3 { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .rank-other { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    /* Tables in Cards */
    .views-table th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.85rem 1rem;
        vertical-align: middle;
        white-space: nowrap;
    }
    .views-table td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        font-size: 0.88rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    .views-table tr:hover td {
        background: #fbfcfe;
    }

    /* Product Avatar */
    .prod-avatar-small {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        object-fit: cover;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    /* Mobile Analytics Cards */
    .mobile-analytics-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem;
        margin-bottom: 0.65rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .mobile-analytics-card .prod-thumb {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        object-fit: cover;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Mobile Specific Tweaks */
    @media (max-width: 767.98px) {
        .views-kpi-card {
            padding: 0.75rem !important;
            border-radius: 11px;
        }
        .views-kpi-value {
            font-size: 1.3rem !important;
        }
        .views-kpi-label {
            font-size: 0.68rem !important;
        }
        .views-kpi-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.9rem !important;
            border-radius: 7px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="analytics-page-container">

    <!-- Top Header -->
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2.5 mb-3 mb-md-4">
        <div>
            <div class="d-flex align-items-center gap-1.5 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5" style="font-size: 0.72rem;">
                    <i class="fas fa-chart-line me-1"></i> Intelligence Clients
                </span>
                <span class="text-muted small" style="font-size: 0.72rem;">&bull; {{ $periodLabel }}</span>
            </div>
            <h1 class="h4 h3-md fw-bold text-dark mb-0" style="letter-spacing: -0.02em;">Vues, Recherches & Besoins Clients</h1>
            <p class="text-muted small mb-0 d-none d-sm-block">Consultez ce que recherchent vos clients, les produits et catégories les plus vus, et identifiez les opportunités de stock.</p>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2 w-100 w-md-auto justify-content-between justify-content-md-end">
            <!-- Period Selector Scrollable on Mobile -->
            <div class="period-filter-scroll flex-grow-1 flex-md-grow-0">
                <div class="btn-group period-filter-group shadow-sm bg-white p-1 rounded-3 border" role="group">
                    <a href="{{ route('analytics.views', array_merge(request()->except('period', 'start_date', 'end_date', 'p_page', 's_page'), ['period' => 'today'])) }}" 
                       class="btn {{ $period === 'today' ? 'btn-primary text-white' : 'btn-light text-muted' }}">
                       Aujourd'hui
                    </a>
                    <a href="{{ route('analytics.views', array_merge(request()->except('period', 'start_date', 'end_date', 'p_page', 's_page'), ['period' => 'yesterday'])) }}" 
                       class="btn {{ $period === 'yesterday' ? 'btn-primary text-white' : 'btn-light text-muted' }}">
                       Hier
                    </a>
                    <a href="{{ route('analytics.views', array_merge(request()->except('period', 'start_date', 'end_date', 'p_page', 's_page'), ['period' => '7days'])) }}" 
                       class="btn {{ $period === '7days' ? 'btn-primary text-white' : 'btn-light text-muted' }}">
                       7 Jours
                    </a>
                    <a href="{{ route('analytics.views', array_merge(request()->except('period', 'start_date', 'end_date', 'p_page', 's_page'), ['period' => '30days'])) }}" 
                       class="btn {{ $period === '30days' ? 'btn-primary text-white' : 'btn-light text-muted' }}">
                       30 Jours
                    </a>
                    <a href="{{ route('analytics.views', array_merge(request()->except('period', 'start_date', 'end_date', 'p_page', 's_page'), ['period' => 'month'])) }}" 
                       class="btn {{ $period === 'month' ? 'btn-primary text-white' : 'btn-light text-muted' }}">
                       Ce Mois
                    </a>
                    <a href="{{ route('analytics.views', array_merge(request()->except('period', 'start_date', 'end_date', 'p_page', 's_page'), ['period' => 'all'])) }}" 
                       class="btn {{ $period === 'all' ? 'btn-primary text-white' : 'btn-light text-muted' }}">
                       Tout
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <!-- Custom Date Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-white border shadow-sm dropdown-toggle rounded-3 py-1.5 px-2.5 fw-semibold text-muted small" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.8rem;">
                        <i class="fas fa-calendar-alt me-1 text-primary"></i> <span class="d-none d-sm-inline">Dates</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border" style="width: 290px;">
                        <form action="{{ route('analytics.views') }}" method="GET">
                            <input type="hidden" name="period" value="custom">
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted mb-1">Date début</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDateStr }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted mb-1">Date fin</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDateStr }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Appliquer le filtre</button>
                        </form>
                    </div>
                </div>

                <!-- Export Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle rounded-3 py-1.5 px-2.5 fw-semibold small shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.8rem;">
                        <i class="fas fa-download me-1"></i> <span class="d-none d-sm-inline">Exporter CSV</span><span class="d-sm-none">CSV</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                        <li><h6 class="dropdown-header small">Exporter les données</h6></li>
                        <li><a class="dropdown-item small" href="{{ route('analytics.views.export', array_merge(request()->all(), ['type' => 'products'])) }}"><i class="fas fa-box me-2 text-primary"></i> Vues Produits (CSV)</a></li>
                        <li><a class="dropdown-item small" href="{{ route('analytics.views.export', array_merge(request()->all(), ['type' => 'categories'])) }}"><i class="fas fa-folder me-2 text-teal"></i> Vues Catégories (CSV)</a></li>
                        <li><a class="dropdown-item small" href="{{ route('analytics.views.export', array_merge(request()->all(), ['type' => 'searches'])) }}"><i class="fas fa-search me-2 text-danger"></i> Mots-clés recherchés (CSV)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Summary Cards (2x2 Grid on Mobile, 4 in row on Desktop) -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <!-- Vues Produits -->
        <div class="col-6 col-xl-3">
            <div class="views-kpi-card kpi-accent-purple h-100">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <div class="views-kpi-label text-truncate">Vues Produits</div>
                    <div class="views-kpi-icon bg-indigo-subtle text-primary">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
                <div class="views-kpi-value">{{ number_format($kpi['product_views'], 0, ',', ' ') }}</div>
                <div class="text-muted small mt-1.5 d-flex align-items-center justify-content-between flex-wrap gap-1" style="font-size: 0.72rem;">
                    <span>Auj. : <strong class="text-dark">{{ $kpi['today_product_views'] }}</strong></span>
                    <span class="badge bg-light text-secondary border px-1.5 py-0.5">{{ $kpi['unique_visitors'] }} vis.</span>
                </div>
            </div>
        </div>

        <!-- Vues Catégories -->
        <div class="col-6 col-xl-3">
            <div class="views-kpi-card kpi-accent-teal h-100">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <div class="views-kpi-label text-truncate">Vues Rayons</div>
                    <div class="views-kpi-icon bg-teal-subtle text-teal">
                        <i class="fas fa-folder-tree"></i>
                    </div>
                </div>
                <div class="views-kpi-value">{{ number_format($kpi['category_views'], 0, ',', ' ') }}</div>
                <div class="text-muted small mt-1.5 text-truncate" style="font-size: 0.72rem;">
                    <span><strong class="text-dark">{{ $categoryViews->count() }}</strong> rayon(s) vu(s)</span>
                </div>
            </div>
        </div>

        <!-- Recherches Effectuées -->
        <div class="col-6 col-xl-3">
            <div class="views-kpi-card kpi-accent-blue h-100">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <div class="views-kpi-label text-truncate">Recherches</div>
                    <div class="views-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="views-kpi-value">{{ number_format($kpi['total_searches'], 0, ',', ' ') }}</div>
                <div class="text-muted small mt-1.5 d-flex align-items-center justify-content-between flex-wrap gap-1" style="font-size: 0.72rem;">
                    <span>Auj. : <strong class="text-dark">{{ $kpi['today_searches'] }}</strong></span>
                    <span class="badge bg-light text-secondary border px-1.5 py-0.5">Mots-clés</span>
                </div>
            </div>
        </div>

        <!-- Recherches Sans Résultat (Besoins Clients) -->
        <div class="col-6 col-xl-3">
            <div class="views-kpi-card kpi-accent-rose h-100">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <div class="views-kpi-label text-truncate">Sans Résultat</div>
                    <div class="views-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-search-minus"></i>
                    </div>
                </div>
                <div class="views-kpi-value text-danger">{{ number_format($kpi['zero_result_searches'], 0, ',', ' ') }}</div>
                <div class="text-danger small mt-1.5 text-truncate fw-semibold" style="font-size: 0.72rem;">
                    <i class="fas fa-exclamation-triangle me-0.5"></i> Demandes non comblées
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row: Daily View Curve & Device Breakdown -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <!-- Main Line Chart: Vues par Jour -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-2.5 p-md-3 bg-white h-100">
                <div class="mb-2.5">
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                        <i class="fas fa-chart-area text-primary me-1.5"></i>Évolution des Vues et Recherches par Jour
                    </h6>
                    <div class="d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 0.72rem;">
                        <span class="badge bg-primary-subtle text-primary border"><i class="fas fa-circle me-1" style="font-size: 7px;"></i> Vues Produits</span>
                        <span class="badge bg-teal-subtle text-teal border"><i class="fas fa-circle me-1" style="font-size: 7px;"></i> Rayons</span>
                        <span class="badge bg-info-subtle text-info border"><i class="fas fa-circle me-1" style="font-size: 7px;"></i> Recherches</span>
                    </div>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="viewsTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Doughnut Chart: Appareils des clients -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-2.5 p-md-3 bg-white h-100">
                <div class="mb-2">
                    <h6 class="fw-bold text-dark mb-0.5" style="font-size: 0.95rem;">
                        <i class="fas fa-mobile-screen-button text-info me-1.5"></i>Répartition par Appareil
                    </h6>
                    <span class="text-muted small" style="font-size: 0.72rem;">D'où vos clients consultent-ils vos produits ?</span>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="height: 160px; position: relative;">
                    <canvas id="deviceDoughnutChart"></canvas>
                </div>
                <div class="row text-center mt-2.5 pt-2 border-top g-1" style="font-size: 0.75rem;">
                    <div class="col-4 px-1">
                        <div class="text-muted text-truncate"><i class="fas fa-desktop text-primary me-0.5"></i>Desktop</div>
                        <div class="fw-bold text-dark">{{ $deviceData['desktop_pct'] }}%</div>
                    </div>
                    <div class="col-4 px-1">
                        <div class="text-muted text-truncate"><i class="fas fa-mobile-alt text-success me-0.5"></i>Mobile</div>
                        <div class="fw-bold text-dark">{{ $deviceData['mobile_pct'] }}%</div>
                    </div>
                    <div class="col-4 px-1">
                        <div class="text-muted text-truncate"><i class="fas fa-tablet-alt text-warning me-0.5"></i>Tablette</div>
                        <div class="fw-bold text-dark">{{ $deviceData['tablet_pct'] }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs (Horizontally Scrollable on Mobile) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-2.5 px-3 px-md-4 pb-0">
            <div class="views-tabs-scroller">
                <ul class="nav views-nav-tabs" id="viewsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-pane" type="button" role="tab" aria-controls="products-pane" aria-selected="true">
                            <i class="fas fa-box me-1.5 text-primary"></i> Top Vues Produits
                            <span class="badge bg-primary-subtle text-primary rounded-pill ms-1">{{ $topProducts->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-pane" type="button" role="tab" aria-controls="categories-pane" aria-selected="false">
                            <i class="fas fa-folder-tree me-1.5 text-teal"></i> Vues par Catégorie
                            <span class="badge bg-teal-subtle text-teal rounded-pill ms-1">{{ $categoryViews->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="searches-tab" data-bs-toggle="tab" data-bs-target="#searches-pane" type="button" role="tab" aria-controls="searches-pane" aria-selected="false">
                            <i class="fas fa-search me-1.5 text-info"></i> Mots & Recherches Clients
                            <span class="badge bg-info-subtle text-info rounded-pill ms-1">{{ $searchLogs->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="insights-tab" data-bs-toggle="tab" data-bs-target="#insights-pane" type="button" role="tab" aria-controls="insights-pane" aria-selected="false">
                            <i class="fas fa-lightbulb me-1.5 text-warning"></i> Besoins & Opportunités
                            @if($zeroResultSearches->count() > 0 || $lostSalesProducts->count() > 0)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $zeroResultSearches->count() + $lostSalesProducts->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity-pane" type="button" role="tab" aria-controls="activity-pane" aria-selected="false">
                            <i class="fas fa-stream me-1.5 text-secondary"></i> Flux en Direct
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="viewsTabsContent">
                
                <!-- ================= TAB 1: TOP VUES PRODUITS ================= -->
                <div class="tab-pane fade show active" id="products-pane" role="tabpanel" aria-labelledby="products-tab" tabindex="0">
                    <!-- Filters bar inside products tab -->
                    <div class="p-2.5 p-md-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <form action="{{ route('analytics.views') }}" method="GET" class="d-flex align-items-center flex-wrap gap-2 w-100 w-md-auto">
                            <input type="hidden" name="period" value="{{ $period }}">
                            @if($period === 'custom')
                                <input type="hidden" name="start_date" value="{{ $startDateStr }}">
                                <input type="hidden" name="end_date" value="{{ $endDateStr }}">
                            @endif
                            <div class="input-group input-group-sm flex-grow-1" style="min-width: 140px; max-width: 250px;">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="p_search" class="form-control" placeholder="Rechercher produit / SKU..." value="{{ $productSearch }}">
                            </div>
                            <select name="p_category" class="form-select form-select-sm flex-grow-1" style="min-width: 140px; max-width: 200px;" onchange="this.form.submit()">
                                <option value="">Toutes les catégories</option>
                                @foreach($allCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-dark flex-shrink-0">Filtrer</button>
                            @if($productSearch || $categoryId)
                                <a href="{{ route('analytics.views', ['period' => $period]) }}" class="btn btn-sm btn-outline-secondary flex-shrink-0">Réinitialiser</a>
                            @endif
                        </form>

                        <div class="text-muted small w-100 w-md-auto text-end" style="font-size: 0.75rem;">
                            Affichage de <strong>{{ $topProducts->firstItem() ?? 0 }}</strong> à <strong>{{ $topProducts->lastItem() ?? 0 }}</strong> sur <strong>{{ $topProducts->total() }}</strong>
                        </div>
                    </div>

                    <!-- Desktop View: Table -->
                    <div class="d-none d-md-block table-responsive">
                        <table class="table views-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">Rang</th>
                                    <th>Produit</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Stock actuel</th>
                                    <th class="text-center" style="width: 140px;">Vues (Période)</th>
                                    <th class="text-center" style="width: 120px;">Visiteurs uniques</th>
                                    <th class="text-end" style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $pv)
                                    @php
                                        $product = $pv->product;
                                        $rank = ($topProducts->currentPage() - 1) * $topProducts->perPage() + $index + 1;
                                        $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                                    @endphp
                                    @if($product)
                                    <tr>
                                        <td class="text-center">
                                            <span class="rank-badge {{ $rankClass }}">#{{ $rank }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="prod-avatar-small" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                                <div>
                                                    <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" target="_blank" class="fw-bold text-dark text-decoration-none d-block">
                                                        {{ $product->name }}
                                                    </a>
                                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">{{ $product->sku }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $product->productCategory?->name ?? 'Non classé' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ currency($product->price) }}</span>
                                        </td>
                                        <td>
                                            @if($product->stock > 5)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $product->stock }} en stock</span>
                                            @elseif($product->stock > 0)
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">{{ $product->stock }} restant(s)</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-exclamation-triangle me-1"></i>Rupture</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-indigo-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-6 fw-bold">
                                                {{ number_format($pv->views_in_period, 0, ',', ' ') }}
                                            </span>
                                        </td>
                                        <td class="text-center text-muted fw-semibold">
                                            <i class="fas fa-user-check me-1 opacity-50"></i> {{ $pv->unique_visitors }}
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" onclick="openProductViewsModal({{ $product->id }})" title="Voir l'historique détaillé des vues par date">
                                                <i class="fas fa-chart-line me-1"></i> Détail
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-chart-bar fa-3x text-muted opacity-25 mb-3"></i>
                                            <h6 class="fw-bold text-dark">Aucune vue enregistrée pour cette sélection</h6>
                                            <p class="text-muted small">Les visites de vos clients s'afficheront ici en temps réel.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View: Touch Cards -->
                    <div class="d-block d-md-none p-2">
                        @forelse($topProducts as $index => $pv)
                            @php
                                $product = $pv->product;
                                $rank = ($topProducts->currentPage() - 1) * $topProducts->perPage() + $index + 1;
                                $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                            @endphp
                            @if($product)
                            <div class="mobile-analytics-card">
                                <!-- Top Row: Rank + Image + Name + Views Badge -->
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <span class="rank-badge {{ $rankClass }} flex-shrink-0 mt-0.5">#{{ $rank }}</span>
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="prod-thumb" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="d-flex align-items-start justify-content-between gap-1">
                                            <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" target="_blank" class="fw-bold text-dark text-decoration-none small text-truncate-2 d-block" style="line-height: 1.25;">
                                                {{ $product->name }}
                                            </a>
                                            <span class="badge bg-indigo-subtle text-primary border border-primary-subtle px-1.5 py-0.5 fw-bold flex-shrink-0" style="font-size: 0.78rem;">
                                                <i class="fas fa-eye me-1"></i>{{ number_format($pv->views_in_period, 0, ',', ' ') }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap mt-1">
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">{{ $product->sku }}</span>
                                            <span class="badge bg-light text-dark border text-truncate" style="font-size: 0.65rem; max-width: 120px;">{{ $product->productCategory?->name ?? 'Non classé' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom Row: Price, Stock, Visitors, Action Button -->
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top flex-wrap gap-1.5" style="font-size: 0.75rem;">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <strong class="text-dark">{{ currency($product->price) }}</strong>
                                        @if($product->stock > 5)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem;">{{ $product->stock }} en stock</span>
                                        @elseif($product->stock > 0)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.65rem;">{{ $product->stock }} rest.</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.65rem;"><i class="fas fa-exclamation-triangle me-0.5"></i>Rupture</span>
                                        @endif
                                        <span class="text-muted" style="font-size: 0.7rem;">
                                            <i class="fas fa-user-check me-0.5 opacity-50"></i>{{ $pv->unique_visitors }}
                                        </span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5 ms-auto" style="font-size: 0.75rem;" onclick="openProductViewsModal({{ $product->id }})">
                                        <i class="fas fa-chart-line me-1"></i> Détail
                                    </button>
                                </div>
                            </div>
                            @endif
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-chart-bar fa-2x mb-2 opacity-25"></i>
                                <div class="small fw-semibold">Aucune vue enregistrée</div>
                            </div>
                        @endforelse
                    </div>

                    @if($topProducts->hasPages())
                        <div class="p-2.5 p-md-3 border-top d-flex justify-content-center justify-content-md-end">
                            {{ $topProducts->links() }}
                        </div>
                    @endif
                </div>

                <!-- ================= TAB 2: VUES PAR CATÉGORIE ================= -->
                <div class="tab-pane fade" id="categories-pane" role="tabpanel" aria-labelledby="categories-tab" tabindex="0">
                    <!-- Desktop Table -->
                    <div class="d-none d-md-block table-responsive">
                        <table class="table views-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Rayon / Catégorie</th>
                                    <th>Produits actifs</th>
                                    <th class="text-center" style="width: 140px;">Vues dans la période</th>
                                    <th style="width: 250px;">Part de trafic</th>
                                    <th class="text-end" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryViews as $index => $cv)
                                    @php $cat = $cv->category; @endphp
                                    @if($cat)
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="views-kpi-icon bg-light text-teal border" style="width: 36px; height: 36px; font-size: 1rem;">
                                                    <i class="fas {{ $cat->icon ?: 'fa-folder' }}"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $cat->name }}</div>
                                                    <span class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $cat->slug }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border">{{ $cat->products()->count() }} produits</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-2.5 py-1 fs-6 fw-bold">
                                                {{ number_format($cv->views_in_period, 0, ',', ' ') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-teal" role="progressbar" style="width: {{ $cv->share_percentage }}%;" aria-valuenow="{{ $cv->share_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="small fw-bold text-dark">{{ $cv->share_percentage }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2 py-1" title="Voir le rayon en boutique">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
                                            <h6 class="fw-bold text-dark">Aucune vue de catégorie pour cette période</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="d-block d-md-none p-2">
                        @forelse($categoryViews as $index => $cv)
                            @php $cat = $cv->category; @endphp
                            @if($cat)
                            <div class="mobile-analytics-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rank-badge rank-other flex-shrink-0" style="width: 22px; height: 22px; font-size: 0.7rem;">#{{ $index + 1 }}</span>
                                        <div class="views-kpi-icon bg-light text-teal border" style="width: 32px; height: 32px; font-size: 0.9rem;">
                                            <i class="fas {{ $cat->icon ?: 'fa-folder' }}"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark small d-block">{{ $cat->name }}</strong>
                                            <span class="text-muted font-monospace" style="font-size: 0.68rem;">{{ $cat->products()->count() }} produits</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-2 py-1 fw-bold" style="font-size: 0.78rem;">
                                            {{ number_format($cv->views_in_period, 0, ',', ' ') }} vues
                                        </span>
                                        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill p-1" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-external-link-alt" style="font-size: 10px;"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-2 pt-1.5 border-top" style="font-size: 0.72rem;">
                                    <span class="text-muted">Part :</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-teal" role="progressbar" style="width: {{ $cv->share_percentage }}%;"></div>
                                    </div>
                                    <strong class="text-dark">{{ $cv->share_percentage }}%</strong>
                                </div>
                            </div>
                            @endif
                        @empty
                            <div class="text-center py-4 text-muted small">Aucune vue de catégorie pour cette période</div>
                        @endforelse
                    </div>
                </div>

                <!-- ================= TAB 3: MOTS & RECHERCHES CLIENTS ================= -->
                <div class="tab-pane fade" id="searches-pane" role="tabpanel" aria-labelledby="searches-tab" tabindex="0">
                    <!-- Filters bar inside searches tab -->
                    <div class="p-2.5 p-md-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <form action="{{ route('analytics.views') }}" method="GET" class="d-flex align-items-center flex-wrap gap-2 w-100 w-md-auto">
                            <input type="hidden" name="period" value="{{ $period }}">
                            @if($period === 'custom')
                                <input type="hidden" name="start_date" value="{{ $startDateStr }}">
                                <input type="hidden" name="end_date" value="{{ $endDateStr }}">
                            @endif
                            <div class="input-group input-group-sm flex-grow-1" style="min-width: 140px; max-width: 250px;">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="s_word" class="form-control" placeholder="Filtrer mot-clé..." value="{{ $searchWord }}">
                            </div>
                            <div class="btn-group btn-group-sm period-filter-scroll" role="group">
                                <button type="submit" name="s_filter" value="all" class="btn {{ $searchFilter === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">Toutes</button>
                                <button type="submit" name="s_filter" value="zero_results" class="btn {{ $searchFilter === 'zero_results' ? 'btn-danger' : 'btn-outline-danger' }}">
                                    <i class="fas fa-exclamation-triangle me-1"></i> 0 Résultat
                                </button>
                                <button type="submit" name="s_filter" value="popular" class="btn {{ $searchFilter === 'popular' ? 'btn-primary' : 'btn-outline-primary' }}">Populaires (>=3)</button>
                            </div>
                        </form>

                        <div class="text-muted small w-100 w-md-auto text-end" style="font-size: 0.75rem;">
                            <strong>{{ $searchLogs->total() }}</strong> expressions recherchées
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="d-none d-md-block table-responsive">
                        <table class="table views-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Mot-clé / Requête recherchée</th>
                                    <th class="text-center" style="width: 140px;">Nombre de recherches</th>
                                    <th>Résultats trouvés</th>
                                    <th>Statut Besoin Client</th>
                                    <th class="text-end">Dernière recherche</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($searchLogs as $index => $sl)
                                    @php $hasZeroResults = ($sl->min_results == 0); @endphp
                                    <tr class="{{ $hasZeroResults ? 'bg-danger-subtle bg-opacity-10' : '' }}">
                                        <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-search text-muted opacity-50"></i>
                                                <span class="fw-bold text-dark fs-6">"{{ $sl->sample_query }}"</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 fs-6 fw-bold">
                                                {{ $sl->search_count }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($sl->max_results > 0)
                                                <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i> {{ $sl->max_results }} article(s) trouvé(s)</span>
                                            @else
                                                <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> Aucun produit trouvé</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($hasZeroResults)
                                                <span class="badge bg-danger text-white px-2 py-1">
                                                    <i class="fas fa-bullhorn me-1"></i> Demande non satisfaite (À ajouter)
                                                </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="fas fa-check me-1"></i> Produits disponibles
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted small">
                                            <i class="fas fa-clock me-1 opacity-50"></i> {{ \Carbon\Carbon::parse($sl->last_searched_at)->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-search-minus fa-3x text-muted opacity-25 mb-3"></i>
                                            <h6 class="fw-bold text-dark">Aucune recherche enregistrée pour cette sélection</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="d-block d-md-none p-2">
                        @forelse($searchLogs as $index => $sl)
                            @php $hasZeroResults = ($sl->min_results == 0); @endphp
                            <div class="mobile-analytics-card {{ $hasZeroResults ? 'border-danger-subtle bg-danger-subtle bg-opacity-10' : '' }}">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-1.5">
                                    <div class="d-flex align-items-center gap-1.5 min-w-0">
                                        <i class="fas fa-search text-muted opacity-50" style="font-size: 11px;"></i>
                                        <strong class="text-dark small text-truncate">"{{ $sl->sample_query }}"</strong>
                                    </div>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5 fw-bold flex-shrink-0" style="font-size: 0.75rem;">
                                        {{ $sl->search_count }} rech.
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-1.5 border-top flex-wrap gap-1" style="font-size: 0.72rem;">
                                    @if($hasZeroResults)
                                        <span class="badge bg-danger text-white px-1.5 py-0.5">
                                            <i class="fas fa-bullhorn me-1"></i>0 résultat (Demande insatisfaite)
                                        </span>
                                    @else
                                        <span class="text-success fw-semibold">
                                            <i class="fas fa-check-circle me-1"></i>{{ $sl->max_results }} trouvé(s)
                                        </span>
                                    @endif
                                    <span class="text-muted ms-auto">
                                        <i class="fas fa-clock me-0.5 opacity-50"></i>{{ \Carbon\Carbon::parse($sl->last_searched_at)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">Aucune recherche enregistrée pour cette sélection</div>
                        @endforelse
                    </div>

                    @if($searchLogs->hasPages())
                        <div class="p-2.5 p-md-3 border-top d-flex justify-content-center justify-content-md-end">
                            {{ $searchLogs->links() }}
                        </div>
                    @endif
                </div>

                <!-- ================= TAB 4: BESOINS & OPPORTUNITÉS ================= -->
                <div class="tab-pane fade" id="insights-pane" role="tabpanel" aria-labelledby="insights-tab" tabindex="0">
                    <div class="p-2.5 p-md-4">
                        <div class="row g-3 g-md-4">
                            <!-- Opportunités de Mots-Clés (0 Résultats) -->
                            <div class="col-12 col-lg-6">
                                <div class="card border border-danger-subtle rounded-4 h-100 shadow-sm">
                                    <div class="card-header bg-danger-subtle text-danger py-2.5 py-md-3 px-3 px-md-4 border-0 rounded-top-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="fw-bold fs-6">
                                                <i class="fas fa-bullhorn me-1.5"></i>Mots-clés avec 0 Résultat
                                            </div>
                                            <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.7rem;">Priorité Achat</span>
                                        </div>
                                        <div class="small text-danger opacity-75 mt-1" style="font-size: 0.74rem;">Vos clients ont cherché ces termes mais la boutique n'a renvoyé aucun résultat. Ajoutez ces produits ou leurs synonymes !</div>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @forelse($zeroResultSearches as $z)
                                                <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 px-md-4 flex-wrap gap-2">
                                                    <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                                                        <i class="fas fa-search text-danger opacity-75" style="font-size: 11px;"></i>
                                                        <strong class="text-dark small text-truncate">"{{ $z->sample_query }}"</strong>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0 ms-auto">
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5" style="font-size: 0.72rem;">
                                                            {{ $z->search_count }} fois
                                                        </span>
                                                        <a href="{{ route('products.create', ['name' => $z->sample_query]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0.5" style="font-size: 0.72rem;" title="Créer un nouveau produit pour cette demande">
                                                            <i class="fas fa-plus"></i> Créer
                                                        </a>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item text-center py-4 text-muted small">
                                                    <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                                                    Aucune recherche infructueuse enregistrée dans cette période !
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Produits Très Consultés mais en Rupture de Stock -->
                            <div class="col-12 col-lg-6">
                                <div class="card border border-warning-subtle rounded-4 h-100 shadow-sm">
                                    <div class="card-header bg-warning-subtle text-dark py-2.5 py-md-3 px-3 px-md-4 border-0 rounded-top-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="fw-bold fs-6">
                                                <i class="fas fa-boxes-packing me-1.5 text-warning"></i>Forte Demande & Rupture
                                            </div>
                                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.7rem;">Ventes Manquées</span>
                                        </div>
                                        <div class="small text-muted mt-1" style="font-size: 0.74rem;">Articles fréquemment consultés mais indisponibles à l'achat. Réapprovisionnez ces produits en priorité !</div>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @forelse($lostSalesProducts as $lp)
                                                @php $p = $lp->product; @endphp
                                                @if($p)
                                                <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 px-md-4 flex-wrap gap-2">
                                                    <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                                                        <img src="{{ $p->thumbnail }}" alt="" class="prod-thumb" style="width: 34px; height: 34px;">
                                                        <div class="min-w-0">
                                                            <div class="fw-bold text-dark text-truncate small" style="max-width: 170px;">{{ $p->name }}</div>
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace" style="font-size: 0.62rem;">Stock : 0</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0 ms-auto">
                                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 0.72rem;">
                                                            {{ $lp->views_in_period }} vues
                                                        </span>
                                                        <a href="{{ route('products.edit', $p->id) }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                                            Stock
                                                        </a>
                                                    </div>
                                                </li>
                                                @endif
                                            @empty
                                                <li class="list-group-item text-center py-4 text-muted small">
                                                    <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                                                    Aucun article populaire n'est en rupture de stock. Bravo !
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 5: FLUX EN DIRECT ================= -->
                <div class="tab-pane fade" id="activity-pane" role="tabpanel" aria-labelledby="activity-tab" tabindex="0">
                    <div class="p-2.5 p-md-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-1">
                        <span class="fw-bold text-dark small"><i class="fas fa-circle text-success me-1.5" style="font-size: 7px;"></i>Dernières actions des visiteurs en direct</span>
                        <span class="badge bg-white text-muted border" style="font-size: 0.7rem;">25 dernières actions</span>
                    </div>

                    <!-- Desktop Table -->
                    <div class="d-none d-md-block table-responsive">
                        <table class="table views-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 140px;">Type d'action</th>
                                    <th>Cible / Contenu</th>
                                    <th>Appareil</th>
                                    <th>Adresse IP</th>
                                    <th class="text-end">Horodatage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivity as $act)
                                    <tr>
                                        <td>
                                            @if($act['type'] === 'product_view')
                                                <span class="badge bg-primary-subtle text-primary border"><i class="fas fa-eye me-1"></i> Vue Produit</span>
                                            @elseif($act['type'] === 'category_view')
                                                <span class="badge bg-teal-subtle text-teal border"><i class="fas fa-folder me-1"></i> Rayon</span>
                                            @else
                                                <span class="badge bg-info-subtle text-info border"><i class="fas fa-search me-1"></i> Recherche</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(!empty($act['image']))
                                                    <img src="{{ $act['image'] }}" class="prod-avatar-small" style="width: 32px; height: 32px;" alt="">
                                                @endif
                                                <div>
                                                    <strong class="text-dark">{{ $act['title'] }}</strong>
                                                    <div class="text-muted small">{{ $act['subtitle'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($act['device'] === 'mobile')
                                                <span class="badge bg-light text-success border"><i class="fas fa-mobile-alt me-1"></i> Mobile</span>
                                            @elseif($act['device'] === 'tablet')
                                                <span class="badge bg-light text-warning border"><i class="fas fa-tablet-alt me-1"></i> Tablette</span>
                                            @else
                                                <span class="badge bg-light text-primary border"><i class="fas fa-desktop me-1"></i> Desktop</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code class="text-muted small">{{ $act['ip'] }}</code>
                                        </td>
                                        <td class="text-end text-muted small">
                                            {{ \Carbon\Carbon::parse($act['created_at'])->format('d/m/Y H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Aucune activité récente enregistrée pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="d-block d-md-none p-2">
                        @forelse($recentActivity as $act)
                            <div class="mobile-analytics-card">
                                <div class="d-flex align-items-start justify-content-between gap-1.5 mb-1.5">
                                    @if($act['type'] === 'product_view')
                                        <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.68rem;"><i class="fas fa-eye me-1"></i>Vue Produit</span>
                                    @elseif($act['type'] === 'category_view')
                                        <span class="badge bg-teal-subtle text-teal border" style="font-size: 0.68rem;"><i class="fas fa-folder me-1"></i>Rayon</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border" style="font-size: 0.68rem;"><i class="fas fa-search me-1"></i>Recherche</span>
                                    @endif

                                    <div class="d-flex align-items-center gap-1 ms-auto">
                                        @if($act['device'] === 'mobile')
                                            <span class="badge bg-light text-success border" style="font-size: 0.65rem;"><i class="fas fa-mobile-alt me-0.5"></i>Mob.</span>
                                        @elseif($act['device'] === 'tablet')
                                            <span class="badge bg-light text-warning border" style="font-size: 0.65rem;"><i class="fas fa-tablet-alt me-0.5"></i>Tab.</span>
                                        @else
                                            <span class="badge bg-light text-primary border" style="font-size: 0.65rem;"><i class="fas fa-desktop me-0.5"></i>Desk.</span>
                                        @endif
                                        <span class="text-muted" style="font-size: 0.68rem;">{{ \Carbon\Carbon::parse($act['created_at'])->format('H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    @if(!empty($act['image']))
                                        <img src="{{ $act['image'] }}" class="prod-thumb" style="width: 32px; height: 32px; border-radius: 6px;" alt="">
                                    @endif
                                    <div class="min-w-0 flex-grow-1">
                                        <strong class="text-dark small d-block text-truncate">{{ $act['title'] }}</strong>
                                        <div class="text-muted" style="font-size: 0.68rem;">{{ $act['subtitle'] }} &bull; <code>{{ $act['ip'] }}</code></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">Aucune activité récente enregistrée pour le moment.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Single Product Views History Detail (Loaded via AJAX) -->
<div class="modal fade" id="productViewsModal" tabindex="-1" aria-labelledby="productViewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-2.5 px-3 px-md-4 bg-light">
                <div class="d-flex align-items-center gap-2.5 min-w-0">
                    <img id="mProdThumb" src="" class="prod-avatar-small" style="width: 40px; height: 40px;" alt="">
                    <div class="min-w-0">
                        <h6 class="modal-title fw-bold text-dark mb-0 text-truncate small" id="mProdName" style="max-width: 230px;">Nom du produit</h6>
                        <div class="d-flex align-items-center gap-1 mt-0.5">
                            <span class="badge bg-white text-secondary border font-monospace" id="mProdSku" style="font-size: 0.62rem;">SKU</span>
                            <span class="badge bg-light text-muted border text-truncate" id="mProdCat" style="font-size: 0.62rem; max-width: 120px;">Catégorie</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="row g-1.5 g-md-2 mb-3 text-center">
                    <div class="col-4 px-1">
                        <div class="p-2 bg-light rounded-3 border">
                            <div class="text-muted text-truncate" style="font-size: 0.68rem;">30 derniers jours</div>
                            <div class="h5 fw-bold text-primary mb-0 mt-0.5" id="mTotalViews">0</div>
                        </div>
                    </div>
                    <div class="col-4 px-1">
                        <div class="p-2 bg-light rounded-3 border">
                            <div class="text-muted text-truncate" style="font-size: 0.68rem;">Visiteurs uniques</div>
                            <div class="h5 fw-bold text-dark mb-0 mt-0.5" id="mTotalUniques">0</div>
                        </div>
                    </div>
                    <div class="col-4 px-1">
                        <div class="p-2 bg-light rounded-3 border">
                            <div class="text-muted text-truncate" style="font-size: 0.68rem;">Total historique</div>
                            <div class="h5 fw-bold text-success mb-0 mt-0.5" id="mAllTimeViews">0</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2 small"><i class="fas fa-calendar-day me-1 text-primary"></i>Historique quotidien des vues (30 derniers jours)</h6>
                <div style="height: 200px; position: relative;">
                    <canvas id="mProductChart"></canvas>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-2 px-3 px-md-4">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize Main Line Trend Chart
    const trendCtx = document.getElementById('viewsTrendChart');
    if (trendCtx) {
        const labels = @json($chartData['labels']);
        const pvData = @json($chartData['product_views']);
        const cvData = @json($chartData['category_views']);
        const searchData = @json($chartData['searches']);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Vues Produits',
                        data: pvData,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2.5,
                        pointHoverRadius: 5,
                    },
                    {
                        label: 'Vues Rayons',
                        data: cvData,
                        borderColor: '#14b8a6',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 2,
                        borderDash: [4, 4]
                    },
                    {
                        label: 'Recherches Clients',
                        data: searchData,
                        borderColor: '#0ea5e9',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 8,
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0, font: { size: 10 }, color: '#64748b' }
                    }
                }
            }
        });
    }

    // 2. Initialize Device Breakdown Doughnut Chart
    const deviceCtx = document.getElementById('deviceDoughnutChart');
    if (deviceCtx) {
        const desktopVal = {{ $deviceData['desktop'] }};
        const mobileVal = {{ $deviceData['mobile'] }};
        const tabletVal = {{ $deviceData['tablet'] }};

        new Chart(deviceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Desktop', 'Mobile', 'Tablette'],
                datasets: [{
                    data: [desktopVal, mobileVal, tabletVal],
                    backgroundColor: ['#6366f1', '#10b981', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.label + ': ' + context.raw + ' vues';
                            }
                        }
                    }
                }
            }
        });
    }
});

// Single Product History Modal Handler
let productModalChart = null;
function openProductViewsModal(productId) {
    const modalEl = document.getElementById('productViewsModal');
    const modal = new bootstrap.Modal(modalEl);

    // Reset fields to loading state
    document.getElementById('mProdName').textContent = 'Chargement...';
    document.getElementById('mProdSku').textContent = '...';
    document.getElementById('mTotalViews').textContent = '...';
    document.getElementById('mTotalUniques').textContent = '...';
    document.getElementById('mAllTimeViews').textContent = '...';

    modal.show();

    fetch(`/analytics/views/product/${productId}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            document.getElementById('mProdThumb').src = data.product.thumbnail;
            document.getElementById('mProdName').textContent = data.product.name;
            document.getElementById('mProdSku').textContent = data.product.sku;
            document.getElementById('mProdCat').textContent = data.product.category;
            document.getElementById('mTotalViews').textContent = data.total_period;
            document.getElementById('mTotalUniques').textContent = data.total_unique;
            document.getElementById('mAllTimeViews').textContent = data.product.views_count;

            // Render Chart
            const ctx = document.getElementById('mProductChart');
            if (productModalChart) {
                productModalChart.destroy();
            }

            productModalChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.chart.labels,
                    datasets: [
                        {
                            label: 'Vues par jour',
                            data: data.chart.values,
                            backgroundColor: '#6366f1',
                            borderRadius: 4
                        },
                        {
                            label: 'Visiteurs uniques',
                            data: data.chart.uniques,
                            backgroundColor: '#93c5fd',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 9 } } },
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } } }
                    }
                }
            });
        })
        .catch(err => {
            console.error('Error loading product views:', err);
        });
}
</script>
@endpush
