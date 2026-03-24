@extends('layouts.frontend')

@php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->translated_name . ' — ' . setting('app_name', 'Hijab Princesses'))
        : (request('q') ? 'نتائج البحث عن "' . request('q') . '" — ' . setting('app_name') : 'المتجر — ' . setting('app_name', 'Hijab Princesses'));
@endphp

@section('meta_title', $pageTitle)

@section('content')

{{-- =============================================
     TOP CATEGORY FILTER (Circular Style)
     ============================================= --}}
<section class="bg-brand-overlay-light bg-silk border-bottom shadow-sm">
    <div class="container px-xl-5">
        <div class="category-circle-grid py-3" style="gap: 20px;">
            <a href="{{ route('shop.index') }}" 
               class="category-ajax-link category-circle-item text-decoration-none {{ !request('category') ? 'active' : '' }}" 
               data-category=""
               style="width: 70px;">
                <div class="category-circle-img-wrap" style="width: 60px; height: 60px; {{ !request('category') ? 'border-color: var(--brand-gold);' : '' }}">
                    <img src="https://img.icons8.com/ios/100/c5a059/infinity.png" alt="الكل" style="padding: 10px;">
                </div>
                <span class="category-circle-name small font-body">الكل</span>
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" 
               class="category-ajax-link category-circle-item text-decoration-none {{ request('category') == $cat->slug ? 'active' : '' }}" 
               data-category="{{ $cat->slug }}"
               style="width: 70px;">
                <div class="category-circle-img-wrap" style="width: 60px; height: 60px; {{ request('category') == $cat->slug ? 'border-color: var(--brand-gold);' : '' }}">
                    <img src="{{ $cat->image ? (Str::startsWith($cat->image, 'http') ? $cat->image : Storage::url($cat->image)) : asset('images/placeholder-cat.jpg') }}" alt="{{ $cat->translated_name }}">
                </div>
                <span class="category-circle-name small font-body">{{ $cat->translated_name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- =============================================
     PRODUCT GRID AREA
     ============================================= --}}
<section class="section-py bg-surface min-vh-100">
    <div class="container px-xl-5" id="shopMainContainer">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4" data-aos="fade-up">
            <div>
                <h2 class="brand-heading h4 m-0" id="categoryTitle">
                    {{ $activeCategory ? $activeCategory->translated_name : 'جميع المنتجات' }}
                    <span class="text-muted small ms-2 fw-normal" id="productCount" style="font-family: var(--font-body);">({{ $products->total() }} قطعة)</span>
                </h2>
                <div class="bg-gold mt-2 rounded" style="width: 40px; height: 3px;"></div>
            </div>
            
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                <span class="small text-muted d-none d-md-inline">ترتيب:</span>
                <select class="form-select form-select-sm brand-card border-0 shadow-sm py-2 px-3 flex-grow-1" id="sortSelect" style="min-width: 160px;">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>✨ الأحدث تقديماً</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>💰 السعر (من الأقل)</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>💎 السعر (من الأعلى)</option>
                </select>
            </div>
        </div>

        <div id="productGridWrapper" class="transition-300">
            @include('frontend.shop.partials.product-grid', ['products' => $products])
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
let currentCategory = "{{ request('category', '') }}";
let currentSort = "{{ request('sort', 'newest') }}";
let currentSearch = "{{ request('q', '') }}";

document.addEventListener('DOMContentLoaded', () => {
    // Intercept Category Clicks
    document.querySelectorAll('.category-ajax-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            currentCategory = this.dataset.category;
            
            // Update UI State
            document.querySelectorAll('.category-ajax-link').forEach(l => {
                l.classList.remove('active');
                l.querySelector('.category-circle-img-wrap').style.borderColor = 'var(--brand-gold-light)';
            });
            this.classList.add('active');
            this.querySelector('.category-circle-img-wrap').style.borderColor = 'var(--brand-gold)';
            
            fetchProducts(true);
        });
    });

    // Handle Sorting
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentSort = this.value;
            fetchProducts(true);
        });
    }

    // Handle Pagination (Delegated)
    document.getElementById('productGridWrapper').addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();
            fetchProducts(false, link.href);
            window.scrollTo({ top: document.getElementById('shopMainContainer').offsetTop - 100, behavior: 'smooth' });
        }
    });
});

function fetchProducts(resetPage = true, customUrl = null) {
    const wrapper = document.getElementById('productGridWrapper');
    wrapper.style.opacity = '0.5';
    wrapper.style.pointerEvents = 'none';

    let url = customUrl || "{{ route('shop.index') }}";
    const params = new URLSearchParams();
    
    if (currentCategory) params.append('category', currentCategory);
    if (currentSort)     params.append('sort',     currentSort);
    if (currentSearch)   params.append('q',        currentSearch);
    
    // If not using a custom pagination URL, add params to the base URL
    if (!customUrl) {
        url = url + '?' + params.toString();
    } else {
        // If it's a pagination URL, ensure other filters are preserved
        let pUrl = new URL(customUrl);
        if (currentCategory) pUrl.searchParams.set('category', currentCategory);
        if (currentSort)     pUrl.searchParams.set('sort',     currentSort);
        if (currentSearch)   pUrl.searchParams.set('q',        currentSearch);
        url = pUrl.toString();
    }

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        wrapper.innerHTML = data.grid_html;
        document.getElementById('categoryTitle').innerHTML = `${data.category_name} <span class="text-muted small ms-2 fw-normal" id="productCount" style="font-family: var(--font-body);">(${data.total_count} قطعة)</span>`;
        
        // Update URL
        history.pushState(null, null, url);
        
        wrapper.style.opacity = '1';
        wrapper.style.pointerEvents = 'all';
        
        // Re-init AOS if needed
        if (window.AOS) AOS.refresh();
    })
    .catch(err => {
        console.error(err);
        wrapper.style.opacity = '1';
        wrapper.style.pointerEvents = 'all';
    });
}
</script>
@endpush
