@extends('layouts.customer')

@section('dashboard_content')
<div class="d-flex align-items-center justify-content-between mb-3 mb-md-4">
    <h3 class="fw-bold m-0 fs-5 fs-md-4">{{ __('My Orders') }}</h3>
    <span class="badge bg-light text-muted border px-2.5 py-1.5 rounded-pill small">
        {{ $orders->total() }} {{ __('commande(s)') }}
    </span>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    {{-- Desktop Table (d-none d-md-block) --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3">{{ __('Order Number') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th class="pe-4 text-end">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="ps-4 fw-bold font-monospace">#{{ $order->order_number }}</td>
                    <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td><span class="badge {{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="fw-bold">{{ $order->formatted_total }}</td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold small border">
                            {{ __('View details') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted opacity-25 mb-3"></i>
                        <h6 class="fw-bold text-muted">{{ __('No orders found') }}</h6>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm rounded-pill mt-2">{{ __('Start Shopping') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Touch Cards (d-block d-md-none, zero horizontal scroll) --}}
    <div class="d-block d-md-none p-2.5">
        @forelse($orders as $order)
        <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold text-dark font-monospace" style="font-size: 0.95rem;">
                    #{{ $order->order_number }}
                </span>
                <span class="badge {{ $order->status_badge_class }} px-2.5 py-1" style="font-size: 0.72rem;">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between text-muted small mb-3">
                <span><i class="far fa-calendar-alt me-1 text-muted"></i>{{ $order->created_at->format('d M Y') }}</span>
                <span class="fw-bold text-danger fs-6">{{ $order->formatted_total }}</span>
            </div>
            <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-light btn-sm w-100 rounded-pill py-2 fw-semibold text-dark d-flex align-items-center justify-content-center gap-1.5 border">
                <span>{{ __('View details') }}</span>
                <i class="fas fa-chevron-right small text-muted"></i>
            </a>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-3x text-muted opacity-25 mb-3"></i>
            <h6 class="fw-bold text-muted">{{ __('No orders found') }}</h6>
            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm rounded-pill mt-2">{{ __('Start Shopping') }}</a>
        </div>
        @endforelse
    </div>

    @if($orders->hasPages())
    <div class="card-footer bg-white p-3 border-top-0 d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
