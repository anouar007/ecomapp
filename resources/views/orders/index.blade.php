@extends('layouts.app')

@section('title', 'Orders Management')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                Orders Management
            </h1>
            <p class="brand-subtitle">Track and manage customer orders, fulfillment status, and logistics</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Create New Order
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="brand-filter-bar">
        <form method="GET" action="{{ route('orders.index') }}" class="d-flex align-items-end gap-3 flex-wrap">
            <div class="brand-search-wrapper flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Order #, name, or email...">
            </div>
            
            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Payment</label>
                <select name="payment_status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'paid', 'failed', 'refunded'] as $pst)
                        <option value="{{ $pst }}" {{ request('payment_status') == $pst ? 'selected' : '' }}>{{ ucfirst($pst) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('orders.index') }}" class="btn-brand-light" title="Reset">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Order #</th>
                        <th>Customer</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Fulfillment</th>
                        <th class="text-center">Payment</th>
                        <th>Date</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary text-decoration-none">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $order->customer_name }}</div>
                            <div class="text-muted small">{{ $order->customer_email }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-secondary px-3 py-1" style="border-radius: 6px;">
                                {{ $order->items->count() }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6">
                            {{ $order->formatted_total }}
                        </td>
                        <td class="text-center">
                            <span class="brand-badge {{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="brand-badge {{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $order->created_at->format('M d, Y') }}</div>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="btn-action-icon" title="View Order">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="btn-action-icon" title="Edit Order">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(in_array($order->status, ['pending', 'cancelled']))
                                    <form method="POST" 
                                          action="{{ route('orders.destroy', $order->id) }}" 
                                          style="display: inline;"
                                          data-confirm-delete="true"
                                          data-item-type="order"
                                          data-item-name="Order #{{ $order->order_number }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-shopping-cart text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No orders found</h5>
                                <p class="text-muted">No order records matching your current selection.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
@endsection
