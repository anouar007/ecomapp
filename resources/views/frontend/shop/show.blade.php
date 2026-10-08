@extends('layouts.frontend')

@section('meta_title', $product->name . ' — ' . setting('app_name', 'WINA SHOP') . ' Maroc')
@section('meta_description', Str::limit(strip_tags($product->description), 155) ?: $product->name . ' — ' . setting('app_name', 'WINA SHOP'))
@section('meta_type', 'product')
@section('meta_image', $product->thumbnail)

@section('extra_meta')
<meta property="product:price:amount" content="{{ $product->isOnSale() ? $product->sale_price : $product->price }}">
<meta property="product:price:currency" content="{{ setting('currency_code', 'MAD') }}">
<meta property="product:availability" content="{{ $product->isInStock() ? 'in stock' : 'out of stock' }}">
<meta property="product:condition" content="new">
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
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{{ addslashes($product->name) }}",
    "image": ["{{ $product->thumbnail }}"],
    "description": "{{ addslashes(Str::limit(strip_tags($product->description), 155)) }}",
    "sku": "{{ $product->sku ?? 'PROD-' . $product->id }}",
    "offers": {
      "@type": "Offer",
      "url": "{{ url()->current() }}",
      "priceCurrency": "{{ setting('currency_code', 'MAD') }}",
      "price": "{{ $product->isOnSale() ? $product->sale_price : $product->price }}",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "{{ $product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
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

                    @if($reviews->total() > 0)
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

                    @if($product->description)
                    <div class="pdp-description entry-content">
                        @if(strip_tags($product->description) !== $product->description)
                            {!! $product->description !!}
                        @else
                            {!! nl2br(e($product->description)) !!}
                        @endif
                    </div>
                    @endif

                    <div class="pdp-divider"></div>

                    @if($product->isInStock())
                    <form id="addToCartForm" onsubmit="pdpAddToCart(event)">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="pdp-cart-row">
                            <div class="pdp-qty-wrap">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="pdpQty" value="1"
                                       min="1" max="{{ $product->stock }}" class="pdp-qty-input">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button type="submit" id="addToCartBtn" class="pdp-add-btn">
                                <i class="fas fa-cart-plus me-2"></i>
                                <span id="addToCartText">{{ __('Add to Cart') }}</span>
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="pdp-cart-row">
                        <button type="button" id="addToCartBtn" class="pdp-add-btn pdp-add-btn--out" disabled>
                            <i class="fas fa-ban me-2"></i>
                            <span id="addToCartText">{{ __('Out of stock') }}</span>
                        </button>
                    </div>
                    @endif

                    <div class="pdp-trust-row">
                        <div class="pdp-trust-pill"><i class="fas fa-shield-halved"></i> {{ __('2-Year Warranty') }}</div>
                        <div class="pdp-trust-pill"><i class="fas fa-truck-fast"></i> {{ __('Secure Delivery 24/48h') }}</div>
                        <div class="pdp-trust-pill"><i class="fas fa-file-invoice"></i> {{ __('ICE Invoicing') }}</div>
                        <div class="pdp-trust-pill"><i class="fas fa-video"></i> {{ __('Showroom Demo Casablanca') }}</div>
                    </div>
                </div>

            </div>
        </div>

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
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                <div class="col-6 col-md-3">
                    <div class="pcard">
                        <div class="pcard-img">
                            <a href="{{ route('shop.show', $related->id) }}">
                                <img src="{{ $related->thumbnail }}"
                                     alt="{{ $related->name }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                            </a>
                            @if(!$related->isInStock())
                                <div class="pcard-badges"><span class="pcard-badge pcard-badge--oos">{{ __('Out of Stock (badge)') }}</span></div>
                            @elseif($related->isOnSale())
                                <div class="pcard-badges"><span class="pcard-badge pcard-badge--sale">−{{ $related->discount_percentage }}%</span></div>
                            @endif
                            <div class="pcard-overlay">
                                <a href="{{ route('shop.show', $related->id) }}" class="pcard-overlay-btn pcard-overlay-btn--ghost">
                                    <i class="fas fa-eye"></i> {{ __('View') }}
                                </a>
                            </div>
                        </div>
                        <div class="pcard-body">
                            @if($related->category_name)
                                <a href="{{ route('shop.index', ['category' => $related->category_slug ?? optional($related->category)->slug ?? optional($related->productCategory)->slug]) }}" class="pcard-cat text-decoration-none d-block">{{ $related->category_name }}</a>
                            @endif
                            <h4 class="pcard-name">
                                <a href="{{ route('shop.show', $related->id) }}">{{ Str::limit($related->name, 42) }}</a>
                            </h4>
                            <div class="pcard-price">
                                @if($related->isOnSale())
                                    <span class="pcard-price-current">{{ $related->formatted_sale_price }}</span>
                                    <span class="pcard-price-old">{{ $related->formatted_price }}</span>
                                @else
                                    <span class="pcard-price-current">{{ $related->formatted_price }}</span>
                                @endif
                            </div>
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
    const btnText  = document.getElementById('addToCartText');
    const quantity = document.getElementById('pdpQty').value;
    const productId = {{ $product->id }};

    btn.disabled = true;
    const orig = btnText.innerHTML;
    btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('Adding...') }}';

    fetch(`{{ url('/cart/add') }}/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btnText.innerHTML = orig;
        if (data.success) {
            ['header-cart-count', 'header-cart-count-mobile'].forEach(id => {
                const badge = document.getElementById(id);
                if (badge && data.cartCount !== undefined) badge.textContent = data.cartCount;
            });
            if (typeof window.updateFloatingCheckout === 'function') {
                window.updateFloatingCheckout(data.cartCount, data.cartTotal);
            }
            if (typeof refreshMiniCart === 'function') refreshMiniCart();
            Swal.fire({ toast:true, position:'top-end', icon:'success',
                title:'{{ __('Added to cart!') }}',
                text:'{{ addslashes($product->name) }}',
                showConfirmButton:false, timer:2500,
                background:'#1a1a2e', color:'#fff' });
        } else {
            throw new Error(data.message || '{{ __('Error') }}');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btnText.innerHTML = orig;
        Swal.fire({ icon:'error', title:'{{ __('Error') }}', text: err.message || '{{ __('Cannot add to cart.') }}' });
    });
}
</script>
@endpush
