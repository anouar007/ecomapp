<?php $__env->startSection('title', __('Custom Code Manager')); ?>

<?php $__env->startSection('content'); ?>


<?php $__env->startSection('title', 'Custom Code Manager'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-code"></i>
                </div>
                <?php echo e(__('Custom Code Manager')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Manage custom CSS, JS, and HTML snippets')); ?></p>
        </div>
        <a href="<?php echo e(route('custom-codes.create')); ?>" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> <?php echo e(__('Add New Snippet')); ?>

        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Codes Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;"><?php echo e(__('Snippet Details')); ?></th>
                        <th><?php echo e(__('Type')); ?></th>
                        <th><?php echo e(__('Position')); ?></th>
                        <th><?php echo e(__('Priority')); ?></th>
                        <th><?php echo e(__('Status')); ?></th>
                        <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <div class="fw-bold text-dark"><?php echo e($code->title); ?></div>
                            <div class="text-muted small">Updated <?php echo e($code->updated_at->diffForHumans()); ?></div>
                        </td>
                        <td>
                            <?php
                                $typeColor = match($code->type) {
                                    'css' => 'info',
                                    'js' => 'warning',
                                    'html' => 'secondary',
                                    default => 'primary'
                                };
                            ?>
                            <span class="brand-badge <?php echo e($typeColor); ?>">
                                <?php echo e(strtoupper($code->type)); ?>

                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo e(ucwords(str_replace('_', ' ', $code->position))); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary font-monospace" style="border: 1px solid #e2e8f0;">
                                <?php echo e($code->priority); ?>

                            </span>
                        </td>
                        <td>
                            <span class="brand-badge <?php echo e($code->is_active ? 'success' : 'danger'); ?>">
                                <?php echo e($code->is_active ? __('Active') : __('Inactive')); ?>

                            </span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('custom-codes.edit', $code)); ?>" class="btn-action-icon" title="<?php echo e(__('Edit Snippet')); ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('custom-codes.destroy', $code)); ?>" method="POST" class="d-inline delete-form">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-action-icon danger" onclick="return confirm('<?php echo e(__('Are you sure you want to delete this snippet?')); ?>')" title="<?php echo e(__('Delete Snippet')); ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-code"></i>
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo e(__('No custom codes found')); ?></h5>
                                <p class="text-muted"><?php echo e(__('Start by adding your first custom snippet.')); ?></p>
                                <a href="<?php echo e(route('custom-codes.create')); ?>" class="btn-brand-primary mt-3">
                                    <?php echo e(__('Add New Snippet')); ?>

                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($codes->hasPages()): ?>
        <div class="px-4 py-3 border-top">
            <?php echo e($codes->links()); ?>

        </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/custom-codes/index.blade.php ENDPATH**/ ?>