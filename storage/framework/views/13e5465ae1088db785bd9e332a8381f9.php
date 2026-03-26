<?php $__env->startSection('title', __('Financial Reports')); ?>

<?php $__env->startSection('content'); ?>
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <?php echo e(__('Financial Reports')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Generate and download standard financial statements and detailed ledgers.')); ?></p>
        </div>
    </div>

    <div class="row">
        <!-- Balance Sheet -->
        <div class="col-md-6 mb-4">
            <div class="brand-table-card h-100 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="brand-stat-icon primary me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark m-0"><?php echo e(__('Balance Sheet (Bilan)')); ?></h5>
                        <div class="text-muted small"><?php echo e(__('Assets, Liabilities & Equity')); ?></div>
                    </div>
                </div>
                
                <form action="<?php echo e(route('accounting.reports.bilan')); ?>" method="GET" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted"><?php echo e(__('Situation Date')); ?></label>
                        <input type="date" name="date" class="form-control brand-input" value="<?php echo e(date('Y-12-31')); ?>">
                    </div>
                    <button type="submit" class="btn-brand-primary w-100">
                        <i class="fas fa-eye me-2"></i> <?php echo e(__('View Report')); ?>

                    </button>
                </form>
            </div>
        </div>

        <!-- Income Statement -->
        <div class="col-md-6 mb-4">
            <div class="brand-table-card h-100 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="brand-stat-icon success me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark m-0"><?php echo e(__('Income Statement (CPC)')); ?></h5>
                        <div class="text-muted small"><?php echo e(__('Revenue, Expenses & Result')); ?></div>
                    </div>
                </div>

                <form action="<?php echo e(route('accounting.reports.cpc')); ?>" method="GET" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted"><?php echo e(__('Period')); ?></label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control brand-input" value="<?php echo e(date('Y-01-01')); ?>">
                            <span class="input-group-text bg-light border-0"><?php echo e(__('to')); ?></span>
                            <input type="date" name="end_date" class="form-control brand-input" value="<?php echo e(date('Y-12-31')); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn-brand-primary w-100">
                        <i class="fas fa-eye me-2"></i> <?php echo e(__('View Report')); ?>

                    </button>
                </form>
            </div>
        </div>

        <!-- General Ledger -->
        <div class="col-md-6 mb-4">
            <div class="brand-table-card h-100 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="brand-stat-icon info me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark m-0"><?php echo e(__('General Ledger (Grand Livre)')); ?></h5>
                        <div class="text-muted small"><?php echo e(__('Detailed Transaction History')); ?></div>
                    </div>
                </div>

                <form action="<?php echo e(route('accounting.reports.gl')); ?>" method="GET" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted"><?php echo e(__('Account (Optional)')); ?></label>
                        <select name="account_id" class="form-select brand-select">
                            <option value=""><?php echo e(__('All Accounts')); ?></option>
                            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($account->id); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted"><?php echo e(__('Date Range')); ?></label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control brand-input" value="<?php echo e(date('Y-01-01')); ?>">
                            <span class="input-group-text bg-light border-0"><?php echo e(__('to')); ?></span>
                            <input type="date" name="end_date" class="form-control brand-input" value="<?php echo e(date('Y-12-31')); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn-brand-primary w-100">
                        <i class="fas fa-eye me-2"></i> <?php echo e(__('View Report')); ?>

                    </button>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/accounting/reports.blade.php ENDPATH**/ ?>