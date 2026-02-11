@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                Business Overview
            </h1>
            <p class="brand-subtitle">Welcome back, {{ auth()->user()->name }}! Here's the latest pulse of your business.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pos.index') }}" class="btn-brand-primary">
                <i class="fas fa-cash-register me-2"></i> POS Terminal
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid">
        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-users"></i>
            </div>
            <div class="brand-stat-label">Total Users</div>
            <div class="brand-stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="brand-stat-desc">
                @php $uGrowth = $stats['users_growth']; @endphp
                <span class="{{ $uGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-arrow-{{ $uGrowth >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ abs($uGrowth) }}%
                </span>
                <span class="ms-1">vs last month</span>
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="brand-stat-label">Total Orders</div>
            <div class="brand-stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="brand-stat-desc">
                @php $oGrowth = $stats['orders_growth']; @endphp
                <span class="{{ $oGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-arrow-{{ $oGrowth >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ abs($oGrowth) }}%
                </span>
                <span class="ms-1">vs last month</span>
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="brand-stat-label">Total Revenue</div>
            <div class="brand-stat-value">{{ currency($stats['total_revenue']) }}</div>
            <div class="brand-stat-desc">
                @php $rGrowth = $stats['revenue_growth']; @endphp
                <span class="{{ $rGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-arrow-{{ $rGrowth >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ abs($rGrowth) }}%
                </span>
                <span class="ms-1">vs last month</span>
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-box"></i>
            </div>
            <div class="brand-stat-label">Total Products</div>
            <div class="brand-stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="brand-stat-desc">
                @php $pGrowth = $stats['products_growth']; @endphp
                <span class="{{ $pGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-arrow-{{ $pGrowth >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ abs($pGrowth) }}%
                </span>
                <span class="ms-1">vs last month</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="brand-table-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark m-0">Revenue Overview</h5>
                    <div class="badge bg-light text-primary py-2 px-3" style="border-radius: 8px;">Weekly Performance</div>
                </div>
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="brand-table-card h-100 p-4">
                <h5 class="fw-bold text-dark mb-4">Order Distribution</h5>
                <div style="height: 300px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="brand-table-card">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <h5 class="fw-bold text-dark m-0">Recent Activity</h5>
                    <a href="{{ route('orders.index') }}" class="btn-brand-light">View All Orders</a>
                </div>
                <div class="table-responsive">
                    <table class="brand-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 1.5rem;">Order</th>
                                <th>Customer</th>
                                <th class="text-center">Items</th>
                                <th>Status</th>
                                <th class="text-end" style="padding-right: 1.5rem;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('orders.show', $order) }}'">
                                <td style="padding-left: 1.5rem;">
                                    <span class="badge bg-light text-primary font-monospace py-2 px-3" style="border-radius: 8px; border: 1px solid #e0e7ff;">
                                        #{{ $order->order_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="brand-avatar" style="width: 36px; height: 36px; background: var(--gradient-primary); color: white; font-size: 0.8rem; font-weight: 700;">
                                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $order->customer_name }}</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">{{ Str::limit($order->customer_email, 22) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="brand-badge info px-2 py-1" style="font-size: 0.65rem;">
                                        {{ $order->items_count }} Items
                                    </span>
                                </td>
                                <td>
                                    <span class="brand-badge {{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark" style="padding-right: 1.5rem;">
                                    {{ currency($order->total) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-5">
                                        <i class="fas fa-receipt text-muted opacity-25 fs-1 mb-3"></i>
                                        <p class="text-muted">No recent activity found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Inventory Warning Widget -->
        <div class="col-lg-4">
            <div class="brand-table-card h-100">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="background: rgba(239, 68, 68, 0.03);">
                    <h5 class="fw-bold text-danger m-0">Inventory Alerts</h5>
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                </div>
                <div class="p-4">
                    @if(count($lowStockProducts) > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach($lowStockProducts as $product)
                            <div class="d-flex align-items-center gap-3 pb-3 border-bottom border-light">
                                <div class="brand-avatar" style="width: 40px; height: 40px;">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="">
                                    @else
                                        <i class="fas fa-box"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-dark fw-bold small line-clamp-1">{{ $product->name }}</div>
                                    <div class="text-muted" style="font-size: 11px;">SKU: {{ $product->sku }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="text-danger fw-bold fs-6">{{ $product->stock }}</div>
                                    <div class="text-muted small" style="font-size: 10px;">left</div>
                                </div>
                            </div>
                            @endforeach
                            <a href="{{ route('inventory.index') }}" class="btn-brand-outline w-100 justify-content-center mt-2 border-danger text-danger">
                                Resolve Alerts <i class="fas fa-arrow-right ms-2 fs-xs"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="brand-avatar mx-auto mb-3" style="background: #f0fdf4; color: #16a34a; width: 64px; height: 64px; font-size: 24px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Healthy Inventory</h6>
                            <p class="text-muted small">No low stock alerts at the moment.</p>
                        </div>
                    @endif
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
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Revenue',
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
                    labels: ['Pending', 'Completed', 'Cancelled'],
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
@endsection
