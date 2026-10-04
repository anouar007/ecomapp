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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 10px;">
                    <label class="form-label" style="margin-bottom: 0;"><i class="fas fa-images"></i> Product Images</label>
                    <button type="button" class="btn btn-sm" onclick="open4kImageModal()" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 7px 16px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.3); transition: all 0.2s;">
                        <i class="fas fa-magic"></i> Find 4K Studio Images
                    </button>
                </div>
                <div class="multi-image-upload" id="imagePreviewContainer">
                    @foreach($product->images as $image)
                    <div class="image-upload-box" id="box-img-{{ $image->id }}">
                        <img src="{{ $image->url }}" alt="Product image" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        @if($image->is_primary)
                            <span class="primary-badge">PRIMARY</span>
                        @endif
                        <span class="image-remove-btn" onclick="markImageForRemoval({{ $image->id }}, this)">×</span>
                    </div>
                    @endforeach
                    @if($product->images->isEmpty() && ($product->image || $product->thumbnail))
                    <div class="image-upload-box" id="box-img-current">
                        <img src="{{ $product->thumbnail }}" alt="Product image" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        <span class="primary-badge">CURRENT</span>
                    </div>
                    @endif
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
                <small class="form-help">Upload new images or use <strong>Find 4K Studio Images</strong> to get ultra-high-definition official product photography.</small>
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

// 4K Studio Image Finder Logic
function open4kImageModal() {
    const modal = document.getElementById('modal4k');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Auto-search if empty
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
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';

    const url = "{{ route('products.search-4k-images', $product) }}?q=" + encodeURIComponent(query);

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
        btn.innerHTML = '<i class="fas fa-bolt"></i> Search 4K';

        if (!data.candidates || data.candidates.length === 0) {
            grid.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">
                    <i class="fas fa-image" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1;"></i>
                    <p style="font-weight: 600;">No 4K studio images found for "${query}"</p>
                    <p style="font-size: 13px;">Try simplifying the search name above or searching for the brand and model number.</p>
                </div>
            `;
            return;
        }

        data.candidates.forEach((cand, idx) => {
            const card = document.createElement('div');
            card.style.cssText = "background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);";
            card.onmouseenter = () => { card.style.borderColor = '#6366f1'; card.style.boxShadow = '0 8px 20px rgba(99, 102, 241, 0.15)'; };
            card.onmouseleave = () => { card.style.borderColor = '#e2e8f0'; card.style.boxShadow = '0 2px 8px rgba(0,0,0,0.04)'; };

            const is4k = cand.width >= 2000;
            const badgeColor = is4k ? "background: linear-gradient(135deg, #10b981, #059669); color: white;" : "background: #f1f5f9; color: #475569;";

            card.innerHTML = `
                <div style="position: relative; width: 100%; height: 200px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px;">
                    <img src="${cand.url}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.src='{{ asset('images/camera/cat_cameras.jpg') }}'">
                    <span style="position: absolute; top: 8px; left: 8px; ${badgeColor} font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        ${cand.width}×${cand.height} ${is4k ? '4K UHD' : 'HD'}
                    </span>
                    <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(15,23,42,0.7); color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px;">
                        ${cand.source}
                    </span>
                </div>
                <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px; background: #ffffff; border-top: 1px solid #f1f5f9;">
                    <button type="button" onclick="applySelected4kImage('${cand.url.replace(/'/g, "\\'")}', this)" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 8px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                        <i class="fas fa-check-circle"></i> Set as Product Image
                    </button>
                </div>
            `;
            grid.appendChild(card);
        });
    })
    .catch(err => {
        loading.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-bolt"></i> Search 4K';
        grid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; padding: 20px; text-align: center;">Error searching images: ${err.message}</div>`;
    });
}

function applySelected4kImage(imageUrl, button) {
    const origHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading 4K...';

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

    fetch("{{ route('products.apply-4k-image', $product) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ image_url: imageUrl })
    })
    .then(res => res.json())
    .then(data => {
        button.disabled = false;
        button.innerHTML = origHtml;

        if (data.success) {
            // Update preview box on page
            const container = document.getElementById('imagePreviewContainer');
            let currentBox = document.getElementById('box-img-current');
            
            if (!currentBox) {
                currentBox = document.createElement('div');
                currentBox.className = 'image-upload-box';
                currentBox.id = 'box-img-current';
                container.insertBefore(currentBox, container.firstElementChild);
            }
            
            currentBox.innerHTML = `
                <img src="${data.image_url}?v=${new Date().getTime()}" alt="Product image">
                <span class="primary-badge" style="background: #10b981;">4K MASTER</span>
            `;

            // Success feedback
            button.style.background = '#10b981';
            button.innerHTML = '<i class="fas fa-check"></i> Applied 4K!';
            
            setTimeout(() => {
                close4kImageModal();
            }, 800);
        } else {
            alert(data.message || 'Could not apply 4K image.');
        }
    })
    .catch(err => {
        button.disabled = false;
        button.innerHTML = origHtml;
        alert('Error: ' + err.message);
    });
}
</script>

<!-- 4K Image Finder Modal -->
<div id="modal4k" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 16px; max-width: 900px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); overflow: hidden;">
        <!-- Header -->
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(to right, #faf5ff, #ffffff);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);">
                    <i class="fas fa-magic"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">4K Ultra-HD Image Studio Finder</h3>
                    <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Find and apply authentic 4K master studio photography</p>
                </div>
            </div>
            <button type="button" onclick="close4kImageModal()" style="background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; padding: 4px 8px; border-radius: 6px;">&times;</button>
        </div>

        <!-- Search Bar -->
        <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; gap: 10px; align-items: center;">
            <div style="position: relative; flex: 1;">
                <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                <input type="text" id="query4k" value="{{ $product->name }}" placeholder="Search official 4K photography..." style="width: 100%; padding: 10px 14px 10px 38px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;" onkeydown="if(event.key==='Enter'){event.preventDefault(); trigger4kSearch();}">
            </div>
            <button type="button" id="btnRun4kSearch" onclick="trigger4kSearch()" style="background: #4f46e5; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-bolt"></i> Search 4K
            </button>
        </div>

        <!-- Content Area -->
        <div id="results4kContainer" style="padding: 24px; overflow-y: auto; flex: 1; min-height: 350px;">
            <div id="loading4k" style="display: none; text-align: center; padding: 60px 20px;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                <p style="margin-top: 16px; font-weight: 600; color: #475569;">Searching official 4K & studio master assets...</p>
                <p style="font-size: 12px; color: #94a3b8;">Scanning Amazon Master, B&H Master, Brand CDNs and Media Kits</p>
                <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
            </div>
            <div id="grid4k" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;"></div>
        </div>
    </div>
</div>
@endpush
@endsection
