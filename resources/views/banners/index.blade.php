@extends('layouts.app')

@section('title', 'Gestion de l\'Accueil — Slider & Bannières')

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header - Aligned with Invoices, Orders & Inventory) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div>
                <h1 class="brand-title">Slider & Bannières</h1>
                <p class="brand-subtitle">Gérez en direct les diapositives du slider héroïque, les 2 encadrés latéraux et la bannière promotionnelle</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-external-link-alt"></i> <span>Voir le site</span>
            </a>
            <button type="button" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3 py-2" style="font-size: 0.84rem;" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">Nouvelle diapositive</span><span class="d-sm-none">Ajouter</span>
            </button>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Veuillez corriger les erreurs suivantes :</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Executive KPI Metrics Row --}}
    <div class="row g-3 mb-4">
        {{-- Total Diapositives --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Slider Principal</div>
                        <div class="stat-kpi-value text-dark">{{ $mainHeroSlides->count() }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">Diapositives actives</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Slides Actives --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Slides En Ligne</div>
                        <div class="stat-kpi-value text-success">{{ $mainHeroSlides->where('status', 'active')->count() }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-circle-check text-success me-1"></i>Visibles en vitrine
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Encadrés Latéraux Hero --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Encadrés Latéraux Hero</div>
                        <div class="stat-kpi-value text-info">2 Blocs</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-table-cells-large text-info me-1"></i>Boîte 2 & Boîte 3
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-table-cells-large"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bannière Promo Large --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Bannière Promo (Large)</div>
                        <div class="stat-kpi-value {{ $wideMiddle->status === 'active' ? 'text-warning' : 'text-secondary' }}">
                            {{ $wideMiddle->status === 'active' ? 'Active' : 'Inactif' }}
                        </div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-bullhorn text-warning me-1"></i>Bandeau 1920px
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Executive Navigation Tabs (Horizontal Scrollable on Mobile) --}}
    <div class="brand-tab-scroll-wrapper mb-4">
        <ul class="nav nav-pills brand-nav-pills flex-nowrap" id="bannerTabs" role="tablist">
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link active" id="tab-slider-btn" data-bs-toggle="pill" data-bs-target="#tab-slider" type="button" role="tab">
                    <i class="fas fa-images"></i>
                    <span>1. Slider Principal</span>
                    <span class="badge bg-light text-dark rounded-pill ms-1" style="font-size: 0.72rem;">{{ $mainHeroSlides->count() }}</span>
                </button>
            </li>
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link" id="tab-side-btn" data-bs-toggle="pill" data-bs-target="#tab-side" type="button" role="tab">
                    <i class="fas fa-table-cells-large"></i>
                    <span>2. Encadrés Latéraux Hero (2 Boîtes)</span>
                </button>
            </li>
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link" id="tab-promo-btn" data-bs-toggle="pill" data-bs-target="#tab-promo" type="button" role="tab">
                    <i class="fas fa-bullhorn"></i>
                    <span>3. Bannière Promo (Large)</span>
                </button>
            </li>
        </ul>
    </div>

    {{-- Tabs Content --}}
    <div class="tab-content" id="bannerTabsContent">

        {{-- =========================================================================
             TAB 1 : SLIDER PRINCIPAL
             ========================================================================= --}}
        <div class="tab-pane fade show active" id="tab-slider" role="tabpanel">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Diapositives du Slider Héroïque</h5>
                    <p class="text-muted small mb-0">Ces diapositives défilent automatiquement dans la grande boîte de gauche de la section Héro.</p>
                </div>
                <button type="button" class="btn-brand-primary d-inline-flex align-items-center gap-1.5 align-self-start align-self-sm-auto px-3 py-1.5 rounded-pill shadow-sm" style="font-size: 0.84rem;" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                    <i class="fas fa-plus"></i>
                    <span>Nouvelle diapositive</span>
                </button>
            </div>

            <div class="row g-3">
                @forelse($mainHeroSlides as $slide)
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="card h-100 banner-admin-card position-relative">
                        {{-- Visual Preview Banner --}}
                        <div class="banner-preview-box" style="background-image: url('{{ $slide->image_url }}');">
                            <div class="banner-overlay-gradient"></div>
                            
                            {{-- Top Badges --}}
                            <div class="position-absolute top-0 start-0 m-3 d-flex align-items-center gap-2 z-2 flex-wrap">
                                @if($slide->badge)
                                    <span class="banner-glass-badge">
                                        <span class="live-dot"></span>
                                        <span>{{ $slide->badge }}</span>
                                    </span>
                                @endif
                                <span class="badge bg-dark bg-opacity-75 text-white px-2 py-1 rounded-pill small fw-semibold border border-secondary border-opacity-25">
                                    Ordre #{{ $slide->sort_order }}
                                </span>
                            </div>

                            {{-- Status Badge --}}
                            <div class="position-absolute top-0 end-0 m-3 z-2">
                                @if($slide->status === 'active')
                                    <span class="badge bg-success bg-opacity-95 px-2.5 py-1 rounded-pill shadow-sm">
                                        <i class="fas fa-check-circle me-1"></i> Actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-95 px-2.5 py-1 rounded-pill shadow-sm">
                                        <i class="fas fa-eye-slash me-1"></i> Inactif
                                    </span>
                                @endif
                            </div>

                            {{-- Title & Desc on Preview Overlay --}}
                            <div class="position-absolute bottom-0 start-0 m-3 text-white z-2 pe-3">
                                <h5 class="fw-bold mb-1 text-truncate" style="max-width: 480px; text-shadow: 0 2px 4px rgba(0,0,0,0.6);">
                                    {{ $slide->title }}
                                </h5>
                                <p class="small text-white-50 mb-0 text-truncate" style="max-width: 480px; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">
                                    {{ $slide->description ?: 'Aucune description' }}
                                </p>
                            </div>
                        </div>

                        {{-- Card Details & Actions --}}
                        <div class="card-body p-3.5 d-flex flex-column justify-content-between bg-white">
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-2">
                                    <span class="d-inline-flex align-items-center gap-1.5">
                                        <i class="fas fa-link text-danger"></i>
                                        <strong class="text-dark">Lien :</strong>
                                    </span>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="text-truncate font-monospace small bg-light px-2 py-0.5 rounded border" style="max-width: 240px;" title="{{ $slide->link }}">
                                            {{ $slide->link ?: 'Non défini' }}
                                        </span>
                                        @if($slide->link)
                                            <a href="{{ $slide->link }}" target="_blank" class="text-secondary text-decoration-none" title="Tester le lien">
                                                <i class="fas fa-external-link-alt" style="font-size: 11px;"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted">
                                    <span class="d-inline-flex align-items-center gap-1.5">
                                        <i class="fas fa-image text-danger"></i>
                                        <strong class="text-dark">Fichier :</strong>
                                    </span>
                                    <span class="text-truncate font-monospace small bg-light px-2 py-0.5 rounded border" style="max-width: 240px;" title="{{ $slide->image }}">
                                        {{ basename($slide->image) }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end gap-2 border-top pt-2.5">
                                <button type="button" 
                                        class="btn btn-sm btn-brand-soft-primary rounded-pill d-inline-flex align-items-center gap-1.5 px-3 py-1.5 edit-slide-btn"
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
                                    <button type="submit" class="btn btn-sm btn-brand-soft-danger rounded-pill d-inline-flex align-items-center gap-1.5 px-3 py-1.5">
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
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 72px; height: 72px;">
                                <i class="fas fa-images fa-2x"></i>
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark">Aucune diapositive configurée</h5>
                        <p class="text-muted small mb-3">Créez votre première diapositive pour alimenter le grand carrousel de la page d'accueil.</p>
                        <div>
                            <button type="button" class="btn-brand-primary rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                                <i class="fas fa-plus me-1.5"></i> Ajouter une diapositive
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
                <div class="col-12 col-xl-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden banner-admin-card">
                        <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small fw-bold">Boîte 2 (Haut)</span>
                                <strong class="text-dark">Encadré Supérieur Droit</strong>
                            </div>
                            <span class="badge {{ $sideTop->status === 'active' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-2.5 py-1">
                                {{ $sideTop->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        {{-- High-Fidelity Live Preview Box --}}
                        <div class="p-3 bg-light border-bottom">
                            <div class="small fw-bold text-muted mb-2 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-eye me-1 text-danger"></i> Aperçu direct en vitrine :</span>
                                <span class="badge bg-white text-secondary border">600x380 px</span>
                            </div>
                            <div id="sideTopLivePreview" class="banner-live-side position-relative" style="background-image: url('{{ $sideTop->image_url }}');">
                                <div class="side-overlay"></div>
                                <span id="sideTopBadgePreview" class="side-tag">
                                    {{ $sideTop->badge ?: 'GIMBAL 4K COMPACT' }}
                                </span>
                                <div class="position-absolute bottom-0 start-0 m-3 z-2">
                                    <span id="sideTopBtnPreview" class="side-btn-preview">
                                        <span>{{ $sideTop->button_text ?: 'Acheter' }}</span>
                                        <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Form --}}
                        <div class="card-body p-3.5 p-md-4 bg-white">
                            <form action="{{ route('banners.save-position', 'side_top') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Titre / Référence interne</label>
                                        <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $sideTop->title) }}" placeholder="Ex: DJI Osmo Pocket 4">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Tag / Badge supérieur</label>
                                        <input type="text" name="badge" id="sideTopBadgeInput" class="form-control rounded-3" value="{{ old('badge', $sideTop->badge) }}" placeholder="Ex: GIMBAL 4K COMPACT">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Texte du bouton</label>
                                        <input type="text" name="button_text" id="sideTopBtnInput" class="form-control rounded-3" value="{{ old('button_text', $sideTop->button_text) }}" placeholder="Ex: Acheter">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Lien de destination</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="fas fa-link"></i></span>
                                            <input type="text" name="link" class="form-control border-start-0 rounded-end-3" value="{{ old('link', $sideTop->link) }}" placeholder="Ex: /shop?q=Osmo+Pocket">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Choisir une image existante du catalogue</label>
                                        <select name="image_preset" id="sideTopPresetSelect" class="form-select rounded-3">
                                            <option value="">-- Conserver l'image actuelle ou téléverser --</option>
                                            @foreach($presetImages as $path => $label)
                                                <option value="{{ $path }}" {{ $sideTop->image === $path ? 'selected' : '' }}>{{ $label }} ({{ basename($path) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Ou téléverser une nouvelle image</label>
                                        <input type="file" name="image_file" class="form-control rounded-3" accept="image/*">
                                        <div class="form-text small text-muted">Formats recommandés : JPG, PNG, WEBP. Dimensions optimales : 600x380 px.</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Statut</label>
                                        <select name="status" class="form-select rounded-3">
                                            <option value="active" {{ $sideTop->status === 'active' ? 'selected' : '' }}>Actif (Affiché en vitrine)</option>
                                            <option value="inactive" {{ $sideTop->status === 'inactive' ? 'selected' : '' }}>Inactif (Masqué)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn btn-brand-primary rounded-pill w-100 py-2.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                                            <i class="fas fa-save"></i>
                                            <span>Enregistrer l'encadré supérieur</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Encadré Inférieur (side_bottom) --}}
                <div class="col-12 col-xl-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden banner-admin-card">
                        <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small fw-bold">Boîte 3 (Bas)</span>
                                <strong class="text-dark">Encadré Inférieur Droit</strong>
                            </div>
                            <span class="badge {{ $sideBottom->status === 'active' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-2.5 py-1">
                                {{ $sideBottom->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        {{-- High-Fidelity Live Preview Box --}}
                        <div class="p-3 bg-light border-bottom">
                            <div class="small fw-bold text-muted mb-2 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-eye me-1 text-danger"></i> Aperçu direct en vitrine :</span>
                                <span class="badge bg-white text-secondary border">600x380 px</span>
                            </div>
                            <div id="sideBottomLivePreview" class="banner-live-side position-relative" style="background-image: url('{{ $sideBottom->image_url }}');">
                                <div class="side-overlay"></div>
                                <span id="sideBottomBadgePreview" class="side-tag">
                                    {{ $sideBottom->badge ?: 'OUTDOOR FLASH 800W' }}
                                </span>
                                <div class="position-absolute bottom-0 start-0 m-3 z-2">
                                    <span id="sideBottomBtnPreview" class="side-btn-preview">
                                        <span>{{ $sideBottom->button_text ?: 'Découvrez la GODOX AD800 PRO' }}</span>
                                        <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Form --}}
                        <div class="card-body p-3.5 p-md-4 bg-white">
                            <form action="{{ route('banners.save-position', 'side_bottom') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Titre / Référence interne</label>
                                        <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $sideBottom->title) }}" placeholder="Ex: GODOX AD800 PRO">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Tag / Badge supérieur</label>
                                        <input type="text" name="badge" id="sideBottomBadgeInput" class="form-control rounded-3" value="{{ old('badge', $sideBottom->badge) }}" placeholder="Ex: OUTDOOR FLASH 800W">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Texte du bouton</label>
                                        <input type="text" name="button_text" id="sideBottomBtnInput" class="form-control rounded-3" value="{{ old('button_text', $sideBottom->button_text) }}" placeholder="Ex: Découvrez la GODOX AD800 PRO">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Lien de destination</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="fas fa-link"></i></span>
                                            <input type="text" name="link" class="form-control border-start-0 rounded-end-3" value="{{ old('link', $sideBottom->link) }}" placeholder="Ex: /shop?q=Godox+AD800">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Choisir une image existante du catalogue</label>
                                        <select name="image_preset" id="sideBottomPresetSelect" class="form-select rounded-3">
                                            <option value="">-- Conserver l'image actuelle ou téléverser --</option>
                                            @foreach($presetImages as $path => $label)
                                                <option value="{{ $path }}" {{ $sideBottom->image === $path ? 'selected' : '' }}>{{ $label }} ({{ basename($path) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Ou téléverser une nouvelle image</label>
                                        <input type="file" name="image_file" class="form-control rounded-3" accept="image/*">
                                        <div class="form-text small text-muted">Formats recommandés : JPG, PNG, WEBP. Dimensions optimales : 600x380 px.</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Statut</label>
                                        <select name="status" class="form-select rounded-3">
                                            <option value="active" {{ $sideBottom->status === 'active' ? 'selected' : '' }}>Actif (Affiché en vitrine)</option>
                                            <option value="inactive" {{ $sideBottom->status === 'inactive' ? 'selected' : '' }}>Inactif (Masqué)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn btn-brand-primary rounded-pill w-100 py-2.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                                            <i class="fas fa-save"></i>
                                            <span>Enregistrer l'encadré inférieur</span>
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
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden banner-admin-card">
                <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small fw-bold">Section Promotionnelle</span>
                        <strong class="text-dark">Bannière Large (Après "Notre sélection de produits")</strong>
                    </div>
                    <span class="badge {{ $wideMiddle->status === 'active' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-2.5 py-1">
                        {{ $wideMiddle->status === 'active' ? 'Actif' : 'Inactif' }}
                    </span>
                </div>

                {{-- Live High-Fidelity Preview Box matching frontend --}}
                <div class="p-3 bg-light border-bottom">
                    <div class="small fw-bold text-muted mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-eye me-1 text-danger"></i> Aperçu panoramique en vitrine :</span>
                        <span class="badge bg-white text-secondary border">1920x450 px</span>
                    </div>

                    <div id="promoLivePreview" class="banner-live-wide p-4 p-md-5 position-relative text-white overflow-hidden" 
                         style="background-image: url('{{ $wideMiddle->image_url }}');">
                        <div class="wide-overlay"></div>
                        
                        <div class="position-relative z-2 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span id="promoBadge1Preview" class="badge bg-danger px-2.5 py-1 fw-bold text-uppercase rounded-pill" style="font-size: 0.70rem; letter-spacing: 0.5px;">
                                        {{ $wideMiddle->badge ?: 'PRODUIT TENDANCE' }}
                                    </span>
                                    <span id="promoBadge2Preview" class="badge bg-dark px-2.5 py-1 fw-bold text-uppercase border border-secondary rounded-pill" style="font-size: 0.70rem; letter-spacing: 0.5px;">
                                        {{ $wideMiddle->subtitle ?: 'PROMOTION EXCLUSIVE' }}
                                    </span>
                                </div>
                                <h3 id="promoTitlePreview" class="fw-bold mb-2 text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.6);">
                                    {{ $wideMiddle->title ?: 'Titre de la promotion' }}
                                </h3>
                                <p id="promoDescPreview" class="text-white-50 mb-3 small" style="max-width: 680px; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">
                                    {{ $wideMiddle->description }}
                                </p>
                                <div id="promoFeaturesPreview" class="d-flex flex-wrap align-items-center gap-3">
                                    @if(!empty($wideMiddle->features_list))
                                        @foreach($wideMiddle->features_list as $f)
                                        <span class="small text-white-50 d-inline-flex align-items-center"><i class="fas fa-check text-danger me-1.5"></i> {{ $f }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0 align-self-start align-self-lg-center">
                                <span id="promoBtnPreview" class="wide-cta-btn">
                                    <span>{{ $wideMiddle->button_text ?: 'Voir la Promotion' }}</span>
                                    <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit Form --}}
                <div class="card-body p-3.5 p-md-4 bg-white">
                    <form action="{{ route('banners.save-position', 'wide_middle') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-dark">Badge 1 (Rouge)</label>
                                <input type="text" name="badge" id="promoBadge1Input" class="form-control rounded-3" value="{{ old('badge', $wideMiddle->badge) }}" placeholder="Ex: PRODUIT TENDANCE">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-dark">Badge 2 (Sombre)</label>
                                <input type="text" name="subtitle" id="promoBadge2Input" class="form-control rounded-3" value="{{ old('subtitle', $wideMiddle->subtitle) }}" placeholder="Ex: PROMOTION EXCLUSIVE">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Titre principal</label>
                                <input type="text" name="title" id="promoTitleInput" class="form-control rounded-3" value="{{ old('title', $wideMiddle->title) }}" placeholder="Ex: Promotion Exclusive sur Insta360">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Description</label>
                                <textarea name="description" id="promoDescInput" class="form-control rounded-3" rows="2" placeholder="Description courte de l'offre...">{{ old('description', $wideMiddle->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Points forts / Puces (Une par ligne)</label>
                                <textarea name="features" id="promoFeaturesInput" class="form-control font-monospace rounded-3" rows="3" placeholder="VIDÉO 360° IMMERSIVE&#10;STABILISATION AVANCÉE&#10;RÉSOLUTION 5.7K ULTRA HD">{{ old('features', $wideMiddle->features) }}</textarea>
                                <div class="form-text small text-muted">Chaque ligne sera affichée avec une icône de validation rouge en vitrine.</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-dark">Texte du bouton CTA</label>
                                <input type="text" name="button_text" id="promoBtnInput" class="form-control rounded-3" value="{{ old('button_text', $wideMiddle->button_text) }}" placeholder="Ex: Voir la Promotion">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-dark">Lien du bouton</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="fas fa-link"></i></span>
                                    <input type="text" name="link" class="form-control border-start-0 rounded-end-3" value="{{ old('link', $wideMiddle->link) }}" placeholder="Ex: /shop?category=accessoires-insta360">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-dark">Choisir une image existante du catalogue</label>
                                <select name="image_preset" id="promoPresetSelect" class="form-select rounded-3">
                                    <option value="">-- Conserver l'image actuelle ou téléverser --</option>
                                    @foreach($presetImages as $path => $label)
                                        <option value="{{ $path }}" {{ $wideMiddle->image === $path ? 'selected' : '' }}>{{ $label }} ({{ basename($path) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-dark">Ou téléverser une nouvelle image d'arrière-plan</label>
                                <input type="file" name="image_file" class="form-control rounded-3" accept="image/*">
                                <div class="form-text small text-muted">Dimensions optimales : 1920x450 px (format panoramique).</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Statut d'affichage</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="active" {{ $wideMiddle->status === 'active' ? 'selected' : '' }}>Actif (Affiché sur la page d'accueil)</option>
                                    <option value="inactive" {{ $wideMiddle->status === 'inactive' ? 'selected' : '' }}>Inactif (Masqué)</option>
                                </select>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-brand-primary rounded-pill px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                    <i class="fas fa-save"></i>
                                    <span>Mettre à jour la bannière promotionnelle</span>
                                </button>
                            </div>
                        </div>
                    </form>
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
            <div class="modal-header border-bottom p-3.5 px-4 bg-light bg-opacity-50">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="addSlideModalLabel">Ajouter une Diapositive au Slider</h5>
                        <p class="text-muted small mb-0">Créez une diapositive pour le grand carrousel de la page d'accueil</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Titre de la diapositive <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3" required placeholder="Ex: DJI Osmo 360 All-In-One">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Badge givré (Haut gauche)</label>
                            <input type="text" name="badge" class="form-control rounded-3" placeholder="Ex: NOUVEAUTÉ EXCLUSIVE • DJI OSMO 360">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Description / Sous-titre</label>
                            <textarea name="description" class="form-control rounded-3" rows="2" placeholder="Courte description de l'équipement présenté..."></textarea>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-bold small text-dark">Lien de redirection au clic</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="fas fa-link"></i></span>
                                <input type="text" name="link" class="form-control border-start-0 rounded-end-3" placeholder="Ex: /shop?q=DJI+Osmo+360 ou /shop/123">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small text-dark">Ordre d'affichage</label>
                            <input type="number" name="sort_order" class="form-control rounded-3" value="{{ $mainHeroSlides->count() + 1 }}" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Choisir parmi les visuels existants</label>
                            <select name="image_preset" class="form-select rounded-3">
                                <option value="">-- Aucun (Téléverser un nouveau fichier) --</option>
                                @foreach($presetImages as $path => $label)
                                    <option value="{{ $path }}">{{ $label }} ({{ basename($path) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Ou téléverser une image personnalisée</label>
                            <input type="file" name="image_file" class="form-control rounded-3" accept="image/*">
                            <div class="form-text small text-muted">Dimensions optimales : 1200x650 px (format paysage haute définition).</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Statut</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="active" selected>Actif (Visible dans le slider)</option>
                                <option value="inactive">Inactif (Masqué)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4 bg-light bg-opacity-50">
                    <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-semibold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-brand-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        <i class="fas fa-check me-1.5"></i> Créer la diapositive
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
            <div class="modal-header border-bottom p-3.5 px-4 bg-light bg-opacity-50">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="editSlideModalLabel">Modifier la Diapositive</h5>
                        <p class="text-muted small mb-0">Mettez à jour les informations et l'ordre d'affichage</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSlideForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Titre de la diapositive <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Badge givré (Haut gauche)</label>
                            <input type="text" name="badge" id="edit_badge" class="form-control rounded-3">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Description / Sous-titre</label>
                            <textarea name="description" id="edit_description" class="form-control rounded-3" rows="2"></textarea>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-bold small text-dark">Lien de redirection</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="fas fa-link"></i></span>
                                <input type="text" name="link" id="edit_link" class="form-control border-start-0 rounded-end-3">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small text-dark">Ordre d'affichage</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control rounded-3" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Image actuelle</label>
                            <div class="d-flex align-items-center gap-3 p-2.5 bg-light rounded-3 border">
                                <img id="edit_image_preview" src="" alt="Aperçu" class="rounded-2 shadow-sm" style="width: 80px; height: 50px; object-fit: cover;">
                                <span id="edit_image_name" class="font-monospace small text-muted text-truncate" style="max-width: 400px;"></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Remplacer par un visuel du catalogue</label>
                            <select name="image_preset" id="edit_image_preset" class="form-select rounded-3">
                                <option value="">-- Conserver l'image actuelle --</option>
                                @foreach($presetImages as $path => $label)
                                    <option value="{{ $path }}">{{ $label }} ({{ basename($path) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Ou téléverser un nouveau fichier image</label>
                            <input type="file" name="image_file" class="form-control rounded-3" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Statut</label>
                            <select name="status" id="edit_status" class="form-select rounded-3" required>
                                <option value="active">Actif (Visible dans le slider)</option>
                                <option value="inactive">Inactif (Masqué)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4 bg-light bg-opacity-50">
                    <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-semibold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-brand-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        <i class="fas fa-save me-1.5"></i> Enregistrer les modifications
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
    // 1. Tab Persistence via Hash and Session Storage
    const tabButtons = document.querySelectorAll('#bannerTabs [data-bs-toggle="pill"]');
    const storedTab = sessionStorage.getItem('active_banner_tab') || window.location.hash;
    
    if (storedTab) {
        const targetBtn = document.querySelector(`#bannerTabs [data-bs-target="${storedTab}"]`) || 
                          document.querySelector(`#bannerTabs [data-bs-target="#${storedTab.replace('#', '')}"]`);
        if (targetBtn) {
            const tabInstance = bootstrap.Tab.getOrCreateInstance(targetBtn);
            tabInstance.show();
        }
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const target = e.target.getAttribute('data-bs-target');
            if (target) {
                sessionStorage.setItem('active_banner_tab', target);
                history.replaceState(null, null, target);
            }
        });
    });

    // 2. Populate Edit Slide Modal
    const editButtons = document.querySelectorAll('.edit-slide-btn');
    const editModalEl = document.getElementById('editSlideModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editForm = document.getElementById('editSlideForm');

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!editForm) return;
            editForm.action = this.dataset.action;
            document.getElementById('edit_title').value = this.dataset.title || '';
            document.getElementById('edit_badge').value = this.dataset.badge || '';
            document.getElementById('edit_description').value = this.dataset.description || '';
            document.getElementById('edit_link').value = this.dataset.link || '';
            document.getElementById('edit_sort_order').value = this.dataset.sort_order || '1';
            document.getElementById('edit_status').value = this.dataset.status || 'active';
            
            const imgPreview = document.getElementById('edit_image_preview');
            const imgName = document.getElementById('edit_image_name');
            if (imgPreview) imgPreview.src = this.dataset.image_url;
            if (imgName) imgName.textContent = this.dataset.image;

            const presetSelect = document.getElementById('edit_image_preset');
            if (presetSelect) presetSelect.value = '';

            if (editModal) editModal.show();
        });
    });

    // 3. Dynamic Live Previews for Tab 2 (Encadrés Latéraux)
    const sideTopBadgeInput = document.getElementById('sideTopBadgeInput');
    const sideTopBadgePreview = document.getElementById('sideTopBadgePreview');
    const sideTopBtnInput = document.getElementById('sideTopBtnInput');
    const sideTopBtnPreview = document.getElementById('sideTopBtnPreview');
    const sideTopPresetSelect = document.getElementById('sideTopPresetSelect');
    const sideTopLivePreview = document.getElementById('sideTopLivePreview');

    if (sideTopBadgeInput && sideTopBadgePreview) {
        sideTopBadgeInput.addEventListener('input', function() {
            sideTopBadgePreview.textContent = this.value || 'GIMBAL 4K COMPACT';
        });
    }

    if (sideTopBtnInput && sideTopBtnPreview) {
        sideTopBtnInput.addEventListener('input', function() {
            const btnSpan = sideTopBtnPreview.querySelector('span');
            if (btnSpan) btnSpan.textContent = this.value || 'Acheter';
        });
    }

    if (sideTopPresetSelect && sideTopLivePreview) {
        sideTopPresetSelect.addEventListener('change', function() {
            if (this.value) {
                sideTopLivePreview.style.backgroundImage = `url('/${this.value}')`;
            }
        });
    }

    const sideBottomBadgeInput = document.getElementById('sideBottomBadgeInput');
    const sideBottomBadgePreview = document.getElementById('sideBottomBadgePreview');
    const sideBottomBtnInput = document.getElementById('sideBottomBtnInput');
    const sideBottomBtnPreview = document.getElementById('sideBottomBtnPreview');
    const sideBottomPresetSelect = document.getElementById('sideBottomPresetSelect');
    const sideBottomLivePreview = document.getElementById('sideBottomLivePreview');

    if (sideBottomBadgeInput && sideBottomBadgePreview) {
        sideBottomBadgeInput.addEventListener('input', function() {
            sideBottomBadgePreview.textContent = this.value || 'OUTDOOR FLASH 800W';
        });
    }

    if (sideBottomBtnInput && sideBottomBtnPreview) {
        sideBottomBtnInput.addEventListener('input', function() {
            const btnSpan = sideBottomBtnPreview.querySelector('span');
            if (btnSpan) btnSpan.textContent = this.value || 'Découvrez la GODOX AD800 PRO';
        });
    }

    if (sideBottomPresetSelect && sideBottomLivePreview) {
        sideBottomPresetSelect.addEventListener('change', function() {
            if (this.value) {
                sideBottomLivePreview.style.backgroundImage = `url('/${this.value}')`;
            }
        });
    }

    // 4. Dynamic Live Previews for Tab 3 (Bannière Promo)
    const promoBadge1Input = document.getElementById('promoBadge1Input');
    const promoBadge1Preview = document.getElementById('promoBadge1Preview');
    const promoBadge2Input = document.getElementById('promoBadge2Input');
    const promoBadge2Preview = document.getElementById('promoBadge2Preview');
    const promoTitleInput = document.getElementById('promoTitleInput');
    const promoTitlePreview = document.getElementById('promoTitlePreview');
    const promoDescInput = document.getElementById('promoDescInput');
    const promoDescPreview = document.getElementById('promoDescPreview');
    const promoBtnInput = document.getElementById('promoBtnInput');
    const promoBtnPreview = document.getElementById('promoBtnPreview');
    const promoPresetSelect = document.getElementById('promoPresetSelect');
    const promoLivePreview = document.getElementById('promoLivePreview');
    const promoFeaturesInput = document.getElementById('promoFeaturesInput');
    const promoFeaturesPreview = document.getElementById('promoFeaturesPreview');

    if (promoBadge1Input && promoBadge1Preview) {
        promoBadge1Input.addEventListener('input', function() {
            promoBadge1Preview.textContent = this.value || 'PRODUIT TENDANCE';
        });
    }

    if (promoBadge2Input && promoBadge2Preview) {
        promoBadge2Input.addEventListener('input', function() {
            promoBadge2Preview.textContent = this.value || 'PROMOTION EXCLUSIVE';
        });
    }

    if (promoTitleInput && promoTitlePreview) {
        promoTitleInput.addEventListener('input', function() {
            promoTitlePreview.textContent = this.value || 'Titre de la promotion';
        });
    }

    if (promoDescInput && promoDescPreview) {
        promoDescInput.addEventListener('input', function() {
            promoDescPreview.textContent = this.value;
        });
    }

    if (promoBtnInput && promoBtnPreview) {
        promoBtnInput.addEventListener('input', function() {
            const btnSpan = promoBtnPreview.querySelector('span');
            if (btnSpan) btnSpan.textContent = this.value || 'Voir la Promotion';
        });
    }

    if (promoPresetSelect && promoLivePreview) {
        promoPresetSelect.addEventListener('change', function() {
            if (this.value) {
                promoLivePreview.style.backgroundImage = `url('/${this.value}')`;
            }
        });
    }

    if (promoFeaturesInput && promoFeaturesPreview) {
        promoFeaturesInput.addEventListener('input', function() {
            const lines = this.value.split('\n').filter(l => l.trim().length > 0);
            promoFeaturesPreview.innerHTML = lines.map(line => 
                `<span class="small text-white-50 d-inline-flex align-items-center"><i class="fas fa-check text-danger me-1.5"></i> ${line.trim()}</span>`
            ).join('');
        });
    }
});
</script>
@endpush
