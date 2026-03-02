<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

  {{-- Home Page --}}
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- Shop Index --}}
  <url>
    <loc>{{ route('shop.index') }}</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- Category Pages --}}
  @foreach($categories as $category)
  @if($category->slug)
  <url>
    <loc>{{ route('shop.index', ['category' => $category->slug]) }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach

  {{-- Product Pages --}}
  @foreach($products as $product)
  <url>
    <loc>{{ route('shop.show', $product->id) }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
    <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
  </url>
  @endforeach

</urlset>
