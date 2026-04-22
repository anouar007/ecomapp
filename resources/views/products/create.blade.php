@extends('layouts.app')

@section('title', 'Add New Product')

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
.variation-input {
    height: 42px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    font-size: 0.9rem !important;
    background-color: #fff !important;
    padding: 0.5rem 0.75rem !important;
}
.variant-img-upload {
    width: 42px !important;
    height: 42px !important;
    border-radius: 8px !important;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.2s;
    flex-shrink: 0;
}
.input-group {
    height: 42px !important;
}
.input-group-text {
    height: 42px !important;
    background-color: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-right: none !important;
    border-radius: 8px 0 0 8px !important;
    padding: 0 12px !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
}
.input-group .variation-input {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> Add New Product</h1>
    <p class="page-subtitle">Create a new product in your catalog</p>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        <strong>Oops! Something went wrong:</strong>
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
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Product Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label"><i class="fas fa-images"></i> Product Images (Multiple)</label>
                <div class="multi-image-upload" id="imagePreviewContainer">
                    <div class="image-upload-box" onclick="document.getElementById('images').click()">
                        <div>
                            <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: #94a3b8;"></i>
                            <p style="margin-top: 6px; color: #64748b; font-size: 11px; text-align: center;">Click to upload</p>
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
                <small class="form-help">You can upload multiple images. First image will be the primary image. Max 4MB each.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="name_fr" class="form-label">
                        Product Name (FR) <span class="required">*</span>
                    </label>
                    <input type="text" id="name_fr" name="name_fr" class="form-control" value="{{ old('name_fr') }}" placeholder="Nom du produit" required autofocus>
                </div>
                <div class="form-group">
                    <label for="name_en" class="form-label">Product Name (EN)</label>
                    <input type="text" id="name_en" name="name_en" class="form-control" value="{{ old('name_en') }}" placeholder="Product Name">
                </div>
                <div class="form-group">
                    <label for="name_ar" class="form-label">Product Name (AR)</label>
                    <input type="text" id="name_ar" name="name_ar" class="form-control" value="{{ old('name_ar') }}" placeholder="اسم المنتج" dir="rtl">
                </div>
            </div>

            <div class="form-row">
                <input type="hidden" name="name" id="name" value="{{ old('name', 'autofill') }}">
                
            <input type="hidden" name="sku" id="sku" value="{{ old('sku') }}">
            </div>

            <div class="form-group mt-3">
                <label for="description_fr" class="form-label">Description (FR)</label>
                <textarea id="description_fr" name="description_fr" class="form-control" rows="2" placeholder="Description en français...">{{ old('description_fr') }}</textarea>
            </div>
            <div class="form-group mt-3">
                <label for="description_en" class="form-label">Description (EN)</label>
                <textarea id="description_en" name="description_en" class="form-control" rows="2" placeholder="English description...">{{ old('description_en') }}</textarea>
            </div>
            <div class="form-group mt-3">
                <label for="description_ar" class="form-label">Description (AR)</label>
                <textarea id="description_ar" name="description_ar" class="form-control" rows="2" placeholder="وصف عربي..." dir="rtl">{{ old('description_ar') }}</textarea>
            </div>

            <div class="form-row mt-4">
                <div class="form-group">
                    <label for="cost_price" class="form-label">
                        <i class="fas fa-dollar-sign"></i> Cost Price ($)
                    </label>
                    <input type="number" 
                           id="cost_price" 
                           name="cost_price" 
                           class="form-control" 
                           value="{{ old('cost_price') }}" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0">
                    <small class="form-help">How much you pay for this product</small>
                </div>

                <div class="form-group">
                    <label for="price" class="form-label">
                        <i class="fas fa-tag"></i> Selling Price ($) <span class="required">*</span>
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
                    <small class="form-help">Price you sell to customers</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock" class="form-label">
                        <i class="fas fa-boxes"></i> Current Stock <span class="required">*</span>
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
                        <i class="fas fa-exclamation-triangle"></i> Minimum Stock Level <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="min_stock" 
                           name="min_stock" 
                           class="form-control" 
                           value="{{ old('min_stock', 10) }}" 
                           placeholder="10" 
                           min="0" 
                           required>
                    <small class="form-help">Alert when stock falls below this level</small>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label"><i class="fas fa-folder"></i> Category</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->breadcrumb }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">Select a category for this product</small>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">
                    Status <span class="required">*</span>
                </label>
                <select id="status" name="status" class="form-control" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Variations Section -->
            <div class="card mt-4 border-0 shadow-sm" style="background: #fdfdfd;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h3 class="card-title mb-0"><i class="fas fa-layer-group text-primary"></i> Product Variations</h3>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="addVariationRow()">
                        <i class="fas fa-plus me-1"></i> Add Variation
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="variantsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 80px; padding-left: 1.5rem;">Image</th>
                                    <th style="width: 25%;">Color / Material</th>
                                    <th style="width: 25%;">Size</th>
                                    <th style="width: 25%;">Price (Override)</th>
                                    <th style="width: 25%;">Stock</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="variantsBody">
                                <!-- Dynamic Rows -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noVariantsMsg" class="p-5 text-center text-muted">
                        <div class="mb-3">
                            <i class="fas fa-layer-group" style="font-size: 40px; color: #e2e8f0;"></i>
                        </div>
                        <h5>No variations configured</h5>
                        <p class="small mb-0">Products without variations use the main color (if defined) and total stock.</p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Product
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
    
    // Validate size (4MB = 4096 KB)
    const oversizedFiles = files.filter(f => f.size > 4 * 1024 * 1024);
    if (oversizedFiles.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'File too large',
            text: 'One or more images exceed the 4MB limit.'
        });
        return;
    }

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
    const nameInput = document.getElementById('name_fr') || document.getElementById('name_en') || document.getElementById('name_ar');
    const name = nameInput ? nameInput.value : '';
    const categorySelect = document.getElementById('category_id');
    const categoryId = categorySelect ? categorySelect.value : '';
    
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
    
    const skuField = document.getElementById('sku');
    if (skuField) {
        skuField.value = sku;
        skuField.style.background = '#f0f9ff';
        setTimeout(() => { skuField.style.background = ''; }, 500);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // 1. Auto-generate SKU on load if empty
    const skuField = document.getElementById('sku');
    if (skuField && !skuField.value) {
        generateSKU();
    }
    
    // 2. Regenerate when category changes
    const catSelect = document.getElementById('category_id');
    if (catSelect) {
        catSelect.addEventListener('change', function() {
            if (this.value) generateSKU();
        });
    }
    
    // 3. Offer to generate SKU when name is typed
    let nameTimeout;
    const nameInput = document.getElementById('name_fr') || document.getElementById('name_en');
    if(nameInput) {
        nameInput.addEventListener('input', function() {
            clearTimeout(nameTimeout);
            nameTimeout = setTimeout(() => {
                const skuField = document.getElementById('sku');
                if (skuField && !skuField.value && this.value.length > 3) {
                    generateSKU();
                }
            }, 800);
        });
    }
});

// ── Variations Management ───────────────────────
let variantIndex = 0;

function addVariationRow() {
    const tbody = document.getElementById('variantsBody');
    const noMsg = document.getElementById('noVariantsMsg');
    if (noMsg) noMsg.classList.add('d-none');
    
    const tr = document.createElement('tr');
    tr.dataset.index = variantIndex;
    tr.innerHTML = `
        <td style="padding-left: 1.5rem;">
            <label class="variant-img-upload mb-0">
                <div class="no-img-placeholder"><i class="fas fa-camera"></i></div>
                <input type="file" name="variants[${variantIndex}][color_image]" class="d-none" accept="image/*" onchange="previewVariantImage(this, ${variantIndex})">
            </label>
        </td>
        <td><input type="text" name="variants[${variantIndex}][color]" class="form-control variation-input" placeholder="e.g. Red or #FF0000"></td>
        <td><input type="text" name="variants[${variantIndex}][size]" class="form-control variation-input" placeholder="e.g. XL"></td>
        <td>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="variants[${variantIndex}][price]" class="form-control variation-input" step="0.01" placeholder="Price">
            </div>
        </td>
        <td><input type="number" name="variants[${variantIndex}][stock]" class="form-control variation-input text-center" value="10" required min="0"></td>
        <td class="text-end pe-3">
            <button type="button" class="btn btn-link text-danger p-0" onclick="removeVariationRow(this)"><i class="fas fa-trash-alt"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    variantIndex++;
}

function removeVariationRow(btn) {
    window.confirmAction(
        'Remove variation?',
        'You will need to add it again if you change your mind.'
    ).then((confirmed) => {
        if (confirmed) {
            const tr = btn.closest('tr');
            tr.remove();
            if (document.getElementById('variantsBody').children.length === 0) {
                document.getElementById('noVariantsMsg').classList.remove('d-none');
            }
        }
    });
}

function previewVariantImage(input, index) {
    if (input.files && input.files[0]) {
        if (input.files[0].size > 4 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'File too large', text: 'Variant image exceeds 4MB limit.' });
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const parent = input.parentElement;
            let img = parent.querySelector('img');
            if (!img) {
                // If no img exists, hide placeholder and add img
                const placeholder = parent.querySelector('.no-img-placeholder');
                if (placeholder) placeholder.style.display = 'none';
                
                img = document.createElement('img');
                img.id = `variant_img_preview_${index}`;
                parent.appendChild(img);
            }
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
