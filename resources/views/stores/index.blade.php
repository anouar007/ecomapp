@extends('layouts.app')

@section('title', 'Gestion des Magasins, Dépôts & Emplacements')

@push('styles')
<style>
/* ==========================================================================
   STORES & WAREHOUSES MANAGEMENT - PRO EXECUTIVE ALIGNMENT
   ========================================================================== */
.store-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.25rem 1.4rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.store-kpi-card:hover {
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
.store-box-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.4rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.store-box-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}
.store-avatar-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="dashboard-container">

    <!-- En-tête de page (Standard Products Style) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0" style="background: linear-gradient(135deg, #7c3aed, #9333ea); box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);">
                <i class="fas fa-warehouse"></i>
            </div>
            <div>
                <h1 class="brand-title">Points de Stockage & Dépôts</h1>
                <p class="brand-subtitle">Configurez vos magasins physiques, vos dépôts logistiques et les emplacements de stockage précis</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('stores.stock') }}" class="btn-brand-outline">
                <i class="fas fa-boxes-stacked me-1.5" style="color: #7c3aed;"></i> <span>Matrice des Rayons</span>
            </a>
            <button type="button" class="btn-brand-primary" data-bs-toggle="modal" data-bs-target="#newStoreModal">
                <i class="fas fa-plus me-1"></i> <span>Nouveau Point de Stockage</span>
            </button>
        </div>
    </div>

    <!-- Navigation Croisée ERP (Inventaire <-> Matrice des Rayons <-> Dépôts) -->
    <div class="subnav-pills-bar">
        <a href="{{ route('inventory.index') }}" class="subnav-pill">
            <i class="fas fa-boxes-stacked"></i>
            <span>Inventaire Global Articles</span>
        </a>
        <a href="{{ route('stores.stock') }}" class="subnav-pill">
            <i class="fas fa-cubes-stacked text-purple"></i>
            <span>Matrice par Dépôt & Rayons</span>
        </a>
        <a href="{{ route('stores.index') }}" class="subnav-pill active">
            <i class="fas fa-warehouse text-info"></i>
            <span>Magasins & Points de Vente</span>
        </a>
        <a href="{{ route('inventory.movements') }}" class="subnav-pill">
            <i class="fas fa-history text-primary"></i>
            <span>Mouvements & Traçabilité</span>
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-triangle-exclamation fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <!-- 4 Perfectly Aligned KPI Cards (Standard Products Style) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('stores.index', ['type' => 'physical_store']) }}" class="stat-kpi-card text-decoration-none {{ request('type') === 'physical_store' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Magasins / Boutiques</div>
                        <div class="stat-kpi-value text-dark">{{ $totalStores }}</div>
                        <div class="stat-kpi-sub text-muted"><span class="badge bg-light text-secondary border">Points de vente physiques</span></div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-shop"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('stores.index', ['type' => 'warehouse']) }}" class="stat-kpi-card text-decoration-none {{ request('type') === 'warehouse' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Dépôts & Entrepôts</div>
                        <div class="stat-kpi-value" style="color: #7c3aed;">{{ $totalWarehouses }}</div>
                        <div class="stat-kpi-sub text-muted"><span class="badge bg-purple-subtle text-purple border">Hubs centraux</span></div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #f3e8ff; color: #7e22ce;">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('stores.index') }}" class="stat-kpi-card text-decoration-none {{ !request('type') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Sites Opérationnels</div>
                        <div class="stat-kpi-value text-success">{{ $totalActive }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-circle-check text-success me-1"></i>En production</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('stores.stock') }}" class="stat-kpi-card text-decoration-none">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Valorisation Stocks</div>
                        <div class="stat-kpi-value text-dark">{{ currency($totalStockCostValuation) }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-boxes-stacked text-warning me-1"></i>{{ number_format($totalStockUnits, 0, ',', ' ') }} pcs au coût</div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Store Cards Grid -->
    <div class="row g-4">
        @forelse($stores as $st)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="store-box-card">
                <div>
                    <!-- Top store info -->
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="store-avatar-icon {{ $st->type === 'warehouse' ? 'bg-purple-subtle text-purple' : 'bg-primary-subtle text-primary' }}" style="{{ $st->type === 'warehouse' ? 'background: #f3e8ff; color: #7e22ce;' : '' }}">
                                <i class="fas {{ $st->type === 'warehouse' ? 'fa-warehouse' : ($st->type === 'showroom' ? 'fa-tv' : 'fa-store') }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 fs-6">{{ $st->name }}</h5>
                                <div class="d-flex align-items-center gap-1.5 mt-1 flex-wrap">
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">{{ $st->code }}</span>
                                    <span class="badge {{ $st->type_badge_class }} border rounded-pill" style="font-size: 0.68rem;">{{ $st->type_label }}</span>
                                    @if($st->is_default)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill" style="font-size: 0.68rem;">
                                            <i class="fas fa-star me-0.5"></i> Principal
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border rounded-circle shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li>
                                    <button class="dropdown-item small" onclick="openEditStoreModal({{ $st->toJson() }})">
                                        <i class="fas fa-pencil text-primary me-2"></i> Modifier les infos
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item small" href="{{ route('stores.stock', ['store_id' => $st->id]) }}">
                                        <i class="fas fa-boxes-stacked text-success me-2"></i> Inventaire de ce site
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small" href="{{ route('expenses.index', ['store_id' => $st->id]) }}">
                                        <i class="fas fa-file-invoice-dollar text-danger me-2"></i> Charges & Dépenses du site
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small" href="{{ route('client-credits.transactions', ['store_id' => $st->id]) }}">
                                        <i class="fas fa-cash-register text-info me-2"></i> Caisse & Règlements clients
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small" href="{{ route('activity-logs.index', ['search' => $st->name]) }}">
                                        <i class="fas fa-history text-secondary me-2"></i> Historique des opérations
                                    </a>
                                </li>
                                @if(!$st->is_default)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('stores.destroy', $st->id) }}" onsubmit="return confirm('Confirmez-vous la suppression de ce point de stockage ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item small text-danger">
                                            <i class="fas fa-trash me-2"></i> Supprimer
                                        </button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Store Details -->
                    <div class="text-muted small space-y-1.5 mb-3" style="font-size: 0.78rem;">
                        @if($st->city || $st->address)
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-location-dot text-secondary opacity-50" style="width: 14px;"></i>
                                <span class="text-truncate">{{ $st->city ? $st->city.' — ' : '' }}{{ $st->address ?? '' }}</span>
                            </div>
                        @endif
                        @if($st->phone)
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-phone text-secondary opacity-50" style="width: 14px;"></i>
                                <span>{{ $st->phone }}</span>
                            </div>
                        @endif
                        @if($st->email)
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-envelope text-secondary opacity-50" style="width: 14px;"></i>
                                <span class="text-truncate">{{ $st->email }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer with Stock Stats & Cross-Module Quick Actions -->
                <div class="pt-3 border-top mt-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span class="text-muted" style="font-size: 0.72rem;">Stock physique :</span>
                            <span class="fw-bold text-success small ms-1">{{ number_format($st->total_units, 0, ',', ' ') }} pcs</span>
                        </div>
                        <div>
                            <span class="text-muted" style="font-size: 0.72rem;">Valeur achat :</span>
                            <span class="fw-bold text-dark small ms-1">{{ currency($st->cost_valuation) }}</span>
                        </div>
                    </div>
                    @if($st->month_expenses > 0 || $st->month_collections > 0)
                    <div class="d-flex align-items-center justify-content-between mb-2.5 p-2 rounded-3 bg-light border" style="font-size: 0.72rem;">
                        <div>
                            <span class="text-muted">Charges mois :</span>
                            <a href="{{ route('expenses.index', ['store_id' => $st->id]) }}" class="fw-bold text-danger text-decoration-none ms-1">
                                {{ currency($st->month_expenses) }}
                            </a>
                        </div>
                        @if($st->month_collections > 0)
                        <div>
                            <span class="text-muted">Caisse :</span>
                            <a href="{{ route('client-credits.transactions', ['store_id' => $st->id]) }}" class="fw-bold text-success text-decoration-none ms-1">
                                +{{ currency($st->month_collections) }}
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="d-flex align-items-center gap-1.5">
                        <a href="{{ route('stores.stock', ['store_id' => $st->id]) }}" class="btn btn-sm btn-outline-dark rounded-pill flex-fill fw-semibold shadow-xs">
                            <i class="fas fa-boxes-stacked me-1.5"></i> Inventaire
                        </a>
                        <a href="{{ route('expenses.index', ['store_id' => $st->id]) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 text-danger" title="Charges de ce point de vente">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </a>
                        <a href="{{ route('client-credits.transactions', ['store_id' => $st->id]) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 text-success" title="Caisse & Règlements">
                            <i class="fas fa-cash-register"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="fas fa-warehouse fa-3x mb-3 text-secondary opacity-25"></i>
                <p class="mb-0 fw-semibold">Aucun point de stockage configuré.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: NOUVEAU POINT DE STOCKAGE                       -->
<!-- ======================================================= -->
<div class="modal fade" id="newStoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('stores.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 fs-6">Nouveau Point de Stockage</h5>
                        <p class="small text-white-50 mb-0" style="font-size: 0.76rem;">Magasin physique, entrepôt logistique ou showroom</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-dark mb-1">Nom du site <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Ex: Magasin Maarif Casablanca" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">Code unique <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control rounded-3 font-monospace text-uppercase" placeholder="MAG-MAARIF" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Type d'emplacement <span class="text-danger">*</span></label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="store" selected>Magasin / Boutique de vente</option>
                            <option value="warehouse">Dépôt Central / Entrepôt logistique</option>
                            <option value="showroom">Showroom</option>
                            <option value="workshop">Atelier SAV / Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Ville</label>
                        <input type="text" name="city" class="form-control rounded-3" placeholder="Casablanca, Rabat, Marrakech...">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Adresse complète</label>
                    <input type="text" name="address" class="form-control rounded-3" placeholder="Numéro, rue, quartier...">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Téléphone de contact</label>
                        <input type="tel" name="phone" class="form-control rounded-3" placeholder="05 22 00 00 00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Email</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="depot@winashop.com">
                    </div>
                </div>

                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label class="form-check-label small fw-bold text-dark" for="is_active">Site actif</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1">
                            <label class="form-check-label small fw-bold text-dark" for="is_default">Définir comme site principal</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-xs">
                    <i class="fas fa-check-circle me-1.5"></i> Créer le Site
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: MODIFIER UN POINT DE STOCKAGE                   -->
<!-- ======================================================= -->
<div class="modal fade" id="editStoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="editStoreForm" method="POST" action="" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            @method('PUT')
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <h5 class="modal-title fw-bold mb-0 fs-6" id="editStoreTitle">Modifier le Point de Stockage</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-dark mb-1">Nom du site <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">Code unique <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit_code" class="form-control rounded-3 font-monospace text-uppercase" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Type d'emplacement <span class="text-danger">*</span></label>
                        <select name="type" id="edit_type" class="form-select rounded-3" required>
                            <option value="store">Magasin / Boutique de vente</option>
                            <option value="warehouse">Dépôt Central / Entrepôt logistique</option>
                            <option value="showroom">Showroom</option>
                            <option value="workshop">Atelier SAV / Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Ville</label>
                        <input type="text" name="city" id="edit_city" class="form-control rounded-3">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Adresse complète</label>
                    <input type="text" name="address" id="edit_address" class="form-control rounded-3">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Téléphone</label>
                        <input type="tel" name="phone" id="edit_phone" class="form-control rounded-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3">
                    </div>
                </div>

                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label small fw-bold text-dark" for="edit_is_active">Site actif</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_default" id="edit_is_default" value="1">
                            <label class="form-check-label small fw-bold text-dark" for="edit_is_default">Site principal</label>
                        </div>
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

@push('scripts')
<script>
function openEditStoreModal(store) {
    document.getElementById('editStoreForm').action = `/stores/${store.id}`;
    document.getElementById('editStoreTitle').textContent = `Modifier : ${store.name}`;
    document.getElementById('edit_name').value = store.name || '';
    document.getElementById('edit_code').value = store.code || '';
    document.getElementById('edit_type').value = store.type || 'store';
    document.getElementById('edit_city').value = store.city || '';
    document.getElementById('edit_address').value = store.address || '';
    document.getElementById('edit_phone').value = store.phone || '';
    document.getElementById('edit_email').value = store.email || '';
    document.getElementById('edit_is_active').checked = !!store.is_active;
    document.getElementById('edit_is_default').checked = !!store.is_default;
    const modal = new bootstrap.Modal(document.getElementById('editStoreModal'));
    modal.show();
}
</script>
@endpush
@endsection
