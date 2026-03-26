@extends('layouts.frontend')

@section('meta_title', $product->translated_name . ' — ' . setting('app_name', 'Hijab Princesses'))
@section('meta_description', Str::limit(strip_tags($product->translated_description), 155))
@section('meta_image', $product->main_image ? url(Storage::url($product->main_image)) : null)

@section('json_ld')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->translated_name }}",
  "image": [
    "{{ $product->main_image ? url(Storage::url($product->main_image)) : '' }}"
  ],
  "description": "{{ Str::limit(strip_tags($product->translated_description), 160) }}",
  "sku": "{{ $product->sku }}",
  "brand": {
    "@type": "Brand",
    "name": "{{ setting('app_name', 'Hijab Princesses') }}"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "MAD",
    "price": "{{ $product->sale_price ?? $product->price }}",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "https://schema.org/{{ $product->getTotalStockAttribute() > 0 ? 'InStock' : 'OutOfStock' }}"
  }
}
</script>
@endsection

@section('content')

{{-- =============================================
     BREADCRUMB
     ============================================= --}}
<section class="pdp-breadcrumb-bar py-3 bg-white border-bottom">
    <div class="container px-xl-5 small font-body">
        <nav class="pdp-breadcrumb" aria-label="breadcrumb">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none"><i class="fas fa-home"></i></a>
            <span class="mx-2 text-muted opacity-50">/</span>
            <a href="{{ route('shop.index') }}" class="text-muted text-decoration-none">المتجر</a>
            @if($product->productCategory)
                <span class="mx-2 text-muted opacity-50">/</span>
                <a href="{{ route('shop.index', ['category' => $product->productCategory->slug]) }}" class="text-muted text-decoration-none">{{ $product->productCategory->translated_name }}</a>
            @endif
            <span class="mx-2 text-muted opacity-50">/</span>
            <span class="text-gold fw-bold">{{ Str::limit($product->translated_name, 40) }}</span>
        </nav>
    </div>
</section>

{{-- =============================================
     MAIN PRODUCT LAYOUT
     ============================================= --}}
<section class="pdp-body section-py">
    <div class="container px-xl-5">
        <div class="row g-4 g-lg-5">

            {{-- ── IMAGE PANEL ── --}}
            <div class="col-lg-6">
                <div class="pdp-main-image-wrap rounded-4 overflow-hidden shadow-sm bg-white mb-3" id="zoomWrap" onmousemove="pdpZoom(event)" style="aspect-ratio: 1/1.2; position: relative; cursor: crosshair;">
                    @if($product->main_image)
                        <img id="mainImage" src="{{ Storage::url($product->main_image) }}"
                             alt="{{ $product->translated_name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                            <i class="fas fa-image fa-4x"></i>
                        </div>
                    @endif

                    {{-- Badges --}}
                    <div class="pdp-badges position-absolute top-0 start-0 p-3">
                        @if($product->getTotalStockAttribute() <= 0)
                            <span class="badge bg-danger rounded-pill px-3 py-2">نفذ من المخزن</span>
                        @elseif($product->isOnSale())
                            <span class="badge bg-primary rounded-pill px-3 py-2">تخفيض {{ $product->discount_percentage }}%</span>
                        @endif
                    </div>
                </div>

                {{-- Thumbnail Strip --}}
                @if($product->images->count() > 0)
                <div class="d-flex gap-2 overflow-auto pdp-thumbs pb-2">
                    <div class="thumb-item active border rounded overflow-hidden" onclick="pdpChangeImage('{{ Storage::url($product->main_image) }}', this)" style="width: 80px; height: 100px; flex-shrink: 0; cursor: pointer; transition: 0.3s;">
                        <img src="{{ Storage::url($product->main_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    @foreach($product->images as $img)
                    <div class="thumb-item border rounded overflow-hidden" onclick="pdpChangeImage('{{ Storage::url($img->image_path) }}', this)" style="width: 80px; height: 100px; flex-shrink: 0; cursor: pointer; transition: 0.3s;">
                        <img src="{{ Storage::url($img->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── INFO PANEL ── --}}
            <div class="col-lg-6">
                <div class="ps-lg-5">
                    @if($product->productCategory)
                    <div class="text-gold small fw-bold mb-2 text-uppercase ls-2 font-body" style="letter-spacing: 2px;">{{ $product->productCategory->translated_name }}</div>
                    @endif
                    
                    <h1 class="brand-heading h1 mb-3 text-dark">{{ $product->translated_name }}</h1>

                    {{-- Price & Stock --}}
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="pdp-price">
                            @if($product->isOnSale())
                                <span class="h2 fw-bold text-gold m-0" id="displayPrice">{{ $product->formatted_sale_price }}</span>
                                <span class="text-muted text-decoration-line-through ms-2 small font-body">{{ $product->formatted_price }}</span>
                            @else
                                <span class="h2 fw-bold text-gold m-0" id="displayPrice">{{ $product->formatted_price }}</span>
                            @endif
                        </div>
                        
                        <span id="stockBadge" class="small px-3 py-1 rounded-pill fw-bold font-body {{ $product->getTotalStockAttribute() > 0 ? 'bg-gold-light text-dark' : 'bg-light text-muted' }}">
                            @if($product->getTotalStockAttribute() > 0)
                                <i class="fas fa-crown me-1 small"></i> متوفر في المتجر
                            @else
                                <i class="fas fa-times me-1"></i> غير متوفر حالياً
                            @endif
                        </span>
                    </div>

                    {{-- Description --}}
                    <div class="text-muted mb-4 font-body lh-lg" style="font-size: 0.95rem;">
                        {!! nl2br(e($product->translated_description)) !!}
                    </div>

                    <div class="bg-gold-light opacity-50 my-4" style="height: 1px;"></div>

                    {{-- VARIANT SELECTION --}}
                    @if($product->variants->count() > 0)
                        <div class="pdp-variants mb-5 font-body">
                            {{-- Colors --}}
                            @php $colors = $product->getAvailableColorsAttribute(); @endphp
                            @if($colors->count() > 0)
                                <div class="mb-4">
                                    <label class="fw-bold mb-2 d-block small text-muted text-uppercase">اللون المختار:</label>
                                    <div class="d-flex flex-wrap gap-3" id="colorOptions">
                                        @foreach($colors as $color)
                                            <div class="variant-option border rounded p-1" 
                                                 data-color="{{ $color->color }}" 
                                                 style="cursor: pointer; transition: 0.3s;"
                                                 onclick="selectColor(this)"
                                                 title="{{ $color->color }}">
                                                @if($color->color_image_url)
                                                    <div class="rounded bg-light" style="width: 52px; height: 52px; overflow: hidden; border: 1px solid rgba(0,0,0,0.1);">
                                                        <img src="{{ $color->color_image_url }}" alt="{{ $color->color }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                @else
                                                    <div class="rounded" style="width: 44px; height: 44px; background-color: {{ $color->color_code ?: '#eee' }}; border: 1px solid rgba(0,0,0,0.1);"></div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Sizes --}}
                            @php $sizes = $product->getAvailableSizesAttribute(); @endphp
                            @if($sizes->count() > 0)
                                <div class="mb-4">
                                    <label class="fw-bold mb-2 d-block small text-muted text-uppercase">المقاس:</label>
                                    <div class="d-flex flex-wrap gap-2" id="sizeOptions">
                                        @foreach($sizes as $size)
                                            <div class="variant-option border rounded px-3 py-2 small fw-bold" 
                                                 style="cursor: pointer; transition: 0.3s;"
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
                        
                        <div class="row g-2 align-items-stretch mb-5">
                            <div class="col-4 col-md-3">
                                <div class="d-flex align-items-center border rounded h-100 bg-white">
                                    <button type="button" class="btn btn-link text-muted p-3 px-4 text-decoration-none" onclick="pdpChangeQty(-1)">
                                        <i class="fas fa-minus fs-5"></i>
                                    </button>
                                    <input type="number" name="quantity" id="pdpQty" value="1"
                                           min="1" max="{{ $product->getTotalStockAttribute() }}" class="form-control border-0 text-center fw-bold p-0 font-body fs-5" style="background: transparent;">
                                    <button type="button" class="btn btn-link text-muted p-3 px-4 text-decoration-none" onclick="pdpChangeQty(1)">
                                        <i class="fas fa-plus fs-5"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-8 col-md-9">
                                <button type="submit" id="addToCartBtn" class="btn-brand-primary w-100 h-100 py-3 font-body">
                                    أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Trust Pills --}}
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="brand-card p-3 border-0 bg-gold-light text-center h-100">
                                <i class="fas fa-truck text-gold mb-2 d-block h4"></i>
                                <span class="small fw-bold font-body" style="font-size: 0.7rem;">توصيل سريع</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="brand-card p-3 border-0 bg-gold-light text-center h-100">
                                <i class="fas fa-undo text-gold mb-2 d-block h4"></i>
                                <span class="small fw-bold font-body" style="font-size: 0.7rem;">استرجاع سهل</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="brand-card p-3 border-0 bg-gold-light text-center h-100">
                                <i class="fas fa-shield-alt text-gold mb-2 d-block h4"></i>
                                <span class="small fw-bold font-body" style="font-size: 0.7rem;">دفع آمن</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RELATED PRODUCTS --}}
        @if($relatedProducts->count() > 0)
        <div class="mt-5 pt-5 border-top">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="brand-heading m-0">منتجات قد تعجبك</h3>
                    <div class="bg-gold mt-2 rounded" style="width: 40px; height: 3px;"></div>
                </div>
            </div>
            <div class="row g-3 g-lg-4">
                @foreach($relatedProducts as $related)
                <div class="col-6 col-md-3">
                    @include('frontend.partials.product_card_v2', ['product' => $related])
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

<div class="d-lg-none" style="height: 90px;"></div>

{{-- ── STICKY MOBILE ADD TO CART BAR ── --}}
<div class="d-lg-none position-fixed bottom-0 start-0 w-100 bg-white border-top p-3 z-3" style="box-shadow: 0 -10px 40px rgba(0,0,0,0.08); border-radius: 1.5rem 1.5rem 0 0;">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div>
            <div class="small text-muted font-body fw-bold mb-1" style="font-size: 0.75rem;">الإجمالي:</div>
            <div class="h5 fw-bold text-gold m-0 lh-1" id="stickyPriceDisplay">{{ $product->isOnSale() ? $product->formatted_sale_price : $product->formatted_price }}</div>
        </div>
        <button type="button" class="btn btn-brand-primary flex-grow-1 py-3 rounded-pill font-body shadow-sm" onclick="document.getElementById('addToCartBtn').click()">
            أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
        </button>
    </div>
</div>

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
        el.style.borderColor = 'rgba(0,0,0,0.1)';
        selectedColor = null;
    } else {
        document.querySelectorAll('.color-pill, .variant-option').forEach(p => {
            if(p.dataset.color) {
                p.classList.remove('active');
                p.style.borderColor = 'rgba(0,0,0,0.1)';
            }
        });
        el.classList.add('active');
        el.style.borderColor = 'var(--brand-gold)';
        selectedColor = el.dataset.color;
    }
    updateVariantSelection();
}

function selectSize(el) {
    if (el.classList.contains('active')) {
        el.classList.remove('active');
        el.style.borderColor = 'rgba(0,0,0,0.1)';
        el.style.color = 'inherit';
        selectedSize = null;
    } else {
        document.querySelectorAll('.size-pill, .variant-option').forEach(p => {
            if(p.dataset.size) {
                p.classList.remove('active');
                p.style.borderColor = 'rgba(0,0,0,0.1)';
                p.style.color = 'inherit';
            }
        });
        el.classList.add('active');
        el.style.borderColor = 'var(--brand-gold)';
        el.style.color = 'var(--brand-gold)';
        selectedSize = el.dataset.size;
    }
    updateVariantSelection();
}

function updateVariantSelection() {
    const btn = document.getElementById('addToCartBtn');
    const input = document.getElementById('selectedVariantId');
    const stockBadge = document.getElementById('stockBadge');
    const priceDisplay = document.getElementById('displayPrice');
    
    input.value = '';

    const match = variants.find(v => 
        (v.color === selectedColor) && 
        (v.size === selectedSize)
    );

    if (match) {
        input.value = match.id;
        if (match.stock > 0) {
            btn.disabled = false;
            stockBadge.innerHTML = '<i class="fas fa-crown me-1 small"></i> متوفر في المتجر';
            stockBadge.className = 'small px-3 py-1 rounded-pill fw-bold font-body bg-gold-light text-dark';
            document.getElementById('pdpQty').max = match.stock;
        } else {
            btn.disabled = true;
            stockBadge.innerHTML = '<i class="fas fa-times me-1"></i> غير متوفر حالياً';
            stockBadge.className = 'small px-3 py-1 rounded-pill fw-bold font-body bg-light text-muted';
        }
        if (match.price) {
            priceDisplay.textContent = match.formatted_price;
            const stickyPrice = document.getElementById('stickyPriceDisplay');
            if (stickyPrice) stickyPrice.textContent = match.formatted_price;
        }
        if (match.color_image_url) {
            pdpChangeImage(match.color_image_url, null);
        } else if (mainImageSrc) {
            pdpChangeImage(mainImageSrc, null);
        }
    } else {
        btn.disabled = (variants.length > 0); 
        
        // Image Fallback: if color is selected, show its image even if no size match
        if (selectedColor) {
            const colorMatch = variants.find(v => v.color === selectedColor && v.color_image_url);
            if (colorMatch) {
                pdpChangeImage(colorMatch.color_image_url, null);
            } else if (mainImageSrc) {
                pdpChangeImage(mainImageSrc, null);
            }
        } else if (mainImageSrc) {
             pdpChangeImage(mainImageSrc, null);
        }
        
        if (selectedColor && selectedSize) {
            stockBadge.innerHTML = '<i class="fas fa-times me-1"></i> غير متوفر بهذا الخيار';
            stockBadge.className = 'small px-3 py-1 rounded-pill fw-bold font-body bg-light text-muted';
        } else {
            const totalStock = {{ $product->getTotalStockAttribute() }};
            stockBadge.innerHTML = totalStock > 0 ? '<i class="fas fa-crown me-1 small"></i> متوفر في المتجر' : '<i class="fas fa-times me-1"></i> غير متوفر حالياً';
            stockBadge.className = totalStock > 0 ? 'small px-3 py-1 rounded-pill fw-bold font-body bg-gold-light text-dark' : 'small px-3 py-1 rounded-pill fw-bold font-body bg-light text-muted';
            
            if (!selectedColor && !selectedSize) {
                priceDisplay.textContent = basePrice;
                if (mainImageSrc) pdpChangeImage(mainImageSrc, document.querySelector('.thumb-item'));
            }
        }
    }
    // Update availability of other pills
    updateAvailability();
}

function updateAvailability() {
    // Update Size Pills
    document.querySelectorAll('#sizeOptions .variant-option').forEach(pill => {
        const size = pill.dataset.size;
        let isAvailable = false;
        if (selectedColor) {
            isAvailable = variants.some(v => v.color == selectedColor && v.size == size && v.stock > 0);
        } else {
            isAvailable = variants.some(v => v.size == size && v.stock > 0);
        }
        pill.classList.toggle('disabled', !isAvailable);
    });

    // Update Color Dots
    document.querySelectorAll('#colorOptions .variant-option').forEach(dot => {
        const color = dot.dataset.color;
        let isAvailable = false;
        if (selectedSize) {
            isAvailable = variants.some(v => v.size == selectedSize && v.color == color && v.stock > 0);
        } else {
            isAvailable = variants.some(v => v.color == color && v.stock > 0);
        }
        dot.classList.toggle('disabled', !isAvailable);
    });
}

function executeAutoSelect() {
    if (variants.length > 0) {
        // Find the first variant that has stock
        const firstAvailable = variants.find(v => v.stock > 0);
        if (firstAvailable) {
            // Auto Select Color
            const colorPill = document.querySelector(`.variant-option[data-color="${firstAvailable.color}"]`);
            if (colorPill && !colorPill.classList.contains('active')) selectColor(colorPill);
            
            // Auto Select Size
            const sizePill = document.querySelector(`.variant-option[data-size="${firstAvailable.size}"]`);
            if (sizePill && !sizePill.classList.contains('active')) selectSize(sizePill);
        }
    }
}

// Initial call
updateAvailability();
document.addEventListener('DOMContentLoaded', () => { setTimeout(executeAutoSelect, 150); });


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
        document.querySelectorAll('.thumb-item').forEach(t => {
            t.classList.remove('active');
            t.style.borderColor = 'rgba(0,0,0,0.1)';
        });
        thumb.classList.add('active');
        thumb.style.borderColor = 'var(--brand-gold)';
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
        Swal.fire({ icon:'warning', title:'تنبيه', text:'الرجاء اختيار اللون والمقاس أولاً', confirmButtonColor: '#c5a059' });
        return;
    }

    btn.disabled = true;
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإضافة…';

    fetch(`{{ url('/cart/add') }}/${productId}`, {
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
            Swal.fire({ 
                toast:true, position:'top-start', icon:'success',
                title:'تمت الإضافة للسلة!',
                showConfirmButton:false, timer:2500,
                background:'#000', color:'#fff' 
            });
        } else {
            throw new Error(data.message || 'فشلت الإضافة');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = orig;
        Swal.fire({ icon:'error', title:'خطأ', text: err.message });
    });
}
</script>
@endpush
