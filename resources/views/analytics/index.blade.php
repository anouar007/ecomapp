@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Advanced Analytics</h1>
        <div>
            <a href="{{ route('analytics.export', ['period' => $period]) }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Report
            </a>
            <div class="btn-group ml-2" role="group">
                <a href="{{ route('analytics.index', ['period' => 'week']) }}" class="btn btn-sm btn-{{ $period == 'week' ? 'secondary' : 'light' }}">Week</a>
                <a href="{{ route('analytics.index', ['period' => 'month']) }}" class="btn btn-sm btn-{{ $period == 'month' ? 'secondary' : 'light' }}">Month</a>
                <a href="{{ route('analytics.index', ['period' => 'year']) }}" class="btn btn-sm btn-{{ $period == 'year' ? 'secondary' : 'light' }}">Year</a>
            </div>
        </div>
    </div>

    <!-- Sales Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ currency($analytics['sales']['total_revenue']) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['sales']['total_orders'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-bag fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Analytics Sections (Simplified for Verification) -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($analytics['sales']['top_selling_products'] as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $product->name }}
                                <span class="badge badge-primary badge-pill">{{ $product->total_sold }} sold</span>
                            </li>
                        @empty
                            <li class="list-group-item">No sales data available.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
