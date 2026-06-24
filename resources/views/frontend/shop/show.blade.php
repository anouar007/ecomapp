@extends('layouts.frontend')

@section('meta_title', $product->translated_name . ' — ' . setting('app_name', 'Hijab Princesses'))
@section('meta_description', Str::limit(strip_tags($product->translated_description), 155))
@section('meta_image', $product->main_image ? url(getImageUrl($product->main_image)) : null)

@section('json_ld')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@graph": [
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "{{ __('Home') }}",
          "item": "{{ url('/') }}"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "{{ __('Shop') }}",
          "item": "{{ route('shop.index') }}"
        }
        @if($product->productCategory),
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ $product->productCategory->translated_name }}",
          "item": "{{ route('shop.index', ['category' => $product->productCategory->slug]) }}"
        }
        @endif
      ]
    },
    {
      "@type": "Product",
      "name": "{{ $product->translated_name }}",
      "image": [
        "{{ $product->main_image ? url(getImageUrl($product->main_image)) : '' }}"
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
        "availability": "https://schema.org/{{ $product->getTotalStockAttribute() > 0 ? 'InStock' : 'OutOfStock' }}",
        "seller": {
            "@type": "Organization",
            "name": "Hijab Princesses"
        }
      }
    }
  ]
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
            <a href="{{ url('/') }}" class="text-muted text-decoration-none fw-bold" style="letter-spacing: 0.5px;">HIJAB <span class="text-gold">PRINCESSES</span></a>
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
<section class="pdp-body section-py pt-5">
    <div class="container px-xl-5">
        <div class="row g-4 g-lg-5">

            {{-- ── IMAGE PANEL ── --}}
            <div class="col-lg-6 mt-0">
                <div class="pdp-main-image-wrap rounded-4 overflow-hidden shadow-sm bg-white mb-3" id="zoomWrap" onmousemove="pdpZoom(event)" style="aspect-ratio: 4/5; position: relative; cursor: crosshair;">
                    @if($product->main_image)
                        <img id="mainImage" src="{{ getImageUrl($product->main_image) }}"
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
                            <span class="badge bg-danger rounded-pill px-3 py-2">تخفيض {{ $product->discount_percentage }}%</span>
                        @endif
                    </div>
                </div>

                {{-- Thumbnail Strip --}}
                @php
                    $uniqueImages = collect([$product->main_image])
                        ->merge($product->images->pluck('image_path'))
                        ->filter()
                        ->unique()
                        ->values();
                @endphp

                @if($uniqueImages->count() >= 1)
                <div class="d-flex gap-2 overflow-auto pdp-thumbs pb-1">
                    @foreach($uniqueImages as $imagePath)
                    <div class="thumb-item {{ $loop->first ? 'active' : '' }} border rounded overflow-hidden" onclick="pdpChangeImage('{{ getImageUrl($imagePath) }}', this)" style="width: 80px; height: 100px; flex-shrink: 0; cursor: pointer; transition: 0.3s;">
                        <img src="{{ getImageUrl($imagePath) }}" style="width: 100%; height: 100%; object-fit: cover;">
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
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="pdp-price" id="priceContainer">
                            @if($product->isOnSale())
                                <span class="h2 fw-bold text-gold m-0" id="displayPrice">{{ $product->formatted_sale_price }}</span>
                                <span class="text-danger text-decoration-line-through ms-2 h4 font-body" id="originalPrice">{{ $product->formatted_price }}</span>
                            @else
                                <span class="h2 fw-bold text-gold m-0" id="displayPrice">{{ $product->formatted_price }}</span>
                                <span class="text-danger text-decoration-line-through ms-2 h4 font-body d-none" id="originalPrice"></span>
                            @endif
                        </div>
                        
                        <span id="stockBadge" class="d-none small px-3 py-1 rounded-pill fw-bold font-body {{ $product->getTotalStockAttribute() > 0 ? 'bg-gold-light text-dark' : 'bg-light text-muted' }}">
                            @if($product->getTotalStockAttribute() > 0)
                            @else
                            @endif
                        </span>
                    </div>


                    <div class="bg-gold-light opacity-50 my-2" style=""></div>

                    {{-- VARIANT SELECTION --}}
                    @if($product->variants->count() > 0)
                        <div class="pdp-variants mb-3 font-body">
                            {{-- Style Images (Replaces Colors) --}}
                            @php $styles = $product->getAvailableStylesAttribute(); @endphp
                            @if($styles->count() > 0)
                                <div class="">
                                    <label class="fw-bold mb-2 d-block small text-muted text-uppercase">اختر اللون:</label>
                                    <div class="d-flex flex-wrap gap-3" id="styleOptions">
                                        @foreach($styles as $style)
                                            <div class="variant-option border rounded p-1" 
                                                 data-style-id="{{ $style->style_id }}"
                                                 data-image="{{ getImageUrl($style->color_image) }}" 
                                                 style="cursor: pointer; transition: 0.3s;"
                                                 onclick="selectStyle(this)"
                                                 title="Style">
                                                @php $imgUrl = getImageUrl($style->color_image) ?: asset('images/placeholder-product.jpg'); @endphp
                                                <div class="rounded bg-light" style="width: 60px; height: 70px; overflow: hidden; border: 1px solid rgba(0,0,0,0.1);">
                                                    <img src="{{ $imgUrl }}" alt="Style" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
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
                                    <button type="button" class="btn btn-link text-muted p-3 px-2 text-decoration-none" onclick="pdpChangeQty(1)" title="زيادة">
                                        <i class="fas fa-plus fs-5"></i>
                                    </button>
                                    <input type="number" name="quantity" id="pdpQty" value="1"
                                           min="1" max="{{ $product->getTotalStockAttribute() }}" 
                                           class="form-control border-0 text-center fw-bold px-1 font-body fs-5 flex-grow-1" 
                                           style="background: transparent; min-width: 40px;"
                                           oninput="if(Number(this.value) > Number(this.max)) this.value = this.max; if(Number(this.value) < 1 && this.value !== '') this.value = 1;">
                                    <button type="button" class="btn btn-link text-muted p-3 px-2 text-decoration-none" onclick="pdpChangeQty(-1)" title="نقص">
                                        <i class="fas fa-minus fs-5"></i>
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
                    <div class="row g-3 d-none">
                        <div class="col-5">
                            <div class="brand-card p-3 border-0 bg-gold-light text-center h-100">
                                <i class="fas fa-truck text-gold mb-2 d-block h4"></i>
                                <span class="small fw-bold font-body" style="font-size: 0.7rem;">توصيل سريع</span>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="brand-card p-3 border-0 bg-gold-light text-center h-100">
                                <i class="fas fa-shield-alt text-gold mb-2 d-block h4"></i>
                                <span class="small fw-bold font-body" style="font-size: 0.7rem;">دفع آمن</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- PRODUCT DESCRIPTION SECTION --}}
        <div class="pt-3 border-top">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="brand-heading m-0">تفاصيل المنتج</h3>
                    <div class="bg-gold mt-2 rounded" style="width: 40px; height: 3px;"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-muted font-body lh-lg" style="font-size: 1.05rem;">
                        {!! nl2br(e($product->translated_description)) !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- RELATED PRODUCTS --}}
        

    </div>
</section>
@endsection

@push('scripts')
<style>
    .variant-option.disabled {
        opacity: 0.35;
        cursor: not-allowed;
        pointer-events: none;
        position: relative;
        filter: grayscale(0.6);
        border-color: #dc3545 !important;
        background-color: rgba(220, 53, 69, 0.05);
    }
    .variant-option.disabled::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top right, transparent 48%, #dc3545 48%, #dc3545 52%, transparent 52%);
        opacity: 0.9;
        border-radius: inherit;
    }
</style>
<script>
// ── Variants Logic ───────────────────────────────
const variants = @json(json_decode($product->variants_json));
let selectedStyleId = null;
let selectedSize = null;
const baseIsOnSale = {{ $product->isOnSale() ? 'true' : 'false' }};
const basePrice = "{{ $product->formatted_price }}";
const baseSalePrice = "{{ $product->isOnSale() ? $product->formatted_sale_price : '' }}";
const mainImageSrc = "{{ getImageUrl($product->main_image) }}";

function selectStyle(el) {
    if (el.classList.contains('disabled')) return;
    if (el.classList.contains('active')) {
        // Prevent deselecting style
    } else {
        document.querySelectorAll('#styleOptions .variant-option').forEach(p => {
            p.classList.remove('active');
            p.style.borderColor = 'rgba(0,0,0,0.1)';
        });
        el.classList.add('active');
        el.style.borderColor = 'var(--brand-gold)';
        selectedStyleId = el.dataset.styleId;
        const styleImg = el.dataset.image;
        if (styleImg) pdpChangeImage(styleImg, null);
    }
    updateVariantSelection(true); 
}

function selectSize(el) {
    if (el.classList.contains('disabled')) return;
    if (el.classList.contains('active')) {
        el.classList.remove('active');
        el.style.borderColor = 'rgba(0,0,0,0.1)';
        selectedSize = null;
    } else {
        document.querySelectorAll('#sizeOptions .variant-option').forEach(p => {
            p.classList.remove('active');
            p.style.borderColor = 'rgba(0,0,0,0.1)';
        });
        el.classList.add('active');
        el.style.borderColor = 'var(--brand-gold)';
        selectedSize = el.dataset.size;
    }
    updateVariantSelection(false); 
}

function updateVariantSelection(updateColors = true) {
    const btn = document.getElementById('addToCartBtn');
    const input = document.getElementById('selectedVariantId');
    const stockBadge = document.getElementById('stockBadge');
    const priceDisplay = document.getElementById('displayPrice');
    
    input.value = '';
    const qtyInp = document.getElementById('pdpQty');
    if (qtyInp) qtyInp.value = 1;

    const match = variants.find(v => 
        (String(v.style_id) === String(selectedStyleId)) && 
        (String(v.size) === String(selectedSize))
    );

    if (match) {
        input.value = match.id;
        if (match.stock > 0) {
            btn.disabled = false;
            document.getElementById('pdpQty').max = match.stock;
        } else {
            btn.disabled = true;
        }
        
        // Update Price with Sale Logic
        const originalPriceEl = document.getElementById('originalPrice');
        if (match.is_on_sale) {
            priceDisplay.textContent = match.formatted_price;
            originalPriceEl.textContent = match.formatted_original_price;
            originalPriceEl.classList.remove('d-none');
        } else {
            priceDisplay.textContent = match.formatted_price;
            originalPriceEl.classList.add('d-none');
        }
    } else {
        if (selectedStyleId && selectedSize) {
            btn.disabled = true;
            if (stockBadge) {
                stockBadge.classList.add('d-none');
            }
        } else {
            btn.disabled = false;
            const totalStock = {{ $product->getTotalStockAttribute() }};
            if (stockBadge) {
                stockBadge.className = totalStock > 0 ? 'small px-3 py-1 rounded-pill fw-bold font-body bg-gold-light text-dark' : 'small px-3 py-1 rounded-pill fw-bold font-body bg-light text-muted';
            }
            
            if (!selectedStyleId && !selectedSize) {
                const originalPriceEl = document.getElementById('originalPrice');
                if (baseIsOnSale) {
                    priceDisplay.textContent = baseSalePrice;
                    originalPriceEl.textContent = basePrice;
                    originalPriceEl.classList.remove('d-none');
                } else {
                    priceDisplay.textContent = basePrice;
                    originalPriceEl.classList.add('d-none');
                }
                
                if (mainImageSrc) pdpChangeImage(mainImageSrc, document.querySelector('.thumb-item'));
            }
        }
    }
    updateAvailability(updateColors);
}

function updateAvailability(shoudUpdateColors = true) {
    const visibleSizePills = document.querySelectorAll('#sizeOptions .variant-option');
    const visibleSizes = Array.from(visibleSizePills).map(p => String(p.dataset.size));

    // 1. Update Size Pills
    visibleSizePills.forEach(pill => {
        const size = pill.dataset.size;
        let isAvailable = false;
        if (selectedStyleId) {
            isAvailable = variants.some(v => String(v.style_id) === String(selectedStyleId) && String(v.size) === String(size) && v.stock > 0);
        } else {
            isAvailable = variants.some(v => String(v.size) === String(size) && v.stock > 0);
        }
        pill.classList.toggle('disabled', !isAvailable);
    });

    // 2. Update Style Options
    if (shoudUpdateColors) {
        document.querySelectorAll('#styleOptions .variant-option').forEach(opt => {
            const sid = opt.dataset.styleId;
            const isAvailable = variants.some(v => {
                const matchesStyle = String(v.style_id) === String(sid);
                const hasStock = v.stock > 0;
                const hasValidSize = visibleSizes.length > 0 ? visibleSizes.includes(String(v.size)) : true;
                return matchesStyle && hasStock && hasValidSize;
            });
            opt.classList.toggle('disabled', !isAvailable);
        });
    }
}

function executeAutoSelect() {
    updateAvailability(true);
    const firstValidStyle = document.querySelector('#styleOptions .variant-option:not(.disabled)');
    if (firstValidStyle) {
        selectStyle(firstValidStyle);
    }
}

// Initial call
document.addEventListener('DOMContentLoaded', () => { 
    executeAutoSelect(); 
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
    let current = parseInt(inp.value);
    if (isNaN(current)) current = 1;

    if (delta > 0 && current >= max) {
        Swal.fire({
            toast: true,
            position: 'top-start',
            icon: 'warning',
            title: `المتوفر في المخزن هو ${max} فقط`,
            showConfirmButton: false,
            timer: 2000,
            background: '#1e293b',
            color: '#fff'
        });
        return;
    }

    const val = Math.min(max, Math.max(1, current + delta));
    inp.value = val;
}

function pdpAddToCart(event) {
    event.preventDefault();
    const btn      = document.getElementById('addToCartBtn');
    const quantity = document.getElementById('pdpQty').value;
    const productId = {{ $product->id }};
    const variantId = document.getElementById('selectedVariantId').value;

    if (variants.length > 0 && !variantId) {
        const hasStyles = document.getElementById('styleOptions');
        const hasSizes = document.getElementById('sizeOptions');
        let msg = 'الرجاء اختيار الشكل والمقاس أولاً';

        if (hasStyles && hasSizes) {
            if (!selectedStyleId && selectedSize) {
                msg = 'الرجاء تحديد الشكل المفضل';
            } else if (selectedStyleId && !selectedSize) {
                msg = 'الرجاء تحديد المقاس المطلوب';
            }
        } else if (hasStyles && !selectedStyleId) {
            msg = 'الرجاء تحديد الشكل المفضل';
        } else if (hasSizes && !selectedSize) {
            msg = 'الرجاء تحديد المقاس المطلوب';
        }

        Swal.fire({ icon:'warning', title:'تنبيه', text: msg, confirmButtonColor: '#c5a059' });
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
