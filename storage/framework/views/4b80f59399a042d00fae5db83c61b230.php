<?php $__env->startSection('title', __('Activity Logs')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-history"></i> <?php echo e(__('Activity Logs')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('Track user actions and system events')); ?></p>
        </div>
        <div>
            <form action="<?php echo e(route('activity-logs.clear')); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete old logs? This action cannot be undone.')); ?>');" style="display: inline;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="older_than_days" value="90">
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash-alt"></i> <?php echo e(__('Clear Old Logs (>90 days)')); ?>

                </button>
            </form>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <!-- Total Activities -->
    <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-list-ul" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Total Logs')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;"><?php echo e(number_format($stats['total_activities'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Today's Activities -->
    <div style="background: linear-gradient(135deg, #ffffff 0%, #d1fae5 100%); border-radius: 16px; padding: 24px; border: 1px solid #a7f3d0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #166534; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__("Today's Activity")); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;"><?php echo e(number_format($stats['today_activities'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Active Users -->
    <div style="background: linear-gradient(135deg, #ffffff 0%, #ede9fe 100%); border-radius: 16px; padding: 24px; border: 1px solid #ddd6fe;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #5b21b6; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Unique Users')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #6d28d9; margin: 0;"><?php echo e(number_format($stats['unique_users'])); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> <?php echo e(__('Filter Logs')); ?></h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('activity-logs.index')); ?>" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control" placeholder="<?php echo e(__('Search description...')); ?>" value="<?php echo e(request('search')); ?>">
            </div>
            
            <div style="width: auto; min-width: 150px;">
                <select name="user_id" class="form-control">
                    <option value=""><?php echo e(__('All Users')); ?></option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                            <?php echo e($user->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div style="width: auto; min-width: 150px;">
                <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>" placeholder="<?php echo e(__('Start Date')); ?>">
            </div>

            <div style="width: auto; min-width: 150px;">
                <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>" placeholder="<?php echo e(__('End Date')); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> <?php echo e(__('Filter')); ?></button>
            <a href="<?php echo e(route('activity-logs.index')); ?>" class="btn btn-secondary"><i class="fas fa-redo"></i> <?php echo e(__('Reset')); ?></a>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> <?php echo e(__('Activity History')); ?></h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e(__('User')); ?></th>
                    <th><?php echo e(__('Action')); ?></th>
                    <th><?php echo e(__('Subject')); ?></th>
                    <th><?php echo e(__('Changes')); ?></th>
                    <th><?php echo e(__('Date & Time')); ?></th>
                    <th><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #475569;">
                                <?php echo e($log->user ? substr($log->user->name, 0, 1) : '?'); ?>

                            </div>
                            <div>
                                <span style="font-weight: 600; color: #334155;"><?php echo e($log->user->name ?? __('System')); ?></span>
                                <br>
                                <small class="text-muted"><?php echo e($log->user->email ?? ''); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-light" style="font-weight: normal; font-size: 13px; color: #334155;">
                            <?php echo e($log->description); ?>

                        </span>
                    </td>
                    <td>
                        <?php if($log->subject_type): ?>
                            <span class="badge badge-secondary" style="font-size: 11px;">
                                <?php echo e(class_basename($log->subject_type)); ?> #<?php echo e($log->subject_id); ?>

                            </span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if(!empty($log->properties)): ?>
                            <?php
                                $props = is_array($log->properties) ? $log->properties : json_decode($log->properties, true);
                            ?>
                            <?php if(isset($props['attributes'])): ?>
                                <div style="font-size: 11px; font-family: monospace; color: #64748b; max-width: 300px; white-space: pre-wrap;"><?php echo e(Str::limit(json_encode($props['attributes']), 100)); ?></div>
                            <?php elseif(count($props) > 0): ?>
                                <div style="font-size: 11px; font-family: monospace; color: #64748b; max-width: 300px; white-space: pre-wrap;"><?php echo e(Str::limit(json_encode($props), 100)); ?></div>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="color: #475569; font-size: 13px;">
                            <?php echo e($log->created_at->format('M d, Y')); ?><br>
                            <small class="text-muted"><?php echo e($log->created_at->format('h:i A')); ?></small>
                        </span>
                    </td>
                    <td>
                       <a href="<?php echo e(route('activity-logs.show', $log)); ?>" class="btn btn-sm btn-icon btn-light" title="<?php echo e(__('View Details')); ?>">
                           <i class="fas fa-eye"></i>
                       </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fas fa-history"></i>
                        <p><?php echo e(__('No activity logs found')); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($logs->hasPages()): ?>
    <div class="card-footer">
        <?php echo e($logs->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/activity-logs/index.blade.php ENDPATH**/ ?>