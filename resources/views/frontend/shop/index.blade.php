@extends('layouts.frontend')

@php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->translated_name . ' — Moubdi3oun')
        : (request('q') ? 'Résultats pour "' . request('q') . '" — Moubdi3oun' : 'Collections — Moubdi3oun');
    $pageDescription = $activeCategory
        ? ('Découvrez notre collection de ' . $activeCategory->translated_name . '. Mobilier artisanal sur mesure, conçu au Maroc.')
        : 'Parcourez nos collections de mobilier artisanal : menuiserie fine, métallurgie design et tapisserie de luxe.';
    $pageKeywords = $activeCategory
        ? ($activeCategory->translated_name . ', mobilier, artisanat, Moubdi3oun, sur mesure')
        : 'mobilier, artisanat, menuiserie, métallurgie, tapisserie, décoration Maroc, Moubdi3oun';
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_keywords', $pageKeywords)

@section('json_ld')
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Accueil",
        "item": "{{ url('/') }}"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Boutique",
        "item": "{{ route('shop.index') }}"
      }
      @if($activeCategory)
      ,{
        "@type": "ListItem",
        "position": 3,
        "name": "{{ addslashes($activeCategory->name) }}",
        "item": "{{ route('shop.index', ['category' => $activeCategory->slug]) }}"
      }
      @endif
    ]
  },
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "{{ addslashes($pageTitle) }}",
    "url": "{{ url()->current() }}",
    "numberOfItems": {{ $products->total() }},
    "itemListElement": [
      @foreach($products as $i => $prod)
      {
        "@type": "ListItem",
        "position": {{ $i + 1 }},
        "url": "{{ route('shop.show', $prod->id) }}",
        "name": "{{ addslashes($prod->name) }}"
      }{{ !$loop->last ? ',' : '' }}
      @endforeach
    ]
  }
]
</script>
@endsection

@section('content')

{{-- =============================================
     SHOP HERO STRIP (dark, matching home page)
     ============================================= --}}
<section class="shop-hero">
    <div class="shop-hero-backdrop"></div>
    <div class="container position-relative">
        <div class="shop-hero-content" data-aos="fade-down">
            <div class="hero-eyebrow text-white mb-3">
                <span class="hero-eyebrow-dot"></span>
                {{ request('q') ? 'Recherche' : (request('category') ? 'Collection' : 'Catalogue') }}
            </div>
            <h1 class="shop-hero-title">
                @if(request('q'))
                    Résultats : <span style="color: var(--accent);">« {{ request('q') }} »</span>
                @elseif(request('category'))
                    <span style="color: var(--accent);">{{ $categories->where('slug', request('category'))->first()->translated_name ?? 'Produits' }}</span>
                @else
                    Nos <span style="color: var(--accent);">Collections</span> Artisanales
                @endif
            </h1>
            <p class="lead opacity-75 mb-4" style="max-width: 600px;">
                Des pièces uniques conçues avec passion dans nos ateliers. Design moderne et savoir-faire traditionnel.
            </p>

            {{-- Breadcrumb --}}
            <nav class="shop-breadcrumb" aria-label="breadcrumb">
                <a href="{{ url('/') }}">Accueil</a>
                <span class="text-white opacity-25">/</span>
                <span class="text-white">Shop</span>
                @if(request('category'))
                    <span class="text-white opacity-25">/</span>
                    <span class="text-white">{{ $categories->where('slug', request('category'))->first()->translated_name ?? 'Catégorie' }}</span>
                @endif
            </nav>
        </div>
    </div>
</section>

{{-- =============================================
     MAIN SHOP LAYOUT
     ============================================= --}}
<section class="shop-body">
    <div class="container">
        <div class="row g-5">

            {{-- ── SIDEBAR ── --}}
            <div class="col-lg-3">
<div class="shop-sidebar sticky-top" style="top: 100px;">

    {{-- Search --}}
    <div class="shop-filter-card mb-4 border-0 shadow-sm">
        <h6 class="shop-filter-title text-dark">Rechercher</h6>
        <form id="searchForm">
            <div class="input-group">
                <input type="text" name="q" class="form-control border-end-0 ps-3 py-2 small"
                       placeholder="Modèle, bois, tissu..." value="{{ request('q') }}" style="border-radius: 30px 0 0 30px;">
                <button type="submit" class="btn border border-start-0 ps-0 pe-3" style="border-radius: 0 30px 30px 0; background: white;">
                    <i class="fas fa-search text-muted small"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- Categories --}}
    <div class="shop-filter-card mb-4 border-0 shadow-sm">
        <h6 class="shop-filter-title text-dark">Collections</h6>
        <div class="shop-cat-list">
            <a href="#" class="shop-cat-link category-filter {{ !request('category') ? 'active' : '' }} text-decoration-none" data-slug="">
                <span>Tout voir</span>
                <span class="badge bg-light text-dark rounded-pill">{{ \App\Models\Product::where('status','active')->count() }}</span>
            </a>
            @foreach($categories as $cat)
            <a href="#" class="shop-cat-link category-filter {{ request('category') == $cat->slug ? 'active' : '' }} text-decoration-none" data-slug="{{ $cat->slug }}">
                <span>{{ $cat->translated_name }}</span>
                <span class="badge bg-light text-dark rounded-pill">{{ $cat->products_count }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Price Range --}}
    <div class="shop-filter-card mb-4 border-0 shadow-sm">
        <h6 class="shop-filter-title text-dark">Prix (DH)</h6>
        <form id="priceFilterForm">
            <div class="d-flex gap-2 align-items-center">
                <input type="number" name="min_price" class="form-control form-control-sm text-center"
                       placeholder="Min" value="{{ request('min_price') }}" style="border-radius: 20px;">
                <span class="text-muted">—</span>
                <input type="number" name="max_price" class="form-control form-control-sm text-center"
                       placeholder="Max" value="{{ request('max_price') }}" style="border-radius: 20px;">
            </div>
            <button type="submit" class="btn btn-dark w-100 mt-3 btn-sm fw-bold py-2" style="border-radius: 30px;">
                Filtrer
            </button>
        </form>
    </div>

</div>
            </div>

            {{-- ── PRODUCT GRID ── --}}
            <div class="col-lg-9">

                {{-- Toolbar --}}
                <div class="shop-toolbar mb-4">
                    <div class="shop-toolbar-left">
                        <span class="shop-toolbar-title" id="categoryTitle">
                            @if(request('category'))
                                {{ $categories->where('slug', request('category'))->first()->name ?? 'Produits' }}
                            @else
                                Tous les équipements
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

@endsection

@push('scripts')
<script>
let currentCategory = "{{ request('category') }}";

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
        document.querySelectorAll('.category-filter').forEach(el => el.classList.remove('active'));
        this.classList.add('active');
        currentCategory = this.dataset.slug;
        document.getElementById('categoryTitle').innerText = this.querySelector('span').innerText;
        fetchProducts();
    });
});

document.getElementById('sortSelect').addEventListener('change', () => fetchProducts());
document.getElementById('priceFilterForm').addEventListener('submit', function(e) { e.preventDefault(); fetchProducts(); });
document.getElementById('searchForm').addEventListener('submit', function(e) { e.preventDefault(); fetchProducts(); });
attachPaginationListeners();

</script>
@endpush
