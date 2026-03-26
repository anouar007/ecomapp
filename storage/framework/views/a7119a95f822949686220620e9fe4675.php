<?php $__env->startSection('title', __('Inventory Management')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <?php echo e(__('Inventory Management')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Monitor stock levels, track sales velocity, and manage reorder points')); ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('inventory.alerts')); ?>" class="btn btn-brand-light font-inter">
                <i class="fas fa-bell me-1" style="color: var(--warning-color)"></i> <?php echo e(__('Stock Alerts')); ?>

            </a>
            <a href="<?php echo e(route('inventory.movements')); ?>" class="btn btn-brand-light font-inter">
                <i class="fas fa-history me-1" style="color: var(--primary-color)"></i> <?php echo e(__('Movements')); ?>

            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid mb-4">
        <div class="brand-stat-card shadow-soft">
            <div class="brand-stat-icon primary">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Total Products')); ?></div>
            <div class="brand-stat-value font-inter text-primary"><?php echo e(number_format($stats['total_products'])); ?></div>
        </div>
        
        <div class="brand-stat-card shadow-soft">
            <div class="brand-stat-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Low Stock')); ?></div>
            <div class="brand-stat-value font-inter text-warning"><?php echo e(number_format($stats['low_stock'])); ?></div>
        </div>
        
        <div class="brand-stat-card shadow-soft">
            <div class="brand-stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Out of Stock')); ?></div>
            <div class="brand-stat-value font-inter text-danger"><?php echo e(number_format($stats['out_of_stock'])); ?></div>
        </div>
        
        <div class="brand-stat-card shadow-soft">
            <div class="brand-stat-icon success">
                <i class="fas fa-coins"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Stock Value')); ?></div>
            <div class="brand-stat-value font-inter text-success"><?php echo e(currency($stats['total_stock_value'])); ?></div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="brand-filter-bar px-3 py-3">
        <!-- Mobile Toggle -->
        <div class="d-lg-none mb-2">
            <button class="btn btn-brand-light w-100 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#mobileInventoryFilters" aria-expanded="false" aria-controls="mobileInventoryFilters">
                <span><i class="fas fa-filter me-2" style="color: var(--primary-color)"></i> <?php echo e(__('Filters & Search')); ?></span>
                <i class="fas fa-chevron-down opacity-50"></i>
            </button>
        </div>
        
        <div class="collapse d-lg-block" id="mobileInventoryFilters">
            <form method="GET" action="<?php echo e(route('inventory.index')); ?>" class="row g-2 align-items-end">
                <div class="col-12 col-lg-5">
                    <div class="brand-search-wrapper w-100">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control font-inter" 
                               placeholder="<?php echo e(__('Search product or SKU...')); ?>"
                               value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                
                <div class="col-6 col-lg-3">
                    <select name="category_id" class="form-select custom-select-premium font-inter">
                        <option value=""><?php echo e(__('Categories')); ?></option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->translated_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <select name="stock_status" class="form-select custom-select-premium font-inter">
                        <option value=""><?php echo e(__('Status')); ?></option>
                        <option value="in_stock" <?php echo e(request('stock_status') == 'in_stock' ? 'selected' : ''); ?>><?php echo e(__('In Stock')); ?></option>
                        <option value="low_stock" <?php echo e(request('stock_status') == 'low_stock' ? 'selected' : ''); ?>><?php echo e(__('Low')); ?></option>
                        <option value="out_of_stock" <?php echo e(request('stock_status') == 'out_of_stock' ? 'selected' : ''); ?>><?php echo e(__('Out')); ?></option>
                    </select>
                </div>
                
                <div class="col-12 col-lg-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand-primary flex-grow-1">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="<?php echo e(route('inventory.index')); ?>" class="btn btn-brand-light px-3">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Inventory Cards -->
    <div class="d-lg-none mt-3 px-1">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="glass-card mb-3 p-3 border-0 shadow-soft product-container" data-product-id="<?php echo e($product->id); ?>">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="brand-avatar" style="width: 50px; height: 50px; border-radius: 12px; background: #f1f5f9;">
                    <?php if($product->image): ?>
                        <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    <?php else: ?>
                        <i class="fas fa-box text-muted"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <h6 class="mb-0 fw-bold text-dark text-truncate"><?php echo e($product->translated_name); ?></h6>
                    <div class="text-muted small font-inter" style="font-size: 0.7rem;"><?php echo e($product->sku ?? __('NO-SKU')); ?></div>
                </div>
                <div class="text-end">
                    <?php if($product->track_inventory): ?>
                        <div class="fw-bold font-inter fs-5 product-total-stock-display"><?php echo e($product->total_stock); ?></div>
                        <div class="text-muted small" style="font-size: 0.65rem;"><?php echo e(__('TOTAL')); ?></div>
                    <?php else: ?>
                        <span class="badge bg-light text-muted"><?php echo e(__('N/A')); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($product->track_inventory): ?>
                <?php if($product->variants->count() > 0): ?>
                    <div class="variant-mobile-list mt-3">
                        <p class="text-uppercase small fw-bold text-muted mb-2" style="font-size: 0.6rem; letter-spacing: 0.5px;"><?php echo e(__('Product Options')); ?></p>
                        <?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="variant-card mb-2 d-flex align-items-center justify-content-between variant-container" 
                                 data-product-id="<?php echo e($product->id); ?>" 
                                 data-variant-id="<?php echo e($variant->id); ?>">
                                <div class="d-flex align-items-center gap-2 variant-info-wrapper">
                                    <?php if($variant->size): ?> <span class="variant-tag"><?php echo e($variant->size); ?></span> <?php endif; ?>
                                    <?php if($variant->color): ?> 
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="color-dot shadow-sm" style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo e($variant->color); ?>"></span>
                                            <span class="small text-muted text-truncate" style="max-width: 80px;"><?php echo e($variant->color_name); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center gap-2 variant-actions-wrapper">
                                    <button type="button" class="quick-adj-btn btn-minus" onclick="quickUpdate('<?php echo e($product->id); ?>', '<?php echo e($variant->id); ?>', -1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="stock-pill font-inter variant-stock-display <?php echo e(($variant->stock ?? 0) <= 0 ? 'out' : (($variant->stock ?? 0) <= 5 ? 'low' : 'in')); ?>">
                                        <?php echo e($variant->stock ?? 0); ?>

                                    </span>
                                    <button type="button" class="quick-adj-btn btn-plus" onclick="quickUpdate('<?php echo e($product->id); ?>', '<?php echo e($variant->id); ?>', 1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: #f8fafc;">
                        <span class="small fw-semibold text-muted"><?php echo e(__('Direct Stock Management')); ?></span>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="quick-adj-btn btn-minus" onclick="quickUpdate('<?php echo e($product->id); ?>', null, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="stock-pill font-inter <?php echo e(($product->stock ?? 0) <= 0 ? 'out' : (($product->stock ?? 0) <= 5 ? 'low' : 'in')); ?> product-stock-display">
                                <?php echo e($product->stock ?? 0); ?>

                            </span>
                            <button type="button" class="quick-adj-btn btn-plus" onclick="quickUpdate('<?php echo e($product->id); ?>', null, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="d-flex justify-content-end align-items-center mt-3 pt-2 border-top">
                <div class="d-flex gap-2">
                    <?php if($product->track_inventory): ?>
                    <button class="btn btn-sm btn-brand-light rounded-pill px-3" onclick="openAdjustModal('<?php echo e($product->id); ?>', '<?php echo e(addslashes($product->translated_name)); ?>', <?php echo e($product->stock ?? 0); ?>)">
                        <i class="fas fa-sliders-h me-1"></i> <?php echo e(__('Manual')); ?>

                    </button>
                    <?php endif; ?>
                    <a href="<?php echo e(route('inventory.movements', ['product_id' => $product->id])); ?>" class="btn btn-sm btn-brand-light rounded-pill px-3">
                        <i class="fas fa-history me-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="glass-card p-5 text-center">
                <i class="fas fa-boxes opacity-25 mb-3" style="font-size: 48px;"></i>
                <h5 class="fw-bold"><?php echo e(__('No inventory items')); ?></h5>
            </div>
        <?php endif; ?>
    </div>

    <!-- Inventory Table (Desktop Only) -->
    <div class="brand-table-card d-none d-lg-block mt-4 overflow-hidden">
        <div class="table-responsive" style="max-height: 70vh;">
            <table class="brand-table mb-0">
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 50px; padding-left: 1.5rem;"></th>
                        <th><?php echo e(__('Product Details')); ?></th>
                        <th class="text-center"><?php echo e(__('Stock Level & Quick Actions')); ?></th>
                        <th class="text-center"><?php echo e(__('30d Vol')); ?></th>
                        <th class="text-center"><?php echo e(__('Value')); ?></th>
                        <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="product-container" data-product-id="<?php echo e($product->id); ?>">
                        <td style="padding-left: 1.5rem;">
                            <?php if($product->variants->count() > 0): ?>
                                <button type="button" class="inventory-expand-btn" data-product-id="<?php echo e($product->id); ?>" onclick="toggleVariants('<?php echo e($product->id); ?>', this)">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="brand-avatar shadow-xs" style="width: 48px; height: 48px;">
                                    <?php if($product->image): ?>
                                        <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="">
                                    <?php else: ?>
                                        <i class="fas fa-box opacity-50"></i>
                                    <?php endif; ?>
                                </div>
                                <div style="max-width: 250px;">
                                    <div class="fw-bold text-dark text-truncate"><?php echo e($product->translated_name); ?></div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.65rem;"><?php echo e($product->sku ?? __('NO-SKU')); ?></span>
                                        <?php if($product->variants->count() > 0): ?>
                                            <span class="badge bg-soft-primary text-primary" style="font-size: 0.6rem;"><?php echo e($product->variants->count()); ?> <?php echo e(__('Variants')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-center">
                                <?php if($product->track_inventory): ?>
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <?php if($product->variants->count() == 0): ?>
                                            <button type="button" class="quick-adj-btn btn-minus" onclick="quickUpdate('<?php echo e($product->id); ?>', null, -1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <div class="text-center">
                                            <div class="fw-bold fs-4 font-inter product-total-stock-display <?php echo e(($product->total_stock <= ($product->low_stock_threshold ?? 10)) ? 'text-danger' : 'text-dark'); ?>">
                                                <?php echo e($product->total_stock); ?>

                                            </div>
                                            <div class="text-muted small text-uppercase" style="font-size: 0.6rem; letter-spacing: 1px;"><?php echo e(__('Total Units')); ?></div>
                                        </div>

                                        <?php if($product->variants->count() == 0): ?>
                                            <button type="button" class="quick-adj-btn btn-plus" onclick="quickUpdate('<?php echo e($product->id); ?>', null, 1)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php
                                        $stock = $product->total_stock;
                                        $threshold = $product->low_stock_threshold ?? 10;
                                        $percent = min(100, $stock > 0 ? ($stock / ($threshold * 4)) * 100 : 0);
                                        $barColor = $stock <= 0 ? '#ef4444' : ($stock <= $threshold ? '#f59e0b' : '#10b981');
                                    ?>
                                    <div class="progress" style="height: 4px; width: 120px; border-radius: 10px; background: #f1f5f9;">
                                        <div class="progress-bar product-stock-progress-bar" role="progressbar" 
                                             style="width: <?php echo e($percent); ?>%; background-color: <?php echo e($barColor); ?>;"></div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small italic"><?php echo e(__('Inventory not tracked')); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <?php if($product->sold_last_30_days > 0): ?>
                                <div class="fw-bold font-inter"><?php echo e(number_format($product->sold_last_30_days)); ?></div>
                                <div class="text-muted small"><?php echo e(__('units/mo')); ?></div>
                            <?php else: ?>
                                <span class="text-muted opacity-25">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($product->track_inventory && $product->cost_price): ?>
                                <div class="fw-bold text-dark font-inter"><?php echo e(currency($product->total_stock * $product->cost_price)); ?></div>
                                <div class="text-muted small"><?php echo e(currency($product->cost_price)); ?> <?php echo e(__('avg cost')); ?></div>
                            <?php else: ?>
                                <span class="text-muted opacity-25">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn-action-icon" 
                                        onclick="openAdjustModal('<?php echo e($product->id); ?>', '<?php echo e(addslashes($product->translated_name)); ?>', <?php echo e($product->total_stock); ?>)"
                                        title="<?php echo e(__('Manual Adjustment')); ?>">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <a href="<?php echo e(route('inventory.movements', ['product_id' => $product->id])); ?>" 
                                   class="btn-action-icon" title="<?php echo e(__('History')); ?>">
                                    <i class="fas fa-history"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <?php if($product->variants->count() > 0): ?>
                    <tr class="variant-row" id="variants-<?php echo e($product->id); ?>">
                        <td colspan="6" class="p-0 border-0">
                            <div class="px-5 py-3" style="background: rgba(99, 102, 241, 0.02);">
                                <div class="row g-3">
                                    <?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-4 col-xl-3">
                                            <div class="variant-card d-flex align-items-center justify-content-between variant-container" 
                                                 data-product-id="<?php echo e($product->id); ?>" 
                                                 data-variant-id="<?php echo e($variant->id); ?>">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <?php if($variant->size): ?> <span class="variant-tag"><?php echo e($variant->size); ?></span> <?php endif; ?>
                                                        <?php if($variant->color): ?> 
                                                            <span class="color-dot shadow-inner" style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo e($variant->color); ?>"></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-muted font-monospace" style="font-size: 0.65rem;"><?php echo e($variant->sku ?? ($product->sku . '-' . ($variant->size ?: $variant->color))); ?></div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="quick-adj-btn btn-minus" onclick="quickUpdate('<?php echo e($product->id); ?>', '<?php echo e($variant->id); ?>', -1)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="stock-pill font-inter <?php echo e(($variant->stock ?? 0) <= 0 ? 'out' : (($variant->stock ?? 0) <= 5 ? 'low' : 'in')); ?> variant-stock-display">
                                                        <?php echo e($variant->stock ?? 0); ?>

                                                    </span>
                                                    <button type="button" class="quick-adj-btn btn-plus" onclick="quickUpdate('<?php echo e($product->id); ?>', '<?php echo e($variant->id); ?>', 1)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo e(__('No products found')); ?></h5>
                                <p class="text-muted"><?php echo e(__('Try adjusting your search or filter criteria')); ?></p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($products->hasPages()): ?>
        <div class="px-4 py-3 border-top">
            <?php echo e($products->links()); ?>

        </div>
        <?php endif; ?>
    </div>

<!-- Quick Adjust Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="adjustStockForm" method="POST" action="">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="variant_id" id="modalVariantId">
            <div class="modal-content glass-card shadow-lg" style="border: none;">
                <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0;">
                    <h5 class="modal-title fw-bold text-dark"><?php echo e(__('Manual Adjustment')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="p-3 mb-4 d-flex align-items-center gap-3" style="background: #f0f9ff; border-radius: var(--radius-lg);">
                        <div class="brand-avatar" style="background: var(--primary-color); color: white;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark" id="modalProductName" style="font-size: 1rem;"></div>
                            <div id="modalVariantName" class="badge bg-soft-primary text-primary mt-1" style="display: none;"></div>
                            <div class="text-primary small fw-semibold mt-1">
                                <?php echo e(__('Current Level:')); ?> <span id="modalCurrentStock" class="font-inter"></span> <?php echo e(__('units')); ?>

                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted"><?php echo e(__('Adjustment Type')); ?></label>
                        <select name="adjustment_type" class="form-select custom-select-premium" required onchange="updateReasonPlaceholder(this.value)">
                            <option value="in">➕ <?php echo e(__('Stock In')); ?></option>
                            <option value="out">➖ <?php echo e(__('Stock Out')); ?></option>
                            <option value="adjustment">🔄 <?php echo e(__('Correct Absolute Value')); ?></option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted"><?php echo e(__('Quantity')); ?></label>
                        <input type="number" name="quantity" class="form-control brand-input font-inter" required min="0" placeholder="0">
                        <div class="form-text mt-1 small opacity-75" id="quantityHelp"><?php echo e(__('Units to ADD to current stock.')); ?></div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-uppercase text-muted"><?php echo e(__('Note / Reason')); ?></label>
                        <textarea name="reason" class="form-control brand-input" rows="2" required placeholder="<?php echo e(__('e.g., Weekly restocking')); ?>"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding: 0 1.5rem 1.5rem;">
                    <button type="submit" class="btn btn-brand-primary w-100 py-3 fw-bold"><?php echo e(__('Apply Adjustment')); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function toggleVariants(productId, badge) {
        const row = document.getElementById('variants-' + productId);
        if (row.classList.contains('show')) {
            row.classList.remove('show');
            badge.classList.remove('active');
        } else {
            row.classList.add('show');
            badge.classList.add('active');
        }
    }

    function openAdjustModal(productId, productName, currentStock, variantId = null, variantName = null) {
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalCurrentStock').textContent = currentStock;
        document.getElementById('modalVariantId').value = variantId;
        
        const vn = document.getElementById('modalVariantName');
        if (variantName) {
            vn.textContent = variantName;
            vn.style.display = 'inline-block';
        } else {
            vn.style.display = 'none';
        }
        
        const form = document.getElementById('adjustStockForm');
        form.action = `/inventory/${productId}/adjust`;
        
        new bootstrap.Modal(document.getElementById('adjustStockModal')).show();
    }

    function updateReasonPlaceholder(type) {
        const textarea = document.querySelector('textarea[name="reason"]');
        const quantityHelp = document.getElementById('quantityHelp');
        
        switch(type) {
            case 'in':
                textarea.placeholder = "<?php echo e(__('e.g., Restocked from supplier')); ?>";
                quantityHelp.textContent = "<?php echo e(__('Units to ADD to current stock.')); ?>";
                break;
            case 'out':
                textarea.placeholder = "<?php echo e(__('e.g., Damaged item, expired')); ?>";
                quantityHelp.textContent = "<?php echo e(__('Units to REMOVE from current stock.')); ?>";
                break;
            case 'adjustment':
                textarea.placeholder = "<?php echo e(__('e.g., Physical audit correction')); ?>";
                quantityHelp.textContent = "<?php echo e(__('The NEW absolute total quantity for this item.')); ?>";
                break;
        }
    }

    // AJAX Quick Update
    async function quickUpdate(productId, variantId, change) {
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch("<?php echo e(route('inventory.quick-update')); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    change: change
                })
            });

            const data = await response.json();

            if (data.success) {
                // Find all containers that might need updating
                const selectors = variantId 
                    ? `.variant-container[data-variant-id="${variantId}"]`
                    : `.product-container[data-product-id="${productId}"]`;
                
                document.querySelectorAll(selectors).forEach(container => {
                    const display = container.querySelector(variantId ? '.variant-stock-display' : '.product-stock-display');
                    if (display) {
                        display.textContent = data.new_stock;
                        // Update color based on level
                        display.classList.remove('in', 'low', 'out');
                        if (data.new_stock <= 0) display.classList.add('out');
                        else if (data.new_stock <= 5) display.classList.add('low');
                        else display.classList.add('in');
                    }
                });

                // Always update the total stock display for the product
                document.querySelectorAll(`.product-container[data-product-id="${productId}"]`).forEach(container => {
                    const totalDisp = container.querySelector('.product-total-stock-display');
                    if (totalDisp) {
                        totalDisp.textContent = data.total_stock;
                        totalDisp.classList.toggle('text-danger', data.total_stock <= 5);
                    }
                    
                    const progressBar = container.querySelector('.product-stock-progress-bar');
                    if (progressBar) {
                        // Estimate percentage (simple logic for feedback)
                        const percent = Math.min(100, (data.total_stock / 40) * 100);
                        progressBar.style.width = percent + '%';
                        progressBar.style.backgroundColor = data.total_stock <= 0 ? '#ef4444' : (data.total_stock <= 10 ? '#f59e0b' : '#10b981');
                    }
                });
                
                // Success feedback
                const toast = document.createElement('div');
                toast.className = 'toast align-items-center text-white bg-success border-0 show position-fixed bottom-0 end-0 m-3';
                toast.style.zIndex = '9999';
                toast.innerHTML = `<div class="d-flex"><div class="toast-body"><i class="fas fa-check-circle me-2"></i> ${"<?php echo e(__('Stock updated successfully')); ?>"}</div></div>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);

            } else {
                alert(data.message || "<?php echo e(__('Update failed')); ?>");
            }
        } catch (error) {
            console.error(error);
            alert("<?php echo e(__('Something went wrong')); ?>");
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/inventory/index.blade.php ENDPATH**/ ?>