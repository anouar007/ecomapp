@extends('layouts.app')

@section('title', __('Edit Category'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
    .icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(45px, 1fr));
        gap: 8px;
        padding: 10px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .icon-option {
        width: 45px;
        height: 45px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: white;
        transition: all 0.2s;
    }
    .icon-option:hover, .icon-option.selected {
        border-color: var(--primary-color);
        background: #f0f4ff;
        transform: translateY(-2px);
    }
    .image-preview-wrapper {
        position: relative;
        width: 100%;
        padding-top: 60%;
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
    }
    .image-preview-wrapper img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="brand-header">
    <div>
        <h1 class="brand-title">
            <div class="brand-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            {{ __('Edit Category') }}: {{ $category->translated_name }}
        </h1>
        <p class="brand-subtitle">{{ __('Update category details, images, and settings') }}</p>
    </div>
    <div class="d-flex gap-2 d-none d-lg-flex">
        <a href="{{ route('categories.index') }}" class="btn-brand-light">
            {{ __('Cancel') }}
        </a>
        <button type="submit" form="editCategoryForm" class="btn-brand-primary">
            <i class="fas fa-save me-2"></i> {{ __('Update Category') }}
        </button>
    </div>
</div>

<form id="editCategoryForm" action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row g-4">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <div class="brand-card h-100">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">
                        <i class="fas fa-info-circle me-2" style="color: var(--primary-color)"></i>
                        {{ __('Category Details') }}
                    </h5>
                </div>
                <div class="brand-card-body">
                    <!-- Language Tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="langTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar-content" type="button" role="tab">
                                🇲🇦 {{ __('Arabic') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-content" type="button" role="tab">
                                🇬🇧 {{ __('English') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr-content" type="button" role="tab">
                                🇫🇷 {{ __('French') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="langTabsContent">
                        <!-- Arabic Content -->
                        <div class="tab-pane fade show active" id="ar-content" role="tabpanel">
                            <div class="mb-4">
                                <label class="brand-label">{{ __('Category Name (Arabic)') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" id="name_ar" class="brand-input @error('name_ar') is-invalid @enderror" 
                                       value="{{ old('name_ar', $category->name_ar) }}" dir="rtl" placeholder="{{ __('اسم الفئة...') }}" required>
                                @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-4">
                                <label class="brand-label">{{ __('Slug') }}</label>
                                <input type="text" name="slug" class="brand-input" value="{{ old('slug', $category->slug) }}" placeholder="{{ __('category-slug') }}">
                            </div>
                            <div class="mb-0">
                                <label class="brand-label">{{ __('Description (Arabic)') }}</label>
                                <textarea name="description_ar" class="brand-input @error('description_ar') is-invalid @enderror" 
                                          rows="4" dir="rtl" placeholder="{{ __('وصف الفئة...') }}">{{ old('description_ar', $category->description_ar) }}</textarea>
                                @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- English Content -->
                        <div class="tab-pane fade" id="en-content" role="tabpanel">
                            <div class="mb-4">
                                <label class="brand-label">{{ __('Category Name (English)') }}</label>
                                <input type="text" name="name_en" class="brand-input" value="{{ old('name_en', $category->name_en) }}" placeholder="{{ __('Category Name in English...') }}">
                            </div>
                            <div class="mb-0">
                                <label class="brand-label">{{ __('Description (English)') }}</label>
                                <textarea name="description_en" class="brand-input" rows="4" placeholder="{{ __('Category Description in English...') }}">{{ old('description_en', $category->description_en) }}</textarea>
                            </div>
                        </div>

                        <!-- French Content -->
                        <div class="tab-pane fade" id="fr-content" role="tabpanel">
                            <div class="mb-4">
                                <label class="brand-label">{{ __('Category Name (French)') }}</label>
                                <input type="text" name="name_fr" class="brand-input" value="{{ old('name_fr', $category->name_fr) }}" placeholder="{{ __('Category Name in French...') }}">
                            </div>
                            <div class="mb-0">
                                <label class="brand-label">{{ __('Description (French)') }}</label>
                                <textarea name="description_fr" class="brand-input" rows="4" placeholder="{{ __('Category Description in French...') }}">{{ old('description_fr', $category->description_fr) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Status & Parent -->
                <div class="brand-card">
                    <div class="brand-card-header">
                        <h5 class="brand-card-title">
                            <i class="fas fa-cog me-2 text-primary"></i>
                            {{ __('Settings') }}
                        </h5>
                    </div>
                    <div class="brand-card-body">
                        <div class="mb-4">
                            <label class="brand-label">{{ __('Status') }}</label>
                            <select name="status" class="brand-input">
                                <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="brand-label">{{ __('Parent Category') }}</label>
                            <select name="parent_id" class="brand-input">
                                <option value="">-- {{ __('No Parent (Top Level)') }} --</option>
                                @foreach($parentCategories as $parent)
                                    @if($parent->id !== $category->id)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->translated_name }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="brand-label">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" class="brand-input" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                        </div>
                    </div>
                </div>

                <!-- Appearance (Icon & Image) -->
                <div class="brand-card">
                    <div class="brand-card-header">
                        <h5 class="brand-card-title">
                            <i class="fas fa-palette me-2 text-primary"></i>
                            {{ __('Appearance') }}
                        </h5>
                    </div>
                    <div class="brand-card-body">
                        <div class="mb-4">
                            <label class="brand-label">{{ __('Category Icon') }}</label>
                            <input type="text" name="icon" id="icon_input" class="brand-input mb-3" 
                                   value="{{ old('icon', $category->icon) }}" placeholder="e.g. fas fa-leaf">
                            <div class="icon-picker-grid">
                                @foreach(['fas fa-leaf', 'fas fa-seedling', 'fas fa-flask', 'fas fa-box', 'fas fa-tag', 'fas fa-star', 'fas fa-home', 'fas fa-shopping-basket', 'fas fa-spray-can', 'fas fa-wine-bottle'] as $iconClass)
                                    <div class="icon-option {{ old('icon', $category->icon) == $iconClass ? 'selected' : '' }}" onclick="selectIcon('{{ $iconClass }}')">
                                        <i class="{{ $iconClass }}"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="brand-label">{{ __('Category Image') }}</label>
                            <div class="image-preview-wrapper" onclick="document.getElementById('category_image').click()">
                                <input type="file" name="image" id="category_image" class="d-none" accept="image/*" onchange="previewImage(this)">
                                @if($category->image)
                                    <img id="preview_img" src="{{ Storage::url($category->image) }}" alt="">
                                    <div id="upload_placeholder" class="image-placeholder" style="display: none;">
                                        <i class="fas fa-cloud-upload-alt fs-2 mb-2"></i>
                                        <span class="small">{{ __('Change Image') }}</span>
                                    </div>
                                @else
                                    <img id="preview_img" src="" alt="" style="display: none;">
                                    <div id="upload_placeholder" class="image-placeholder">
                                        <i class="fas fa-cloud-upload-alt fs-2 mb-2"></i>
                                        <span class="small">{{ __('Click to upload') }}</span>
                                    </div>
                                @endif
                            </div>
                            @if($category->image)
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" name="remove_image" id="remove_image" value="1">
                                <label class="form-check-label text-danger small" for="remove_image">
                                    <i class="fas fa-trash me-1"></i> {{ __('Remove current image') }}
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Mobile Actions -->
    <div class="sticky-mobile-actions d-lg-none">
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i> {{ __('Update') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    function selectIcon(iconClass) {
        document.getElementById('icon_input').value = iconClass;
        document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('selected'));
        event.currentTarget.classList.add('selected');
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview_img').src = e.target.result;
                document.getElementById('preview_img').style.display = 'block';
                document.getElementById('upload_placeholder').style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
