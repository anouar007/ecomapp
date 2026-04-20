@extends('layouts.frontend')

@section('meta_title', __('Order Confirmation') . ' — ' . setting('app_name', 'Moubdi3oun'))

@section('content')
<div class="bg-light section-py min-vh-100">
    <div class="container">
        <h1 class="fw-black mb-5 h2 border-start-primary ps-3 text-uppercase ls-1">{{ __('Checkout Title') }}</h1>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-black mb-4 h5 text-uppercase ls-1">{{ __('Shipping Info') }}</h4>
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">{{ __('Full Name') }}</label>
                                    <input type="text" name="customer_name" class="form-control bg-white border py-3 rounded-3" placeholder="{{ __('Name and Surname') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">{{ __('Email Label') }} ({{ __('Optional') }})</label>
                                    <input type="email" name="customer_email" class="form-control bg-white border py-3 rounded-3" placeholder="{{ __('Optional Track') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">{{ __('Phone Label') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border rounded-start-3" dir="ltr">+212</span>
                                        <input type="tel" name="customer_phone" class="form-control bg-white border py-3 rounded-end-3" 
                                               placeholder="6 XX XX XX XX" 
                                               pattern="[0-9]{9}" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">{{ __('Address Label') }}</label>
                                    <input type="text" name="shipping_address" class="form-control bg-white border py-3 rounded-3" placeholder="{{ __('Address Placeholder') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted">{{ __('City Label') }}</label>
                                    <select name="shipping_city" class="form-select bg-white border py-3 rounded-3" required>
                                        <option value="">{{ __('Select Your City') }}</option>
                                        @foreach(['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Fès', 'Agadir', 'Meknès', 'Oujda', 'Kénitra', 'Tétouan', 'Témara', 'Safi', 'Mohammédia', 'Béni Mellal', 'El Jadida', 'Nador', 'Settat', 'Taza', 'Khémisset', 'Larache', 'Laâyoune', 'Dakhla'] as $city)
                                            <option value="{{ $city }}">{{ $city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
                    <div class="card-body p-4 p-md-5 text-center">
                        <i class="fas fa-wallet fa-3x text-dark mb-3 opacity-25"></i>
                        <h4 class="fw-black mb-3 h5 text-uppercase ls-1">{{ __('Payment Method') }}</h4>
                        <div class="p-3 border rounded-4 bg-light d-inline-block px-5">
                            <span class="fw-bold h6 m-0"><i class="fas fa-money-bill-wave me-2"></i> {{ __('Cash on Delivery') }}</span>
                        </div>
                        <p class="text-muted small mt-3">{{ __('COD Description') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 100px;">
                    <div class="card-header bg-white p-4 border-bottom-0 pb-0">
                        <h5 class="fw-black m-0 text-uppercase ls-1" style="font-size: 1rem;">{{ __('Order Summary') }}</h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @foreach($cart as $key => $details)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 position-relative">
                                @if($details['image'])
                                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 border" style="width: 60px; height: 80px; object-fit: cover;">
                                @else
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 80px;">
                                    <i class="fas fa-image text-muted opacity-25 fa-2x"></i>
                                </div>
                                @endif
                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-dark border-0 shadow-sm">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-truncate" style="max-width: 160px; font-size: 0.9rem;">{{ $details['name'] }}</h6>
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    @if(($details['color'] ?? null)) {{ $details['color'] }} @endif
                                    @if(($details['color'] ?? null) && ($details['size'] ?? null)) | @endif
                                    @if(($details['size'] ?? null)) {{ $details['size'] }} @endif
                                </div>
                            </div>
                            <div class="fw-bold small text-dark">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach
                        
                        <div class="bg-light p-3 rounded-3 mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">{{ __('Sub-total') }}</span>
                                <span class="fw-bold small">{{ currency($total) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted small">{{ __('Delivery') }}</span>
                                <span class="text-success fw-bold small">{{ __('Free') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <span class="fw-black mb-0 text-uppercase ls-1">{{ __('TOTAL') }}</span>
                                <span class="h4 fw-black mb-0" style="color: var(--accent);">{{ currency($total) }}</span>
                            </div>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-dark btn-lg w-100 rounded-pill py-3 fw-black shadow mt-4 text-uppercase ls-1" style="font-size: 0.95rem;">
                            {{ __('Confirm Order') }} <i class="fas fa-check-circle ms-2"></i>
                        </button>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none small fw-bold">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('Modify Cart') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof trackAdEvent === 'function') {
            trackAdEvent('InitiateCheckout', {
                content_ids: [ @foreach($cart as $key => $details) '{{ explode('_', $key)[0] }}', @endforeach ],
                content_type: 'product',
                value: {{ $total }},
                currency: 'MAD',
                num_items: {{ count($cart) }}
            });
        }
    });
</script>
@endpush
