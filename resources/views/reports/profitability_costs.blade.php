@extends('layouts.app')

@section('title', 'Gestion des Prix de Revient & Coûts d\'Achat (Admin)')

@push('styles')
<style>
/* ==========================================================================
   PRODUCT COSTS MANAGEMENT STYLES - PRO EXECUTIVE ALIGNMENT
   ========================================================================== */
.cost-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.25rem 1.35rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.cost-kpi-card:hover {
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
.chips-scroll-cost {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.chips-scroll-cost::-webkit-scrollbar {
    display: none;
}
.cost-pill-btn {
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
.cost-pill-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.cost-pill-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}
.prod-thumb-cost {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    flex-shrink: 0;
}
.cost-input-group {
    max-width: 155px;
    transition: all 0.2s ease;
}
.cost-input-group.is-dirty input {
    border-color: #3b82f6 !important;
    background-color: #eff6ff !important;
    font-weight: 700;
}
.cost-mobile-card {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.floating-batch-bar {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(150px);
    z-index: 1050;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.floating-batch-bar.show {
    transform: translateX(-50%) translateY(0);
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
                    <div class="rounded-circle bg-danger bg-opacity-25 border border-danger border-opacity-50 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; font-size: 1.35rem;">
                        <i class="fas fa-coins text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-danger rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                <i class="fas fa-lock me-1"></i> GESTION DES COÛTS D'ACHAT
                            </span>
                            <span class="text-white-50 small">• Paramétrage des Prix de Revient Fournisseurs</span>
                        </div>
                        <h1 class="h3 fw-bold text-white mb-0">Gestion des Coûts d'Achat Produits</h1>
                        <p class="text-white-50 small mb-0 mt-1">Définissez vos prix d'achat réels pour débloquer des rapports de marge brute et de rentabilité 100% exacts.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('reports.profitability') }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold shadow-xs" style="font-size: 0.85rem;">
                        <i class="fas fa-arrow-left me-1.5"></i> Tableau de Bord Rentabilité
                    </a>
                    <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 fw-semibold shadow-sm" id="btnSaveAllTop" onclick="saveAllDirtyCosts()" disabled style="font-size: 0.85rem;">
                        <i class="fas fa-floppy-disk me-1.5"></i> Enregistrer les Modifications
                        <span class="badge bg-white text-primary rounded-pill ms-1 d-none" id="dirtyCountBadgeTop">0</span>
                    </button>
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

    <!-- 4 Financial KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Couverture Catalogue -->
        <div class="col-6 col-xl-3">
            <div class="cost-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Couverture Catalogue</span>
                        <div class="kpi-icon-pill bg-primary-subtle text-primary">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ $configuredCount }} <span class="fs-6 fw-normal text-muted">/ {{ $totalProducts }}</span></div>
                </div>
                <div class="pt-2 border-top mt-2">
                    <div class="d-flex align-items-center justify-content-between text-muted small mb-1" style="font-size: 0.74rem;">
                        <span>Produits configurés</span>
                        <strong class="text-primary">{{ $coveragePercent }}%</strong>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 99px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $coveragePercent }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Coûts Manquants (0 DH) -->
        <div class="col-6 col-xl-3">
            <div class="cost-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Coûts Manquants (0 DH)</span>
                        <div class="kpi-icon-pill {{ $missingCostCount > 0 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }}">
                            <i class="fas {{ $missingCostCount > 0 ? 'fa-triangle-exclamation' : 'fa-circle-check' }}"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold {{ $missingCostCount > 0 ? 'text-warning' : 'text-success' }} mb-1">{{ $missingCostCount }}</div>
                </div>
                <div class="pt-2 border-top mt-2 d-flex align-items-center justify-content-between text-muted small" style="font-size: 0.74rem;">
                    @if($missingCostCount > 0)
                        <span>À renseigner d'urgence</span>
                        <a href="{{ route('reports.profitability.costs', ['status' => 'missing']) }}" class="text-warning fw-bold text-decoration-none">
                            Filtrer <i class="fas fa-arrow-right ms-0.5"></i>
                        </a>
                    @else
                        <span class="text-success fw-semibold"><i class="fas fa-check me-1"></i> Tous les articles ont un coût !</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Valeur Stock au Coût d'Achat -->
        <div class="col-6 col-xl-3">
            <div class="cost-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Valeur Stock (Coût Achat)</span>
                        <div class="kpi-icon-pill bg-info-subtle text-info">
                            <i class="fas fa-warehouse"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ currency($totalStockCostValue) }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    <span>{{ number_format($totalStockUnits, 0, ',', ' ') }} unités physiques en stock</span>
                </div>
            </div>
        </div>

        <!-- 4. Bénéfice Brut Potentiel Stock -->
        <div class="col-6 col-xl-3">
            <div class="cost-kpi-card" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); color: #ffffff;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Gain Potentiel Stock</span>
                        <div class="kpi-icon-pill bg-white text-success">
                            <i class="fas fa-sack-dollar"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-white mb-1">{{ currency($potentialGrossProfit) }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-white-50 small" style="font-size: 0.74rem;">
                    Valeur vente : {{ currency($totalStockRetailValue) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <!-- Fast Touch Filter Pills -->
            <div class="chips-scroll-cost mb-3">
                <a href="{{ route('reports.profitability.costs', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
                   class="cost-pill-btn {{ $status === 'all' ? 'active' : '' }}">
                    <span>Tous les produits</span>
                    <span class="badge bg-secondary text-white rounded-pill ms-1">{{ $totalProducts }}</span>
                </a>
                <a href="{{ route('reports.profitability.costs', array_merge(request()->except('status', 'page'), ['status' => 'missing'])) }}" 
                   class="cost-pill-btn {{ $status === 'missing' ? 'active' : '' }}">
                    <i class="fas fa-triangle-exclamation text-warning"></i>
                    <span>Sans coût d'achat</span>
                    <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $missingCostCount }}</span>
                </a>
                <a href="{{ route('reports.profitability.costs', array_merge(request()->except('status', 'page'), ['status' => 'configured'])) }}" 
                   class="cost-pill-btn {{ $status === 'configured' ? 'active' : '' }}">
                    <i class="fas fa-check text-success"></i>
                    <span>Avec coût d'achat</span>
                    <span class="badge bg-success text-white rounded-pill ms-1">{{ $configuredCount }}</span>
                </a>
                <a href="{{ route('reports.profitability.costs', array_merge(request()->except('status', 'page'), ['status' => 'negative_margin'])) }}" 
                   class="cost-pill-btn {{ $status === 'negative_margin' ? 'active' : '' }}">
                    <i class="fas fa-arrow-trend-down text-danger"></i>
                    <span>Marge Négative (Perte)</span>
                </a>
                <a href="{{ route('reports.profitability.costs', array_merge(request()->except('status', 'page'), ['status' => 'low_margin'])) }}" 
                   class="cost-pill-btn {{ $status === 'low_margin' ? 'active' : '' }}">
                    <i class="fas fa-percent text-warning"></i>
                    <span>Marge Faible (&lt; 15%)</span>
                </a>
            </div>

            <!-- Search & Filters Form -->
            <form method="GET" action="{{ route('reports.profitability.costs') }}" class="row g-2 align-items-center">
                <input type="hidden" name="status" value="{{ $status }}">

                <!-- Search Input -->
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control bg-light border-start-0" placeholder="Rechercher par nom, SKU...">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="col-6 col-md-2">
                    <select name="category_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">Toutes Catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand Filter -->
                <div class="col-6 col-md-2">
                    <select name="brand_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">Toutes Marques</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="col-8 col-md-3">
                    <select name="sort" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nom (A → Z)</option>
                        <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Nom (Z → A)</option>
                        <option value="cost_desc" {{ $sort === 'cost_desc' ? 'selected' : '' }}>Coût le plus élevé</option>
                        <option value="cost_asc" {{ $sort === 'cost_asc' ? 'selected' : '' }}>Coût le plus bas</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Prix de vente décroissant</option>
                        <option value="stock_desc" {{ $sort === 'stock_desc' ? 'selected' : '' }}>Stock le plus fort</option>
                    </select>
                </div>

                <!-- Action Button -->
                <div class="col-4 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-semibold rounded-3">Filtrer</button>
                    @if($search || $categoryId || $brandId || $status !== 'all' || $sort !== 'name_asc')
                        <a href="{{ route('reports.profitability.costs') }}" class="btn btn-sm btn-light border rounded-3" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Main Products Cost Matrix -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        
        <!-- Table Header Bar -->
        <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark">Catalogue des Produits ({{ $products->total() }} résultats)</h6>
                <span class="text-muted small">Modifiez directement les coûts dans les cases, appuyez sur <kbd class="bg-light text-dark border">Entrée</kbd> ou sauvegardez en lot.</span>
            </div>
            <div class="text-muted small">
                Affichage de {{ $products->firstItem() ?? 0 }} à {{ $products->lastItem() ?? 0 }} sur {{ $products->total() }}
            </div>
        </div>

        <!-- 1. DESKTOP VIEW (d-none d-lg-block) -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0" id="productsCostTable">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 34%;">Article</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 8%;">Stock</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 14%;">Prix Vente TTC</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 18%;">Coût d'Achat (DH)</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 14%;">Marge Unitaire</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($products as $p)
                    @php
                        $effectivePrice = (float)($p->sale_price ?? $p->price ?? 0);
                        $costPrice = (float)($p->cost_price ?? 0);
                        $profitDh = $effectivePrice - $costPrice;
                        $marginPct = $effectivePrice > 0 ? round(($profitDh / $effectivePrice) * 100, 1) : 0;
                        $markupPct = $costPrice > 0 ? round(($profitDh / $costPrice) * 100, 1) : 0;
                        $stockQty = $p->stock ?? $p->stock_quantity ?? 0;
                    @endphp
                    <tr class="hover-bg-light" id="row-prod-{{ $p->id }}" data-product-id="{{ $p->id }}" data-selling-price="{{ $effectivePrice }}">
                        <!-- Article info -->
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-2.5">
                                @if($p->image)
                                    <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="prod-thumb-cost shadow-xs"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                @else
                                    <div class="prod-thumb-cost d-flex align-items-center justify-content-center text-muted">
                                        <i class="fas fa-box"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('products.edit', $p->id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block text-truncate" title="{{ $p->name }}" style="max-width: 320px;">
                                        {{ $p->name }}
                                    </a>
                                    <div class="d-flex align-items-center gap-1.5 text-muted small mt-0.5 flex-wrap" style="font-size: 0.72rem;">
                                        <span class="font-monospace text-secondary">{{ $p->sku ?? 'SANS SKU' }}</span>
                                        @if($p->brand)
                                            <span>• <span class="badge bg-light text-dark border">{{ $p->brand->name }}</span></span>
                                        @endif
                                        @if($p->category)
                                            <span>• {{ $p->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Stock -->
                        <td class="py-3 text-center">
                            @if($stockQty > 5)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold">{{ $stockQty }}</span>
                            @elseif($stockQty > 0)
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold">{{ $stockQty }}</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Épuisé</span>
                            @endif
                        </td>

                        <!-- Selling Price -->
                        <td class="py-3 text-end">
                            <div class="fw-bold text-dark fs-6">{{ currency($effectivePrice) }}</div>
                            @if($p->sale_price && $p->sale_price < $p->price)
                                <div class="text-muted text-decoration-line-through small" style="font-size: 0.7rem;">{{ currency($p->price) }}</div>
                            @endif
                        </td>

                        <!-- Cost Price Input (Interactive) -->
                        <td class="py-3">
                            <div class="input-group input-group-sm cost-input-group" id="input-group-{{ $p->id }}">
                                <input type="number" step="0.01" min="0" 
                                       class="form-control fw-bold cost-field" 
                                       id="cost-input-{{ $p->id }}" 
                                       value="{{ $costPrice > 0 ? $costPrice : '' }}" 
                                       placeholder="0.00"
                                       data-original-cost="{{ $costPrice }}"
                                       oninput="handleCostChange({{ $p->id }})"
                                       onkeydown="handleCostKeydown(event, {{ $p->id }})">
                                <span class="input-group-text bg-light text-muted fw-semibold">DH</span>
                            </div>
                        </td>

                        <!-- Real-time Calculated Margin -->
                        <td class="py-3 text-end" id="margin-cell-{{ $p->id }}">
                            <div class="fw-bold fs-6 {{ $profitDh >= 0 ? 'text-success' : 'text-danger' }}" id="profit-dh-{{ $p->id }}">
                                {{ currency($profitDh) }}
                            </div>
                            <div class="mt-0.5" id="margin-badge-wrap-{{ $p->id }}">
                                @php
                                    $badgeClass = 'bg-secondary-subtle text-secondary';
                                    if ($costPrice <= 0) {
                                        $badgeClass = 'bg-warning-subtle text-warning';
                                    } elseif ($profitDh < 0) {
                                        $badgeClass = 'bg-danger-subtle text-danger';
                                    } elseif ($marginPct >= 30) {
                                        $badgeClass = 'bg-success-subtle text-success';
                                    } elseif ($marginPct >= 15) {
                                        $badgeClass = 'bg-primary-subtle text-primary';
                                    } else {
                                        $badgeClass = 'bg-warning-subtle text-warning';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} border rounded-pill px-2 py-0.5 fw-semibold" id="margin-pct-badge-{{ $p->id }}" style="font-size: 0.7rem;">
                                    @if($costPrice <= 0)
                                        Coût non défini
                                    @else
                                        {{ $marginPct }}% marge
                                    @endif
                                </span>
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="pe-3 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                <!-- Save Row Button -->
                                <button type="button" class="btn btn-sm btn-outline-success rounded-circle d-inline-flex align-items-center justify-content-center btn-save-row"
                                        id="btn-save-{{ $p->id }}"
                                        style="width: 32px; height: 32px; padding: 0;"
                                        onclick="saveSingleCost({{ $p->id }})"
                                        title="Enregistrer ce coût d'achat">
                                    <i class="fas fa-check"></i>
                                </button>

                                <!-- Margin Simulator Modal Trigger -->
                                <button type="button" class="btn btn-sm btn-light border rounded-circle text-secondary d-inline-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px; padding: 0;"
                                        onclick="openSimulatorModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $effectivePrice }}, {{ $costPrice }})"
                                        title="Simulateur de marge & coefficient">
                                    <i class="fas fa-calculator"></i>
                                </button>

                                <!-- Product Page Link -->
                                <a href="{{ route('products.edit', $p->id) }}" target="_blank" class="btn btn-sm btn-light border rounded-circle text-primary d-inline-flex align-items-center justify-content-center"
                                   style="width: 32px; height: 32px; padding: 0;"
                                   title="Modifier la fiche produit complète">
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-boxes-stacked fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0 fw-semibold">Aucun produit ne correspond à ces critères de recherche.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. MOBILE VIEW (d-block d-lg-none) -->
        <div class="d-block d-lg-none p-3 space-y-3 bg-light bg-opacity-50">
            @forelse($products as $p)
            @php
                $mEffectivePrice = (float)($p->sale_price ?? $p->price ?? 0);
                $mCostPrice = (float)($p->cost_price ?? 0);
                $mProfitDh = $mEffectivePrice - $mCostPrice;
                $mMarginPct = $mEffectivePrice > 0 ? round(($mProfitDh / $mEffectivePrice) * 100, 1) : 0;
            @endphp
            <div class="cost-mobile-card p-3 bg-white" id="mob-card-{{ $p->id }}">
                <div class="d-flex align-items-start gap-2.5 mb-2.5">
                    @if($p->image)
                        <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="prod-thumb-cost shadow-xs"
                             onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                    @else
                        <div class="prod-thumb-cost d-flex align-items-center justify-content-center text-muted">
                            <i class="fas fa-box"></i>
                        </div>
                    @endif
                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">{{ $p->sku ?? 'SANS SKU' }}</span>
                            <span class="badge {{ $p->stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill" style="font-size: 0.68rem;">
                                Stock: {{ $p->stock ?? 0 }}
                            </span>
                        </div>
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem;">{{ $p->name }}</div>
                        <div class="text-muted small" style="font-size: 0.72rem;">
                            Prix Vente : <strong class="text-dark">{{ currency($mEffectivePrice) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Input & Dynamic Margin on Mobile -->
                <div class="p-2.5 rounded-3 bg-light border mb-2.5">
                    <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.75rem;">Prix de Revient (Coût d'Achat) :</label>
                    <div class="input-group input-group-sm mb-2">
                        <input type="number" step="0.01" min="0" 
                               class="form-control fw-bold" 
                               id="mob-cost-input-{{ $p->id }}" 
                               value="{{ $mCostPrice > 0 ? $mCostPrice : '' }}" 
                               placeholder="0.00"
                               oninput="handleMobCostChange({{ $p->id }}, {{ $mEffectivePrice }})">
                        <span class="input-group-text bg-white text-muted fw-semibold">DH</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-1 border-top" style="font-size: 0.78rem;">
                        <span class="text-muted">Marge estimée :</span>
                        <div class="d-flex align-items-center gap-1.5">
                            <strong class="{{ $mProfitDh >= 0 ? 'text-success' : 'text-danger' }}" id="mob-profit-{{ $p->id }}">{{ currency($mProfitDh) }}</strong>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" id="mob-badge-{{ $p->id }}">{{ $mMarginPct }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Touch Buttons -->
                <div class="d-flex align-items-center justify-content-between gap-2 pt-1 border-top">
                    <button type="button" class="btn btn-sm btn-light border text-secondary rounded-pill px-2.5 py-1"
                            onclick="openSimulatorModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $mEffectivePrice }}, {{ $mCostPrice }})">
                        <i class="fas fa-calculator me-1"></i> Simulateur
                    </button>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold shadow-xs"
                            onclick="saveMobCost({{ $p->id }})">
                        <i class="fas fa-check me-1"></i> Enregistrer
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <p class="mb-0 fw-semibold">Aucun produit trouvé.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="p-3 border-top bg-light d-flex align-items-center justify-content-center">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Floating Action Bar for Batch Saving -->
<div class="floating-batch-bar" id="floatingBatchBar">
    <div class="bg-dark text-white rounded-pill shadow-lg px-4 py-3 d-flex align-items-center gap-3 border border-secondary border-opacity-50">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold fs-6" id="floatingDirtyCount">0</span>
            <span class="small fw-semibold text-white">prix d'achat modifié(s) non enregistré(s)</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="resetDirtyCosts()">Annuler</button>
            <button type="button" class="btn btn-sm btn-success rounded-pill px-3.5 fw-bold" onclick="saveAllDirtyCosts()">
                <i class="fas fa-floppy-disk me-1"></i> Tout Enregistrer
            </button>
        </div>
    </div>
</div>

<!-- Margin Simulator & Calculator Modal -->
<div class="modal fade" id="marginSimulatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white py-3 px-3.5 border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-calculator text-warning"></i>
                    <h6 class="modal-title fw-bold mb-0">Simulateur & Calculateur de Marge</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="simProductId">

                <div class="mb-3">
                    <span class="badge bg-light text-dark border font-monospace mb-1" id="simSkuBadge">PRODUIT</span>
                    <h6 class="fw-bold text-dark mb-0" id="simProductName">—</h6>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-1">Prix de Vente (DH)</label>
                        <input type="number" step="0.01" class="form-control rounded-3 fw-bold" id="simSellingPrice" readonly>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-1">Prix de Revient (DH)</label>
                        <input type="number" step="0.01" min="0" class="form-control rounded-3 fw-bold" id="simCostPrice" oninput="runSimulatorForward()">
                    </div>
                </div>

                <!-- Preset Target Margin Quick Buttons -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted mb-1">Objectif de Marge Rapide :</label>
                    <div class="d-flex gap-1.5 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyTargetMargin(20)">20%</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyTargetMargin(30)">30%</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyTargetMargin(40)">40%</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyTargetMargin(50)">50%</button>
                    </div>
                </div>

                <!-- Simulation Results Card -->
                <div class="p-3 rounded-4 bg-light border space-y-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">Bénéfice Net Unitaire :</span>
                        <strong class="fs-6 text-success" id="simProfitDh">0,00 DH</strong>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">Taux de Marge Brute (sur Vente) :</span>
                        <span class="badge bg-success-subtle text-success fs-6 fw-bold px-2.5 py-1 rounded-pill" id="simMarginPct">0%</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="text-muted small">Taux de Marque / Markup (sur Coût) :</span>
                        <strong class="text-primary small" id="simMarkupPct">+0%</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-2.5 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold" onclick="applySimulatorToTable()">
                    <i class="fas fa-check me-1"></i> Appliquer & Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// State of edited cost inputs { productId: newCostPrice }
const dirtyCosts = {};

/**
 * Handle input keystrokes in desktop table
 */
function handleCostChange(productId) {
    const input = document.getElementById(`cost-input-${productId}`);
    const row = document.getElementById(`row-prod-${productId}`);
    const inputGroup = document.getElementById(`input-group-${productId}`);
    const sellingPrice = parseFloat(row.getAttribute('data-selling-price')) || 0;
    const originalCost = parseFloat(input.getAttribute('data-original-cost')) || 0;
    const newCost = parseFloat(input.value) || 0;

    // Check if dirty
    if (Math.abs(newCost - originalCost) > 0.001) {
        dirtyCosts[productId] = newCost;
        inputGroup.classList.add('is-dirty');
    } else {
        delete dirtyCosts[productId];
        inputGroup.classList.remove('is-dirty');
    }

    // Recalculate live margin
    updateRowMarginVisual(productId, sellingPrice, newCost);
    updateDirtyBar();
}

/**
 * Handle Enter key in input
 */
function handleCostKeydown(e, productId) {
    if (e.key === 'Enter') {
        e.preventDefault();
        saveSingleCost(productId);
    }
}

/**
 * Recalculate margin and badge colors
 */
function updateRowMarginVisual(productId, sellingPrice, costPrice) {
    const profitEl = document.getElementById(`profit-dh-${productId}`);
    const badgeWrap = document.getElementById(`margin-badge-wrap-${productId}`);

    const profit = sellingPrice - costPrice;
    const marginPct = sellingPrice > 0 ? ((profit / sellingPrice) * 100).toFixed(1) : 0;

    profitEl.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(profit) + ' DH';
    profitEl.className = `fw-bold fs-6 ${profit >= 0 ? 'text-success' : 'text-danger'}`;

    let badgeClass = 'bg-secondary-subtle text-secondary';
    let label = `${marginPct}% marge`;

    if (costPrice <= 0) {
        badgeClass = 'bg-warning-subtle text-warning';
        label = 'Coût non défini';
    } else if (profit < 0) {
        badgeClass = 'bg-danger-subtle text-danger';
        label = `${marginPct}% perte`;
    } else if (marginPct >= 30) {
        badgeClass = 'bg-success-subtle text-success';
    } else if (marginPct >= 15) {
        badgeClass = 'bg-primary-subtle text-primary';
    } else {
        badgeClass = 'bg-warning-subtle text-warning';
    }

    badgeWrap.innerHTML = `<span class="badge ${badgeClass} border rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.7rem;">${label}</span>`;
}

/**
 * Save single cost via AJAX
 */
function saveSingleCost(productId) {
    const input = document.getElementById(`cost-input-${productId}`);
    const btn = document.getElementById(`btn-save-${productId}`);
    const cost = parseFloat(input.value);

    if (isNaN(cost) || cost < 0) {
        alert("Veuillez renseigner un coût d'achat valide (>= 0).");
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`/reports/profitability/products/${productId}/cost`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ cost_price: cost })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.className = 'btn btn-sm btn-success rounded-circle text-white d-inline-flex align-items-center justify-content-center';
        
        setTimeout(() => {
            btn.className = 'btn btn-sm btn-outline-success rounded-circle d-inline-flex align-items-center justify-content-center';
        }, 1500);

        input.setAttribute('data-original-cost', cost);
        document.getElementById(`input-group-${productId}`).classList.remove('is-dirty');
        delete dirtyCosts[productId];
        updateDirtyBar();
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        alert("Erreur lors de l'enregistrement du coût d'achat.");
    });
}

/**
 * Mobile input change
 */
function handleMobCostChange(productId, sellingPrice) {
    const input = document.getElementById(`mob-cost-input-${productId}`);
    const profitEl = document.getElementById(`mob-profit-${productId}`);
    const badgeEl = document.getElementById(`mob-badge-${productId}`);
    const cost = parseFloat(input.value) || 0;

    const profit = sellingPrice - cost;
    const marginPct = sellingPrice > 0 ? ((profit / sellingPrice) * 100).toFixed(1) : 0;

    profitEl.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(profit) + ' DH';
    profitEl.className = profit >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
    badgeEl.textContent = `${marginPct}%`;
}

function saveMobCost(productId) {
    const input = document.getElementById(`mob-cost-input-${productId}`);
    const cost = parseFloat(input.value);

    if (isNaN(cost) || cost < 0) {
        alert("Veuillez entrer un coût valide.");
        return;
    }

    fetch(`/reports/profitability/products/${productId}/cost`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ cost_price: cost })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || "Prix de revient enregistré !");
    })
    .catch(err => alert("Erreur d'enregistrement"));
}

/**
 * Update dirty bar counter
 */
function updateDirtyBar() {
    const count = Object.keys(dirtyCosts).length;
    const floatingBar = document.getElementById('floatingBatchBar');
    const floatingCount = document.getElementById('floatingDirtyCount');
    const topBtn = document.getElementById('btnSaveAllTop');
    const topBadge = document.getElementById('dirtyCountBadgeTop');

    if (count > 0) {
        floatingBar.classList.add('show');
        floatingCount.textContent = count;
        topBtn.disabled = false;
        topBadge.textContent = count;
        topBadge.classList.remove('d-none');
    } else {
        floatingBar.classList.remove('show');
        topBtn.disabled = true;
        topBadge.classList.add('d-none');
    }
}

/**
 * Save all modified costs in batch
 */
function saveAllDirtyCosts() {
    const items = Object.entries(dirtyCosts).map(([id, cost]) => ({ id: parseInt(id), cost_price: cost }));
    if (items.length === 0) return;

    const topBtn = document.getElementById('btnSaveAllTop');
    topBtn.disabled = true;
    topBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1.5"></i> Enregistrement en cours...';

    fetch('{{ route("reports.profitability.update-costs-batch") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ costs: items })
    })
    .then(res => res.json())
    .then(data => {
        window.location.reload();
    })
    .catch(err => {
        alert("Erreur lors de l'enregistrement en lot.");
        topBtn.disabled = false;
        topBtn.innerHTML = '<i class="fas fa-floppy-disk me-1.5"></i> Enregistrer les Modifications';
    });
}

function resetDirtyCosts() {
    for (const [productId, val] of Object.entries(dirtyCosts)) {
        const input = document.getElementById(`cost-input-${productId}`);
        if (input) {
            const original = input.getAttribute('data-original-cost');
            input.value = original > 0 ? original : '';
            document.getElementById(`input-group-${productId}`).classList.remove('is-dirty');
        }
    }
    for (const k in dirtyCosts) delete dirtyCosts[k];
    updateDirtyBar();
}

/**
 * Margin Simulator Modal
 */
function openSimulatorModal(productId, productName, sellingPrice, costPrice) {
    document.getElementById('simProductId').value = productId;
    document.getElementById('simProductName').textContent = productName;
    document.getElementById('simSellingPrice').value = sellingPrice;
    document.getElementById('simCostPrice').value = costPrice > 0 ? costPrice : '';
    runSimulatorForward();

    const modal = new bootstrap.Modal(document.getElementById('marginSimulatorModal'));
    modal.show();
}

function runSimulatorForward() {
    const sellingPrice = parseFloat(document.getElementById('simSellingPrice').value) || 0;
    const costPrice = parseFloat(document.getElementById('simCostPrice').value) || 0;

    const profit = sellingPrice - costPrice;
    const marginPct = sellingPrice > 0 ? ((profit / sellingPrice) * 100).toFixed(1) : 0;
    const markupPct = costPrice > 0 ? ((profit / costPrice) * 100).toFixed(1) : 0;

    const profitEl = document.getElementById('simProfitDh');
    profitEl.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(profit) + ' DH';
    profitEl.className = `fs-6 fw-bold ${profit >= 0 ? 'text-success' : 'text-danger'}`;

    const marginEl = document.getElementById('simMarginPct');
    marginEl.textContent = `${marginPct}%`;
    marginEl.className = `badge fs-6 fw-bold px-2.5 py-1 rounded-pill ${marginPct >= 30 ? 'bg-success-subtle text-success' : (marginPct >= 15 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning')}`;

    document.getElementById('simMarkupPct').textContent = `+${markupPct}%`;
}

function applyTargetMargin(targetPct) {
    const sellingPrice = parseFloat(document.getElementById('simSellingPrice').value) || 0;
    if (sellingPrice <= 0) return;

    const recommendedCost = (sellingPrice * (1 - (targetPct / 100))).toFixed(2);
    document.getElementById('simCostPrice').value = recommendedCost;
    runSimulatorForward();
}

function applySimulatorToTable() {
    const productId = document.getElementById('simProductId').value;
    const cost = parseFloat(document.getElementById('simCostPrice').value);

    if (isNaN(cost) || cost < 0) {
        alert("Coût invalide.");
        return;
    }

    const input = document.getElementById(`cost-input-${productId}`);
    if (input) {
        input.value = cost;
        handleCostChange(productId);
        saveSingleCost(productId);
    }

    bootstrap.Modal.getInstance(document.getElementById('marginSimulatorModal')).hide();
}
</script>
@endpush
@endsection
