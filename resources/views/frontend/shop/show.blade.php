@extends('layouts.frontend')

@section('meta_title', $product->translated_name . ' — Moubdi3oun')
@section('meta_description', Str::limit(strip_tags($product->translated_description), 160))
@section('meta_image', $product->main_image ? asset('storage/' . $product->main_image) : asset('images/og-default.jpg'))

@section('commerce_meta')
    <meta property="og:price:amount" content="{{ $product->price }}">
    <meta property="og:price:currency" content="MAD">
    <meta property="og:availability" content="{{ $product->getTotalStockAttribute() > 0 ? 'instock' : 'oos' }}">
    <meta property="product:brand" content="{{ setting('app_name', 'Moubdi3oun') }}">
    <meta property="product:condition" content="new">
    <meta property="product:item_group_id" content="{{ $product->category_id }}">
@endsection

@section('json_ld')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->translated_name }}",
  "image": "{{ $product->main_image ? asset('storage/' . $product->main_image) : asset('images/placeholder.jpg') }}",
  "description": "{{ Str::limit(strip_tags($product->translated_description), 300) }}",
  "sku": "{{ $product->sku ?? $product->id }}",
  "brand": {
    "@type": "Brand",
    "name": "{{ setting('app_name', 'Moubdi3oun') }}"
  },
  @if($product->review_count > 0)
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ $product->average_rating }}",
    "reviewCount": "{{ $product->review_count }}"
  },
  @endif
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "MAD",
    "price": "{{ $product->sale_price ?? $product->price }}",
    "availability": "https://schema.org/{{ $product->getTotalStockAttribute() > 0 ? 'InStock' : 'OutOfStock' }}",
    "itemCondition": "https://schema.org/NewCondition"
  }
}
</script>

{{-- Breadcrumb Schema --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "{{ __('Home') }}",
    "item": "{{ url('/') }}"
  },{
    "@type": "ListItem",
    "position": 2,
    "name": "{{ __('Shop') }}",
    "item": "{{ route('shop.index') }}"
  }
  @if($product->productCategory)
  ,{
    "@type": "ListItem",
    "position": 3,
    "name": "{{ $product->productCategory->translated_name }}",
    "item": "{{ route('shop.index', ['category' => $product->productCategory->slug]) }}"
  }
  @endif
  ,{
    "@type": "ListItem",
    "position": {{ $product->productCategory ? 4 : 3 }},
    "name": "{{ $product->translated_name }}",
    "item": "{{ url()->current() }}"
  }]
}
</script>
@endsection

@section('content')

{{-- =============================================
     BREADCRUMB
     ============================================= --}}
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <nav class="shop-breadcrumb mb-0" aria-label="breadcrumb">
            <a href="{{ url('/') }}" class="text-muted small fw-bold">{{ __('Home') }}</a>
            <span class="mx-2 text-muted opacity-50">/</span>
            <a href="{{ route('shop.index') }}" class="text-muted small fw-bold">{{ __('Shop') }}</a>
            @if($product->productCategory)
                <span class="mx-2 text-muted opacity-50">/</span>
                <a href="{{ route('shop.index', ['category' => $product->productCategory->slug]) }}" class="text-muted small fw-bold">{{ $product->productCategory->translated_name }}</a>
            @endif
            <span class="mx-2 text-muted opacity-50">/</span>
            <span class="text-dark small fw-black">{{ Str::limit($product->translated_name, 30) }}</span>
        </nav>
    </div>
</section>

{{-- =============================================
     MAIN PRODUCT LAYOUT
     ============================================= --}}
<section class="pdp-body section-py">
    <div class="container">
        <div class="row g-5">

            {{-- ── IMAGE PANEL ── --}}
            <div class="col-lg-6">
                <div class="pdp-main-image-wrap rounded-4 overflow-hidden shadow-sm bg-white mb-3" id="zoomWrap" onmousemove="pdpZoom(event)" style="aspect-ratio: 1/1.2; position: relative; cursor: crosshair;">
                    @if($product->main_image)
                        <img id="mainImage" src="{{ Storage::url($product->main_image) }}"
                             alt="{{ $product->translated_name }}" 
                             loading="eager" 
                             decoding="async"
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                            <i class="fas fa-image fa-4x"></i>
                        </div>
                    @endif

                    {{-- Badges --}}
                    <div class="pdp-badges position-absolute top-0 start-0 p-3">
                        @if($product->getTotalStockAttribute() <= 0)
                            <span class="badge bg-dark rounded-pill px-3 py-2 text-uppercase fw-bold">{{ __('Out of Stock') }}</span>
                        @elseif($product->isOnSale())
                            <span class="badge bg-danger rounded-pill px-3 py-2 text-uppercase fw-bold">{{ __('Sale') }} -{{ $product->discount_percentage }}%</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── INFO PANEL ── --}}
            <div class="col-lg-6">
                <div class="ps-lg-5">
                    @if($product->productCategory)
                    <div class="text-uppercase small fw-black mb-2 ls-2" style="color: var(--accent); font-size: 0.75rem;">{{ $product->productCategory->translated_name }}</div>
                    @endif
                    
                    <h1 class="display-6 fw-black mb-3" style="letter-spacing: -1px;">{{ $product->translated_name }}</h1>

                    {{-- Urgency & Social Proof --}}
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <div class="px-3 py-1 rounded-pill small fw-bold bg-light border d-flex align-items-center">
                            <span class="pulse-red me-2"></span>
                            <span id="live-viewers">...</span> {{ __('people are looking at this right now') }}
                        </div>
                        @if($product->getTotalStockAttribute() > 0 && $product->getTotalStockAttribute() <= 5)
                        <div class="px-3 py-1 rounded-pill small fw-bold text-danger bg-danger-subtle d-flex align-items-center">
                            <i class="fas fa-fire me-2"></i> {{ __('Almost Gone! Only') }} {{ $product->getTotalStockAttribute() }} {{ __('left') }}
                        </div>
                        @endif
                    </div>

                    <style>
                        .pulse-red {
                            display: inline-block;
                            width: 8px;
                            height: 8px;
                            background: #ff4d4d;
                            border-radius: 50%;
                            box-shadow: 0 0 0 rgba(255, 77, 77, 0.4);
                            animation: pulse 1.5s infinite;
                        }
                        @keyframes pulse {
                            0% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7); }
                            70% { box-shadow: 0 0 0 10px rgba(255, 77, 77, 0); }
                            100% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0); }
                        }
                    </style>

                    {{-- Price & Stock --}}
                    <div class="d-flex align-items-center gap-4 mb-4 pb-2">
                        @if($product->isOnSale())
                            <span class="h3 fw-black m-0" id="displayPrice">{{ $product->formatted_sale_price }}</span>
                            <span class="text-muted text-decoration-line-through">{{ $product->formatted_price }}</span>
                        @else
                            <span class="h3 fw-black m-0" id="displayPrice">{{ $product->formatted_price }}</span>
                        @endif
                        
                        <div id="stockBadge" class="small px-3 py-1 rounded-pill fw-bold {{ $product->getTotalStockAttribute() > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            @if($product->getTotalStockAttribute() > 0)
                                <i class="fas fa-check-circle me-1"></i> {{ __('In Stock') }}
                            @else
                                <i class="fas fa-times-circle me-1"></i> {{ __('Out of Stock') }}
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="text-muted mb-4 lead-sm lh-lg">
                        {!! nl2br(e($product->translated_description)) !!}
                    </div>

                    <hr class="my-4 opacity-10">

                    {{-- VARIANT SELECTION --}}
                    @if($product->variants->count() > 0)
                        <div class="pdp-variants mb-5">
                            {{-- Colors --}}
                            @php $colors = $product->getAvailableColorsAttribute(); @endphp
                            @if($colors->count() > 0)
                                <div class="mb-4">
                                    <label class="fw-black mb-3 d-block small text-uppercase ls-1">{{ __('Finish / Material') }} :</label>
                                    <div class="d-flex flex-wrap gap-2" id="colorOptions">
                                        @foreach($colors as $color)
                                            <div class="variant-option p-2 px-3 {{ $loop->first ? 'active' : '' }}" 
                                                 data-color="{{ $color->color }}" 
                                                 onclick="selectColor(this)">
                                                @if($color->color_code)
                                                    <span class="rounded-circle d-inline-block border align-middle me-2" style="width: 14px; height: 14px; background: {{ $color->color_code }}"></span>
                                                @endif
                                                {{ $color->color }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Sizes --}}
                            @php $sizes = $product->getAvailableSizesAttribute(); @endphp
                            @if($sizes->count() > 0)
                                <div class="mb-4">
                                    <label class="fw-black mb-3 d-block small text-uppercase ls-1">{{ __('Dimensions') }} :</label>
                                    <div class="d-flex flex-wrap gap-2" id="sizeOptions">
                                        @foreach($sizes as $size)
                                            <div class="variant-option size-pill" 
                                                 data-size="{{ $size }}"
                                                 onclick="selectSize(this)">
                                                {{ $size }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Add to Cart Form --}}
                    <form id="addToCartForm" onsubmit="pdpAddToCart(event)">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" id="selectedVariantId" value="">
                        
                        <div class="row g-3 align-items-center mb-5">
                            <div class="col-auto">
                                <div class="qty-selector">
                                    <button type="button" class="btn btn-link link-dark p-2 text-decoration-none" onclick="pdpChangeQty(-1)">
                                        <i class="fas fa-minus small"></i>
                                    </button>
                                    <input type="number" name="quantity" id="pdpQty" value="1"
                                           min="1" max="{{ $product->getTotalStockAttribute() }}" 
                                           class="form-control border-0 text-center fw-black shadow-none bg-transparent" style="width: 50px;">
                                    <button type="button" class="btn btn-link link-dark p-2 text-decoration-none" onclick="pdpChangeQty(1)">
                                        <i class="fas fa-plus small"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" id="addToCartBtn" class="btn btn-dark w-100 py-3 fw-black text-uppercase ls-1"
                                        style="border-radius: 40px;"
                                        {{ $product->getTotalStockAttribute() <= 0 ? 'disabled' : '' }}>
                                    {{ __('Add to Cart') }} <i class="fas fa-shopping-bag ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Trust Pills --}}
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="p-3 bg-white border rounded-4 text-center h-100">
                                <i class="fas fa-truck text-dark mb-2 d-block h4"></i>
                                <span class="small fw-black text-uppercase ls-1" style="font-size: 0.65rem;">{{ __('Shipping Morocco') }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="p-3 bg-white border rounded-4 text-center h-100">
                                <i class="fas fa-certificate text-dark mb-2 d-block h4"></i>
                                <span class="small fw-black text-uppercase ls-1" style="font-size: 0.65rem;">{{ __('Guaranteed Quality') }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="p-3 bg-white border rounded-4 text-center h-100">
                                <i class="fas fa-user-shield text-dark mb-2 d-block h4"></i>
                                <span class="small fw-black text-uppercase ls-1" style="font-size: 0.65rem;">{{ __('Secure Payment') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RELATED PRODUCTS --}}
        @if($relatedProducts->count() > 0)
        <div class="mt-5 pt-5 border-top">
            <h3 class="fw-black text-uppercase ls-1 mb-4">{{ __('Related Products') }}</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                <div class="col-6 col-md-3">
                    <div class="pcard">
                        <div class="pcard-img">
                            <a href="{{ route('shop.show', $related->id) }}">
                                <img src="{{ $related->main_image ? Storage::url($related->main_image) : asset('images/placeholder-product.jpg') }}" alt="{{ $related->translated_name }}">
                            </a>
                        </div>
                        <div class="pcard-body">
                            <h5 class="pcard-name"><a href="{{ route('shop.show', $related->id) }}">{{ Str::limit($related->translated_name, 30) }}</a></h5>
                            <div class="pcard-price">{{ $related->formatted_price }}</div>
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
// ── Variants Logic ───────────────────────────────
const variants = @json($product->variants);
let selectedColor = null;
let selectedSize = null;
const basePrice = "{{ $product->isOnSale() ? $product->formatted_sale_price : $product->formatted_price }}";
const mainImageSrc = "{{ $product->main_image ? Storage::url($product->main_image) : '' }}";

function selectColor(el) {
    if (el.classList.contains('active')) {
        el.classList.remove('active');
        selectedColor = null;
    } else {
        document.querySelectorAll('#colorOptions .variant-option').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        selectedColor = el.dataset.color;
    }
    updateVariantSelection();
}

function selectSize(el) {
    if (el.classList.contains('active')) {
        el.classList.remove('active');
        selectedSize = null;
    } else {
        document.querySelectorAll('#sizeOptions .variant-option').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        selectedSize = el.dataset.size;
    }
    updateVariantSelection();
}

function updateVariantSelection() {
    const btn = document.getElementById('addToCartBtn');
    const input = document.getElementById('selectedVariantId');
    const stockBadge = document.getElementById('stockBadge');
    const priceDisplay = document.getElementById('displayPrice');
    
    // Reset hidden input
    input.value = '';

    // Find the specific matching variant
    const match = variants.find(v => 
        (v.color === selectedColor) && 
        (v.size === selectedSize)
    );

    if (match) {
        input.value = match.id;
        if (match.stock > 0) {
            btn.disabled = false;
            stockBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> {{ __("In Stock") }}';
            stockBadge.className = 'small px-3 py-1 rounded-pill fw-bold bg-success-subtle text-success';
            document.getElementById('pdpQty').max = match.stock;
        } else {
            btn.disabled = true;
            stockBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> {{ __("Out of Stock") }}';
            stockBadge.className = 'small px-3 py-1 rounded-pill fw-bold bg-danger-subtle text-danger';
        }
        if (match.price) {
            // Format price with DH
            priceDisplay.textContent = new Intl.NumberFormat('fr-FR').format(match.price) + ' DH';
        }
        if (match.color_image) {
            pdpChangeImage(`/storage/${match.color_image}`, null);
        }
    } else {
        // Handle partial or no selection
        btn.disabled = (variants.length > 0); 
        
        if (selectedColor && selectedSize) {
            stockBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> {{ __("Unavailable") }}';
            stockBadge.className = 'small px-3 py-1 rounded-pill fw-bold bg-danger-subtle text-danger';
        } else {
            const totalStock = {{ $product->getTotalStockAttribute() }};
            stockBadge.innerHTML = totalStock > 0 ? '<i class="fas fa-check-circle me-1"></i> {{ __("In Stock") }}' : '<i class="fas fa-times-circle me-1"></i> {{ __("Out of Stock") }}';
            stockBadge.className = totalStock > 0 ? 'small px-3 py-1 rounded-pill fw-bold bg-success-subtle text-success' : 'small px-3 py-1 rounded-pill fw-bold bg-danger-subtle text-danger';
            
            if (!selectedColor && !selectedSize) {
                priceDisplay.textContent = basePrice;
                if (mainImageSrc) pdpChangeImage(mainImageSrc, document.querySelector('.thumb-item'));
            } else if (selectedColor) {
                const firstColVariant = variants.find(v => v.color === selectedColor);
                if (firstColVariant && firstColVariant.price) {
                    priceDisplay.textContent = new Intl.NumberFormat('fr-FR').format(firstColVariant.price) + ' DH';
                }
                if (firstColVariant && firstColVariant.color_image) {
                    pdpChangeImage(`/storage/${firstColVariant.color_image}`, null);
                }
            } else if (selectedSize) {
                const firstSizeVariant = variants.find(v => v.size === selectedSize);
                if (firstSizeVariant && firstSizeVariant.price) {
                    priceDisplay.textContent = new Intl.NumberFormat('fr-FR').format(firstSizeVariant.price) + ' DH';
                }
            }
        }
    }

    updateAvailability();
}

function updateAvailability() {
    // 1. Update Size Availability based on selected color
    document.querySelectorAll('#sizeOptions .variant-option').forEach(pill => {
        const size = pill.dataset.size;
        let isAvailable = false;
        
        if (selectedColor) {
            // Check if this size is in stock for the selected color
            isAvailable = variants.some(v => v.color === selectedColor && v.size === size && v.stock > 0);
        } else {
            // If no color selected, check if this size is in stock in ANY color
            isAvailable = variants.some(v => v.size === size && v.stock > 0);
        }
        
        pill.classList.toggle('disabled-option', !isAvailable);
    });

    // 2. Update Color Availability based on selected size
    document.querySelectorAll('#colorOptions .variant-option').forEach(pill => {
        const color = pill.dataset.color;
        let isAvailable = false;
        
        if (selectedSize) {
            // Check if this color is in stock for the selected size
            isAvailable = variants.some(v => v.size === selectedSize && v.color === color && v.stock > 0);
        } else {
            // If no size selected, check if this color is in stock in ANY size
            isAvailable = variants.some(v => v.color === color && v.stock > 0);
        }
        
        pill.classList.toggle('disabled-option', !isAvailable);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Initial availability check
    updateAvailability();

    // Auto-select first available and visible combo
    const firstAvailable = variants.find(v => v.stock > 0);
    if (firstAvailable) {
        const colorPill = document.querySelector(`#colorOptions .variant-option[data-color="${firstAvailable.color}"]`);
        if (colorPill) selectColor(colorPill);
        const sizePill = document.querySelector(`#sizeOptions .variant-option[data-size="${firstAvailable.size}"]`);
        if (sizePill) selectSize(sizePill);
    } else {
        // Just select first ones if everything is out of stock
        const firstSize = document.querySelector('#sizeOptions .variant-option');
        if (firstSize) selectSize(firstSize);
    }

    // Track ViewContent on load
    if (typeof trackAdEvent === 'function') {
        trackAdEvent('ViewContent', {
            content_name: '{{ $product->translated_name }}',
            content_ids: ['{{ $product->id }}'],
            content_type: 'product',
            value: {{ $product->sale_price ?? $product->price }},
            currency: 'MAD'
        });
    }

    // Dynamic Viewers Simulation
    const viewerCount = document.getElementById('live-viewers');
    if (viewerCount) {
        const updateViewers = () => {
            const count = Math.floor(Math.random() * (12 - 3 + 1)) + 3;
            viewerCount.textContent = count;
        };
        updateViewers();
        setInterval(updateViewers, 30000);
    }
});

function pdpZoom(e) {
    const wrap = document.getElementById('zoomWrap');
    const img  = document.getElementById('mainImage');
    if (!img) return;
    const x = (e.offsetX / wrap.offsetWidth)  * 100;
    const y = (e.offsetY / wrap.offsetHeight) * 100;
    img.style.transformOrigin = `${x}% ${y}%`;
    img.style.transform = "scale(2)";
}
document.getElementById('zoomWrap').onmouseleave = () => {
    const img = document.getElementById('mainImage');
    if(img) img.style.transform = "scale(1)";
}

function pdpChangeImage(src, thumb) {
    const mainImg = document.getElementById('mainImage');
    if (!mainImg) return;
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = src;
        mainImg.style.opacity = '1';
    }, 120);
    if (thumb) {
        document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
        thumb.style.borderColor = 'var(--primary)';
    }
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
    const quantity = document.getElementById('pdpQty').value;
    const productId = {{ $product->id }};
    const variantId = document.getElementById('selectedVariantId').value;

    if (variants.length > 0 && !variantId) {
        Swal.fire({ icon:'warning', title:'Sélectionner une option', text:'Veuillez choisir la finition et les dimensions avant d\'ajouter au panier.', confirmButtonColor: '#1a1a1a' });
        return;
    }

    btn.disabled = true;
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';

    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: parseInt(quantity), variant_id: variantId })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = orig;
        if (data.success) {
            const badge = document.getElementById('header-cart-count');
            if (badge && data.cartCount !== undefined) badge.textContent = data.cartCount;
            if (typeof refreshMiniCart === 'function') refreshMiniCart();

            // Track Ad Event
            if (typeof trackAdEvent === 'function') {
                trackAdEvent('AddToCart', {
                    content_name: '{{ $product->translated_name }}',
                    content_ids: ['{{ $product->id }}'],
                    content_type: 'product',
                    value: {{ $product->sale_price ?? $product->price }},
                    currency: 'MAD'
                });
            }

            Swal.fire({ 
                toast:true, position:'top-end', icon:'success',
                title: '{{ __("Added to cart!") }}',
                showConfirmButton:false, timer:2500,
                background:'#1a1a1a', color:'#fff' 
            });
        } else {
            throw new Error(data.message || 'Erreur lors de l\'ajout.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = orig;
        Swal.fire({ icon:'error', title:'Erreur', text: err.message, confirmButtonColor: '#1a1a1a' });
    });
}
</script>
@endpush
