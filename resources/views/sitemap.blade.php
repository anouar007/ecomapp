<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
@php
  $siteUrl = isset($baseUrl) && !empty($baseUrl) ? rtrim($baseUrl, '/') : 'https://winashop.com';
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

  {{-- Home Page --}}
  <url>
    <loc>{{ $siteUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $siteUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $siteUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $siteUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $siteUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $siteUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="en-MA" href="{{ $siteUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $siteUrl }}"/>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- Shop Catalog --}}
  @php $shopUrl = $siteUrl . '/shop'; @endphp
  <url>
    <loc>{{ $shopUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $shopUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $shopUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $shopUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $shopUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $shopUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="en-MA" href="{{ $shopUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $shopUrl }}"/>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- About Page --}}
  @php $aboutUrl = $siteUrl . '/about'; @endphp
  <url>
    <loc>{{ $aboutUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $aboutUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $aboutUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $aboutUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $aboutUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $aboutUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="en-MA" href="{{ $aboutUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $aboutUrl }}"/>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- Contact & Showroom Page --}}
  @php $contactUrl = $siteUrl . '/contact'; @endphp
  <url>
    <loc>{{ $contactUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $contactUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $contactUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $contactUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $contactUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $contactUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="en-MA" href="{{ $contactUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $contactUrl }}"/>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- Category Pages --}}
  @foreach($categories as $category)
  @if($category->slug)
  @php $catUrl = $siteUrl . '/shop?category=' . urlencode($category->slug); @endphp
  <url>
    <loc>{{ $catUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $catUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $catUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $catUrl }}&amp;lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $catUrl }}&amp;lang=ar"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $catUrl }}&amp;lang=en"/>
    <xhtml:link rel="alternate" hreflang="en-MA" href="{{ $catUrl }}&amp;lang=en"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $catUrl }}"/>
    <changefreq>daily</changefreq>
    <priority>0.85</priority>
    <lastmod>{{ $category->updated_at ? $category->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach

  {{-- Brand Catalog Pages --}}
  @if(isset($brands))
  @foreach($brands as $brand)
  @if($brand->slug)
  @php $brandUrl = $siteUrl . '/shop?brand=' . urlencode($brand->slug); @endphp
  <url>
    <loc>{{ $brandUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $brandUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $brandUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $brandUrl }}&amp;lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $brandUrl }}&amp;lang=ar"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $brandUrl }}&amp;lang=en"/>
    <xhtml:link rel="alternate" hreflang="en-MA" href="{{ $brandUrl }}&amp;lang=en"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $brandUrl }}"/>
    <changefreq>weekly</changefreq>
    <priority>0.80</priority>
    <lastmod>{{ $brand->updated_at ? $brand->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach
  @endif

  {{-- Product Pages with Multilingual Links and Google Images --}}
  @foreach($products as $product)
  @php
    $prodUrl = $siteUrl . '/shop/' . ($product->slug ?: $product->id);
    $thumb = $product->thumbnail;
    $primaryImg = '';
    if (!empty($thumb)) {
        $primaryImg = str_starts_with($thumb, 'http')
            ? preg_replace('#^https?://[^/]+#', $siteUrl, $thumb)
            : ($siteUrl . '/' . ltrim($thumb, '/'));
    }
  @endphp
  <url>
    <loc>{{ $prodUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $prodUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $prodUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $prodUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $prodUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $prodUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="en-MA" href="{{ $prodUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $prodUrl }}"/>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
    <lastmod>{{ $product->updated_at ? $product->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
    
    {{-- Primary Product Image --}}
    @if(!empty($primaryImg))
    <image:image>
      <image:loc>{{ $primaryImg }}</image:loc>
      <image:title>{{ htmlspecialchars($product->name, ENT_XML1, 'UTF-8') }}</image:title>
      <image:caption>{{ htmlspecialchars($product->name . ' au Maroc — ' . setting('app_name', 'WINA SHOP') . ' Casablanca', ENT_XML1, 'UTF-8') }}</image:caption>
    </image:image>
    @endif

    {{-- Additional Gallery Images --}}
    @if($product->relationLoaded('images') && $product->images)
    @foreach($product->images->take(4) as $galImg)
    @if(!empty($galImg->image_path))
    @php
      $galUrl = str_starts_with($galImg->image_path, 'http') 
        ? preg_replace('#^https?://[^/]+#', $siteUrl, $galImg->image_path)
        : ($siteUrl . '/storage/' . ltrim($galImg->image_path, '/'));
    @endphp
    <image:image>
      <image:loc>{{ $galUrl }}</image:loc>
      <image:title>{{ htmlspecialchars($product->name . ' - Vue détaillée', ENT_XML1, 'UTF-8') }}</image:title>
      <image:caption>{{ htmlspecialchars($product->name . ' matériel professionnel garanti au Maroc', ENT_XML1, 'UTF-8') }}</image:caption>
    </image:image>
    @endif
    @endforeach
    @endif
  </url>
  @endforeach

  {{-- Dynamic CMS Pages --}}
  @if(isset($pages))
  @foreach($pages as $page)
  @if($page->slug)
  @php $pageUrl = $siteUrl . '/page/' . $page->slug; @endphp
  <url>
    <loc>{{ $pageUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $pageUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $pageUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $pageUrl }}?lang=en"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $pageUrl }}"/>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
    <lastmod>{{ $page->updated_at ? $page->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach
  @endif

</urlset>