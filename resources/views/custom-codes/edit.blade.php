@extends('layouts.app')

@section('title', isset($customCode) ? 'Modifier le Snippet : ' . $customCode->title : 'Nouveau Snippet de Code')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/monokai.min.css">
<style>
    .CodeMirror {
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 0.75rem;
        height: 440px;
        font-family: 'Fira Code', 'JetBrains Mono', Monaco, Consolas, monospace;
        font-size: 0.875rem;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.04);
    }
    .editor-container {
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid var(--border-color, #e2e8f0);
    }
    .editor-toolbar-header {
        background: #1e1e24;
        color: #94a3b8;
        padding: 0.625rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8125rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .editor-dots {
        display: flex;
        gap: 0.375rem;
    }
    .editor-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .editor-dot.red { background: #ef4444; }
    .editor-dot.yellow { background: #f59e0b; }
    .editor-dot.green { background: #10b981; }
    .editor-container .CodeMirror {
        border: none;
        border-radius: 0 0 0.75rem 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- Brand Header -->
    <div class="brand-header mb-4">
        <div class="brand-header-content">
            <div class="d-flex align-items-center gap-2.5">
                <div class="brand-header-icon flex-shrink-0">
                    <i class="fas fa-code"></i>
                </div>
                <div>
                    <h1 class="brand-title">{{ isset($customCode) ? 'Modifier le Snippet' : 'Nouveau Snippet de Code' }}</h1>
                    <p class="brand-subtitle">{{ isset($customCode) ? 'Édition de l\'extrait #' . $customCode->id . ' — ' . $customCode->title : 'Ajoutez un script JS, une feuille CSS ou une balise de tracking personnalisée' }}</p>
                </div>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('custom-codes.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour aux Snippets</span>
            </a>
        </div>
    </div>

    <!-- Alert / Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="fas fa-exclamation-circle text-danger mt-1"></i>
                <div>
                    <div class="fw-semibold mb-1">Veuillez corriger les erreurs suivantes :</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-primary"></i>
                <h5 class="card-title fw-bold text-dark mb-0 fs-6">Configuration de l'Extrait</h5>
            </div>
            <span class="badge {{ isset($customCode) ? ($customCode->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary') : 'bg-primary-subtle text-primary' }} px-2.5 py-1.5 rounded-pill">
                {{ isset($customCode) ? ($customCode->is_active ? 'Actif sur le site' : 'Désactivé') : 'Nouveau' }}
            </span>
        </div>

        <div class="card-body p-3 p-md-4">
            <form action="{{ isset($customCode) ? route('custom-codes.update', $customCode) : route('custom-codes.store') }}" method="POST" id="snippet-form">
                @csrf
                @if(isset($customCode))
                    @method('PUT')
                @endif

                <div class="row g-3 g-md-4 mb-4">
                    <!-- Title -->
                    <div class="col-12 col-lg-6">
                        <label for="title" class="form-label fw-semibold text-dark small mb-1.5">
                            Titre du Snippet <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-heading"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $customCode->title ?? '') }}" placeholder="Ex: Google Analytics 4, Pixel Meta, CSS Navbar..." required>
                        </div>
                        <small class="text-muted d-block mt-1 fs-xs">Nom mnémonique pour identifier l'extrait dans le tableau de bord.</small>
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="type" class="form-label fw-semibold text-dark small mb-1.5">
                            Type de Code <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-file-code"></i>
                            </span>
                            <select class="form-select border-start-0 ps-0 @error('type') is-invalid @enderror" id="type" name="type" required onchange="updateEditorMode()">
                                <option value="css" {{ old('type', $customCode->type ?? '') == 'css' ? 'selected' : '' }}>CSS (Feuille de styles)</option>
                                <option value="js" {{ old('type', $customCode->type ?? '') == 'js' ? 'selected' : '' }}>JavaScript (Script)</option>
                                <option value="html" {{ old('type', $customCode->type ?? '') == 'html' ? 'selected' : '' }}>HTML / Balises</option>
                            </select>
                        </div>
                        <small class="text-muted d-block mt-1 fs-xs">Définit la coloration syntaxique et l'encapsulation.</small>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Position -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="position" class="form-label fw-semibold text-dark small mb-1.5">
                            Emplacement d'Injection <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <select class="form-select border-start-0 ps-0 @error('position') is-invalid @enderror" id="position" name="position" required>
                                <option value="head" {{ old('position', $customCode->position ?? '') == 'head' ? 'selected' : '' }}>Head (&lt;head&gt;)</option>
                                <option value="body_start" {{ old('position', $customCode->position ?? '') == 'body_start' ? 'selected' : '' }}>Body Start (Début de &lt;body&gt;)</option>
                                <option value="body_end" {{ old('position', $customCode->position ?? '') == 'body_end' ? 'selected' : '' }}>Body End (Fin de &lt;/body&gt;)</option>
                            </select>
                        </div>
                        <small class="text-muted d-block mt-1 fs-xs">Emplacement où le code sera injecté dans le DOM.</small>
                        @error('position')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="priority" class="form-label fw-semibold text-dark small mb-1.5">
                            Priorité d'Exécution
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-sort-numeric-down"></i>
                            </span>
                            <input type="number" class="form-control border-start-0 ps-0 @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{ old('priority', $customCode->priority ?? 0) }}" min="0" step="1">
                        </div>
                        <small class="text-muted d-block mt-1 fs-xs">Ordre croissant (les valeurs les plus élevées s'exécutent en premier).</small>
                        @error('priority')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Switch -->
                    <div class="col-12 col-sm-6 col-lg-9">
                        <label class="form-label fw-semibold text-dark small mb-1.5 d-block">
                            État de Diffusion
                        </label>
                        <div class="p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2.5">
                                <i class="fas fa-power-off text-muted"></i>
                                <div>
                                    <div class="fw-semibold text-dark small">Activer l'injection sur la boutique</div>
                                    <div class="text-muted fs-xs">Si activé, ce script sera compilé et rendu pour tous les visiteurs.</div>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0 fs-5">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $customCode->is_active ?? true) ? 'checked' : '' }} role="switch">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CodeMirror Editor -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label for="code-editor" class="form-label fw-semibold text-dark small mb-0 d-flex align-items-center gap-2">
                            <span>Contenu du Code</span>
                            <span id="editor-type-badge" class="badge bg-primary-subtle text-primary border border-primary-subtle text-uppercase font-monospace px-2 py-0.5" style="font-size: 0.7rem;">CSS</span>
                        </label>
                        <span class="text-muted fs-xs">
                            <i class="fas fa-info-circle me-1"></i>Les balises &lt;style&gt; ou &lt;script&gt; sont ajoutées automatiquement selon le type.
                        </span>
                    </div>

                    <div class="editor-container">
                        <div class="editor-toolbar-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="editor-dots">
                                    <span class="editor-dot red"></span>
                                    <span class="editor-dot yellow"></span>
                                    <span class="editor-dot green"></span>
                                </div>
                                <span class="ms-2 font-monospace text-light-50" id="editor-filename">snippet.css</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span><i class="fas fa-palette me-1"></i>Thème Monokai</span>
                                <span class="badge bg-dark border border-secondary text-muted px-2 py-0.5">UTF-8</span>
                            </div>
                        </div>
                        <textarea id="code-editor" name="content">{{ old('content', $customCode->content ?? '') }}</textarea>
                    </div>
                    @error('content')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="d-flex align-items-center justify-content-between pt-3 border-top flex-wrap gap-2">
                    <a href="{{ route('custom-codes.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3">
                        <i class="fas fa-times"></i>
                        <span>Annuler</span>
                    </a>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 shadow-sm">
                        <i class="fas fa-save"></i>
                        <span>{{ isset($customCode) ? 'Mettre à jour le Snippet' : 'Enregistrer le Snippet' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editor = CodeMirror.fromTextArea(document.getElementById("code-editor"), {
            lineNumbers: true,
            theme: "monokai",
            mode: "css",
            tabSize: 2,
            indentWithTabs: false,
            lineWrapping: true
        });

        window.updateEditorMode = function() {
            var typeSelect = document.getElementById('type');
            if (!typeSelect) return;
            var type = typeSelect.value;
            var mode = 'css';
            var ext = 'snippet.css';
            var badgeText = 'CSS';

            if (type === 'js') {
                mode = 'javascript';
                ext = 'snippet.js';
                badgeText = 'JavaScript';
            } else if (type === 'html') {
                mode = 'htmlmixed';
                ext = 'snippet.html';
                badgeText = 'HTML';
            }

            editor.setOption("mode", mode);
            
            var filenameElem = document.getElementById('editor-filename');
            if (filenameElem) filenameElem.textContent = ext;

            var badgeElem = document.getElementById('editor-type-badge');
            if (badgeElem) badgeElem.textContent = badgeText;
        };

        // Initialize mode on load
        updateEditorMode();

        // Refresh editor after a tiny delay to ensure proper rendering inside Bootstrap containers
        setTimeout(function() {
            editor.refresh();
        }, 150);
    });
</script>
@endpush
