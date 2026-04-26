@extends('layouts.frontend')

@section('meta_title', $product->translated_name . ' — ' . __('Ait') . ' ' . __('Oumdis'))
@section('meta_description', Str::limit(strip_tags($product->translated_description), 155))
@section('meta_image', $product->main_image ? url(Storage::url($product->main_image)) : null)

@push('styles')
<style>
/* ── Product Page ── */
.pdp-wrap { background: #f9fafb; min-height: 80vh; }

/* Gallery */
.pdp-main-img {
    border-radius: 24px; overflow: hidden;
    aspect-ratio: 4/5; position: relative;
    background: #fff;
    box-shadow: 0 8px 40px rgba(0,0,0,.08);
}
.pdp-main-img img { width:100%; height:100%; object-fit:cover; transition: transform .6s cubic-bezier(.4,0,.2,1); }
.pdp-main-img:hover img { transform: scale(1.04); }
.pdp-badge { position:absolute; top:16px; right:16px; background:#ef4444; color:#fff; font-size:.72rem; font-weight:800; padding:6px 12px; border-radius:100px; }
.pdp-thumbs { display:flex; gap:10px; margin-top:14px; overflow-x:auto; padding-bottom:4px; }
.thumb-item {
    width:76px; height:96px; flex-shrink:0; border-radius:14px; overflow:hidden;
    border:2.5px solid transparent; cursor:pointer;
    transition:border-color .2s, box-shadow .2s;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.thumb-item.border-green { border-color: #3BB878; box-shadow: 0 4px 16px rgba(59,184,120,.25); }
.thumb-item img { width:100%; height:100%; object-fit:cover; }

/* Info panel */
.pdp-info { background:#fff; border-radius:24px; padding:36px; box-shadow:0 8px 40px rgba(0,0,0,.06); height:fit-content; }
.pdp-cat { font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:#3BB878; margin-bottom:8px; }
.pdp-title { font-size:clamp(1.5rem,3vw,2rem); font-weight:900; color:#111827; line-height:1.2; margin-bottom:16px; }
.pdp-rating { display:flex; align-items:center; gap:8px; margin-bottom:20px; }
.pdp-stars { font-size:.65rem; color:#f59e0b; }
.pdp-price { font-size:1.9rem; font-weight:900; color:#3BB878; }
.pdp-price-old { font-size:1rem; color:#9CA3AF; text-decoration:line-through; font-weight:600; }
.pdp-divider { height:1px; background:#f1f5f9; margin:24px 0; }
.pdp-desc { font-size:.93rem; color:#6B7280; line-height:1.75; margin-bottom:24px; }

/* Variant selectors */
.var-label { font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#374151; margin-bottom:10px; }
.size-pill {
    padding:9px 20px; border-radius:100px; font-size:.84rem; font-weight:700;
    cursor:pointer; border:2px solid #e5e7eb; color:#374151;
    transition:all .2s; white-space:nowrap;
}
.size-pill:hover { border-color:#3BB878; color:#3BB878; }
.size-pill.bg-green { background:#3BB878 !important; color:#fff !important; border-color:#3BB878; }

/* Qty + CTA */
.qty-wrap {
    display:flex; align-items:center; border:2px solid #e5e7eb;
    border-radius:100px; overflow:hidden; background:#f9fafb;
}
.qty-btn {
    width:42px; height:42px; border:none; background:transparent;
    font-size:.9rem; color:#374151; cursor:pointer; display:flex;
    align-items:center; justify-content:center; transition:background .2s;
}
.qty-btn:hover { background:#e8f7ef; color:#3BB878; }
.qty-input {
    width:48px; border:none; background:transparent; text-align:center;
    font-weight:800; font-size:1rem; color:#111827; outline:none;
}
.btn-atc {
    flex:1; background:#3BB878; color:#fff; border:none;
    border-radius:100px; font-weight:800; font-size:.95rem;
    padding:14px 24px; cursor:pointer; display:flex;
    align-items:center; justify-content:center; gap:8px;
    transition:all .3s cubic-bezier(.34,1.56,.64,1);
    box-shadow:0 6px 24px rgba(59,184,120,.3);
    white-space:nowrap;
}
.btn-atc:hover { background:#2f9461; transform:translateY(-2px); box-shadow:0 10px 32px rgba(59,184,120,.4); }
.btn-atc:active { transform:translateY(0); }

/* Trust badges */
.trust-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(110px, 1fr)); gap:12px; margin-top:24px; }
.trust-item {
    display:flex; align-items:center; gap:10px;
    background:#f9fafb; border-radius:12px; padding:12px;
    border:1px solid #f1f5f9;
}
.trust-icon { width:32px; height:32px; border-radius:50%; background:#e8f7ef; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.trust-text { font-size:.8rem; font-weight:700; color:#374151; }

/* Description tab */
.desc-section { background:#fff; border-radius:24px; padding:36px; box-shadow:0 4px 24px rgba(0,0,0,.05); margin-top:32px; }
.desc-section h3 { font-size:1.1rem; font-weight:800; color:#111827; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
.desc-section h3::after { content:''; flex:1; height:1px; background:#f1f5f9; }
.desc-body { font-size:.93rem; color:#6B7280; line-height:1.85; }

/* Stock indicator */
.stock-badge { display:inline-flex; align-items:center; gap:6px; font-size:.78rem; font-weight:700; }
.stock-dot { width:7px; height:7px; border-radius:50%; }

/* Breadcrumb */
    .hover-green { transition: all 0.3s; }
    .hover-green:hover { color: #3BB878 !important; }

    /* Shake animation */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .shake { animation: shake 0.2s ease-in-out 0s 2; }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="pdp-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-green text-decoration-none fw-600">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-green text-decoration-none fw-600">{{ __('Shop') }}</a></li>
                @if($product->productCategory)
                    <li class="breadcrumb-item"><a href="{{ route('shop.index', ['category' => $product->productCategory->slug]) }}" class="text-green text-decoration-none fw-600">{{ $product->productCategory->translated_name }}</a></li>
                @endif
                <li class="breadcrumb-item active text-muted">{{ Str::limit($product->translated_name, 30) }}</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Main Product Section --}}
<div class="pdp-wrap py-5">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-start">

            {{-- ── LEFT: Gallery ── --}}
            <div class="col-lg-6">
                <div class="pdp-main-img">
                    <img id="mainImage"
                         src="{{ $product->main_image ? Storage::url($product->main_image) : asset('images/placeholder-product.jpg') }}"
                         alt="{{ $product->translated_name }}">
                    @if($product->isOnSale())
                        <div class="pdp-badge">-{{ $product->discount_percentage }}%</div>
                    @endif
                </div>

                @php
                    $images = collect([$product->main_image])->merge($product->images->pluck('image_path'))->filter()->unique();
                @endphp
                @if($images->count() > 1)
                <div class="pdp-thumbs">
                    @foreach($images as $img)
                    <div class="thumb-item {{ $loop->first ? 'border-green' : '' }}"
                         onclick="changeImage('{{ Storage::url($img) }}', this)">
                        <img src="{{ Storage::url($img) }}" alt="">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── RIGHT: Info ── --}}
            <div class="col-lg-6">
                <div class="pdp-info">

                        @if($product->variants->count() == 0)
                            @if($product->isInStock())
                                <span class="stock-badge text-success">
                                    <span class="stock-dot bg-success"></span>{{ __('In Stock') }}
                                </span>
                            @else
                                <span class="stock-badge text-danger">
                                    <span class="stock-dot bg-danger"></span>{{ __('Out of Stock') }}
                                </span>
                            @endif
                        @else
                            <span id="variantStockBadge" class="stock-badge">
                                {{-- Will be updated by JS --}}
                            </span>
                        @endif

                    {{-- Title --}}
                    <h1 class="pdp-title">{{ $product->translated_name }}</h1>

                    {{-- Rating --}}
                    <div class="pdp-rating">
                        <div class="pdp-stars">
                            @for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor
                        </div>
                        <span class="small text-muted fw-600">({{ $product->reviews_count ?? rand(8,40) }} {{ __('reviews') }})</span>
                    </div>

                    {{-- Price --}}
                    <div class="d-flex align-items-baseline gap-3 mb-3">
                        <div class="pdp-price" id="displayPrice">
                            {{ $product->isOnSale() ? $product->formatted_sale_price : $product->formatted_price }}
                        </div>
                        @if($product->isOnSale())
                            <div class="pdp-price-old">{{ $product->formatted_price }}</div>
                        @endif
                    </div>

                    <div class="pdp-divider"></div>

                    {{-- Short description --}}
                    <p class="pdp-desc">{{ Str::limit(strip_tags($product->translated_description), 200) }}</p>

                    {{-- Form --}}
                    <form id="pdpForm" onsubmit="handleAddToCart(event)">
                        @csrf
                        <input type="hidden" name="variant_id" id="selectedVariantId" value="">

                        @if($product->variants->count() > 0)
                            {{-- Sizes --}}
                            @php $sizes = $product->getAvailableSizesAttribute(); @endphp
                            @if($sizes->count() > 0)
                                <div class="mb-4">
                                    <div class="var-label">{{ __('Select Size') }}</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($sizes as $size)
                                        <div class="size-pill"
                                             onclick="selectSize('{{ $size }}', this)">
                                            {{ $size }}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Qty + Add to Cart --}}
                        <div class="d-flex gap-3 mt-4 align-items-center" id="atc-wrapper">
                            {{-- Quantity --}}
                            <div class="qty-wrap">
                                <button type="button" class="qty-btn" onclick="updatePdpQty(-1)">
                                    <i class="fas fa-minus" style="font-size:.65rem"></i>
                                </button>
                                <input type="number" id="pdpQty" value="1" min="1" readonly class="qty-input">
                                <button type="button" class="qty-btn" onclick="updatePdpQty(1)">
                                    <i class="fas fa-plus" style="font-size:.65rem"></i>
                                </button>
                            </div>
                            {{-- CTA --}}
                            <div id="atc-button-container" class="flex-grow-1">
                                @if($product->isInStock())
                                <button type="submit" class="btn-atc w-100">
                                    <i class="fas fa-shopping-bag"></i>
                                    {{ __('Add to Cart') }}
                                </button>
                                @else
                                <button type="button" class="btn-atc w-100" disabled style="background:#9CA3AF;box-shadow:none;cursor:not-allowed;">
                                    <i class="fas fa-times-circle"></i>
                                    {{ __('Out of Stock') }}
                                </button>
                                @endif
                            </div>
                        </div>
                        <p id="stockLimitMsg" class="text-danger x-small fw-bold mt-2" style="display:none;">
                            <i class="fas fa-exclamation-triangle me-1"></i> {{ __('Only {stock} available in stock') }}
                        </p>
                    </form>

                    {{-- Trust badges --}}
                    <div class="pdp-divider"></div>
                    <div class="trust-grid">
                        <div class="trust-item">
                            <div class="trust-icon"><i class="fas fa-shield-alt text-green" style="font-size:.8rem"></i></div>
                            <span class="trust-text">{{ __('Guaranteed Quality') }}</span>
                        </div>
                        <div class="trust-item">
                            <div class="trust-icon"><i class="fas fa-truck text-green" style="font-size:.8rem"></i></div>
                            <span class="trust-text">{{ __('Fast Delivery') }}</span>
                        </div>
                        <div class="trust-item">
                            <div class="trust-icon"><i class="fas fa-leaf text-green" style="font-size:.8rem"></i></div>
                            <span class="trust-text">{{ __('100% Natural') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Full Description --}}
        <div class="desc-section">
            <h3>{{ __('Product Description') }}</h3>
            <div class="desc-body">
                {!! nl2br(e($product->translated_description)) !!}
            </div>
        </div>

        {{-- Related Products --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div>
                    <div class="section-label">{{ __('You May Also Like') }}</div>
                    <h3 class="section-title mb-0">{{ __('Related Products') }}</h3>
                </div>
            </div>
            <div class="row g-3">
                @foreach($relatedProducts as $product)
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                    @include('frontend.partials.product_card_v2', ['product' => $product])
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
    const variants = @json(json_decode($product->variants_json));
    let selectedSize = null;
    let currentStockLimit = {{ $product->stock ?? 0 }};

    function changeImage(src, el) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-item').forEach(i => i.classList.remove('border-green'));
        el.classList.add('border-green');
    }

    function selectSize(size, el) {
        selectedSize = size;
        document.querySelectorAll('.size-pill').forEach(i => i.classList.remove('bg-green', 'text-white'));
        el.classList.add('bg-green', 'text-white');
        checkVariant();
    }

    function checkVariant() {
        const variant = variants.find(v => v.size == selectedSize);
        if (variant) {
            document.getElementById('selectedVariantId').value = variant.id;
            document.getElementById('displayPrice').innerText = variant.formatted_price;
            
            // Update stock info
            currentStockLimit = variant.stock;
            updateStockUI(variant.stock);
            
            // Reset qty to 1 on size change
            const qtyInput = document.getElementById('pdpQty');
            qtyInput.value = 1;
            document.getElementById('stockLimitMsg').style.display = 'none';
        }
    }

    function updateStockUI(stock) {
        const badge = document.getElementById('variantStockBadge');
        const atcContainer = document.getElementById('atc-button-container');
        const qtyWrapper = document.querySelector('.qty-wrap');
        const limitMsg = document.getElementById('stockLimitMsg');

        if (!badge) return;

        if (stock > 0) {
            badge.innerHTML = `<span class="stock-dot bg-success"></span><span class="text-success">{{ __('In Stock') }} (${stock})</span>`;
            atcContainer.innerHTML = `
                <button type="submit" class="btn-atc w-100">
                    <i class="fas fa-shopping-bag"></i>
                    {{ __('Add to Cart') }}
                </button>`;
            qtyWrapper.style.opacity = '1';
            qtyWrapper.style.pointerEvents = 'auto';
            limitMsg.style.display = 'none';
        } else {
            badge.innerHTML = `<span class="stock-dot bg-danger"></span><span class="text-danger">{{ __('Out of Stock') }}</span>`;
            atcContainer.innerHTML = `
                <button type="button" class="btn-atc w-100" disabled style="background:#9CA3AF;box-shadow:none;cursor:not-allowed;">
                    <i class="fas fa-times-circle"></i>
                    {{ __('Out of Stock') }}
                </button>`;
            qtyWrapper.style.opacity = '0.5';
            qtyWrapper.style.pointerEvents = 'none';
            limitMsg.style.display = 'none';
        }
    }

    function updatePdpQty(delta) {
        const inp = document.getElementById('pdpQty');
        let newVal = parseInt(inp.value) + delta;
        const limitMsg = document.getElementById('stockLimitMsg');

        if (newVal < 1) newVal = 1;
        
        if (newVal > currentStockLimit) {
            newVal = currentStockLimit;
            if (currentStockLimit > 0) {
                limitMsg.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> ${"{{ __('Only {stock} available in stock') }}".replace('{stock}', currentStockLimit)}`;
                limitMsg.style.display = 'block';
                
                // Shake effect on the input
                inp.classList.add('shake');
                setTimeout(() => inp.classList.remove('shake'), 500);
            }
        } else {
            limitMsg.style.display = 'none';
        }
        
        inp.value = newVal;
    }

    function handleAddToCart(e) {
        e.preventDefault();
        const variantId = document.getElementById('selectedVariantId').value;
        const qty = parseInt(document.getElementById('pdpQty').value);

        if (variants.length > 0 && !variantId) {
            Swal.fire({ 
                icon: 'warning', 
                title: '{{ __('Please select a size') }}', 
                text: '{{ __('Please choose a size before adding to cart.') }}',
                confirmButtonColor: '#3BB878'
            });
            return;
        }

        if (qty > currentStockLimit) {
            Swal.fire({ 
                icon: 'error', 
                title: '{{ __('Stock Limit') }}', 
                text: '{{ __('Sorry, we only have {stock} items left in stock.') }}'.replace('{stock}', currentStockLimit),
                confirmButtonColor: '#3BB878'
            });
            return;
        }

        fetch(`{{ url('/cart/add') }}/{{ $product->id }}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ quantity: qty, variant_id: variantId })
        }).then(r => r.json()).then(data => {
            if(data.success) {
                Swal.fire({ icon: 'success', title: '{{ __('Added to cart!') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                document.getElementById('header-cart-count').innerText = data.cartCount;
                refreshMiniCart();
                const miniCartElement = document.getElementById('miniCart');
                if (miniCartElement) {
                    const miniCart = new bootstrap.Offcanvas(miniCartElement);
                    miniCart.show();
                }
            } else {
                Swal.fire({ icon: 'error', title: '{{ __('Oops!') }}', text: data.message || '{{ __('Could not add item to cart.') }}' });
            }
        }).catch(err => {
            console.error('Cart Error:', err);
            Swal.fire({ icon: 'error', title: '{{ __('Error') }}', text: '{{ __('Something went wrong. Please try again.') }}' });
        });
    }

    // Initialize stock UI if no variants
    document.addEventListener('DOMContentLoaded', () => {
        if (variants.length === 0) {
            updateStockUI({{ $product->stock ?? 0 }});
        }
    });
</script>

{{-- Product Schema (SEO/AEO/GEO) --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->translated_name }}",
  "image": [
    "{{ $product->main_image ? url(Storage::url($product->main_image)) : asset('images/placeholder-product.jpg') }}"
  ],
  "description": "{{ Str::limit(strip_tags($product->translated_description), 160) }}",
  "sku": "{{ $product->sku }}",
  "brand": {
    "@type": "Brand",
    "name": "{{ setting('app_name', 'Coop Ait Oumdis') }}"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "MAD",
    "price": "{{ $product->isOnSale() ? $product->sale_price : $product->price }}",
    "availability": "{{ $product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
    "itemCondition": "https://schema.org/NewCondition"
  }
}
</script>
@endpush
