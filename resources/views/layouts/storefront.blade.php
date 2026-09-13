<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="@yield('description', 'منتجات طبيعية، نقية وأصيلة من قلب الأطلس المغربي.')">
<meta name="theme-color" content="#073c2d">
<link rel="canonical" href="{{ url()->current() }}">
<link rel="icon" href="{{ asset('images/storefront/logo.png') }}">
<title>@yield('title', 'تعاونية آيت أومديس')</title>
<link rel="stylesheet" href="{{ asset('css/storefront-base.css') }}">
@if(request()->routeIs('home'))
<link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}">
<script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}" defer></script>
@endif
<link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
<script src="{{ asset('vendor/lucide/lucide.js') }}" defer></script>
<script src="{{ asset('js/storefront.js') }}" defer></script>
@include('storefront.partials.custom-code', ['position' => 'head'])
</head>
<body class="@yield('body-class')" data-cart-update="{{ route('cart.update') }}" data-cart-remove="{{ route('cart.remove') }}" data-cart-clear="{{ route('cart.clear') }}" data-wishlist-url="{{ route('wishlist.toggle') }}" data-login-url="{{ route('login') }}" data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">
<script type="application/json" id="saved-favorites">{!! json_encode(auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id') : []) !!}</script>
@include('storefront.partials.custom-code', ['position' => 'body_start'])
<a class="skip-link" href="#main-content">انتقل إلى المحتوى</a>
<div id="app">
<header>
<a class="brand" href="{{ route('home') }}"><img src="{{ asset('images/storefront/logo.png') }}" alt="شعار التعاونية"><span><b>تعاونية آيت أومديس</b><small>منتجات طبيعية من قلب الأطلس</small></span></a>
<nav>
<a class="{{ request()->routeIs('home') ? 'active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif href="{{ route('home') }}">الرئيسية</a><a class="{{ request()->routeIs('shop.*') ? 'active' : '' }}" @if(request()->routeIs('shop.*')) aria-current="page" @endif href="{{ route('shop.index') }}">المتجر</a><a class="{{ request()->routeIs('contact') ? 'active' : '' }}" @if(request()->routeIs('contact')) aria-current="page" @endif href="{{ route('contact') }}">اتصل بنا</a>
</nav>
<div class="tools">
<form action="{{ route('shop.index') }}">
<input name="q" value="{{ request('q') }}" placeholder="بحث عن منتج..." aria-label="بحث عن منتج"><button aria-label="بحث"><i data-lucide="search" aria-hidden="true"></i></button>
</form>
<a class="account-link" style="display: none;" aria-label="حسابي" href="{{ auth()->check() ? route('customer.dashboard') : route('login') }}"><i data-lucide="user-round" aria-hidden="true"></i></a><a class="cart-link" href="{{ route('cart.index') }}" aria-label="سلة التسوق"><i data-lucide="shopping-cart" aria-hidden="true"></i><span class="cart-count">{{ array_sum(array_column(session('cart', []), 'quantity')) }}</span></a>
</div>
</header>
<div id="main-content" tabindex="-1">
@yield('content')
</div>
<footer class="site-footer">
<div class="footer-inner">
<a class="footer-brand" href="{{ route('home') }}"><img src="{{ asset('images/storefront/logo.png') }}" alt="شعار تعاونية آيت أومديس"><span><b>تعاونية آيت أومديس</b><small>منتجات طبيعية من قلب الأطلس</small></span></a>
<nav aria-label="روابط التذييل">
<a href="{{ route('home') }}">الرئيسية</a><a href="{{ route('shop.index') }}">منتجاتنا</a><a href="{{ route('contact') }}">اتصل بنا</a>
</nav>
<span class="footer-copy">© 2026 تعاونية آيت أومديس. جميع الحقوق محفوظة.</span>
</div>
</footer>
</div>
<div id="toast" role="status" aria-live="polite" @if(session('success') || session('error')) data-message="{{ session('success') ?: session('error') }}" @endif></div>
<dialog id="modal" aria-label="معلومات التعاونية">
<button class="close" aria-label="إغلاق" onclick="this.closest('dialog').close()"><i data-lucide="x" aria-hidden="true"></i></button>
<div id="modal-content">
<section data-dialog="zoom" hidden>
<img class="zoomed" alt="صورة المنتج مكبرة">
</section>
<section data-dialog="faq" hidden>
<h2>الأسئلة الشائعة</h2>
<details><summary>كيف أطلب منتجاتكم؟</summary><p>اختر المنتج والحجم والكمية، ثم أضفه إلى السلة وانتقل إلى إتمام الطلب.</p></details>
<details><summary>ما هي طريقة الدفع المتاحة؟</summary><p>يمكنك الدفع نقداً عند استلام طلبك.</p></details>
<details><summary>كيف أتابع طلبي؟</summary><p>افتح صفحة طلباتي من حسابك، أو تواصل معنا مع رقم طلبك.</p></details>
</section>
</div>
</dialog>
@include('storefront.partials.custom-code', ['position' => 'body_end'])
</body>
</html>
