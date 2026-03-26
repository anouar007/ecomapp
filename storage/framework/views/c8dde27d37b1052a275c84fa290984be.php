<?php $__env->startSection('title', 'Create Role'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> Create New Role</h1>
    <p class="page-subtitle">Define a new role and assign permissions</p>
</div>

<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        <strong>Oops! Something went wrong:</strong>
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
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Role Information</h3>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('roles.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label for="name" class="form-label">
                    Role Name <span class="required">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       value="<?php echo e(old('name')); ?>" 
                       placeholder="Enter role name (e.g., Manager, Customer)" 
                       required 
                       autofocus>
                <small class="form-help">Use a descriptive name that clearly identifies this role's purpose</small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-key"></i> Assign Permissions
                </label>
                <div class="permissions-grid">
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="permission-checkbox">
                        <input type="checkbox" 
                               id="permission-<?php echo e($permission->id); ?>" 
                               name="permissions[]" 
                               value="<?php echo e($permission->id); ?>"
                               <?php echo e(in_array($permission->id, old('permissions', [])) ? 'checked' : ''); ?>>
                        <label for="permission-<?php echo e($permission->id); ?>">
                            <?php echo e(ucwords(str_replace('_', ' ', $permission->name))); ?>

                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Role
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/roles/create.blade.php ENDPATH**/ ?>