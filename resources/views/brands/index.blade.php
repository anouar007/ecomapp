@extends('layouts.app')

@section('title', 'Gestion des marques')

@section('content')
<div class="categories-container">
    <!-- En-tête de page -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <h1 class="brand-title">Gestion des marques</h1>
                <p class="brand-subtitle">Marques constructeurs, logos et filtrage intelligent pour le shop</p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2">
            <form action="{{ route('brands.auto-detect') }}" method="POST" class="d-inline" onsubmit="return confirm('Scanner les produits sans marque et les associer automatiquement selon leur nom ?');">
                @csrf
                <button type="submit" class="btn-brand-outline" title="Associer automatiquement les produits aux marques selon leurs titres">
                    <i class="fas fa-magic text-danger me-1"></i> <span class="d-none d-sm-inline">Auto-détection</span>
                </button>
            </form>
            <a href="{{ route('brands.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus"></i> <span>Nouvelle marque</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2.5 p-3">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2.5 p-3">
            <i class="fas fa-exclamation-triangle fs-5 text-danger"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Pro KPI Metric Cards Grid -->
    <div class="stats-grid-pro mb-4">
        <!-- KPI 1 : Total marques -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-red">
                    <i class="fas fa-tags"></i>
                </div>
                <span class="stat-trend-badge trend-up">
                    <i class="fas fa-layer-group"></i> Total
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Total marques</div>
                <div class="stat-value-pro">{{ $kpiStats['total'] ?? 0 }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">Constructeurs répertoriés</span>
            </div>
        </div>

        <!-- KPI 2 : Marques Actives -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span class="stat-trend-badge trend-up">
                    <i class="fas fa-signal"></i> En ligne
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Marques actives</div>
                <div class="stat-value-pro">{{ $kpiStats['active'] ?? 0 }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">
                    {{ ($kpiStats['inactive'] ?? 0) > 0 ? ($kpiStats['inactive'] . ' inactive(s)') : 'Visibles sur le shop' }}
                </span>
            </div>
        </div>

        <!-- KPI 3 : Avec Produits -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-indigo">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <span class="stat-trend-badge trend-up">
                    <i class="fas fa-box"></i> Avec stock
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Marques garnies</div>
                <div class="stat-value-pro">{{ $kpiStats['with_products'] ?? 0 }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">Possédant au moins 1 produit</span>
            </div>
        </div>

        <!-- KPI 4 : Produits sans marque -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-amber">
                    <i class="fas fa-question-circle"></i>
                </div>
                <span class="stat-trend-badge">
                    <i class="fas fa-exclamation"></i> À lier
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Produits sans marque</div>
                <div class="stat-value-pro">{{ $kpiStats['unassigned_products'] ?? 0 }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">Non associés à une marque</span>
            </div>
        </div>
    </div>

    <!-- Barre de filtres & Recherche Pro -->
    <div class="brand-filter-bar mb-4">
        <form action="{{ route('brands.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <div class="brand-search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" 
                           placeholder="Rechercher par nom, slug ou description...">
                </div>
            </div>
            
            <div class="col-6 col-md-auto">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actives uniquement</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactives uniquement</option>
                </select>
            </div>

            <div class="col-6 col-md-auto d-flex gap-2">
                <button type="submit" class="btn-brand-primary flex-grow-1 flex-md-grow-0">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('brands.index') }}" class="btn-brand-light" title="Réinitialiser les filtres">
                        <i class="fas fa-times me-1"></i> Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    @php
        $isFiltered = request()->filled('search') || request()->filled('status');
        $hasItems = $brands->isNotEmpty();
    @endphp

    @if(!$hasItems)
        <!-- Empty State Pro -->
        <div class="brand-table-card">
            <div class="empty-state-pro py-5 text-center">
                <div class="empty-state-icon mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 70px; height: 70px; font-size: 1.8rem; color: #dc3545;">
                    <i class="fas fa-tag"></i>
                </div>
                <h3 class="empty-state-title fw-bold text-dark mb-1">Aucune marque trouvée</h3>
                <p class="empty-state-desc text-muted mb-3" style="max-width: 420px; margin: 0 auto;">
                    @if($isFiltered)
                        Aucune marque ne correspond à vos filtres actuels.
                    @else
                        Vous n'avez pas encore créé de marques pour vos produits.
                    @endif
                </p>
                <div>
                    @if($isFiltered)
                        <a href="{{ route('brands.index') }}" class="btn-brand-light me-2">
                            <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                        </a>
                    @endif
                    <a href="{{ route('brands.create') }}" class="btn-brand-primary">
                        <i class="fas fa-plus me-1"></i> Ajouter une marque
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Desktop Table Presentation (Hidden on mobile) -->
        <div class="brand-table-card d-none d-md-block shadow-sm rounded-3 overflow-hidden bg-white mb-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3" style="width: 60px;">Logo</th>
                            <th class="py-3">Nom & Identifiant</th>
                            <th class="py-3 text-center" style="width: 140px;">Produits liés</th>
                            <th class="py-3 text-center" style="width: 100px;">Ordre</th>
                            <th class="py-3 text-center" style="width: 120px;">Statut</th>
                            <th class="pe-3 py-3 text-end" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($brands as $brand)
                        <tr class="brand-row hover-bg-light">
                            <!-- Logo / Icon -->
                            <td class="ps-3">
                                @if($brand->logo)
                                    <div class="brand-logo-preview rounded p-1 border bg-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-width: 36px; max-height: 36px; object-fit: contain;">
                                    </div>
                                @else
                                    <div class="brand-logo-preview rounded border bg-light d-flex align-items-center justify-content-center text-muted fw-bold" style="width: 44px; height: 44px; font-size: 0.95rem;">
                                        {{ mb_substr($brand->name, 0, 2) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Name & Slug -->
                            <td>
                                <div class="fw-bold text-dark d-flex align-items-center gap-1.5">
                                    <span>{{ $brand->name }}</span>
                                    @if($brand->is_featured)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle small py-0.5 px-1.5" title="Marque vedette">
                                            <i class="fas fa-star" style="font-size: 0.68rem;"></i> Vedette
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted small font-monospace" style="font-size: 0.78rem;">
                                    {{ $brand->slug }}
                                    @if($brand->website)
                                        <a href="{{ $brand->website }}" target="_blank" rel="noopener noreferrer" class="ms-1.5 text-decoration-none text-muted" title="{{ $brand->website }}">
                                            <i class="fas fa-external-link-alt" style="font-size: 0.7rem;"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- Products Count -->
                            <td class="text-center">
                                @if($brand->products_count > 0)
                                    <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}" target="_blank" class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 text-decoration-none" title="Voir les produits dans le shop">
                                        <i class="fas fa-box me-1"></i> {{ $brand->products_count }}
                                    </a>
                                @else
                                    <span class="badge rounded-pill bg-light text-muted border px-2.5 py-1">0</span>
                                @endif
                            </td>

                            <!-- Sort Order -->
                            <td class="text-center text-muted small font-monospace">
                                {{ $brand->sort_order }}
                            </td>

                            <!-- Status -->
                            <td class="text-center">
                                @if($brand->status === 'active')
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        <i class="fas fa-check-circle me-1"></i> Actif
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border px-2.5 py-1">
                                        <i class="fas fa-pause-circle me-1"></i> Inactif
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('brands.edit', $brand) }}" class="btn btn-outline-secondary" title="Modifier la marque">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement la marque « {{ addslashes($brand->name) }} » ? Les produits associés ne seront pas supprimés mais n\'auront plus de marque.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer la marque">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards Presentation (Visible only on phone) -->
        <div class="d-md-none mb-3">
            @foreach($brands as $brand)
            <div class="card border shadow-sm rounded-3 mb-2 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        @if($brand->logo)
                            <div class="rounded p-1 border bg-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-width: 28px; max-height: 28px; object-fit: contain;">
                            </div>
                        @else
                            <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                {{ mb_substr($brand->name, 0, 2) }}
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold text-dark">{{ $brand->name }}</div>
                            <span class="text-muted small font-monospace">{{ $brand->slug }}</span>
                        </div>
                    </div>
                    <div>
                        @if($brand->status === 'active')
                            <span class="badge bg-success-subtle text-success border border-success-subtle small px-2 py-0.5">Actif</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border small px-2 py-0.5">Inactif</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <span class="badge rounded-pill bg-light text-muted border px-2 py-1 small">
                        <i class="fas fa-box me-1"></i> {{ $brand->products_count }} {{ __('produit(s)') }}
                    </span>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('brands.edit', $brand) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                        <form action="{{ route('brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement la marque « {{ addslashes($brand->name) }} » ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($brands->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $brands->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
