<?php $__env->startSection('title', __('Customer Management')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <?php echo e(__('Customer Management')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Manage your customer relationships, groups, and revenue tracking')); ?></p>
        </div>
        <a href="<?php echo e(route('customers.create')); ?>" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> <?php echo e(__('Add Customer')); ?>

        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid">
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Total Customers')); ?></div>
            <div class="brand-stat-value"><?php echo e(number_format($stats['total_customers'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-info-circle"></i> <?php echo e(__('Registered in system')); ?>

            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Active Customers')); ?></div>
            <div class="brand-stat-value"><?php echo e(number_format($stats['active_customers'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-toggle-on"></i> <?php echo e(__('Currently active accounts')); ?>

            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Total Revenue')); ?></div>
            <div class="brand-stat-value"><?php echo e(currency($stats['total_revenue'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-chart-line"></i> <?php echo e(__('Total sales from customers')); ?>

            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Avg Order Value')); ?></div>
            <div class="brand-stat-value"><?php echo e(currency($stats['avg_order_value'] ?? 0)); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-calculator"></i> <?php echo e(__('Average spend per order')); ?>

            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="brand-filter-bar">
        <form method="GET" action="<?php echo e(route('customers.index')); ?>" class="d-flex align-items-end gap-3 flex-wrap">
            <div class="brand-search-wrapper flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="<?php echo e(request('search')); ?>" 
                       placeholder="<?php echo e(__('Search name, email, phone, code...')); ?>">
            </div>
            
            <div style="min-width: 160px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;"><?php echo e(__('Status')); ?></label>
                <select name="status" class="form-select">
                    <option value=""><?php echo e(__('All Statuses')); ?></option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>><?php echo e(__('Active')); ?></option>
                    <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>><?php echo e(__('Inactive')); ?></option>
                    <option value="blocked" <?php echo e(request('status') === 'blocked' ? 'selected' : ''); ?>><?php echo e(__('Blocked')); ?></option>
                </select>
            </div>

            <div style="min-width: 160px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;"><?php echo e(__('Customer Group')); ?></label>
                <select name="customer_group_id" class="form-select">
                    <option value=""><?php echo e(__('All Groups')); ?></option>
                    <?php $__currentLoopData = $customerGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($group->id); ?>" <?php echo e(request('customer_group_id') == $group->id ? 'selected' : ''); ?>><?php echo e($group->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-filter me-1"></i> <?php echo e(__('Filter')); ?>

                </button>
                <a href="<?php echo e(route('customers.index')); ?>" class="btn-brand-light" title="Reset">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;"><?php echo e(__('Customer')); ?></th>
                        <th><?php echo e(__('Contact info')); ?></th>
                        <th><?php echo e(__('Group')); ?></th>
                        <th class="text-center"><?php echo e(__('Orders')); ?></th>
                        <th class="text-end"><?php echo e(__('Total Spent')); ?></th>
                        <th class="text-center"><?php echo e(__('Status')); ?></th>
                        <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <div>
                                <div class="fw-bold text-dark fs-6"><?php echo e($customer->name); ?></div>
                                <div class="text-muted small font-monospace mt-1"><?php echo e($customer->customer_code); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark small"><?php echo e($customer->email); ?></div>
                            <?php if($customer->phone): ?>
                                <div class="text-muted" style="font-size: 0.75rem;"><?php echo e($customer->phone); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($customer->customerGroup): ?>
                                <span class="brand-badge info" style="background: <?php echo e($customer->customerGroup->color); ?>15; color: <?php echo e($customer->customerGroup->color); ?>;">
                                    <?php echo e($customer->customerGroup->name); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-muted small"><?php echo e(__('No Group')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-dark"><?php echo e($customer->total_orders); ?></span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-dark fs-6"><?php echo e($customer->formatted_total_spent); ?></span>
                        </td>
                        <td class="text-center">
                            <?php
                                $statusClasses = [
                                    'active' => 'success',
                                    'inactive' => 'warning',
                                    'blocked' => 'danger',
                                ];
                                $badgeType = $statusClasses[$customer->status] ?? 'primary';
                            ?>
                            <span class="brand-badge <?php echo e($badgeType); ?>">
                                <?php echo e($customer->status_label); ?>

                            </span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('customers.show', $customer)); ?>" class="btn-action-icon" title="<?php echo e(__('View Customer')); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('customers.edit', $customer)); ?>" class="btn-action-icon" title="<?php echo e(__('Edit Customer')); ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('customers.destroy', $customer)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this customer?')); ?>');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-action-icon text-danger" title="<?php echo e(__('Delete Customer')); ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-users-slash"></i>
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo e(__('No customers found')); ?></h5>
                                <p class="text-muted"><?php echo e(__('No customer records matching your current filters.')); ?></p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($customers->hasPages()): ?>
        <div class="px-4 py-3 border-top">
            <?php echo e($customers->links()); ?>

        </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/customers/index.blade.php ENDPATH**/ ?>