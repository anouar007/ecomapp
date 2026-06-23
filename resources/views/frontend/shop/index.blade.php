@extends('layouts.frontend')
@section('meta_title', __('Shop') . ' — Ait Oumdis')

@push('styles')
<style>
.shop-hero{background:linear-gradient(135deg,#0d1f14,#1a5c38 55%,#3BB878);padding:64px 0 80px;position:relative;overflow:hidden}
.shop-hero::before{content:'';position:absolute;inset:0;background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.06) 1px,transparent 0);background-size:32px 32px}
.shop-wave{position:absolute;bottom:-1px;left:0;right:0}
.sidebar-card{background:#fff;border-radius:18px;padding:20px;margin-bottom:14px;box-shadow:0 2px 16px rgba(0,0,0,.05)}
.sidebar-label{font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:#9CA3AF;margin-bottom:12px}
.cpill{display:flex;align-items:center;justify-content:space-between;padding:9px 12px;border-radius:10px;text-decoration:none;font-size:.855rem;font-weight:600;color:#374151;transition:all .2s;border:1.5px solid transparent;margin-bottom:3px}
.cpill:hover{background:#e8f7ef;color:#3BB878}
.cpill.on{background:#3BB878;color:#fff;border-color:#3BB878;box-shadow:0 4px 12px rgba(59,184,120,.25)}
.cpill .badge{font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:100px;background:rgba(0,0,0,.08);color:inherit}
.cpill.on .badge{background:rgba(255,255,255,.25)}
.p-input{width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 10px;font-size:.85rem;text-align:center;outline:none;font-family:inherit;transition:border-color .2s}
.p-input:focus{border-color:#3BB878;box-shadow:0 0 0 3px rgba(59,184,120,.12)}
.toolbar{background:#fff;border-radius:16px;padding:13px 20px;box-shadow:0 2px 16px rgba(0,0,0,.05);margin-bottom:18px}
.sort-sel{border:1.5px solid #e5e7eb;border-radius:10px;padding:7px 12px;font-size:.84rem;font-weight:600;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border .2s;font-family:inherit}
.sort-sel:focus{border-color:#3BB878}
.fchip{display:inline-flex;align-items:center;gap:6px;background:#fff;border:1.5px solid #e5e7eb;border-radius:100px;padding:5px 13px;font-size:.78rem;font-weight:600;color:#374151}
.fchip a{color:#ef4444;text-decoration:none;font-size:1rem;line-height:1}
/* Card */
.pc{background:#fff;border-radius:20px;overflow:hidden;border:1.5px solid #f1f5f9;transition:transform .4s cubic-bezier(.34,1.56,.64,1),box-shadow .3s,border-color .3s;height:100%}
.pc:hover{transform:translateY(-8px);box-shadow:0 20px 50px rgba(59,184,120,.12);border-color:#bbf7d0}
.pc-img{position:relative;padding-top:118%;overflow:hidden}
.pc-img img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .7s cubic-bezier(.4,0,.2,1)}
.pc:hover .pc-img img{transform:scale(1.08)}
.pc-overlay{position:absolute;inset:0;background:rgba(59,184,120,.1);backdrop-filter:blur(2px);opacity:0;transition:opacity .3s;display:flex;align-items:center;justify-content:center;gap:8px}
.pc:hover .pc-overlay{opacity:1}
.pc-action{background:#fff;border:none;border-radius:100px;padding:8px 16px;font-size:.78rem;font-weight:700;cursor:pointer;color:#1F2937;transition:all .2s;font-family:inherit}
.pc-action:hover{background:#3BB878;color:#fff}
.pc-body{padding:15px}
.pc-cat{font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#3BB878;margin-bottom:3px}
.pc-name{font-size:.9rem;font-weight:700;color:#1F2937;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.4rem;text-decoration:none}
.pc-name:hover{color:#3BB878}
.pc-stars{font-size:.58rem;color:#f59e0b}
.pc-price{font-size:1rem;font-weight:800;color:#1F2937}
.pc-sale{font-size:1rem;font-weight:800;color:#3BB878}
.pc-old{font-size:.75rem;color:#9CA3AF;text-decoration:line-through}

/* Pagination */
.shop-pag .pagination{gap:4px}
.shop-pag .page-link{border-radius:10px!important;border:none;background:#fff;color:#374151;font-weight:600;min-width:38px;height:38px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,.06);transition:all .2s}
.shop-pag .page-link:hover{background:#e8f7ef;color:#3BB878}
.shop-pag .page-item.active .page-link{background:#3BB878;color:#fff;box-shadow:0 4px 14px rgba(59,184,120,.35)}
.grid-loading{opacity:.3;pointer-events:none;transition:opacity .3s}
.empty-box{background:#fff;border-radius:20px;padding:60px 20px;text-align:center}
.empty-ico{width:80px;height:80px;background:#e8f7ef;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem}
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="shop-hero">
    <div class="container position-relative" style="z-index:1">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7" data-aos="fade-right">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white opacity-60 text-decoration-none small">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item active text-white small">{{ __('Shop') }}</li>
                    </ol>
                </nav>
                <h1 class="text-white fw-900 lh-1 mb-3" style="font-size:clamp(2rem,4vw,3rem)">
                    {{ __('Our Natural Products') }}<br>
                    <span style="color:#a7f3c8">{{ __('from the Atlas Mountains') }}</span>
                </h1>
                <p class="text-white lh-lg mb-0" style="opacity:.72;max-width:480px;font-size:.95rem">
                    {{ __('Pure honey, argan oil, saffron & organic cosmetics — harvested with care and tradition.') }}
                </p>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end" data-aos="fade-left">
                {{-- Category icons removed --}}
            </div>
        </div>
    </div>
    <div class="shop-wave">
        <svg viewBox="0 0 1440 48" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display:block;width:100%;height:48px">
            <path d="M0,48 C480,0 960,48 1440,12 L1440,48 Z" fill="#f9fafb"/>
        </svg>
    </div>
</div>

{{-- SHOP BODY --}}
<div style="background:#f9fafb;min-height:80vh">
    <div class="container py-5">
        <div class="row g-4 align-items-start">

            {{-- SIDEBAR (desktop) --}}
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="sticky-top" style="top:95px">

                    {{-- Search --}}
                    <div class="sidebar-card">
                        <div class="sidebar-label">{{ __('Search') }}</div>
                        <div class="position-relative">
                            <input type="text" id="shopSearch" value="{{ request('q') }}"
                                   placeholder="{{ __('Search products...') }}"
                                   class="form-control border-0 bg-light rounded-3 pe-5" style="height:44px"
                                   autocomplete="off">
                            <button type="button" id="searchBtn"
                                    class="position-absolute end-0 top-0 h-100 px-3 border-0 bg-transparent"
                                    style="color:#3BB878">
                                <i class="fas fa-search small"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Categories --}}
                    <div class="sidebar-card">
                        <div class="sidebar-label">{{ __('Categories') }}</div>
                        <a href="#" class="cpill {{ !request('category') ? 'on' : '' }}"
                           data-filter="category" data-value="">
                            <span><i class="fas fa-th-large me-2 opacity-60" style="font-size:.75rem"></i>{{ __('All Products') }}</span>
                            <span class="badge">{{ $products->total() }}</span>
                        </a>
                        @foreach($allCategories as $cat)
                        <a href="#" class="cpill {{ request('category') == $cat->slug ? 'on' : '' }}"
                           data-filter="category" data-value="{{ $cat->slug }}" data-label="{{ $cat->translated_name }}">
                            <span class="text-truncate">{{ $cat->translated_name }}</span>
                            <span class="badge">{{ $cat->products->count() }}</span>
                        </a>
                        @endforeach
                    </div>

                    {{-- Price --}}
                    <div class="sidebar-card">
                        <div class="sidebar-label">{{ __('Price Range') }} ({{ __('DH') }})</div>
                        <div class="d-flex gap-2 align-items-center mb-3">
                            <input type="number" id="minPrice" value="{{ request('min_price') }}"
                                   placeholder="0" class="p-input" min="0">
                            <span class="text-muted fw-700 small">–</span>
                            <input type="number" id="maxPrice" value="{{ request('max_price') }}"
                                   placeholder="{{ __('max') }}" class="p-input" min="0">
                        </div>
                        <button type="button" id="applyPrice"
                                class="btn w-100 rounded-3 fw-700 py-2"
                                style="background:#3BB878;color:#fff;font-size:.84rem">
                            <i class="fas fa-check me-1"></i>{{ __('Apply Filter') }}
                        </button>
                    </div>

                    {{-- Clear --}}
                    <button type="button" id="clearFilters"
                            class="btn btn-outline-danger w-100 rounded-3 fw-700 small py-2"
                            style="display:{{ request()->hasAny(['category','q','sort','min_price','max_price']) ? 'block' : 'none' }}">
                        <i class="fas fa-times me-1"></i>{{ __('Clear All Filters') }}
                    </button>
                </div>
            </aside>

            {{-- MAIN AREA --}}
            <div class="col-lg-9">

                {{-- Toolbar --}}
                <div class="toolbar d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn border fw-700 rounded-3 py-2 px-3 d-lg-none"
                                style="font-size:.82rem" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">
                            <i class="fas fa-sliders-h me-1 text-green"></i>{{ __('Filters') }}
                        </button>
                        <p id="toolbar-info" class="mb-0 small text-muted">
                            @if(request('category'))
                                @php $activeCat = $allCategories->firstWhere('slug', request('category')); @endphp
                                <strong class="text-dark">{{ $activeCat?->translated_name }}</strong> &mdash;
                            @elseif(request('q'))
                                {{ __('Results for') }}: <strong class="text-dark">"{{ request('q') }}"</strong> &mdash;
                            @endif
                            <strong class="text-green">{{ $products->total() }}</strong> {{ __('products') }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="x-small text-muted fw-600 d-none d-sm-inline">{{ __('Sort:') }}</span>
                        <select id="sortSel" class="sort-sel">
                            <option value="newest"     {{ request('sort','newest')==='newest'    ? 'selected' : '' }}>{{ __('Newest') }}</option>
                            <option value="price_asc"  {{ request('sort')==='price_asc'          ? 'selected' : '' }}>{{ __('Price ↑') }}</option>
                            <option value="price_desc" {{ request('sort')==='price_desc'         ? 'selected' : '' }}>{{ __('Price ↓') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Active filter chips (JS-managed) --}}
                <div id="filter-chips" class="flex-wrap gap-2 mb-4"
                     style="display:{{ request()->hasAny(['category','q','min_price','max_price']) ? 'flex' : 'none' }}">
                </div>

                {{-- Products Grid --}}
                <div id="pgrid">
                    @include('frontend.shop.partials.product-grid')
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MOBILE FILTERS OFFCANVAS --}}
<div class="offcanvas offcanvas-start border-0 shadow-lg" id="mobileFilters" style="max-width:300px">
    <div class="offcanvas-header" style="background:#f9fafb;border-bottom:1px solid #f1f5f9">
        <h5 class="fw-800 mb-0"><i class="fas fa-sliders-h text-green me-2"></i>{{ __('Filters') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column gap-4">
        <div>
            <div class="sidebar-label">{{ __('Search') }}</div>
            <input type="text" id="mobSearch" placeholder="{{ __('Search...') }}"
                   value="{{ request('q') }}"
                   class="form-control rounded-3 border-light" autocomplete="off">
        </div>
        <div>
            <div class="sidebar-label">{{ __('Categories') }}</div>
            <div class="d-flex flex-column gap-1">
                <a href="#" class="cpill {{ !request('category') ? 'on' : '' }}"
                   data-filter="category" data-value="">{{ __('All Products') }}</a>
                @foreach($allCategories as $cat)
                <a href="#" class="cpill {{ request('category') == $cat->slug ? 'on' : '' }}"
                   data-filter="category" data-value="{{ $cat->slug }}" data-label="{{ $cat->translated_name }}">
                    {{ $cat->translated_name }}
                </a>
                @endforeach
            </div>
        </div>
        <div>
            <div class="sidebar-label">{{ __('Price') }} ({{ __('DH') }})</div>
            <div class="d-flex gap-2">
                <input type="number" id="mobMinPrice" value="{{ request('min_price') }}" placeholder="0"   class="p-input">
                <input type="number" id="mobMaxPrice" value="{{ request('max_price') }}" placeholder="{{ __('max') }}" class="p-input">
            </div>
        </div>
        <button type="button" id="mobApply" class="btn fw-700 rounded-3 py-2 w-100" style="background:#3BB878;color:#fff">
            {{ __('Apply Filters') }}
        </button>
        <button type="button" id="mobClear" class="btn btn-outline-secondary rounded-3 small fw-700 w-100">
            {{ __('Clear All') }}
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    /* ─── State ─── */
    const state = {
        q:         '{{ request('q') }}',
        category:  '{{ request('category') }}',
        sort:      '{{ request('sort', 'newest') }}',
        min_price: '{{ request('min_price') }}',
        max_price: '{{ request('max_price') }}',
        page:      '1',
    };

    const ENDPOINT = '{{ route('shop.index') }}';

    /* ─── Elements ─── */
    const pgrid      = document.getElementById('pgrid');
    const toolbarInfo = document.getElementById('toolbar-info');
    const chipsEl    = document.getElementById('filter-chips');
    const clearBtn   = document.getElementById('clearFilters');
    const sortSel    = document.getElementById('sortSel');

    /* ─── Core AJAX loader ─── */
    function load(newState, pushHistory) {
        // Merge state
        Object.assign(state, newState, { page: newState.page || '1' });

        // Build query string (skip empty values)
        const params = new URLSearchParams();
        Object.entries(state).forEach(([k,v]) => { if (v && v !== '1' || k === 'page' && v !== '1') if(v) params.set(k, v); });
        if (state.page && state.page !== '1') params.set('page', state.page);

        const url = ENDPOINT + '?' + params.toString();

        // UI: loading
        pgrid.style.opacity = '0.35';
        pgrid.style.pointerEvents = 'none';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            pgrid.innerHTML = data.grid_html;
            pgrid.style.opacity = '';
            pgrid.style.pointerEvents = '';

            // Update toolbar text
            renderToolbar(data.total_count);

            // Render chips
            renderChips();

            // Sync pills active state
            syncPills();

            // Show/hide clear button
            const hasFilters = state.q || state.category || state.min_price || state.max_price;
            if (clearBtn) clearBtn.style.display = hasFilters ? 'block' : 'none';

            // Bind pagination
            bindPagination();

            // Update URL
            if (pushHistory !== false) {
                history.pushState(Object.assign({}, state), '', url);
            }

            // Scroll grid into view smoothly
            pgrid.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            if (window.AOS) AOS.refresh();
        })
        .catch(() => {
            pgrid.style.opacity = '';
            pgrid.style.pointerEvents = '';
        });
    }

    /* ─── Render toolbar text ─── */
    function renderToolbar(count) {
        if (!toolbarInfo) return;
        let html = '';
        if (state.category) {
            const pill = document.querySelector(`.cpill[data-value="${state.category}"]`);
            const label = pill ? (pill.dataset.label || pill.textContent.trim()) : state.category;
            html += `<strong class="text-dark">${label}</strong> &mdash; `;
        } else if (state.q) {
            html += `{{ __('Results for') }}: <strong class="text-dark">"${state.q}"</strong> &mdash; `;
        }
        html += `<strong class="text-green">${count}</strong> {{ __('products') }}`;
        toolbarInfo.innerHTML = html;
    }

    /* ─── Render chips ─── */
    function renderChips() {
        if (!chipsEl) return;
        let html = '';

        if (state.category) {
            const pill = document.querySelector(`.cpill[data-value="${state.category}"]`);
            const label = pill ? (pill.dataset.label || pill.textContent.trim()) : state.category;
            html += `<span class="fchip"><i class="fas fa-th-large text-green" style="font-size:.62rem"></i>${label}<button class="chip-x" data-clear="category">×</button></span>`;
        }
        if (state.q) {
            html += `<span class="fchip"><i class="fas fa-search text-green" style="font-size:.62rem"></i>"${state.q}"<button class="chip-x" data-clear="q">×</button></span>`;
        }
        if (state.min_price || state.max_price) {
            html += `<span class="fchip"><i class="fas fa-tag text-green" style="font-size:.62rem"></i>${state.min_price||0}–${state.max_price||'∞'} {{ __('DH') }}<button class="chip-x" data-clear="price">×</button></span>`;
        }

        chipsEl.innerHTML = html;
        chipsEl.style.display = html ? 'flex' : 'none';

        // Chip remove buttons
        chipsEl.querySelectorAll('.chip-x').forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.clear;
                if (key === 'price') load({ min_price: '', max_price: '' });
                else load({ [key]: '' });
            });
        });
    }

    /* ─── Sync sidebar active states ─── */
    function syncPills() {
        document.querySelectorAll('.cpill[data-filter="category"]').forEach(p => {
            p.classList.toggle('on', p.dataset.value === state.category);
        });
    }

    /* ─── Bind pagination ─── */
    function bindPagination() {
        pgrid.querySelectorAll('.shop-pag .page-link').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const href = link.getAttribute('href');
                if (!href || href === '#') return;
                const pg = new URL(href, location.href).searchParams.get('page') || '1';
                load({ page: pg });
            });
        });
    }

    /* ═══════════════════════════
       EVENT BINDINGS
    ═══════════════════════════ */

    /* Category pills (sidebar + mobile) */
    document.addEventListener('click', e => {
        const pill = e.target.closest('.cpill[data-filter="category"]');
        if (!pill) return;
        e.preventDefault();
        const val = pill.dataset.value || '';
        load({ category: val });

        // Close mobile offcanvas if open
        const oc = document.getElementById('mobileFilters');
        if (oc && oc.classList.contains('show')) {
            bootstrap.Offcanvas.getInstance(oc)?.hide();
        }
    });

    /* Sort */
    sortSel?.addEventListener('change', function () {
        load({ sort: this.value });
    });

    /* Desktop search — debounced */
    let _debounce;
    document.getElementById('shopSearch')?.addEventListener('input', function () {
        clearTimeout(_debounce);
        _debounce = setTimeout(() => load({ q: this.value }), 400);
    });

    /* Desktop search button */
    document.getElementById('searchBtn')?.addEventListener('click', () => {
        const val = document.getElementById('shopSearch')?.value || '';
        load({ q: val });
    });

    /* Desktop price apply */
    document.getElementById('applyPrice')?.addEventListener('click', () => {
        load({
            min_price: document.getElementById('minPrice')?.value || '',
            max_price: document.getElementById('maxPrice')?.value || '',
        });
    });

    /* Desktop clear all */
    clearBtn?.addEventListener('click', () => {
        state.q = ''; state.category = ''; state.min_price = ''; state.max_price = ''; state.sort = 'newest';
        if (document.getElementById('shopSearch')) document.getElementById('shopSearch').value = '';
        if (document.getElementById('minPrice'))   document.getElementById('minPrice').value = '';
        if (document.getElementById('maxPrice'))   document.getElementById('maxPrice').value = '';
        if (sortSel) sortSel.value = 'newest';
        load({ q:'', category:'', min_price:'', max_price:'', sort:'newest' });
    });

    /* Mobile search debounce */
    document.getElementById('mobSearch')?.addEventListener('input', function () {
        clearTimeout(_debounce);
        _debounce = setTimeout(() => {
            if (document.getElementById('shopSearch')) document.getElementById('shopSearch').value = this.value;
            load({ q: this.value });
        }, 400);
    });

    /* Mobile apply button */
    document.getElementById('mobApply')?.addEventListener('click', () => {
        const q   = document.getElementById('mobSearch')?.value || '';
        const min = document.getElementById('mobMinPrice')?.value || '';
        const max = document.getElementById('mobMaxPrice')?.value || '';
        // Sync desktop inputs
        if (document.getElementById('shopSearch')) document.getElementById('shopSearch').value = q;
        if (document.getElementById('minPrice'))   document.getElementById('minPrice').value   = min;
        if (document.getElementById('maxPrice'))   document.getElementById('maxPrice').value   = max;
        load({ q, min_price: min, max_price: max });
        bootstrap.Offcanvas.getInstance(document.getElementById('mobileFilters'))?.hide();
    });

    /* Mobile clear */
    document.getElementById('mobClear')?.addEventListener('click', () => {
        ['mobSearch','mobMinPrice','mobMaxPrice'].forEach(id => {
            const el = document.getElementById(id); if (el) el.value = '';
        });
        clearBtn?.click();
        bootstrap.Offcanvas.getInstance(document.getElementById('mobileFilters'))?.hide();
    });

    /* Browser back/forward */
    window.addEventListener('popstate', e => {
        if (e.state) Object.assign(state, e.state);
        const p = new URLSearchParams(location.search);
        Object.assign(state, {
            q: p.get('q')||'', category: p.get('category')||'',
            sort: p.get('sort')||'newest',
            min_price: p.get('min_price')||'', max_price: p.get('max_price')||'',
            page: p.get('page')||'1'
        });
        load(state, false);
    });

    /* Initial pagination bind */
    bindPagination();

    /* Initial chips render (for server-side rendered filters) */
    renderChips();

    /* Chip style fix */
    const style = document.createElement('style');
    style.textContent = `.chip-x{background:none;border:none;color:#ef4444;cursor:pointer;font-size:1rem;line-height:1;padding:0;margin-left:6px;}`;
    document.head.appendChild(style);

})();
</script>
@endpush

