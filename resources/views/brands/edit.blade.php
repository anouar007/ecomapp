@extends('layouts.app')

@section('title', 'Modifier la marque : ' . $brand->name)

@section('content')
<div class="categories-container">
    <!-- En-tête de page -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h1 class="brand-title">Modifier : {{ $brand->name }}</h1>
                <p class="brand-subtitle">
                    Identifiant : <span class="font-monospace text-muted">{{ $brand->slug }}</span>
                    @if($brand->products_count > 0)
                        &bull; <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5">{{ $brand->products_count }} produit(s) associé(s)</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2">
            <a href="{{ route('brands.index') }}" class="btn-brand-outline">
                <i class="fas fa-arrow-left me-1"></i> <span>Retour</span>
            </a>
            <form action="{{ route('brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement la marque « {{ addslashes($brand->name) }} » ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    @if($errors->any())
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
    <div class="cat-form-card shadow-sm rounded-3 p-4 bg-white border">
        <form action="{{ route('brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                <i class="fas fa-tag text-danger"></i>
                <span class="fw-bold text-dark fs-6">Modifier les informations</span>
            </div>

            <div class="row g-3 g-md-4">
                <!-- Nom -->
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-bold small text-dark mb-1">
                        Nom de la marque <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $brand->name) }}" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="col-12 col-md-6">
                    <label for="slug" class="form-label fw-bold small text-dark mb-1">
                        Identifiant d'URL (Slug)
                    </label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           class="form-control font-monospace @error('slug') is-invalid @enderror" 
                           value="{{ old('slug', $brand->slug) }}">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Site Web -->
                <div class="col-12 col-md-6">
                    <label for="website" class="form-label fw-bold small text-dark mb-1">
                        Site officiel
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-globe"></i></span>
                        <input type="url" 
                               id="website" 
                               name="website" 
                               class="form-control @error('website') is-invalid @enderror" 
                               value="{{ old('website', $brand->website) }}" 
                               placeholder="https://www.brand.com">
                    </div>
                    @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Statut & Ordre -->
                <div class="col-12 col-md-6">
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="status" class="form-label fw-bold small text-dark mb-1">
                                Statut <span class="text-danger">*</span>
                            </label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $brand->status) === 'active' ? 'selected' : '' }}>🟢 Actif</option>
                                <option value="inactive" {{ old('status', $brand->status) === 'inactive' ? 'selected' : '' }}>⚪ Inactif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-6">
                            <label for="sort_order" class="form-label fw-bold small text-dark mb-1">
                                Ordre d'affichage
                            </label>
                            <input type="number" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   value="{{ old('sort_order', $brand->sort_order) }}" 
                                   min="0" 
                                   step="1">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label fw-bold small text-dark mb-1">
                        Description / Présentation (Optionnel)
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3" 
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $brand->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Logo Upload -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small text-dark mb-1">
                        Logo de la marque
                    </label>

                    @if($brand->logo)
                        <div class="d-flex align-items-center gap-3 p-2 mb-2 rounded border bg-light">
                            <div class="rounded p-1 bg-white border d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-height: 40px; max-width: 40px; object-fit: contain;">
                            </div>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo">
                                    <label class="form-check-label text-danger small fw-semibold cursor-pointer" for="removeLogo">
                                        <i class="fas fa-trash-alt me-1"></i> Supprimer ce logo
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <input type="file" 
                           id="logoInput" 
                           name="logo" 
                           class="form-control @error('logo') is-invalid @enderror" 
                           accept="image/*"
                           onchange="previewBrandLogo(this)">
                    <div class="form-text small text-muted">Format recommandé : PNG fond transparent ou SVG, max 2Mo.</div>
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div id="brandLogoPreviewBox" class="mt-2 p-2 border rounded bg-light text-center" style="display: none; max-width: 140px;">
                        <img id="brandLogoPreviewImg" src="#" alt="Aperçu" style="max-height: 50px; max-width: 100%; object-fit: contain;">
                    </div>
                </div>

                <!-- Is Featured Checkbox -->
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="form-check p-3 rounded border bg-light w-100">
                        <input class="form-check-input ms-0 me-2" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $brand->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark cursor-pointer" for="is_featured">
                            <i class="fas fa-star text-warning me-1"></i> Mettre en avant cette marque
                        </label>
                        <div class="form-text small text-muted ms-4">La marque sera mise en avant dans les sélections vedettes.</div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('brands.index') }}" class="btn-brand-light">
                    Annuler
                </a>
                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewBrandLogo(input) {
    const box = document.getElementById('brandLogoPreviewBox');
    const img = document.getElementById('brandLogoPreviewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            box.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        box.style.display = 'none';
    }
}
</script>
@endsection
