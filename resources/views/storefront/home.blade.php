@extends('layouts.storefront')
@section('body-class', 'index')
@section('content')
<section class="hero">
<div class="hero-copy">
<h1>
من جبال الأطلس...<br>إلى كل بيت بالعالم
</h1>
<p>
منتجات طبيعية، نقية وأصيلة، من صنع أيادٍ نسائية<br>تعاونية، تحافظ على إرث الأجداد بجودة عالية.
</p>
<a class="gold" href="{{ route('shop.index') }}">تسوق الآن <i data-lucide="arrow-left" aria-hidden="true"></i></a>
<div class="hero-points">
<span><i data-lucide="users-round" aria-hidden="true"></i>دعم تعاونيات نسائية</span><span><i data-lucide="mountain" aria-hidden="true"></i>من قلب الأطلس</span><span><i data-lucide="leaf" aria-hidden="true"></i>طبيعي 100%</span>
</div>
</div>
</section>
<div class="benefits standard">
<div>
<i data-lucide="badge-check" aria-hidden="true"></i><span><b>عضو معتمد</b><small>منتجاتنا معتمدة وذات جودة عالية</small></span>
</div>
<div>
<i data-lucide="truck" aria-hidden="true"></i><span><b>توصيل سريع</b><small>توصيل إلى جميع أنحاء العالم</small></span>
</div>
<div>
<i data-lucide="lock-keyhole" aria-hidden="true"></i><span><b>دفع آمن</b><small>معاملات آمنة ومشفرة 100%</small></span>
</div>
</div>
<section class="home-categories">
<div class="section-heading">
<div>
<span>تسوّق حسب الفئة</span>
<h2>
اكتشف منتجاتنا الطبيعية
</h2>
</div>
</div>
<div class="category-grid">
@forelse($allCategories->take(4) as $category)
<a class="category-card" href="{{ route('shop.index', ['category' => $category->slug]) }}"><img src="{{ \App\Support\Storefront::image($category->image) }}" alt="{{ \App\Support\Storefront::name($category) }}"><span><b>{{ \App\Support\Storefront::name($category) }}</b><small>اكتشف المجموعة ←</small></span></a>
@empty
<p class="empty-state">ستتوفر فئات منتجاتنا قريباً.</p>
@endforelse
</div>
</section>
<section class="home-products catalog">
<div class="section-heading products-heading">
<div>
<span>مختاراتنا لك</span>
<h2>
منتجات مميزة
</h2>
</div>
<a href="{{ route('shop.index') }}">عرض جميع المنتجات <i data-lucide="chevron-left" style="width: 20px !important;height:20px !important" aria-hidden="true"></i></a>
</div>
<div class="product-carousel-toolbar">
<div class="product-tabs" role="tablist" aria-label="تصفية المنتجات">
<button class="active" role="tab" aria-selected="true" data-home-filter="all" onclick="setHomeProductFilter('all',this)">الكل</button><button role="tab" aria-selected="false" data-home-filter="new" onclick="setHomeProductFilter('new',this)">الأحدث</button><button role="tab" aria-selected="false" data-home-filter="bestseller" onclick="setHomeProductFilter('bestseller',this)">الأكثر مبيعاً</button>
</div>
<div class="carousel-controls">
<button type="button" data-carousel-next aria-label="المنتجات التالية"><i data-lucide="chevron-left" aria-hidden="true"></i></button><button type="button" data-carousel-prev aria-label="المنتجات السابقة"><i data-lucide="chevron-right" aria-hidden="true"></i></button>
</div>
</div>
<div class="product-carousel swiper" id="home-product-carousel" aria-label="منتجات مميزة">
<div class="carousel-track swiper-wrapper" id="home-product-track">
@forelse($featuredProducts as $product)
@include('storefront.partials.product-card', ['product' => $product, 'carouselSlide' => true])
@empty
<p class="empty-state">ستتوفر منتجاتنا قريباً.</p>
@endforelse
</div>
</div>
<p id="home-filter-empty" class="empty-state" hidden>لا توجد منتجات في هذه المجموعة حالياً.</p>
</section>

@endsection
