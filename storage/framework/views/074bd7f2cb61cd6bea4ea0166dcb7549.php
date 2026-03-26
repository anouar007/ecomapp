<?php $__env->startSection('title', __('Edit Profile')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<style>
.avatar-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 4px solid #e2e8f0;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    color: #667eea;
    font-weight: 700;
    overflow: hidden;
    margin-bottom: 16px;
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-upload {
    text-align: center;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-edit"></i> <?php echo e(__('Edit Profile')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('Update your account information and password')); ?></p>
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
        <h3 class="card-title"><i class="fas fa-user"></i> <?php echo e(__('Profile Information')); ?></h3>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="avatar-upload">
                <div class="avatar-preview" id="avatarPreview">
                    <?php if($user->avatar): ?>
                        <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>" id="avatarImg">
                    <?php else: ?>
                        <span id="avatarInitial"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="avatar" class="form-label"><?php echo e(__('Profile Picture')); ?></label>
                    <input type="file" 
                           id="avatar" 
                           name="avatar" 
                           class="form-control" 
                           accept="image/*"
                           onchange="previewAvatar(event)">
                    <small class="text-muted"><?php echo e(__('Max size: 2MB. Accepted: JPG, PNG, GIF')); ?></small>
                </div>
            </div>
            
            <hr style="margin: 32px 0;">
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="name" class="form-label">
                        <?php echo e(__('Full Name')); ?> <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           value="<?php echo e(old('name', $user->name)); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <?php echo e(__('Email Address')); ?> <span class="required">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           value="<?php echo e(old('email', $user->email)); ?>" 
                           required>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('profile.show')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> <?php echo e(__('Cancel')); ?>

                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo e(__('Update Profile')); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lock"></i> <?php echo e(__('Change Password')); ?></h3>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('profile.password')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="current_password" class="form-label">
                        <?php echo e(__('Current Password')); ?> <span class="required">*</span>
                    </label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           class="form-control" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <?php echo e(__('New Password')); ?> <span class="required">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required>
                    <small class="text-muted"><?php echo e(__('Minimum 8 characters')); ?></small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <?php echo e(__('Confirm New Password')); ?> <span class="required">*</span>
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control" 
                           required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key"></i> <?php echo e(__('Change Password')); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            const initial = document.getElementById('avatarInitial');
            
            if (initial) {
                initial.remove();
            }
            
            let img = document.getElementById('avatarImg');
            if (!img) {
                img = document.createElement('img');
                img.id = 'avatarImg';
                preview.appendChild(img);
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/profile/edit.blade.php ENDPATH**/ ?>