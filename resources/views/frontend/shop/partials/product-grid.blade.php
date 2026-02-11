<div class="row g-4">
    @forelse($products as $product)
    <div class="col-6 col-md-4">
        <div class="product-card-elegant">
            <div class="product-image">
                <a href="{{ route('shop.show', $product->id) }}">
                    @if($product->main_image)
                    <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}">
                    @else
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; background: var(--bg-warm);">
                        <i class="fas fa-image fa-3x opacity-25"></i>
                    </div>
                    @endif
                </a>
                
                <div class="product-badges">
                    @if(!$product->isInStock())
                        <span class="product-badge" style="background: #dc2626;">Out of Stock</span>
                    @elseif($product->created_at->diffInDays(now()) < 7)
                        <span class="product-badge product-badge-new">New</span>
                    @elseif($product->isOnSale())
                        <span class="product-badge product-badge-sale">{{ $product->discount_percentage }}% Off</span>
                    @endif
                </div>
                
                <div class="product-actions">
                    @if($product->isInStock())
                    <button class="product-action-btn" onclick="addToCart({{ $product->id }})" title="Add to Cart">
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                    @else
                    <button class="product-action-btn" disabled title="Out of Stock" style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                    @endif
                    <a href="{{ route('shop.show', $product->id) }}" class="product-action-btn" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            
            <div class="product-info">
                @if($product->category_name)
                <div class="product-category">{{ $product->category_name }}</div>
                @endif
                <h4 class="product-title">
                    <a href="{{ route('shop.show', $product->id) }}" class="text-decoration-none" style="color: inherit;">
                        {{ Str::limit($product->name, 45) }}
                    </a>
                </h4>
                <div class="product-rating">
                    <div class="stars">
                        @if($product->reviews_count > 0)
                            @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star{{ $i < round($product->reviews_avg_rating) ? '' : ' opacity-25' }}"></i>
                            @endfor
                        @else
                            @for($i = 0; $i < 5; $i++)
                                <i class="far fa-star"></i>
                            @endfor
                        @endif
                    </div>
                    <span>({{ $product->reviews_count ?? 0 }})</span>
                </div>
                <div class="product-price">
                    @if($product->isOnSale())
                        <span class="price-current">{{ $product->formatted_sale_price }}</span>
                        <span class="price-original">{{ $product->formatted_price }}</span>
                    @else
                        <span class="price-current">{{ $product->formatted_price }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="mb-4">
            <i class="fas fa-box-open fa-4x" style="color: var(--border-light);"></i>
        </div>
        <h5 style="color: var(--text-dark);">No products found</h5>
        <p class="text-muted mb-4">Try adjusting your filters or search query.</p>
        <a href="{{ route('shop.index') }}" class="btn-elegant btn-elegant-dark">Clear All Filters</a>
    </div>
    @endforelse
</div>

@if($products->hasPages())
<div class="mt-5 d-flex justify-content-center">
    {{ $products->links() }}
</div>
@endif
