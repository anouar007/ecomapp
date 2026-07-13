@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <!-- Page Header -->
    <div class="brand-header px-1">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                {{ __('Administrative Control') }}
            </h1>
            <p class="brand-subtitle">{{ __('Welcome back') }}, {{ auth()->user()->name }}. {{ __('Overview of your business performance.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('orders.index') }}" class="btn btn-brand-primary font-inter">
                <i class="fas fa-plus-circle me-1"></i> {{ __('Manage Orders') }}
            </a>
        </div>
    </div>


    <!-- Top Level Stats (Row 1) -->
    <div class="row g-3 mb-4">
        <!-- New: Today's Revenue -->
        <div class="col-12 col-md-4">
            <div class="stat-card glass-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">{{ __('Today\'s Revenue') }}</div>
                    <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-money-bill-trend-up"></i>
                    </div>
                </div>
                <div class="stat-card-value font-inter">{{ currency($stats['today_revenue']) }}</div>
                <div class="stat-card-desc">
                    <span class="text-success fw-bold"><i class="fas fa-calendar-day me-1"></i></span>
                    <span class="ms-1 text-muted small">{{ __('today') }}</span>
                </div>
            </div>
        </div>

        <!-- New: Pending Orders -->
        <div class="col-12 col-md-4">
            <div class="stat-card glass-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">{{ __('Pending Orders') }}</div>
                    <div class="stat-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div class="stat-card-value font-inter text-warning">{{ number_format($stats['pending_orders']) }}</div>
                <div class="stat-card-desc">
                    <span class="text-warning fw-bold"><i class="fas fa-exclamation-circle me-1"></i></span>
                    <span class="ms-1 text-muted small">{{ __('unprocessed items') }}</span>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-12 col-md-4">
            <div class="stat-card glass-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">{{ __('Total Revenue') }}</div>
                    <div class="stat-card-icon primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="stat-card-value font-inter">{{ currency($stats['total_revenue']) }}</div>
                <div class="stat-card-desc">
                    <span class="{{ $stats['revenue_growth'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                        <i class="fas fa-arrow-{{ $stats['revenue_growth'] >= 0 ? 'up' : 'down' }} me-1"></i>
                        {{ abs($stats['revenue_growth']) }}%
                    </span>
                    <span class="ms-1 text-muted small">{{ __('this month') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats (Row 2) -->
    <div class="dashboard-stats mb-4">
        <div class="stat-card glass-card">
            <div class="stat-card-header">
                <div class="stat-card-title">{{ __('Total Orders') }}</div>
                <div class="stat-card-icon success">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <div class="stat-card-value font-inter">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-card-desc">
                <span class="{{ $stats['orders_growth'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                    <i class="fas fa-arrow-{{ $stats['orders_growth'] >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ abs($stats['orders_growth']) }}%
                </span>
                <span class="ms-1 text-muted small">{{ __('this month') }}</span>
            </div>
        </div>

        <div class="stat-card glass-card">
            <div class="stat-card-header">
                <div class="stat-card-title">{{ __('Active Products') }}</div>
                <div class="stat-card-icon warning">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
            <div class="stat-card-value font-inter">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-card-desc">
                <span class="text-success fw-bold">+{{ $stats['products_growth'] }}%</span>
                <span class="ms-1 text-muted small">{{ __('new additions') }}</span>
            </div>
        </div>

        <div class="stat-card glass-card">
            <div class="stat-card-header">
                <div class="stat-card-title">{{ __('Total Customers') }}</div>
                <div class="stat-card-icon info">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-card-value font-inter">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-card-desc">
                <span class="text-success fw-bold">+{{ $stats['users_growth'] }}%</span>
                <span class="ms-1 text-muted small">{{ __('growth') }}</span>
            </div>
        </div>
    </div>

    <!-- Analytics & Activity Grid (Row 2 & 3 Combined for PC Space Optimization) -->
    <div class="row g-4 mb-4">
        <!-- Revenue Trend (Large Chart) -->
        <div class="col-lg-8">
            <div class="brand-table-card h-100 p-4">
                <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                    <h5 class="dashboard-card-title">{{ __('Revenue Trend') }}</h5>
                    <div class="small text-muted font-inter">{{ __('Last 7 Days') }}</div>
                </div>
                <div style="height: 350px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Order Distribution (Donut) -->
        <div class="col-lg-4">
            <div class="brand-table-card h-100 p-4">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">{{ __('Order Distribution') }}</h5>
                </div>
                <div style="height: 280px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted"><i class="fas fa-circle text-warning me-2"></i> {{ __('Pending') }}</span>
                        <span class="fw-bold font-inter">{{ $orderStatusCounts['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted"><i class="fas fa-circle text-success me-2"></i> {{ __('Completed') }}</span>
                        <span class="fw-bold font-inter">{{ $orderStatusCounts['completed'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders Table -->
        <div class="col-lg-6">
            <div class="brand-table-card h-100 overflow-hidden">
                <div class="recent-activity-header p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <h5 class="dashboard-card-title">
                        <i class="fas fa-history text-primary me-2"></i> {{ __('Recent Activity') }}
                    </h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-link btn-sm text-primary text-decoration-none p-0">{{ __('View All') }}</a>
                </div>
                <div class="responsive-table-container">
                    <table class="brand-table d-none d-lg-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('Order') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="pe-4 text-end">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="font-inter">
                            @foreach($recentOrders as $order)
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('orders.show', $order) }}'">
                                <td class="ps-4">
                                    <span class="fw-bold text-primary">#{{ $order->order_number }}</span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $order->customer_name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $order->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $order->status === 'completed' ? 'bg-success' : ($order->status === 'pending' ? 'bg-warning' : 'bg-danger') }} bg-opacity-10 {{ $order->status === 'completed' ? 'text-success' : ($order->status === 'pending' ? 'text-warning' : 'text-danger') }} px-2">
                                        {{ __($order->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end fw-bold">{{ currency($order->total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Mobile View -->
                    <div class="d-lg-none p-3">
                        @foreach($recentOrders as $order)
                        <div class="p-3 border-bottom border-light" onclick="window.location='{{ route('orders.show', $order) }}'">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">#{{ $order->order_number }}</span>
                                <span class="fw-bold text-primary">{{ currency($order->total) }}</span>
                            </div>
                            <div class="text-muted small">{{ $order->customer_name }} • {{ __($order->status) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-lg-3">
            <div class="brand-table-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom bg-white">
                    <h5 class="dashboard-card-title">{{ __('Top Performance') }}</h5>
                </div>
                <div class="scroll-y-300">
                    @forelse($topSellingProducts as $item)
                    <div class="top-product-item d-flex align-items-center gap-3">
                        <div class="brand-avatar" style="width: 40px; height: 40px; border-radius: 8px;">
                            @if($item->product && $item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius: 8px;">
                            @else
                                <i class="fas fa-box text-muted"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-bold text-dark small text-truncate">{{ $item->product ? $item->product->translated_name : __('Unknown') }}</div>
                            <div class="text-muted" style="font-size: 10px;">{{ __('Sales qty') }}: {{ $item->total_qty }}</div>
                        </div>
                        <span class="qty-pill font-inter">{{ $item->total_qty }}</span>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted small">{{ __('No sales data yet') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Inventory Warning Widget -->
        <div class="col-lg-3">
            <div class="brand-table-card alert-glass-card h-100 overflow-hidden" style="background: rgba(254, 242, 242, 0.4);">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white bg-opacity-50">
                    <h5 class="dashboard-card-title text-danger">
                        <i class="fas fa-exclamation-triangle pulse-slow me-1"></i> {{ __('Low Stock') }}
                    </h5>
                    <span class="badge bg-danger text-white font-inter">{{ count($lowStockProducts) }}</span>
                </div>
                <div class="p-3">
                    @forelse($lowStockProducts as $product)
                    <div class="d-flex align-items-center gap-3 mb-3 pb-2 border-bottom border-danger border-opacity-10">
                        <div class="flex-grow-1 min-width-0">
                            <div class="text-dark fw-bold small text-truncate">{{ $product->translated_name }}</div>
                            <div class="text-muted" style="font-size: 10px;">SKU: {{ $product->sku }}</div>
                        </div>
                        <div class="text-end">
                            <div class="text-danger fw-bold font-inter">{{ $product->stock }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fs-3 mb-2"></i>
                        <div class="small fw-bold">{{ __('Stock Healthy') }}</div>
                    </div>
                    @endforelse
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-danger btn-sm w-100 mt-2 font-inter fw-bold">{{ __('Manage Stock') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Product Views -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="brand-table-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                    <h5 class="dashboard-card-title m-0">{{ __('Product Views') }} ({{ __('Today') }} vs {{ __('Yesterday') }})</h5>
                    <i class="fas fa-eye text-primary"></i>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('Product') }}</th>
                                <th class="text-center">{{ __('Today') }}</th>
                                <th class="text-center pe-4">{{ __('Yesterday') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productViews as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="brand-avatar" style="width: 40px; height: 40px; border-radius: 8px;">
                                            @if($item['product'] && $item['product']->main_image)
                                                <img src="{{ asset('storage/' . $item['product']->main_image) }}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius: 8px;">
                                            @else
                                                <div style="width:100%; height:100%; background:#f3f4f6; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="fw-bold text-dark small">{{ $item['product'] ? $item['product']->translated_name : __('Unknown') }}</div>
                                    </div>
                                </td>
                                <td class="text-center font-inter">
                                    <span class="badge bg-primary text-white">{{ $item['today_views'] }}</span>
                                </td>
                                <td class="text-center pe-4 font-inter">
                                    <span class="badge bg-secondary text-white">{{ $item['yesterday_views'] }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center p-4 text-muted small">{{ __('No views data available yet') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // General Chart Options
            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            };

            // Revenue Trend Chart (Line)
            const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
            new Chart(revenueTrendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($revenueLabels) !!},
                    datasets: [{
                        label: '{{ __('Daily Revenue') }}',
                        data: {!! json_encode($revenueData) !!},
                        borderColor: '#6366f1',
                        backgroundColor: (context) => {
                            const ctx = context.chart.ctx;
                            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
                            gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');
                            return gradient;
                        },
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    ...chartDefaults,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { family: 'Cairo', size: 11 } }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { font: { family: 'Cairo', size: 11 } }
                        }
                    }
                }
            });

            // Order Status Chart (Doughnut)
            const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['{{ __('Pending') }}', '{{ __('Completed') }}', '{{ __('Cancelled') }}'],
                    datasets: [{
                        data: [
                            {{ $orderStatusCounts['pending'] }}, 
                            {{ $orderStatusCounts['completed'] }}, 
                            {{ $orderStatusCounts['cancelled'] }}
                        ],
                        backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 20
                    }]
                },
                options: {
                    ...chartDefaults,
                    cutout: '80%',
                    plugins: {
                        legend: { 
                            display: true, 
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { family: 'Cairo', size: 12 }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
