@php
    $total = 0;
    foreach($cart as $details) {
        $total += $details['price'] * $details['quantity'];
    }
@endphp

<div class="brand-card p-4 border-0 shadow-sm bg-white sticky-top mb-4" style="top: 100px; z-index: 10;">
    <h5 class="brand-heading h4 mb-4">ملخص الطلب</h5>
    
    <div class="d-flex justify-content-between mb-3 font-body">
        <span class="text-muted">المجموع الفرعي</span>
        <span class="fw-bold">{{ currency($total) }}</span>
    </div>
    
    <div class="d-flex justify-content-between mb-3 font-body">
        <span class="text-muted">التوصيل</span>
        <div class="text-end">
            <span class="text-gold fw-bold">من 20 إلى 40 د.م.</span>
            <div class="small text-muted mt-1 lh-base" style="font-size: 0.75rem;">تُحدد التكلفة حسب مدينتك</div>
        </div>
    </div>

    <div class="bg-gold-light my-4" style="height: 1px;"></div>
    
    <div class="d-flex justify-content-between mb-5 align-items-center font-body">
        <span class="h5 fw-bold mb-0">المجموع النهائي</span>
        <span class="h6 fw-bold text-muted mb-0" id="cart-final-total">يُحدد في الدفع</span>
    </div>

    @if(count($cart) > 0)
    <a href="{{ route('checkout.index') }}" class="btn-brand-primary w-100 py-3 mb-3 text-center text-decoration-none hvr-grow d-flex justify-content-center align-items-center">
        <span>تابعي لإتمام الطلب</span>
        <i class="fas fa-crown ms-2"></i>
    </a>
    @endif
    
    <div class="text-center">
        <span class="small text-muted font-body"><i class="fas fa-shield-alt me-1"></i> دفع آمن عند الاستلام</span>
    </div>
</div>
