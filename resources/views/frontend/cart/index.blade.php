@extends('layouts.frontend')

@section('meta_title', __('Shopping Cart') . ' — Ait Oumdis')

@section('content')

<section class="py-5 bg-brand-cream border-bottom">
    <div class="container py-2 text-center">
        <h1 class="fw-bold text-dark mb-2">{{ __('Your Shopping Cart') }}</h1>
        <p class="text-muted small mb-0">{{ __('Review your selected items before checkout') }}</p>
    </div>
</section>

<section class="py-5 bg-white min-vh-50">
    <div class="container">
        <div class="row g-4" id="cart-main-row">
            @if(session('cart') && count(session('cart')) > 0)
                <div class="col-lg-8" id="cart-items-container">
                    @include('frontend.cart.partials.full-cart-items', ['cart' => session('cart')])
                </div>
                <div class="col-lg-4" id="cart-summary-container">
                    @include('frontend.cart.partials.full-cart-summary', ['cart' => session('cart')])
                </div>
            @else
                <div class="col-12 text-center py-5" data-aos="fade-up">
                    <div class="fs-1 mb-3">🛍️</div>
                    <h2 class="fw-bold text-dark mb-3">{{ __('Your cart is empty') }}</h2>
                    <p class="text-muted mb-4">{{ __('Browse our organic treasures and find what suits you.') }}</p>
                    <a href="{{ route('shop.index') }}" class="btn-brand btn-brand-primary px-5 py-3 rounded-pill">{{ __('Discover the collection now') }}</a>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    function updateQty(id, qty) {
        if (qty < 1) return;
        fetch('{{ route('cart.update') }}', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ id, quantity: qty })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                refreshCart();
            }
        });
    }

    function removeItem(id) {
        Swal.fire({
            title: '{{ __("Delete this item?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D4A574',
            confirmButtonText: '{{ __("Yes, delete") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route('cart.remove') }}', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ id })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        refreshCart();
                    }
                });
            }
        });
    }

    function refreshCart() {
        fetch('{{ route('cart.full.items') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text()).then(html => {
                const container = document.getElementById('cart-items-container');
                if (container) container.innerHTML = html;
                else location.reload();
            });
        fetch('{{ route('cart.full.summary') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text()).then(html => {
                const summary = document.getElementById('cart-summary-container');
                if (summary) summary.innerHTML = html;
            });
        if (typeof refreshMiniCart === 'function') refreshMiniCart();
    }
</script>
@endpush
