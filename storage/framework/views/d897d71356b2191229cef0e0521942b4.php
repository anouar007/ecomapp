<?php $__env->startSection('title', __('Edit Product')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<style>
    .multi-image-upload {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .image-upload-box {
        width: 100%;
        aspect-ratio: 1/1;
        border: 2px dashed #e2e8f0;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f8fafc;
        position: relative;
        overflow: hidden;
    }

    .image-upload-box:hover {
        border-color: var(--primary-color, #667eea);
        background: #f0f4ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .image-upload-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-remove-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(239, 68, 68, 0.9);
        backdrop-filter: blur(4px);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        z-index: 10;
        transition: all 0.2s;
    }
    
    .image-remove-btn:hover {
        background: #ef4444;
        transform: scale(1.1);
    }

    .primary-badge {
        position: absolute;
        bottom: 6px;
        left: 6px;
        background: var(--primary-color, #667eea);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 8px;
        font-weight: 800;
        z-index: 5;
    }

    /* Tabs Styling */
    .nav-tabs-custom {
        border-bottom: 2px solid #f1f5f9;
        gap: 8px;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        padding: 10px 20px;
        border-radius: 10px 10px 0 0;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s;
        position: relative;
    }
    .nav-tabs-custom .nav-link.active {
        color: var(--primary-color, #667eea);
        background: transparent;
    }
    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--primary-color, #667eea);
    }

    .variant-card {
        display: block !important;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        position: relative;
    }
    
    .variant-header {
        display: flex !important;
        align-items: center !important;
        gap: 16px !important;
        margin-bottom: 20px !important;
        padding-bottom: 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        width: 100% !important;
    }

    .variant-img-wrapper {
        width: 70px !important;
        height: 70px !important;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }
    
    .variant-grid-container {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
        gap: 20px !important;
        width: 100% !important;
    }

    .variant-input-group {
        display: block !important;
        width: 100% !important;
    }

    .variant-input-xl {
        display: block !important;
        width: 100% !important;
        height: 46px !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        border-radius: 10px !important;
        padding: 0 15px !important;
        border: 1px solid #e2e8f0 !important;
        background: #fff !important;
    }
    
    .variant-input-xl:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    }

    .variant-label-xl {
        display: block !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #64748b !important;
        margin-bottom: 8px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    .variant-img-wrapper span {
        font-size: 10px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .btn-remove-variant {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fff;
        border: 1px solid #fee2e2;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-remove-variant:hover {
        background: #ef4444;
        color: #fff;
        border-color: #ef4444;
    }

    .form-section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f1f5f9;
    }

    .sticky-actions {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="brand-header mb-4">
    <div>
        <h1 class="brand-title">
            <div class="brand-header-icon"><i class="fas fa-edit"></i></div>
            <?php echo e(__('Edit Product')); ?>

        </h1>
        <p class="brand-subtitle"><?php echo e(__('Modify product details for')); ?> <span class="text-primary fw-bold"><?php echo e($product->translated_name); ?></span></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-times"></i> <?php echo e(__('Cancel')); ?>

        </a>
        <button type="submit" form="productForm" class="btn btn-primary">
            <i class="fas fa-save"></i> <?php echo e(__('Update Product')); ?>

        </button>
    </div>
</div>

<?php if($errors->any()): ?>
<div class="alert alert-danger mb-4">
    <i class="fas fa-exclamation-circle fs-4"></i>
    <div>
        <h6 class="fw-bold mb-1"><?php echo e(__('Validation Errors')); ?></h6>
        <ul class="mb-0 small ps-3">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<form action="<?php echo e(route('products.update', $product)); ?>" method="POST" enctype="multipart/form-data" id="productForm">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="row g-4">
        <!-- Main Column -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> <?php echo e(__('Basic Information')); ?>

                    </div>

                    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="langTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar-content" type="button" role="tab"><?php echo e(__('Arabic')); ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr-content" type="button" role="tab"><?php echo e(__('French')); ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-content" type="button" role="tab"><?php echo e(__('English')); ?></button>
                        </li>
                    </ul>

                    <div class="tab-content" id="langTabsContent">
                        <!-- Arabic -->
                        <div class="tab-pane fade show active" id="ar-content" role="tabpanel">
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Product Name (AR)')); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" class="form-control form-control-lg" value="<?php echo e(old('name_ar', $product->name_ar)); ?>" placeholder="اسم المنتج بالكامل" dir="rtl" required autofocus>
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label"><?php echo e(__('Description (AR)')); ?></label>
                                <textarea name="description_ar" class="form-control" rows="4" placeholder="وصف المنتج بالتفصيل..." dir="rtl"><?php echo e(old('description_ar', $product->description_ar)); ?></textarea>
                            </div>
                        </div>
                        <!-- French -->
                        <div class="tab-pane fade" id="fr-content" role="tabpanel">
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Product Name (FR)')); ?></label>
                                <input type="text" name="name_fr" class="form-control form-control-lg" value="<?php echo e(old('name_fr', $product->name_fr)); ?>" placeholder="Nom du produit">
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label"><?php echo e(__('Description (FR)')); ?></label>
                                <textarea name="description_fr" class="form-control" rows="4" placeholder="Description détaillée en français..."><?php echo e(old('description_fr', $product->description_fr)); ?></textarea>
                            </div>
                        </div>
                        <!-- English -->
                        <div class="tab-pane fade" id="en-content" role="tabpanel">
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Product Name (EN)')); ?></label>
                                <input type="text" name="name_en" class="form-control form-control-lg" value="<?php echo e(old('name_en', $product->name_en)); ?>" placeholder="Product display name">
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label"><?php echo e(__('Description (EN)')); ?></label>
                                <textarea name="description_en" class="form-control" rows="4" placeholder="Detailed English description..."><?php echo e(old('description_en', $product->description_en)); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="name" value="<?php echo e($product->name); ?>">
                </div>
            </div>

            <!-- Pricing & Inventory Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="form-section-title">
                        <i class="fas fa-wallet"></i> <?php echo e(__('Pricing & Inventory')); ?>

                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Cost Price')); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-hand-holding-dollar text-muted"></i></span>
                                    <input type="number" name="cost_price" class="form-control border-start-0" value="<?php echo e(old('cost_price', $product->cost_price)); ?>" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('Regular Price')); ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-tag text-muted"></i></span>
                                    <input type="number" name="price" class="form-control border-start-0 fw-bold" value="<?php echo e(old('price', $product->price)); ?>" step="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label text-danger fw-bold"><?php echo e(__('Sale Price')); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-danger-subtle border-danger-subtle border-end-0"><i class="fas fa-bolt text-danger"></i></span>
                                    <input type="number" name="sale_price" class="form-control border-danger-subtle border-start-0 text-danger fw-bold" value="<?php echo e(old('sale_price', $product->sale_price)); ?>" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label"><?php echo e(__('Current Stock')); ?> <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" value="<?php echo e(old('stock', $product->stock)); ?>" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label"><?php echo e(__('Low Stock Alert')); ?></label>
                                <input type="number" name="min_stock" class="form-control" value="<?php echo e(old('min_stock', $product->min_stock)); ?>" required min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variations Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div class="form-section-title mb-0" style="flex: none;">
                        <i class="fas fa-layer-group"></i> <?php echo e(__('Product Variations')); ?>

                    </div>
                    <button type="button" onclick="addVariationRow()" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-plus"></i> <?php echo e(__('Add Variation')); ?>

                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="variantsContainer">
                        <?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="variant-card" data-id="<?php echo e($variant->id); ?>">
                            <input type="hidden" name="variants[<?php echo e($index); ?>][id]" value="<?php echo e($variant->id); ?>">
                            
                            <!-- Variant Header -->
                            <div class="variant-header">
                                <div class="variant-img-wrapper" onclick="this.querySelector('input').click()">
                                    <?php if($variant->color_image): ?>
                                        <img src="<?php echo e(asset('storage/' . $variant->color_image)); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                    <?php else: ?>
                                        <i class="fas fa-camera text-muted"></i>
                                    <?php endif; ?>
                                    <input type="file" name="variants[<?php echo e($index); ?>][color_image]" class="d-none" onchange="previewVariantImage(this)">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0" style="color: #475569;"><?php echo e(__('Variation')); ?> #<?php echo e($index + 1); ?></h6>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="this.closest('.variant-card').remove(); checkVariantsEmpty();">
                                    <i class="fas fa-trash me-1"></i> <?php echo e(__('Remove')); ?>

                                </button>
                            </div>

                            <div class="variant-grid-container">
                                <div class="variant-input-group">
                                    <label class="variant-label-xl"><?php echo e(__('Size / Name')); ?></label>
                                    <input type="text" name="variants[<?php echo e($index); ?>][size]" class="variant-input-xl" value="<?php echo e($variant->size); ?>" placeholder="M, XL, 42...">
                                </div>

                                <div class="variant-input-group">
                                    <label class="variant-label-xl"><?php echo e(__('Grouping Key')); ?></label>
                                    <input type="text" name="variants[<?php echo e($index); ?>][style_key]" class="variant-input-xl" value="<?php echo e($variant->style_key); ?>" placeholder="Set A">
                                </div>

                                <div class="variant-input-group">
                                    <label class="variant-label-xl"><?php echo e(__('Price')); ?> (DH)</label>
                                    <input type="number" name="variants[<?php echo e($index); ?>][price]" class="variant-input-xl" value="<?php echo e($variant->price); ?>" step="0.01">
                                </div>

                                <div class="variant-input-group">
                                    <label class="variant-label-xl text-danger"><?php echo e(__('Sale Price')); ?> (DH)</label>
                                    <input type="number" name="variants[<?php echo e($index); ?>][sale_price]" class="variant-input-xl border-danger-subtle text-danger" value="<?php echo e($variant->sale_price); ?>" step="0.01">
                                </div>

                                <div class="variant-input-group">
                                    <label class="variant-label-xl"><?php echo e(__('Stock')); ?></label>
                                    <input type="number" name="variants[<?php echo e($index); ?>][stock]" class="variant-input-xl" value="<?php echo e($variant->stock); ?>">
                                </div>
                            </div>
                            <input type="hidden" name="variants[<?php echo e($index); ?>][sku]" value="<?php echo e($variant->sku); ?>">
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div id="noVariantsMsg" class="text-center py-5 bg-light rounded-4 border-2 border-dashed <?php echo e($product->variants->count() > 0 ? 'd-none' : ''); ?>">
                        <i class="fas fa-swatchbook fa-3x text-muted opacity-25 mb-3"></i>
                        <h6 class="fw-bold text-muted"><?php echo e(__('No variations yet')); ?></h6>
                        <p class="small text-muted mb-0"><?php echo e(__('Add sizes, colors or styles to this product.')); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="sticky-actions">
                <!-- Status & Category Card -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="fas fa-cog"></i> <?php echo e(__('Status & Category')); ?>

                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Product Status')); ?></label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo e(old('status', $product->status) == 'active' ? 'selected' : ''); ?>><?php echo e(__('Active / Visible')); ?></option>
                                <option value="inactive" <?php echo e(old('status', $product->status) == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('Hidden / Draft')); ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('Primary Category')); ?></label>
                            <select name="category_id" class="form-select" id="category_id">
                                <option value=""><?php echo e(__('-- Select Category --')); ?></option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id', $product->category_id) == $category->id ? 'selected' : ''); ?>>
                                        <?php echo e($category->breadcrumb); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label"><?php echo e(__('SKU / Identifier')); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="sku" id="sku" class="form-control font-monospace small" value="<?php echo e(old('sku', $product->sku)); ?>" placeholder="AUTOGEN" required>
                                <button type="button" onclick="generateSKU()" class="btn btn-light border"><i class="fas fa-sync-alt"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Gallery Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="fas fa-images"></i> <?php echo e(__('Media Gallery')); ?>

                        </div>
                        
                        <div class="multi-image-upload" id="imagePreviewContainer">
                            <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="image-upload-box" data-image-id="<?php echo e($image->id); ?>">
                                <img src="<?php echo e(asset('storage/' . $image->image_path)); ?>">
                                <?php if($image->is_primary): ?>
                                    <span class="primary-badge"><?php echo e(__('PRIMARY')); ?></span>
                                <?php endif; ?>
                                <span class="image-remove-btn" onclick="markImageForRemoval(<?php echo e($image->id); ?>, this)"><i class="fas fa-times"></i></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="image-upload-box" onclick="document.getElementById('images').click()">
                                <div class="text-center">
                                    <i class="fas fa-cloud-arrow-up fs-3 text-primary mb-2"></i>
                                    <div class="x-small fw-bold"><?php echo e(__('Upload')); ?></div>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple style="display: none;" onchange="handleNewImages(event)">
                        
                        <div class="mt-3 p-3 bg-light rounded-3 small text-muted">
                            <i class="fas fa-info-circle me-1"></i> <?php echo e(__('First image will be used as the primary thumbnail.')); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Mobile Actions -->
    <div class="sticky-mobile-actions d-lg-none">
        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary">
            <?php echo e(__('Cancel')); ?>

        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?php echo e(__('Update')); ?>

        </button>
    </div>
</form>

<?php $__env->startPush('scripts'); ?>
<script>
let imagesToRemove = [];

function markImageForRemoval(imageId, button) {
    const box = button.closest('.image-upload-box');
    if (!imagesToRemove.includes(imageId)) {
        imagesToRemove.push(imageId);
        box.style.opacity = '0.3';
        button.innerHTML = '<i class="fas fa-undo"></i>';
        button.style.background = '#64748b';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_images[]';
        input.value = imageId;
        input.id = 'remove_' + imageId;
        document.getElementById('productForm').appendChild(input);
    } else {
        imagesToRemove = imagesToRemove.filter(id => id !== imageId);
        box.style.opacity = '1';
        button.innerHTML = '<i class="fas fa-times"></i>';
        button.style.background = '';
        document.getElementById('remove_' + imageId).remove();
    }
}

function handleNewImages(event) {
    const container = document.getElementById('imagePreviewContainer');
    const files = Array.from(event.target.files);
    
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const box = document.createElement('div');
            box.className = 'image-upload-box';
            box.innerHTML = `
                <img src="${e.target.result}">
                <span class="image-remove-btn" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></span>
            `;
            container.insertBefore(box, container.lastElementChild);
        };
        reader.readAsDataURL(file);
    });
}

function generateSKU() {
    const name = document.getElementById('name_ar')?.value || 'PRD';
    const prefix = name.substring(0, 3).toUpperCase();
    const random = Math.floor(1000 + Math.random() * 9000);
    document.getElementById('sku').value = `${prefix}-${Date.now().toString().slice(-4)}${random}`;
}

let variantCounter = <?php echo e($product->variants->count() + 100); ?>;
function addVariationRow() {
    const idx = variantCounter++;
    const container = document.getElementById('variantsContainer');
    document.getElementById('noVariantsMsg').classList.add('d-none');
    
    const div = document.createElement('div');
    div.className = 'variant-card';
    div.innerHTML = `
        <input type="hidden" name="variants[${idx}][id]" value="new_${idx}">
        <div class="variant-header">
            <div class="variant-img-wrapper" onclick="this.querySelector('input').click()">
                <i class="fas fa-camera fa-2x text-muted"></i>
                <input type="file" name="variants[${idx}][color_image]" class="d-none" onchange="previewVariantImage(this)">
            </div>
            <div class="flex-grow-1">
                <h4 class="fw-bold mb-1" style="color: #667eea;"><?php echo e(__('New Variation')); ?></h4>
                <p class="text-muted mb-0"><?php echo e(__('Configuration for this specific item')); ?></p>
            </div>
            <button type="button" class="btn btn-danger btn-lg rounded-pill" onclick="this.closest('.variant-card').remove(); checkVariantsEmpty();">
                <i class="fas fa-trash me-2"></i> <?php echo e(__('Remove')); ?>

            </button>
        </div>

        <div class="variant-grid-container">
            <div class="variant-input-group">
                <label class="variant-label-xl"><?php echo e(__('Size / Name')); ?></label>
                <input type="text" name="variants[${idx}][size]" class="variant-input-xl" placeholder="e.g. XL, Red, 42...">
            </div>
            <div class="variant-input-group">
                <label class="variant-label-xl"><?php echo e(__('Grouping Key')); ?></label>
                <input type="text" name="variants[${idx}][style_key]" class="variant-input-xl" placeholder="e.g. Set A">
            </div>
            <div class="variant-input-group">
                <label class="variant-label-xl"><?php echo e(__('Regular Price')); ?> (DH)</label>
                <input type="number" name="variants[${idx}][price]" class="variant-input-xl" step="0.01">
            </div>
            <div class="col-md-4">
                <label class="variant-label-xl text-danger"><?php echo e(__('Sale Price')); ?> (DH)</label>
                <input type="number" name="variants[${idx}][sale_price]" class="variant-input-xl border-danger-subtle text-danger" step="0.01">
            </div>
            <div class="col-md-4">
                <label class="variant-label-xl text-success"><?php echo e(__('Stock Quantity')); ?></label>
                <input type="number" name="variants[${idx}][stock]" class="variant-input-xl" value="0">
            </div>
        </div>
        <input type="hidden" name="variants[${idx}][sku]" value="V-${Date.now()}-${idx}">
    `;
    container.appendChild(div);
}

function previewVariantImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrap = input.closest('.variant-img-wrapper');
            wrap.innerHTML = `<img src="${e.target.result}"><input type="file" name="${input.name}" class="d-none" onchange="previewVariantImage(this)">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function checkVariantsEmpty() {
    const container = document.getElementById('variantsContainer');
    if (container.children.length === 0) {
        document.getElementById('noVariantsMsg').classList.remove('d-none');
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/products/edit.blade.php ENDPATH**/ ?>