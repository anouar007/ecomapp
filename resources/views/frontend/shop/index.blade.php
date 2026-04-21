@extends('layouts.frontend')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endpush

@php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->translated_name . ' — Moubdi3oun')
        : (request('q') ? __('Search Results') . ' — ' . setting('app_name', 'Moubdi3oun') : __('Shop') . ' — ' . setting('app_name', 'Moubdi3oun'));
@endphp

@section('meta_title', $pageTitle)

@section('content')


{{-- =============================================
     MAIN SHOP LAYOUT
     ============================================= --}}
<section class="shop-body bg-light section-py">
    <div class="container">
        
        {{-- Mobile Filter Trigger --}}
        <div class="d-lg-none mb-4">
            <button class="btn btn-dark w-100 rounded-pill py-3 fw-black text-uppercase ls-1 d-flex align-items-center justify-content-center gap-2" 
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">
                <i class="fas fa-filter"></i> {{ __('Filter') }}
            </button>
        </div>

        <div class="row g-4">

            {{-- ── SIDEBAR (Compact & Sticky) ── --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="shop-sidebar">
                    
                    {{-- Price Filter (Pinned at top of sidebar) --}}
                    <div class="shop-filter-group">
                        <h6 class="filter-title">{{ __('Price Range') }} <i class="fas fa-tag small opacity-50"></i></h6>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="number" id="minPriceInput" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 py-2 text-center"
                                   placeholder="Min" value="{{ request('min_price') }}">
                            <input type="number" id="maxPriceInput" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 py-2 text-center"
                                   placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                        <button class="btn btn-dark w-100 mt-3 rounded-pill py-2 fw-black text-uppercase ls-1" id="applyPriceFilter" style="font-size: 0.75rem;">
                            {{ __('Apply Filter') }}
                        </button>
                    </div>

                    {{-- Advanced Filters (Color) --}}
                    @if($availableColors->count() > 0)
                    <div class="shop-filter-group">
                        <h6 class="filter-title">{{ __('Colors') }} <i class="fas fa-palette small opacity-50"></i></h6>
                        <div class="color-filter-grid">
                            @foreach($availableColors as $color)
                                <div class="color-swatch filter-checkbox color-filter-item" 
                                     data-type="colors"
                                     data-value="{{ $color->color }}"
                                     style="background: {{ $color->color_code ?: '#eee' }}" 
                                     title="{{ $color->color }}">
                                    <i class="fas fa-check color-swatch-check"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Advanced Filters (Size) --}}
                    @if($availableSizes->count() > 0)
                    <div class="shop-filter-group">
                        <h6 class="filter-title">{{ __('Dimensions') }} <i class="fas fa-ruler-combined small opacity-50"></i></h6>
                        <div class="size-filter-grid">
                            @foreach($availableSizes as $size)
                                <div class="size-pill filter-checkbox size-filter-item"
                                     data-type="sizes"
                                     data-value="{{ $size }}">
                                    {{ $size }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            {{-- ── MAIN CONTENT ── --}}
            <div class="col-lg-9">
                
                {{-- Legend Top Bar (Search & Collections) --}}
                <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="search-input-wrapper">
                                <input type="text" id="searchInput" class="form-control border-0 bg-light rounded-pill px-4 py-3" 
                                       placeholder="{{ __('Type to search...') }}" value="{{ request('q') }}">
                                <i class="fas fa-search position-absolute end-0 top-50 translate-middle-y me-4 opacity-50"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select border-0 bg-light rounded-pill px-4 py-3 fw-bold" id="categorySelect">
                                <option value="">{{ __('All Collections') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->translated_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select border-0 bg-light rounded-pill px-4 py-3 fw-bold" id="sortSelect">
                                <option value="newest"  {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('Newest Arrivals') }}</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Active Filter Summary --}}
                <div id="activeFiltersContainer" class="d-flex flex-wrap gap-2 mb-3 px-2"></div>

                {{-- Toolbar (View Mode) --}}
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <div class="view-toggle">
                        <div class="toggle-btn active" data-view="grid"><i class="fas fa-th-large"></i> {{ __('Gallery') }}</div>
                        <div class="toggle-btn" data-view="list"><i class="fas fa-list"></i> {{ __('List View') }}</div>
                    </div>
                    <span class="text-muted small fw-bold"><span id="productTotal">{{ $products->total() }}</span> {{ __('items found') }}</span>
                </div>

                {{-- Product Container --}}
                <div id="productGridContainer" class="position-relative min-vh-50">
                    @include('frontend.shop.partials.product-grid')
                </div>

                {{-- Legend Loader (Skeleton) --}}
                <div id="shopLoader" class="d-none">
                    <div class="row g-4">
                        @foreach(range(1, 6) as $i)
                            <div class="col-md-4">
                                <div class="skeleton skeleton-card"></div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- Mobile Filter Offcanvas --}}
<div class="offcanvas offcanvas-start border-0" tabindex="-1" id="mobileFilters">
    <div class="offcanvas-header bg-dark text-white py-4">
        <h5 class="offcanvas-title fw-black text-uppercase ls-1">{{ __('Filter') }}</h5>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-4">
        <div class="shop-sidebar">
             {{-- Price Filter --}}
             <div class="shop-filter-group mb-5">
                <h6 class="filter-title">{{ __('Price Range') }}</h6>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="minPriceInputMobile" class="form-control border-0 bg-light rounded-pill px-3 py-2 text-center"
                           placeholder="Min" value="{{ request('min_price') }}">
                    <input type="number" id="maxPriceInputMobile" class="form-control border-0 bg-light rounded-pill px-3 py-2 text-center"
                           placeholder="Max" value="{{ request('max_price') }}">
                </div>
                <button class="btn btn-dark w-100 mt-3 rounded-pill py-3 fw-black text-uppercase ls-1" id="applyPriceFilterMobile">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Color Filter --}}
            @if($availableColors->count() > 0)
            <div class="shop-filter-group mb-5">
                <h6 class="filter-title">{{ __('Colors') }}</h6>
                <div class="color-filter-grid">
                    @foreach($availableColors as $color)
                        <div class="color-swatch filter-checkbox color-filter-item" 
                             data-type="colors"
                             data-value="{{ $color->color }}"
                             style="background: {{ $color->color_code ?: '#eee' }}">
                            <i class="fas fa-check color-swatch-check"></i>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Size Filter --}}
            @if($availableSizes->count() > 0)
            <div class="shop-filter-group mb-5">
                <h6 class="filter-title">{{ __('Dimensions') }}</h6>
                <div class="size-filter-grid">
                    @foreach($availableSizes as $size)
                        <div class="size-pill filter-checkbox size-filter-item"
                             data-type="sizes"
                             data-value="{{ $size }}">
                            {{ $size }}
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
<script>
    let filters = {
        category: "{{ request('category', '') }}",
        q: "{{ request('q', '') }}",
        min_price: "{{ request('min_price', '') }}",
        max_price: "{{ request('max_price', '') }}",
        sort: "{{ request('sort', 'newest') }}",
        colors: @json(request('colors', [])),
        sizes: @json(request('sizes', []))
    };

    let currentViewMode = 'grid';

    function applyViewMode() {
        const container = document.getElementById('productGridContainer');
        if (!container) return;
        const row = container.querySelector('.row');
        if (!row) return;

        if (currentViewMode === 'list') {
            row.classList.add('list-view');
            row.querySelectorAll('.col-6, .col-md-4').forEach(col => {
                col.classList.remove('col-6', 'col-md-4');
                col.classList.add('col-12');
            });
        } else {
            row.classList.remove('list-view');
            row.querySelectorAll('.col-12').forEach(col => {
                col.classList.remove('col-12');
                col.classList.add('col-6', 'col-md-4');
            });
        }
    }

    function renderActiveFilters() {
        const container = document.getElementById('activeFiltersContainer');
        if (!container) return;
        container.innerHTML = '';
        
        const addPill = (label, type, value = null) => {
            const pill = document.createElement('div');
            pill.className = 'filter-pill';
            pill.innerHTML = `${label} <i class="fas fa-times ms-2 opacity-50"></i>`;
            pill.onclick = () => {
                if (value === null) {
                    if (type === 'price_range') {
                        filters.min_price = '';
                        filters.max_price = '';
                        document.getElementById('minPriceInput').value = '';
                        document.getElementById('maxPriceInput').value = '';
                    } else {
                        filters[type] = '';
                        if (type === 'category') {
                             const sel = document.getElementById('categorySelect');
                             if (sel) sel.value = '';
                        }
                        if (type === 'q') {
                            const inp = document.getElementById('searchInput');
                            if (inp) inp.value = '';
                        }
                    }
                } else {
                    filters[type] = filters[type].filter(v => v !== value);
                    const el = document.querySelector(`[data-type="${type}"][data-value="${value}"]`);
                    if (el) el.classList.remove('active');
                }
                fetchProducts();
            };
            container.appendChild(pill);
        };

        if (filters.q) addPill(`{{ __('Search') }}: ${filters.q}`, 'q');
        
        if (filters.category) {
            const sel = document.getElementById('categorySelect');
            let catName = filters.category;
            if (sel) {
                const opt = Array.from(sel.options).find(o => o.value === filters.category);
                if (opt) catName = opt.text;
            }
            addPill(`{{ __('Category') }}: ${catName}`, 'category');
        }

        if (filters.min_price || filters.max_price) {
            addPill(`{{ __('Price') }}: ${filters.min_price || 0} - ${filters.max_price || '∞'}`, 'price_range');
        }

        filters.colors.forEach(c => addPill(`{{ __('Color') }}: ${c}`, 'colors', c));
        filters.sizes.forEach(s => addPill(`{{ __('Size') }}: ${s}`, 'sizes', s));

        if (container.children.length > 0) {
            const clearAll = document.createElement('a');
            clearAll.href = '#';
            clearAll.className = 'small text-muted text-decoration-none ms-2 align-self-center hover-accent';
            clearAll.innerText = '{{ __("Reset All") }}';
            clearAll.onclick = (e) => { e.preventDefault(); resetFilters(); };
            container.appendChild(clearAll);
        }
    }

    function resetFilters() {
        filters = { category: '', q: '', min_price: '', max_price: '', sort: 'newest', colors: [], sizes: [] };
        document.querySelectorAll('.filter-checkbox').forEach(el => el.classList.remove('active'));
        const catSel = document.getElementById('categorySelect');
        if (catSel) catSel.value = '';
        const searchInp = document.getElementById('searchInput');
        if (searchInp) searchInp.value = '';
        const minP = document.getElementById('minPriceInput');
        if (minP) minP.value = '';
        const maxP = document.getElementById('maxPriceInput');
        if (maxP) maxP.value = '';
        fetchProducts();
    }

    function updateUrl() {
        const p = new URLSearchParams();
        for (let key in filters) {
            if (Array.isArray(filters[key])) {
                filters[key].forEach(val => p.append(key + '[]', val));
            } else if (filters[key]) {
                p.append(key, filters[key]);
            }
        }
        const newUrl = `${window.location.pathname}?${p.toString()}`;
        window.history.pushState(null, '', newUrl);
        return newUrl;
    }

    async function fetchProducts() {
        const grid = document.getElementById('productGridContainer');
        const loader = document.getElementById('shopLoader');
        
        // Show subtle loading state
        if (grid) grid.style.opacity = '0.6';
        if (loader) loader.classList.remove('d-none');
        
        renderActiveFilters();
        const url = updateUrl();
        
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            
            if (grid) {
                grid.innerHTML = html;
                applyViewMode();
            }
            
            // Re-trigger animations for new items
            setTimeout(() => {
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
            }, 100);
            
            const countMatch = html.match(/data-total-count="(\d+)"/);
            if (countMatch && document.getElementById('productTotal')) {
                document.getElementById('productTotal').innerText = countMatch[1];
            }
            
        } catch (err) {
            console.error('Fetch error:', err);
        } finally {
            // ALWAYS restore full opacity and hide loader
            if (grid) grid.style.opacity = '1';
            if (loader) loader.classList.add('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderActiveFilters();
        filters.colors.forEach(c => {
            const el = document.querySelector(`[data-type="colors"][data-value="${c}"]`);
            if (el) el.classList.add('active');
        });
        filters.sizes.forEach(s => {
            const el = document.querySelector(`[data-type="sizes"][data-value="${s}"]`);
            if (el) el.classList.add('active');
        });

        const catSel = document.getElementById('categorySelect');
        if (catSel) catSel.addEventListener('change', (e) => { filters.category = e.target.value; fetchProducts(); });

        const searchInp = document.getElementById('searchInput');
        if (searchInp) searchInp.addEventListener('input', _.debounce((e) => { filters.q = e.target.value; fetchProducts(); }, 500));

        const sortSel = document.getElementById('sortSelect');
        if (sortSel) sortSel.addEventListener('change', (e) => { filters.sort = e.target.value; fetchProducts(); });

        document.querySelectorAll('.filter-checkbox').forEach(el => {
            el.addEventListener('click', function() {
                const type = this.dataset.type;
                const val = this.dataset.value;
                this.classList.toggle('active');
                if (this.classList.contains('active')) filters[type].push(val);
                else filters[type] = filters[type].filter(i => i !== val);
                fetchProducts();
            });
        });

        const priceBtn = document.getElementById('applyPriceFilter');
        if (priceBtn) priceBtn.addEventListener('click', () => {
            filters.min_price = document.getElementById('minPriceInput').value;
            filters.max_price = document.getElementById('maxPriceInput').value;
            fetchProducts();
        });

        const priceBtnMobile = document.getElementById('applyPriceFilterMobile');
        if (priceBtnMobile) priceBtnMobile.addEventListener('click', () => {
            filters.min_price = document.getElementById('minPriceInputMobile').value;
            filters.max_price = document.getElementById('maxPriceInputMobile').value;
            fetchProducts();
            bootstrap.Offcanvas.getInstance(document.getElementById('mobileFilters')).hide();
        });

        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentViewMode = this.dataset.view;
                document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                applyViewMode();
            });
        });
    });

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.quick-add-btn');
        if (btn) {
            const id = btn.dataset.productId;
            btn.disabled = true;
            try {
                const res = await fetch(`/cart/add/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ quantity: 1 })
                });
                const data = await res.json();
                if (data.success) {
                    const badge = document.querySelector('.cart-badge');
                    if (badge) badge.innerText = data.cartCount;
                    if (window.Toastify) Toastify({ text: "{{ __('Added to cart!') }}", duration: 3000, backgroundColor: "var(--accent)" }).showToast();
                }
            } catch (err) { console.error(err); }
            finally { btn.disabled = false; }
        }
    });

</script>
@endpush
