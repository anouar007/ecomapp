@extends('layouts.app')

@section('title', isset($customCode) ? 'Edit Snippet' : 'New Snippet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/monokai.min.css">
<style>
    .CodeMirror {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        height: 400px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-code"></i> {{ isset($customCode) ? 'Edit Snippet' : 'Create New Snippet' }}
    </h1>
    <p class="page-subtitle">{{ isset($customCode) ? 'Update snippet details and content' : 'Add a new custom CSS, JS, or HTML snippet' }}</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit"></i> Snippet Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ isset($customCode) ? route('custom-codes.update', $customCode) : route('custom-codes.store') }}" method="POST">
            @csrf
            @if(isset($customCode))
                @method('PUT')
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label for="title" class="form-label">Title <span class="required">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $customCode->title ?? '') }}" placeholder="e.g., Homepage Animations" required>
                    <small class="form-help">A descriptive name for this code snippet.</small>
                </div>
                
                <div class="form-group">
                    <label for="type" class="form-label">Type <span class="required">*</span></label>
                    <select class="form-control" id="type" name="type" required onchange="updateEditorMode()">
                        <option value="css" {{ old('type', $customCode->type ?? '') == 'css' ? 'selected' : '' }}>CSS</option>
                        <option value="js" {{ old('type', $customCode->type ?? '') == 'js' ? 'selected' : '' }}>JavaScript</option>
                        <option value="html" {{ old('type', $customCode->type ?? '') == 'html' ? 'selected' : '' }}>HTML</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="position" class="form-label">Position <span class="required">*</span></label>
                    <select class="form-control" id="position" name="position" required>
                        <option value="head" {{ old('position', $customCode->position ?? '') == 'head' ? 'selected' : '' }}>Head (&lt;head&gt;)</option>
                        <option value="body_start" {{ old('position', $customCode->position ?? '') == 'body_start' ? 'selected' : '' }}>Body Start (&lt;body&gt;)</option>
                        <option value="body_end" {{ old('position', $customCode->position ?? '') == 'body_end' ? 'selected' : '' }}>Body End (&lt;/body&gt;)</option>
                    </select>
                    <small class="form-help">Where this code will be injected.</small>
                </div>
            </div>

            <div class="form-group">
                <label for="content" class="form-label">Code Content</label>
                <textarea id="code-editor" name="content">{{ old('content', $customCode->content ?? '') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="priority" class="form-label">Priority</label>
                    <input type="number" class="form-control" id="priority" name="priority" value="{{ old('priority', $customCode->priority ?? 0) }}">
                    <small class="form-help">Higher numbers run first. Default is 0.</small>
                </div>
                
                <div class="form-group d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $customCode->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Enable Functionality</label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('custom-codes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Snippet
                </button>
            </div>
        </form>
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
    var editor = CodeMirror.fromTextArea(document.getElementById("code-editor"), {
        lineNumbers: true,
        theme: "monokai",
        mode: "css"
    });

    function updateEditorMode() {
        var type = document.getElementById('type').value;
        var mode = 'css';
        if(type === 'js') mode = 'javascript';
        if(type === 'html') mode = 'htmlmixed';
        editor.setOption("mode", mode);
    }

    // Set initial mode
    updateEditorMode();
</script>
@endpush
