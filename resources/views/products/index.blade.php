@extends('layouts.app')

@section('title', 'Gestion des produits')

@section('content')
    <!-- En-tête de page -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                Gestion des produits
            </h1>
            <p class="brand-subtitle">Gérez votre catalogue de produits, les tarifs, la qualité 4K et la disponibilité en stock</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('products.image-quality') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2" style="border-color: #8b5cf6; color: #6d28d9; background: #faf5ff; font-weight: 600; text-decoration: none;">
                <i class="fas fa-wand-magic-sparkles text-warning"></i> Studio 4K Hub
                @if(!empty($qualityStats['needs_upgrade']) && $qualityStats['needs_upgrade'] > 0)
                    <span class="badge rounded-pill bg-warning text-dark px-2" style="font-size: 0.72rem;">{{ $qualityStats['needs_upgrade'] }} à améliorer</span>
                @endif
            </a>
            <a href="{{ route('products.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus me-2"></i> Ajouter un produit
            </a>
        </div>
    </div>

    <!-- KPI Metric Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('products.index') }}" class="stat-kpi-card text-decoration-none {{ !request('stock_status') && !request('quality') && !request('category') && !request('search') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Produits</div>
                        <div class="stat-kpi-value text-dark">{{ number_format($stats['total'] ?? $products->total(), 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><span class="badge bg-light text-secondary border">{{ $stats['active'] ?? 0 }} actifs</span></div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('products.index', ['stock_status' => 'in_stock']) }}" class="stat-kpi-card text-decoration-none {{ request('stock_status') === 'in_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">En Stock</div>
                        <div class="stat-kpi-value text-success">{{ number_format($stats['in_stock'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-check-circle text-success me-1"></i>Disponibles</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('products.index', ['stock_status' => 'low_stock']) }}" class="stat-kpi-card text-decoration-none {{ request('stock_status') === 'low_stock' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Stock Faible / Alerte</div>
                        <div class="stat-kpi-value {{ (($stats['low_stock'] ?? 0) + ($stats['out_of_stock'] ?? 0)) > 0 ? 'text-warning' : 'text-muted' }}">
                            {{ number_format(($stats['low_stock'] ?? 0) + ($stats['out_of_stock'] ?? 0), 0, ',', ' ') }}
                        </div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-exclamation-triangle text-warning me-1"></i>{{ $stats['out_of_stock'] ?? 0 }} en rupture</div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-boxes-packing"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('products.index', ['quality' => '4k']) }}" class="stat-kpi-card text-decoration-none {{ request('quality') === '4k' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Images Studio 4K</div>
                        <div class="stat-kpi-value" style="color: #7c3aed;">{{ number_format($stats['four_k'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-wand-magic-sparkles text-warning me-1"></i>{{ $stats['needs_upgrade'] ?? 0 }} à optimiser</div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #f5f3ff; color: #7c3aed;">
                        <i class="fas fa-wand-magic-sparkles"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre de filtres -->
    <div class="brand-filter-bar mb-4">
        <form method="GET" action="{{ route('products.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <div class="brand-search-wrapper flex-grow-1" style="min-width: 200px;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       placeholder="Rechercher par nom, SKU..."
                       value="{{ request('search') }}">
            </div>
            
            <select name="category" class="form-select w-auto">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="stock_status" class="form-select w-auto">
                <option value="">Tous les stocks</option>
                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>📦 En stock (&gt; 5)</option>
                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>⚠️ Stock faible (≤ 5)</option>
                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>🚫 Rupture (0)</option>
            </select>

            <select name="quality" class="form-select w-auto">
                <option value="">Toutes les résolutions</option>
                <option value="needs_upgrade" {{ request('quality') == 'needs_upgrade' ? 'selected' : '' }}>
                    ⚠️ À améliorer (&lt; 1200px) ({{ $qualityStats['needs_upgrade'] ?? 0 }})
                </option>
                <option value="4k" {{ request('quality') == '4k' ? 'selected' : '' }}>
                    ✨ 4K Ultra-HD ({{ $qualityStats['four_k'] ?? 0 }})
                </option>
                <option value="fhd" {{ request('quality') == 'fhd' ? 'selected' : '' }}>
                    📷 Full HD ({{ $qualityStats['fhd'] ?? 0 }})
                </option>
                <option value="missing" {{ request('quality') == 'missing' ? 'selected' : '' }}>
                    ❌ Sans image / Placeholder
                </option>
            </select>

            <select name="status" class="form-select w-auto">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
            </select>

            <button type="submit" class="btn-brand-primary" title="Appliquer les filtres">
                <i class="fas fa-filter me-1"></i> Filtrer
            </button>
            <a href="{{ route('products.index') }}" class="btn-brand-light" title="Réinitialiser les filtres">
                <i class="fas fa-redo"></i>
            </a>
            
            @if(request('search') || request('category') || request('quality') || request('stock_status') || request('status'))
            <span class="badge bg-light text-secondary border px-2.5 py-2">
                {{ $products->total() }} résultat(s)
            </span>
            @endif

            <div class="ms-auto d-flex gap-2">
                <button type="button" class="btn-brand-outline" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-upload me-1.5" style="color: var(--primary-color)"></i>Importer Excel
                </button>
                <a href="{{ route('export.products') }}" class="btn-brand-outline">
                    <i class="fas fa-file-csv me-1.5" style="color: var(--success-color)"></i>Exporter CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold" id="importModalLabel">
                        <i class="fas fa-file-upload me-2 text-primary"></i>Importer des produits depuis Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Sélectionner le fichier Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Formats pris en charge : .xlsx, .xls, .csv (max 10 Mo)</div>
                        </div>
                        
                        <div class="alert alert-info border-0 rounded-3 mb-0">
                            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Colonnes requises</h6>
                            <p class="mb-2 small">Votre fichier doit comporter ces en-têtes de colonnes :</p>
                            <code class="d-block bg-white p-2 rounded small">name, sku, description, price, cost_price, stock, min_stock, category, status</code>
                            <p class="mb-0 mt-2 small">
                                <a href="{{ route('products.template') }}" class="fw-bold">
                                    <i class="fas fa-download me-1"></i>Télécharger le modèle type
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn-brand-primary">
                            <i class="fas fa-upload me-2"></i>Importer les produits
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Barre d'actions groupées -->
    <div id="bulkActionsBar" class="bulk-actions-bar" style="display: none;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="selected-count fw-bold">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="selectedCount">0</span> produit(s) sélectionné(s)
                </span>
                <button type="button" class="btn btn-sm btn-light" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 140px;" id="stockAmountGroup">
                    <span class="input-group-text">Qté</span>
                    <input type="number" id="stockAmount" class="form-control" value="10" min="1" max="9999">
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm" onclick="executeBulkAction('increase_stock')" title="Ajouter du stock">
                        <i class="fas fa-plus me-1"></i>Ajouter stock
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="executeBulkAction('decrease_stock')" title="Retirer du stock">
                        <i class="fas fa-minus me-1"></i>Retirer stock
                    </button>
                </div>
                <button type="button" class="btn btn-info btn-sm text-white" onclick="executeBulkAction('duplicate')" title="Dupliquer">
                    <i class="fas fa-copy me-1"></i>Dupliquer
                </button>
                <button type="button" class="btn btn-sm text-white" onclick="bulkUpgradeTo4k()" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none;" title="Mettre à niveau la sélection vers la qualité Studio 4K">
                    <i class="fas fa-wand-magic-sparkles me-1 text-warning"></i>Mettre à niveau 4K
                </button>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="executeBulkAction('activate')" title="Activer">
                        <i class="fas fa-check me-1"></i>Activer
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="executeBulkAction('deactivate')" title="Désactiver">
                        <i class="fas fa-ban me-1"></i>Désactiver
                    </button>
                </div>
                <button type="button" class="btn btn-danger btn-sm" onclick="executeBulkAction('delete')" title="Supprimer">
                    <i class="fas fa-trash me-1"></i>Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="brand-table-card">
        <div class="products-table-wrapper">
            <table class="brand-table brand-table-products">
                <thead>
                    <tr>
                        <th class="col-check">
                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll(this)" title="Tout sélectionner">
                        </th>
                        <th class="col-product">Produit</th>
                        <th class="col-sku">SKU</th>
                        <th class="col-category">Catégorie</th>
                        <th class="col-price">Prix</th>
                        <th class="col-stock">Stock</th>
                        <th class="col-status">Statut</th>
                        <th class="col-actions text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr data-product-id="{{ $product->id }}" id="product-row-{{ $product->id }}">
                        <td class="col-check">
                            <input type="checkbox" class="form-check-input product-checkbox" 
                                   value="{{ $product->id }}" 
                                   onchange="updateSelection()">
                        </td>
                        <td class="col-product">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="position-relative flex-shrink-0">
                                    <div class="brand-avatar prod-avatar" id="prod-avatar-wrap-{{ $product->id }}">
                                        <img id="prod-img-{{ $product->id }}" src="{{ $product->thumbnail }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    </div>
                                    <button type="button" 
                                            class="prod-avatar-badge" 
                                            onclick="open4kFinderModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->thumbnail }}', '{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}')"
                                            title="Trouver la meilleure image 4K"
                                            data-bs-toggle="tooltip">
                                        <i class="fas fa-wand-magic-sparkles"></i>
                                    </button>
                                </div>
                                <div class="prod-info-wrap">
                                    <div class="prod-name" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                        <span id="prod-badge-{{ $product->id }}">{!! $product->image_quality_badge !!}</span>
                                        @if($product->description)
                                            <span class="prod-desc-text" title="{{ strip_tags($product->description) }}">
                                                {{ Str::limit(strip_tags($product->description), 35) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="col-sku">
                            <span class="badge bg-light text-secondary font-monospace prod-sku-badge" title="SKU: {{ $product->sku }}">
                                {{ $product->sku }}
                            </span>
                        </td>
                        <td class="col-category">
                            @if($product->category_name)
                                <span class="badge-category" title="{{ $product->category_name }}">
                                    <i class="fas fa-folder me-1 opacity-60"></i>{{ Str::limit($product->category_name, 20) }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="col-price">
                            <div class="fw-bold text-dark">{{ currency($product->price) }}</div>
                            @if($product->cost_price)
                                <div class="text-muted small prod-cost-text" title="Prix de revient">Coût: {{ currency($product->cost_price) }}</div>
                            @endif
                        </td>
                        <td class="col-stock">
                            @php
                                $stock = $product->stock;
                                $min = $product->min_stock ?? 5;
                                if ($stock <= 0) {
                                    $badgeType = 'out';
                                    $dotColor = 'red';
                                    $badgeText = 'Rupture (0)';
                                } elseif ($stock <= $min) {
                                    $badgeType = 'low';
                                    $dotColor = 'amber';
                                    $badgeText = 'Faible: ' . $stock;
                                } else {
                                    $badgeType = 'in';
                                    $dotColor = 'green';
                                    $badgeText = 'En stock: ' . $stock;
                                }
                            @endphp
                            <span class="badge-stock badge-stock-{{ $badgeType }}">
                                <span class="status-dot dot-{{ $dotColor }}"></span>{{ $badgeText }}
                            </span>
                        </td>
                        <td class="col-status">
                            @php
                                $st = strtolower($product->status);
                                $isActive = in_array($st, ['active', 'actif']);
                            @endphp
                            <span class="badge-status {{ $isActive ? 'badge-status-active' : 'badge-status-inactive' }}">
                                <span class="status-dot {{ $isActive ? 'dot-green' : 'dot-gray' }}"></span>
                                {{ $isActive ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="col-actions text-end">
                            <div class="action-buttons-group">
                                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" 
                                   target="_blank" 
                                   class="action-btn action-btn-view" 
                                   title="Voir sur la boutique" 
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" 
                                        class="action-btn action-btn-4k" 
                                        title="Trouver image 4K Studio" 
                                        data-bs-toggle="tooltip"
                                        onclick="open4kFinderModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->thumbnail }}', '{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}')">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                </button>
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="action-btn action-btn-edit" 
                                   title="Modifier le produit" 
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('products.destroy', $product->id) }}" 
                                      class="d-inline m-0 p-0"
                                      data-confirm-delete="true"
                                      data-item-type="product"
                                      data-item-name="{{ $product->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="action-btn action-btn-delete" 
                                            title="Supprimer le produit" 
                                            data-bs-toggle="tooltip">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucun produit trouvé</h5>
                                <p class="text-muted">Essayez d'ajuster vos critères de recherche ou vos filtres.</p>
                                @if(request('search') || request('category') || request('quality') || request('stock_status') || request('status'))
                                    <a href="{{ route('products.index') }}" class="btn-brand-primary mt-3">
                                        <i class="fas fa-redo me-1"></i> Réinitialiser tous les filtres
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="products-table-footer px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="text-muted small">
                <i class="fas fa-list-ul me-1 opacity-50"></i>
                Affichage de <span class="fw-semibold text-dark">{{ $products->firstItem() ?? 0 }}</span> à <span class="fw-semibold text-dark">{{ $products->lastItem() ?? 0 }}</span> sur <span class="fw-semibold text-dark">{{ $products->total() }}</span> produits
            </div>
            @if($products->hasPages())
            <div>
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>

@push('styles')
<style>
    /* Prevent horizontal scrolling & optimize products table */
    .products-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
    
    @media (min-width: 992px) {
        .products-table-wrapper {
            overflow-x: hidden; /* Strict removal of horizontal scroll on desktop */
        }
    }

    .brand-table-products {
        width: 100%;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .brand-table-products thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 0.85rem 0.5rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
        vertical-align: middle;
    }

    .brand-table-products tbody td {
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #1e293b;
        font-size: 0.85rem;
    }

    .brand-table-products thead th.col-check,
    .brand-table-products tbody td.col-check {
        width: 44px;
        min-width: 44px;
        padding-left: 1.25rem;
        padding-right: 0.25rem;
        text-align: center;
    }

    .brand-table-products thead th.col-actions,
    .brand-table-products tbody td.col-actions {
        width: 155px;
        min-width: 155px;
        padding-right: 1.25rem;
        padding-left: 0.25rem;
        text-align: right;
    }

    .brand-table-products .col-product {
        width: auto;
        min-width: 190px;
    }

    .brand-table-products .col-sku {
        width: 95px;
        white-space: nowrap;
    }

    .brand-table-products .col-category {
        width: 120px;
        white-space: nowrap;
    }

    .brand-table-products .col-price {
        width: 95px;
        white-space: nowrap;
    }

    .brand-table-products .col-stock {
        width: 120px;
        white-space: nowrap;
    }

    .brand-table-products .col-status {
        width: 85px;
        white-space: nowrap;
    }

    /* Product Details Styling */
    .prod-avatar {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        flex-shrink: 0;
    }

    .prod-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .prod-avatar-badge {
        position: absolute;
        bottom: -3px;
        right: -3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        cursor: pointer;
        z-index: 2;
        font-size: 0.58rem;
    }

    .prod-info-wrap {
        min-width: 0;
        flex-grow: 1;
    }

    .prod-name {
        font-weight: 600;
        font-size: 0.84rem;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }

    .prod-desc-text {
        font-size: 0.72rem;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 130px;
        display: inline-block;
    }

    .prod-sku-badge {
        font-size: 0.7rem;
        border: 1px solid #e2e8f0;
        padding: 3px 6px;
        border-radius: 6px;
    }

    .prod-cost-text {
        font-size: 0.72rem;
        color: #94a3b8;
    }

    /* Action Buttons Row */
    .action-buttons-group {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
        white-space: nowrap;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        text-decoration: none !important;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        background: #f8fafc;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }

    /* View button - Teal */
    .action-btn-view {
        background: #f0fdfa;
        color: #0d9488;
        border-color: #ccfbf1;
    }
    .action-btn-view:hover {
        background: #0d9488;
        color: #ffffff;
        border-color: #0d9488;
    }

    /* 4K Studio button - Purple */
    .action-btn-4k {
        background: #faf5ff;
        color: #7c3aed;
        border-color: #ede9fe;
    }
    .action-btn-4k:hover {
        background: linear-gradient(135deg, #7c3aed, #9333ea);
        color: #ffffff;
        border-color: #7c3aed;
    }

    /* Edit button - Blue */
    .action-btn-edit {
        background: #eff6ff;
        color: #2563eb;
        border-color: #dbeafe;
    }
    .action-btn-edit:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    /* Delete button - Red */
    .action-btn-delete {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fee2e2;
    }
    .action-btn-delete:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
    }

    /* KPI Cards Styling */
    .stat-kpi-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 16px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        display: block;
        transition: all 0.2s ease;
    }
    .stat-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.07);
        border-color: #cbd5e1;
    }
    .stat-kpi-card.active-filter {
        border-color: #6366f1;
        background: #fafafe;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }
    .stat-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 4px;
    }
    .stat-kpi-value {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .stat-kpi-sub {
        font-size: 0.72rem;
        margin-top: 4px;
    }
    .stat-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    /* Row hover & selection highlight */
    .brand-table-products tbody tr {
        transition: background-color 0.15s ease;
    }
    .brand-table-products tbody tr:hover {
        background-color: #f8fafc;
    }
    .brand-table-products tbody tr.selected {
        background-color: #eef2ff !important;
        box-shadow: inset 3px 0 0 #6366f1;
    }
    .brand-table-products tbody tr:last-child td { border-bottom: none; }

    /* Pro badges */
    .badge-category,
    .badge-stock,
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
        line-height: 1.2;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .badge-category {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }
    .badge-category i { color: #6366f1; font-size: 0.68rem; }

    .badge-stock-in  { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
    .badge-stock-low { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-stock-out { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .badge-status-active   { background: #ffffff; color: #047857; border: 1px solid #d1fae5; }
    .badge-status-inactive { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
        display: inline-block;
    }
    .dot-green { background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.18); }
    .dot-amber { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.18); }
    .dot-red   { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.18); animation: pulseDot 1.8s infinite; }
    .dot-gray  { background: #94a3b8; }
    @keyframes pulseDot {
        0%,100% { box-shadow: 0 0 0 3px rgba(239,68,68,0.18); }
        50%     { box-shadow: 0 0 0 5px rgba(239,68,68,0.05); }
    }

    .prod-avatar-badge i { color: #7c3aed; }
    .prod-avatar-badge:hover { background: #7c3aed; border-color: #7c3aed; }
    .prod-avatar-badge:hover i { color: #fff; }

    .prod-sku-badge {
        background: #f8fafc !important;
        color: #475569 !important;
        letter-spacing: 0.02em;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }

    /* Table footer */
    .products-table-footer {
        background: #fafbfc;
        border-top: 1px solid #eef2f6;
    }
    .products-table-footer .custom-pagination-nav { justify-content: flex-end; }
</style>
@endpush

@push('scripts')
<script>
    let selectedProducts = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        initTooltips();
    });

    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateSelection();
    }
    
    function updateSelection() {
        selectedProducts = [];
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        checkboxes.forEach(cb => {
            selectedProducts.push(parseInt(cb.value));
            cb.closest('tr').classList.add('selected');
        });
        
        // Remove selected class from unchecked rows
        document.querySelectorAll('.product-checkbox:not(:checked)').forEach(cb => {
            cb.closest('tr').classList.remove('selected');
        });
        
        // Update count display
        document.getElementById('selectedCount').textContent = selectedProducts.length;
        
        // Show/hide bulk actions bar
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        if (selectedProducts.length > 0) {
            bulkActionsBar.style.display = 'block';
        } else {
            bulkActionsBar.style.display = 'none';
        }
        
        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.product-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        if (allCheckboxes.length > 0 && selectedProducts.length === allCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (selectedProducts.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }
    
    function clearSelection() {
        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        updateSelection();
    }
    
    function executeBulkAction(action) {
        if (selectedProducts.length === 0) {
            Swal.fire('No Selection', 'Please select at least one product.', 'warning');
            return;
        }
        
        const actionLabels = {
            'delete': 'delete',
            'duplicate': 'duplicate',
            'increase_stock': 'increase stock for',
            'decrease_stock': 'decrease stock for',
            'activate': 'activate',
            'deactivate': 'deactivate'
        };
        
        const confirmMessage = `Are you sure you want to ${actionLabels[action]} ${selectedProducts.length} product(s)?`;
        
        Swal.fire({
            title: 'Confirm Action',
            text: confirmMessage,
            icon: action === 'delete' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#dc3545' : '#4F46E5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction(action);
            }
        });
    }
    
    function performBulkAction(action) {
        const stockAmount = document.getElementById('stockAmount').value || 10;
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we process your request.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route('products.bulk-action') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                product_ids: selectedProducts,
                stock_amount: parseInt(stockAmount)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reload the page to reflect changes
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'An error occurred.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        });
    }

    // 4K Finder Modal State
    let current4kProductId = null;

    function open4kFinderModal(productId, productName, currentImg, currentRes) {
        current4kProductId = productId;
        document.getElementById('modal4kSubtitle').innerText = productName;
        document.getElementById('modalCurrentImg').src = currentImg;
        document.getElementById('modalCurrentRes').innerText = currentRes || 'Résolution standard';
        document.getElementById('query4k').value = productName;
        document.getElementById('modal4k').style.display = 'flex';
        document.body.style.overflow = 'hidden';

        trigger4kSearch();
    }

    function close4kImageModal() {
        document.getElementById('modal4k').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function trigger4kSearch() {
        if (!current4kProductId) return;

        const query = document.getElementById('query4k').value.trim();
        if (!query) return;

        const loading = document.getElementById('loading4k');
        const grid = document.getElementById('grid4k');
        const btn = document.getElementById('btnRun4kSearch');

        loading.style.display = 'block';
        grid.innerHTML = '';
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Recherche...';

        fetch(`/products/${current4kProductId}/search-4k-images?q=` + encodeURIComponent(query), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Rechercher';

            if (!data.candidates || data.candidates.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-image" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1;"></i>
                        <p style="font-weight: 600;">Aucune image studio 4K trouvée pour "${query}"</p>
                        <p style="font-size: 13px;">Essayez de simplifier le nom avec la marque et le numéro de modèle.</p>
                    </div>
                `;
                return;
            }

            data.candidates.forEach((cand) => {
                const card = document.createElement('div');
                card.style.cssText = "background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);";
                card.onmouseenter = () => { card.style.borderColor = '#6366f1'; card.style.boxShadow = '0 8px 20px rgba(99, 102, 241, 0.15)'; };
                card.onmouseleave = () => { card.style.borderColor = '#e2e8f0'; card.style.boxShadow = '0 2px 8px rgba(0,0,0,0.04)'; };

                const is4k = cand.width >= 2000;
                const badgeColor = is4k ? "background: linear-gradient(135deg, #10b981, #059669); color: white;" : "background: #f1f5f9; color: #475569;";

                card.innerHTML = `
                    <div style="position: relative; width: 100%; height: 210px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px;">
                        <img src="${cand.url}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        <span style="position: absolute; top: 8px; left: 8px; ${badgeColor} font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            ${cand.width}×${cand.height} ${is4k ? '4K UHD' : 'HD'}
                        </span>
                        <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(15,23,42,0.7); color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px;">
                            ${cand.source}
                        </span>
                    </div>
                    <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px; background: #ffffff; border-top: 1px solid #f1f5f9;">
                        <button type="button" onclick="applySelected4kImage('${cand.url.replace(/'/g, "\\'")}', this)" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 8px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                            <i class="fas fa-check-circle"></i> Choisir cette image 4K
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });
        })
        .catch(err => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Rechercher';
            grid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; padding: 20px; text-align: center;">Erreur de recherche : ${err.message}</div>`;
        });
    }

    function applySelected4kImage(imageUrl, button) {
        const origHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Téléchargement 4K...';

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

        fetch(`/products/${current4kProductId}/apply-4k-image`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ image_url: imageUrl })
        })
        .then(res => res.json())
        .then(data => {
            button.disabled = false;
            button.innerHTML = origHtml;

            if (data.success) {
                // Update product row live
                const prodImg = document.getElementById(`prod-img-${current4kProductId}`);
                if (prodImg) {
                    prodImg.src = data.image_url + '?t=' + new Date().getTime();
                }

                const prodBadge = document.getElementById(`prod-badge-${current4kProductId}`);
                if (prodBadge && data.badge_html) {
                    prodBadge.innerHTML = data.badge_html;
                }

                close4kImageModal();

                Swal.fire({
                    title: 'Image 4K Appliquée !',
                    text: `Résolution : ${data.width}×${data.height} pixels (${data.quality.toUpperCase()})`,
                    icon: 'success',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                Swal.fire('Erreur', data.message || 'Impossible d\'appliquer l\'image 4K', 'error');
            }
        })
        .catch(err => {
            button.disabled = false;
            button.innerHTML = origHtml;
            Swal.fire('Erreur', 'Erreur de téléchargement : ' + err.message, 'error');
        });
    }

    function bulkUpgradeTo4k() {
        if (!selectedProducts || selectedProducts.length === 0) {
            Swal.fire('Aucun produit sélectionné', 'Veuillez cocher au moins un produit à mettre à niveau.', 'info');
            return;
        }

        Swal.fire({
            title: 'Mise à niveau 4K Studio',
            text: `Voulez-vous rechercher et appliquer automatiquement les meilleures images 4K Ultra-HD pour les ${selectedProducts.length} produit(s) sélectionné(s) ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '🚀 Oui, mettre à niveau en 4K !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Traitement Studio 4K en cours...',
                    text: 'Téléchargement des masters 4K Ultra-HD sans compression...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

                fetch("{{ route('products.bulk-upgrade-4k') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_ids: selectedProducts })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Mise à niveau terminée !',
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Erreur', data.message || 'Une erreur est survenue.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Erreur', 'Une erreur inattendue est survenue : ' + err.message, 'error');
                });
            }
        });
    }
</script>
@endpush

<!-- Modal 4K Image Finder -->
<div id="modal4k" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); z-index: 9999; overflow-y: auto; padding: 20px; align-items: center; justify-content: center;">
    <div style="background: #ffffff; width: 100%; max-width: 900px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; margin: auto; border: 1px solid rgba(255, 255, 255, 0.2);">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: white; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fbbf24; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -0.01em;">Studio Recherche 4K Ultra-HD</h4>
                    <p style="margin: 3px 0 0; font-size: 13px; color: #c7d2fe;" id="modal4kSubtitle">Sélectionnez la meilleure image studio en haute résolution</p>
                </div>
            </div>
            <button type="button" onclick="close4kImageModal()" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 34px; height: 34px; border-radius: 8px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Current Image & Search Bar -->
        <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img id="modalCurrentImg" src="" alt="Current" style="width: 46px; height: 46px; object-fit: contain; background: white; border: 1px solid #cbd5e1; border-radius: 8px; padding: 2px;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.05em;">Image actuelle</div>
                        <div id="modalCurrentRes" style="font-size: 13px; font-weight: 600; color: #334155;">-</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; flex-grow: 1; max-width: 500px;">
                    <input type="text" id="query4k" class="form-control" placeholder="Rechercher par nom de produit..." style="border-radius: 10px; font-size: 13px; padding: 9px 14px;">
                    <button type="button" id="btnRun4kSearch" onclick="trigger4kSearch()" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5; border-radius: 10px; padding: 9px 18px; font-size: 13px; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-bolt me-1"></i> Rechercher
                    </button>
                </div>
            </div>
        </div>

        <!-- Candidates Grid -->
        <div style="padding: 24px; max-height: 520px; overflow-y: auto;">
            <div id="loading4k" style="display: none; text-align: center; padding: 50px 20px;">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <p style="margin-top: 16px; font-weight: 600; color: #475569; font-size: 15px;">Exploration des masters 4K Ultra-HD...</p>
                <p style="font-size: 13px; color: #94a3b8;">Extraction sans compression depuis les serveurs officiels</p>
            </div>
            <div id="grid4k" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px;">
                <!-- Candidate cards injected dynamically -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 12px; color: #64748b;">
                <i class="fas fa-check-shield text-success me-1"></i> Qualité studio vérifiée &bull; Zéro filigrane &bull; Résolution garantie
            </div>
            <button type="button" onclick="close4kImageModal()" class="btn btn-secondary btn-sm rounded-pill px-4">
                Fermer
            </button>
        </div>
    </div>
</div>
@endsection

