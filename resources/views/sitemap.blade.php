<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

  @php $locales = ['en', 'fr', 'ar']; @endphp

  {{-- 1. Home Page Variants --}}
  @foreach($locales as $locale)
  <url>
    <loc>{{ url('/') }}{{ $locale != 'en' ? '?hl=' . $locale : '' }}</loc>
    @foreach($locales as $alt)
    <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ url('/') }}{{ $alt != 'en' ? '?hl=' . $alt : '' }}" />
    @endforeach
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>
  @endforeach

  {{-- 2. Shop Listing Variants --}}
  @foreach($locales as $locale)
  <url>
    <loc>{{ route('shop.index') }}{{ $locale != 'en' ? '?hl=' . $locale : '' }}</loc>
    @foreach($locales as $alt)
    <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ route('shop.index') }}{{ $alt != 'en' ? '?hl=' . $alt : '' }}" />
    @endforeach
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  @endforeach

  {{-- 3. Category Pages Variants --}}
  @foreach($categories as $category)
    @if($category->slug)
      @foreach($locales as $locale)
      <url>
        <loc>{{ route('shop.index', ['category' => $category->slug]) }}{{ $locale != 'en' ? (strpos(route('shop.index', ['category' => $category->slug]), '?') !== false ? '&' : '?') . 'hl=' . $locale : '' }}</loc>
        @foreach($locales as $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ route('shop.index', ['category' => $category->slug]) }}{{ $alt != 'en' ? (strpos(route('shop.index', ['category' => $category->slug]), '?') !== false ? '&' : '?') . 'hl=' . $alt : '' }}" />
        @endforeach
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
      </url>
      @endforeach
    @endif
  @endforeach

  {{-- 4. Product Pages Variants with Images --}}
  @foreach($products as $product)
    @foreach($locales as $locale)
    <url>
      <loc>{{ route('shop.show', $product->id) }}{{ $locale != 'en' ? '?hl=' . $locale : '' }}</loc>
      @foreach($locales as $alt)
      <xhtml:link rel="alternate" hreflang="{{ $alt }}" href="{{ route('shop.show', $product->id) }}{{ $alt != 'en' ? '?hl=' . $alt : '' }}" />
      @endforeach
      
      @if($product->main_image)
      <image:image>
        <image:loc>{{ Storage::url($product->main_image) }}</image:loc>
        <image:title>{{ $product->translated_name }}</image:title>
      </image:image>
      @endif

      <changefreq>weekly</changefreq>
      <priority>0.7</priority>
      <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
    </url>
    @endforeach
  @endforeach

</urlset>
