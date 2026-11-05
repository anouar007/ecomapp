@php
    $isInCart = isset(session('cart', [])[$product->id]);
    $isAr = app()->getLocale() === 'ar';
    $cardTags = $product->relationLoaded('activeTags') ? $product->activeTags : $product->activeTags()->get();
    $tagsByPosition = $cardTags->groupBy(fn($t) => $t->position ?: 'top-left');
    $autoDiscountTag = \App\Models\ProductTag::discountTag();
    $hasAutoDiscount = $product->isOnSale() && $product->discount_percentage > 0 && $autoDiscountTag->status === 'active';
    $prefDiscountPos = $autoDiscountTag->position ?: 'top-left';
    $discountCorner = $tagsByPosition->has($prefDiscountPos) ? $prefDiscountPos : ($tagsByPosition->keys()->first() ?? $prefDiscountPos);
    $cardUrl = route('shop.show', $product->slug ?: $product->id);
    $cardCompactKeywords = method_exists($product, 'getCompactSeoKeywordsString') ? $product->getCompactSeoKeywordsString(14) : $product->name;
    $cardAltTitle = $product->name . ' — Prix Maroc WINA SHOP | شراء في المغرب | Buy in Morocco';
@endphp
<div class="featured-pro-card selection-card product-card-tag-container h-100 d-flex flex-column justify-content-between position-relative" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="name" content="{{ $product->name }}">
    <meta itemprop="sku" content="{{ $product->sku ?: ('WINA-' . $product->id) }}">
    <meta itemprop="keywords" content="{{ $cardCompactKeywords }}">
    <link itemprop="url" href="{{ $cardUrl }}">
    <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="d-none">
        <meta itemprop="url" content="{{ $cardUrl }}">
        <meta itemprop="priceCurrency" content="{{ setting('currency_code', 'MAD') }}">
        <meta itemprop="price" content="{{ number_format((float) $product->final_price, 2, '.', '') }}">
        <link itemprop="availability" href="{{ $product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}">
        <link itemprop="itemCondition" href="{{ str_contains(strtolower($product->name), 'occasion') ? 'https://schema.org/UsedCondition' : 'https://schema.org/NewCondition' }}">
    </div>
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

        <a href="{{ $cardUrl }}" class="w-100 h-100 d-flex align-items-center justify-content-center" aria-label="{{ $product->name }}" title="{{ $cardAltTitle }}">
            <img itemprop="image" src="{{ $product->thumbnail }}" alt="{{ $cardAltTitle }}" width="220" height="220" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';" @if($product->primary_image_padding_style) style="padding: {{ $product->primary_image_padding_style }} !important;" @endif>
        </a>
    </div>

    {{-- Product Info --}}
    <div class="featured-pro-body mb-1 flex-grow-1 d-flex flex-column justify-content-between">
        <div>
            <h3 class="featured-pro-title">
                <a href="{{ $cardUrl }}" title="{{ $cardAltTitle }}">
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
                    @if($product->isComingSoon())
                        <span class="featured-stock-pill featured-stock-pill--coming-soon featured-stock-badge-corner"
                              @if($product->custom_tag_style) style="{{ $product->custom_tag_style }}" @endif
                              title="{{ $product->stock_tag_label }}">
                            <i class="fas fa-clock" aria-hidden="true" @if(!empty($product->custom_tag_color)) style="color: {{ $product->custom_tag_color }} !important;" @endif></i><span class="featured-stock-label">{{ $product->stock_tag_label }}</span>
                        </span>
                    @elseif($product->isInStock())
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
        @if($product->isComingSoon())
            <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" 
               class="btn w-100 featured-pro-btn featured-pro-btn--gold" 
               @if($product->custom_button_style) style="{{ $product->custom_button_style }}" @endif
               title="{{ $product->stock_button_label }}">
                <i class="fas fa-clock" style="font-size: 11px; @if(!empty($product->custom_btn_color)) color: {{ $product->custom_btn_color }} !important; @endif"></i>
                <span>{{ $product->stock_button_label }}</span>
            </a>
        @elseif($product->isInStock())
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
