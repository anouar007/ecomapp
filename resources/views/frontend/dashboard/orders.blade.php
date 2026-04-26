@extends('layouts.customer')

@section('dashboard_content')
<div class="d-flex flex-column mb-4" data-aos="fade-up">
    <h3 class="brand-heading h2 mb-2">{{ __('Your Order History') }}</h3>
    <div class="bg-gold rounded" style="width: 40px; height: 3px;"></div>
</div>

<div class="brand-card border-0 shadow-sm overflow-hidden bg-white" data-aos="fade-up">
    <div class="table-responsive">
        <table class="table align-middle mb-0 font-body">
            <thead class="bg-gold-light border-bottom border-gold-subtle">
                <tr>
                    <th class="ps-4 py-3 small text-muted fw-bold">{{ __('Order Number') }}</th>
                    <th class="py-3 small text-muted fw-bold">{{ __('Date') }}</th>
                    <th class="py-3 small text-muted fw-bold">{{ __('Status') }}</th>
                    <th class="py-3 small text-muted fw-bold">{{ __('Total') }}</th>
                    <th class="pe-4 py-3 text-end small text-muted fw-bold">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-bottom border-light hover-gold-subtle transition-300">
                    <td class="ps-4 fw-bold text-dark">#{{ $order->order_number }}</td>
                    <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        @php
                            $statusMap = [
                                'pending' => ['bg' => 'bg-warning-subtle text-warning', 'text' => __('Pending')],
                                'confirmed' => ['bg' => 'bg-info-subtle text-info', 'text' => __('Confirmed')],
                                'shipping' => ['bg' => 'bg-primary-subtle text-primary', 'text' => __('Shipping')],
                                'delivered' => ['bg' => 'bg-success-subtle text-success', 'text' => __('Delivered')],
                                'cancelled' => ['bg' => 'bg-danger-subtle text-danger', 'text' => __('Cancelled')],
                            ];
                            $s = $statusMap[$order->status] ?? ['bg' => 'bg-secondary-subtle text-secondary', 'text' => $order->status];
                        @endphp
                        <span class="badge {{ $s['bg'] }} rounded-pill px-3 py-1 font-body fw-normal" style="font-size: 0.7rem;">{{ $s['text'] }}</span>
                    </td>
                    <td class="fw-bold text-dark">{{ $order->formatted_total }}</td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn-brand-outline py-1 px-3 small text-decoration-none">
                            {{ __('Details') }} <i class="fas fa-search ms-1 small"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-25 mb-3"><i class="fas fa-shopping-bag fa-4x"></i></div>
                        <h6 class="text-muted mb-4 font-body">{{ __('We did not find any previous orders in your account.') }}</h6>
                        <a href="{{ route('shop.index') }}" class="btn-brand-primary px-4">{{ __('Discover the collection now') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white p-4 border-top-0">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
