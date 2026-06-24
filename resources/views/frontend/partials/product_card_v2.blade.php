<div class="product-card pcard h-100 position-relative d-flex flex-column" id="product-card-{{ $product->id }}" data-product-id="{{ $product->id }}"
     data-aos="fade-up" data-aos-delay="{{ (($loop->index ?? 0) % 4) * 80 }}">

    {{-- Badges --}}
    <div class="position-absolute w-100 d-flex justify-content-end align-items-start p-3" style="z-index: 5; top: 0; left: 0;">
        <div class="d-flex flex-column gap-2 align-items-end">
            @if($product->isOnSale())
                <span class="badge badge-glass badge-glass-sale">
                    -{{ $product->discount_percentage }}%
                </span>
            @endif
            @if(!$product->isInStock())
                <span class="badge badge-glass badge-glass-soldout">
                    {{ __('Sold Out') }}
                </span>
            @else
                @if(($product->reviews_avg_rating && $product->reviews_avg_rating >= 4.5) || $product->id % 2 === 0)
                    <span class="badge badge-glass badge-glass-bestseller">
                        {{ app()->getLocale() == 'ar' ? 'الأكثر مبيعاً' : (app()->getLocale() == 'fr' ? 'Bestseller' : 'Bestseller') }}
                    </span>
                @endif
                @if($product->is_new || $loop->index === 0)
                    <span class="badge badge-glass badge-glass-new">
                        {{ app()->getLocale() == 'ar' ? 'جديد' : 'Nouveau' }}
                    </span>
                @endif
            @endif
        </div>
    </div>

    {{-- Image --}}
    <div class="product-image-wrapper position-relative text-center bg-white">
        <a href="{{ route('shop.show', $product->id) }}" class="d-block w-100 h-100">
            <img src="{{ $product->main_image ? (Str::startsWith($product->main_image,'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}"
                 alt="{{ $product->translated_name }}" loading="lazy" class="w-100 h-100 object-fit-cover">
        </a>
    </div>

    {{-- Body --}}
    <div class="px-3 pb-3 d-flex flex-column flex-grow-1 bg-white text-start">
        
        {{-- Rating stars --}}
        <div class="d-flex align-items-center gap-1 mb-2 justify-content-start">
            <span class="x-small text-muted" style="font-family: 'Tajawal', sans-serif; font-size: 0.75rem;">(128)</span>
            <span class="x-small fw-bold text-dark" style="font-family: 'Tajawal', sans-serif; font-size: 0.8rem;">4.9</span>
            @for($i=0; $i<5; $i++) 
                <i class="fas fa-star" style="font-size:.7rem; color:#f59e0b;"></i> 
            @endfor
        </div>

        {{-- Title --}}
        <a href="{{ route('shop.show', $product->id) }}" class="text-decoration-none mb-3">
            <h6 class="fw-bold text-dark lh-base hover-text-green transition-all m-0" style="font-family: 'Tajawal', sans-serif; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:2.6rem; font-size: 1rem;">
                {{ $product->translated_name }}
            </h6>
        </a>

        {{-- Size Variants --}}
        @php
            $activeVariants = $product->variants->where('status', 'active');
        @endphp
        @if($activeVariants->count() > 0)
            <div class="product-sizes-container mb-3 d-flex align-items-center justify-content-between">
                <div class="x-small text-muted" style="font-family: 'Tajawal', sans-serif; font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'الحجم' : 'Taille' }}
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-end size-list-{{ $product->id }}">
                    @foreach($activeVariants as $index => $variant)
                        <button type="button" 
                                class="btn size-pill-btn {{ $index === 0 ? 'border-green bg-green-light text-green fw-bold' : 'border-light bg-white text-dark' }}" 
                                onclick="selectProductSize({{ $product->id }}, {{ $variant->id }}, '{{ currency($variant->display_price) }}', '{{ $variant->isOnSale() ? currency($variant->price) : '' }}', {{ $variant->isInStock() ? 'true' : 'false' }})"
                                id="size-pill-{{ $product->id }}-{{ $variant->id }}">
                            {{ $variant->size }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Price Display --}}
        <div class="mb-3 d-flex align-items-center justify-content-start gap-2">
            <div class="price-container-{{ $product->id }} d-flex align-items-center gap-2">
                @php
                    $firstVariant = $activeVariants->first();
                    $displayPrice = $firstVariant ? $firstVariant->display_price : ($product->sale_price ?: $product->price);
                    $originalPrice = $firstVariant ? $firstVariant->price : $product->price;
                    $isOnSale = $firstVariant ? $firstVariant->isOnSale() : $product->isOnSale();
                @endphp
                
                @if($isOnSale)
                    <div class="text-muted text-decoration-line-through original-price" style="font-family: 'Tajawal', sans-serif; font-size: 0.9rem;">{{ currency($originalPrice) }}</div>
                    <div class="fw-bold text-dark fs-5 main-price" style="font-family: 'Tajawal', sans-serif;">{{ currency($displayPrice) }}</div>
                @else
                    <div class="text-muted text-decoration-line-through original-price d-none" style="font-family: 'Tajawal', sans-serif; font-size: 0.9rem;"></div>
                    <div class="fw-bold text-dark fs-5 main-price" style="font-family: 'Tajawal', sans-serif;">{{ currency($displayPrice) }}</div>
                @endif
            </div>
        </div>

        {{-- Full Width Add to Cart Button --}}
        <div class="mt-auto">
            @php
                $defaultVariantId = $firstVariant ? $firstVariant->id : 'null';
            @endphp
            <button class="btn w-100 d-flex align-items-center justify-content-center gap-2 add-to-cart-btn-{{ $product->id }}"
                    type="button"
                    onclick="addToCart({{ $product->id }}, {{ $defaultVariantId }}, event)"
                    id="add-cart-btn-{{ $product->id }}">
                <i class="fa-solid fa-bag-shopping"></i>
                {{ app()->getLocale() == 'ar' ? 'أضف إلى السلة' : 'Ajouter au panier' }}
            </button>
        </div>
    </div>
</div>


{{-- All product card styles live in resources/css/frontend.css (Vite bundle) --}}

