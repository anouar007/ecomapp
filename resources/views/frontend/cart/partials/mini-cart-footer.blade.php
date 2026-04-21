<div class="border-top p-4 bg-white mt-auto shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
        <span class="text-muted small text-uppercase fw-black ls-1 opacity-75">{{ __('Total') }}</span>
        <span class="h3 fw-black text-dark mb-0" id="mini-cart-total" style="letter-spacing: -1px;">{{ currency($total) }}</span>
    </div>
    <div class="d-grid gap-3">
        <a href="{{ route('checkout.index') }}" class="btn btn-dark py-3 rounded-pill fw-black shadow-sm d-flex justify-content-between align-items-center px-4 text-uppercase ls-1" style="font-size: 0.9rem;">
            <span>{{ __('Checkout') }}</span>
            <i class="fas fa-arrow-right"></i>
        </a>
        <div class="text-center">
            <a href="{{ route('cart.index') }}" class="btn-link-premium text-muted fw-bold small text-decoration-none text-uppercase ls-1">
                {{ __('View Full Cart') }}
            </a>
        </div>
    </div>
</div>

<style>
.btn-link-premium {
    position: relative;
    padding-bottom: 2px;
    transition: all 0.3s;
    font-size: 0.75rem;
}
.btn-link-premium::after {
    content: '';
    position: absolute;
    width: 0;
    height: 1.5px;
    bottom: 0;
    left: 50%;
    background-color: var(--accent);
    transition: all 0.3s;
    transform: translateX(-50%);
}
.btn-link-premium:hover {
    color: var(--accent) !important;
}
.btn-link-premium:hover::after {
    width: 60%;
}
</style>
