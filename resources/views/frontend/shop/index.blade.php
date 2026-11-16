@extends('layouts.frontend')

@php
    $seoLoc = \App\Services\SeoService::getLocale();
    $activeCategory = $categories->where('slug', request('category'))->first();
    $searchTerm = request('q') ?: request('search');

    $brandName = match ($seoLoc) {
        'ar' => 'تعاونية آيت أومديس',
        'en' => 'Aït Oumdis Cooperative',
        default => setting('app_name', 'Coopérative Aït Oumdis'),
    };

    if ($activeCategory) {
        $pageTitle = $activeCategory->name . ' — ' . $brandName;
        $pageDescription = match ($seoLoc) {
            'ar' => "اكتشف تشكيلة {$activeCategory->name} الطبيعية 100% من {$brandName}. عسل حر وأملو وزعفران أصيل مع التوصيل لجميع مدن المغرب ({$activeCategory->products_count} منتجات متوفرة).",
            'en' => "Explore our {$activeCategory->name} collection from {$brandName}. 100% pure natural Moroccan honey, artisanal Amlou & terroir products ({$activeCategory->products_count} products available).",
            default => "Découvrez notre sélection de {$activeCategory->name}. Miels purs, Amlou artisanal et produits 100% naturels de la {$brandName}, livrés partout au Maroc ({$activeCategory->products_count} produits disponibles).",
        };
        $pageKeywords = $activeCategory->name . ', ' . \App\Services\SeoService::getKeywords('shop.index');
    } elseif ($searchTerm) {
        $pageTitle = match ($seoLoc) {
            'ar' => "نتائج البحث عن \"{$searchTerm}\" — {$brandName}",
            'en' => "Search results for \"{$searchTerm}\" — {$brandName}",
            default => "Résultats pour \"{$searchTerm}\" — {$brandName}",
        };
        $pageDescription = \App\Services\SeoService::getDescription('shop.index');
        $pageKeywords = $searchTerm . ', ' . \App\Services\SeoService::getKeywords('shop.index');
    } else {
        $pageTitle = \App\Services\SeoService::getTitle('shop.index');
        $pageDescription = \App\Services\SeoService::getDescription('shop.index');
        $pageKeywords = \App\Services\SeoService::getKeywords('shop.index');
    }

    $homeLabel = match ($seoLoc) { 'ar' => 'الرئيسية', 'en' => 'Home', default => 'Accueil' };
    $shopLabel = match ($seoLoc) { 'ar' => 'المتجر', 'en' => 'Shop', default => 'Boutique' };

    $breadcrumbItems = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => $homeLabel, 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $shopLabel, 'item' => route('shop.index')],
    ];
    if ($activeCategory) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $activeCategory->name,
            'item' => route('shop.index', ['category' => $activeCategory->slug]),
        ];
    }

    $shopLdGraph = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $pageTitle,
            'url' => \App\Services\SeoService::getCanonicalUrl(),
            'numberOfItems' => $products->total(),
            'itemListElement' => $products->values()->map(fn ($prod, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'url' => route('shop.show', $prod->id),
                'name' => $prod->name,
            ])->all(),
        ],
    ];
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_keywords', $pageKeywords)

@section('json_ld')
<script type="application/ld+json">{!! json_encode($shopLdGraph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endsection

@section('content')

{{-- =============================================
     CLEAN MOROCCAN ZELLIGE SHOP HEADER
     ============================================= --}}
  <header class="aod-zellige-soft" style="padding: 44px 0 36px; border-bottom: 1px solid #e3ede6;">
    <div class="aod-container" style="position: relative; z-index: 1;">
      <nav aria-label="Fil d'Ariane" style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px; font-size: 0.84rem;">
        <a href="{{ route('home') }}" style="color: #5f6f65; text-decoration: none; font-weight: 600;">Accueil</a>
        <span style="color: #2f8825;">/</span>
        <span style="color: #2f8825; font-weight: 700;">Nos Produits</span>
        @if(request('category'))
          <span style="color: #2f8825;">/</span>
          <span style="color: #1e4d34; font-weight: 700;">{{ $categories->where('slug', request('category'))->first()->name ?? 'Catégorie' }}</span>
        @endif
      </nav>

      <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
        <div style="max-width: 680px;">
          <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.9rem, 3vw, 2.4rem); font-weight: 700; color: #2b302c; margin: 0 0 8px 0;">
            @if(request('q'))
                Résultats pour « {{ request('q') }} »
            @elseif(request('category'))
                {{ $categories->where('slug', request('category'))->first()->name ?? 'Nos Produits' }}
            @else
                Catalogue Coopérative Aït Oumdis
            @endif
          </h1>
          <p style="font-size: 0.95rem; line-height: 1.6; color: #5f6f65; margin: 0;">
            Miels 100% purs du Maroc, Amlou traditionnel aux amandes et à l'huile d'Argan, et Safran véritable.
          </p>
        </div>

        <div style="background: #2f8825; padding: 8px 18px; border-radius: 9999px; color: #ffffff; font-size: 0.86rem; font-weight: 700;">
          <span>{{ $products->total() ?? $products->count() }} {{ ($products->total() ?? $products->count()) > 1 ? 'produits disponibles' : 'produit disponible' }}</span>
        </div>
      </div>
    </div>
  </header>

{{-- =============================================
     MAIN SHOP LAYOUT
     ============================================= --}}
<section class="shop-body" style="background-color: #ffffff; padding: 48px 0 90px;">
    <div class="aod-container">
        <div class="row g-5">

            {{-- ── MOBILE FLOATING FILTER BUTTON (FAB) ── --}}
            <button class="btn btn-primary btn-fab d-lg-none shadow-lg d-flex align-items-center justify-content-center" 
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#shopFiltersBottom">
                <i class="fas fa-sliders-h fs-4"></i>
            </button>

            {{-- ── SIDEBAR (Desktop) / OFFCANVAS (Mobile) ── --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="shop-sidebar sticky-top" style="top: 100px;">
                    @include('frontend.shop.partials.sidebar-content')
                </div>
            </div>

            {{-- ── PRODUCT GRID ── --}}
            <div class="col-lg-9">

                {{-- Toolbar (Desktop only) --}}
                <style>
.sp-cat-pills-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 22px;
    flex-wrap: wrap;
}
.sp-cat-pill-btn {
    background: #ffffff;
    border: 1.5px solid rgba(12, 38, 30, 0.1);
    color: #0c261e;
    padding: 8px 18px;
    border-radius: 9999px;
    font-size: 0.86rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.22s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}
.sp-cat-pill-btn:hover {
    border-color: #c28d32;
    color: #c28d32;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(194, 141, 50, 0.15);
}
.sp-cat-pill-btn.active {
    background: #0c261e;
    color: #ffffff;
    border-color: #0c261e;
    box-shadow: 0 4px 14px rgba(12, 38, 30, 0.25);
}
.sp-cat-pill-btn .pill-badge {
    font-size: 0.74rem;
    opacity: 0.7;
}
.sp-cat-pill-btn.active .pill-badge {
    color: #e2ad50;
    opacity: 1;
}
</style>

                <div class="shop-toolbar mb-4 d-none d-lg-flex">
                    <div class="shop-toolbar-left">
                        <span class="shop-toolbar-title" id="categoryTitle">
                            @if(request('category'))
                                {{ $categories->where('slug', request('category'))->first()->name ?? 'Produits' }}
                            @else
                                Tous nos produits du terroir
                            @endif
                        </span>
                        <span class="shop-toolbar-count">{{ $products->total() }} produit{{ $products->total() != 1 ? 's' : '' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="shop-sort-label">Trier :</label>
                        <select class="shop-sort-select" id="sortSelect">
                            <option value="newest"  {{ request('sort') == 'newest'     ? 'selected' : '' }}>Plus récents</option>
                            <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        </select>
                    </div>
                </div>

                {{-- Active Filters --}}
                @if(request('q') || request('category') || request('min_price') || request('max_price'))
                <div class="shop-active-filters mb-4">
                    <span class="shop-active-label">Filtres actifs :</span>
                    @if(request('q'))
                        <span class="shop-filter-tag">Recherche : {{ request('q') }}</span>
                    @endif
                    @if(request('category'))
                        <span class="shop-filter-tag">Catégorie : {{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}</span>
                    @endif
                    @if(request('min_price') || request('max_price'))
                        <span class="shop-filter-tag">Prix : {{ request('min_price', '0') }} — {{ request('max_price', '∞') }} DH</span>
                    @endif
                    <a href="{{ route('shop.index') }}" class="shop-clear-link">
                        <i class="fas fa-times me-1"></i>Effacer tout
                    </a>
                </div>
                @endif

                {{-- Product Grid (AJAX-swapped partial) --}}
                <div id="productGridContainer">
                    @include('frontend.shop.partials.product-grid')
                </div>

                {{-- Loader --}}
                <div id="loader" class="d-none text-center py-5">
                    <div class="shop-loader-spinner"></div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── OFFCANVAS COMPONENTS (Moved outside main containers) ── --}}
<div class="offcanvas offcanvas-bottom border-0 shadow-lg d-lg-none" tabindex="-1" id="shopFiltersBottom" style="height: 85vh; border-radius: 24px 24px 0 0; background: #faf9f5;">
    <!-- Drag Handle -->
    <div style="display:flex;justify-content:center;padding:10px 0 0;flex-shrink:0;">
        <div style="width:40px;height:4px;border-radius:9999px;background:rgba(12,38,30,0.15);"></div>
    </div>
    <!-- Header -->
    <div style="background:#0c261e;margin:10px 16px 0;border-radius:16px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;font-family:'Playfair Display',Georgia,serif;font-size:1rem;font-weight:700;color:#ffffff;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e2ad50" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Options de filtrage
        </div>
        <button type="button" data-bs-dismiss="offcanvas" aria-label="Close" style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);color:#ffffff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <!-- Scrollable content -->
    <div class="offcanvas-body" style="overflow-y:auto;padding:16px;flex:1;">
        @include('frontend.shop.partials.sidebar-content')
    </div>
    <!-- Sticky footer CTA -->
    <div style="flex-shrink:0;padding:12px 16px 24px;background:#ffffff;border-top:1px solid rgba(12,38,30,0.07);">
        <button class="mc-btn-checkout" data-bs-dismiss="offcanvas" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 22px;background:#0c261e;color:#ffffff;border:none;border-radius:14px;font-size:0.9rem;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;cursor:pointer;">
            <span>Voir les {{ $products->total() }} produits</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>

<div class="offcanvas offcanvas-start border-0 shadow-lg d-lg-none" tabindex="-1" id="shopSidebarOffcanvas" style="width: 320px;">
    <div class="offcanvas-header bg-white border-bottom py-3">
        <h5 class="offcanvas-title fw-bold" id="shopSidebarOffcanvasLabel">
            <i class="fas fa-filter me-2 text-primary"></i>Filtres
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4 bg-light">
        @include('frontend.shop.partials.sidebar-content')
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentCategory = "{{ request('category') }}";

function updateSort(val) {
    const select = document.getElementById('sortSelect');
    if (select) select.value = val;
    fetchProducts();
}

function getParams() {
    const p = new URLSearchParams();
    if (currentCategory) p.append('category', currentCategory);
    const sort     = document.getElementById('sortSelect').value;
    const q        = document.querySelector('input[name="q"]').value;
    const minPrice = document.querySelector('input[name="min_price"]').value;
    const maxPrice = document.querySelector('input[name="max_price"]').value;
    if (sort)     p.append('sort',      sort);
    if (q)        p.append('q',         q);
    if (minPrice) p.append('min_price', minPrice);
    if (maxPrice) p.append('max_price', maxPrice);
    return p;
}

function fetchProducts(url = "{{ route('shop.index') }}") {
    const grid   = document.getElementById('productGridContainer');
    const loader = document.getElementById('loader');
    grid.style.opacity = '0.4';
    loader.classList.remove('d-none');
    const fetchUrl = url.includes('?') ? url : `${url}?${getParams().toString()}`;
    window.history.pushState(null, '', fetchUrl);
    fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            grid.innerHTML = html;
            grid.style.opacity = '1';
            loader.classList.add('d-none');
            attachPaginationListeners();
        })
        .catch(err => {
            console.error(err);
            grid.style.opacity = '1';
            loader.classList.add('d-none');
        });
}

function attachPaginationListeners() {
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            fetchProducts(this.href);
            document.getElementById('productGridContainer').scrollIntoView({ behavior: 'smooth' });
        });
    });
}

document.querySelectorAll('.category-filter').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const slug = this.dataset.slug || '';
        currentCategory = slug;
        document.querySelectorAll('.category-filter').forEach(el => {
            const isMatch = (el.dataset.slug || '') === slug;
            el.classList.toggle('active', isMatch);
        });
        const titleSpan = this.querySelector('span');
        const titleText = (titleSpan && titleSpan.innerText && slug) ? titleSpan.innerText : 'Tous nos produits du terroir';
        const catTitle = document.getElementById('categoryTitle');
        if (catTitle) catTitle.innerText = titleText;
        fetchProducts();
    });
});

document.getElementById('sortSelect').addEventListener('change', () => fetchProducts());
document.getElementById('priceFilterForm').addEventListener('submit', function(e) { e.preventDefault(); fetchProducts(); });
document.getElementById('searchForm').addEventListener('submit', function(e) { e.preventDefault(); fetchProducts(); });
attachPaginationListeners();

// Uses global addToCart from layouts/frontend.blade.php
</script>
@endpush
