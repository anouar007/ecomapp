@extends('layouts.app')

@section('title', 'Gestion des produits')

@section('content')
<div class="products-dashboard-container">
    <!-- En-tête de page -->
    <div class="brand-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1 text-primary bg-primary-subtle fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                    <i class="fas fa-boxes-stacked me-1"></i> CATALOGUE PRODUITS
                </span>
            </div>
            <h1 class="brand-title mb-1">
                <div class="brand-header-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                Gestion des Produits
            </h1>
            <p class="brand-subtitle mb-0">Pilotez votre inventaire, vos tarifs et la qualité visuelle Studio 4K en temps réel</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary border shadow-sm rounded-pill px-3 py-2 btn-header-action" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-excel text-success me-1"></i> Importer
            </button>
            <a href="{{ route('export.products') }}" class="btn btn-outline-secondary border shadow-sm rounded-pill px-3 py-2 btn-header-action">
                <i class="fas fa-file-csv text-primary me-1"></i> Exporter
            </a>
            <a href="{{ route('products.image-quality') }}" class="btn btn-outline-purple shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 btn-header-action" style="border-color: #8b5cf6; color: #6d28d9; background: #faf5ff; font-weight: 600;">
                <i class="fas fa-wand-magic-sparkles text-warning"></i> Studio 4K Hub
                @if(!empty($stats['needs_upgrade']) && $stats['needs_upgrade'] > 0)
                    <span class="badge rounded-pill bg-warning text-dark px-2" style="font-size: 0.72rem;">{{ $stats['needs_upgrade'] }} à optimiser</span>
                @endif
            </a>
            <a href="{{ route('products.create') }}" class="btn-brand-primary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Nouveau Produit</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="brand-stats-grid mb-4">
        <!-- Total Produits -->
        <div class="brand-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="brand-stat-label mb-0">Total Produits</span>
                <div class="brand-stat-icon primary mb-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
            <div class="brand-stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
            <div class="brand-stat-desc d-flex align-items-center justify-content-between text-muted small mt-2">
                <a href="{{ route('products.index', ['status' => 'active']) }}" class="text-decoration-none text-success fw-semibold">
                    <i class="fas fa-check-circle me-1"></i><strong>{{ $stats['active'] ?? 0 }}</strong> actifs
                </a>
                <a href="{{ route('products.index', ['status' => 'inactive']) }}" class="text-decoration-none text-secondary fw-semibold">
                    <i class="fas fa-pause-circle me-1"></i><strong>{{ $stats['inactive'] ?? 0 }}</strong> inactifs
                </a>
            </div>
        </div>

        <!-- Valeur Catalogue & Stock -->
        <div class="brand-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="brand-stat-label mb-0">Valeur Marchande</span>
                <div class="brand-stat-icon success mb-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="brand-stat-value">{{ currency($stats['inventory_value'] ?? 0) }}</div>
            <div class="brand-stat-desc text-muted small mt-2">
                <i class="fas fa-warehouse text-success me-1"></i><strong>{{ number_format($stats['total_stock'] ?? 0) }}</strong> unités au total en stock
            </div>
        </div>

        <!-- Alertes de Stock -->
        <div class="brand-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="brand-stat-label mb-0">Santé du Stock</span>
                <div class="brand-stat-icon {{ ($stats['low_stock'] + $stats['out_of_stock']) > 0 ? 'warning' : 'success' }} mb-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                    <i class="fas {{ ($stats['low_stock'] + $stats['out_of_stock']) > 0 ? 'fa-triangle-exclamation' : 'fa-circle-check' }}"></i>
                </div>
            </div>
            <div class="brand-stat-value {{ ($stats['low_stock'] + $stats['out_of_stock']) > 0 ? 'text-warning' : 'text-success' }}">
                <a href="{{ route('products.index', ['stock_status' => 'alerts']) }}" class="text-decoration-none {{ ($stats['low_stock'] + $stats['out_of_stock']) > 0 ? 'text-warning' : 'text-success' }}">
                    {{ $stats['low_stock'] + $stats['out_of_stock'] }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: #64748b;">alertes</span>
                </a>
            </div>
            <div class="brand-stat-desc d-flex align-items-center justify-content-between text-muted small mt-2">
                <a href="{{ route('products.index', ['stock_status' => 'out_of_stock']) }}" class="text-decoration-none text-danger fw-semibold">
                    <i class="fas fa-times-circle me-1"></i>{{ $stats['out_of_stock'] ?? 0 }} épuisés
                </a>
                <a href="{{ route('products.index', ['stock_status' => 'low_stock']) }}" class="text-decoration-none text-warning fw-semibold">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $stats['low_stock'] ?? 0 }} faibles
                </a>
            </div>
        </div>

        <!-- Qualité Studio 4K -->
        <div class="brand-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="brand-stat-label mb-0">Qualité Studio 4K</span>
                <div class="brand-stat-icon info mb-0" style="width: 42px; height: 42px; font-size: 1.1rem; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    <i class="fas fa-wand-magic-sparkles text-white"></i>
                </div>
            </div>
            <div class="brand-stat-value text-indigo" style="color: #6366f1;">
                {{ $stats['four_k'] ?? 0 }}
                <span style="font-size: 0.85rem; font-weight: 500; color: #64748b;">/ {{ $stats['total'] ?? 0 }} ({{ $stats['four_k_percentage'] ?? 0 }}%)</span>
            </div>
            <div class="mt-2">
                <div class="progress" style="height: 6px; border-radius: 999px; background: #e2e8f0;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $stats['four_k_percentage'] ?? 0 }}%; background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: 999px;" aria-valuenow="{{ $stats['four_k_percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1 small" style="font-size: 0.75rem;">
                    <a href="{{ route('products.index', ['quality' => 'fhd']) }}" class="text-decoration-none text-muted">
                        {{ $stats['fhd'] ?? 0 }} FHD
                    </a>
                    <a href="{{ route('products.index', ['quality' => 'needs_upgrade']) }}" class="text-decoration-none text-warning fw-semibold">
                        {{ $stats['needs_upgrade'] ?? 0 }} à optimiser &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de filtres unifiée et responsive -->
    <div class="brand-filter-bar mb-4">
        <form method="GET" action="{{ route('products.index') }}" id="filterForm">
            <!-- Ligne 1: Filtres et barre de recherche -->
            <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                <!-- Champ de recherche avec input-group (Zéro chevauchement d'icône) -->
                <div class="filter-search-box flex-grow-1" style="min-width: 240px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2" style="border-radius: 10px 0 0 10px;">
                            <i class="fas fa-magnifying-glass text-secondary"></i>
                        </span>
                        <input type="text" name="search" id="productSearchInput" 
                               class="form-control border-start-0 border-end-0 ps-1" 
                               placeholder="Rechercher nom, SKU, référence..."
                               value="{{ request('search') }}">
                        @if(request('search'))
                            <button type="button" class="btn btn-outline-secondary border-start-0 border-end-0 bg-white text-muted" onclick="clearSearch()" title="Effacer la recherche">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        @endif
                        <button type="submit" class="btn btn-primary px-3 fw-semibold" style="border-radius: 0 10px 10px 0; background: #4f46e5; border-color: #4f46e5;">
                            Rechercher
                        </button>
                    </div>
                </div>

                <!-- Sélecteur Catégorie -->
                <div style="min-width: 170px;">
                    <select name="category" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sélecteur Statut de Stock -->
                <div style="min-width: 160px;">
                    <select name="stock_status" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="">Tous les stocks</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>En stock (&gt; 5)</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>⚠️ Stock faible (&le; 5)</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>❌ Rupture de stock</option>
                    </select>
                </div>

                <!-- Sélecteur Résolution d'image -->
                <div style="min-width: 170px;">
                    <select name="quality" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="">Toutes résolutions</option>
                        <option value="4k" {{ request('quality') == '4k' ? 'selected' : '' }}>✨ 4K UHD ({{ $qualityStats['four_k'] ?? 0 }})</option>
                        <option value="fhd" {{ request('quality') == 'fhd' ? 'selected' : '' }}>📺 Full HD ({{ $qualityStats['fhd'] ?? 0 }})</option>
                        <option value="needs_upgrade" {{ request('quality') == 'needs_upgrade' ? 'selected' : '' }}>⚠️ À optimiser ({{ $qualityStats['needs_upgrade'] ?? 0 }})</option>
                        <option value="missing" {{ request('quality') == 'missing' ? 'selected' : '' }}>❌ Sans image</option>
                    </select>
                </div>

                <!-- Sélecteur Tri -->
                <div style="min-width: 160px;">
                    <select name="sort" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Tri : Plus récents</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Tri : Plus anciens</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix : Décroissant</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix : Croissant</option>
                        <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stock : Décroissant</option>
                        <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stock : Croissant</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nom : A &rarr; Z</option>
                    </select>
                </div>

                <!-- Bouton Réinitialiser (si filtres actifs) -->
                @if(request()->hasAny(['search', 'category', 'quality', 'stock_status', 'status', 'sort']))
                    <div>
                        <a href="{{ route('products.index') }}" class="btn btn-light border rounded-pill px-3 py-2 text-danger fw-semibold d-inline-flex align-items-center gap-1" title="Réinitialiser tous les filtres">
                            <i class="fas fa-rotate-left"></i> <span>Effacer</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Ligne 2: Filtres rapides en 1 clic (Quick Filter Pills) -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <span class="text-muted small me-1 fw-bold" style="font-size: 0.78rem;">Filtres rapides :</span>
                    
                    <a href="{{ route('products.index') }}" 
                       class="quick-pill {{ !request()->hasAny(['status', 'stock_status', 'quality']) ? 'active' : '' }}">
                        Tous ({{ $stats['total'] ?? 0 }})
                    </a>

                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['status' => 'active'])) }}" 
                       class="quick-pill {{ request('status') === 'active' ? 'active' : '' }}">
                        <span class="quick-pill-dot bg-success"></span> Actifs ({{ $stats['active'] ?? 0 }})
                    </a>

                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['status' => 'inactive'])) }}" 
                       class="quick-pill {{ request('status') === 'inactive' ? 'active' : '' }}">
                        <span class="quick-pill-dot bg-secondary"></span> Inactifs ({{ $stats['inactive'] ?? 0 }})
                    </a>

                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['stock_status' => 'out_of_stock'])) }}" 
                       class="quick-pill {{ request('stock_status') === 'out_of_stock' ? 'active' : '' }}">
                        <span class="quick-pill-dot bg-danger"></span> Ruptures ({{ $stats['out_of_stock'] ?? 0 }})
                    </a>

                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['stock_status' => 'low_stock'])) }}" 
                       class="quick-pill {{ request('stock_status') === 'low_stock' ? 'active' : '' }}">
                        <span class="quick-pill-dot bg-warning"></span> Stock faible ({{ $stats['low_stock'] ?? 0 }})
                    </a>

                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['quality' => '4k'])) }}" 
                       class="quick-pill {{ request('quality') === '4k' ? 'active' : '' }}">
                        <i class="fas fa-wand-magic-sparkles text-warning me-1"></i> 4K UHD ({{ $stats['four_k'] ?? 0 }})
                    </a>

                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['quality' => 'needs_upgrade'])) }}" 
                       class="quick-pill {{ request('quality') === 'needs_upgrade' ? 'active' : '' }}">
                        <i class="fas fa-triangle-exclamation text-warning me-1"></i> À optimiser ({{ $stats['needs_upgrade'] ?? 0 }})
                    </a>
                </div>

                <div class="text-muted small fw-medium">
                    <span class="badge bg-light text-dark border px-3 py-1 rounded-pill" style="font-size: 0.8rem;">
                        <strong>{{ $products->total() }}</strong> produit(s) trouvé(s)
                    </span>
                </div>
            </div>
        </form>
    </div>

    <!-- Barre d'actions groupées flottante (Bulk Actions Bar) -->
    <div id="bulkActionsBar" class="bulk-actions-floating-bar" style="display: none;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="bulk-counter-pill">
                    <i class="fas fa-check-double me-1 text-primary"></i>
                    <span id="selectedCount" class="fw-bold">0</span> produit(s) sélectionné(s)
                </div>
                <button type="button" class="btn btn-sm btn-link text-white-50 p-0 text-decoration-none" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Ajustement rapide du stock -->
                <div class="input-group input-group-sm rounded-pill overflow-hidden border-0" style="width: 140px;">
                    <span class="input-group-text bg-white bg-opacity-25 text-white border-0 py-1 px-2" style="font-size: 0.75rem;">Qté</span>
                    <input type="number" id="stockAmount" class="form-control border-0 text-center py-1 fw-bold" value="10" min="1" max="9999" style="font-size: 0.8rem;">
                </div>
                
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-success btn-sm rounded-start-pill px-3" onclick="executeBulkAction('increase_stock')" title="Ajouter du stock">
                        <i class="fas fa-plus me-1"></i>+ Stock
                    </button>
                    <button type="button" class="btn btn-warning btn-sm rounded-end-pill px-3 text-dark fw-semibold" onclick="executeBulkAction('decrease_stock')" title="Retirer du stock">
                        <i class="fas fa-minus me-1"></i>- Stock
                    </button>
                </div>

                <button type="button" class="btn btn-sm btn-studio-4k rounded-pill px-3 text-white fw-semibold" onclick="bulkUpgradeTo4k()" title="Mettre à niveau en qualité Studio 4K">
                    <i class="fas fa-wand-magic-sparkles me-1 text-warning"></i>Mettre à niveau 4K
                </button>

                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-light btn-sm rounded-start-pill px-2" onclick="executeBulkAction('activate')" title="Activer les produits sélectionnés">
                        <i class="fas fa-check me-1"></i>Activer
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-end-pill px-2" onclick="executeBulkAction('deactivate')" title="Désactiver les produits sélectionnés">
                        <i class="fas fa-ban me-1"></i>Désactiver
                    </button>
                </div>

                <button type="button" class="btn btn-info btn-sm text-white rounded-pill px-2" onclick="executeBulkAction('duplicate')" title="Dupliquer">
                    <i class="fas fa-copy me-1"></i>Dupliquer
                </button>

                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" onclick="executeBulkAction('delete')" title="Supprimer définitivement">
                    <i class="fas fa-trash-alt me-1"></i>Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Carte du Tableau des Produits (ZERO HORIZONTAL SCROLL & PLEINE LARGEUR) -->
    <div class="brand-table-card shadow-sm border-0 mb-4">
        <div class="products-table-wrapper">
            <table class="brand-table products-table mb-0 w-100">
                <colgroup>
                    <col style="width: 42px;">
                    <col style="width: 34%;">
                    <col style="width: 15%;">
                    <col style="width: 12%;">
                    <col style="width: 15%;">
                    <col style="width: 11%;">
                    <col style="width: 13%;">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center" style="padding-left: 0.75rem; padding-right: 0.5rem;">
                            <input type="checkbox" class="form-check-input select-all-checkbox" id="selectAll" onchange="toggleSelectAll(this)" title="Tout sélectionner">
                        </th>
                        <th>Détails du Produit</th>
                        <th>Catégorie</th>
                        <th>Prix & Marge</th>
                        <th>Stock & Quantité</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end" style="padding-right: 1.25rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr data-product-id="{{ $product->id }}" id="product-row-{{ $product->id }}">
                        <!-- 1. Checkbox -->
                        <td class="text-center" style="padding-left: 0.75rem; padding-right: 0.5rem;">
                            <input type="checkbox" class="form-check-input product-checkbox" 
                                   value="{{ $product->id }}" 
                                   onchange="updateSelection()">
                        </td>

                        <!-- 2. Détails du Produit (Thumbnail + Titre + SKU + Badge 4K) -->
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <!-- Vignette Produit avec badge 4K et hover action -->
                                <div class="product-thumb-container position-relative flex-shrink-0" id="prod-avatar-wrap-{{ $product->id }}">
                                    <img id="prod-img-{{ $product->id }}" 
                                         src="{{ $product->thumbnail }}" 
                                         alt="{{ $product->name }}" 
                                         class="product-thumb-img"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    
                                    <!-- Bouton rapide de recherche 4K sur l'image -->
                                    <button type="button" 
                                            class="btn-thumb-4k-trigger shadow-sm" 
                                            onclick="open4kFinderModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->thumbnail }}', '{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}')"
                                            title="Explorer et appliquer une image Studio 4K">
                                        <i class="fas fa-wand-magic-sparkles text-warning"></i>
                                    </button>
                                </div>

                                <!-- Infos textuelles -->
                                <div class="product-info-column min-w-0 flex-grow-1">
                                    <a href="{{ route('products.edit', $product) }}" 
                                       class="product-title-link text-truncate d-block fw-bold mb-1" 
                                       title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </a>

                                    <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.72rem;">
                                        <!-- SKU monospacé -->
                                        <span class="product-sku-pill" title="Code SKU / Référence">
                                            <i class="fas fa-barcode opacity-50 me-1"></i>{{ $product->sku }}
                                        </span>

                                        <!-- Badge de qualité visuelle -->
                                        <span id="prod-badge-{{ $product->id }}" class="prod-badge-holder">
                                            {!! $product->image_quality_badge !!}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- 3. Catégorie -->
                        <td>
                            @if($product->category_name)
                                <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="product-category-pill text-truncate d-inline-block text-decoration-none" title="Filtrer par catégorie : {{ $product->category_name }}">
                                    <i class="fas fa-folder me-1 opacity-50"></i>{{ $product->category_name }}
                                </a>
                            @else
                                <span class="text-muted small italic opacity-75">— Non classé</span>
                            @endif
                        </td>

                        <!-- 4. Prix & Marge -->
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                {{ currency($product->price) }}
                            </div>
                            @if($product->cost_price && $product->cost_price > 0)
                                <div class="text-muted small" style="font-size: 0.72rem;" title="Coût d'achat">
                                    Coût : {{ currency($product->cost_price) }}
                                </div>
                            @endif
                        </td>

                        <!-- 5. Stock & Disponibilité -->
                        <td>
                            @php
                                $stock = (int) $product->stock;
                                $min = (int) ($product->min_stock ?? 5);
                            @endphp

                            @if($stock <= 0)
                                <span class="stock-badge stock-badge-danger" title="Rupture totale de stock">
                                    <span class="stock-indicator-dot bg-danger"></span>
                                    Rupture (0)
                                </span>
                            @elseif($stock <= $min)
                                <span class="stock-badge stock-badge-warning" title="Niveau inférieur au seuil d'alerte ({{ $min }})">
                                    <span class="stock-indicator-dot bg-warning"></span>
                                    Faible : {{ $stock }}
                                </span>
                            @else
                                <span class="stock-badge stock-badge-success" title="{{ $stock }} unités disponibles">
                                    <span class="stock-indicator-dot bg-success"></span>
                                    {{ $stock }} en stock
                                </span>
                            @endif
                        </td>

                        <!-- 6. Statut (Interrupteur interactif en 1 clic AJAX) -->
                        <td class="text-center">
                            @php
                                $st = strtolower($product->status);
                                $isActive = ($st === 'active' || $st === 'actif');
                            @endphp
                            <button type="button" 
                                    class="status-toggle-btn {{ $isActive ? 'status-active' : 'status-inactive' }}"
                                    onclick="toggleProductStatus({{ $product->id }}, this)"
                                    id="status-btn-{{ $product->id }}"
                                    title="Cliquer pour changer instantanément le statut">
                                <span class="status-toggle-dot"></span>
                                <span class="status-toggle-text">{{ $isActive ? 'Actif' : 'Inactif' }}</span>
                            </button>
                        </td>

                        <!-- 7. Actions -->
                        <td style="padding-right: 1.25rem;">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <!-- Bouton Studio 4K -->
                                <button type="button" 
                                        class="table-action-icon action-4k" 
                                        title="Rechercher une image Studio 4K Ultra-HD"
                                        onclick="open4kFinderModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->thumbnail }}', '{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}')">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                </button>

                                <!-- Bouton Modifier -->
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="table-action-icon action-edit" 
                                   title="Modifier la fiche produit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Bouton Supprimer -->
                                <form method="POST" 
                                      action="{{ route('products.destroy', $product->id) }}" 
                                      class="d-inline m-0 p-0"
                                      data-confirm-delete="true"
                                      data-item-type="product"
                                      data-item-name="{{ $product->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="table-action-icon action-delete" title="Supprimer le produit">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-5">
                                <div class="empty-state-avatar mx-auto mb-3">
                                    <i class="fas fa-box-open text-muted opacity-50"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Aucun produit ne correspond à ces critères</h5>
                                <p class="text-muted small mb-3">Modifiez vos termes de recherche ou réinitialisez les filtres sélectionnés.</p>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                                        <i class="fas fa-rotate-left me-1"></i> Réinitialiser les filtres
                                    </a>
                                    <a href="{{ route('products.create') }}" class="btn-brand-primary rounded-pill px-4 btn-sm">
                                        <i class="fas fa-plus me-1"></i> Créer un nouveau produit
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Compteur bas de tableau -->
        @if($products->total() > 0)
        <div class="px-4 py-3 border-top bg-light-subtle d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="text-muted small">
                Affichage de <span class="fw-bold text-dark">{{ $products->firstItem() ?? 0 }}</span> à 
                <span class="fw-bold text-dark">{{ $products->lastItem() ?? 0 }}</span> sur 
                <span class="fw-bold text-dark">{{ $products->total() }}</span> produit(s)
            </div>
            <div class="products-pagination-container">
                {{ $products->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="importModalLabel">Importer des Produits</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Sélectionner le fichier Excel / CSV</label>
                        <input type="file" name="file" class="form-control rounded-3" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text small mt-1">Formats compatibles : .xlsx, .xls, .csv (Taille max 10 Mo)</div>
                    </div>
                    
                    <div class="alert alert-light border rounded-3 p-3 mb-0">
                        <h6 class="fw-bold text-dark mb-2" style="font-size: 0.85rem;"><i class="fas fa-table-columns text-primary me-2"></i>Colonnes obligatoires</h6>
                        <code class="d-block bg-white p-2 border rounded small font-monospace text-dark" style="font-size: 0.75rem;">name, sku, description, price, cost_price, stock, min_stock, category, status</code>
                        <div class="mt-3">
                            <a href="{{ route('products.template') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-download me-1"></i>Télécharger le modèle Excel type
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-brand-primary rounded-pill px-4">
                        <i class="fas fa-cloud-arrow-up me-2"></i>Lancer l'importation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4K Image Finder -->
<div id="modal4k" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); z-index: 9999; overflow-y: auto; padding: 20px; align-items: center; justify-content: center;">
    <div style="background: #ffffff; width: 100%; max-width: 920px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); overflow: hidden; margin: auto; border: 1px solid rgba(255, 255, 255, 0.2);">
        <!-- En-tête Modal 4K -->
        <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: white; padding: 22px 28px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fbbf24; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -0.01em;">Studio Recherche 4K Ultra-HD</h4>
                    <p style="margin: 3px 0 0; font-size: 13px; color: #c7d2fe;" id="modal4kSubtitle">Sélectionnez la meilleure image studio en haute résolution</p>
                </div>
            </div>
            <button type="button" onclick="close4kImageModal()" style="background: rgba(255,255,255,0.12); border: none; color: white; width: 36px; height: 36px; border-radius: 10px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Barre de recherche et aperçu miniature actuelle -->
        <div style="padding: 16px 28px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img id="modalCurrentImg" src="" alt="Current" style="width: 48px; height: 48px; object-fit: contain; background: white; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 3px;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.05em;">Image Actuelle</div>
                        <div id="modalCurrentRes" style="font-size: 13px; font-weight: 600; color: #1e293b;">-</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; flex-grow: 1; max-width: 520px;">
                    <input type="text" id="query4k" class="form-control" placeholder="Rechercher par référence produit, marque..." style="border-radius: 10px; font-size: 13px; padding: 10px 14px;">
                    <button type="button" id="btnRun4kSearch" onclick="trigger4kSearch()" class="btn btn-primary shadow-sm" style="background: #4f46e5; border-color: #4f46e5; border-radius: 10px; padding: 10px 20px; font-size: 13px; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-bolt me-1"></i> Rechercher
                    </button>
                </div>
            </div>
        </div>

        <!-- Grille de résultats 4K -->
        <div style="padding: 24px; max-height: 520px; overflow-y: auto;">
            <div id="loading4k" style="display: none; text-align: center; padding: 50px 20px;">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <p style="margin-top: 16px; font-weight: 600; color: #475569; font-size: 15px;">Exploration des masters 4K Ultra-HD...</p>
                <p style="font-size: 13px; color: #94a3b8;">Extraction sans compression depuis les serveurs officiels</p>
            </div>
            <div id="grid4k" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px;">
                <!-- Les cartes candidates sont injectées ici dynamiquement -->
            </div>
        </div>

        <!-- Pied du modal -->
        <div style="padding: 16px 28px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
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

@push('styles')
<style>
    /* ========================================================
       PREMIUM PRODUCTS DASHBOARD & STRICT NO-SCROLL STYLES
       ======================================================== */
    .products-dashboard-container {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    /* En-tête */
    .btn-header-action {
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .btn-header-action:hover {
        transform: translateY(-1px);
    }
    .btn-outline-purple:hover {
        background: #ede9fe !important;
        color: #5b21b6 !important;
    }

    /* Filter Toolbar Inputs */
    .filter-select {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.55rem 0.85rem;
        font-size: 0.85rem;
        color: #1e293b;
        font-weight: 500;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .filter-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    .filter-search-box .form-control {
        font-size: 0.85rem;
        padding: 0.55rem 0.85rem;
    }
    .filter-search-box .form-control:focus {
        border-color: #4f46e5;
        box-shadow: none;
    }

    /* Quick Filter Pills */
    .quick-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 11px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.77rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }
    .quick-pill:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .quick-pill.active {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
    }
    .quick-pill-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        margin-right: 6px;
    }

    /* Floating Bulk Actions Bar */
    .bulk-actions-floating-bar {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 14px;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px -5px rgba(30, 27, 75, 0.35);
        animation: slideInDown 0.25s ease-out;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .bulk-counter-pill {
        background: rgba(255, 255, 255, 0.15);
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        color: #ffffff;
        backdrop-filter: blur(4px);
    }
    .btn-studio-4k {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
    }
    .btn-studio-4k:hover {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
    }

    /* TABLE: STRICT ZERO HORIZONTAL SCROLL & 100% FULL WIDTH */
    .products-table-wrapper {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }
    @media (max-width: 991.98px) {
        .products-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .products-table {
            min-width: 820px;
        }
    }

    .products-table {
        width: 100% !important;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .products-table thead th {
        padding: 1rem 0.85rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .products-table tbody td {
        padding: 0.85rem 0.85rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .products-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .products-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .products-table tbody tr.selected {
        background-color: #f5f3ff !important;
    }

    /* Product Thumbnail & Overlay */
    .product-thumb-container {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .product-thumb-container:hover {
        transform: scale(1.06);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
    .product-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-thumb-4k-trigger {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.55rem;
        cursor: pointer;
        z-index: 2;
        padding: 0;
        transition: transform 0.15s ease;
    }
    .btn-thumb-4k-trigger:hover {
        transform: scale(1.2);
    }

    /* Product Title & Info */
    .product-title-link {
        color: #1e293b;
        font-size: 0.88rem;
        text-decoration: none;
        transition: color 0.15s ease;
        line-height: 1.3;
    }
    .product-title-link:hover {
        color: #4f46e5;
    }
    .product-sku-pill {
        display: inline-block;
        font-family: var(--bs-font-monospace);
        background: #f8fafc;
        color: #64748b;
        padding: 2px 6px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        font-size: 0.68rem;
        font-weight: 500;
    }

    /* Category Pill */
    .product-category-pill {
        background: #f1f5f9;
        color: #475569;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        max-width: 100%;
        transition: all 0.15s ease;
    }
    .product-category-pill:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    /* Stock Status Badges */
    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .stock-indicator-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .stock-badge-success {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .stock-badge-warning {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .stock-badge-danger {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Live AJAX Status Toggle Button */
    .status-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        background: none;
    }
    .status-toggle-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        transition: background-color 0.2s ease;
    }
    .status-active {
        background: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0;
    }
    .status-active .status-toggle-dot {
        background: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    .status-active:hover {
        background: #d1fae5;
    }
    .status-inactive {
        background: #f1f5f9;
        color: #64748b;
        border-color: #cbd5e1;
    }
    .status-inactive .status-toggle-dot {
        background: #94a3b8;
    }
    .status-inactive:hover {
        background: #e2e8f0;
    }

    /* Table Action Icon Buttons */
    .table-action-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: #64748b;
        text-decoration: none;
        transition: all 0.15s ease;
        cursor: pointer;
        padding: 0;
    }
    .table-action-icon:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.06);
    }
    .action-4k {
        color: #8b5cf6;
        border-color: #ede9fe;
        background: #faf5ff;
    }
    .action-4k:hover {
        background: #8b5cf6;
        color: #ffffff;
        border-color: #8b5cf6;
    }
    .action-edit {
        color: #2563eb;
        border-color: #dbeafe;
        background: #eff6ff;
    }
    .action-edit:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
    .action-delete {
        color: #dc2626;
        border-color: #fee2e2;
        background: #fef2f2;
    }
    .action-delete:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
    }

    /* Empty state */
    .empty-state-avatar {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Checkboxes */
    .form-check-input {
        cursor: pointer;
        border-radius: 5px;
        width: 17px;
        height: 17px;
    }
    .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }

    /* Clean Pagination inside product table card */
    .products-pagination-container .pagination-summary {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // ========================================================
    // SELECTION & ACTIONS GROUPÉES (BULK ACTIONS)
    // ========================================================
    let selectedProducts = [];
    
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
            const row = cb.closest('tr');
            if (row) row.classList.add('selected');
        });
        
        document.querySelectorAll('.product-checkbox:not(:checked)').forEach(cb => {
            const row = cb.closest('tr');
            if (row) row.classList.remove('selected');
        });
        
        // Mettre à jour le compteur
        const countSpan = document.getElementById('selectedCount');
        if (countSpan) countSpan.textContent = selectedProducts.length;
        
        // Afficher/Masquer la barre d'actions groupées
        const bulkBar = document.getElementById('bulkActionsBar');
        if (bulkBar) {
            bulkBar.style.display = selectedProducts.length > 0 ? 'block' : 'none';
        }
        
        // Mettre à jour la case globale
        const allCheckboxes = document.querySelectorAll('.product-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox && allCheckboxes.length > 0) {
            if (selectedProducts.length === allCheckboxes.length) {
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
    }
    
    function clearSelection() {
        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.checked = false;
        });
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        updateSelection();
    }
    
    function executeBulkAction(action) {
        if (selectedProducts.length === 0) {
            Swal.fire('Aucune sélection', 'Veuillez cocher au moins un produit.', 'warning');
            return;
        }
        
        const actionLabels = {
            'delete': 'supprimer définitivement',
            'duplicate': 'dupliquer',
            'increase_stock': 'ajouter du stock pour',
            'decrease_stock': 'retirer du stock pour',
            'activate': 'activer',
            'deactivate': 'désactiver'
        };
        
        const confirmMessage = `Voulez-vous vraiment ${actionLabels[action]} les ${selectedProducts.length} produit(s) sélectionné(s) ?`;
        
        Swal.fire({
            title: 'Confirmation requise',
            text: confirmMessage,
            icon: action === 'delete' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#ef4444' : '#4f46e5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, continuer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction(action);
            }
        });
    }
    
    function performBulkAction(action) {
        const stockAmount = document.getElementById('stockAmount')?.value || 10;
        
        Swal.fire({
            title: 'Traitement en cours...',
            text: 'Veuillez patienter pendant l\'application de l\'action.',
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
                    title: 'Succès !',
                    text: data.message,
                    icon: 'success',
                    timer: 1600,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Erreur', data.message || 'Une erreur est survenue.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Erreur', 'Une erreur inattendue est survenue.', 'error');
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

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

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

    // ========================================================
    // BASCULE DU STATUT EN 1 CLIC AJAX (ACTIVE / INACTIVE)
    // ========================================================
    function toggleProductStatus(productId, button) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        button.disabled = true;
        const origHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 0.7rem;"></i>';

        fetch(`/products/${productId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            button.disabled = false;
            if (data.success) {
                const isActive = (data.status === 'active');
                button.className = `status-toggle-btn ${isActive ? 'status-active' : 'status-inactive'}`;
                button.innerHTML = `
                    <span class="status-toggle-dot"></span>
                    <span class="status-toggle-text">${data.status_label}</span>
                `;

                if (typeof Toast !== 'undefined') {
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                }
            } else {
                button.innerHTML = origHtml;
                Swal.fire('Erreur', data.message || 'Impossible de mettre à jour le statut.', 'error');
            }
        })
        .catch(err => {
            button.disabled = false;
            button.innerHTML = origHtml;
            console.error('Toggle status error:', err);
            Swal.fire('Erreur', 'Erreur de connexion au serveur.', 'error');
        });
    }

    // Effacer la recherche rapidement
    function clearSearch() {
        const input = document.getElementById('productSearchInput');
        if (input) {
            input.value = '';
            document.getElementById('filterForm').submit();
        }
    }

    // ========================================================
    // STUDIO 4K IMAGE FINDER MODAL
    // ========================================================
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
                        <p style="font-size: 13px;">Essayez de simplifier le nom avec la marque et le modèle.</p>
                    </div>
                `;
                return;
            }

            data.candidates.forEach((cand) => {
                const card = document.createElement('div');
                card.style.cssText = "background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);";
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
                        <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(15,23,42,0.75); color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px;">
                            ${cand.source}
                        </span>
                    </div>
                    <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px; background: #ffffff; border-top: 1px solid #f1f5f9;">
                        <button type="button" onclick="applySelected4kImage('${cand.url.replace(/'/g, "\\'")}', this)" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 9px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
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

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

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
</script>
@endpush
