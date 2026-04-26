@extends('layouts.frontend')
@section('meta_title', __('Coop Ait Oumdis — Natural Moroccan Products'))

@section('content')
{{-- HERO --}}
<section class="position-relative overflow-hidden" style="min-height:88vh;background:linear-gradient(135deg,#f0faf5 0%,#fff 60%);">
    <div class="position-absolute end-0 top-0 h-100" style="width:45%;background:linear-gradient(160deg,#e8f7ef,#f0faf5);clip-path:polygon(15% 0,100% 0,100% 100%,0 100%);"></div>
    <div class="position-absolute" style="top:10%;right:5%;width:300px;height:300px;background:radial-gradient(circle,rgba(59,184,120,.12),transparent 70%);border-radius:50%;"></div>
    <div class="container position-relative h-100 py-5">
        <div class="row align-items-center" style="min-height:80vh;">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="900">
                <div class="section-label mb-4">{{ __('hero.label') }}</div>
                <h1 class="fw-900 lh-1 mb-4" style="font-size:clamp(2.2rem,5vw,3.8rem);color:#1F2937;">
                    {{ __('hero.title1') }}<br>
                    <span style="color:#3BB878;">{{ __('hero.title2') }}</span><br>
                    {{ __('hero.title3') }}
                </h1>
                <p class="text-muted mb-5 lh-lg" style="font-size:1.05rem;max-width:480px;">
                    {{ __('hero.description') }}
                </p>
                <div class="d-flex flex-wrap gap-3 mb-5">
                    <a href="#catalog" class="btn-brand btn-brand-primary">
                        <i class="fas fa-shopping-bag"></i> {{ __('Shop Now') }}
                    </a>
                    <a href="#about" class="btn-brand btn-brand-outline">
                        {{ __('Our Story') }} <i class="fas fa-arrow-right small"></i>
                    </a>
                </div>
                <div class="d-flex gap-4">
                    <div><span class="fw-800 text-dark fs-4">500+</span><br><span class="x-small text-muted">{{ __('hero.stats1') }}</span></div>
                    <div style="width:1px;background:#e5e7eb;"></div>
                    <div><span class="fw-800 text-dark fs-4">100%</span><br><span class="x-small text-muted">{{ __('hero.stats2') }}</span></div>
                    <div style="width:1px;background:#e5e7eb;"></div>
                    <div><span class="fw-800 text-dark fs-4">50+</span><br><span class="x-small text-muted">{{ __('hero.stats3') }}</span></div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center" data-aos="fade-left" data-aos-duration="900">
                <div class="position-relative" style="width:420px;height:480px;">
                    {{-- Blob background --}}
                    <div class="position-absolute" style="inset:0;background:linear-gradient(135deg,#3BB878,#2f9461);border-radius:40% 60% 60% 40%/40% 40% 60% 60%;opacity:.07;"></div>
                    <div class="position-absolute" style="inset:30px;background:linear-gradient(135deg,#e8f7ef,#f0faf5);border-radius:40% 60% 60% 40%/40% 40% 60% 60%;opacity:.6;"></div>

                    {{-- Logo centered --}}
                    <div class="position-absolute d-flex align-items-center justify-content-center" style="inset:0;">
                        @if(setting('app_logo'))
                            <img src="{{ Storage::url(setting('app_logo')) }}" alt="Ait Oumdis Logo"
                                 style="max-width:260px;max-height:260px;object-fit:contain;filter:drop-shadow(0 20px 40px rgba(59,184,120,.25));">
                        @else
                            <div style="text-align:center;">
                                <div style="width:160px;height:160px;background:linear-gradient(135deg,#3BB878,#2f9461);border-radius:30px;display:flex;align-items:center;justify-content:center;margin:0 auto;box-shadow:0 20px 50px rgba(59,184,120,.3);">
                                    <i class="fas fa-leaf text-white" style="font-size:4rem;"></i>
                                </div>
                                <div class="mt-4 fw-900 text-dark" style="font-size:1.8rem;">{{ __('Ait') }} <span style="color:#3BB878;">{{ __('Oumdis') }}</span></div>
                                <div class="x-small text-muted mt-1">{{ __('Natural Cooperative') }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- Floating badges --}}
                    <div class="float-anim position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center gap-2" style="top:8%;left:-8%;animation-delay:0s;">
                        <div style="width:34px;height:34px;background:#e8f7ef;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-leaf text-green small"></i></div>
                        <div><div class="fw-700 x-small text-dark">{{ __('hero.badge1_title') }}</div><div style="font-size:.6rem;color:#9CA3AF;">{{ __('hero.badge1_subtitle') }}</div></div>
                    </div>
                    <div class="float-anim position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center gap-2" style="bottom:18%;right:-8%;animation-delay:1.5s;">
                        <div style="width:34px;height:34px;background:#fff7ed;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-star" style="color:#f59e0b;"></i></div>
                        <div><div class="fw-700 x-small text-dark">4.9 / 5.0</div><div style="font-size:.6rem;color:#9CA3AF;">{{ __('hero.badge2_subtitle') }}</div></div>
                    </div>
                    <div class="float-anim position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center gap-2" style="bottom:2%;left:8%;animation-delay:0.8s;">
                        <i class="fas fa-truck text-green small"></i>
                        <div class="fw-700 x-small text-dark">{{ __('Delivery to all Morocco') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TRUST STRIP --}}
<section class="py-4 border-top border-bottom" style="background:#f9fafb;">
    <div class="container">
        <div class="row g-3 align-items-center justify-content-around text-center">
            @foreach([
                ['fas fa-leaf','#3BB878',__('100% Natural')],
                ['fas fa-flask','#6366f1',__('Lab Certified')],
                ['fas fa-truck','#f59e0b',__('Morocco-Wide Delivery')],
                ['fas fa-money-bill-wave','#3BB878',__('Cash on Delivery')],
            ] as $t)
            <div class="col-6 col-md-auto">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="fas {{ $t[0] }}" style="color:{{ $t[1] }};font-size:1.1rem;"></i>
                    <span class="small fw-700 text-dark">{{ $t[2] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
<section class="py-5 overflow-hidden" id="categories" style="background:#f9fafb;">
    <div class="container py-3">
        <div class="row align-items-end mb-5" data-aos="fade-up">
            <div class="col-lg-7">
                <div class="section-label">{{ __('Collections') }}</div>
                <h2 class="section-title mb-2">{{ __('Explore Our Natural') }}<br><span style="color:#3BB878;">{{ __('Product Categories') }}</span></h2>
                <p class="text-muted mb-0">{{ __('Discover authentic Moroccan products from the Atlas Mountains.') }}</p>
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('shop.index') }}" class="btn-brand btn-brand-outline py-2 px-4">
                    {{ __('View All Products') }} <i class="fas fa-arrow-right small ms-1"></i>
                </a>
            </div>
        </div>

        @php $cats = $allCategories; @endphp
        @if($cats->count() > 0)
        <div class="cat-masonry-grid">
            {{-- First category: large featured --}}
            @if($cats->count() >= 1)
            @php $c = $cats[0]; @endphp
            <div class="cat-masonry-featured" data-aos="fade-right" data-aos-delay="0">
                <a href="#" class="cat-masonry-card text-decoration-none" data-slug="{{ $c->slug }}">
                    <div class="cat-masonry-inner position-relative overflow-hidden rounded-4">
                        <img src="{{ $c->image ? (Str::startsWith($c->image,'http') ? $c->image : Storage::url($c->image)) : asset('images/placeholder-cat.jpg') }}"
                             class="cat-masonry-img w-100 h-100 object-fit-cover" alt="{{ $c->translated_name }}">
                        <div class="cat-masonry-overlay position-absolute" style="inset:0;background:linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 60%);"></div>
                        <div class="cat-masonry-content position-absolute w-100" style="bottom:0;left:0;padding:16px;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div style="width:4px;height:4px;background:#3BB878;border-radius:50%;"></div>
                                <span class="x-small text-white opacity-75 text-uppercase fw-700" style="letter-spacing:1px;font-size:0.6rem;">{{ __('Collection') }}</span>
                            </div>
                            <h3 class="text-white fw-800 mb-2" style="font-size:1.1rem; line-height: 1.2;">{{ $c->translated_name }}</h3>
                            <div class="cat-masonry-btn d-inline-flex align-items-center gap-2 rounded-pill px-3 py-1" style="background:rgba(255,255,255,.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.2);width:fit-content;">
                                <span class="x-small text-white fw-700" style="font-size:0.65rem;">{{ __('Explore') }}</span>
                                <i class="fas fa-arrow-right x-small text-white cat-arrow" style="font-size:0.55rem;"></i>
                            </div>
                        </div>
                        {{-- Number badge --}}
                        <div class="position-absolute top-0 end-0 m-3" style="font-size:4rem;font-weight:900;color:rgba(255,255,255,.06);line-height:1;font-family:'Playfair Display',serif;">01</div>
                    </div>
                </a>
            </div>
            @endif

            {{-- Right side: smaller grid --}}
            <div class="cat-masonry-right">
                @foreach($cats->skip(1)->take(3) as $i => $c)
                <div data-aos="fade-left" data-aos-delay="{{ ($i + 1) * 100 }}">
                    <a href="#" class="cat-masonry-card text-decoration-none" data-slug="{{ $c->slug }}">
                        <div class="cat-masonry-inner position-relative overflow-hidden rounded-4">
                            <img src="{{ $c->image ? (Str::startsWith($c->image,'http') ? $c->image : Storage::url($c->image)) : asset('images/placeholder-cat.jpg') }}"
                                 class="cat-masonry-img w-100 h-100 object-fit-cover" alt="{{ $c->translated_name }}">
                            <div class="cat-masonry-overlay position-absolute" style="inset:0;background:linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 60%);"></div>
                            <div class="cat-masonry-content position-absolute w-100" style="bottom:0;left:0;padding:14px;">
                                <h6 class="text-white fw-800 mb-1" style="font-size:0.85rem; line-height: 1.2;">{{ $c->translated_name }}</h6>
                                <div class="d-flex align-items-center gap-1 opacity-75">
                                    <span class="x-small text-white" style="font-size:0.6rem;">{{ __('Explore') }}</span>
                                    <i class="fas fa-arrow-right" style="font-size:0.5rem;color:#3BB878;"></i>
                                </div>
                            </div>
                            {{-- Number badge --}}
                            <div class="position-absolute top-0 end-0 m-2" style="font-size:2.5rem;font-weight:900;color:rgba(255,255,255,.07);line-height:1;font-family:'Playfair Display',serif;">0{{ $i + 2 }}</div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            {{-- Extra categories below if more than 4 --}}
            @if($cats->count() > 4)
            <div class="cat-masonry-extra">
                @foreach($cats->skip(4) as $i => $c)
                <div data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                    <a href="#" class="cat-masonry-card text-decoration-none" data-slug="{{ $c->slug }}">
                        <div class="cat-masonry-inner position-relative overflow-hidden rounded-4">
                            <img src="{{ $c->image ? (Str::startsWith($c->image,'http') ? $c->image : Storage::url($c->image)) : asset('images/placeholder-cat.jpg') }}"
                                 class="cat-masonry-img w-100 h-100 object-fit-cover" alt="{{ $c->translated_name }}">
                            <div class="cat-masonry-overlay position-absolute" style="inset:0;background:linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 60%);"></div>
                            <div class="cat-masonry-content position-absolute w-100" style="bottom:0;left:0;padding:14px;">
                                <h6 class="text-white fw-800 mb-1" style="font-size:0.85rem; line-height: 1.2;">{{ $c->translated_name }}</h6>
                                <div class="d-flex align-items-center gap-1 opacity-75">
                                    <span class="x-small text-white" style="font-size:0.6rem;">{{ __('Explore') }} <span class="fa-arrow-right">→</span></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif
    </div>
</section>

@push('styles')
<style>
/* Masonry Layout */
.cat-masonry-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    gap: 16px;
}
.cat-masonry-featured { grid-column: 1; grid-row: 1; }
.cat-masonry-right {
    grid-column: 2; grid-row: 1;
    display: grid; grid-template-rows: repeat(3,1fr); gap: 16px;
}
.cat-masonry-extra {
    grid-column: 1 / -1;
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 16px;
}

/* Card sizes */
.cat-masonry-featured .cat-masonry-inner { height: 480px; }
.cat-masonry-right .cat-masonry-inner { height: 148px; }
.cat-masonry-extra .cat-masonry-inner { height: 160px; }

/* Card base */
.cat-masonry-inner {
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0,0,0,.1);
    transition: transform .45s cubic-bezier(.34,1.56,.64,1), box-shadow .35s ease;
}
.cat-masonry-card:hover .cat-masonry-inner {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px rgba(59,184,120,.18);
}
.cat-masonry-img { transition: transform .8s cubic-bezier(.4,0,.2,1); }
.cat-masonry-card:hover .cat-masonry-img { transform: scale(1.08); }
.cat-masonry-card:hover .cat-arrow { transform: translateX(5px); }
.cat-masonry-card:hover .cat-masonry-btn { background: rgba(59,184,120,.35) !important; border-color: rgba(59,184,120,.4) !important; }

/* Mobile */
@media(max-width:767px) {
    .cat-masonry-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
    .cat-masonry-featured { grid-column: 1 / -1; }
    .cat-masonry-featured .cat-masonry-inner { height: 180px !important; }
    .cat-masonry-right { display: contents; }
    .cat-masonry-right .cat-masonry-inner { height: 140px !important; }
    .cat-masonry-extra { grid-template-columns: 1fr 1fr; gap: 10px; }
    
    .section-title { font-size: 1.4rem !important; }
    .section-label { font-size: 0.7rem !important; padding: 4px 12px !important; }
    
    @media(max-width: 480px) {
        .cat-masonry-inner { height: 150px !important; }
        .cat-masonry-featured .cat-masonry-inner { height: 160px !important; }
    }
}

/* FAQ & Alignment */
[dir="rtl"] .accordion-button { text-align: right !important; }
[dir="rtl"] .accordion-button::after { margin-right: auto; margin-left: 0; }
[dir="rtl"] .accordion-button .me-3 { margin-right: 0 !important; margin-left: 1rem !important; }

.accordion-button:not(.collapsed) {
    background-color: #f0faf5 !important;
    color: #3BB878 !important;
}
.accordion-button:focus { box-shadow: none !important; }
.bg-white.rounded-4.shadow-sm { transition: all .3s ease; border: 1px solid #f1f5f9 !important; }
.bg-white.rounded-4.shadow-sm:hover { transform: translateY(-3px); shadow: 0 10px 25px rgba(0,0,0,.05); }
</style>
@endpush

{{-- FEATURED PRODUCTS --}}
<section class="py-5 position-relative" id="catalog" style="background:#f9fafb;">
    <div id="catalog-loader" class="position-absolute inset-0 d-none align-items-center justify-content-center" style="inset:0;background:rgba(255,255,255,.8);backdrop-filter:blur(4px);z-index:10;">
        <div class="spinner-border text-green"></div>
    </div>
    <div class="container py-3">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-5" data-aos="fade-up">
            <div>
                <div class="section-label">{{ __('Fresh From Nature') }}</div>
                <h2 class="section-title mb-0">{{ __('Our Products') }}</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-brand btn-brand-outline py-2 px-4">{{ __('View All') }} <i class="fas fa-arrow-right small"></i></a>
        </div>
        <div id="catalog-container">@include('frontend.partials.catalog-content')</div>
        <div class="text-center mt-5 {{ $hasMore ? '' : 'd-none' }}" id="load-more-container">
            <button id="load-more-btn" class="btn-brand btn-brand-primary px-5">{{ __('Load More') }} <i class="fas fa-plus small"></i></button>
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="py-5 bg-white" id="how">
    <div class="container py-3">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label mx-auto">{{ __('Simple Process') }}</div>
            <h2 class="section-title">{{ __('How It Works') }}</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            @foreach([
                ['1','fas fa-search','#e8f7ef','#3BB878',__('Browse & Select'),__('Explore our curated selection of natural Moroccan products and find exactly what you need.')],
                ['2','fas fa-cart-plus','#fff7ed','#f59e0b',__('Add to Cart'),__('Add your favorites to the cart with one click. No account required to shop with us.')],
                ['3','fas fa-phone','#ede9fe','#8b5cf6',__('We Confirm'),__('Our team will call you to confirm your order and arrange the best delivery option.')],
                ['4','fas fa-home','#fef2f2','#ef4444',__('Doorstep Delivery'),__('Your order arrives safely packed. Pay when delivered — no risk, full trust.')],
            ] as $i => $s)
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="bg-white rounded-4 p-4 h-100 border border-light shadow-sm hover-translate-y transition-all position-relative">
                    <div class="position-absolute top-0 end-0 m-3 fw-900 opacity-10" style="font-size:3rem;color:#3BB878;">{{ $s[0] }}</div>
                    <div class="rounded-3 mb-4 d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:{{ $s[2] }};">
                        <i class="fas {{ $s[1] }}" style="color:{{ $s[3] }};font-size:1.2rem;"></i>
                    </div>
                    <h6 class="fw-800 mb-2 text-dark">{{ $s[4] }}</h6>
                    <p class="small text-muted mb-0 lh-lg">{{ $s[5] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ABOUT SECTION --}}
<section class="py-5 overflow-hidden" id="about" style="background:linear-gradient(135deg,#f0faf5 0%,#fff 100%);">
    <div class="container py-3">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="position-relative">
                    <div class="rounded-4 overflow-hidden shadow-lg" style="aspect-ratio:4/3;background:#e8f7ef;">
                        <div class="d-flex align-items-center justify-content-center h-100 text-green" style="font-size:5rem;">
                            <i class="fas fa-mountain"></i>
                        </div>
                    </div>
                    <div class="position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center gap-3" style="bottom:-20px;right:-15px;min-width:200px;">
                        <div style="width:48px;height:48px;background:#e8f7ef;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-users text-green"></i>
                        </div>
                        <div>
                            <div class="fw-800 text-dark">30+ {{ __('Families') }}</div>
                            <div class="x-small text-muted">{{ __('Supporting our cooperative') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="section-label">{{ __('Our Story') }}</div>
                <h2 class="section-title">{{ __('A Cooperative Born from Tradition') }}</h2>
                <p class="text-muted lh-lg mb-4">{{ __('Nestled in the heart of the Atlas Mountains, Ait Oumdis cooperative was founded to empower local families and bring Morocco\'s finest natural products to the world. Every jar of honey, every bottle of argan oil carries the story of our land and our people.') }}</p>
                <p class="text-muted lh-lg mb-4">{{ __('We believe in transparency — from our beehives and argan groves to your table. No additives, no shortcuts. Just the pure gift of nature, harvested with care and respect for generations of tradition.') }}</p>
                <div class="row g-3 mb-4">
                    @foreach([['fas fa-seedling',__('Sustainable Farming')],['fas fa-certificate',__('Certified Quality')],['fas fa-heart',__('Community First')]] as $f)
                    <div class="col-4">
                        <div class="text-center p-3 rounded-3 bg-green-light">
                            <i class="fas {{ $f[0] }} text-green mb-2"></i>
                            <div class="x-small fw-700 text-dark">{{ $f[1] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA BANNER --}}
<section class="py-0">
    <div class="container-fluid px-0">
        <div class="position-relative overflow-hidden py-5" style="background:linear-gradient(135deg,#1a5c38,#3BB878);">
            <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'%3E%3Ccircle cx='30' cy='30' r='20' fill='none' stroke='white' stroke-width='.5'/%3E%3C/svg%3E&quot;);background-size:60px;"></div>
            <div class="container position-relative py-3">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-7 text-white text-center text-lg-start" data-aos="fade-right">
                        <div class="badge bg-white text-green fw-700 mb-3 px-3 py-2 rounded-pill"><i class="fas fa-gift me-1"></i> {{ __('Limited Time Offer') }}</div>
                        <h2 class="fw-900 mb-3" style="font-size:clamp(1.6rem,3vw,2.5rem);">{{ __('First Order Special — 15% OFF') }}</h2>
                        <p class="opacity-85 mb-0 lh-lg">{{ __('Use our exclusive welcome offer on your first purchase. Discover why thousands of Moroccan families trust Ait Oumdis for their natural product needs.') }}</p>
                    </div>
                    <div class="col-lg-5 text-center" data-aos="fade-left">
                        <a href="#catalog" class="btn-brand btn-brand-white px-5 py-3 shadow-lg fs-6">
                            {{ __('Shop & Save Now') }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TESTIMONIALS --}}
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label mx-auto">{{ __('Community Love') }}</div>
            <h2 class="section-title">{{ __('What Customers Say') }}</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            @foreach([
                [__('Sarah A.'),__('Rabat'),'⭐⭐⭐⭐⭐',__('The honey I ordered exceeded all expectations. Incredibly pure taste. I can feel the quality with every spoonful. This is the real thing — nothing like supermarket honey!')],
                [__('Maryam B.'),__('Casablanca'),'⭐⭐⭐⭐⭐',__('Fast delivery, beautiful packaging, and the argan oil works wonders on my skin. The cooperative is extremely responsive. Ordering again soon!')],
                [__('Khadija M.'),__('Tangier'),'⭐⭐⭐⭐⭐',__('The saffron is absolutely authentic — rich color and intense aroma. My whole family now orders from Ait Oumdis. Simply the best natural products in Morocco.')],
            ] as $i => $t)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="rounded-4 p-4 h-100 border-0 shadow-sm bg-white position-relative overflow-hidden" style="border-top: 3px solid #3BB878 !important;">
                    <div class="position-absolute top-0 end-0 opacity-5" style="font-size:5rem;line-height:1;font-family:Georgia,serif;">"</div>
                    <div class="mb-3">{{ $t[2] }}</div>
                    <p class="text-muted small lh-lg mb-4">"{{ $t[3] }}"</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-green d-flex align-items-center justify-content-center fw-800 text-white" style="width:44px;height:44px;flex-shrink:0;">{{ mb_substr($t[0],0,1) }}</div>
                        <div>
                            <div class="fw-700 text-dark small">{{ $t[0] }}</div>
                            <div class="x-small text-muted"><i class="fas fa-map-marker-alt me-1 text-green"></i>{{ $t[1] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-5" style="background:#f9fafb;">
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5" data-aos="fade-up">
                    <div class="section-label mx-auto">{{ __('Support') }}</div>
                    <h2 class="section-title">{{ __('Frequently Asked Questions') }}</h2>
                    <div class="section-divider mx-auto"></div>
                </div>
                <div class="accordion accordion-flush" id="faqAccordion">
                    @foreach([
                        [__('Are your products 100% natural?'),__('Yes. All our products are sourced directly from our cooperative farms in the Atlas Mountains. We never add artificial preservatives, colors, or additives. What you receive is exactly what nature provides.')],
                        [__('How long does delivery take?'),__('We deliver to all Moroccan cities within 2–4 business days. Casablanca, Rabat and surrounding areas typically receive orders within 24–48 hours.')],
                        [__('Can I pay on delivery?'),__('Absolutely! We offer Cash on Delivery (COD) for all orders across Morocco. You pay only when you physically receive and are satisfied with your products.')],
                    ] as $i => $faq)
                    <div class="mb-3" data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">
                        <div class="bg-white rounded-4 border-0 shadow-sm overflow-hidden">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} fw-700 text-dark bg-white border-0 px-4 py-3 w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}" style="box-shadow:none;">
                                <span class="me-3 text-green fw-900">{{ str_pad($i+1,'2','0',STR_PAD_LEFT) }}.</span> {{ $faq[0] }}
                            </button>
                            <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                <div class="px-4 pb-4 text-muted small lh-lg">{{ $faq[1] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CONTACT CTA --}}
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-10">
                <div class="rounded-5 p-5 text-center border border-light shadow-sm" data-aos="zoom-in">
                    <div class="mb-3 text-green" style="font-size:2.5rem;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <h3 class="fw-800 text-dark mb-3">{{ __('Still have questions?') }}</h3>
                    <p class="text-muted mb-4 mx-auto" style="max-width:450px;">{{ __('Our team is here to help. Reach out via WhatsApp or phone and we\'ll get back to you within minutes.') }}</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="https://wa.me/{{ str_replace(['+','  ',' '],'',(setting('app_phone','212600000000'))) }}" target="_blank" class="btn-brand btn-brand-primary">
                            <i class="fab fa-whatsapp"></i> {{ __('Chat on WhatsApp') }}
                        </a>
                        <a href="tel:{{ setting('app_phone','+212600000000') }}" class="btn-brand btn-brand-outline">
                            <i class="fas fa-phone"></i> {{ __('Call Us') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
let currentCategory = '', currentPage = 1;

document.querySelectorAll('.category-story-pill').forEach(pill => {
    pill.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.category-story-pill .rounded-4').forEach(el => {
            el.style.borderColor = '#f1f5f9';
        });
        pill.querySelector('.rounded-4').style.borderColor = '#3BB878';
        currentCategory = pill.dataset.slug;
        currentPage = 1;
        fetchProducts(true);
    });
});

document.getElementById('load-more-btn')?.addEventListener('click', () => { currentPage++; fetchProducts(false); });

function fetchProducts(isNew) {
    const loader = document.getElementById('catalog-loader');
    const btn = document.getElementById('load-more-btn');
    if(isNew) { loader.classList.remove('d-none'); loader.classList.add('d-flex'); }
    else { btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; btn.disabled = true; }

    const params = new URLSearchParams({ page: currentPage, category: currentCategory });
    fetch(`${location.origin}${location.pathname}?${params}`, { headers: {'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json()).then(data => {
        const container = document.getElementById('catalog-container');
        if(isNew) { container.innerHTML = data.html; }
        else {
            const tmp = document.createElement('div'); tmp.innerHTML = data.html;
            const grid = document.getElementById('products-grid');
            if(grid) tmp.querySelectorAll('#products-grid > div').forEach(el => grid.appendChild(el));
        }
        const lmc = document.getElementById('load-more-container');
        if(data.hasMore) { lmc.classList.remove('d-none'); btn.innerHTML = '{{ __("Load More") }} <i class="fas fa-plus small"></i>'; btn.disabled = false; }
        else lmc.classList.add('d-none');
        if(window.AOS) AOS.refreshHard();
    }).finally(() => { loader.classList.add('d-none'); loader.classList.remove('d-flex'); });
}
</script>
<style>
.hover-translate-y:hover { transform: translateY(-6px); }
.transition-all { transition: all .35s ease; }
.fw-800 { font-weight:800; }
.fw-900 { font-weight:900; }
.category-story-pill:hover .rounded-4 { transform:scale(1.04); border-color:#3BB878 !important; }
.inset-0 { inset:0; }
</style>
@endpush
