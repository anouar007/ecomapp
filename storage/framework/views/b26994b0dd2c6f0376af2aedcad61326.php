<?php $__env->startSection('title', __('Edit Order')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-edit"></i> <?php echo e(__('Edit Order')); ?>: <?php echo e($order->order_number); ?></h1>
    <p class="page-subtitle"><?php echo e(__('Update order status and payment information')); ?></p>
</div>

<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        <strong><?php echo e(__('Oops! Something went wrong:')); ?></strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt"></i> <?php echo e(__('Order Information')); ?></h3>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('orders.update', $order)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="status" class="form-label">
                        <?php echo e(__('Order Status')); ?> <span class="required">*</span>
                    </label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="pending" <?php echo e(old('status', $order->status) == 'pending' ? 'selected' : ''); ?>><?php echo e(__('Pending')); ?></option>
                        <option value="processing" <?php echo e(old('status', $order->status) == 'processing' ? 'selected' : ''); ?>><?php echo e(__('Processing')); ?></option>
                        <option value="shipped" <?php echo e(old('status', $order->status) == 'shipped' ? 'selected' : ''); ?>><?php echo e(__('Shipped')); ?></option>
                        <option value="delivered" <?php echo e(old('status', $order->status) == 'delivered' ? 'selected' : ''); ?>><?php echo e(__('Delivered')); ?></option>
                        <option value="cancelled" <?php echo e(old('status', $order->status) == 'cancelled' ? 'selected' : ''); ?>><?php echo e(__('Cancelled')); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_status" class="form-label">
                        <?php echo e(__('Payment Status')); ?> <span class="required">*</span>
                    </label>
                    <select id="payment_status" name="payment_status" class="form-control" required>
                        <option value="pending" <?php echo e(old('payment_status', $order->payment_status) == 'pending' ? 'selected' : ''); ?>><?php echo e(__('Pending')); ?></option>
                        <option value="paid" <?php echo e(old('payment_status', $order->payment_status) == 'paid' ? 'selected' : ''); ?>><?php echo e(__('Paid')); ?></option>
                        <option value="failed" <?php echo e(old('payment_status', $order->payment_status) == 'failed' ? 'selected' : ''); ?>><?php echo e(__('Failed')); ?></option>
                        <option value="refunded" <?php echo e(old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : ''); ?>><?php echo e(__('Refunded')); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_method" class="form-label"><?php echo e(__('Payment Method')); ?></label>
                    <input type="text" 
                           id="payment_method" 
                           name="payment_method" 
                           class="form-control" 
                           value="<?php echo e(old('payment_method', $order->payment_method)); ?>" 
                           placeholder="<?php echo e(__('e.g., Credit Card')); ?>">
                </div>

                <div class="form-group">
                    <label for="transaction_id" class="form-label"><?php echo e(__('Transaction ID')); ?></label>
                    <input type="text" 
                           id="transaction_id" 
                           name="transaction_id" 
                           class="form-control" 
                           value="<?php echo e(old('transaction_id', $order->transaction_id)); ?>" 
                           placeholder="<?php echo e(__('Transaction ID')); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label"><?php echo e(__('Notes')); ?></label>
                <textarea id="notes" 
                          name="notes" 
                          class="form-control" 
                          rows="4" 
                          placeholder="<?php echo e(__('Order notes...')); ?>"><?php echo e(old('notes', $order->notes)); ?></textarea>
            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> <?php echo e(__('Cancel')); ?>

                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo e(__('Update Order')); ?>

                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/orders/edit.blade.php ENDPATH**/ ?>