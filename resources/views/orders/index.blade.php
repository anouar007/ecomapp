@extends('layouts.app')

@section('title', __('Orders Management'))

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                {{ __('Orders Management') }}
            </h1>
            <p class="brand-subtitle">{{ __('Track and manage customer orders, fulfillment status, and logistics') }}</p>
        </div>
{{-- 
        <a href="{{ route('pos.index') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> {{ __('Create New Order') }}
        </a>
--}}
    </div>

    <!-- Filter Bar -->
    <div class="brand-filter-bar px-3 py-3">
        <form method="GET" action="{{ route('orders.index') }}" class="row g-3 align-items-end">
            <div class="col-12 col-lg-4">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ __('Search') }}</label>
                <div class="brand-search-wrapper w-100">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" 
                           placeholder="{{ __('Order #, name...') }}">
                </div>
            </div>
            
            <div class="col-6 col-lg-3">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ __('Status') }}</label>
                <select name="status" class="form-select custom-select-premium">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ __(ucfirst($st)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-lg-3">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ __('Payment') }}</label>
                <select name="payment_status" class="form-select custom-select-premium">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach(['pending', 'paid', 'failed', 'refunded'] as $pst)
                        <option value="{{ $pst }}" {{ request('payment_status') == $pst ? 'selected' : '' }}>{{ __(ucfirst($pst)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-lg-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-brand-primary flex-grow-1">
                        <i class="fas fa-filter me-2"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-brand-light px-3" title="{{ __('Reset') }}">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Mobile Orders List -->
    <div class="d-lg-none mt-3 px-1">
        @forelse($orders as $order)
        <div class="glass-card mb-3 p-3 border-0 shadow-soft">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary text-decoration-none d-block mb-1" style="font-size: 1.1rem;">
                        #{{ $order->order_number }}
                    </a>
                    <div class="text-muted small">
                        <i class="far fa-calendar-alt me-1 opacity-50"></i> {{ $order->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div class="d-flex flex-column gap-2 align-items-end">
                    <select class="brand-badge-select status-update-ajax {{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info') }}" 
                            data-order-id="{{ $order->id }}"
                            data-update-url="{{ route('orders.update', $order) }}"
                            style="font-size: 0.65rem; width: 100px; padding: 0.25rem 0.5rem;">
                        @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                            <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ __(ucfirst($st)) }}</option>
                        @endforeach
                    </select>
                    <select class="brand-badge-select payment-update-ajax {{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning') }}"
                            data-order-id="{{ $order->id }}"
                            data-update-url="{{ route('orders.update', $order) }}"
                            style="font-size: 0.65rem; width: 100px; padding: 0.25rem 0.5rem;">
                        @foreach(['pending', 'paid', 'failed', 'refunded'] as $pst)
                            <option value="{{ $pst }}" {{ $order->payment_status === $pst ? 'selected' : '' }}>{{ __(ucfirst($pst)) }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="d-flex align-items-center justify-content-between py-3 border-top border-bottom" style="border-style: dashed !important; border-color: #f1f5f9 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar" style="width: 40px; height: 40px; font-size: 0.8rem;">
                        {{ substr($order->customer_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark">{{ $order->customer_name }}</div>
                        <div class="text-muted small">{{ $order->customer_email }}</div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-muted small mb-1">{{ __('Total Amount') }}</div>
                    <div class="fw-bold text-dark fs-5 font-inter">{{ $order->formatted_total }}</div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    <i class="fas fa-shopping-bag me-1 opacity-50"></i> 
                    <span class="fw-bold text-dark">{{ $order->items->count() }}</span> {{ __('Items') }}
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
                        <i class="fas fa-eye me-1"></i> {{ __('View') }}
                    </a>
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-brand-primary rounded-pill px-3">
                        <i class="fas fa-edit me-1"></i> {{ __('Edit') }}
                    </a>
                </div>
            </div>
        </div>
        @empty
            <div class="glass-card p-5 text-center">
                <i class="fas fa-shopping-basket opacity-25 mb-3" style="font-size: 48px;"></i>
                <h5 class="fw-bold">{{ __('No orders found') }}</h5>
            </div>
        @endforelse
    </div>

    <!-- Orders Table (Desktop) -->
    <div class="brand-table-card d-none d-lg-block mt-4">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">{{ __('Order #') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th class="text-center">{{ __('Items') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                        <th class="text-center">{{ __('Fulfillment') }}</th>
                        <th class="text-center">{{ __('Payment') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th class="text-end" style="padding-right: 1.5rem;">{{ __('Actions') }}</th>
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
                            <select class="brand-badge-select status-update-ajax {{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info') }}" 
                                    data-order-id="{{ $order->id }}"
                                    data-update-url="{{ route('orders.update', $order) }}">
                                @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                                    <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ __(ucfirst($st)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <select class="brand-badge-select payment-update-ajax {{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning') }}"
                                    data-order-id="{{ $order->id }}"
                                    data-update-url="{{ route('orders.update', $order) }}">
                                @foreach(['pending', 'paid', 'failed', 'refunded'] as $pst)
                                    <option value="{{ $pst }}" {{ $order->payment_status === $pst ? 'selected' : '' }}>{{ __(ucfirst($pst)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $order->created_at->format('M d, Y') }}</div>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="btn-action-icon" title="{{ __('View Order') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="btn-action-icon" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
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
                                <h5 class="fw-bold text-dark">{{ __('No orders found') }}</h5>
                                <p class="text-muted">{{ __('No order records matching your current selection.') }}</p>
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
                    body: JSON.stringify({ [field]: value })
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
@endsection

