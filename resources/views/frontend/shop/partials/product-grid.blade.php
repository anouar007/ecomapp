<div class="row g-2 g-sm-3 g-md-4" id="shopProductRow">
    @forelse($products as $product)
    <div class="col-6 col-md-4 product-col">
        <div class="pcard h-100 d-flex flex-column">
            {{-- Image --}}
            <div class="pcard-img">
                <a href="{{ route('shop.show', $product->id) }}" class="pcard-img-link">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                </a>

                {{-- Badges --}}
                <div class="pcard-badges">
                    @if(!$product->isInStock())
                        <span class="pcard-badge pcard-badge--oos">{{ __('Out of Stock (badge)') }}</span>
                    @elseif($product->created_at->diffInDays(now()) < 14)
                        <span class="pcard-badge pcard-badge--new">{{ __('New') }}</span>
                    @elseif($product->isOnSale())
                        <span class="pcard-badge pcard-badge--sale">−{{ $product->discount_percentage }}%</span>
                    @endif
                </div>

                {{-- Desktop Hover overlay actions --}}
                <div class="pcard-overlay d-none d-md-flex">
                    @if($product->isInStock())
                    <button class="pcard-overlay-btn" onclick="addToCart({{ $product->id }})" title="{{ __('Add to cart') }}">
                        <i class="fas fa-cart-plus"></i> {{ __('Add') }}
                    </button>
                    @endif
                    <a href="{{ route('shop.show', $product->id) }}" class="pcard-overlay-btn pcard-overlay-btn--ghost" title="{{ __('View Product') }}">
                        <i class="fas fa-eye"></i> {{ __('Details') }}
                    </a>
                </div>
            </div>

            {{-- Info --}}
            <div class="pcard-body d-flex flex-column flex-grow-1">
                @if($product->category_name)
                <div class="pcard-cat text-truncate">{{ $product->category_name }}</div>
                @endif
                <h4 class="pcard-name">
                    <a href="{{ route('shop.show', $product->id) }}" title="{{ $product->name }}">{{ $product->name }}</a>
                </h4>
                <div class="pcard-rating">
                    <div class="pcard-stars">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fa{{ $i < round($product->reviews_avg_rating ?? 0) ? 's' : 'r' }} fa-star"></i>
                        @endfor
                    </div>
                    <span class="pcard-reviews">({{ $product->reviews_count ?? 0 }})</span>
                </div>
                
                {{-- Price & Quick Cart Button --}}
                <div class="pcard-bottom-row mt-auto">
                    <div class="pcard-price">
                        @if($product->isOnSale())
                            <span class="pcard-price-current text-nowrap">{{ $product->formatted_sale_price }}</span>
                            <span class="pcard-price-old text-nowrap">{{ $product->formatted_price }}</span>
                        @else
                            <span class="pcard-price-current text-nowrap">{{ $product->formatted_price }}</span>
                        @endif
                    </div>

                    @if($product->isInStock())
                    <button class="pcard-quick-cart-btn" onclick="addToCart({{ $product->id }})" title="{{ __('Add to cart') }}" aria-label="{{ __('Add to cart') }}">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="pcard-quick-cart-label d-none">{{ __('Add to cart') }}</span>
                    </button>
                    @else
                    <span class="pcard-quick-out" title="{{ __('Out of stock') }}">
                        <i class="fas fa-ban"></i>
                        <span class="pcard-quick-out-label d-none">{{ __('Out of stock') }}</span>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="shop-empty">
            <i class="fas fa-camera shop-empty-icon"></i>
            <h5>{{ __('No product found') }}</h5>
            <p>{{ __('Modify your filters or search to see more results.') }}</p>
            <a href="{{ route('shop.index') }}" class="shop-apply-btn d-inline-flex gap-2 align-items-center">
                <i class="fas fa-redo"></i> {{ __('Reset filters') }}
            </a>
        </div>
    </div>
    @endforelse
</div>

@if($products->hasPages())
<div class="mt-5 d-flex justify-content-center shop-pagination">
    {{ $products->links() }}
</div>
@endif
