@php
    $variants = $product->relationLoaded('variants') ? $product->variants : $product->variants()->get();
    $defaultVariant = $variants->firstWhere('is_default', true) ?? $variants->first();
    $unit = $defaultVariant ? $defaultVariant->size : ($product->size ?: ($product->volume ?: '1 KG'));
    $displayPrice = $defaultVariant ? (float) $defaultVariant->price : (float) $product->price;
    $inStock = method_exists($product, 'isInStock') ? $product->isInStock() : (($product->stock ?? 1) > 0);
    $onSale = !$defaultVariant && method_exists($product, 'isOnSale') ? $product->isOnSale() : false;

    // Extract up to 2 crisp bullet benefits from description if available
    $rawLines = preg_split('/\r\n|\r|\n/', strip_tags((string) ($product->description ?? '')));
    $bullets = [];
    foreach ($rawLines as $line) {
        $trimmed = trim($line);
        if (str_starts_with($trimmed, '•') || str_starts_with($trimmed, '-')) {
            $clean = trim(ltrim($trimmed, "•- \t"));
            if ($clean !== '') {
                $bullets[] = $clean;
            }
        }
        if (count($bullets) >= 2) {
            break;
        }
    }
    $fallbackExcerpt = Str::limit(trim(strip_tags((string) ($product->description ?? ''))), 85);
@endphp

<div class="aod-product-card"
     data-category="cat-{{ $product->category_id }}"
     data-selected-variant-id="{{ $defaultVariant ? $defaultVariant->id : '' }}"
     id="productCard-{{ $product->id }}"
     onclick="if (!event.target.closest('.aod-add-btn') && !event.target.closest('.aod-size-pill') && !event.target.closest('a')) window.location.href='{{ route('shop.show', $product->id) }}';"
     style="cursor: pointer;">
    
    {{-- Full-Bleed Studio Image --}}
    <div class="aod-card-media">
        <span class="aod-card-weight-badge js-card-weight-badge">{{ $unit }}</span>

        @if(!$inStock)
            <span class="aod-card-sale-badge" style="background: #dc2626;">Rupture</span>
        @elseif($onSale)
            <span class="aod-card-sale-badge">−{{ $product->discount_percentage }}%</span>
        @endif

        <a href="{{ route('shop.show', $product->id) }}" class="aod-card-media-link" aria-label="Découvrir {{ $product->name }}">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aod-card-img" loading="lazy">
        </a>

        <span class="aod-card-quick-hint">Voir détails &rarr;</span>
    </div>

    {{-- Card Body --}}
    <div class="aod-card-body">
        <div class="aod-card-meta-row">
            <span class="aod-card-cat-text">{{ $product->category_name ?: 'Terroir Aït Oumdis' }}</span>
            <span class="aod-card-stock {{ !$inStock ? 'is-out' : '' }}">
                {{ $inStock ? 'En stock' : 'Épuisé' }}
            </span>
        </div>

        <h3 class="aod-card-title">
            <a href="{{ route('shop.show', $product->id) }}">{{ $product->name }}</a>
        </h3>

        @if(count($bullets) > 0)
            <ul class="aod-card-benefits-list">
                @foreach($bullets as $b)
                    <li>
                        <i class="fas fa-check"></i>
                        <span>{{ $b }}</span>
                    </li>
                @endforeach
            </ul>
        @elseif($fallbackExcerpt)
            <p class="aod-card-excerpt">{{ $fallbackExcerpt }}</p>
        @else
            <p class="aod-card-excerpt">Produit 100% pur et naturel de la Coopérative Aït Oumdis.</p>
        @endif

        @if($variants->count() > 1)
            <div class="aod-size-pills" onclick="event.stopPropagation();">
                @foreach($variants as $v)
                    <button type="button"
                            class="aod-size-pill {{ ($defaultVariant && $defaultVariant->id === $v->id) ? 'active' : '' }}"
                            data-variant-id="{{ $v->id }}"
                            data-size="{{ $v->size }}"
                            data-formatted-price="{{ currency($v->price) }}"
                            onclick="selectAodCardSize(this, event)">
                        {{ $v->size }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Card Footer --}}
    <div class="aod-card-footer">
        <div class="aod-card-price-wrap">
            <div class="aod-card-price-row">
                <span class="aod-card-price js-card-price">
                    {{ $onSale ? $product->formatted_sale_price : currency($displayPrice) }}
                </span>
                <span class="aod-card-price-unit js-card-unit">/ {{ $unit }}</span>
            </div>
            @if($onSale)
                <span class="aod-card-price-old">{{ currency($product->price) }}</span>
            @endif
        </div>

        <button type="button"
                class="aod-add-btn"
                onclick="event.stopPropagation(); addToCart({{ $product->id }}, 1, this, this.closest('.aod-product-card').getAttribute('data-selected-variant-id') || null)"
                aria-label="Ajouter {{ $product->name }} au panier"
                {{ !$inStock ? 'disabled' : '' }}>
            <i class="fas fa-shopping-basket"></i>
            <span>{{ $inStock ? 'Ajouter' : 'Épuisé' }}</span>
        </button>
    </div>

</div>
