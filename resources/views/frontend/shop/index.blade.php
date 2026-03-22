@extends('layouts.frontend')

@php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->translated_name . ' — ' . setting('app_name', 'Hijab Princesses'))
        : (request('q') ? 'نتائج البحث عن "' . request('q') . '" — ' . setting('app_name') : 'المتجر — ' . setting('app_name', 'Hijab Princesses'));
@endphp

@section('meta_title', $pageTitle)

@section('content')

@section('content')

{{-- =============================================
     TOP CATEGORY FILTER (Circular Style)
     ============================================= --}}
<section class="bg-white border-bottom sticky-top shadow-sm" style="top: 70px; z-index: 1020;">
    <div class="container">
        <div class="cat-circle-list py-3" style="justify-content: center; gap: 30px;">
            <a href="{{ route('shop.index') }}" class="cat-circle-item {{ !request('category') ? 'active' : '' }}" style="min-width: 80px;">
                <div class="cat-circle-img" style="width: 65px; height: 65px; {{ !request('category') ? 'border-color: var(--primary); box-shadow: 0 0 10px var(--accent);' : '' }}">
                    <img src="{{ asset('images/all-products.jpg') }}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3050/3050239.png'" alt="الكل">
                </div>
                <span class="cat-circle-name small">الكل</span>
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="cat-circle-item {{ request('category') == $cat->slug ? 'active' : '' }}" style="min-width: 80px;">
                <div class="cat-circle-img" style="width: 65px; height: 65px; {{ request('category') == $cat->slug ? 'border-color: var(--primary); box-shadow: 0 0 10px var(--accent);' : '' }}">
                    <img src="{{ $cat->image ? (Str::startsWith($cat->image, 'http') ? $cat->image : Storage::url($cat->image)) : asset('images/placeholder-cat.jpg') }}" alt="{{ $cat->translated_name }}">
                </div>
                <span class="cat-circle-name small">{{ $cat->translated_name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- =============================================
     PRODUCT GRID
     ============================================= --}}
<section class="section-py bg-surface min-vh-100">
    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
            <h2 class="h4 fw-bold m-0 border-start-primary ps-3">
                {{ $activeCategory ? $activeCategory->translated_name : 'جميع المنتجات' }}
                <span class="text-muted small ms-2">({{ $products->total() }} منتج)</span>
            </h2>
            
            {{-- Simple Sort --}}
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted d-none d-md-inline">ترتيب:</span>
                <select class="form-select form-select-sm rounded-pill border-0 shadow-sm" id="sortSelect" style="width: 140px;" onchange="location = '{{ route('shop.index', array_merge(request()->query(), ['sort' => ''])) }}'.replace('sort=', 'sort=' + this.value)">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>الأقل سعراً</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>الأعلى سعراً</option>
                </select>
            </div>
        </div>

        @if($products->isEmpty())
            <div class="text-center py-5" data-aos="fade-up">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x text-muted opacity-25"></i>
                </div>
                <h3 class="fw-bold">لم نجد أي منتجات</h3>
                <p class="text-muted">جربي اختيار فئة أخرى أو العودة للمتجر الرئيسي.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 mt-3">عرض كل المنتجات</a>
            </div>
        @else
            <div class="row g-4" id="productGridContainer">
                @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="product-card-v2 h-100">
                        <div class="product-v2-image">
                            <img src="{{ $product->main_image ? (Str::startsWith($product->main_image, 'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}" alt="{{ $product->translated_name }}">
                            <div class="product-v2-overlay">
                                <a href="{{ route('shop.show', $product->id) }}" class="btn-overlay">
                                    <i class="fas fa-eye me-2"></i> تفاصيل
                                </a>
                            </div>
                        </div>
                        <div class="product-v2-body">
                            <h5 class="product-v2-name mb-2">{{ Str::limit($product->translated_name, 30) }}</h5>
                            <div class="product-v2-price mb-3">
                                <span class="text-primary fw-bold">{{ $product->formatted_price }}</span>
                            </div>
                            <button onclick="addToCart({{ $product->id }})" class="btn btn-primary w-100 rounded-pill btn-sm py-2">
                                أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        @endif

    </div>
</section>

@endsection

@push('scripts')
<script>
function addToCart(productId) {
    fetch(`{{ url('/cart/add') }}/${productId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;
        if (!response.ok) throw new Error((data && data.message) || `حدث خطأ في الخادم`);
        
        const countEl = document.getElementById('header-cart-count');
        if(countEl && data.cartCount !== undefined) countEl.textContent = data.cartCount;
        
        Swal.fire({ 
            toast:true, 
            position:'top-start', 
            icon:'success', 
            title:'تمت الإضافة للسلة!', 
            showConfirmButton:false, 
            timer:2500, 
            background:'#000', 
            color:'#fff' 
        });
        if (typeof refreshMiniCart === 'function') refreshMiniCart();
    })
    .catch(error => {
        Swal.fire({ toast:true, position:'top-start', icon:'error', title: error.message || 'خطأ', showConfirmButton:false, timer:3000 });
    });
}
</script>
@endpush
