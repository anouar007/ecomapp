@extends('layouts.app')

@section('title', 'Créer une catégorie')

@section('content')
<div class="categories-container">
    <!-- En-tête de page -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <h1 class="brand-title">Nouvelle catégorie</h1>
                <p class="brand-subtitle">Créez une catégorie principale ou sous-catégorie pour organiser votre catalogue</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('categories.index') }}" class="btn-brand-outline">
                <i class="fas fa-arrow-left me-1"></i> <span>Retour aux catégories</span>
            </a>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-start gap-3 p-3">
            <i class="fas fa-exclamation-circle text-danger fs-5 mt-1"></i>
            <div>
                <strong class="d-block mb-1">Veuillez corriger les erreurs suivantes :</strong>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Carte du formulaire -->
    <div class="cat-form-card">
        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                <i class="fas fa-layer-group text-danger"></i>
                <span class="fw-bold text-dark fs-6">Informations principales</span>
            </div>

            <div class="row g-3 g-md-4">
                <!-- Nom -->
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-bold small text-dark mb-1">
                        Nom de la catégorie <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           value="{{ old('name') }}" 
                           placeholder="ex: Caméras Cinéma, Objectifs..." 
                           required 
                           autofocus
                           oninput="autoGenerateSlug(this.value)">
                </div>

                <!-- Slug -->
                <div class="col-12 col-md-6">
                    <label for="slug" class="form-label fw-bold small text-dark mb-1">
                        Identifiant d'URL (Slug)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0 font-monospace small">/</span>
                        <input type="text" 
                               id="slug" 
                               name="slug" 
                               class="form-control border-start-0 font-monospace" 
                               value="{{ old('slug') }}" 
                               placeholder="cameras-cinema">
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Généré automatiquement si laissé vide.</small>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label fw-bold small text-dark mb-1">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Brève description de la catégorie pour vos clients et le référencement...">{{ old('description') }}</textarea>
                </div>

                <!-- Catégorie parente -->
                <div class="col-12 col-md-4">
                    <label for="parent_id" class="form-label fw-bold small text-dark mb-1">Catégorie parente</label>
                    <select id="parent_id" name="parent_id" class="form-select">
                        <option value="">-- Catégorie principale (Racine) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" style="font-size: 0.72rem;">Sélectionnez un parent pour en faire une sous-catégorie.</small>
                </div>

                <!-- Statut -->
                <div class="col-12 col-md-4">
                    <label for="status" class="form-label fw-bold small text-dark mb-1">
                        Statut de publication <span class="text-danger">*</span>
                    </label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Actif (Visible sur la boutique)</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactif (Masqué)</option>
                    </select>
                </div>

                <!-- Ordre de tri -->
                <div class="col-12 col-md-4">
                    <label for="sort_order" class="form-label fw-bold small text-dark mb-1">Ordre d'affichage</label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           class="form-control" 
                           value="{{ old('sort_order', 0) }}" 
                           min="0">
                    <small class="text-muted" style="font-size: 0.72rem;">Les nombres les plus bas s'affichent en premier.</small>
                </div>

                <!-- Icône FontAwesome -->
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label fw-bold small text-dark mb-0">Icône (FontAwesome)</label>
                        <span id="iconBadgePreview" class="badge bg-light text-dark border">
                            <i id="previewIcon" class="{{ old('icon', 'fas fa-folder') }} me-1 text-danger"></i> Aperçu
                        </span>
                    </div>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-light"><i class="fas fa-icons text-muted"></i></span>
                        <input type="text" 
                               id="icon" 
                               name="icon" 
                               class="form-control" 
                               value="{{ old('icon') }}" 
                               placeholder="ex: fas fa-camera"
                               oninput="updateIconPreview(this.value)">
                    </div>
                    <small class="text-muted d-block mb-2" style="font-size: 0.72rem;">Cliquez sur une icône populaire ci-dessous ou saisissez une classe FontAwesome :</small>
                    
                    <div class="icon-picker-grid">
                        @php
                            $suggestedIcons = [
                                'fas fa-camera', 'fas fa-video', 'fas fa-circle-notch', 'fas fa-lightbulb',
                                'fas fa-microphone', 'fas fa-podcast', 'fas fa-hand-holding', 'fas fa-briefcase',
                                'fas fa-grip-lines-vertical', 'fas fa-battery-three-quarters', 'fas fa-sd-card', 'fas fa-tools',
                                'fas fa-box', 'fas fa-laptop', 'fas fa-mobile-alt', 'fas fa-sliders-h',
                                'fas fa-spray-can', 'fas fa-globe', 'fas fa-tv', 'fas fa-headphones',
                                'fas fa-tag', 'fas fa-star', 'fas fa-gem', 'fas fa-shield-alt'
                            ];
                        @endphp
                        @foreach($suggestedIcons as $iconClass)
                            <div class="icon-choice-btn {{ old('icon') == $iconClass ? 'selected' : '' }}" 
                                 onclick="selectIcon('{{ $iconClass }}')" 
                                 title="{{ $iconClass }}">
                                <i class="{{ $iconClass }}"></i>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Image de catégorie -->
                <div class="col-12">
                    <label class="form-label fw-bold small text-dark mb-1">Image de présentation</label>
                    <div class="cat-upload-dropzone" id="imageUploadArea" onclick="document.getElementById('image').click()">
                        <input type="file" 
                               id="image" 
                               name="image" 
                               class="d-none" 
                               accept="image/*"
                               onchange="previewImage(this)">
                        
                        <div id="imagePreview" style="display: none;">
                            <div class="d-flex flex-column align-items-center">
                                <img id="previewImg" src="" alt="Aperçu" style="display: none; max-width: 220px; max-height: 160px; object-fit: cover; border-radius: 12px; border: 2px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-3" onclick="removeImage(event)">
                                    <i class="fas fa-times me-1"></i> Supprimer l'image
                                </button>
                            </div>
                        </div>

                        <div id="uploadPlaceholder">
                            <div class="brand-avatar mx-auto mb-2" style="width: 50px; height: 50px; background: rgba(220, 38, 38, 0.08); color: #dc2626;">
                                <i class="fas fa-cloud-arrow-up fs-5"></i>
                            </div>
                            <p class="mb-1 text-dark fw-bold small">Cliquez pour choisir ou glissez-déposez une image</p>
                            <small class="text-muted" style="font-size: 0.72rem;">Formats acceptés : PNG, JPG, WEBP (Max : 2 Mo)</small>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="col-12 mt-4 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <a href="{{ route('categories.index') }}" class="btn-brand-light">
                        <i class="fas fa-times me-1"></i> Annuler
                    </a>
                    <button type="submit" class="btn-brand-primary">
                        <i class="fas fa-check me-1"></i> Enregistrer la catégorie
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function autoGenerateSlug(text) {
    const slugInput = document.getElementById('slug');
    if (!slugInput.dataset.manual) {
        slugInput.value = text
            .toString()
            .toLowerCase()
            .trim()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }
}

document.getElementById('slug').addEventListener('input', function() {
    this.dataset.manual = this.value.length > 0 ? 'true' : '';
});

function selectIcon(iconClass) {
    document.getElementById('icon').value = iconClass;
    updateIconPreview(iconClass);
    document.querySelectorAll('.icon-choice-btn').forEach(el => el.classList.remove('selected'));
    if (window.event && window.event.currentTarget) {
        window.event.currentTarget.classList.add('selected');
    }
}

function updateIconPreview(iconClass) {
    const preview = document.getElementById('previewIcon');
    if (preview) {
        preview.className = (iconClass.trim() || 'fas fa-folder') + ' me-1 text-danger';
    }
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(event) {
    event.stopPropagation();
    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    
    input.value = '';
    previewImg.src = '';
    previewImg.style.display = 'none';
    preview.style.display = 'none';
    placeholder.style.display = 'block';
}
</script>
@endpush
@endsection
