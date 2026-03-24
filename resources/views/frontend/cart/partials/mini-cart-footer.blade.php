<div class="border-top p-4 bg-white mt-auto shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <span class="text-muted small text-uppercase fw-bold ls-1">المجموع الفرعي</span>
        <span class="h4 fw-bold text-dark mb-0 ls-tight" id="mini-cart-total">{{ currency($total) }}</span>
    </div>
    <div class="d-grid gap-2">
        <a href="{{ route('checkout.index') }}" class="btn btn-primary py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-between align-items-center px-4">
            <span>إتمام الطلب</span>
            <i class="fas fa-arrow-left"></i>
        </a>
        <a href="{{ route('cart.index') }}" class="btn btn-light py-2 rounded-pill fw-bold text-muted small">
            عرض تفاصيل السلة
        </a>
    </div>
</div>
