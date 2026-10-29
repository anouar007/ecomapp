@extends('layouts.app')

@section('title', 'Tags & Badges Produits')

@section('content')
<style>
    .tag-preview-stage {
        width: 195px;
        height: 48px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px dashed #cbd5e1;
        border-radius: 8px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        container-type: inline-size;
        position: relative;
        overflow: hidden;
        padding: 4px 8px;
    }

    .tag-swatch-dot {
        width: 12px;
        height: 12px;
        border-radius: 50% !important;
        display: inline-block;
        border: 1px solid rgba(0, 0, 0, 0.12);
        vertical-align: middle;
    }

    .tag-pos-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px !important;
        font-size: 0.76rem;
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="categories-container">
    <!-- En-tête de page aligné sur le design global -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <h1 class="brand-title">Tags & Badges Produits</h1>
                <p class="brand-subtitle">Créez et personnalisez vos badges (« NEW », « TOP SELLER », « PROMO »...) avec taille proportionnelle à la carte produit</p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            @if(isset($discountTag))
                <a href="{{ route('product-tags.edit', $discountTag) }}" class="btn-brand-outline" title="Personnaliser le style et la taille du badge de réduction automatique (−%)">
                    <i class="fas fa-percent text-danger me-1"></i> <span>Style du Badge −% (Auto)</span>
                </a>
            @endif
            <a href="{{ route('product-tags.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus"></i> <span>Nouveau Tag / Badge</span>
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

    <!-- Pro KPI Metric Cards Grid (Identique au design Marques & Produits) -->
    <div class="stats-grid-pro mb-4">
        <!-- KPI 1 : Total des tags -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-red">
                    <i class="fas fa-bolt"></i>
                </div>
                <span class="stat-trend-badge trend-up">
                    <i class="fas fa-layer-group"></i> Total
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Total des tags</div>
                <div class="stat-value-pro">{{ $totalTags ?? 0 }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">Badges configurés dans le studio</span>
            </div>
        </div>

        <!-- KPI 2 : Tags Actifs -->
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
                <div class="stat-label-pro">Tags actifs</div>
                <div class="stat-value-pro">{{ $activeTags ?? 0 }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">
                    {{ ($inactiveTags ?? 0) > 0 ? ($inactiveTags . ' inactif(s)') : 'Visibles sur la boutique' }}
                </span>
            </div>
        </div>

        <!-- KPI 3 : Tags utilisés -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-indigo">
                    <i class="fas fa-tags"></i>
                </div>
                <span class="stat-trend-badge trend-up">
                    <i class="fas fa-link"></i> Assignés
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Tags utilisés</div>
                <div class="stat-value-pro">{{ $tagsWithProducts ?? 0 }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">Associés à au moins 1 produit</span>
            </div>
        </div>

        <!-- KPI 4 : Produits badgés -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-amber">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <span class="stat-trend-badge trend-up">
                    <i class="fas fa-expand-arrows-alt"></i> Auto-scale
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Produits badgés</div>
                <div class="stat-value-pro">{{ $taggedProductsCount ?? 0 }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">Affichant un badge sur leurs cartes</span>
            </div>
        </div>
    </div>

    <!-- Barre de filtres & Recherche Pro -->
    <div class="brand-filter-bar mb-4">
        <form action="{{ route('product-tags.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <div class="brand-search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Rechercher un tag par nom ou identifiant (ex: NEW, TOP SELLER, PROMO)...">
                </div>
            </div>

            <div class="col-6 col-md-auto">
                <select name="position" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes les positions</option>
                    <option value="top-left" {{ request('position') === 'top-left' ? 'selected' : '' }}>↖ Haut Gauche</option>
                    <option value="top-right" {{ request('position') === 'top-right' ? 'selected' : '' }}>↗ Haut Droite</option>
                    <option value="bottom-left" {{ request('position') === 'bottom-left' ? 'selected' : '' }}>↙ Bas Gauche</option>
                    <option value="bottom-right" {{ request('position') === 'bottom-right' ? 'selected' : '' }}>↘ Bas Droite</option>
                </select>
            </div>

            <div class="col-6 col-md-auto">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs uniquement</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs uniquement</option>
                </select>
            </div>

            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn-brand-primary flex-grow-1 flex-md-grow-0">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                @if(request()->filled('search') || request()->filled('status') || request()->filled('position'))
                    <a href="{{ route('product-tags.index') }}" class="btn-brand-light" title="Réinitialiser les filtres">
                        <i class="fas fa-times me-1"></i> Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    @php
        $isFiltered = request()->filled('search') || request()->filled('status') || request()->filled('position');
        $hasItems = $tags->isNotEmpty();
    @endphp

    @if(!$hasItems)
        <!-- Empty State Pro -->
        <div class="brand-table-card">
            <div class="empty-state-pro py-5 text-center">
                <div class="empty-state-icon mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 70px; height: 70px; font-size: 1.8rem; color: #dc3545;">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="empty-state-title fw-bold text-dark mb-1">Aucun tag ou badge trouvé</h3>
                <p class="empty-state-desc text-muted mb-3" style="max-width: 440px; margin: 0 auto;">
                    @if($isFiltered)
                        Aucun badge ne correspond à vos critères de recherche actuels.
                    @else
                        Vous n'avez pas encore créé de tags promotionnels ou informatifs pour vos cartes produits.
                    @endif
                </p>
                <div>
                    @if($isFiltered)
                        <a href="{{ route('product-tags.index') }}" class="btn-brand-light me-2">
                            <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                        </a>
                    @endif
                    <a href="{{ route('product-tags.create') }}" class="btn-brand-primary">
                        <i class="fas fa-plus me-1"></i> Créer un Tag / Badge
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Tableau Desktop (Masqué sur mobile) -->
        <div class="brand-table-card d-none d-md-block shadow-sm rounded-3 overflow-hidden bg-white mb-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3" style="width: 220px;">Aperçu (Échelle Carte)</th>
                            <th class="py-3">Nom & Identifiant</th>
                            <th class="py-3">Style & Géométrie</th>
                            <th class="py-3" style="width: 150px;">Position</th>
                            <th class="py-3 text-center" style="width: 130px;">Taille Relative</th>
                            <th class="py-3 text-center" style="width: 140px;">Produits liés</th>
                            <th class="py-3 text-center" style="width: 90px;">Ordre</th>
                            <th class="py-3 text-center" style="width: 120px;">Statut</th>
                            <th class="pe-3 py-3 text-end" style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($tags as $tag)
                        <tr class="brand-row hover-bg-light" style="{{ $tag->is_discount_tag ? 'background: rgba(254, 242, 242, 0.45);' : '' }}">
                            <!-- Live Scaled Badge Preview -->
                            <td class="ps-3">
                                <div class="tag-preview-stage" title="Aperçu proportionnel calculé pour une carte de 195px">
                                    <span class="product-card-tag-badge {{ $tag->pulse_animation ? 'tag-pulse' : '' }}" style="{{ $tag->inline_style }}">
                                        @if(!empty($tag->icon))
                                            <i class="{{ $tag->icon }}"></i>
                                        @endif
                                        <span>{{ $tag->is_discount_tag ? $tag->formatDiscountLabel(25) : $tag->name }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Name & Slug -->
                            <td>
                                <div class="fw-bold text-dark d-flex align-items-center gap-1.5 flex-wrap">
                                    <span>{{ $tag->is_discount_tag ? 'Badge Réduction Auto (' . $tag->name . ')' : $tag->name }}</span>
                                    @if($tag->is_discount_tag)
                                        <span class="badge bg-danger text-white small py-0.5 px-2" style="font-size: 0.65rem;">
                                            <i class="fas fa-calculator me-1"></i>Auto-calculé (−%)
                                        </span>
                                    @endif
                                    @if($tag->pulse_animation)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle small py-0.5 px-1.5" style="font-size: 0.65rem;" title="Animation pulsation activée">
                                            <i class="fas fa-wave-square"></i> Animé
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted small font-monospace" style="font-size: 0.76rem;">
                                    {{ $tag->is_discount_tag ? 'Sous les tags • Calculé selon Ancien/Nouveau prix' : $tag->slug }}
                                </div>
                            </td>

                            <!-- Style & Shape -->
                            <td>
                                <div class="d-flex align-items-center gap-1.5 text-dark small">
                                    <span class="tag-swatch-dot" style="background: {{ $tag->bg_color }};"></span>
                                    @if($tag->style_type === 'gradient' && $tag->bg_gradient)
                                        <span class="tag-swatch-dot" style="background: {{ $tag->bg_gradient }};"></span>
                                    @endif
                                    <span>{{ $tag->style_label }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.74rem;">
                                    {{ $tag->shape_label }}
                                </div>
                            </td>

                            <!-- Corner Position -->
                            <td>
                                <span class="tag-pos-badge">
                                    <i class="fas fa-arrows-to-dot text-danger"></i>
                                    <span>{{ $tag->position_label }}</span>
                                </span>
                            </td>

                            <!-- Relative Scale -->
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2.5 py-1 font-monospace">
                                    {{ $tag->size_scale }}%
                                </span>
                                <div class="text-muted" style="font-size: 0.68rem;">Auto-scale CQW</div>
                            </td>

                            <!-- Linked Products -->
                            <td class="text-center">
                                @if($tag->is_discount_tag)
                                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" title="Appliqué automatiquement à tous les produits en promotion">
                                        <i class="fas fa-bolt me-1"></i> Auto ({{ $onSaleProductsCount ?? 0 }})
                                    </span>
                                @elseif($tag->products_count > 0)
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">
                                        <i class="fas fa-box me-1"></i> {{ $tag->products_count }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-muted border px-2.5 py-1">0</span>
                                @endif
                            </td>

                            <!-- Sort Order -->
                            <td class="text-center text-muted small font-monospace">
                                {{ $tag->is_discount_tag ? 'Auto' : $tag->sort_order }}
                            </td>

                            <!-- Status -->
                            <td class="text-center">
                                @if($tag->status === 'active')
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
                                    <a href="{{ route('product-tags.edit', $tag) }}" class="btn btn-outline-secondary" title="Personnaliser le style et la taille">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$tag->is_discount_tag)
                                        <form action="{{ route('product-tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement le tag « {{ addslashes($tag->name) }} » ? Il sera retiré des produits associés.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer le tag">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cartes Mobile (Visible uniquement sur smartphone) -->
        <div class="d-md-none mb-3">
            @foreach($tags as $tag)
            <div class="card border shadow-sm rounded-3 mb-2 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="tag-preview-stage" style="width: 155px; height: 42px;">
                        <span class="product-card-tag-badge {{ $tag->pulse_animation ? 'tag-pulse' : '' }}" style="{{ $tag->inline_style }}">
                            @if(!empty($tag->icon))
                                <i class="{{ $tag->icon }}"></i>
                            @endif
                            <span>{{ $tag->is_discount_tag ? $tag->formatDiscountLabel(25) : $tag->name }}</span>
                        </span>
                    </div>

                    @if($tag->status === 'active')
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.7rem;">Actif</span>
                    @else
                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border px-2 py-1" style="font-size: 0.7rem;">Inactif</span>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap small text-muted">
                        <span class="badge bg-light text-dark border">{{ $tag->position_label }}</span>
                        <span class="badge bg-light text-dark border">{{ $tag->size_scale }}%</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                            <i class="fas fa-box me-1"></i>{{ $tag->products_count }} produit(s)
                        </span>
                    </div>

                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('product-tags.edit', $tag) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('product-tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer le tag « {{ addslashes($tag->name) }} » ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($tags->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $tags->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
