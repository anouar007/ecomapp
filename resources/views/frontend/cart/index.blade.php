@extends('layouts.frontend')

@section('meta_title', __('Cart') . ' — ' . setting('app_name', 'Moubdi3oun'))

@section('content')
<div class="bg-light section-py min-vh-100">
    <div class="container">
        {{-- Header Section --}}
        <div class="text-center mb-5" data-aos="fade-down">
            <span class="text-muted small text-uppercase fw-black ls-2 opacity-50">{{ __('Your Journey') }}</span>
            <h1 class="fw-black text-uppercase ls-1 h2 mt-2">{{ __('Shopping Bag') }}</h1>
            <div class="mx-auto bg-dark mt-3" style="width: 40px; height: 3px;"></div>
        </div>

        @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-5">
            {{-- 🧺 ITEM LIST --}}
            <div class="col-lg-8">
                <div class="cart-items-container" data-aos="fade-right">
                    @php $total = 0; @endphp
                    @foreach(session('cart') as $key => $details)
                        @php $total += $details['price'] * $details['quantity']; @endphp
                        
                        <div class="cart-page-item bg-white rounded-4 shadow-xs border border-light p-4 mb-4 transition-all hover-shadow-md position-relative" id="cart-row-{{ $key }}">
                            <div class="row align-items-center g-4">
                                {{-- Image Section --}}
                                <div class="col-md-3 col-4">
                                    <div class="position-relative overflow-hidden rounded-3 shadow-xs aspect-9-10">
                                        @if($details['image'])
                                            <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="w-100 h-100 object-fit-cover transition-all hover-scale">
                                        @else
                                            <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                                <i class="fas fa-image fa-2x opacity-25"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Info Section --}}
                                <div class="col-md-5 col-8">
                                    <div class="d-flex flex-column h-100">
                                        @php $pId = $details['product_id'] ?? (is_numeric($key) ? $key : explode('_', $key)[0]); @endphp
                                        <h4 class="fw-black mb-2 h5">
                                            <a href="{{ route('shop.show', $pId) }}" class="text-dark text-decoration-none hover-accent transition-all">
                                                {{ $details['name'] }}
                                            </a>
                                        </h4>
                                        
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @if(($details['color'] ?? null))
                                                <span class="badge bg-light text-dark border-0 fw-medium px-3 py-2 rounded-pill small">
                                                    {{ __('Finish') }}: <span class="fw-black">{{ $details['color'] }}</span>
                                                </span>
                                            @endif
                                            @if(($details['size'] ?? null))
                                                <span class="badge bg-light text-dark border-0 fw-medium px-3 py-2 rounded-pill small">
                                                    {{ __('Dimensions') }}: <span class="fw-black">{{ $details['size'] }}</span>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-auto d-md-none">
                                            <span class="h5 fw-black mb-0 text-accent">{{ currency($details['price']) }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Controls Section (Desktop) --}}
                                <div class="col-md-4 d-none d-md-block text-end">
                                    <div class="d-flex flex-column align-items-end gap-3">
                                        <h5 class="fw-black mb-0 text-accent">{{ currency($details['price'] * $details['quantity']) }}</h5>
                                        
                                        <div class="mc-qty-selector bg-light rounded-pill border d-inline-flex align-items-center px-2 py-1">
                                            <button class="qty-btn" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="text" class="qty-input mx-2 bg-transparent border-0 text-center fw-black" value="{{ $details['quantity'] }}" readonly style="width: 30px;">
                                            <button class="qty-btn" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>

                                        <button class="btn btn-link text-danger p-0 transition-all text-decoration-none small fw-bold" onclick="removeItem('{{ $key }}')">
                                            <i class="fas fa-trash-alt me-1"></i> {{ __('Remove Item') }}
                                        </button>
                                    </div>
                                </div>

                                {{-- Mobile Controls --}}
                                <div class="col-12 d-md-none border-top pt-3 mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="mc-qty-selector bg-light rounded-pill border d-inline-flex align-items-center px-1 py-1">
                                            <button class="qty-btn" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})">
                                                <i class="fas fa-minus small"></i>
                                            </button>
                                            <input type="text" class="qty-input mx-2 bg-transparent border-0 text-center fw-black small" value="{{ $details['quantity'] }}" readonly style="width: 25px;">
                                            <button class="qty-btn" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})">
                                                <i class="fas fa-plus small"></i>
                                            </button>
                                        </div>
                                        <button class="btn btn-link text-danger p-0 opacity-75 text-decoration-none small" onclick="removeItem('{{ $key }}')">
                                            <i class="fas fa-trash-alt me-1"></i> {{ __('Remove') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-5" data-aos="fade-up">
                    <a href="{{ route('shop.index') }}" class="btn-link-premium text-muted fw-bold small text-decoration-none text-uppercase ls-1">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('Continue Shopping') }}
                    </a>
                </div>
            </div>

            {{-- 🧾 ORDER SUMMARY --}}
            <div class="col-lg-4">
                <div class="cart-summary-sticky" data-aos="fade-left">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-white text-dark p-4 border-bottom">
                            <h5 class="fw-black mb-0 text-uppercase ls-2 small">{{ __('Order Summary') }}</h5>
                        </div>
                        <div class="card-body p-4 p-xl-5">
                            <div class="summary-details">
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="text-muted fw-medium">{{ __('Sub-total') }}</span>
                                    <span class="fw-black h5 mb-0">{{ currency($total) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="text-muted fw-medium">{{ __('Delivery') }}</span>
                                    <span class="text-success fw-black small text-uppercase ls-1">{{ __('Free') }}</span>
                                </div>
                                
                                <div class="promo-box bg-light rounded-4 p-3 mb-5 border border-dashed text-center">
                                    <span class="small text-muted mb-0 d-block">{{ __('Complimentary express shipping applied') }}</span>
                                </div>

                                <hr class="my-4 opacity-10">
                                
                                <div class="d-flex justify-content-between mb-5 align-items-center">
                                    <div>
                                        <span class="fw-black text-uppercase ls-1 small d-block opacity-50">{{ __('Total') }}</span>
                                        <span class="h3 fw-black mb-0" style="letter-spacing: -1px;">{{ currency($total) }}</span>
                                    </div>
                                    <div class="bg-accent-soft rounded-3 p-2">
                                        <i class="fas fa-shield-alt text-accent fa-lg"></i>
                                    </div>
                                </div>

                                <button class="btn btn-dark w-100 py-4 rounded-pill fw-black shadow-lg d-flex justify-content-between align-items-center px-4 text-uppercase ls-1 transition-all hover-scale" onclick="location.href='{{ route('checkout.index') }}'">
                                    <span>{{ __('Secure Checkout') }}</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                                
                                {{-- Payments removal --}}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-white rounded-4 shadow-sm border border-light text-center" data-aos="fade-up" data-aos-delay="200">
                        <i class="fas fa-truck text-accent mb-2"></i>
                        <p class="small text-muted mb-0 fw-medium">
                            {{ __('Cash on delivery available throughout Morocco') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5 mt-4" data-aos="zoom-in">
            <div class="mb-5 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 150px; height: 150px;">
                <i class="fas fa-shopping-bag fa-4x text-muted opacity-25"></i>
            </div>
            <h2 class="fw-black mb-3 text-uppercase ls-1 h3">{{ __('Your bag is empty') }}</h2>
            <p class="text-muted mb-5 mw-500 mx-auto opacity-75">{{ __('It seems you haven’t added any masterpieces to your collection yet.') }}</p>
            <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-black shadow-lg text-uppercase ls-1">{{ __('Discover Collections') }}</a>
        </div>
        @endif
    </div>
</div>

<style>
/* ── Cart Specific Enhancements ── */
.section-py { padding: 80px 0; }
.text-accent { color: var(--accent) !important; }
.bg-accent-soft { background: rgba(var(--accent-rgb), 0.1); }
.ls-2 { letter-spacing: 2px; }
.hover-scale:hover { transform: scale(1.05); }
.hover-accent:hover { color: var(--accent) !important; }
.shadow-xs { box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

.cart-page-item {
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.cart-page-item:hover {
    transform: translateX(10px);
    border-color: rgba(0,0,0,0.1) !important;
}

.cart-summary-sticky {
    position: sticky;
    top: 120px;
}

.grayscale { filter: grayscale(100%); }

.btn-link-premium {
    position: relative;
    padding-bottom: 2px;
    transition: all 0.3s;
}
.btn-link-premium::after {
    content: '';
    position: absolute;
    width: 0;
    height: 1.5px;
    bottom: 0;
    left: 0;
    background-color: var(--accent);
    transition: all 0.3s;
}
.btn-link-premium:hover::after {
    width: 100%;
}

.mw-500 { max-width: 500px; }

@media (max-width: 768px) {
    .cart-page-item:hover { transform: none; }
}
</style>
@endsection

@push('scripts')
<script>
function updateQty(id, qty) {
    if (qty < 1) {
        removeItem(id);
        return;
    }
    
    // Luxury Loading State (Optional)
    const row = document.getElementById(`cart-row-${id}`);
    if(row) row.style.opacity = '0.5';

    fetch('/cart/update', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: id, quantity: qty })
    })
    .then(async r => {
        const data = await r.json();
        if (!r.ok) throw new Error(data.message || 'RefreshRequired');
        return data;
    })
    .then(data => {
        if (data.success) {
            // Hard refresh for main cart to ensure all totals & discounts are perfectly recalculated
            location.reload();
        }
    })
    .catch(error => {
        if(row) row.style.opacity = '1';
        console.error('Error:', error);
        
        if (error.message === 'RefreshRequired') {
            window.location.reload();
            return;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: '{{ __("Stock Limit") }}',
            text: error.message,
            showConfirmButton: false,
            timer: 3500,
            background: '#1a1a1a',
            color: '#fff',
            iconColor: '#f59e0b'
        });
    });
}

function removeItem(id) {
    Swal.fire({
        title: '{{ __("Remove Item?") }}',
        text: '{{ __("Are you sure you want to remove this masterpiece?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '{{ __("Yes, remove") }}',
        cancelButtonText: '{{ __("Cancel") }}',
        background: '#fff',
        color: '#000',
        iconColor: '#000'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/cart/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            })
            .catch(console.error);
        }
    });
}
</script>
@endpush
