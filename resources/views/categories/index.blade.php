@extends('layouts.app')

@section('title', 'Gestion des catégories')

@section('content')
    <!-- En-tête de page -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-folder-tree"></i>
                </div>
                Gestion des catégories
            </h1>
            <p class="brand-subtitle">Organisez et gérez la hiérarchie et la classification de vos produits</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Créer une catégorie
        </a>
    </div>

    <!-- Barre de recherche -->
    <div class="brand-filter-bar">
        <form action="{{ route('categories.index') }}" method="GET" class="d-flex align-items-center gap-3">
            <div class="brand-search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Rechercher par nom ou description...">
            </div>
            
            <button type="submit" class="btn-brand-primary">
                <i class="fas fa-filter me-1"></i> Rechercher
            </button>
            
            @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn-brand-light" title="Effacer la recherche">
                    <i class="fas fa-times me-1"></i> Effacer
                </a>
            @endif
        </form>
    </div>

    <!-- Tableau des catégories -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Hiérarchie des catégories</th>
                        <th>Lien / Identifiant (Slug)</th>
                        <th class="text-center">Produits associés</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories->where('parent_id', null) as $category)
                        @include('categories.partials.category-row', ['category' => $category, 'level' => 0])
                        
                        @if(!request('search')) {{-- Only show children relationships if not searching --}}
                            @foreach($category->children as $child)
                                @include('categories.partials.category-row', ['category' => $child, 'level' => 1])
                                
                                @foreach($child->children as $grandchild)
                                    @include('categories.partials.category-row', ['category' => $grandchild, 'level' => 2])
                                @endforeach
                            @endforeach
                        @endif
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucune catégorie trouvée</h5>
                                <p class="text-muted">Commencez par créer votre première catégorie de produits.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
