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
        <div class="shop-empty text-center py-4 px-3 rounded-4 bg-white border shadow-sm mb-3">
            <div class="shop-empty-icon-wrap mb-2 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger" style="width: 60px; height: 60px; font-size: 1.5rem;">
                <i class="fas fa-search"></i>
            </div>
            <div class="h5 text-dark mb-1" style="font-weight: 600;">{{ __('Aucun produit ne correspond à vos critères') }}</div>
            <p class="text-muted small mb-3 mx-auto" style="max-width: 480px;">
                @if(request('q'))
                    {{ __('Aucun résultat pour') }} « <strong>{{ request('q') }}</strong> ». {{ __('Découvrez nos suggestions et produits recommandés ci-dessous :') }}
                @else
                    {{ __('Essayez de réinitialiser vos filtres ou explorez nos suggestions recommandées ci-dessous :') }}
                @endif
            </p>

            <div class="d-flex justify-content-center gap-2 flex-wrap mb-1">
                <button type="button" onclick="if(window.resetAllFilters) { window.resetAllFilters(); }" class="btn btn-outline-danger rounded-pill px-3 py-1.5 d-inline-flex gap-2 align-items-center shadow-sm" style="font-weight: 500; font-size: 0.85rem;">
                    <i class="fas fa-redo"></i> <span>{{ __('Réinitialiser les filtres') }}</span>
                </button>
                <a href="{{ route('shop.index') }}" class="btn btn-danger rounded-pill px-3 py-1.5 d-inline-flex gap-2 align-items-center shadow-sm" style="font-weight: 500; font-size: 0.85rem; background: linear-gradient(45deg, #e10126, #a50019); border: none;">
                    <i class="fas fa-th-large"></i> <span>{{ __('Voir tout le catalogue') }}</span>
                </a>
            </div>
        </div>

        {{-- SLIDER OF RANDOM PRODUCTS (EXACTLY AS ON PRODUCT PAGE) --}}
        @php
            if (!isset($randomProducts) || $randomProducts->isEmpty()) {
                $fileCache = \Illuminate\Support\Facades\Cache::store('file');
                $cacheVer = \App\Http\Middleware\FastGuestPageCache::getVersion();
                $randomProducts = $fileCache->remember("shop_random_slider_pool_{$cacheVer}", 1800, function () {
                    return \App\Models\Product::with(['images', 'activeTags', 'productCategory', 'brand'])
                        ->where('status', 'active')
                        ->where('stock', '>', 0)
                        ->latest('updated_at')
                        ->take(40)
                        ->get();
                });
                if ($randomProducts->count() > 10) {
                    $randomProducts = $randomProducts->random(10)->values();
                }
            }
        @endphp

        @if(isset($randomProducts) && $randomProducts->isNotEmpty())
        <div class="pdp-slider-section shop-empty-slider-section mb-4" id="shop-empty-random-slider">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: clamp(1.05rem, 2vw, 1.25rem); letter-spacing: -0.3px;">
                        <i class="fas fa-fire text-warning me-2"></i>{{ __('Suggestions pour vous') }}
                    </h2>
                </div>
                <div>
                    <a href="{{ route('shop.index') }}" 
                       class="btn btn-outline-danger fw-bold px-3 py-1 text-nowrap d-inline-flex align-items-center gap-2" 
                       style="border-radius: 8px; font-size: 0.82rem; border-color: #c8102e;">
                        <span>{{ __('Tout le catalogue') }}</span>
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}" style="font-size: 10px;"></i>
                    </a>
                </div>
            </div>

            <div class="pdp-slider-carousel-wrap">
                {{-- Desktop Side Navigation Buttons --}}
                <button type="button" class="btn-slider-nav pdp-slider-side-nav pdp-slider-side-prev shop-empty-swiper-prev d-none d-lg-flex" aria-label="{{ __('Précédent') }}" title="{{ __('Précédent') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }}"></i>
                </button>
                <button type="button" class="btn-slider-nav pdp-slider-side-nav pdp-slider-side-next shop-empty-swiper-next d-none d-lg-flex" aria-label="{{ __('Suivant') }}" title="{{ __('Suivant') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}"></i>
                </button>

                <div class="swiper pdp-products-swiper shop-empty-random-swiper">
                    <div class="swiper-wrapper">
                        @foreach($randomProducts as $randomItem)
                            <div class="swiper-slide">
                                @include('frontend.partials.product_card_slider', ['product' => $randomItem])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Dots & Navigation Arrows on Left and Right of Dots for Mobile --}}
            <div class="pdp-mobile-pagination-wrap">
                <button type="button" class="btn btn-slider-nav pdp-slider-mobile-nav shop-empty-swiper-prev d-lg-none" aria-label="{{ __('Précédent') }}" title="{{ __('Précédent') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }}"></i>
                </button>
                <div class="pdp-slider-pagination shop-empty-random-pagination"></div>
                <button type="button" class="btn btn-slider-nav pdp-slider-mobile-nav shop-empty-swiper-next d-lg-none" aria-label="{{ __('Suivant') }}" title="{{ __('Suivant') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}"></i>
                </button>
            </div>
        </div>

        <script>
            if (typeof window.initShopEmptySlider === 'function') {
                window.initShopEmptySlider();
            }
        </script>
        @endif
    </div>
    @endforelse
</div>

@if($products->hasPages())
<div class="mt-4 mt-md-5 d-flex justify-content-center shop-pagination">
    {{ $products->links() }}
</div>
@endif
