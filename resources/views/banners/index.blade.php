@extends('layouts.app')

@section('title', 'Gestion de l\'Accueil — Slider & Bannières')

@section('content')
<div class="container-fluid px-0">
    {{-- Header --}}
    <div class="brand-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
                Slider & Bannières d'Accueil
            </h1>
            <p class="brand-subtitle mb-0">
                Gérez en temps réel les diapositives du slider héroïque, les 2 encadrés latéraux et la bannière promotionnelle.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i class="fas fa-external-link-alt"></i>
                <span>Voir le site</span>
            </a>
            <button type="button" class="btn-brand-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                <i class="fas fa-plus"></i>
                <span>Ajouter une diapositive</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Veuillez corriger les erreurs suivantes :</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Navigation Tabs --}}
    <ul class="nav nav-pills brand-nav-pills mb-4 gap-2" id="bannerTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 fw-bold d-flex align-items-center gap-2" id="tab-slider-btn" data-bs-toggle="pill" data-bs-target="#tab-slider" type="button" role="tab">
                <i class="fas fa-images"></i>
                <span>1. Slider Principal ({{ $mainHeroSlides->count() }} slides)</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-bold d-flex align-items-center gap-2" id="tab-side-btn" data-bs-toggle="pill" data-bs-target="#tab-side" type="button" role="tab">
                <i class="fas fa-th-large"></i>
                <span>2. Encadrés Latéraux Hero (2 Boîtes)</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-bold d-flex align-items-center gap-2" id="tab-promo-btn" data-bs-toggle="pill" data-bs-target="#tab-promo" type="button" role="tab">
                <i class="fas fa-bullhorn"></i>
                <span>3. Bannière Promo (Large)</span>
            </button>
        </li>
    </ul>

    {{-- Tabs Content --}}
    <div class="tab-content" id="bannerTabsContent">

        {{-- =========================================================================
             TAB 1 : SLIDER PRINCIPAL
             ========================================================================= --}}
        <div class="tab-pane fade show active" id="tab-slider" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Diapositives du Slider Héroïque</h5>
                    <p class="text-muted small mb-0">Ces diapositives défilent automatiquement dans la grande boîte de gauche de la section Héro.</p>
                </div>
                <button type="button" class="btn btn-sm btn-brand-primary" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                    <i class="fas fa-plus me-1"></i> Nouvelle diapositive
                </button>
            </div>

            <div class="row g-3">
                @forelse($mainHeroSlides as $slide)
                <div class="col-md-6 col-xl-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative banner-admin-card">
                        {{-- Visual Preview Banner --}}
                        <div class="banner-preview-box position-relative" style="height: 190px; background: #0b0f19 url('{{ $slide->image_url }}') center/cover no-repeat;">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(11, 15, 25, 0.2) 0%, rgba(11, 15, 25, 0.85) 100%);"></div>
                            
                            {{-- Top Badges --}}
                            <div class="position-absolute top-0 start-0 m-3 d-flex align-items-center gap-2 z-2">
                                @if($slide->badge)
                                    <span class="badge bg-dark bg-opacity-75 text-white border border-secondary border-opacity-50 px-2 py-1 small">
                                        <i class="fas fa-circle text-danger me-1" style="font-size: 8px;"></i> {{ $slide->badge }}
                                    </span>
                                @endif
                                <span class="badge bg-primary bg-opacity-80 px-2 py-1">Ordre #{{ $slide->sort_order }}</span>
                            </div>

                            {{-- Status Badge --}}
                            <div class="position-absolute top-0 end-0 m-3 z-2">
                                @if($slide->status === 'active')
                                    <span class="badge bg-success bg-opacity-90 px-2 py-1"><i class="fas fa-check-circle me-1"></i> Actif</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1"><i class="fas fa-eye-slash me-1"></i> Inactif</span>
                                @endif
                            </div>

                            {{-- Title & Desc on Preview --}}
                            <div class="position-absolute bottom-0 start-0 m-3 text-white z-2 pe-3">
                                <h5 class="fw-bold mb-1 text-truncate" style="max-width: 440px;">{{ $slide->title }}</h5>
                                <p class="small text-white-50 mb-0 text-truncate" style="max-width: 440px;">{{ $slide->description ?: 'Aucune description' }}</p>
                            </div>
                        </div>

                        {{-- Card Details & Actions --}}
                        <div class="card-body p-3 d-flex flex-column justify-content-between bg-white">
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-2">
                                    <span><i class="fas fa-link me-1 text-primary"></i> <strong>Lien :</strong></span>
                                    <span class="text-truncate font-monospace" style="max-width: 280px;" title="{{ $slide->link }}">{{ $slide->link ?: 'Non défini' }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted">
                                    <span><i class="fas fa-image me-1 text-info"></i> <strong>Fichier :</strong></span>
                                    <span class="text-truncate font-monospace" style="max-width: 280px;" title="{{ $slide->image }}">{{ basename($slide->image) }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end gap-2 border-top pt-2">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 edit-slide-btn"
                                        data-id="{{ $slide->id }}"
                                        data-title="{{ $slide->title }}"
                                        data-badge="{{ $slide->badge }}"
                                        data-description="{{ $slide->description }}"
                                        data-link="{{ $slide->link }}"
                                        data-sort_order="{{ $slide->sort_order }}"
                                        data-status="{{ $slide->status }}"
                                        data-image="{{ $slide->image }}"
                                        data-image_url="{{ $slide->image_url }}"
                                        data-action="{{ route('banners.update', $slide->id) }}">
                                    <i class="fas fa-edit"></i>
                                    <span>Modifier</span>
                                </button>

                                <form action="{{ route('banners.destroy', $slide->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette diapositive ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                        <i class="fas fa-trash-alt"></i>
                                        <span>Supprimer</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                        <i class="fas fa-images text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold">Aucune diapositive trouvée</h5>
                        <p class="text-muted small">Créez votre première diapositive pour alimenter le carrousel de la page d'accueil.</p>
                        <div>
                            <button type="button" class="btn btn-brand-primary" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                                <i class="fas fa-plus me-1"></i> Ajouter une diapositive
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>


        {{-- =========================================================================
             TAB 2 : ENCADRÉS LATÉRAUX HERO (SIDE_TOP & SIDE_BOTTOM)
             ========================================================================= --}}
        <div class="tab-pane fade" id="tab-side" role="tabpanel">
            <div class="row g-4">
                {{-- Encadré Supérieur (side_top) --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-danger text-white me-2">Boîte 2 (Haut)</span>
                                <strong class="text-dark">Encadré Supérieur Droit</strong>
                            </div>
                            <span class="badge {{ $sideTop->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $sideTop->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        {{-- Preview Box --}}
                        <div class="position-relative p-3" style="min-height: 180px; background: #0f172a url('{{ $sideTop->image_url }}') center/cover no-repeat;">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0.15) 0%, rgba(15,23,42,0.65) 100%); pointer-events: none;"></div>
                            <span class="badge bg-danger text-white position-relative z-2 fw-bold text-uppercase" style="font-size: 0.75rem;">
                                {{ $sideTop->badge ?: 'GIMBAL 4K COMPACT' }}
                            </span>
                            <div class="position-absolute bottom-0 start-0 m-3 z-2">
                                <span class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #c8102e, #a80c26); font-size: 0.78rem;">
                                    <span>{{ $sideTop->button_text ?: 'Acheter' }}</span>
                                    <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('banners.save-position', 'side_top') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Titre / Référence interne</label>
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $sideTop->title) }}" placeholder="Ex: DJI Osmo Pocket 4">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Tag / Badge supérieur</label>
                                        <input type="text" name="badge" class="form-control" value="{{ old('badge', $sideTop->badge) }}" placeholder="Ex: GIMBAL 4K COMPACT">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Texte du bouton</label>
                                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $sideTop->button_text) }}" placeholder="Ex: Acheter">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Lien de destination</label>
                                        <input type="text" name="link" class="form-control" value="{{ old('link', $sideTop->link) }}" placeholder="Ex: /shop?q=Osmo+Pocket">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Choisir une image existante du catalogue</label>
                                        <select name="image_preset" class="form-select">
                                            <option value="">-- Conserver l'image actuelle ou téléverser --</option>
                                            @foreach($presetImages as $path => $label)
                                                <option value="{{ $path }}" {{ $sideTop->image === $path ? 'selected' : '' }}>{{ $label }} ({{ basename($path) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Ou téléverser une nouvelle image</label>
                                        <input type="file" name="image_file" class="form-control" accept="image/*">
                                        <div class="form-text small">Formats recommandés : JPG, PNG, WEBP. Dimensions idéales : 600x380 px.</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Statut</label>
                                        <select name="status" class="form-select">
                                            <option value="active" {{ $sideTop->status === 'active' ? 'selected' : '' }}>Actif (Affiché)</option>
                                            <option value="inactive" {{ $sideTop->status === 'inactive' ? 'selected' : '' }}>Inactif (Masqué)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn btn-brand-primary w-100">
                                            <i class="fas fa-save me-1"></i> Enregistrer l'encadré supérieur
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Encadré Inférieur (side_bottom) --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-danger text-white me-2">Boîte 3 (Bas)</span>
                                <strong class="text-dark">Encadré Inférieur Droit</strong>
                            </div>
                            <span class="badge {{ $sideBottom->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $sideBottom->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        {{-- Preview Box --}}
                        <div class="position-relative p-3" style="min-height: 180px; background: #0f172a url('{{ $sideBottom->image_url }}') center/cover no-repeat;">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0.15) 0%, rgba(15,23,42,0.65) 100%); pointer-events: none;"></div>
                            <span class="badge bg-danger text-white position-relative z-2 fw-bold text-uppercase" style="font-size: 0.75rem;">
                                {{ $sideBottom->badge ?: 'OUTDOOR FLASH 800W' }}
                            </span>
                            <div class="position-absolute bottom-0 start-0 m-3 z-2">
                                <span class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #c8102e, #a80c26); font-size: 0.78rem;">
                                    <span>{{ $sideBottom->button_text ?: 'Découvrez la GODOX AD800 PRO' }}</span>
                                    <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('banners.save-position', 'side_bottom') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Titre / Référence interne</label>
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $sideBottom->title) }}" placeholder="Ex: GODOX AD800 PRO">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Tag / Badge supérieur</label>
                                        <input type="text" name="badge" class="form-control" value="{{ old('badge', $sideBottom->badge) }}" placeholder="Ex: OUTDOOR FLASH 800W">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Texte du bouton</label>
                                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $sideBottom->button_text) }}" placeholder="Ex: Découvrez la GODOX AD800 PRO">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Lien de destination</label>
                                        <input type="text" name="link" class="form-control" value="{{ old('link', $sideBottom->link) }}" placeholder="Ex: /shop?q=Godox+AD800">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Choisir une image existante du catalogue</label>
                                        <select name="image_preset" class="form-select">
                                            <option value="">-- Conserver l'image actuelle ou téléverser --</option>
                                            @foreach($presetImages as $path => $label)
                                                <option value="{{ $path }}" {{ $sideBottom->image === $path ? 'selected' : '' }}>{{ $label }} ({{ basename($path) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Ou téléverser une nouvelle image</label>
                                        <input type="file" name="image_file" class="form-control" accept="image/*">
                                        <div class="form-text small">Formats recommandés : JPG, PNG, WEBP. Dimensions idéales : 600x380 px.</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Statut</label>
                                        <select name="status" class="form-select">
                                            <option value="active" {{ $sideBottom->status === 'active' ? 'selected' : '' }}>Actif (Affiché)</option>
                                            <option value="inactive" {{ $sideBottom->status === 'inactive' ? 'selected' : '' }}>Inactif (Masqué)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn btn-brand-primary w-100">
                                            <i class="fas fa-save me-1"></i> Enregistrer l'encadré inférieur
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- =========================================================================
             TAB 3 : BANNIÈRE PROMO (APRÈS NOTRE SÉLECTION DE PRODUITS)
             ========================================================================= --}}
        <div class="tab-pane fade" id="tab-promo" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-danger text-white me-2">Section Promotionnelle</span>
                        <strong class="text-dark">Bannière Large (Après "Notre sélection de produits")</strong>
                    </div>
                    <span class="badge {{ $wideMiddle->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ $wideMiddle->status === 'active' ? 'Actif' : 'Inactif' }}
                    </span>
                </div>

                {{-- Live Preview Box matching frontend --}}
                <div class="p-4 p-md-5 position-relative text-white overflow-hidden" 
                     style="background: #080c14 url('{{ $wideMiddle->image_url }}') center/cover no-repeat; min-height: 220px;">
                    <div class="position-absolute top-0 start-0 w-100 h-100" 
                         style="background: linear-gradient(90deg, rgba(8, 12, 20, 0.95) 0%, rgba(8, 12, 20, 0.75) 55%, rgba(8, 12, 20, 0.2) 100%); pointer-events: none;"></div>
                    
                    <div class="position-relative z-2 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if($wideMiddle->badge)
                                    <span class="badge bg-danger px-2 py-1 fw-bold text-uppercase" style="font-size: 0.7rem;">{{ $wideMiddle->badge }}</span>
                                @endif
                                @if($wideMiddle->subtitle)
                                    <span class="badge bg-dark px-2 py-1 fw-bold text-uppercase border border-secondary" style="font-size: 0.7rem;">{{ $wideMiddle->subtitle }}</span>
                                @endif
                            </div>
                            <h3 class="fw-bold mb-2">{{ $wideMiddle->title ?: 'Titre de la promotion' }}</h3>
                            <p class="text-white-50 mb-2 small" style="max-width: 650px;">{{ $wideMiddle->description }}</p>
                            @if(!empty($wideMiddle->features_list))
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-2">
                                @foreach($wideMiddle->features_list as $f)
                                <span class="small text-white-50"><i class="fas fa-check text-danger me-1"></i> {{ $f }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <span class="btn text-white fw-bold px-4 py-2 shadow" style="background: linear-gradient(135deg, #c8102e, #a80c26); border-radius: 999px;">
                                <span>{{ $wideMiddle->button_text ?: 'Voir la Promotion' }}</span>
                                <i class="fas fa-arrow-right ms-2" style="font-size: 11px;"></i>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Edit Form --}}
                <div class="card-body p-4 bg-white">
                    <form action="{{ route('banners.save-position', 'wide_middle') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Badge 1 (Rouge)</label>
                                <input type="text" name="badge" class="form-control" value="{{ old('badge', $wideMiddle->badge) }}" placeholder="Ex: PRODUIT TENDANCE">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Badge 2 (Sombre)</label>
                                <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $wideMiddle->subtitle) }}" placeholder="Ex: PROMOTION EXCLUSIVE">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Titre principal</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $wideMiddle->title) }}" placeholder="Ex: Promotion Exclusive sur Insta360">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Description courte de l'offre...">{{ old('description', $wideMiddle->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Points forts / Puces (Une par ligne)</label>
                                <textarea name="features" class="form-control font-monospace" rows="3" placeholder="VIDÉO 360° IMMERSIVE&#10;STABILISATION AVANCÉE&#10;RÉSOLUTION 5.7K ULTRA HD">{{ old('features', $wideMiddle->features) }}</textarea>
                                <div class="form-text small">Chaque ligne sera affichée avec une icône de validation rouge.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Texte du bouton CTA</label>
                                <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $wideMiddle->button_text) }}" placeholder="Ex: Voir la Promotion">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Lien du bouton</label>
                                <input type="text" name="link" class="form-control" value="{{ old('link', $wideMiddle->link) }}" placeholder="Ex: /shop?category=accessoires-insta360">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Choisir une image existante du catalogue</label>
                                <select name="image_preset" class="form-select">
                                    <option value="">-- Conserver l'image actuelle ou téléverser --</option>
                                    @foreach($presetImages as $path => $label)
                                        <option value="{{ $path }}" {{ $wideMiddle->image === $path ? 'selected' : '' }}>{{ $label }} ({{ basename($path) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Ou téléverser une nouvelle image d'arrière-plan</label>
                                <input type="file" name="image_file" class="form-control" accept="image/*">
                                <div class="form-text small">Dimensions idéales : 1920x450 px.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Statut d'affichage</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ $wideMiddle->status === 'active' ? 'selected' : '' }}>Actif (Affiché sur la page d'accueil)</option>
                                    <option value="inactive" {{ $wideMiddle->status === 'inactive' ? 'selected' : '' }}>Inactif (Masqué)</option>
                                </select>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-brand-primary px-4 py-2">
                                    <i class="fas fa-save me-1"></i> Mettre à jour la bannière promotionnelle
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- =========================================================================
     MODAL : AJOUTER UNE DIAPOSITIVE AU SLIDER
     ========================================================================= --}}
<div class="modal fade" id="addSlideModal" tabindex="-1" aria-labelledby="addSlideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom p-3 px-4">
                <h5 class="modal-title fw-bold" id="addSlideModalLabel">
                    <i class="fas fa-plus-circle text-primary me-2"></i> Ajouter une Diapositive au Slider
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small">Titre de la diapositive <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="Ex: DJI Osmo 360 All-In-One">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Badge givré (Haut gauche)</label>
                            <input type="text" name="badge" class="form-control" placeholder="Ex: NOUVEAUTÉ EXCLUSIVE • DJI OSMO 360">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Description / Sous-titre</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Courte description de l'équipement présenté..."></textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Lien de redirection au clic</label>
                            <input type="text" name="link" class="form-control" placeholder="Ex: /shop?q=DJI+Osmo+360 ou /shop/123">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Ordre d'affichage</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $mainHeroSlides->count() + 1 }}" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Choisir parmi les visuels existants</label>
                            <select name="image_preset" class="form-select">
                                <option value="">-- Aucun (Téléverser un nouveau fichier) --</option>
                                @foreach($presetImages as $path => $label)
                                    <option value="{{ $path }}">{{ $label }} ({{ basename($path) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Ou téléverser une image personnalisée</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            <div class="form-text small">Dimensions idéales : 1200x650 px (format paysage haute définition).</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Statut</label>
                            <select name="status" class="form-select" required>
                                <option value="active" selected>Actif (Visible dans le slider)</option>
                                <option value="inactive">Inactif (Masqué)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-brand-primary">
                        <i class="fas fa-check me-1"></i> Créer la diapositive
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================================================================
     MODAL : MODIFIER UNE DIAPOSITIVE EXISTANTE
     ========================================================================= --}}
<div class="modal fade" id="editSlideModal" tabindex="-1" aria-labelledby="editSlideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom p-3 px-4">
                <h5 class="modal-title fw-bold" id="editSlideModalLabel">
                    <i class="fas fa-edit text-primary me-2"></i> Modifier la Diapositive
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSlideForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small">Titre de la diapositive <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Badge givré (Haut gauche)</label>
                            <input type="text" name="badge" id="edit_badge" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Description / Sous-titre</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Lien de redirection</label>
                            <input type="text" name="link" id="edit_link" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Ordre d'affichage</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Image actuelle</label>
                            <div class="d-flex align-items-center gap-3 p-2 bg-light rounded-3">
                                <img id="edit_image_preview" src="" alt="Aperçu" class="rounded-2" style="width: 80px; height: 50px; object-fit: cover;">
                                <span id="edit_image_name" class="font-monospace small text-muted text-truncate" style="max-width: 400px;"></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Remplacer par un visuel du catalogue</label>
                            <select name="image_preset" id="edit_image_preset" class="form-select">
                                <option value="">-- Conserver l'image actuelle --</option>
                                @foreach($presetImages as $path => $label)
                                    <option value="{{ $path }}">{{ $label }} ({{ basename($path) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Ou téléverser un nouveau fichier image</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Statut</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Actif (Visible dans le slider)</option>
                                <option value="inactive">Inactif (Masqué)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-brand-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Populate Edit Slide Modal
    const editButtons = document.querySelectorAll('.edit-slide-btn');
    const editModal = new bootstrap.Modal(document.getElementById('editSlideModal'));
    const editForm = document.getElementById('editSlideForm');

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            editForm.action = this.dataset.action;
            document.getElementById('edit_title').value = this.dataset.title || '';
            document.getElementById('edit_badge').value = this.dataset.badge || '';
            document.getElementById('edit_description').value = this.dataset.description || '';
            document.getElementById('edit_link').value = this.dataset.link || '';
            document.getElementById('edit_sort_order').value = this.dataset.sort_order || '1';
            document.getElementById('edit_status').value = this.dataset.status || 'active';
            
            const imgPreview = document.getElementById('edit_image_preview');
            const imgName = document.getElementById('edit_image_name');
            imgPreview.src = this.dataset.image_url;
            imgName.textContent = this.dataset.image;

            editModal.show();
        });
    });
});
</script>
@endpush
