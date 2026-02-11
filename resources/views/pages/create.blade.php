@extends('layouts.app')

@section('title', isset($page) ? 'Edit Page' : 'Create Page')

@section('content')
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-magic"></i>
                </div>
                {{ isset($page) ? 'Edit Page: ' . $page->title : 'Create New Page' }}
            </h1>
            <p class="brand-subtitle">Configure page layout, SEO, and visual content blocks</p>
        </div>
        <a href="{{ route('pages.index') }}" class="btn-brand-light">
            <i class="fas fa-arrow-left me-2"></i> Back to List
        </a>
    </div>

    <form action="{{ isset($page) ? route('pages.update', $page) : route('pages.store') }}" method="POST">
        @csrf
        @if(isset($page))
            @method('PUT')
        @endif

        <div class="row g-4">
            <!-- Main Settings -->
            <div class="col-lg-8">
                <div class="brand-table-card p-4 mb-4">
                    <h5 class="fw-bold mb-4">Page Content</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Page Title</label>
                        <input type="text" name="title" id="page-title" class="form-control" 
                               value="{{ old('title', $page->title ?? '') }}" required placeholder="e.g., Home Page">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">URL Slug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">{{ url('/') }}/</span>
                            <input type="text" name="slug" id="page-slug" class="form-control" 
                                   value="{{ old('slug', $page->slug ?? '') }}" required placeholder="home-page">
                        </div>
                    </div>

                    <div id="content-builder" class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0">Dynamic Blocks</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBlock()">
                                <i class="fas fa-plus me-1"></i> Add Block
                            </button>
                        </div>
                        
                        <div id="blocks-container" class="border rounded bg-light p-3" style="min-height: 100px;">
                            @php
                                $blocks = old('content_blocks', $page->content ?? []);
                            @endphp
                            
                            @forelse($blocks as $index => $block)
                                <div class="card shadow-sm mb-3 block-item" data-index="{{ $index }}">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                                        <select name="content_blocks[{{ $index }}][type]" class="form-select form-select-sm" style="max-width: 150px;">
                                            <option value="hero" {{ $block['type'] === 'hero' ? 'selected' : '' }}>Hero Section</option>
                                            <option value="features" {{ $block['type'] === 'features' ? 'selected' : '' }}>Features</option>
                                            <option value="content" {{ $block['type'] === 'content' ? 'selected' : '' }}>Rich Text</option>
                                            <option value="faq" {{ ($block['type'] ?? '') === 'faq' ? 'selected' : '' }}>FAQ Accordion</option>
                                            <option value="testimonials" {{ ($block['type'] ?? '') === 'testimonials' ? 'selected' : '' }}>Testimonials</option>
                                            <option value="cta" {{ ($block['type'] ?? '') === 'cta' ? 'selected' : '' }}>Call to Action</option>
                                            <option value="contact" {{ $block['type'] === 'contact' ? 'selected' : '' }}>Contact Form</option>
                                        </select>
                                        <button type="button" class="btn btn-link text-danger p-0" onclick="removeBlock(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-4">
                                                <label class="small text-muted fw-bold">BG COLOR</label>
                                                <input type="color" name="content_blocks[{{ $index }}][bg_color]" class="form-control form-control-color w-100" value="{{ $block['bg_color'] ?? '#ffffff' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small text-muted fw-bold">TEXT COLOR</label>
                                                <input type="color" name="content_blocks[{{ $index }}][text_color]" class="form-control form-control-color w-100" value="{{ $block['text_color'] ?? '#334155' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small text-muted fw-bold">ANIMATION</label>
                                                <select name="content_blocks[{{ $index }}][animation]" class="form-select form-select-sm">
                                                    <option value="none" {{ ($block['animation'] ?? 'none') === 'none' ? 'selected' : '' }}>None</option>
                                                    <option value="fade-up" {{ ($block['animation'] ?? '') === 'fade-up' ? 'selected' : '' }}>Fade Up</option>
                                                    <option value="fade-down" {{ ($block['animation'] ?? '') === 'fade-down' ? 'selected' : '' }}>Fade Down</option>
                                                    <option value="zoom-in" {{ ($block['animation'] ?? '') === 'zoom-in' ? 'selected' : '' }}>Zoom In</option>
                                                    <option value="slide-up" {{ ($block['animation'] ?? '') === 'slide-up' ? 'selected' : '' }}>Slide Up</option>
                                                </select>
                                            </div>
                                        </div>
                                        <textarea name="content_blocks[{{ $index }}][body]" class="form-control form-control-sm" rows="3" placeholder="Enter content or JSON settings...">{{ $block['body'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted py-4 m-0" id="no-blocks-msg">No blocks added yet Click "Add Block" to start building.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Settings -->
            <div class="col-lg-4">
                <div class="brand-table-card p-4 mb-4">
                    <h5 class="fw-bold mb-4">Publishing</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Layout</label>
                        <select name="layout" class="form-select">
                            <option value="default" {{ old('layout', $page->layout ?? '') === 'default' ? 'selected' : '' }}>Default Layout</option>
                            <option value="full_width" {{ old('layout', $page->layout ?? '') === 'full_width' ? 'selected' : '' }}>Full Width</option>
                            <option value="landing" {{ old('layout', $page->layout ?? '') === 'landing' ? 'selected' : '' }}>Landing Page</option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input type="hidden" name="is_published" value="0">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publish-switch" {{ old('is_published', $page->is_published ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="publish-switch">Publish this page</label>
                    </div>

                    <button type="submit" class="btn-brand-primary w-100 py-2">
                        <i class="fas fa-save me-2"></i> {{ isset($page) ? 'Update Page' : 'Save Page' }}
                    </button>
                </div>

                <div class="brand-table-card p-4">
                    <h5 class="fw-bold mb-4">SEO Settings</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" 
                               value="{{ old('meta_title', $page->meta_title ?? '') }}" placeholder="Custom browser title">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="4" placeholder="Brief page summary for search engines...">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        let blockIndex = {{ count($blocks) }};

        function addBlock() {
            const container = document.getElementById('blocks-container');
            const noBlocksMsg = document.getElementById('no-blocks-msg');
            if (noBlocksMsg) noBlocksMsg.remove();

            const html = `
                <div class="card shadow-sm mb-3 block-item" data-index="${blockIndex}">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <select name="content_blocks[${blockIndex}][type]" class="form-select form-select-sm" style="max-width: 150px;">
                            <option value="hero">Hero Section</option>
                            <option value="features">Features</option>
                            <option value="content">Rich Text</option>
                            <option value="faq">FAQ Accordion</option>
                            <option value="testimonials">Testimonials</option>
                            <option value="cta">Call to Action</option>
                            <option value="contact">Contact Form</option>
                        </select>
                        <button type="button" class="btn btn-link text-danger p-0" onclick="removeBlock(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">BG COLOR</label>
                                <input type="color" name="content_blocks[${blockIndex}][bg_color]" class="form-control form-control-color w-100" value="#ffffff">
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">TEXT COLOR</label>
                                <input type="color" name="content_blocks[${blockIndex}][text_color]" class="form-control form-control-color w-100" value="#334155">
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">ANIMATION</label>
                                <select name="content_blocks[${blockIndex}][animation]" class="form-select form-select-sm">
                                    <option value="none">None</option>
                                    <option value="fade-up">Fade Up</option>
                                    <option value="fade-down">Fade Down</option>
                                    <option value="zoom-in">Zoom In</option>
                                    <option value="slide-up">Slide Up</option>
                                </select>
                            </div>
                        </div>
                        <textarea name="content_blocks[${blockIndex}][body]" class="form-control form-control-sm" rows="3" placeholder="Enter content or JSON settings..."></textarea>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            blockIndex++;
        }

        function removeBlock(btn) {
            btn.closest('.block-item').remove();
        }

        @if(!isset($page))
        // Simple slug generator
        document.getElementById('page-title').addEventListener('input', function() {
            const title = this.value;
            const slug = title.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            document.getElementById('page-slug').value = slug;
        });
        @endif
    </script>
    @endpush
@endsection
