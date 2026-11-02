@if(isset($isGroupedByCategory) && $isGroupedByCategory && !empty($groupedCategories))
    <div class="shop-grouped-catalog">
        @foreach($groupedCategories as $group)
            @php
                $cat = $group['category'];
                $catProducts = $group['products'];
            @endphp
            @if($catProducts->count() > 0)
            <div class="shop-cat-section mb-5 pb-4 border-bottom border-light">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 pb-2 border-bottom border-light">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;">
                            <i class="fas fa-tag me-1"></i> {{ $cat->name }}
                        </span>
                        <span class="text-muted small fw-semibold">({{ $cat->products_count }} {{ __('refs.') }})</span>
                    </div>
                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" 
                       class="btn-link-arrow category-filter text-decoration-none small fw-bold" 
                       data-slug="{{ $cat->slug }}">
                        <span>{{ __('Voir tous les :name', ['name' => strtolower($cat->name)]) }} ({{ $cat->products_count }})</span>
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} ms-1"></i>
                    </a>
                </div>

                <div class="row g-2 g-sm-3 g-md-4">
                    @foreach($catProducts as $product)
                    <div class="col-6 col-md-4 product-col">
                        <div class="pcard h-100 d-flex flex-column">
                            {{-- Image --}}
                            <div class="pcard-img">
                                <a href="{{ route('shop.show', $product->id) }}" class="pcard-img-link">
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                </a>

                                {{-- Badges --}}
                                <div class="pcard-badges">
                                    @if(!$product->isInStock())
                                        <span class="pcard-badge pcard-badge--oos">{{ __('Out of Stock (badge)') }}</span>
                                    @elseif($product->created_at && $product->created_at->diffInDays(now()) < 14)
                                        <span class="pcard-badge pcard-badge--new">{{ __('New') }}</span>
                                    @elseif($product->isOnSale())
                                        <span class="pcard-badge pcard-badge--sale">−{{ $product->discount_percentage }}%</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="pcard-body d-flex flex-column flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="pcard-cat text-truncate mb-0">{{ $cat->name }}</div>
                                    <span class="pro-stock-indicator">
                                        <span class="pulse-dot"></span> {{ __('En stock') }}
                                    </span>
                                </div>
                                <h4 class="pcard-name">
                                    <a href="{{ route('shop.show', $product->id) }}" title="{{ $product->name }}">{{ $product->name }}</a>
                                </h4>
                                
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
                                    </button>
                                    @else
                                    <span class="pcard-quick-out" title="{{ __('Out of stock') }}">
                                        <i class="fas fa-ban"></i>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>
@else
<div class="row g-2 g-sm-3 g-md-4" id="shopProductRow">
    @forelse($products as $product)
    <div class="col-6 col-md-4 product-col">
        <div class="pcard h-100 d-flex flex-column">
            {{-- Image --}}
            <div class="pcard-img">
                <a href="{{ route('shop.show', $product->id) }}" class="pcard-img-link">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                </a>

                {{-- Badges --}}
                <div class="pcard-badges">
                    @if(!$product->isInStock())
                        <span class="pcard-badge pcard-badge--oos">{{ __('Out of Stock (badge)') }}</span>
                    @elseif($product->created_at && $product->created_at->diffInDays(now()) < 14)
                        <span class="pcard-badge pcard-badge--new">{{ __('New') }}</span>
                    @elseif($product->isOnSale())
                        <span class="pcard-badge pcard-badge--sale">−{{ $product->discount_percentage }}%</span>
                    @endif
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
@endif
