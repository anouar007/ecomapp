@extends('layouts.app')

@section('title', 'Arrivages & Coûts d\'Expédition Fournisseurs (Admin)')

@push('styles')
<style>
.shipment-kpi-card {
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
.shipment-kpi-card:hover {
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
.chips-scroll-shipment {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.chips-scroll-shipment::-webkit-scrollbar {
    display: none;
}
.shipment-pill-btn {
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
.shipment-pill-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.shipment-pill-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}
.shipment-mobile-card {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- En-tête de page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-truck-ramp-box"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="badge bg-danger rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                        <i class="fas fa-lock me-1"></i> GESTION DES EXPÉDITIONS & COÛTS
                    </span>
                    <span class="text-muted small">• Coûts d'Achat par Arrivage & Lot</span>
                </div>
                <h1 class="brand-title">Arrivages & Coûts par Expédition</h1>
                <p class="brand-subtitle">Enregistrez le coût d'achat spécifique de chaque arrivage fournisseur, ventilez les frais de douane et de transport, et suivez vos marges réelles par lot.</p>
            </div>
        </div>

        <div class="brand-header-actions">
            <a href="{{ route('reports.profitability.costs') }}" class="btn-brand-outline">
                <i class="fas fa-coins me-1.5" style="color: var(--primary-color)"></i> Coûts Catalogue
            </a>
            <a href="{{ route('shipments.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus-circle me-1.5"></i> Nouvel Arrivage
            </a>
        </div>
    </div>

    <!-- Navigation Croisée ERP (Arrivages <-> Coûts <-> Rentabilité) -->
    <div class="subnav-pills-bar">
        <a href="{{ route('shipments.index') }}" class="subnav-pill active">
            <i class="fas fa-truck-ramp-box text-danger"></i>
            <span>Arrivages & Expéditions Fournisseurs</span>
            <span class="subnav-pill-badge">{{ $totalShipments }}</span>
        </a>
        <a href="{{ route('reports.profitability.costs') }}" class="subnav-pill">
            <i class="fas fa-coins text-warning"></i>
            <span>Coûts d'Achat & Prix de Revient</span>
        </a>
        <a href="{{ route('reports.profitability') }}" class="subnav-pill">
            <i class="fas fa-chart-pie text-success"></i>
            <span>Rapport de Rentabilité & Marges</span>
        </a>
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

    <!-- 4 Financial KPI Cards (Product Section Standard) -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Arrivages -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Arrivages</div>
                        <div class="stat-kpi-value text-dark">{{ $totalShipments }}</div>
                        <div class="stat-kpi-sub text-muted">{{ $activeSuppliersCount }} fournisseur(s)</div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-boxes-packing"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Valeur Marchandises Achetées -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Marchandises Net</div>
                        <div class="stat-kpi-value text-dark">{{ currency($totalLandedValue - $totalExtraFees) }}</div>
                        <div class="stat-kpi-sub text-muted">Prix d'achat facturé</div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Frais de Transit & Douane -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Frais Approche</div>
                        <div class="stat-kpi-value text-info">{{ currency($totalExtraFees) }}</div>
                        <div class="stat-kpi-sub text-muted">Douane, fret & transport</div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Coût Global Rendu Magasin -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card" style="border-left: 3.5px solid var(--success-color, #10b981);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Coût Global Rendu</div>
                        <div class="stat-kpi-value text-success">{{ currency($totalLandedValue) }}</div>
                        <div class="stat-kpi-sub text-muted">{{ number_format($totalUnitsReceived, 0, ',', ' ') }} unités reçues</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <!-- Fast Touch Filter Pills -->
            <div class="chips-scroll-shipment mb-3">
                <a href="{{ route('shipments.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
                   class="shipment-pill-btn {{ $status === 'all' ? 'active' : '' }}">
                    <span>Tous les arrivages</span>
                    <span class="badge bg-secondary text-white rounded-pill ms-1">{{ $totalShipments }}</span>
                </a>
                <a href="{{ route('shipments.index', array_merge(request()->except('status', 'page'), ['status' => 'received'])) }}" 
                   class="shipment-pill-btn {{ $status === 'received' ? 'active' : '' }}">
                    <i class="fas fa-check text-success"></i>
                    <span>Reçus en stock</span>
                </a>
                <a href="{{ route('shipments.index', array_merge(request()->except('status', 'page'), ['status' => 'in_transit'])) }}" 
                   class="shipment-pill-btn {{ $status === 'in_transit' ? 'active' : '' }}">
                    <i class="fas fa-truck text-primary"></i>
                    <span>En cours d'acheminement</span>
                </a>
                <a href="{{ route('shipments.index', array_merge(request()->except('status', 'page'), ['status' => 'draft'])) }}" 
                   class="shipment-pill-btn {{ $status === 'draft' ? 'active' : '' }}">
                    <i class="fas fa-file-pen text-secondary"></i>
                    <span>Brouillons</span>
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('shipments.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="status" value="{{ $status }}">

                <!-- Search Input -->
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control bg-light border-start-0" placeholder="Rechercher par référence, fournisseur, facture...">
                    </div>
                </div>

                <!-- Store Filter -->
                <div class="col-6 col-md-3">
                    <select name="store_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">Tous les dépôts / magasins</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ $storeId == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="col-6 col-md-4 d-flex align-items-center gap-1.5">
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm bg-light" placeholder="Du" title="Date début">
                    <span class="text-muted small">à</span>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm bg-light" placeholder="Au" title="Date fin">
                </div>

                <!-- Submit / Reset -->
                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-semibold rounded-3">Filtrer</button>
                    @if($search || $storeId || $dateFrom || $dateTo || $status !== 'all')
                        <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-light border rounded-3" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Shipments Table & Cards -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        
        <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark">Historique des Arrivages & Coûts de Lots ({{ $shipments->total() }})</h6>
                <span class="text-muted small">Chaque arrivage conserve l'historique exact des coûts fournisseurs et des frais d'approche.</span>
            </div>
            <a href="{{ route('shipments.create') }}" class="btn btn-sm btn-danger rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                <i class="fas fa-plus me-1"></i> Nouvel Arrivage
            </a>
        </div>

        <!-- 1. DESKTOP VIEW (d-none d-lg-block) -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 16%;">Réf. Arrivage</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 20%;">Fournisseur & Facture</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 14%;">Destination</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 10%;">Articles</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 14%;">Coût Marchandises</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 14%;">Coût Rendu Total</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Statut & Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($shipments as $shp)
                    <tr class="hover-bg-light">
                        <!-- Reference & Date -->
                        <td class="ps-3 py-3">
                            <a href="{{ route('shipments.show', $shp->id) }}" class="fw-bold text-dark text-decoration-none hover-primary font-monospace" style="font-size: 0.9rem;">
                                {{ $shp->reference }}
                            </a>
                            <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                <i class="far fa-calendar me-1 opacity-50"></i>{{ $shp->received_date->format('d/m/Y') }}
                            </div>
                        </td>

                        <!-- Supplier & Invoice -->
                        <td class="py-3">
                            <div class="fw-semibold text-dark">{{ $shp->supplier_name }}</div>
                            <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                @if($shp->invoice_number)
                                    <span>Facture: <strong>{{ $shp->invoice_number }}</strong></span>
                                @endif
                                @if($shp->tracking_number)
                                    <span>• Suivi: {{ $shp->tracking_number }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Store / Depot -->
                        <td class="py-3">
                            @if($shp->store)
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fas fa-warehouse text-secondary me-1"></i>{{ $shp->store->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1">Dépôt Central</span>
                            @endif
                        </td>

                        <!-- Items & Units Count -->
                        <td class="py-3 text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold">
                                {{ $shp->items->count() }} réf(s)
                            </span>
                            <div class="text-muted small mt-0.5" style="font-size: 0.7rem;">
                                {{ $shp->total_units }} unité(s)
                            </div>
                        </td>

                        <!-- Net Items Cost -->
                        <td class="py-3 text-end">
                            <div class="fw-semibold text-dark">{{ currency($shp->total_items_cost) }}</div>
                            @if($shp->total_extra_fees > 0)
                                <div class="text-muted small" style="font-size: 0.7rem;">
                                    + {{ currency($shp->total_extra_fees) }} frais
                                </div>
                            @endif
                        </td>

                        <!-- Total Landed Cost & Margin -->
                        <td class="py-3 text-end">
                            <div class="fw-bold fs-6 text-dark">{{ currency($shp->total_landed_cost) }}</div>
                            @php $avgMargin = $shp->average_margin; @endphp
                            <div class="mt-0.5">
                                <span class="badge {{ $avgMargin >= 30 ? 'bg-success-subtle text-success' : ($avgMargin >= 15 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning') }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    Marge moy. {{ $avgMargin }}%
                                </span>
                            </div>
                        </td>

                        <!-- Status & Actions -->
                        <td class="pe-3 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                <span class="badge {{ $shp->status_badge_class }} rounded-pill px-2.5 py-1 fw-semibold me-1" style="font-size: 0.72rem;">
                                    {{ $shp->status_label }}
                                </span>
                                <a href="{{ route('shipments.show', $shp->id) }}" class="btn btn-sm btn-light border rounded-circle text-primary d-inline-flex align-items-center justify-content-center"
                                   style="width: 32px; height: 32px; padding: 0;" title="Consulter les coûts détaillés">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('shipments.destroy', $shp->id) }}" class="d-inline" onsubmit="return confirm('Confirmer la suppression de cet arrivage ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-circle text-danger d-inline-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; padding: 0;" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-truck-ramp-box fa-3x mb-3 opacity-25"></i>
                            <p class="mb-2 fw-semibold">Aucun arrivage enregistré pour le moment.</p>
                            <a href="{{ route('shipments.create') }}" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5">
                                <i class="fas fa-plus me-1"></i> Créer votre premier arrivage
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. MOBILE VIEW (d-block d-lg-none) -->
        <div class="d-block d-lg-none p-3 space-y-3 bg-light bg-opacity-50">
            @forelse($shipments as $shp)
            <div class="shipment-mobile-card p-3 bg-white">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <div>
                        <a href="{{ route('shipments.show', $shp->id) }}" class="fw-bold text-dark text-decoration-none font-monospace fs-6">
                            {{ $shp->reference }}
                        </a>
                        <div class="text-muted small mt-0.5" style="font-size: 0.74rem;">
                            {{ $shp->supplier_name }} • {{ $shp->received_date->format('d/m/Y') }}
                        </div>
                    </div>
                    <span class="badge {{ $shp->status_badge_class }} rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                        {{ $shp->status_label }}
                    </span>
                </div>

                <div class="p-2.5 rounded-3 bg-light border space-y-1.5 mb-2.5" style="font-size: 0.78rem;">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">Articles / Unités :</span>
                        <strong class="text-dark">{{ $shp->items->count() }} réf(s) ({{ $shp->total_units }} pcs)</strong>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">Frais annexes (fret/douane) :</span>
                        <span class="text-muted">{{ currency($shp->total_extra_fees) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-1.5 border-top">
                        <span class="fw-semibold text-dark">Coût Total Rendu :</span>
                        <strong class="fs-6 text-dark">{{ currency($shp->total_landed_cost) }}</strong>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-1">
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                        Marge estimée : {{ $shp->average_margin }}%
                    </span>
                    <a href="{{ route('shipments.show', $shp->id) }}" class="btn btn-sm btn-dark rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                        Détail des coûts <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <p class="mb-0 fw-semibold">Aucun arrivage trouvé.</p>
            </div>
            @endforelse
        </div>

        @if($shipments->hasPages())
        <div class="p-3 border-top bg-light d-flex justify-content-center">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal: Product Cost History Across Shipments -->
<div class="modal fade" id="productShipmentHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white py-3 px-3.5 border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-warning"></i>
                    <h6 class="modal-title fw-bold mb-0">Historique des Coûts d'Achat par Arrivage</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom flex-wrap gap-2">
                    <div>
                        <span class="badge bg-light text-secondary border font-monospace" id="histSku">—</span>
                        <h5 class="fw-bold text-dark mb-0 mt-1" id="histProdName">—</h5>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small">Coût actuel au catalogue :</span>
                        <div class="fs-5 fw-bold text-primary" id="histActiveCost">—</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 small text-secondary">Réf. Arrivage</th>
                                <th class="py-2 small text-secondary">Date</th>
                                <th class="py-2 small text-secondary">Fournisseur</th>
                                <th class="py-2 small text-secondary text-center">Qté</th>
                                <th class="py-2 small text-secondary text-end">Coût Fournisseur</th>
                                <th class="py-2 small text-secondary text-end">Frais Annexe</th>
                                <th class="py-2 small text-secondary text-end">Coût Rendu</th>
                                <th class="py-2 small text-secondary text-end">Marge %</th>
                            </tr>
                        </thead>
                        <tbody id="histTableBody" class="divide-y">
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Chargement de l'historique...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-2 border-0">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewProductShipmentHistory(productId) {
    const modal = new bootstrap.Modal(document.getElementById('productShipmentHistoryModal'));
    modal.show();

    fetch(`/shipments/product-history/${productId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('histSku').textContent = data.product.sku;
            document.getElementById('histProdName').textContent = data.product.name;
            document.getElementById('histActiveCost').textContent = data.product.active_cost;

            const tbody = document.getElementById('histTableBody');
            if (data.history.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Aucun arrivage enregistré pour cet article pour le moment.</td></tr>';
                return;
            }

            let html = '';
            data.history.forEach(item => {
                html += `
                    <tr>
                        <td class="py-2.5 font-monospace fw-bold">${item.reference}</td>
                        <td class="py-2.5 small text-muted">${item.received_date}</td>
                        <td class="py-2.5 small fw-semibold">${item.supplier_name}</td>
                        <td class="py-2.5 text-center fw-bold">${item.quantity}</td>
                        <td class="py-2.5 text-end small">${item.unit_cost}</td>
                        <td class="py-2.5 text-end small text-muted">+${item.allocated_fees}</td>
                        <td class="py-2.5 text-end fw-bold text-dark">${item.landed_cost}</td>
                        <td class="py-2.5 text-end"><span class="badge bg-success-subtle text-success rounded-pill">${item.margin_pct}</span></td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        })
        .catch(err => {
            document.getElementById('histTableBody').innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Erreur de chargement.</td></tr>';
        });
}
</script>
@endpush
@endsection
