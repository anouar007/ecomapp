@extends('layouts.app')

@section('title', __('Edit Product'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
.multi-image-upload {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    margin-top: 12px;
}

.image-upload-box {
    width: 120px;
    height: 120px;
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8fafc;
    position: relative;
}

.image-upload-box:hover {
    border-color: #667eea;
    background: #f0f4ff;
}

.image-upload-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}

.image-remove-btn {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    z-index: 10;
}

.primary-badge {
    position: absolute;
    top: 4px;
    left: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 600;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

/* Variations Table Styles */
.variant-img-upload {
    width: 48px;
    height: 48px;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    transition: all 0.2s ease;
}
.variant-img-upload:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}
.variant-img-upload img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.no-img-placeholder {
    color: #94a3b8;
    font-size: 12px;
}
.form-control-color {
    padding: 0.2rem;
    height: 31px;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-edit"></i> {{ __('Edit Product') }}: {{ $product->translated_name }}</h1>
    <p class="page-subtitle">{{ __('Update product information') }}</p>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        <strong>{{ __('Oops! Something went wrong:') }}</strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt"></i> {{ __('Product Information') }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label"><i class="fas fa-images"></i> {{ __('Product Images') }}</label>
                <div class="multi-image-upload" id="imagePreviewContainer">
                    @foreach($product->images as $image)
                    <div class="image-upload-box">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image">
                        @if($image->is_primary)
                            <span class="primary-badge">{{ __('PRIMARY') }}</span>
                        @endif
                        <span class="image-remove-btn" onclick="markImageForRemoval({{ $image->id }}, this)">×</span>
                    </div>
                    @endforeach
                    <div class="image-upload-box" onclick="document.getElementById('images').click()">
                        <div>
                            <i class="fas fa-plus" style="font-size: 24px; color: #94a3b8;"></i>
                            <p style="margin-top: 6px; color: #64748b; font-size: 11px; text-align: center;">{{ __('Add more') }}</p>
                        </div>
                    </div>
                </div>
                <input type="file" 
                       id="images" 
                       name="images[]" 
                       accept="image/*" 
                       multiple
                       style="display: none;" 
                       onchange="handleNewImages(event)">
                <small class="form-help">{{ __('Upload new images or remove existing ones. First image is the primary image.') }}</small>
            </div>

            <div class="form-row d-none">
                <div class="form-group">
                    <label for="name_fr" class="form-label">
                        {{ __('Product Name (FR)') }}
                    </label>
                    <input type="text" id="name_fr" name="name_fr" class="form-control" value="{{ old('name_fr', $product->name_fr ?: $product->name) }}" placeholder="Nom du produit">
                </div>
                <div class="form-group">
                    <label for="name_en" class="form-label">{{ __('Product Name (EN)') }}</label>
                    <input type="text" id="name_en" name="name_en" class="form-control" value="{{ old('name_en', $product->name_en) }}" placeholder="Product Name">
                </div>
            </div>

            <div class="form-group">
                <label for="name_ar" class="form-label">{{ __('Product Name (AR)') }} <span class="required">*</span></label>
                <input type="text" id="name_ar" name="name_ar" class="form-control" value="{{ old('name_ar', $product->name_ar) }}" placeholder="اسم المنتج" dir="rtl" required autofocus>
            </div>

            <div class="form-row">
                <input type="hidden" name="name" id="name" value="{{ old('name', $product->name) }}">
                
                <div class="form-group">
                    <label for="sku" class="form-label">
                        {{ __('SKU') }} <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               id="sku" 
                               name="sku" 
                               class="form-control" 
                               value="{{ old('sku', $product->sku) }}" 
                               placeholder="{{ __('Unique SKU') }}" 
                               required>
                        <button type="button" 
                                onclick="generateSKU()" 
                                class="btn btn-outline-primary">
                            <i class="fas fa-magic"></i> {{ __('Generate') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-none">
                <div class="form-group mt-3">
                    <label for="description_fr" class="form-label">{{ __('Description (FR)') }}</label>
                    <textarea id="description_fr" name="description_fr" class="form-control" rows="2" placeholder="Description en français...">{{ old('description_fr', $product->description_fr ?: $product->description) }}</textarea>
                </div>
                <div class="form-group mt-3">
                    <label for="description_en" class="form-label">{{ __('Description (EN)') }}</label>
                    <textarea id="description_en" name="description_en" class="form-control" rows="2" placeholder="English description...">{{ old('description_en', $product->description_en) }}</textarea>
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="description_ar" class="form-label">{{ __('Description (AR)') }}</label>
                <textarea id="description_ar" name="description_ar" class="form-control" rows="2" placeholder="وصف عربي..." dir="rtl">{{ old('description_ar', $product->description_ar) }}</textarea>
            </div>

            <div class="form-row mt-4">
                <div class="form-group">
                    <label for="cost_price" class="form-label">
                        <i class="fas fa-dollar-sign"></i> {{ __('Cost Price ($)') }}
                    </label>
                    <input type="number" 
                           id="cost_price" 
                           name="cost_price" 
                           class="form-control" 
                           value="{{ old('cost_price', $product->cost_price) }}" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0">
                    <small class="form-help">{{ __('How much you pay for this product') }}</small>
                </div>

                <div class="form-group">
                    <label for="price" class="form-label">
                        <i class="fas fa-tag"></i> {{ __('Selling Price ($)') }} <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           class="form-control" 
                           value="{{ old('price', $product->price) }}" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0" 
                           required>
                    <small class="form-help">{{ __('Price you sell to customers') }}</small>
                </div>

                <div class="form-group">
                    <label for="sale_price" class="form-label">
                        <i class="fas fa-percentage text-danger"></i> {{ __('Discount Price ($)') }}
                    </label>
                    <input type="number" 
                           id="sale_price" 
                           name="sale_price" 
                           class="form-control border-danger-subtle" 
                           value="{{ old('sale_price', $product->sale_price) }}" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0">
                    <small class="form-help text-danger">{{ __('If set, this will be the new price with the old one crossed out') }}</small>
                </div>
            </div>

            @if($product->cost_price)
            <div class="alert alert-info" style="margin-bottom: 24px;">
                <i class="fas fa-chart-line"></i>
                <strong>{{ __('Profit Margin:') }}</strong> {{ number_format($product->profit_margin, 2) }}% 
                ({{ __('Profit:') }} ${{ number_format($product->price - $product->cost_price, 2) }} {{ __('per unit') }})
            </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label for="stock" class="form-label">
                        <i class="fas fa-boxes"></i> {{ __('Current Stock') }} <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="stock" 
                           name="stock" 
                           class="form-control" 
                           value="{{ old('stock', $product->stock) }}" 
                           placeholder="0" 
                           min="0" 
                           required>
                    @if($product->isLowStock())
                        <small class="form-help" style="color: #d97706;">⚠️ {{ __('Stock is below minimum level!') }}</small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="min_stock" class="form-label">
                        <i class="fas fa-exclamation-triangle"></i> {{ __('Minimum Stock Level') }} <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="min_stock" 
                           name="min_stock" 
                           class="form-control" 
                           value="{{ old('min_stock', $product->min_stock) }}" 
                           placeholder="10" 
                           min="0" 
                           required>
                    <small class="form-help">{{ __('Alert when stock falls below this level') }}</small>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label"><i class="fas fa-folder"></i> {{ __('Category') }}</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">{{ __('-- Select Category --') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->breadcrumb }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">{{ __('Select a category for this product') }}</small>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">
                    {{ __('Status') }} <span class="required">*</span>
                </label>
                <select id="status" name="status" class="form-control" required>
                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- Variations Section -->
            <div class="card mt-4 border-0 shadow-sm glass-card">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-0">
                    <h3 class="card-title mb-0 fw-bold">
                        <i class="fas fa-layer-group text-primary me-2"></i> {{ __('Product Variations') }}
                    </h3>
                    <button type="button" class="btn btn-brand-primary btn-sm rounded-pill px-3" onclick="addVariationRow()">
                        <i class="fas fa-plus me-1"></i> {{ __('Add Variation') }}
                    </button>
                </div>
                <div class="card-body p-3">
                    <div id="variantsContainer" class="variants-container p-3">
                        @foreach($product->variants as $index => $variant)
                        <div class="variant-card" data-index="{{ $index }}">
                            <div class="variant-actions">
                                <button type="button" class="btn-remove-variant" onclick="removeVariationRow(this)" title="{{ __('Remove Variation') }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            
                            <div class="variant-header-modern">
                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                <div class="variant-img-wrapper" onclick="this.querySelector('input').click()">
                                    @if($variant->color_image)
                                        <img src="{{ asset('storage/' . $variant->color_image) }}" id="variant_img_preview_{{ $index }}">
                                    @else
                                        <i class="fas fa-camera"></i>
                                    @endif
                                    <input type="file" name="variants[{{ $index }}][color_image]" class="d-none" accept="image/*" onchange="previewVariantImage(this, {{ $index }})">
                                </div>
                                <div class="variant-input-group d-none">
                                    <label>{{ __('Color') }}</label>
                                    <input type="color" name="variants[{{ $index }}][color_code]" class="form-control form-control-color" value="{{ $variant->color_code ?: '#000000' }}">
                                </div>
                                <div class="variant-input-group">
                                    <label>{{ __('اسم اللون') }}</label>
                                    <input type="text" name="variants[{{ $index }}][color]" class="form-control" value="{{ $variant->color }}" placeholder="{{ __('مثال: أحمر') }}">
                                </div>
                                <div class="variant-input-group">
                                    <label>{{ __('Size') }}</label>
                                    <input type="text" name="variants[{{ $index }}][size]" class="form-control" value="{{ $variant->size }}" placeholder="{{ __('e.g. XL') }}">
                                </div>
                                <div class="variant-input-group">
                                    <label>{{ __('Style Key (Grouping)') }}</label>
                                    <input type="text" name="variants[{{ $index }}][style_key]" class="form-control" value="{{ $variant->style_key }}" placeholder="{{ __('e.g. Noir-Luxe') }}">
                                </div>
                            </div>

                            <div class="variant-grid-inputs">
                                <div class="variant-input-group d-none">
                                    <label>{{ __('SKU') }}</label>
                                    <div class="input-group">
                                        <input type="text" name="variants[{{ $index }}][sku]" class="form-control font-inter" value="{{ $variant->sku }}" placeholder="{{ __('Unique SKU') }}">
                                        <button class="btn btn-outline-secondary" type="button" onclick="generateVariantSKU(this)" title="{{ __('Generate SKU') }}">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="variant-input-group">
                                    <label>{{ __('Price Override ($)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="variants[{{ $index }}][price]" class="form-control font-inter" value="{{ $variant->price }}" step="0.01" placeholder="{{ $product->price }}">
                                    </div>
                                </div>
                                <div class="variant-input-group">
                                    <label class="text-danger fw-bold">{{ __('Discount Price ($)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-danger-subtle text-danger border-danger-subtle">$</span>
                                        <input type="number" name="variants[{{ $index }}][sale_price]" class="form-control font-inter border-danger-subtle" value="{{ $variant->sale_price }}" step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="variant-input-group">
                                    <label>{{ __('Inventory Stock') }}</label>
                                    <div class="variant-stock-wrapper">
                                        <input type="number" name="variants[{{ $index }}][stock]" value="{{ $variant->stock }}" required min="0">
                                        <span class="variant-stock-unit">{{ __('units') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div id="noVariantsMsg" class="p-5 text-center text-muted {{ $product->variants->count() > 0 ? 'd-none' : '' }}">
                        <div class="mb-3">
                            <i class="fas fa-layer-group opacity-25" style="font-size: 48px;"></i>
                        </div>
                        <h5 class="fw-bold">{{ __('No variations configured') }}</h5>
                        <p class="small mb-0">{{ __('Add variations like size or color to this product.') }}</p>
                    </div>
                </div>
                <div class="card-footer bg-light py-3 border-0 d-flex justify-content-center">
                    <button type="button" onclick="addVariationRow()" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                        <i class="fas fa-plus"></i> {{ __('Add Another Variation') }}
                    </button>
                </div>
            </div>

            <div class="form-actions border-0 bg-transparent p-0 mt-5">
                <a href="{{ route('products.index') }}" class="btn btn-brand-light px-4 rounded-pill">
                    <i class="fas fa-times me-2"></i> {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-brand-primary px-5 rounded-pill shadow-sm">
                    <i class="fas fa-save me-2"></i> {{ __('Update Product') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// SKU Auto-Generation
function generateSKU() {
    const name = document.getElementById('name_ar').value || '';
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
    }
    
    const randomNum = Math.floor(Math.random() * 90000) + 10000;
    const timestamp = Date.now().toString().slice(-4);
    const sku = `${categoryPrefix}-${timestamp}${randomNum}`.substring(0, 16);
    
    document.getElementById('sku').value = sku;
    document.getElementById('sku').style.background = '#f0f9ff';
    setTimeout(() => { document.getElementById('sku').style.background = ''; }, 500);
}

// Variant SKU Auto-Generation
function generateVariantSKU(btn) {
    const card = btn.closest('.variant-card');
    const skuInput = card.querySelector('input[name*="[sku]"]');
    if (skuInput) {
        const baseProductSku = document.getElementById('sku').value || 'PROD-' + Date.now().toString().slice(-4);
        const randomNum = Math.floor(Math.random() * 900) + 100; // 3 digits
        skuInput.value = `${baseProductSku}-V${randomNum}`;
        
        // Visual feedback
        skuInput.style.background = '#f0f9ff';
        setTimeout(() => { skuInput.style.background = ''; }, 500);
    }
}

let imagesToRemove = [];
let newFiles = [];

function markImageForRemoval(imageId, button) {
    if (!imagesToRemove.includes(imageId)) {
        imagesToRemove.push(imageId);
        button.parentElement.style.opacity = '0.3';
        button.innerHTML = '↺';
        
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
        button.innerHTML = '×';
        document.getElementById('remove_' + imageId).remove();
    }
}

function handleNewImages(event) {
    const container = document.getElementById('imagePreviewContainer');
    const files = Array.from(event.target.files);
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    let validFiles = [];
    let errorMessages = [];

    files.forEach(file => {
        if (!file.type.match('image.*')) {
            errorMessages.push(`- ${file.name}: {{ __('Not a valid image file.') }}`);
        } else if (file.size > maxSize) {
            errorMessages.push(`- ${file.name}: {{ __('The image is too large. Max size is 10MB.') }}`);
        } else {
            validFiles.push(file);
        }
    });

    if (errorMessages.length > 0) {
        showValidationErrors("{{ __('Images Issue') }}", errorMessages);
        // If all selected files are invalid, clear the input so nothing is submitted
        if (validFiles.length === 0) {
            event.target.value = '';
            return;
        }
    }
    
    validFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const box = document.createElement('div');
            box.className = 'image-upload-box';
            box.innerHTML = `
                <img src="${e.target.result}" alt="New image">
                <span class="image-remove-btn" onclick="this.parentElement.remove()">×</span>
            `;
            // Insert before the "add more" button
            container.insertBefore(box, container.lastElementChild);
        };
        reader.readAsDataURL(file);
    });
}

// ── Variations Management ───────────────────────
let variantCounter = {{ $product->variants->count() + 100 }}; // Offset to avoid any collision with existing indices

function addVariationRow() {
    const vIndex = variantCounter++;
    const container = document.getElementById('variantsContainer');
    const noMsg = document.getElementById('noVariantsMsg');
    
    if (noMsg) noMsg.classList.add('d-none');
    
    const div = document.createElement('div');
    div.className = 'variant-card';
    div.dataset.index = vIndex;
    div.innerHTML = `
        <div class="variant-actions">
            <button type="button" class="btn-remove-variant" onclick="removeVariationRow(this)" title="{{ __('Remove Variation') }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
        
        <div class="variant-header-modern">
            <div class="variant-img-wrapper" onclick="this.querySelector('input').click()">
                <i class="fas fa-camera"></i>
                <span>{{ __('صورة') }}</span>
                <input type="file" name="variants[${vIndex}][color_image]" class="d-none" accept="image/*" onchange="previewVariantImage(this, ${vIndex})">
            </div>
            <div class="variant-input-group d-none">
                <label>{{ __('Color') }}</label>
                <input type="color" name="variants[${vIndex}][color_code]" class="form-control form-control-color" value="#000000">
            </div>
            <div class="variant-input-group">
                <label>{{ __('اسم اللون') }}</label>
                <input type="text" name="variants[${vIndex}][color]" class="form-control" placeholder="{{ __('مثال: أحمر') }}">
            </div>
            <div class="variant-input-group">
                <label>{{ __('Size') }}</label>
                <input type="text" name="variants[${vIndex}][size]" class="form-control" placeholder="{{ __('e.g. XL') }}">
            </div>
            <div class="variant-input-group">
                <label>{{ __('Style Key (Grouping)') }}</label>
                <input type="text" name="variants[${vIndex}][style_key]" class="form-control" placeholder="{{ __('e.g. Noir-Luxe') }}">
            </div>
        </div>

        <div class="variant-grid-inputs">
            <div class="variant-input-group d-none">
                <label>{{ __('SKU') }}</label>
                <div class="input-group">
                    <input type="text" name="variants[${vIndex}][sku]" class="form-control font-inter" placeholder="{{ __('رمز المنتج') }}">
                    <button class="btn btn-outline-secondary" type="button" onclick="generateVariantSKU(this)" title="{{ __('Generate SKU') }}">
                        <i class="fas fa-magic"></i>
                    </button>
                </div>
            </div>
            <div class="variant-input-group">
                <label>{{ __('Price Override ($)') }}</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="variants[${vIndex}][price]" class="form-control font-inter" step="0.01" placeholder="0.00">
                </div>
            </div>
            <div class="variant-input-group">
                <label class="text-danger fw-bold">{{ __('Discount Price ($)') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-danger-subtle text-danger border-danger-subtle">$</span>
                    <input type="number" name="variants[${vIndex}][sale_price]" class="form-control font-inter border-danger-subtle" step="0.01" placeholder="0.00">
                </div>
            </div>
            <div class="variant-input-group">
                <label>{{ __('Inventory Stock') }}</label>
                <div class="variant-stock-wrapper">
                    <input type="number" name="variants[${vIndex}][stock]" value="10" required min="0">
                    <span class="variant-stock-unit">{{ __('units') }}</span>
                </div>
            </div>
        </div>
    `;
    container.appendChild(div);

    // Smooth scroll to the new variant
    div.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Auto-generate SKU for the new variant
    const addBtn = div.querySelector('.fa-magic')?.parentElement;
    if (addBtn) generateVariantSKU(addBtn);
}

function removeVariationRow(btn) {
    window.confirmAction("{{ __('Are you sure?') }}", "{{ __('Are you sure you want to remove this variation?') }}").then(confirmed => {
        if (confirmed) {
            const tr = btn.closest('.variant-card');
            tr.remove();
            
            const container = document.getElementById('variantsContainer');
            if (container.children.length === 0) {
                document.getElementById('noVariantsMsg').classList.remove('d-none');
            }
        }
    });
}

function previewVariantImage(input, index) {
    const maxSize = 10 * 1024 * 1024; // 10MB
    if (input.files && input.files[0]) {
        if (input.files[0].size > maxSize) {
            showError("{{ __('Error') }}", "{{ __('The image is too large. Max size is 10MB.') }}");
            input.value = ''; // Reset input
            return;
        }

        // Type Check
        if (!input.files[0].type.match('image.*')) {
            showError("{{ __('Error') }}", "{{ __('Please select a valid image file.') }}");
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const container = input.closest('.variant-img-wrapper');
            let img = container.querySelector('img');
            
            if (!img) {
                const icon = container.querySelector('i');
                const span = container.querySelector('span');
                if (icon) icon.remove();
                if (span) span.remove();
                
                img = document.createElement('img');
                img.id = `variant_img_preview_${index}`;
                container.appendChild(img);
            }
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Pre-save Validation
document.getElementById('productForm')?.addEventListener('submit', function(e) {
    const variants = document.querySelectorAll('.variant-card');
    let hasError = false;
    let errorMessage = "";

    variants.forEach((card, i) => {
        const skuInput = card.querySelector('input[name*="[sku]"]');
        const stockInput = card.querySelector('input[name*="[stock]"]');
        const sizeInput = card.querySelector('input[name*="[size]"]');
        const fileInput = card.querySelector('input[type="file"]');

        // Basic variant validation
        if (!skuInput.value.trim()) {
            hasError = true;
            errorMessage += `\n- ${'{{ __("Variant") }}'} ${i+1}: ${'{{ __("SKU is required") }}'}`;
        }
        if (stockInput.value < 0) {
            hasError = true;
            errorMessage += `\n- ${'{{ __("Variant") }}'} ${i+1}: ${'{{ __("Stock cannot be negative") }}'}`;
        }

        // Final File Size Check (double-check before submit)
        if (fileInput && fileInput.files[0] && fileInput.files[0].size > 10 * 1024 * 1024) {
            hasError = true;
            errorMessage += `\n- ${'{{ __("Variant") }}'} ${i+1}: ${'{{ __("Image exceeds 10MB limit") }}'}`;
        }
    });

    // Check main product images
    const mainImagesInput = document.getElementById('images');
    if (mainImagesInput && mainImagesInput.files.length > 0) {
        Array.from(mainImagesInput.files).forEach((file, idx) => {
            if (file.size > 10 * 1024 * 1024) {
                hasError = true;
                errorMessage += `\n- {{ __('Main Product Image') }} ${file.name}: {{ __('Image exceeds 10MB limit') }}`;
            }
        });
    }

    if (hasError) {
        e.preventDefault();
        
        // Show validation errors as a list
        const errors = errorMessage.split('\n').filter(e => e.trim().length > 0);
        showValidationErrors("{{ __('Please fix the following issues before saving:') }}", errors);
    }
});
</script>
@endpush
@endsection
