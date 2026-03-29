@extends('layouts.frontend')

@section('meta_title', 'أناقة الأميرة — متجر العبايات والخمارات الفاخرة')
@section('meta_description', 'اكتشفي تشكيلتنا الحصرية من العبايات الفاخرة والخمارات الأنيقة. جودة عالية وتوصيل لكل مدن المغرب.')

@section('content')

{{-- =============================================
     IMMERSIVE FASHION HERO
     ============================================= --}}
<section class="hero-immersive bg-brand-fashion bg-brand-overlay d-flex align-items-center" style="min-height: 95vh;">
    <div class="container px-xl-5">
        <div class="hero-content text-center py-5" data-aos="zoom-out" data-aos-duration="1500">
            <div class="glass-capsule-dark mb-4 mx-auto" style="max-width: 850px;">
                <span class="text-uppercase tracking-widest text-gold fw-bold mb-3 d-block small" style="letter-spacing: 4px;">المجموعة الجديدة</span>
                <h1 class="display-2 fw-bold mb-4 text-white brand-heading" style="line-height:1.1;">
                    تألقي كالأميرة مع<br>
                    <span class="text-gold">أرقى العبايات</span>
                </h1>
                <p class="lead mb-5 text-white opacity-90 mx-auto font-body" style="max-width: 650px; font-size: 1.15rem;">
                    اكتشفي تشكيلتنا الحصرية التي تمزج بين الأصالة المغربية واللمسة العصرية لكل مناسباتك الملكية.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('shop.index') }}" class="btn-brand-primary px-5 py-3 text-decoration-none shadow-lg">
                        تسوقي الآن
                        <i class="fas fa-shopping-bag ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     EDITORIAL CATEGORY GRID
     ============================================= --}}
<section class="section-py bg-white overflow-hidden">
    <div class="container px-xl-5">
        <div class="section-header mb-5 text-center" data-aos="fade-up">
            <h2 class="brand-heading mb-0">اكتشفي المجموعات</h2>
            <div class="bg-gold mt-3 rounded mx-auto" style="width: 50px; height: 3px;"></div>
        </div>
        
        <div class="editorial-category-grid" data-aos="fade-up" data-aos-delay="100">
            @foreach($allCategories as $category)
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="editorial-category-card">
                <img src="{{ $category->image ? (Str::startsWith($category->image, 'http') ? $category->image : Storage::url($category->image)) : asset('images/placeholder-cat.jpg') }}" 
                     alt="{{ $category->translated_name }} - {{ setting('app_name', 'Hijab Princesses') }}" 
                     class="editorial-category-img">
                <div class="editorial-category-overlay">
                    <span class="editorial-category-badge">
                        {{ $category->translated_name }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- =============================================
     BOUTIQUE INTERIOR (The Experience)
     ============================================= --}}
<section class="section-py bg-brand-interior bg-brand-overlay d-flex align-items-center text-white" style="min-height: 600px;">
    <div class="container px-xl-5">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-left">
                <div class="glass-capsule-dark p-lg-5 p-4 border-gold-subtle">
                    <h2 class="brand-heading text-white display-5 mb-4">متعة التسوق في <span class="text-gold">بوتيك الأميرات</span></h2>
                    <p class="lead opacity-90 mb-4 font-body">نحن لا نبيع مجرد ملابس، بل نقدم لكِ تجربة فريدة تعكس رُقيكِ وأناقتكِ. كل قطعة مختارة بعناية لتناسب ذوقكِ الرفيع.</p>
                    <div class="d-flex gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-gold-light text-gold rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-gem h5 mb-0"></i>
                            </div>
                            <span class="small fw-bold text-uppercase ls-1">جودة ملكية</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-gold-light text-gold rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-star h5 mb-0"></i>
                            </div>
                            <span class="small fw-bold text-uppercase ls-1">تصاميم حصرية</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     MINIMALIST CATEGORY PRODUCT ROWS
     ============================================= --}}
@foreach($categoriesWithProducts as $category)
<section class="section-py bg-white overflow-hidden" style="border-top: 1px solid rgba(0,0,0,0.05);">
    <div class="container px-xl-5">
        <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-up">
            <div class="text-right">
                <h3 class="brand-heading h2 mb-0 text-dark">{{ $category->translated_name }}</h3>
                <p class="text-muted small font-body mb-0">مجموعتنا المختارة من {{ $category->translated_name }}</p>
            </div>
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="text-gold fw-bold text-decoration-none font-body small hvr-forward">
                عرض الكل <i class="fas fa-arrow-left ms-1"></i>
            </a>
        </div>

        <div class="h-scroll-container pb-2" data-aos="fade-up" data-aos-delay="100">
            @foreach($category->products as $product)
            <div class="h-scroll-item">
                @include('frontend.partials.product_card_v2', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>
</section>
@endforeach

{{-- =============================================
     SHOPPING PROCESS (Tactile Silk Texture)
     ============================================= --}}
<section class="section-py bg-silk bg-brand-overlay-light">
    <div class="container text-center px-xl-5">
        <div class="section-header mb-5" data-aos="fade-up">
            <div class="glass-capsule mb-2">
                <h2 class="brand-heading text-dark m-0">كيف تتسوقين كالأميرة؟</h2>
                <div class="bg-gold mt-3 rounded mx-auto" style="width: 50px; height: 3px;"></div>
                <p class="text-muted mt-3 font-body mb-0 fw-bold">خطوات بسيطة تضمن لكِ وصول طلبكِ بكامل أناقته</p>
            </div>
        </div>

        <div class="row g-4 mt-2">
            @php
            $steps = [
                ['icon'=>'fa-shopping-cart', 'title'=>'الطلب من الموقع', 'desc'=>'اختاري قطعك المفضلة وأضيفيها لسلة التسوق ثم أكملي الطلب بسهولة.'],
                ['icon'=>'fa-phone-alt',     'title'=>'مكالمة التأكيد', 'desc'=>'سيقوم فريقنا بالاتصال بك لتأكيد المقاسات وتجهيز طلبك بعناية.'],
                ['icon'=>'fa-box-open',      'title'=>'تجهيز الطلب',    'desc'=>'يتم تغليف طلبك بأرقى الأساليب لضمان وصوله إليك كهدية فاخرة.'],
                ['icon'=>'fa-truck',         'title'=>'التوصيل للمنزل', 'desc'=>'يصلك المندوب حتى باب بيتك، والدفع عند الاستلام بكل أمان.'],
            ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="brand-card p-4 border-0 shadow-premium h-100 bg-white-90 backdrop-blur">
                    <div class="app-icon mx-auto mb-4 bg-gold-light text-gold rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <i class="fas {{ $step['icon'] }}"></i>
                    </div>
                    <h5 class="brand-heading h6 mb-2 text-dark">{{ $step['title'] }}</h5>
                    <p class="small text-muted font-body mb-0" style="font-size: 0.75rem; line-height: 1.6;">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .hero-immersive {
        background-attachment: fixed;
    }
    .bg-white-90 {
        background: rgba(255,255,255,0.9) !important;
    }
    .backdrop-blur {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .border-gold-subtle {
        border: 1px solid rgba(197, 160, 89, 0.2) !important;
    }
    @media (max-width: 991px) {
        .hero-immersive {
            background-attachment: scroll;
            min-height: 70vh !important;
        }
        .glass-capsule-dark {
            padding: 1.5rem;
        }
        .display-2 { font-size: 2.5rem; }
    }
</style>
@endpush
