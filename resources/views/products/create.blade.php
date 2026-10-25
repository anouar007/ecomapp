@extends('layouts.app')

@section('title', 'Nouveau produit')

@push('styles')
<style>
    /* Product Form Styles */
    .product-form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .product-form-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .product-form-card-title {
        font-family: var(--font-heading, 'Outfit', sans-serif);
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-form-card-subtitle {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 4px;
    }

    /* Multi Image Upload Grid */
    .multi-image-upload {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 12px;
        margin-top: 10px;
    }

    .image-upload-box {
        width: 100%;
        aspect-ratio: 1 / 1;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8fafc;
        position: relative;
        overflow: hidden;
    }

    .image-upload-box:hover {
        border-color: #dc2626;
        background: #fef2f2;
    }

    .image-upload-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-remove-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(220, 38, 38, 0.9);
        color: #ffffff;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        z-index: 10;
        transition: transform 0.15s ease;
    }

    .image-remove-btn:hover {
        transform: scale(1.15);
        background: #dc2626;
    }

    .primary-badge-indicator {
        position: absolute;
        top: 6px;
        left: 6px;
        background: #10b981;
        color: #ffffff;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        z-index: 5;
    }

    /* Live Profit Box */
    .profit-calc-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        margin-top: 14px;
        transition: all 0.2s ease;
    }

    @media (max-width: 767.98px) {
        .product-form-card {
            padding: 16px 14px;
            margin-bottom: 16px;
            border-radius: 14px;
        }
        .product-form-card-header {
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .multi-image-upload {
            grid-template-columns: repeat(auto-fill, minmax(85px, 1fr));
            gap: 8px;
        }
        .product-form-card-title {
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- En-tête de la page -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Produits</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-dark fw-semibold">Ajouter un produit</span>
                </div>
                <h1 class="brand-title">Ajouter un produit</h1>
                <p class="brand-subtitle">Créez une nouvelle fiche produit dans votre catalogue avec tarifs et gestion du stock</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-1.5"></i> <span>Retour aux produits</span>
            </a>
            <button type="submit" form="productForm" id="btn-create-product-top" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3.5 py-1.5 shadow-sm rounded-pill" style="font-size: 0.84rem;">
                <i class="fas fa-save me-1.5"></i> <span>Enregistrer</span>
            </button>
        </div>
    </div>

    <!-- Alertes Erreurs -->
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <div class="d-flex align-items-start gap-3">
                <i class="fas fa-exclamation-circle fs-5 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-1">Veuillez corriger les erreurs suivantes :</h6>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm" novalidate>
        @csrf

        <div class="row g-4">
            <!-- Colonne Principale (Gauche) -->
            <div class="col-12 col-lg-8">
                <!-- Informations Générales -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-box text-danger"></i> Informations Générales
                            </h2>
                            <p class="product-form-card-subtitle">Nom, référence SKU et détails de votre produit</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Nom du produit -->
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold text-dark">
                                Nom du produit <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
                                   placeholder="ex: Caméra Sony Alpha 7 IV Plein Format" 
                                   required 
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU & Générateur -->
                        <div class="col-12">
                            <label for="sku" class="form-label fw-semibold text-dark">
                                Référence SKU <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" 
                                       id="sku" 
                                       name="sku" 
                                       class="form-control font-monospace @error('sku') is-invalid @enderror" 
                                       value="{{ old('sku') }}" 
                                       placeholder="Généré automatiquement ou personnalisé" 
                                       required>
                                <button type="button" 
                                        onclick="generateSKU()" 
                                        class="btn btn-outline-secondary d-flex align-items-center gap-1.5"
                                        title="Générer un code SKU unique">
                                    <i class="fas fa-magic text-warning"></i>
                                    <span>Générer</span>
                                </button>
                            </div>
                            <div class="form-text small text-muted">
                                <i class="fas fa-info-circle me-1"></i> Identifiant unique pour la gestion des stocks et la caisse POS.
                            </div>
                            @error('sku')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description Détaillée avec Smart Editor -->
                    <div class="mt-4">
                        @include('products.partials.smart-editor', ['value' => old('description')])
                    </div>
                </div>

                <!-- Galerie & Photographies -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-images text-primary"></i> Photographies du Produit
                            </h2>
                            <p class="product-form-card-subtitle">Ajoutez des visuels haute résolution pour la vitrine client</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="multi-image-upload" id="imagePreviewContainer">
                            <div class="image-upload-box" onclick="document.getElementById('images').click()">
                                <i class="fas fa-cloud-upload-alt fs-3 text-muted mb-1"></i>
                                <span class="small text-muted fw-semibold" style="font-size: 0.72rem;">Téléverser</span>
                            </div>
                        </div>
                        <input type="file" 
                               id="images" 
                               name="images[]" 
                               accept="image/*" 
                               multiple 
                               style="display: none;" 
                               onchange="handleMultipleImages(event)">
                    </div>

                    <div class="alert alert-light border rounded-3 p-3 mb-0 small text-muted">
                        <div class="d-flex align-items-center gap-2 mb-1 text-dark fw-semibold">
                            <i class="fas fa-wand-magic-sparkles text-warning"></i> Téléchargement automatique officiel
                        </div>
                        Si vous ne téléversez aucune image, le système recherchera et appliquera automatiquement le master officiel 4K Ultra-HD à partir du nom du produit dès l'enregistrement.
                    </div>
                </div>
            </div>

            <!-- Colonne Latérale (Droite) -->
            <div class="col-12 col-lg-4">
                <!-- Tarification & Rentabilité -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <h2 class="product-form-card-title">
                            <i class="fas fa-tag text-success"></i> Tarification
                        </h2>
                    </div>

                    <!-- Nouveau prix de vente -->
                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold text-dark d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-tag text-success me-1"></i> Nouveau prix (Prix de vente TTC) <span class="text-danger">*</span></span>
                            <span class="badge bg-success-subtle text-success small">Prix actuel</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">
                                <i class="fas fa-money-bill-wave"></i>
                            </span>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   class="form-control form-control-lg fw-bold @error('price') is-invalid @enderror" 
                                   value="{{ old('price') }}" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   required 
                                   oninput="updatePricingAndProfit()">
                            <span class="input-group-text bg-light text-muted fw-bold">
                                {{ setting('currency_symbol', 'MAD') }}
                            </span>
                        </div>
                        <div class="form-text text-muted small mt-1">
                            Prix réel et effectif payé par le client.
                        </div>
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ancien prix (Prix barré / Promo) -->
                    <div class="mb-3">
                        <label for="old_price" class="form-label fw-semibold text-dark d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-history text-secondary me-1"></i> Ancien prix (Prix barré)</span>
                            <span class="badge bg-light text-muted border small">Optionnel</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">
                                <i class="fas fa-strikethrough"></i>
                            </span>
                            <input type="number" 
                                   id="old_price" 
                                   name="old_price" 
                                   class="form-control @error('old_price') is-invalid @enderror" 
                                   value="{{ old('old_price') }}" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   oninput="updatePricingAndProfit()">
                            <span class="input-group-text bg-light text-muted fw-bold">
                                {{ setting('currency_symbol', 'MAD') }}
                            </span>
                        </div>
                        <div class="form-text text-muted small mt-1">
                            Affiché barré pour mettre en valeur la promotion (ex: <del class="text-muted">799 MAD</del> <strong class="text-success">599 MAD</strong>).
                        </div>
                        @error('old_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <!-- Promo pill live preview -->
                        <div id="promoBadgeBox" class="mt-2 p-2 rounded-3 bg-danger-subtle border border-danger-subtle text-danger small d-none align-items-center justify-content-between">
                            <span><i class="fas fa-fire me-1"></i> Remise : <strong id="promoPercentVal">-0%</strong></span>
                            <span class="text-muted" id="promoSavingsVal">Économie: 0.00 MAD</span>
                        </div>
                        <div id="promoWarningBox" class="mt-2 p-2 rounded-3 bg-warning-subtle text-warning-emphasis small d-none">
                            <i class="fas fa-exclamation-triangle me-1"></i> L'ancien prix doit être supérieur au nouveau prix pour appliquer une réduction.
                        </div>
                    </div>

                    <!-- Prix d'achat / revient -->
                    <div class="mb-3">
                        <label for="cost_price" class="form-label fw-semibold text-dark">
                            Prix d'achat / revient ({{ setting('currency_symbol', 'MAD') }})
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">
                                <i class="fas fa-coins"></i>
                            </span>
                            <input type="number" 
                                   id="cost_price" 
                                   name="cost_price" 
                                   class="form-control @error('cost_price') is-invalid @enderror" 
                                   value="{{ old('cost_price') }}" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   oninput="updatePricingAndProfit()">
                        </div>
                        <div class="form-text small text-muted">Votre coût d'achat unitaire pour estimer la marge.</div>
                        @error('cost_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Calcul en direct de la marge (Cartes KPI) -->
                    <div class="profit-calc-box" id="profitCalcBox" style="display: none;">
                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-white border border-success-subtle shadow-xs">
                                    <div class="text-muted small fw-semibold" style="font-size: 0.7rem;">Marge brute</div>
                                    <div class="fw-bold text-success fs-6" id="profitMarginVal">0%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-white border shadow-xs">
                                    <div class="text-muted small fw-semibold" style="font-size: 0.7rem;">Bénéfice net</div>
                                    <div class="fw-bold text-dark fs-6" id="profitAmountVal">0.00 MAD</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organisation & Stock -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <h2 class="product-form-card-title">
                            <i class="fas fa-warehouse text-warning"></i> Organisation & Stock
                        </h2>
                    </div>

                    <!-- Catégories Multiples -->
                    @php
                        $selectedCategoryIds = old('category_ids', old('category_id') ? [(int)old('category_id')] : []);
                        if (!is_array($selectedCategoryIds)) {
                            $selectedCategoryIds = [$selectedCategoryIds];
                        }
                        $selectedCategoryIds = array_map('intval', $selectedCategoryIds);
                        $primaryCatId = old('category_id', $selectedCategoryIds[0] ?? null);
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark mb-0">
                                <i class="fas fa-tags text-primary me-1"></i> Catégories assignées
                            </label>
                            <span class="badge bg-light text-primary border" id="categoryCountBadge">
                                <span id="selectedCatCount">{{ count($selectedCategoryIds) }}</span> sélectionnée(s)
                            </span>
                        </div>
                        <div class="form-text text-muted small mb-2">
                            Sélectionnez une ou plusieurs catégories où apparaîtra ce produit.
                        </div>

                        <!-- Badges des catégories sélectionnées -->
                        <div id="selectedCategoryChips" class="d-flex flex-wrap gap-1 mb-2 p-2 rounded bg-light border" style="min-height: 42px;">
                            @forelse($categories->whereIn('id', $selectedCategoryIds) as $selCat)
                                <span class="badge {{ (int)$primaryCatId === (int)$selCat->id ? 'bg-primary' : 'bg-secondary' }} d-inline-flex align-items-center gap-1 py-1 px-2 selected-chip" data-id="{{ $selCat->id }}">
                                    @if((int)$primaryCatId === (int)$selCat->id)
                                        <i class="fas fa-star text-warning" title="Catégorie Principale"></i>
                                    @endif
                                    <span class="text-truncate" style="max-width: 180px;">{{ $selCat->name }}</span>
                                    <button type="button" class="btn-close btn-close-white btn-sm ms-1 uncheck-cat-btn" data-id="{{ $selCat->id }}" style="font-size: 0.65rem;" aria-label="Supprimer"></button>
                                </span>
                            @empty
                                <span class="text-muted small fst-italic py-1 px-2 no-cat-placeholder">Aucune catégorie sélectionnée</span>
                            @endforelse
                        </div>

                        <!-- Champ de recherche de catégorie -->
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="categoryFilterInput" class="form-control" placeholder="Rechercher une catégorie..." autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="btnClearCatFilter" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Liste scrollable des catégories avec checkboxes et bouton radio principale -->
                        <div class="category-selection-box border rounded p-2 bg-white" style="max-height: 220px; overflow-y: auto;">
                            @foreach($categories as $category)
                                @php
                                    $isCatChecked = in_array((int)$category->id, $selectedCategoryIds);
                                    $isPrimary = (int)$primaryCatId === (int)$category->id;
                                @endphp
                                <div class="category-item d-flex align-items-center justify-content-between py-1 px-2 rounded" data-name="{{ strtolower($category->breadcrumb) }}" style="transition: background 0.15s ease;">
                                    <div class="form-check mb-0 flex-grow-1 text-truncate">
                                        <input class="form-check-input category-checkbox" 
                                               type="checkbox" 
                                               name="category_ids[]" 
                                               value="{{ $category->id }}" 
                                               id="cat_cb_{{ $category->id }}"
                                               data-name="{{ $category->name }}"
                                               data-breadcrumb="{{ $category->breadcrumb }}"
                                               {{ $isCatChecked ? 'checked' : '' }}>
                                        <label class="form-check-label small text-truncate d-inline-block w-100" for="cat_cb_{{ $category->id }}" style="cursor: pointer;" title="{{ $category->breadcrumb }}">
                                            {{ $category->breadcrumb }}
                                        </label>
                                    </div>
                                    <div class="cat-primary-selector ms-2">
                                        <label class="badge {{ $isPrimary ? 'bg-primary text-white' : 'bg-light text-muted border' }} small py-1 px-2 radio-label" 
                                               style="font-size: 0.72rem; cursor: pointer;" 
                                               title="Définir comme catégorie principale">
                                            <input type="radio" 
                                                   name="category_id" 
                                                   value="{{ $category->id }}" 
                                                   class="d-none category-primary-radio" 
                                                   id="cat_primary_{{ $category->id }}"
                                                   {{ $isPrimary ? 'checked' : '' }}>
                                            <i class="fas fa-star {{ $isPrimary ? 'text-warning' : 'text-muted' }} me-1"></i>
                                            <span class="primary-text">{{ $isPrimary ? 'Principale' : 'Définir princ.' }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="fas fa-info-circle text-info"></i> L'étoile <i class="fas fa-star text-warning"></i> marque la catégorie principale.
                            </small>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none text-danger p-0" id="btnDeselectAllCategories" style="font-size: 0.75rem;">
                                Tout désélectionner
                            </button>
                        </div>

                        @error('category_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('category_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold text-dark">
                            Statut de visibilité <span class="text-danger">*</span>
                        </label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>🟢 Actif (Visible en ligne)</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>⚪ Inactif (Brouillon masqué)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock initial -->
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="stock" class="form-label fw-semibold text-dark">
                                Stock <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   id="stock" 
                                   name="stock" 
                                   class="form-control @error('stock') is-invalid @enderror" 
                                   value="{{ old('stock', 0) }}" 
                                   placeholder="0" 
                                   min="0" 
                                   required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock d'alerte -->
                        <div class="col-6">
                            <label for="min_stock" class="form-label fw-semibold text-dark">
                                Seuil alerte <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   id="min_stock" 
                                   name="min_stock" 
                                   class="form-control @error('min_stock') is-invalid @enderror" 
                                   value="{{ old('min_stock', 5) }}" 
                                   placeholder="5" 
                                   min="0" 
                                   required>
                            @error('min_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Boutons d'Action -->
                <div class="product-form-card">
                    <button type="submit" class="btn-brand-primary w-100 py-2.5 justify-content-center shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> Créer le produit
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 py-2 mt-2 rounded-pill text-center d-block text-decoration-none">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.getElementById('btn-create-product-top')?.addEventListener('click', function(e) {
    const form = document.getElementById('productForm');
    if (form) {
        e.preventDefault();
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }
    }
});

let selectedFiles = [];

function handleMultipleImages(event) {
    const files = Array.from(event.target.files);
    selectedFiles = selectedFiles.concat(files);
    displayImages();
}

function displayImages() {
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const box = document.createElement('div');
            box.className = 'image-upload-box';
            box.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${index + 1}">
                ${index === 0 ? '<span class="primary-badge-indicator">Principale</span>' : ''}
                <span class="image-remove-btn" onclick="removeImage(${index})" title="Supprimer">&times;</span>
            `;
            container.appendChild(box);
        }
        reader.readAsDataURL(file);
    });
    
    // Add upload button at the end
    const uploadBox = document.createElement('div');
    uploadBox.className = 'image-upload-box';
    uploadBox.onclick = () => document.getElementById('images').click();
    uploadBox.innerHTML = `
        <i class="fas fa-plus fs-4 text-muted mb-1"></i>
        <span class="small text-muted fw-semibold" style="font-size: 0.72rem;">Ajouter</span>
    `;
    container.appendChild(uploadBox);
    
    updateFileInput();
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    displayImages();
}

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    document.getElementById('images').files = dataTransfer.files;
}

// SKU Auto-Generation
function generateSKU() {
    const name = document.getElementById('name')?.value || '';
    const checkedPrimary = document.querySelector('input[name="category_id"]:checked');
    const primaryCatId = checkedPrimary ? checkedPrimary.value : (document.querySelector('input[name="category_ids[]"]:checked')?.value || null);
    let categoryPrefix = 'PROD';
    
    if (primaryCatId) {
        const cb = document.getElementById('cat_cb_' + primaryCatId);
        const categoryName = cb ? (cb.getAttribute('data-name') || cb.getAttribute('data-breadcrumb') || '') : '';
        const words = categoryName.split(/[\s\->]+/).filter(w => w.length > 0);
        if (words.length >= 2) {
            categoryPrefix = (words[0].substring(0, 2) + words[1].substring(0, 2)).toUpperCase();
        } else if (words.length === 1) {
            categoryPrefix = words[0].substring(0, 4).toUpperCase();
        }
    } else if (name) {
        const words = name.split(/\s+/).filter(w => w.length > 0);
        if (words.length >= 2) {
            categoryPrefix = (words[0].substring(0, 2) + words[1].substring(0, 2)).toUpperCase();
        } else if (words.length === 1) {
            categoryPrefix = words[0].substring(0, 4).toUpperCase();
        }
    }
    
    const randomNum = Math.floor(Math.random() * 90000) + 10000;
    const timestamp = Date.now().toString().slice(-4);
    const sku = `${categoryPrefix}-${timestamp}${randomNum}`.substring(0, 16);
    
    const skuInput = document.getElementById('sku');
    if (skuInput) {
        skuInput.value = sku;
        skuInput.style.backgroundColor = '#ecfdf5';
        setTimeout(() => {
            skuInput.style.backgroundColor = '';
        }, 600);
    }
}

// Live Pricing & Profit Calculation
function updatePricingAndProfit() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const oldPrice = parseFloat(document.getElementById('old_price').value) || 0;
    const cost = parseFloat(document.getElementById('cost_price').value) || 0;
    
    // 1. Promo Badge live preview
    const promoBox = document.getElementById('promoBadgeBox');
    const warningBox = document.getElementById('promoWarningBox');
    
    if (oldPrice > 0 && price > 0) {
        if (oldPrice > price) {
            const savings = oldPrice - price;
            const discountPercent = Math.round((savings / oldPrice) * 100);
            document.getElementById('promoPercentVal').textContent = '-' + discountPercent + '%';
            document.getElementById('promoSavingsVal').textContent = 'Économie: ' + savings.toFixed(2) + ' {{ setting("currency_symbol", "MAD") }}';
            promoBox.classList.remove('d-none');
            promoBox.classList.add('d-flex');
            warningBox.classList.add('d-none');
        } else {
            promoBox.classList.add('d-none');
            promoBox.classList.remove('d-flex');
            warningBox.classList.remove('d-none');
        }
    } else {
        promoBox.classList.add('d-none');
        promoBox.classList.remove('d-flex');
        warningBox.classList.add('d-none');
    }

    // 2. Profit margin calculation
    const profitBox = document.getElementById('profitCalcBox');
    if (price > 0 && cost > 0) {
        const profit = price - cost;
        const margin = ((profit / price) * 100).toFixed(1);
        
        profitBox.style.display = 'block';
        document.getElementById('profitMarginVal').textContent = margin + '%';
        document.getElementById('profitMarginVal').className = profit >= 0 ? 'fw-bold text-success' : 'fw-bold text-danger';
        document.getElementById('profitAmountVal').textContent = profit.toFixed(2) + ' {{ setting("currency_symbol", "MAD") }}';
    } else {
        profitBox.style.display = 'none';
    }
}
window.updateProfitCalc = updatePricingAndProfit;

document.addEventListener('DOMContentLoaded', function() {
    const skuField = document.getElementById('sku');
    if (!skuField.value) {
        generateSKU();
    }
    
    let nameTimeout;
    document.getElementById('name').addEventListener('input', function() {
        clearTimeout(nameTimeout);
        nameTimeout = setTimeout(() => {
            if (!document.getElementById('sku').value && this.value.length > 3) {
                generateSKU();
            }
        }, 800);
    });

    updateProfitCalc();

    // Multi-category selector management
    (function() {
        const filterInput = document.getElementById('categoryFilterInput');
        const clearBtn = document.getElementById('btnClearCatFilter');
        const items = document.querySelectorAll('.category-item');
        const checkboxes = document.querySelectorAll('.category-checkbox');
        const primaryRadios = document.querySelectorAll('.category-primary-radio');
        const chipsContainer = document.getElementById('selectedCategoryChips');
        const countSpan = document.getElementById('selectedCatCount');
        const deselectAllBtn = document.getElementById('btnDeselectAllCategories');

        // 1. Live Filter
        if (filterInput) {
            filterInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                if (clearBtn) clearBtn.style.display = query.length > 0 ? 'inline-block' : 'none';
                items.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    item.style.display = name.includes(query) ? 'flex' : 'none';
                });
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    filterInput.value = '';
                    clearBtn.style.display = 'none';
                    items.forEach(item => item.style.display = 'flex');
                    filterInput.focus();
                });
            }
        }

        // 2. Render chips & sync state
        function updateSelectedChips() {
            const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
            if (countSpan) countSpan.textContent = checkedBoxes.length;

            let checkedRadio = Array.from(primaryRadios).find(r => r.checked);
            if (checkedBoxes.length > 0 && (!checkedRadio || !document.getElementById('cat_cb_' + checkedRadio.value)?.checked)) {
                const firstRadio = document.getElementById('cat_primary_' + checkedBoxes[0].value);
                if (firstRadio) {
                    firstRadio.checked = true;
                    checkedRadio = firstRadio;
                }
            } else if (checkedBoxes.length === 0 && checkedRadio) {
                checkedRadio.checked = false;
                checkedRadio = null;
            }

            primaryRadios.forEach(r => {
                const label = r.closest('.radio-label');
                const star = label ? label.querySelector('.fa-star') : null;
                const text = label ? label.querySelector('.primary-text') : null;
                if (r.checked) {
                    if (label) label.className = 'badge bg-primary text-white small cursor-pointer py-1 px-2 radio-label';
                    if (star) star.className = 'fas fa-star text-warning me-1';
                    if (text) text.textContent = 'Principale';
                } else {
                    if (label) label.className = 'badge bg-light text-muted border small cursor-pointer py-1 px-2 radio-label';
                    if (star) star.className = 'fas fa-star text-muted me-1';
                    if (text) text.textContent = 'Définir princ.';
                }
            });

            if (!chipsContainer) return;

            if (checkedBoxes.length === 0) {
                chipsContainer.innerHTML = '<span class="text-muted small fst-italic py-1 px-2 no-cat-placeholder">Aucune catégorie sélectionnée</span>';
                return;
            }

            const primaryVal = checkedRadio ? checkedRadio.value : (checkedBoxes[0] ? checkedBoxes[0].value : null);
            let chipsHtml = '';
            checkedBoxes.forEach(cb => {
                const isPrim = cb.value === primaryVal;
                const catName = cb.getAttribute('data-name') || cb.value;
                const catBreadcrumb = cb.getAttribute('data-breadcrumb') || catName;
                chipsHtml += `
                    <span class="badge ${isPrim ? 'bg-primary' : 'bg-secondary'} d-inline-flex align-items-center gap-1 py-1 px-2 selected-chip" data-id="${cb.value}" title="${catBreadcrumb}">
                        ${isPrim ? '<i class="fas fa-star text-warning" title="Catégorie Principale"></i>' : ''}
                        <span class="text-truncate" style="max-width: 180px;">${catName}</span>
                        <button type="button" class="btn-close btn-close-white btn-sm ms-1 uncheck-cat-btn" data-id="${cb.value}" style="font-size: 0.65rem;" aria-label="Supprimer"></button>
                    </span>
                `;
            });
            chipsContainer.innerHTML = chipsHtml;
        }

        // 3. Checkbox events
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) {
                    const currentlyChecked = Array.from(checkboxes).filter(c => c.checked);
                    if (currentlyChecked.length === 1) {
                        const r = document.getElementById('cat_primary_' + this.value);
                        if (r) r.checked = true;
                    }
                } else {
                    const r = document.getElementById('cat_primary_' + this.value);
                    if (r && r.checked) {
                        r.checked = false;
                        const remaining = Array.from(checkboxes).filter(c => c.checked);
                        if (remaining.length > 0) {
                            const nextR = document.getElementById('cat_primary_' + remaining[0].value);
                            if (nextR) nextR.checked = true;
                        }
                    }
                }
                updateSelectedChips();
                generateSKU();
            });
        });

        // 4. Primary radio changes
        primaryRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const cb = document.getElementById('cat_cb_' + this.value);
                if (cb && !cb.checked) {
                    cb.checked = true;
                }
                updateSelectedChips();
                generateSKU();
            });
        });

        // 5. Remove chip button
        chipsContainer.addEventListener('click', function(e) {
            const btn = e.target.closest('.uncheck-cat-btn');
            if (btn) {
                const catId = btn.getAttribute('data-id');
                const cb = document.getElementById('cat_cb_' + catId);
                if (cb) {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change'));
                }
            }
        });

        // 6. Deselect all
        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(cb => cb.checked = false);
                primaryRadios.forEach(r => r.checked = false);
                updateSelectedChips();
                generateSKU();
            });
        }

        updateSelectedChips();
    })();
});
</script>
@endpush
