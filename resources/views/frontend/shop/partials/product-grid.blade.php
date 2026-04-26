<div class="row g-3 g-md-4">
@forelse($products as $product)
    <div class="col-6 col-md-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 6) * 60 }}">
        <div class="pc">
            <a href="{{ route('shop.show', $product->id) }}" class="text-decoration-none">
                <div class="pc-img">
                    <img src="{{ $product->main_image ? (Str::startsWith($product->main_image,'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}"
                         alt="{{ $product->translated_name }}" loading="lazy">
                    <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1" style="z-index:2">
                        @if($product->isOnSale())
                            <span class="badge rounded-pill px-2 py-1" style="background:#ef4444;font-size:.62rem;font-weight:700">-{{ $product->discount_percentage }}%</span>
                        @endif
                        @if(!$product->isInStock())
                            <span class="badge rounded-pill bg-dark px-2 py-1" style="font-size:.62rem">{{ __('Sold Out') }}</span>
                        @endif
                    </div>
                    <div class="pc-overlay">
                        <a href="{{ route('shop.show', $product->id) }}" class="pc-action">
                            <i class="fas fa-eye me-1"></i>{{ __('View Product') }}
                        </a>
                    </div>
                </div>
            </a>
            <div class="pc-body">
                <div class="pc-cat">{{ $product->productCategory?->translated_name ?? __('Natural') }}</div>
                <a href="{{ route('shop.show', $product->id) }}" class="pc-name d-block">{{ $product->translated_name }}</a>
                <div class="pc-stars my-2">
                    @for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor
                    <span class="text-muted" style="font-size:.68rem"> ({{ $product->reviews_count ?? rand(5,30) }})</span>
                </div>
                <div class="mt-2">
                    @if($product->isOnSale())
                        <div class="pc-sale">{{ $product->formatted_sale_price }}</div>
                        <div class="pc-old">{{ $product->formatted_price }}</div>
                    @else
                        <div class="pc-price">{{ $product->formatted_price }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="empty-box">
            <div class="empty-ico">🔍</div>
            <h5 class="fw-800 text-dark mb-2">{{ __('No products found') }}</h5>
            <p class="text-muted small mb-4">{{ __('Try a different search or clear your filters.') }}</p>
            <a href="{{ route('shop.index') }}" class="btn-brand btn-brand-primary px-4 py-2 text-decoration-none">
                <i class="fas fa-undo me-1"></i>{{ __('View All Products') }}
            </a>
        </div>
    </div>
@endforelse
</div>

@if($products->hasPages())
<div class="mt-5 d-flex justify-content-center shop-pag">
    {{ $products->links() }}
</div>
@endif
