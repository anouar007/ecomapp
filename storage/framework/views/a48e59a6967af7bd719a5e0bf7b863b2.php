<?php $__env->startSection('title', __('Page Management')); ?>

<?php $__env->startSection('content'); ?>
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <?php echo e(__('Page Management')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Create and manage your client-facing website pages')); ?></p>
        </div>
        <a href="<?php echo e(route('pages.create')); ?>" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> <?php echo e(__('Create New Page')); ?>

        </a>
    </div>

    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;"><?php echo e(__('Page Title')); ?></th>
                        <th><?php echo e(__('Slug')); ?></th>
                        <th class="text-center"><?php echo e(__('Status')); ?></th>
                        <th><?php echo e(__('Last Updated')); ?></th>
                        <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <div class="fw-bold text-dark"><?php echo e($page->title); ?></div>
                            <div class="text-muted small"><?php echo e(__('Layout:')); ?> <?php echo e(ucfirst($page->layout)); ?></div>
                        </td>
                        <td>
                            <a href="<?php echo e(url($page->slug)); ?>" target="_blank" class="text-primary text-decoration-none">
                                /<?php echo e($page->slug); ?> <i class="fas fa-external-link-alt ms-1 small"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="brand-badge <?php echo e($page->is_published ? 'success' : 'warning'); ?>">
                                <?php echo e($page->is_published ? __('Published') : __('Draft')); ?>

                            </span>
                        </td>
                        <td>
                            <div class="text-muted small"><?php echo e($page->updated_at->format('M d, Y H:i')); ?></div>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('pages.edit', $page)); ?>" class="btn-action-icon" title="Edit Page">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('pages.destroy', $page)); ?>" 
                                      style="display: inline;"
                                      data-confirm-delete="true"
                                      data-item-type="page"
                                      data-item-name="<?php echo e($page->title); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-action-icon danger" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-file-code text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo e(__('No pages created yet')); ?></h5>
                                <p class="text-muted"><?php echo e(__('Start by creating your first client-facing page.')); ?></p>
                                <a href="<?php echo e(route('pages.create')); ?>" class="btn-brand-primary mt-2">
                                    <?php echo e(__('Create First Page')); ?>

                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($pages->hasPages()): ?>
        <div class="px-4 py-3 border-top">
            <?php echo e($pages->links()); ?>

        </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/pages/index.blade.php ENDPATH**/ ?>