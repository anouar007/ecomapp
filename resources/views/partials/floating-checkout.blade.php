@php
    $cart = session('cart', []);
    $cartCount = array_sum(array_column($cart, 'quantity'));
@endphp

@if(!request()->routeIs('checkout.*'))
<!-- Floating Button to Confirm Order -->
<style>
.sp-floating-checkout {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(30px) scale(0.92);
    z-index: 9980;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #0c261e 0%, #174236 100%);
    color: #ffffff !important;
    text-decoration: none !important;
    padding: 10px 22px 10px 14px;
    border-radius: 9999px;
    border: 1.5px solid rgba(226, 173, 80, 0.45);
    box-shadow: 0 10px 28px rgba(12, 38, 30, 0.42), 0 2px 8px rgba(0, 0, 0, 0.18);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.32s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.25s ease,
                border-color 0.25s ease;
    cursor: pointer;
    user-select: none;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.sp-floating-checkout.is-visible {
    opacity: 1;
    pointer-events: auto;
    transform: translateX(-50%) translateY(0) scale(1);
}

.sp-floating-checkout.is-hidden {
    opacity: 0 !important;
    pointer-events: none !important;
    transform: translateX(-50%) translateY(30px) scale(0.92) !important;
}

.sp-floating-checkout:hover {
    background: linear-gradient(135deg, #0e2e25 0%, #1c5243 100%);
    border-color: #e2ad50;
    transform: translateX(-50%) translateY(-3px) scale(1.03);
    box-shadow: 0 14px 34px rgba(12, 38, 30, 0.5), 0 0 18px rgba(226, 173, 80, 0.28);
    color: #ffffff !important;
}

.sp-floating-checkout:active {
    transform: translateX(-50%) translateY(0) scale(0.98);
}

.sp-fc-icon-box {
    position: relative;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(226, 173, 80, 0.16);
    border: 1px solid rgba(226, 173, 80, 0.38);
    color: #e2ad50;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}

.sp-floating-checkout:hover .sp-fc-icon-box {
    transform: scale(1.06);
    background: rgba(226, 173, 80, 0.26);
}

.sp-fc-badge {
    position: absolute;
    top: -5px;
    right: -6px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 9999px;
    background: #e2ad50;
    color: #0c261e;
    font-size: 0.68rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #0c261e;
    line-height: 1;
}

.sp-fc-text {
    font-size: 0.88rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #ffffff;
    white-space: nowrap;
}

.sp-fc-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #e2ad50;
    transition: transform 0.22s ease;
    flex-shrink: 0;
}

.sp-floating-checkout:hover .sp-fc-arrow {
    transform: translateX(4px);
}

@keyframes spFloatingPulse {
    0% { transform: translateX(-50%) scale(1); }
    30% { transform: translateX(-50%) scale(1.08); box-shadow: 0 0 25px rgba(226, 173, 80, 0.6); }
    60% { transform: translateX(-50%) scale(0.98); }
    100% { transform: translateX(-50%) scale(1); }
}

.sp-floating-checkout.sp-pulse {
    animation: spFloatingPulse 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Mobile: position on bottom-left, opposite of WhatsApp on bottom-right */
@media (max-width: 767px) {
    .sp-floating-checkout {
        bottom: 18px;
        left: 14px;
        transform: translateY(30px) scale(0.92);
        padding: 9px 16px 9px 11px;
        gap: 9px;
        max-width: calc(100vw - 86px);
    }
    .sp-floating-checkout.is-visible {
        transform: translateY(0) scale(1);
    }
    .sp-floating-checkout.is-hidden {
        transform: translateY(30px) scale(0.92) !important;
    }
    .sp-floating-checkout:hover {
        transform: translateY(-2px) scale(1.02);
    }
    .sp-floating-checkout:active {
        transform: translateY(0) scale(0.98);
    }
    .sp-fc-text {
        font-size: 0.82rem;
    }
    @keyframes spFloatingPulseMobile {
        0% { transform: scale(1); }
        30% { transform: scale(1.08); box-shadow: 0 0 25px rgba(226, 173, 80, 0.6); }
        60% { transform: scale(0.98); }
        100% { transform: scale(1); }
    }
    .sp-floating-checkout.sp-pulse {
        animation: spFloatingPulseMobile 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
}
</style>

<a href="{{ route('checkout.index') }}" 
   id="spFloatingCheckout" 
   class="sp-floating-checkout {{ $cartCount > 0 ? 'is-visible' : '' }}" 
   title="Confirmer la commande"
   aria-label="Confirmer la commande">
    <span class="sp-fc-icon-box">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>
        <span class="sp-fc-badge" id="spFloatingCartBadge">{{ $cartCount }}</span>
    </span>
    <span class="sp-fc-text">Confirmer la commande</span>
    <span class="sp-fc-arrow">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
    </span>
</a>
@endif
