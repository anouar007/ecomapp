<div class="row g-4">
    @forelse($products as $product)
    @php
        // Resolve product card image: always use the uploaded image from product page
        $imgUrl = $product->image_url;

        $lowerName = strtolower($product->name ?? '');

        // Use custom product size from dashboard, fallback to name-based detection if empty
        $volume = $product->size ?: $product->volume;
        if (!$volume) {
            if (str_contains($lowerName, 'shampooing')) $volume = '200ml';
            elseif (str_contains($lowerName, 'huile')) $volume = '50ml';
            elseif (str_contains($lowerName, 'spray')) $volume = '125ml';
            elseif (str_contains($lowerName, 'solaire') || str_contains($lowerName, 'crème')) $volume = '50ml';
            elseif (str_contains($lowerName, 'coffret') || str_contains($lowerName, 'pack')) $volume = 'Coffret 4 Soins';
            else $volume = 'Soin Bio';
        }

        $activeTag = '100% Bio du Haut Atlas';
        if (str_contains($lowerName, 'shampooing')) $activeTag = 'Romarin sauvage & Argan';
        elseif (str_contains($lowerName, 'huile')) $activeTag = "Nigelle & Cèdre de l'Atlas";
        elseif (str_contains($lowerName, 'spray')) $activeTag = 'Lavande & Romarin pur';
        elseif (str_contains($lowerName, 'solaire')) $activeTag = 'Filtre Minéral SPF 50+';
        elseif (str_contains($lowerName, 'coffret')) $activeTag = 'Rituel Intégral Botanique';

        $badgeText = $loop->first ? '★ Best-Seller' : ($product->isOnSale() ? '−' . $product->discount_percentage . '%' : '100% Bio');
    @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <div class="sp-card-pro h-100" id="productCard-{{ $product->id }}">
            {{-- Zone Média --}}
            <div class="sp-card-pro-media" style="height: 220px;">
                <div class="sp-card-top-badges">
                    <span class="sp-badge-tag">{{ $badgeText }}</span>
                    <span class="sp-badge-volume">{{ $volume }}</span>
                </div>

                <a href="{{ route('shop.show', $product->id) }}" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;" aria-label="Découvrir {{ $product->name }}">
                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="sp-card-pro-img" loading="lazy" style="max-height: 180px; width: auto; object-fit: contain;">
                </a>

                <a href="{{ route('shop.show', $product->id) }}" class="sp-card-quickview-btn" style="text-decoration: none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <span>Découvrir le soin</span>
                </a>
            </div>

            {{-- Corps de carte --}}
            <div class="sp-card-pro-body">
                <div class="sp-card-rating-row">
                    <span class="sp-stars">★★★★★</span>
                    <span class="sp-rating-score">5.0</span>
                    <span style="font-size: 0.72rem; color: var(--sp-text-muted, #6b7a72);">(142 avis)</span>
                </div>

                <h3 class="sp-card-title-pro">
                    <a href="{{ route('shop.show', $product->id) }}">{{ $product->name }}</a>
                </h3>

                <div class="sp-card-botanical-tag" title="Actif star">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    <span>{{ $activeTag }}</span>
                </div>

                <p class="sp-card-excerpt">
                    {{ Str::limit(strip_tags((string)($product->description ?? '')), 85) }}
                </p>

                <div class="sp-stock-tag">
                    <span class="sp-stock-dot"></span>
                    <span>En stock • Atelier Aït Oumdis</span>
                </div>
            </div>

            {{-- Pied de carte --}}
            <div class="sp-card-pro-footer">
                <div class="sp-card-pro-price">
                    <span class="sp-price-main">{{ currency($product->price) }}</span>
                    <span class="sp-price-sub">Paiement à la livraison</span>
                </div>

                <div class="sp-card-actions-group">
                    <button type="button" class="sp-btn-add-main" onclick="event.stopPropagation(); addToCart({{ $product->id }}, 1, this)" aria-label="Ajouter {{ $product->name }} au panier" title="Ajouter au panier">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        <span>Ajouter</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="shop-empty text-center py-5" style="background: #ffffff; border-radius: 20px; border: 1px solid rgba(12,38,30,0.06); padding: 48px 24px;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #faf8f3; color: #c28d32; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; border: 1px solid rgba(226,173,80,0.3);">
                <i class="fas fa-leaf fa-lg"></i>
            </div>
            <h5 style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.3rem; color: #0c261e; font-weight: 700;">Aucun soin trouvé</h5>
            <p style="color: #6b7a72; font-size: 0.9rem; max-width: 420px; margin: 0 auto 20px;">Modifiez vos critères ou réinitialisez les filtres pour découvrir notre collection botanique.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4 py-2" style="background: #0c261e; border: none; font-weight: 700; font-size: 0.88rem;">
                <i class="fas fa-redo me-2"></i>Réinitialiser les filtres
            </a>
        </div>
    </div>
    @endforelse
</div>

@if($products->hasPages())
<div class="mt-5 d-flex justify-content-center shop-pagination">
    {{ $products->links() }}
</div>
@endif