<?php $__env->startSection('title', __('Roles Management')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-user-shield"></i> <?php echo e(__('Roles Management')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('Manage system roles and their permissions')); ?></p>
        </div>
        <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> <?php echo e(__('Create New Role')); ?>

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

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> <?php echo e(__('All Roles')); ?></h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e(__('Role Name')); ?></th>
                    <th><?php echo e(__('Permissions')); ?></th>
                    <th><?php echo e(__('Users')); ?></th>
                    <th><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <strong><?php echo e($role->name); ?></strong>
                        <?php if(in_array($role->name, ['Admin', 'Manager', 'Staff'])): ?>
                            <span class="badge badge-primary"><?php echo e(__('System')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($role->permissions->count() > 0): ?>
                            <div class="permission-tags">
                                <?php $__currentLoopData = $role->permissions->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge badge-secondary"><?php echo e(ucwords(str_replace('_', ' ', $permission->name))); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($role->permissions->count() > 3): ?>
                                    <span class="badge badge-light">+<?php echo e($role->permissions->count() - 3); ?> <?php echo e(__('more')); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted"><?php echo e(__('No permissions')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($role->users->count()); ?> <?php echo e(__('users')); ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('roles.edit', $role)); ?>" class="btn-action btn-action-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if(!in_array($role->name, ['Admin', 'Manager', 'Staff'])): ?>
                                    <form method="POST" 
                                          action="<?php echo e(route('roles.destroy', $role->id)); ?>" 
                                          style="display: inline;"
                                          data-confirm-delete="true"
                                          data-item-type="role"
                                          data-item-name="<?php echo e($role->name); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="empty-state">
                        <i class="fas fa-user-shield"></i>
                        <p><?php echo e(__('No roles found. Create your first role to get started.')); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/roles/index.blade.php ENDPATH**/ ?>