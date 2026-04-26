@extends('layouts.frontend')

@section('meta_title', __('Order Confirmed') . ' — Ait Oumdis')

@push('styles')
<style>
/* ── Success Page ── */
.success-bg {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0fdf4 0%, #ffffff 50%, #f9fafb 100%);
    display: flex; align-items: center; padding: 60px 0;
}

/* Animated checkmark circle */
.check-wrap {
    width: 96px; height: 96px; border-radius: 50%;
    background: linear-gradient(135deg, #3BB878, #2f9461);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 32px;
    box-shadow: 0 12px 40px rgba(59,184,120,.35);
    animation: popIn .6s cubic-bezier(.34,1.56,.64,1) both;
}
.check-wrap i { color: #fff; font-size: 2.2rem; }

@keyframes popIn {
    from { transform: scale(0); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}

/* Confetti dots */
.confetti-dot {
    position: absolute; width: 8px; height: 8px; border-radius: 50%;
    animation: confettiFall 2.5s ease-out forwards;
    opacity: 0;
}
@keyframes confettiFall {
    0%   { transform: translateY(-20px) scale(0); opacity: 1; }
    80%  { opacity: 1; }
    100% { transform: translateY(80px) scale(1); opacity: 0; }
}

/* Card */
.success-card {
    background: #fff; border-radius: 28px;
    padding: 52px 44px; box-shadow: 0 20px 60px rgba(0,0,0,.08);
    border: 1px solid #f0fdf4; position: relative; overflow: hidden;
    animation: slideUp .5s ease both .1s;
}
@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to   { transform: translateY(0); opacity: 1; }
}
.success-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 4px; background: linear-gradient(90deg, #3BB878, #2f9461, #3BB878);
    background-size: 200% 100%; animation: shimmer 2s linear infinite;
}
@keyframes shimmer { to { background-position: 200% 0; } }

.success-title {
    font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 900;
    color: #111827; line-height: 1.25; margin-bottom: 14px;
}
.success-subtitle { font-size: .93rem; color: #6B7280; line-height: 1.7; margin-bottom: 36px; }

/* Order detail box */
.order-box {
    background: #f9fafb; border-radius: 18px; border: 1px solid #e8f7ef;
    overflow: hidden; margin-bottom: 32px;
}
.order-box-header {
    background: linear-gradient(135deg, #3BB878, #2f9461);
    padding: 14px 24px; display: flex; align-items: center; gap: 10px;
}
.order-box-header span { color: #fff; font-size: .8rem; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; }
.order-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 24px; border-bottom: 1px solid #f1f5f9;
}
.order-row:last-child { border-bottom: none; }
.order-row-label { font-size: .78rem; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: .8px; }
.order-row-value { font-size: .93rem; font-weight: 800; color: #111827; }
.order-row-value.green { font-size: 1.25rem; color: #3BB878; }

/* CTA */
.btn-success-cta {
    display: flex; align-items: center; justify-content: center; gap:10px;
    width: 100%; padding: 16px; border-radius: 14px;
    background: #3BB878; color: #fff; text-decoration: none;
    font-weight: 800; font-size: .95rem; letter-spacing: .3px;
    transition: all .3s cubic-bezier(.34,1.56,.64,1);
    box-shadow: 0 8px 28px rgba(59,184,120,.3);
}
.btn-success-cta:hover { background: #2f9461; color: #fff; transform: translateY(-2px); box-shadow: 0 14px 36px rgba(59,184,120,.4); }

/* Steps */
.next-steps { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-top: 32px; }
.step-item { text-align: center; padding: 16px 10px; background: #f9fafb; border-radius: 14px; border: 1px solid #f1f5f9; }
.step-icon { width: 40px; height: 40px; border-radius: 50%; background: #e8f7ef; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
.step-icon i { color: #3BB878; font-size: .85rem; }
.step-label { font-size: .72rem; font-weight: 700; color: #374151; line-height: 1.4; }

/* Phone notice */
.phone-notice {
    margin-top: 28px; padding: 18px 20px; border-radius: 14px;
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border: 1px solid #fde68a; display: flex; align-items: center; gap: 14px;
}
.phone-notice i { color: #f59e0b; font-size: 1.2rem; flex-shrink: 0; }
.phone-notice-text { font-size: .85rem; color: #92400e; font-weight: 600; line-height: 1.5; }

@media(max-width:600px) {
    .success-card { padding: 32px 22px; border-radius: 20px; }
    .next-steps { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="success-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="success-card">

                    {{-- Animated check icon --}}
                    <div class="position-relative" id="confettiZone">
                        <div class="check-wrap">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div class="text-center">
                        <h1 class="success-title">{{ __('Order Confirmed! 🎉') }}</h1>
                        <p class="success-subtitle">
                            {{ __('Thank you for shopping with Ait Oumdis. Your order has been received and is being prepared with care.') }}
                        </p>
                    </div>

                    {{-- Order details --}}
                    <div class="order-box">
                        <div class="order-box-header">
                            <i class="fas fa-receipt" style="color:#fff;font-size:.85rem"></i>
                            <span>{{ __('Order Summary') }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-row-label">{{ __('Order Number') }}</span>
                            <span class="order-row-value">#{{ $order->order_number }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-row-label">{{ __('Customer') }}</span>
                            <span class="order-row-value">{{ $order->customer_name }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-row-label">{{ __('City') }}</span>
                            <span class="order-row-value">{{ $order->shipping_city }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-row-label">{{ __('Date') }}</span>
                            <span class="order-row-value">{{ $order->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-row-label">{{ __('Total') }}</span>
                            <span class="order-row-value green">{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <a href="{{ route('shop.index') }}" class="btn-success-cta">
                        <i class="fas fa-shopping-bag"></i>
                        {{ __('Continue Shopping') }}
                    </a>

                    {{-- What happens next --}}
                    <div class="next-steps">
                        <div class="step-item">
                            <div class="step-icon"><i class="fas fa-phone-alt"></i></div>
                            <div class="step-label">{{ __('We call to confirm') }}</div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon"><i class="fas fa-box-open"></i></div>
                            <div class="step-label">{{ __('Order prepared') }}</div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon"><i class="fas fa-truck"></i></div>
                            <div class="step-label">{{ __('Delivered to you') }}</div>
                        </div>
                    </div>

                    {{-- Phone notice --}}
                    <div class="phone-notice">
                        <i class="fas fa-phone-volume"></i>
                        <div class="phone-notice-text">
                            {{ __('We will contact you shortly at') }} <strong>{{ $order->customer_phone }}</strong> {{ __('to confirm your delivery time. Please keep your phone available.') }}
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
// Confetti burst on load
(function() {
    const colors = ['#3BB878','#2f9461','#bbf7d0','#f59e0b','#fde68a','#60a5fa'];
    const zone = document.getElementById('confettiZone');
    for (let i = 0; i < 18; i++) {
        const dot = document.createElement('span');
        dot.className = 'confetti-dot';
        dot.style.cssText = `
            background:${colors[i % colors.length]};
            left:${10 + Math.random() * 80}%;
            top:${Math.random() * 40}px;
            width:${6 + Math.random() * 8}px;
            height:${6 + Math.random() * 8}px;
            animation-delay:${Math.random() * .8}s;
            animation-duration:${1.5 + Math.random()}s;
        `;
        zone.appendChild(dot);
    }
})();
</script>
@endpush
