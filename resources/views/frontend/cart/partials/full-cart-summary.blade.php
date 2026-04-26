@php
    $total = 0;
    foreach($cart as $details) {
        $total += $details['price'] * $details['quantity'];
    }
@endphp

<div class="bg-light p-4 rounded-4 shadow-sm border border-light sticky-top" style="top: 100px;">
    <h5 class="fw-bold text-dark mb-4">{{ __('Order Summary') }}</h5>
    
    <div class="d-flex justify-content-between mb-3">
        <span class="text-muted">{{ __('Subtotal') }}</span>
        <span class="fw-bold">{{ currency($total) }}</span>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <span class="text-muted">{{ __('Delivery') }}</span>
        <div class="text-end">
            <span class="text-gold fw-bold small d-block">{{ __('Determined at checkout') }}</span>
            <span class="x-small text-muted">{{ __('Starting from') }} 20 {{ __('DH') }}</span>
        </div>
    </div>

    <hr class="my-4 opacity-10">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <span class="h5 fw-bold mb-0 text-dark">{{ __('Total') }}</span>
        <span class="h4 fw-bold text-gold mb-0">{{ currency($total) }}</span>
    </div>

    <a href="{{ route('checkout.index') }}" class="btn-brand btn-brand-primary w-100 justify-content-center py-3 rounded-pill shadow-sm mb-3">
        {{ __('Proceed to Checkout') }} <i class="fas fa-arrow-right ms-2"></i>
    </a>
    
    <div class="text-center">
        <p class="x-small text-muted mb-0"><i class="fas fa-money-bill-wave text-gold me-1"></i> {{ __('Cash on Delivery Available') }}</p>
    </div>
</div>
