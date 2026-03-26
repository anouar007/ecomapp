<?php $__env->startSection('title', __('Permissions Management')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-key"></i> <?php echo e(__('Permissions Management')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('Manage system permissions and capabilities')); ?></p>
        </div>
        <a href="<?php echo e(route('permissions.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> <?php echo e(__('Create New Permission')); ?>

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
        <h3 class="card-title"><i class="fas fa-list"></i> <?php echo e(__('All Permissions')); ?></h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e(__('Permission Name')); ?></th>
                    <th><?php echo e(__('Assigned To Roles')); ?></th>
                    <th><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <strong><?php echo e(ucwords(str_replace('_', ' ', $permission->name))); ?></strong>
                        <br>
                        <small class="text-muted"><?php echo e($permission->name); ?></small>
                    </td>
                    <td>
                        <?php if($permission->roles->count() > 0): ?>
                            <div class="role-tags">
                                <?php $__currentLoopData = $permission->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge badge-primary"><?php echo e($role->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted"><?php echo e(__('Not assigned to any role')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('permissions.edit', $permission)); ?>" class="btn-action btn-action-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('permissions.destroy', $permission->id)); ?>" method="POST" style="display:inline;" 
                                  data-confirm-delete="true"
                                  data-item-type="permission"
                                  data-item-name="<?php echo e($permission->name); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-action btn-action-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" class="empty-state">
                        <i class="fas fa-key"></i>
                        <p><?php echo e(__('No permissions found. Create your first permission to get started.')); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/permissions/index.blade.php ENDPATH**/ ?>