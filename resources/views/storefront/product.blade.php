@extends('layouts.storefront')
@section('title', \App\Support\Storefront::name($product) . ' | تعاونية آيت أومديس')
@section('body-class', 'product')
@section('content')
@php
    $name = \App\Support\Storefront::name($product);
    $description = $product->description_ar ?: $product->description;
    $variant = $product->variants->where('status', 'active')->firstWhere('stock', '>', 0);
    $available = $product->variants->isNotEmpty() ? (bool) $variant : $product->stock > 0;
    $displayVariant = $variant ?? $product->variants->firstWhere('status', 'active');
    $displayImage = $displayVariant?->color_image ?: $product->main_image;
    $gallery = collect([$displayImage, $product->main_image])
        ->merge($product->images->pluck('image_path'))
        ->merge($product->variants->where('status', 'active')->pluck('color_image'))
        ->filter()->unique()->values();
@endphp
<main class="product-page">
<div class="breadcrumb"><a href="{{ route('home') }}">الرئيسية</a>　‹　<a href="{{ route('shop.index', ['category' => $product->productCategory?->slug]) }}">{{ $product->productCategory ? \App\Support\Storefront::name($product->productCategory) : 'المتجر' }}</a>　‹　 {{ $name }}</div>
<div class="product-top" data-product-images>
<section class="details panel">
<h1>{{ $name }}</h1>
<div class="reviews">({{ $product->reviews_count }})　{{ $product->reviews_count ? number_format($product->reviews_avg_rating, 1) : '—' }}　 <span class="stars" aria-label="تقييم المنتج">★ ★ ★ ★ ★</span></div>
<p class="origin">طبيعي 100% • من قلب الأطلس</p>
<p>{{ \Illuminate\Support\Str::limit(strip_tags($description ?? ''), 180) }}</p>
<div class="qualities"><span><i data-lucide="users-round" aria-hidden="true"></i>منتج تعاوني</span><span><i data-lucide="flask-conical" aria-hidden="true"></i>بدون إضافات</span><span><i data-lucide="droplets" aria-hidden="true"></i>جودة عالية</span><span><i data-lucide="leaf" aria-hidden="true"></i>طبيعي 100%</span></div>
<form action="{{ route('cart.add', $product->id) }}" method="post" data-add-to-cart>
@csrf
<div class="purchase"><div>الحجم @include('storefront.partials.variants')</div><div><strong data-product-price>{{ \App\Support\Storefront::money($variant?->price ?? $product->price) }}</strong><small>شامل الضريبة</small></div></div>
<label for="product-quantity"><b>الكمية</b></label>
<div class="buy-row">
<div class="quantity"><button type="button" onclick="adjustProductQuantity(1)" aria-label="زيادة الكمية"><i data-lucide="plus" aria-hidden="true"></i></button><input id="product-quantity" name="quantity" type="number" min="1" max="{{ $variant?->stock ?? $product->stock }}" value="1" aria-label="الكمية"><button type="button" onclick="adjustProductQuantity(-1)" aria-label="تقليل الكمية"><i data-lucide="minus" aria-hidden="true"></i></button></div>
<button type="submit" class="primary" @disabled(!$available)><i data-lucide="shopping-cart" aria-hidden="true"></i> {{ $available ? 'أضف إلى السلة' : 'غير متوفر حالياً' }}</button>
<button type="button" class="square" onclick="shareProduct()" aria-label="مشاركة"><i data-lucide="share-2" aria-hidden="true"></i></button><button type="button" class="square" data-favorite="{{ $product->id }}" aria-pressed="false" onclick="toggleFavorite({{ $product->id }},this)" aria-label="إضافة للمفضلة: {{ $name }}"><i data-lucide="heart" aria-hidden="true"></i></button>
</div>
</form>
</section>
<section class="gallery" aria-label="صور المنتج">
<div class="main-photo"><img id="main-photo" data-product-image src="{{ \App\Support\Storefront::image($displayImage) }}" alt="{{ $name }}"><button class="zoom" onclick="zoomPhoto()" aria-label="تكبير الصورة"><i data-lucide="expand" aria-hidden="true"></i></button></div>
<div class="thumbnails">
@forelse($gallery as $photo)
<button class="{{ $photo === $displayImage ? 'selected' : '' }}" aria-pressed="{{ $photo === $displayImage ? 'true' : 'false' }}" onclick="selectPhoto(this)"><img src="{{ \App\Support\Storefront::image($photo) }}" alt="{{ $name }} — صورة {{ $loop->iteration }}"></button>
@empty
<button class="selected" aria-pressed="true" onclick="selectPhoto(this)"><img src="{{ \App\Support\Storefront::image(null) }}" alt="{{ $name }}"></button>
@endforelse
</div>
</section>
</div>
<div class="product-bottom">
<aside class="atlas panel"><h2>من قلب الأطلس</h2><p>ندعم المنتجين المحليين ونحافظ<br>على التقاليد الطبيعية للأجيال القادمة.</p><img src="{{ asset('images/storefront/atlas.png') }}" alt="جبال الأطلس"></aside>
<section class="description">
<div class="benefits product"><div><i data-lucide="truck" aria-hidden="true"></i><span><b>توصيل سريع</b><small>إلى جميع أنحاء المغرب</small></span></div><div><i data-lucide="lock-keyhole" aria-hidden="true"></i><span><b>دفع آمن</b><small>100% آمن وموثوق</small></span></div><div><i data-lucide="badge-check" aria-hidden="true"></i><span><b>منتج مضمون</b><small>جودة مضمونة 100%</small></span></div></div>
<div class="panel tabs-panel">
<div class="tabs" role="tablist" aria-label="تفاصيل المنتج">
@foreach(['الوصف', 'الفوائد', 'طريقة الاستخدام', 'التقييمات (' . $product->reviews_count . ')'] as $label)
<button id="product-tab-{{ $loop->index }}" role="tab" aria-controls="product-panel-{{ $loop->index }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}" class="{{ $loop->first ? 'active' : '' }}" onclick="tab({{ $loop->index }},this)">{{ $label }}</button>
@endforeach
</div>
<div id="product-panel-0" data-tab-panel="0" role="tabpanel" aria-labelledby="product-tab-0"><h3>وصف المنتج</h3><p class="product-description">{{ strip_tags($description ?? '') ?: 'تواصل معنا لمعرفة المزيد عن هذا المنتج.' }}</p></div>
<div id="product-panel-1" data-tab-panel="1" role="tabpanel" aria-labelledby="product-tab-1" hidden><h3>معلومات إضافية</h3><p>{{ $name }}<br>رمز المنتج: <bdi>{{ $product->sku }}</bdi><br>{{ $available ? 'متوفر في المخزون' : 'غير متوفر حالياً' }}</p></div>
<div id="product-panel-2" data-tab-panel="2" role="tabpanel" aria-labelledby="product-tab-2" hidden><h3>الحفظ والاستخدام</h3><p>يرجى اتباع تعليمات الحفظ والاستخدام الموجودة على عبوة المنتج. <a href="{{ route('contact') }}">تواصل معنا</a> لأي استفسار.</p></div>
<div id="product-panel-3" data-tab-panel="3" role="tabpanel" aria-labelledby="product-tab-3" hidden><h3>التقييمات ({{ $product->reviews_count }})</h3>
@forelse($reviews as $review)
<article class="customer-review"><b>{{ $review->customer_name ?: 'عميل' }}</b><span class="stars"> {{ str_repeat('★', (int) $review->rating) }}</span><p>{{ $review->comment }}</p></article>
@empty<p>لا توجد تقييمات بعد.</p>@endforelse
@include('storefront.partials.pagination', ['paginator' => $reviews])
</div>
</div>
</section>
<aside class="specs panel"><h3>معلومات إضافية</h3><div><b>النوع</b><span>{{ $product->productCategory ? \App\Support\Storefront::name($product->productCategory) : 'منتج طبيعي' }}</span></div><div><b>المصدر</b><span>جبال الأطلس – المغرب</span></div><div><b>رمز المنتج</b><bdi>{{ $product->sku }}</bdi></div><div><b>التوفر</b><span>{{ $available ? 'متوفر' : 'غير متوفر' }}</span></div><div><b>الحفظ</b><span>حسب تعليمات العبوة</span></div></aside>
</div>
</main>
@endsection
