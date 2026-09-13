@php
    $name = \App\Support\Storefront::name($product);
    $variant = $product->variants->where('status', 'active')->firstWhere('stock', '>', 0);
    $available = $product->variants->isNotEmpty() ? (bool) $variant : $product->stock > 0;
    $isNew = $product->created_at && $product->created_at->greaterThan(now()->subDays(30));
@endphp
<article class="card{{ !empty($carouselSlide) ? ' swiper-slide' : '' }}" data-product-id="{{ $product->id }}" data-label="{{ $isNew ? 'new' : '' }}" data-sales="{{ $product->order_items_sum_quantity ?? 0 }}">
<form action="{{ route('cart.add', $product->id) }}" method="post" data-add-to-cart>
@csrf
<div class="card-photo">
    <a href="{{ route('shop.show', $product->id) }}"><img src="{{ \App\Support\Storefront::image($product->main_image) }}" alt="{{ $name }}" loading="lazy"></a>
    @if($isNew)<span class="product-badge new">جديد</span>@endif
    <button type="button" class="favorite" data-favorite="{{ $product->id }}" aria-pressed="false" aria-label="إضافة للمفضلة: {{ $name }}" onclick="toggleFavorite({{ $product->id }},this)"><i data-lucide="heart" aria-hidden="true"></i></button>
</div>
<div class="rating"><span>★</span> {{ $product->reviews_count ? number_format($product->reviews_avg_rating, 1) : '—' }} &nbsp;({{ $product->reviews_count ?? 0 }})</div>
<a class="product-name" href="{{ route('shop.show', $product->id) }}">{{ $name }}</a>
<div class="size-label">الحجم</div>
@include('storefront.partials.variants')
<b class="price" data-product-price>{{ \App\Support\Storefront::money($variant?->price ?? $product->price) }}</b>
<button class="primary add" type="submit" @disabled(!$available)><i data-lucide="shopping-cart" aria-hidden="true"></i> {{ $available ? 'أضف إلى السلة' : 'غير متوفر حالياً' }}</button>
</form>
</article>
