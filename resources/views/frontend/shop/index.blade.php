@extends('layouts.frontend')

@section('meta_title', 'Catalogue — ' . setting('app_name', 'HM Collection'))
@section('meta_description', 'Parcourez notre catalogue complet de pyjamas raffinés, articles de décoration chaleureux et soins sublimateurs.')

@section('content')

{{-- =============================================
     SHOP HERO STRIP (dark, matching home page)
     ============================================= --}}
<section class="shop-hero">
    <div class="shop-hero-backdrop"></div>
    <div class="container position-relative">
        <div class="shop-hero-content">
            <div class="hero-eyebrow">
                <span class="hero-eyebrow-dot"></span>
                {{ request('q') ? 'Résultats de recherche' : (request('category') ? 'Catégorie' : 'Catalogue complet') }}
            </div>
            <h1 class="shop-hero-title">
                @if(request('q'))
                    Résultats pour <span class="text-gradient-primary">« {{ request('q') }} »</span>
                @elseif(request('category'))
                    <span class="text-gradient-primary">{{ $categories->where('slug', request('category'))->first()->name ?? 'Produits' }}</span>
                @else
                    Nos <span class="text-gradient-primary">Créations</span> & Soins
                @endif
            </h1>
            <p class="shop-hero-sub">
                Élégance, douceur et bien-être — tout pour sublimer votre quotidien.
            </p>

            {{-- Breadcrumb --}}
            <nav class="shop-breadcrumb" aria-label="breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <span class="shop-bc-sep">/</span>
                <a href="{{ route('shop.index') }}">Catalogue</a>
                @if(request('category'))
                    <span class="shop-bc-sep">/</span>
                    <span>{{ $categories->where('slug', request('category'))->first()->name ?? 'Catégorie' }}</span>
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
                <div class="shop-sidebar sticky-top" style="top: 90px;">

                    {{-- Search --}}
                    <div class="shop-filter-card mb-4">
                        <h6 class="shop-filter-title"><i class="fas fa-search me-2"></i>Recherche</h6>
                        <form id="searchForm">
                            <div class="shop-search-wrap">
                                <input type="text" name="q" class="shop-search-input"
                                       placeholder="Nom du produit…" value="{{ request('q') }}">
                                <button type="submit" class="shop-search-btn">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Categories --}}
                    <div class="shop-filter-card mb-4">
                        <h6 class="shop-filter-title"><i class="fas fa-th-large me-2"></i>Catégories</h6>
                        <ul class="shop-cat-list">
                            <li>
                                <a href="#" class="shop-cat-link category-filter {{ !request('category') ? 'active' : '' }}" data-slug="">
                                    <span>Tous les produits</span>
                                    <span class="shop-cat-count">{{ \App\Models\Product::where('status','active')->count() }}</span>
                                </a>
                            </li>
                            @foreach($categories as $cat)
                            <li>
                                <a href="#" class="shop-cat-link category-filter {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="shop-cat-count">{{ $cat->products_count }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Price Range --}}
                    <div class="shop-filter-card mb-4">
                        <h6 class="shop-filter-title"><i class="fas fa-tag me-2"></i>Fourchette de prix</h6>
                        <form id="priceFilterForm">
                            <div class="shop-price-inputs">
                                <input type="number" name="min_price" class="shop-price-input"
                                       placeholder="Min" value="{{ request('min_price') }}" min="0">
                                <span class="shop-price-sep">—</span>
                                <input type="number" name="max_price" class="shop-price-input"
                                       placeholder="Max" value="{{ request('max_price') }}" min="0">
                            </div>
                            <button type="submit" class="shop-apply-btn w-100 mt-3">
                                <i class="fas fa-filter me-2"></i>Appliquer
                            </button>
                        </form>
                    </div>

                    {{-- Quick Links --}}
                    <div class="shop-filter-card">
                        <h6 class="shop-filter-title"><i class="fas fa-bolt me-2"></i>Raccourcis</h6>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('shop.index') }}?sort=newest" class="shop-quick-link">
                                <i class="fas fa-star me-2 text-accent"></i>Nouveautés
                            </a>
                            <a href="{{ route('shop.index') }}?sort=price_asc" class="shop-quick-link">
                                <i class="fas fa-sort-amount-up me-2 text-accent"></i>Prix croissant
                            </a>
                            <a href="{{ route('shop.index') }}?sort=price_desc" class="shop-quick-link">
                                <i class="fas fa-sort-amount-down me-2 text-accent"></i>Prix décroissant
                            </a>
                        </div>
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
