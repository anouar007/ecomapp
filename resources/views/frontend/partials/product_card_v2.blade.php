<div class="product-card pcard h-100" data-product-id="{{ $product->id }}"
     data-aos="fade-up" data-aos-delay="{{ (($loop->index ?? 0) % 4) * 80 }}">

    {{-- Image --}}
    <div class="product-image-wrapper">
        <img src="{{ $product->main_image ? (Str::startsWith($product->main_image,'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}"
             alt="{{ $product->translated_name }}" loading="lazy">

        {{-- Badges --}}
        <div class="position-absolute top-0 start-0 m-3 d-flex flex-column gap-1" style="z-index:2;">
            @if($product->isOnSale())
                <span class="badge px-2 py-1 rounded-pill" style="background:#ef4444;font-size:.7rem;">-{{ $product->discount_percentage }}% {{ __('OFF') }}</span>
            @endif
            @if(!$product->isInStock())
                <span class="badge bg-dark px-2 py-1 rounded-pill" style="font-size:.7rem;">{{ __('Sold Out') }}</span>
            @endif
        </div>

        {{-- Overlay --}}
        <div class="product-card-overlay">
            <a href="{{ route('shop.show', $product->id) }}" class="btn btn-sm bg-white text-dark fw-700 rounded-pill px-3 shadow">
                <i class="fas fa-eye me-1 text-green"></i> {{ __('View') }}
            </a>
        </div>
    </div>

    {{-- Body --}}
    <div class="p-3 bg-white">
        @if($product->productCategory)
            <span class="x-small fw-700 text-green text-uppercase ls-1 d-block mb-1">{{ $product->productCategory->translated_name }}</span>
        @endif
        <a href="{{ route('shop.show', $product->id) }}" class="text-decoration-none">
            <h6 class="fw-700 text-dark lh-base mb-0" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.8rem;">{{ $product->translated_name }}</h6>
        </a>

        <div class="d-flex align-items-center gap-1 my-2">
            @for($i=0; $i<5; $i++) <i class="fas fa-star" style="font-size:.6rem;color:#f59e0b;"></i> @endfor
            <span class="x-small text-muted ms-1">({{ rand(8,52) }})</span>
        </div>

            <div class="mt-2">
                @if($product->isOnSale())
                    <div class="fw-800 text-green" style="font-size:1.05rem;">{{ $product->formatted_sale_price }}</div>
                    <div class="text-muted text-decoration-line-through x-small">{{ $product->formatted_price }}</div>
                @else
                    <div class="fw-800 text-dark" style="font-size:1.05rem;">{{ $product->formatted_price }}</div>
                @endif
            </div>
    </div>
</div>
<style>
.fw-800{font-weight:800}.x-small{font-size:.75rem}.ls-1{letter-spacing:1px}
</style>
