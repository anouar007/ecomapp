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
<div class="container-fluid py-4">

    <!-- Top Executive Header -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-purple bg-opacity-25 border border-purple border-opacity-50 text-white d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.35rem; background: rgba(147, 51, 234, 0.25);">
                        <i class="fas fa-warehouse text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-purple rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem; background: #9333ea;">LOGISTIQUE MULTI-SITES</span>
                            <span class="text-white-50 small">• Points de Vente, Dépôts & Emplacements de Stock</span>
                        </div>
                        <h1 class="h3 fw-bold text-white mb-0">Points de Stockage & Dépôts</h1>
                        <p class="text-white-50 small mb-0 mt-1">Configurez vos magasins physiques, vos dépôts logistiques et les emplacements de stockage précis.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('stores.stock') }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold" style="font-size: 0.85rem;">
                        <i class="fas fa-boxes-stacked me-1.5"></i> Matrice des Stocks & Rayons
                    </a>
                    <button type="button" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#newStoreModal" style="font-size: 0.85rem;">
                        <i class="fas fa-plus-circle me-1.5"></i> Nouveau Point de Stockage
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

    <!-- 4 Perfectly Aligned KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="store-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Magasins / Boutiques</span>
                        <div class="kpi-icon-pill bg-primary-subtle text-primary">
                            <i class="fas fa-shop"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $totalStores }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    Boutiques & points de vente physiques
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="store-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Dépôts & Entrepôts</span>
                        <div class="kpi-icon-pill bg-purple-subtle text-purple" style="background: #f3e8ff; color: #7e22ce;">
                            <i class="fas fa-warehouse"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $totalWarehouses }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    Hubs & entrepôts centraux
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="store-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Sites Actifs</span>
                        <div class="kpi-icon-pill bg-success-subtle text-success">
                            <i class="fas fa-circle-check"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-success mb-0">{{ $totalActive }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    Opérationnels en production
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="store-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Unités Stockées</span>
                        <div class="kpi-icon-pill bg-danger-subtle text-danger">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-danger mb-0">{{ number_format($totalStockUnits, 0, ',', ' ') }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    Inventaire global multi-sites
                </div>
            </div>
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

                <!-- Footer with Stock Stats & Quick Action -->
                <div class="pt-3 border-top mt-2">
                    <div class="d-flex align-items-center justify-content-between mb-2.5">
                        <div>
                            <span class="text-muted" style="font-size: 0.72rem;">Références :</span>
                            <span class="fw-bold text-dark small ms-1">{{ $st->product_stocks_count }}</span>
                        </div>
                        <div>
                            <span class="text-muted" style="font-size: 0.72rem;">Unités en stock :</span>
                            <span class="fw-bold text-success small ms-1">{{ number_format($st->total_units, 0, ',', ' ') }} pcs</span>
                        </div>
                    </div>
                    <a href="{{ route('stores.stock', ['store_id' => $st->id]) }}" class="btn btn-sm btn-outline-dark rounded-pill w-100 fw-semibold shadow-xs">
                        <i class="fas fa-boxes-stacked me-1.5"></i> Consulter l'inventaire de ce site
                    </a>
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
