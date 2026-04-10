<div class="brand-card h-100 pcard pcard-reveal border-0 position-relative" 
     data-product-id="{{ $product->id }}" 
     data-aos="fade-up" 
     data-aos-delay="{{ (($loop->index ?? 0) % 4) * 50 }}"
     style="--reveal-delay: {{ (($loop->index ?? 0) % 8) * 0.1 }}s">
    {{-- Main Clickable Area (Z-index 1) --}}
    <a href="{{ route('shop.show', $product->id) }}" class="position-absolute top-0 start-0 w-100 h-100 z-1" aria-label="View {{ $product->translated_name }}"></a>

    {{-- Visual Image Area --}}
    <div class="product-v2-image position-relative overflow-hidden" style="aspect-ratio: 4/5;">
        <img src="{{ $product->main_image ? (Str::startsWith($product->main_image, 'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}" 
             alt="{{ $product->translated_name }} - Hijab Princesses" 
             class="w-100 h-100 object-fit-cover transition-hero"
             loading="lazy"
             decoding="async">
             
        @if($product->isOnSale())
            <div class="position-absolute shadow-sm" style="top: 10px; right: 10px; z-index: 5;">
                <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold font-body" style="font-size: 0.75rem; letter-spacing: 0.5px;">تخفيض {{ $product->discount_percentage }}%</span>
            </div>
        @endif
    </div>

    {{-- Content Body (Z-index 2 to allow interaction with variants/buttons) --}}
    {{-- We use pointer-events: none on the body and auto on children to let clicks "fall through" to the card link in whitespace --}}
    <div class="product-v2-body p-3 d-flex flex-column position-relative z-2" style="pointer-events: none;">
        <h5 class="brand-heading h6 mb-2 text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; pointer-events: auto;">{{ $product->translated_name }}</h5>
        
        <div class="product-v2-price mb-3" style="pointer-events: auto;">
             @if($product->isOnSale())
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-{{ $product->id }}">{{ $product->formatted_sale_price }}</span>
                <span class="price-old text-danger small text-decoration-line-through ms-2">{{ $product->formatted_price }}</span>
            @else
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-{{ $product->id }}">{{ $product->formatted_price }}</span>
            @endif
        </div>

        @if(!$product->isInStock())
        <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle me-1"></i>نفذ من المخزن</div>
        @endif
    </div>
</div>
