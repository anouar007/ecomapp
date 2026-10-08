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

  {{-- Shop Catalog --}}
  <url>
    <loc>{{ route('shop.index') }}</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>

  {{-- About Page --}}
  @if(Route::has('about'))
  <url>
    <loc>{{ route('about') }}</loc>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>
  @endif

  {{-- Contact & Showroom Page --}}
  @if(Route::has('contact'))
  <url>
    <loc>{{ route('contact') }}</loc>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ now()->toAtomString() }}</lastmod>
  </url>
  @endif

  {{-- Category Pages --}}
  @foreach($categories as $category)
  @if($category->slug)
  <url>
    <loc>{{ route('shop.index', ['category' => $category->slug]) }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ $category->updated_at ? $category->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach

  {{-- Product Pages with Google Images --}}
  @foreach($products as $product)
  <url>
    <loc>{{ route('shop.show', $product->id) }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod>{{ $product->updated_at ? $product->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
    @if($product->thumbnail)
    <image:image>
      <image:loc>{{ str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : url($product->thumbnail) }}</image:loc>
      <image:title>{{ htmlspecialchars($product->name, ENT_XML1, 'UTF-8') }}</image:title>
      <image:caption>{{ htmlspecialchars($product->name . ' au Maroc — ' . setting('app_name', 'WINA SHOP'), ENT_XML1, 'UTF-8') }}</image:caption>
    </image:image>
    @endif
  </url>
  @endforeach

  {{-- Dynamic CMS Pages --}}
  @if(isset($pages))
  @foreach($pages as $page)
  @if($page->slug)
  <url>
    <loc>{{ url('/page/' . $page->slug) }}</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
    <lastmod>{{ $page->updated_at ? $page->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
  </url>
  @endif
  @endforeach
  @endif

</urlset>
