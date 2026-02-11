@extends('layouts.frontend')

@section('content')
<!-- Hero Section - Pro Level -->
<section class="hero-elegant">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                <div class="hero-badge">
                    <i class="fas fa-star me-2"></i>Premium Quality Guaranteed
                </div>
                <h1 class="hero-title">
                    Welcome to<br>
                    <span style="color: var(--primary-accent);">{{ setting('app_name', 'Speed Store') }}</span>
                </h1>
                <p class="hero-subtitle">
                    {{ setting('app_description', 'Discover exceptional products curated for quality-conscious customers. Elevate your lifestyle with our premium selection.') }}
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('shop.index') }}" class="btn-elegant btn-elegant-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Collection
                    </a>
                    <a href="#categories" class="btn-elegant btn-elegant-outline">
                        <i class="fas fa-th-large me-2"></i>Browse Categories
                    </a>
                </div>
                
                <!-- Trust Indicators -->
                <div class="d-flex gap-4 mt-5 pt-2" style="opacity: 0.8;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <span style="font-size: 0.85rem;">Free Shipping</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-shield-alt" style="color: #10b981;"></i>
                        <span style="font-size: 0.85rem;">Secure Checkout</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-undo" style="color: #10b981;"></i>
                        <span style="font-size: 0.85rem;">Easy Returns</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <div class="position-relative">
                    @if(isset($heroSlides) && $heroSlides->count() > 0)
                        <img src="{{ $heroSlides[0]->image_url ?? Storage::url($heroSlides[0]->main_image) }}" 
                             alt="Featured" 
                             class="img-fluid rounded-4 shadow-lg"
                             style="max-height: 520px; object-fit: cover; width: 100%;">
                    @else
                        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=800" 
                             alt="Shopping" 
                             class="img-fluid rounded-4 shadow-lg"
                             style="max-height: 520px; object-fit: cover; width: 100%;">
                    @endif
                    
                    <!-- Floating Badge -->
                    <div class="position-absolute" style="bottom: 30px; left: -30px; background: #fff; padding: 20px 24px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-accent), #ff6b6b); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-percent text-white"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Limited Offer</div>
                                <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-dark);">Up to 40% Off</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section style="background: var(--bg-white); border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <div class="row py-4">
            <div class="col-6 col-md-3 text-center py-3">
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-dark);">10K+</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Happy Customers</div>
            </div>
            <div class="col-6 col-md-3 text-center py-3">
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-dark);">500+</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Products</div>
            </div>
            <div class="col-6 col-md-3 text-center py-3">
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-dark);">4.9</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Average Rating</div>
            </div>
            <div class="col-6 col-md-3 text-center py-3">
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-dark);">24/7</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Customer Support</div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section id="categories" class="section-elegant" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-heading" data-aos="fade-up">
            <h2>Shop by Category</h2>
            <p>Explore our curated collections</p>
            <div class="section-divider"></div>
        </div>
        
        <div class="row g-4">
            @foreach($allCategories->take(4) as $index => $category)
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="category-card-elegant">
                    <div class="category-image">
                        @if($category->image)
                            @if(Str::startsWith($category->image, ['http://', 'https://']))
                                <img src="{{ $category->image }}" alt="{{ $category->name }}">
                            @else
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}">
                            @endif
                        @else
                            <div style="background: linear-gradient(135deg, #f8f6f3 0%, #e8e8e8 100%); height: 100%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tag" style="font-size: 3rem; color: #ccc;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="category-content">
                        <h3>{{ $category->name }}</h3>
                        <span>{{ $category->products_count ?? $category->products()->count() }} products</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        
        @if($allCategories->count() > 4)
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('shop.index') }}" class="btn-elegant btn-elegant-dark">
                View All Categories <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured" class="section-elegant">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
            <div class="section-heading text-start mb-0">
                <h2 class="mb-2">Featured Products</h2>
                <p class="mb-0">Handpicked selections just for you</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-elegant btn-elegant-dark d-none d-md-inline-flex">
                View All <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $index => $product)
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 100 }}">
                <div class="product-card-elegant">
                    <div class="product-image">
                        <img src="{{ $product->thumbnail ?? asset('images/placeholder-product.jpg') }}" 
                             alt="{{ $product->name }}">
                        
                        <div class="product-badges">
                            @if($product->created_at->diffInDays(now()) < 7)
                                <span class="product-badge product-badge-new">New</span>
                            @endif
                            @if($product->isOnSale())
                                <span class="product-badge product-badge-sale">{{ $product->discount_percentage }}% Off</span>
                            @endif
                        </div>
                        
                        <div class="product-actions">
                            @if($product->isInStock())
                            <button class="product-action-btn" onclick="addToCart({{ $product->id }})" title="Add to Cart">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            @else
                            <button class="product-action-btn" disabled title="Out of Stock" style="opacity: 0.5; cursor: not-allowed;">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            @endif
                            <a href="{{ route('shop.show', $product->id) }}" class="product-action-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        @if($product->productCategory)
                            <div class="product-category">{{ $product->productCategory->name }}</div>
                        @endif
                        <h4 class="product-title">{{ Str::limit($product->name, 40) }}</h4>
                        <div class="product-rating">
                            <div class="stars">
                                @php $rating = $product->reviews()->avg('rating') ?? 0; @endphp
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star{{ $i < $rating ? '' : ' opacity-25' }}"></i>
                                @endfor
                            </div>
                            <span>({{ $product->reviews()->count() }})</span>
                        </div>
                        <div class="product-price">
                            @if($product->isOnSale())
                                <span class="price-current">{{ $product->formatted_sale_price }}</span>
                                <span class="price-original">{{ $product->formatted_price }}</span>
                            @else
                                <span class="price-current">{{ $product->formatted_price }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5 d-md-none">
            <a href="{{ route('shop.index') }}" class="btn-elegant btn-elegant-dark">
                View All Products <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Promo Banner -->
<section class="promo-banner-elegant" data-aos="fade-up">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 style="font-size: 2.5rem;">Special Offers Await</h2>
                <p class="mb-4" style="font-size: 1.15rem; opacity: 0.9;">Exclusive deals on premium products. Don't miss out!</p>
                <a href="{{ route('shop.index') }}" class="btn-elegant" style="background: #fff; color: var(--primary-accent); padding: 16px 40px;">
                    Shop Now <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
@if($testimonials->count() > 0)
<section class="section-elegant" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-heading" data-aos="fade-up">
            <h2>Customer Reviews</h2>
            <p>What our customers are saying</p>
            <div class="section-divider"></div>
        </div>
        
        <div class="row g-4">
            @foreach($testimonials->take(3) as $index => $testimonial)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="testimonial-card-elegant">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            {{ substr($testimonial->name, 0, 1) }}
                        </div>
                        <div class="testimonial-info">
                            <h5>{{ $testimonial->name }}</h5>
                            <div class="testimonial-stars">
                                @for($i = 0; $i < $testimonial->rating; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p class="testimonial-text">"{{ Str::limit($testimonial->content, 150) }}"</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Newsletter Section -->
<section class="newsletter-elegant text-center">
    <div class="container" data-aos="fade-up">
        <h2>Stay Updated</h2>
        <p>Subscribe for exclusive offers and new arrivals</p>
        
        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
            @csrf
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Subscribe</button>
        </form>
        
        <p class="mt-4 mb-0" style="font-size: 0.8rem; opacity: 0.5;">
            No spam, unsubscribe anytime.
        </p>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Cart functionality
function addToCart(productId) {
    fetch(`{{ url('/cart/add') }}/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;

        if (!response.ok) {
             const errorMsg = (data && data.message) || `Server Error: ${response.status}`;
             throw new Error(errorMsg);
        }

        // Update cart count badge
        const countEl = document.getElementById('header-cart-count');
        if(countEl && data.cartCount !== undefined) countEl.textContent = data.cartCount;
        
        // Refresh mini-cart content
        refreshMiniCart();
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Added to cart!',
            showConfirmButton: false,
            timer: 2500,
            background: '#1a1a2e',
            color: '#fff'
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: error.message || 'Error adding to cart',
            showConfirmButton: false,
            timer: 3000
        });
    });
}


</script>
@endpush
