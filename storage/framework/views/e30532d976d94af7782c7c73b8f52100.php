<?php $__env->startSection('title', __('Coupons')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-ticket-alt"></i> <?php echo e(__('Coupons & Discounts')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('Manage promotional codes and discount campaigns')); ?></p>
        </div>
        <a href="<?php echo e(route('coupons.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> <?php echo e(__('Create Coupon')); ?>

        </a>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-ticket-alt" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Total Coupons')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;"><?php echo e(number_format($stats['total_coupons'])); ?></p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #d1fae5 100%); border-radius: 16px; padding: 24px; border: 1px solid #a7f3d0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #166534; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Active Coupons')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;"><?php echo e(number_format($stats['active_coupons'])); ?></p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #ddd6fe 100%); border-radius: 16px; padding: 24px; border: 1px solid #c4b5fd;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #5b21b6; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Total Usage')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #6d28d9; margin: 0;"><?php echo e(number_format($stats['total_usage'])); ?></p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%); border-radius: 16px; padding: 24px; border: 1px solid #fde68a;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-dollar-sign" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #92400e; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Total Savings')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #b45309; margin: 0;"><?php echo e(currency($stats['total_savings'])); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> <?php echo e(__('Filters')); ?></h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('coupons.index')); ?>" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" style="flex: 1; min-width: 200px;" 
                   placeholder="<?php echo e(__('Search coupons...')); ?>" value="<?php echo e(request('search')); ?>">
            
            <select name="status" class="form-control" style="width: auto; min-width: 150px;">
                <option value=""><?php echo e(__('All Status')); ?></option>
                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>><?php echo e(__('Active')); ?></option>
                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('Inactive')); ?></option>
                <option value="expired" <?php echo e(request('status') == 'expired' ? 'selected' : ''); ?>><?php echo e(__('Expired')); ?></option>
            </select>
            
            <select name="type" class="form-control" style="width: auto; min-width: 150px;">
                <option value=""><?php echo e(__('All Types')); ?></option>
                <option value="percentage" <?php echo e(request('type') == 'percentage' ? 'selected' : ''); ?>><?php echo e(__('Percentage')); ?></option>
                <option value="fixed" <?php echo e(request('type') == 'fixed' ? 'selected' : ''); ?>><?php echo e(__('Fixed Amount')); ?></option>
                <option value="free_shipping" <?php echo e(request('type') == 'free_shipping' ? 'selected' : ''); ?>><?php echo e(__('Free Shipping')); ?></option>
                <option value="buy_x_get_y" <?php echo e(request('type') == 'buy_x_get_y' ? 'selected' : ''); ?>><?php echo e(__('Buy X Get Y')); ?></option>
            </select>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> <?php echo e(__('Filter')); ?></button>
            <a href="<?php echo e(route('coupons.index')); ?>" class="btn btn-secondary"><i class="fas fa-redo"></i> <?php echo e(__('Reset')); ?></a>
        </form>
    </div>
</div>

<!-- Coupons Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> <?php echo e(__('Coupons')); ?> (<?php echo e($coupons->total()); ?>)</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e(__('Code')); ?></th>
                    <th><?php echo e(__('Name')); ?></th>
                    <th><?php echo e(__('Type')); ?></th>
                    <th><?php echo e(__('Value')); ?></th>
                    <th><?php echo e(__('Usage')); ?></th>
                    <th><?php echo e(__('Valid Period')); ?></th>
                    <th><?php echo e(__('Status')); ?></th>
                    <th><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <code style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; letter-spacing: 0.5px;"><?php echo e($coupon->code); ?></code>
                    </td>
                    <td>
                        <strong><?php echo e($coupon->name); ?></strong>
                        <?php if($coupon->description): ?>
                            <br><small class="text-muted"><?php echo e(Str::limit($coupon->description, 50)); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($coupon->type == 'percentage'): ?>
                            <span class="badge badge-primary"><i class="fas fa-percent"></i> <?php echo e(__('Percentage')); ?></span>
                        <?php elseif($coupon->type == 'fixed'): ?>
                            <span class="badge badge-success"><i class="fas fa-dollar-sign"></i> <?php echo e(__('Fixed')); ?></span>
                        <?php elseif($coupon->type == 'free_shipping'): ?>
                            <span class="badge badge-info"><i class="fas fa-shipping-fast"></i> <?php echo e(__('Free Shipping')); ?></span>
                        <?php else: ?>
                            <span class="badge badge-warning"><i class="fas fa-gift"></i> <?php echo e(__('Buy X Get Y')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($coupon->type == 'percentage'): ?>
                            <strong style="color: #3b82f6;"><?php echo e($coupon->value); ?>%</strong>
                        <?php elseif($coupon->type == 'fixed'): ?>
                            <strong style="color: #10b981;"><?php echo e(currency($coupon->value)); ?></strong>
                        <?php elseif($coupon->type == 'free_shipping'): ?>
                            <strong style="color: #06b6d4;"><?php echo e(__('Free')); ?></strong>
                        <?php else: ?>
                            <strong style="color: #f59e0b;"><?php echo e($coupon->buy_quantity); ?> + <?php echo e($coupon->get_quantity); ?></strong>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <strong><?php echo e($coupon->usage_count); ?></strong>
                            <?php if($coupon->usage_limit): ?>
                                <span class="text-muted">/ <?php echo e($coupon->usage_limit); ?></span>
                            <?php else: ?>
                                <span class="text-muted">/ ∞</span>
                            <?php endif; ?>
                        </div>
                        <?php if($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit): ?>
                            <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e(__('Limit reached')); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($coupon->valid_from): ?>
                            <small><?php echo e(__('From:')); ?> <?php echo e($coupon->valid_from->format('M d, Y')); ?></small><br>
                        <?php endif; ?>
                        <?php if($coupon->valid_to): ?>
                            <small><?php echo e(__('To:')); ?> <?php echo e($coupon->valid_to->format('M d, Y')); ?></small>
                        <?php else: ?>
                            <small class="text-muted"><?php echo e(__('No expiry')); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($coupon->status == 'active'): ?>
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> <?php echo e(__('Active')); ?></span>
                        <?php elseif($coupon->status == 'expired'): ?>
                            <span class="badge badge-danger"><i class="fas fa-clock"></i> <?php echo e(__('Expired')); ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><i class="fas fa-times-circle"></i> <?php echo e(__('Inactive')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('coupons.show', $coupon)); ?>" class="btn-action btn-action-view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('coupons.edit', $coupon)); ?>" class="btn-action btn-action-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if($coupon->usage_count == 0): ?>
                            <form action="<?php echo e(route('coupons.destroy', $coupon)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-action btn-action-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="fas fa-ticket-alt"></i>
                        <p><?php echo e(__('No coupons found')); ?></p>
                        <a href="<?php echo e(route('coupons.create')); ?>" class="btn btn-primary"><?php echo e(__('Create Your First Coupon')); ?></a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($coupons->hasPages()): ?>
    <div class="card-footer">
        <?php echo e($coupons->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/coupons/index.blade.php ENDPATH**/ ?>