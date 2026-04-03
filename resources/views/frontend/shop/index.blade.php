@extends('layouts.frontend')

@section('meta_title', 'المتجر الرسمي — Hijab Princesses — أناقة الأميرة')
@section('meta_description', 'Hijab Princesses: اكتشفي أرقى تشكيلة من العبايات والخمارات المغربية الراقية. جودة فاخرة وتوصيل سريع لكل مدن المغرب. تسوقي الآن من أناقة الأميرة.')

@section('content')

{{-- ── SHOP HERO & VISUAL CATEGORY NAVIGATOR ──────────────────── --}}
<section class="shop-hero py-4 py-lg-5">
    <div class="container px-xl-5 text-center">
        <h1 class="display-5 display-lg-4 brand-heading mb-2 text-dark soft-glow-text font-corsiva" data-aos="fade-down" style="letter-spacing: -0.02em;">
            مجموعة <span class="text-gold">Hijab Princesses</span> الفاخرة
        </h1>
        <p class="text-muted small mb-4 font-body opacity-75 mx-auto" data-aos="fade-up" style="max-width: 600px;">تمتعي بتجربة تسوق استثنائية مع أرقى تصاميم أناقة الأميرة</p>

        {{-- Visual Category Navigator (Story Pills) --}}
        <div class="category-story-track d-flex justify-content-lg-center" data-aos="fade-up">
            {{-- All (Story Pill) --}}
            <a href="{{ route('shop.index') }}" class="category-story-pill {{ !request('category') ? 'active' : '' }}">
                <div class="category-story-img-wrapper">
                    <div class="category-story-img d-flex align-items-center justify-content-center bg-white border border-gold-light" style="font-size: 1.25rem;">
                       <i class="fas fa-border-all text-gold"></i>
                    </div>
                </div>
                <span class="category-story-label">عرض الكل</span>
            </a>

            @foreach($allCategories as $cat)
                <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" 
                   class="category-story-pill {{ request('category') == $cat->slug ? 'active' : '' }}">
                    <div class="category-story-img-wrapper">
                        <img src="{{ $cat->image ? (Str::startsWith($cat->image, 'http') ? $cat->image : Storage::url($cat->image)) : asset('images/placeholder-cat.jpg') }}" 
                             class="category-story-img" alt="{{ $cat->translated_name }}">
                    </div>
                    <span class="category-story-label">{{ $cat->translated_name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ── PRODUCT CATALOG ────────────────────────────────────────── --}}
<section class="section-py bg-surface" style="border-top: 1px solid rgba(0,0,0,0.02);">
    <div class="container px-xl-5">

        @if(request('q') || request('sort'))
            {{-- SEARCH OR SORT VIEW (Single List) --}}
            <div class="d-flex align-items-center gap-3 mb-5" data-aos="fade-left">
                <div class="bg-gold rounded" style="width: 4px; height: 35px;"></div>
                <div>
                    <h2 class="brand-heading mb-0 h3">نتائج البحث</h2>
                    <p class="text-muted small mb-0 font-body fw-bold">تم العثور على <span class="text-gold">{{ $products->total() }}</span> قطعة</p>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="brand-heading">لم نجد أي منتجات تطابق بحثك</h4>
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

        @elseif(request('category'))
            {{-- SINGLE CATEGORY VIEW --}}
            @php $currentCat = $allCategories->where('slug', request('category'))->first(); @endphp
            @if($currentCat)
                <div class="d-flex align-items-center gap-3 mb-5" data-aos="fade-left">
                    <div class="bg-gold rounded" style="width: 4px; height: 35px;"></div>
                    <div>
                        <h2 class="brand-heading mb-0 h3">{{ $currentCat->translated_name }}</h2>
                        <p class="text-muted small mb-0 font-body fw-bold">يتوفر <span class="text-gold">{{ $currentCat->products->count() }}</span> قطعة حصرية</p>
                    </div>
                </div>

                @if($currentCat->products->isEmpty())
                    <div class="text-center py-5 text-muted">لم يتم إضافة منتجات في هذا القسم بعد.</div>
                @else
                    <div class="row g-3 g-lg-4">
                        @foreach($currentCat->products as $product)
                            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                                @include('frontend.partials.product_card_v2', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

        @else
            {{-- ALL PRODUCTS GROUPED BY CATEGORY --}}
            @foreach($allCategories as $category)
                @if($category->products->count() > 0)
                    <div class="category-section-luxe" data-aos="fade-up" id="category-{{ $category->slug }}">
                        <div class="section-premium-header">
                            <h2 class="section-premium-title">{{ $category->translated_name }}</h2>
                            <div class="section-premium-divider">
                                <div class="section-premium-line"></div>
                                <i class="fas fa-crown section-premium-icon"></i>
                                <div class="section-premium-line"></div>
                            </div>
                        </div>
                        
                        <div class="row g-3 g-lg-4">
                            @foreach($category->products as $product)
                                <div class="col-6 col-md-4 col-lg-3">
                                    @include('frontend.partials.product_card_v2', ['product' => $product])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

    </div>
</section>

@endsection
