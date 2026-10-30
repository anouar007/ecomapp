@extends('layouts.app')

@section('title', 'Logos & Icônes du Site')

@push('styles')
<style>
    .asset-studio-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        background: #ffffff;
        padding: 8px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .asset-tab-btn {
        border: none;
        background: transparent;
        color: #475569;
        font-weight: 600;
        font-size: 0.86rem;
        padding: 10px 18px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .asset-tab-btn:hover {
        background: #f8fafc;
        color: #0f172a;
    }
    .asset-tab-btn.active {
        background: #dc2626;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.22);
    }
    .asset-slot-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .asset-slot-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }
    .asset-preview-stage {
        min-height: 120px;
        border-radius: 12px;
        border: 1px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        position: relative;
        overflow: hidden;
        background-color: #f8fafc;
        background-image: radial-gradient(#cbd5e1 0.75px, transparent 0.75px);
        background-size: 12px 12px;
    }
    .asset-preview-stage.stage-dark {
        background-color: #0f172a;
        background-image: radial-gradient(#334155 0.75px, transparent 0.75px);
        border-color: #334155;
    }
    .asset-preview-stage img {
        max-width: 100%;
        object-fit: contain;
        transition: all 0.2s ease;
    }
    .icon-slot-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        height: 100%;
        transition: all 0.2s ease;
    }
    .icon-slot-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    }
    .icon-live-box {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: #dc2626;
        flex-shrink: 0;
    }
    .fa-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(92px, 1fr));
        gap: 8px;
        max-height: 360px;
        overflow-y: auto;
        padding: 4px;
    }
    .fa-picker-item {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 10px;
        padding: 10px 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: center;
    }
    .fa-picker-item:hover,
    .fa-picker-item.active {
        border-color: #dc2626;
        background: #fef2f2;
        color: #dc2626;
        transform: translateY(-1px);
    }
    .fa-picker-item i {
        font-size: 1.25rem;
    }
    .fa-picker-item span {
        font-size: 0.68rem;
        color: #64748b;
        word-break: break-all;
        line-height: 1.1;
    }
</style>
@endpush

@section('content')
    <!-- Executive Header -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-icons"></i>
            </div>
            <div>
                <h1 class="brand-title">Logos & Icônes du Site</h1>
                <p class="brand-subtitle">Gérez tous les logos (En-tête, Mobile, Footer, Factures, Favicon) et toutes les icônes de la boutique depuis un seul endroit</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-external-link-alt text-danger"></i> <span>Voir le site</span>
            </a>
            <button type="button" onclick="document.getElementById('masterLogosIconsForm').submit()" class="btn-brand-primary justify-content-center text-nowrap px-4 py-2 shadow-sm rounded-pill" style="font-size: 0.84rem;">
                <i class="fas fa-save me-1.5"></i> <span>Enregistrer tout</span>
            </button>
        </div>
    </div>

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

    <!-- KPI Overview Row -->
    @php
        $logoSlotKeys = ['app_logo', 'header_logo', 'mobile_logo', 'footer_logo', 'site_favicon', 'apple_touch_icon', 'invoice_logo', 'admin_logo', 'company_stamp', 'og_image'];
        $configuredLogosCount = collect($logoSlotKeys)->filter(fn($k) => !empty(setting($k)))->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Emplacements Logos</div>
                        <div class="stat-kpi-value text-dark">{{ $configuredLogosCount }} / 10</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Héritage intelligent actif</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-image"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Favicon & Mobile</div>
                        <div class="stat-kpi-value text-primary">
                            {{ setting('site_favicon') ? 'Personnalisé' : 'Logo Global' }}
                        </div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-globe text-primary me-1"></i>Onglet navigateur & iOS
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-mobile-screen-button"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Icônes Catégories</div>
                        <div class="stat-kpi-value text-success">{{ $categories->count() }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-folder-tree text-success me-1"></i>Menu, Mobile & Boutique
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Logos des Marques</div>
                        <div class="stat-kpi-value text-warning">{{ $brands->count() }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-tags text-warning me-1"></i>Filtres & Fiches produits
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-award"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="asset-studio-nav mb-4">
        <button type="button" class="asset-tab-btn active" data-tab="logos" onclick="switchAssetTab('logos')">
            <i class="fas fa-images"></i>
            <span>1. Logos & Favicons (Tous les emplacements)</span>
        </button>
        <button type="button" class="asset-tab-btn" data-tab="ui-icons" onclick="switchAssetTab('ui-icons')">
            <i class="fas fa-wand-magic-sparkles"></i>
            <span>2. Icônes de l'Interface & Réassurance</span>
        </button>
        <button type="button" class="asset-tab-btn" data-tab="categories" onclick="switchAssetTab('categories')">
            <i class="fas fa-folder-open"></i>
            <span>3. Icônes & Images des Catégories ({{ $categories->count() }})</span>
        </button>
        <button type="button" class="asset-tab-btn" data-tab="brands" onclick="switchAssetTab('brands')">
            <i class="fas fa-copyright"></i>
            <span>4. Logos des Marques ({{ $brands->count() }})</span>
        </button>
    </div>

    <!-- MASTER FORM FOR LOGOS, DIMENSIONS & UI ICONS -->
    <form action="{{ route('settings.logos-icons.save') }}" method="POST" enctype="multipart/form-data" id="masterLogosIconsForm">
        @csrf

        <!-- ================================================================
             TAB 1 : TOUS LES LOGOS & FAVICONS PAR EMPLACEMENT
             ================================================================ -->
        <div class="asset-tab-pane" id="pane-logos">
            @php
                $logoSlots = [
                    [
                        'key' => 'app_logo',
                        'slot' => 'default',
                        'title' => 'Logo Principal Global (Par défaut)',
                        'location' => 'Partout sur le site (si aucun logo spécifique n\'est défini)',
                        'desc' => 'C\'est le logo maître de votre boutique. Tous les autres emplacements l\'utilisent automatiquement par défaut.',
                        'icon' => 'fas fa-star text-danger',
                        'dark_stage' => false,
                        'height_key' => null,
                    ],
                    [
                        'key' => 'header_logo',
                        'slot' => 'header',
                        'title' => 'Logo En-tête Desktop (Navbar)',
                        'location' => 'Barre de navigation supérieure sur ordinateur',
                        'desc' => 'Affiché en haut à gauche sur toutes les pages du site. Ajustez sa hauteur ci-dessous.',
                        'icon' => 'fas fa-desktop text-primary',
                        'dark_stage' => false,
                        'height_key' => 'header_logo_height',
                        'height_default' => 96,
                        'height_min' => 30,
                        'height_max' => 130,
                    ],
                    [
                        'key' => 'mobile_logo',
                        'slot' => 'mobile',
                        'title' => 'Logo Menu Mobile (Tiroir #mobileNavDrawer)',
                        'location' => 'En-tête du menu latéral sur smartphone & tablette',
                        'desc' => 'Affiché lorsque le visiteur ouvre le menu sur mobile. Hérite du logo En-tête ou Global si vide.',
                        'icon' => 'fas fa-mobile-screen text-success',
                        'dark_stage' => false,
                        'height_key' => 'mobile_logo_height',
                        'height_default' => 70,
                        'height_min' => 28,
                        'height_max' => 110,
                    ],
                    [
                        'key' => 'footer_logo',
                        'slot' => 'footer',
                        'title' => 'Logo Pied de Page (Footer Sombre)',
                        'location' => 'Bas de page (Footer sur fond noir/ardoise)',
                        'desc' => 'Idéal pour téléverser une version claire ou blanche de votre logo adaptée au fond sombre du footer.',
                        'icon' => 'fas fa-shoe-prints text-info',
                        'dark_stage' => true,
                        'height_key' => 'footer_logo_height',
                        'height_default' => 56,
                        'height_min' => 28,
                        'height_max' => 110,
                    ],
                    [
                        'key' => 'site_favicon',
                        'slot' => 'favicon',
                        'title' => 'Favicon du Site (Onglet Navigateur)',
                        'location' => 'Onglets Chrome, Safari, Firefox & Résultats Google (16x16 / 32x32)',
                        'desc' => 'Format carré recommandé (PNG, ICO ou SVG). S\'affiche dans l\'onglet du navigateur et sur Google.',
                        'icon' => 'fas fa-Window-maximize text-warning',
                        'dark_stage' => false,
                        'height_key' => null,
                        'preview_max_h' => 48,
                    ],
                    [
                        'key' => 'apple_touch_icon',
                        'slot' => 'apple_touch',
                        'title' => 'Icône Écran d\'Accueil Mobile (Apple Touch / PWA)',
                        'location' => 'Raccourci iPhone, iPad & Android (180x180 px)',
                        'desc' => 'Icône affichée lorsqu\'un client ajoute votre boutique sur l\'écran d\'accueil de son téléphone.',
                        'icon' => 'fab fa-apple text-dark',
                        'dark_stage' => false,
                        'height_key' => null,
                        'preview_max_h' => 64,
                    ],
                    [
                        'key' => 'invoice_logo',
                        'slot' => 'invoice',
                        'title' => 'Logo Factures, Devis, BL & Certificats de Garantie',
                        'location' => 'Documents PDF & Impressions officielles',
                        'desc' => 'Imprimé en haut à gauche de vos factures, devis, bons de livraison et certificats de garantie.',
                        'icon' => 'fas fa-file-invoice text-danger',
                        'dark_stage' => false,
                        'height_key' => null,
                    ],
                    [
                        'key' => 'company_stamp',
                        'slot' => 'stamp',
                        'title' => 'Cachet & Signature Officielle de l\'Entreprise',
                        'location' => 'Bas des Factures, Devis, BL & Garanties',
                        'desc' => 'Cachet officiel apposé automatiquement sur vos documents commerciaux.',
                        'icon' => 'fas fa-stamp text-primary',
                        'dark_stage' => false,
                        'height_key' => 'company_stamp_scale',
                        'height_label' => 'Échelle du cachet (%)',
                        'height_unit' => '%',
                        'height_default' => 100,
                        'height_min' => 25,
                        'height_max' => 200,
                    ],
                    [
                        'key' => 'admin_logo',
                        'slot' => 'admin',
                        'title' => 'Logo Tableau de Bord Admin & Connexion',
                        'location' => 'Barre supérieure du Dashboard, pages Login & Inscription',
                        'desc' => 'Logo affiché dans l\'interface d\'administration et sur la page de connexion.',
                        'icon' => 'fas fa-user-shield text-secondary',
                        'dark_stage' => false,
                        'height_key' => 'admin_logo_height',
                        'height_default' => 48,
                        'height_min' => 24,
                        'height_max' => 80,
                    ],
                    [
                        'key' => 'og_image',
                        'slot' => 'og',
                        'title' => 'Image / Logo d\'Aperçu Partage Social (OpenGraph)',
                        'location' => 'Aperçu lors du partage du lien du site sur WhatsApp, Facebook, LinkedIn',
                        'desc' => 'Image affichée par défaut lorsqu\'on partage le lien de l\'accueil ou de la boutique sur WhatsApp.',
                        'icon' => 'fas fa-share-nodes text-success',
                        'dark_stage' => false,
                        'height_key' => null,
                    ],
                ];
            @endphp

            <div class="row g-3">
                @foreach($logoSlots as $slotItem)
                    @php
                        $rawCustomVal = setting($slotItem['key']);
                        $hasCustom = !empty($rawCustomVal);
                        $resolvedUrl = $slotItem['slot'] === 'stamp'
                            ? ($hasCustom ? resolve_setting_asset_url($rawCustomVal) : null)
                            : site_logo_url($slotItem['slot']);
                        $hKey = $slotItem['height_key'] ?? null;
                        $hVal = $hKey ? (int) setting($hKey, $slotItem['height_default'] ?? 70) : null;
                        $hUnit = $slotItem['height_unit'] ?? 'px';
                        $maxPreviewH = $slotItem['preview_max_h'] ?? ($hUnit === 'px' && $hVal ? min(95, $hVal) : 78);
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="asset-slot-card">
                            <div>
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="{{ $slotItem['icon'] }} fs-5"></i>
                                        <div>
                                            <h3 class="fw-bold text-dark mb-0" style="font-size: 0.94rem;">{{ $slotItem['title'] }}</h3>
                                            <span class="text-muted" style="font-size: 0.74rem;"><i class="fas fa-location-dot me-1"></i>{{ $slotItem['location'] }}</span>
                                        </div>
                                    </div>
                                    @if($hasCustom)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                                            <i class="fas fa-check-circle me-1"></i>Personnalisé
                                        </span>
                                    @elseif($slotItem['key'] === 'app_logo')
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                                            Par défaut
                                        </span>
                                    @elseif($slotItem['slot'] === 'stamp')
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                                            Non défini
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.68rem;" title="Utilise automatiquement le Logo Principal Global">
                                            <i class="fas fa-link me-1"></i>Hérite du Global
                                        </span>
                                    @endif
                                </div>

                                <p class="text-muted small mb-3" style="font-size: 0.78rem; line-height: 1.45;">
                                    {{ $slotItem['desc'] }}
                                </p>

                                <!-- Visual Preview Box -->
                                <div class="asset-preview-stage {{ !empty($slotItem['dark_stage']) ? 'stage-dark' : '' }} mb-3">
                                    @if($resolvedUrl)
                                        <img src="{{ $resolvedUrl }}"
                                             id="preview_img_{{ $slotItem['key'] }}"
                                             alt="{{ $slotItem['title'] }}"
                                             style="max-height: {{ $maxPreviewH }}px;">
                                    @else
                                        <div class="text-center text-muted small">
                                            <i class="fas fa-image fs-3 mb-1 opacity-50 d-block"></i>
                                            <span>Aucun visuel défini</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Optional Height / Scale Slider -->
                                @if($hKey)
                                    <div class="p-2.5 bg-light rounded-3 border mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label class="small fw-bold text-dark mb-0" style="font-size: 0.76rem;">
                                                <i class="fas fa-arrows-up-down text-danger me-1"></i>
                                                {{ $slotItem['height_label'] ?? 'Hauteur d\'affichage' }}
                                            </label>
                                            <span class="badge bg-dark text-white rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                                <span id="val_{{ $hKey }}">{{ $hVal }}</span> {{ $hUnit }}
                                            </span>
                                        </div>
                                        <input type="range"
                                               class="form-range mb-0"
                                               name="settings[{{ $hKey }}]"
                                               min="{{ $slotItem['height_min'] }}"
                                               max="{{ $slotItem['height_max'] }}"
                                               step="2"
                                               value="{{ $hVal }}"
                                               oninput="document.getElementById('val_{{ $hKey }}').textContent = this.value; @if($hUnit === 'px') const img = document.getElementById('preview_img_{{ $slotItem['key'] }}'); if(img) img.style.maxHeight = Math.min(105, this.value) + 'px'; @endif">
                                    </div>
                                @endif
                            </div>

                            <!-- Upload & Reset Controls -->
                            <div class="d-flex align-items-center gap-2 pt-2 border-top">
                                <label class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold flex-grow-1 text-center mb-0" style="cursor: pointer; font-size: 0.78rem;">
                                    <i class="fas fa-upload me-1"></i>
                                    <span>{{ $hasCustom ? 'Remplacer le fichier' : 'Choisir un fichier' }}</span>
                                    <input type="file"
                                           name="asset_files[{{ $slotItem['key'] }}]"
                                           accept="image/*,.ico,.svg"
                                           class="d-none"
                                           onchange="previewAndAutoSubmitAsset(this, '{{ $slotItem['key'] }}')">
                                </label>

                                @if($hasCustom)
                                    <button type="button"
                                            class="btn btn-sm btn-light border text-danger rounded-pill px-2.5 py-1.5"
                                            title="Supprimer ce visuel spécifique"
                                            onclick="removeSingleBrandAsset('{{ $slotItem['key'] }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ================================================================
             TAB 2 : ICÔNES DE L'INTERFACE & RÉASSURANCE
             ================================================================ -->
        <div class="asset-tab-pane d-none" id="pane-ui-icons">
            @php
                $uiIconGroups = [
                    [
                        'group_title' => 'En-tête, Navigation, Panier & Menu Mobile',
                        'group_desc' => 'Personnalisez les icônes principales affichées dans la barre de navigation en haut du site et sur mobile.',
                        'group_icon' => 'fas fa-compass text-danger',
                        'items' => [
                            [
                                'key' => 'icon_nav_categories',
                                'label' => 'Bouton Rouge « CATÉGORIES » (Desktop & Mobile)',
                                'default' => 'fas fa-bars',
                                'location' => 'En-tête principal & Tiroir mobile',
                            ],
                            [
                                'key' => 'icon_nav_cart',
                                'label' => 'Icône Panier d\'Achat',
                                'default' => 'fas fa-shopping-bag',
                                'location' => 'En-tête à droite du champ de recherche',
                            ],
                            [
                                'key' => 'icon_nav_search',
                                'label' => 'Icône Barre de Recherche',
                                'default' => 'fas fa-search',
                                'location' => 'Champ de recherche en haut',
                            ],
                            [
                                'key' => 'icon_nav_mobile_menu',
                                'label' => 'Icône Menu Burger Mobile',
                                'default' => 'fas fa-bars',
                                'location' => 'Bouton d\'ouverture du menu sur mobile',
                            ],
                        ],
                    ],
                    [
                        'group_title' => 'Boutons d\'Action Produit & Bulle WhatsApp',
                        'group_desc' => 'Icônes des boutons d\'achat et du bouton flottant WhatsApp.',
                        'group_icon' => 'fas fa-bag-shopping text-primary',
                        'items' => [
                            [
                                'key' => 'icon_add_to_cart',
                                'label' => 'Bouton « Ajouter au panier »',
                                'default' => 'fas fa-cart-plus',
                                'location' => 'Fiche produit',
                            ],
                            [
                                'key' => 'icon_whatsapp_float',
                                'label' => 'Bulle Flottante WhatsApp (Bas de page)',
                                'default' => 'fab fa-whatsapp',
                                'location' => 'Bouton flottant permanent en bas à droite',
                            ],
                        ],
                    ],
                    [
                        'group_title' => 'Les 4 Piliers de Réassurance (Page d\'Accueil)',
                        'group_desc' => 'Icônes, titres et textes des 4 blocs de confiance affichés sur la page d\'accueil.',
                        'group_icon' => 'fas fa-shield-halved text-success',
                        'items' => [
                            [
                                'key' => 'icon_trust_1',
                                'label' => 'Pilier 1 : Livraison Express',
                                'default' => 'fas fa-truck-fast',
                                'location' => 'Accueil — Bloc Réassurance #1',
                                'title_key' => 'trust_1_title',
                                'title_default' => 'Livraison Express 24-48h',
                                'desc_key' => 'trust_1_desc',
                                'desc_default' => 'Expédition rapide et sécurisée avec emballage renforcé anti-chocs dans tout le Maroc.',
                            ],
                            [
                                'key' => 'icon_trust_2',
                                'label' => 'Pilier 2 : Garantie & Authenticité',
                                'default' => 'fas fa-shield-halved',
                                'location' => 'Accueil — Bloc Réassurance #2',
                                'title_key' => 'trust_2_title',
                                'title_default' => '100% Neuf & Scellé',
                                'desc_key' => 'trust_2_desc',
                                'desc_default' => "Matériel d'origine constructeur sous garantie officielle avec SAV réactif à Casablanca.",
                            ],
                            [
                                'key' => 'icon_trust_3',
                                'label' => 'Pilier 3 : Support & Conseil',
                                'default' => 'fas fa-headset',
                                'location' => 'Accueil — Bloc Réassurance #3',
                                'title_key' => 'trust_3_title',
                                'title_default' => 'Conseil Technique Pro',
                                'desc_key' => 'trust_3_desc',
                                'desc_default' => 'Une équipe de vidéastes et photographes vous aide à choisir la configuration idéale.',
                            ],
                            [
                                'key' => 'icon_trust_4',
                                'label' => 'Pilier 4 : Paiement Sécurisé',
                                'default' => 'fas fa-wallet',
                                'location' => 'Accueil — Bloc Réassurance #4',
                                'title_key' => 'trust_4_title',
                                'title_default' => 'Paiement à la Livraison',
                                'desc_key' => 'trust_4_desc',
                                'desc_default' => 'Payez en toute sérénité à la réception de votre commande ou en ligne par carte bancaire.',
                            ],
                        ],
                    ],
                    [
                        'group_title' => 'Badges de Réassurance sur la Fiche Produit',
                        'group_desc' => 'Icônes et textes affichés sous le bouton d\'achat sur chaque page produit.',
                        'group_icon' => 'fas fa-certificate text-warning',
                        'items' => [
                            [
                                'key' => 'icon_pdp_trust_1',
                                'label' => 'Badge Produit #1 (Livraison)',
                                'default' => 'fas fa-truck-fast',
                                'location' => 'Page Produit sous le bouton Ajouter au panier',
                                'title_key' => 'pdp_trust_1_text',
                                'title_default' => __('Secure Delivery 24/48h'),
                            ],
                            [
                                'key' => 'icon_pdp_trust_2',
                                'label' => 'Badge Produit #2 (Showroom)',
                                'default' => 'fas fa-video',
                                'location' => 'Page Produit sous le bouton Ajouter au panier',
                                'title_key' => 'pdp_trust_2_text',
                                'title_default' => __('Showroom Demo Casablanca'),
                            ],
                        ],
                    ],
                    [
                        'group_title' => 'Icônes des Réseaux Sociaux (Pied de Page / Footer)',
                        'group_desc' => 'Icônes des boutons sociaux dans le footer du site.',
                        'group_icon' => 'fas fa-share-alt text-info',
                        'items' => [
                            [
                                'key' => 'icon_social_facebook',
                                'label' => 'Facebook',
                                'default' => 'fab fa-facebook-f',
                                'location' => 'Footer du site',
                            ],
                            [
                                'key' => 'icon_social_instagram',
                                'label' => 'Instagram',
                                'default' => 'fab fa-instagram',
                                'location' => 'Footer du site',
                            ],
                            [
                                'key' => 'icon_social_whatsapp',
                                'label' => 'WhatsApp Footer',
                                'default' => 'fab fa-whatsapp',
                                'location' => 'Footer du site',
                            ],
                            [
                                'key' => 'icon_social_twitter',
                                'label' => 'X / Twitter',
                                'default' => 'fab fa-twitter',
                                'location' => 'Footer du site',
                            ],
                            [
                                'key' => 'icon_social_linkedin',
                                'label' => 'LinkedIn',
                                'default' => 'fab fa-linkedin-in',
                                'location' => 'Footer du site',
                            ],
                        ],
                    ],
                ];
            @endphp

            @foreach($uiIconGroups as $group)
                <div class="settings-section-card mb-4">
                    <div class="mb-3">
                        <h3 class="settings-section-title mb-1">
                            <i class="{{ $group['group_icon'] }}"></i>
                            <span>{{ $group['group_title'] }}</span>
                        </h3>
                        <p class="settings-section-desc mb-0">{{ $group['group_desc'] }}</p>
                    </div>

                    <div class="row g-3">
                        @foreach($group['items'] as $item)
                            @php
                                $iconVal = setting($item['key'], $item['default']);
                                $customImgKey = $item['key'] . '_image';
                                $customImgVal = setting($customImgKey);
                                $customImgUrl = $customImgVal ? resolve_setting_asset_url($customImgVal) : null;
                            @endphp
                            <div class="col-12 col-md-6">
                                <div class="icon-slot-card">
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Live Icon Box -->
                                        <div class="icon-live-box" id="live_box_{{ $item['key'] }}">
                                            @if($customImgUrl)
                                                <img src="{{ $customImgUrl }}" alt="" style="width: 28px; height: 28px; object-fit: contain;">
                                            @else
                                                <i class="{{ $iconVal }}" id="live_icon_{{ $item['key'] }}"></i>
                                            @endif
                                        </div>

                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                <h4 class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">{{ $item['label'] }}</h4>
                                                @if($customImgUrl)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.65rem;">Image SVG/PNG</span>
                                                @endif
                                            </div>
                                            <div class="text-muted mb-2" style="font-size: 0.73rem;">
                                                <i class="fas fa-location-dot me-1"></i>{{ $item['location'] }}
                                            </div>

                                            <!-- FontAwesome Class Input + Visual Picker Button -->
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text bg-light text-muted"><i class="fas fa-icons"></i></span>
                                                <input type="text"
                                                       name="settings[{{ $item['key'] }}]"
                                                       id="input_{{ $item['key'] }}"
                                                       class="form-control font-monospace"
                                                       value="{{ $iconVal }}"
                                                       placeholder="{{ $item['default'] }}"
                                                       oninput="updateLiveFaIcon('{{ $item['key'] }}', this.value)">
                                                <button type="button"
                                                        class="btn btn-outline-danger fw-semibold"
                                                        onclick="openFaIconPicker('{{ $item['key'] }}')">
                                                    <i class="fas fa-grip me-1"></i>Choisir
                                                </button>
                                            </div>

                                            <!-- Custom Image/SVG Upload Option for this Icon -->
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <label class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-secondary mb-0" style="font-size: 0.72rem; cursor: pointer;">
                                                    <i class="fas fa-file-image text-primary me-1"></i>
                                                    <span>{{ $customImgUrl ? 'Changer l\'image SVG/PNG' : 'Ou importer une icône SVG/PNG' }}</span>
                                                    <input type="file"
                                                           name="asset_files[{{ $customImgKey }}]"
                                                           accept="image/*,.svg,.ico"
                                                           class="d-none"
                                                           onchange="document.getElementById('masterLogosIconsForm').submit()">
                                                </label>
                                                @if($customImgUrl)
                                                    <button type="button"
                                                            class="btn btn-sm btn-light border text-danger rounded-pill px-2 py-1"
                                                            style="font-size: 0.72rem;"
                                                            onclick="removeSingleBrandAsset('{{ $customImgKey }}')">
                                                        <i class="fas fa-times me-1"></i>Retirer l'image
                                                    </button>
                                                @endif
                                                @if($iconVal !== $item['default'])
                                                    <button type="button"
                                                            class="btn btn-sm btn-link text-muted p-0 text-decoration-none"
                                                            style="font-size: 0.72rem;"
                                                            onclick="updateLiveFaIcon('{{ $item['key'] }}', '{{ $item['default'] }}'); document.getElementById('input_{{ $item['key'] }}').value = '{{ $item['default'] }}';">
                                                        Par défaut
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Optional Title & Description inputs (for Reassurance Pillars & PDP Trust Badges) -->
                                            @if(!empty($item['title_key']))
                                                <div class="mt-2 pt-2 border-top">
                                                    <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.73rem;">Titre / Libellé affiché</label>
                                                    <input type="text"
                                                           name="settings[{{ $item['title_key'] }}]"
                                                           class="form-control form-control-sm rounded-2 mb-1.5"
                                                           value="{{ setting($item['title_key'], $item['title_default']) }}">
                                                    @if(!empty($item['desc_key']))
                                                        <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.73rem;">Description courte</label>
                                                        <textarea name="settings[{{ $item['desc_key'] }}]"
                                                                  class="form-control form-control-sm rounded-2"
                                                                  rows="2">{{ setting($item['desc_key'], $item['desc_default']) }}</textarea>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="text-end mb-4">
                <button type="submit" class="btn btn-brand-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                    <i class="fas fa-save me-1.5"></i> Enregistrer toutes les icônes
                </button>
            </div>
        </div>
    </form>

    <!-- ================================================================
         TAB 3 : ICÔNES & IMAGES DES CATÉGORIES (Édition Rapide)
         ================================================================ -->
    <div class="asset-tab-pane d-none" id="pane-categories">
        <div class="settings-section-card">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h3 class="settings-section-title mb-1">
                        <i class="fas fa-folder-tree text-danger"></i>
                        <span>Icônes & Visuels des Catégories ({{ $categories->count() }})</span>
                    </h3>
                    <p class="settings-section-desc mb-0">
                        Ces icônes et images s'affichent dans le menu déroulant « CATÉGORIES », le menu mobile, le slider d'accueil et la page Boutique.
                    </p>
                </div>
                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="catAssetSearch" class="form-control" placeholder="Filtrer une catégorie..." oninput="filterAssetCards('.cat-asset-col', this.value)">
                </div>
            </div>

            <div class="row g-3">
                @foreach($categories as $cat)
                    @php
                        $catImgUrl = $cat->image_url;
                        $catIconClass = !empty($cat->icon)
                            ? (str_starts_with(trim($cat->icon), 'fa') ? trim($cat->icon) : 'fas fa-' . trim($cat->icon))
                            : 'fas fa-camera';
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4 cat-asset-col" data-name="{{ strtolower($cat->name) }}">
                        <form action="{{ route('settings.logos-icons.category', $cat) }}" method="POST" enctype="multipart/form-data" class="icon-slot-card d-flex flex-column justify-content-between">
                            @csrf
                            <div>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="icon-live-box" style="width: 56px; height: 56px; overflow: hidden;">
                                        @if($catImgUrl)
                                            <img src="{{ $catImgUrl }}" alt="{{ $cat->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                        @else
                                            <i class="{{ $catIconClass }}" id="live_icon_cat_{{ $cat->id }}"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 text-truncate">
                                        <h4 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.9rem;">{{ $cat->name }}</h4>
                                        <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.68rem;">Slug: {{ $cat->slug }}</span>
                                    </div>
                                </div>

                                <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.74rem;">Icône FontAwesome (Menu & Fallback)</label>
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text"
                                           name="icon"
                                           id="input_cat_{{ $cat->id }}"
                                           class="form-control font-monospace"
                                           value="{{ $cat->icon }}"
                                           placeholder="Ex: fas fa-camera">
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="openFaIconPicker('cat_{{ $cat->id }}')">
                                        <i class="fas fa-grip"></i>
                                    </button>
                                </div>

                                <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.74rem;">Image / Miniature de la catégorie</label>
                                <input type="file" name="image" accept="image/*,.svg" class="form-control form-control-sm mb-2">
                            </div>

                            <div class="d-flex align-items-center justify-content-between gap-2 pt-2 border-top mt-1">
                                @if($catImgUrl)
                                    <button type="submit" name="remove_image" value="1" class="btn btn-sm btn-light border text-danger rounded-pill px-2.5 py-1" style="font-size: 0.73rem;">
                                        <i class="fas fa-trash-alt me-1"></i>Retirer l'image
                                    </button>
                                @else
                                    <span></span>
                                @endif
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.76rem;">
                                    <i class="fas fa-save me-1"></i>Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ================================================================
         TAB 4 : LOGOS DES MARQUES (Édition Rapide)
         ================================================================ -->
    <div class="asset-tab-pane d-none" id="pane-brands">
        <div class="settings-section-card">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h3 class="settings-section-title mb-1">
                        <i class="fas fa-tags text-danger"></i>
                        <span>Logos Officiels des Marques ({{ $brands->count() }})</span>
                    </h3>
                    <p class="settings-section-desc mb-0">
                        Ces logos s'affichent dans le filtre « Marque » de la boutique et sur les fiches produits.
                    </p>
                </div>
                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="brandAssetSearch" class="form-control" placeholder="Filtrer une marque..." oninput="filterAssetCards('.brand-asset-col', this.value)">
                </div>
            </div>

            <div class="row g-3">
                @foreach($brands as $brand)
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3 brand-asset-col" data-name="{{ strtolower($brand->name) }}">
                        <form action="{{ route('settings.logos-icons.brand', $brand) }}" method="POST" enctype="multipart/form-data" class="icon-slot-card d-flex flex-column justify-content-between">
                            @csrf
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h4 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.9rem;">{{ $brand->name }}</h4>
                                    <span class="badge {{ $brand->logo_url ? 'bg-success-subtle text-success' : 'bg-light text-muted' }} border rounded-pill" style="font-size: 0.66rem;">
                                        {{ $brand->logo_url ? 'Logo actif' : 'Sans logo' }}
                                    </span>
                                </div>

                                <div class="asset-preview-stage mb-2" style="min-height: 82px; padding: 10px;">
                                    @if($brand->logo_url)
                                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-height: 52px;">
                                    @else
                                        <span class="text-muted small"><i class="fas fa-tag me-1"></i>Aucun logo</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1.5 pt-2 border-top">
                                <label class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold flex-grow-1 text-center mb-0" style="font-size: 0.74rem; cursor: pointer;">
                                    <i class="fas fa-upload me-1"></i>{{ $brand->logo_url ? 'Changer' : 'Importer' }}
                                    <input type="file" name="logo" accept="image/*,.svg" class="d-none" onchange="this.form.submit()">
                                </label>
                                @if($brand->logo_url)
                                    <button type="submit" name="remove_logo" value="1" class="btn btn-sm btn-light border text-danger rounded-pill px-2 py-1" title="Supprimer le logo">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Hidden Form to Delete a Single Asset Slot -->
    <form id="removeSingleAssetForm" action="{{ route('settings.logos-icons.remove') }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
        <input type="hidden" name="slot_key" id="removeSlotKeyInput">
    </form>

    <!-- Visual FontAwesome Icon Picker Modal -->
    <div class="modal fade" id="faIconPickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-icons text-danger"></i>
                        <span>Choisir une Icône</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="faPickerSearchInput" class="form-control" placeholder="Rechercher une icône (ex: camera, cart, truck, whatsapp, shield, star)..." oninput="filterFaPickerIcons(this.value)">
                    </div>
                    <div class="fa-picker-grid" id="faPickerGrid"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const CURATED_FA_ICONS = [
        'fas fa-bars', 'fas fa-grip', 'fas fa-th-large', 'fas fa-list',
        'fas fa-shopping-bag', 'fas fa-cart-shopping', 'fas fa-cart-plus', 'fas fa-basket-shopping',
        'fas fa-search', 'fas fa-magnifying-glass', 'fas fa-sliders', 'fas fa-filter',
        'fas fa-camera', 'fas fa-camera-retro', 'fas fa-video', 'fas fa-film',
        'fas fa-circle-notch', 'fas fa-lightbulb', 'fas fa-bolt', 'fas fa-microphone',
        'fas fa-microphone-lines', 'fas fa-podcast', 'fas fa-headphones', 'fas fa-headset',
        'fas fa-helicopter', 'fas fa-arrows-to-dot', 'fas fa-bag-shopping', 'fas fa-sd-card',
        'fas fa-battery-full', 'fas fa-camera-rotate', 'fas fa-spray-can-sparkles', 'fas fa-cube',
        'fas fa-truck-fast', 'fas fa-truck', 'fas fa-box-open', 'fas fa- parachute-box',
        'fas fa-shield-halved', 'fas fa-shield-check', 'fas fa-award', 'fas fa-certificate',
        'fas fa-medal', 'fas fa-check-circle', 'fas fa-check-double', 'fas fa-thumbs-up',
        'fas fa-wallet', 'fas fa-credit-card', 'fas fa-money-bill-wave', 'fas fa-hand-holding-dollar',
        'fas fa-lock', 'fas fa-clock', 'fas fa-bolt-lightning', 'fas fa-fire',
        'fas fa-star', 'fas fa-heart', 'fas fa-tag', 'fas fa-tags',
        'fas fa-percent', 'fas fa-gift', 'fas fa-store', 'fas fa-location-dot',
        'fas fa-phone', 'fas fa-envelope', 'fas fa-comments', 'fas fa-paper-plane',
        'fab fa-whatsapp', 'fab fa-facebook-f', 'fab fa-instagram', 'fab fa-tiktok',
        'fab fa-youtube', 'fab fa-twitter', 'fab fa-x-twitter', 'fab fa-linkedin-in'
    ];

    let currentIconTargetKey = null;
    let faModalInstance = null;

    function switchAssetTab(tabName) {
        document.querySelectorAll('.asset-tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tabName);
        });
        document.querySelectorAll('.asset-tab-pane').forEach(pane => {
            pane.classList.toggle('d-none', pane.id !== 'pane-' + tabName);
        });
        sessionStorage.setItem('active_logos_icons_tab', tabName);
        history.replaceState(null, null, '#' + tabName);
    }

    function previewAndAutoSubmitAsset(fileInput, slotKey) {
        if (!fileInput.files || !fileInput.files[0]) return;
        const form = document.getElementById('masterLogosIconsForm');
        if (form) form.submit();
    }

    function removeSingleBrandAsset(slotKey) {
        if (!confirm('Voulez-vous vraiment supprimer ce visuel personnalisé et revenir à la valeur par défaut ?')) {
            return;
        }
        document.getElementById('removeSlotKeyInput').value = slotKey;
        document.getElementById('removeSingleAssetForm').submit();
    }

    function updateLiveFaIcon(key, iconClass) {
        const iconEl = document.getElementById('live_icon_' + key);
        if (iconEl) {
            iconEl.className = iconClass.trim() || 'fas fa-circle';
        }
    }

    function openFaIconPicker(targetKey) {
        currentIconTargetKey = targetKey;
        renderFaPickerGrid('');
        const searchInput = document.getElementById('faPickerSearchInput');
        if (searchInput) searchInput.value = '';
        if (!faModalInstance) {
            faModalInstance = new bootstrap.Modal(document.getElementById('faIconPickerModal'));
        }
        faModalInstance.show();
    }

    function renderFaPickerGrid(filterText) {
        const grid = document.getElementById('faPickerGrid');
        if (!grid) return;
        const q = (filterText || '').toLowerCase().trim();
        const currentVal = document.getElementById('input_' + currentIconTargetKey)?.value?.trim() || '';

        grid.innerHTML = CURATED_FA_ICONS
            .filter(ic => !q || ic.toLowerCase().includes(q))
            .map(ic => {
                const shortName = ic.replace(/^(fas|fab|far)\s+fa-/, '');
                const activeCls = ic === currentVal ? 'active' : '';
                return `<button type="button" class="fa-picker-item ${activeCls}" onclick="selectFaIcon('${ic}')">
                    <i class="${ic}"></i>
                    <span>${shortName}</span>
                </button>`;
            }).join('');
    }

    function filterFaPickerIcons(val) {
        renderFaPickerGrid(val);
    }

    function selectFaIcon(iconClass) {
        if (!currentIconTargetKey) return;
        const input = document.getElementById('input_' + currentIconTargetKey);
        if (input) {
            input.value = iconClass;
        }
        updateLiveFaIcon(currentIconTargetKey, iconClass);
        if (faModalInstance) {
            faModalInstance.hide();
        }
    }

    function filterAssetCards(selector, query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll(selector).forEach(col => {
            const name = col.dataset.name || '';
            col.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash.replace('#', '');
        const saved = sessionStorage.getItem('active_logos_icons_tab');
        const initial = ['logos', 'ui-icons', 'categories', 'brands'].includes(hash) ? hash : (saved || 'logos');
        switchAssetTab(initial);
    });
</script>
@endpush
