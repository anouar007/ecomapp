@extends('layouts.storefront')
@section('title', 'سلة التسوق | تعاونية آيت أومديس')
@section('body-class', 'cart')
@section('content')
<main class="cart-page">
<div class="breadcrumb"><a href="{{ route('home') }}">الرئيسية</a>　‹　 سلة التسوق</div>
<h1><i data-lucide="shopping-cart" aria-hidden="true"></i> سلة التسوق</h1>
<p class="cart-subtitle">لديك {{ count($cart) }} منتجات في سلة التسوق</p>
<div class="cart-layout">
<aside class="summary panel">
<h2>ملخص الطلب <i data-lucide="lock-keyhole" aria-hidden="true"></i></h2>
<div class="summary-line"><span>المجموع الفرعي</span><b data-subtotal>{{ \App\Support\Storefront::money($total) }}</b></div>
<div class="summary-line"><span>التوصيل</span><b>يُحسب عند إتمام الطلب</b></div>
<p class="muted">التوصيل إلى جميع أنحاء المغرب</p>
<div class="summary-line"><span>خصم</span><span class="discount">0 درهم</span></div>
<div class="summary-line total"><b>المجموع قبل التوصيل</b><strong data-total>{{ \App\Support\Storefront::money($total) }}</strong></div>
<p class="muted">تشمل جميع الضرائب</p>
<div class="coupon"><label for="coupon">كود الخصم</label><div><input id="coupon" placeholder="أدخل كود الخصم"><button type="button" onclick="checkCoupon()">تطبيق ♧</button></div></div>
<a class="primary checkout" href="{{ route('checkout.index') }}" @if(empty($cart)) aria-disabled="true" tabindex="-1" @endif><i data-lucide="lock-keyhole" aria-hidden="true"></i> إتمام الطلب</a>
<p class="secure">◈　الدفع عند الاستلام</p>
<div class="payments"><p>طريقة الدفع المتاحة</p><div class="cash-payment"><i data-lucide="banknote" aria-hidden="true"></i> الدفع نقداً عند الاستلام</div></div>
</aside>
<section class="cart-table panel" aria-label="منتجات السلة">
<div class="table-head"><span>المنتج</span><span>السعر</span><span>الكمية</span><span>الإجمالي</span><span></span></div>
@foreach($cart as $key => $item)
<div class="cart-row" data-cart-id="{{ $key }}" data-price="{{ $item['price'] }}">
<a href="{{ route('shop.show', $item['product_id'] ?? explode('_', $key)[0]) }}" class="cart-product"><img src="{{ \App\Support\Storefront::image($item['image'] ?? null) }}" alt="{{ $item['name'] }}"><div><h3>{{ $item['name'] }}</h3><small>منتج طبيعي من قلب الأطلس</small><p>الحجم: <em>{{ $item['size'] ?? 'الحجم القياسي' }}</em>@if(!empty($item['color'])) · {{ $item['color'] }}@endif</p></div></a>
<b>{{ \App\Support\Storefront::money($item['price']) }}</b>
<div class="quantity"><button type="button" onclick="changeQty(this,1)" aria-label="زيادة كمية {{ $item['name'] }}"><i data-lucide="plus" aria-hidden="true"></i></button><span data-cart-quantity>{{ $item['quantity'] }}</span><button type="button" onclick="changeQty(this,-1)" aria-label="تقليل كمية {{ $item['name'] }}"><i data-lucide="minus" aria-hidden="true"></i></button></div>
<b data-cart-total>{{ \App\Support\Storefront::money($item['price'] * $item['quantity']) }}</b><button class="remove" aria-label="حذف {{ $item['name'] }}" onclick="removeItem(this)"><i data-lucide="trash-2" aria-hidden="true"></i></button>
</div>
@endforeach
<div class="empty empty-state" @if(count($cart)) hidden @endif><h2>سلة التسوق فارغة</h2><p>اكتشف منتجاتنا الطبيعية وأضف ما يعجبك إلى سلتك.</p></div>
<div class="cart-actions"><a class="outline" href="{{ route('shop.index') }}">←　متابعة التسوق</a><button type="button" onclick="clearCart(this)" @disabled(empty($cart))><i data-lucide="trash-2" aria-hidden="true"></i> إفراغ سلة التسوق</button></div>
</section>
</div>
<div class="cart-benefits"><div class="benefits"><div><i data-lucide="truck" aria-hidden="true"></i><span><b>توصيل سريع</b><small>إلى جميع أنحاء المغرب</small></span></div><div><i data-lucide="lock-keyhole" aria-hidden="true"></i><span><b>دفع آمن</b><small>الدفع عند الاستلام</small></span></div><div><i data-lucide="badge-check" aria-hidden="true"></i><span><b>جودة مضمونة</b><small>منتجات طبيعية وأصيلة</small></span></div></div></div>
</main>
@endsection
