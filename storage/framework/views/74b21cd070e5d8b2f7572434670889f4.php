<?php $__env->startSection('title', 'General Ledger'); ?>

<?php $__env->startSection('content'); ?>
<div class="brand-header ignore-print">
    <div>
        <h1 class="brand-title">
            <div class="brand-header-icon"><i class="fas fa-book"></i></div>
            General Ledger (Grand Livre)
        </h1>
        <p class="brand-subtitle">Period: <span class="fw-bold text-dark"><?php echo e($startDate); ?></span> to <span class="fw-bold text-dark"><?php echo e($endDate); ?></span></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('accounting.reports.gl.excel', ['start_date' => $startDate, 'end_date' => $endDate, 'account_id' => $accountId])); ?>" class="btn-brand-primary">
            <i class="fas fa-file-excel me-2"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn-brand-light">
            <i class="fas fa-print me-2"></i> Print
        </button>
        <a href="<?php echo e(route('accounting.reports')); ?>" class="btn-brand-outline">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>
</div>

<div class="brand-table-card p-4">
    <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accountId => $accountData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="mb-5 last:mb-0">
        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3 border">
            <div class="brand-stat-icon info me-3" style="width: 32px; height: 32px; font-size: 1rem;">
                <i class="fas fa-hashtag"></i>
            </div>
            <div>
                <h5 class="fw-bold m-0 text-dark"><?php echo e($accountData['account']->code); ?> - <?php echo e($accountData['account']->name); ?></h5>
                <div class="text-muted small mt-1">
                    Total Debit: <span class="fw-bold text-dark"><?php echo e(number_format($accountData['total_debit'], 2)); ?></span> • 
                    Total Credit: <span class="fw-bold text-dark"><?php echo e(number_format($accountData['total_credit'], 2)); ?></span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem; width: 120px;">Date</th>
                        <th style="width: 150px;">Reference</th>
                        <th>Description</th>
                        <th class="text-end" style="width: 120px;">Debit</th>
                        <th class="text-end" style="padding-right: 1.5rem; width: 120px;">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $accountData['lines']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="padding-left: 1.5rem;" class="text-muted"><?php echo e($line->entry->date->format('Y-m-d')); ?></td>
                        <td>
                            <span class="badge bg-light text-primary font-monospace border">
                                <?php echo e($line->entry->reference); ?>

                            </span>
                        </td>
                        <td class="text-dark"><?php echo e($line->entry->description); ?></td>
                        <td class="text-end"><?php echo e($line->debit > 0 ? number_format($line->debit, 2) : '-'); ?></td>
                        <td class="text-end" style="padding-right: 1.5rem;"><?php echo e($line->credit > 0 ? number_format($line->credit, 2) : '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="bg-light fw-bold" style="border-top: 1px solid #e2e8f0;">
                        <td colspan="3" class="text-end pe-4 py-3">Closing Balance</td>
                        <td class="text-end py-3"><?php echo e(number_format($accountData['total_debit'], 2)); ?></td>
                        <td class="text-end py-3 pe-4"><?php echo e(number_format($accountData['total_credit'], 2)); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center py-5">
        <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; background: #f1f5f9; color: #94a3b8; font-size: 24px;">
            <i class="fas fa-folder-open"></i>
        </div>
        <h6 class="fw-bold text-dark">No transactions found</h6>
        <p class="text-muted small">There are no journal entries for the selected period.</p>
    </div>
    <?php endif; ?>
</div>

<style>
    @media print {
        @page { size: landscape; margin: 10mm; }
        .ignore-print { display: none !important; }
        .brand-table-card { box-shadow: none !important; border: none !important; padding: 0 !important; }
        .bg-light { background-color: #f8f9fc !important; -webkit-print-color-adjust: exact; }
        .badge { border: 1px solid #ccc !important; color: #000 !important; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/accounting/reports/gl.blade.php ENDPATH**/ ?>