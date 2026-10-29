@extends('layouts.app')

@section('title', 'Modifier le produit : ' . $product->name)

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
        flex-wrap: wrap;
        gap: 10px;
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
        font-size: 13px;
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

    /* Variation Cards 2-Row Layout */
    .variant-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 18px;
        transition: all 0.2s ease;
        position: relative;
    }
    .variant-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    }
    .variant-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .variant-card-badge {
        font-size: 0.72rem;
        font-weight: 700;
        font-family: var(--bs-font-monospace, monospace);
        padding: 4px 10px;
        border-radius: 6px;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
    }
    .variant-card .form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .variant-card .form-control {
        font-size: 0.92rem;
        border-radius: 8px;
        padding: 8px 12px;
    }
    .variant-card .input-group-text {
        font-size: 0.82rem;
        border-radius: 8px;
        background: #f8fafc;
        color: #64748b;
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
                <i class="fas fa-pencil-alt"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Produits</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-dark fw-semibold text-truncate" style="max-width: 250px;">{{ $product->name }}</span>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-muted">Modifier</span>
                </div>
                <h1 class="brand-title">Modifier : <span class="text-truncate d-inline-block align-bottom" style="max-width: 450px;">{{ $product->name }}</span></h1>
                <p class="brand-subtitle">
                    SKU: <span class="badge bg-light text-secondary border font-monospace">{{ $product->sku }}</span> &bull; 
                    Statut: <span class="badge {{ strtolower($product->status) === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ ucfirst($product->status) }}</span>
                </p>
            </div>
        </div>
        <div class="brand-header-actions flex-wrap">
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1.5 text-muted fw-semibold d-inline-flex align-items-center" title="Retour à la liste">
                <i class="fas fa-arrow-left me-1"></i> <span>Retour</span>
            </a>
            <a href="{{ Route::has('shop.show') ? route('shop.show', $product->slug ?: $product->id) : url('/shop/' . ($product->slug ?: $product->id)) }}" 
               target="_blank" 
               class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-1 flex-grow-1 flex-md-grow-0 shadow-sm"
               title="Voir sur la boutique">
                <i class="fas fa-eye text-teal"></i> <span class="d-none d-sm-inline">Voir boutique</span><span class="d-sm-none">Boutique</span>
            </a>
            <button type="button" 
                    class="btn btn-sm text-white rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-1 flex-grow-1 flex-md-grow-0 shadow-sm"
                    style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none;"
                    onclick="open4kImageModal()"
                    title="Studio 4K Ultra-HD">
                <i class="fas fa-wand-magic-sparkles text-warning"></i> <span>Studio 4K</span>
            </button>
            <button type="submit" form="productForm" id="btn-save-product-top" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3.5 py-1.5 shadow-sm rounded-pill" style="font-size: 0.84rem;">
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

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm" novalidate>
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Colonne Principale (Gauche) -->
            <div class="col-12 col-lg-8">
                <!-- Photographies & Galerie -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-images text-primary"></i> Galerie & Photographies Studio 4K
                            </h2>
                            <p class="product-form-card-subtitle">Gérez les images de la vitrine ou recherchez des masters 4K Ultra-HD</p>
                        </div>
                        <button type="button" 
                                class="btn btn-sm text-white rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm"
                                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none; font-size: 0.8rem;"
                                onclick="open4kImageModal()">
                            <i class="fas fa-wand-magic-sparkles text-warning"></i> Trouver images 4K Studio
                        </button>
                    </div>

                    <div class="mb-3">
                        <div class="multi-image-upload" id="imagePreviewContainer">
                            <!-- Images existantes -->
                            @foreach($product->images as $image)
                                <div class="image-upload-box" id="box-img-{{ $image->id }}">
                                    <img src="{{ $image->url }}" alt="Image produit" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    @if($image->is_primary)
                                        <span class="primary-badge-indicator">Principale</span>
                                    @endif
                                    <span class="image-remove-btn" onclick="markImageForRemoval({{ $image->id }}, this)" title="Supprimer cette image">&times;</span>
                                </div>
                            @endforeach

                            <!-- Image principale de repli si la relation est vide -->
                            @if($product->images->isEmpty() && ($product->image || $product->thumbnail))
                                <div class="image-upload-box" id="box-img-current">
                                    <img src="{{ $product->thumbnail }}" alt="Image produit" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    <span class="primary-badge-indicator">Actuelle</span>
                                </div>
                            @endif

                            <!-- Bouton pour ajouter plus -->
                            <div class="image-upload-box" onclick="document.getElementById('images').click()">
                                <i class="fas fa-plus fs-4 text-muted mb-1"></i>
                                <span class="small text-muted fw-semibold" style="font-size: 0.72rem;">Ajouter</span>
                            </div>
                        </div>

                        <input type="file" 
                               id="images" 
                               name="images[]" 
                               accept="image/*" 
                               multiple 
                               style="display: none;" 
                               onchange="handleNewImages(event)">
                    </div>

                    <div class="alert alert-light border rounded-3 p-2.5 mb-0 small text-muted d-flex align-items-start gap-2">
                        <i class="fas fa-info-circle text-primary mt-0.5"></i>
                        <div>Touchez <strong>×</strong> pour retirer une photo. Utilisez <strong>Studio 4K</strong> pour importer des visuels officiels ultra-HD.</div>
                    </div>
                </div>

                <!-- Informations Générales -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-box text-danger"></i> Informations Générales
                            </h2>
                            <p class="product-form-card-subtitle">Nom, référence SKU et description de votre produit</p>
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
                                   value="{{ old('name', $product->name) }}" 
                                   placeholder="Nom du produit" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU -->
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
                                       value="{{ old('sku', $product->sku) }}" 
                                       placeholder="ex: PROD-001" 
                                       required>
                            </div>
                            <div class="form-text small text-muted">
                                Identifiant unique utilisé pour le code-barres et la gestion des stocks.
                            </div>
                            @error('sku')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description Détaillée avec Smart Editor -->
                    <div class="mt-4">
                        @include('products.partials.smart-editor', ['value' => old('description', $product->description)])
                    </div>
                </div>

                <!-- Variations & Déclinaisons de Produit -->
                <div class="product-form-card" id="productVariationsCard">
                    <div class="product-form-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-layer-group text-danger"></i> Variations & Déclinaisons
                            </h2>
                            <p class="product-form-card-subtitle">Ex: Carte SD (512GB, 256GB, 128GB), couleur, ou kit avec objectif</p>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="has_variants" id="hasVariantsToggle" value="1" {{ old('has_variants', $product->has_variants) ? 'checked' : '' }} onchange="toggleVariationsSection(this.checked)" style="width: 2.75em; height: 1.4em; cursor: pointer;">
                            <label class="form-check-label fw-bold text-dark ms-1 small" for="hasVariantsToggle" style="cursor: pointer;">
                                Activer les variations
                            </label>
                        </div>
                    </div>

                    <div id="variationsContainer" style="{{ old('has_variants', $product->has_variants) ? '' : 'display: none;' }}">
                        <div class="alert alert-light border rounded-3 p-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="small text-muted">
                                <i class="fas fa-info-circle text-primary me-1"></i>
                                Chaque variation possède son propre <strong>prix</strong> et <strong>stock</strong>. Le stock total du produit sera calculé automatiquement.
                            </div>
                            <button type="button" class="btn btn-sm btn-brand-primary rounded-pill px-3 py-1.5" onclick="addVariantRow()">
                                <i class="fas fa-plus me-1"></i> Ajouter une variante
                            </button>
                        </div>

                        <!-- Dynamic 2-Row Variant Cards Container -->
                        <div id="variantsCardsContainer" class="d-flex flex-column gap-3 mb-3">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top px-1 flex-wrap gap-2">
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5" id="variantSummaryBadge" style="font-size: 0.8rem;">
                                <i class="fas fa-boxes text-danger me-1"></i> <span id="variantCount">{{ $product->variants->count() }}</span> variante(s) &bull; Stock total : <strong id="variantTotalStock" class="text-dark">{{ $product->variants->sum('stock') }}</strong>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5" onclick="addVariantRow()">
                                <i class="fas fa-plus me-1"></i> + Variante supplémentaire
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Latérale (Droite) -->
            <div class="col-12 col-lg-4">
                <!-- Tarification & Rentabilité -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <h2 class="product-form-card-title">
                            <i class="fas fa-tag text-success"></i> Tarifs & Rentabilité
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
                                   value="{{ old('price', $product->isOnSale() ? $product->sale_price : $product->price) }}" 
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
                                   value="{{ old('old_price', $product->isOnSale() ? $product->price : '') }}" 
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
                        <div id="promoBadgeBox" class="mt-2 p-2 rounded-3 bg-danger-subtle border border-danger-subtle text-danger small {{ $product->isOnSale() ? 'd-flex' : 'd-none' }} align-items-center justify-content-between">
                            <span><i class="fas fa-fire me-1"></i> Remise : <strong id="promoPercentVal">-{{ $product->discount_percentage }}%</strong></span>
                            <span class="text-muted" id="promoSavingsVal">Économie: {{ currency(($product->price ?? 0) - ($product->sale_price ?? 0)) }}</span>
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
                                   value="{{ old('cost_price', $product->cost_price) }}" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   oninput="updatePricingAndProfit()">
                        </div>
                        <div class="form-text small text-muted">Coût unitaire pour calculer votre marge commerciale.</div>
                        @error('cost_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Calcul en direct de la marge (Cartes KPI) -->
                    <div class="profit-calc-box" id="profitCalcBox" style="{{ $product->cost_price ? 'display: block;' : 'display: none;' }}">
                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-white border border-success-subtle shadow-xs">
                                    <div class="text-muted small fw-semibold" style="font-size: 0.7rem;">Marge brute</div>
                                    <div class="fw-bold text-success fs-6" id="profitMarginVal">
                                        {{ $product->cost_price && $product->active_price > 0 ? number_format((($product->active_price - $product->cost_price) / $product->active_price) * 100, 1) . '%' : '0%' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-white border shadow-xs">
                                    <div class="text-muted small fw-semibold" style="font-size: 0.7rem;">Bénéfice net</div>
                                    <div class="fw-bold text-dark fs-6" id="profitAmountVal">
                                        {{ currency($product->active_price - ($product->cost_price ?? 0)) }}
                                    </div>
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
                        $selectedCategoryIds = old('category_ids', $product->category_ids ?? ($product->category_id ? [(int)$product->category_id] : []));
                        if (!is_array($selectedCategoryIds)) {
                            $selectedCategoryIds = [$selectedCategoryIds];
                        }
                        $selectedCategoryIds = array_map('intval', $selectedCategoryIds);
                        $primaryCatId = old('category_id', $product->category_id ?? ($selectedCategoryIds[0] ?? null));
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

                    <!-- Marque du Produit -->
                    <div class="mb-4">
                        <label for="brand_id" class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-tag text-primary me-1"></i> Marque</span>
                            @can('manage_products')
                                <a href="{{ route('brands.create') }}" target="_blank" class="small text-decoration-none text-primary" style="font-size: 0.78rem;">
                                    <i class="fas fa-plus-circle me-1"></i>Nouvelle marque
                                </a>
                            @endcan
                        </label>
                        <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">-- Sans marque / Autre --</option>
                            @foreach($brands ?? [] as $b)
                                <option value="{{ $b->id }}" {{ old('brand_id', $product->brand_id) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted small">
                            Associez ce produit à sa marque pour le filtre intelligent du shop.
                        </div>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tags & Badges Produit -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-bolt text-danger me-1"></i> Tags & Badges</span>
                            @can('manage_products')
                                <a href="{{ route('product-tags.index') }}" target="_blank" class="small text-decoration-none text-danger" style="font-size: 0.78rem;">
                                    <i class="fas fa-sliders-h me-1"></i>Gérer les tags
                                </a>
                            @endcan
                        </label>
                        @php
                            $currentTagIds = old('tag_ids', $product->tags->pluck('id')->toArray());
                            $currentTagIds = array_map('intval', is_array($currentTagIds) ? $currentTagIds : []);
                        @endphp
                        <div class="border rounded p-2.5 bg-light d-flex flex-wrap gap-2 product-card-tag-container" style="min-height: 48px;">
                            @forelse($productTags ?? [] as $pTag)
                                @php $isTagSelected = in_array((int) $pTag->id, $currentTagIds); @endphp
                                <label class="d-inline-flex align-items-center gap-1.5 border rounded px-2 py-1 bg-white shadow-2xs" style="cursor: pointer; border-color: {{ $isTagSelected ? '#dc2626' : '#e5e7eb' }} !important;">
                                    <input type="checkbox" name="tag_ids[]" value="{{ $pTag->id }}" class="form-check-input mt-0" {{ $isTagSelected ? 'checked' : '' }}>
                                    <span class="product-card-tag-badge" style="{{ $pTag->inline_style }} font-size: 0.68rem !important; padding: 2px 7px !important;">
                                        @if(!empty($pTag->icon))
                                            <i class="{{ $pTag->icon }}"></i>
                                        @endif
                                        <span>{{ $pTag->name }}</span>
                                    </span>
                                </label>
                            @empty
                                <span class="text-muted small">Aucun tag actif. <a href="{{ route('product-tags.create') }}" target="_blank">Créer un tag</a></span>
                            @endforelse
                        </div>
                        <div class="form-text text-muted small">
                            Les badges sélectionnés s'afficheront et s'adapteront automatiquement à la taille de la carte produit.
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold text-dark">
                            Statut de visibilité <span class="text-danger">*</span>
                        </label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>🟢 Actif (Visible en ligne)</option>
                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>⚪ Inactif (Brouillon masqué)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ordre d'affichage / Priorité -->
                    <div class="mb-3">
                        <label for="sort_order" class="form-label fw-semibold text-dark d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-sort-numeric-down text-primary me-1"></i> Ordre d'affichage / Priorité</span>
                            <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.7rem;">0 = standard</span>
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               class="form-control @error('sort_order') is-invalid @enderror" 
                               value="{{ old('sort_order', $product->sort_order ?? 0) }}" 
                               min="0" 
                               placeholder="0">
                        <div class="form-text text-muted small">
                            Les produits avec un ordre prioritaire (ex: 1, 2, 3...) apparaissent en premier sur la boutique. Laissez 0 pour le classement automatique standard.
                        </div>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock actuel & alerte -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="stock" class="form-label fw-semibold text-dark mb-0">
                                    Stock <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-primary-subtle text-primary border small" id="stockVariantNotice" style="{{ $product->has_variants ? '' : 'display: none;' }}; font-size: 0.65rem;">
                                    <i class="fas fa-layer-group me-1"></i> Variantes
                                </span>
                            </div>
                            <input type="number" 
                                   id="stock" 
                                   name="stock" 
                                   class="form-control @error('stock') is-invalid @enderror {{ $product->has_variants ? 'bg-light' : '' }}" 
                                   value="{{ old('stock', $product->stock) }}" 
                                   placeholder="0" 
                                   min="0" 
                                   {{ $product->has_variants ? 'readonly' : '' }}
                                   required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-6">
                            <label for="min_stock" class="form-label fw-semibold text-dark">
                                Seuil alerte <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   id="min_stock" 
                                   name="min_stock" 
                                   class="form-control @error('min_stock') is-invalid @enderror" 
                                   value="{{ old('min_stock', $product->min_stock) }}" 
                                   placeholder="5" 
                                   min="0" 
                                   required>
                            @error('min_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Alerte Stock Faible si applicable -->
                    @if($product->stock <= 0)
                        <div class="alert alert-danger border-0 p-2.5 rounded-3 mb-0 small d-flex align-items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><strong>Rupture de stock :</strong> 0 unité restante.</span>
                        </div>
                    @elseif($product->isLowStock())
                        <div class="alert alert-warning border-0 p-2.5 rounded-3 mb-0 small d-flex align-items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <span><strong>Stock faible :</strong> niveau sous le seuil d'alerte ({{ $product->stock }} restant).</span>
                        </div>
                    @endif
                </div>

                <!-- Boutons d'Action -->
                <div class="product-form-card">
                    <button type="submit" class="btn-brand-primary w-100 py-2.5 justify-content-center shadow-sm">
                        <i class="fas fa-save me-2"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 py-2 mt-2 rounded-pill text-center d-block text-decoration-none">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    <!-- 4K Image Finder Modal -->
    <div id="modal4k" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; border-radius: 16px; max-width: 900px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.2);">
            <!-- Header -->
            <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: white;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 18px; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <i class="fas fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Studio Recherche d'Images & 4K</h3>
                        <p style="margin: 2px 0 0; font-size: 12px; color: #c7d2fe;">Trouvez la photo officielle ou e-commerce exacte pour votre fiche produit</p>
                    </div>
                </div>
                <button type="button" onclick="close4kImageModal()" style="background: rgba(255,255,255,0.1); border: none; font-size: 18px; color: white; cursor: pointer; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Search Bar with Product Name -->
            <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <div style="position: relative; flex: 1;">
                        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                        <input type="text" id="query4k" value="{{ $product->name }}" placeholder="Nom du produit..." style="width: 100%; padding: 10px 14px 10px 38px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none;" onkeydown="if(event.key==='Enter'){event.preventDefault(); trigger4kSearch();}">
                    </div>
                    <button type="button" id="btnRun4kSearch" onclick="trigger4kSearch()" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5; border-radius: 10px; padding: 10px 20px; font-weight: 600; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                        <i class="fas fa-bolt me-1"></i> Rechercher
                    </button>
                </div>
                <div id="chips4k" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                    <span style="font-size: 12px; color: #475569; font-weight: 500;">
                        <i class="fas fa-camera text-primary me-1"></i>Recherche par nom du produit : <strong style="color: #1e1b4b;">{{ $product->name }}</strong>
                    </span>
                    <button type="button" onclick="setQuery4k('{{ addslashes($product->name) }}')" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5" style="font-size: 11.5px; font-weight: 600; color: #4338ca; background: #eef2ff; border-color: #c7d2fe;">
                        <i class="fas fa-undo me-1"></i> Réinitialiser au nom du produit
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div id="results4kContainer" style="padding: 24px; overflow-y: auto; flex: 1; min-height: 350px;">
                <div id="loading4k" style="display: none; text-align: center; padding: 60px 20px;">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                    <p style="margin-top: 16px; font-weight: 600; color: #475569;">Recherche multi-sources des photos du produit...</p>
                    <p style="font-size: 12px; color: #94a3b8;">Exploration des distributeurs, catalogues officiels et e-commerce</p>
                </div>
                <div id="grid4k" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('btn-save-product-top')?.addEventListener('click', function(e) {
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

let imagesToRemove = [];
let newFiles = [];

function markImageForRemoval(imageId, button) {
    if (!imagesToRemove.includes(imageId)) {
        imagesToRemove.push(imageId);
        button.parentElement.style.opacity = '0.3';
        button.innerHTML = '↺';
        button.title = 'Restaurer cette image';
        
        // Add hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_images[]';
        input.value = imageId;
        input.id = 'remove_' + imageId;
        document.getElementById('productForm').appendChild(input);
    } else {
        // Undo removal
        imagesToRemove = imagesToRemove.filter(id => id !== imageId);
        button.parentElement.style.opacity = '1';
        button.innerHTML = '&times;';
        button.title = 'Supprimer cette image';
        const el = document.getElementById('remove_' + imageId);
        if (el) el.remove();
    }
}

function handleNewImages(event) {
    const container = document.getElementById('imagePreviewContainer');
    const files = Array.from(event.target.files);
    
    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const box = document.createElement('div');
            box.className = 'image-upload-box';
            box.innerHTML = `
                <img src="${e.target.result}" alt="Nouvelle image">
                <span class="image-remove-btn" onclick="this.parentElement.remove()" title="Supprimer">&times;</span>
            `;
            container.insertBefore(box, container.lastElementChild);
        };
        reader.readAsDataURL(file);
    });
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

// 4K Studio Image Finder Logic
window.setQuery4k = function(val) {
    const input = document.getElementById('query4k');
    if (input) {
        input.value = val;
        trigger4kSearch();
    }
};

function open4kImageModal() {
    const modal = document.getElementById('modal4k');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    const grid = document.getElementById('grid4k');
    if (grid.children.length === 0) {
        trigger4kSearch();
    }
}

function close4kImageModal() {
    document.getElementById('modal4k').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function trigger4kSearch() {
    const query = document.getElementById('query4k').value.trim();
    if (!query) return;

    const loading = document.getElementById('loading4k');
    const grid = document.getElementById('grid4k');
    const btn = document.getElementById('btnRun4kSearch');

    loading.style.display = 'block';
    grid.innerHTML = '';
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Recherche...';

    const searchRouteUrl = "{{ Route::has('products.search-4k-images') ? route('products.search-4k-images', $product) : url('/products/' . $product->id . '/search-4k-images') }}";
    const url = searchRouteUrl + "?q=" + encodeURIComponent(query);

    fetch(url, {
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
                    <p style="font-weight: 600; font-size: 15px; margin-bottom: 6px;">Aucune photo trouvée pour "${query}"</p>
                    <p style="font-size: 13px; color: #94a3b8; max-width: 480px; margin: 0 auto 16px;">Cliquez sur l'une des suggestions ci-dessus (SKU ou Modèle simplifié) pour élargir la recherche.</p>
                </div>
            `;
            return;
        }

        data.candidates.forEach((cand) => {
            const card = document.createElement('div');
            card.style.cssText = "background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);";
            card.onmouseenter = () => { card.style.borderColor = '#6366f1'; card.style.boxShadow = '0 8px 20px rgba(99, 102, 241, 0.15)'; };
            card.onmouseleave = () => { card.style.borderColor = '#e2e8f0'; card.style.boxShadow = '0 2px 8px rgba(0,0,0,0.04)'; };

            const is4k = (cand.width >= 1800 || cand.height >= 1800);
            const isFullHd = (cand.width >= 1000 || cand.height >= 1000);
            
            let badgeStyle = "background: #f1f5f9; color: #475569;";
            let qualityText = "HD";
            if (is4k) {
                badgeStyle = "background: linear-gradient(135deg, #059669, #10b981); color: white;";
                qualityText = "4K UHD";
            } else if (isFullHd) {
                badgeStyle = "background: linear-gradient(135deg, #0284c7, #38bdf8); color: white;";
                qualityText = "Full HD";
            }

            const previewSrc = cand.preview_url || cand.url;
            const safeUrl = (cand.url || '').replace(/'/g, "\\'");
            const safeFallback = (cand.preview_url || '').replace(/'/g, "\\'");
            const fallbackImg = "{{ asset('images/camera/cat_cameras.jpg') }}";
            const safeTitle = (cand.title || 'Photo produit').replace(/"/g, '&quot;');

            card.innerHTML = `
                <div style="position: relative; width: 100%; height: 210px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 12px;">
                    <img src="${previewSrc}" alt="Preview" referrerpolicy="no-referrer" loading="lazy" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null; this.src='${fallbackImg}';">
                    <span style="position: absolute; top: 8px; left: 8px; ${badgeStyle} font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.12);">
                        ${cand.width}×${cand.height} ${qualityText}
                    </span>
                    <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(15,23,42,0.85); backdrop-filter: blur(4px); color: #f8fafc; font-size: 10.5px; padding: 3px 8px; border-radius: 6px; font-weight: 600; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <i class="fas fa-globe me-1" style="opacity: 0.7;"></i>${cand.source || 'E-commerce'}
                    </span>
                </div>
                <div style="padding: 12px; display: flex; flex-direction: column; justify-content: space-between; flex: 1; gap: 10px; background: #ffffff; border-top: 1px solid #f1f5f9;">
                    <p style="margin: 0; font-size: 12px; color: #334155; line-height: 1.35; font-weight: 500; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="${safeTitle}">
                        ${cand.title || 'Photo produit'}
                    </p>
                    <button type="button" onclick="applySelected4kImage('${safeUrl}', '${safeFallback}', this)" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 8px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                        <i class="fas fa-check-circle"></i> Définir comme photo principale
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
        grid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; padding: 20px; text-align: center;">Erreur : ${err.message}</div>`;
    });
}

function applySelected4kImage(imageUrl, fallbackUrl, button) {
    const origHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Téléchargement...';

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

    const applyRouteUrl = "{{ Route::has('products.apply-4k-image') ? route('products.apply-4k-image', $product) : url('/products/' . $product->id . '/apply-4k-image') }}";

    fetch(applyRouteUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            image_url: imageUrl,
            fallback_url: fallbackUrl
        })
    })
    .then(res => res.json())
    .then(data => {
        button.disabled = false;
        button.innerHTML = origHtml;

        if (data.success) {
            const container = document.getElementById('imagePreviewContainer');
            if (container) {
                // Remove existing primary indicators
                container.querySelectorAll('.primary-badge-indicator').forEach(b => b.remove());

                let currentBox = document.getElementById('box-img-current');
                if (!currentBox) {
                    currentBox = document.createElement('div');
                    currentBox.className = 'image-upload-box';
                    currentBox.id = 'box-img-current';
                    container.insertBefore(currentBox, container.firstElementChild);
                }
                
                currentBox.innerHTML = `
                    <img src="${data.image_url}?v=${new Date().getTime()}" alt="Image produit">
                    <span class="primary-badge-indicator" style="background: #10b981;">PRINCIPALE</span>
                `;
            }

            close4kImageModal();

            Swal.fire({
                title: 'Photo Appliquée !',
                text: `Résolution : ${data.width}×${data.height} pixels (${data.quality || 'HD'})`,
                icon: 'success',
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            Swal.fire('Erreur', data.message || 'Impossible d\'appliquer l\'image', 'error');
        }
    })
    .catch(err => {
        button.disabled = false;
        button.innerHTML = origHtml;
        Swal.fire('Erreur', err.message, 'error');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updatePricingAndProfit();

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
            });
        }

        updateSelectedChips();
    })();
});

/* ==========================================================================
   PRODUCT VARIATIONS MANAGEMENT
   ========================================================================== */
let variantIndex = 0;

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}

function toggleVariationsSection(enable) {
    const container = document.getElementById('variationsContainer');
    const notice = document.getElementById('stockVariantNotice');
    const mainStock = document.getElementById('stock');
    if (!container) return;
    container.style.display = enable ? 'block' : 'none';
    if (notice) notice.style.display = enable ? 'inline-block' : 'none';
    if (mainStock) {
        if (enable) {
            mainStock.setAttribute('readonly', 'readonly');
            mainStock.classList.add('bg-light');
        } else {
            mainStock.removeAttribute('readonly');
            mainStock.classList.remove('bg-light');
        }
    }
    if (enable) {
        const cardsContainer = document.getElementById('variantsCardsContainer');
        if (cardsContainer && cardsContainer.children.length === 0) {
            addVariantRow();
        }
    }
    recalculateVariantSummary();
}

function updateVariantTitlePreview(input, cardId) {
    const card = document.getElementById(cardId);
    if (!card) return;
    const preview = card.querySelector('.variant-title-preview');
    if (preview) {
        preview.textContent = input.value ? `— ${input.value}` : '';
    }
}

function addVariantRow(data = {}) {
    const container = document.getElementById('variantsCardsContainer');
    if (!container) return;

    const rowId = 'variant_card_' + variantIndex;
    const defaultPrice = (data.price !== undefined && data.price !== '') ? data.price : (document.getElementById('price') ? document.getElementById('price').value : '');
    const defaultCost = (data.cost_price !== undefined && data.cost_price !== '') ? data.cost_price : (document.getElementById('cost_price') ? document.getElementById('cost_price').value : '');
    const defaultStock = (data.stock !== undefined && data.stock !== '') ? data.stock : '10';
    const defaultMinStock = (data.min_stock !== undefined && data.min_stock !== '') ? data.min_stock : '5';
    const defaultName = data.name || '';
    const defaultSku = data.sku || '';
    const variantDbId = data.id || '';

    const card = document.createElement('div');
    card.id = rowId;
    card.className = 'variant-card shadow-2xs';
    card.innerHTML = `
        <div class="variant-card-header">
            <div class="d-flex align-items-center gap-2">
                <span class="variant-card-badge">
                    <i class="fas fa-layer-group me-1"></i> Variante <span class="variant-num-label">#${container.children.length + 1}</span>
                </span>
                <span class="variant-title-preview fw-bold text-dark small">${escapeHtml(defaultName ? '— ' + defaultName : '')}</span>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5" onclick="removeVariantRow('${rowId}')" title="Supprimer cette variante">
                <i class="fas fa-trash-alt"></i>
                <span class="d-none d-sm-inline" style="font-size: 0.76rem;">Supprimer</span>
            </button>
        </div>

        ${variantDbId ? `<input type="hidden" name="variants[${variantIndex}][id]" value="${variantDbId}">` : ''}

        <!-- Ligne 1 : Nom, SKU et Prix de vente -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-5">
                <label class="form-label">
                    <span>Nom de la déclinaison <span class="text-danger">*</span></span>
                    <span class="text-muted fw-normal" style="font-size: 0.72rem;">(ex: 512GB, 256GB, Noir...)</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                    <input type="text" 
                           name="variants[${variantIndex}][name]" 
                           class="form-control fw-bold text-dark variant-name-input" 
                           placeholder="ex: 512GB" 
                           value="${escapeHtml(defaultName)}" 
                           required 
                           oninput="updateVariantTitlePreview(this, '${rowId}'); recalculateVariantSummary()">
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label">
                    <span>SKU spécifique</span>
                    <span class="text-muted fw-normal" style="font-size: 0.72rem;">(optionnel)</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                    <input type="text" 
                           name="variants[${variantIndex}][sku]" 
                           class="form-control font-monospace text-uppercase" 
                           placeholder="ex: SDCARD-512" 
                           value="${escapeHtml(defaultSku)}">
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">
                    <span>Prix de vente TTC <span class="text-danger">*</span></span>
                </label>
                <div class="input-group">
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="variants[${variantIndex}][price]" 
                           class="form-control fw-bold text-primary variant-price-input" 
                           placeholder="0.00" 
                           value="${defaultPrice}" 
                           required 
                           oninput="recalculateVariantSummary()">
                    <span class="input-group-text fw-bold text-dark">DH</span>
                </div>
            </div>
        </div>

        <!-- Ligne 2 : Prix d'achat, Stock disponible et Seuil d'alerte -->
        <div class="row g-3">
            <div class="col-12 col-sm-4 col-md-4">
                <label class="form-label">
                    <span>Prix de revient / d'achat</span>
                    <span class="text-muted fw-normal" style="font-size: 0.72rem;">(HT)</span>
                </label>
                <div class="input-group">
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="variants[${variantIndex}][cost_price]" 
                           class="form-control" 
                           placeholder="0.00" 
                           value="${defaultCost}">
                    <span class="input-group-text">DH</span>
                </div>
            </div>

            <div class="col-12 col-sm-4 col-md-4">
                <label class="form-label">
                    <span>Stock disponible <span class="text-danger">*</span></span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-success-subtle text-success border-success-subtle"><i class="fas fa-boxes"></i></span>
                    <input type="number" 
                           min="0" 
                           name="variants[${variantIndex}][stock]" 
                           class="form-control fw-bold variant-stock-input" 
                           placeholder="0" 
                           value="${defaultStock}" 
                           required 
                           oninput="recalculateVariantSummary()">
                </div>
            </div>

            <div class="col-12 col-sm-4 col-md-4">
                <label class="form-label">
                    <span>Seuil d'alerte stock</span>
                    <span class="text-muted fw-normal" style="font-size: 0.72rem;">(min)</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-warning-subtle text-warning border-warning-subtle"><i class="fas fa-bell"></i></span>
                    <input type="number" 
                           min="0" 
                           name="variants[${variantIndex}][min_stock]" 
                           class="form-control" 
                           placeholder="5" 
                           value="${defaultMinStock}">
                </div>
            </div>
        </div>
    `;

    container.appendChild(card);
    variantIndex++;
    recalculateVariantSummary();
}

function removeVariantRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.remove();
        recalculateVariantSummary();
    }
}

function recalculateVariantSummary() {
    const toggle = document.getElementById('hasVariantsToggle');
    if (!toggle || !toggle.checked) return;

    const container = document.getElementById('variantsCardsContainer');
    if (!container) return;

    const cards = container.querySelectorAll('.variant-card');
    let totalStock = 0;
    cards.forEach((card, idx) => {
        const numLabel = card.querySelector('.variant-num-label');
        if (numLabel) {
            numLabel.textContent = '#' + (idx + 1);
        }
        const stockInput = card.querySelector('.variant-stock-input');
        if (stockInput) {
            totalStock += parseInt(stockInput.value, 10) || 0;
        }
    });

    const countEl = document.getElementById('variantCount');
    const totalStockEl = document.getElementById('variantTotalStock');
    if (countEl) countEl.textContent = cards.length;
    if (totalStockEl) totalStockEl.textContent = totalStock;

    const mainStockInput = document.getElementById('stock');
    if (mainStockInput) {
        mainStockInput.value = totalStock;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const serverVariants = {!! json_encode(old('variants', $product->variants ? $product->variants->toArray() : [])) !!};
    if (Array.isArray(serverVariants) && serverVariants.length > 0) {
        serverVariants.forEach(v => addVariantRow(v));
        const toggle = document.getElementById('hasVariantsToggle');
        if (toggle) {
            toggle.checked = true;
            toggleVariationsSection(true);
        }
    }
});
</script>
@endpush
