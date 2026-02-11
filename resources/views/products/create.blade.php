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
                <small class="form-help">You can upload multiple images. First image will be the primary image. Max 2MB each.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">
                        Product Name <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           value="{{ old('name') }}" 
                           placeholder="Enter product name" 
                           required 
                           autofocus>
                </div>

                <div class="form-group">
                    <label for="sku" class="form-label">
                        SKU <span class="required">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="text" 
                               id="sku" 
                               name="sku" 
                               class="form-control" 
                               value="{{ old('sku') }}" 
                               placeholder="Auto-generated or custom" 
                               required
                               style="padding-right: 110px;">
                        <button type="button" 
                                onclick="generateSKU()" 
                                class="btn btn-sm btn-outline-primary" 
                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); height: 34px; font-size: 13px;">
                            <i class="fas fa-magic"></i> Generate
                        </button>
                    </div>
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i> Auto-generates on page load or click "Generate" for new SKU
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          class="form-control" 
                          rows="4" 
                          placeholder="Product description...">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
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
    document.getElementById('images').files = dataTransfer.files;
}

// SKU Auto-Generation
function generateSKU() {
    const name = document.getElementById('name').value;
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
    document.getElementById('name').addEventListener('input', function() {
        clearTimeout(nameTimeout);
        nameTimeout = setTimeout(() => {
            if (!document.getElementById('sku').value && this.value.length > 3) {
                generateSKU();
            }
        }, 800); // Wait 800ms after user stops typing
    });
});
</script>
@endpush
@endsection
