<div class="glass-card mb-3 p-3 position-relative border-0 shadow-soft" 
     style="<?php echo e($level > 0 ? 'margin-' . (app()->getLocale() == 'ar' ? 'right' : 'left') . ': ' . ($level * 20) . 'px;' : ''); ?>">
    
    <?php if($level > 0): ?>
        <div class="position-absolute" style="<?php echo e(app()->getLocale() == 'ar' ? 'right: -15px' : 'left: -15px'); ?>; top: 20px; color: var(--primary-light); opacity: 0.5;">
            <i class="fas fa-level-down-alt fa-flip-horizontal"></i>
        </div>
    <?php endif; ?>

    <div class="d-flex align-items-center gap-3">
        <div class="brand-avatar" style="width: 50px; height: 50px; flex-shrink: 0; border-radius: 12px; background: <?php echo e($level == 0 ? 'var(--gradient-primary)' : '#f1f5f9'); ?>; color: <?php echo e($level == 0 ? 'white' : 'var(--text-dark)'); ?>">
            <?php if($category->icon): ?>
                <i class="<?php echo e($category->icon); ?>"></i>
            <?php elseif($category->image): ?>
                <img src="<?php echo e(asset('storage/' . $category->image)); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
            <?php else: ?>
                <i class="fas fa-folder"></i>
            <?php endif; ?>
        </div>

        <div class="flex-grow-1 min-width-0">
            <h6 class="mb-0 fw-bold text-dark text-truncate"><?php echo e($category->translated_name); ?></h6>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-light text-muted font-inter px-2" style="font-size: 0.65rem; border: 1px solid #e2e8f0;">
                    <?php echo e($category->slug); ?>

                </span>
                <span class="brand-badge <?php echo e($category->status === 'active' ? 'success' : 'secondary'); ?>" style="font-size: 0.6rem; padding: 2px 8px;">
                    <?php echo e(__(ucfirst($category->status))); ?>

                </span>
            </div>
        </div>

        <div class="dropdown">
            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px;">
                <li>
                    <a class="dropdown-item py-2" href="<?php echo e(route('categories.edit', $category)); ?>">
                        <i class="fas fa-edit me-2 text-primary"></i> <?php echo e(__('Edit')); ?>

                    </a>
                </li>
                <?php if($category->products_count == 0 && !$category->hasChildren()): ?>
                <li>
                    <form method="POST" action="<?php echo e(route('categories.destroy', $category->id)); ?>" class="d-inline" data-confirm-delete="true">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="fas fa-trash me-2"></i> <?php echo e(__('Delete')); ?>

                        </button>
                    </form>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top" style="border-top-style: dashed !important; border-top-color: #e2e8f0 !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="text-muted" style="font-size: 0.75rem;">
                <i class="fas fa-box me-1 opacity-50"></i> 
                <span class="fw-bold font-inter text-dark"><?php echo e($category->products_count); ?></span> <?php echo e(__('Products')); ?>

            </div>
            
            <?php if($category->hasChildren()): ?>
                <div class="text-primary" style="font-size: 0.75rem; font-weight: 700;">
                    <i class="fas fa-sitemap me-1 opacity-50"></i> 
                    <span class="font-inter"><?php echo e($category->children->count()); ?></span> <?php echo e(__('Subcategories')); ?>

                </div>
            <?php endif; ?>
        </div>
        
        <a href="<?php echo e(route('categories.edit', $category)); ?>" class="btn btn-sm btn-brand-light rounded-pill px-3" style="font-size: 0.7rem;">
            <?php echo e(__('View Details')); ?>

        </a>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/categories/partials/category-card.blade.php ENDPATH**/ ?>