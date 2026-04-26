@extends('layouts.frontend')

@section('meta_title', __('Checkout') . ' — Ait Oumdis')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
/* ── Checkout Layout ── */
.co-wrap { background:#f9fafb; min-height:80vh; }

/* Progress bar */
.co-steps { display:flex; align-items:center; justify-content:center; gap:0; margin-bottom:0; }
.co-step { display:flex; align-items:center; gap:8px; }
.co-step-num {
    width:32px; height:32px; border-radius:50%; display:flex; align-items:center;
    justify-content:center; font-size:.75rem; font-weight:800;
    background:#3BB878; color:#fff; flex-shrink:0;
}
.co-step-label { font-size:.8rem; font-weight:700; color:#374151; }
.co-step-line { width:48px; height:2px; background:#d1fae5; margin:0 8px; }

/* Cards */
.co-card {
    background:#fff; border-radius:20px;
    padding:32px; box-shadow:0 4px 24px rgba(0,0,0,.06);
    border:1px solid #f1f5f9;
}
.co-card-title {
    font-size:.78rem; font-weight:800; text-transform:uppercase;
    letter-spacing:1.5px; color:#3BB878; margin-bottom:20px;
    display:flex; align-items:center; gap:8px;
}
.co-card-title i { font-size:.85rem; }

/* Form inputs */
.co-label {
    font-size:.72rem; font-weight:800; text-transform:uppercase;
    letter-spacing:1px; color:#6B7280; margin-bottom:6px; display:block;
}
.co-input {
    width:100%; border:2px solid #f1f5f9; border-radius:12px;
    padding:12px 16px; font-size:.93rem; font-weight:500; color:#111827;
    background:#fff; outline:none; transition:border-color .2s, box-shadow .2s;
    font-family:inherit;
}
.co-input:focus { border-color:#3BB878; box-shadow:0 0 0 4px rgba(59,184,120,.1); }
.co-input::placeholder { color:#9CA3AF; font-weight:400; }
textarea.co-input { resize:none; }

/* Tom-select overrides */
.ts-control {
    border:2px solid #f1f5f9 !important; border-radius:12px !important;
    padding:10px 14px !important; background:#fff !important;
    box-shadow:none !important; min-height:50px !important;
    font-size:.93rem !important; font-weight:500 !important;
}
.ts-control.focus { border-color:#3BB878 !important; box-shadow:0 0 0 4px rgba(59,184,120,.1) !important; }
.ts-dropdown { border-radius:14px !important; border:none !important; box-shadow:0 12px 40px rgba(0,0,0,.12) !important; margin-top:6px !important; }
.ts-dropdown .option { padding:11px 16px !important; font-size:.9rem !important; }
.ts-dropdown .active { background:#3BB878 !important; color:#fff !important; }

/* Payment badge */
.co-payment {
    background:linear-gradient(135deg,#f0fdf4,#dcfce7);
    border:2px solid #bbf7d0; border-radius:16px;
    padding:20px 24px; display:flex; align-items:center; gap:16px;
}
.co-pay-icon {
    width:48px; height:48px; border-radius:14px; background:#3BB878;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:1.1rem; flex-shrink:0;
}

/* Submit button */
.btn-co-submit {
    width:100%; background:#3BB878; color:#fff; border:none;
    border-radius:14px; font-weight:800; font-size:1rem;
    padding:16px 24px; cursor:pointer; display:flex;
    align-items:center; justify-content:center; gap:10px;
    transition:all .3s cubic-bezier(.34,1.56,.64,1);
    box-shadow:0 8px 28px rgba(59,184,120,.35);
    letter-spacing:.3px;
}
.btn-co-submit:hover { background:#2f9461; transform:translateY(-2px); box-shadow:0 14px 36px rgba(59,184,120,.4); }
.btn-co-submit:active { transform:translateY(0); }

/* Order summary card */
.co-summary { background:#fff; border-radius:20px; padding:28px; box-shadow:0 4px 24px rgba(0,0,0,.06); border:1px solid #f1f5f9; }
.co-item { display:flex; gap:14px; padding:14px 0; border-bottom:1px solid #f9fafb; }
.co-item:last-child { border-bottom:none; }
.co-item-img { width:64px; height:80px; border-radius:12px; overflow:hidden; flex-shrink:0; background:#f9fafb; }
.co-item-img img { width:100%; height:100%; object-fit:cover; }
.co-item-name { font-size:.88rem; font-weight:700; color:#111827; line-height:1.3; margin-bottom:6px; }
.co-item-size { display:inline-block; background:#f3f4f6; border-radius:100px; padding:2px 10px; font-size:.72rem; font-weight:700; color:#6B7280; }
.co-item-qty { display:inline-block; background:#e8f7ef; color:#3BB878; border-radius:100px; padding:2px 10px; font-size:.72rem; font-weight:700; }
.co-item-price { font-size:.93rem; font-weight:800; color:#3BB878; margin-top:4px; }

/* Totals */
.co-totals { background:#f9fafb; border-radius:14px; padding:18px; margin-top:16px; }
.co-totals-row { display:flex; justify-content:space-between; align-items:center; }
.co-totals-divider { height:1px; background:#e5e7eb; margin:12px 0; }
.co-total-label { font-size:.83rem; color:#6B7280; font-weight:600; }
.co-total-value { font-size:.88rem; font-weight:700; color:#374151; }
.co-grand-label { font-size:.95rem; font-weight:800; color:#111827; }
.co-grand-value { font-size:1.4rem; font-weight:900; color:#3BB878; }

/* Security badge */
.co-secure { display:flex; align-items:center; gap:8px; justify-content:center; margin-top:16px; }
.co-secure span { font-size:.75rem; color:#9CA3AF; font-weight:600; }

/* Breadcrumb */
.co-hero { background:linear-gradient(135deg,#0d1f14,#1a5c38 60%,#3BB878); padding:40px 0; position:relative; overflow:hidden; }
.co-hero::before { content:''; position:absolute; inset:0; background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.06) 1px,transparent 0); background-size:28px 28px; }

@media(max-width:991px){
    .co-card { padding:22px; border-radius:16px; }
    .co-summary { padding:20px; }
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="co-hero">
    <div class="container position-relative" style="z-index:1">
        <div class="text-center mb-4">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white opacity-60 text-decoration-none small">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-white opacity-60 text-decoration-none small">{{ __('Cart') }}</a></li>
                    <li class="breadcrumb-item active text-white small">{{ __('Checkout') }}</li>
                </ol>
            </nav>
            <h1 class="text-white fw-900 mb-2" style="font-size:clamp(1.6rem,3vw,2.2rem)">{{ __('Complete Your Order') }}</h1>
            <p class="text-white mb-0" style="opacity:.7;font-size:.9rem">{{ __('Just a few details away from getting your natural products') }}</p>
        </div>

        {{-- Steps --}}
        <div class="co-steps">
            <div class="co-step">
                <div class="co-step-num">1</div>
                <span class="co-step-label text-white">{{ __('Delivery') }}</span>
            </div>
            <div class="co-step-line"></div>
            <div class="co-step">
                <div class="co-step-num" style="background:rgba(255,255,255,.25)">2</div>
                <span class="co-step-label text-white opacity-60">{{ __('Confirm') }}</span>
            </div>
            <div class="co-step-line"></div>
            <div class="co-step">
                <div class="co-step-num" style="background:rgba(255,255,255,.25)">3</div>
                <span class="co-step-label text-white opacity-60">{{ __('Done') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Main --}}
<div class="co-wrap py-5">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-start">

            {{-- ── LEFT: Form ── --}}
            <div class="col-lg-7">

                {{-- Delivery card --}}
                <div class="co-card mb-4">
                    <div class="co-card-title">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ __('Delivery Information') }}
                    </div>

                    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                        @csrf
                        <div class="d-flex flex-column gap-4">

                            {{-- Name --}}
                            <div>
                                <label class="co-label">{{ __('Full Name') }}</label>
                                <input type="text" name="customer_name" class="co-input"
                                       placeholder="{{ __('Example: Ahmed Alami') }}"
                                       value="{{ old('customer_name') }}" required>
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="co-label">{{ __('Phone Number') }}</label>
                                <div class="position-relative">
                                    <span class="position-absolute" style="left:14px;top:50%;transform:translateY(-50%);color:#3BB878;font-size:.85rem">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" name="customer_phone" class="co-input ps-5"
                                           placeholder="06 XX XX XX XX"
                                           value="{{ old('customer_phone') }}" required>
                                </div>
                            </div>

                            {{-- Hidden input carries arabic_name to backend --}}
                                <input type="hidden" name="shipping_city" id="shipping_city_value">
                                <label class="co-label">{{ __('City') }}</label>
                                <select id="city-select">
                                    <option value="">{{ __('Search for your city...') }}</option>
                                </select>

                            {{-- Address --}}
                            <div>
                                <label class="co-label">{{ __('Detailed Address') }}</label>
                                <textarea name="shipping_address" class="co-input" rows="3"
                                          placeholder="{{ __('Neighborhood, street, house number...') }}"
                                          required>{{ old('shipping_address') }}</textarea>
                            </div>
                        </div>

                        {{-- Payment method --}}
                        <div class="co-payment mt-4">
                            <div class="co-pay-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="fw-800 text-dark mb-1" style="font-size:.93rem">{{ __('Cash on Delivery') }}</div>
                                <div class="small text-muted">{{ __('Our team will contact you to confirm the order before shipping') }}</div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn-co-submit mt-4">
                            <i class="fas fa-check-circle"></i>
                            {{ __('Confirm My Order') }}
                            <i class="fas fa-arrow-right ms-1" style="font-size:.8rem"></i>
                        </button>

                    </form>
                </div>

                {{-- Reassurance icons --}}
                <div class="row g-3">
                    @foreach([
                        ['fas fa-shield-alt', __('Secure Order'), __('Your data is fully protected')],
                        ['fas fa-truck',      __('Fast Shipping'), __('Delivered within 2–5 business days')],
                    ] as $r)
                    <div class="col-6">
                        <div class="text-center p-3 bg-white rounded-3 border" style="border-color:#f1f5f9!important">
                            <i class="{{ $r[0] }} text-green mb-2" style="font-size:1.1rem"></i>
                            <div class="fw-800 text-dark mb-0" style="font-size:.75rem">{{ $r[1] }}</div>
                            <div class="text-muted" style="font-size:.68rem">{{ $r[2] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── RIGHT: Order Summary ── --}}
            <div class="col-lg-5">
                <div class="sticky-top" style="top:24px">
                    <div class="co-summary">
                        <div class="co-card-title">
                            <i class="fas fa-shopping-bag"></i>
                            {{ __('Order Summary') }}
                        </div>

                        {{-- Items list --}}
                        <div>
                            @foreach($cart as $key => $details)
                            <div class="co-item">
                                <div class="co-item-img">
                                    <img src="{{ $details['image'] ? Storage::url($details['image']) : asset('images/placeholder-product.jpg') }}"
                                         alt="{{ $details['name'] }}">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="co-item-name">{{ $details['name'] }}</div>
                                    <div class="d-flex gap-1 flex-wrap mt-1">
                                        @if($details['size'] ?? null)
                                            <span class="co-item-size">{{ $details['size'] }}</span>
                                        @endif
                                        <span class="co-item-qty">×{{ $details['quantity'] }}</span>
                                    </div>
                                    <div class="co-item-price">{{ currency($details['price'] * $details['quantity']) }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Totals --}}
                        <div class="co-totals">
                            <div class="co-totals-row">
                                <span class="co-total-label">{{ __('Subtotal') }}</span>
                                <span class="co-total-value">{{ currency($total) }}</span>
                            </div>
                            <div class="co-totals-row mt-2">
                                <span class="co-total-label">{{ __('Delivery') }}</span>
                                <span class="co-total-value text-green fw-800" id="shippingDisplay">{{ __('Select city...') }}</span>
                            </div>
                            <div class="co-totals-divider"></div>
                            <div class="co-totals-row">
                                <span class="co-grand-label">{{ __('Total') }}</span>
                                <span class="co-grand-value" id="totalDisplay">{{ currency($total) }}</span>
                            </div>
                        </div>

                        {{-- Secure --}}
                        <div class="co-secure">
                            <i class="fas fa-lock text-green" style="font-size:.75rem"></i>
                            <span>{{ __('Secure & Encrypted Checkout') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cities = @json($cities->values());
        const subtotal = {{ $total }};

        // Build lookup map by id for fast access
        const cityMap = {};
        const options = cities.map(c => {
            cityMap[c.id] = c;
            const primaryName = '{{ app()->getLocale() }}' === 'ar' ? c.arabic_name : c.name;
            const secondaryName = '{{ app()->getLocale() }}' === 'ar' ? c.name : c.arabic_name;
            return {
                value: String(c.id),
                text: primaryName + ' (' + secondaryName + ')',
                price: parseFloat(c.price)
            };
        });

        const ts = new TomSelect('#city-select', {
            options: options,
            valueField: 'value',
            labelField: 'text',
            searchField: ['text'],
            placeholder: '{{ __("Search for your city...") }}',
            onChange: function(value) {
                const city = cityMap[value];
                if (city) {
                    // Update hidden input with Arabic name for backend
                    document.getElementById('shipping_city_value').value = city.arabic_name;
                    document.getElementById('shippingDisplay').innerText = parseFloat(city.price).toFixed(2) + ' {{ __('DH') }}';
                    document.getElementById('totalDisplay').innerText = (subtotal + parseFloat(city.price)).toFixed(2) + ' {{ __('DH') }}';
                }
            }
        });
    });
</script>
@endpush
