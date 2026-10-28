{{-- Meta Bridge for Dynamic AJAX Count & State Synchronization --}}
<div id="shopMetaBridge" class="d-none" 
     data-total="{{ $products->total() }}" 
     data-category-title="{{ $activeCategoryTitle ?? ($categories->where('slug', request('category'))->first()->name ?? ($products->total() . ' ' . __('produits'))) }}"
     data-category-slug="{{ request('category', '') }}"
     data-brand-slug="{{ request('brand', '') }}"
     data-brand-name="{{ isset($activeBrand) && $activeBrand ? $activeBrand->name : '' }}"
     data-current-page="{{ $products->currentPage() }}"
     data-last-page="{{ $products->lastPage() }}"
     data-has-pages="{{ $products->hasPages() ? '1' : '0' }}">
    <script type="application/json" id="shopMetaBrandsPayload">
        {!! json_encode(($smartBrands ?? collect())->map(function($b) {
            return [
                'id' => $b->id,
                'name' => $b->name,
                'slug' => $b->slug,
                'logo_url' => $b->logo_url,
                'count' => $b->products_count,
            ];
        })->values()) !!}
    </script>
</div>

@if(request('q') && isset($matchingCategories) && $matchingCategories->isNotEmpty())
<div class="shop-matching-categories mb-3 p-2 rounded-3 bg-light border d-flex align-items-center flex-wrap gap-2">
    <span class="small text-muted d-flex align-items-center gap-1" style="font-weight: 500;">
        <i class="fas fa-th-large text-danger"></i> {{ __('Rayons suggérés :') }}
    </span>
    @foreach($matchingCategories as $mCat)
        <a href="{{ route('shop.index', ['category' => $mCat['slug'], 'q' => request('q')]) }}" class="badge rounded-pill bg-white text-dark border py-1.5 px-3 text-decoration-none shadow-sm" style="font-weight: 400; font-size: 0.8rem;">
            {{ $mCat['name'] }} <span class="text-danger ms-1">({{ $mCat['count'] }})</span>
        </a>
    @endforeach
</div>
@endif

<div class="row g-2 home-products-grid" id="shopProductRow">
    @forelse($products as $product)
    <div class="col-6 col-sm-6 col-md-4 col-xl-3 product-col">
        @include('frontend.partials.product_card_slider', ['product' => $product])
    </div>
    @empty
    <div class="col-12">
        <div class="shop-empty text-center py-5 px-3 rounded-4 bg-white border shadow-sm">
            <div class="shop-empty-icon-wrap mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger" style="width: 72px; height: 72px; font-size: 1.8rem;">
                <i class="fas fa-search"></i>
            </div>
            <div class="h5 text-dark mb-2" style="font-weight: 500;">{{ __('Aucun produit ne correspond à vos critères') }}</div>
            <p class="text-muted small mb-3 mx-auto" style="max-width: 440px;">
                @if(request('q'))
                    {{ __('Aucun produit trouvé pour la recherche') }} « <strong>{{ request('q') }}</strong> ». {{ __('Vérifiez l\'orthographe ou explorez nos modèles populaires :') }}
                @else
                    {{ __('Essayez d\'élargir vos filtres de prix, de rechercher un autre modèle ou de consulter tous les rayons du catalogue.') }}
                @endif
            </p>

            @php
                $popSearches = $popularSearches ?? \App\Services\ProductSearchService::getPopularSearches();
            @endphp
            @if(!empty($popSearches))
            <div class="my-4 mx-auto" style="max-width: 580px;">
                <div class="text-uppercase small text-muted mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 500;">
                    <i class="fas fa-fire text-danger me-1"></i> {{ __('Recherches populaires') }}
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    @foreach($popSearches as $popularItem)
                    <a href="{{ route('shop.index', ['q' => $popularItem]) }}" class="badge rounded-pill bg-light text-dark border px-3 py-2 text-decoration-none shadow-sm" style="font-weight: 400; font-size: 0.8rem;">
                        <i class="fas fa-search text-danger me-1 small"></i> {{ $popularItem }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <button type="button" onclick="if(window.resetAllFilters) { window.resetAllFilters(); }" class="btn btn-outline-danger rounded-pill px-4 py-2 d-inline-flex gap-2 align-items-center shadow-sm" style="font-weight: 500;">
                    <i class="fas fa-redo"></i> <span>{{ __('Réinitialiser les filtres') }}</span>
                </button>
                <a href="{{ route('shop.index') }}" class="btn btn-danger rounded-pill px-4 py-2 d-inline-flex gap-2 align-items-center shadow-sm" style="font-weight: 500;">
                    <i class="fas fa-th-large"></i> <span>{{ __('Voir tout le catalogue') }}</span>
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($products->hasPages())
<div class="mt-4 mt-md-5 d-flex justify-content-center shop-pagination">
    {{ $products->links() }}
</div>
@endif
