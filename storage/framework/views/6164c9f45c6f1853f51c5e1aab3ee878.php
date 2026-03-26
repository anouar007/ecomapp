<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Activity Log Details</h1>
        <a href="<?php echo e(route('activity-logs.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Log Information</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Date:</div>
                <div class="col-md-9"><?php echo e($activityLog->created_at->format('Y-m-d H:i:s')); ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">User:</div>
                <div class="col-md-9"><?php echo e($activityLog->user ? $activityLog->user->name : 'System'); ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Event:</div>
                <div class="col-md-9"><span class="badge bg-<?php echo e($activityLog->event_color); ?>"><?php echo e(ucfirst($activityLog->event)); ?></span></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Description:</div>
                <div class="col-md-9"><?php echo e($activityLog->description); ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Subject:</div>
                <div class="col-md-9">
                    <?php if($activityLog->subject): ?>
                        <?php echo e(class_basename($activityLog->subject_type)); ?> #<?php echo e($activityLog->subject_id); ?>

                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            
            <h5 class="mb-3">Changes</h5>
            <?php if(count($activityLog->changes) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Attribute</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $activityLog->changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e(ucfirst($attribute)); ?></td>
                                    <td class="text-danger"><?php echo e(is_array($values['old']) ? json_encode($values['old']) : $values['old']); ?></td>
                                    <td class="text-success"><?php echo e(is_array($values['new']) ? json_encode($values['new']) : $values['new']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No specific changes recorded or available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/activity-logs/show.blade.php ENDPATH**/ ?>