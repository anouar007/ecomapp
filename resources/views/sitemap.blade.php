<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

  {{-- Home Page --}}
  <url>
    <loc>{{ url('/') }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ url('/') }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ url('/') }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ url('/') }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ url('/') }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/') }}"/>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- Shop Catalog --}}
  <url>
    <loc>{{ route('shop.index') }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ route('shop.index') }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ route('shop.index') }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ route('shop.index') }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ route('shop.index') }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ route('shop.index') }}"/>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- About Page --}}
  @if(Route::has('about'))
  <url>
    <loc>{{ route('about') }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ route('about') }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ route('about') }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ route('about') }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ route('about') }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ route('about') }}"/>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>
  @endif

  {{-- Contact & Showroom Page --}}
  @if(Route::has('contact'))
  <url>
    <loc>{{ route('contact') }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ route('contact') }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ route('contact') }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ route('contact') }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ route('contact') }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ route('contact') }}"/>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>
  @endif

  {{-- Category Pages --}}
  @foreach($categories as $category)
  @if($category->slug)
  @php $catUrl = route('shop.index', ['category' => $category->slug]); @endphp
  <url>
    <loc>{{ $catUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $catUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $catUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $catUrl }}&amp;lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $catUrl }}&amp;lang=ar"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $catUrl }}"/>
    <changefreq>daily</changefreq>
    <priority>0.85</priority>
    <lastmod>{{ $category->updated_at ? $category->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach

  {{-- Product Pages with Multilingual Links and Google Images --}}
  @foreach($products as $product)
  @php $prodUrl = route('shop.show', $product->id); @endphp
  <url>
    <loc>{{ $prodUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $prodUrl }}"/>
    <xhtml:link rel="alternate" hreflang="fr-MA" href="{{ $prodUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $prodUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="ar-MA" href="{{ $prodUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $prodUrl }}"/>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
    <lastmod>{{ $product->updated_at ? $product->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
    
    {{-- Primary Product Image --}}
    @if($product->thumbnail)
    <image:image>
      <image:loc>{{ str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : url($product->thumbnail) }}</image:loc>
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
        ? $galImg->image_path 
        : asset('storage/' . $galImg->image_path);
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
  @php $pageUrl = url('/page/' . $page->slug); @endphp
  <url>
    <loc>{{ $pageUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $pageUrl }}"/>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $pageUrl }}?lang=ar"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $pageUrl }}"/>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
    <lastmod>{{ $page->updated_at ? $page->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach
  @endif

</urlset>
