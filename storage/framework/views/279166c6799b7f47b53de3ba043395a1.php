<?php $__env->startSection('title', 'Customer Details'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<style>
.customer-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    border-radius: 16px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.credit-card {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    padding: 1.5rem;
    border-radius: 12px;
    color: white;
    position: relative;
    overflow: hidden;
}

.credit-card::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.stat-box {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    text-align: center;
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.12);
}

.info-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    height: 100%;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: #667eea;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Header -->
<div class="customer-hero">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div style="opacity: 0.8; font-size: 0.875rem; margin-bottom: 0.5rem;">Customer Code: <?php echo e($customer->customer_code); ?></div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                <?php echo e($customer->name); ?>

            </h1>
            <div style="opacity: 0.9; font-size: 1rem;">
                <i class="fas fa-envelope me-2"></i><?php echo e($customer->email); ?>

                <?php if($customer->phone): ?>
                    <i class="fas fa-phone ms-3 me-2"></i><?php echo e($customer->phone); ?>

                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('customers.edit', $customer)); ?>" class="btn btn-light">
                <i class="fas fa-edit me-2"></i> Edit
            </a>
            <a href="<?php echo e(route('customers.index')); ?>" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-box">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Status</div>
            <?php
                $statusColors = ['active' => ['bg' => '#dcfce7', 'text' => '#166534'], 'inactive' => ['bg' => '#fef3c7', 'text' => '#92400e'], 'blocked' => ['bg' => '#fee2e2', 'text' => '#991b1b']];
                $status = $statusColors[$customer->status] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
            ?>
            <div style="background: <?php echo e($status['bg']); ?>; color: <?php echo e($status['text']); ?>; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; text-transform: uppercase; font-size: 0.875rem; display: inline-block;">
                <?php echo e($customer->status_label); ?>

            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Total Orders</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1f2937;"><?php echo e($customer->total_orders); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Total Spent</div>
            <div style="font-size: 2rem; font-weight: 700; color: #10b981;"><?php echo e($customer->formatted_total_spent); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Loyalty Points</div>
            <div style="font-size: 2rem; font-weight: 700; color: #8b5cf6;"><?php echo e(number_format($customer->loyalty_points)); ?></div>
        </div>
    </div>
</div>

<!-- Credit Limit Section -->
<?php if($customer->credit_limit > 0 || $customer->current_balance > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="credit-card">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Credit Limit</div>
                    <div style="font-size: 1.75rem; font-weight: 700;"><?php echo e(currency($customer->credit_limit)); ?></div>
                </div>
                <div class="col-md-3">
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Current Balance</div>
                    <div style="font-size: 1.75rem; font-weight: 700;"><?php echo e(currency($customer->current_balance)); ?></div>
                </div>
                <div class="col-md-3">
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Available Credit</div>
                    <div style="font-size: 1.75rem; font-weight: 700;">
                        <?php echo e(currency(max(0, $customer->credit_limit - $customer->current_balance))); ?>

                    </div>
                </div>
                <div class="col-md-3">
                    <?php
                        $utilization = $customer->credit_limit > 0 ? ($customer->current_balance / $customer->credit_limit * 100) : 0;
                    ?>
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Utilization</div>
                    <div style="font-size: 1.75rem; font-weight: 700;"><?php echo e(number_format($utilization, 1)); ?>%</div>
                    <div class="mt-2" style="height: 6px; background: rgba(255,255,255,0.3); border-radius: 10px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo e(min($utilization, 100)); ?>%; background: white; border-radius: 10px;"></div>
                    </div>
                </div>
            </div>
            
            <?php if($customer->hasReachedCreditLimit()): ?>
            <div class="mt-3" style="background: rgba(239, 68, 68, 0.2); padding: 0.75rem 1rem; border-radius: 8px; border-left: 4px solid #ef4444;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Credit Limit Exceeded!</strong> Customer has exceeded their credit limit.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Information Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="info-card">
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                Personal Information
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Full Name</label>
                    <div style="font-size: 1rem; color: #1f2937; font-weight: 500;"><?php echo e($customer->name); ?></div>
                </div>
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Email Address</label>
                    <div style="font-size: 1rem; color: #1f2937; font-weight: 500;">
                        <a href="mailto:<?php echo e($customer->email); ?>" style="text-decoration: none; color: #667eea;"><?php echo e($customer->email); ?></a>
                    </div>
                </div>
                <?php if($customer->phone): ?>
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Phone Number</label>
                    <div style="font-size: 1rem; color: #1f2937; font-weight: 500;">
                        <a href="tel:<?php echo e($customer->phone); ?>" style="text-decoration: none; color: #667eea;"><?php echo e($customer->phone); ?></a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if($customer->date_of_birth): ?>
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Date of Birth</label>
                    <div style="font-size: 1rem; color: #1f2937; font-weight: 500;"><?php echo e($customer->date_of_birth->format('F d, Y')); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="info-card">
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i>
                Address Information
            </div>
            <?php if($customer->address || $customer->city || $customer->state || $customer->country): ?>
            <div style="color: #1f2937; line-height: 1.8;">
                <?php if($customer->address): ?><div><?php echo e($customer->address); ?></div><?php endif; ?>
                <?php if($customer->city || $customer->state || $customer->zip): ?>
                    <div><?php echo e($customer->city); ?><?php echo e($customer->state ? ', ' . $customer->state : ''); ?> <?php echo e($customer->zip); ?></div>
                <?php endif; ?>
                <?php if($customer->country): ?><div><?php echo e($customer->country); ?></div><?php endif; ?>
            </div>
            <?php else: ?>
            <p style="color: #9ca3af; font-style: italic;">No address provided</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Customer Group & Notes -->
<?php if($customer->customerGroup || $customer->notes): ?>
<div class="row g-3 mb-4">
    <?php if($customer->customerGroup): ?>
    <div class="col-md-<?php echo e($customer->notes ? '6' : '12'); ?>">
        <div class="info-card">
            <div class="section-title">
                <i class="fas fa-tag"></i>
                Customer Group
            </div>
            <div style="display: inline-block; background: <?php echo e($customer->customerGroup->color); ?>15; color: <?php echo e($customer->customerGroup->color); ?>; padding: 1rem 1.5rem; border-radius: 12px; font-size: 1.25rem; font-weight: 700;">
                <?php echo e($customer->customerGroup->name); ?>

            </div>
            <div style="margin-top: 1rem; color: #6b7280;">
                Discount: <?php echo e($customer->customerGroup->formatted_discount); ?>

            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if($customer->notes): ?>
    <div class="col-md-<?php echo e($customer->customerGroup ? '6' : '12'); ?>">
        <div class="info-card">
            <div class="section-title">
                <i class="fas fa-sticky-note"></i>
                Notes
            </div>
            <p style="color: #4b5563; line-height: 1.6; margin: 0;"><?php echo e($customer->notes); ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Recent Orders -->
<?php if($recentOrders->count() > 0): ?>
<div class="info-card mb-4">
    <div class="section-title">
        <i class="fas fa-shopping-cart"></i>
        Recent Orders (<?php echo e($recentOrders->count()); ?>)
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="fw-semibold"><?php echo e($order->order_number); ?></td>
                    <td><?php echo e($order->created_at->format('M d, Y')); ?></td>
                    <td><?php echo e($order->items->count()); ?> items</td>
                    <td class="text-end fw-bold"><?php echo e(currency($order->total)); ?></td>
                    <td class="text-center">
                        <span class="badge bg-<?php echo e($order->status_badge_class ?? 'secondary'); ?>">
                            <?php echo e(ucfirst($order->status)); ?>

                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Recent Invoices -->
<?php if($recentInvoices->count() > 0): ?>
<div class="info-card">
    <div class="section-title">
        <i class="fas fa-file-invoice"></i>
        Recent Invoices (<?php echo e($recentInvoices->count()); ?>)
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Invoice #</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Paid</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $recentInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statusColors = ['paid' => ['bg' => '#dcfce7', 'text' => '#166534'], 'unpaid' => ['bg' => '#fef3c7', 'text' => '#92400e'], 'partial' => ['bg' => '#dbeafe', 'text' => '#1e40af']];
                    $status = $statusColors[$invoice->payment_status] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                ?>
                <tr>
                    <td class="fw-semibold"><?php echo e($invoice->invoice_number); ?></td>
                    <td><?php echo e($invoice->issued_at->format('M d, Y')); ?></td>
                    <td class="text-end fw-bold"><?php echo e($invoice->formatted_total_amount); ?></td>
                    <td class="text-end text-success fw-semibold"><?php echo e(currency($invoice->total_amount - $invoice->remaining_balance)); ?></td>
                    <td class="text-center">
                        <span style="background: <?php echo e($status['bg']); ?>; color: <?php echo e($status['text']); ?>; padding: 0.25rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                            <?php echo e(ucfirst($invoice->payment_status)); ?>

                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/customers/show.blade.php ENDPATH**/ ?>