@php
    $isInCart = isset(session('cart', [])[$product->id]);
    $isAr = app()->getLocale() === 'ar';
    $cardTags = $product->relationLoaded('activeTags') ? $product->activeTags : $product->activeTags()->get();
    $tagsByPosition = $cardTags->groupBy(fn($t) => $t->position ?: 'top-left');
    $autoDiscountTag = \App\Models\ProductTag::discountTag();
    $hasAutoDiscount = $product->isOnSale() && $product->discount_percentage > 0 && $autoDiscountTag->status === 'active';
    $prefDiscountPos = $autoDiscountTag->position ?: 'top-left';
    $discountCorner = $tagsByPosition->has($prefDiscountPos) ? $prefDiscountPos : ($tagsByPosition->keys()->first() ?? $prefDiscountPos);
@endphp
<div class="featured-pro-card selection-card product-card-tag-container h-100 d-flex flex-column justify-content-between position-relative">
    {{-- Product Image with clean white background --}}
    <div class="featured-pro-img-box mb-2 position-relative">
        @foreach(['top-left', 'top-right', 'bottom-left', 'bottom-right'] as $cornerPos)
            @php
                $cornerTags = $tagsByPosition->get($cornerPos, collect());
                $showDiscountHere = $hasAutoDiscount && $discountCorner === $cornerPos;
            @endphp
            @if($cornerTags->isNotEmpty() || $showDiscountHere)
                <div class="product-card-tags-slot pos-{{ $cornerPos }}">
                    @foreach($cornerTags as $cTag)
                        <span class="product-card-tag-badge {{ $cTag->pulse_animation ? 'tag-pulse' : '' }}" style="{{ $cTag->inline_style }}">
                            @if(!empty($cTag->icon))
                                <i class="{{ $cTag->icon }}" aria-hidden="true"></i>
                            @endif
                            <span>{{ $cTag->name }}</span>
                        </span>
                    @endforeach
                    @if($showDiscountHere)
                        <span class="product-card-tag-badge product-discount-auto-badge {{ $autoDiscountTag->pulse_animation ? 'tag-pulse' : '' }}" style="{{ $autoDiscountTag->inline_style }}">
                            @if(!empty($autoDiscountTag->icon))
                                <i class="{{ $autoDiscountTag->icon }}" aria-hidden="true"></i>
                            @endif
                            <span>{{ $autoDiscountTag->formatDiscountLabel($product->discount_percentage) }}</span>
                        </span>
                    @endif
                </div>
            @endif
        @endforeach

        <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center" aria-label="{{ $product->name }}">
            <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" width="220" height="220" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';" @if($product->image_padding_style) style="padding: {{ $product->image_padding_style }} !important;" @endif>
        </a>
    </div>

    {{-- Product Info --}}
    <div class="featured-pro-body mb-1 flex-grow-1 d-flex flex-column justify-content-between">
        <div>
            <h3 class="featured-pro-title">
                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        <div>
            {{-- Price formatted dynamically as on the shop card --}}
            <div class="pcard-price mb-0">
                <div class="pcard-price-main-line d-flex align-items-center justify-content-start gap-2 w-100">
                    @if($product->isOnSale())
                        <span class="pcard-price-current text-nowrap">{{ $product->formatted_sale_price }}</span>
                    @elseif($product->price && $product->price > 0)
                        <span class="pcard-price-current text-nowrap">{{ $product->formatted_price }}</span>
                    @else
                        <span class="pcard-price-current text-nowrap">{{ __('Sur devis') }}</span>
                    @endif

                    {{-- Stock Status Badge aligned with price --}}
                    @if($product->isInStock())
                        <span class="featured-stock-pill featured-stock-badge-corner featured-stock-pill--in">
                            <i class="fas fa-check-circle" aria-hidden="true"></i><span class="featured-stock-label">{{ __('In stock') }}</span>
                        </span>
                    @else
                        <span class="featured-stock-pill featured-stock-pill--out featured-stock-badge-corner">
                            <i class="fas fa-times-circle" aria-hidden="true"></i><span class="featured-stock-label">{{ __('Out of stock') }}</span>
                        </span>
                    @endif
                </div>

                @if($product->formatted_card_old_price)
                    <span class="pcard-price-old text-nowrap">{{ $product->formatted_card_old_price }}</span>
                @else
                    <span class="pcard-price-old text-nowrap invisible" aria-hidden="true">&nbsp;</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Full-width Action Button --}}
    <div class="featured-pro-action mt-auto pt-0">
        @if($product->isInStock())
            <button type="button" 
                    class="btn w-100 featured-pro-btn {{ $isInCart ? 'is-in-cart' : '' }}" 
                    onclick="addToCart({{ $product->id }}, 1, this)"
                    data-product-id="{{ $product->id }}"
                    aria-label="{{ $isInCart ? ($isAr ? 'في السلة' : 'Dans le panier') : ($isAr ? 'أضف إلى السلة' : 'Ajouter au panier') }}"
                    title="{{ $isInCart ? ($isAr ? 'في السلة' : 'Dans le panier') : ($isAr ? 'أضف إلى السلة' : 'Ajouter au panier') }}">
                @if($isInCart)
                    <i class="fas fa-check" style="font-size: 11px;"></i>
                    <span>{{ $isAr ? 'في السلة' : 'Dans le panier' }}</span>
                @else
                    <span>{{ $isAr ? 'أضف إلى السلة' : 'Ajouter au panier' }}</span>
                @endif
            </button>
        @else
            <button type="button" 
                    class="btn w-100 featured-pro-btn featured-pro-btn--out" 
                    disabled 
                    title="{{ $isAr ? 'نفذت الكمية' : 'Rupture' }}">
                <i class="fas fa-ban" style="font-size: 11px;"></i>
                <span>{{ $isAr ? 'نفذت الكمية' : 'Rupture' }}</span>
            </button>
        @endif
    </div>
</div>
