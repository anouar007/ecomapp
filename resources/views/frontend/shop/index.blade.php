@extends('layouts.frontend')

@php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->name . ' — ' . setting('app_name', 'WINA SHOP'))
        : (request('q') ? 'Résultats pour "' . request('q') . '" — ' . setting('app_name', 'WINA SHOP') : 'Catalogue Matériel Photo & Vidéo — ' . setting('app_name', 'WINA SHOP'));
    $pageDescription = $activeCategory
        ? ('Découvrez notre gamme de ' . $activeCategory->name . '. Garantie constructeur, showroom à Casablanca et livraison express partout au Maroc.')
        : 'Parcourez notre catalogue complet de matériel photo et vidéo, caméras, drones DJI, objectifs, stabilisateurs et éclairage studio au Maroc.';
    $pageKeywords = $activeCategory
        ? ($activeCategory->name . ', ' . setting('app_name', 'WINA SHOP') . ', acheter ' . $activeCategory->name . ' Maroc, prix ' . $activeCategory->name)
        : setting('app_name', 'WINA SHOP') . ', caméras cinéma, objectifs photo, stabilisateurs DJI, éclairage vidéo, Casablanca, Maroc';
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
     SHOP HERO STRIP (Cinema Viewfinder Theme)
     ============================================= --}}
<section class="shop-hero">
    <div class="shop-hero-backdrop"></div>
    <div class="container position-relative">
        <div class="shop-hero-content" data-aos="fade-up">
            <div class="hero-eyebrow mb-3">
                <span class="hero-eyebrow-dot"></span>
                {{ request('q') ? __('Search results for') : (request('category') ? __('Specialized catalogue') : __('Pro audiovisual equipment')) }}
            </div>
            <h1 class="shop-hero-title">
                @if(request('q'))
                    {{ __('Results for') }} <span class="text-brand-red">&laquo; {{ request('q') }} &raquo;</span>
                @elseif(request('category'))
                    <span class="text-brand-red">{{ $categories->where('slug', request('category'))->first()->name ?? __('Products') }}</span>
                @else
                    {{ __('Cameras & Cinema Optics') }}
                @endif
            </h1>
            <p class="shop-hero-sub">
                {{ __('Explore full-frame 4K/8K cameras...') }}
            </p>

            {{-- Breadcrumb --}}
            <nav class="shop-breadcrumb mt-4" aria-label="breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i> {{ __('Home') }}</a>
                <span class="shop-bc-sep mx-2 opacity-50">/</span>
                <a href="{{ route('shop.index') }}">{{ __('Shop') }}</a>
                @if(request('category'))
                    <span class="shop-bc-sep mx-2 opacity-50">/</span>
                    <span class="text-brand-black fw-bold">{{ $categories->where('slug', request('category'))->first()->name ?? __('Category') }}</span>
                @endif
            </nav>
        </div>
    </div>
</section>

{{-- =============================================
     MAIN SHOP LAYOUT
     ============================================= --}}
<section class="shop-body">
    <div class="container px-2 px-md-3">
        {{-- ── MOBILE CATEGORY HORIZONTAL SCROLLER ── --}}
        @php
            $catIcons = [
                'cameras-hybrides'       => 'fa-camera',
                'objectifs-optiques'     => 'fa-circle-notch',
                'stabilisateurs-gimbals' => 'fa-video',
                'eclairage-studio'       => 'fa-lightbulb',
                'audio-micros-sans-fil'  => 'fa-microphone',
                'drones-cine'            => 'fa-helicopter',
            ];
        @endphp
        <div class="shop-mobile-categories-wrap d-lg-none">
            <div class="shop-mobile-cat-scroll">
                <button type="button" class="mobile-cat-pill {{ !request('category') ? 'active' : '' }}" data-slug="">
                    <i class="fas fa-th-large"></i>
                    <span>{{ __('All') }}</span>
                </button>
                @foreach($categories as $cat)
                    @php $icon = $catIcons[$cat->slug] ?? 'fa-tag'; @endphp
                    <button type="button" class="mobile-cat-pill {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}">
                        <i class="fas {{ $icon }}"></i>
                        <span>{{ $cat->name }}</span>
                        @if($cat->products_count > 0)
                            <span class="mobile-cat-badge">{{ $cat->products_count }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── MOBILE STICKY CONTROL TOOLBAR (Filters, Sort, View Toggle) ── --}}
        <div class="shop-mobile-toolbar d-lg-none">
            <div class="d-flex align-items-center justify-content-between gap-2">
                {{-- Filter trigger button --}}
                <button type="button" class="btn-mobile-tool" data-bs-toggle="offcanvas" data-bs-target="#shopFilterSheet" id="mobileFilterTrigger">
                    <i class="fas fa-sliders-h text-danger"></i>
                    <span>{{ __('Filter') }}</span>
                    <span class="badge rounded-pill bg-danger d-none" id="mobileFilterBadge">0</span>
                </button>

                {{-- Sort Dropdown --}}
                <div class="dropdown flex-grow-1">
                    <button class="btn-mobile-tool w-100 justify-content-between" type="button" id="mobileSortDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-flex align-items-center gap-1 text-truncate">
                            <i class="fas fa-sort-amount-down text-muted"></i>
                            <span id="mobileSortLabel">{{ __('Newest') }}</span>
                        </span>
                        <i class="fas fa-chevron-down opacity-50 ms-1" style="font-size: 0.65rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2" aria-labelledby="mobileSortDropdownBtn" style="z-index: 1095;">
                        <li><button class="dropdown-item rounded-3 py-2 mobile-sort-item {{ request('sort') == 'newest' || !request('sort') ? 'active' : '' }}" type="button" data-sort="newest"><i class="fas fa-clock me-2"></i>{{ __('Newest') }}</button></li>
                        <li><button class="dropdown-item rounded-3 py-2 mobile-sort-item {{ request('sort') == 'price_asc' ? 'active' : '' }}" type="button" data-sort="price_asc"><i class="fas fa-arrow-up-1-9 me-2"></i>{{ __('Price: Low to High') }}</button></li>
                        <li><button class="dropdown-item rounded-3 py-2 mobile-sort-item {{ request('sort') == 'price_desc' ? 'active' : '' }}" type="button" data-sort="price_desc"><i class="fas fa-arrow-down-9-1 me-2"></i>{{ __('Price: High to Low') }}</button></li>
                    </ul>
                </div>

                {{-- View Mode Toggle (2-cols vs 1-col) --}}
                <button type="button" class="btn-mobile-tool btn-mobile-view-toggle" id="mobileViewToggle" title="Basculer 1 colonne / 2 colonnes" aria-label="Affichage">
                    <i class="fas fa-th-large" id="viewToggleIcon"></i>
                </button>
            </div>

            {{-- Result Count & Removable Filter Chips --}}
            <div class="mobile-active-strip d-flex align-items-center justify-content-between pt-2">
                <div class="mobile-results-count text-muted">
                    <span id="mobileProductTotal" class="fw-bold text-dark">{{ $products->total() }}</span> {{ __('items found') }}
                </div>
                <div class="mobile-chips-scroll" id="mobileActiveChipsContainer"></div>
            </div>
        </div>

        <div class="row gx-2 gx-lg-5 gy-4">
            {{-- ── SIDEBAR (Desktop) ── --}}
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
                                {{ $categories->where('slug', request('category'))->first()->name ?? __('Products') }}
                            @else
                                {{ __('All equipment') }}
                            @endif
                        </span>
                        <span class="shop-toolbar-count" id="desktopProductTotalBadge">{{ $products->total() }} {{ __('products') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="shop-sort-label">{{ __('Sort by:') }}</label>
                        <select class="shop-sort-select" id="sortSelect">
                            <option value="newest"  {{ request('sort') == 'newest'     ? 'selected' : '' }}>{{ __('Newest') }}</option>
                            <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Active Filters (Desktop) --}}
                <div class="shop-active-filters mb-4 {{ (request('q') || request('category') || request('min_price') || request('max_price')) ? '' : 'd-none' }}" id="desktopActiveFilters">
                    <span class="shop-active-label">{{ __('Active filters:') }}</span>
                    <span id="desktopActiveFilterChips">
                        @if(request('q'))
                            <span class="shop-filter-tag">{{ __('Search:') }} {{ request('q') }}</span>
                        @endif
                        @if(request('category'))
                            <span class="shop-filter-tag">{{ __('Category:') }} {{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}</span>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <span class="shop-filter-tag">{{ __('Price:') }} {{ request('min_price', '0') }} — {{ request('max_price', '∞') }} DH</span>
                        @endif
                    </span>
                    <a href="#" class="shop-clear-link" id="desktopResetFiltersBtn">
                        <i class="fas fa-times me-1"></i>{{ __('Clear all') }}
                    </a>
                </div>

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

{{-- ── MOBILE DEDICATED BOTTOM SHEET FILTER DRAWER ── --}}
<div class="offcanvas offcanvas-bottom shop-bottom-sheet d-lg-none" tabindex="-1" id="shopFilterSheet" aria-labelledby="shopFilterSheetLabel">
    <div class="sheet-drag-handle"></div>
    <div class="offcanvas-header border-bottom py-3 px-4">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-sliders-h text-danger fs-5"></i>
            <h5 class="offcanvas-title fw-bold mb-0" id="shopFilterSheetLabel">{{ __('Filters & Options') }}</h5>
        </div>
        <button type="button" class="btn-reset-filters text-danger border-0 bg-transparent fw-bold small" id="sheetResetBtn">
            {{ __('Reset') }}
        </button>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body p-4">
        {{-- Section 1: Search inside shop --}}
        <div class="filter-section mb-4">
            <label class="filter-section-title"><i class="fas fa-search me-2 text-danger"></i>{{ __('Search in shop') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control bg-light border-start-0 ps-1" id="sheetSearchInput" placeholder="{{ __('Model, body, brand...') }}" value="{{ request('q') }}">
            </div>
        </div>

        <div class="filter-section mb-4">
            <label class="filter-section-title"><i class="fas fa-th-large me-2 text-danger"></i>{{ __('Categories') }}</label>
            <div class="d-flex flex-wrap gap-2" id="sheetCategoryChips">
                <button type="button" class="sheet-cat-chip {{ !request('category') ? 'active' : '' }}" data-slug="">
                    {{ __('All') }} ({{ \App\Models\Product::where('status','active')->count() }})
                </button>
                @foreach($categories as $cat)
                <button type="button" class="sheet-cat-chip {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}">
                    {{ $cat->name }} ({{ $cat->products_count }})
                </button>
                @endforeach
            </div>
        </div>

        <div class="filter-section mb-4">
            <label class="filter-section-title"><i class="fas fa-tag me-2 text-danger"></i>{{ __('Budget (MAD)') }}</label>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <input type="number" class="form-control rounded-3" id="sheetMinPrice" placeholder="Min MAD" value="{{ request('min_price') }}" min="0">
                </div>
                <div class="col-6">
                    <input type="number" class="form-control rounded-3" id="sheetMaxPrice" placeholder="Max MAD" value="{{ request('max_price') }}" min="0">
                </div>
            </div>
            <div class="d-flex flex-wrap gap-1 mt-2">
                <button type="button" class="sheet-budget-chip" data-min="0" data-max="5000">&lt; 5,000 DH</button>
                <button type="button" class="sheet-budget-chip" data-min="5000" data-max="20000">5,000 – 20,000 DH</button>
                <button type="button" class="sheet-budget-chip" data-min="20000" data-max="">&gt; 20,000 DH</button>
            </div>
        </div>

        <div class="filter-section mb-4">
            <label class="filter-section-title"><i class="fas fa-sort-amount-down me-2 text-danger"></i>{{ __('Sort by') }}</label>
            <div class="d-flex flex-column gap-2" id="sheetSortGroup">
                <label class="sheet-radio-tile {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}">
                    <input type="radio" name="sheet_sort_radio" value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'checked' : '' }}>
                    <span>{{ __('Newest (New arrivals)') }}</span>
                    <i class="fas fa-check check-icon"></i>
                </label>
                <label class="sheet-radio-tile {{ request('sort') == 'price_asc' ? 'selected' : '' }}">
                    <input type="radio" name="sheet_sort_radio" value="price_asc" {{ request('sort') == 'price_asc' ? 'checked' : '' }}>
                    <span>{{ __('Price: Low to High (Cheapest)') }}</span>
                    <i class="fas fa-check check-icon"></i>
                </label>
                <label class="sheet-radio-tile {{ request('sort') == 'price_desc' ? 'selected' : '' }}">
                    <input type="radio" name="sheet_sort_radio" value="price_desc" {{ request('sort') == 'price_desc' ? 'checked' : '' }}>
                    <span>{{ __('Price: High to Low (Premium)') }}</span>
                    <i class="fas fa-check check-icon"></i>
                </label>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer p-3 bg-white border-top">
        <button class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-lg" id="sheetApplyBtn">
            {{ __('Apply filters') }}
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Shop State Manager
const shopState = {
    category: "{{ request('category', '') }}",
    sort: "{{ request('sort', 'newest') }}",
    q: "{{ request('q', '') }}",
    min_price: "{{ request('min_price', '') }}",
    max_price: "{{ request('max_price', '') }}",
    viewMode: localStorage.getItem('camera_shop_view') || (window.innerWidth < 768 ? 'grid-1' : 'grid-2')
};

// Category Titles Map
const categoryNamesMap = {
    '': '{{ addslashes(__('All equipment')) }}',
    @foreach($categories as $cat)
        '{{ $cat->slug }}': '{{ addslashes($cat->name) }}',
    @endforeach
};

// Sort Labels Map
const sortLabelsMap = {
    'newest': '{{ __('Newest') }}',
    'price_asc': '{{ __('Price: Low to High') }}',
    'price_desc': '{{ __('Price: High to Low') }}'
};

function getParams() {
    const p = new URLSearchParams();
    if (shopState.category) p.append('category', shopState.category);
    if (shopState.sort && shopState.sort !== 'newest') p.append('sort', shopState.sort);
    if (shopState.q) p.append('q', shopState.q);
    if (shopState.min_price) p.append('min_price', shopState.min_price);
    if (shopState.max_price) p.append('max_price', shopState.max_price);
    return p;
}

function updateUIState() {
    // 1. Category scroller pills & sheet chips
    document.querySelectorAll('.mobile-cat-pill, .sheet-cat-chip, .shop-cat-link').forEach(el => {
        const slug = el.getAttribute('data-slug');
        if (slug === shopState.category) {
            el.classList.add('active');
        } else {
            el.classList.remove('active');
        }
    });

    // 2. Desktop title & Sort Selects
    const titleEl = document.getElementById('categoryTitle');
    if (titleEl) {
        titleEl.textContent = categoryNamesMap[shopState.category] || '{{ __('Products') }}';
    }

    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) sortSelect.value = shopState.sort;

    // Mobile sort label
    const mobileSortLabel = document.getElementById('mobileSortLabel');
    if (mobileSortLabel) {
        mobileSortLabel.textContent = sortLabelsMap[shopState.sort] || '{{ __('Sort by') }}';
    }

    // Mobile sort dropdown items active class
    document.querySelectorAll('.mobile-sort-item').forEach(item => {
        if (item.getAttribute('data-sort') === shopState.sort) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    // Sheet radio tiles
    document.querySelectorAll('.sheet-radio-tile').forEach(tile => {
        const radio = tile.querySelector('input');
        if (radio && radio.value === shopState.sort) {
            radio.checked = true;
            tile.classList.add('selected');
        } else {
            if (radio) radio.checked = false;
            tile.classList.remove('selected');
        }
    });

    // 3. Inputs sync
    const desktopSearch = document.querySelector('input[name="q"].shop-search-input');
    if (desktopSearch) desktopSearch.value = shopState.q;
    const sheetSearch = document.getElementById('sheetSearchInput');
    if (sheetSearch) sheetSearch.value = shopState.q;

    const desktopMin = document.querySelector('input[name="min_price"].shop-price-input');
    if (desktopMin) desktopMin.value = shopState.min_price;
    const sheetMin = document.getElementById('sheetMinPrice');
    if (sheetMin) sheetMin.value = shopState.min_price;

    const desktopMax = document.querySelector('input[name="max_price"].shop-price-input');
    if (desktopMax) desktopMax.value = shopState.max_price;
    const sheetMax = document.getElementById('sheetMaxPrice');
    if (sheetMax) sheetMax.value = shopState.max_price;

    // 4. Active filters count badge on mobile
    let activeFilterCount = 0;
    if (shopState.category) activeFilterCount++;
    if (shopState.q) activeFilterCount++;
    if (shopState.min_price || shopState.max_price) activeFilterCount++;
    if (shopState.sort && shopState.sort !== 'newest') activeFilterCount++;

    const filterBadge = document.getElementById('mobileFilterBadge');
    if (filterBadge) {
        if (activeFilterCount > 0) {
            filterBadge.textContent = activeFilterCount;
            filterBadge.classList.remove('d-none');
        } else {
            filterBadge.classList.add('d-none');
        }
    }

    // 5. Render active filter chips on mobile and desktop
    renderActiveFilterChips();

    // 6. View Mode
    applyViewMode(shopState.viewMode);
}

function renderActiveFilterChips() {
    const mobileContainer = document.getElementById('mobileActiveChipsContainer');
    let chipsHtml = '';

    if (shopState.category) {
        const catName = categoryNamesMap[shopState.category] || shopState.category;
        chipsHtml += `<span class="mobile-chip" onclick="removeFilter('category')">${catName} <i class="fas fa-times ms-1"></i></span>`;
    }
    if (shopState.q) {
        chipsHtml += `<span class="mobile-chip" onclick="removeFilter('q')">« ${shopState.q} » <i class="fas fa-times ms-1"></i></span>`;
    }
    if (shopState.min_price || shopState.max_price) {
        const min = shopState.min_price || '0';
        const max = shopState.max_price || '∞';
        chipsHtml += `<span class="mobile-chip" onclick="removeFilter('price')">${min}-${max} DH <i class="fas fa-times ms-1"></i></span>`;
    }
    if (chipsHtml) {
        chipsHtml += `<span class="mobile-chip-clear" onclick="resetAllFilters()">{{ __('Clear all') }}</span>`;
    }

    if (mobileContainer) {
        mobileContainer.innerHTML = chipsHtml;
    }
}

function removeFilter(type) {
    if (type === 'category') shopState.category = '';
    if (type === 'q') shopState.q = '';
    if (type === 'price') { shopState.min_price = ''; shopState.max_price = ''; }
    fetchProducts();
}

function resetAllFilters() {
    shopState.category = '';
    shopState.q = '';
    shopState.min_price = '';
    shopState.max_price = '';
    shopState.sort = 'newest';
    fetchProducts();
}

function applyViewMode(mode) {
    const gridContainer = document.getElementById('productGridContainer');
    const toggleIcon = document.getElementById('viewToggleIcon');
    const toggleBtn = document.getElementById('mobileViewToggle');
    if (!gridContainer) return;

    if (mode === 'grid-1') {
        gridContainer.classList.add('shop-grid-1col');
        if (toggleIcon) toggleIcon.className = 'fas fa-th-large';
        if (toggleBtn) {
            toggleBtn.setAttribute('title', 'Passer en grille (2 colonnes)');
            toggleBtn.setAttribute('aria-label', 'Passer en grille (2 colonnes)');
        }
    } else {
        gridContainer.classList.remove('shop-grid-1col');
        if (toggleIcon) toggleIcon.className = 'fas fa-square';
        if (toggleBtn) {
            toggleBtn.setAttribute('title', 'Passer en grand format (1 colonne)');
            toggleBtn.setAttribute('aria-label', 'Passer en grand format (1 colonne)');
        }
    }
}

function fetchProducts(url = "{{ route('shop.index') }}") {
    const grid = document.getElementById('productGridContainer');
    const loader = document.getElementById('loader');
    if (!grid) return;

    grid.style.opacity = '0.35';
    if (loader) loader.classList.remove('d-none');

    const params = getParams();
    const fetchUrl = url.includes('?') ? url : `${url}?${params.toString()}`;
    window.history.pushState(null, '', fetchUrl);

    fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            grid.innerHTML = html;
            grid.style.opacity = '1';
            if (loader) loader.classList.add('d-none');

            // Update item count numbers
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const paginationCountMatch = html.match(/(\d+)\s+produit/i);
            
            // Re-apply view mode on new content
            applyViewMode(shopState.viewMode);
            attachPaginationListeners();
            updateUIState();

            // Smooth scroll on mobile if triggered from sheet or filters
            if (window.innerWidth < 992) {
                const mobileToolbar = document.querySelector('.shop-mobile-toolbar');
                if (mobileToolbar) {
                    mobileToolbar.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        })
        .catch(err => {
            console.error('Fetch products error:', err);
            grid.style.opacity = '1';
            if (loader) loader.classList.add('d-none');
        });
}

function attachPaginationListeners() {
    document.querySelectorAll('.shop-pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            fetchProducts(this.href);
            window.scrollTo({ top: 150, behavior: 'smooth' });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateUIState();
    attachPaginationListeners();

    // 1. Mobile Category Scroller clicks
    document.querySelectorAll('.mobile-cat-pill').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            shopState.category = this.getAttribute('data-slug') || '';
            fetchProducts();
            // Scroll selected pill into view
            this.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        });
    });

    // 2. Desktop Category clicks
    document.querySelectorAll('.shop-cat-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            shopState.category = this.getAttribute('data-slug') || '';
            fetchProducts();
        });
    });

    // 3. Desktop Sort Select
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            shopState.sort = this.value;
            fetchProducts();
        });
    }

    // 4. Mobile Sort Dropdown Items
    document.querySelectorAll('.mobile-sort-item').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            shopState.sort = this.getAttribute('data-sort');
            fetchProducts();
        });
    });

    // 5. Mobile View Mode Toggle (2-cols vs 1-col)
    const viewToggleBtn = document.getElementById('mobileViewToggle');
    if (viewToggleBtn) {
        viewToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            shopState.viewMode = (shopState.viewMode === 'grid-2') ? 'grid-1' : 'grid-2';
            localStorage.setItem('camera_shop_view', shopState.viewMode);
            applyViewMode(shopState.viewMode);
        });
    }

    // 6. Mobile Sheet Category Chips
    document.querySelectorAll('.sheet-cat-chip').forEach(chip => {
        chip.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sheet-cat-chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            shopState.category = this.getAttribute('data-slug') || '';
        });
    });

    // 7. Mobile Sheet Budget Chips
    document.querySelectorAll('.sheet-budget-chip').forEach(chip => {
        chip.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sheetMinPrice').value = this.getAttribute('data-min');
            document.getElementById('sheetMaxPrice').value = this.getAttribute('data-max');
        });
    });

    // 8. Mobile Sheet Radio Tiles
    document.querySelectorAll('.sheet-radio-tile').forEach(tile => {
        tile.addEventListener('click', function() {
            document.querySelectorAll('.sheet-radio-tile').forEach(t => t.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input');
            if (radio) {
                radio.checked = true;
                shopState.sort = radio.value;
            }
        });
    });

    // 9. Mobile Sheet Apply Button
    const sheetApplyBtn = document.getElementById('sheetApplyBtn');
    if (sheetApplyBtn) {
        sheetApplyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            shopState.q = document.getElementById('sheetSearchInput').value.trim();
            shopState.min_price = document.getElementById('sheetMinPrice').value.trim();
            shopState.max_price = document.getElementById('sheetMaxPrice').value.trim();

            const checkedRadio = document.querySelector('input[name="sheet_sort_radio"]:checked');
            if (checkedRadio) shopState.sort = checkedRadio.value;

            // Close offcanvas
            const sheetEl = document.getElementById('shopFilterSheet');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(sheetEl);
            if (bsOffcanvas) bsOffcanvas.hide();

            fetchProducts();
        });
    }

    // 10. Mobile Sheet Reset Button
    const sheetResetBtn = document.getElementById('sheetResetBtn');
    if (sheetResetBtn) {
        sheetResetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sheetSearchInput').value = '';
            document.getElementById('sheetMinPrice').value = '';
            document.getElementById('sheetMaxPrice').value = '';
            shopState.category = '';
            document.querySelectorAll('.sheet-cat-chip').forEach(c => {
                if (c.getAttribute('data-slug') === '') c.classList.add('active');
                else c.classList.remove('active');
            });
            resetAllFilters();
            const sheetEl = document.getElementById('shopFilterSheet');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(sheetEl);
            if (bsOffcanvas) bsOffcanvas.hide();
        });
    }

    // 11. Desktop Reset Filters
    const desktopResetBtn = document.getElementById('desktopResetFiltersBtn');
    if (desktopResetBtn) {
        desktopResetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            resetAllFilters();
        });
    }

    // 12. Desktop Forms
    const desktopPriceForm = document.getElementById('priceFilterForm');
    if (desktopPriceForm) {
        desktopPriceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            shopState.min_price = this.querySelector('input[name="min_price"]').value.trim();
            shopState.max_price = this.querySelector('input[name="max_price"]').value.trim();
            fetchProducts();
        });
    }

    const desktopSearchForm = document.getElementById('searchForm');
    if (desktopSearchForm) {
        desktopSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            shopState.q = this.querySelector('input[name="q"]').value.trim();
            fetchProducts();
        });
    }
});
</script>
@endpush
