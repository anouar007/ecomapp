@php
    $isInCart = isset(session('cart', [])[$product->id]);
    $isAr = app()->getLocale() === 'ar';
@endphp
<div class="featured-pro-card selection-card h-100 d-flex flex-column justify-content-between position-relative">
    {{-- Product Image with clean white background --}}
    <div class="featured-pro-img-box mb-2">
        <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
            <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
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
                <div class="pcard-price-main-line d-flex align-items-center justify-content-around gap-1 w-100">
                    @if($product->isOnSale())
                        <span class="pcard-price-current text-nowrap">{{ $product->formatted_sale_price }}</span>
                    @elseif($product->price && $product->price > 0)
                        <span class="pcard-price-current text-nowrap">{{ $product->formatted_price }}</span>
                    @else
                        <span class="pcard-price-current text-nowrap">{{ __('Sur devis') }}</span>
                    @endif

                    {{-- Stock Status Badge aligned with price --}}
                    @if($product->isInStock())
                        <span class="featured-stock-pill featured-stock-badge-corner">
                            <span class="stock-pulse-dot"></span>
                            <span>{{ $isAr ? 'متوفر' : 'EN STOCK' }}</span>
                        </span>
                    @else
                        <span class="featured-stock-pill featured-stock-pill--out featured-stock-badge-corner">
                            <i class="fas fa-times-circle text-danger" style="font-size: 10px;"></i>
                            <span>{{ $isAr ? 'نفذت الكمية' : 'RUPTURE DE STOCK' }}</span>
                        </span>
                    @endif
                </div>

                @if($product->isOnSale())
                    <span class="pcard-price-old text-nowrap">{{ $product->formatted_price }}</span>
                @elseif($product->price && $product->price > 0)
                    <span class="pcard-price-old text-nowrap invisible" aria-hidden="true">&nbsp;</span>
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
                    title="{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}">
                <i class="fas fa-ban" style="font-size: 11px;"></i>
                <span>{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}</span>
            </button>
        @endif
    </div>
</div>
