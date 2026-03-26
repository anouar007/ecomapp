<tr>
    <td>
        <div class="d-flex align-items-center gap-3" style="<?php echo e($level > 0 ? 'padding-left: ' . ($level * 40) . 'px;' : ''); ?>">
            <?php if($level > 0): ?>
                <span class="text-muted fw-bold" style="opacity: 0.5;">└─</span>
            <?php endif; ?>
            <div class="brand-avatar">
                <?php if($category->icon): ?>
                    <i class="<?php echo e($category->icon); ?>"></i>
                <?php elseif($category->image): ?>
                    <img src="<?php echo e(asset('storage/' . $category->image)); ?>" alt="">
                <?php else: ?>
                    <i class="fas fa-folder"></i>
                <?php endif; ?>
            </div>
            <div>
                <div class="fw-bold text-dark"><?php echo e($category->translated_name); ?></div>
                <?php if($category->translated_description): ?>
                    <div class="text-muted small" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <?php echo e($category->translated_description); ?>

                    </div>
                <?php endif; ?>
                <?php if($category->hasChildren()): ?>
                    <div class="mt-1" style="font-size: 0.65rem; color: var(--primary-color); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                        <i class="fas fa-sitemap me-1"></i> <?php echo e($category->children->count()); ?> <?php echo e(__('subcategories')); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </td>
    <td>
        <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.7rem; border: 1px solid #e2e8f0;">
            <?php echo e($category->slug); ?>

        </span>
    </td>
    <td class="text-center">
        <span class="brand-badge info">
            <i class="fas fa-box me-1"></i> <?php echo e($category->products_count); ?> <?php echo e(__('Products')); ?>

        </span>
    </td>
    <td class="text-center">
        <span class="brand-badge <?php echo e($category->status === 'active' ? 'success' : 'secondary'); ?>">
            <?php echo e(__(ucfirst($category->status))); ?>

        </span>
    </td>
    <td style="padding-right: 1.5rem;">
        <div class="d-flex justify-content-end gap-2">
            <a href="<?php echo e(route('categories.edit', $category)); ?>" class="btn-action-icon" title="<?php echo e(__('Edit Category')); ?>">
                <i class="fas fa-edit"></i>
            </a>
            <?php if($category->products_count == 0 && !$category->hasChildren()): ?>
                <form method="POST"
                      action="<?php echo e(route('categories.destroy', $category->id)); ?>"
                      style="display: inline;"
                      data-confirm-delete="true"
                      data-item-type="category"
                      data-item-name="<?php echo e($category->translated_name); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-action-icon danger" title="<?php echo e(__('Delete')); ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/categories/partials/category-row.blade.php ENDPATH**/ ?>