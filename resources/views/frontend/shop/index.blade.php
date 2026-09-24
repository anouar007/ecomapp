@extends('layouts.frontend')

@php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->name . ' — ' . setting('app_name', 'Coopérative Aït Oumdis'))
        : (request('q') ? 'Résultats pour "' . request('q') . '" — ' . setting('app_name') : 'Boutique — ' . setting('app_name', 'Coopérative Aït Oumdis'));
        $pageDescription = $activeCategory
        ? ('Découvrez notre gamme de ' . $activeCategory->name . '. Cosmétiques 100% naturels et bio du Haut Atlas, livrés partout au Maroc. ' . $activeCategory->products_count . ' soins disponibles.')
        : "Découvrez notre collection de soins botaniques 100% bio du Haut Atlas : soins capillaires anti-chute au romarin, protection solaire minérale SPF 50+ et rituels artisanaux d'Aït Oumdis. Livraison partout au Maroc.";
    $pageKeywords = $activeCategory
        ? ($activeCategory->name . ', ' . setting('app_name', 'boutique') . ', acheter ' . $activeCategory->name . ' Maroc, prix ' . $activeCategory->name)
        : setting('app_name', 'Coopérative Aït Oumdis') . ', cosmétiques naturels maroc, huiles végétales bio, soins du terroir, argan pur, Haut Atlas Azilal';
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
     MAIN SHOP LAYOUT
     ============================================= --}}
<section class="shop-body" style="background-color: #faf7f2; background-image: url('{{ asset('assets/images/botanical_pattern_bg.jpg') }}'); background-size: 850px auto; background-repeat: repeat; background-position: center top; padding: 50px 0 100px;">
    <div class="container">
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
                                Tous nos soins botaniques
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

{{-- =============================================
     SHOP HERO STRIP (dark, matching home page)
     ============================================= --}}
  <header class="sp-boutique-hero" style="background-image: linear-gradient(90deg, rgba(8, 26, 19, 0.94) 0%, rgba(8, 26, 19, 0.80) 45%, rgba(8, 26, 19, 0.35) 82%, rgba(8, 26, 19, 0.65) 100%), url('{{ asset('assets/images/shop-hero.jpg') }}'); background-size: cover; background-position: center right; background-repeat: no-repeat; padding: 130px 0 35px 0; color: #ffffff; position: relative;">
    <div class="sp-boutique-hero-inner" style="max-width: 1280px; margin: 0 auto; padding: 0 24px;">
      <nav class="sp-breadcrumb" aria-label="Fil d'Ariane" style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px; font-size: 0.84rem;">
        <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.65); text-decoration: none;">Accueil</a>
        <span class="sep" style="color: rgba(226,173,80,0.5);">/</span>
        <span class="current" style="color: #e2ad50; font-weight: 600;">Boutique</span>
        @if(request('category'))
          <span class="sep" style="color: rgba(226,173,80,0.5);">/</span>
          <span class="current" style="color: #ffffff; font-weight: 600;">{{ $categories->where('slug', request('category'))->first()->name ?? 'Catégorie' }}</span>
        @endif
      </nav>

      <div class="sp-boutique-header-row" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
        <div class="sp-boutique-title-group" style="max-width: 680px;">
          <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(2rem, 3.2vw, 2.7rem); font-weight: 600; color: #ffffff; margin: 0 0 10px 0;">
            @if(request('q'))
                Résultats pour « {{ request('q') }} »
            @elseif(request('category'))
                {{ $categories->where('slug', request('category'))->first()->name ?? 'Nos Soins' }}
            @else
                La Boutique Botanique
            @endif
          </h1>
          <p class="sp-boutique-subtitle" style="font-size: 0.95rem; line-height: 1.6; color: rgba(255,255,255,0.8); margin: 0;">
            Soins d'altitude formulés à 100% d'actifs purs du Haut Atlas marocain. Récoltés et distillés artisanalement par les femmes de la Coopérative Aït Oumdis.
          </p>
        </div>

        <div class="sp-shop-count" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); padding: 8px 18px; border-radius: 9999px; border: 1px solid rgba(226,173,80,0.35); color: #ffffff; font-size: 0.88rem;">
          <span><strong>{{ $products->total() ?? $products->count() }}</strong> {{ ($products->total() ?? $products->count()) > 1 ? 'soins disponibles' : 'soin disponible' }}</span>
        </div>
      </div>

      <div class="sp-boutique-trust-strip" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; padding: 18px 22px; background: #ffffff; border-radius: 18px; border: 1px solid rgba(12,38,30,0.08); box-shadow: 0 10px 30px rgba(12, 38, 30, 0.08); margin-top: 24px;">
        <div class="sp-trust-strip-item" style="display: flex; align-items: center; gap: 12px;">
          <div class="sp-trust-strip-icon" style="width: 40px; height: 40px; border-radius: 12px; background: rgba(226,173,80,0.14); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          </div>
          <div>
            <div style="font-size: 0.86rem; font-weight: 700; color: #0c261e; line-height: 1.25;">Livraison Express</div>
            <div style="font-size: 0.74rem; color: #6b7a72; margin-top: 2px;">24 à 48h partout au Maroc</div>
          </div>
        </div>

        <div class="sp-trust-strip-item" style="display: flex; align-items: center; gap: 12px;">
          <div class="sp-trust-strip-icon" style="width: 40px; height: 40px; border-radius: 12px; background: rgba(226,173,80,0.14); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          </div>
          <div>
            <div style="font-size: 0.86rem; font-weight: 700; color: #0c261e; line-height: 1.25;">Paiement à la Livraison</div>
            <div style="font-size: 0.74rem; color: #6b7a72; margin-top: 2px;">Réglez en espèces à réception</div>
          </div>
        </div>

        <div class="sp-trust-strip-item" style="display: flex; align-items: center; gap: 12px;">
          <div class="sp-trust-strip-icon" style="width: 40px; height: 40px; border-radius: 12px; background: rgba(226,173,80,0.14); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div>
            <div style="font-size: 0.86rem; font-weight: 700; color: #0c261e; line-height: 1.25;">100% Bio & Terroir</div>
            <div style="font-size: 0.74rem; color: #6b7a72; margin-top: 2px;">Actifs purs du Haut Atlas</div>
          </div>
        </div>

        <div class="sp-trust-strip-item" style="display: flex; align-items: center; gap: 12px;">
          <div class="sp-trust-strip-icon" style="width: 40px; height: 40px; border-radius: 12px; background: rgba(226,173,80,0.14); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
          </div>
          <div>
            <div style="font-size: 0.86rem; font-weight: 700; color: #0c261e; line-height: 1.25;">Artisanat Solidaire</div>
            <div style="font-size: 0.74rem; color: #6b7a72; margin-top: 2px;">Coopérative Aït Oumdis</div>
          </div>
        </div>
      </div>

    </div>
  </header>



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
        const titleText = (titleSpan && titleSpan.innerText && slug) ? titleSpan.innerText : 'Tous nos soins botaniques';
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
