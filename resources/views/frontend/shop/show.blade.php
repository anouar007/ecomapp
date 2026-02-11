@extends('layouts.frontend')

@section('meta_title', $product->name . ' - Speed Platform')
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@section('content')
<style>
    .zoom-container {
        position: relative;
        overflow: hidden;
        cursor: zoom-in;
    }
    .zoom-container img {
        transition: transform 0.1s ease-out;
        transform-origin: center center;
    }
    .zoom-container:hover img {
        transform: scale(2); 
    }
</style>

<div class="bg-light py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
                @if($product->category_id && $product->category instanceof \App\Models\Category)
                <li class="breadcrumb-item"><a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
                @elseif($product->category_name)
                 <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">{{ $product->category_name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-body p-0">
                <div class="row g-0">
                    <!-- Product Image -->
                    <div class="col-lg-6 bg-white border-end">
                        
                        <div class="p-5 d-flex align-items-center justify-content-center" style="min-height: 500px;">
                            <div class="zoom-container rounded-3 overflow-hidden" onmousemove="zoom(event)" style="width: 100%; height: 400px; display: flex; align-items: center; justify-content: center;">
                                @if($product->main_image)
                                <img id="mainImage" src="{{ Storage::url($product->main_image) }}" class="img-fluid" alt="{{ $product->name }}" style="max-height: 400px;">
                                @else
                                <i class="fas fa-image fa-5x text-muted opacity-25"></i>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Thumbnail Gallery -->
                        @if($product->images->count() > 0)
                        <div class="px-5 pb-5">
                            <div class="row g-2 justify-content-center">
                                <div class="col-3">
                                    <img src="{{ Storage::url($product->main_image) }}" 
                                         class="img-thumbnail cursor-pointer gallery-thumb active-thumb w-100" 
                                         onclick="changeImage(this.src, this)"
                                         style="height: 80px; object-fit: cover;">
                                </div>
                                @foreach($product->images as $image)
                                <div class="col-3">
                                    <img src="{{ Storage::url($image->image_path) }}" 
                                         class="img-thumbnail cursor-pointer gallery-thumb w-100" 
                                         onclick="changeImage(this.src, this)"
                                         style="height: 80px; object-fit: cover;">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Product Details -->
                    <div class="col-lg-6 bg-white">
                        <div class="p-5">
                            <h6 class="text-uppercase fw-bold text-muted mb-2">{{ $product->category_name }}</h6>
                            <h1 class="display-6 fw-bold mb-3">{{ $product->name }}</h1>
                            <div class="mb-4">
                                <span class="h2 fw-bold text-primary me-2">{{ $product->formatted_price }}</span>
                                @if(!$product->isInStock())
                                    <span class="badge bg-danger rounded-pill align-top">Out of Stock</span>
                                @else
                                    <span class="badge bg-success rounded-pill align-top">In Stock</span>
                                @endif
                            </div>

                            <div class="mb-5 lead opacity-75">
                                {!! nl2br(e($product->description)) !!}
                            </div>

                            <form id="addToCartForm" onsubmit="addToCartAjax(event)">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="row g-3 align-items-end mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">QUANTITY</label>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control form-control-lg text-center bg-light border-0">
                                    </div>
                                    <div class="col-md-8">
                                        <button type="submit" id="addToCartBtn" class="btn btn-primary btn-lg w-100 rounded-3 py-3 fw-bold" {{ !$product->isInStock() ? 'disabled' : '' }}>
                                            <i class="fas fa-shopping-cart me-2"></i> <span id="addToCartText">Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <script>
                            function addToCartAjax(event) {
                                event.preventDefault();
                                
                                const btn = document.getElementById('addToCartBtn');
                                const btnText = document.getElementById('addToCartText');
                                const quantity = document.getElementById('quantity').value;
                                const productId = {{ $product->id }};
                                
                                // Disable button and show loading
                                btn.disabled = true;
                                const originalText = btnText.innerHTML;
                                btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                                
                                fetch(`/cart/add/${productId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ quantity: parseInt(quantity) })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Update cart count in header (consistent with other pages)
                                        const cartCount = document.getElementById('header-cart-count');
                                        if (cartCount && data.cartCount !== undefined) {
                                            cartCount.textContent = data.cartCount;
                                        }
                                        
                                        // Refresh mini-cart content
                                        if (typeof refreshMiniCart === 'function') {
                                            refreshMiniCart();
                                        }
                                        
                                        // Show success feedback
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Added to Cart!',
                                                text: '{{ $product->name }} has been added to your cart.',
                                                showConfirmButton: false,
                                                timer: 2000,
                                                toast: true,
                                                position: 'top-end',
                                                background: '#1a1a2e',
                                                color: '#fff'
                                            });
                                        } else {
                                            // Fallback: show inline success
                                            btnText.innerHTML = '<i class="fas fa-check"></i> Added!';
                                            btn.classList.remove('btn-primary');
                                            btn.classList.add('btn-success');
                                            setTimeout(() => {
                                                btnText.innerHTML = originalText;
                                                btn.classList.remove('btn-success');
                                                btn.classList.add('btn-primary');
                                            }, 2000);
                                        }
                                    } else {
                                        throw new Error(data.message || 'Failed to add to cart');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops!',
                                            text: error.message || 'Failed to add item to cart. Please try again.',
                                        });
                                    } else {
                                        alert('Failed to add item to cart. Please try again.');
                                    }
                                })
                                .finally(() => {
                                    btn.disabled = false;
                                    if (document.getElementById('addToCartText').innerHTML.includes('Adding')) {
                                        btnText.innerHTML = originalText;
                                    }
                                });
                            }
                            </script>

                            <hr class="my-4 opacity-10">

                            <div class="d-flex align-items-center text-muted small">
                                <div class="me-4"><i class="fas fa-truck me-2"></i> Free Shipping</div>
                                <div class="me-4"><i class="fas fa-undo me-2"></i> 30-Day Returns</div>
                                <div><i class="fas fa-shield-alt me-2"></i> Secure Checkout</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="row mb-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="fw-bold mb-4">Customer Reviews ({{ $reviews->total() }})</h3>
                        
                        @forelse($reviews as $review)
                        <div class="mb-4 pb-4 border-bottom last-border-0">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-bold m-0">{{ $review->title }}</h6>
                                <div class="text-warning small">
                                    {{ $review->stars }}
                                </div>
                            </div>
                            <p class="text-muted mb-2">{{ $review->comment }}</p>
                            <small class="text-muted opacity-50">
                                By {{ $review->customer_name }} on {{ $review->created_at->format('M d, Y') }}
                            </small>
                        </div>
                        @empty
                        <p class="text-muted opacity-75">No reviews yet. Be the first to write one!</p>
                        @endforelse
                        
                        @if($reviews->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $reviews->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Write a Review</h5>
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            @guest
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">YOUR NAME</label>
                                <input type="text" name="customer_name" class="form-control bg-light border-0" 
                                       placeholder="John Doe" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">YOUR EMAIL</label>
                                <input type="email" name="customer_email" class="form-control bg-light border-0" 
                                       placeholder="johndoe@example.com" value="{{ old('customer_email') }}" required>
                                @error('customer_email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            @endguest
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">RATING</label>
                                <select name="rating" class="form-select bg-light border-0" required>
                                    <option value="5">★★★★★ (5/5)</option>
                                    <option value="4">★★★★☆ (4/5)</option>
                                    <option value="3">★★★☆☆ (3/5)</option>
                                    <option value="2">★★☆☆☆ (2/5)</option>
                                    <option value="1">★☆☆☆☆ (1/5)</option>
                                </select>
                                @error('rating')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">TITLE</label>
                                <input type="text" name="title" class="form-control bg-light border-0" 
                                       placeholder="Summary of your experience" value="{{ old('title') }}" required>
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">COMMENT</label>
                                <textarea name="comment" class="form-control bg-light border-0" rows="4" 
                                          placeholder="How was the product?" required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">Submit Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <h3 class="fw-bold mb-4">Related Products</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 product-card overflow-hidden">
                    <a href="{{ route('shop.show', $related->id) }}" class="position-relative d-block bg-white p-3 text-center">
                        @if($related->image)
                        <img src="{{ Storage::url($related->image) }}" class="img-fluid" alt="{{ $related->name }}" style="height: 150px; object-fit: contain;">
                        @else
                        <i class="fas fa-image fa-3x text-muted opacity-25 my-4"></i>
                        @endif
                    </a>
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-1">
                            <a href="{{ route('shop.show', $related->id) }}" class="text-decoration-none text-dark">{{ $related->name }}</a>
                        </h6>
                        <p class="text-primary fw-bold mb-0">{{ $related->formatted_price }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
    function zoom(e) {
        var zoomer = e.currentTarget;
        var offsetX = e.offsetX ? e.offsetX : e.touches[0].pageX;
        var offsetY = e.offsetY ? e.offsetY : e.touches[0].pageY;
        var x = offsetX/zoomer.offsetWidth * 100;
        var y = offsetY/zoomer.offsetHeight * 100;
        zoomer.querySelector('img').style.transformOrigin = x + '% ' + y + '%';
    }

    function changeImage(src, thumb) {
        // Change Main Image
        const mainImg = document.getElementById('mainImage');
        mainImg.src = src;
        
        // Update Active Class
        document.querySelectorAll('.gallery-thumb').forEach(el => el.classList.remove('active-thumb', 'border-primary'));
        thumb.classList.add('active-thumb', 'border-primary');
        
        // Anime.js effect if desired, or simple fade
        mainImg.style.opacity = 0;
        setTimeout(() => {
            mainImg.style.opacity = 1;
        }, 50);
    }
</script>

<style>
    .cursor-pointer { cursor: pointer; }
    .gallery-thumb {
        transition: all 0.2s ease;
        opacity: 0.6;
    }
    .gallery-thumb:hover, .gallery-thumb.active-thumb {
        opacity: 1;
        border-color: var(--bs-primary) !important;
    }
    #mainImage {
        transition: opacity 0.2s ease, transform 0.1s ease-out;
    }
</style>
@endsection
