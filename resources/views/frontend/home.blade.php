@extends('layouts.frontend')

@section('meta_title', setting('app_name') . ' — ' . setting('app_description', 'Équipements d\'impression grand format au Maroc'))
@section('meta_description', 'Découvrez notre gamme d\'imprimantes éco-solvant, traceurs de découpe, encres et consommables. Livraison partout au Maroc, installation et formation incluses. Devis gratuit !')
@section('meta_keywords', setting('app_name', 'boutique') . ', imprimantes grand format Maroc, traceur de découpe, éco-solvant, encres imprimante, consommables impression, équipement atelier impression Maroc')

@section('json_ld')
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "{{ setting('app_name', 'Speed Platform') }}",
    "url": "{{ url('/') }}",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "{{ route('shop.index') }}?q={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  },
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "{{ setting('app_name', 'Speed Platform') }}",
    "url": "{{ url('/') }}",
    "logo": "{{ setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/logo.png') }}",
    @if(setting('company_phone'))
    "telephone": "{{ setting('company_phone') }}",
    @endif
    @if(setting('company_email'))
    "email": "{{ setting('company_email') }}",
    @endif
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "{{ setting('company_phone', '') }}",
      "contactType": "customer service",
      "areaServed": "MA",
      "availableLanguage": ["French", "Arabic"]
    }
  },
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "Quelle est la différence entre éco-solvant et UV ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "L'éco-solvant utilise des encres à base de solvant doux, idéal pour les supports souples (bâche, vinyle). L'UV imprime directement sur des supports rigides (bois, verre, métal) et sèche instantanément par lampe ultraviolet."
        }
      },
      {
        "@type": "Question",
        "name": "Livrez-vous dans tout le Maroc ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui. Nous livrons partout au Maroc avec notre propre réseau logistique. Les grandes villes bénéficient d'une livraison J+1 et d'une installation sur site incluse."
        }
      },
      {
        "@type": "Question",
        "name": "La formation est-elle incluse à l'achat ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, chaque machine est accompagnée d'une formation opérateur gratuite (1 à 2 jours selon la complexité) réalisée sur votre lieu de production par nos techniciens certifiés."
        }
      },
      {
        "@type": "Question",
        "name": "Proposez-vous des facilités de paiement ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, nous proposons des solutions de financement personnalisées (paiement en plusieurs fois, leasing) selon votre situation. Contactez-nous pour un devis adapté à votre budget."
        }
      },
      {
        "@type": "Question",
        "name": "Quels types d'encres compatibles sont disponibles ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Nous stockons les encres d'origine pour toutes les marques (Roland, Epson, Mimaki...) ainsi que des encres compatibles certifiées offrant un excellent rapport qualité/prix sans altérer la tête d'impression."
        }
      }
    ]
  }
]
</script>
@endsection

@section('content')

{{-- =============================================
     HERO — Creative E-Commerce Slider
     ============================================= --}}
<section class="hero-slider-section">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            @if(isset($heroSlides) && $heroSlides->count() > 0)
                @foreach($heroSlides as $i => $slide)
                <div class="swiper-slide">
                    <div class="hero-slide" style="background-image: url('{{ $slide->image_url ?? ($slide->main_image ? Storage::url($slide->main_image) : 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1920') }}');">
                        <div class="hero-slide-overlay"></div>
                        <div class="container position-relative" style="z-index:3;">
                            <div class="row align-items-center hero-slide-row">
                                <div class="col-lg-7 col-xl-6">
                                    <div class="hero-slide-content">
                                        <span class="hero-badge">
                                            <i class="fas fa-bolt"></i>
                                            {{ $slide->subtitle ?? setting('app_name', 'SpeedPub') }}
                                        </span>
                                        <h2 class="hero-slide-title">{{ $slide->title ?? $slide->name ?? 'Discover Our Products' }}</h2>
                                        <p class="hero-slide-desc">{{ $slide->description ?? $slide->short_description ?? 'Explore our catalog of professional-grade products' }}</p>
                                        <div class="hero-slide-actions">
                                        </div>
                                        <div class="hero-trust-row">
                                            <div class="hero-trust-item"><i class="fas fa-check-circle"></i> {{ __('Free Shipping') }}</div>
                                            <div class="hero-trust-item"><i class="fas fa-check-circle"></i> {{ __('Warranty Included') }}</div>
                                            <div class="hero-trust-item"><i class="fas fa-check-circle"></i> {{ __('Expert Support') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                {{-- Default slides with stunning images --}}
                @php
                $defaultSlides = [
                    [
                        'title' => 'Professional Printing Equipment',
                        'desc' => 'Discover our complete range of large-format printers, cutting plotters, and professional supplies delivered across Morocco.',
                        'badge' => 'New Collection',
                        'icon' => 'fa-star',
                        'img' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1920',
                    ],
                    [
                        'title' => 'Fast Delivery Across Morocco',
                        'desc' => 'On-site installation, operator training included, and reactive technical support to keep your business running.',
                        'badge' => 'Free Delivery',
                        'icon' => 'fa-truck',
                        'img' => 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&q=80&w=1920',
                    ],
                    [
                        'title' => 'Best Prices Guaranteed',
                        'desc' => 'Competitive pricing with financing available. Get a personalized quote tailored to your project and budget.',
                        'badge' => 'Special Offer',
                        'icon' => 'fa-tags',
                        'img' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=1920',
                    ],
                ];
                @endphp
                @foreach($defaultSlides as $i => $ds)
                <div class="swiper-slide">
                    <div class="hero-slide" style="background-image: url('{{ $ds['img'] }}');">
                        <div class="hero-slide-overlay"></div>
                        <div class="container position-relative" style="z-index:3;">
                            <div class="row align-items-center hero-slide-row">
                                <div class="col-lg-7 col-xl-6">
                                    <div class="hero-slide-content">
                                        <span class="hero-badge">
                                            <i class="fas {{ $ds['icon'] }}"></i>
                                            {{ $ds['badge'] }}
                                        </span>
                                        <h2 class="hero-slide-title">{{ $ds['title'] }}</h2>
                                        <p class="hero-slide-desc">{{ $ds['desc'] }}</p>
                                        <div class="hero-slide-actions">
                                        </div>
                                        <div class="hero-trust-row">
                                            <div class="hero-trust-item"><i class="fas fa-check-circle"></i> {{ __('Free Shipping') }}</div>
                                            <div class="hero-trust-item"><i class="fas fa-check-circle"></i> {{ __('Warranty Included') }}</div>
                                            <div class="hero-trust-item"><i class="fas fa-check-circle"></i> {{ __('Expert Support') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>



        <!-- Custom Pagination -->
        <div class="hero-pagination-wrap">
            <div class="container">
                <div class="swiper-pagination hero-dots"></div>
            </div>
        </div>
    </div>
</section>


{{-- =============================================
     TRUST BAR — Standard E-Commerce
     ============================================= --}}
<section class="trust-bar-section">
    <div class="container my-3">
        <div class="row g-0 trust-bar-row">
            <div class="col-6 col-md-3">
                <div class="trust-bar-item">
                    <i class="fas fa-shipping-fast"></i>
                    <div>
                        <strong>{{ __('Free Shipping') }}</strong>
                        <span>{{ __('On orders over 500 DH') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-bar-item">
                    <i class="fas fa-lock"></i>
                    <div>
                        <strong>{{ __('Secure Payment') }}</strong>
                        <span>{{ __('100% protected') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-bar-item">
                    <i class="fas fa-undo-alt"></i>
                    <div>
                        <strong>{{ __('Easy Returns') }}</strong>
                        <span>{{ __('30-day guarantee') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-bar-item border-end-0">
                    <i class="fas fa-headset"></i>
                    <div>
                        <strong>{{ __('24/7 Support') }}</strong>
                        <span>{{ __('Expert assistance') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     SHOP BY CATEGORY
     ============================================= --}}
<section id="categories" class="section-py bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="section-title mb-1">{{ __('Shop by Category') }}</h2>
                <p class="text-muted mb-0">{{ __('Browse our product categories') }}</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-link-arrow d-none d-md-inline-flex">
                {{ __('All Categories') }} <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        @if($allCategories->count() > 0)
        <div class="row g-3">
            @foreach($allCategories->take(6) as $index => $category)
            <div class="col-4 col-lg-2">
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="cat-card-v2">
                    <div class="cat-card-img">
                        @if($category->image)
                            @if(Str::startsWith($category->image, ['http://', 'https://']))
                                <img src="{{ $category->image }}" alt="{{ $category->name }}">
                            @else
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}">
                            @endif
                        @else
                            @php $catIcons = ['fa-print','fa-cut','fa-fill-drip','fa-scroll','fa-palette','fa-layer-group']; @endphp
                            <div class="cat-icon-placeholder cat-icon-{{ $index % 4 }}">
                                <i class="fas {{ $catIcons[$index % 6] }}"></i>
                            </div>
                        @endif
                    </div>
                    <div class="cat-card-body">
                        <h3 class="cat-card-name">{{ $category->name }}</h3>
                        <span class="cat-card-count">{{ $category->products_count ?? $category->products()->count() }} {{ __('products') }}</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>


{{-- =============================================
     FEATURED PRODUCTS
     ============================================= --}}
<section id="featured" class="section-py bg-surface">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="section-title mb-1">{{ __('Featured Products') }}</h2>
                <p class="text-muted mb-0">{{ __('Handpicked products just for you') }}</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-link-arrow d-none d-md-inline-flex">
                {{ __('View All') }} <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $index => $product)
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 80 }}">
                <div class="product-card-v2">
                    <div class="product-v2-image">
                        <img src="{{ $product->thumbnail ?? asset('images/placeholder-product.jpg') }}" alt="{{ $product->name }}">

                        <div class="product-v2-badges">
                            @if($product->created_at->diffInDays(now()) < 7)
                                <span class="badge-v2 badge-new">Nouveau</span>
                            @endif
                            @if($product->isOnSale())
                                <span class="badge-v2 badge-sale">-{{ $product->discount_percentage }}%</span>
                            @endif
                        </div>

                        <div class="product-v2-overlay">
                            @if($product->isInStock())
                            <button class="btn-overlay" onclick="addToCart({{ $product->id }})" title="Ajouter au panier">
                                <i class="fas fa-cart-plus"></i> Ajouter
                            </button>
                            @else
                            <span class="btn-overlay btn-overlay-disabled">
                                <i class="fas fa-ban"></i> Rupture
                            </span>
                            @endif
                            <a href="{{ route('shop.show', $product->id) }}" class="btn-overlay-icon" title="Voir la fiche">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product-v2-body">
                        @if($product->productCategory)
                            <span class="product-v2-cat">{{ $product->productCategory->name }}</span>
                        @endif
                        <h4 class="product-v2-name">{{ Str::limit($product->name, 40) }}</h4>
                        <div class="product-v2-rating">
                            @php $rating = round($product->reviews()->avg('rating') ?? 0); @endphp
                            <div class="stars-row">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star{{ $i < $rating ? '' : ' opacity-25' }}"></i>
                                @endfor
                            </div>
                            <span class="reviews-count">({{ $product->reviews()->count() }})</span>
                        </div>
                        <div class="product-v2-price">
                            @if($product->isOnSale())
                                <span class="price-sale">{{ $product->formatted_sale_price }}</span>
                                <span class="price-old">{{ $product->formatted_price }}</span>
                            @else
                                <span class="price-sale">{{ $product->formatted_price }}</span>
                            @endif
                        </div>
                        @if(!$product->isInStock())
                        <div class="out-of-stock-label"><i class="fas fa-exclamation-circle me-1"></i>Rupture de stock</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('shop.index') }}" class="btn-cta-outline">
                {{ __('View All Products') }} <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
 
 {{-- =============================================
      CATEGORY PRODUCT ROWS
      ============================================= --}}
 @foreach($allCategories as $index => $category)
     @if($category->products->count() > 0)
     <section class="section-py category-row-section {{ $index % 2 == 0 ? 'bg-white' : 'bg-surface' }}">
         <div class="container">
             <div class="d-flex justify-content-between align-items-end mb-4 category-row-header">
                 <div>
                     <h2 class="section-title mb-1">{{ $category->name }}</h2>
                     <p class="text-muted mb-0">{{ __('Discover our selection of') }} {{ strtolower($category->name) }}</p>
                 </div>
                 <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="btn-link-arrow">
                     {{ __('View Category') }} <i class="fas fa-arrow-right ms-2"></i>
                 </a>
             </div>
 
             <div class="row g-4">
                 @foreach($category->products as $product)
                 <div class="col-6 col-md-4 col-lg-3">
                     <div class="product-card-v2">
                         <div class="product-v2-image">
                             <img src="{{ $product->thumbnail ?? asset('images/placeholder-product.jpg') }}" alt="{{ $product->name }}">
                             
                             <div class="product-v2-badges">
                                 @if($product->created_at->diffInDays(now()) < 7)
                                     <span class="badge-v2 badge-new">Nouveau</span>
                                 @endif
                             </div>
 
                             <div class="product-v2-overlay">
                                 <button class="btn-overlay" onclick="addToCart({{ $product->id }})">
                                     <i class="fas fa-cart-plus"></i> {{ __('Add') }}
                                 </button>
                                 <a href="{{ route('shop.show', $product->id) }}" class="btn-overlay-icon">
                                     <i class="fas fa-eye"></i>
                                 </a>
                             </div>
                         </div>
 
                         <div class="product-v2-body">
                             <h4 class="product-v2-name">{{ Str::limit($product->name, 40) }}</h4>
                             <div class="product-v2-rating">
                                 <div class="stars-row">
                                     @for($i = 0; $i < 5; $i++)
                                         <i class="fas fa-star{{ $i < $product->rating ? '' : ' opacity-25' }}"></i>
                                     @endfor
                                 </div>
                             </div>
                             <div class="product-v2-price">
                                 <span class="price-sale">{{ $product->formatted_price }}</span>
                             </div>
                         </div>
                     </div>
                 </div>
                 @endforeach
             </div>
         </div>
     </section>
     @endif
 @endforeach
 


{{-- =============================================
     PROMOTIONAL CTA BANNER
     ============================================= --}}
<section class="promo-cta-section">
    <div class="container">
        <div class="promo-cta-card">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h2 class="promo-cta-title">{{ __("Can't find what you need?") }}</h2>
                    <p class="promo-cta-desc">{{ __('Contact us for a free personalized quote. We deliver and install everywhere in Morocco.') }}</p>
                </div>
                <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                    <a href="tel:{{ setting('company_phone', '+212600000000') }}" class="btn-hero-shop me-2">
                        <i class="fas fa-phone-alt"></i> {{ __('Call Us') }}
                    </a>
                    <a href="mailto:{{ setting('company_email', 'contact@speedprint.ma') }}" class="btn-hero-outline" style="border-color: rgba(255,255,255,.5);">
                        <i class="fas fa-envelope"></i> {{ __('Email Us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     OUR SERVICES — Simple Icons
     ============================================= --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">{{ __('Why Shop With Us') }}</h2>
            <p class="text-muted">{{ __('We deliver more than just products') }}</p>
        </div>
        <div class="row g-4">
            @php
            $services = [
                ['icon' => 'fa-truck-moving', 'title' => __('Fast Delivery'), 'desc' => __('Free shipping on orders over 500 DH. Delivery across Morocco.')],
                ['icon' => 'fa-shield-alt',   'title' => __('Warranty'), 'desc' => __('All products come with manufacturer warranty and after-sales support.')],
                ['icon' => 'fa-tools',        'title' => __('Installation'), 'desc' => __('On-site installation and setup by our certified technicians.')],
                ['icon' => 'fa-graduation-cap', 'title' => __('Training'), 'desc' => __('Free operator training included with every equipment purchase.')],
            ];
            @endphp
            @foreach($services as $svc)
            <div class="col-6 col-md-3">
                <div class="service-card text-center">
                    <div class="service-icon"><i class="fas {{ $svc['icon'] }}"></i></div>
                    <h5 class="service-title">{{ $svc['title'] }}</h5>
                    <p class="service-desc">{{ $svc['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- =============================================
     TESTIMONIALS
     ============================================= --}}
@if($testimonials->count() > 0)
<section class="section-py bg-surface">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">{{ __('Customer Reviews') }}</h2>
            <p class="text-muted">{{ __('What our customers say about us') }}</p>
        </div>
        <div class="row g-4">
            @foreach($testimonials->take(3) as $i => $t)
            <div class="col-md-4">
                <div class="review-card">
                    <div class="review-stars">
                        @for($s = 0; $s < $t->rating; $s++)<i class="fas fa-star"></i>@endfor
                    </div>
                    <p class="review-text">"{{ Str::limit($t->content, 160) }}"</p>
                    <div class="review-author">
                        <div class="review-avatar">{{ strtoupper(substr($t->name, 0, 1)) }}</div>
                        <div>
                            <div class="review-name">{{ $t->name }}</div>
                            <div class="review-role">{{ __('Verified Customer') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- =============================================
     WHATSAPP FLOATING BUTTON
     ============================================= --}}
@php
    $waNumber = setting('social_whatsapp', '');
    // Strip to digits only for the wa.me link
    $waLink = $waNumber ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $waNumber) : '';
@endphp

@if($waLink)
<a href="{{ $waLink }}?text=Bonjour%2C%20je%20suis%20intéressé%20par%20vos%20machines%20d'impression." 
   class="whatsapp-float" 
   target="_blank" 
   rel="noopener noreferrer"
   title="Contactez-nous sur WhatsApp">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
    <span class="whatsapp-float-label">WhatsApp</span>
</a>
@endif

@endsection

@push('scripts')
<script>
// Hero Slider
const heroSwiper = new Swiper('.hero-swiper', {
    loop: true,
    autoplay: {
        delay: 6000,
        disableOnInteraction: false,
    },
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    },
    speed: 1000,
    pagination: {
        el: '.hero-dots',
        clickable: true,
        renderBullet: function (index, className) {
            return '<span class="' + className + '"><i></i></span>';
        },
    }
});

function addToCart(productId) {
    fetch(`{{ url('/cart/add') }}/${productId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;
        if (!response.ok) throw new Error((data && data.message) || `Server Error: ${response.status}`);
        const countEl = document.getElementById('header-cart-count');
        if(countEl && data.cartCount !== undefined) countEl.textContent = data.cartCount;
        refreshMiniCart();
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Ajouté au panier !', showConfirmButton:false, timer:2500, background:'#1a1a2e', color:'#fff' });
    })
    .catch(error => {
        Swal.fire({ toast:true, position:'top-end', icon:'error', title: error.message || 'Erreur', showConfirmButton:false, timer:3000 });
    });
}

function toggleFaq(btn) {
    const item   = btn.closest('.faq-item');
    const answer = item.querySelector('.faq-answer');
    const allItems = document.querySelectorAll('.faq-item');

    allItems.forEach(el => {
        if (el !== item) {
            el.classList.remove('faq-open');
            el.querySelector('.faq-answer').style.display = 'none';
        }
    });

    if (item.classList.contains('faq-open')) {
        item.classList.remove('faq-open');
        answer.style.display = 'none';
    } else {
        item.classList.add('faq-open');
        answer.style.display = 'block';
    }
}
</script>
@endpush
