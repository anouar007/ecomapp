<?php $__env->startSection('title', __('My Profile')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<style>
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px;
    border-radius: 16px;
    color: white;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 24px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #667eea;
    font-weight: 700;
    overflow: hidden;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info h2 {
    margin: 0 0 8px 0;
    font-size: 32px;
}

.profile-info p {
    margin: 0;
    opacity: 0.9;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 24px;
}

.info-item {
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #667eea;
}

.info-label {
    font-size: 12px;
    text-transform: uppercase;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 6px;
}

.info-value {
    font-size: 16px;
    color: #0f172a;
    font-weight: 500;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-user"></i> <?php echo e(__('My Profile')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('View and manage your account information')); ?></p>
        </div>
        <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> <?php echo e(__('Edit Profile')); ?>

        </a>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<div class="profile-header">
    <div class="profile-avatar">
        <?php if($user->avatar): ?>
            <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>">
        <?php else: ?>
            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

        <?php endif; ?>
    </div>
    <div class="profile-info">
        <h2><?php echo e($user->name); ?></h2>
        <p><i class="fas fa-envelope"></i> <?php echo e($user->email); ?></p>
        <p style="margin-top: 8px;">
            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge badge-success" style="margin-right: 6px;">
                    <i class="fas fa-shield-alt"></i> <?php echo e($role->name); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle"></i> <?php echo e(__('Account Information')); ?></h3>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label"><i class="fas fa-user"></i> <?php echo e(__('Full Name')); ?></div>
                <div class="info-value"><?php echo e($user->name); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-envelope"></i> <?php echo e(__('Email Address')); ?></div>
                <div class="info-value"><?php echo e($user->email); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-calendar"></i> <?php echo e(__('Member Since')); ?></div>
                <div class="info-value"><?php echo e($user->created_at->format('F d, Y')); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-clock"></i> <?php echo e(__('Last Updated')); ?></div>
                <div class="info-value"><?php echo e($user->updated_at->diffForHumans()); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-shield-alt"></i> <?php echo e(__('Roles & Permissions')); ?></h3>
    </div>
    <div class="card-body">
        <h4 style="margin-bottom: 12px;"><?php echo e(__('Your Roles:')); ?></h4>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px;">
            <?php $__empty_1 = true; $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <span class="badge badge-primary" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fas fa-user-shield"></i> <?php echo e($role->name); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-muted"><?php echo e(__('No roles assigned')); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if($user->roles->isNotEmpty()): ?>
        <h4 style="margin-bottom: 12px;"><?php echo e(__('Your Permissions:')); ?></h4>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <?php
                $permissions = $user->roles->flatMap->permissions->unique('id');
            ?>
            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge badge-secondary" style="padding: 6px 12px;">
                    <i class="fas fa-key"></i> <?php echo e($permission->name); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/profile/show.blade.php ENDPATH**/ ?>