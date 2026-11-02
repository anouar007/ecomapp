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
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <label for="description" class="form-label mb-0 fw-bold">Description</label>
                    <div class="d-flex align-items-center gap-2">
                        <select id="descStyleSelect" class="form-select form-select-sm rounded-pill w-auto py-1 shadow-xs" style="font-size: 11px; border-color: #cbd5e1;">
                            <option value="pro_ecommerce">✨ E-commerce Pro (Points forts &amp; Fiche)</option>
                            <option value="technical">📋 Technique Détaillé</option>
                            <option value="storytelling">🎬 Storytelling &amp; Créateurs</option>
                        </select>
                        <button type="button" 
                                onclick="generateBestDescriptionForEdit()" 
                                id="btnGenDescEdit" 
                                class="btn btn-sm rounded-pill px-3 fw-semibold text-white d-flex align-items-center gap-1 shadow-sm" 
                                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none; font-size: 12px; transition: all 0.2s;">
                            <i class="fas fa-sparkles text-warning"></i>
                            <span>Générer la meilleure description</span>
                        </button>
                    </div>
                </div>
                <textarea id="description" 
                          name="description" 
                          class="form-control" 
                          rows="8" 
                          placeholder="Description détaillée du produit...">{{ old('description', $product->description) }}</textarea>
                <small class="form-help text-muted">Formaté avec sections claires, spécifications techniques et points forts du matériel.</small>
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

// 4K Studio Image Finder Logic (Multi-Image & Gallery Support)
let currentCandidates = [];
let selectedCandidateUrls = new Set();
let activeCandidateFilter = 'all';
let galleryTotalCount = {{ $product->images->count() }};

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

function setSearchQueryAndRun(term) {
    document.getElementById('query4k').value = term;
    trigger4kSearch();
}

function trigger4kSearch() {
    const query = document.getElementById('query4k').value.trim();
    if (!query) return;

    const loading = document.getElementById('loading4k');
    const grid = document.getElementById('grid4k');
    const btn = document.getElementById('btnRun4kSearch');
    const toolbar = document.getElementById('selectionToolbar4k');

    loading.style.display = 'block';
    grid.innerHTML = '';
    toolbar.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching 4K...';
    selectedCandidateUrls.clear();
    updateSelectedCountUI();

    const url = "{{ route('products.search-4k-images', $product) }}?q=" + encodeURIComponent(query) + "&limit=50";

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
                <div style="grid-column: 1 / -1; text-align: center; padding: 48px 20px; color: #64748b;">
                    <i class="fas fa-image" style="font-size: 40px; margin-bottom: 12px; color: #cbd5e1;"></i>
                    <p style="font-weight: 700; font-size: 16px;">No 4K studio images found for "${query}"</p>
                    <p style="font-size: 13px;">Try simplifying the search name above or searching for the brand and model number.</p>
                </div>
            `;
            return;
        }

        currentCandidates = data.candidates;
        toolbar.style.display = 'flex';
        renderCandidateCards();
    })
    .catch(err => {
        loading.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-bolt"></i> Search 4K';
        grid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; padding: 24px; text-align: center; font-weight: 600;">Error searching images: ${err.message}</div>`;
    });
}

function filterCandidates(type, chipBtn) {
    activeCandidateFilter = type;
    document.querySelectorAll('.filter-chip-btn').forEach(b => {
        b.style.background = '#f1f5f9';
        b.style.color = '#475569';
    });
    chipBtn.style.background = '#4f46e5';
    chipBtn.style.color = '#ffffff';
    renderCandidateCards();
}

function renderCandidateCards() {
    const grid = document.getElementById('grid4k');
    grid.innerHTML = '';

    const countAll = currentCandidates.length;
    const count4k = currentCandidates.filter(c => c.is_4k || (c.width >= 2000 || c.height >= 2000)).length;
    const countFhd = currentCandidates.filter(c => c.is_fhd || (c.width >= 1200 || c.height >= 1200)).length;
    const countOfficial = currentCandidates.filter(c => c.is_official).length;

    document.getElementById('countAllChip').innerText = countAll;
    document.getElementById('count4kChip').innerText = count4k;
    document.getElementById('countFhdChip').innerText = countFhd;
    document.getElementById('countOfficialChip').innerText = countOfficial;
    document.getElementById('totalResultsBadge').innerText = `${countAll} images found`;

    const filtered = currentCandidates.filter(c => {
        if (activeCandidateFilter === '4k') return c.is_4k || (c.width >= 2000 || c.height >= 2000);
        if (activeCandidateFilter === 'fhd') return c.is_fhd || (c.width >= 1200 || c.height >= 1200);
        if (activeCandidateFilter === 'official') return c.is_official;
        return true;
    });

    if (filtered.length === 0) {
        grid.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; padding: 30px; color: #94a3b8;">No images match this filter.</div>`;
        return;
    }

    filtered.forEach((cand, idx) => {
        const isSelected = selectedCandidateUrls.has(cand.url);
        const is4k = (cand.width >= 2000 || cand.height >= 2000);
        const badgeColor = is4k ? "background: linear-gradient(135deg, #10b981, #059669); color: white;" : "background: #e0e7ff; color: #4338ca;";

        const card = document.createElement('div');
        card.id = `cand-card-${idx}`;
        card.style.cssText = `
            background: #ffffff; 
            border: 2px solid ${isSelected ? '#4f46e5' : '#e2e8f0'}; 
            border-radius: 12px; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            transition: all 0.2s; 
            box-shadow: ${isSelected ? '0 8px 24px rgba(79, 70, 229, 0.2)' : '0 2px 8px rgba(0,0,0,0.04)'};
            position: relative;
        `;

        card.innerHTML = `
            <!-- Selection Checkbox -->
            <div style="position: absolute; top: 8px; right: 8px; z-index: 5;">
                <input type="checkbox" 
                       id="check-cand-${idx}" 
                       ${isSelected ? 'checked' : ''} 
                       onchange="toggleCandidateSelection('${cand.url.replace(/'/g, "\\'")}', this.checked, ${idx})"
                       style="width: 20px; height: 20px; cursor: pointer; accent-color: #4f46e5;">
            </div>

            <div style="position: relative; width: 100%; height: 200px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px;">
                <img src="${cand.thumbnail || cand.url}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain; cursor: pointer;" onclick="toggleCandidateSelection('${cand.url.replace(/'/g, "\\'")}', !document.getElementById('check-cand-${idx}').checked, ${idx})" onerror="this.src='{{ asset('images/camera/cat_cameras.jpg') }}'">
                
                <span style="position: absolute; top: 8px; left: 8px; ${badgeColor} font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    ${cand.width}×${cand.height} ${is4k ? '4K UHD' : 'HD'}
                </span>
                
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(15,23,42,0.75); color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px; max-width: 75%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    ${cand.source}
                </span>

                <a href="${cand.url}" target="_blank" title="Open full resolution in new tab" style="position: absolute; bottom: 8px; right: 8px; background: rgba(255,255,255,0.9); color: #334155; width: 24px; height: 24px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 11px; text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>

            <div style="padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; background: #ffffff; border-top: 1px solid #f1f5f9; flex: 1; justify-content: space-between;">
                <div style="font-size: 11.5px; color: #475569; font-weight: 500; line-height: 1.3; max-height: 28px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    ${cand.title || 'Studio Packshot'}
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 4px;">
                    <button type="button" 
                            onclick="applySingle4k('${cand.url.replace(/'/g, "\\'")}', this, false)" 
                            class="btn-add-gallery"
                            style="background: #10b981; color: white; border: none; border-radius: 6px; padding: 7px 6px; font-size: 11px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: all 0.15s;">
                        <i class="fas fa-plus"></i> + Gallery
                    </button>
                    <button type="button" 
                            onclick="applySingle4k('${cand.url.replace(/'/g, "\\'")}', this, true)" 
                            class="btn-set-main"
                            style="background: #4f46e5; color: white; border: none; border-radius: 6px; padding: 7px 6px; font-size: 11px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: all 0.15s;">
                        <i class="fas fa-star"></i> Set Main
                    </button>
                </div>
            </div>
        `;

        grid.appendChild(card);
    });
}

function toggleCandidateSelection(url, checked, cardIdx) {
    if (checked) {
        selectedCandidateUrls.add(url);
    } else {
        selectedCandidateUrls.delete(url);
    }
    
    const card = document.getElementById(`cand-card-${cardIdx}`);
    const check = document.getElementById(`check-cand-${cardIdx}`);
    if (check) check.checked = checked;
    if (card) {
        card.style.borderColor = checked ? '#4f46e5' : '#e2e8f0';
        card.style.boxShadow = checked ? '0 8px 24px rgba(79, 70, 229, 0.2)' : '0 2px 8px rgba(0,0,0,0.04)';
    }

    updateSelectedCountUI();
}

function toggleSelectAllCandidates(checked) {
    const filtered = currentCandidates.filter(c => {
        if (activeCandidateFilter === '4k') return c.is_4k || (c.width >= 2000 || c.height >= 2000);
        if (activeCandidateFilter === 'fhd') return c.is_fhd || (c.width >= 1200 || c.height >= 1200);
        if (activeCandidateFilter === 'official') return c.is_official;
        return true;
    });

    filtered.forEach(c => {
        if (checked) {
            selectedCandidateUrls.add(c.url);
        } else {
            selectedCandidateUrls.delete(c.url);
        }
    });

    renderCandidateCards();
    updateSelectedCountUI();
}

function updateSelectedCountUI() {
    const count = selectedCandidateUrls.size;
    const btnBulk = document.getElementById('btnBulkImport4k');
    const badgeCount = document.getElementById('bulkSelectedCount');
    const selectAllCheck = document.getElementById('selectAllCandCheck');

    if (badgeCount) badgeCount.innerText = count;
    if (btnBulk) {
        btnBulk.disabled = (count === 0);
        btnBulk.style.opacity = (count === 0) ? '0.5' : '1';
        btnBulk.style.cursor = (count === 0) ? 'not-allowed' : 'pointer';
    }
    if (selectAllCheck && currentCandidates.length > 0) {
        selectAllCheck.checked = (count > 0 && count === currentCandidates.length);
    }
}

// Single Image Apply (as Primary or as Gallery)
function applySingle4k(imageUrl, button, asPrimary) {
    const origHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

    fetch("{{ route('products.apply-4k-image', $product) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            image_url: imageUrl,
            as_primary: asPrimary
        })
    })
    .then(res => res.json())
    .then(data => {
        button.disabled = false;
        button.innerHTML = origHtml;

        if (data.success) {
            // Success styling on button
            button.style.background = '#059669';
            button.innerHTML = asPrimary ? '<i class="fas fa-check"></i> Primary Set!' : '<i class="fas fa-check"></i> In Gallery!';
            button.disabled = true;

            // Append or update in the main form preview
            appendOrUpdatePreviewImage(data);

            // Toast feedback
            show4kToast(asPrimary ? '⭐ 4K Main Image Updated!' : '➕ 4K Image Added to Product Gallery!');
        } else {
            alert(data.message || 'Could not download 4K image.');
        }
    })
    .catch(err => {
        button.disabled = false;
        button.innerHTML = origHtml;
        alert('Erreur: ' + err.message);
    });
}

// Bulk Import Multiple Selected Images
function importSelected4kImages() {
    const urls = Array.from(selectedCandidateUrls);
    if (urls.length === 0) return;

    const btnBulk = document.getElementById('btnBulkImport4k');
    const origHtml = btnBulk.innerHTML;
    btnBulk.disabled = true;
    btnBulk.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Downloading ${urls.length} images in 4K...`;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

    fetch("{{ route('products.apply-4k-image', $product) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            image_urls: urls,
            as_primary: false // Add as gallery images
        })
    })
    .then(res => res.json())
    .then(data => {
        btnBulk.disabled = false;
        btnBulk.innerHTML = origHtml;

        if (data.success && data.images) {
            data.images.forEach(imgData => {
                appendOrUpdatePreviewImage(imgData);
            });

            show4kToast(`🎉 ${data.count} 4K images imported successfully into product gallery!`);
            selectedCandidateUrls.clear();
            renderCandidateCards();
            updateSelectedCountUI();
        } else {
            alert(data.message || 'Could not import selected images.');
        }
    })
    .catch(err => {
        btnBulk.disabled = false;
        btnBulk.innerHTML = origHtml;
        alert('Erreur: ' + err.message);
    });
}

function appendOrUpdatePreviewImage(imgData) {
    const container = document.getElementById('imagePreviewContainer');
    if (!container) return;

    if (imgData.is_primary) {
        // Demote existing primary badges
        container.querySelectorAll('.primary-badge').forEach(b => {
            b.textContent = 'GALLERY';
            b.style.background = '#64748b';
        });
        const curBox = document.getElementById('box-img-current');
        if (curBox) curBox.remove();
    }

    let existingBox = document.getElementById('box-img-' + imgData.image_id);
    if (existingBox) {
        existingBox.querySelector('img').src = imgData.image_url + '?t=' + new Date().getTime();
        return;
    }

    const box = document.createElement('div');
    box.className = 'image-upload-box';
    box.id = 'box-img-' + imgData.image_id;
    box.style.border = '2px solid #10b981';
    box.innerHTML = `
        <img src="${imgData.image_url}" alt="Product image">
        <span class="primary-badge" style="background: ${imgData.is_primary ? '#10b981' : '#6366f1'};">${imgData.is_primary ? 'PRIMARY 4K' : '4K STUDIO'}</span>
        <span class="image-remove-btn" onclick="markImageForRemoval(${imgData.image_id}, this)">×</span>
    `;

    // Insert before the last box (the "Add more" upload button)
    container.insertBefore(box, container.lastElementChild);
    
    galleryTotalCount++;
    const totalStatus = document.getElementById('modalGalleryTotalStatus');
    if (totalStatus) totalStatus.innerText = `${galleryTotalCount} images in gallery`;
}

function show4kToast(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    } else {
        const toast = document.createElement('div');
        toast.style.cssText = "position: fixed; bottom: 24px; right: 24px; background: #0f172a; color: white; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 13px; z-index: 100000; box-shadow: 0 10px 25px rgba(0,0,0,0.3); animation: fadeIn 0.3s;";
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }
}

function generateBestDescriptionForEdit() {
    const btn = document.getElementById('btnGenDescEdit');
    const textarea = document.getElementById('description');
    const style = document.getElementById('descStyleSelect')?.value || 'pro_ecommerce';
    const origHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-warning"></i> Génération en cours...';

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

    fetch("{{ route('products.generate-description', $product) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ style: style })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (data.success && data.description) {
            textarea.value = data.description;
            textarea.style.transition = 'all 0.3s';
            textarea.style.borderColor = '#10b981';
            textarea.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.2)';
            setTimeout(() => {
                textarea.style.borderColor = '';
                textarea.style.boxShadow = '';
            }, 2000);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Meilleure description générée !',
                    text: `${data.length} caractères insérés avec succès.`,
                    showConfirmButton: false,
                    timer: 2500
                });
            } else {
                alert('Meilleure description générée et insérée avec succès !');
            }
        } else {
            alert(data.message || 'Impossible de générer la description.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = origHtml;
        alert('Erreur: ' + err.message);
    });
}
</script>

<!-- 4K Image Finder Modal (Multi-Image & Gallery Support) -->
<div id="modal4k" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 18px; max-width: 1050px; width: 100%; max-height: 92vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45); overflow: hidden; border: 1px solid rgba(255,255,255,0.2);">
        <!-- Header -->
        <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(to right, #f8fafc, #ffffff);">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);">
                    <i class="fas fa-magic"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span>4K Ultra-HD Studio Image Finder</span>
                        <span style="font-size: 11px; font-weight: 600; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 9999px;">Multi-Image Enabled</span>
                    </h3>
                    <p style="margin: 2px 0 0; font-size: 12.5px; color: #64748b;">Find, choose, and import as many 4K studio & packshot images as you need into this product.</p>
                </div>
            </div>
            <button type="button" onclick="close4kImageModal()" style="background: #f1f5f9; border: none; font-size: 20px; color: #64748b; cursor: pointer; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseenter="this.style.background='#e2e8f0'; this.style.color='#0f172a'" onmouseleave="this.style.background='#f1f5f9'; this.style.color='#64748b'">&times;</button>
        </div>

        <!-- Search Bar & Quick Tags -->
        <div style="padding: 14px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="position: relative; flex: 1;">
                    <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" 
                           id="query4k" 
                           value="{{ $product->name }}" 
                           placeholder="Search official 4K photography, brand, model..." 
                           style="width: 100%; padding: 10px 14px 10px 38px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; background: white;" 
                           onkeydown="if(event.key==='Enter'){event.preventDefault(); trigger4kSearch();}">
                </div>
                <button type="button" 
                        id="btnRun4kSearch" 
                        onclick="trigger4kSearch()" 
                        style="background: linear-gradient(135deg, #4f46e5, #6366f1); color: white; border: none; border-radius: 10px; padding: 10px 22px; font-weight: 600; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-bolt"></i> Search 4K
                </button>
            </div>

            <!-- Quick Angle / Search Presets -->
            <div style="display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; align-items: center;">
                <span style="font-size: 11px; font-weight: 600; color: #64748b; margin-right: 4px;">Quick Queries:</span>
                <button type="button" 
                        onclick="setSearchQueryAndRun('{{ addslashes($product->name) }} studio packshot white background')"
                        style="background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 3px 8px; font-size: 11px; color: #475569; cursor: pointer;">
                    📸 Packshot White BG
                </button>
                <button type="button" 
                        onclick="setSearchQueryAndRun('{{ addslashes($product->name) }} camera angle side back front')"
                        style="background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 3px 8px; font-size: 11px; color: #475569; cursor: pointer;">
                    🔄 Multi-Angles & Sides
                </button>
                <button type="button" 
                        onclick="setSearchQueryAndRun('{{ addslashes($product->name) }} details close up')"
                        style="background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 3px 8px; font-size: 11px; color: #475569; cursor: pointer;">
                    🔍 Close-Up & Details
                </button>
                @if($product->category_name)
                <button type="button" 
                        onclick="setSearchQueryAndRun('{{ addslashes($product->name) }} {{ addslashes($product->category_name) }}')"
                        style="background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 3px 8px; font-size: 11px; color: #475569; cursor: pointer;">
                    📁 {{ $product->category_name }}
                </button>
                @endif
            </div>
        </div>

        <!-- Selection & Bulk Actions Toolbar (Visible when results arrive) -->
        <div id="selectionToolbar4k" style="display: none; padding: 12px 24px; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" id="selectAllCandCheck" onchange="toggleSelectAllCandidates(this.checked)" style="width: 17px; height: 17px; accent-color: #4f46e5; cursor: pointer;">
                    <span>Select All</span>
                </label>
                <span id="totalResultsBadge" style="font-size: 12px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 3px 10px; border-radius: 20px;">0 images found</span>
                <span style="font-size: 12px; color: #64748b;">•</span>
                <span style="font-size: 12px; font-weight: 700; color: #4f46e5;">
                    <span id="bulkSelectedCount">0</span> selected
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <!-- Filter Chips -->
                <div style="display: flex; gap: 4px; background: #f8fafc; padding: 3px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <button type="button" class="filter-chip-btn" onclick="filterCandidates('all', this)" style="background: #4f46e5; color: white; border: none; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
                        All (<span id="countAllChip">0</span>)
                    </button>
                    <button type="button" class="filter-chip-btn" onclick="filterCandidates('4k', this)" style="background: #f1f5f9; color: #475569; border: none; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
                        4K UHD (<span id="count4kChip">0</span>)
                    </button>
                    <button type="button" class="filter-chip-btn" onclick="filterCandidates('fhd', this)" style="background: #f1f5f9; color: #475569; border: none; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
                        FHD (<span id="countFhdChip">0</span>)
                    </button>
                    <button type="button" class="filter-chip-btn" onclick="filterCandidates('official', this)" style="background: #f1f5f9; color: #475569; border: none; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">
                        Official (<span id="countOfficialChip">0</span>)
                    </button>
                </div>

                <!-- Bulk Import Button -->
                <button type="button" 
                        id="btnBulkImport4k" 
                        onclick="importSelected4kImages()" 
                        disabled
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; padding: 7px 16px; font-size: 12.5px; font-weight: 700; cursor: not-allowed; opacity: 0.5; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-layer-group"></i> Add Selected to Gallery
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div id="results4kContainer" style="padding: 20px 24px; overflow-y: auto; flex: 1; min-height: 380px; background: #fbfcfe;">
            <div id="loading4k" style="display: none; text-align: center; padding: 70px 20px;">
                <div style="display: inline-block; width: 44px; height: 44px; border: 4px solid #e2e8f0; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                <p style="margin-top: 18px; font-weight: 700; font-size: 15px; color: #1e293b;">Searching 4K & studio master assets across web & manufacturer databases...</p>
                <p style="font-size: 12.5px; color: #64748b;">Scanning Amazon Master, B&H Studio, Official SQLite Catalog, Brand CDNs and Packshots</p>
                <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
            </div>
            <div id="grid4k" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(235px, 1fr)); gap: 16px;"></div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 14px 24px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: #475569;">
                <i class="fas fa-images" style="color: #6366f1;"></i>
                <span id="modalGalleryTotalStatus" style="font-weight: 600;">{{ $product->images->count() }} images in product gallery</span>
                <span style="font-size: 11.5px; color: #94a3b8;">(Click "+ Gallery" or select multiple to keep adding)</span>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button type="button" onclick="close4kImageModal()" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border: none; border-radius: 8px; padding: 9px 20px; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-check"></i> Done / Return to Product
                </button>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection
