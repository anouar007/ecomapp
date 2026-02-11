@extends('layouts.customer')

@section('dashboard_content')
<h3 class="fw-bold mb-4">My Dashboard</h3>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white">
            <div class="card-body p-4">
                <h6 class="opacity-75 mb-3">TOTAL ORDERS</h6>
                <h2 class="fw-bold display-4 mb-0">{{ $user->orders()->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="text-muted fw-bold mb-3">ACCOUNT DETAILS</h6>
                <p class="mb-1 fw-bold">{{ $user->name }}</p>
                <p class="text-muted mb-0">{{ $user->email }}</p>
                <a href="{{ route('customer.profile') }}" class="btn btn-link px-0 text-decoration-none small fw-bold">Edit Profile &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white p-4 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0">Recent Orders</h5>
            <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">View All</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                    <td class="text-muted">{{ $order->created_at->format('M d, Y') }}</td>
                    <td><span class="badge {{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="fw-bold">{{ $order->formatted_total }}</td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-light btn-sm rounded-circle">
                            <i class="fas fa-eye text-muted"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-50 mb-2">No orders found.</div>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm rounded-pill">Start Shopping</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
