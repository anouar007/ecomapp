@extends('layouts.app')

@section('title', 'Modifier la catégorie : ' . $category->name)

@section('content')
<div class="categories-container">
    <!-- Fil d'Ariane -->
    <nav class="cat-breadcrumb-nav" aria-label="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fas fa-home me-1"></i>Accueil</a>
        <span class="text-muted">/</span>
        <a href="{{ route('categories.index') }}">Catégories</a>
        <span class="text-muted">/</span>
        <span class="text-dark fw-bold text-truncate" style="max-width: 150px;">{{ $category->name }}</span>
    </nav>

    <!-- En-tête de page exécutif -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5 min-w-0">
            <div class="brand-header-icon flex-shrink-0">
                @if($category->icon)
                    <i class="{{ $category->icon }}"></i>
                @else
                    <i class="fas fa-folder"></i>
                @endif
            </div>
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="brand-title text-truncate" id="pageHeaderTitle">{{ $category->name }}</h1>
                    @php $isActive = in_array(strtolower($category->status), ['active', 'actif']); @endphp
                    <span class="brand-badge {{ $isActive ? 'success' : 'danger' }}" id="headerStatusBadge">
                        <span class="badge-dot" style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
                        {{ $isActive ? 'Actif' : 'Inactif' }}
                    </span>
                    <span class="badge bg-light text-secondary border font-monospace d-none d-sm-inline-flex" style="font-size: 0.7rem;">#{{ $category->id }}</span>
                </div>
                <p class="brand-subtitle">Classification, hiérarchie et visuels du catalogue</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('categories.index') }}" class="btn-brand-outline flex-grow-1 flex-sm-grow-0 justify-content-center">
                <i class="fas fa-arrow-left me-1"></i> <span>Retour</span>
            </a>
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" target="_blank" class="btn-brand-light d-none d-md-inline-flex" title="Voir sur la boutique en direct">
                <i class="fas fa-external-link-alt text-danger me-1"></i> <span>Boutique</span>
            </a>
            <button type="submit" form="categoryEditForm" id="btn-save-category-top" class="btn-brand-primary flex-grow-1 flex-sm-grow-0 justify-content-center">
                <i class="fas fa-check me-1"></i> <span>Enregistrer</span>
            </button>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 d-flex align-items-start gap-2.5 p-3">
            <i class="fas fa-exclamation-circle text-danger fs-5 mt-0.5 flex-shrink-0"></i>
            <div>
                <strong class="d-block mb-1 small">Veuillez corriger les erreurs suivantes :</strong>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Formulaire Principal -->
    <form id="categoryEditForm" action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3 g-lg-4">
            <!-- COLONNE GAUCHE (Contenu Principal) -->
            <div class="col-12 col-lg-8">
                <!-- CARTE 1 : Informations Générales -->
                <div class="cat-edit-card">
                    <div class="cat-card-header">
                        <h2 class="cat-card-title">
                            <i class="fas fa-pen-to-square text-danger"></i>
                            <span>Informations Générales</span>
                        </h2>
                        <span class="text-muted small" style="font-size: 0.72rem;">* Champs requis</span>
                    </div>

                    <div class="row g-3">
                        <!-- Nom -->
                        <div class="col-12">
                            <label for="name" class="form-label fw-bold small text-dark mb-1">
                                Nom de la catégorie <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control form-control-lg fs-6" 
                                   value="{{ old('name', $category->name) }}" 
                                   placeholder="ex: Caméras Cinéma, Objectifs Hybrides..." 
                                   required 
                                   autofocus
                                   oninput="updateLivePreviewTitle(this.value)">
                        </div>

                        <!-- Slug / URL de la boutique -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label for="slug" class="form-label fw-bold small text-dark mb-0">
                                    Identifiant d'URL (Slug)
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-muted" id="btnToggleSlugLock" onclick="toggleSlugLock()" title="Déverrouiller pour modifier le slug">
                                        <i class="fas fa-lock me-1 text-secondary" id="slugLockIcon"></i> <span id="slugLockText" style="font-size: 0.72rem;">Verrouillé</span>
                                    </button>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted font-monospace small px-2.5">/</span>
                                <input type="text" 
                                       id="slug" 
                                       name="slug" 
                                       class="form-control font-monospace border-start-0" 
                                       value="{{ old('slug', $category->slug) }}" 
                                       placeholder="cameras-cinema"
                                       readonly
                                       oninput="updateLivePreviewSlug(this.value)">
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-1 text-muted" style="font-size: 0.72rem;">
                                <span class="text-truncate font-monospace" style="max-width: 260px;">
                                    <i class="fas fa-link me-1 text-danger"></i>{{ url('/shop?category=') }}<strong id="slugUrlPreview">{{ $category->slug }}</strong>
                                </span>
                                <button type="button" class="btn btn-sm btn-link p-0 text-danger text-decoration-none fw-semibold" onclick="copyCategoryUrl()">
                                    <i class="fas fa-copy me-1"></i>Copier l'URL
                                </button>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label for="description" class="form-label fw-bold small text-dark mb-0">Description commerciale & SEO</label>
                                <span id="descCharCount" class="text-muted" style="font-size: 0.7rem;">{{ strlen($category->description ?? '') }} caractères</span>
                            </div>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Présentez les spécificités de cette catégorie pour vos clients et le référencement..."
                                      oninput="updateDescCharCount(this.value)">{{ old('description', $category->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- CARTE 2 : Identité Visuelle & Médias -->
                <div class="cat-edit-card">
                    <div class="cat-card-header">
                        <h2 class="cat-card-title">
                            <i class="fas fa-palette text-danger"></i>
                            <span>Image & Identité Visuelle</span>
                        </h2>
                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.68rem;">PNG, JPG, WEBP &bull; Max 2Mo</span>
                    </div>

                    <div class="row g-3">
                        <!-- Image Actuelle & Téléversement -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark mb-1">Image de présentation</label>
                            
                            @if($category->image)
                                <div class="p-2.5 p-sm-3 bg-light rounded-3 border mb-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
                                        <div class="d-flex align-items-center gap-2.5 min-w-0">
                                            <img id="currentImageThumb" 
                                                 src="{{ $category->thumbnail }}" 
                                                 alt="{{ $category->name }}" 
                                                 style="width: 58px; height: 58px; object-fit: cover; border-radius: 10px; border: 1.5px solid #e2e8f0; flex-shrink: 0;"
                                                 onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                            <div class="min-w-0">
                                                <div class="fw-bold small text-dark text-truncate">Image actuelle</div>
                                                <div class="text-muted text-truncate font-monospace" style="font-size: 0.68rem; max-width: 180px;" title="{{ $category->image }}">{{ basename($category->image) }}</div>
                                                <a href="{{ $category->thumbnail }}" target="_blank" class="small text-danger text-decoration-none" style="font-size: 0.7rem;">
                                                    <i class="fas fa-external-link-alt me-0.5"></i> Plein écran
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1" onchange="toggleRemoveImageState(this.checked)" style="cursor: pointer;">
                                            <label class="form-check-label text-danger fw-semibold small" for="remove_image" style="cursor: pointer; font-size: 0.78rem;">
                                                <i class="fas fa-trash-alt me-1"></i> Supprimer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="cat-upload-dropzone" id="imageUploadArea" onclick="document.getElementById('image').click()">
                                <input type="file" 
                                       id="image" 
                                       name="image" 
                                       class="d-none" 
                                       accept="image/*"
                                       onchange="handleImageSelection(this)">
                                
                                <div id="imagePreview" style="display: none;">
                                    <div class="d-flex flex-column align-items-center">
                                        <img id="previewImg" src="" alt="Aperçu" style="display: none; max-width: 200px; max-height: 140px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                        <div class="d-flex align-items-center gap-2 mt-2">
                                            <span class="badge bg-success" style="font-size: 0.72rem;"><i class="fas fa-check me-1"></i> Fichier sélectionné</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSelectedImage(event)">
                                                <i class="fas fa-times me-1"></i> Annuler
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="uploadPlaceholder">
                                    <div class="brand-avatar mx-auto mb-2" style="width: 46px; height: 46px; background: rgba(220, 38, 38, 0.08); color: #dc2626;">
                                        <i class="fas fa-cloud-arrow-up fs-5"></i>
                                    </div>
                                    <p class="mb-1 text-dark fw-bold small">{{ $category->image ? 'Choisir une nouvelle image pour la remplacer' : 'Cliquez ou glissez une image ici' }}</p>
                                    <small class="text-muted" style="font-size: 0.7rem;">Ratio 4:3 conseillé &bull; Max 2 Mo</small>
                                </div>
                            </div>
                        </div>

                        <!-- Icône FontAwesome -->
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold small text-dark mb-0">Icône vectorielle</label>
                                <span id="iconBadgePreview" class="badge bg-light text-danger border fs-6 px-2 py-1">
                                    <i id="previewIcon" class="{{ old('icon', $category->icon ?: 'fas fa-folder') }}"></i>
                                </span>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light"><i class="fas fa-icons text-muted"></i></span>
                                <input type="text" 
                                       id="icon" 
                                       name="icon" 
                                       class="form-control font-monospace" 
                                       value="{{ old('icon', $category->icon) }}" 
                                       placeholder="ex: fas fa-camera"
                                       oninput="updateCustomIcon(this.value)">
                            </div>
                            <small class="text-muted d-block mb-2" style="font-size: 0.7rem;">Cliquez sur une icône populaire :</small>
                            
                            <div class="icon-picker-grid">
                                @php
                                    $suggestedIcons = [
                                        'fas fa-camera', 'fas fa-video', 'fas fa-circle-notch', 'fas fa-lightbulb',
                                        'fas fa-microphone', 'fas fa-podcast', 'fas fa-hand-holding', 'fas fa-briefcase',
                                        'fas fa-grip-lines-vertical', 'fas fa-battery-three-quarters', 'fas fa-sd-card', 'fas fa-tools',
                                        'fas fa-box', 'fas fa-laptop', 'fas fa-mobile-alt', 'fas fa-sliders-h',
                                        'fas fa-spray-can', 'fas fa-globe', 'fas fa-tv', 'fas fa-headphones',
                                        'fas fa-tag', 'fas fa-star', 'fas fa-gem', 'fas fa-shield-alt'
                                    ];
                                @endphp
                                @foreach($suggestedIcons as $iconClass)
                                    <div class="icon-choice-btn {{ old('icon', $category->icon) == $iconClass ? 'selected' : '' }}" 
                                         onclick="pickIcon('{{ $iconClass }}')" 
                                         title="{{ $iconClass }}">
                                        <i class="{{ $iconClass }}"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARTE 3 : Sous-catégories rattachées -->
                <div class="cat-edit-card">
                    <div class="cat-card-header">
                        <h2 class="cat-card-title">
                            <i class="fas fa-sitemap text-danger"></i>
                            <span>Sous-catégories rattachées</span>
                        </h2>
                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.72rem;">
                            {{ $category->children->count() }}
                        </span>
                    </div>

                    @if($category->children->count() > 0)
                        <div class="cat-children-list">
                            @foreach($category->children as $child)
                                <div class="cat-child-item">
                                    <div class="d-flex align-items-center gap-2.5 min-w-0">
                                        <div class="brand-avatar" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                            @if($child->image)
                                                <img src="{{ $child->thumbnail }}" alt="{{ $child->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                            @elseif($child->icon)
                                                <i class="{{ $child->icon }}"></i>
                                            @else
                                                <i class="fas fa-folder"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark text-truncate small">{{ $child->name }}</div>
                                            <span class="badge bg-white text-muted border font-monospace" style="font-size: 0.65rem;">{{ $child->slug }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <span class="badge bg-light text-secondary small d-none d-sm-inline-flex">
                                            {{ $child->products()->count() }} prod.
                                        </span>
                                        <a href="{{ route('categories.edit', $child) }}" class="action-btn action-btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 bg-light rounded-3 border">
                            <div class="brand-avatar mx-auto mb-2" style="width: 40px; height: 40px; color: #94a3b8; font-size: 0.9rem;">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0.5" style="font-size: 0.82rem;">Aucune sous-catégorie</h6>
                            <p class="text-muted small mb-2" style="font-size: 0.72rem;">Cette catégorie est au premier niveau.</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; font-size: 0.75rem; padding: 0.25rem 0.65rem;">
                                <i class="fas fa-plus me-1"></i> Créer une sous-catégorie
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- COLONNE DROITE (Paramètres & Métriques) -->
            <div class="col-12 col-lg-4">
                <!-- SIDEBAR CARTE 1 : Statut & Hiérarchie -->
                <div class="cat-edit-card">
                    <div class="cat-card-header">
                        <h2 class="cat-card-title">
                            <i class="fas fa-toggle-on text-danger"></i>
                            <span>Statut & Hiérarchie</span>
                        </h2>
                    </div>

                    <!-- Statut Interactif -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark mb-1">Visibilité sur la boutique</label>
                        <input type="hidden" name="status" id="statusInput" value="{{ old('status', $category->status) }}">
                        <div class="cat-status-selector">
                            <div class="cat-status-option {{ old('status', $category->status) === 'active' ? 'active-option' : '' }}" 
                                 id="optStatusActive" 
                                 onclick="setStatusOption('active')">
                                <div class="fw-bold text-success small mb-0.5"><i class="fas fa-check-circle me-1"></i>Actif</div>
                                <div class="text-muted" style="font-size: 0.65rem;">Visible en ligne</div>
                            </div>
                            <div class="cat-status-option {{ old('status', $category->status) === 'inactive' ? 'inactive-option' : '' }}" 
                                 id="optStatusInactive" 
                                 onclick="setStatusOption('inactive')">
                                <div class="fw-bold text-danger small mb-0.5"><i class="fas fa-eye-slash me-1"></i>Inactif</div>
                                <div class="text-muted" style="font-size: 0.65rem;">Masqué du shop</div>
                            </div>
                        </div>
                    </div>

                    <!-- Catégorie Parente -->
                    <div class="mb-3">
                        <label for="parent_id" class="form-label fw-bold small text-dark mb-1">Catégorie parente</label>
                        <select id="parent_id" name="parent_id" class="form-select" onchange="updateLivePreviewParent(this)">
                            <option value="">-- Catégorie principale (Racine) --</option>
                            @foreach($parentCategories as $parent)
                                @if($parent->id !== $category->id)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.7rem;">Niveau hiérarchique.</small>
                    </div>

                    <!-- Ordre d'affichage -->
                    <div>
                        <label for="sort_order" class="form-label fw-bold small text-dark mb-1">Ordre de priorité (Tri)</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" onclick="adjustSortOrder(-1)"><i class="fas fa-minus"></i></button>
                            <input type="number" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   class="form-control text-center font-monospace" 
                                   value="{{ old('sort_order', $category->sort_order ?? 0) }}" 
                                   min="0">
                            <button type="button" class="btn btn-outline-secondary" onclick="adjustSortOrder(1)"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR CARTE 2 : Aperçu Visuel (Seulement sur grand écran, masqué sur mobile) -->
                <div class="cat-edit-card d-none d-lg-block">
                    <div class="cat-card-header">
                        <h2 class="cat-card-title">
                            <i class="fas fa-eye text-danger"></i>
                            <span>Aperçu Catalogue</span>
                        </h2>
                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.65rem;">En direct</span>
                    </div>

                    <div class="cat-live-preview">
                        <!-- Parent Badge in preview -->
                        <div id="livePreviewParentBadge" class="mobile-cat-parent-badge" style="{{ $category->parent_id ? 'display: inline-flex;' : 'display: none;' }}">
                            <i class="fas fa-level-up-alt fa-rotate-90 text-danger me-1"></i>
                            <span>Sous-catégorie de : <strong id="livePreviewParentName">{{ $category->parent?->name ?? 'Racine' }}</strong></span>
                        </div>

                        <!-- Card Header Mock -->
                        <div class="mobile-cat-header">
                            <div class="mobile-cat-main">
                                <div class="mobile-cat-avatar">
                                    <img id="livePreviewThumb" 
                                         src="{{ $category->thumbnail }}" 
                                         alt="{{ $category->name }}" 
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                </div>
                                <div class="min-w-0">
                                    <div class="mobile-cat-title text-truncate" id="livePreviewName">{{ $category->name }}</div>
                                    <span class="mobile-cat-slug" id="livePreviewSlugBadge">{{ $category->slug }}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="brand-badge {{ $isActive ? 'success' : 'danger' }}" id="livePreviewStatusBadge">
                                    <span class="badge-dot" style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
                                    <span id="livePreviewStatusText">{{ $isActive ? 'Actif' : 'Inactif' }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Card Description Mock -->
                        <p class="mobile-cat-desc" id="livePreviewDesc">{{ $category->description ?: 'Aucune description saisie.' }}</p>

                        <!-- Card Pills Mock -->
                        <div class="mobile-cat-meta">
                            <span class="mobile-cat-pill">
                                <i class="fas fa-box text-primary"></i>
                                <span><strong>{{ $category->products_count }}</strong> produits</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR CARTE 3 : Métriques & Historique -->
                <div class="cat-edit-card">
                    <div class="cat-card-header">
                        <h2 class="cat-card-title">
                            <i class="fas fa-chart-pie text-danger"></i>
                            <span>Métriques & Historique</span>
                        </h2>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2.5 bg-light rounded-3 mb-2.5 border">
                        <div>
                            <div class="text-muted small" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 700;">Articles Classés</div>
                            <div class="fw-bold text-dark fs-5 font-monospace" style="line-height: 1.1;">{{ $category->products_count }}</div>
                        </div>
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; font-weight: 600; font-size: 0.75rem; padding: 0.35rem 0.65rem;">
                            <i class="fas fa-box-open me-1"></i> Voir stock
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-1.5" style="font-size: 0.75rem;">
                        <div class="d-flex justify-content-between text-muted border-bottom pb-1">
                            <span>Créée le :</span>
                            <span class="fw-semibold text-dark">{{ $category->created_at ? $category->created_at->format('d/m/Y à H:i') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted border-bottom pb-1">
                            <span>Modifiée le :</span>
                            <span class="fw-semibold text-dark">{{ $category->updated_at ? $category->updated_at->format('d/m/Y à H:i') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span>Lien boutique :</span>
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" target="_blank" class="fw-semibold text-danger text-decoration-none text-truncate" style="max-width: 150px;">
                                /shop?category={{ $category->slug }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR CARTE 4 : Zone de Sécurité / Suppression -->
                <div class="cat-edit-card border-danger-subtle" style="background: #fffdfd;">
                    <div class="cat-card-header border-danger-subtle">
                        <h2 class="cat-card-title text-danger">
                            <i class="fas fa-shield-halved text-danger"></i>
                            <span>Gestion Critique</span>
                        </h2>
                    </div>

                    @if($category->products_count == 0 && $category->children->count() == 0)
                        <p class="text-muted small mb-2.5" style="font-size: 0.72rem;">
                            Cette catégorie est vide. Elle peut être supprimée en toute sécurité.
                        </p>
                        <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1.5 d-flex align-items-center justify-content-center gap-1.5" style="border-radius: 8px; font-weight: 600;" onclick="submitDeleteCategory()">
                            <i class="fas fa-trash-alt"></i>
                            <span>Supprimer la catégorie</span>
                        </button>
                    @else
                        <div class="d-flex align-items-start gap-2 text-muted small" style="font-size: 0.72rem;">
                            <i class="fas fa-lock text-warning fs-6 mt-0.5 flex-shrink-0"></i>
                            <div>
                                <strong class="text-dark d-block">Suppression verrouillée</strong>
                                Contient <strong>{{ $category->products_count }}</strong> produit(s) et <strong>{{ $category->children->count() }}</strong> sous-catégorie(s).
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- BARRE D'ACTIONS AU BAS DU FORMULAIRE (Non-sticky, naturelle et fluide) -->
        <div class="cat-form-footer">
            <div class="d-none d-md-flex align-items-center gap-2 text-muted small" style="font-size: 0.75rem;">
                <i class="fas fa-keyboard text-secondary"></i>
                <span><strong>Ctrl + S</strong> ou <strong>Cmd + S</strong> pour enregistrer rapidement.</span>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto justify-content-end">
                <a href="{{ route('categories.index') }}" class="btn-brand-light flex-grow-1 flex-md-grow-0 justify-content-center">
                    <i class="fas fa-times me-1"></i> Annuler
                </a>
                <button type="submit" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center">
                    <i class="fas fa-save me-1"></i> Mettre à jour la catégorie
                </button>
            </div>
        </div>
    </form>

    <!-- Formulaire de Suppression Standalone (En dehors de categoryEditForm) -->
    <form id="deleteCategoryForm" method="POST" action="{{ route('categories.destroy', $category->id) }}" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
// Toggle slug lock
let isSlugLocked = true;
function toggleSlugLock() {
    isSlugLocked = !isSlugLocked;
    const slugInput = document.getElementById('slug');
    const lockIcon = document.getElementById('slugLockIcon');
    const lockText = document.getElementById('slugLockText');

    if (isSlugLocked) {
        slugInput.setAttribute('readonly', 'readonly');
        lockIcon.className = 'fas fa-lock me-1 text-secondary';
        lockText.innerText = 'Verrouillé';
    } else {
        slugInput.removeAttribute('readonly');
        lockIcon.className = 'fas fa-lock-open me-1 text-danger';
        lockText.innerText = 'Déverrouillé';
        slugInput.focus();
    }
}

// Copy Category URL
function copyCategoryUrl() {
    const slug = document.getElementById('slug').value;
    const url = "{{ url('/shop?category=') }}" + slug;
    navigator.clipboard.writeText(url).then(() => {
        if (typeof Toast !== 'undefined') {
            Toast.fire({
                icon: 'success',
                title: 'URL copiée dans le presse-papier !'
            });
        } else {
            alert('URL copiée : ' + url);
        }
    }).catch(err => {
        console.error('Erreur copie:', err);
    });
}

// Interactive Status Option
function setStatusOption(status) {
    document.getElementById('statusInput').value = status;
    const optActive = document.getElementById('optStatusActive');
    const optInactive = document.getElementById('optStatusInactive');
    const liveBadge = document.getElementById('livePreviewStatusBadge');
    const liveText = document.getElementById('livePreviewStatusText');
    const headerBadge = document.getElementById('headerStatusBadge');

    if (status === 'active') {
        optActive.classList.add('active-option');
        optInactive.classList.remove('inactive-option');
        if (liveBadge) liveBadge.className = 'brand-badge success';
        if (liveText) liveText.innerText = 'Actif';
        if (headerBadge) {
            headerBadge.className = 'brand-badge success';
            headerBadge.innerHTML = '<span class="badge-dot" style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span> Actif';
        }
    } else {
        optActive.classList.remove('active-option');
        optInactive.classList.add('inactive-option');
        if (liveBadge) liveBadge.className = 'brand-badge danger';
        if (liveText) liveText.innerText = 'Inactif';
        if (headerBadge) {
            headerBadge.className = 'brand-badge danger';
            headerBadge.innerHTML = '<span class="badge-dot" style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span> Inactif';
        }
    }
}

// Sort order adjust buttons
function adjustSortOrder(delta) {
    const input = document.getElementById('sort_order');
    let val = parseInt(input.value) || 0;
    val = Math.max(0, val + delta);
    input.value = val;
}

// Live update of title
function updateLivePreviewTitle(val) {
    const titleText = val.trim() || 'Sans titre';
    document.getElementById('pageHeaderTitle').innerText = titleText;
    const previewName = document.getElementById('livePreviewName');
    if (previewName) previewName.innerText = titleText;
}

// Live update of slug
function updateLivePreviewSlug(val) {
    const slugVal = val.trim() || 'slug';
    const previewSlug = document.getElementById('livePreviewSlugBadge');
    const previewUrl = document.getElementById('slugUrlPreview');
    if (previewSlug) previewSlug.innerText = slugVal;
    if (previewUrl) previewUrl.innerText = slugVal;
}

// Live update of description character count
function updateDescCharCount(val) {
    document.getElementById('descCharCount').innerText = val.length + ' caractères';
    const previewDesc = document.getElementById('livePreviewDesc');
    if (previewDesc) previewDesc.innerText = val.trim() || 'Aucune description saisie.';
}

// Live update of parent category badge
function updateLivePreviewParent(select) {
    const badge = document.getElementById('livePreviewParentBadge');
    const nameEl = document.getElementById('livePreviewParentName');
    if (badge && nameEl) {
        if (select.value) {
            const selectedText = select.options[select.selectedIndex].text.trim();
            nameEl.innerText = selectedText;
            badge.style.display = 'inline-flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Icon selection
function pickIcon(iconClass) {
    document.getElementById('icon').value = iconClass;
    updateCustomIcon(iconClass);
    document.querySelectorAll('.icon-choice-btn').forEach(el => el.classList.remove('selected'));
    if (window.event && window.event.currentTarget) {
        window.event.currentTarget.classList.add('selected');
    }
}

function updateCustomIcon(iconClass) {
    const cls = iconClass.trim() || 'fas fa-folder';
    const preview = document.getElementById('previewIcon');
    if (preview) preview.className = cls;
}

// Image handling
function handleImageSelection(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const liveThumb = document.getElementById('livePreviewThumb');
    const placeholder = document.getElementById('uploadPlaceholder');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            if (liveThumb) liveThumb.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeSelectedImage(e) {
    e.stopPropagation();
    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    const liveThumb = document.getElementById('livePreviewThumb');
    const currentImg = document.getElementById('currentImageThumb');

    input.value = '';
    previewImg.src = '';
    previewImg.style.display = 'none';
    preview.style.display = 'none';
    placeholder.style.display = 'block';

    if (liveThumb && currentImg) {
        liveThumb.src = currentImg.src;
    }
}

function toggleRemoveImageState(isRemoved) {
    const currentImg = document.getElementById('currentImageThumb');
    const liveThumb = document.getElementById('livePreviewThumb');
    if (currentImg) {
        currentImg.style.opacity = isRemoved ? '0.3' : '1';
        currentImg.style.filter = isRemoved ? 'grayscale(100%)' : 'none';
    }
    if (liveThumb && isRemoved) {
        liveThumb.style.opacity = '0.3';
    } else if (liveThumb) {
        liveThumb.style.opacity = '1';
    }
}

// Keyboard shortcut: Ctrl + S or Cmd + S to save
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
        e.preventDefault();
        const form = document.getElementById('categoryEditForm');
        if (form) {
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }
    }
});

// Top save button listener
document.getElementById('btn-save-category-top')?.addEventListener('click', function(e) {
    const form = document.getElementById('categoryEditForm');
    if (form) {
        e.preventDefault();
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }
    }
});

// Delete confirmation handler
function submitDeleteCategory() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: 'Voulez-vous vraiment supprimer cette catégorie ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteCategoryForm').submit();
            }
        });
    } else {
        if (confirm('Voulez-vous vraiment supprimer cette catégorie ?')) {
            document.getElementById('deleteCategoryForm').submit();
        }
    }
}
</script>
@endpush
@endsection
