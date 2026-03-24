<?php $__env->startSection('title', __('Dashboard')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <?php echo e(__('Business Overview')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Welcome back')); ?>, <?php echo e(auth()->user()->name); ?>! <?php echo e(__('Here\'s the latest pulse of your business.')); ?></p>
        </div>

    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-stats">
        <div class="stat-card glass-card">
            <div class="stat-card-header">
                <div class="stat-card-title"><?php echo e(__('Total Revenue')); ?></div>
                <div class="stat-card-icon primary">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stat-card-value font-inter"><?php echo e(currency($stats['total_revenue'])); ?></div>
            <div class="stat-card-desc">
                <?php $rGrowth = $stats['revenue_growth']; ?>
                <span class="<?php echo e($rGrowth >= 0 ? 'text-success' : 'text-danger'); ?>">
                    <i class="fas fa-arrow-<?php echo e($rGrowth >= 0 ? 'up' : 'down'); ?> me-1"></i>
                    <?php echo e(abs($rGrowth)); ?>%
                </span>
                <span class="ms-1"><?php echo e(__('vs last month')); ?></span>
            </div>
        </div>

        <div class="stat-card glass-card">
            <div class="stat-card-header">
                <div class="stat-card-title"><?php echo e(__('Total Orders')); ?></div>
                <div class="stat-card-icon success">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
            <div class="stat-card-value font-inter"><?php echo e(number_format($stats['total_orders'])); ?></div>
            <div class="stat-card-desc">
                <?php $oGrowth = $stats['orders_growth']; ?>
                <span class="<?php echo e($oGrowth >= 0 ? 'text-success' : 'text-danger'); ?>">
                    <i class="fas fa-arrow-<?php echo e($oGrowth >= 0 ? 'up' : 'down'); ?> me-1"></i>
                    <?php echo e(abs($oGrowth)); ?>%
                </span>
                <span class="ms-1"><?php echo e(__('vs last month')); ?></span>
            </div>
        </div>

        <div class="stat-card glass-card">
            <div class="stat-card-header">
                <div class="stat-card-title"><?php echo e(__('Total Products')); ?></div>
                <div class="stat-card-icon warning">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <div class="stat-card-value font-inter"><?php echo e(number_format($stats['total_products'])); ?></div>
            <div class="stat-card-desc">
                <?php $pGrowth = $stats['products_growth']; ?>
                <span class="<?php echo e($pGrowth >= 0 ? 'text-success' : 'text-danger'); ?>">
                    <i class="fas fa-arrow-<?php echo e($pGrowth >= 0 ? 'up' : 'down'); ?> me-1"></i>
                    <?php echo e(abs($pGrowth)); ?>%
                </span>
                <span class="ms-1"><?php echo e(__('vs last month')); ?></span>
            </div>
        </div>

        <div class="stat-card glass-card">
            <div class="stat-card-header">
                <div class="stat-card-title"><?php echo e(__('Total Customers')); ?></div>
                <div class="stat-card-icon info">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
            <div class="stat-card-value font-inter"><?php echo e(number_format($stats['total_users'])); ?></div>
            <div class="stat-card-desc">
                <?php $uGrowth = $stats['users_growth']; ?>
                <span class="<?php echo e($uGrowth >= 0 ? 'text-success' : 'text-danger'); ?>">
                    <i class="fas fa-arrow-<?php echo e($uGrowth >= 0 ? 'up' : 'down'); ?> me-1"></i>
                    <?php echo e(abs($uGrowth)); ?>%
                </span>
                <span class="ms-1"><?php echo e(__('vs last month')); ?></span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">

        <div class="col-lg-12">
            <div class="brand-table-card h-100 p-4">
                <h5 class="fw-bold text-dark mb-4"><?php echo e(__('Order Distribution')); ?></h5>
                <div style="height: 300px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="brand-table-card h-100 overflow-hidden">
                <div class="recent-activity-header p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-history text-primary"></i> <?php echo e(__('Recent Activity')); ?>

                    </h5>
                    <a href="<?php echo e(route('orders.index')); ?>" class="btn-brand-light btn-sm rounded-pill px-3"><?php echo e(__('View All Orders')); ?></a>
                </div>
                <div class="responsive-table-container">
                    <table class="brand-table d-none d-lg-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 1.5rem;"><?php echo e(__('Order')); ?></th>
                                <th><?php echo e(__('Customer')); ?></th>
                                <th class="text-center"><?php echo e(__('Items')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Total')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="font-inter">
                            <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr style="cursor: pointer;" onclick="window.location='<?php echo e(route('orders.show', $order)); ?>'">
                                <td style="padding-left: 1.5rem;">
                                    <span class="badge bg-light text-primary font-monospace py-2 px-3" style="border-radius: 8px; border: 1px solid #e0e7ff;">
                                        #<?php echo e($order->order_number); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="brand-avatar-ring">
                                            <div class="brand-avatar" style="width: 34px; height: 34px; background: white; color: var(--primary-color); font-size: 0.8rem; font-weight: 800; border: 1px solid #e0e7ff;">
                                                <?php echo e(strtoupper(substr($order->customer_name, 0, 1))); ?>

                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small"><?php echo e($order->customer_name); ?></div>
                                            <div class="text-muted" style="font-size: 0.7rem;"><?php echo e(Str::limit($order->customer_email, 22)); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="brand-badge info px-2 py-1" style="font-size: 0.65rem;">
                                        <?php echo e($order->items_count); ?> <?php echo e(__('Items')); ?>

                                    </span>
                                </td>
                                <td>
                                    <span class="brand-badge <?php echo e($order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger')); ?>">
                                        <?php echo e(__($order->status == 'completed' ? 'Completed' : ($order->status == 'pending' ? 'Pending' : 'Cancelled'))); ?>

                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark" style="padding-right: 1.5rem;">
                                    <?php echo e(currency($order->total)); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-5">
                                        <i class="fas fa-receipt text-muted opacity-25 fs-1 mb-3"></i>
                                        <p class="text-muted"><?php echo e(__('No recent activity found')); ?></p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Mobile Card List for Recent Activity -->
                    <div class="mobile-card-list d-lg-none p-3">
                        <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="mobile-product-card glass-card mb-3" onclick="window.location='<?php echo e(route('orders.show', $order)); ?>'">
                                <div class="mobile-product-info">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="mobile-product-name">#<?php echo e($order->order_number); ?></div>
                                        <span class="brand-badge <?php echo e($order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger')); ?>">
                                            <?php echo e(__($order->status == 'completed' ? 'Completed' : ($order->status == 'pending' ? 'Pending' : 'Cancelled'))); ?>

                                        </span>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-user-circle ml-1"></i> <?php echo e($order->customer_name); ?>

                                    </div>
                                    <div class="mobile-product-meta">
                                        <div class="mobile-product-price font-inter">
                                            <?php echo e(currency($order->total)); ?>

                                        </div>
                                        <div class="small text-muted font-inter">
                                            <?php echo e($order->created_at->format('M d')); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-5 text-muted">
                                <?php echo e(__('No recent activity found')); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Warning Widget -->
        <div class="col-lg-4">
            <div class="brand-table-card alert-glass-card h-100 overflow-hidden">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white bg-opacity-50">
                    <h5 class="fw-bold text-danger m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-bell-exclamation pulse-slow"></i> <?php echo e(__('Inventory Alerts')); ?>

                    </h5>
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-2 py-1 font-inter"><?php echo e(count($lowStockProducts)); ?></span>
                </div>
                <div class="p-4">
                    <?php if(count($lowStockProducts) > 0): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex align-items-center gap-3 pb-3 border-bottom border-light">
                                <div class="brand-avatar" style="width: 40px; height: 40px;">
                                    <?php if($product->image): ?>
                                        <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="">
                                    <?php else: ?>
                                        <i class="fas fa-box"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-dark fw-bold small line-clamp-1"><?php echo e($product->translated_name); ?></div>
                                    <div class="text-muted" style="font-size: 11px;">SKU: <?php echo e($product->sku); ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="text-danger fw-bold fs-6"><?php echo e($product->stock); ?></div>
                                    <div class="text-muted small" style="font-size: 10px;"><?php echo e(__('left')); ?></div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('inventory.index')); ?>" class="btn-brand-outline w-100 justify-content-center mt-2 border-danger text-danger">
                                <?php echo e(__('Resolve Alerts')); ?> <i class="fas fa-arrow-right ms-2 fs-xs"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="brand-avatar mx-auto mb-3" style="background: #f0fdf4; color: #16a34a; width: 64px; height: 64px; font-size: 24px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h6 class="fw-bold text-dark"><?php echo e(__('Healthy Inventory')); ?></h6>
                            <p class="text-muted small"><?php echo e(__('No low stock alerts at the moment.')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    }
                }
            };

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['<?php echo e(__('Mon')); ?>', '<?php echo e(__('Tue')); ?>', '<?php echo e(__('Wed')); ?>', '<?php echo e(__('Thu')); ?>', '<?php echo e(__('Fri')); ?>', '<?php echo e(__('Sat')); ?>', '<?php echo e(__('Sun')); ?>'],
                    datasets: [{
                        label: '<?php echo e(__('Revenue')); ?>',
                        data: [1200, 1900, 3000, 500, 2000, 3000, 4500],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.05)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: chartOptions
            });

            // Order Status Chart
            const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['<?php echo e(__('Pending')); ?>', '<?php echo e(__('Completed')); ?>', '<?php echo e(__('Cancelled')); ?>'],
                    datasets: [{
                        data: [30, 50, 20],
                        backgroundColor: ['#fbbf24', '#10b981', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 11 } } }
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/dashboard.blade.php ENDPATH**/ ?>