<?php $__env->startSection('title', __('Orders Management')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <?php echo e(__('Orders Management')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Track and manage customer orders, fulfillment status, and logistics')); ?></p>
        </div>

    </div>

    <!-- Filter Bar -->
    <div class="brand-filter-bar px-3 py-3">
        <form method="GET" action="<?php echo e(route('orders.index')); ?>" class="row g-3 align-items-end">
            <div class="col-12 col-lg-3">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;"><?php echo e(__('Search')); ?></label>
                <div class="brand-search-wrapper w-100">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           value="<?php echo e(request('search')); ?>" 
                           placeholder="<?php echo e(__('Order #, name...')); ?>">
                </div>
            </div>
            
            <div class="col-6 col-lg-2">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;"><?php echo e(__('Status')); ?></label>
                <select name="status" class="form-select custom-select-premium">
                    <option value=""><?php echo e(__('All Statuses')); ?></option>
                    <?php $__currentLoopData = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($st); ?>" <?php echo e(request('status') == $st ? 'selected' : ''); ?>><?php echo e(__(ucfirst($st))); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;"><?php echo e(__('Payment')); ?></label>
                <select name="payment_status" class="form-select custom-select-premium">
                    <option value=""><?php echo e(__('All Statuses')); ?></option>
                    <?php $__currentLoopData = ['pending', 'paid', 'failed', 'refunded']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($pst); ?>" <?php echo e(request('payment_status') == $pst ? 'selected' : ''); ?>><?php echo e(__(ucfirst($pst))); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-12 col-lg-4">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;"><?php echo e(__('Date Range')); ?></label>
                <div class="brand-search-wrapper w-100">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="text" name="date_range" id="date_range" class="form-control" 
                           value="<?php echo e(request('date_range')); ?>" 
                           placeholder="<?php echo e(__('Select date range...')); ?>">
                </div>
            </div>

            <div class="col-12 col-lg-1">
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-brand-primary flex-grow-1 px-1" title="<?php echo e(__('Filter')); ?>">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-brand-light px-2" title="<?php echo e(__('Reset')); ?>">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .brand-search-wrapper .flatpickr-input {
            padding-inline-start: 40px !important;
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#date_range", {
                mode: "range",
                dateFormat: "Y-m-d",
                allowInput: true,
                locale: "<?php echo e(app()->getLocale() == 'ar' ? 'ar' : 'en'); ?>"
            });
        });
    </script>
    <?php $__env->stopPush(); ?>

    <!-- Mobile Orders List -->
    <div class="d-lg-none mt-3 px-1">
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="glass-card mb-3 p-3 border-0 shadow-soft">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <a href="<?php echo e(route('orders.show', $order)); ?>" class="fw-bold text-primary text-decoration-none d-block mb-1" style="font-size: 1.1rem;">
                        #<?php echo e($order->order_number); ?>

                    </a>
                    <div class="text-muted small">
                        <i class="far fa-calendar-alt me-1 opacity-50"></i> <?php echo e($order->created_at->format('M d, Y')); ?>

                    </div>
                </div>
                <div class="d-flex flex-column gap-2 align-items-end">
                    <select class="brand-badge-select status-update-ajax <?php echo e($order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info')); ?>" 
                            data-order-id="<?php echo e($order->id); ?>"
                            data-update-url="<?php echo e(route('orders.update', $order)); ?>"
                            style="font-size: 0.65rem; width: 100px; padding: 0.25rem 0.5rem;">
                        <?php $__currentLoopData = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st); ?>" <?php echo e($order->status === $st ? 'selected' : ''); ?>><?php echo e(__(ucfirst($st))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select class="brand-badge-select payment-update-ajax <?php echo e($order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning')); ?>"
                            data-order-id="<?php echo e($order->id); ?>"
                            data-update-url="<?php echo e(route('orders.update', $order)); ?>"
                            style="font-size: 0.65rem; width: 100px; padding: 0.25rem 0.5rem;">
                        <?php $__currentLoopData = ['pending', 'paid', 'failed', 'refunded']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pst); ?>" <?php echo e($order->payment_status === $pst ? 'selected' : ''); ?>><?php echo e(__(ucfirst($pst))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

            </div>

            <div class="d-flex align-items-center justify-content-between py-3 border-top border-bottom" style="border-style: dashed !important; border-color: #f1f5f9 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar" style="width: 40px; height: 40px; font-size: 0.8rem;">
                        <?php echo e(substr($order->customer_name, 0, 1)); ?>

                    </div>
                    <div>
                        <div class="fw-bold text-dark"><?php echo e($order->customer_name); ?></div>
                        <div class="text-muted small"><?php echo e($order->customer_email); ?></div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-muted small mb-1"><?php echo e(__('Total Amount')); ?></div>
                    <div class="fw-bold text-dark fs-5 font-inter"><?php echo e($order->formatted_total); ?></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    <i class="fas fa-shopping-bag me-1 opacity-50"></i> 
                    <span class="fw-bold text-dark"><?php echo e($order->items->count()); ?></span> <?php echo e(__('Items')); ?>

                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-sm btn-brand-light rounded-pill px-3">
                        <i class="fas fa-eye me-1"></i> <?php echo e(__('View')); ?>

                    </a>
                    <a href="<?php echo e(route('orders.edit', $order)); ?>" class="btn btn-sm btn-brand-primary rounded-pill px-3">
                        <i class="fas fa-edit me-1"></i> <?php echo e(__('Edit')); ?>

                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="glass-card p-5 text-center">
                <i class="fas fa-shopping-basket opacity-25 mb-3" style="font-size: 48px;"></i>
                <h5 class="fw-bold"><?php echo e(__('No orders found')); ?></h5>
            </div>
        <?php endif; ?>
    </div>

    <!-- Orders Table (Desktop) -->
    <div class="brand-table-card mt-4">
        <div class="table-responsive d-none d-lg-block">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;"><?php echo e(__('Order #')); ?></th>
                        <th><?php echo e(__('Customer')); ?></th>
                        <th class="text-center"><?php echo e(__('Items')); ?></th>
                        <th class="text-end"><?php echo e(__('Total')); ?></th>
                        <th class="text-center"><?php echo e(__('Fulfillment')); ?></th>
                        <th class="text-center"><?php echo e(__('Payment')); ?></th>
                        <th><?php echo e(__('Date')); ?></th>
                        <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <a href="<?php echo e(route('orders.show', $order)); ?>" class="fw-bold text-primary text-decoration-none">
                                #<?php echo e($order->order_number); ?>

                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6"><?php echo e($order->customer_name); ?></div>
                            <div class="text-muted small"><?php echo e($order->customer_email); ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-secondary px-3 py-1" style="border-radius: 6px;">
                                <?php echo e($order->items->count()); ?>

                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6">
                            <?php echo e($order->formatted_total); ?>

                        </td>
                        <td class="text-center">
                            <select class="brand-badge-select status-update-ajax <?php echo e($order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info')); ?>" 
                                    data-order-id="<?php echo e($order->id); ?>"
                                    data-update-url="<?php echo e(route('orders.update', $order)); ?>">
                                <?php $__currentLoopData = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($st); ?>" <?php echo e($order->status === $st ? 'selected' : ''); ?>><?php echo e(__(ucfirst($st))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="text-center">
                            <select class="brand-badge-select payment-update-ajax <?php echo e($order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning')); ?>"
                                    data-order-id="<?php echo e($order->id); ?>"
                                    data-update-url="<?php echo e(route('orders.update', $order)); ?>">
                                <?php $__currentLoopData = ['pending', 'paid', 'failed', 'refunded']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($pst); ?>" <?php echo e($order->payment_status === $pst ? 'selected' : ''); ?>><?php echo e(__(ucfirst($pst))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td>
                            <div class="text-muted small"><?php echo e($order->created_at->format('M d, Y')); ?></div>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn-action-icon" title="<?php echo e(__('View Order')); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('orders.edit', $order)); ?>" class="btn-action-icon" title="<?php echo e(__('Edit')); ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-shopping-cart text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo e(__('No orders found')); ?></h5>
                                <p class="text-muted"><?php echo e(__('No order records matching your current selection.')); ?></p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($orders->hasPages()): ?>
        <div class="px-4 py-3 border-top">
            <?php echo e($orders->links()); ?>

        </div>
        <?php endif; ?>
    </div>

    <style>
    .brand-badge-select {
        border: none;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.8rem;
        border-radius: 99px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
        text-align: center;
        width: 120px;
        transition: all 0.3s ease;
    }

    .brand-badge-select.info { background: linear-gradient(135deg, #3b82f6, #2563eb) !important; color: #fff !important; }
    .brand-badge-select.success { background: linear-gradient(135deg, #10b981, #059669) !important; color: #fff !important; }
    .brand-badge-select.warning { background: linear-gradient(135deg, #f59e0b, #d97706) !important; color: #fff !important; }
    .brand-badge-select.danger { background: linear-gradient(135deg, #ef4444, #dc2626) !important; color: #fff !important; }
    
    .brand-badge-select option {
        background: white;
        color: #333;
        font-weight: normal;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateStatus = async (el, field) => {
            const orderId = el.dataset.orderId;
            const url = el.dataset.updateUrl;
            const value = el.value;
            const payload = { [field]: value };
            let dualUpdate = false;

            // Smart Workflow: Sync Payment on Delivery
            if (field === 'status' && value === 'delivered') {
                const result = await Swal.fire({
                    title: 'تحديث حالة الدفع؟',
                    text: 'هل ترغب في وضع علامة على هذا الطلب كمدفوع أيضاً؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، مدفوع',
                    cancelButtonText: 'لا، فقط موصول',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b'
                });

                if (result.isConfirmed) {
                    payload.payment_status = 'paid';
                    dualUpdate = true;
                }
            }
            
            // Add loading state
            el.style.opacity = '0.5';
            el.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                    
                    // Update classes
                    el.classList.remove('info', 'success', 'warning', 'danger');
                    if (field === 'status') {
                        el.classList.add(value === 'delivered' ? 'success' : (value === 'cancelled' ? 'danger' : 'info'));
                    } else {
                        el.classList.add(value === 'paid' ? 'success' : (value === 'failed' ? 'danger' : 'warning'));
                    }

                    // Refresh if multiple fields were updated
                    if (dualUpdate) {
                        setTimeout(() => location.reload(), 1500);
                    }
                } else {
                    throw new Error(data.message || 'Update failed');
                }
            } catch (error) {
                Toast.fire({
                    icon: 'error',
                    title: error.message
                });
                // Revert or reload
                location.reload();
            } finally {
                el.style.opacity = '1';
                el.disabled = false;
            }
        };

        document.querySelectorAll('.status-update-ajax').forEach(select => {
            select.addEventListener('change', () => updateStatus(select, 'status'));
        });

        document.querySelectorAll('.payment-update-ajax').forEach(select => {
            select.addEventListener('change', () => updateStatus(select, 'payment_status'));
        });
    });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/orders/index.blade.php ENDPATH**/ ?>