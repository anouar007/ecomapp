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
                            Prix réel et effectif payé par le client (TVA comprise).
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

                    <!-- Catégorie -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold text-dark">
                            Catégorie
                        </label>
                        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- Sans catégorie parente --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->breadcrumb }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
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
    const name = document.getElementById('name').value;
    const categoryId = document.getElementById('category_id').value;
    const categorySelect = document.getElementById('category_id');
    let categoryPrefix = 'PROD';
    
    if (categoryId && categorySelect.selectedIndex > 0) {
        const categoryName = categorySelect.options[categorySelect.selectedIndex].text;
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
    skuInput.value = sku;
    skuInput.style.backgroundColor = '#ecfdf5';
    setTimeout(() => {
        skuInput.style.backgroundColor = '';
    }, 600);
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
    
    document.getElementById('category_id').addEventListener('change', function() {
        if (this.value) {
            generateSKU();
        }
    });
    
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
});
</script>
@endpush
