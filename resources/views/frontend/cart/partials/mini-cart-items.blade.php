@forelse(session('cart', []) as $id => $details)
    <div class="mc-item" id="cart-item-{{ $id }}">
        <div class="mc-item-img-wrap">
            @if(!empty($details['image']))
                <img src="{{ Storage::url($details['image']) }}"
                     alt="{{ $details['name'] }}"
                     class="mc-item-img"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="mc-item-img-placeholder" style="display:none;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
            @else
                <div class="mc-item-img-placeholder">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
            @endif
            <span class="mc-qty-badge">x{{ $details['quantity'] }}</span>
        </div>
        <div class="mc-item-body">
            <div class="mc-item-name" title="{{ $details['name'] }}">{{ $details['name'] }}</div>
            <div class="mc-item-cat">{{ $details['category_name'] ?? 'Soin naturel' }}</div>
            <div class="mc-item-footer">
                <span class="mc-item-price">{{ currency($details['price']) }}</span>
                <div class="mc-qty-ctrl">
                    <button class="mc-qty-btn" onclick="updateQty({{ $id }}, {{ $details['quantity'] - 1 }})">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <input class="mc-qty-val" value="{{ $details['quantity'] }}" readonly>
                    <button class="mc-qty-btn" onclick="updateQty({{ $id }}, {{ $details['quantity'] + 1 }})">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <button class="mc-remove-btn" onclick="removeItem({{ $id }})" title="Retirer">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
@empty
    <div class="mc-empty">
        <div class="mc-empty-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <div class="mc-empty-title">Votre panier est vide</div>
        <div class="mc-empty-sub">Découvrez nos soins 100% naturels<br>du Haut Atlas marocain.</div>
        <a href="{{ route('shop.index') }}" class="mc-btn-checkout" style="margin-top:12px;width:auto;padding:12px 28px;display:inline-flex;">
            <span>Voir la boutique</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    </div>
@endforelse
