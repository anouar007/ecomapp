<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('Advanced Analytics')); ?></h1>
        <div>
            <a href="<?php echo e(route('analytics.export', ['period' => $period])); ?>" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> <?php echo e(__('Export Report')); ?>

            </a>
            <div class="btn-group ml-2" role="group">
                <a href="<?php echo e(route('analytics.index', ['period' => 'week'])); ?>" class="btn btn-sm btn-<?php echo e(\$period == 'week' ? 'secondary' : 'light'); ?>"><?php echo e(__('Week')); ?></a>
                <a href="<?php echo e(route('analytics.index', ['period' => 'month'])); ?>" class="btn btn-sm btn-<?php echo e(\$period == 'month' ? 'secondary' : 'light'); ?>"><?php echo e(__('Month')); ?></a>
                <a href="<?php echo e(route('analytics.index', ['period' => 'year'])); ?>" class="btn btn-sm btn-<?php echo e(\$period == 'year' ? 'secondary' : 'light'); ?>"><?php echo e(__('Year')); ?></a>
            </div>
        </div>
    </div>

    <!-- Sales Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><?php echo e(__('Total Revenue')); ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(currency($analytics['sales']['total_revenue'])); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><?php echo e(__('Total Orders')); ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($analytics['sales']['total_orders']); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-bag fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Analytics Sections (Simplified for Verification) -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Top Selling Products')); ?></h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $analytics['sales']['top_selling_products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo e($product->translated_name); ?>

                                <span class="badge badge-primary badge-pill"><?php echo e(\$product->total_sold); ?> <?php echo e(__('sold')); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="list-group-item"><?php echo e(__('No sales data available.')); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/analytics/index.blade.php ENDPATH**/ ?>