@extends('layouts.frontend')

@php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->name . ' — ' . setting('app_name', 'Speed Platform'))
        : (request('q') ? 'Résultats pour "' . request('q') . '" — ' . setting('app_name') : 'Boutique — ' . setting('app_name', 'Speed Platform'));
    $pageDescription = $activeCategory
        ? ('Découvrez notre gamme de ' . $activeCategory->name . '. Livraison partout au Maroc, installation et SAV inclus. ' . $activeCategory->products_count . ' produits disponibles.')
        : 'Parcourez notre catalogue complet de machines d\'impression grand format, traceurs de découpe, encres et consommables. Livraison Maroc, devis gratuit.';
    $pageKeywords = $activeCategory
        ? ($activeCategory->name . ', ' . setting('app_name', 'boutique') . ', acheter ' . $activeCategory->name . ' Maroc, prix ' . $activeCategory->name)
        : setting('app_name', 'boutique') . ', machines impression, traceur découpe, encres, consommables, Maroc';
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
        <div class="shop-hero-content" data-aos="fade-up">
            <div class="hero-eyebrow mb-3">
                <span class="hero-eyebrow-dot"></span>
                {{ request('q') ? 'Résultats de recherche' : (request('category') ? 'Catalogue Spécialisé' : 'Solutions d\'Impression') }}
            </div>
            <h1 class="shop-hero-title">
                @if(request('q'))
                    Résultats pour <span class="text-white">« {{ request('q') }} »</span>
                @elseif(request('category'))
                    <span class="text-white">{{ $categories->where('slug', request('category'))->first()->name ?? 'Produits' }}</span>
                @else
                    Équipements <span class="text-primary-light">Premium</span> & Consommables
                @endif
            </h1>
            <p class="shop-hero-sub">
                Explorez notre sélection de machines éco-solvant, traceurs de découpe et encres certifiées pour une production d'excellence.
            </p>

            {{-- Breadcrumb --}}
            <nav class="shop-breadcrumb mt-4" aria-label="breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i> Accueil</a>
                <span class="shop-bc-sep mx-2 opacity-50">/</span>
                <a href="{{ route('shop.index') }}">Boutique</a>
                @if(request('category'))
                    <span class="shop-bc-sep mx-2 opacity-50">/</span>
                    <span class="text-white fw-bold">{{ $categories->where('slug', request('category'))->first()->name ?? 'Catégorie' }}</span>
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

            {{-- ── MOBILE CATEGORY SCROLLER ── --}}
            <div class="shop-mobile-categories d-lg-none py-3 mb-2 overflow-auto" style="white-space: nowrap; -webkit-overflow-scrolling: touch;">
                <div class="container-fluid px-3 d-flex gap-2">
                    <a href="#" class="btn btn-sm rounded-pill px-4 py-2 fw-bold border category-filter {{ !request('category') ? 'btn-primary text-white border-primary' : 'btn-white text-muted' }}" data-slug="">
                        Tous
                    </a>
                    @foreach($categories as $cat)
                        <a href="#" class="btn btn-sm rounded-pill px-4 py-2 fw-bold border category-filter {{ request('category') == $cat->slug ? 'btn-primary text-white border-primary' : 'btn-white text-muted' }}" data-slug="{{ $cat->slug }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>

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
                <div class="shop-toolbar mb-4 d-none d-lg-flex">
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

{{-- ── OFFCANVAS COMPONENTS (Moved outside main containers) ── --}}
<div class="offcanvas offcanvas-bottom border-0 shadow-lg d-lg-none" tabindex="-1" id="shopFiltersBottom" style="height: 70vh; border-radius: 28px 28px 0 0;">
    <div class="offcanvas-header bg-white border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold">Options de filtrage</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4 bg-light">
        @include('frontend.shop.partials.sidebar-content')
        <div class="mt-4 pb-5">
            <button class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg" data-bs-dismiss="offcanvas">
                Voir les {{ $products->total() }} produits
            </button>
        </div>
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

function addToCart(id) {
    const btn = document.querySelector(`button[onclick="addToCart(${id})"]`);
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`/cart/add/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if (data.success) {
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Ajouté au panier !',
                showConfirmButton:false, timer:2500, background:'#1a1a2e', color:'#fff' });
            const badge = document.getElementById('header-cart-count');
            if (badge && data.cartCount !== undefined) badge.innerText = data.cartCount;
            if (typeof refreshMiniCart === 'function') refreshMiniCart();
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
@endpush
