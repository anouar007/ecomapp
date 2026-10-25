@extends('layouts.app')

@section('title', 'Mouvements de stock - Historique & Audit')

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h1 class="brand-title">Mouvements de Stock</h1>
                <p class="brand-subtitle">Journal d'audit exhaustif et traçabilité de toutes les variations d'inventaire</p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-arrow-left"></i> <span>Inventaire</span>
            </a>
            <a href="{{ route('inventory.alerts') }}" class="btn btn-outline-warning shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-bell text-warning"></i> <span>Alertes</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 shadow-sm mb-3">
            <i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('inventory.movements') }}" id="movementFilterForm">
            <!-- Row 1 : Champ Recherche + Actions Principales (Filtrer + Reset + Compteur) -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 240px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher par article, référence SKU ou motif..."
                           value="{{ request('search', request('product')) }}">
                </div>

                @php
                    $activeFilterKeys = ['type', 'product_id', 'start_date', 'end_date', 'date_from', 'date_to'];
                    $hasActiveFilters = collect($activeFilterKeys)->some(fn($k) => request()->filled($k));
                    $activeFilterCount = collect($activeFilterKeys)->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile (d-md-none) -->
                <button type="button" 
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="collapse" 
                        data-bs-target="#movementFiltersCollapse" 
                        aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filtres</span>
                    @if($activeFilterCount > 0)
                        <span class="badge bg-white text-dark rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $activeFilterCount }}</span>
                    @endif
                </button>

                <!-- Actions Desktop Principales (Filtrer + Reset + Compteur) (d-none d-md-flex) -->
                <div class="d-none d-md-flex align-items-center gap-2">
                    <button type="submit" class="btn-brand-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1.5" title="Appliquer les filtres">
                        <i class="fas fa-filter"></i> <span>Filtrer</span>
                    </button>
                    @if(request('search') || request('product') || $hasActiveFilters)
                        <a href="{{ route('inventory.movements') }}" class="btn-brand-light btn-sm px-2.5 py-2 text-decoration-none" title="Réinitialiser tous les filtres">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                    <span class="badge bg-light text-secondary border px-3 py-2 fw-semibold" style="font-size: 0.8rem;">
                        <i class="fas fa-history text-primary me-1.5"></i>{{ $movements->total() }} mouvement(s)
                    </span>
                </div>
            </div>

            <!-- Filtres Avancés : Collapsible sur Mobile (d-md-none), Grid intégrée sur Desktop (d-none d-md-block) -->
            <div class="collapse d-md-block mt-2.5" id="movementFiltersCollapse">
                <div class="row g-2 pt-2 border-top">
                    <!-- Type de Mouvement -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Type de mouvement</label>
                        <select name="type" class="form-select form-select-sm" onchange="document.getElementById('movementFilterForm').submit()">
                            <option value="">Tous les types</option>
                            <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Entrée de stock (+)</option>
                            <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Sortie de stock (-)</option>
                            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Correction / Ajustement</option>
                            <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfert interne</option>
                            <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Retour client</option>
                        </select>
                    </div>

                    <!-- Article Spécifique -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Article spécifique</label>
                        <select name="product_id" class="form-select form-select-sm" onchange="document.getElementById('movementFilterForm').submit()">
                            <option value="">Tous les articles</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                    {{ Str::limit($prod->name, 28) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date de début -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Du</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" 
                               value="{{ request('start_date', request('date_from')) }}"
                               onchange="document.getElementById('movementFilterForm').submit()">
                    </div>

                    <!-- Date de fin -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Au</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" 
                               value="{{ request('end_date', request('date_to')) }}"
                               onchange="document.getElementById('movementFilterForm').submit()">
                    </div>
                </div>

                <!-- Actions pour Drawer Mobile (d-md-none) -->
                <div class="d-flex d-md-none align-items-center gap-2 mt-3 pt-2 border-top">
                    <button type="submit" class="btn btn-brand-primary btn-sm flex-grow-1 py-2">
                        <i class="fas fa-check me-1"></i> Appliquer
                    </button>
                    @if(request('search') || request('product') || $hasActiveFilters)
                        <a href="{{ route('inventory.movements') }}" class="btn btn-brand-light btn-sm px-3 py-2 text-decoration-none">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- =========================================================================
         AFFICHAGE MOBILE (d-block d-md-none) : CARTES DE MOUVEMENTS
         ========================================================================= -->
    <div class="d-block d-md-none">
        @forelse($movements as $movement)
            @include('inventory.partials.mobile-movement-card', ['movement' => $movement])
        @empty
            <div class="text-center py-5 bg-white rounded-3 border">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                    <i class="fas fa-history"></i>
                </div>
                <h6 class="fw-bold text-dark">Aucun mouvement trouvé</h6>
                <p class="text-muted small mb-3">Modifiez vos critères de recherche ou réinitialisez les filtres.</p>
                <a href="{{ route('inventory.movements') }}" class="btn btn-brand-light btn-sm">
                    <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                </a>
            </div>
        @endforelse

        @if($movements->hasPages())
            <div class="mt-3 mb-4">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

    <!-- =========================================================================
         AFFICHAGE DESKTOP (d-none d-md-block) : TABLEAU BRAND HISTORIQUE SANS OVERFLOW
         ========================================================================= -->
    <div class="brand-table-card inventory-table-card d-none d-md-block">
        <div class="table-responsive">
            <table class="brand-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 14%;">Date & Heure</th>
                        <th style="width: 26%;">Article</th>
                        <th style="width: 13%;">Type</th>
                        <th style="width: 10%;" class="text-center">Variation</th>
                        <th style="width: 11%;" class="text-center">Avant → Après</th>
                        <th style="width: 14%;">Motif & Ref</th>
                        <th style="width: 12%;">Opérateur</th>
                    </tr>
                </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        @php
                            $typeColor = match($movement->type) {
                                'in' => 'success',
                                'out' => 'danger',
                                'adjustment' => 'warning',
                                'transfer' => 'info',
                                'return' => 'primary',
                                default => 'secondary'
                            };

                            $typeLabel = match($movement->type) {
                                'in' => 'Entrée',
                                'out' => 'Sortie',
                                'adjustment' => 'Ajustement',
                                'transfer' => 'Transfert',
                                'return' => 'Retour',
                                default => ucfirst($movement->type)
                            };

                            $typeIcon = match($movement->type) {
                                'in' => 'fa-arrow-down',
                                'out' => 'fa-arrow-up',
                                'adjustment' => 'fa-sliders-h',
                                'transfer' => 'fa-exchange-alt',
                                'return' => 'fa-undo',
                                default => 'fa-circle'
                            };

                            $qtyPrefix = ($movement->quantity > 0 && in_array($movement->type, ['in', 'return'])) ? '+' : (($movement->type === 'out') ? '-' : '');
                            $qtyColor = ($movement->type === 'in' || $movement->type === 'return') ? '#10b981' : (($movement->type === 'out') ? '#ef4444' : '#2563eb');
                        @endphp
                        <tr>
                            <!-- Date & Heure -->
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.82rem;">{{ $movement->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small" style="font-size: 0.72rem;">{{ $movement->created_at->format('H:i:s') }}</div>
                            </td>

                            <!-- Article -->
                            <td>
                                @if($movement->product)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="mobile-inv-avatar flex-shrink-0" style="width: 38px; height: 38px;">
                                        <img src="{{ $movement->product->thumbnail }}" 
                                             alt="{{ $movement->product->name }}" 
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <a href="{{ route('products.edit', $movement->product) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.85rem;" title="{{ $movement->product->name }}">
                                            {{ $movement->product->name }}
                                        </a>
                                        <span class="badge bg-light text-secondary border font-monospace mt-0.5" style="font-size: 0.65rem;">
                                            {{ $movement->product->sku ?? 'SANS-SKU' }}
                                        </span>
                                        @if($movement->variant)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-0.5 ms-1" style="font-size: 0.65rem;">
                                                <i class="fas fa-layer-group me-1"></i>{{ $movement->variant->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <span class="text-muted small fst-italic">Produit supprimé</span>
                                @endif
                            </td>

                            <!-- Type -->
                            <td>
                                <span class="badge bg-{{ $typeColor }}-subtle text-{{ $typeColor }} border border-{{ $typeColor }}-subtle d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size: 0.72rem;">
                                    <i class="fas {{ $typeIcon }}"></i> {{ $typeLabel }}
                                </span>
                            </td>

                            <!-- Variation -->
                            <td>
                                <span class="fw-bold fs-6" style="color: {{ $qtyColor }};">
                                    {{ $qtyPrefix }}{{ $movement->quantity }}
                                </span>
                            </td>

                            <!-- Avant -> Après -->
                            <td>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="badge bg-secondary-subtle text-secondary px-1.5 py-0.5" style="font-size: 0.72rem;">{{ $movement->stock_before ?? 0 }}</span>
                                    <i class="fas fa-arrow-right text-muted opacity-50" style="font-size: 0.6rem;"></i>
                                    <span class="badge bg-primary text-white px-1.5 py-0.5" style="font-size: 0.72rem;">{{ $movement->stock_after ?? 0 }}</span>
                                </div>
                            </td>

                            <!-- Motif & Référence -->
                            <td>
                                <div class="text-dark small text-truncate" style="max-width: 170px;" title="{{ $movement->reason ?? 'Aucun motif' }}">
                                    {{ $movement->reason ?? '—' }}
                                </div>
                                @if($movement->reference_type && $movement->reference_id)
                                    <div class="text-muted small font-monospace" style="font-size: 0.68rem;">
                                        #{{ $movement->reference_id }}
                                    </div>
                                @endif
                            </td>

                            <!-- Opérateur -->
                            <td>
                                <div class="fw-semibold text-dark small" style="font-size: 0.8rem;">
                                    {{ $movement->creator->name ?? 'Système' }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Aucun mouvement enregistré</h6>
                                    <p class="text-muted small mb-0">Tous les mouvements de stock apparaîtront ici.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages())
                <div class="px-4 py-3 border-top bg-light">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
@endsection
