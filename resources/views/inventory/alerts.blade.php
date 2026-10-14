@extends('layouts.app')

@section('title', 'Centre d\'alertes de stock')

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h1 class="brand-title">Centre d'Alertes Stock</h1>
                <p class="brand-subtitle">Surveillance proactive des seuils critiques et réapprovisionnements prioritaires</p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-arrow-left"></i> <span>Inventaire</span>
            </a>
            @if(($stats['unacknowledged'] ?? 0) > 0)
                <form action="{{ route('inventory.bulk-acknowledge') }}" method="POST" class="d-inline m-0 flex-grow-1 flex-md-grow-0">
                    @csrf
                    @foreach($alerts->whereNull('acknowledged_at')->pluck('id') as $alertId)
                        <input type="hidden" name="alert_ids[]" value="{{ $alertId }}">
                    @endforeach
                    <button type="submit" class="btn btn-brand-primary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 w-100 fw-semibold" style="font-size: 0.84rem;">
                        <i class="fas fa-check-double"></i> <span>Tout acquitter ({{ $stats['unacknowledged'] }})</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 shadow-sm mb-3">
            <i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- KPI Metric Stat Cards (Row of 4) -->
    <div class="row g-3 mb-4">
        <!-- Non Traitées -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.alerts', ['status' => 'unacknowledged']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'unacknowledged' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Non traitées</div>
                        <div class="stat-kpi-value text-warning">{{ number_format($stats['unacknowledged'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-clock text-warning me-1"></i>En attente de revue
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Rupture de Stock -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.alerts', ['alert_type' => 'out_of_stock']) }}" class="stat-kpi-card text-decoration-none {{ request('alert_type') === 'out_of_stock' || request('type') === 'out_of_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Rupture de Stock</div>
                        <div class="stat-kpi-value text-danger">{{ number_format($stats['out_of_stock'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-bolt text-danger me-1"></i>Stock nul (0)
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Stock Faible -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.alerts', ['alert_type' => 'low_stock']) }}" class="stat-kpi-card text-decoration-none {{ request('alert_type') === 'low_stock' || request('type') === 'low_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Stock Faible</div>
                        <div class="stat-kpi-value text-orange" style="color: #ea580c;">{{ number_format($stats['low_stock'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-triangle-exclamation text-warning me-1"></i>Sous le seuil
                        </div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #fff7ed; color: #ea580c;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Traitées / Lues -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.alerts', ['status' => 'acknowledged']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'acknowledged' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Traitées / Lues</div>
                        <div class="stat-kpi-value text-success">{{ number_format($stats['acknowledged'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i>Historique acquitté
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('inventory.alerts') }}" id="alertFilterForm">
            <!-- Row 1 : Champ Recherche + Actions Principales (Filtrer + Reset + Compteur) -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 240px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher par article ou référence SKU..."
                           value="{{ request('search', request('product')) }}">
                </div>

                @php
                    $activeFilterKeys = ['status', 'alert_type', 'type'];
                    $hasActiveFilters = collect($activeFilterKeys)->some(fn($k) => request()->filled($k));
                    $activeFilterCount = collect($activeFilterKeys)->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile (d-md-none) -->
                <button type="button" 
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="collapse" 
                        data-bs-target="#alertFiltersCollapse" 
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
                        <a href="{{ route('inventory.alerts') }}" class="btn-brand-light btn-sm px-2.5 py-2 text-decoration-none" title="Réinitialiser tous les filtres">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                    <span class="badge bg-light text-secondary border px-3 py-2 fw-semibold" style="font-size: 0.8rem;">
                        <i class="fas fa-bell text-primary me-1.5"></i>{{ $alerts->total() }} alerte(s)
                    </span>
                </div>
            </div>

            <!-- Filtres Avancés : Collapsible sur Mobile (d-md-none), Grid intégrée sur Desktop (d-none d-md-block) -->
            <div class="collapse d-md-block mt-2.5" id="alertFiltersCollapse">
                <div class="row g-2 pt-2 border-top">
                    <!-- Statut -->
                    <div class="col-6 col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Statut de traitement</label>
                        <select name="status" class="form-select form-select-sm" onchange="document.getElementById('alertFilterForm').submit()">
                            <option value="">Toutes les alertes</option>
                            <option value="unacknowledged" {{ request('status') === 'unacknowledged' ? 'selected' : '' }}>En attente / Non traitées</option>
                            <option value="acknowledged" {{ request('status') === 'acknowledged' ? 'selected' : '' }}>Traitées / Acquittées</option>
                        </select>
                    </div>

                    <!-- Type d'Alerte -->
                    <div class="col-6 col-md-6">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Type d'alerte</label>
                        <select name="alert_type" class="form-select form-select-sm" onchange="document.getElementById('alertFilterForm').submit()">
                            <option value="">Tous les types</option>
                            <option value="out_of_stock" {{ (request('alert_type') ?? request('type')) === 'out_of_stock' ? 'selected' : '' }}>Rupture de stock</option>
                            <option value="low_stock" {{ (request('alert_type') ?? request('type')) === 'low_stock' ? 'selected' : '' }}>Stock faible</option>
                        </select>
                    </div>
                </div>

                <!-- Actions pour Drawer Mobile (d-md-none) -->
                <div class="d-flex d-md-none align-items-center gap-2 mt-3 pt-2 border-top">
                    <button type="submit" class="btn btn-brand-primary btn-sm flex-grow-1 py-2">
                        <i class="fas fa-check me-1"></i> Appliquer
                    </button>
                    @if(request('search') || request('product') || $hasActiveFilters)
                        <a href="{{ route('inventory.alerts') }}" class="btn btn-brand-light btn-sm px-3 py-2 text-decoration-none">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- =========================================================================
         AFFICHAGE MOBILE (d-block d-md-none) : CARTES D'ALERTES
         ========================================================================= -->
    <div class="d-block d-md-none">
        @forelse($alerts as $alert)
            @include('inventory.partials.mobile-alert-card', ['alert' => $alert])
        @empty
            <div class="text-center py-5 bg-white rounded-3 border">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                    <i class="fas fa-circle-check text-success"></i>
                </div>
                <h6 class="fw-bold text-dark">Aucune alerte active</h6>
                <p class="text-muted small mb-0">Tous vos niveaux de stock sont optimaux ou les alertes ont été traitées.</p>
            </div>
        @endforelse

        @if($alerts->hasPages())
            <div class="mt-3 mb-4">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>

    <!-- =========================================================================
         AFFICHAGE DESKTOP (d-none d-md-block) : TABLEAU BRAND D'ALERTES SANS OVERFLOW
         ========================================================================= -->
    <div class="brand-table-card inventory-table-card d-none d-md-block">
        <div class="table-responsive">
            <table class="brand-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 30%;">Article</th>
                        <th style="width: 15%;">Gravité</th>
                        <th style="width: 12%;" class="text-center">Stock Déclenché</th>
                        <th style="width: 11%;" class="text-center">Seuil d'alerte</th>
                        <th style="width: 13%;">Déclenchée le</th>
                        <th style="width: 10%;" class="text-center">Statut</th>
                        <th class="text-end" style="width: 9%; padding-right: 1.25rem;">Actions</th>
                    </tr>
                </thead>
                    <tbody>
                        @forelse($alerts as $alert)
                        @php
                            $isOutOfStock = ($alert->alert_type === 'out_of_stock');
                            $isAcknowledged = (bool)$alert->acknowledged_at;
                        @endphp
                        <tr>
                            <!-- Article -->
                            <td>
                                @if($alert->product)
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="mobile-inv-avatar flex-shrink-0" style="width: 40px; height: 40px;">
                                        <img src="{{ $alert->product->thumbnail }}" 
                                             alt="{{ $alert->product->name }}" 
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <a href="{{ route('products.edit', $alert->product) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.88rem;" title="{{ $alert->product->name }}">
                                            {{ $alert->product->name }}
                                        </a>
                                        <span class="badge bg-light text-secondary border font-monospace mt-0.5" style="font-size: 0.65rem;">
                                            {{ $alert->product->sku ?? 'SANS-SKU' }}
                                        </span>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted small fst-italic">Produit supprimé</span>
                                @endif
                            </td>

                            <!-- Gravité -->
                            <td>
                                @if($isOutOfStock)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1.5 px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">
                                        <i class="fas fa-times-circle"></i> Rupture de stock
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle d-inline-flex align-items-center gap-1.5 px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">
                                        <i class="fas fa-exclamation-triangle"></i> Stock faible
                                    </span>
                                @endif
                            </td>

                            <!-- Stock Déclenché -->
                            <td>
                                <span class="badge {{ $alert->current_stock <= 0 ? 'bg-danger' : 'bg-warning text-dark' }} px-2 py-1 font-monospace" style="font-size: 0.78rem;">
                                    {{ $alert->current_stock }} u.
                                </span>
                            </td>

                            <!-- Seuil -->
                            <td>
                                <span class="fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    {{ $alert->threshold_value }} u.
                                </span>
                            </td>

                            <!-- Déclenchée le -->
                            <td>
                                <div class="text-dark small" style="font-size: 0.8rem;">{{ $alert->triggered_at->format('d/m/Y H:i') }}</div>
                                <div class="text-muted small" style="font-size: 0.7rem;">{{ $alert->triggered_at->diffForHumans() }}</div>
                            </td>

                            <!-- Statut -->
                            <td>
                                @if($isAcknowledged)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-check me-1"></i>Traitée
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-clock me-1"></i>En attente
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-end">
                                <div class="action-buttons-group justify-content-end">
                                    @if(!$isAcknowledged)
                                        <form action="{{ route('inventory.acknowledge-alert', $alert) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="action-btn text-success" title="Marquer comme traitée">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($alert->product)
                                        <a href="{{ route('inventory.adjust', $alert->product) }}" 
                                           class="action-btn action-btn-primary" 
                                           title="Réapprovisionner">
                                            <i class="fas fa-sliders-h"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                                        <i class="fas fa-circle-check text-success"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Aucune alerte enregistrée</h6>
                                    <p class="text-muted small mb-0">Tous vos niveaux de stock sont optimaux.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($alerts->hasPages())
                <div class="px-4 py-3 border-top bg-light">
                    {{ $alerts->links() }}
                </div>
            @endif
        </div>
@endsection
