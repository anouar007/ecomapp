@extends('layouts.customer')

@section('dashboard_content')
<div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
    <div class="min-w-0">
        <h3 class="fw-bold m-0 fs-5 fs-md-4 text-truncate">{{ __('Order') }} #{{ $order->order_number }}</h3>
        <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i>{{ $order->created_at->format('d M Y, H:i') }}</span>
    </div>
    <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 flex-shrink-0">
        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right me-1' : 'fa-arrow-left me-1' }}"></i> {{ __('Back to orders') }}
    </a>
</div>

<div class="row g-3 g-md-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 mb-md-4">
            <div class="card-header bg-white p-3 p-md-4 border-bottom">
                <h5 class="fw-bold m-0 fs-6 fs-md-5">{{ __('Ordered items') }}</h5>
            </div>

            {{-- Desktop Table (d-none d-md-block) --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table align-middle mb-0">
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-3 border d-flex align-items-center justify-content-center me-3 flex-shrink-0 overflow-hidden bg-white" style="width: 52px; height: 52px; padding: 2px;">
                                        <img src="{{ $item->product_image }}" 
                                             alt="{{ $item->product_name }}" 
                                             style="width: 100%; height: 100%; object-fit: contain;" 
                                             onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                                             loading="lazy">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $item->product_name }}</h6>
                                        @if($item->variant_name)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">
                                                <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                            </span>
                                        @endif
                                        <small class="text-muted d-block">{{ __('Qty:') }} {{ $item->quantity }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end pe-4 fw-bold">
                                {{ currency($item->subtotal) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td class="text-end pe-4 py-3 border-0">{{ __('Subtotal') }}</td>
                            <td class="text-end pe-4 py-3 border-0 fw-bold">{{ currency($order->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4 py-2 border-0">{{ __('Shipping') }}</td>
                            <td class="text-end pe-4 py-2 border-0">{{ currency($order->shipping_cost) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4 py-3 border-0 h5 fw-bold text-dark">{{ __('Total') }}</td>
                            <td class="text-end pe-4 py-3 border-0 h5 fw-bold text-primary">{{ $order->formatted_total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Mobile Touch Cards (d-block d-md-none, zero horizontal scroll) --}}
            <div class="d-block d-md-none p-2.5">
                @foreach($order->items as $item)
                <div class="card border rounded-3 p-2.5 mb-2 bg-white shadow-xs">
                    <div class="d-flex align-items-start gap-2.5">
                        <div class="rounded-3 border d-flex align-items-center justify-content-center flex-shrink-0 overflow-hidden bg-white" style="width: 48px; height: 48px; padding: 2px;">
                            <img src="{{ $item->product_image }}" 
                                 alt="{{ $item->product_name }}" 
                                 style="width: 100%; height: 100%; object-fit: contain;" 
                                 onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                                 loading="lazy">
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <h6 class="fw-bold mb-1 lh-sm" style="font-size: 0.88rem;">{{ $item->product_name }}</h6>
                            @if($item->variant_name)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1" style="font-size: 0.70rem;">
                                    <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                </span>
                            @endif
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 0.72rem;">
                                    {{ __('Qty:') }} {{ $item->quantity }}
                                </span>
                                <span class="fw-bold text-danger" style="font-size: 0.95rem;">
                                    {{ currency($item->subtotal) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Mobile Order Totals --}}
                <div class="bg-light rounded-3 p-3 mt-2 border">
                    <div class="d-flex justify-content-between align-items-center mb-1.5 small">
                        <span class="text-muted">{{ __('Subtotal') }}</span>
                        <span class="fw-semibold text-dark">{{ currency($order->subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span class="text-muted">{{ __('Shipping') }}</span>
                        <span class="fw-semibold text-dark">{{ currency($order->shipping_cost) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold text-dark">{{ __('Total') }}</span>
                        <span class="fw-bold text-primary fs-5">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
            <div class="card-body p-3 p-md-4">
                <h6 class="fw-bold text-muted small text-uppercase mb-2.5" style="letter-spacing: 0.04em;">
                    <i class="fas fa-truck text-primary me-1.5"></i>{{ __('SHIPPING ADDRESS') }}
                </h6>
                <p class="mb-0 fw-bold text-dark">{{ $order->shipping_address }}</p>
                <p class="mb-0 text-secondary">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                <p class="mb-0 text-muted small">{{ $order->shipping_country }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 p-md-4">
                <h6 class="fw-bold text-muted small text-uppercase mb-3" style="letter-spacing: 0.04em;">
                    <i class="fas fa-info-circle text-primary me-1.5"></i>{{ __('ORDER INFO') }}
                </h6>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">{{ __('Status') }}</span>
                    <span class="badge {{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">{{ __('Payment') }}</span>
                    <span class="badge {{ $order->payment_status_badge_class }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">{{ __('Date') }}</span>
                    <span class="fw-bold text-dark small">{{ $order->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
