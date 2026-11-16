<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!-- Developed by Elegant Boost (https://elegantboost.com/) -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

@php
    $addLang = function (string $baseUrl, string $lang): string {
        $sep = str_contains($baseUrl, '?') ? '&' : '?';
        return $baseUrl . $sep . 'lang=' . $lang;
    };
@endphp

  {{-- Home Page --}}
  @php $homeUrl = url('/'); @endphp
  <url>
    <loc>{{ $homeUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $addLang($homeUrl, 'fr') }}" />
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $addLang($homeUrl, 'fr') }}" />
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $addLang($homeUrl, 'ar') }}" />
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $addLang($homeUrl, 'ar') }}" />
    <xhtml:link rel="alternate" hreflang="en" href="{{ $addLang($homeUrl, 'en') }}" />
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $homeUrl }}" />
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- Shop Index --}}
  @php $shopUrl = route('shop.index'); @endphp
  <url>
    <loc>{{ $shopUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $addLang($shopUrl, 'fr') }}" />
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $addLang($shopUrl, 'fr') }}" />
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $addLang($shopUrl, 'ar') }}" />
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $addLang($shopUrl, 'ar') }}" />
    <xhtml:link rel="alternate" hreflang="en" href="{{ $addLang($shopUrl, 'en') }}" />
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $shopUrl }}" />
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- Category Pages --}}
  @foreach($categories as $category)
  @if($category->slug)
  @php $catUrl = route('shop.index', ['category' => $category->slug]); @endphp
  <url>
    <loc>{{ $catUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $addLang($catUrl, 'fr') }}" />
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $addLang($catUrl, 'fr') }}" />
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $addLang($catUrl, 'ar') }}" />
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $addLang($catUrl, 'ar') }}" />
    <xhtml:link rel="alternate" hreflang="en" href="{{ $addLang($catUrl, 'en') }}" />
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $catUrl }}" />
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach

  {{-- Product Pages --}}
  @foreach($products as $product)
  @php $prodUrl = route('shop.show', $product->id); @endphp
  <url>
    <loc>{{ $prodUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $addLang($prodUrl, 'fr') }}" />
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $addLang($prodUrl, 'fr') }}" />
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $addLang($prodUrl, 'ar') }}" />
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $addLang($prodUrl, 'ar') }}" />
    <xhtml:link rel="alternate" hreflang="en" href="{{ $addLang($prodUrl, 'en') }}" />
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $prodUrl }}" />
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
    <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
  </url>
  @endforeach

</urlset>
