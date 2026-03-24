@extends('layouts.app')

@section('title', __('Add New Product'))

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
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> {{ __('Add New Product') }}</h1>
    <p class="page-subtitle">{{ __('Create a new product in your catalog') }}</p>
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
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label"><i class="fas fa-images"></i> {{ __('Product Images (Multiple)') }}</label>
                <div class="multi-image-upload" id="imagePreviewContainer">
                    <div class="image-upload-box" onclick="document.getElementById('images').click()">
                        <div>
                            <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: #94a3b8;"></i>
                            <p style="margin-top: 6px; color: #64748b; font-size: 11px; text-align: center;">{{ __('Click to upload') }}</p>
                        </div>
                    </div>
                </div>
                <input type="file" 
                       id="images" 
                       name="images[]" 
                       accept="image/*" 
                       multiple
                       style="display: none;" 
                       onchange="handleMultipleImages(event)">
                <small class="form-help">{{ __('You can upload multiple images. First image will be the primary image. Max 2MB each.') }}</small>
            </div>

            <div class="form-row d-none">
                <div class="form-group">
                    <label for="name_fr" class="form-label">
                        {{ __('Product Name (FR)') }}
                    </label>
                    <input type="text" id="name_fr" name="name_fr" class="form-control" value="{{ old('name_fr') }}" placeholder="Nom du produit">
                </div>
                <div class="form-group">
                    <label for="name_en" class="form-label">{{ __('Product Name (EN)') }}</label>
                    <input type="text" id="name_en" name="name_en" class="form-control" value="{{ old('name_en') }}" placeholder="Product Name">
                </div>
            </div>

            <div class="form-group">
                <label for="name_ar" class="form-label">{{ __('Product Name (AR)') }} <span class="required">*</span></label>
                <input type="text" id="name_ar" name="name_ar" class="form-control" value="{{ old('name_ar') }}" placeholder="اسم المنتج" dir="rtl" required autofocus>
            </div>

            <div class="form-row">
                <input type="hidden" name="name" id="name" value="{{ old('name', 'autofill') }}">
                
                <div class="form-group">
                    <label for="sku" class="form-label">
                        {{ __('SKU') }} <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               id="sku" 
                               name="sku" 
                               class="form-control" 
                               value="{{ old('sku') }}" 
                               placeholder="{{ __('Auto-generated or custom') }}" 
                               required>
                        <button type="button" 
                                onclick="generateSKU()" 
                                class="btn btn-outline-primary">
                            <i class="fas fa-magic"></i> {{ __('Generate') }}
                        </button>
                    </div>
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i> {{ __('Auto-generates on page load or click "Generate" for new SKU') }}
                    </small>
                </div>
            </div>

            <div class="d-none">
                <div class="form-group mt-3">
                    <label for="description_fr" class="form-label">{{ __('Description (FR)') }}</label>
                    <textarea id="description_fr" name="description_fr" class="form-control" rows="2" placeholder="Description en français...">{{ old('description_fr') }}</textarea>
                </div>
                <div class="form-group mt-3">
                    <label for="description_en" class="form-label">{{ __('Description (EN)') }}</label>
                    <textarea id="description_en" name="description_en" class="form-control" rows="2" placeholder="English description...">{{ old('description_en') }}</textarea>
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="description_ar" class="form-label">{{ __('Description (AR)') }}</label>
                <textarea id="description_ar" name="description_ar" class="form-control" rows="2" placeholder="وصف عربي..." dir="rtl">{{ old('description_ar') }}</textarea>
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
                           value="{{ old('cost_price') }}" 
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
                           value="{{ old('price') }}" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0" 
                           required>
                    <small class="form-help">{{ __('Price you sell to customers') }}</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock" class="form-label">
                        <i class="fas fa-boxes"></i> {{ __('Current Stock') }} <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="stock" 
                           name="stock" 
                           class="form-control" 
                           value="{{ old('stock', 0) }}" 
                           placeholder="0" 
                           min="0" 
                           required>
                </div>

                <div class="form-group">
                    <label for="min_stock" class="form-label">
                        <i class="fas fa-exclamation-triangle"></i> {{ __('Minimum Stock Level') }} <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="min_stock" 
                           name="min_stock" 
                           class="form-control" 
                           value="{{ old('min_stock', 10) }}" 
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
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- Variations Section -->
            <div class="card mt-4 border-0 shadow-sm" style="background: #fdfdfd;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h3 class="card-title mb-0"><i class="fas fa-layer-group text-primary"></i> {{ __('Product Variations') }}</h3>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="addVariationRow()">
                        <i class="fas fa-plus me-1"></i> {{ __('Add Variation') }}
                    </button>
                </div>
            <!-- Product Variations Section -->
            <div class="card glass-card border-0 mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">{{ __('Product Variations') }}</h5>
                    <button type="button" onclick="addVariationRow()" class="btn btn-brand-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Add Variation') }}
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="variantsContainer">
                        <!-- Dynamic Variant Cards -->
                    </div>
                    <div id="noVariantsMsg" class="p-5 text-center text-muted">
                        <div class="mb-3">
                            <i class="fas fa-layer-group" style="font-size: 40px; color: #e2e8f0;"></i>
                        </div>
                        <h5>{{ __('No variations configured') }}</h5>
                        <p class="small mb-0">{{ __('Products without variations use the main color (if defined) and total stock.') }}</p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('Create Product') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
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
                <span class="image-remove-btn" onclick="removeImage(${index})">&times;</span>
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
        <div>
            <i class="fas fa-plus" style="font-size: 24px; color: #94a3b8;"></i>
            <p style="margin-top: 6px; color: #64748b; font-size: 11px; text-align: center;">Add more</p>
        </div>
    `;
    container.appendChild(uploadBox);
    
    // Update file input
    updateFileInput();
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    displayImages();
}

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    const input = document.getElementById('images');
    if (input) {
        input.files = dataTransfer.files;
    }
}

// SKU Auto-Generation
function generateSKU() {
    // We will use FR name or EN name as backup for generating SKU
    const name = document.getElementById('name_fr').value || document.getElementById('name_en').value || document.getElementById('name_ar').value || '';
    const categoryId = document.getElementById('category_id').value;
    
    // Get category select element
    const categorySelect = document.getElementById('category_id');
    let categoryPrefix = 'PROD';
    
    // Try to get category abbreviation from selected category
    if (categoryId && categorySelect.selectedIndex > 0) {
        const categoryName = categorySelect.options[categorySelect.selectedIndex].text;
        // Extract category prefix (first letters of first 2-3 words, max 4 chars)
        const words = categoryName.split(/[\s\->]+/).filter(w => w.length > 0);
        if (words.length >= 2) {
            categoryPrefix = (words[0].substring(0, 2) + words[1].substring(0, 2)).toUpperCase();
        } else if (words.length === 1) {
            categoryPrefix = words[0].substring(0, 4).toUpperCase();
        }
    } else if (name) {
        // Use product name if no category
        const words = name.split(/\s+/).filter(w => w.length > 0);
        if (words.length >= 2) {
            categoryPrefix = (words[0].substring(0, 2) + words[1].substring(0, 2)).toUpperCase();
        } else if (words.length === 1) {
            categoryPrefix = words[0].substring(0, 4).toUpperCase();
        }
    }
    
    // Generate random number
    const randomNum = Math.floor(Math.random() * 90000) + 10000; // 5-digit number
    const timestamp = Date.now().toString().slice(-4); // Last 4 digits of timestamp
    
    // Combine to create SKU
    const sku = `${categoryPrefix}-${timestamp}${randomNum}`.substring(0, 16); // Limit length
    
    document.getElementById('sku').value = sku;
    
    // Visual feedback
    document.getElementById('sku').style.background = '#f0f9ff';
    setTimeout(() => {
        document.getElementById('sku').style.background = '';
    }, 500);
}

// Auto-generate SKU on page load if field is empty
document.addEventListener('DOMContentLoaded', function() {
    const skuField = document.getElementById('sku');
    if (!skuField.value) {
        generateSKU();
    }
    
    // Also regenerate when category changes
    document.getElementById('category_id').addEventListener('change', function() {
        // Auto-regenerate without confirmation if SKU hasn't been manually edited
        if (this.value) {
            generateSKU();
        }
    });
    
    // Offer to generate SKU when product name is typed
    let nameTimeout;
    const nameInput = document.getElementById('name_fr') || document.getElementById('name_en');
    if(nameInput) {
        nameInput.addEventListener('input', function() {
            clearTimeout(nameTimeout);
            nameTimeout = setTimeout(() => {
                if (!document.getElementById('sku').value && this.value.length > 3) {
                    generateSKU();
                }
            }, 800); // Wait 800ms after user stops typing
        });
    }
});

// ── Variations Management ───────────────────────
let variantIndex = 0;

function addVariationRow() {
    const container = document.getElementById('variantsContainer');
    const noMsg = document.getElementById('noVariantsMsg');
    
    if (noMsg) noMsg.classList.add('d-none');
    
    const div = document.createElement('div');
    div.className = 'variant-card';
    div.dataset.index = variantIndex;
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
                <input type="file" name="variants[${variantIndex}][color_image]" class="d-none" accept="image/*" onchange="previewVariantImage(this, ${variantIndex})">
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="variant-input-group" style="width: 45%;">
                        <label>{{ __('اللون') }}</label>
                        <input type="color" name="variants[${variantIndex}][color_code]" class="form-control form-control-color w-100" value="#000000">
                    </div>
                    <div class="variant-input-group" style="width: 45%;">
                        <label>{{ __('الحجم') }}</label>
                        <input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="{{ __('مثال: XL') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="variant-grid-inputs mt-2">
            <div class="variant-input-group">
                <label>{{ __('رمز المنتج') }}</label>
                <input type="text" name="variants[${variantIndex}][sku]" class="form-control font-inter" placeholder="{{ __('رمز المنتج') }}">
            </div>
            <div class="variant-input-group">
                <label>{{ __('تجاوز السعر') }}</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="variants[${variantIndex}][price]" class="form-control font-inter" step="0.01" placeholder="{{ __('السعر') }}">
                </div>
            </div>
            <div class="variant-input-group col-12">
                <label>{{ __('المخزون') }}</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" name="variants[${variantIndex}][stock]" class="form-control text-center fw-bold font-inter" value="10" required min="0">
                    <span class="text-muted small">{{ __('وحدة') }}</span>
                </div>
            </div>
        </div>
    `;
    container.appendChild(div);
    variantIndex++;
}

function removeVariationRow(btn) {
    if (confirm("{{ __('Are you sure you want to remove this variation?') }}")) {
        const tr = btn.closest('.variant-card');
        tr.remove();
        
        const container = document.getElementById('variantsContainer');
        if (container.children.length === 0) {
            document.getElementById('noVariantsMsg').classList.remove('d-none');
        }
    }
}

function previewVariantImage(input, index) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let img = document.getElementById(`variant_img_preview_${index}`);
            const container = input.closest('.variant-img-wrapper');
            
            if (!img) {
                // Clear the icon and span
                container.innerHTML = `<input type="file" name="variants[${index}][color_image]" class="d-none" accept="image/*" onchange="previewVariantImage(this, ${index})">`;
                
                // Create and add img
                img = document.createElement('img');
                img.id = `variant_img_preview_${index}`;
                img.onclick = () => container.querySelector('input').click();
                container.appendChild(img);
            }
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
