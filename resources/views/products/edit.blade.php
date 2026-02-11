@extends('layouts.app')

@section('title', 'Edit Product')

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
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-edit"></i> Edit Product: {{ $product->name }}</h1>
    <p class="page-subtitle">Update product information</p>
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
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label"><i class="fas fa-images"></i> Product Images</label>
                <div class="multi-image-upload" id="imagePreviewContainer">
                    @foreach($product->images as $image)
                    <div class="image-upload-box">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image">
                        @if($image->is_primary)
                            <span class="primary-badge">PRIMARY</span>
                        @endif
                        <span class="image-remove-btn" onclick="markImageForRemoval({{ $image->id }}, this)">×</span>
                    </div>
                    @endforeach
                    <div class="image-upload-box" onclick="document.getElementById('images').click()">
                        <div>
                            <i class="fas fa-plus" style="font-size: 24px; color: #94a3b8;"></i>
                            <p style="margin-top: 6px; color: #64748b; font-size: 11px; text-align: center;">Add more</p>
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
                <small class="form-help">Upload new images or remove existing ones. First image is the primary image.</small>
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
                           value="{{ old('name', $product->name) }}" 
                           placeholder="Enter product name" 
                           required>
                </div>

                <div class="form-group">
                    <label for="sku" class="form-label">
                        SKU <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="sku" 
                           name="sku" 
                           class="form-control" 
                           value="{{ old('sku', $product->sku) }}" 
                           placeholder="e.g., PROD-001" 
                           required>
                    <small class="form-help">Unique product identifier</small>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          class="form-control" 
                          rows="4" 
                          placeholder="Product description...">{{ old('description', $product->description) }}</textarea>
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
                           value="{{ old('cost_price', $product->cost_price) }}" 
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
                           value="{{ old('price', $product->price) }}" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0" 
                           required>
                    <small class="form-help">Price you sell to customers</small>
                </div>
            </div>

            @if($product->cost_price)
            <div class="alert alert-info" style="margin-bottom: 24px;">
                <i class="fas fa-chart-line"></i>
                <strong>Profit Margin:</strong> {{ number_format($product->profit_margin, 2) }}% 
                (Profit: ${{ number_format($product->price - $product->cost_price, 2) }} per unit)
            </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label for="stock" class="form-label">
                        <i class="fas fa-boxes"></i> Current Stock <span class="required">*</span>
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
                        <small class="form-help" style="color: #d97706;">⚠️ Stock is below minimum level!</small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="min_stock" class="form-label">
                        <i class="fas fa-exclamation-triangle"></i> Minimum Stock Level <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="min_stock" 
                           name="min_stock" 
                           class="form-control" 
                           value="{{ old('min_stock', $product->min_stock) }}" 
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
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
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
    
    files.forEach((file, index) => {
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
</script>
@endpush
@endsection
