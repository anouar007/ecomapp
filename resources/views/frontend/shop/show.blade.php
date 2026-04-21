@extends('layouts.frontend')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endpush

@section('meta_title', $product->translated_name . ' — Moubdi3oun')
@section('meta_description', Str::limit(strip_tags($product->translated_description), 160))
@section('meta_image', $product->main_image ? asset('storage/' . $product->main_image) : asset('images/og-default.jpg'))

@section('commerce_meta')
    <meta property="og:price:amount" content="{{ $product->price }}">
    <meta property="og:price:currency" content="MAD">
    <meta property="og:availability" content="{{ $product->getTotalStockAttribute() > 0 ? 'instock' : 'oos' }}">
    <meta property="product:brand" content="{{ setting('app_name', 'Moubdi3oun') }}">
@endsection

@section('content')

{{-- ── Minimal Breadcrumb ── --}}
<div class="py-4 bg-white border-bottom">
    <div class="container text-center">
        <nav class="shop-breadcrumb mb-0" aria-label="breadcrumb">
            <a href="{{ url('/') }}">{{ __('Home') }}</a>
            <span class="mx-3">/</span>
            <a href="{{ route('shop.index') }}">{{ __('Shop') }}</a>
            @if($product->productCategory)
                <span class="mx-3">/</span>
                <a href="{{ route('shop.index', ['category' => $product->productCategory->slug]) }}">{{ $product->productCategory->translated_name }}</a>
            @endif
            <span class="mx-3">/</span>
            <span class="fw-bold">{{ Str::limit($product->translated_name, 25) }}</span>
        </nav>
    </div>
</div>

<section class="pdp-body section-py bg-light">
    <div class="container">
        <div class="row g-5">

            {{-- 📸 GALLERY PANEL --}}
            <div class="col-lg-7" data-aos="fade-right">
                <div class="pdp-main-image-wrap" id="zoomWrap" onmousemove="pdpZoom(event)">
                    @if($product->main_image)
                        <img id="mainImage" src="{{ Storage::url($product->main_image) }}"
                             alt="{{ $product->translated_name }}" 
                             class="w-100 shadow-sm aspect-9-10" style="transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-white aspect-9-10">
                            <i class="fas fa-image fa-4x text-muted opacity-25"></i>
                        </div>
                    @endif

                    <div class="pdp-badges position-absolute top-0 start-0 p-4">
                        @if($product->getTotalStockAttribute() <= 0)
                            <span class="pbadge pbadge-oos">{{ __('Out of Stock') }}</span>
                        @elseif($product->isOnSale())
                            <span class="pbadge pbadge-sale">−{{ $product->discount_percentage }}%</span>
                        @endif
                    </div>
                </div>

                {{-- Thumbnails --}}
                @if($product->images->count() > 1)
                <div class="pdp-gallery-thumbs" data-aos="fade-up">
                    <img src="{{ Storage::url($product->main_image) }}" 
                         class="pdp-thumb admin-thumb active" 
                         onclick="pdpChangeImage('{{ Storage::url($product->main_image) }}', this)">
                    @foreach($product->images as $img)
                        <img src="{{ Storage::url($img->image_path) }}" 
                             class="pdp-thumb" 
                             onclick="pdpChangeImage('{{ Storage::url($img->image_path) }}', this)">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- 📝 INFO PANEL --}}
            <div class="col-lg-5" data-aos="fade-left">
                <div class="pdp-sticky-info">
                    
                    @if($product->productCategory)
                        <span class="pdp-category-tag">{{ $product->productCategory->translated_name }}</span>
                    @endif
                    <h1 class="pdp-title">{{ $product->translated_name }}</h1>

                    <div class="live-viewer-pill" data-aos="fade-up" data-aos-delay="100">
                        <span class="pulse-red me-3"></span>
                        <span id="live-viewers">...</span>&nbsp;{{ __('people viewing this right now') }}
                    </div>

                    {{-- Pricing --}}
                    <div class="pdp-pricing" data-aos="fade-up" data-aos-delay="200">
                        @if($product->isOnSale())
                            <span class="pdp-price-actual" id="displayPrice">{{ $product->formatted_sale_price }}</span>
                            <span class="pdp-price-old">{{ $product->formatted_price }}</span>
                        @else
                            <span class="pdp-price-actual" id="displayPrice">{{ $product->formatted_price }}</span>
                        @endif
                        
                        <div id="stockBadge" class="ms-auto badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-black text-uppercase ls-1" style="font-size: 0.6rem;">
                             <i class="fas fa-check-circle me-1"></i> {{ __('In Stock') }}
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="text-muted mb-5 lh-lg" style="font-size: 0.95rem;" data-aos="fade-up" data-aos-delay="300">
                        {!! nl2br(e($product->translated_description)) !!}
                    </div>

                    <form id="addToCartForm" onsubmit="pdpAddToCart(event)" data-aos="fade-up" data-aos-delay="400">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" id="selectedVariantId" value="">

                        {{-- Variants --}}
                        @if($product->variants->count() > 0)
                            {{-- Colors --}}
                            @php $colors = $product->getAvailableColorsAttribute(); @endphp
                            @if($colors->count() > 0)
                                <label class="pdp-variant-label">{{ __('Finish / Material') }}</label>
                                <div class="pdp-variant-options" id="colorOptions">
                                    @foreach($colors as $color)
                                        @php
                                            $isGloballyOOS = $product->variants->where('color', $color->color)->where('status', 'active')->sum('stock') <= 0;
                                        @endphp
                                        <div class="variant-pill {{ $isGloballyOOS ? 'disabled-option' : '' }}" 
                                             data-color="{{ $color->color }}" onclick="selectColor(this)">
                                            @if($color->color_code)
                                                <span class="rounded-circle border" style="width: 14px; height: 14px; background: {{ $color->color_code }}"></span>
                                            @endif
                                            {{ $color->color }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Sizes --}}
                            @php $sizes = $product->getAvailableSizesAttribute(); @endphp
                            @if($sizes->count() > 0)
                                <label class="pdp-variant-label">{{ __('Dimensions') }}</label>
                                <div class="pdp-variant-options" id="sizeOptions">
                                    @foreach($sizes as $size)
                                        @php
                                            $isGloballyOOS = $product->variants->where('size', $size)->where('status', 'active')->sum('stock') <= 0;
                                        @endphp
                                        <div class="variant-pill {{ $isGloballyOOS ? 'disabled-option' : '' }}" 
                                             data-size="{{ $size }}" onclick="selectSize(this)">
                                            {{ $size }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        {{-- Qty & Add --}}
                        <div class="pdp-cta-group">
                            <div class="pdp-qty-wrap">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(-1)"><i class="fas fa-minus"></i></button>
                                <input type="number" name="quantity" id="pdpQty" value="1" min="1" max="{{ $product->getTotalStockAttribute() }}" 
                                       class="pdp-qty-input shadow-none" 
                                       oninput="this.value = !!this.value && Math.abs(this.value) >= 1 ? Math.min(parseInt(this.max) || 1, Math.abs(this.value)) : 1">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(1)"><i class="fas fa-plus"></i></button>
                            </div>
                            <button type="submit" id="addToCartBtn" class="pdp-add-btn">
                                {{ __('Add to Bag') }} <i class="fas fa-shopping-bag ms-2"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        {{-- 📐 SPECIFICATIONS SECTION --}}
        <div class="pdp-spec-section" data-aos="fade-up">
            <h2 class="pdp-section-title text-center">{{ __('Technical Details & Craftsmanship') }}</h2>
            <div class="pdp-spec-grid">
                <div class="spec-item">
                    <span class="spec-label">{{ __('Material') }}</span>
                    <span class="spec-value">{{ __('Artisanal Solid Oak & Velvet') }}</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">{{ __('Handmade in') }}</span>
                    <span class="spec-value">{{ __('Marrakech, Morocco') }}</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">{{ __('Lead Time') }}</span>
                    <span class="spec-value">{{ __('4-6 Weeks for Custom Pieces') }}</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">{{ __('Warranty') }}</span>
                    <span class="spec-value">{{ __('5 Years Structural') }}</span>
                </div>
            </div>
        </div>

        {{-- ── RELATED PRODUCTS ── --}}
        @if($relatedProducts->count() > 0)
        <div class="mt-5 pt-5 border-top">
            <h2 class="related-header" data-aos="fade-right">{{ __('Related Masterpieces') }}</h2>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="pcard-v2">
                        <div class="pcard-media">
                            <a href="{{ route('shop.show', $related->id) }}">
                                @php
                                    $mainThumb = $related->main_image ? Storage::url($related->main_image) : asset('images/placeholder-product.jpg');
                                    $hoverImage = $related->images->count() > 1 ? Storage::url($related->images[1]->image_path) : $mainThumb;
                                @endphp
                                <img src="{{ $mainThumb }}" alt="{{ $related->translated_name }}" class="pcard-img-main">
                                <img src="{{ $hoverImage }}" alt="{{ $related->translated_name }} Hover" class="pcard-img-hover">
                            </a>
                        </div>
                        <div class="pcard-info">
                            @if($related->productCategory)
                                <div class="pcard-tag">{{ $related->productCategory->translated_name }}</div>
                            @endif
                            <h4 class="pcard-name-creative">
                                <a href="{{ route('shop.show', $related->id) }}">{{ $related->translated_name }}</a>
                            </h4>
                            <div class="pcard-meta-row">
                                <span class="price-val">{{ $related->formatted_price }}</span>
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

<style>
.pulse-red {
    display: inline-block; width: 8px; height: 8px; background: #ef4444;
    border-radius: 50%; box-shadow: 0 0 0 rgba(239, 68, 68, 0.4);
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
    100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}
</style>

@endsection

@push('scripts')
<script>
const variants = {!! $product->variants->where('status', 'active')->values()->toJson() !!};
let selectedColor = null;
let selectedSize = null;
const basePrice = "{{ $product->isOnSale() ? $product->formatted_sale_price : $product->formatted_price }}";
const mainImageSrc = "{{ $product->main_image ? Storage::url($product->main_image) : '' }}";

function selectColor(el) {
    if (el.classList.contains('disabled-option')) return;
    
    if (el.classList.contains('active')) {
        el.classList.remove('active');
        selectedColor = null;
    } else {
        document.querySelectorAll('#colorOptions .variant-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        selectedColor = el.dataset.color;
    }
    
    // Reset quantity on change for perfect UX
    const qtyInput = document.getElementById('pdpQty');
    if (qtyInput) qtyInput.value = 1;
    
    updateVariantSelection();
}

function selectSize(el) {
    if (el.classList.contains('disabled-option')) return;

    if (el.classList.contains('active')) {
        el.classList.remove('active');
        selectedSize = null;
    } else {
        document.querySelectorAll('#sizeOptions .variant-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        selectedSize = el.dataset.size;
    }
    
    // Reset quantity on change for perfect UX
    const qtyInput = document.getElementById('pdpQty');
    if (qtyInput) qtyInput.value = 1;
    
    updateVariantSelection();
}

function updateVariantSelection() {
    const btn = document.getElementById('addToCartBtn');
    const input = document.getElementById('selectedVariantId');
    const stockBadge = document.getElementById('stockBadge');
    const priceDisplay = document.getElementById('displayPrice');
    const qtyInput = document.getElementById('pdpQty');
    
    input.value = '';

    const match = variants.find(v => (v.color === selectedColor) && (v.size === selectedSize));

    if (match) {
        input.value = match.id;
        const availableStock = parseInt(match.stock) || 0;

        if (availableStock > 0) {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
            stockBadge.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${availableStock} {{ __("In Stock") }}`;
            stockBadge.className = 'ms-auto badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-black text-uppercase ls-1';
            
            // Manage Quantity
            qtyInput.max = availableStock;
            if (parseInt(qtyInput.value) > availableStock) {
                qtyInput.value = availableStock;
            }
        } else {
            btn.disabled = true;
            btn.classList.add('opacity-50');
            stockBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> {{ __("Out of Stock") }}';
            stockBadge.className = 'ms-auto badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-black text-uppercase ls-1';
            qtyInput.value = 1;
            qtyInput.max = 1;
        }
        
        if (match.price) {
            priceDisplay.textContent = new Intl.NumberFormat('fr-FR').format(match.price) + ' DH';
        }
    } else {
        // Handle incomplete selection
        btn.disabled = (variants.length > 0); 
        if (variants.length > 0) {
            btn.classList.add('opacity-50');
            stockBadge.innerHTML = '<i class="fas fa-info-circle me-1"></i> {{ __("Select Options") }}';
            stockBadge.className = 'ms-auto badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2 fw-black text-uppercase ls-1';
        }
    }
    updateAvailability();
}

function updateAvailability() {
    if (!variants || variants.length === 0) return;

    // Cross-check sizes based on selected color
    document.querySelectorAll('#sizeOptions .variant-pill').forEach(pill => {
        const size = pill.dataset.size;
        const isAvailable = variants.some(v => 
            (!selectedColor || v.color === selectedColor) && 
            (v.size === size) && 
            (parseInt(v.stock) > 0)
        );
        
        const isDisabled = !isAvailable;
        pill.classList.toggle('disabled-option', isDisabled);
        
        // If it's now disabled but was active, deselect it
        if (isDisabled && pill.classList.contains('active')) {
            pill.classList.remove('active');
            selectedSize = null;
            // Recursively update to re-enable other options that might have been blocked by this selection
            setTimeout(updateVariantSelection, 0); 
        }
    });

    // Cross-check colors based on selected size
    document.querySelectorAll('#colorOptions .variant-pill').forEach(pill => {
        const color = pill.dataset.color;
        const isAvailable = variants.some(v => 
            (!selectedSize || v.size === selectedSize) && 
            (v.color === color) && 
            (parseInt(v.stock) > 0)
        );
        
        const isDisabled = !isAvailable;
        pill.classList.toggle('disabled-option', isDisabled);
        
        // If it's now disabled but was active, deselect it
        if (isDisabled && pill.classList.contains('active')) {
            pill.classList.remove('active');
            selectedColor = null;
            setTimeout(updateVariantSelection, 0);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateAvailability();
    
    const totalStock = {{ $product->getTotalStockAttribute() }};
    const btn = document.getElementById('addToCartBtn');
    const stockBadge = document.getElementById('stockBadge');

    if (totalStock <= 0) {
        btn.disabled = true;
        btn.classList.add('opacity-50');
        btn.innerHTML = '{{ __("Out of Stock") }}';
        stockBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> {{ __("Out of Stock") }}';
        stockBadge.className = 'ms-auto badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-black text-uppercase ls-1';
    } 
    else if (variants.length === 0) {
        stockBadge.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${totalStock} {{ __("In Stock") }}`;
    }
    else {
        const firstAvailable = variants.find(v => parseInt(v.stock) > 0);
        if (firstAvailable) {
            const colorPill = document.querySelector(`#colorOptions .variant-pill[data-color="${firstAvailable.color}"]`);
            if (colorPill) selectColor(colorPill);
            const sizePill = document.querySelector(`#sizeOptions .variant-pill[data-size="${firstAvailable.size}"]`);
            if (sizePill) selectSize(sizePill);
        }
    }

    const viewerCount = document.getElementById('live-viewers');
    if (viewerCount) {
        const updateViewers = () => { viewerCount.textContent = Math.floor(Math.random() * 10) + 3; };
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
    img.style.transform = "scale(1.8)";
}
document.getElementById('zoomWrap').onmouseleave = () => {
    const img = document.getElementById('mainImage');
    if(img) img.style.transform = "scale(1)";
}

function pdpChangeImage(src, thumb) {
    const mainImg = document.getElementById('mainImage');
    if (!mainImg) return;
    mainImg.style.opacity = '0.5';
    mainImg.src = src;
    setTimeout(() => { mainImg.style.opacity = '1'; }, 100);
    if (thumb) {
        document.querySelectorAll('.pdp-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    }
}

function pdpChangeQty(delta) {
    const inp = document.getElementById('pdpQty');
    const max = parseInt(inp.max) || 99;
    const val = Math.min(max, Math.max(1, parseInt(inp.value) + delta));
    inp.value = val;
}

function pdpAddToCart(event) {
    event.preventDefault();
    const btn = document.getElementById('addToCartBtn');
    const quantity = document.getElementById('pdpQty').value;
    const productId = {{ $product->id }};
    const variantId = document.getElementById('selectedVariantId').value;

    const colorLabel = document.querySelector('#colorOptions')?.previousElementSibling;
    const sizeLabel = document.querySelector('#sizeOptions')?.previousElementSibling;

    if (variants.length > 0 && !variantId) {
        // Modern Visual Feedback
        if (!selectedColor && colorLabel) {
            colorLabel.classList.add('shake-attention', 'text-danger');
            setTimeout(() => colorLabel.classList.remove('shake-attention', 'text-danger'), 1000);
        }
        if (!selectedSize && sizeLabel) {
            sizeLabel.classList.add('shake-attention', 'text-danger');
            setTimeout(() => sizeLabel.classList.remove('shake-attention', 'text-danger'), 1000);
        }

        Swal.fire({ 
            toast: true,
            position: 'top-end',
            icon: 'warning', 
            title: '{{ __("Selection Required") }}', 
            text: '{{ __("Please choose your preferred finish and dimensions.") }}', 
            showConfirmButton: false, 
            timer: 4000,
            timerProgressBar: true,
            background: '#1a1a1a',
            color: '#fff',
            iconColor: '#f59e0b'
        });
        return;
    }

    btn.disabled = true;
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
            Swal.fire({ 
                toast: true,
                position: 'top-end',
                icon: 'success', 
                title: '{{ __("Item Added") }}',
                text: '{{ __("The masterpiece has been added to your bag.") }}',
                showConfirmButton: false, 
                timer: 4000,
                timerProgressBar: true,
                background: '#1a1a1a',
                color: '#fff',
                iconColor: '#fff'
            });
            if (typeof refreshMiniCart === 'function') refreshMiniCart();
        } else {
            // Display backend validation error (e.g., maximum stock reached)
            Swal.fire({ 
                toast: true,
                position: 'top-end',
                icon: 'warning', 
                title: '{{ __("Stock Limit") }}',
                text: data.message || '{{ __("Unable to add item.") }}',
                showConfirmButton: false, 
                timer: 4000,
                timerProgressBar: true,
                background: '#1a1a1a',
                color: '#fff',
                iconColor: '#f59e0b'
            });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = orig;
        Swal.fire({ 
            icon: 'error', 
            title: '{{ __("Error") }}',
            text: '{{ __("Something went wrong. Please try again.") }}',
            confirmButtonColor: '#000'
        });
    });
}
</script>
@endpush
