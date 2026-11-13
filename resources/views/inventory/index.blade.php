@extends('layouts.app')

@section('title', 'Gestion des stocks & inventaire')

@push('styles')
<style>
.chips-scroll-store {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 4px;
}
.chips-scroll-store::-webkit-scrollbar { display: none; }
.chip-store {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.45rem 0.95rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    flex-shrink: 0;
}
.chip-store:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
}
.chip-store.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.16);
}
.chip-store.active .badge {
    background: rgba(255, 255, 255, 0.22) !important;
    color: #ffffff !important;
    border-color: transparent !important;
}
.store-inline-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 600;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
}
.store-inline-pill:hover {
    background: #ffffff;
    border-color: #ef4444;
    color: #0f172a;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
}
</style>
@endpush

@section('content')
@php
    $selectedStoreId = $selectedStoreId ?? null;
    $selectedStore = $selectedStore ?? null;
    $stores = $stores ?? collect();
    $storeUnitStats = $storeUnitStats ?? collect();
    $isAdmin = auth()->check() && auth()->user()->hasRole('Admin');
    $totalAllStoreUnits = (int) $storeUnitStats->sum('total_units');
@endphp

    <!-- En-tête de page -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div>
                <h1 class="brand-title">
                    Inventaire &amp; Stocks
                    @if($selectedStore)
                        <span class="badge bg-dark text-white rounded-pill ms-1.5 align-middle" style="font-size: 0.72rem;">
                            <i class="fas {{ $selectedStore->type === 'warehouse' ? 'fa-warehouse' : 'fa-store' }} me-1"></i>{{ $selectedStore->name }}
                        </span>
                    @endif
                </h1>
                <p class="brand-subtitle">
                    @if($selectedStore)
                        Gestion des quantités, emplacements (Rayon / Casier) et réapprovisionnements pour {{ $selectedStore->name }} ({{ $selectedStore->code }})
                    @else
                        Pilotez vos stocks globalement et par magasin, showroom ou dépôt en temps réel
                    @endif
                </p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            @if($stores->count() >= 2 && (auth()->user()?->hasRole('Admin') || auth()->user()?->can('manage_inventory') || auth()->user()?->can('manage_stores')))
            <button type="button" class="btn-brand-primary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;" onclick="openInventoryTransferModal(null, {{ $selectedStoreId ? $selectedStoreId : 'null' }})">
                <i class="fas fa-arrow-right-arrow-left"></i> <span>Transférer Stock</span>
            </button>
            @endif
            <a href="{{ route('inventory.alerts', array_filter(['store_id' => $selectedStoreId])) }}" class="btn btn-outline-warning shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-bell text-warning"></i>
                <span>Alertes</span>
                @if(($stats['low_stock'] + $stats['out_of_stock']) > 0)
                    <span class="badge bg-danger text-white rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $stats['low_stock'] + $stats['out_of_stock'] }}</span>
                @endif
            </a>
            <a href="{{ route('inventory.movements', array_filter(['store_id' => $selectedStoreId])) }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-history text-primary"></i> <span>Mouvements</span>
            </a>
            <a href="{{ route('inventory.export', request()->all()) }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-none d-md-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
            </a>
        </div>
    </div>

    <!-- Flash Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-circle-check fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-triangle-exclamation fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <!-- Navigation Croisée ERP (Inventaire <-> Matrice des Rayons <-> Dépôts) -->
    @include('partials.subnav.inventory')

    <!-- Sélecteur Rapide de Magasin / Dépôt / Point de Stockage -->
    @if($stores->isNotEmpty())
    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between mb-1.5">
            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.04em;">
                <i class="fas fa-warehouse text-danger me-1"></i> Stock par Magasin &amp; Dépôt
            </span>
            @if(auth()->user()?->hasRole('Admin') || auth()->user()?->can('manage_stores'))
            <a href="{{ route('stores.index') }}" class="text-decoration-none small fw-semibold text-primary" style="font-size: 0.75rem;">
                <i class="fas fa-plus-circle me-0.5"></i> Gérer les dépôts
            </a>
            @endif
        </div>
        <div class="chips-scroll-store">
            <a href="{{ route('inventory.index', request()->except(['page', 'store_id'])) }}"
               class="chip-store {{ !$selectedStoreId ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked"></i>
                <span>Tous les dépôts</span>
                <span class="badge bg-light text-dark border rounded-pill px-1.5" style="font-size: 0.68rem;">
                    {{ number_format($totalAllStoreUnits, 0, ',', ' ') }} pcs
                </span>
                @if(($unallocatedUnits ?? 0) > 0)
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-1.5" style="font-size: 0.64rem;" title="Unités en stock global non encore affectées à un dépôt">
                        +{{ number_format($unallocatedUnits, 0, ',', ' ') }} non affecté
                    </span>
                @endif
            </a>
            @foreach($stores as $st)
                @php
                    $isActiveStore = (int) $selectedStoreId === (int) $st->id;
                    $stUnits = (int) ($storeUnitStats->get($st->id)->total_units ?? 0);
                    $stIcon = match($st->type) {
                        'warehouse' => 'fa-warehouse text-purple',
                        'showroom' => 'fa-tv text-info',
                        'workshop' => 'fa-wrench text-warning',
                        default => 'fa-shop text-primary',
                    };
                @endphp
                <a href="{{ route('inventory.index', array_merge(request()->except(['page', 'store_id']), ['store_id' => $st->id])) }}"
                   class="chip-store {{ $isActiveStore ? 'active' : '' }}">
                    <i class="fas {{ $stIcon }}"></i>
                    <span>{{ $st->name }}</span>
                    <span class="badge bg-light text-dark border rounded-pill px-1.5" style="font-size: 0.68rem;">
                        {{ number_format($stUnits, 0, ',', ' ') }} pcs
                    </span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- KPI Metric Stat Cards (Row of 4) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.index', array_filter(['store_id' => $selectedStoreId])) }}" class="stat-kpi-card text-decoration-none {{ !request('stock_status') && !request('search') && !request('category_id') && !request('location_code') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">{{ $selectedStore ? 'Unités sur ce site' : 'Total Articles' }}</div>
                        <div class="stat-kpi-value text-dark">
                            {{ number_format($selectedStore ? ($stats['total_units'] ?? 0) : ($stats['total_products'] ?? 0), 0, ',', ' ') }}
                        </div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">
                                {{ $selectedStore ? ($stats['stocked_products'] ?? 0) . ' réf. en stock' : 'Articles suivis' }}
                            </span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas {{ $selectedStore ? 'fa-warehouse' : 'fa-boxes-stacked' }}"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.index', array_filter(['store_id' => $selectedStoreId, 'stock_status' => 'low_stock'])) }}" class="stat-kpi-card text-decoration-none {{ request('stock_status') === 'low_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Stock Faible</div>
                        <div class="stat-kpi-value text-warning">{{ number_format($stats['low_stock'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-clock text-warning me-1"></i>{{ $selectedStore ? 'Sous le seuil du site' : 'À réapprovisionner' }}
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.index', array_filter(['store_id' => $selectedStoreId, 'stock_status' => 'out_of_stock'])) }}" class="stat-kpi-card text-decoration-none {{ request('stock_status') === 'out_of_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Rupture de Stock</div>
                        <div class="stat-kpi-value text-danger">{{ number_format($stats['out_of_stock'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-bolt text-danger me-1"></i>{{ $selectedStore ? 'Absent de ce dépôt' : 'Action immédiate' }}
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.index', array_filter(['store_id' => $selectedStoreId, 'stock_status' => 'in_stock'])) }}" class="stat-kpi-card text-decoration-none {{ request('stock_status') === 'in_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        @if($isAdmin)
                            <div class="stat-kpi-label">{{ $selectedStore ? 'Valeur Dépôt' : 'Valeur du Stock' }}</div>
                            <div class="stat-kpi-value text-success" style="font-size: clamp(0.95rem, 3.5vw, 1.35rem);">{{ currency($stats['total_stock_value'] ?? 0) }}</div>
                            <div class="stat-kpi-sub text-muted">
                                <i class="fas fa-coins text-success me-1"></i>{{ number_format($stats['total_units'] ?? 0, 0, ',', ' ') }} unité(s)
                            </div>
                        @else
                            <div class="stat-kpi-label">En Stock</div>
                            <div class="stat-kpi-value text-success">{{ number_format($stats['stocked_products'] ?? 0, 0, ',', ' ') }}</div>
                            <div class="stat-kpi-sub text-muted">
                                <i class="fas fa-cubes text-success me-1"></i>{{ number_format($stats['total_units'] ?? 0, 0, ',', ' ') }} unité(s)
                            </div>
                        @endif
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-vault"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre de filtres unifiée -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('inventory.index') }}" id="inventoryFilterForm">
            <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 240px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Rechercher par nom d'article ou référence SKU..."
                           value="{{ request('search') }}">
                </div>

                @php
                    $activeFilterKeys = ['store_id', 'category_id', 'stock_status', 'location_code', 'sort_by', 'per_page'];
                    $hasActiveFilters = collect($activeFilterKeys)->some(fn($k) => request()->filled($k));
                    $activeFilterCount = collect($activeFilterKeys)->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <button type="button"
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="collapse"
                        data-bs-target="#inventoryFiltersCollapse"
                        aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filtres</span>
                    @if($activeFilterCount > 0)
                        <span class="badge bg-white text-dark rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $activeFilterCount }}</span>
                    @endif
                </button>

                <div class="d-none d-md-flex align-items-center gap-2">
                    <button type="submit" class="btn-brand-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1.5" title="Appliquer les filtres">
                        <i class="fas fa-filter"></i> <span>Filtrer</span>
                    </button>
                    @if(request('search') || $hasActiveFilters)
                        <a href="{{ route('inventory.index') }}" class="btn-brand-light btn-sm px-2.5 py-2 text-decoration-none" title="Réinitialiser tous les filtres">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                    <span class="badge bg-light text-secondary border px-3 py-2 fw-semibold" style="font-size: 0.8rem;">
                        <i class="fas fa-boxes-stacked text-primary me-1.5"></i>{{ $products->total() }} article(s)
                    </span>
                </div>
            </div>

            <div class="collapse d-md-block mt-2.5" id="inventoryFiltersCollapse">
                <div class="row g-2 pt-2 border-top">
                    <!-- Magasin / Dépôt -->
                    <div class="col-6 col-lg-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Magasin / Dépôt</label>
                        <select name="store_id" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="">Tous les dépôts (Global)</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ (int)$selectedStoreId === (int)$st->id ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Catégorie -->
                    <div class="col-6 col-lg-2">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Catégorie</label>
                        <select name="category_id" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="">Toutes catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Statut du Stock -->
                    <div class="col-6 col-lg-2">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Niveau Stock</label>
                        <select name="stock_status" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="">Tous statuts</option>
                            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>En stock</option>
                            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Stock faible</option>
                            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Rupture (0)</option>
                        </select>
                    </div>

                    <!-- Emplacement / Rayon -->
                    <div class="col-6 col-lg-2">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Emplacement / Rayon</label>
                        <input type="text" name="location_code" class="form-control form-control-sm"
                               placeholder="Ex: Rayon A, B2..."
                               value="{{ request('location_code') }}">
                    </div>

                    <!-- Tri par -->
                    <div class="col-6 col-lg-2">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Trier par</label>
                        <select name="sort_by" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="name" {{ request('sort_by', 'name') === 'name' ? 'selected' : '' }}>Nom du produit</option>
                            <option value="sku" {{ request('sort_by') === 'sku' ? 'selected' : '' }}>Code SKU</option>
                            <option value="stock" {{ request('sort_by') === 'stock' ? 'selected' : '' }}>Quantité {{ $selectedStore ? '(ce dépôt)' : '' }}</option>
                            <option value="sold_last_30_days" {{ request('sort_by') === 'sold_last_30_days' ? 'selected' : '' }}>Ventes (30j)</option>
                        </select>
                    </div>

                    <!-- Par page -->
                    <div class="col-6 col-lg-1">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Page</label>
                        <select name="per_page" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>

                <!-- Drawer Mobile Buttons -->
                <div class="d-flex d-md-none align-items-center gap-2 mt-3 pt-2 border-top">
                    <button type="submit" class="btn btn-brand-primary btn-sm flex-grow-1 py-2">
                        <i class="fas fa-check me-1"></i> Appliquer
                    </button>
                    @if(request('search') || $hasActiveFilters)
                        <a href="{{ route('inventory.index') }}" class="btn btn-brand-light btn-sm px-2.5 py-2 text-decoration-none" title="Réinitialiser">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                    <a href="{{ route('inventory.export', request()->all()) }}" class="btn btn-outline-secondary btn-sm px-2.5 py-2 text-decoration-none d-inline-flex align-items-center gap-1.5" title="Exporter CSV">
                        <i class="fas fa-file-csv text-success"></i> <span>Export</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- AFFICHAGE MOBILE -->
    <div class="d-block d-md-none">
        @forelse($products as $product)
            @include('inventory.partials.mobile-inventory-card', [
                'product' => $product,
                'stores' => $stores,
                'selectedStore' => $selectedStore,
                'selectedStoreId' => $selectedStoreId,
            ])
        @empty
            <div class="text-center py-5 bg-white rounded-3 border">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                    <i class="fas fa-box-open"></i>
                </div>
                <h6 class="fw-bold text-dark">Aucun article trouvé</h6>
                <p class="text-muted small mb-3">Modifiez vos critères de recherche ou réinitialisez les filtres.</p>
                <a href="{{ route('inventory.index') }}" class="btn btn-brand-light btn-sm">
                    <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                </a>
            </div>
        @endforelse

        @if($products->hasPages())
            <div class="mt-3 mb-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- AFFICHAGE DESKTOP -->
    <div class="brand-table-card inventory-table-card d-none d-md-block shadow-sm border" style="overflow: hidden !important;">
        <div style="overflow-x: hidden !important; width: 100% !important;">
            <table class="brand-table align-middle" style="table-layout: fixed !important; width: 100% !important; margin: 0 !important;">
                <thead>
                    <tr>
                        <th style="width: 24%; padding-left: 1.25rem;">Article</th>
                        <th style="width: 26%;">
                            {{ $selectedStore ? 'Stock : ' . $selectedStore->name : 'Stock & Répartition Dépôts' }}
                        </th>
                        <th class="text-center" style="width: 8%;">Ventes (30j)</th>
                        <th class="text-center" style="width: 9%;">Autonomie</th>
                        <th class="text-center" style="width: 8%;">{{ $selectedStore ? 'Seuil Site' : 'Seuil' }}</th>
                        <th class="text-end" style="width: 11%;">{{ $isAdmin ? 'Valeur Stock' : 'Statut' }}</th>
                        <th class="text-end" style="width: 14%; padding-right: 1.25rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    @php
                        $activeStoreRec = $selectedStoreId ? $product->storeStocks->firstWhere('store_id', $selectedStoreId) : null;
                        $stock = $selectedStoreId
                            ? (int) ($activeStoreRec->quantity ?? 0)
                            : (int) ($product->stock ?? 0);
                        $threshold = $selectedStoreId
                            ? (int) ($activeStoreRec->min_quantity ?? 2)
                            : (int) ($product->low_stock_threshold ?? $product->min_stock ?? 10);
                        $storeLoc = $activeStoreRec->location_code ?? null;
                        $isTracked = (bool) $product->track_inventory;

                        if (!$isTracked) {
                            $badgeClass = 'secondary';
                            $badgeText = 'Non suivi';
                            $barClass = 'bg-secondary';
                            $percent = 0;
                        } elseif ($stock <= 0) {
                            $badgeClass = 'danger';
                            $badgeText = 'Rupture';
                            $barClass = 'bg-danger';
                            $percent = 0;
                        } elseif ($stock <= $threshold) {
                            $badgeClass = 'warning';
                            $badgeText = 'Faible';
                            $barClass = 'bg-warning';
                            $percent = max(10, min(100, ($stock / max(1, $threshold * 2)) * 100));
                        } else {
                            $badgeClass = 'success';
                            $badgeText = 'En stock';
                            $barClass = 'bg-success';
                            $percent = min(100, ($stock / max(1, $threshold * 3)) * 100);
                        }

                        $daysText = '—';
                        $forecastType = '';
                        if ($isTracked && $stock > 0 && ($product->sold_last_30_days ?? 0) > 0) {
                            $dailyVelocity = $product->sold_last_30_days / 30;
                            $daysCalc = round($stock / $dailyVelocity);
                            if ($daysCalc > 365) {
                                $daysText = '> 1 an';
                                $forecastType = 'success';
                            } elseif ($daysCalc > 30) {
                                $daysText = $daysCalc . ' j';
                                $forecastType = 'success';
                            } elseif ($daysCalc > 7) {
                                $daysText = $daysCalc . ' j';
                                $forecastType = 'warning';
                            } else {
                                $daysText = $daysCalc . ' j';
                                $forecastType = 'danger';
                            }
                        } elseif ($isTracked && $stock <= 0) {
                            $daysText = '0 j';
                            $forecastType = 'danger';
                        }

                        $reorderPt = '—';
                        $reorderClass = 'text-muted';
                        if ($selectedStoreId) {
                            $reorderPt = $threshold;
                            if ($isTracked && $stock <= $threshold) {
                                $reorderClass = 'text-danger fw-bold';
                            }
                        } elseif ($isTracked && ($product->sold_last_30_days ?? 0) > 0) {
                            $avgDailySales = $product->sold_last_30_days / 30;
                            $leadTime = $product->lead_time_days ?? 7;
                            $safetyStock = $product->safety_stock ?? 5;
                            $reorderPt = ceil(($avgDailySales * $leadTime) + $safetyStock);
                            if ($stock <= $reorderPt) {
                                $reorderClass = 'text-danger fw-bold';
                            }
                        }
                    @endphp
                    <tr>
                        <!-- Article -->
                        <td style="padding-left: 1.25rem;">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <div class="mobile-inv-avatar flex-shrink-0" style="width: 38px; height: 38px;">
                                    <img src="{{ $product->thumbnail }}"
                                         alt="{{ $product->name }}"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                </div>
                                <div class="min-w-0 flex-grow-1" style="max-width: calc(100% - 46px);">
                                    <a href="{{ route('products.edit', $product) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.85rem;" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </a>
                                    <div class="d-flex align-items-center gap-1.5 mt-0.5 text-truncate">
                                        <span class="badge bg-light text-secondary border font-monospace px-1 py-0.5" style="font-size: 0.62rem;">
                                            {{ $product->sku ?? 'SANS-SKU' }}
                                        </span>
                                        <span class="text-muted small">•</span>
                                        <span class="text-muted small text-truncate" style="font-size: 0.72rem;">
                                            {{ $product->productCategory->name ?? ($product->category->name ?? 'Non classé') }}
                                        </span>
                                    </div>
                                    @if($product->variants && $product->variants->isNotEmpty())
                                    <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                        @foreach($product->variants as $variant)
                                            <button type="button"
                                                    class="btn btn-xs btn-outline-secondary py-0 px-1.5 d-inline-flex align-items-center gap-1 rounded"
                                                    style="font-size: 0.65rem; line-height: 1.4;"
                                                    onclick="openAdjustModal({{ $product->id }}, {{ $selectedStoreId ? $selectedStoreId : 'null' }}, {{ $variant->id }})"
                                                    title="Ajuster la variante {{ $variant->name }}">
                                                <span class="fw-semibold">{{ $variant->name }}:</span>
                                                <span class="badge {{ $variant->stock <= 0 ? 'bg-danger text-white' : ($variant->stock <= ($variant->min_stock ?: 5) ? 'bg-warning text-dark' : 'bg-light text-dark border') }} px-1 py-0" style="font-size: 0.6rem;">{{ $variant->stock }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Stock Global / Par Dépôt -->
                        <td>
                            @if($isTracked)
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="fw-bold text-dark fs-6">{{ $stock }}</span>
                                        <span class="brand-badge {{ $badgeClass }} py-0.5 px-1.5" style="font-size: 0.68rem;">
                                            {{ $badgeText }}
                                        </span>
                                        @if($selectedStoreId && $storeLoc)
                                            <span class="badge bg-light text-dark border font-monospace px-1.5 py-0.5" style="font-size: 0.65rem;" title="Emplacement Rayon">
                                                <i class="fas fa-map-pin text-danger me-0.5"></i>{{ $storeLoc }}
                                            </span>
                                        @endif
                                        @if($selectedStoreId)
                                            <span class="text-muted small" style="font-size: 0.68rem;">
                                                / {{ (int)($product->stock ?? 0) }} total
                                            </span>
                                        @endif
                                    </div>
                                    <div class="progress" style="height: 4px; border-radius: 999px; background: #e2e8f0; max-width: 140px;">
                                        <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ $percent }}%; border-radius: 999px;"></div>
                                    </div>
                                    @if(!$selectedStoreId && $stores->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1 mt-0.5">
                                        @foreach($stores as $st)
                                            @php
                                                $stRec = $product->storeStocks->firstWhere('store_id', $st->id);
                                                $stQty = (int) ($stRec->quantity ?? 0);
                                                $stLoc = $stRec->location_code ?? null;
                                                $stMin = (int) ($stRec->min_quantity ?? 2);
                                                $dotColor = $stQty <= 0 ? '#94a3b8' : ($stQty <= $stMin ? '#f59e0b' : '#10b981');
                                            @endphp
                                            <button type="button"
                                                    class="store-inline-pill"
                                                    onclick="openAdjustModal({{ $product->id }}, {{ $st->id }})"
                                                    title="Cliquer pour ajuster {{ $st->name }}{{ $stLoc ? ' · ' . $stLoc : '' }}">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                                <span class="text-truncate" style="max-width: 85px;">{{ $st->name }}</span>
                                                <strong class="text-dark">{{ $stQty }}</strong>
                                                @if($stLoc)
                                                    <span class="text-muted font-monospace" style="font-size: 0.6rem;">· {{ $stLoc }}</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            @else
                                <span class="brand-badge secondary py-0.5 px-2" style="font-size: 0.7rem;">Non suivi</span>
                            @endif
                        </td>

                        <!-- Ventes (30j) -->
                        <td class="text-center">
                            @if(($product->sold_last_30_days ?? 0) > 0)
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ number_format($product->sold_last_30_days) }}</div>
                                <div class="text-muted small" style="font-size: 0.68rem;">u. / mois</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Autonomie -->
                        <td class="text-center">
                            @if($forecastType)
                                <span class="badge bg-{{ $forecastType }}-subtle text-{{ $forecastType }} border px-2 py-1" style="font-size: 0.7rem;">
                                    <i class="fas fa-hourglass-half me-1"></i>{{ $daysText }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Seuil -->
                        <td class="text-center">
                            <span class="{{ $reorderClass }}" style="font-size: 0.85rem;">{{ $reorderPt }}</span>
                        </td>

                        <!-- Valeur marchande (Admin uniquement) / Statut -->
                        <td class="text-end">
                            @if($isAdmin && $isTracked && $product->cost_price)
                                <div class="fw-bold text-dark" style="font-size: 0.84rem;">{{ currency($stock * $product->cost_price) }}</div>
                                <div class="text-muted small" style="font-size: 0.68rem;">{{ currency($product->cost_price) }} / u.</div>
                            @elseif($isAdmin)
                                <span class="text-muted">—</span>
                            @else
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.65rem;"><i class="fas fa-lock me-1"></i>Réservé</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td style="padding-right: 1.25rem;" class="text-end">
                            <div class="action-buttons-group justify-content-end">
                                @if($isTracked)
                                <button type="button"
                                        class="action-btn action-btn-primary"
                                        onclick="openAdjustModal({{ $product->id }}, {{ $selectedStoreId ? $selectedStoreId : 'null' }})"
                                        title="Ajuster le stock {{ $selectedStore ? '(' . $selectedStore->name . ')' : '(global / par dépôt)' }}">
                                    <i class="fas fa-sliders-h"></i>
                                </button>
                                @endif
                                @if($stores->count() >= 2)
                                <button type="button"
                                        class="action-btn"
                                        onclick="openInventoryTransferModal({{ $product->id }}, {{ $selectedStoreId ? $selectedStoreId : 'null' }})"
                                        title="Transférer vers un autre dépôt">
                                    <i class="fas fa-arrow-right-arrow-left text-info"></i>
                                </button>
                                @endif
                                <a href="{{ route('inventory.movements', array_filter(['product_id' => $product->id, 'store_id' => $selectedStoreId])) }}"
                                   class="action-btn"
                                   title="Historique des mouvements">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="{{ route('inventory.adjust', array_filter(['product' => $product->id, 'store_id' => $selectedStoreId])) }}"
                                   class="action-btn"
                                   title="Fiche d'ajustement détaillée">
                                    <i class="fas fa-up-right-from-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Aucun produit trouvé</h6>
                                <p class="text-muted small mb-0">Essayez de modifier vos critères de recherche ou de filtre.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-4 py-3 border-top bg-light">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL AJUSTEMENT RAPIDE DE STOCK (GLOBAL OU PAR MAGASIN / DÉPÔT) -->
    <div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <form id="adjustStockForm" method="POST" action="">
                    @csrf

                    <div class="modal-header border-0 bg-light p-3.5 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="brand-header-icon" style="width: 36px; height: 36px; font-size: 16px;">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark fs-6" id="adjustStockModalLabel">Ajustement du Stock</h5>
                                <div class="text-muted small" style="font-size: 0.72rem;">Mise à jour immédiate globale ou par magasin / dépôt</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body p-3.5">
                        <!-- Product Preview Card -->
                        <div class="p-3 mb-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <div class="min-w-0">
                                <div class="fw-bold text-dark text-truncate" id="modalProductName" style="font-size: 0.95rem;">Nom du Produit</div>
                                <div class="text-muted small mt-0.5" style="font-size: 0.75rem;">
                                    <span id="modalScopeLabel">Niveau actuel</span> : <span class="fw-bold text-primary fs-6" id="modalCurrentStock">0</span> unité(s)
                                </div>
                            </div>
                            <div class="text-end ps-2">
                                <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Nouveau niveau</div>
                                <div class="fw-extrabold fs-5 text-dark" id="modalProjectedStock">0</div>
                            </div>
                        </div>

                        <!-- Magasin / Dépôt Selector -->
                        @if($stores->isNotEmpty())
                        <div class="mb-3 p-2.5 rounded-3 bg-light border">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                                <i class="fas fa-warehouse text-danger me-1"></i>Magasin / Point de stockage ciblé
                            </label>
                            <select name="store_id" id="modalStoreSelect" class="form-select form-select-sm" onchange="onStoreSelectionChange()">
                                <option value="">Stock global uniquement (tous dépôts)</option>
                                @foreach($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                                @endforeach
                            </select>

                            <div class="row g-2 mt-2" id="modalStoreMetaContainer" style="display: none;">
                                <div class="col-7">
                                    <label class="form-label text-muted small mb-0.5" style="font-size: 0.68rem;">Emplacement (Rayon / Casier)</label>
                                    <input type="text" name="location_code" id="modalLocationCodeInput" class="form-control form-control-sm" placeholder="Ex: Rayon A - B2">
                                </div>
                                <div class="col-5">
                                    <label class="form-label text-muted small mb-0.5" style="font-size: 0.68rem;">Seuil alerte site</label>
                                    <input type="number" min="0" name="min_quantity" id="modalMinQtyInput" class="form-control form-control-sm" value="2">
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Variant Selector -->
                        <div class="mb-3" id="modalVariantContainer" style="display: none;">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Déclinaison / Variante</label>
                            <select name="variant_id" id="modalVariantSelect" class="form-select form-select-sm" onchange="onVariantSelectionChange()">
                                <option value="">Ajustement sans variante</option>
                            </select>
                            <div class="form-text small" style="font-size: 0.7rem;">Sélectionnez une variante spécifique si nécessaire.</div>
                        </div>

                        <!-- Operation Type Selector -->
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1.5" style="font-size: 0.68rem; letter-spacing: 0.04em;">Type d'opération</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="operation-pill-btn active" id="pill-in" onclick="selectOperation('in')">
                                        <div class="text-success fs-5 mb-0.5"><i class="fas fa-arrow-down"></i></div>
                                        <div class="fw-bold text-dark" style="font-size: 0.78rem;">Entrée</div>
                                        <div class="text-muted" style="font-size: 0.65rem;">+ Ajouter</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="operation-pill-btn" id="pill-out" onclick="selectOperation('out')">
                                        <div class="text-danger fs-5 mb-0.5"><i class="fas fa-arrow-up"></i></div>
                                        <div class="fw-bold text-dark" style="font-size: 0.78rem;">Sortie</div>
                                        <div class="text-muted" style="font-size: 0.65rem;">- Retirer</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="operation-pill-btn" id="pill-adjustment" onclick="selectOperation('adjustment')">
                                        <div class="text-primary fs-5 mb-0.5"><i class="fas fa-equals"></i></div>
                                        <div class="fw-bold text-dark" style="font-size: 0.78rem;">Inventaire</div>
                                        <div class="text-muted" style="font-size: 0.65rem;">= Réel</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="adjustment_type" id="modalAdjustmentType" value="in">
                        </div>

                        <!-- Quantity Stepper -->
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;" id="modalQtyLabel">Quantité à ajouter</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="stepQuantity(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number"
                                       name="quantity"
                                       id="modalQuantityInput"
                                       class="form-control text-center fw-bold fs-5"
                                       value="1"
                                       min="1"
                                       required
                                       oninput="recalculateProjected()">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="stepQuantity(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="form-text small" id="modalQtyHelp" style="font-size: 0.72rem;">Nombre d'unités ajoutées à l'inventaire.</div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-2">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Motif de l'ajustement</label>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setReason('Arrivage fournisseur')">Arrivage</button>
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setReason('Retour client')">Retour client</button>
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setReason('Inventaire physique dépôt')">Inventaire audit</button>
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setReason('Produit cassé / défectueux')">Casse / Perte</button>
                            </div>
                            <textarea name="reason"
                                      id="modalReasonInput"
                                      class="form-control"
                                      rows="2"
                                      required
                                      placeholder="Précisez la raison de cet ajustement..."
                                      style="border-radius: 12px; font-size: 0.85rem;"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-3.5 pt-0">
                        <button type="submit" class="btn btn-brand-primary w-100 py-2.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-check"></i> <span>Enregistrer l'ajustement</span>
                        </button>
                        <button type="button" class="btn btn-link text-muted small w-100 text-decoration-none mt-1" data-bs-dismiss="modal">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TRANSFERT INTER-MAGASINS DIRECT DEPUIS INVENTAIRE -->
    @if($stores->count() >= 2)
    <div class="modal fade" id="transferInventoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('stores.stock.transfer') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fas fa-arrow-right-arrow-left"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 fs-6">Transfert de Stock Inter-Dépôts</h5>
                            <p class="small text-white-50 mb-0" style="font-size: 0.76rem;">Déplacer des quantités entre vos magasins, showrooms et dépôts</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Article à transférer <span class="text-danger">*</span></label>
                        <select name="product_id" id="inv_transfer_product_id" class="form-select rounded-3" required onchange="refreshTransferSourceLabels()">
                            <option value="">Sélectionnez un article...</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->sku ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Dépôt Source (Origine) <span class="text-danger">*</span></label>
                            <select name="from_store_id" id="inv_transfer_from_store" class="form-select rounded-3" required>
                                @foreach($stores as $st)
                                    <option value="{{ $st->id }}" data-name="{{ $st->name }} ({{ $st->code }})">{{ $st->name }} ({{ $st->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Dépôt Destination (Arrivée) <span class="text-danger">*</span></label>
                            <select name="to_store_id" id="inv_transfer_to_store" class="form-select rounded-3" required>
                                @foreach($stores as $idx => $st)
                                    <option value="{{ $st->id }}" data-name="{{ $st->name }} ({{ $st->code }})" {{ $idx === 1 ? 'selected' : '' }}>{{ $st->name }} ({{ $st->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Quantité à transférer <span class="text-danger">*</span></label>
                            <input type="number" min="1" step="1" name="quantity" class="form-control rounded-3 fw-bold" placeholder="Ex: 5" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Emplacement destination (Optionnel)</label>
                            <input type="text" name="to_location" class="form-control rounded-3" placeholder="Ex: Rayon B - Étagère 2">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark mb-1">Motif du transfert</label>
                        <input type="text" name="reason" class="form-control rounded-3" placeholder="Ex: Réapprovisionnement point de vente...">
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-xs">
                        <i class="fas fa-check-circle me-1.5"></i> Transférer le stock
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
@php
    $invMetaMap = [];
    foreach ($products as $prodItem) {
        $variantList = [];
        foreach ($prodItem->variants as $v) {
            $variantList[] = [
                'id' => $v->id,
                'name' => $v->name,
                'stock' => (int) ($v->stock ?? 0),
            ];
        }
        $storeStockList = [];
        foreach ($prodItem->storeStocks as $ss) {
            $storeStockList[] = [
                'store_id' => (int) $ss->store_id,
                'quantity' => (int) $ss->quantity,
                'location_code' => $ss->location_code,
                'min_quantity' => (int) ($ss->min_quantity ?? 2),
            ];
        }
        $invMetaMap[$prodItem->id] = [
            'name' => $prodItem->name,
            'stock' => (int) ($prodItem->stock ?? 0),
            'variants' => $variantList,
            'storeStocks' => $storeStockList,
        ];
    }
@endphp
<script>
    window.__invProducts = Object.assign(window.__invProducts || {}, @json($invMetaMap));

    let currentModalProductId = null;
    let currentProductStock = 0;
    let modalBaseStock = 0;
    let modalVariantsList = [];
    let modalStoreStocks = [];

    function openAdjustModal(productId, preferredStoreId = null, selectedVariantId = null) {
        const meta = window.__invProducts[productId];
        if (!meta) return;

        currentModalProductId = productId;
        modalBaseStock = parseInt(meta.stock, 10) || 0;
        modalVariantsList = meta.variants || [];
        modalStoreStocks = meta.storeStocks || [];

        document.getElementById('modalProductName').textContent = meta.name;
        const form = document.getElementById('adjustStockForm');
        form.action = `/inventory/${productId}/adjust`;

        // Populate Store Dropdown with live per-store counts
        const storeSelect = document.getElementById('modalStoreSelect');
        if (storeSelect) {
            Array.from(storeSelect.options).forEach(opt => {
                if (!opt.value) {
                    opt.textContent = `Stock global (${modalBaseStock} pcs)`;
                    return;
                }
                const baseLabel = opt.dataset.baseLabel || opt.textContent.replace(/\s*—\s*\d+\s*pcs$/, '');
                opt.dataset.baseLabel = baseLabel;
                const rec = modalStoreStocks.find(s => String(s.store_id) === String(opt.value));
                const qty = rec ? rec.quantity : 0;
                opt.textContent = `${baseLabel} — ${qty} pcs`;
            });
            storeSelect.value = preferredStoreId ? String(preferredStoreId) : '';
        }

        // Populate Variants Dropdown
        const variantContainer = document.getElementById('modalVariantContainer');
        const variantSelect = document.getElementById('modalVariantSelect');
        variantSelect.innerHTML = '<option value="">Ajustement produit (sans variante ciblée)</option>';

        if (modalVariantsList && modalVariantsList.length > 0) {
            variantContainer.style.display = 'block';
            modalVariantsList.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = `${v.name} (Stock global: ${v.stock ?? 0})`;
                opt.dataset.stock = v.stock ?? 0;
                if (selectedVariantId && String(v.id) === String(selectedVariantId)) {
                    opt.selected = true;
                }
                variantSelect.appendChild(opt);
            });
        } else {
            variantContainer.style.display = 'none';
        }

        document.getElementById('modalQuantityInput').value = 1;
        document.getElementById('modalReasonInput').value = '';
        selectOperation('in');
        syncModalStockContext();

        new bootstrap.Modal(document.getElementById('adjustStockModal')).show();
    }

    function syncModalStockContext() {
        const variantSelect = document.getElementById('modalVariantSelect');
        const storeSelect = document.getElementById('modalStoreSelect');
        const storeMetaBox = document.getElementById('modalStoreMetaContainer');
        const scopeLabel = document.getElementById('modalScopeLabel');

        const selectedVariant = variantSelect && variantSelect.value
            ? variantSelect.options[variantSelect.selectedIndex]
            : null;
        const selectedStoreId = storeSelect ? storeSelect.value : '';

        if (selectedStoreId) {
            const storeOpt = storeSelect.options[storeSelect.selectedIndex];
            const storeName = storeOpt ? (storeOpt.dataset.baseLabel || storeOpt.textContent) : 'Dépôt';
            const rec = modalStoreStocks.find(s => String(s.store_id) === String(selectedStoreId));
            currentProductStock = rec ? (parseInt(rec.quantity, 10) || 0) : 0;
            scopeLabel.textContent = `Stock sur ${storeName}`;

            if (storeMetaBox) {
                storeMetaBox.style.display = 'flex';
                document.getElementById('modalLocationCodeInput').value = rec && rec.location_code ? rec.location_code : '';
                document.getElementById('modalMinQtyInput').value = rec ? (rec.min_quantity ?? 2) : 2;
            }
        } else {
            if (storeMetaBox) storeMetaBox.style.display = 'none';
            if (selectedVariant) {
                currentProductStock = parseInt(selectedVariant.dataset.stock, 10) || 0;
                scopeLabel.textContent = 'Stock variante (global)';
            } else {
                currentProductStock = modalBaseStock;
                scopeLabel.textContent = 'Stock global';
            }
        }

        document.getElementById('modalCurrentStock').textContent = currentProductStock;
        if (document.getElementById('modalAdjustmentType').value === 'adjustment') {
            document.getElementById('modalQuantityInput').value = currentProductStock;
        }
        recalculateProjected();
    }

    function onStoreSelectionChange() {
        syncModalStockContext();
    }

    function onVariantSelectionChange() {
        syncModalStockContext();
    }

    function selectOperation(op) {
        document.getElementById('modalAdjustmentType').value = op;

        ['in', 'out', 'adjustment'].forEach(type => {
            const pill = document.getElementById('pill-' + type);
            if (pill) {
                pill.classList.toggle('active', type === op);
            }
        });

        const qtyLabel = document.getElementById('modalQtyLabel');
        const qtyHelp = document.getElementById('modalQtyHelp');
        const qtyInput = document.getElementById('modalQuantityInput');

        if (op === 'in') {
            qtyLabel.textContent = "Quantité à ajouter";
            qtyHelp.textContent = "Unités ajoutées au stock sélectionné.";
            qtyInput.min = 1;
            if (parseInt(qtyInput.value, 10) < 1) qtyInput.value = 1;
        } else if (op === 'out') {
            qtyLabel.textContent = "Quantité à retirer";
            qtyHelp.textContent = "Unités déduites du stock sélectionné.";
            qtyInput.min = 1;
            if (parseInt(qtyInput.value, 10) < 1) qtyInput.value = 1;
        } else {
            qtyLabel.textContent = "Nouveau stock réel (exact)";
            qtyHelp.textContent = "Valeur exacte comptée dans le dépôt / produit.";
            qtyInput.min = 0;
            qtyInput.value = currentProductStock;
        }

        recalculateProjected();
    }

    function stepQuantity(delta) {
        const input = document.getElementById('modalQuantityInput');
        let current = parseInt(input.value, 10) || 0;
        const minVal = parseInt(input.min, 10) || 0;
        let next = Math.max(minVal, current + delta);
        input.value = next;
        recalculateProjected();
    }

    function recalculateProjected() {
        const op = document.getElementById('modalAdjustmentType').value;
        const qty = parseInt(document.getElementById('modalQuantityInput').value, 10) || 0;
        const projEl = document.getElementById('modalProjectedStock');

        let projected = currentProductStock;
        if (op === 'in') {
            projected = currentProductStock + qty;
            projEl.className = 'fw-extrabold fs-5 text-success';
        } else if (op === 'out') {
            projected = currentProductStock - qty;
            projEl.className = projected < 0 ? 'fw-extrabold fs-5 text-danger' : 'fw-extrabold fs-5 text-warning';
        } else {
            projected = qty;
            projEl.className = 'fw-extrabold fs-5 text-primary';
        }

        projEl.textContent = projected;
    }

    function setReason(text) {
        const reasonInput = document.getElementById('modalReasonInput');
        reasonInput.value = text;
        reasonInput.focus();
    }

    function openInventoryTransferModal(productId = null, sourceStoreId = null) {
        const modalEl = document.getElementById('transferInventoryModal');
        if (!modalEl) return;

        const prodSelect = document.getElementById('inv_transfer_product_id');
        if (prodSelect && productId) {
            prodSelect.value = String(productId);
        }

        const fromSelect = document.getElementById('inv_transfer_from_store');
        const toSelect = document.getElementById('inv_transfer_to_store');
        if (fromSelect && sourceStoreId) {
            fromSelect.value = String(sourceStoreId);
            if (toSelect && toSelect.value === fromSelect.value) {
                const other = Array.from(toSelect.options).find(o => o.value !== fromSelect.value);
                if (other) toSelect.value = other.value;
            }
        }

        refreshTransferSourceLabels();
        new bootstrap.Modal(modalEl).show();
    }

    function refreshTransferSourceLabels() {
        const prodSelect = document.getElementById('inv_transfer_product_id');
        const fromSelect = document.getElementById('inv_transfer_from_store');
        const toSelect = document.getElementById('inv_transfer_to_store');
        if (!prodSelect) return;

        const meta = window.__invProducts[prodSelect.value];
        [fromSelect, toSelect].forEach(sel => {
            if (!sel) return;
            Array.from(sel.options).forEach(opt => {
                const base = opt.dataset.name || opt.textContent;
                if (!meta) {
                    opt.textContent = base;
                    return;
                }
                const rec = (meta.storeStocks || []).find(s => String(s.store_id) === String(opt.value));
                const qty = rec ? rec.quantity : 0;
                opt.textContent = `${base} — ${qty} pcs`;
            });
        });
    }
</script>
@endpush
