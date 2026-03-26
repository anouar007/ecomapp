<div class="brand-card h-100 pcard border-0 position-relative" data-product-id="{{ $product->id }}">
    {{-- Main Clickable Area (Z-index 1) --}}
    <a href="{{ route('shop.show', $product->id) }}" class="position-absolute top-0 start-0 w-100 h-100 z-1" aria-label="View {{ $product->translated_name }}"></a>

    {{-- Visual Image Area --}}
    <div class="product-v2-image position-relative overflow-hidden" style="aspect-ratio: 4/5;">
        <img src="{{ $product->main_image ? (Str::startsWith($product->main_image, 'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}" alt="{{ $product->translated_name }}" class="w-100 h-100 object-fit-cover transition-hero">
    </div>

    {{-- Content Body (Z-index 2 to allow interaction with variants/buttons) --}}
    {{-- We use pointer-events: none on the body and auto on children to let clicks "fall through" to the card link in whitespace --}}
    <div class="product-v2-body p-3 d-flex flex-column position-relative z-2" style="pointer-events: none;">
        <h5 class="brand-heading h6 mb-2 text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; pointer-events: auto;">{{ $product->translated_name }}</h5>
        
        <div class="product-v2-price mb-3" style="pointer-events: auto;">
             @if($product->isOnSale())
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-{{ $product->id }}">{{ $product->formatted_sale_price }}</span>
                <span class="price-old text-muted small text-decoration-line-through ms-2">{{ $product->formatted_price }}</span>
            @else
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-{{ $product->id }}">{{ $product->formatted_price }}</span>
            @endif
        </div>

        {{-- Card Variations Selector (Only In-Stock via Model Accessor) --}}
        @php 
            $sizes = $product->available_sizes;
            $colors = $product->available_colors;
        @endphp

        @if($product->variants->count() > 0 && ($colors->count() > 0 || $sizes->count() > 0))
        <div class="pcard-variants mb-4" style="pointer-events: auto;">
            @if($colors->count() > 0)
            <div class="d-flex align-items-center gap-2 mb-2 pcard-variant-row">
                @foreach($colors->take(5) as $color)
                    @if($color->color_image_url)
                        <div class="pcard-color-dot border shadow-sm" 
                             style="width: 34px; height: 34px; cursor: pointer; border-radius: 4px; overflow: hidden;"
                             onclick="selectCardVariant({{ $product->id }}, 'color', '{{ $color->color }}', this, false); event.stopPropagation();"
                             title="{{ $color->color }}">
                         <img src="{{ $color->color_image_url }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $product->translated_name }} - {{ $color->color }}">
                        </div>
                    @else
                        <div class="pcard-color-dot border shadow-sm" 
                             style="background: {{ $color->color_code ?: '#eee' }}; width: 24px; height: 24px; cursor: pointer; border-radius: 4px;" 
                             onclick="selectCardVariant({{ $product->id }}, 'color', '{{ $color->color }}', this, false); event.stopPropagation();"
                             title="{{ $color->color }}">
                        </div>
                    @endif
                @endforeach
                @if($colors->count() > 5) <span class="text-muted small">+{{ $colors->count() - 5 }}</span> @endif
            </div>
            @endif

            @if($sizes->count() > 0)
            <div class="d-flex flex-wrap gap-1 pcard-variant-row">
                @foreach($sizes->take(4) as $size)
                <div class="pcard-size-pill border px-2 py-0 small text-muted rounded-pill" 
                     style="font-size: 0.65rem; cursor: pointer;"
                     onclick="selectCardVariant({{ $product->id }}, 'size', '{{ $size }}', this, false); event.stopPropagation();">
                    {{ $size }}
                </div>
                @endforeach
                @if($sizes->count() > 4) <span class="text-muted small" style="font-size: 0.65rem;">+</span> @endif
            </div>
            @endif

            <input type="hidden" id="card-selected-variant-{{ $product->id }}" value="">
        </div>
        <script>
            if (typeof window.cardVariants === 'undefined') window.cardVariants = {};
            window.cardVariants[{{ $product->id }}] = {!! $product->variants_json !!};
        </script>
        @endif

        @if(!$product->isInStock())
        <div class="text-danger small mb-2"><i class="fas fa-exclamation-circle me-1"></i>نفذ من المخزن</div>
        @endif

        <div style="pointer-events: auto;" class="mt-auto">
            <button onclick="addToCart({{ $product->id }}); event.stopPropagation();" class="btn btn-brand-primary w-100 btn-sm py-2 rounded-pill d-flex align-items-center justify-content-center">
                أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
            </button>
        </div>
    </div>
</div>
