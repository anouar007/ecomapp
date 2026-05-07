<div class="product-card h-100 card-dangila group" data-product-id="{{ $product->id }}"
     data-aos="fade-up" data-aos-delay="{{ (($loop->index ?? 0) % 4) * 100 }}">

    {{-- Product Image Container --}}
    <a href="{{ route('shop.show', $product->id) }}" class="d-block position-relative overflow-hidden rounded-5 mb-3" style="background: var(--color-bg);">
        <div style="padding-top: 110%; position: relative;">
            <img src="{{ $product->main_image ? (Str::startsWith($product->main_image,'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}"
                 class="position-absolute top-0 start-0 w-100 h-100 object-fit-contain transition-all duration-1000 group-hover:scale-105" alt="{{ $product->translated_name }}" loading="lazy">
        </div>
        
        {{-- Sale Badge --}}
        @if($product->isOnSale())
            <div class="position-absolute top-0 end-0 p-3">
                <span class="badge-dangila">{{ __('Sale') }}</span>
            </div>
        @endif
    </a>

    {{-- Product Body --}}
    <div class="px-3 pb-3">
        @if($product->productCategory)
            <span class="text-dim x-small text-uppercase fw-bold d-block mb-1">{{ $product->productCategory->translated_name }}</span>
        @endif
        
        <a href="{{ route('shop.show', $product->id) }}" class="text-decoration-none">
            <h6 class="dangila-heading text-black fs-5 mb-2 transition-all hover-text-primary">{{ $product->translated_name }}</h6>
        </a>

        <div class="d-flex align-items-center gap-2">
            @if($product->isOnSale())
                <span class="text-primary fw-bold fs-5">{{ $product->formatted_sale_price }}</span>
                <span class="text-dim text-decoration-line-through small">{{ $product->formatted_price }}</span>
            @else
                <span class="text-primary fw-bold fs-5">{{ $product->formatted_price }}</span>
            @endif
        </div>
    </div>
</div>

<style>
    .hover-text-primary:hover { color: var(--color-primary) !important; }
</style>
