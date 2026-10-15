@extends('layouts.frontend')

@php
    $brandGuess = 'WINA SHOP';
    $nameLower = strtolower($product->name);
    if (str_contains($nameLower, 'sony')) $brandGuess = 'Sony';
    elseif (str_contains($nameLower, 'canon')) $brandGuess = 'Canon';
    elseif (str_contains($nameLower, 'dji')) $brandGuess = 'DJI';
    elseif (str_contains($nameLower, 'godox')) $brandGuess = 'Godox';
    elseif (str_contains($nameLower, 'lexar')) $brandGuess = 'Lexar';
    elseif (str_contains($nameLower, 'nikon')) $brandGuess = 'Nikon';
    elseif (str_contains($nameLower, 'fujifilm') || str_contains($nameLower, 'fuji')) $brandGuess = 'Fujifilm';
    elseif (str_contains($nameLower, 'blackmagic')) $brandGuess = 'Blackmagic Design';
    elseif (str_contains($nameLower, 'sigma')) $brandGuess = 'Sigma';
    elseif (str_contains($nameLower, 'tamron')) $brandGuess = 'Tamron';
    elseif (str_contains($nameLower, 'rode') || str_contains($nameLower, 'røde')) $brandGuess = 'RØDE';
    elseif (str_contains($nameLower, 'hollyland')) $brandGuess = 'Hollyland';
    elseif (str_contains($nameLower, 'smallrig')) $brandGuess = 'SmallRig';
    elseif (str_contains($nameLower, 'zhiyun')) $brandGuess = 'Zhiyun';
    elseif (str_contains($nameLower, 'insta360')) $brandGuess = 'Insta360';
    elseif (str_contains($nameLower, 'sandisk')) $brandGuess = 'SanDisk';
    elseif (str_contains($nameLower, 'aputure')) $brandGuess = 'Aputure';
    elseif (str_contains($nameLower, 'nanlite')) $brandGuess = 'Nanlite';
    elseif (str_contains($nameLower, 'manfrotto')) $brandGuess = 'Manfrotto';

    $isOccasion = str_contains($nameLower, 'occasion') || ($product->category && str_contains(strtolower($product->category->name), 'occasion'));
    $itemConditionUrl = $isOccasion ? 'https://schema.org/UsedCondition' : 'https://schema.org/NewCondition';
    
    $cleanDesc = Str::limit(strip_tags($product->description), 160) ?: ($product->name . ' disponible au Maroc chez WINA SHOP Casablanca. Matériel garanti, devis proforma avec ICE et livraison express partout au Maroc.');
    
    $productKeywords = implode(', ', [
        $product->name,
        'Acheter ' . $product->name . ' Maroc',
        'Prix ' . $product->name . ' Casablanca',
        $brandGuess . ' Maroc',
        $product->category ? $product->category->name : 'Matériel photo',
        'Vente matériel audiovisuel Maroc',
        'Garantie constructeur 2 ans',
        'Devis proforma INDH',
        'Livraison Casablanca Rabat Marrakech Maroc',
        'شراء ' . $product->name . ' المغرب',
        'سعر ' . $product->name . ' الدار البيضاء',
        'كاميرات ومعدات تصوير المغرب'
    ]);

    $allImages = [$product->thumbnail];
    if ($product->relationLoaded('images') && $product->images) {
        foreach ($product->images as $img) {
            if (!empty($img->image_path)) {
                $allImages[] = str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path);
            }
        }
    }
    $allImages = array_values(array_unique(array_filter($allImages)));
@endphp

@section('meta_title', $product->name . ' — Prix au Maroc, Achat & Garantie | ' . setting('app_name', 'WINA SHOP'))
@section('meta_description', $cleanDesc)
@section('meta_keywords', $productKeywords)
@section('meta_type', 'product')
@section('meta_image', $product->thumbnail)

@section('extra_meta')
<meta property="product:price:amount" content="{{ $product->isOnSale() ? $product->sale_price : $product->price }}">
<meta property="product:price:currency" content="{{ setting('currency_code', 'MAD') }}">
<meta property="product:availability" content="{{ $product->isInStock() ? 'in stock' : 'out of stock' }}">
<meta property="product:condition" content="{{ $isOccasion ? 'used' : 'new' }}">
<meta property="product:brand" content="{{ $brandGuess }}">
<meta property="product:retailer_item_id" content="{{ $product->sku ?? ('PROD-' . $product->id) }}">
@endsection

@section('json_ld')
@php
    $avgRating = $product->reviews()->avg('rating') ?? 0;
    $reviewCount = $product->reviews()->count();
@endphp
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Accueil",
        "item": "{{ url('/') }}"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Boutique",
        "item": "{{ route('shop.index') }}"
      }
      @if($product->category)
      ,{
        "@type": "ListItem",
        "position": 3,
        "name": "{{ addslashes($product->category->name) }}",
        "item": "{{ route('shop.index', ['category' => $product->category->slug]) }}"
      },
      {
        "@type": "ListItem",
        "position": 4,
        "name": "{{ addslashes($product->name) }}",
        "item": "{{ url()->current() }}"
      }
      @else
      ,{
        "@type": "ListItem",
        "position": 3,
        "name": "{{ addslashes($product->name) }}",
        "item": "{{ url()->current() }}"
      }
      @endif
    ]
  },
  {
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": {!! json_encode($product->name) !!},
    "image": {!! json_encode($allImages) !!},
    "description": {!! json_encode($cleanDesc) !!},
    "sku": "{{ $product->sku ?? 'WINA-' . $product->id }}",
    "mpn": "{{ $product->sku ?? 'WINA-' . $product->id }}",
    "brand": {
      "@type": "Brand",
      "name": {!! json_encode($brandGuess) !!}
    },
    @if($product->category)
    "category": {!! json_encode($product->category->name) !!},
    @endif
    "offers": {
      "@type": "Offer",
      "url": "{{ url()->current() }}",
      "priceCurrency": "{{ setting('currency_code', 'MAD') }}",
      "price": "{{ number_format($product->final_price, 2, '.', '') }}",
      "priceValidUntil": "{{ now()->addYear()->format('Y-12-31') }}",
      "itemCondition": "{{ $itemConditionUrl }}",
      "availability": "{{ $product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
      "seller": {
        "@type": "Store",
        "name": "{{ setting('app_name', 'WINA SHOP') }}"
      },
      "shippingDetails": {
        "@type": "OfferShippingDetails",
        "shippingRate": {
          "@type": "MonetaryAmount",
          "value": "0",
          "currency": "{{ setting('currency_code', 'MAD') }}"
        },
        "shippingDestination": {
          "@type": "DefinedRegion",
          "addressCountry": "MA"
        },
        "deliveryTime": {
          "@type": "ShippingDeliveryTime",
          "handlingTime": {
            "@type": "QuantitativeValue",
            "minValue": 0,
            "maxValue": 1,
            "unitCode": "d"
          },
          "transitTime": {
            "@type": "QuantitativeValue",
            "minValue": 1,
            "maxValue": 2,
            "unitCode": "d"
          }
        }
      },
      "hasMerchantReturnPolicy": {
        "@type": "MerchantReturnPolicy",
        "applicableCountry": "MA",
        "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
        "merchantReturnDays": 7,
        "returnMethod": "https://schema.org/ReturnByMail",
        "returnFees": "https://schema.org/FreeReturn"
      }
    }
    @if($reviewCount > 0)
    ,
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "{{ number_format($avgRating, 1) }}",
      "reviewCount": "{{ $reviewCount }}",
      "bestRating": "5",
      "worstRating": "1"
    }
    @endif
  }
]
</script>
@endsection

@section('content')

{{-- BREADCRUMB --}}
<section class="pdp-breadcrumb-bar">
    <div class="container">
        <nav class="pdp-breadcrumb" aria-label="breadcrumb">
            <a href="{{ url('/') }}"><i class="fas fa-home"></i></a>
            <span class="pdp-bc-sep">/</span>
            <a href="{{ route('shop.index') }}">{{ __('Catalogue') }}</a>
            @if($product->category_name)
                <span class="pdp-bc-sep">/</span>
                <a href="{{ route('shop.index', ['category' => $product->category_slug ?? optional($product->category)->slug ?? optional($product->productCategory)->slug]) }}">{{ $product->category_name }}</a>
            @endif
            <span class="pdp-bc-sep">/</span>
            <span class="pdp-bc-current">{{ Str::limit($product->name, 40) }}</span>
        </nav>
    </div>
</section>

{{-- MAIN PRODUCT LAYOUT --}}
<section class="pdp-body">
    <div class="container">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="pdp-card">
            <div class="row g-0">

                {{-- IMAGE PANEL --}}
                <div class="col-lg-6 pdp-image-panel">
                    <div class="pdp-main-image-wrap" id="zoomWrap" onmousemove="pdpZoom(event)">
                        <img id="mainImage" src="{{ $product->thumbnail }}"
                             alt="{{ $product->name }}" class="pdp-main-image">

                        <div class="pdp-badges">
                            @if(!$product->isInStock())
                                <span class="pdp-badge pdp-badge--oos">{{ __('Out of stock') }}</span>
                            @elseif($product->created_at->diffInDays(now()) < 14)
                                <span class="pdp-badge pdp-badge--new">{{ __('New') }}</span>
                            @elseif($product->isOnSale())
                                <span class="pdp-badge pdp-badge--sale">−{{ $product->discount_percentage }}%</span>
                            @endif
                        </div>
                    </div>

                    @if($product->images->count() > 0)
                    <div class="pdp-thumbs">
                        <div class="pdp-thumb active" onclick="pdpChangeImage('{{ $product->thumbnail }}', this)">
                            <img src="{{ $product->thumbnail }}" alt="{{ __('Product') }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        </div>
                        @foreach($product->images as $img)
                        <div class="pdp-thumb" onclick="pdpChangeImage('{{ $img->url }}', this)">
                            <img src="{{ $img->url }}" alt="{{ __('View') }} {{ $loop->iteration + 1 }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- INFO PANEL --}}
                <div class="col-lg-6 pdp-info-panel">
                    @if($product->category_name)
                    <a href="{{ route('shop.index', ['category' => $product->category_slug ?? optional($product->category)->slug ?? optional($product->productCategory)->slug]) }}" class="pdp-cat-label d-inline-block text-decoration-none">{{ $product->category_name }}</a>
                    @endif

                    <h1 class="pdp-title">{{ $product->name }}</h1>

                    @if(isset($reviews) && $reviews->total() > 0)
                    <div class="pdp-rating-row">
                        <div class="pdp-stars">
                            @php $avg = $product->reviews()->avg('rating') ?? 0; @endphp
                            @for($i = 0; $i < 5; $i++)
                                <i class="fa{{ $i < round($avg) ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                        <span class="pdp-rating-count">{{ number_format($avg, 1) }} ({{ $reviews->total() }} {{ __('reviews') }})</span>
                    </div>
                    @endif

                    <div class="pdp-price-row">
                        @if($product->isOnSale())
                            <span class="pdp-price-main">{{ $product->formatted_sale_price }}</span>
                            <span class="pdp-price-old">{{ $product->formatted_price }}</span>
                            <span class="pdp-discount-badge">−{{ $product->discount_percentage }}%</span>
                        @else
                            <span class="pdp-price-main">{{ $product->formatted_price }}</span>
                        @endif
                        @if($product->isInStock())
                            <span class="pdp-stock-badge pdp-stock-badge--in">
                                <i class="fas fa-check-circle me-1"></i>{{ __('In stock') }}
                            </span>
                        @else
                            <span class="pdp-stock-badge pdp-stock-badge--out">
                                <i class="fas fa-times-circle me-1"></i>{{ __('Out of stock') }}
                            </span>
                        @endif
                    </div>

                    @if($product->isInStock())
                    @php
                        $isInCart = isset(session('cart', [])[$product->id]);
                        $locale = app()->getLocale();
                        if ($locale === 'ar') {
                            $inCartLabel = 'في السلة';
                            $notInCartLabel = 'أضف إلى السلة';
                        } elseif ($locale === 'en') {
                            $inCartLabel = 'In Cart';
                            $notInCartLabel = 'Add to Cart';
                        } else {
                            $inCartLabel = 'Dans le panier';
                            $notInCartLabel = 'Ajouter au panier';
                        }
                    @endphp
                    <form id="addToCartForm" onsubmit="pdpAddToCart(event)">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="pdp-cart-row d-flex flex-row flex-nowrap align-items-stretch w-100">
                            <div class="pdp-qty-wrap flex-shrink-0">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="pdpQty" value="1"
                                       min="1" max="{{ $product->stock }}" class="pdp-qty-input">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button type="submit" id="addToCartBtn" 
                                    class="pdp-add-btn {{ $isInCart ? 'is-in-cart' : '' }} flex-grow-1 text-nowrap" 
                                    data-product-id="{{ $product->id }}" 
                                    title="{{ $isInCart ? $inCartLabel : $notInCartLabel }}">
                                <i class="fas {{ $isInCart ? 'fa-check' : 'fa-cart-plus' }} me-2"></i>
                                <span id="addToCartText">{{ $isInCart ? $inCartLabel : $notInCartLabel }}</span>
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="pdp-cart-row d-flex flex-row flex-nowrap align-items-stretch w-100">
                        <button type="button" id="addToCartBtn" class="pdp-add-btn pdp-add-btn--out w-100 text-nowrap" disabled>
                            <i class="fas fa-ban me-2"></i>
                            <span id="addToCartText">{{ __('Out of stock') }}</span>
                        </button>
                    </div>
                    @endif

                    {{-- Commander Button (Shown when cart has at least 1 product) --}}
                    @php
                        $cartTotalCount = array_sum(array_column(session('cart', []), 'quantity'));
                        $currentLocale = app()->getLocale();
                        $commanderLabel = $currentLocale === 'ar' ? 'إتمام الطلب' : ($currentLocale === 'en' ? 'Checkout' : 'Commander');
                    @endphp
                    <div id="pdpCommanderWrap" class="pdp-commander-wrap mt-2 {{ $cartTotalCount > 0 ? '' : 'd-none' }}">
                        <button type="button" id="pdpCommanderBtn" class="pdp-commander-btn w-100" onclick="pdpCommanderAction(event)">
                            <i class="fas fa-shopping-bag me-2"></i>
                            <span id="pdpCommanderText">{{ $commanderLabel }}</span>
                            <i class="fas {{ $currentLocale === 'ar' ? 'fa-arrow-left ms-2' : 'fa-arrow-right ms-2' }}"></i>
                        </button>
                    </div>

                    @php
                        $cleanDescText = $product->clean_description;
                        $shortSnippet = null;
                        if (!empty($cleanDescText)) {
                            $firstLine = trim(explode("\n", $cleanDescText)[0] ?? $cleanDescText);
                            $shortSnippet = Str::limit($firstLine, 210);
                        }
                    @endphp
                    @if($shortSnippet)
                    <div class="pdp-divider my-3"></div>
                    <div class="pdp-short-desc">
                        <p class="mb-1">{{ $shortSnippet }}</p>
                        @if(strlen($cleanDescText) > 210)
                            <a href="#pdp-full-description" class="pdp-read-more-link">
                                {{ __('Voir la fiche complète') }} <i class="fas fa-arrow-down ms-1"></i>
                            </a>
                        @endif
                    </div>
                    @endif

                    <div class="pdp-trust-row mt-3">
                        <div class="pdp-trust-pill"><i class="fas fa-shield-halved"></i> {{ __('2-Year Warranty') }}</div>
                        <div class="pdp-trust-pill"><i class="fas fa-truck-fast"></i> {{ __('Secure Delivery 24/48h') }}</div>
                        <div class="pdp-trust-pill"><i class="fas fa-file-invoice"></i> {{ __('ICE Invoicing') }}</div>
                        <div class="pdp-trust-pill"><i class="fas fa-video"></i> {{ __('Showroom Demo Casablanca') }}</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- DEDICATED PRODUCT DESCRIPTION CONTAINER --}}
        @php
            $cleanDescription = $product->clean_description;
        @endphp
        @if(!empty($cleanDescription))
        <div class="pdp-description-wrapper mt-4" id="pdp-full-description">
            <div class="pdp-section-card pdp-description-card">
                <div class="pdp-description-header d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom flex-wrap gap-2">
                    <h2 class="pdp-section-title mb-0 d-flex align-items-center">
                        <i class="fas fa-file-lines me-2 text-danger"></i>
                        <span>{{ __('Description détaillée') }}</span>
                    </h2>
                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill small">
                        <i class="fas fa-check-double text-success me-1"></i>{{ __('Fiche technique officielle') }}
                    </span>
                </div>
                <div class="pdp-description pdp-description--clean">
                    @foreach(explode("\n\n", $cleanDescription) as $para)
                        @if(trim($para))
                            <p class="pdp-clean-p">{!! nl2br(e(trim($para))) !!}</p>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- REVIEWS --}}
        <div class="row g-4 mt-4">
            <div class="col-lg-8">
                <div class="pdp-section-card">
                    <h3 class="pdp-section-title">
                        <i class="fas fa-star me-2 text-accent"></i>
                        {{ __('Customer reviews') }} <span class="pdp-section-count">({{ $reviews->total() }})</span>
                    </h3>

                    @forelse($reviews as $review)
                    <div class="pdp-review">
                        <div class="pdp-review-header">
                            <span class="pdp-review-title">{{ $review->title }}</span>
                            <div class="pdp-review-stars">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fa{{ $i < $review->rating ? 's' : 'r' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="pdp-review-body">{{ $review->comment }}</p>
                        <div class="pdp-review-meta">
                            {{ __('By') }} <strong>{{ $review->customer_name }}</strong> · {{ $review->created_at->format('d M Y') }}
                        </div>
                    </div>
                    @empty
                    <div class="pdp-review-empty">
                        <i class="far fa-comment-dots"></i>
                        <p>{{ __('No reviews yet. Be the first!') }}</p>
                    </div>
                    @endforelse

                    @if($reviews->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $reviews->links() }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Write Review --}}
            <div class="col-lg-4">
                <div class="pdp-section-card">
                    <h3 class="pdp-section-title">
                        <i class="fas fa-pen me-2 text-accent"></i>{{ __('Write a review') }}
                    </h3>
                    <form action="{{ route('reviews.store') }}" method="POST" class="pdp-review-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        @guest
                        <div class="pdp-form-group">
                            <label class="pdp-form-label">{{ __('Your name') }}</label>
                            <input type="text" name="customer_name" class="pdp-form-input"
                                   placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: محمد الأمين' : 'ex. Jean Dupont' }}" value="{{ old('customer_name') }}" required>
                            @error('customer_name')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pdp-form-group">
                            <label class="pdp-form-label">{{ __('Your email') }}</label>
                            <input type="email" name="customer_email" class="pdp-form-input"
                                   placeholder="email@exemple.com" value="{{ old('customer_email') }}" required>
                            @error('customer_email')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>
                        @endguest

                        <div class="pdp-form-group">
                            <label class="pdp-form-label">{{ __('Rating') }}</label>
                            <select name="rating" class="pdp-form-select" required>
                                <option value="5">★★★★★ {{ __('Excellent (5/5)') }}</option>
                                <option value="4">★★★★☆ {{ __('Very good (4/5)') }}</option>
                                <option value="3">★★★☆☆ {{ __('Good (3/5)') }}</option>
                                <option value="2">★★☆☆☆ {{ __('Average (2/5)') }}</option>
                                <option value="1">★☆☆☆☆ {{ __('Bad (1/5)') }}</option>
                            </select>
                        </div>

                        <div class="pdp-form-group">
                            <label class="pdp-form-label">{{ __('Title') }}</label>
                            <input type="text" name="title" class="pdp-form-input"
                                   placeholder="{{ __('Summary of your experience') }}" value="{{ old('title') }}" required>
                            @error('title')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="pdp-form-group">
                            <label class="pdp-form-label">{{ __('Comment') }}</label>
                            <textarea name="comment" class="pdp-form-input" rows="4"
                                      placeholder="{{ __('How did you find this product?') }}" required>{{ old('comment') }}</textarea>
                            @error('comment')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="pdp-submit-btn w-100">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Publish review') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RELATED PRODUCTS --}}
        @if($relatedProducts->count() > 0)
        <div class="pdp-related mt-5">
            <div class="pdp-related-header">
                <h3 class="pdp-related-title">{{ __('Related products') }}</h3>
                <a href="{{ route('shop.index') }}" class="pdp-related-link">
                    {{ __('See all') }} <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left ms-1' : 'fa-arrow-right ms-1' }}"></i>
                </a>
            </div>
            <div class="row g-2 home-products-grid">
                @foreach($relatedProducts as $related)
                @php
                    $relInCart = isset(session('cart', [])[$related->id]);
                    $isAr = app()->getLocale() === 'ar';
                @endphp
                <div class="col-6 col-md-3">
                    <div class="featured-pro-card selection-card h-100 d-flex flex-column justify-content-between position-relative">
                        {{-- Stock Status Badge at Top Left of Card --}}
                        @if($related->isInStock())
                            <span class="featured-stock-pill featured-stock-badge-corner">
                                <span class="stock-pulse-dot"></span>
                                <span>{{ $isAr ? 'متوفر' : 'EN STOCK' }}</span>
                            </span>
                        @else
                            <span class="featured-stock-pill featured-stock-pill--out featured-stock-badge-corner">
                                <i class="fas fa-times-circle text-danger" style="font-size: 10px;"></i>
                                <span>{{ $isAr ? 'نفذت الكمية' : 'RUPTURE DE STOCK' }}</span>
                            </span>
                        @endif

                        {{-- Product Image with clean white background --}}
                        <div class="featured-pro-img-box mb-2">
                            <a href="{{ route('shop.show', $related->slug ?: $related->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                <img src="{{ $related->thumbnail }}"
                                     alt="{{ $related->name }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                            </a>
                        </div>
                        <div class="featured-pro-body mb-1 flex-grow-1 d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="featured-pro-title">
                                    <a href="{{ route('shop.show', $related->slug ?: $related->id) }}">{{ $related->name }}</a>
                                </h3>
                            </div>
                            <div>
                                <div class="pcard-price mb-0">
                                    @if($related->isOnSale())
                                        <span class="pcard-price-current text-nowrap">{{ $related->formatted_sale_price }}</span>
                                        <span class="pcard-price-old text-nowrap">{{ $related->formatted_price }}</span>
                                    @elseif($related->price && $related->price > 0)
                                        <span class="pcard-price-current text-nowrap">{{ $related->formatted_price }}</span>
                                        <span class="pcard-price-old text-nowrap invisible" aria-hidden="true">&nbsp;</span>
                                    @else
                                        <span class="pcard-price-current text-nowrap">{{ __('Sur devis') }}</span>
                                        <span class="pcard-price-old text-nowrap invisible" aria-hidden="true">&nbsp;</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="featured-pro-action mt-auto pt-0">
                            @if($related->isInStock())
                                <button type="button" 
                                        class="btn w-100 featured-pro-btn {{ $relInCart ? 'is-in-cart' : '' }}" 
                                        onclick="addToCart({{ $related->id }}, 1, this)"
                                        data-product-id="{{ $related->id }}"
                                        title="{{ $relInCart ? ($isAr ? 'في السلة' : 'Dans le panier') : ($isAr ? 'أضف إلى السلة' : 'Ajouter au panier') }}">
                                    @if($relInCart)
                                        <i class="fas fa-check" style="font-size: 11px;"></i>
                                        <span>{{ $isAr ? 'في السلة' : 'Dans le panier' }}</span>
                                    @else
                                        <span>{{ $isAr ? 'أضف إلى السلة' : 'Ajouter au panier' }}</span>
                                    @endif
                                </button>
                            @else
                                <button type="button" 
                                        class="btn w-100 featured-pro-btn featured-pro-btn--out" 
                                        disabled 
                                        title="{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}">
                                    <i class="fas fa-ban" style="font-size: 11px;"></i>
                                    <span>{{ $isAr ? 'نفذت الكمية' : 'Rupture de stock' }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

@endsection

@push('scripts')
<script>
function pdpZoom(e) {
    const wrap = document.getElementById('zoomWrap');
    const img  = document.getElementById('mainImage');
    if (!img) return;
    const x = (e.offsetX / wrap.offsetWidth)  * 100;
    const y = (e.offsetY / wrap.offsetHeight) * 100;
    img.style.transformOrigin = `${x}% ${y}%`;
}

function pdpChangeImage(src, thumb) {
    const mainImg = document.getElementById('mainImage');
    if (!mainImg) return;
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = src;
        mainImg.style.opacity = '1';
    }, 120);
    document.querySelectorAll('.pdp-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

function pdpChangeQty(delta) {
    const inp = document.getElementById('pdpQty');
    const max = parseInt(inp.max) || 9999;
    const val = Math.min(max, Math.max(1, parseInt(inp.value) + delta));
    inp.value = val;
}

function pdpAddToCart(event) {
    event.preventDefault();
    const btn      = document.getElementById('addToCartBtn');
    const qtyInput = document.getElementById('pdpQty');
    const quantity = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
    const productId = {{ $product->id }};

    if (typeof window.addToCart === 'function') {
        const promise = window.addToCart(productId, quantity, btn);
        if (promise && typeof promise.then === 'function') {
            promise.then(() => {
                const commanderWrap = document.getElementById('pdpCommanderWrap');
                if (commanderWrap) {
                    commanderWrap.classList.remove('d-none');
                }
            });
        }
    }
}

function pdpCommanderAction(event) {
    if (event) event.preventDefault();
    const btn = document.getElementById('pdpCommanderBtn');
    if (!btn || btn.disabled) return;

    const productId = {{ $product->id }};
    const addBtn = document.getElementById('addToCartBtn');
    const isProductInStock = {{ $product->isInStock() ? 'true' : 'false' }};
    const isCurrentInCart = addBtn ? addBtn.classList.contains('is-in-cart') : false;

    const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    const redirectingText = isAr ? 'جاري التحويل...' : 'Redirection...';

    // If product is ALREADY in cart, or out of stock -> go directly to checkout
    if (isCurrentInCart || !isProductInStock) {
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>${redirectingText}</span>`;
        window.location.href = "{{ route('checkout.index') }}";
        return;
    }

    // Product is NOT in cart and IS in stock -> add it first with chosen quantity, then go to checkout!
    const qtyInput = document.getElementById('pdpQty');
    const quantity = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>${redirectingText}</span>`;

    fetch(`{{ url('/cart/add') }}/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Error adding product to cart');
        }
        if (typeof window.syncAllProductCartButtons === 'function' && data.cartProductIds) {
            window.syncAllProductCartButtons(data.cartProductIds);
        }
        window.location.href = "{{ route('checkout.index') }}";
    })
    .catch(err => {
        btn.disabled = false;
        const defaultLabel = isAr ? 'إتمام الطلب' : 'Commander';
        const arrowClass = isAr ? 'fa-arrow-left ms-2' : 'fa-arrow-right ms-2';
        btn.innerHTML = `<i class="fas fa-shopping-bag me-2"></i><span>${defaultLabel}</span><i class="fas ${arrowClass}"></i>`;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: isAr ? 'خطأ' : 'Erreur',
                text: err.message || (isAr ? 'تعذر إضافة المنتج إلى السلة' : 'Impossible d\'ajouter le produit au panier')
            });
        } else {
            alert(err.message || 'Impossible d\'ajouter le produit au panier');
        }
    });
}
</script>
@endpush
