@extends('layouts.app')

@section('title', 'Gestion des catégories')

@section('content')
<div class="categories-container">
    <!-- En-tête de page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-folder-tree"></i>
            </div>
            <div>
                <h1 class="brand-title">Gestion des catégories</h1>
                <p class="brand-subtitle">Structure, hiérarchie et organisation de votre catalogue de produits</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('categories.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus"></i> <span>Nouvelle catégorie</span>
            </a>
            <a href="{{ route('products.index') }}" class="btn-brand-outline d-none d-sm-inline-flex">
                <i class="fas fa-boxes-stacked text-danger"></i> <span>Catalogue produits</span>
            </a>
        </div>
    </div>

    <!-- Metric KPI Stat Cards (Product Section Standard) -->
    <div class="row g-3 mb-4">
        <!-- KPI 1 : Total catégories -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('categories.index') }}" class="stat-kpi-card text-decoration-none {{ !request('status') && !request('search') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total catégories</div>
                        <div class="stat-kpi-value text-dark">{{ $kpiStats['total'] ?? 0 }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">{{ ($kpiStats['subcategories'] ?? 0) > 0 ? ($kpiStats['subcategories'] . ' sous-catégories') : 'Arborescence active' }}</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-folder-tree"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- KPI 2 : Catégories Actives -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('categories.index', ['status' => 'active']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'active' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Catégories actives</div>
                        <div class="stat-kpi-value text-success">{{ $kpiStats['active'] ?? 0 }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i>Visibles sur le shop
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- KPI 3 : Produits Classés -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('products.index') }}" class="stat-kpi-card text-decoration-none">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Produits associés</div>
                        <div class="stat-kpi-value text-primary">{{ number_format($kpiStats['total_products'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">Dans le catalogue Wina</div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- KPI 4 : Sous-catégories -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Sous-catégories</div>
                        <div class="stat-kpi-value text-dark">{{ $kpiStats['subcategories'] ?? 0 }}</div>
                        <div class="stat-kpi-sub text-muted">Hiérarchie secondaire</div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de filtres & Recherche Pro -->
    <div class="brand-filter-bar">
        <form action="{{ route('categories.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <div class="brand-search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" 
                           placeholder="Rechercher par nom, description ou identifiant (slug)...">
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
                    <a href="{{ route('categories.index') }}" class="btn-brand-light" title="Réinitialiser les filtres">
                        <i class="fas fa-times me-1"></i> Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    @php
        $isFiltered = request()->filled('search') || request()->filled('status');
        $rootCategories = $categories->whereNull('parent_id');
        $hasItems = $categories->isNotEmpty();
    @endphp

    @if(!$hasItems)
        <!-- Empty State Pro -->
        <div class="brand-table-card">
            <div class="empty-state-pro">
                <div class="empty-state-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="empty-state-title">Aucune catégorie trouvée</h3>
                <p class="empty-state-desc">
                    @if($isFiltered)
                        Aucune catégorie ne correspond à vos filtres actuels. Essayez de réinitialiser la recherche.
                    @else
                        Vous n'avez pas encore configuré de catégories pour vos produits.
                    @endif
                </p>
                <div>
                    @if($isFiltered)
                        <a href="{{ route('categories.index') }}" class="btn-brand-light me-2">
                            <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                        </a>
                    @endif
                    <a href="{{ route('categories.create') }}" class="btn-brand-primary">
                        <i class="fas fa-plus me-1"></i> Créer une catégorie
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Desktop Table Presentation (Hidden on mobile) -->
        <div class="brand-table-card d-none d-md-block">
            <div class="table-responsive">
                <table class="brand-table">
                    <thead>
                        <tr>
                            <th style="padding-left: 1.5rem;">Catégorie & Arborescence</th>
                            <th>Identifiant (Slug)</th>
                            <th class="text-center">Produits associés</th>
                            <th class="text-center">Ordre</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($isFiltered)
                            {{-- Flat listing of all matching categories when filtered --}}
                            @foreach($categories as $category)
                                @include('categories.partials.category-row', [
                                    'category' => $category, 
                                    'level' => $category->parent_id ? 1 : 0,
                                    'isFiltered' => true
                                ])
                            @endforeach
                        @else
                            {{-- Hierarchical tree listing when browsing normally --}}
                            @foreach($rootCategories as $category)
                                @include('categories.partials.category-row', ['category' => $category, 'level' => 0, 'isFiltered' => false])
                                
                                @foreach($category->children as $child)
                                    @include('categories.partials.category-row', ['category' => $child, 'level' => 1, 'isFiltered' => false])
                                    
                                    @foreach($child->children as $grandchild)
                                        @include('categories.partials.category-row', ['category' => $grandchild, 'level' => 2, 'isFiltered' => false])
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Touch Cards Presentation (Hidden on desktop) -->
        <div class="mobile-category-cards d-block d-md-none">
            @if($isFiltered)
                {{-- Flat listing of matching cards on mobile --}}
                @foreach($categories as $category)
                    @include('categories.partials.mobile-category-card', [
                        'category' => $category, 
                        'level' => $category->parent_id ? 1 : 0,
                        'isFiltered' => true
                    ])
                @endforeach
            @else
                {{-- Hierarchical cards on mobile --}}
                @foreach($rootCategories as $category)
                    @include('categories.partials.mobile-category-card', ['category' => $category, 'level' => 0, 'isFiltered' => false])
                    
                    @foreach($category->children as $child)
                        @include('categories.partials.mobile-category-card', ['category' => $child, 'level' => 1, 'isFiltered' => false])
                        
                        @foreach($child->children as $grandchild)
                            @include('categories.partials.mobile-category-card', ['category' => $grandchild, 'level' => 2, 'isFiltered' => false])
                        @endforeach
                    @endforeach
                @endforeach
            @endif
        </div>
    @endif
</div>
@endsection
