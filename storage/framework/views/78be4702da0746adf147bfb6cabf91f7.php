<?php $__env->startSection('title', 'Coupon Details'); ?>

<?php $__env->startSection('content'); ?>
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <?php echo e($coupon->code); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e($coupon->name); ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('coupons.index')); ?>" class="btn-brand-light">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
            <a href="<?php echo e(route('coupons.edit', $coupon)); ?>" class="btn-brand-primary">
                <i class="fas fa-edit me-2"></i> Edit Coupon
            </a>
            <form action="<?php echo e(route('coupons.destroy', $coupon)); ?>" method="POST" onsubmit="return confirm('Are you sure?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger h-100">
                    <i class="fas fa-trash-alt me-2"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">Coupon Information</h5>
                </div>
                <div class="brand-card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Type</div>
                            <span class="badge bg-primary text-uppercase"><?php echo e(str_replace('_', ' ', $coupon->type)); ?></span>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Value</div>
                            <div class="fs-5 fw-bold text-dark">
                                <?php if($coupon->type == 'percentage'): ?>
                                    <?php echo e($coupon->value); ?>%
                                <?php elseif($coupon->type == 'fixed'): ?>
                                    <?php echo e(currency($coupon->value)); ?>

                                <?php elseif($coupon->type == 'free_shipping'): ?>
                                    Free Shipping
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
                            <span class="badge bg-<?php echo e($coupon->status == 'active' ? 'success' : 'secondary'); ?> text-uppercase">
                                <?php echo e($coupon->status); ?>

                            </span>
                        </div>
                    </div>

                    <?php if($coupon->description): ?>
                    <div class="mb-4">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Description</div>
                        <p class="text-dark"><?php echo e($coupon->description); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Validity Period</div>
                            <div class="text-dark">
                                <?php if($coupon->valid_from): ?>
                                    From: <?php echo e($coupon->valid_from->format('M d, Y')); ?><br>
                                <?php endif; ?>
                                <?php if($coupon->valid_to): ?>
                                    To: <?php echo e($coupon->valid_to->format('M d, Y')); ?>

                                <?php else: ?>
                                    No expiration
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Usage Limits</div>
                            <div class="text-dark">
                                Total: <?php echo e($coupon->usage_limit ?: 'Unlimited'); ?><br>
                                Per Customer: <?php echo e($coupon->per_customer_limit ?: 'Unlimited'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            <div class="brand-card">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">Recent Usage</h5>
                </div>
                <div class="brand-card-body p-0">
                    <div class="table-responsive">
                        <table class="brand-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $coupon->usages()->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($usage->created_at->format('M d, Y H:i')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('orders.show', $usage->order_id)); ?>" class="fw-bold">
                                            #<?php echo e($usage->order_id); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e(currency($usage->discount_amount)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No usage history yet.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">Statistics</h5>
                </div>
                <div class="brand-card-body">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Times Used</span>
                        <span class="fw-bold fs-5"><?php echo e($coupon->usage_count); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Discounted</span>
                        <span class="fw-bold fs-5 text-success"><?php echo e(currency($coupon->usages()->sum('discount_amount'))); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/coupons/show.blade.php ENDPATH**/ ?>