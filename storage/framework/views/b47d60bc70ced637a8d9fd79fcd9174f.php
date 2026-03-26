<?php $__env->startSection('title', __('Edit Customer')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-edit"></i> <?php echo e(__('Edit Customer')); ?>: <?php echo e($customer->name); ?></h1>
    <p class="page-subtitle"><?php echo e(__('Update customer information')); ?></p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt"></i> <?php echo e(__('Customer Information')); ?></h3>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('customers.update', $customer)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="name" class="form-label"><?php echo e(__('Full Name')); ?> <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo e(old('name', $customer->name)); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label"><?php echo e(__('Email')); ?> <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo e(old('email', $customer->email)); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label"><?php echo e(__('Phone')); ?></label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo e(old('phone', $customer->phone)); ?>">
                </div>

                <div class="form-group">
                    <label for="customer_group_id" class="form-label"><?php echo e(__('Customer Group')); ?></label>
                    <select id="customer_group_id" name="customer_group_id" class="form-control">
                        <option value=""><?php echo e(__('No Group')); ?></option>
                        <?php $__currentLoopData = $customerGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($group->id); ?>" <?php echo e(old('customer_group_id', $customer->customer_group_id) == $group->id ? 'selected' : ''); ?>>
                            <?php echo e($group->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label"><?php echo e(__('Status')); ?> <span class="required">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" <?php echo e(old('status', $customer->status) == 'active' ? 'selected' : ''); ?>><?php echo e(__('Active')); ?></option>
                        <option value="inactive" <?php echo e(old('status', $customer->status) == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('Inactive')); ?></option>
                        <option value="blocked" <?php echo e(old('status', $customer->status) == 'blocked' ? 'selected' : ''); ?>><?php echo e(__('Blocked')); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label"><?php echo e(__('Date of Birth')); ?></label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?php echo e(old('date_of_birth', $customer->date_of_birth?->format('Y-m-d'))); ?>">
                </div>

                <div class="form-group">
                    <label for="credit_limit" class="form-label"><?php echo e(__('Credit Limit')); ?></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" id="credit_limit" name="credit_limit" class="form-control" value="<?php echo e(old('credit_limit', $customer->credit_limit)); ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label"><?php echo e(__('Address')); ?></label>
                <textarea id="address" name="address" class="form-control" rows="2"><?php echo e(old('address', $customer->address)); ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="city" class="form-label"><?php echo e(__('City')); ?></label>
                    <input type="text" id="city" name="city" class="form-control" value="<?php echo e(old('city', $customer->city)); ?>">
                </div>

                <div class="form-group">
                    <label for="state" class="form-label"><?php echo e(__('State/Province')); ?></label>
                    <input type="text" id="state" name="state" class="form-control" value="<?php echo e(old('state', $customer->state)); ?>">
                </div>

                <div class="form-group">
                    <label for="zip" class="form-label"><?php echo e(__('ZIP Code')); ?></label>
                    <input type="text" id="zip" name="zip" class="form-control" value="<?php echo e(old('zip', $customer->zip)); ?>">
                </div>

                <div class="form-group">
                    <label for="country" class="form-label"><?php echo e(__('Country')); ?></label>
                    <input type="text" id="country" name="country" class="form-control" value="<?php echo e(old('country', $customer->country)); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label"><?php echo e(__('Notes')); ?></label>
                <textarea id="notes" name="notes" class="form-control" rows="3"><?php echo e(old('notes', $customer->notes)); ?></textarea>
            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('customers.show', $customer)); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> <?php echo e(__('Cancel')); ?>

                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo e(__('Update Customer')); ?>

                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/customers/edit.blade.php ENDPATH**/ ?>