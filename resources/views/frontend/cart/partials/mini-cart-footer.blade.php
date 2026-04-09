@php
    $cart = session('cart', []);
    $total = 0;
    foreach($cart as $details) {
        $total += $details['price'] * $details['quantity'];
    }
@endphp
@if(count($cart) > 0)

<div class="mc-footer">
    <div class="mc-shipping">
        <i class="fas fa-truck me-1"></i> التوصيل: 20 درهم الدار البيضاء و 30 درهم النواحي
و 40 درهم باقي المدن
    </div>
    <div class="mc-total-row">
        <span class="mc-total-label">المجموع الفرعي</span>
        <span class="mc-total-val" id="mini-cart-total">{{ currency($total) }}</span>
    </div>
    <a href="{{ route('checkout.index') }}" class="mc-checkout-btn">
        <i class="fas fa-check-circle"></i>
        <span>إتمام الطلب</span>
    </a>
    <a href="{{ route('cart.index') }}" class="mc-view-btn">عرض السلة كاملة</a>
</div>
@endif

