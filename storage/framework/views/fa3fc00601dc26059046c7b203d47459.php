<?php $__env->startSection('title', __('Invoices Management')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <?php echo e(__('Invoices Management')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Track billings, manage customer payments, and monitor revenue')); ?></p>
        </div>
        <a href="<?php echo e(route('invoices.create')); ?>" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> <?php echo e(__('Create Invoice')); ?>

        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid">
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Total Invoices')); ?></div>
            <div class="brand-stat-value"><?php echo e($stats['total_invoices']); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-history"></i> <?php echo e(__('Lifetime generated')); ?>

            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Paid Amount')); ?></div>
            <div class="brand-stat-value"><?php echo e(currency($stats['paid_amount'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-arrow-up text-success"></i> <?php echo e(__('Successfully collected')); ?>

            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-hourglass-start"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Unpaid Amount')); ?></div>
            <div class="brand-stat-value"><?php echo e(currency($stats['unpaid_amount'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-exclamation-circle text-warning"></i> <?php echo e(__('Pending collections')); ?>

            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Total Revenue')); ?></div>
            <div class="brand-stat-value"><?php echo e(currency($stats['total_revenue'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-chart-line"></i> <?php echo e(__('Combined gross value')); ?>

            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="brand-filter-bar">
        <form method="GET" action="<?php echo e(route('invoices.index')); ?>" class="d-flex align-items-end gap-3 flex-wrap">
            <div class="brand-search-wrapper flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="<?php echo e(request('search')); ?>" 
                       placeholder="<?php echo e(__('Invoice #, customer name...')); ?>">
            </div>
            
            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Status</label>
                <select name="status" class="form-select">
                    <option value=""><?php echo e(__('All Statuses')); ?></option>
                    <option value="paid" <?php echo e(request('status') === 'paid' ? 'selected' : ''); ?>><?php echo e(__('Paid')); ?></option>
                    <option value="unpaid" <?php echo e(request('status') === 'unpaid' ? 'selected' : ''); ?>><?php echo e(__('Unpaid')); ?></option>
                    <option value="partial" <?php echo e(request('status') === 'partial' ? 'selected' : ''); ?>><?php echo e(__('Partial')); ?></option>
                    <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>><?php echo e(__('Cancelled')); ?></option>
                </select>
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;"><?php echo e(__('From Date')); ?></label>
                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="form-control">
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;"><?php echo e(__('To Date')); ?></label>
                <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-filter me-1"></i> <?php echo e(__('Filter')); ?>

                </button>
                <a href="<?php echo e(route('invoices.index')); ?>" class="btn-brand-light" title="Reset">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;"><?php echo e(__('Invoice #')); ?></th>
                        <th><?php echo e(__('Customer')); ?></th>
                        <th><?php echo e(__('Date')); ?></th>
                        <th class="text-end"><?php echo e(__('Total Amount')); ?></th>
                        <th class="text-center"><?php echo e(__('Status')); ?></th>
                        <th><?php echo e(__('Method')); ?></th>
                        <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="fw-bold text-primary text-decoration-none">
                                <?php echo e($invoice->invoice_number); ?>

                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo e($invoice->customer_name); ?></div>
                            <?php if($invoice->customer_email): ?>
                                <div class="text-muted small"><?php echo e($invoice->customer_email); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="text-dark"><?php echo e($invoice->issued_at->format('M d, Y')); ?></div>
                            <div class="text-muted small"><?php echo e($invoice->issued_at->format('h:i A')); ?></div>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6">
                            <?php echo e($invoice->formatted_total_amount); ?>

                        </td>
                        <td class="text-center">
                            <?php
                                $statusClasses = [
                                    'paid' => 'success',
                                    'unpaid' => 'warning',
                                    'partial' => 'info',
                                    'cancelled' => 'danger',
                                ];
                                $badgeType = $statusClasses[$invoice->payment_status] ?? 'primary';
                            ?>
                            <span class="brand-badge <?php echo e($badgeType); ?>">
                                <?php echo e($invoice->status_label); ?>

                            </span>
                        </td>
                        <td class="text-muted small">
                            <span class="text-uppercase" style="letter-spacing: 0.02em;"><?php echo e(str_replace('_', ' ', $invoice->payment_method)); ?></span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="btn-action-icon" title="<?php echo e(__('View Details')); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('invoices.download', $invoice)); ?>" class="btn-action-icon" title="<?php echo e(__('Download PDF')); ?>">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a href="<?php echo e(route('invoices.print', $invoice)); ?>" target="_blank" class="btn-action-icon" title="<?php echo e(__('Print Invoice')); ?>">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo e(__('No invoices found')); ?></h5>
                                <p class="text-muted"><?php echo e(__("You haven't generated any invoices matching your search.")); ?></p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($invoices->hasPages()): ?>
        <div class="px-4 py-3 border-top">
            <?php echo e($invoices->links()); ?>

        </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/invoices/index.blade.php ENDPATH**/ ?>