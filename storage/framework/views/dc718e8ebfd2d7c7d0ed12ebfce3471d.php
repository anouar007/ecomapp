<?php $__env->startSection('title', isset($page) ? 'Edit Page' : 'Create Page'); ?>

<?php $__env->startSection('content'); ?>
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-magic"></i>
                </div>
                <?php echo e(isset($page) ? 'Edit Page: ' . $page->title : 'Create New Page'); ?>

            </h1>
            <p class="brand-subtitle">Configure page layout, SEO, and visual content blocks</p>
        </div>
        <a href="<?php echo e(route('pages.index')); ?>" class="btn-brand-light">
            <i class="fas fa-arrow-left me-2"></i> Back to List
        </a>
    </div>

    <form action="<?php echo e(isset($page) ? route('pages.update', $page) : route('pages.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php if(isset($page)): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Main Settings -->
            <div class="col-lg-8">
                <div class="brand-table-card p-4 mb-4">
                    <h5 class="fw-bold mb-4">Page Content</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Page Title</label>
                        <input type="text" name="title" id="page-title" class="form-control" 
                               value="<?php echo e(old('title', $page->title ?? '')); ?>" required placeholder="e.g., Home Page">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">URL Slug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><?php echo e(url('/')); ?>/</span>
                            <input type="text" name="slug" id="page-slug" class="form-control" 
                                   value="<?php echo e(old('slug', $page->slug ?? '')); ?>" required placeholder="home-page">
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
                            <?php
                                $blocks = old('content_blocks', $page->content ?? []);
                            ?>
                            
                            <?php $__empty_1 = true; $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="card shadow-sm mb-3 block-item" data-index="<?php echo e($index); ?>">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                                        <select name="content_blocks[<?php echo e($index); ?>][type]" class="form-select form-select-sm" style="max-width: 150px;">
                                            <option value="hero" <?php echo e($block['type'] === 'hero' ? 'selected' : ''); ?>>Hero Section</option>
                                            <option value="features" <?php echo e($block['type'] === 'features' ? 'selected' : ''); ?>>Features</option>
                                            <option value="content" <?php echo e($block['type'] === 'content' ? 'selected' : ''); ?>>Rich Text</option>
                                            <option value="faq" <?php echo e(($block['type'] ?? '') === 'faq' ? 'selected' : ''); ?>>FAQ Accordion</option>
                                            <option value="testimonials" <?php echo e(($block['type'] ?? '') === 'testimonials' ? 'selected' : ''); ?>>Testimonials</option>
                                            <option value="cta" <?php echo e(($block['type'] ?? '') === 'cta' ? 'selected' : ''); ?>>Call to Action</option>
                                            <option value="contact" <?php echo e($block['type'] === 'contact' ? 'selected' : ''); ?>>Contact Form</option>
                                        </select>
                                        <button type="button" class="btn btn-link text-danger p-0" onclick="removeBlock(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-4">
                                                <label class="small text-muted fw-bold">BG COLOR</label>
                                                <input type="color" name="content_blocks[<?php echo e($index); ?>][bg_color]" class="form-control form-control-color w-100" value="<?php echo e($block['bg_color'] ?? '#ffffff'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small text-muted fw-bold">TEXT COLOR</label>
                                                <input type="color" name="content_blocks[<?php echo e($index); ?>][text_color]" class="form-control form-control-color w-100" value="<?php echo e($block['text_color'] ?? '#334155'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small text-muted fw-bold">ANIMATION</label>
                                                <select name="content_blocks[<?php echo e($index); ?>][animation]" class="form-select form-select-sm">
                                                    <option value="none" <?php echo e(($block['animation'] ?? 'none') === 'none' ? 'selected' : ''); ?>>None</option>
                                                    <option value="fade-up" <?php echo e(($block['animation'] ?? '') === 'fade-up' ? 'selected' : ''); ?>>Fade Up</option>
                                                    <option value="fade-down" <?php echo e(($block['animation'] ?? '') === 'fade-down' ? 'selected' : ''); ?>>Fade Down</option>
                                                    <option value="zoom-in" <?php echo e(($block['animation'] ?? '') === 'zoom-in' ? 'selected' : ''); ?>>Zoom In</option>
                                                    <option value="slide-up" <?php echo e(($block['animation'] ?? '') === 'slide-up' ? 'selected' : ''); ?>>Slide Up</option>
                                                </select>
                                            </div>
                                        </div>
                                        <textarea name="content_blocks[<?php echo e($index); ?>][body]" class="form-control form-control-sm" rows="3" placeholder="Enter content or JSON settings..."><?php echo e($block['body'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-center text-muted py-4 m-0" id="no-blocks-msg">No blocks added yet Click "Add Block" to start building.</p>
                            <?php endif; ?>
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
                            <option value="default" <?php echo e(old('layout', $page->layout ?? '') === 'default' ? 'selected' : ''); ?>>Default Layout</option>
                            <option value="full_width" <?php echo e(old('layout', $page->layout ?? '') === 'full_width' ? 'selected' : ''); ?>>Full Width</option>
                            <option value="landing" <?php echo e(old('layout', $page->layout ?? '') === 'landing' ? 'selected' : ''); ?>>Landing Page</option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input type="hidden" name="is_published" value="0">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publish-switch" <?php echo e(old('is_published', $page->is_published ?? false) ? 'checked' : ''); ?>>
                        <label class="form-check-label fw-bold" for="publish-switch">Publish this page</label>
                    </div>

                    <button type="submit" class="btn-brand-primary w-100 py-2">
                        <i class="fas fa-save me-2"></i> <?php echo e(isset($page) ? 'Update Page' : 'Save Page'); ?>

                    </button>
                </div>

                <div class="brand-table-card p-4">
                    <h5 class="fw-bold mb-4">SEO Settings</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" 
                               value="<?php echo e(old('meta_title', $page->meta_title ?? '')); ?>" placeholder="Custom browser title">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="4" placeholder="Brief page summary for search engines..."><?php echo e(old('meta_description', $page->meta_description ?? '')); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php $__env->startPush('scripts'); ?>
    <script>
        let blockIndex = <?php echo e(count($blocks)); ?>;

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

        <?php if(!isset($page)): ?>
        // Simple slug generator
        document.getElementById('page-title').addEventListener('input', function() {
            const title = this.value;
            const slug = title.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            document.getElementById('page-slug').value = slug;
        });
        <?php endif; ?>
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/pages/create.blade.php ENDPATH**/ ?>