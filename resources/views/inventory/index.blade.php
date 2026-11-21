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
.inv-transfer-store-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 12px;
    height: 100%;
}
.inv-transfer-store-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px;
}
.inv-transfer-store-card {
    appearance: none;
    width: 100%;
    text-align: left;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 7px 9px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}
.inv-transfer-store-card:hover {
    border-color: #94a3b8;
    background: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
}
.inv-transfer-store-card.is-source.active {
    background: #fff7ed;
    border-color: #ea580c;
    box-shadow: 0 0 0 2px rgba(234, 88, 12, 0.18);
}
.inv-transfer-store-card.is-dest.active {
    background: #ecfdf5;
    border-color: #059669;
    box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.18);
}
.inv-transfer-store-card.is-adjust.active {
    background: #eff6ff;
    border-color: #dc2626;
    box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.18);
}
.inv-transfer-store-card.is-empty {
    opacity: 0.62;
}
.inv-transfer-store-card .inv-store-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    width: 100%;
}
.inv-transfer-store-card .inv-store-name {
    font-size: 0.78rem;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.inv-transfer-store-card .inv-store-check {
    display: none;
    font-size: 0.72rem;
    flex-shrink: 0;
}
.inv-transfer-store-card.active .inv-store-check {
    display: inline-block;
}
.inv-transfer-store-card.is-source.active .inv-store-check {
    color: #ea580c;
}
.inv-transfer-store-card.is-dest.active .inv-store-check {
    color: #059669;
}
.inv-transfer-store-card.is-adjust.active .inv-store-check {
    color: #dc2626;
}
.inv-transfer-store-card .inv-store-card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    font-size: 0.68rem;
    color: #64748b;
}
.inv-transfer-store-card .inv-store-qty {
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 9999px;
    background: #f1f5f9;
    color: #334155;
    font-variant-numeric: tabular-nums;
}
.inv-transfer-store-card.is-has-stock .inv-store-qty {
    background: #dcfce7;
    color: #166534;
}
.inv-transfer-store-card.is-empty .inv-store-qty {
    background: #fee2e2;
    color: #991b1b;
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
                @php $alertsBadgeCount = $unacknowledgedAlertsCount ?? ($stats['low_stock'] + $stats['out_of_stock']); @endphp
                @if($alertsBadgeCount > 0)
                    <span class="badge bg-danger text-white rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $alertsBadgeCount }}</span>
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
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-3" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="fas fa-triangle-exclamation fs-5 mt-0.5"></i>
                <div>
                    <div class="fw-semibold">Veuillez corriger les erreurs suivantes :</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
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
                        'warehouse' => 'fa-warehouse text-danger',
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
                    $activeFilterKeys = ['store_id', 'category_id', 'stock_status', 'location_code', 'sort_by', 'sort_dir', 'per_page'];
                    $hasActiveFilters = collect($activeFilterKeys)->some(fn($k) => request()->filled($k));
                    $activeFilterCount = collect($activeFilterKeys)->filter(fn($k) => request()->filled($k))->count();
                    $currentSortBy = $sortBy ?? request('sort_by', 'name');
                    $currentSortDir = $sortDir ?? request('sort_dir', 'asc');
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
                        <div class="input-group input-group-sm">
                            <select name="sort_by" class="form-select form-select-sm" onchange="onSortByChange(this)">
                                <option value="name" {{ $currentSortBy === 'name' ? 'selected' : '' }}>Nom du produit</option>
                                <option value="sku" {{ $currentSortBy === 'sku' ? 'selected' : '' }}>Code SKU</option>
                                <option value="stock" {{ $currentSortBy === 'stock' ? 'selected' : '' }}>Quantité {{ $selectedStore ? '(ce dépôt)' : '' }}</option>
                                <option value="sold_last_30_days" {{ $currentSortBy === 'sold_last_30_days' ? 'selected' : '' }}>Ventes (30j)</option>
                            </select>
                            <input type="hidden" name="sort_dir" id="inventorySortDirInput" value="{{ $currentSortDir }}">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleInventorySortDir()" title="{{ $currentSortDir === 'desc' ? 'Ordre décroissant (cliquer pour inverser)' : 'Ordre croissant (cliquer pour inverser)' }}">
                                <i class="fas {{ $currentSortDir === 'desc' ? 'fa-arrow-down-wide-short' : 'fa-arrow-up-short-wide' }}"></i>
                            </button>
                        </div>
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

    <!-- BARRE D'ACTIONS GROUPÉES (Desktop) -->
    <div id="invBulkBar" class="card border-0 shadow-sm mb-3 d-none" style="border-radius: 14px; background: #0f172a; color: #fff;">
        <div class="card-body py-2.5 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-pill px-2.5 py-1" id="invBulkCount">0</span>
                <span class="small fw-semibold">article(s) sélectionné(s)</span>
                @if($selectedStore)
                    <span class="badge bg-light text-dark rounded-pill" style="font-size: 0.7rem;">Dépôt : {{ $selectedStore->name }}</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 110px;">
                    <span class="input-group-text bg-dark text-white border-secondary">Qté</span>
                    <input type="number" id="invBulkQty" class="form-control bg-dark text-white border-secondary text-center fw-bold" value="1" min="1">
                </div>
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold" onclick="submitInvBulkAction('adjust_in')">
                    <i class="fas fa-plus me-1"></i>Entrée
                </button>
                <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold" onclick="submitInvBulkAction('adjust_out')">
                    <i class="fas fa-minus me-1"></i>Sortie
                </button>
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-2.5" onclick="clearInvSelection()" title="Tout désélectionner">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- AFFICHAGE DESKTOP -->
    <div class="brand-table-card inventory-table-card d-none d-md-block shadow-sm border" style="overflow: hidden !important;">
        <div style="overflow-x: hidden !important; width: 100% !important;">
            <table class="brand-table align-middle" style="table-layout: fixed !important; width: 100% !important; margin: 0 !important;">
                <thead>
                    <tr>
                        <th style="width: 38px; padding-left: 1rem;">
                            <input type="checkbox" class="form-check-input" id="invSelectAll" onchange="toggleInvSelectAll(this)" title="Tout sélectionner">
                        </th>
                        <th style="width: 23%;">
                            <a href="{{ route('inventory.index', array_merge(request()->except('page'), ['sort_by' => 'name', 'sort_dir' => ($currentSortBy === 'name' && $currentSortDir === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-reset d-inline-flex align-items-center gap-1">
                                <span>Article</span>
                                @if($currentSortBy === 'name')
                                    <i class="fas fa-sort-{{ $currentSortDir === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 25%;">
                            <a href="{{ route('inventory.index', array_merge(request()->except('page'), ['sort_by' => 'stock', 'sort_dir' => ($currentSortBy === 'stock' && $currentSortDir === 'desc') ? 'asc' : 'desc'])) }}" class="text-decoration-none text-reset d-inline-flex align-items-center gap-1">
                                <span>{{ $selectedStore ? 'Stock : ' . $selectedStore->name : 'Stock & Répartition Dépôts' }}</span>
                                @if($currentSortBy === 'stock')
                                    <i class="fas fa-sort-{{ $currentSortDir === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center" style="width: 9%;">
                            <a href="{{ route('inventory.index', array_merge(request()->except('page'), ['sort_by' => 'sold_last_30_days', 'sort_dir' => ($currentSortBy === 'sold_last_30_days' && $currentSortDir === 'desc') ? 'asc' : 'desc'])) }}" class="text-decoration-none text-reset d-inline-flex align-items-center gap-1">
                                <span>Ventes (30j)</span>
                                @if($currentSortBy === 'sold_last_30_days')
                                    <i class="fas fa-sort-{{ $currentSortDir === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center" style="width: 8%;">Autonomie</th>
                        <th class="text-center" style="width: 7%;">{{ $selectedStore ? 'Seuil Site' : 'Seuil' }}</th>
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
                    <tr id="inv-row-{{ $product->id }}"
                        data-product-id="{{ $product->id }}"
                        data-selected-store-id="{{ $selectedStoreId ?? '' }}"
                        data-threshold="{{ $threshold }}"
                        data-cost-price="{{ (float) ($product->cost_price ?? 0) }}">
                        <!-- Checkbox -->
                        <td style="padding-left: 1rem;">
                            <input type="checkbox" class="form-check-input inv-row-checkbox" value="{{ $product->id }}" onchange="updateInvBulkBar()">
                        </td>
                        <!-- Article -->
                        <td>
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
                                            {{ $product->productCategory->name ?? 'Non classé' }}
                                        </span>
                                    </div>
                                    @if($product->variants && $product->variants->isNotEmpty())
                                    <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                        @foreach($product->variants as $variant)
                                            <button type="button"
                                                    class="btn btn-xs btn-outline-secondary py-0 px-1.5 d-inline-flex align-items-center gap-1 rounded"
                                                    style="font-size: 0.65rem; line-height: 1.4;"
                                                    data-inv-variant-btn="{{ $product->id }}-{{ $variant->id }}"
                                                    onclick="openAdjustModal({{ $product->id }}, {{ $selectedStoreId ? $selectedStoreId : 'null' }}, {{ $variant->id }})"
                                                    title="Ajuster la variante {{ $variant->name }}">
                                                <span class="fw-semibold">{{ $variant->name }}:</span>
                                                <span class="badge {{ $variant->stock <= 0 ? 'bg-danger text-white' : ($variant->stock <= ($variant->min_stock ?: 5) ? 'bg-warning text-dark' : 'bg-light text-dark border') }} px-1 py-0" data-inv-variant-badge="{{ $product->id }}-{{ $variant->id }}" style="font-size: 0.6rem;">{{ $variant->stock }}</span>
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
                                        <span class="fw-bold text-dark fs-6" id="inv-row-stock-{{ $product->id }}">{{ $stock }}</span>
                                        <span class="brand-badge {{ $badgeClass }} py-0.5 px-1.5" id="inv-row-badge-{{ $product->id }}" style="font-size: 0.68rem;">
                                            {{ $badgeText }}
                                        </span>
                                        @if($selectedStoreId)
                                            <span class="badge bg-light text-dark border font-monospace px-1.5 py-0.5 {{ $storeLoc ? '' : 'd-none' }}" id="inv-row-loc-{{ $product->id }}" style="font-size: 0.65rem;" title="Emplacement Rayon">
                                                <i class="fas fa-map-pin text-danger me-0.5"></i><span data-inv-loc-text>{{ $storeLoc }}</span>
                                            </span>
                                            <span class="text-muted small" id="inv-row-total-{{ $product->id }}" style="font-size: 0.68rem;">
                                                / {{ (int)($product->stock ?? 0) }} total
                                            </span>
                                        @endif
                                    </div>
                                    <div class="progress" style="height: 4px; border-radius: 999px; background: #e2e8f0; max-width: 140px;">
                                        <div class="progress-bar {{ $barClass }}" id="inv-row-bar-{{ $product->id }}" role="progressbar" style="width: {{ $percent }}%; border-radius: 999px;"></div>
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
                                                    data-inv-store-pill="{{ $product->id }}-{{ $st->id }}"
                                                    data-store-name="{{ $st->name }}"
                                                    onclick="openAdjustModal({{ $product->id }}, {{ $st->id }})"
                                                    title="Cliquer pour ajuster {{ $st->name }}{{ $stLoc ? ' · ' . $stLoc : '' }}">
                                                <span data-inv-store-dot style="width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                                <span class="text-truncate" style="max-width: 85px;">{{ $st->name }}</span>
                                                <strong class="text-dark" data-inv-store-qty>{{ $stQty }}</strong>
                                                <span class="text-muted font-monospace {{ $stLoc ? '' : 'd-none' }}" data-inv-store-loc style="font-size: 0.6rem;">{{ $stLoc ? '· ' . $stLoc : '' }}</span>
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
                            <span class="{{ $reorderClass }}" id="inv-row-threshold-{{ $product->id }}" style="font-size: 0.85rem;">{{ $reorderPt }}</span>
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
                        <td colspan="8">
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
                        <div id="modalAdjustError" class="alert alert-danger py-2 px-3 small rounded-3 d-none mb-3" role="alert"></div>
                        <div id="modalAdjustSuccess" class="alert alert-success py-2 px-3 small rounded-3 d-none mb-3 d-flex align-items-center justify-content-between" role="alert">
                            <div><i class="fas fa-check-circle me-1.5"></i><span id="modalAdjustSuccessText">Modifications enregistrées.</span></div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem;">Enregistré</span>
                        </div>

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

                        <!-- Magasin / Dépôt Selector (1-Click Visual Cards) -->
                        @if($stores->isNotEmpty())
                        <div class="mb-3 p-2.5 rounded-3 bg-light border">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1.5" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                                <i class="fas fa-warehouse text-danger me-1"></i>Magasin / Point de stockage ciblé (1 clic)
                            </label>
                            <select name="store_id" id="modalStoreSelect" class="form-select form-select-sm d-none" onchange="onStoreSelectionChange()">
                                <option value="">Stock global uniquement (tous dépôts)</option>
                                @foreach($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                                @endforeach
                            </select>
                            <div class="inv-transfer-store-grid" id="modalAdjustStorePills">
                                <button type="button"
                                        class="inv-transfer-store-card is-adjust active"
                                        data-store-id=""
                                        onclick="selectAdjustStoreCard('')">
                                    <div class="inv-store-card-top">
                                        <span class="inv-store-name">Stock global</span>
                                        <i class="fas fa-check-circle inv-store-check"></i>
                                    </div>
                                    <div class="inv-store-card-meta">
                                        <span>Tous dépôts</span>
                                        <span class="inv-store-qty" data-adjust-store-qty>0 pcs</span>
                                    </div>
                                </button>
                                @foreach($stores as $st)
                                    <button type="button"
                                            class="inv-transfer-store-card is-adjust"
                                            data-store-id="{{ $st->id }}"
                                            onclick="selectAdjustStoreCard({{ $st->id }})">
                                        <div class="inv-store-card-top">
                                            <span class="inv-store-name">{{ $st->name }}</span>
                                            <i class="fas fa-check-circle inv-store-check"></i>
                                        </div>
                                        <div class="inv-store-card-meta">
                                            <span>{{ $st->code }}</span>
                                            <span class="inv-store-qty" data-adjust-store-qty>0 pcs</span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>

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

                        <!-- Reason (Optional) -->
                        <div class="mb-2">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Motif de l'ajustement (Optionnel)</label>
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
                                      placeholder="Motif optionnel (ex: Arrivage, Retour client...)"
                                      style="border-radius: 12px; font-size: 0.85rem;"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-3.5 pt-0">
                        <button type="submit" id="modalAdjustSubmitBtn" class="btn btn-brand-primary w-100 py-2.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-check"></i> <span>Enregistrer l'ajustement</span>
                        </button>
                        <button type="button" class="btn btn-link text-muted small w-100 text-decoration-none mt-1" data-bs-dismiss="modal">
                            <span id="modalAdjustCloseLabel">Fermer</span>
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
                    <div id="invTransferError" class="alert alert-danger py-2 px-3 small rounded-3 d-none mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Article à transférer <span class="text-danger">*</span></label>
                        <input type="text" id="inv_transfer_product_search" class="form-control form-control-sm rounded-3 mb-1.5" placeholder="Filtrer par nom ou SKU..." oninput="filterTransferProducts(this.value)">
                        <select name="product_id" id="inv_transfer_product_id" class="form-select rounded-3" required onchange="refreshTransferSourceLabels()">
                            <option value="">Sélectionnez un article...</option>
                            @foreach(($transferProducts ?? $products) as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->sku ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <span class="small fw-bold text-dark">
                            <i class="fas fa-route text-primary me-1"></i>Sélection rapide des dépôts (1 clic)
                        </span>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold"
                                style="font-size: 0.74rem;"
                                onclick="swapTransferStores()"
                                title="Inverser Dépôt Source et Dépôt Destination">
                            <i class="fas fa-arrow-right-arrow-left me-1"></i>Inverser Source / Destination
                        </button>
                    </div>

                    <div class="row g-2.5 mb-3">
                        <div class="col-md-6">
                            <div class="inv-transfer-store-box">
                                <label class="form-label small fw-bold text-dark mb-1.5 d-flex align-items-center justify-content-between">
                                    <span><i class="fas fa-arrow-up-from-bracket text-warning me-1"></i>Dépôt Source (Origine) <span class="text-danger">*</span></span>
                                </label>
                                <select name="from_store_id" id="inv_transfer_from_store" class="form-select d-none" required onchange="onTransferStoreChange('from')">
                                    @foreach($stores as $st)
                                        <option value="{{ $st->id }}" data-name="{{ $st->name }} ({{ $st->code }})">{{ $st->name }} ({{ $st->code }})</option>
                                    @endforeach
                                </select>
                                <div class="inv-transfer-store-grid" id="invTransferFromPills">
                                    @foreach($stores as $idx => $st)
                                        <button type="button"
                                                class="inv-transfer-store-card is-source {{ $idx === 0 ? 'active' : '' }}"
                                                data-store-id="{{ $st->id }}"
                                                onclick="selectTransferStoreCard('from', {{ $st->id }})">
                                            <div class="inv-store-card-top">
                                                <span class="inv-store-name">{{ $st->name }}</span>
                                                <i class="fas fa-check-circle inv-store-check"></i>
                                            </div>
                                            <div class="inv-store-card-meta">
                                                <span>{{ $st->code }}</span>
                                                <span class="inv-store-qty" data-store-qty>—</span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="inv-transfer-store-box">
                                <label class="form-label small fw-bold text-dark mb-1.5 d-flex align-items-center justify-content-between">
                                    <span><i class="fas fa-arrow-right-to-bracket text-success me-1"></i>Dépôt Destination (Arrivée) <span class="text-danger">*</span></span>
                                </label>
                                <select name="to_store_id" id="inv_transfer_to_store" class="form-select d-none" required onchange="onTransferStoreChange('to')">
                                    @foreach($stores as $idx => $st)
                                        <option value="{{ $st->id }}" data-name="{{ $st->name }} ({{ $st->code }})" {{ $idx === 1 ? 'selected' : '' }}>{{ $st->name }} ({{ $st->code }})</option>
                                    @endforeach
                                </select>
                                <div class="inv-transfer-store-grid" id="invTransferToPills">
                                    @foreach($stores as $idx => $st)
                                        <button type="button"
                                                class="inv-transfer-store-card is-dest {{ $idx === 1 ? 'active' : '' }}"
                                                data-store-id="{{ $st->id }}"
                                                onclick="selectTransferStoreCard('to', {{ $st->id }})">
                                            <div class="inv-store-card-top">
                                                <span class="inv-store-name">{{ $st->name }}</span>
                                                <i class="fas fa-check-circle inv-store-check"></i>
                                            </div>
                                            <div class="inv-store-card-meta">
                                                <span>{{ $st->code }}</span>
                                                <span class="inv-store-qty" data-store-qty>—</span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Quantité à transférer <span class="text-danger">*</span>
                                <span class="badge bg-light text-secondary border ms-1 fw-normal" id="invTransferAvailBadge"></span>
                            </label>
                            <input type="number" min="1" step="1" name="quantity" id="inv_transfer_qty" class="form-control rounded-3 fw-bold" placeholder="Ex: 5" required oninput="validateTransferQty()">
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

    $transferStockMap = [];
    foreach (($transferProducts ?? collect()) as $tp) {
        $ssMap = [];
        foreach ($tp->storeStocks as $ss) {
            $ssMap[(int) $ss->store_id] = (int) $ss->quantity;
        }
        $transferStockMap[$tp->id] = $ssMap;
    }
@endphp
<script>
    window.__invProducts = Object.assign(window.__invProducts || {}, @json($invMetaMap));
    window.__transferStoreStocks = @json($transferStockMap);

    function onSortByChange(selectEl) {
        const dirInput = document.getElementById('inventorySortDirInput');
        if (dirInput) {
            dirInput.value = selectEl.value === 'sold_last_30_days' ? 'desc' : 'asc';
        }
        document.getElementById('inventoryFilterForm').submit();
    }

    function toggleInventorySortDir() {
        const dirInput = document.getElementById('inventorySortDirInput');
        if (!dirInput) return;
        dirInput.value = dirInput.value === 'desc' ? 'asc' : 'desc';
        document.getElementById('inventoryFilterForm').submit();
    }

    function getSelectedInvProductIds() {
        return Array.from(document.querySelectorAll('.inv-row-checkbox:checked')).map(cb => parseInt(cb.value, 10));
    }

    function toggleInvSelectAll(masterCb) {
        document.querySelectorAll('.inv-row-checkbox').forEach(cb => {
            cb.checked = masterCb.checked;
        });
        updateInvBulkBar();
    }

    function clearInvSelection() {
        const master = document.getElementById('invSelectAll');
        if (master) master.checked = false;
        document.querySelectorAll('.inv-row-checkbox').forEach(cb => cb.checked = false);
        updateInvBulkBar();
    }

    function updateInvBulkBar() {
        const ids = getSelectedInvProductIds();
        const bar = document.getElementById('invBulkBar');
        const countEl = document.getElementById('invBulkCount');
        if (!bar || !countEl) return;
        countEl.textContent = ids.length;
        bar.classList.toggle('d-none', ids.length === 0);
    }

    async function submitInvBulkAction(action) {
        const ids = getSelectedInvProductIds();
        if (ids.length === 0) return;

        const qty = parseInt(document.getElementById('invBulkQty')?.value || '1', 10) || 1;
        const selectedStoreId = @json($selectedStoreId);

        try {
            const resp = await fetch(@json(route('inventory.bulk-action')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    action: action,
                    product_ids: ids,
                    quantity: qty,
                    store_id: selectedStoreId,
                    reason: 'Ajustement groupé depuis l\'inventaire'
                })
            });
            const data = await resp.json();
            if (!resp.ok || !data.success) {
                alert(data.message || 'Erreur lors de l\'action groupée.');
                return;
            }
            window.location.reload();
        } catch (err) {
            alert('Erreur réseau lors de l\'action groupée.');
        }
    }

    var currentModalProductId = null;
    var currentProductStock = 0;
    var modalBaseStock = 0;
    var modalVariantsList = [];
    var modalStoreStocks = [];

    function openAdjustModal(productId, preferredStoreId = null, selectedVariantId = null) {
        const meta = window.__invProducts[productId];
        if (!meta) return;

        currentModalProductId = productId;
        modalBaseStock = parseInt(meta.stock, 10) || 0;
        modalVariantsList = meta.variants || [];
        modalStoreStocks = meta.storeStocks || [];

        document.getElementById('modalProductName').textContent = meta.name;
        const errBox = document.getElementById('modalAdjustError');
        if (errBox) {
            errBox.classList.add('d-none');
            errBox.textContent = '';
        }
        const okBox = document.getElementById('modalAdjustSuccess');
        if (okBox) {
            okBox.classList.add('d-none');
        }
        const form = document.getElementById('adjustStockForm');
        form.action = `/inventory/${productId}/adjust`;

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
                opt.dataset.name = v.name;
                opt.dataset.stock = v.stock ?? 0;
                if (selectedVariantId && String(v.id) === String(selectedVariantId)) {
                    opt.selected = true;
                }
                variantSelect.appendChild(opt);
            });
        } else {
            variantContainer.style.display = 'none';
        }

        const storeSelect = document.getElementById('modalStoreSelect');
        if (storeSelect) {
            storeSelect.value = preferredStoreId ? String(preferredStoreId) : '';
        }
        refreshModalSelectLabels();

        document.getElementById('modalQuantityInput').value = 1;
        document.getElementById('modalReasonInput').value = '';
        selectOperation('in');
        syncModalStockContext();

        bootstrap.Modal.getOrCreateInstance(document.getElementById('adjustStockModal')).show();
    }

    function refreshModalSelectLabels() {
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
                const qty = rec ? (parseInt(rec.quantity, 10) || 0) : 0;
                opt.textContent = `${baseLabel} — ${qty} pcs`;
            });
        }

        const variantSelect = document.getElementById('modalVariantSelect');
        if (variantSelect && Array.isArray(modalVariantsList)) {
            Array.from(variantSelect.options).forEach(opt => {
                if (!opt.value) return;
                const v = modalVariantsList.find(item => String(item.id) === String(opt.value));
                if (v) {
                    opt.dataset.stock = v.stock ?? 0;
                    opt.textContent = `${v.name} (Stock global: ${v.stock ?? 0})`;
                }
            });
        }

        syncAdjustStoreCards();
    }

    function selectAdjustStoreCard(storeId) {
        const storeSelect = document.getElementById('modalStoreSelect');
        if (!storeSelect) return;
        storeSelect.value = storeId ? String(storeId) : '';
        syncModalStockContext();
    }

    function syncAdjustStoreCards() {
        const storeSelect = document.getElementById('modalStoreSelect');
        const activeVal = storeSelect ? String(storeSelect.value || '') : '';
        document.querySelectorAll('#modalAdjustStorePills .inv-transfer-store-card').forEach(btn => {
            const sid = String(btn.dataset.storeId || '');
            btn.classList.toggle('active', sid === activeVal);

            let qty = modalBaseStock;
            if (sid !== '') {
                const rec = (modalStoreStocks || []).find(s => String(s.store_id) === sid);
                qty = rec ? (parseInt(rec.quantity, 10) || 0) : 0;
            }
            const qtyEl = btn.querySelector('[data-adjust-store-qty]');
            if (qtyEl) qtyEl.textContent = `${qty} pcs`;
            btn.classList.toggle('is-empty', qty <= 0);
            btn.classList.toggle('is-has-stock', qty > 0);
        });
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
        syncAdjustStoreCards();
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

    window.invTransferExplicitSource = false;

    function openInventoryTransferModal(productId = null, sourceStoreId = null) {
        const modalEl = document.getElementById('transferInventoryModal');
        if (!modalEl) return;

        const searchInput = document.getElementById('inv_transfer_product_search');
        if (searchInput) {
            searchInput.value = '';
            filterTransferProducts('');
        }

        const errBox = document.getElementById('invTransferError');
        if (errBox) {
            errBox.classList.add('d-none');
            errBox.textContent = '';
        }

        const prodSelect = document.getElementById('inv_transfer_product_id');
        if (prodSelect) {
            prodSelect.value = productId ? String(productId) : '';
        }

        const fromSelect = document.getElementById('inv_transfer_from_store');
        const toSelect = document.getElementById('inv_transfer_to_store');
        window.invTransferExplicitSource = Boolean(sourceStoreId);
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

    function filterTransferProducts(query) {
        const prodSelect = document.getElementById('inv_transfer_product_id');
        if (!prodSelect) return;
        const q = (query || '').trim().toLowerCase();
        Array.from(prodSelect.options).forEach(opt => {
            if (!opt.value) return;
            const match = !q || opt.textContent.toLowerCase().includes(q);
            opt.hidden = !match;
        });
    }

    function selectTransferStoreCard(role, storeId) {
        const fromSelect = document.getElementById('inv_transfer_from_store');
        const toSelect = document.getElementById('inv_transfer_to_store');
        if (!fromSelect || !toSelect) return;

        const nextId = String(storeId);
        const prevFrom = fromSelect.value;
        const prevTo = toSelect.value;

        if (role === 'from') {
            window.invTransferExplicitSource = true;
            fromSelect.value = nextId;
            if (toSelect.value === nextId) {
                const swapCandidate = prevFrom && prevFrom !== nextId ? prevFrom : null;
                if (swapCandidate) {
                    toSelect.value = swapCandidate;
                } else {
                    const other = Array.from(toSelect.options).find(o => o.value !== nextId);
                    if (other) toSelect.value = other.value;
                }
            }
        } else {
            toSelect.value = nextId;
            if (fromSelect.value === nextId) {
                const swapCandidate = prevTo && prevTo !== nextId ? prevTo : null;
                if (swapCandidate) {
                    fromSelect.value = swapCandidate;
                } else {
                    const other = Array.from(fromSelect.options).find(o => o.value !== nextId);
                    if (other) fromSelect.value = other.value;
                }
            }
        }

        syncTransferStoreCards();
        validateTransferQty();
    }

    function swapTransferStores() {
        const fromSelect = document.getElementById('inv_transfer_from_store');
        const toSelect = document.getElementById('inv_transfer_to_store');
        if (!fromSelect || !toSelect) return;

        const oldFrom = fromSelect.value;
        const oldTo = toSelect.value;
        if (!oldFrom || !oldTo || oldFrom === oldTo) return;

        window.invTransferExplicitSource = true;
        fromSelect.value = oldTo;
        toSelect.value = oldFrom;
        syncTransferStoreCards();
        validateTransferQty();
    }

    function syncTransferStoreCards() {
        const prodSelect = document.getElementById('inv_transfer_product_id');
        const fromSelect = document.getElementById('inv_transfer_from_store');
        const toSelect = document.getElementById('inv_transfer_to_store');
        if (!fromSelect || !toSelect) return;

        const fromVal = fromSelect.value;
        const toVal = toSelect.value;
        const hasProduct = Boolean(prodSelect && prodSelect.value);
        const map = hasProduct ? window.__transferStoreStocks?.[prodSelect.value] : null;
        const meta = hasProduct ? window.__invProducts[prodSelect.value] : null;

        const resolveQty = (storeId) => {
            if (!hasProduct || (!map && !meta)) return null;
            if (map) return parseInt(map[storeId] ?? 0, 10) || 0;
            const rec = (meta.storeStocks || []).find(s => String(s.store_id) === String(storeId));
            return rec ? (parseInt(rec.quantity, 10) || 0) : 0;
        };

        document.querySelectorAll('#invTransferFromPills .inv-transfer-store-card').forEach(btn => {
            const sid = String(btn.dataset.storeId || '');
            btn.classList.toggle('active', sid === fromVal);
            const qty = resolveQty(sid);
            const qtyEl = btn.querySelector('[data-store-qty]');
            if (qtyEl) qtyEl.textContent = qty === null ? '—' : `${qty} pcs`;
            btn.classList.toggle('is-empty', qty !== null && qty <= 0);
            btn.classList.toggle('is-has-stock', qty !== null && qty > 0);
        });

        document.querySelectorAll('#invTransferToPills .inv-transfer-store-card').forEach(btn => {
            const sid = String(btn.dataset.storeId || '');
            btn.classList.toggle('active', sid === toVal);
            const qty = resolveQty(sid);
            const qtyEl = btn.querySelector('[data-store-qty]');
            if (qtyEl) qtyEl.textContent = qty === null ? '—' : `${qty} pcs`;
        });
    }

    function onTransferStoreChange(changed) {
        const fromSelect = document.getElementById('inv_transfer_from_store');
        const toSelect = document.getElementById('inv_transfer_to_store');
        if (fromSelect && toSelect && fromSelect.value === toSelect.value) {
            const target = changed === 'from' ? toSelect : fromSelect;
            const other = Array.from(target.options).find(o => o.value !== fromSelect.value);
            if (other) target.value = other.value;
        }
        syncTransferStoreCards();
        validateTransferQty();
    }

    function getTransferSourceAvailable() {
        const prodSelect = document.getElementById('inv_transfer_product_id');
        const fromSelect = document.getElementById('inv_transfer_from_store');
        if (!prodSelect || !fromSelect || !prodSelect.value) return null;
        const map = window.__transferStoreStocks?.[prodSelect.value];
        if (map) {
            return parseInt(map[fromSelect.value] ?? 0, 10) || 0;
        }
        const meta = window.__invProducts[prodSelect.value];
        if (meta) {
            const rec = (meta.storeStocks || []).find(s => String(s.store_id) === String(fromSelect.value));
            return rec ? rec.quantity : 0;
        }
        return 0;
    }

    function validateTransferQty() {
        const badge = document.getElementById('invTransferAvailBadge');
        const qtyInput = document.getElementById('inv_transfer_qty');
        const errBox = document.getElementById('invTransferError');
        const avail = getTransferSourceAvailable();

        if (badge) {
            badge.textContent = avail === null ? '' : `Dispo : ${avail} u.`;
        }
        if (qtyInput && avail !== null) {
            const requested = parseInt(qtyInput.value || '0', 10) || 0;
            if (requested > avail && errBox) {
                errBox.textContent = `Attention : la quantité demandée (${requested}) dépasse le stock disponible dans le dépôt source (${avail}).`;
                errBox.classList.remove('d-none');
            } else if (errBox) {
                errBox.classList.add('d-none');
            }
        }
    }

    function refreshTransferSourceLabels() {
        const prodSelect = document.getElementById('inv_transfer_product_id');
        const fromSelect = document.getElementById('inv_transfer_from_store');
        const toSelect = document.getElementById('inv_transfer_to_store');
        if (!prodSelect) return;

        const map = window.__transferStoreStocks?.[prodSelect.value];
        const meta = window.__invProducts[prodSelect.value];

        const getQty = (storeId) => {
            if (!prodSelect.value || (!map && !meta)) return 0;
            if (map) return parseInt(map[storeId] ?? 0, 10) || 0;
            const rec = (meta.storeStocks || []).find(s => String(s.store_id) === String(storeId));
            return rec ? (parseInt(rec.quantity, 10) || 0) : 0;
        };

        // Auto-pick the source depot with highest stock if product is chosen and current source has 0 (or no explicit source)
        if (prodSelect.value && (map || meta) && fromSelect && toSelect) {
            const currentSourceQty = getQty(fromSelect.value);
            let bestOpt = null;
            let bestQty = -1;
            Array.from(fromSelect.options).forEach(opt => {
                const q = getQty(opt.value);
                if (q > bestQty) {
                    bestQty = q;
                    bestOpt = opt;
                }
            });
            if (bestOpt && bestQty > 0 && (!window.invTransferExplicitSource || currentSourceQty <= 0)) {
                fromSelect.value = bestOpt.value;
                if (toSelect.value === fromSelect.value) {
                    const other = Array.from(toSelect.options).find(o => o.value !== fromSelect.value);
                    if (other) toSelect.value = other.value;
                }
            }
        }

        [fromSelect, toSelect].forEach(sel => {
            if (!sel) return;
            Array.from(sel.options).forEach(opt => {
                const base = opt.dataset.name || opt.textContent;
                if (!prodSelect.value || (!map && !meta)) {
                    opt.textContent = base;
                    return;
                }
                opt.textContent = `${base} — ${getQty(opt.value)} pcs`;
            });
        });

        syncTransferStoreCards();
        validateTransferQty();
    }

    function updateInventoryDomAfterSave(productId, data) {
        if (!productId || !data) return;

        const meta = window.__invProducts[productId] || { name: '', stock: 0, variants: [], storeStocks: [] };
        const globalStock = parseInt(data.new_stock, 10) || 0;
        meta.stock = globalStock;

        if (Array.isArray(data.variants)) {
            meta.variants = data.variants;
        } else if (data.variant_id) {
            const existingVar = (meta.variants || []).find(v => String(v.id) === String(data.variant_id));
            if (existingVar) {
                existingVar.stock = parseInt(data.variant_stock ?? existingVar.stock, 10) || 0;
            }
        }

        if (Array.isArray(data.store_stocks)) {
            meta.storeStocks = data.store_stocks;
        } else if (data.store_id) {
            meta.storeStocks = meta.storeStocks || [];
            let existingStore = meta.storeStocks.find(s => String(s.store_id) === String(data.store_id));
            if (!existingStore) {
                existingStore = { store_id: parseInt(data.store_id, 10), quantity: 0, location_code: null, min_quantity: 2 };
                meta.storeStocks.push(existingStore);
            }
            existingStore.quantity = parseInt(data.store_stock ?? existingStore.quantity, 10) || 0;
            if ('location_code' in data) existingStore.location_code = data.location_code;
            if (data.min_quantity !== null && data.min_quantity !== undefined) {
                existingStore.min_quantity = parseInt(data.min_quantity, 10) || 0;
            }
        }
        window.__invProducts[productId] = meta;

        if (window.__transferStoreStocks && meta.storeStocks) {
            const ssMap = window.__transferStoreStocks[productId] || {};
            meta.storeStocks.forEach(ss => {
                ssMap[ss.store_id] = parseInt(ss.quantity, 10) || 0;
            });
            window.__transferStoreStocks[productId] = ssMap;
        }

        const row = document.getElementById('inv-row-' + productId);
        const selectedStoreId = row && row.dataset.selectedStoreId ? String(row.dataset.selectedStoreId) : '';
        const selectedRec = selectedStoreId
            ? (meta.storeStocks || []).find(s => String(s.store_id) === selectedStoreId)
            : null;
        const activeStock = selectedStoreId ? (selectedRec ? (parseInt(selectedRec.quantity, 10) || 0) : 0) : globalStock;
        const threshold = selectedStoreId
            ? (selectedRec ? (parseInt(selectedRec.min_quantity ?? 2, 10) || 0) : (parseInt(row?.dataset.threshold || '2', 10) || 2))
            : (parseInt(data.threshold ?? row?.dataset.threshold ?? '10', 10) || 0);

        if (row) {
            row.dataset.threshold = String(threshold);
            let badgeClass = 'success';
            let badgeText = 'En stock';
            let barClass = 'bg-success';
            let percent = Math.min(100, (activeStock / Math.max(1, threshold * 3)) * 100);

            if (activeStock <= 0) {
                badgeClass = 'danger';
                badgeText = 'Rupture';
                barClass = 'bg-danger';
                percent = 0;
            } else if (activeStock <= threshold) {
                badgeClass = 'warning';
                badgeText = 'Faible';
                barClass = 'bg-warning';
                percent = Math.max(10, Math.min(100, (activeStock / Math.max(1, threshold * 2)) * 100));
            }

            const rowStockEl = document.getElementById('inv-row-stock-' + productId);
            if (rowStockEl) rowStockEl.textContent = activeStock;

            const rowBadgeEl = document.getElementById('inv-row-badge-' + productId);
            if (rowBadgeEl) {
                rowBadgeEl.className = `brand-badge ${badgeClass} py-0.5 px-1.5`;
                rowBadgeEl.textContent = badgeText;
            }

            const rowBarEl = document.getElementById('inv-row-bar-' + productId);
            if (rowBarEl) {
                rowBarEl.className = `progress-bar ${barClass}`;
                rowBarEl.style.width = `${percent}%`;
            }

            const rowTotalEl = document.getElementById('inv-row-total-' + productId);
            if (rowTotalEl) rowTotalEl.textContent = `/ ${globalStock} total`;

            const rowLocEl = document.getElementById('inv-row-loc-' + productId);
            if (rowLocEl) {
                const locText = selectedRec && selectedRec.location_code ? selectedRec.location_code : '';
                const locSpan = rowLocEl.querySelector('[data-inv-loc-text]');
                if (locSpan) locSpan.textContent = locText;
                rowLocEl.classList.toggle('d-none', !locText);
            }

            if (selectedStoreId) {
                const rowThreshEl = document.getElementById('inv-row-threshold-' + productId);
                if (rowThreshEl) {
                    rowThreshEl.textContent = threshold;
                    rowThreshEl.className = activeStock <= threshold ? 'text-danger fw-bold' : 'text-muted';
                }
            }

            row.querySelectorAll('[data-inv-store-pill^="' + productId + '-"]').forEach(pill => {
                const attr = pill.getAttribute('data-inv-store-pill') || '';
                const storeId = attr.split('-')[1];
                const rec = (meta.storeStocks || []).find(s => String(s.store_id) === String(storeId));
                const qty = rec ? (parseInt(rec.quantity, 10) || 0) : 0;
                const minQty = rec ? (parseInt(rec.min_quantity ?? 2, 10) || 0) : 2;
                const loc = rec && rec.location_code ? rec.location_code : '';
                const dotColor = qty <= 0 ? '#94a3b8' : (qty <= minQty ? '#f59e0b' : '#10b981');

                const dotEl = pill.querySelector('[data-inv-store-dot]');
                if (dotEl) dotEl.style.background = dotColor;

                const qtyEl = pill.querySelector('[data-inv-store-qty]');
                if (qtyEl) qtyEl.textContent = qty;

                const locEl = pill.querySelector('[data-inv-store-loc]');
                if (locEl) {
                    locEl.textContent = loc ? `· ${loc}` : '';
                    locEl.classList.toggle('d-none', !loc);
                }

                const storeName = pill.dataset.storeName || '';
                if (storeName) {
                    pill.title = `Cliquer pour ajuster ${storeName}${loc ? ' · ' + loc : ''}`;
                }
            });
        }

        // Update variants (desktop + mobile)
        (meta.variants || []).forEach(v => {
            const vStock = parseInt(v.stock ?? 0, 10) || 0;
            const vMin = parseInt(v.min_stock ?? 5, 10) || 5;
            document.querySelectorAll(`[data-inv-variant-badge="${productId}-${v.id}"]`).forEach(el => {
                el.textContent = vStock;
                if (el.tagName === 'SPAN') {
                    const cls = vStock <= 0
                        ? 'bg-danger text-white'
                        : (vStock <= vMin ? 'bg-warning text-dark' : 'bg-light text-dark border');
                    el.className = `badge ${cls} px-1 py-0`;
                }
            });
            document.querySelectorAll(`[data-mob-variant-btn="${productId}-${v.id}"]`).forEach(btn => {
                btn.className = `badge ${vStock <= 0 ? 'bg-danger-subtle text-danger' : 'bg-light text-secondary border'} px-1.5 py-0.5`;
            });
        });

        // Update mobile card if present
        const mobCard = document.getElementById('mobile-inventory-card-' + productId);
        if (mobCard) {
            const mobValEl = document.getElementById('mob-stock-val-' + productId);
            if (mobValEl) mobValEl.textContent = activeStock;

            let statusClass = 'success';
            let statusLabel = 'En stock';
            let statusDot = 'dot-green';
            let barColor = '#10b981';
            let mobPercent = Math.min(100, (activeStock / Math.max(1, threshold * 3)) * 100);

            if (activeStock <= 0) {
                statusClass = 'danger';
                statusLabel = 'Rupture';
                statusDot = 'dot-red';
                barColor = '#ef4444';
                mobPercent = 0;
            } else if (activeStock <= threshold) {
                statusClass = 'warning';
                statusLabel = 'Faible';
                statusDot = 'dot-amber';
                barColor = '#f59e0b';
                mobPercent = Math.max(12, Math.min(100, (activeStock / Math.max(1, threshold * 2)) * 100));
            }

            const mobBadgeEl = document.getElementById('mob-stock-badge-' + productId);
            if (mobBadgeEl) {
                mobBadgeEl.className = `brand-badge ${statusClass} d-inline-flex align-items-center gap-1 py-0.5 px-1.5 mt-0.5`;
                const dotSpan = mobBadgeEl.querySelector('.status-dot');
                if (dotSpan) dotSpan.className = `status-dot ${statusDot}`;
                const spans = mobBadgeEl.querySelectorAll('span');
                if (spans.length > 1) spans[1].textContent = statusLabel;
            }

            const mobBarEl = document.getElementById('mob-stock-bar-' + productId);
            if (mobBarEl) {
                mobBarEl.style.width = `${mobPercent}%`;
                mobBarEl.style.background = barColor;
            }

            mobCard.querySelectorAll(`[data-mob-store-pill^="${productId}-"]`).forEach(pill => {
                const attr = pill.getAttribute('data-mob-store-pill') || '';
                const storeId = attr.split('-')[1];
                const rec = (meta.storeStocks || []).find(s => String(s.store_id) === String(storeId));
                const qty = rec ? (parseInt(rec.quantity, 10) || 0) : 0;
                const minQty = rec ? (parseInt(rec.min_quantity ?? 2, 10) || 0) : 2;
                const loc = rec && rec.location_code ? rec.location_code : '';

                const qtyEl = pill.querySelector('[data-mob-store-qty]');
                if (qtyEl) qtyEl.textContent = qty;

                const locEl = pill.querySelector('[data-mob-store-loc]');
                if (locEl) {
                    locEl.textContent = loc ? `(${loc})` : '';
                    locEl.classList.toggle('d-none', !loc);
                }

                const pillBadge = qty <= 0
                    ? 'bg-light text-muted border'
                    : (qty <= minQty ? 'bg-warning-subtle text-warning border-warning-subtle' : 'bg-primary-subtle text-primary border-primary-subtle');
                pill.className = `badge ${pillBadge} border d-inline-flex align-items-center gap-1 px-2 py-1`;
            });
        }
    }

    if (!window.__invAdjustListenerBound) {
        window.__invAdjustListenerBound = true;
        document.addEventListener('submit', async function (e) {
            const adjustForm = e.target;
            if (!adjustForm || adjustForm.id !== 'adjustStockForm') return;

            e.preventDefault();
            const errBox = document.getElementById('modalAdjustError');
            const okBox = document.getElementById('modalAdjustSuccess');
            const okText = document.getElementById('modalAdjustSuccessText');
            const submitBtn = document.getElementById('modalAdjustSubmitBtn');
            if (errBox) {
                errBox.classList.add('d-none');
                errBox.textContent = '';
            }
            if (okBox) {
                okBox.classList.add('d-none');
            }

            const origHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';
            }

            try {
                const formData = new FormData(adjustForm);
                const resp = await fetch(adjustForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: formData
                });
                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    let msg = data.message || 'Impossible d\'enregistrer l\'ajustement.';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join(' ');
                    }
                    if (errBox) {
                        errBox.textContent = msg;
                        errBox.classList.remove('d-none');
                    }
                    return;
                }

                // Keep the modal open & update state in place without refreshing the page
                const productId = data.product_id || currentModalProductId;
                updateInventoryDomAfterSave(productId, data);

                const updatedMeta = window.__invProducts[productId];
                if (updatedMeta) {
                    modalBaseStock = parseInt(updatedMeta.stock, 10) || 0;
                    modalVariantsList = updatedMeta.variants || [];
                    modalStoreStocks = updatedMeta.storeStocks || [];
                }
                refreshModalSelectLabels();
                syncModalStockContext();

                const successMsg = data.message || 'Stock ajusté avec succès.';
                if (okBox && okText) {
                    okText.textContent = successMsg;
                    okBox.classList.remove('d-none');
                }
                if (window.Toast) {
                    window.Toast.fire({
                        icon: 'success',
                        title: successMsg
                    });
                }
            } catch (err) {
                if (errBox) {
                    errBox.textContent = 'Erreur réseau lors de l\'enregistrement.';
                    errBox.classList.remove('d-none');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origHtml;
                }
            }
        });
    }
</script>
@endpush
