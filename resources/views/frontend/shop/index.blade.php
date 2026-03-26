@extends('layouts.frontend')

@section('meta_title', 'المتجر — أناقة الأميرة')
@section('meta_description', 'تسوقي أرقى العبايات والخمارات المغربية بأفضل الأسعار. توصيل سريع لكل المدن.')

@section('content')

{{-- ── BREADCRUMB ─────────────────────────────────────────── --}}
<section class="pdp-breadcrumb-bar py-3 bg-white border-bottom">
    <div class="container px-xl-5 small font-body">
        <nav class="pdp-breadcrumb" aria-label="breadcrumb">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none"><i class="fas fa-home"></i></a>
            <span class="mx-2 text-muted opacity-50">/</span>
            <span class="text-gold fw-bold">المتجر</span>
        </nav>

        {{-- Stylist Pill Navigation (Premium Minimalist) --}}
        <div class="stylist-pill-track mt-4" data-aos="fade-up">
            <a href="{{ route('shop.index') }}" class="stylist-pill {{ !request('category') ? 'active' : '' }}">الكل</a>
            @foreach($allCategories as $cat)
                <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" 
                   class="stylist-pill {{ request('category') == $cat->slug ? 'active' : '' }}">
                    {{ $cat->translated_name }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ── PRODUCT CATALOG ────────────────────────────────────────── --}}
<section class="section-py bg-surface">
    <div class="container px-xl-5">
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="brand-heading mb-0">
                    @if(request('category'))
                        @php $currentCat = $allCategories->where('slug', request('category'))->first(); @endphp
                        {{ $currentCat ? $currentCat->translated_name : 'البحث' }}
                    @else
                        كل المنتجات
                    @endif
                </h2>
                <p class="text-muted small mb-0 font-body">{{ $products->total() }} قطعة مميزة</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-white btn-sm border px-3 rounded-pill dropdown-toggle font-body" type="button" data-bs-toggle="dropdown">
                        ترتيب حسب
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">الأحدث</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}">السعر: من الأقل</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}">السعر: من الأعلى</a></li>
                    </ul>
                </div>
            </div>
        </div>

        @if($products->isEmpty())
            <div class="text-center py-5">
                <div class="app-icon mx-auto mb-4 bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-search"></i>
                </div>
                <h4 class="brand-heading">لم نجد أي منتجات</h4>
                <p class="text-muted font-body">حاولي تغيير معايير البحث أو تصفح كل المجموعات</p>
                <a href="{{ route('shop.index') }}" class="btn btn-brand-primary rounded-pill px-4 mt-3">عرض كل المجموعة</a>
            </div>
        @else
            <div class="row g-3 g-lg-4">
                @foreach($products as $product)
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                        @include('frontend.partials.product_card_v2', ['product' => $product])
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center brand-pagination">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
