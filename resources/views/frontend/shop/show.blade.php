@extends('layouts.frontend')

@section('meta_title', $product->translated_name . ' — ' . setting('app_name', 'Hijab Princesses'))
@section('meta_description', Str::limit(strip_tags($product->translated_description), 155))

@section('content')

{{-- =============================================
     BREADCRUMB
     ============================================= --}}
<section class="pdp-breadcrumb-bar py-3 bg-light border-bottom">
    <div class="container small">
        <nav class="pdp-breadcrumb" aria-label="breadcrumb">
            <a href="{{ url('/') }}" class="text-muted"><i class="fas fa-home"></i></a>
            <span class="mx-2 text-muted">/</span>
            <a href="{{ route('shop.index') }}" class="text-muted">المتجر</a>
            @if($product->productCategory)
                <span class="mx-2 text-muted">/</span>
                <a href="{{ route('shop.index', ['category' => $product->productCategory->slug]) }}" class="text-muted">{{ $product->productCategory->translated_name }}</a>
            @endif
            <span class="mx-2 text-muted">/</span>
            <span class="text-dark fw-bold">{{ Str::limit($product->translated_name, 40) }}</span>
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
                    <div class="thumb-item active" onclick="pdpChangeImage('{{ Storage::url($product->main_image) }}', this)" style="width: 80px; height: 100px; flex-shrink: 0; cursor: pointer; border: 2px solid transparent; border-radius: 8px; overflow: hidden;">
                        <img src="{{ Storage::url($product->main_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    @foreach($product->images as $img)
                    <div class="thumb-item" onclick="pdpChangeImage('{{ Storage::url($img->image_path) }}', this)" style="width: 80px; height: 100px; flex-shrink: 0; cursor: pointer; border: 2px solid transparent; border-radius: 8px; overflow: hidden;">
                        <img src="{{ Storage::url($img->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── INFO PANEL ── --}}
            <div class="col-lg-6">
                <div class="ps-lg-4">
                    @if($product->productCategory)
                    <div class="text-primary small fw-bold mb-2 text-uppercase ls-1">{{ $product->productCategory->translated_name }}</div>
                    @endif
                    
                    <h1 class="h2 fw-black mb-3">{{ $product->translated_name }}</h1>

                    {{-- Price & Stock --}}
                    <div class="d-flex align-items-baseline gap-3 mb-4">
                        @if($product->isOnSale())
                            <span class="h3 fw-bold text-primary m-0" id="displayPrice">{{ $product->formatted_sale_price }}</span>
                            <span class="text-muted text-decoration-line-through">{{ $product->formatted_price }}</span>
                        @else
                            <span class="h3 fw-bold text-primary m-0" id="displayPrice">{{ $product->formatted_price }}</span>
                        @endif
                        
                        <span id="stockBadge" class="small px-2 py-1 rounded-pill {{ $product->getTotalStockAttribute() > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            @if($product->getTotalStockAttribute() > 0)
                                <i class="fas fa-check-circle me-1"></i> متوفر حالياً
                            @else
                                <i class="fas fa-times-circle me-1"></i> غير متوفر
                            @endif
                        </span>
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
                                    <label class="fw-bold mb-3 d-block h6">اللون:</label>
                                    <div class="d-flex flex-wrap gap-3" id="colorOptions">
                                        @foreach($colors as $color)
                                            <div class="variant-option color-pill {{ $loop->first ? 'active' : '' }}" 
                                                 data-color="{{ $color->color }}" 
                                                 title="{{ $color->color }}"
                                                 onclick="selectColor(this)">
                                                @if($color->color_image)
                                                    <img src="{{ Storage::url($color->color_image) }}" alt="{{ $color->color }}">
                                                @elseif($color->color_code)
                                                    <span class="color-box" style="background-color: {{ $color->color_code }}"></span>
                                                @else
                                                    <span class="color-text">{{ $color->color }}</span>
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
                                    <label class="fw-bold mb-3 d-block h6">المقاس:</label>
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
                                <div class="d-flex align-items-center border rounded-pill bg-white px-2">
                                    <button type="button" class="btn btn-link link-dark p-2" onclick="pdpChangeQty(-1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" name="quantity" id="pdpQty" value="1"
                                           min="1" max="{{ $product->getTotalStockAttribute() }}" class="form-control border-0 text-center fw-bold" style="width: 50px; background: transparent;">
                                    <button type="button" class="btn btn-link link-dark p-2" onclick="pdpChangeQty(1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" id="addToCartBtn" class="btn btn-primary w-100 rounded-pill py-3 fw-bold"
                                        {{ $product->getTotalStockAttribute() <= 0 ? 'disabled' : '' }}>
                                    أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Trust Pills --}}
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 text-center h-100">
                                <i class="fas fa-truck text-primary mb-2 d-block h4"></i>
                                <span class="small fw-bold">توصيل سريع</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 text-center h-100">
                                <i class="fas fa-undo text-primary mb-2 d-block h4"></i>
                                <span class="small fw-bold">استرجاع سهل</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 text-center h-100">
                                <i class="fas fa-shield-alt text-primary mb-2 d-block h4"></i>
                                <span class="small fw-bold">دفع آمن</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RELATED PRODUCTS --}}
        @if($relatedProducts->count() > 0)
        <div class="mt-5 pt-5 border-top">
            <h3 class="fw-bold mb-4">منتجات قد تعجبك</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                <div class="col-6 col-md-3">
                    <div class="product-card-v2">
                        <div class="product-v2-image">
                            <a href="{{ route('shop.show', $related->id) }}">
                                <img src="{{ $related->main_image ? Storage::url($related->main_image) : asset('images/placeholder-product.jpg') }}" alt="{{ $related->translated_name }}">
                            </a>
                        </div>
                        <div class="product-v2-body">
                            <h5 class="product-v2-name"><a href="{{ route('shop.show', $related->id) }}">{{ Str::limit($related->translated_name, 30) }}</a></h5>
                            <div class="product-v2-price">{{ $related->formatted_price }}</div>
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
        document.querySelectorAll('.color-pill').forEach(p => p.classList.remove('active'));
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
        document.querySelectorAll('.size-pill').forEach(p => p.classList.remove('active'));
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
            stockBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> متوفر حالياً';
            stockBadge.className = 'small px-2 py-1 rounded-pill bg-success-subtle text-success';
            document.getElementById('pdpQty').max = match.stock;
        } else {
            btn.disabled = true;
            stockBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> غير متوفر';
            stockBadge.className = 'small px-2 py-1 rounded-pill bg-danger-subtle text-danger';
        }
        if (match.price) {
            priceDisplay.textContent = match.price + ' DH';
        }
        if (match.color_image) {
            pdpChangeImage(`/storage/${match.color_image}`, null);
        }
    } else {
        // Handle partial or no selection
        btn.disabled = (variants.length > 0); // Disable if we need a variant but don't have a full match
        
        if (selectedColor && selectedSize) {
            stockBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> غير متوفر بهذا المقاس/اللون';
            stockBadge.className = 'small px-2 py-1 rounded-pill bg-danger-subtle text-danger';
        } else {
            // Revert to global stock status
            const totalStock = {{ $product->getTotalStockAttribute() }};
            stockBadge.innerHTML = totalStock > 0 ? '<i class="fas fa-check-circle me-1"></i> متوفر حالياً' : '<i class="fas fa-times-circle me-1"></i> نفذ من المخزن';
            stockBadge.className = totalStock > 0 ? 'small px-2 py-1 rounded-pill bg-success-subtle text-success' : 'small px-2 py-1 rounded-pill bg-danger-subtle text-danger';
            
            // Revert price and image if nothing selected
            if (!selectedColor && !selectedSize) {
                priceDisplay.textContent = basePrice;
                if (mainImageSrc) pdpChangeImage(mainImageSrc, document.querySelector('.thumb-item'));
            } else if (selectedColor) {
                // If only color selected, maybe find first variant of that color to show price?
                const firstColVariant = variants.find(v => v.color === selectedColor);
                if (firstColVariant && firstColVariant.price) {
                    priceDisplay.textContent = firstColVariant.price + ' DH';
                }
                if (firstColVariant && firstColVariant.color_image) {
                    pdpChangeImage(`/storage/${firstColVariant.color_image}`, null);
                }
            }
        }
    }

    updateAvailability();
}

function updateAvailability() {
    // 1. Update Size Availability based on selected color
    document.querySelectorAll('.size-pill').forEach(pill => {
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
    document.querySelectorAll('.color-pill').forEach(pill => {
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

    // Auto-select first available combo
    const firstAvailable = variants.find(v => v.stock > 0);
    if (firstAvailable) {
        const colorPill = document.querySelector(`.color-pill[data-color="${firstAvailable.color}"]`);
        if (colorPill) selectColor(colorPill);
        const sizePill = document.querySelector(`.size-pill[data-size="${firstAvailable.size}"]`);
        if (sizePill) selectSize(sizePill);
    } else {
        // Just select first ones if everything is out of stock
        const firstColor = document.querySelector('.color-pill');
        if (firstColor) selectColor(firstColor);
        const firstSize = document.querySelector('.size-pill');
        if (firstSize) selectSize(firstSize);
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
        Swal.fire({ icon:'warning', title:'تنبيه', text:'الرجاء اختيار اللون والمقاس أولاً', confirmButtonColor: '#c5a059' });
        return;
    }

    btn.disabled = true;
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإضافة…';

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
