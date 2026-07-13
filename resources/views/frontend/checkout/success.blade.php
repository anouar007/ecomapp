@extends('layouts.frontend')

@section('meta_title', 'تم تأكيد طلبكِ بنجاح — ' . setting('app_name', 'Hijab Princesses'))

@push('styles')
<style>
.success-page {
    background-color: #ffffff;
    min-height: 100vh;
    padding: 3rem 0;
    font-family: 'Tajawal', 'Cairo', system-ui, -apple-system, sans-serif;
}
.success-card {
    background: #fff;
    border: none;
    box-shadow: none; 
    padding: 2rem 1rem;
    max-width: 650px;
    margin: 0 auto;
}
.crown-icon-wrap {
    width: 80px;
    height: 80px;
    border: 1.5px solid #d4b581;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: #c99f57;
    font-size: 2.2rem;
    position: relative;
}
.crown-icon-wrap::before, .crown-icon-wrap::after {
    content: "✨";
    position: absolute;
    font-size: 1.1rem;
    color: #d4b581;
    opacity: 0.6;
}
.crown-icon-wrap::before { top: -5px; left: -25px; }
.crown-icon-wrap::after { bottom: 10px; right: -25px; }

.success-title {
    font-size: 1.7rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}
.success-title i {
    color: #df9c31;
    font-size: 1.5rem;
}
.success-subtitle {
    font-size: 1.05rem;
    color: #374151;
    font-weight: 600;
    margin-bottom: 2.5rem;
}
.order-summary-box {
    border: 1.5px solid #f5eee4;
    border-radius: 12px;
    padding: 1.5rem 1rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    background: #fff;
}
.order-summary-col {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.25rem;
}
.order-summary-divider {
    width: 1.5px;
    height: 55px;
    background: #f5eee4;
    margin: 0 1rem;
}
.summary-icon {
    width: 48px;
    height: 48px;
    border: 1.5px solid #f5eee4;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ab7d39;
    font-size: 1.35rem;
    background: #fff;
    flex-shrink: 0;
}
.summary-label {
    font-size: 0.85rem;
    color: #111827;
    font-weight: 800;
    margin-bottom: 0.35rem;
}
.summary-value {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}
.summary-value.price {
    color: #ab7d39;
    font-size: 1.2rem;
    font-weight: 800;
}

.whats-next-divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #ab7d39;
    font-weight: 800;
    font-size: 1.2rem;
    margin: 2rem 0 1.5rem;
}
.whats-next-divider::before,
.whats-next-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1.5px solid #f5eee4;
}
.whats-next-divider::before { margin-left: 1rem; }
.whats-next-divider::after { margin-right: 1rem; }

.info-box {
    border: 1.5px solid #f5eee4;
    border-radius: 12px;
    padding: 0.85rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-bottom: 0.85rem;
    background: #ffffff;
}
.info-box.highlight {
    background: #faf4ec;
    border-color: #faf4ec;
}
.info-icon {
    width: 50px;
    height: 50px;
    background: #faf4ec;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ab7d39;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.info-box.highlight .info-icon {
    background: #ffffff;
}
.info-text {
    font-weight: 700;
    color: #111827;
    font-size: 1.05rem;
    flex: 1;
    text-align: center;
    padding-left: 50px; /* offset the icon width to truly center the text */
}
@media (max-width: 576px) {
    .info-text {
        padding-left: 0;
        text-align: right;
    }
}
.info-box.highlight .info-text {
    display: flex;
    flex-direction: column;
}
.info-box.highlight .info-text small {
    font-weight: 700;
    color: #4b5563;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.btn-store {
    background: #ab7d39;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 1.15rem;
    width: 100%;
    font-weight: 800;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-top: 2rem;
    transition: 0.2s;
}
.btn-store:hover {
    background: #94692e;
    color: #fff;
}

@media (max-width: 576px) {
    .order-summary-box {
        flex-direction: column;
        gap: 1.5rem;
        padding: 1.5rem;
    }
    .order-summary-divider {
        width: 100%;
        height: 1.5px;
        margin: 0;
    }
    .order-summary-col {
        justify-content: flex-start;
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="success-page">
    <div class="container px-xl-5">
        <div class="success-card">
            
            <div class="text-center">
                <div class="crown-icon-wrap">
                    <i class="fas fa-crown"></i>
                </div>
                
                <h1 class="success-title">
                    <i class="fas fa-check-circle"></i>
                    تم استلام طلبك بنجاح
                </h1>
                
                <p class="success-subtitle">
                    شكراً لك على ثقتك في Hijab Princesses <i class="fas fa-heart mx-1" style="color: #df9c31;"></i>
                </p>
            </div>

            <div class="order-summary-box">
                <!-- Amount -->
                <div class="order-summary-col">
                    <div class="summary-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="text-end text-sm-start text-md-end">
                        <div class="summary-label">المبلغ عند الاستلام</div>
                        <div class="summary-value price" dir="ltr">{{ $order->formatted_total }}</div>
                    </div>
                </div>
                
                <div class="order-summary-divider"></div>
                
                <!-- Order ID -->
                <div class="order-summary-col">
                    <div class="summary-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="text-end text-sm-start text-md-end">
                        <div class="summary-label">رقم الطلب</div>
                        <div class="summary-value" style="font-size: 0.95rem;">{{ $order->order_number }}#</div>
                    </div>
                </div>
            </div>

            <div class="whats-next-divider">ماذا بعد؟</div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div class="info-text">
                    سنتصل بك عبر الهاتف لتأكيد الطلب
                </div>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="info-text">
                    سيصلك طلبك خلال 24 إلى 48 ساعة بإذن الله
                </div>
            </div>

            <div class="info-box highlight">
                <div class="info-icon">
                    <i class="fas fa-phone-volume"></i>
                </div>
                <div class="info-text">
                    <small>المرجو إبقاء هاتفك متاحاً</small>
                    حتى نتمكن من التواصل معك
                </div>
            </div>

            <a href="{{ route('shop.index') }}" class="btn-store text-decoration-none">
                <i class="fas fa-shopping-bag"></i> العودة للمتجر
            </a>

        </div>
    </div>
</div>
@endsection
