@extends('layouts.app')

@section('title', 'Matrice des Stocks Multi-Magasins & Emplacements')

@push('styles')
<style>
/* ==========================================================================
   MULTI-STORE STOCKS & LOCATIONS MATRIX - MOBILE-FIRST DESIGN
   ========================================================================== */
.chips-scroll-store {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 3px;
}
.chips-scroll-store::-webkit-scrollbar {
    display: none;
}
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
    gap: 0.4rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    flex-shrink: 0;
}
.chip-store:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.chip-store.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
}
.chip-store.active .badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

.stock-prod-card {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.stock-prod-card:active {
    transform: scale(0.995);
}

.store-stock-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.35rem 0.7rem;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 0.74rem;
    transition: all 0.15s ease;
}
.store-stock-pill-badge:hover {
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.prod-thumb-matrix {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    flex-shrink: 0;
}
.fixed-stock-table {
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
                    <div class="rounded-circle bg-purple bg-opacity-25 border border-purple border-opacity-50 text-white d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.35rem; background: rgba(147, 51, 234, 0.25);">
                        <i class="fas fa-boxes-stacked text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="{{ route('stores.index') }}" class="text-white-50 text-decoration-none small">
                                <i class="fas fa-arrow-left me-1"></i> Points de Stockage
                            </a>
                            <span class="text-white-50 small">• Matrice des Quantités & Rayons</span>
                        </div>
                        <h1 class="h3 fw-bold text-white mb-0">Quantités par Magasin & Emplacements de Stock</h1>
                        <p class="text-white-50 small mb-0 mt-1">Consultez en un coup d'œil les unités disponibles par site ainsi que l'emplacement précis (Rayon, Étagère, Casier).</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('stores.index') }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold" style="font-size: 0.85rem;">
                        <i class="fas fa-warehouse me-1.5"></i> Configurer les Dépôts
                    </a>
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#transferStockModal" style="font-size: 0.85rem;">
                        <i class="fas fa-arrow-right-arrow-left me-1.5"></i> Transférer du Stock
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-triangle-exclamation fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <!-- Quick Swipeable Chips Store Filter Bar -->
    <div class="mb-3">
        <div class="chips-scroll-store">
            <a href="{{ route('stores.stock', request()->except(['page', 'store_id'])) }}" 
               class="chip-store {{ !request()->filled('store_id') ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked"></i>
                <span>Tous les dépôts</span>
                <span class="badge bg-secondary rounded-pill px-1.5" style="font-size: 0.65rem;">{{ $products->total() }}</span>
            </a>
            @foreach($stores as $st)
                @php
                    $isActiveStore = request('store_id') == $st->id;
                    $stTotalUnits = $st->total_units;
                @endphp
                <a href="{{ route('stores.stock', array_merge(request()->except(['page', 'store_id']), ['store_id' => $st->id])) }}" 
                   class="chip-store {{ $isActiveStore ? 'active' : '' }}">
                    <i class="fas {{ $st->type === 'warehouse' ? 'fa-warehouse text-purple' : 'fa-shop text-primary' }}"></i>
                    <span>{{ $st->name }}</span>
                    <span class="badge bg-light text-dark border rounded-pill px-1.5" style="font-size: 0.65rem;">
                        {{ number_format($stTotalUnits, 0, ',', ' ') }} pcs
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Clean Aligned Filters Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3.5">
            <form method="GET" action="{{ route('stores.stock') }}" class="row g-2.5 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Recherche Produit</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-start-0" placeholder="Nom, SKU...">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Filtrer par Dépôt</label>
                    <select name="store_id" class="form-select form-select-sm">
                        <option value="">Tous les dépôts & magasins</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->name }} ({{ $st->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Catégorie</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Emplacement Rayon</label>
                    <input type="text" name="location_code" value="{{ request('location_code') }}" class="form-control form-control-sm" placeholder="Ex: Rayon A, B-2...">
                </div>

                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 fw-semibold flex-fill">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    @if(request()->anyFilled(['search', 'store_id', 'category_id', 'location_code']))
                        <a href="{{ route('stores.stock') }}" class="btn btn-sm btn-light border rounded-pill px-2.5" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products & Multi-Store Stocks Matrix Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0 fixed-stock-table">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 30%;">Article</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 14%;">Stock Total</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 42%;">Répartition par Magasin & Emplacement Rayon</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 14%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($products as $p)
                    <tr class="hover-bg-light">
                        <!-- Product Info -->
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-2.5">
                                @if($p->image)
                                    <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="prod-thumb-matrix shadow-xs"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                @else
                                    <div class="prod-thumb-matrix d-flex align-items-center justify-content-center text-muted">
                                        <i class="fas fa-box"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-truncate" title="{{ $p->name }}">
                                        {{ $p->name }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5 text-muted small mt-0.5" style="font-size: 0.72rem;">
                                        <span class="font-monospace text-secondary fw-medium">SKU: {{ $p->sku }}</span>
                                        @if($p->brand)
                                            <span>• {{ $p->brand->name }}</span>
                                        @endif
                                        @if($p->productCategory)
                                            <span class="badge bg-light text-muted border px-1.5 py-0">{{ $p->productCategory->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Stock Total -->
                        <td class="py-3">
                            @php
                                $totalUnits = $p->storeStocks->sum('quantity');
                            @endphp
                            <div class="fs-6 fw-bold {{ $totalUnits > 0 ? 'text-dark' : 'text-danger' }}">
                                {{ $totalUnits }} unités
                            </div>
                            <span class="badge {{ $totalUnits > 5 ? 'bg-success-subtle text-success' : ($totalUnits > 0 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }} rounded-pill" style="font-size: 0.68rem;">
                                {{ $totalUnits > 5 ? 'En stock' : ($totalUnits > 0 ? 'Stock bas' : 'Rupture') }}
                            </span>
                        </td>

                        <!-- Store Breakdown & Shelf Location -->
                        <td class="py-3">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                @forelse($stores as $st)
                                    @php
                                        $stockRecord = $p->storeStocks->firstWhere('store_id', $st->id);
                                        $qty = $stockRecord ? $stockRecord->quantity : 0;
                                        $loc = $stockRecord ? $stockRecord->location_code : null;
                                    @endphp
                                    <div class="store-stock-pill-badge {{ $qty > 0 ? 'border-primary-subtle' : 'opacity-75' }}">
                                        <div class="d-flex align-items-center gap-1.5">
                                            <i class="fas {{ $st->type === 'warehouse' ? 'fa-warehouse text-purple' : 'fa-shop text-primary' }}"></i>
                                            <span class="fw-semibold text-dark">{{ $st->name }} :</span>
                                            <span class="badge {{ $qty > 0 ? 'bg-primary text-white' : 'bg-light text-muted border' }} rounded-pill px-2 py-0.5">
                                                {{ $qty }} pcs
                                            </span>
                                        </div>
                                        @if($loc)
                                            <div class="badge bg-white text-secondary border font-monospace ms-1 px-1.5 py-0.5" style="font-size: 0.65rem;" title="Emplacement précis">
                                                <i class="fas fa-map-pin me-0.5 text-danger opacity-75"></i>{{ $loc }}
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <span class="text-muted small">Aucun dépôt actif.</span>
                                @endforelse
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="pe-3 py-3 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary shadow-xs"
                                        onclick="openLocationEditModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ json_encode($p->storeStocks) }})"
                                        title="Modifier emplacement / quantité par magasin">
                                    <i class="fas fa-sliders me-1"></i> Emplacement
                                </button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-secondary shadow-xs"
                                        onclick="openTransferForProduct({{ $p->id }}, '{{ addslashes($p->name) }}')"
                                        title="Transférer vers un autre magasin">
                                    <i class="fas fa-arrow-right-arrow-left"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-25"></i>
                                <p class="mb-0 fw-semibold">Aucun produit trouvé.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View (d-block d-lg-none) -->
        <div class="d-block d-lg-none p-3 space-y-3">
            @forelse($products as $p)
            @php $totalUnits = $p->storeStocks->sum('quantity'); @endphp
            <div class="stock-prod-card p-3">
                <div class="d-flex align-items-start gap-2.5 mb-2.5">
                    @if($p->image)
                        <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="prod-thumb-matrix shadow-xs"
                             onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                    @else
                        <div class="prod-thumb-matrix d-flex align-items-center justify-content-center text-muted">
                            <i class="fas fa-box"></i>
                        </div>
                    @endif
                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                            <span class="badge {{ $totalUnits > 5 ? 'bg-success-subtle text-success border border-success-subtle' : ($totalUnits > 0 ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle') }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                {{ $totalUnits > 5 ? 'En stock' : ($totalUnits > 0 ? 'Stock bas' : 'Rupture') }}
                            </span>
                            <span class="font-monospace fw-bold {{ $totalUnits > 0 ? 'text-dark' : 'text-danger' }}" style="font-size: 0.82rem;">
                                Total : {{ $totalUnits }} pcs
                            </span>
                        </div>
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem;" title="{{ $p->name }}">{{ $p->name }}</div>
                        <div class="text-muted small" style="font-size: 0.72rem;">
                            <span class="font-monospace">SKU: {{ $p->sku }}</span>
                            @if($p->brand) • {{ $p->brand->name }} @endif
                            @if($p->productCategory) • {{ $p->productCategory->name }} @endif
                        </div>
                    </div>
                </div>

                <!-- Per-store Breakdown Rows on Mobile -->
                <div class="p-2.5 rounded-3 bg-light border space-y-2 mb-2.5">
                    @foreach($stores as $st)
                        @php
                            $stRecord = $p->storeStocks->firstWhere('store_id', $st->id);
                            $stQty = $stRecord ? $stRecord->quantity : 0;
                            $stLoc = $stRecord ? $stRecord->location_code : null;
                        @endphp
                        <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.76rem;">
                            <div class="d-flex align-items-center gap-1.5 text-truncate" style="max-width: 65%;">
                                <i class="fas {{ $st->type === 'warehouse' ? 'fa-warehouse text-purple' : 'fa-shop text-primary' }}" style="width: 14px;"></i>
                                <span class="fw-medium text-dark text-truncate">{{ $st->name }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                <span class="badge {{ $stQty > 0 ? 'bg-primary' : 'bg-white text-muted border' }} rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                    {{ $stQty }} pcs
                                </span>
                                @if($stLoc)
                                    <span class="badge bg-white text-dark border font-monospace px-1.5 py-0.5" style="font-size: 0.65rem;" title="Rayon / Étagère">
                                        <i class="fas fa-map-pin text-danger me-0.5"></i>{{ $stLoc }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Thumb-friendly action buttons -->
                <div class="row g-2 pt-1 border-top">
                    <div class="col-6">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill w-100 py-1.5 fw-semibold"
                                onclick="openLocationEditModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ json_encode($p->storeStocks) }})"
                                style="font-size: 0.76rem;">
                            <i class="fas fa-sliders me-1"></i> Emplacement
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill w-100 py-1.5 fw-semibold"
                                onclick="openTransferForProduct({{ $p->id }}, '{{ addslashes($p->name) }}')"
                                style="font-size: 0.76rem;">
                            <i class="fas fa-arrow-right-arrow-left me-1"></i> Transférer
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted small">
                <i class="fas fa-box-open fa-2x mb-2 text-secondary opacity-25"></i>
                <p class="mb-0">Aucun produit trouvé.</p>
            </div>
            @endforelse
        </div>

        @if($products->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: AJUSTER EMPLACEMENT (RAYON) & QUANTITÉ          -->
<!-- ======================================================= -->
<div class="modal fade" id="locationEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('stores.stock.location') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <input type="hidden" name="product_id" id="loc_product_id">
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-map-pin text-danger"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 fs-6" id="locModalTitle">Emplacement & Stock Magasin</h6>
                        <span class="text-white-50" style="font-size: 0.72rem;" id="locModalSub">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Point de Stockage / Dépôt <span class="text-danger">*</span></label>
                    <select name="store_id" id="loc_store_id" class="form-select rounded-3" required onchange="onStoreSelectChange()">
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="fas fa-tags text-primary me-1"></i> Emplacement précis (Rayon / Étagère / Casier)
                    </label>
                    <input type="text" name="location_code" id="loc_location_code" class="form-control rounded-3" placeholder="Ex: Rayon A - Étagère 3 - Casier 12">
                    <div class="form-text" style="font-size: 0.72rem;">Indiquez où le préparateur ou vendeur trouvera l'article.</div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Quantité en stock <span class="text-danger">*</span></label>
                        <input type="number" min="0" name="quantity" id="loc_quantity" class="form-control rounded-3 fw-bold" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Seuil Alerte Stock Bas</label>
                        <input type="number" min="0" name="min_quantity" id="loc_min_quantity" class="form-control rounded-3" value="2">
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-xs">
                    <i class="fas fa-save me-1.5"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: TRANSFÉRER DU STOCK ENTRE MAGASINS              -->
<!-- ======================================================= -->
<div class="modal fade" id="transferStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('stores.stock.transfer') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 fs-6">Transfert de Stock Inter-Magasins</h5>
                        <p class="small text-white-50 mb-0" style="font-size: 0.76rem;">Déplacer des unités d'un dépôt source vers un dépôt destination</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Article à transférer <span class="text-danger">*</span></label>
                    <select name="product_id" id="transfer_product_id" class="form-select rounded-3" required>
                        <option value="">Sélectionnez un produit...</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->sku }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Site Source (Origine) <span class="text-danger">*</span></label>
                        <select name="from_store_id" class="form-select rounded-3" required>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Site Destination (Arrivée) <span class="text-danger">*</span></label>
                        <select name="to_store_id" class="form-select rounded-3" required>
                            @foreach($stores as $idx => $st)
                                <option value="{{ $st->id }}" {{ $idx === 1 ? 'selected' : '' }}>{{ $st->name }} ({{ $st->code }})</option>
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
                        <label class="form-label small fw-bold text-dark mb-1">Nouvel Emplacement à l'arrivée (Optionnel)</label>
                        <input type="text" name="to_location" class="form-control rounded-3" placeholder="Ex: Rayon B - Étagère 1">
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Motif du transfert</label>
                    <input type="text" name="reason" class="form-control rounded-3" placeholder="Ex: Réapprovisionnement boutique, demande client...">
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-xs">
                    <i class="fas fa-check-circle me-1.5"></i> Effectuer le Transfert
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentProductStocks = [];

function openLocationEditModal(productId, productName, stocks) {
    currentProductStocks = stocks || [];
    document.getElementById('loc_product_id').value = productId;
    document.getElementById('locModalTitle').textContent = `Emplacement & Stock : ${productName}`;
    document.getElementById('locModalSub').textContent = `ID #${productId}`;
    onStoreSelectChange();
    const modal = new bootstrap.Modal(document.getElementById('locationEditModal'));
    modal.show();
}

function onStoreSelectChange() {
    const storeId = parseInt(document.getElementById('loc_store_id').value);
    const stock = currentProductStocks.find(s => parseInt(s.store_id) === storeId);
    if (stock) {
        document.getElementById('loc_location_code').value = stock.location_code || '';
        document.getElementById('loc_quantity').value = stock.quantity || 0;
        document.getElementById('loc_min_quantity').value = stock.min_quantity || 2;
    } else {
        document.getElementById('loc_location_code').value = '';
        document.getElementById('loc_quantity').value = 0;
        document.getElementById('loc_min_quantity').value = 2;
    }
}

function openTransferForProduct(productId, productName) {
    document.getElementById('transfer_product_id').value = productId;
    const modal = new bootstrap.Modal(document.getElementById('transferStockModal'));
    modal.show();
}
</script>
@endpush
@endsection
