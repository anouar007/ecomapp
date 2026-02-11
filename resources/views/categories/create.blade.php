@extends('layouts.app')

@section('title','Create Category')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
.icon-picker {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    gap: 8px;
    max-height: 200px;
    overflow-y: auto;
    padding: 12px;
    background: #f8fafc;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
}

.icon-option {
    width: 60px;
    height: 60px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.icon-option:hover, .icon-option.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
    transform: scale(1.1);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.image-upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.image-upload-area:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
}

.image-preview-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> Create New Category</h1>
    <p class="page-subtitle">Add a new category to organize your products</p>
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
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Category Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">
                        Category Name <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           value="{{ old('name') }}" 
                           placeholder="e.g., Electronics" 
                           required 
                           autofocus>
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           class="form-control" 
                           value="{{ old('slug') }}" 
                           placeholder="auto-generated from name">
                    <small class="form-help">Leave empty to auto-generate</small>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          class="form-control" 
                          rows="3" 
                          placeholder="Category description...">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="parent_id" class="form-label">Parent Category</label>
                    <select id="parent_id" name="parent_id" class="form-control">
                        <option value="">-- No Parent (Top Level) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">Create a subcategory by selecting a parent</small>
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

                <div class="form-group">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           class="form-control" 
                           value="{{ old('sort_order', 0) }}" 
                           min="0">
                    <small class="form-help">Lower numbers appear first</small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Icon (FontAwesome)</label>
                <input type="text" 
                       id="icon" 
                       name="icon" 
                       class="form-control" 
                       value="{{ old('icon') }}" 
                       placeholder="e.g., fas fa-laptop">
                <small class="form-help">Enter FontAwesome class or select from popular icons:</small>
                <div class="icon-picker" style="margin-top: 12px;">
                    @foreach(['fas fa-laptop', 'fas fa-mobile', 'fas fa-tshirt', 'fas fa-book', 'fas fa-utensils', 'fas fa-home', 'fas fa-car', 'fas fa-gamepad', 'fas fa-music', 'fas fa-camera', 'fas fa-toolbox', 'fas fa-heart'] as $iconClass)
                        <div class="icon-option" onclick="selectIcon('{{ $iconClass }}')">
                            <i class="{{ $iconClass }}" style="font-size: 24px; color: #667eea;"></i>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Category Image</label>
                <div class="image-upload-area" id="imageUploadArea" onclick="document.getElementById('image').click()">
                    <input type="file" 
                           id="image" 
                           name="image" 
                           class="d-none" 
                           accept="image/*"
                           onchange="previewImage(this)">
                    <div id="imagePreview" class="image-preview-container" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 180px; border-radius: 8px;">
                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage(event)">
                            <i class="fas fa-times me-1"></i> Remove
                        </button>
                    </div>
                    <div id="uploadPlaceholder" class="text-center py-4">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: #667eea; opacity: 0.6;"></i>
                        <p class="mb-0 mt-2 text-muted">Click to upload or drag and drop</p>
                        <small class="text-muted">PNG, JPG up to 2MB</small>
                    </div>
                </div>
                <small class="form-help">Optional image to represent this category</small>
            </div>

            <div class="form-actions">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Category
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function selectIcon(iconClass) {
    document.getElementById('icon').value = iconClass;
    document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'flex';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(event) {
    event.stopPropagation();
    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    
    input.value = '';
    preview.style.display = 'none';
    placeholder.style.display = 'block';
}
</script>
@endpush
@endsection
