<?php $__env->startSection('title', __('Users Management')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users"></i> <?php echo e(__('Users Management')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('Manage user roles and access permissions')); ?></p>
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
        <h3 class="card-title"><i class="fas fa-list"></i> <?php echo e(__('All Users')); ?></h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e(__('User')); ?></th>
                    <th><?php echo e(__('Email')); ?></th>
                    <th><?php echo e(__('Current Roles')); ?></th>
                    <th><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                            </div>
                            <strong><?php echo e($user->name); ?></strong>
                        </div>
                    </td>
                    <td><?php echo e($user->email); ?></td>
                    <td>
                        <?php if($user->roles->count() > 0): ?>
                            <div class="role-tags">
                                <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge badge-primary"><?php echo e($role->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted"><?php echo e(__('No roles assigned')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" 
                                class="btn-action btn-action-edit" 
                                onclick="openRoleModal(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>', <?php echo e(json_encode($user->roles->pluck('id'))); ?>)" 
                                title="<?php echo e(__('Manage Roles')); ?>">
                            <i class="fas fa-user-shield"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="empty-state">
                        <i class="fas fa-users"></i>
                        <p><?php echo e(__('No users found in the system.')); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Role Assignment Modal -->
<div id="roleModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-user-shield"></i> <?php echo e(__('Manage User Roles')); ?></h3>
            <button type="button" class="modal-close" onclick="closeRoleModal()">&times;</button>
        </div>
        <form id="roleForm" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="modal-body">
                <p class="modal-subtitle"><?php echo e(__('Select roles for')); ?> <strong id="userName"></strong></p>
                <div class="roles-grid">
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="role-checkbox">
                        <input type="checkbox" id="role-<?php echo e($role->id); ?>" name="roles[]" value="<?php echo e($role->id); ?>">
                        <label for="role-<?php echo e($role->id); ?>"><?php echo e($role->name); ?></label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRoleModal()">
                    <i class="fas fa-times"></i> <?php echo e(__('Cancel')); ?>

                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo e(__('Update Roles')); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openRoleModal(userId, userName, userRoles) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('roleForm').action = `/users/${userId}/roles`;
    
    // Uncheck all checkboxes first
    document.querySelectorAll('#roleModal input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Check user's current roles
    userRoles.forEach(roleId => {
        const checkbox = document.getElementById(`role-${roleId}`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
    
    document.getElementById('roleModal').style.display = 'flex';
}

function closeRoleModal() {
    document.getElementById('roleModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('roleModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRoleModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('roleModal').style.display === 'flex') {
        closeRoleModal();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/users/index.blade.php ENDPATH**/ ?>