{{-- Meta Bridge for Dynamic AJAX Count & State Synchronization --}}
<div id="shopMetaBridge" class="d-none" 
     data-total="{{ $products->total() }}" 
     data-category-title="{{ $activeCategoryTitle ?? ($categories->where('slug', request('category'))->first()->name ?? ($products->total() . ' ' . __('produits'))) }}"
     data-category-slug="{{ request('category', '') }}"
     data-current-page="{{ $products->currentPage() }}"
     data-last-page="{{ $products->lastPage() }}"
     data-has-pages="{{ $products->hasPages() ? '1' : '0' }}"></div>

<div class="row g-2 g-sm-3 g-md-4" id="shopProductRow">
    @forelse($products as $product)
    <div class="col-6 col-sm-6 col-md-4 col-xl-4 col-xxl-3 product-col">
        <div class="pcard h-100 d-flex flex-column position-relative">
            {{-- Image & Badges --}}
            <div class="pcard-img position-relative overflow-hidden">
                <a href="{{ route('shop.show', $product->id) }}" class="pcard-img-link d-block w-100 h-100" aria-label="{{ $product->name }}">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/hero_cinema_rig.jpg') }}';">
                </a>

                {{-- Badges --}}
                <div class="pcard-badges">
                    @if(!$product->isInStock())
                        <span class="pcard-badge pcard-badge--oos">{{ __('Épuisé') }}</span>
                    @elseif($product->created_at && $product->created_at->diffInDays(now()) < 14)
                        <span class="pcard-badge pcard-badge--new">{{ __('Nouveau') }}</span>
                    @elseif($product->isOnSale())
                        <span class="pcard-badge pcard-badge--sale">−{{ $product->discount_percentage }}%</span>
                    @endif
                </div>

                {{-- Desktop Hover overlay actions --}}
                @php $inCart = isset(session('cart', [])[$product->id]); @endphp
                <div class="pcard-overlay d-none d-md-flex">
                    @if($product->isInStock())
                    <button type="button" class="pcard-overlay-btn {{ $inCart ? 'is-in-cart' : '' }}" data-product-id="{{ $product->id }}" onclick="addToCart({{ $product->id }}, 1, this)" title="{{ $inCart ? __('Dans le panier') : __('Ajouter au panier') }}">
                        <i class="fas {{ $inCart ? 'fa-check' : 'fa-cart-plus' }}"></i> <span>{{ $inCart ? __('Ajouté') : __('Ajouter') }}</span>
                    </button>
                    @endif
                    <a href="{{ route('shop.show', $product->id) }}" class="pcard-overlay-btn pcard-overlay-btn--ghost" title="{{ __('Voir les détails') }}">
                        <i class="fas fa-eye"></i> <span>{{ __('Détails') }}</span>
                    </a>
                </div>
            </div>

            {{-- Info Body --}}
            <div class="pcard-body d-flex flex-column flex-grow-1">
                @if($product->category_name)
                    @php
                        $catSlug = $product->category_slug ?? optional($product->category)->slug ?? optional($product->productCategory)->slug ?? '';
                    @endphp
                    <a href="{{ route('shop.index', ['category' => $catSlug]) }}" 
                       class="pcard-cat text-truncate text-decoration-none d-block shop-filter-cat-link" 
                       data-slug="{{ $catSlug }}"
                       title="{{ $product->category_name }}">
                        {{ $product->category_name }}
                    </a>
                @else
                    <span class="pcard-cat pcard-cat--placeholder d-block" style="visibility: hidden;" aria-hidden="true">&nbsp;</span>
                @endif

                <h4 class="pcard-name">
                    <a href="{{ route('shop.show', $product->id) }}" title="{{ $product->name }}">{{ $product->name }}</a>
                </h4>

                <div class="pcard-rating">
                    @php $reviewsCount = (int)($product->reviews_count ?? 0); @endphp
                    @if($reviewsCount > 0)
                        <div class="pcard-stars">
                            @php $rating = round($product->reviews_avg_rating ?? 0); @endphp
                            @for($i = 0; $i < 5; $i++)
                                <i class="fa{{ $i < $rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                        <span class="pcard-reviews">({{ $reviewsCount }})</span>
                    @else
                        <span class="pcard-trust-pill">
                            <i class="fas fa-shield-alt me-1 text-danger"></i>{{ __('Garantie Constructeur') }}
                        </span>
                    @endif
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
                    <button type="button" class="pcard-quick-cart-btn {{ $inCart ? 'is-in-cart' : '' }}" data-product-id="{{ $product->id }}" onclick="addToCart({{ $product->id }}, 1, this)" title="{{ $inCart ? __('Déjà dans le panier') : __('Ajouter au panier') }}" aria-label="{{ $inCart ? __('Déjà dans le panier') : __('Ajouter au panier') }}">
                        <i class="fas {{ $inCart ? 'fa-check' : 'fa-shopping-bag' }}"></i>
                        <span class="pcard-quick-cart-label d-none">{{ $inCart ? __('Dans le panier') : __('Ajouter') }}</span>
                    </button>
                    @else
                    <span class="pcard-quick-out" title="{{ __('Rupture de stock') }}" aria-label="{{ __('Rupture de stock') }}">
                        <i class="fas fa-ban"></i>
                        <span class="pcard-quick-out-label d-none">{{ __('Épuisé') }}</span>
                    </span>
                    @endif
                </div>
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
