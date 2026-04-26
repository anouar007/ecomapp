@extends('layouts.app')

@section('title', __('Add New Product'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
    .multi-image-upload {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .image-upload-box {
        width: 100%;
        aspect-ratio: 1/1;
        border: 2px dashed #e2e8f0;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f8fafc;
        position: relative;
        overflow: hidden;
    }

    .image-upload-box:hover {
        border-color: var(--primary-color, #667eea);
        background: #f0f4ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
        background: rgba(239, 68, 68, 0.9);
        backdrop-filter: blur(4px);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        z-index: 10;
        transition: all 0.2s;
    }
    
    .image-remove-btn:hover {
        background: #ef4444;
        transform: scale(1.1);
    }

    /* Tabs Styling */
    .nav-tabs-custom {
        border-bottom: 2px solid #f1f5f9;
        gap: 8px;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        padding: 10px 20px;
        border-radius: 10px 10px 0 0;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s;
        position: relative;
    }
    .nav-tabs-custom .nav-link.active {
        color: var(--primary-color, #667eea);
        background: transparent;
    }
    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--primary-color, #667eea);
    }

    /* Variation Card */
    .variant-card {
        display: block !important;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        position: relative;
    }
    
    .variant-header {
        display: flex !important;
        align-items: center !important;
        gap: 16px !important;
        margin-bottom: 20px !important;
        padding-bottom: 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        width: 100% !important;
    }

    .variant-img-wrapper {
        width: 70px !important;
        height: 70px !important;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }
    
    .variant-grid-container {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
        gap: 20px !important;
        width: 100% !important;
    }

    .variant-input-group {
        display: block !important;
        width: 100% !important;
    }

    .variant-input-xl {
        display: block !important;
        width: 100% !important;
        height: 46px !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        border-radius: 10px !important;
        padding: 0 15px !important;
        border: 1px solid #e2e8f0 !important;
        background: #fff !important;
    }
    
    .variant-input-xl:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    }

    .variant-label-xl {
        display: block !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #64748b !important;
        margin-bottom: 8px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    .variant-img-wrapper span {
        font-size: 10px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .btn-remove-variant {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fff;
        border: 1px solid #fee2e2;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-remove-variant:hover {
        background: #ef4444;
        color: #fff;
        border-color: #ef4444;
    }

    .form-section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f1f5f9;
    }

    .sticky-actions {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
</style>
@endpush

@section('content')
<div class="brand-header mb-4">
    <div>
        <h1 class="brand-title">
            <div class="brand-header-icon"><i class="fas fa-plus-circle"></i></div>
            {{ __('Add New Product') }}
        </h1>
        <p class="brand-subtitle">{{ __('Create a premium entry in your boutique catalog') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> {{ __('Cancel') }}
        </a>
        <button type="submit" form="productForm" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('Save Product') }}
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <i class="fas fa-exclamation-circle fs-4"></i>
    <div>
        <h6 class="fw-bold mb-1">{{ __('Validation Errors') }}</h6>
        <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf
    <div class="row g-4">
        <!-- Main Column -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> {{ __('Basic Information') }}
                    </div>

                    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="langTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar-content" type="button" role="tab">{{ __('Arabic') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr-content" type="button" role="tab">{{ __('French') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-content" type="button" role="tab">{{ __('English') }}</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="langTabsContent">
                        <!-- Arabic -->
                        <div class="tab-pane fade show active" id="ar-content" role="tabpanel">
                            <div class="form-group">
                                <label class="form-label">{{ __('Product Name (AR)') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" class="form-control form-control-lg" value="{{ old('name_ar') }}" placeholder="اسم المنتج بالكامل" dir="rtl" required autofocus>
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label">{{ __('Description (AR)') }}</label>
                                <textarea name="description_ar" class="form-control" rows="4" placeholder="وصف المنتج بالتفصيل..." dir="rtl">{{ old('description_ar') }}</textarea>
                            </div>
                        </div>
                        <!-- French -->
                        <div class="tab-pane fade" id="fr-content" role="tabpanel">
                            <div class="form-group">
                                <label class="form-label">{{ __('Product Name (FR)') }}</label>
                                <input type="text" name="name_fr" class="form-control form-control-lg" value="{{ old('name_fr') }}" placeholder="Nom du produit">
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label">{{ __('Description (FR)') }}</label>
                                <textarea name="description_fr" class="form-control" rows="4" placeholder="Description détaillée en français...">{{ old('description_fr') }}</textarea>
                            </div>
                        </div>
                        <!-- English -->
                        <div class="tab-pane fade" id="en-content" role="tabpanel">
                            <div class="form-group">
                                <label class="form-label">{{ __('Product Name (EN)') }}</label>
                                <input type="text" name="name_en" class="form-control form-control-lg" value="{{ old('name_en') }}" placeholder="Product display name">
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label">{{ __('Description (EN)') }}</label>
                                <textarea name="description_en" class="form-control" rows="4" placeholder="Detailed English description...">{{ old('description_en') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="name" value="autofill">
                </div>
            </div>

            <!-- Pricing & Inventory Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="form-section-title">
                        <i class="fas fa-wallet"></i> {{ __('Pricing & Inventory') }}
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('Cost Price') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-hand-holding-dollar text-muted"></i></span>
                                    <input type="number" name="cost_price" class="form-control border-start-0" value="{{ old('cost_price') }}" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('Regular Price') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-tag text-muted"></i></span>
                                    <input type="number" name="price" class="form-control border-start-0 fw-bold" value="{{ old('price') }}" step="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label text-danger fw-bold">{{ __('Sale Price') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-danger-subtle border-danger-subtle border-end-0"><i class="fas fa-bolt text-danger"></i></span>
                                    <input type="number" name="sale_price" class="form-control border-danger-subtle border-start-0 text-danger fw-bold" value="{{ old('sale_price') }}" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label">{{ __('Current Stock') }} <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label">{{ __('Low Stock Alert') }}</label>
                                <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', 5) }}" required min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variations Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div class="form-section-title mb-0" style="flex: none;">
                        <i class="fas fa-layer-group"></i> {{ __('Product Variations') }}
                    </div>
                    <button type="button" onclick="addVariationRow()" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-plus"></i> {{ __('Add Variation') }}
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="variantsContainer">
                        <!-- Variations injected here -->
                    </div>
                    <div id="noVariantsMsg" class="text-center py-5 bg-light rounded-4 border-2 border-dashed">
                        <i class="fas fa-swatchbook fa-3x text-muted opacity-25 mb-3"></i>
                        <h6 class="fw-bold text-muted">{{ __('No variations yet') }}</h6>
                        <p class="small text-muted mb-0">{{ __('Add sizes, colors or styles to this product.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="sticky-actions">
                <!-- Status & Category Card -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="fas fa-cog"></i> {{ __('Status & Category') }}
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">{{ __('Product Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('Active / Visible') }}</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Hidden / Draft') }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('Primary Category') }}</label>
                            <select name="category_id" class="form-select" id="category_id">
                                <option value="">{{ __('-- Select Category --') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->breadcrumb }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">{{ __('SKU / Identifier') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="sku" id="sku" class="form-control font-monospace small" value="{{ old('sku') }}" placeholder="AUTOGEN" required>
                                <button type="button" onclick="generateSKU()" class="btn btn-light border"><i class="fas fa-sync-alt"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Gallery Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="fas fa-images"></i> {{ __('Media Gallery') }}
                        </div>
                        
                        <div class="multi-image-upload" id="imagePreviewContainer">
                            <div class="image-upload-box" onclick="document.getElementById('images').click()">
                                <div class="text-center">
                                    <i class="fas fa-cloud-arrow-up fs-3 text-primary mb-2"></i>
                                    <div class="x-small fw-bold">{{ __('Upload') }}</div>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple style="display: none;" onchange="handleMultipleImages(event)">
                        
                        <div class="mt-3 p-3 bg-light rounded-3 small text-muted">
                            <i class="fas fa-info-circle me-1"></i> {{ __('First image will be used as the primary thumbnail.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Mobile Actions -->
    <div class="sticky-mobile-actions d-lg-none">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('Save') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
let selectedFiles = [];

function handleMultipleImages(event) {
    const files = Array.from(event.target.files);
    const maxSize = 4 * 1024 * 1024;
    let validFiles = [];

    files.forEach(file => {
        if (file.size <= maxSize && file.type.match('image.*')) {
            validFiles.push(file);
        } else {
            Toast.fire({ icon: 'error', title: `{{ __('Invalid file:') }} ${file.name}` });
        }
    });

    selectedFiles = selectedFiles.concat(validFiles);
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
                <img src="${e.target.result}">
                <span class="image-remove-btn" onclick="removeImage(${index})"><i class="fas fa-times"></i></span>
            `;
            container.appendChild(box);
        }
        reader.readAsDataURL(file);
    });
    
    const uploadBox = document.createElement('div');
    uploadBox.className = 'image-upload-box';
    uploadBox.onclick = () => document.getElementById('images').click();
    uploadBox.innerHTML = '<div class="text-center"><i class="fas fa-plus text-primary fs-4"></i></div>';
    container.appendChild(uploadBox);
    
    updateFileInput();
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    displayImages();
}

function updateFileInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    document.getElementById('images').files = dt.files;
}

function generateSKU() {
    const name = document.getElementById('name_ar')?.value || 'PRD';
    const prefix = name.substring(0, 3).toUpperCase();
    const random = Math.floor(1000 + Math.random() * 9000);
    document.getElementById('sku').value = `${prefix}-${Date.now().toString().slice(-4)}${random}`;
}

let variantCounter = 0;
function addVariationRow() {
    const idx = variantCounter++;
    const container = document.getElementById('variantsContainer');
    document.getElementById('noVariantsMsg').classList.add('d-none');
    
    const div = document.createElement('div');
    div.className = 'variant-card';
    div.innerHTML = `
        <input type="hidden" name="variants[${idx}][id]" value="new_${idx}">
        <div class="variant-header">
            <div class="variant-img-wrapper" onclick="this.querySelector('input').click()">
                <i class="fas fa-camera text-muted"></i>
                <input type="file" name="variants[${idx}][color_image]" class="d-none" onchange="previewVariantImage(this)">
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-0" style="color: #475569;">{{ __('New Variation') }}</h6>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="this.closest('.variant-card').remove(); checkVariantsEmpty();">
                <i class="fas fa-trash me-1"></i> {{ __('Remove') }}
            </button>
        </div>

        <div class="variant-grid-container">
            <div class="variant-input-group">
                <label class="variant-label-xl">{{ __('Size / Name') }}</label>
                <input type="text" name="variants[${idx}][size]" class="variant-input-xl" placeholder="M, XL, 42...">
            </div>
            <div class="variant-input-group">
                <label class="variant-label-xl">{{ __('Grouping Key') }}</label>
                <input type="text" name="variants[${idx}][style_key]" class="variant-input-xl" placeholder="Set A">
            </div>
            <div class="variant-input-group">
                <label class="variant-label-xl">{{ __('Price') }} (DH)</label>
                <input type="number" name="variants[${idx}][price]" class="variant-input-xl" step="0.01">
            </div>
            <div class="variant-input-group">
                <label class="variant-label-xl text-danger">{{ __('Sale Price') }} (DH)</label>
                <input type="number" name="variants[${idx}][sale_price]" class="variant-input-xl border-danger-subtle text-danger" step="0.01">
            </div>
            <div class="variant-input-group">
                <label class="variant-label-xl">{{ __('Stock') }}</label>
                <input type="number" name="variants[${idx}][stock]" class="variant-input-xl" value="0">
            </div>
        </div>
        <input type="hidden" name="variants[${idx}][sku]" value="V-${Date.now()}-${idx}">
    `;
    container.appendChild(div);
}

function previewVariantImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrap = input.closest('.variant-img-wrapper');
            wrap.innerHTML = `<img src="${e.target.result}"><input type="file" name="${input.name}" class="d-none" onchange="previewVariantImage(this)">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function checkVariantsEmpty() {
    const container = document.getElementById('variantsContainer');
    if (container.children.length === 0) {
        document.getElementById('noVariantsMsg').classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if(!document.getElementById('sku').value) generateSKU();
});
</script>
@endpush
@endsection
