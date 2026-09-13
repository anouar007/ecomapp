@extends('layouts.storefront')
@section('title', 'منتجاتنا الطبيعية | تعاونية آيت أومديس')
@section('body-class', 'products')
@section('content')
<section class="shop-banner"><h1>منتجاتنا الطبيعية</h1><p><a href="{{ route('home') }}">الرئيسية</a>　‹　 المتجر</p></section>
<main class="shop-layout">
<form id="catalog-filters" class="filters" action="{{ route('shop.index') }}" method="get">
<h3><button type="button" class="filter-toggle" aria-expanded="false" aria-controls="filter-fields" onclick="toggleFilters(this)">تصفية المنتجات <span><i data-lucide="sliders-horizontal" aria-hidden="true"></i></span></button></h3>
@if(request()->filled('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
<div id="filter-fields">
<fieldset><legend>الفئة</legend>
@foreach($categories as $category)
<label>{{ \App\Support\Storefront::name($category) }} ({{ $category->products_count }})<input type="checkbox" name="category[]" value="{{ $category->slug }}" @checked(in_array($category->slug, (array) request('category', [])))></label>
@endforeach
</fieldset>
<fieldset><legend>السعر</legend>
@php
    $selectedMinPrice = max(0, min((int) request('min_price', 0), $priceCeiling));
    $selectedMaxPrice = max($selectedMinPrice, min((int) request('max_price', $priceCeiling), $priceCeiling));
@endphp
<div class="price-range" id="price-range">
<div class="price-range-track">
<input type="range" id="min-price" name="min_price" min="0" max="{{ $priceCeiling }}" value="{{ $selectedMinPrice }}" aria-label="الحد الأدنى للسعر">
<input type="range" id="max-price" name="max_price" min="0" max="{{ $priceCeiling }}" value="{{ $selectedMaxPrice }}" aria-label="الحد الأقصى للسعر">
</div>
<div class="range-label"><output id="min-price-value" for="min-price">{{ $selectedMinPrice }} درهم</output><span aria-hidden="true">—</span><output id="max-price-value" for="max-price">{{ $selectedMaxPrice }} درهم</output></div>
</div>
</fieldset>
</div>
<button class="primary" type="submit"><i data-lucide="list-filter" aria-hidden="true"></i> تطبيق الفلاتر</button><a class="reset" href="{{ route('shop.index') }}">إعادة تعيين الفلاتر</a>
</form>
<section class="catalog" aria-label="المنتجات">
<div class="catalog-bar"><span id="result-count">عرض {{ $products->total() }} منتجات</span><div>
<button class="view selected" onclick="setCatalogView(false,this)" aria-pressed="true" aria-label="عرض الشبكة"><i data-lucide="layout-grid" aria-hidden="true"></i></button><button class="view" onclick="setCatalogView(true,this)" aria-pressed="false" aria-label="عرض القائمة"><i data-lucide="list" aria-hidden="true"></i></button>
<select name="sort" form="catalog-filters" aria-label="ترتيب المنتجات" onchange="this.form.requestSubmit()">
<option value="popular" @selected(request('sort', 'popular') === 'popular')>الأكثر مبيعاً</option>
<option value="price_asc" @selected(request('sort') === 'price_asc')>السعر: الأقل أولاً</option>
<option value="price_desc" @selected(request('sort') === 'price_desc')>السعر: الأعلى أولاً</option>
<option value="newest" @selected(request('sort') === 'newest')>الأحدث</option>
</select></div></div>
<div class="cards" id="product-grid">@include('storefront.partials.product-grid')</div>
@include('storefront.partials.pagination', ['paginator' => $products])
</section>
</main>
@endsection
