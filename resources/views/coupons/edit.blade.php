@extends('layouts.app')

@section('title', 'Modifier le Code Promo - ' . $coupon->code)

@push('styles')
<style>
/* Custom styling for searchable picker lists */
.picker-search-box {
    position: relative;
}
.picker-search-box .picker-clear-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 0.85rem;
    padding: 0;
    display: none;
}
.picker-search-box .picker-clear-btn:hover {
    color: #0f172a;
}
.picker-checklist-container {
    max-height: 290px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #ffffff;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
}
.picker-checklist-container::-webkit-scrollbar {
    width: 6px;
}
.picker-checklist-container::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 6px;
}
.picker-item-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background 0.15s ease;
    user-select: none;
}
.picker-item-row:last-child {
    border-bottom: none;
}
.picker-item-row:hover {
    background: #f8fafc;
}
.picker-item-row.is-checked {
    background: #f0fdf4;
}
.picker-item-thumb {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    object-fit: cover;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.picker-chips-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    max-height: 120px;
    overflow-y: auto;
    padding: 8px 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 10px;
}
.picker-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    padding: 3px 10px 3px 10px;
    font-size: 0.78rem;
    font-weight: 500;
    color: #1e293b;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.picker-chip-remove {
    background: none;
    border: none;
    color: #94a3b8;
    padding: 0;
    cursor: pointer;
    font-size: 0.85rem;
    line-height: 1;
}
.picker-chip-remove:hover {
    color: #ef4444;
}

/* Live Ticket Voucher Preview */
.voucher-ticket-preview {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    color: #ffffff;
    padding: 24px;
    position: relative;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
}
.voucher-ticket-preview::before,
.voucher-ticket-preview::after {
    content: "";
    position: absolute;
    width: 22px;
    height: 22px;
    background: #f8fafc;
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
}
.voucher-ticket-preview::before { left: -11px; }
.voucher-ticket-preview::after { right: -11px; }
.voucher-code-display {
    font-family: var(--font-mono, monospace);
    letter-spacing: 2px;
    font-size: 1.5rem;
    font-weight: 800;
    color: #ffffff;
    background: rgba(220, 38, 38, 0.25);
    border: 1px dashed rgba(220, 38, 38, 0.6);
    padding: 8px 14px;
    border-radius: 8px;
    display: inline-block;
}
</style>
@endpush

@section('content')
<!-- En-tête de page -->
<div class="brand-header">
    <div class="d-flex align-items-center gap-2.5">
        <div class="brand-header-icon flex-shrink-0">
            <i class="fas fa-edit"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="brand-title">{{ __('Modifier le Code Promo') }} : <span class="font-monospace text-primary">{{ $coupon->code }}</span></h1>
            </div>
            <p class="brand-subtitle">{{ __('Ajustez les conditions, restrictions de prix ou catégories et le programme d\'ambassadeur affilié') }}</p>
        </div>
    </div>
    <div class="brand-header-actions">
        <a href="{{ route('coupons.show', $coupon) }}" class="btn-brand-light">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Annuler') }}
        </a>
        <a href="{{ route('coupons.show', $coupon) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">
            <i class="fas fa-eye me-1"></i> {{ __('Voir détails') }}
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
    <div class="d-flex align-items-start gap-2">
        <i class="fas fa-exclamation-triangle text-danger mt-1"></i>
        <div>
            <strong class="d-block mb-1">{{ __('Veuillez corriger les erreurs ci-dessous :') }}</strong>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<form action="{{ route('coupons.update', $coupon) }}" method="POST" id="couponForm">
    @csrf
    @method('PUT')
    
    <div class="row g-4">
        <!-- Main Column (Left) -->
        <div class="col-12 col-lg-8">
            <!-- 1. Informations Générales -->
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">
                        <i class="fas fa-info-circle text-primary me-2"></i> {{ __('Informations Générales') }}
                    </h5>
                </div>
                <div class="brand-card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Code Promo') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="couponCodeInput" class="form-control font-monospace text-uppercase fw-bold" value="{{ old('code', $coupon->code) }}" required autocomplete="off">
                            <div class="form-text small">{{ __('Code utilisé par les clients au panier / paiement.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Nom / Titre du code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="couponNameInput" class="form-control" value="{{ old('name', $coupon->name) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $coupon->description) }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Type de Réduction') }} <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" id="couponType" required>
                                <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>{{ __('Pourcentage (%)') }}</option>
                                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>{{ __('Montant Fixe (DH)') }}</option>
                                <option value="free_shipping" {{ old('type', $coupon->type) == 'free_shipping' ? 'selected' : '' }}>{{ __('Livraison Gratuite') }}</option>
                                <option value="buy_x_get_y" {{ old('type', $coupon->type) == 'buy_x_get_y' ? 'selected' : '' }}>{{ __('Achetez X, Obtenez Y offert') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="valueField">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Valeur de la réduction') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="value" id="couponValueInput" class="form-control" value="{{ old('value', $coupon->value) }}">
                                <span class="input-group-text fw-bold" id="valueSuffix">%</span>
                            </div>
                            <div class="form-text small" id="valueHint">{{ __('Pourcentage déduit sur les articles éligibles.') }}</div>
                        </div>
                    </div>

                    <!-- Buy X Get Y Section -->
                    <div class="row g-3 mt-2 p-3 bg-light rounded-3" id="buyXGetYFields" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">{{ __('Quantité à acheter (X)') }} <span class="text-danger">*</span></label>
                            <input type="number" name="buy_quantity" class="form-control" value="{{ old('buy_quantity', $coupon->buy_quantity ?? 2) }}" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">{{ __('Quantité offerte (Y)') }} <span class="text-danger">*</span></label>
                            <input type="number" name="get_quantity" class="form-control" value="{{ old('get_quantity', $coupon->get_quantity ?? 1) }}" min="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. CHAMP D'APPLICATION AVEC RECHERCHE PRODUITS (NOM/SKU) ET CATÉGORIES -->
            <div class="brand-card mb-4">
                <div class="brand-card-header d-flex justify-content-between align-items-center">
                    <h5 class="brand-card-title mb-0">
                        <i class="fas fa-boxes-stacked text-primary me-2"></i> {{ __('Champ d\'Application des Produits & Catégories') }}
                    </h5>
                    <span class="badge bg-light text-muted border small">{{ __('Cible d\'éligibilité') }}</span>
                </div>
                <div class="brand-card-body p-4">
                    @php
                        $applicableTo = old('applicable_to', $coupon->applicable_to ?? 'all');
                        $applicableIds = array_map('intval', (array) old('applicable_ids', $coupon->applicable_ids ?? []));
                    @endphp

                    <!-- Options Radio -->
                    <div class="d-flex flex-wrap gap-4 mb-3 p-3 bg-light rounded-3 border">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="applicable_to" id="applyAll" value="all" {{ $applicableTo == 'all' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="applyAll">
                                <i class="fas fa-globe text-primary me-1"></i> {{ __('Tous les produits') }}
                            </label>
                            <div class="text-muted small ps-4">{{ __('S\'applique à l\'ensemble du catalogue de la boutique.') }}</div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="applicable_to" id="applyProducts" value="specific_products" {{ $applicableTo == 'specific_products' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="applyProducts">
                                <i class="fas fa-box-open text-primary me-1"></i> {{ __('Produits Spécifiques') }}
                            </label>
                            <div class="text-muted small ps-4">{{ __('Sélection ciblée par nom de produit ou code SKU.') }}</div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="applicable_to" id="applyCategories" value="specific_categories" {{ $applicableTo == 'specific_categories' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="applyCategories">
                                <i class="fas fa-folder-tree text-primary me-1"></i> {{ __('Catégories Spécifiques') }}
                            </label>
                            <div class="text-muted small ps-4">{{ __('S\'applique aux produits des catégories sélectionnées.') }}</div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- SÉLECTEUR AVANCÉ DE PRODUITS (RECHERCHE NOM/SKU) -->
                    <!-- ============================================== -->
                    <div id="productPickerSection" style="display: {{ $applicableTo == 'specific_products' ? 'block' : 'none' }};" class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold small text-uppercase text-muted mb-0">
                                {{ __('Rechercher & Sélectionner les Produits') }}
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary" id="selectedProductsCount">0 sélectionné(s)</span>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none small d-none" id="btnDeselectAllProducts">
                                    {{ __('Tout désélectionner') }}
                                </button>
                            </div>
                        </div>

                        <!-- Chips / Badges des Produits Sélectionnés -->
                        <div id="selectedProductsChips" class="picker-chips-wrapper d-none"></div>

                        <!-- Barre de Recherche Produit (Nom ou SKU) -->
                        <div class="picker-search-box mb-2">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="productSearchInput" class="form-control border-start-0 ps-1" placeholder="{{ __('Tapez un nom de produit ou code SKU (ex: Sony, A7, FX3, 24-70, SKU-001)...') }}" autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnProductSelectAllVisible" title="{{ __('Sélectionner les résultats visibles') }}">
                                    <i class="fas fa-check-double me-1"></i> {{ __('Cocher visibles') }}
                                </button>
                            </div>
                            <button type="button" class="picker-clear-btn" id="btnProductSearchClear">&times;</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                            <span class="text-muted small" id="productSearchResultsCount">{{ count($products) }} {{ __('produits disponibles') }}</span>
                            <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>{{ __('Cliquez sur une ligne pour cocher/décocher') }}</span>
                        </div>

                        <!-- Liste à Cocher Produits (Scrollable) -->
                        <div class="picker-checklist-container" id="productListContainer">
                            @foreach($products as $prod)
                                @php
                                    $isChecked = ($applicableTo == 'specific_products') && in_array($prod->id, $applicableIds);
                                    $prodImg = $prod->thumbnail ?: asset('images/camera/cat_cameras.jpg');
                                    $prodSku = $prod->sku ?: 'SANS-SKU';
                                @endphp
                                <div class="picker-item-row product-item-row {{ $isChecked ? 'is-checked' : '' }}" 
                                     data-id="{{ $prod->id }}"
                                     data-name="{{ strtolower($prod->name) }}"
                                     data-sku="{{ strtolower($prodSku) }}"
                                     data-price="{{ $prod->price }}">
                                    <input type="checkbox" name="applicable_ids[]" value="{{ $prod->id }}" class="form-check-input product-checkbox" {{ $isChecked ? 'checked' : '' }} {{ $applicableTo != 'specific_products' ? 'disabled' : '' }}>
                                    <img src="{{ $prodImg }}" alt="{{ $prod->name }}" class="picker-item-thumb" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-dark text-truncate product-name-label" style="font-size: 0.88rem;">{{ $prod->name }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-0.5">
                                            <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.68rem;">SKU: {{ $prodSku }}</span>
                                            <span class="fw-bold text-dark small">{{ currency($prod->price) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div id="noProductsFound" class="p-4 text-center text-muted d-none">
                                <i class="fas fa-search-minus fa-2x mb-2 text-muted opacity-50"></i>
                                <div>{{ __('Aucun produit ne correspond à votre recherche.') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- SÉLECTEUR AVANCÉ DE CATÉGORIES (AVEC RECHERCHE) -->
                    <!-- ============================================== -->
                    <div id="categoryPickerSection" style="display: {{ $applicableTo == 'specific_categories' ? 'block' : 'none' }};" class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold small text-uppercase text-muted mb-0">
                                {{ __('Rechercher & Sélectionner les Catégories') }}
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary" id="selectedCategoriesCount">0 sélectionnée(s)</span>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none small d-none" id="btnDeselectAllCategories">
                                    {{ __('Tout désélectionner') }}
                                </button>
                            </div>
                        </div>

                        <!-- Chips / Badges des Catégories Sélectionnées -->
                        <div id="selectedCategoriesChips" class="picker-chips-wrapper d-none"></div>

                        <!-- Barre de Recherche Catégorie -->
                        <div class="picker-search-box mb-2">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="categorySearchInput" class="form-control border-start-0 ps-1" placeholder="{{ __('Rechercher une catégorie (ex: Boîtiers, Optiques, Éclairage, Trépieds)...') }}" autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCategorySelectAllVisible" title="{{ __('Sélectionner les visibles') }}">
                                    <i class="fas fa-check-double me-1"></i> {{ __('Cocher visibles') }}
                                </button>
                            </div>
                            <button type="button" class="picker-clear-btn" id="btnCategorySearchClear">&times;</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                            <span class="text-muted small" id="categorySearchResultsCount">{{ count($categories) }} {{ __('catégories disponibles') }}</span>
                            <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>{{ __('Cliquez sur une ligne pour cocher/décocher') }}</span>
                        </div>

                        <!-- Liste à Cocher Catégories (Scrollable) -->
                        <div class="picker-checklist-container" id="categoryListContainer">
                            @foreach($categories as $cat)
                                @php
                                    $isCatChecked = ($applicableTo == 'specific_categories') && in_array($cat->id, $applicableIds);
                                @endphp
                                <div class="picker-item-row category-item-row {{ $isCatChecked ? 'is-checked' : '' }}" 
                                     data-id="{{ $cat->id }}"
                                     data-name="{{ strtolower($cat->name) }}">
                                    <input type="checkbox" name="applicable_ids[]" value="{{ $cat->id }}" class="form-check-input category-checkbox" {{ $isCatChecked ? 'checked' : '' }} {{ $applicableTo != 'specific_categories' ? 'disabled' : '' }}>
                                    <div class="rounded-3 bg-light border p-2 d-flex align-items-center justify-content-center text-primary" style="width: 38px; height: 38px;">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-dark text-truncate category-name-label" style="font-size: 0.88rem;">{{ $cat->name }}</div>
                                        <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                            <i class="fas fa-box text-muted me-1"></i> {{ $cat->products_count ?? 0 }} {{ __('produits rattachés') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div id="noCategoriesFound" class="p-4 text-center text-muted d-none">
                                <i class="fas fa-search-minus fa-2x mb-2 text-muted opacity-50"></i>
                                <div>{{ __('Aucune catégorie trouvée.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Conditions & Plages de Prix -->
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">
                        <i class="fas fa-sliders-h text-primary me-2"></i> {{ __('Conditions & Plage de Prix') }}
                    </h5>
                </div>
                <div class="brand-card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                <i class="fas fa-coins text-warning me-1"></i> {{ __('Montant Minimum du Panier') }}
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="min_order_amount" id="couponMinOrderInput" class="form-control" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" placeholder="Ex: 500">
                                <span class="input-group-text">DH</span>
                            </div>
                            <div class="form-text small">{{ __('Total minimum de commande requis pour valider ce code.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Plafond Max de Réduction') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" placeholder="{{ __('Illimité') }}">
                                <span class="input-group-text">DH</span>
                            </div>
                            <div class="form-text small">{{ __('Montant maximum de réduction accordé (plafond).') }}</div>
                        </div>
                    </div>

                    <!-- Plage de prix produit -->
                    <div class="p-3 bg-light rounded-4 border">
                        <h6 class="fw-bold mb-2 text-dark">
                            <i class="fas fa-tags text-primary me-1"></i> {{ __('Restriction par Plage de Prix des Produits') }}
                        </h6>
                        <p class="text-muted small mb-3">{{ __('Le code ne s\'appliquera qu\'aux articles dont le prix unitaire est dans cet intervalle.') }}</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('Prix Produit Minimum') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="min_product_price" class="form-control" value="{{ old('min_product_price', $coupon->min_product_price) }}" placeholder="Ex: 1000">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('Prix Produit Maximum') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="max_product_price" class="form-control" value="{{ old('max_product_price', $coupon->max_product_price) }}" placeholder="Ex: 10000">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Système Affiliation & Gain de Points -->
            <div class="brand-card mb-4 border-start border-4 border-success">
                <div class="brand-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="brand-card-title text-dark">
                            <i class="fas fa-share-alt text-success me-2"></i> {{ __('Affiliation & Récompense en Points') }}
                        </h5>
                        <p class="text-muted small mb-0">{{ __('Attribuez ce code à une personne spécifique pour qu\'elle gagne des points à chaque achat client.') }}</p>
                    </div>
                    <div class="form-check form-switch fs-5 mb-0">
                        <input class="form-check-input" type="checkbox" id="isAffiliateSwitch" name="is_affiliate" value="1" {{ old('is_affiliate', $coupon->is_affiliate) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="brand-card-body p-4" id="affiliateSettingsSection" style="display: {{ old('is_affiliate', $coupon->is_affiliate) ? 'block' : 'none' }};">
                    <div class="alert alert-success bg-success-subtle border-0 rounded-3 mb-3">
                        <i class="fas fa-gift text-success me-2"></i>
                        <span>{{ __('Total cumulé de points déjà accordés par ce code :') }} <strong>{{ number_format($coupon->total_affiliate_points_awarded) }} pts</strong></span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                {{ __('Bénéficiaire (Compte Utilisateur)') }}
                            </label>
                            <select name="affiliate_user_id" class="form-select">
                                <option value="">-- {{ __('Choisir un utilisateur') }} --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('affiliate_user_id', $coupon->affiliate_user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">{{ __('Compte utilisateur qui recevra les points dans son espace My-Account.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                {{ __('OU Fiche Client CRM') }}
                            </label>
                            <select name="affiliate_customer_id" class="form-select">
                                <option value="">-- {{ __('Choisir un client CRM') }} --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ old('affiliate_customer_id', $coupon->affiliate_customer_id) == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }} ({{ $cust->email ?? $cust->phone }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">{{ __('Créditera également les points de fidélité du client.') }}</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Calcul des Points Gagnés') }}</label>
                            <select name="affiliate_points_type" class="form-select" id="affiliatePointsType">
                                <option value="fixed" {{ old('affiliate_points_type', $coupon->affiliate_points_type) == 'fixed' ? 'selected' : '' }}>{{ __('Points Fixes par commande') }}</option>
                                <option value="percentage" {{ old('affiliate_points_type', $coupon->affiliate_points_type) == 'percentage' ? 'selected' : '' }}>{{ __('% du montant de la commande en points') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Montant / Taux de Points') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="affiliate_points_value" class="form-control" value="{{ old('affiliate_points_value', $coupon->affiliate_points_value) }}">
                                <span class="input-group-text fw-bold" id="affiliatePointsSuffix">{{ $coupon->affiliate_points_type === 'percentage' ? '%' : 'Pts' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Période & Limites d'Utilisation -->
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">
                        <i class="far fa-calendar-alt text-primary me-2"></i> {{ __('Validité & Limites d\'Utilisation') }}
                    </h5>
                </div>
                <div class="brand-card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Date de Début') }}</label>
                            <input type="datetime-local" name="valid_from" class="form-control" value="{{ old('valid_from', $coupon->valid_from ? $coupon->valid_from->format('Y-m-d\TH:i') : '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Date d\'Expiration') }}</label>
                            <input type="datetime-local" name="valid_to" class="form-control" value="{{ old('valid_to', $coupon->valid_to ? $coupon->valid_to->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Limite Globale d\'Utilisations') }}</label>
                            <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1" placeholder="{{ __('Illimité') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Limite par Client') }}</label>
                            <input type="number" name="per_customer_limit" class="form-control" value="{{ old('per_customer_limit', $coupon->per_customer_limit) }}" min="1">
                        </div>
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="firstOrderOnly" name="first_order_only" value="1" {{ old('first_order_only', $coupon->first_order_only) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="firstOrderOnly">
                            {{ __('Réservé uniquement à la première commande du client') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="col-12 col-lg-4">
            <!-- Statut & Publication -->
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">{{ __('Statut & Mise à jour') }}</h5>
                </div>
                <div class="brand-card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">{{ __('État du code promo') }}</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $coupon->status) == 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                            <option value="inactive" {{ old('status', $coupon->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                            <option value="expired" {{ old('status', $coupon->status) == 'expired' ? 'selected' : '' }}>{{ __('Expiré') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-brand-primary w-100 justify-content-center py-2.5 text-center shadow-sm">
                        <i class="fas fa-save me-1"></i> {{ __('Mettre à jour le Code Promo') }}
                    </button>
                </div>
            </div>

            <!-- APERÇU EN DIRECT DU BON DE RÉDUCTION (LIVE VOUCHER TICKET PREVIEW) -->
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">
                        <i class="fas fa-eye text-primary me-1.5"></i> {{ __('Aperçu en Direct') }}
                    </h5>
                </div>
                <div class="brand-card-body p-3">
                    <div class="voucher-ticket-preview text-center">
                        <div class="text-uppercase small text-muted mb-2 fw-bold" style="letter-spacing: 1px; font-size: 0.72rem;">
                            {{ setting('app_name', 'WINA SHOP') }} — {{ __('BON DE RÉDUCTION') }}
                        </div>
                        <div class="voucher-code-display mb-3" id="previewCodeDisplay">
                            {{ old('code', $coupon->code) }}
                        </div>
                        <div class="fs-4 fw-bold text-white mb-1" id="previewValueDisplay">
                            {{ $coupon->formatted_value }}
                        </div>
                        <div class="small text-white-50 mb-2" id="previewScopeDisplay">
                            {{ $coupon->applicable_to === 'all' ? __('Valable sur tous les produits') : ($coupon->applicable_to === 'specific_products' ? __('Sur les produits sélectionnés') : __('Sur les catégories sélectionnées')) }}
                        </div>
                        <div class="small text-warning" id="previewMinOrderDisplay">
                            {{ $coupon->min_order_amount > 0 ? __('Dès ') . currency($coupon->min_order_amount) . __(' d\'achat') : __('Sans minimum de commande') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('couponType');
    const valueField = document.getElementById('valueField');
    const valueSuffix = document.getElementById('valueSuffix');
    const valueHint = document.getElementById('valueHint');
    const valueInput = document.getElementById('couponValueInput');
    const codeInput = document.getElementById('couponCodeInput');
    const minOrderInput = document.getElementById('couponMinOrderInput');
    const buyXGetYFields = document.getElementById('buyXGetYFields');

    const previewCodeDisplay = document.getElementById('previewCodeDisplay');
    const previewValueDisplay = document.getElementById('previewValueDisplay');
    const previewMinOrderDisplay = document.getElementById('previewMinOrderDisplay');
    const previewScopeDisplay = document.getElementById('previewScopeDisplay');

    function updatePreview() {
        const code = (codeInput.value || '').trim().toUpperCase();
        previewCodeDisplay.textContent = code || 'CODE-PROMO';

        const type = typeSelect.value;
        const val = valueInput.value || 0;
        if (type === 'percentage') {
            previewValueDisplay.textContent = val + '% {{ __("DE RÉDUCTION") }}';
        } else if (type === 'fixed') {
            previewValueDisplay.textContent = val + ' DH {{ __("DE REMISE") }}';
        } else if (type === 'free_shipping') {
            previewValueDisplay.textContent = '{{ __("LIVRAISON GRATUITE") }}';
        } else {
            previewValueDisplay.textContent = '{{ __("OFFRE GROUPÉE") }}';
        }

        const minOrder = parseFloat(minOrderInput.value) || 0;
        if (minOrder > 0) {
            previewMinOrderDisplay.textContent = '{{ __("Dès") }} ' + minOrder + ' DH {{ __("d\'achat") }}';
        } else {
            previewMinOrderDisplay.textContent = '{{ __("Sans minimum de commande") }}';
        }
    }

    function updateType() {
        const type = typeSelect.value;
        if (type === 'free_shipping') {
            valueField.style.display = 'none';
            buyXGetYFields.style.display = 'none';
        } else if (type === 'buy_x_get_y') {
            valueField.style.display = 'none';
            buyXGetYFields.style.display = 'flex';
        } else {
            valueField.style.display = 'block';
            buyXGetYFields.style.display = 'none';
            if (type === 'percentage') {
                valueSuffix.textContent = '%';
                valueHint.textContent = '{{ __("Pourcentage déduit sur les articles éligibles.") }}';
            } else {
                valueSuffix.textContent = 'DH';
                valueHint.textContent = '{{ __("Montant en DH déduit sur les articles éligibles.") }}';
            }
        }
        updatePreview();
    }

    typeSelect.addEventListener('change', updateType);
    if (valueInput) valueInput.addEventListener('input', updatePreview);
    if (codeInput) codeInput.addEventListener('input', updatePreview);
    if (minOrderInput) minOrderInput.addEventListener('input', updatePreview);
    updateType();

    const applyAll = document.getElementById('applyAll');
    const applyProducts = document.getElementById('applyProducts');
    const applyCategories = document.getElementById('applyCategories');
    const productPickerSection = document.getElementById('productPickerSection');
    const categoryPickerSection = document.getElementById('categoryPickerSection');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');

    function updateApplicability() {
        if (applyProducts.checked) {
            productPickerSection.style.display = 'block';
            categoryPickerSection.style.display = 'none';
            productCheckboxes.forEach(cb => cb.disabled = false);
            categoryCheckboxes.forEach(cb => cb.disabled = true);
            previewScopeDisplay.textContent = '{{ __("Sur les produits sélectionnés") }}';
        } else if (applyCategories.checked) {
            productPickerSection.style.display = 'none';
            categoryPickerSection.style.display = 'block';
            productCheckboxes.forEach(cb => cb.disabled = true);
            categoryCheckboxes.forEach(cb => cb.disabled = false);
            previewScopeDisplay.textContent = '{{ __("Sur les catégories sélectionnées") }}';
        } else {
            productPickerSection.style.display = 'none';
            categoryPickerSection.style.display = 'none';
            productCheckboxes.forEach(cb => cb.disabled = true);
            categoryCheckboxes.forEach(cb => cb.disabled = true);
            previewScopeDisplay.textContent = '{{ __("Valable sur tous les produits") }}';
        }
    }

    [applyAll, applyProducts, applyCategories].forEach(el => el.addEventListener('change', updateApplicability));

    // RECHERCHE PRODUITS (NOM/SKU) & CHIPS
    const productSearchInput = document.getElementById('productSearchInput');
    const productClearBtn = document.getElementById('btnProductSearchClear');
    const productRows = document.querySelectorAll('.product-item-row');
    const noProductsFound = document.getElementById('noProductsFound');
    const productResultsCount = document.getElementById('productSearchResultsCount');
    const selectedProductsCount = document.getElementById('selectedProductsCount');
    const selectedProductsChips = document.getElementById('selectedProductsChips');
    const btnDeselectAllProducts = document.getElementById('btnDeselectAllProducts');
    const btnProductSelectAllVisible = document.getElementById('btnProductSelectAllVisible');

    function refreshSelectedProductsUI() {
        let checkedCount = 0;
        selectedProductsChips.innerHTML = '';

        productRows.forEach(row => {
            const cb = row.querySelector('.product-checkbox');
            if (cb && cb.checked) {
                checkedCount++;
                const name = row.querySelector('.product-name-label').textContent.trim();
                const sku = row.getAttribute('data-sku') || '';
                const id = row.getAttribute('data-id');

                const chip = document.createElement('span');
                chip.className = 'picker-chip';
                chip.innerHTML = `<span>${escapeHtml(name)} <small class="text-muted">(${escapeHtml(sku)})</small></span>
                                  <button type="button" class="picker-chip-remove" data-id="${id}">&times;</button>`;
                selectedProductsChips.appendChild(chip);
            }
        });

        selectedProductsCount.textContent = `${checkedCount} {{ __("sélectionné(s)") }}`;
        if (checkedCount > 0) {
            selectedProductsChips.classList.remove('d-none');
            btnDeselectAllProducts.classList.remove('d-none');
        } else {
            selectedProductsChips.classList.add('d-none');
            btnDeselectAllProducts.classList.add('d-none');
        }
    }

    productRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName.toLowerCase() === 'input') return;
            const cb = this.querySelector('.product-checkbox');
            if (cb) {
                cb.checked = !cb.checked;
                this.classList.toggle('is-checked', cb.checked);
                refreshSelectedProductsUI();
            }
        });

        const cb = row.querySelector('.product-checkbox');
        if (cb) {
            cb.addEventListener('change', function() {
                row.classList.toggle('is-checked', this.checked);
                refreshSelectedProductsUI();
            });
        }
    });

    selectedProductsChips.addEventListener('click', function(e) {
        if (e.target.classList.contains('picker-chip-remove')) {
            const id = e.target.getAttribute('data-id');
            const targetRow = document.querySelector(`.product-item-row[data-id="${id}"]`);
            if (targetRow) {
                const cb = targetRow.querySelector('.product-checkbox');
                if (cb) {
                    cb.checked = false;
                    targetRow.classList.remove('is-checked');
                    refreshSelectedProductsUI();
                }
            }
        }
    });

    if (btnDeselectAllProducts) {
        btnDeselectAllProducts.addEventListener('click', function() {
            productRows.forEach(row => {
                const cb = row.querySelector('.product-checkbox');
                if (cb) cb.checked = false;
                row.classList.remove('is-checked');
            });
            refreshSelectedProductsUI();
        });
    }

    function filterProducts() {
        const query = (productSearchInput.value || '').trim().toLowerCase();
        let visibleCount = 0;

        if (productClearBtn) {
            productClearBtn.style.display = query ? 'block' : 'none';
        }

        productRows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const sku = row.getAttribute('data-sku') || '';
            const isMatch = name.includes(query) || sku.includes(query);

            if (isMatch) {
                row.style.display = 'flex';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noProductsFound) {
            noProductsFound.classList.toggle('d-none', visibleCount > 0);
        }
        if (productResultsCount) {
            productResultsCount.textContent = `${visibleCount} {{ __("produits trouvés") }}`;
        }
    }

    if (productSearchInput) {
        productSearchInput.addEventListener('input', filterProducts);
    }
    if (productClearBtn) {
        productClearBtn.addEventListener('click', function() {
            productSearchInput.value = '';
            filterProducts();
            productSearchInput.focus();
        });
    }

    if (btnProductSelectAllVisible) {
        btnProductSelectAllVisible.addEventListener('click', function() {
            productRows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cb = row.querySelector('.product-checkbox');
                    if (cb) cb.checked = true;
                    row.classList.add('is-checked');
                }
            });
            refreshSelectedProductsUI();
        });
    }

    refreshSelectedProductsUI();

    // RECHERCHE CATÉGORIES & CHIPS
    const categorySearchInput = document.getElementById('categorySearchInput');
    const categoryClearBtn = document.getElementById('btnCategorySearchClear');
    const categoryRows = document.querySelectorAll('.category-item-row');
    const noCategoriesFound = document.getElementById('noCategoriesFound');
    const categoryResultsCount = document.getElementById('categorySearchResultsCount');
    const selectedCategoriesCount = document.getElementById('selectedCategoriesCount');
    const selectedCategoriesChips = document.getElementById('selectedCategoriesChips');
    const btnDeselectAllCategories = document.getElementById('btnDeselectAllCategories');
    const btnCategorySelectAllVisible = document.getElementById('btnCategorySelectAllVisible');

    function refreshSelectedCategoriesUI() {
        let checkedCount = 0;
        selectedCategoriesChips.innerHTML = '';

        categoryRows.forEach(row => {
            const cb = row.querySelector('.category-checkbox');
            if (cb && cb.checked) {
                checkedCount++;
                const name = row.querySelector('.category-name-label').textContent.trim();
                const id = row.getAttribute('data-id');

                const chip = document.createElement('span');
                chip.className = 'picker-chip';
                chip.innerHTML = `<span><i class="fas fa-folder text-primary me-1"></i>${escapeHtml(name)}</span>
                                  <button type="button" class="picker-chip-remove" data-id="${id}">&times;</button>`;
                selectedCategoriesChips.appendChild(chip);
            }
        });

        selectedCategoriesCount.textContent = `${checkedCount} {{ __("sélectionnée(s)") }}`;
        if (checkedCount > 0) {
            selectedCategoriesChips.classList.remove('d-none');
            btnDeselectAllCategories.classList.remove('d-none');
        } else {
            selectedCategoriesChips.classList.add('d-none');
            btnDeselectAllCategories.classList.add('d-none');
        }
    }

    categoryRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName.toLowerCase() === 'input') return;
            const cb = this.querySelector('.category-checkbox');
            if (cb) {
                cb.checked = !cb.checked;
                this.classList.toggle('is-checked', cb.checked);
                refreshSelectedCategoriesUI();
            }
        });

        const cb = row.querySelector('.category-checkbox');
        if (cb) {
            cb.addEventListener('change', function() {
                row.classList.toggle('is-checked', this.checked);
                refreshSelectedCategoriesUI();
            });
        }
    });

    selectedCategoriesChips.addEventListener('click', function(e) {
        if (e.target.classList.contains('picker-chip-remove')) {
            const id = e.target.getAttribute('data-id');
            const targetRow = document.querySelector(`.category-item-row[data-id="${id}"]`);
            if (targetRow) {
                const cb = targetRow.querySelector('.category-checkbox');
                if (cb) {
                    cb.checked = false;
                    targetRow.classList.remove('is-checked');
                    refreshSelectedCategoriesUI();
                }
            }
        }
    });

    if (btnDeselectAllCategories) {
        btnDeselectAllCategories.addEventListener('click', function() {
            categoryRows.forEach(row => {
                const cb = row.querySelector('.category-checkbox');
                if (cb) cb.checked = false;
                row.classList.remove('is-checked');
            });
            refreshSelectedCategoriesUI();
        });
    }

    function filterCategories() {
        const query = (categorySearchInput.value || '').trim().toLowerCase();
        let visibleCount = 0;

        if (categoryClearBtn) {
            categoryClearBtn.style.display = query ? 'block' : 'none';
        }

        categoryRows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const isMatch = name.includes(query);

            if (isMatch) {
                row.style.display = 'flex';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noCategoriesFound) {
            noCategoriesFound.classList.toggle('d-none', visibleCount > 0);
        }
        if (categoryResultsCount) {
            categoryResultsCount.textContent = `${visibleCount} {{ __("catégories trouvées") }}`;
        }
    }

    if (categorySearchInput) {
        categorySearchInput.addEventListener('input', filterCategories);
    }
    if (categoryClearBtn) {
        categoryClearBtn.addEventListener('click', function() {
            categorySearchInput.value = '';
            filterCategories();
            categorySearchInput.focus();
        });
    }

    if (btnCategorySelectAllVisible) {
        btnCategorySelectAllVisible.addEventListener('click', function() {
            categoryRows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cb = row.querySelector('.category-checkbox');
                    if (cb) cb.checked = true;
                    row.classList.add('is-checked');
                }
            });
            refreshSelectedCategoriesUI();
        });
    }

    refreshSelectedCategoriesUI();

    // AFFILIATE
    const isAffiliateSwitch = document.getElementById('isAffiliateSwitch');
    const affiliateSettingsSection = document.getElementById('affiliateSettingsSection');
    const affiliatePointsType = document.getElementById('affiliatePointsType');
    const affiliatePointsSuffix = document.getElementById('affiliatePointsSuffix');

    if (isAffiliateSwitch && affiliateSettingsSection) {
        isAffiliateSwitch.addEventListener('change', function() {
            affiliateSettingsSection.style.display = this.checked ? 'block' : 'none';
        });
    }

    if (affiliatePointsType) {
        affiliatePointsType.addEventListener('change', function() {
            if (this.value === 'percentage') {
                affiliatePointsSuffix.textContent = '%';
            } else {
                affiliatePointsSuffix.textContent = 'Pts';
            }
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endpush
@endsection
