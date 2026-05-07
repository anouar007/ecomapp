@extends('layouts.frontend')

@section('content')

{{-- DANGILA HERO (SECTION 1 RECREATED) --}}
<section class="hero-dangila min-vh-100 overflow-hidden d-flex align-items-center position-relative">
    {{-- Massive Backdrop Text --}}
    <div class="position-absolute top-50 start-50 translate-middle w-100 text-center" style="z-index: 1; pointer-events: none;">
        <span class="dangila-heading text-black opacity-05" style="font-size: 25rem; letter-spacing: -0.05em; line-height: 1;">dangila</span>
    </div>

    <div class="container-dangila w-100 position-relative" style="z-index: 10;">
        <div class="row align-items-center">
            <div class="col-lg-5" data-aos="fade-right" data-aos-duration="1200">
                <div class="mb-5">
                    <h1 class="dangila-heading text-black mb-3" style="font-size: 6rem; line-height: 0.9; letter-spacing: -0.05em;">dangila</h1>
                    <h2 class="fs-4 text-espresso fw-bold mb-4" style="letter-spacing: 0.05em;">Natural Inner Beauty</h2>
                    <p class="text-espresso opacity-60 mb-5 fs-6 lh-lg" style="max-width: 450px;">
                        Provide deluxe hydration for those with dry or age-related skin concerns. They add intense moisture to dehydrated or mature skin, alleviating uneven, sun-damaged textures to promote natural radiance.
                    </p>
                    <a href="{{ route('shop.index') }}" class="btn-split-dangila shadow-lg">
                        <span class="btn-main">Buy Now</span>
                        <span class="btn-price">$49.99</span>
                    </a>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left" data-aos-duration="1500">
                <div class="ps-lg-5 position-relative">
                    <img src="{{ asset('dangila_v2_hero_bottles_layered_1777663150872.png') }}" alt="Dangila Argan Hero" class="img-fluid rounded-5 shadow-2xl transition-all hover-scale-105" style="z-index: 10;">
                    
                    {{-- Vertical Decorative Text --}}
                    <div class="vertical-title-bg d-none d-xxl-block" style="right: -25%;">ARGAN OIL</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TRUST BAR (SECTION 2) --}}
<section class="py-5" style="background: var(--color-surface); border-top: 1px solid rgba(0,0,0,0.05); border-bottom: 1px solid rgba(0,0,0,0.05);">
    <div class="container-dangila">
        <div class="row justify-content-center text-center gy-5">
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">25,356</div>
                <div class="stat-label">{{ __('Happy Customers') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">6,050</div>
                <div class="stat-label">{{ __('Followers') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">851</div>
                <div class="stat-label">{{ __('Shops') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">95%</div>
                <div class="stat-label">{{ __('Happy Customers') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- WHY DANGILA (SECTION 2.1) --}}
<section class="section-padding" style="background: var(--color-bg);">
    <div class="container-dangila text-center mb-5">
        <h2 class="dangila-heading display-3 text-uppercase opacity-20 mb-4" style="letter-spacing: 0.1em;">Why Dangila?</h2>
        <p class="text-espresso opacity-40 mx-auto" style="max-width: 600px;">
            Yourself required no at thoughts delicate landlord it be. Branched dashwood do is whatever it. Farther be chapter at visited married in it pressed.
        </p>
    </div>
    <div class="container-dangila">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fa-solid fa-leaf fs-3"></i>
                    </div>
                    <h4 class="dangila-heading fs-4 mb-3">Natural</h4>
                    <p class="text-espresso opacity-40 x-small">Yourself required no at thoughts delicate landlord it be.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fa-solid fa-syringe-slash fs-3"></i>
                    </div>
                    <h4 class="dangila-heading fs-4 mb-3">No Side effect</h4>
                    <p class="text-espresso opacity-40 x-small">Yourself required no at thoughts delicate landlord it be.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fa-solid fa-certificate fs-3"></i>
                    </div>
                    <h4 class="dangila-heading fs-4 mb-3">100% Organic</h4>
                    <p class="text-espresso opacity-40 x-small">Yourself required no at thoughts delicate landlord it be.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ABOUT US (SECTION 3) --}}
<section class="section-padding overflow-hidden" id="about">
    <div class="container-dangila">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="dangila-heading display-1 mb-5" style="letter-spacing: -0.05em; opacity: 0.2; font-weight: 300;">ABOUT US</h2>
                <p class="text-espresso opacity-60 mb-5 fs-6 lh-lg" style="max-width: 500px;">
                    And produce say the ten moments parties. Simple innate summer fat appear basket his desire joy. Outward clothes promise at gravity do excited. Sufficient particular impossible by reasonable oh expression is. Yet preference connection unpleasant yet melancholy but end appearance. And excellence partiality estimating terminated day everything.
                </p>
                <div class="d-flex flex-wrap gap-4 mt-5">
                    <a href="{{ route('shop.index') }}" class="btn-dangila">{{ __('Buy Now') }}</a>
                    <a href="#" class="btn-dangila-secondary">{{ __('View Details') }}</a>
                </div>
            </div>
            <div class="col-lg-6 position-relative" data-aos="fade-left">
                <div class="ps-lg-5 position-relative">
                    <div class="dots-pattern"></div>
                    <img src="{{ asset('dangila_about_us_product_1777660600575.png') }}" alt="About Dangila" class="img-fluid position-relative" style="z-index: 10;">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- OUR PRODUCTS (SECTION 4) --}}
<section class="section-padding" style="background: var(--color-surface);">
    <div class="container-dangila text-center mb-5">
        <h2 class="dangila-heading display-3 text-uppercase opacity-20 mb-5" style="letter-spacing: 0.1em;">Our Products</h2>
    </div>
    
    <div class="container-dangila">
        <div class="row justify-content-center g-5">
            @forelse($allCategories as $index => $category)
                <div class="col-6 col-md-4 col-lg-2" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="category-circle-item">
                        <div class="category-circle">
                            @php
                                $imageUrl = $category->image ? asset('storage/' . $category->image) : asset('assets/img/brand/logo.png');
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $category->translated_name }}">
                        </div>
                        <span class="category-label">{{ $category->translated_name }}</span>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-espresso opacity-40">{{ __('No collections available yet.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- SIGNATURE PRODUCTS --}}
<section class="section-padding">
    <div class="container-dangila text-center mb-5">
        <h2 class="dangila-heading display-5 mb-4">{{ __('The Signature Selection.') }}</h2>
        <div class="d-flex justify-content-center gap-3">
             <button class="btn-dangila-outline" style="padding: 10px 24px; font-size: 0.85rem;">{{ __('Best Seller') }}</button>
             <button class="btn-dangila-outline" style="padding: 10px 24px; font-size: 0.85rem;">{{ __('New Arrival') }}</button>
        </div>
    </div>
    
    <div class="container-dangila">
        <div id="catalog-container">@include('frontend.partials.catalog-content')</div>
        
        <div class="text-center mt-5 pt-4">
            <a href="{{ route('shop.index') }}" class="btn-dangila">{{ __('Explore Full Shop') }}</a>
        </div>
    </div>
</section>

{{-- TESTIMONIALS (SECTION 5) --}}
<section class="section-padding" style="background: var(--color-bg);">
    <div class="container-dangila text-center mb-5">
        <h2 class="dangila-heading display-3 text-uppercase opacity-20 mb-5" style="letter-spacing: 0.1em;">Testimonials</h2>
    </div>
    
    <div class="container-dangila">
        <div class="row g-5">
            <div class="col-md-4" data-aos="fade-up">
                <div class="testimonial-card">
                    <div class="testimonial-portrait">
                        <img src="{{ asset('dangila_customer_portraits_1777660734523.png') }}" alt="Customer" style="object-position: left;">
                    </div>
                    <p class="testimonial-quote">"The argan oil has completely transformed my skin's texture. It feels more hydrated and glowing than ever before."</p>
                    <div class="testimonial-author">Sarah Johnson</div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="testimonial-portrait">
                        <img src="{{ asset('dangila_customer_portraits_1777660734523.png') }}" alt="Customer" style="object-position: center;">
                    </div>
                    <p class="testimonial-quote">"I love the purity of these products. You can really feel the difference when using 100% natural ingredients."</p>
                    <div class="testimonial-author">Elena Rodriguez</div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="testimonial-portrait">
                        <img src="{{ asset('dangila_customer_portraits_1777660734523.png') }}" alt="Customer" style="object-position: right;">
                    </div>
                    <p class="testimonial-quote">"The best customer service and the products are simply divine. Highly recommend the face oil for mature skin."</p>
                    <div class="testimonial-author">Mei Ling</div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hero Bottle Rotation
        gsap.to('#heroBottle', {
            scrollTrigger: {
                trigger: '#hero',
                start: 'top top',
                end: 'bottom top',
                scrub: 1
            },
            rotationY: 360,
            ease: 'none'
        });

        // Horizontal Scroll
        const horizontalWrapper = document.getElementById('horizontalWrapper');
        const sections = document.querySelectorAll('.horizontal-panel');
        
        if (horizontalWrapper && sections.length > 0) {
            gsap.to(horizontalWrapper, {
                x: () => -(sections.length - 1) * window.innerWidth,
                ease: 'none',
                scrollTrigger: {
                    trigger: '#story',
                    start: 'top top',
                    end: () => '+=' + (sections.length * window.innerHeight),
                    scrub: 1,
                    pin: true,
                    anticipatePin: 1
                }
            });
        }

        // Image Reveal
        document.querySelectorAll('.reveal-image').forEach(el => {
            gsap.to(el, {
                scrollTrigger: { trigger: el, start: 'top 80%', onEnter: () => el.classList.add('revealed') }
            });
        });
    });
</script>
<style>
    .hover-gold-bg:hover { background: var(--brand-accent) !important; color: #000 !important; }
    .x-small { font-size: 0.6rem; }
    @media (max-width: 991px) {
        .horizontal-panel { padding: 40px 20px; }
        .horizontal-wrapper { flex-direction: column; }
    }
</style>
@endpush
