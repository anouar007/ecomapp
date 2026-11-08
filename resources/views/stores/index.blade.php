@extends('layouts.app')

@section('title', 'Gestion des Magasins, Dépôts & Emplacements')

@push('styles')
<style>
.store-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.5rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.store-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.06);
}
.stat-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.25rem;
}
.store-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-purple-subtle text-purple border border-purple-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                    <i class="fas fa-warehouse me-1"></i> Logistique Multi-Sites
                </span>
                <span class="text-muted small">• Magasins, Dépôts & Emplacements de Stock</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0">Magasins & Emplacements de Stockage</h1>
            <p class="text-muted small mb-0">Gérez vos boutiques physiques, vos dépôts centraux et le stock stocké dans chaque rayon/étagère.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('stores.stock') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
                <i class="fas fa-boxes-stacked me-1.5"></i> Matrice des Stocks & Rayons
            </a>
            <button type="button" class="btn btn-danger rounded-pill px-3.5 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#newStoreModal">
                <i class="fas fa-plus-circle me-1.5"></i> Nouveau Point de Stockage
            </button>
        </div>
    </div>

    <!-- Alert / Flash message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-check-circle fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-circle fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Magasins / Boutiques</span>
                    <div class="rounded-3 bg-primary-subtle text-primary p-2">
                        <i class="fas fa-shop"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-dark mb-0">{{ $totalStores }}</div>
                <div class="text-muted small mt-1">Points de vente physiques</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Dépôts & Entrepôts</span>
                    <div class="rounded-3 bg-purple-subtle text-purple p-2">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-dark mb-0">{{ $totalWarehouses }}</div>
                <div class="text-muted small mt-1">Plateformes logistiques</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Points Actifs</span>
                    <div class="rounded-3 bg-success-subtle text-success p-2">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-success mb-0">{{ $totalActive }}</div>
                <div class="text-muted small mt-1">Opérationnels en temps réel</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Unités en Stock</span>
                    <div class="rounded-3 bg-danger-subtle text-danger p-2">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-danger mb-0">{{ number_format($totalStockUnits, 0, ',', ' ') }}</div>
                <div class="text-muted small mt-1">Quantité totale stockée</div>
            </div>
        </div>
    </div>

    <!-- Store Cards Grid -->
    <div class="row g-4">
        @forelse($stores as $st)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="store-card">
                <div>
                    <!-- Top store info -->
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="store-icon-wrap {{ $st->type === 'warehouse' ? 'bg-purple-subtle text-purple' : 'bg-primary-subtle text-primary' }}">
                                <i class="fas {{ $st->type === 'warehouse' ? 'fa-warehouse' : ($st->type === 'showroom' ? 'fa-tv' : 'fa-store') }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 fs-6">{{ $st->name }}</h5>
                                <div class="d-flex align-items-center gap-2 mt-0.5">
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">{{ $st->code }}</span>
                                    <span class="badge {{ $st->type_badge_class }} border rounded-pill" style="font-size: 0.68rem;">{{ $st->type_label }}</span>
                                    @if($st->is_default)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill" style="font-size: 0.68rem;">Principal</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li>
                                    <button class="dropdown-item small" onclick="openEditStoreModal({{ $st->toJson() }})">
                                        <i class="fas fa-pencil text-primary me-2"></i> Modifier
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item small" href="{{ route('stores.stock', ['store_id' => $st->id]) }}">
                                        <i class="fas fa-boxes-stacked text-success me-2"></i> Voir les stocks
                                    </a>
                                </li>
                                @if(!$st->is_default)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('stores.destroy', $st->id) }}" onsubmit="return confirm('Supprimer ce point de stockage ?');">
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
                    <div class="space-y-1.5 text-muted small mb-3">
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
                            <span class="text-muted" style="font-size: 0.72rem;">Articles distincts :</span>
                            <span class="fw-bold text-dark small ms-1">{{ $st->product_stocks_count }}</span>
                        </div>
                        <div>
                            <span class="text-muted" style="font-size: 0.72rem;">Total Unités :</span>
                            <span class="fw-bold text-success small ms-1">{{ number_format($st->total_units, 0, ',', ' ') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('stores.stock', ['store_id' => $st->id]) }}" class="btn btn-sm btn-outline-dark rounded-pill w-100 fw-semibold">
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
                        <p class="small text-white-50 mb-0" style="font-size: 0.76rem;">Magasin physique, entrepôt ou dépôt central</p>
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

