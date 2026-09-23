<div class="mc-footer">
    <div class="mc-subtotal-row">
        <span class="mc-subtotal-label">Sous-total</span>
        <span class="mc-subtotal-value" id="mini-cart-total">{{ currency($total) }}</span>
    </div>
    <a href="{{ route('checkout.index') }}" class="mc-btn-checkout">
        <span>Commander</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
    <a href="{{ route('cart.index') }}" class="mc-btn-view-cart" onclick="navigateFromCart(event, '{{ route('cart.index') }}')">
        Voir le panier complet
    </a>
</div>
