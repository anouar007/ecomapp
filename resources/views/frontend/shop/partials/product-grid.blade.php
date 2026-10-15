{{-- Meta Bridge for Dynamic AJAX Count & State Synchronization --}}
<div id="shopMetaBridge" class="d-none" 
     data-total="{{ $products->total() }}" 
     data-category-title="{{ $activeCategoryTitle ?? ($categories->where('slug', request('category'))->first()->name ?? ($products->total() . ' ' . __('produits'))) }}"
     data-category-slug="{{ request('category', '') }}"
     data-current-page="{{ $products->currentPage() }}"
     data-last-page="{{ $products->lastPage() }}"
     data-has-pages="{{ $products->hasPages() ? '1' : '0' }}"></div>

<div class="row g-2 home-products-grid" id="shopProductRow">
    @forelse($products as $product)
    @php
        $inCart = isset(session('cart', [])[$product->id]);
        $isAr = app()->getLocale() === 'ar';
    @endphp
    <div class="col-6 col-sm-6 col-md-4 col-xl-4 col-xxl-3 product-col">
        <div class="featured-pro-card selection-card h-100 d-flex flex-column justify-content-between position-relative">
            {{-- Stock Status Badge at Top Left of Card --}}
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

            {{-- Product Image with clean white background --}}
            <div class="featured-pro-img-box mb-2">
                <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center" aria-label="{{ $product->name }}">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/hero_cinema_rig.jpg') }}';">
                </a>
            </div>

            {{-- Product Info --}}
            <div class="featured-pro-body mb-1 flex-grow-1 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="featured-pro-title">
                        <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" title="{{ $product->name }}">
                            {{ $product->name }}
                        </a>
                    </h3>
                </div>

                <div>
                    {{-- Price --}}
                    <div class="pcard-price mb-0">
                        @if($product->isOnSale())
                            <span class="pcard-price-current text-nowrap">{{ $product->formatted_sale_price }}</span>
                            <span class="pcard-price-old text-nowrap">{{ $product->formatted_price }}</span>
                        @elseif($product->price && $product->price > 0)
                            <span class="pcard-price-current text-nowrap">{{ $product->formatted_price }}</span>
                            <span class="pcard-price-old text-nowrap invisible" aria-hidden="true">&nbsp;</span>
                        @else
                            <span class="pcard-price-current text-nowrap">{{ __('Sur devis') }}</span>
                            <span class="pcard-price-old text-nowrap invisible" aria-hidden="true">&nbsp;</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Full-width Action Button --}}
            <div class="featured-pro-action mt-auto pt-0">
                @if($product->isInStock())
                    <button type="button" 
                            class="btn w-100 featured-pro-btn {{ $inCart ? 'is-in-cart' : '' }}" 
                            onclick="addToCart({{ $product->id }}, 1, this)"
                            data-product-id="{{ $product->id }}"
                            title="{{ $inCart ? ($isAr ? 'في السلة' : 'Dans le panier') : ($isAr ? 'أضف إلى السلة' : 'Ajouter au panier') }}">
                        @if($inCart)
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
    </div>
    @empty
    <div class="col-12">
        <div class="shop-empty text-center py-5 px-3 rounded-4 bg-white border shadow-sm">
            <div class="shop-empty-icon-wrap mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger" style="width: 72px; height: 72px; font-size: 1.8rem;">
                <i class="fas fa-search"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">{{ __('Aucun produit ne correspond à vos critères') }}</h5>
            <p class="text-muted small mb-4 mx-auto" style="max-width: 420px;">{{ __('Essayez d\'élargir vos filtres de prix, de rechercher un autre modèle ou de consulter tous les rayons du catalogue.') }}</p>
            <button type="button" onclick="if(window.resetAllFilters) { window.resetAllFilters(); }" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold d-inline-flex gap-2 align-items-center shadow-sm">
                <i class="fas fa-redo"></i> <span>{{ __('Réinitialiser les filtres') }}</span>
            </button>
        </div>
    </div>
    @endforelse
</div>

@if($products->hasPages())
<div class="mt-4 mt-md-5 d-flex justify-content-center shop-pagination">
    {{ $products->links() }}
</div>
@endif
