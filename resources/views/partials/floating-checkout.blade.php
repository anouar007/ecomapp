@php
    $cart = session('cart', []);
    $cartCount = array_sum(array_column($cart, 'quantity'));
@endphp

@if(!request()->routeIs('checkout.*'))
<!-- Floating Button to Confirm Order -->
<style>
.sp-floating-checkout {
    position: fixed !important;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9980 !important;
    display: none;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #1e4d34 0%, #2f8825 100%) !important;
    color: #ffffff !important;
    text-decoration: none !important;
    padding: 10px 22px 10px 14px;
    border-radius: 9999px;
    border: 1.5px solid rgba(107, 191, 54, 0.55) !important;
    box-shadow: 0 10px 28px rgba(30, 77, 52, 0.35), 0 2px 8px rgba(0, 0, 0, 0.14) !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    cursor: pointer;
    user-select: none;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.sp-floating-checkout.is-visible {
    display: inline-flex !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    visibility: visible !important;
    transform: translateX(-50%) translateY(0) scale(1) !important;
}

.sp-floating-checkout.is-hidden {
    display: none !important;
    opacity: 0 !important;
    pointer-events: none !important;
    visibility: hidden !important;
}

.sp-floating-checkout:hover {
    background: linear-gradient(135deg, #163b27 0%, #256f1c 100%) !important;
    border-color: #6bbf36 !important;
    transform: translateX(-50%) translateY(-3px) scale(1.03) !important;
    box-shadow: 0 14px 34px rgba(30, 77, 52, 0.45) !important;
    color: #ffffff !important;
}

.sp-floating-checkout:active {
    transform: translateX(-50%) translateY(0) scale(0.98) !important;
}

.sp-fc-icon-box {
    position: relative;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}

.sp-floating-checkout:hover .sp-fc-icon-box {
    transform: scale(1.08);
    background: rgba(255, 255, 255, 0.24);
}

.sp-fc-badge {
    position: absolute;
    top: -5px;
    right: -6px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 9999px;
    background: #6bbf36;
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #1e4d34;
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

@keyframes spPulseDesktop {
    0% { transform: translateX(-50%) scale(1); }
    40% { transform: translateX(-50%) scale(1.08); box-shadow: 0 0 24px rgba(226, 173, 80, 0.6) !important; }
    100% { transform: translateX(-50%) scale(1); }
}

.sp-floating-checkout.sp-pulse {
    animation: spPulseDesktop 0.55s ease-out;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .sp-floating-checkout {
        bottom: 18px !important;
        left: 14px !important;
        transform: none !important;
        padding: 9px 16px 9px 11px !important;
        gap: 9px !important;
        max-width: calc(100vw - 86px) !important;
    }
    .sp-floating-checkout.is-visible {
        display: inline-flex !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        visibility: visible !important;
        transform: none !important;
    }
    .sp-floating-checkout.is-hidden {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
        visibility: hidden !important;
    }
    .sp-floating-checkout:hover {
        transform: translateY(-2px) scale(1.02) !important;
    }
    .sp-floating-checkout:active {
        transform: translateY(0) scale(0.98) !important;
    }
    .sp-fc-text {
        font-size: 0.82rem;
    }
    @keyframes spPulseMobile {
        0% { transform: scale(1); }
        40% { transform: scale(1.08); box-shadow: 0 0 24px rgba(226, 173, 80, 0.6) !important; }
        100% { transform: scale(1); }
    }
    .sp-floating-checkout.sp-pulse {
        animation: spPulseMobile 0.55s ease-out;
    }
}
</style>

<a href="{{ route('checkout.index') }}" 
   id="spFloatingCheckout" 
   class="sp-floating-checkout {{ $cartCount > 0 ? 'is-visible' : '' }}" 
   style="{{ $cartCount > 0 ? 'display: inline-flex; opacity: 1; pointer-events: auto;' : 'display: none; opacity: 0; pointer-events: none;' }}"
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
