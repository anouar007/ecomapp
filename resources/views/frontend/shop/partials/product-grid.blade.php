{{-- We embed a hidden span to let AJAX know the total count --}}
<span id="product-count-metadata" data-total-count="{{ $products->total() }}" class="d-none"></span>

<div class="row g-4">
    @forelse($products as $product)
    <div class="col-6 col-md-4" data-aos="fade-up">
        <div class="pcard-v2 {{ $product->isOutOfStock() ? 'pcard-v2--oos' : '' }}">
            {{-- Image / Media Section --}}
            <div class="pcard-media">
                <a href="{{ route('shop.show', $product->id) }}">
                    @php
                        $mainThumb = $product->main_image ? Storage::url($product->main_image) : asset('images/placeholder-product.jpg');
                        $hoverImage = $product->images->count() > 1 ? Storage::url($product->images[1]->image_path) : $mainThumb;
                    @endphp
                    <img src="{{ $mainThumb }}" alt="{{ $product->translated_name }}" class="pcard-img-main" loading="lazy">
                    <img src="{{ $hoverImage }}" alt="{{ $product->translated_name }} Hover" class="pcard-img-hover" loading="lazy">
                </a>


                {{-- Status Badges --}}
                <div class="pcard-badges">
                    @if(!$product->isInStock())
                        <span class="pbadge pbadge-oos">{{ __('Out of Stock') }}</span>
                    @elseif($product->created_at->diffInDays(now()) < 14)
                        <span class="pbadge pbadge-new">{{ __('New') }}</span>
                    @elseif($product->isOnSale())
                        <span class="pbadge pbadge-sale">−{{ $product->discount_percentage }}%</span>
                    @endif
                </div>
            </div>

            {{-- Content Section (Editorial Style) --}}
            <div class="pcard-info">
                @if($product->productCategory)
                    <div class="pcard-tag">
                        {{ $product->productCategory->translated_name }}
                    </div>
                @endif
                
                <h4 class="pcard-name-creative">
                    <a href="{{ route('shop.show', $product->id) }}">
                        {{ $product->translated_name }}
                    </a>
                </h4>

                <div class="pcard-meta-row">
                    @if($product->isOnSale())
                        <span class="price-val price-val--sale">{{ $product->formatted_sale_price }}</span>
                        <span class="price-val price-val--old">{{ $product->formatted_price }}</span>
                    @else
                        <span class="price-val">{{ $product->formatted_price }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 py-5 text-center" data-aos="fade-in">
        <div class="bg-white p-5 rounded-4 shadow-sm">
            <i class="fas fa-search-minus fa-4x text-muted mb-4 opacity-25"></i>
            <h5 class="fw-black text-uppercase ls-1">{{ __('No matching products') }}</h5>
            <p class="text-muted">{{ __('Try refining your filters or search terms.') }}</p>
            <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-5 py-3 mt-3 fw-bold text-uppercase ls-1 shadow">
                {{ __('Reset All Filters') }}
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
