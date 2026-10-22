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
    
    $firstLineDesc = trim(explode("\n", $product->clean_description)[0] ?? '');
    $cleanDesc = Str::limit($firstLineDesc, 140) ?: ($product->name . ' disponible au Maroc chez WINA SHOP Casablanca.');
    $cleanDesc .= ' Prix: ' . number_format($product->final_price, 2, '.', ' ') . ' MAD. Garantie officielle, devis proforma INDH, facture ICE, livraison 24h/48h partout au Maroc. شراء مع التوصيل والدفع عند الاستلام.';

    $extractedSpecs = [];
    if (preg_match_all('/<div class="pdp-spec-label"><i[^>]*><\/i>\s*([^<]+)<\/div>\s*<div class="pdp-spec-value">([^<]+)<\/div>/is', $product->description, $mSpecs)) {
        foreach ($mSpecs[1] as $idx => $sName) {
            $extractedSpecs[] = [
                '@type' => 'PropertyValue',
                'name' => html_entity_decode(trim($sName), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'value' => html_entity_decode(trim($mSpecs[2][$idx]), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            ];
        }
    }
    
    $productKeywords = implode(', ', [
        $product->name,
        'Acheter ' . $product->name . ' Maroc',
        'Prix ' . $product->name . ' Casablanca',
        $brandGuess . ' Maroc',
        $product->category ? $product->category->name : 'Matériel photo',
        'Vente matériel audiovisuel Maroc',
        'Garantie constructeur officielle',
        'Devis proforma INDH',
        'Facture avec ICE',
        'Livraison Casablanca Rabat Marrakech Tanger Agadir Maroc',
        'شراء ' . $product->name . ' المغرب',
        'سعر ' . $product->name . ' الدار البيضاء',
        'كاميرات ومعدات تصوير المغرب',
        'متجر وينا شوب كازابلانكا',
        'Buy ' . $product->name . ' Morocco',
        $product->name . ' price in Morocco',
        'Camera shop Casablanca Morocco',
        'Taman ' . $product->name . ' maroc',
        'Chri ' . $product->name . ' f lmaghrib'
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
@if(!empty($product->thumbnail))
<link rel="preload" as="image" href="{{ $product->thumbnail }}" fetchpriority="high">
@endif
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
    "alternateName": [
      {!! json_encode($product->name . ' Maroc') !!},
      {!! json_encode('Acheter ' . $product->name . ' Casablanca') !!},
      {!! json_encode('شراء ' . $product->name . ' المغرب') !!},
      {!! json_encode('سعر ' . $product->name . ' في المغرب') !!},
      {!! json_encode('Buy ' . $product->name . ' Morocco') !!},
      {!! json_encode($brandGuess . ' ' . $product->name) !!}
    ],
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
    @if(!empty($extractedSpecs))
    "additionalProperty": {!! json_encode($extractedSpecs) !!},
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
        "@type": "CameraStore",
        "@id": "{{ url('/') }}#organization",
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
  },
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": {!! json_encode('Où acheter ' . $product->name . ' au Maroc avec garantie officielle ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode(setting('app_name', 'WINA SHOP') . ' propose ' . $product->name . ' avec garantie constructeur, disponible dans notre showroom à Casablanca (Al Oulfa) et en livraison express 24h-48h partout au Maroc (Rabat, Marrakech, Tanger, Fès, Agadir, Oujda).') !!}
        }
      },
      {
        "@type": "Question",
        "name": {!! json_encode('Quel est le prix et le délai de livraison de ' . $product->name . ' au Maroc ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('Le prix de ' . $product->name . ' est de ' . number_format($product->final_price, 2, '.', ' ') . ' MAD chez WINA SHOP. La livraison est effectuée en 24h à 48h partout au Maroc avec option de paiement à la livraison (Cash on Delivery).') !!}
        }
      },
      {
        "@type": "Question",
        "name": {!! json_encode('Proposez-vous un devis proforma INDH ou une facture avec ICE pour ' . $product->name . ' ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('Oui, WINA SHOP délivre des devis proforma certifiés éligibles aux dossiers INDH ainsi que des factures officielles avec identifiant fiscal (ICE) pour sociétés, auto-entrepreneurs et vidéastes professionnels.') !!}
        }
      },
      {
        "@type": "Question",
        "name": {!! json_encode('كيف يمكنني طلب ' . $product->name . ' في المغرب وما هي طرق الدفع؟') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('يمكنك شراء ' . $product->name . ' مباشرة عبر متجر WINA SHOP أو بالتواصل عبر الواتساب على الرقم ' . setting('company_phone', '+212 629-035777') . '. نوفر الدفع نقداً عند الاستلام بعد معاينة المنتج مع التوصيل السريع لكافة المدن المغربية.') !!}
        }
      }
      @if(!empty($extractedSpecs))
      @php
          $specsAnswer = collect($extractedSpecs)->map(fn($s) => $s['name'] . ': ' . $s['value'])->implode(', ');
      @endphp
      ,{
        "@type": "Question",
        "name": {!! json_encode('Quelles sont les spécifications techniques officielles de ' . $product->name . ' ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('Spécifications officielles de ' . $product->name . ' disponibles chez WINA SHOP Maroc : ' . $specsAnswer . '.') !!}
        }
      }
      @endif
    ]
  }
]
</script>
@endsection

@section('content')

{{-- MAIN PRODUCT LAYOUT --}}
<section class="pdp-body">
    <div class="container">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
        @endif

        <div class="pdp-card">
            <div class="row g-0">

                {{-- IMAGE PANEL --}}
                <div class="col-lg-6 pdp-image-panel">
                    <div class="pdp-main-image-wrap" id="zoomWrap" onmousemove="pdpZoom(event)">
                        <img id="mainImage" src="{{ $product->thumbnail }}"
                             alt="{{ $product->name }}" width="600" height="600" class="pdp-main-image" fetchpriority="high" decoding="async">

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

                    @php
                        $galleryImages = collect();
                        if (!empty($product->thumbnail)) {
                            $galleryImages->push($product->thumbnail);
                        }
                        if ($product->images && $product->images->count() > 0) {
                            foreach ($product->images as $img) {
                                if (!empty($img->url) && !$galleryImages->contains($img->url)) {
                                    $galleryImages->push($img->url);
                                }
                            }
                        }
                        // Enforce max 4 images
                        $galleryImages = $galleryImages->take(4);
                    @endphp

                    @if($galleryImages->count() > 1)
                    <div class="pdp-thumbs" id="pdpThumbs">
                        @foreach($galleryImages as $imgUrl)
                        <div class="pdp-thumb {{ $loop->first ? 'active' : '' }}" onclick="pdpChangeImage('{{ $imgUrl }}', this)" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')pdpChangeImage('{{ $imgUrl }}', this)" aria-label="{{ $product->name }} {{ $loop->iteration }}">
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }} {{ $loop->iteration }}" width="76" height="76" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- INFO PANEL --}}
                <div class="col-lg-6 pdp-info-panel">
                    @if($product->category_name)
                        <a href="{{ route('shop.index', ['category' => $product->category_slug ?? optional($product->category)->slug ?? optional($product->productCategory)->slug]) }}" class="pdp-cat-label d-inline-block text-decoration-none mb-2">{{ $product->category_name }}</a>
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
                        <div class="pdp-price-group">
                            @if($product->isOnSale())
                                <span class="pdp-discount-badge">−{{ $product->discount_percentage }}%</span>
                            @endif

                            <div class="pdp-price-main-line d-flex align-items-center">
                                <span class="pdp-price-main">{{ $product->isOnSale() ? $product->formatted_sale_price : $product->formatted_price }}</span>

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

                            @if($product->isOnSale())
                                <span class="pdp-price-old">{{ $product->formatted_price }}</span>
                            @endif
                        </div>
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
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(-1)" aria-label="{{ __('Diminuer la quantité') }}">
                                    <i class="fas fa-minus" aria-hidden="true"></i>
                                </button>
                                <label for="pdpQty" class="visually-hidden">{{ __('Quantité') }}</label>
                                <input type="number" name="quantity" id="pdpQty" value="1"
                                       min="1" max="{{ $product->stock }}" class="pdp-qty-input" aria-label="{{ __('Quantité') }}">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(1)" aria-label="{{ __('Augmenter la quantité') }}">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                            <button type="submit" id="addToCartBtn" 
                                    class="pdp-add-btn {{ $isInCart ? 'is-in-cart' : '' }} flex-grow-1 text-nowrap shadow-none" 
                                    data-product-id="{{ $product->id }}" 
                                    title="{{ $isInCart ? $inCartLabel : $notInCartLabel }}">
                                <i class="fas {{ $isInCart ? 'fa-check' : 'fa-cart-plus' }} me-2"></i>
                                <span id="addToCartText">{{ $isInCart ? $inCartLabel : $notInCartLabel }}</span>
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="pdp-cart-row d-flex flex-row flex-nowrap align-items-stretch w-100">
                        <button type="button" id="addToCartBtn" class="pdp-add-btn pdp-add-btn--out w-100 text-nowrap shadow-none" disabled>
                            <i class="fas fa-ban me-2"></i>
                            <span id="addToCartText">{{ __('Out of stock') }}</span>
                        </button>
                    </div>
                    @endif

                    {{-- Acheter maintenant / Buy Now Button (Always visible on product page) --}}
                    @php
                        $cartTotalCount = array_sum(array_column(session('cart', []), 'quantity'));
                        $currentLocale = app()->getLocale();
                        $commanderLabel = $currentLocale === 'ar' ? 'اشتري الآن' : ($currentLocale === 'en' ? 'Buy now' : 'Acheter maintenant');
                    @endphp
                    <div id="pdpCommanderWrap" class="pdp-commander-wrap mt-2">
                        <button type="button" id="pdpCommanderBtn" class="pdp-commander-btn w-100 shadow-none" onclick="pdpCommanderAction(event)">
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

                    @if(!empty($product->sku))
                    <div class="pdp-sku-trust-box mt-3">
                        <button type="button" 
                                class="pdp-sku-badge" 
                                id="pdpSkuCopyBtn" 
                                onclick="copyPdpSku('{{ e($product->sku) }}')" 
                                title="{{ __('Cliquer pour copier le code SKU') }}"
                                aria-label="{{ __('Code SKU : ') }}{{ $product->sku }}">
                            <i class="fas fa-barcode pdp-sku-icon"></i>
                            <span class="pdp-sku-label">{{ __('Code SKU') }} :</span>
                            <span class="pdp-sku-value font-monospace" id="pdpSkuValue">{{ $product->sku }}</span>
                            <i class="far fa-copy pdp-sku-copy-icon ms-1" id="pdpSkuCopyIcon"></i>
                            <span class="pdp-sku-copied-tooltip" id="pdpSkuCopiedTooltip">{{ __('Copié !') }}</span>
                        </button>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- DEDICATED PRODUCT DESCRIPTION CONTAINER --}}
        @php
            $cleanDescription = $product->clean_description;
        @endphp
        @if(!empty($cleanDescription) || !empty(trim($product->description ?? '')))
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
                <div class="pdp-description pdp-description--clean entry-content">
                    @if(strip_tags($product->description) !== $product->description)
                        {!! $product->lighthouse_safe_description_html !!}
                    @else
                        @foreach(explode("\n\n", $cleanDescription) as $para)
                            @if(trim($para))
                                <p class="pdp-clean-p">{!! nl2br(e(trim($para))) !!}</p>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- REVIEWS --}}
        <div class="row g-4 mt-4">
            <div class="col-lg-8">
                <div class="pdp-section-card">
                    <h2 class="pdp-section-title">
                        <i class="fas fa-star me-2 text-accent"></i>
                        {{ __('Customer reviews') }} <span class="pdp-section-count">({{ $reviews->total() }})</span>
                    </h2>

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
                    <h2 class="pdp-section-title">
                        <i class="fas fa-pen me-2 text-accent"></i>{{ __('Write a review') }}
                    </h2>
                    <form action="{{ route('reviews.store') }}" method="POST" class="pdp-review-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        @guest
                        <div class="pdp-form-group">
                            <label for="reviewCustomerName" class="pdp-form-label">{{ __('Your name') }}</label>
                            <input type="text" id="reviewCustomerName" name="customer_name" class="pdp-form-input"
                                   placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: محمد الأمين' : 'ex. Jean Dupont' }}" value="{{ old('customer_name') }}" required>
                            @error('customer_name')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pdp-form-group">
                            <label for="reviewCustomerEmail" class="pdp-form-label">{{ __('Your email') }}</label>
                            <input type="email" id="reviewCustomerEmail" name="customer_email" class="pdp-form-input"
                                   placeholder="email@exemple.com" value="{{ old('customer_email') }}" required>
                            @error('customer_email')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>
                        @endguest

                        <div class="pdp-form-group">
                            <label for="reviewRating" class="pdp-form-label">{{ __('Rating') }}</label>
                            <select id="reviewRating" name="rating" class="pdp-form-select" aria-label="{{ __('Rating') }}" required>
                                <option value="5">★★★★★ {{ __('Excellent (5/5)') }}</option>
                                <option value="4">★★★★☆ {{ __('Very good (4/5)') }}</option>
                                <option value="3">★★★☆☆ {{ __('Good (3/5)') }}</option>
                                <option value="2">★★☆☆☆ {{ __('Average (2/5)') }}</option>
                                <option value="1">★☆☆☆☆ {{ __('Bad (1/5)') }}</option>
                            </select>
                        </div>

                        <div class="pdp-form-group">
                            <label for="reviewTitle" class="pdp-form-label">{{ __('Title') }}</label>
                            <input type="text" id="reviewTitle" name="title" class="pdp-form-input"
                                   placeholder="{{ __('Summary of your experience') }}" value="{{ old('title') }}" required>
                            @error('title')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="pdp-form-group">
                            <label for="reviewComment" class="pdp-form-label">{{ __('Comment') }}</label>
                            <textarea id="reviewComment" name="comment" class="pdp-form-input" rows="4"
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

        {{-- QUESTIONS FRÉQUENTES & INFORMATIONS D'ACHAT MAROC (SEO, GOOGLE RICH RESULTS & AI SEARCH) --}}
        <div class="pdp-section-card mt-4 p-4 rounded-4 bg-white shadow-sm border">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                <h2 class="h5 fw-bold m-0 d-flex align-items-center gap-2 text-dark">
                    <i class="fas fa-question-circle text-danger"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'الأسئلة الشائعة حول الشراء والضمان بالمغرب' : __('Questions fréquentes & Achat au Maroc') }}</span>
                </h2>
                <span class="badge bg-light text-muted border px-2 py-1">
                    <i class="fas fa-shield-alt text-success me-1"></i>{{ __('Garantie & Authenticité') }}
                </span>
            </div>

            <div class="accordion accordion-flush" id="pdpFaqAccordion">
                {{-- FAQ 1: Where to buy & warranty --}}
                <div class="accordion-item border-bottom">
                    <h3 class="accordion-header" id="faqHeading1">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                            {{ __('Où acheter') }} {{ $product->name }} {{ __('au Maroc avec garantie officielle ?') }}
                        </button>
                    </h3>
                    <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            {{ setting('app_name', 'WINA SHOP') }} propose <strong>{{ $product->name }}</strong> neuf et 100% authentique dans son showroom à <strong>Casablanca (Al Oulfa)</strong> ainsi qu'en vente en ligne. Nous assurons la livraison express et sécurisée dans toutes les villes marocaines : <em>Casablanca, Rabat, Marrakech, Tanger, Fès, Agadir, Oujda, Meknès, Kénitra</em>.
                        </div>
                    </div>
                </div>

                {{-- FAQ 2: Pricing, Delivery & Payment --}}
                <div class="accordion-item border-bottom">
                    <h3 class="accordion-header" id="faqHeading2">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                            {{ __('Quel est le prix et le délai de livraison au Maroc ?') }}
                        </button>
                    </h3>
                    <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            Le prix de {{ $product->name }} est de <strong>{{ number_format($product->final_price, 2, '.', ' ') }} {{ setting('currency_code', 'MAD') }}</strong>. La livraison est effectuée sous <strong>24h à 48h ouvrables</strong>. Le paiement s'effectue en toute sérénité <strong>à la livraison (Cash on Delivery)</strong> après inspection de votre colis, ou par virement bancaire sécurisé.
                        </div>
                    </div>
                </div>

                {{-- FAQ 3: Devis INDH & Facture ICE --}}
                <div class="accordion-item border-bottom">
                    <h3 class="accordion-header" id="faqHeading3">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                            {{ __('Puis-je obtenir un devis proforma INDH ou une facture avec ICE ?') }}
                        </button>
                    </h3>
                    <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            <strong>Oui, absolument.</strong> WINA SHOP délivre des devis proforma certifiés éligibles aux financements <strong>INDH (Initiative Nationale pour le Développement Humain)</strong> ainsi que des factures officielles avec identifiant fiscal (ICE, RC, IF) pour entreprises audiovisuelles, créateurs de contenu et administrations.
                        </div>
                    </div>
                </div>

                {{-- FAQ 4: Arabic summary --}}
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faqHeading4">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                            كيف يمكنني شراء {{ $product->name }} في المغرب وما هي الضمانات؟
                        </button>
                    </h3>
                    <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            يمكنك طلب <strong>{{ $product->name }}</strong> عبر متجر WINA SHOP أو بالتواصل مباشرة على الواتساب على الرقم <strong>{{ setting('company_phone', '+212 629-035777') }}</strong>. نوفر لك منتجات أصلية مع ضمان رسمي، وتوصيل سريع في ظرف 24 إلى 48 ساعة لكافة ربوع المملكة المغربية مع الدفع عند الاستلام.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RELATED PRODUCTS --}}
        @if($relatedProducts->count() > 0)
        <div class="pdp-related mt-5">
            <div class="pdp-related-header">
                <h2 class="pdp-related-title">{{ __('Related products') }}</h2>
                <a href="{{ route('shop.index') }}" class="pdp-related-link">
                    {{ __('See all') }} <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left ms-1' : 'fa-arrow-right ms-1' }}"></i>
                </a>
            </div>
            <div class="row g-2 home-products-grid">
                @foreach($relatedProducts as $related)
                <div class="col-6 col-md-3">
                    @include('frontend.partials.product_card_slider', ['product' => $related])
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
        window.addToCart(productId, quantity, btn);
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
        const defaultLabel = isAr ? 'اشتري الآن' : (document.documentElement.lang === 'en' ? 'Buy now' : 'Acheter maintenant');
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

function copyPdpSku(sku) {
    if (!sku) return;
    const badge = document.getElementById('pdpSkuCopyBtn');
    const icon = document.getElementById('pdpSkuCopyIcon');

    const onSuccess = () => {
        if (badge && icon) {
            badge.classList.add('copied');
            icon.className = 'fas fa-check pdp-sku-copy-icon text-success ms-1';
            setTimeout(() => {
                badge.classList.remove('copied');
                icon.className = 'far fa-copy pdp-sku-copy-icon ms-1';
            }, 1800);
        }
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(sku).then(onSuccess).catch(() => fallbackCopy(sku, onSuccess));
    } else {
        fallbackCopy(sku, onSuccess);
    }
}

function fallbackCopy(text, cb) {
    const temp = document.createElement('textarea');
    temp.value = text;
    temp.style.position = 'fixed';
    temp.style.opacity = '0';
    document.body.appendChild(temp);
    temp.focus();
    temp.select();
    try {
        document.execCommand('copy');
        if (cb) cb();
    } catch (e) {}
    document.body.removeChild(temp);
}
</script>
@endpush
