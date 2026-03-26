<?php $__env->startSection('title', __('Reports & Analytics')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <?php echo e(__('Reports & Analytics')); ?>

            </h1>
            <p class="brand-subtitle">
                <i class="fas fa-calendar me-1 opacity-50"></i>
                <?php echo e($startDate->format('M d, Y')); ?> — <?php echo e($endDate->format('M d, Y')); ?>

            </p>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <div class="btn-group bg-white p-1 rounded-3 shadow-soft" style="border: 1px solid rgba(0,0,0,0.05);">
                <?php $__currentLoopData = ['today' => 'Today', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button class="btn btn-sm <?php echo e($period === $key ? 'btn-primary' : 'btn-light border-0'); ?>" 
                            style="<?php echo e($period === $key ? 'border-radius: 8px;' : 'background: transparent; border-radius: 8px;'); ?>"
                            onclick="changePeriod('<?php echo e($key); ?>')">
                        <?php echo e(__($label)); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <button class="btn-brand-outline border-danger text-danger" onclick="exportPDF()">
                <i class="fas fa-file-pdf me-2"></i> PDF
            </button>
            <button class="btn-brand-outline border-success text-success" onclick="exportCSV()">
                <i class="fas fa-file-csv me-2"></i> CSV
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="brand-stats-grid mb-4">
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Total Revenue')); ?></div>
            <div class="brand-stat-value text-primary"><?php echo e(currency($metrics['total_revenue'])); ?></div>
            <div class="brand-stat-desc">
                <span class="<?php echo e($metrics['revenue_change'] >= 0 ? 'text-success' : 'text-danger'); ?> fw-bold">
                    <i class="fas fa-<?php echo e($metrics['revenue_change'] >= 0 ? 'arrow-up' : 'arrow-down'); ?> me-1"></i>
                    <?php echo e(number_format(abs($metrics['revenue_change']), 1)); ?>%
                </span>
                <span class="ms-1"><?php echo e(__('vs previous period')); ?></span>
            </div>
        </div>

        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Total Orders')); ?></div>
            <div class="brand-stat-value text-success"><?php echo e(number_format($metrics['total_orders'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-check-circle me-1 opacity-50"></i> <?php echo e(__('Completed transactions')); ?>

            </div>
        </div>

        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-box"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Products Sold')); ?></div>
            <div class="brand-stat-value text-info"><?php echo e(number_format($metrics['products_sold'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-cubes me-1 opacity-50"></i> <?php echo e(__('Total units moved')); ?>

            </div>
        </div>

        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="brand-stat-label"><?php echo e(__('Avg Order Value')); ?></div>
            <div class="brand-stat-value text-warning"><?php echo e(currency($metrics['avg_order_value'])); ?></div>
            <div class="brand-stat-desc">
                <i class="fas fa-chart-line me-1 opacity-50"></i> <?php echo e(__('Average per ticket')); ?>

            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="brand-table-card p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="brand-stat-icon primary small me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0"><?php echo e(__('Revenue Trend')); ?></h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="brand-table-card p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="brand-stat-icon success small me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0"><?php echo e(__('Top Performing Products')); ?></h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="productsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="brand-table-card p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="brand-stat-icon info small me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0"><?php echo e(__('Category Breakdown')); ?></h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="brand-table-card p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="brand-stat-icon warning small me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0"><?php echo e(__('Order Status Distribution')); ?></h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row g-4">
        <!-- Low Stock Alerts -->
        <?php if($lowStockProducts->count() > 0): ?>
        <div class="col-lg-12">
            <div class="brand-table-card">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="background: rgba(239, 68, 68, 0.03);">
                    <div class="d-flex align-items-center text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <h5 class="fw-bold m-0"><?php echo e(__('Critical Inventory Levels')); ?> (<?php echo e($lowStockProducts->count()); ?>)</h5>
                    </div>
                    <a href="<?php echo e(route('inventory.index')); ?>" class="btn-brand-light text-danger"><?php echo e(__('Full Audit')); ?></a>
                </div>
                <div class="table-responsive">
                    <table class="brand-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 1.5rem;"><?php echo e(__('Product')); ?></th>
                                <th><?php echo e(__('Category')); ?></th>
                                <th class="text-center"><?php echo e(__('Available')); ?></th>
                                <th class="text-center"><?php echo e(__('Minimum')); ?></th>
                                <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Status')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="padding-left: 1.5rem;">
                                    <div class="fw-bold text-dark"><?php echo e($product->translated_name); ?></div>
                                    <div class="text-muted small">SKU: <?php echo e($product->sku); ?></div>
                                </td>
                                <td><?php echo e($product->category->name ?? 'N/A'); ?></td>
                                <td class="text-center fw-bold text-danger"><?php echo e($product->stock); ?></td>
                                <td class="text-center text-muted"><?php echo e($product->min_stock); ?></td>
                                <td class="text-end" style="padding-right: 1.5rem;">
                                    <span class="brand-badge danger"><?php echo e(__('REPLENISH')); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Transactions -->
        <div class="col-lg-12">
            <div class="brand-table-card">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark m-0"><?php echo e(__('Recent Transactions Reference')); ?></h5>
                    <a href="<?php echo e(route('orders.index')); ?>" class="btn-brand-light"><?php echo e(__('History')); ?></a>
                </div>
                <div class="table-responsive">
                    <table class="brand-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 1.5rem;"><?php echo e(__('Order #')); ?></th>
                                <th><?php echo e(__('Customer')); ?></th>
                                <th><?php echo e(__('Timestamp')); ?></th>
                                <th class="text-center"><?php echo e(__('Qty')); ?></th>
                                <th class="text-end"><?php echo e(__('Value')); ?></th>
                                <th class="text-center" style="padding-right: 1.5rem;"><?php echo e(__('Status')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="padding-left: 1.5rem;">
                                    <span class="fw-bold text-primary">#<?php echo e($order->order_number); ?></span>
                                </td>
                                <td><?php echo e($order->customer_name); ?></td>
                                <td class="text-muted small"><?php echo e($order->created_at->format('M d, Y H:i')); ?></td>
                                <td class="text-center"><?php echo e($order->items->count()); ?></td>
                                <td class="text-end fw-bold text-dark"><?php echo e(currency($order->total)); ?></td>
                                <td class="text-center" style="padding-right: 1.5rem;">
                                    <span class="brand-badge <?php echo e($order->status === 'completed' ? 'success' : 'info'); ?>">
                                        <?php echo e(__($order->status == 'completed' ? 'Completed' : ($order->status == 'pending' ? 'Pending' : 'Cancelled'))); ?>

                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    function changePeriod(period) { window.location.href = `<?php echo e(route('reports.index')); ?>?period=${period}`; }
    function exportPDF() { window.location.href = `<?php echo e(route('reports.export.pdf')); ?>?period=<?php echo e($period); ?>`; }
    function exportCSV() { window.location.href = `<?php echo e(route('reports.export.csv')); ?>?period=<?php echo e($period); ?>`; }

    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color = '#64748b';

    const colors = { primary: '#6366f1', success: '#10b981', warning: '#f59e0b', danger: '#ef4444', purple: '#8b5cf6', teal: '#14b8a6' };

    // Common options
    const doughnutOptions = {
        responsive: true, maintainAspectRatio: false, cutout: '70%',
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 11 } } } }
    };

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($revenueChartData['labels'], 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($revenueChartData['revenue'], 15, 512) ?>,
                borderColor: colors.primary,
                backgroundColor: 'rgba(99, 102, 241, 0.05)',
                borderWidth: 3, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { borderDash: [5, 5], color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // Top Products Chart
    new Chart(document.getElementById('productsChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($topProducts->pluck('product_name'), 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($topProducts->pluck('revenue'), 15, 512) ?>,
                backgroundColor: [colors.primary, colors.success, colors.warning, colors.purple, colors.teal],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { borderDash: [5, 5], color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 45, minRotation: 45 } }
            }
        }
    });

    // Category Chart
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($categoryBreakdown->pluck('category_name'), 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($categoryBreakdown->pluck('revenue'), 15, 512) ?>,
                backgroundColor: [colors.primary, colors.success, colors.warning, colors.purple, colors.teal, colors.danger],
                borderWidth: 0
            }]
        },
        options: doughnutOptions
    });

    // Status Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($orderStatus->pluck('status')->map(fn($s) => ucfirst($s)), 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($orderStatus->pluck('count'), 15, 512) ?>,
                backgroundColor: [colors.warning, colors.primary, colors.purple, colors.success, colors.danger],
                borderWidth: 0
            }]
        },
        options: doughnutOptions
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/reports/index.blade.php ENDPATH**/ ?>