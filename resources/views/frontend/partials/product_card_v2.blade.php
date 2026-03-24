<div class="brand-card h-100 pcard border-0" data-product-id="{{ $product->id }}">
    <div class="product-v2-image position-relative overflow-hidden" style="aspect-ratio: 4/5;">
        <img src="{{ $product->main_image ? (Str::startsWith($product->main_image, 'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}" alt="{{ $product->translated_name }}" class="w-100 h-100 object-fit-cover">
        <div class="product-v2-overlay">
            <a href="{{ route('shop.show', $product->id) }}" class="btn-overlay brand-heading text-uppercase small" style="letter-spacing: 1px;">
                <i class="fas fa-eye me-2"></i> التفاصيل
            </a>
        </div>
    </div>
    <div class="product-v2-body p-3 d-flex flex-column">
        <h5 class="brand-heading h6 mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">{{ $product->translated_name }}</h5>
        
        <div class="product-v2-price mb-3">
             @if($product->isOnSale())
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-{{ $product->id }}">{{ $product->formatted_sale_price }}</span>
                <span class="price-old text-muted small text-decoration-line-through ms-2">{{ $product->formatted_price }}</span>
            @else
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-{{ $product->id }}">{{ $product->formatted_price }}</span>
            @endif
        </div>

        {{-- Card Variations Selector --}}
        @if($product->variants->count() > 0)
        <div class="pcard-variants mb-3">
             @php 
                $sizes = $product->available_sizes;
                $colors = $product->available_colors;
            @endphp

            @if($colors->count() > 0)
            <div class="d-flex align-items-center gap-2 mb-2">
                @foreach($colors->take(5) as $color)
                <div class="pcard-color-dot border" 
                     style="background: {{ $color->color_code ?: '#eee' }}; width: 14px; height: 14px; cursor: pointer; border-radius: 50%;" 
                     onclick="selectCardVariant({{ $product->id }}, 'color', '{{ $color->color }}', this, false)"
                     title="{{ $color->color }}">
                </div>
                @endforeach
                @if($colors->count() > 5) <span class="text-muted small">+{{ $colors->count() - 5 }}</span> @endif
            </div>
            @endif

            @if($sizes->count() > 0)
            <div class="d-flex flex-wrap gap-1">
                @foreach($sizes->take(4) as $size)
                <div class="pcard-size-pill border px-2 py-0 small text-muted rounded-pill" 
                     style="font-size: 0.65rem; cursor: pointer;"
                     onclick="selectCardVariant({{ $product->id }}, 'size', '{{ $size }}', this, false)">
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

        <button onclick="addToCart({{ $product->id }})" class="btn-brand-primary w-100 btn-sm py-2 mt-auto">
            أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
        </button>
    </div>
</div>
