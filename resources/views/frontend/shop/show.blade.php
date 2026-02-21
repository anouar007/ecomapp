@extends('layouts.frontend')

@section('meta_title', $product->name . ' — ' . setting('app_name', 'Speed Print'))
@section('meta_description', Str::limit(strip_tags($product->description), 155))

@section('content')

{{-- =============================================
     PRODUCT HERO STRIP (dark, matching site design)
     ============================================= --}}
<section class="pdp-breadcrumb-bar">
    <div class="container">
        <nav class="pdp-breadcrumb" aria-label="breadcrumb">
            <a href="{{ url('/') }}"><i class="fas fa-home"></i></a>
            <span class="pdp-bc-sep">/</span>
            <a href="{{ route('shop.index') }}">Catalogue</a>
            @if($product->category_name)
                <span class="pdp-bc-sep">/</span>
                <a href="{{ route('shop.index', ['category' => optional($product->category)->slug]) }}">{{ $product->category_name }}</a>
            @endif
            <span class="pdp-bc-sep">/</span>
            <span class="pdp-bc-current">{{ Str::limit($product->name, 40) }}</span>
        </nav>
    </div>
</section>

{{-- =============================================
     MAIN PRODUCT LAYOUT
     ============================================= --}}
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

                {{-- ── IMAGE PANEL ── --}}
                <div class="col-lg-6 pdp-image-panel">
                    {{-- Main Image --}}
                    <div class="pdp-main-image-wrap" id="zoomWrap" onmousemove="pdpZoom(event)">
                        @if($product->main_image)
                            <img id="mainImage" src="{{ Storage::url($product->main_image) }}"
                                 alt="{{ $product->name }}" class="pdp-main-image">
                        @else
                            <div class="pdp-no-image"><i class="fas fa-print"></i></div>
                        @endif

                        {{-- Badges --}}
                        <div class="pdp-badges">
                            @if(!$product->isInStock())
                                <span class="pdp-badge pdp-badge--oos">Rupture de stock</span>
                            @elseif($product->created_at->diffInDays(now()) < 14)
                                <span class="pdp-badge pdp-badge--new">Nouveau</span>
                            @elseif($product->isOnSale())
                                <span class="pdp-badge pdp-badge--sale">−{{ $product->discount_percentage }}%</span>
                            @endif
                        </div>
                    </div>

                    {{-- Thumbnail Strip --}}
                    @if($product->images->count() > 0)
                    <div class="pdp-thumbs">
                        {{-- First thumb = main image --}}
                        <div class="pdp-thumb active"
                             onclick="pdpChangeImage('{{ Storage::url($product->main_image) }}', this)">
                            <img src="{{ Storage::url($product->main_image) }}" alt="Main">
                        </div>
                        @foreach($product->images as $img)
                        <div class="pdp-thumb"
                             onclick="pdpChangeImage('{{ Storage::url($img->image_path) }}', this)">
                            <img src="{{ Storage::url($img->image_path) }}" alt="Vue {{ $loop->iteration + 1 }}">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- ── INFO PANEL ── --}}
                <div class="col-lg-6 pdp-info-panel">
                    {{-- Category --}}
                    @if($product->category_name)
                    <div class="pdp-cat-label">{{ $product->category_name }}</div>
                    @endif

                    {{-- Title --}}
                    <h1 class="pdp-title">{{ $product->name }}</h1>

                    {{-- Rating Row --}}
                    @if($reviews->total() > 0)
                    <div class="pdp-rating-row">
                        <div class="pdp-stars">
                            @php $avg = $product->reviews()->avg('rating') ?? 0; @endphp
                            @for($i = 0; $i < 5; $i++)
                                <i class="fa{{ $i < round($avg) ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                        <span class="pdp-rating-count">{{ number_format($avg, 1) }} ({{ $reviews->total() }} avis)</span>
                    </div>
                    @endif

                    {{-- Price --}}
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
                                <i class="fas fa-check-circle me-1"></i>En stock
                            </span>
                        @else
                            <span class="pdp-stock-badge pdp-stock-badge--out">
                                <i class="fas fa-times-circle me-1"></i>Rupture
                            </span>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if($product->description)
                    <div class="pdp-description">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                    @endif

                    {{-- Divider --}}
                    <div class="pdp-divider"></div>

                    {{-- Add to Cart Form --}}
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
                            <button type="submit" id="addToCartBtn" class="pdp-add-btn"
                                    {{ !$product->isInStock() ? 'disabled' : '' }}>
                                <i class="fas fa-cart-plus me-2"></i>
                                <span id="addToCartText">Ajouter au panier</span>
                            </button>
                        </div>
                    </form>

                    {{-- Trust Pills --}}
                    <div class="pdp-trust-row">
                        <div class="pdp-trust-pill"><i class="fas fa-truck"></i> Livraison rapide</div>
                        <div class="pdp-trust-pill"><i class="fas fa-undo"></i> Retours 30j</div>
                        <div class="pdp-trust-pill"><i class="fas fa-shield-alt"></i> Paiement sécurisé</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- =============================================
             REVIEWS + WRITE A REVIEW
             ============================================= --}}
        <div class="row g-4 mt-4">
            {{-- Reviews List --}}
            <div class="col-lg-8">
                <div class="pdp-section-card">
                    <h3 class="pdp-section-title">
                        <i class="fas fa-star me-2 text-accent"></i>
                        Avis clients <span class="pdp-section-count">({{ $reviews->total() }})</span>
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
                            Par <strong>{{ $review->customer_name }}</strong> · {{ $review->created_at->format('d M Y') }}
                        </div>
                    </div>
                    @empty
                    <div class="pdp-review-empty">
                        <i class="far fa-comment-dots"></i>
                        <p>Aucun avis pour le moment. Soyez le premier à partager le vôtre !</p>
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
                        <i class="fas fa-pen me-2 text-accent"></i>Laisser un avis
                    </h3>
                    <form action="{{ route('reviews.store') }}" method="POST" class="pdp-review-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        @guest
                        <div class="pdp-form-group">
                            <label class="pdp-form-label">Votre nom</label>
                            <input type="text" name="customer_name" class="pdp-form-input"
                                   placeholder="Jean Dupont" value="{{ old('customer_name') }}" required>
                            @error('customer_name')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pdp-form-group">
                            <label class="pdp-form-label">Votre email</label>
                            <input type="email" name="customer_email" class="pdp-form-input"
                                   placeholder="jean@exemple.com" value="{{ old('customer_email') }}" required>
                            @error('customer_email')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>
                        @endguest

                        <div class="pdp-form-group">
                            <label class="pdp-form-label">Note</label>
                            <select name="rating" class="pdp-form-select" required>
                                <option value="5">★★★★★ Excellent (5/5)</option>
                                <option value="4">★★★★☆ Très bien (4/5)</option>
                                <option value="3">★★★☆☆ Bien (3/5)</option>
                                <option value="2">★★☆☆☆ Moyen (2/5)</option>
                                <option value="1">★☆☆☆☆ Mauvais (1/5)</option>
                            </select>
                        </div>

                        <div class="pdp-form-group">
                            <label class="pdp-form-label">Titre</label>
                            <input type="text" name="title" class="pdp-form-input"
                                   placeholder="Résumé de votre expérience" value="{{ old('title') }}" required>
                            @error('title')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="pdp-form-group">
                            <label class="pdp-form-label">Commentaire</label>
                            <textarea name="comment" class="pdp-form-input" rows="4"
                                      placeholder="Comment avez-vous trouvé ce produit ?" required>{{ old('comment') }}</textarea>
                            @error('comment')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="pdp-submit-btn w-100">
                            <i class="fas fa-paper-plane me-2"></i>Publier l'avis
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- =============================================
             RELATED PRODUCTS — uses pcard style from shop
             ============================================= --}}
        @if($relatedProducts->count() > 0)
        <div class="pdp-related mt-5">
            <div class="pdp-related-header">
                <h3 class="pdp-related-title">Produits similaires</h3>
                <a href="{{ route('shop.index') }}" class="pdp-related-link">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                <div class="col-6 col-md-3">
                    <div class="pcard">
                        <div class="pcard-img">
                            <a href="{{ route('shop.show', $related->id) }}">
                                @if($related->main_image)
                                    <img src="{{ Storage::url($related->main_image) }}"
                                         alt="{{ $related->name }}" loading="lazy">
                                @else
                                    <div class="pcard-no-img"><i class="fas fa-print"></i></div>
                                @endif
                            </a>
                            {{-- Badges --}}
                            @if(!$related->isInStock())
                                <div class="pcard-badges"><span class="pcard-badge pcard-badge--oos">Rupture</span></div>
                            @elseif($related->isOnSale())
                                <div class="pcard-badges"><span class="pcard-badge pcard-badge--sale">−{{ $related->discount_percentage }}%</span></div>
                            @endif
                            {{-- Overlay --}}
                            <div class="pcard-overlay">
                                <a href="{{ route('shop.show', $related->id) }}" class="pcard-overlay-btn pcard-overlay-btn--ghost">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                        <div class="pcard-body">
                            @if($related->category_name)
                                <div class="pcard-cat">{{ $related->category_name }}</div>
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
// ── Zoom ──────────────────────────────────────────
function pdpZoom(e) {
    const wrap = document.getElementById('zoomWrap');
    const img  = document.getElementById('mainImage');
    if (!img) return;
    const x = (e.offsetX / wrap.offsetWidth)  * 100;
    const y = (e.offsetY / wrap.offsetHeight) * 100;
    img.style.transformOrigin = `${x}% ${y}%`;
}

// ── Gallery ───────────────────────────────────────
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

// ── Quantity ──────────────────────────────────────
function pdpChangeQty(delta) {
    const inp = document.getElementById('pdpQty');
    const max = parseInt(inp.max) || 9999;
    const val = Math.min(max, Math.max(1, parseInt(inp.value) + delta));
    inp.value = val;
}

// ── Add to Cart ───────────────────────────────────
function pdpAddToCart(event) {
    event.preventDefault();
    const btn      = document.getElementById('addToCartBtn');
    const btnText  = document.getElementById('addToCartText');
    const quantity = document.getElementById('pdpQty').value;
    const productId = {{ $product->id }};

    btn.disabled = true;
    const orig = btnText.innerHTML;
    btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout…';

    fetch(`/cart/add/${productId}`, {
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
            const badge = document.getElementById('header-cart-count');
            if (badge && data.cartCount !== undefined) badge.textContent = data.cartCount;
            if (typeof refreshMiniCart === 'function') refreshMiniCart();
            Swal.fire({ toast:true, position:'top-end', icon:'success',
                title:'Ajouté au panier !',
                text:'{{ addslashes($product->name) }}',
                showConfirmButton:false, timer:2500,
                background:'#1a1a2e', color:'#fff' });
        } else {
            throw new Error(data.message || 'Erreur');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btnText.innerHTML = orig;
        Swal.fire({ icon:'error', title:'Erreur', text: err.message || 'Impossible d\'ajouter au panier.' });
    });
}
</script>
@endpush
