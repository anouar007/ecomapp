@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                Reports & Analytics
            </h1>
            <p class="brand-subtitle">
                <i class="fas fa-calendar me-1 opacity-50"></i>
                {{ $startDate->format('M d, Y') }} — {{ $endDate->format('M d, Y') }}
            </p>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <div class="btn-group bg-white p-1 rounded-3 shadow-soft" style="border: 1px solid rgba(0,0,0,0.05);">
                @foreach(['today' => 'Today', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $key => $label)
                    <button class="btn btn-sm {{ $period === $key ? 'btn-primary' : 'btn-light border-0' }}" 
                            style="{{ $period === $key ? 'border-radius: 8px;' : 'background: transparent; border-radius: 8px;' }}"
                            onclick="changePeriod('{{ $key }}')">
                        {{ $label }}
                    </button>
                @endforeach
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
            <div class="brand-stat-label">Total Revenue</div>
            <div class="brand-stat-value text-primary">{{ currency($metrics['total_revenue']) }}</div>
            <div class="brand-stat-desc">
                <span class="{{ $metrics['revenue_change'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                    <i class="fas fa-{{ $metrics['revenue_change'] >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                    {{ number_format(abs($metrics['revenue_change']), 1) }}%
                </span>
                <span class="ms-1">vs previous period</span>
            </div>
        </div>

        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="brand-stat-label">Total Orders</div>
            <div class="brand-stat-value text-success">{{ number_format($metrics['total_orders']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-check-circle me-1 opacity-50"></i> Completed transactions
            </div>
        </div>

        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-box"></i>
            </div>
            <div class="brand-stat-label">Products Sold</div>
            <div class="brand-stat-value text-info">{{ number_format($metrics['products_sold']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-cubes me-1 opacity-50"></i> Total units moved
            </div>
        </div>

        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="brand-stat-label">Avg Order Value</div>
            <div class="brand-stat-value text-warning">{{ currency($metrics['avg_order_value']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-chart-line me-1 opacity-50"></i> Average per ticket
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
                    <h5 class="fw-bold text-dark m-0">Revenue Trend</h5>
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
                    <h5 class="fw-bold text-dark m-0">Top Performing Products</h5>
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
                    <h5 class="fw-bold text-dark m-0">Category Breakdown</h5>
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
                    <h5 class="fw-bold text-dark m-0">Order Status Distribution</h5>
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
        @if($lowStockProducts->count() > 0)
        <div class="col-lg-12">
            <div class="brand-table-card">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="background: rgba(239, 68, 68, 0.03);">
                    <div class="d-flex align-items-center text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <h5 class="fw-bold m-0">Critical Inventory Levels ({{ $lowStockProducts->count() }})</h5>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="btn-brand-light text-danger">Full Audit</a>
                </div>
                <div class="table-responsive">
                    <table class="brand-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 1.5rem;">Product</th>
                                <th>Category</th>
                                <th class="text-center">Available</th>
                                <th class="text-center">Minimum</th>
                                <th class="text-end" style="padding-right: 1.5rem;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                            <tr>
                                <td style="padding-left: 1.5rem;">
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    <div class="text-muted small">SKU: {{ $product->sku }}</div>
                                </td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td class="text-center fw-bold text-danger">{{ $product->stock }}</td>
                                <td class="text-center text-muted">{{ $product->min_stock }}</td>
                                <td class="text-end" style="padding-right: 1.5rem;">
                                    <span class="brand-badge danger">REPLENISH</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Transactions -->
        <div class="col-lg-12">
            <div class="brand-table-card">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark m-0">Recent Transactions Reference</h5>
                    <a href="{{ route('orders.index') }}" class="btn-brand-light">History</a>
                </div>
                <div class="table-responsive">
                    <table class="brand-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 1.5rem;">Order #</th>
                                <th>Customer</th>
                                <th>Timestamp</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Value</th>
                                <th class="text-center" style="padding-right: 1.5rem;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td style="padding-left: 1.5rem;">
                                    <span class="fw-bold text-primary">#{{ $order->order_number }}</span>
                                </td>
                                <td>{{ $order->customer_name }}</td>
                                <td class="text-muted small">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td class="text-center">{{ $order->items->count() }}</td>
                                <td class="text-end fw-bold text-dark">{{ currency($order->total) }}</td>
                                <td class="text-center" style="padding-right: 1.5rem;">
                                    <span class="brand-badge {{ $order->status === 'completed' ? 'success' : 'info' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    function changePeriod(period) { window.location.href = `{{ route('reports.index') }}?period=${period}`; }
    function exportPDF() { window.location.href = `{{ route('reports.export.pdf') }}?period={{ $period }}`; }
    function exportCSV() { window.location.href = `{{ route('reports.export.csv') }}?period={{ $period }}`; }

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
            labels: @json($revenueChartData['labels']),
            datasets: [{
                data: @json($revenueChartData['revenue']),
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
            labels: @json($topProducts->pluck('product_name')),
            datasets: [{
                data: @json($topProducts->pluck('revenue')),
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
            labels: @json($categoryBreakdown->pluck('category_name')),
            datasets: [{
                data: @json($categoryBreakdown->pluck('revenue')),
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
            labels: @json($orderStatus->pluck('status')->map(fn($s) => ucfirst($s))),
            datasets: [{
                data: @json($orderStatus->pluck('count')),
                backgroundColor: [colors.warning, colors.primary, colors.purple, colors.success, colors.danger],
                borderWidth: 0
            }]
        },
        options: doughnutOptions
    });
</script>
@endpush
