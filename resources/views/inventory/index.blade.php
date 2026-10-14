@extends('layouts.app')

@section('title', 'Gestion des stocks & inventaire')

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header - Aligned with Invoices & Orders) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div>
                <h1 class="brand-title">Inventaire & Stocks</h1>
                <p class="brand-subtitle">Suivez vos niveaux de stock, anticipez les réapprovisionnements et optimisez la valeur marchande</p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('inventory.alerts') }}" class="btn btn-outline-warning shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-bell text-warning"></i>
                <span>Alertes</span>
                @if(($stats['low_stock'] + $stats['out_of_stock']) > 0)
                    <span class="badge bg-danger text-white rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $stats['low_stock'] + $stats['out_of_stock'] }}</span>
                @endif
            </a>
            <a href="{{ route('inventory.movements') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-history text-primary"></i> <span>Mouvements</span>
            </a>
            <a href="{{ route('inventory.export', request()->all()) }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-none d-md-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Stat Cards (Row of 4 - Exactly Aligned with Invoices & Orders) -->
    <div class="row g-3 mb-4">
        <!-- Total Produits Suivis -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.index') }}" class="stat-kpi-card text-decoration-none {{ !request('stock_status') && !request('search') && !request('category_id') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Articles</div>
                        <div class="stat-kpi-value text-dark">{{ number_format($stats['total_products'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">Articles suivis</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Stock Faible -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.index', ['stock_status' => 'low_stock']) }}" class="stat-kpi-card text-decoration-none {{ request('stock_status') === 'low_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Stock Faible</div>
                        <div class="stat-kpi-value text-warning">{{ number_format($stats['low_stock'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-clock text-warning me-1"></i>À réapprovisionner
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Rupture de Stock -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.index', ['stock_status' => 'out_of_stock']) }}" class="stat-kpi-card text-decoration-none {{ request('stock_status') === 'out_of_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Rupture de Stock</div>
                        <div class="stat-kpi-value text-danger">{{ number_format($stats['out_of_stock'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-bolt text-danger me-1"></i>Action immédiate
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Valeur du Stock -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('inventory.index', ['stock_status' => 'in_stock']) }}" class="stat-kpi-card text-decoration-none {{ request('stock_status') === 'in_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Valeur du Stock</div>
                        <div class="stat-kpi-value text-success" style="font-size: clamp(0.95rem, 3.5vw, 1.35rem);">{{ currency($stats['total_stock_value'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-coins text-success me-1"></i>Valeur marchande
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-vault"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('inventory.index') }}" id="inventoryFilterForm">
            <!-- Row 1 : Champ Recherche + Actions Principales (Filtrer + Reset + Compteur) -->
            <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 240px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher par nom d'article ou référence SKU..."
                           value="{{ request('search') }}">
                </div>

                @php
                    $activeFilterKeys = ['category_id', 'stock_status', 'track_inventory', 'sort_by', 'per_page'];
                    $hasActiveFilters = collect($activeFilterKeys)->some(fn($k) => request()->filled($k));
                    $activeFilterCount = collect($activeFilterKeys)->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile (d-md-none) -->
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

                <!-- Actions Desktop Principales (Filtrer + Reset + Compteur) (d-none d-md-flex) -->
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

            <!-- Filtres Avancés : Collapsible sur Mobile (d-md-none), Grid intégrée sur Desktop (d-none d-md-block) -->
            <div class="collapse d-md-block mt-2.5" id="inventoryFiltersCollapse">
                <div class="row g-2 pt-2 border-top">
                    <!-- Catégorie -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Catégorie</label>
                        <select name="category_id" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Statut du Stock -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Niveau Stock</label>
                        <select name="stock_status" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="">Tous les statuts</option>
                            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>En stock (&gt; seuil)</option>
                            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Stock faible (≤ seuil)</option>
                            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Rupture de stock (0)</option>
                        </select>
                    </div>

                    <!-- Tri par -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Trier par</label>
                        <select name="sort_by" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="name" {{ request('sort_by', 'name') === 'name' ? 'selected' : '' }}>Nom du produit</option>
                            <option value="sku" {{ request('sort_by') === 'sku' ? 'selected' : '' }}>Code SKU</option>
                            <option value="stock" {{ request('sort_by') === 'stock' ? 'selected' : '' }}>Quantité en stock</option>
                            <option value="sold_last_30_days" {{ request('sort_by') === 'sold_last_30_days' ? 'selected' : '' }}>Ventes (30 jours)</option>
                        </select>
                    </div>

                    <!-- Par page -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Affichage</label>
                        <select name="per_page" class="form-select form-select-sm" onchange="document.getElementById('inventoryFilterForm').submit()">
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 articles / page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 articles / page</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 articles / page</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action pour le Drawer Mobile (d-md-none) -->
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

    <!-- =========================================================================
         AFFICHAGE MOBILE (d-block d-md-none) : CARTES TACTILES TOUCH-FRIENDLY
         ========================================================================= -->
    <div class="d-block d-md-none">
        @forelse($products as $product)
            @include('inventory.partials.mobile-inventory-card', ['product' => $product])
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

    <!-- =========================================================================
         AFFICHAGE DESKTOP (d-none d-md-block) : TABLEAU BRAND EXECUTIVE - 0 SCROLL HORIZONTAL
         ========================================================================= -->
    <div class="brand-table-card inventory-table-card d-none d-md-block shadow-sm border" style="overflow: hidden !important;">
        <div style="overflow-x: hidden !important; width: 100% !important;">
            <table class="brand-table align-middle" style="table-layout: fixed !important; width: 100% !important; margin: 0 !important;">
                <thead>
                    <tr>
                        <th style="width: 27%; padding-left: 1.25rem;">Article</th>
                        <th style="width: 17%;">Stock Actuel</th>
                        <th class="text-center" style="width: 10%;">Ventes (30j)</th>
                        <th class="text-center" style="width: 11%;">Autonomie</th>
                        <th class="text-center" style="width: 10%;">Seuil Réapp.</th>
                        <th class="text-end" style="width: 12%;">Valeur Stock</th>
                        <th class="text-end" style="width: 13%; padding-right: 1.25rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    @php
                        $stock = $product->stock ?? 0;
                        $threshold = $product->low_stock_threshold ?? 10;
                        $isTracked = (bool)$product->track_inventory;

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

                        // Forecast
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

                        // Reorder point
                        $reorderPt = '—';
                        $reorderClass = 'text-muted';
                        if ($isTracked && ($product->sold_last_30_days ?? 0) > 0) {
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
                                </div>
                            </div>
                        </td>

                        <!-- Niveau de stock -->
                        <td>
                            @if($isTracked)
                                <div class="d-flex flex-column gap-1" style="max-width: 140px;">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="fw-bold text-dark fs-6">{{ $stock }}</span>
                                        <span class="brand-badge {{ $badgeClass }} py-0.5 px-1.5" style="font-size: 0.68rem;">
                                            {{ $badgeText }}
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 5px; border-radius: 999px; background: #e2e8f0; width: 100%;">
                                        <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ $percent }}%; border-radius: 999px;"></div>
                                    </div>
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

                        <!-- Seuil réapp. -->
                        <td class="text-center">
                            <span class="{{ $reorderClass }}" style="font-size: 0.85rem;">{{ $reorderPt }}</span>
                        </td>

                        <!-- Valeur marchande -->
                        <td class="text-end">
                            @if($isTracked && $product->cost_price)
                                <div class="fw-bold text-dark" style="font-size: 0.84rem;">{{ currency($stock * $product->cost_price) }}</div>
                                <div class="text-muted small" style="font-size: 0.68rem;">{{ currency($product->cost_price) }} / u.</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td style="padding-right: 1.25rem;" class="text-end">
                            <div class="action-buttons-group justify-content-end">
                                @if($isTracked)
                                <button type="button" 
                                        class="action-btn action-btn-primary" 
                                        onclick="openAdjustModal('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $stock }})"
                                        title="Ajuster le stock">
                                    <i class="fas fa-sliders-h"></i>
                                </button>
                                @endif
                                <a href="{{ route('inventory.movements', ['product_id' => $product->id]) }}" 
                                   class="action-btn" 
                                   title="Historique des mouvements">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="action-btn" 
                                   title="Modifier l'article">
                                    <i class="fas fa-pen"></i>
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

    <!-- =========================================================================
         MODAL AJUSTEMENT RAPIDE DE STOCK (EXECUTIVE TOUCH & DESKTOP MODAL)
         ========================================================================= -->
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
                                <div class="text-muted small" style="font-size: 0.72rem;">Mise à jour immédiate du niveau d'inventaire</div>
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
                                    Niveau actuel : <span class="fw-bold text-primary fs-6" id="modalCurrentStock">0</span> unité(s)
                                </div>
                            </div>
                            <div class="text-end ps-2">
                                <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Nouveau niveau</div>
                                <div class="fw-extrabold fs-5 text-dark" id="modalProjectedStock">0</div>
                            </div>
                        </div>

                        <!-- Operation Type Selector (Visual Pills) -->
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
                            <div class="form-text small" id="modalQtyHelp" style="font-size: 0.72rem;">Nombre d'unités ajoutées à l'inventaire actuel.</div>
                        </div>

                        <!-- Reason with Quick Chips -->
                        <div class="mb-2">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">Motif de l'ajustement</label>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setReason('Arrivage fournisseur')">Arrivage</button>
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setReason('Retour client')">Retour client</button>
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setReason('Inventaire physique périodique')">Inventaire audit</button>
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
@endsection

@push('scripts')
<script>
    let currentProductStock = 0;

    function openAdjustModal(productId, productName, currentStock) {
        currentProductStock = parseInt(currentStock, 10) || 0;
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalCurrentStock').textContent = currentProductStock;
        
        const form = document.getElementById('adjustStockForm');
        form.action = `/inventory/${productId}/adjust`;
        
        selectOperation('in');
        document.getElementById('modalQuantityInput').value = 1;
        document.getElementById('modalReasonInput').value = '';
        recalculateProjected();
        
        new bootstrap.Modal(document.getElementById('adjustStockModal')).show();
    }

    function selectOperation(op) {
        document.getElementById('modalAdjustmentType').value = op;
        
        // Toggle pill buttons
        ['in', 'out', 'adjustment'].forEach(type => {
            const pill = document.getElementById('pill-' + type);
            if (pill) {
                if (type === op) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            }
        });

        const qtyLabel = document.getElementById('modalQtyLabel');
        const qtyHelp = document.getElementById('modalQtyHelp');
        const qtyInput = document.getElementById('modalQuantityInput');

        if (op === 'in') {
            qtyLabel.textContent = "Quantité à ajouter";
            qtyHelp.textContent = "Nombre d'unités ajoutées au stock actuel.";
            qtyInput.min = 1;
            if (parseInt(qtyInput.value, 10) < 1) qtyInput.value = 1;
        } else if (op === 'out') {
            qtyLabel.textContent = "Quantité à retirer";
            qtyHelp.textContent = "Nombre d'unités déduites du stock actuel.";
            qtyInput.min = 1;
            if (parseInt(qtyInput.value, 10) < 1) qtyInput.value = 1;
        } else {
            qtyLabel.textContent = "Nouveau stock réel (exact)";
            qtyHelp.textContent = "La valeur exacte dénombrée lors de l'inventaire physique.";
            qtyInput.min = 0;
            qtyInput.value = currentProductStock;
        }

        recalculateProjected();
    }

    function stepQuantity(delta) {
        const input = document.getElementById('modalQuantityInput');
        let current = parseInt(input.value, 10) || 0;
        const minVal = parseInt(input.min, 10) || 0;
        let next = current + delta;
        if (next < minVal) next = minVal;
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
</script>
@endpush
