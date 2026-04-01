@extends('layouts.frontend')

@section('meta_title', setting('app_name') . ' — ' . setting('app_description', 'Mobilier d\'Intérieur Artisanal & Sur-Mesure'))
@section('meta_description', 'Découvrez notre gamme de mobilier artisanal, menuiserie, tapisserie, et métallurgie au Maroc. Installation et livraison incluses.')
@section('meta_keywords', setting('app_name', 'Moubdi3oun') . ', mobilier, artisanat, canapés, tables, lits, décoration, ameublement Maroc')

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
        "name": "Faites-vous du mobilier sur-mesure ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, nous concevons et fabriquons du mobilier 100% sur-mesure selon vos dimensions, matériaux et finitions souhaitées."
        }
      },
      {
        "@type": "Question",
        "name": "Quels matériaux utilisez-vous ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Nous utilisons des bois nobles, des métaux traités de haute qualité et des tissus premium sélectionnés pour leur durabilité et leur élégance."
        }
      },
      {
        "@type": "Question",
        "name": "Livrez-vous dans tout le Maroc ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, notre équipe assure la livraison et l'installation de vos meubles dans l'ensemble du territoire marocain."
        }
      },
      {
        "@type": "Question",
        "name": "Quel est le délai de fabrication ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Le délai de fabrication varie généralement entre 2 et 4 semaines selon la complexité et les matériaux choisis."
        }
      }
    ]
  }
]
</script>
@endsection

@section('content')

<section id="home" class="hero-v3">
    <video autoplay muted loop playsinline class="hero-video-bg">
        <source src="https://assets.mixkit.co/videos/preview/mixkit-handcrafted-wooden-furniture-making-worker-42010-large.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay-dark"></div>
    <div class="hero-v3-content" data-aos="zoom-out" data-aos-duration="1200">
        <h1 class="text-white">{{ __('Hero Title 1') }} <span style="color: var(--accent);">{{ __('Hero Title 2') }}</span> <br> {{ __('Hero Title 3') }} <span style="color: var(--accent);">{{ __('Hero Title 4') }}</span></h1>
        <p class="mb-4">{{ __('Hero Subtitle') }}</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="#departments" class="btn-cta-primary">{{ __('Our Workshops') }}</a>
            <a href="#contact" class="btn-cta-outline border-white text-white">{{ __('Contact Us') }}</a>
        </div>
    </div>
</section>

{{-- NEWSLETTER SECTION --}}
<section class="section-py bg-dark text-white position-relative overflow-hidden">
    <div class="container py-5 text-center position-relative z-1" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <i class="fas fa-paper-plane fs-1 mb-4 opacity-25"></i>
                <h2 class="fw-black text-uppercase ls-1 display-5 mb-4">{{ __('Newsletter Title') }}</h2>
                <p class="opacity-75 mb-5">{{ __('Newsletter Sub') }}</p>
                
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form position-relative">
                    @csrf
                    <input type="email" name="email" class="form-control bg-white border-0 py-4 px-5 rounded-pill shadow" placeholder="{{ __('Email Placeholder') }}" required>
                    <button type="submit" class="btn btn-dark rounded-pill py-3 px-5 position-absolute top-50 end-0 translate-middle-y me-2 h-75 fw-black text-uppercase ls-1">{{ __('Subscribe') }}</button>
                </form>
                <p class="small opacity-50 mt-4">{{ __('Newsletter Discount Note') }}</p>
            </div>
        </div>
    </div>
</section>
<section id="about" class="section-py bg-white">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-9" data-aos="fade-up">
                <span class="section-eyebrow">{{ __('Our Philosophy') }}</span>
                <h2 class="display-5 fw-black mb-4" style="letter-spacing: -1.5px;">{{ __('Philosophy Title 1') }} <span style="color: var(--accent);">{{ __('Philosophy Title 2') }}</span></h2>
                <p class="lead text-muted lh-lg">
                    {{ __('Philosophy Description') }}
                </p>
                <div class="mt-5 d-flex justify-content-center gap-5">
                    <div class="text-center">
                        <h4 class="fw-black mb-0" style="color: var(--accent);">100%</h4>
                        <span class="small text-uppercase fw-bold ls-1">{{ __('Custom Made') }}</span>
                    </div>
                    <div class="text-center text-dark" style="width: 1px; height: 50px; background: #eee;"></div>
                    <div class="text-center">
                        <h4 class="fw-black mb-0" style="color: var(--accent);">Premium</h4>
                        <span class="small text-uppercase fw-bold ls-1">{{ __('Materials') }}</span>
                    </div>
                    <div class="text-center text-dark" style="width: 1px; height: 50px; background: #eee;"></div>
                    <div class="text-center">
                        <h4 class="fw-black mb-0" style="color: var(--accent);">SAV</h4>
                        <span class="small text-uppercase fw-bold ls-1">{{ __('Guaranteed') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    {{-- BRANDS / MARQUEE STATEMENT --}}
    <section class="py-4 bg-dark text-white border-top border-bottom border-secondary-subtle">
        <div class="announcement-bar bg-transparent">
            <div class="promo-marquee">
                <div class="promo-item fw-black text-uppercase ls-2">L'Art de Vivre Moubdi3oun</div>
                <div class="promo-item fw-black text-uppercase ls-2">Conception Artisanale</div>
                <div class="promo-item fw-black text-uppercase ls-2">Matériaux Nobles</div>
                <div class="promo-item fw-black text-uppercase ls-2">Expertise Sur-Mesure</div>
                <!-- Loop -->
                <div class="promo-item fw-black text-uppercase ls-2">L'Art de Vivre Moubdi3oun</div>
                <div class="promo-item fw-black text-uppercase ls-2">Conception Artisanale</div>
                <div class="promo-item fw-black text-uppercase ls-2">Matériaux Nobles</div>
                <div class="promo-item fw-black text-uppercase ls-2">Expertise Sur-Mesure</div>
            </div>
        </div>
    </section>

    {{-- SHOPPABLE LOOKBOOK --}}
    <section class="section-py bg-white overflow-hidden">
        <div class="container">
            <div class="row align-items-center mb-5" data-aos="fade-up">
                <div class="col-lg-6">
                    <span class="badge-new mb-3 d-inline-block">Inspiration</span>
                    <h2 class="fw-black text-uppercase ls-1">Shop The Room</h2>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <p class="text-muted small mb-0">Cliquez sur les points pour découvrir les pièces.</p>
                </div>
            </div>
            
            <div class="position-relative rounded-5 overflow-hidden shadow-lg" data-aos="zoom-in">
                <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&q=80" alt="Lookbook" class="w-100">
                
                {{-- Hotspots mapped to seeded product slugs --}}
                @php
                    $h1 = \App\Models\Product::where('slug', 'canape-royal-atlas')->first();
                    $h2 = \App\Models\Product::where('slug', 'table-basse-orion-metal')->first();
                    $h3 = \App\Models\Product::where('slug', 'lampe-ambre-soufflee')->first();
                @endphp

                @if($h1)
                <a href="{{ route('shop.show', $h1->id) }}" class="lookbook-pin" style="top: 30%; left: 45%;" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="{{ $h1->translated_name }}" data-bs-content="{{ Str::limit($h1->description, 50) }} - {{ $h1->formatted_price }}">
                    <i class="fas fa-plus"></i>
                </a>
                @endif

                @if($h2)
                <a href="{{ route('shop.show', $h2->id) }}" class="lookbook-pin" style="top: 60%; left: 25%;" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="{{ $h2->translated_name }}" data-bs-content="{{ Str::limit($h2->description, 50) }} - {{ $h2->formatted_price }}">
                    <i class="fas fa-plus"></i>
                </a>
                @endif

                @if($h3)
                <a href="{{ route('shop.show', $h3->id) }}" class="lookbook-pin" style="top: 15%; left: 75%;" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="{{ $h3->translated_name }}" data-bs-content="{{ Str::limit($h3->description, 50) }} - {{ $h3->formatted_price }}">
                    <i class="fas fa-plus"></i>
                </a>
                @endif
            </div>
        </div>
    </section>

    {{-- FEATURED CATEGORIES GRID --}}
    <section class="section-py bg-light">
        <div class="container">
            <h2 class="fw-black text-uppercase ls-1 text-center mb-5" data-aos="fade-up">Univers Collections</h2>
            <div class="row g-4">
                @foreach($allCategories->take(3) as $cat)
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="text-decoration-none">
                        <div class="category-card shadow-sm rounded-5 overflow-hidden bg-white h-100 border-0 transition-all hover-translate-y">
                            <div class="aspect-ratio-4-5 overflow-hidden">
                                <img src="{{ $cat->image ? Storage::url($cat->image) : 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80' }}" alt="{{ $cat->name }}" class="w-100 h-100 object-fit-cover transition-all hover-scale-110">
                            </div>
                            <div class="p-4 text-center">
                                <h4 class="fw-black text-uppercase ls-1 h5 mb-2">{{ $cat->translated_name }}</h4>
                                <span class="small text-muted text-uppercase fw-bold ls-1">{{ __('Discover') }} <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} ms-1" style="font-size: 0.6rem;"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
<section id="departments" class="section-py bg-surface overflow-hidden">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <span class="section-eyebrow">Nos Ateliers</span>
            <h2 class="section-title">L'Excellence de l'Artisanat</h2>
            <p class="section-subtitle">Chaque département est dédié à la maîtrise d'un art spécifique.</p>
        </div>

        @php
            $displayCats = $allCategories->count() > 0 ? $allCategories : collect([]);
            $icons = ['fa-tree', 'fa-fire', 'fa-couch', 'fa-paint-roller', 'fa-box-open'];
        @endphp
        @foreach($displayCats as $index => $category)
        <div class="dept-row" data-aos="{{ $index % 2 == 0 ? 'fade-right' : 'fade-left' }}">
            <div class="dept-media-box">
                @if(isset($category->image))
                    <img src="{{ Str::startsWith($category->image, 'http') ? $category->image : Storage::url($category->image) }}" alt="{{ $category->translated_name }}">
                @else
                    <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=800" alt="Placeholder">
                @endif
            </div>
            <div class="dept-content-box">
                <div class="dept-pulse-icon">
                    <i class="fas {{ $icons[$index % count($icons)] }}"></i>
                </div>
                <h3 class="fw-black h2 mb-3">{{ $category->translated_name }}</h3>
                <p class="text-muted mb-4 lead">
                    {{ $category->translated_description ?? 'Expertise et savoir-faire pour des créations qui durent toute une vie.' }}
                </p>
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="btn-cta-primary">
                    Voir les réalisations <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- =============================================
     PORTFOLIO SECTION
     ============================================= --}}
<section id="portfolio" class="section-py bg-white">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <span class="section-eyebrow">Portfolio</span>
            <h2 class="section-title">Nos Plus Belles Créations</h2>
            <p class="section-subtitle">Chaque pièce est une œuvre d'art unique conçue pour durer.</p>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $index => $product)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="portfolio-card">
                    <img src="{{ Str::startsWith($product->thumbnail, 'http') ? $product->thumbnail : Storage::url($product->thumbnail) }}" alt="{{ $product->translated_name }}">
                    <div class="portfolio-mask">
                        <span class="small text-uppercase fw-bold ls-1 mb-2 d-block" style="color: var(--accent);">{{ $product->productCategory->translated_name ?? 'Collection' }}</span>
                        <h4 class="fw-black text-white h3 mb-3">{{ $product->translated_name }}</h4>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top border-secondary">
                            <span class="h4 mb-0 fw-bold">{{ $product->formatted_price }}</span>
                            <a href="{{ route('shop.show', $product->id) }}" class="btn-cta-primary p-2 px-3">
                                <i class="fas fa-eye me-1"></i> Détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =============================================
     APPLICATIONS — What Can You Print?
     ============================================= --}}
<section class="section-py bg-dark-gradient">
    <div class="container">
        <div class="section-header section-header-light" data-aos="fade-up">
            <span class="section-eyebrow eyebrow-light">Expertise</span>
            <h2 class="section-title text-white">Pour Chaque Espace</h2>
            <p class="section-subtitle" style="color: rgba(255,255,255,.65);">Nous transformons et aménageons chaque pièce de votre intérieur.</p>
        </div>
        <div class="row g-3 mt-2">
            @php
            $apps = [
                ['icon'=>'fa-couch',         'title'=>'Salon',                'desc'=>'Canapés, tables basses, et meubles TV sur mesure.'],
                ['icon'=>'fa-bed',           'title'=>'Chambre à Coucher',    'desc'=>'Lits complets, dressings, et coiffeuses élégantes.'],
                ['icon'=>'fa-utensils',      'title'=>'Salle à Manger',       'desc'=>'Tables en bois massif, chaises tapissées, buffets.'],
                ['icon'=>'fa-laptop-house',  'title'=>'Bureau',               'desc'=>'Bureaux ergonomiques, bibliothèques, et étagères murales.'],
                ['icon'=>'fa-door-open',     'title'=>'Entrée & Couloirs',    'desc'=>'Consoles, miroirs, et portemanteaux design.'],
                ['icon'=>'fa-chair',         'title'=>'Espaces Commerciaux',  'desc'=>'Aménagement de restaurants, hôtels, et boutiques luxueuses.'],
            ];
            @endphp
            @foreach($apps as $i => $app)
            <div class="col-6 col-md-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}">
                <div class="app-card">
                    <div class="app-icon"><i class="fas {{ $app['icon'] }}"></i></div>
                    <h5 class="app-title">{{ $app['title'] }}</h5>
                    <p class="app-desc">{{ $app['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =============================================
     PROCESS SECTION (VERTICAL TIMELINE)
     ============================================= --}}
<section id="process" class="section-py bg-surface overflow-hidden">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <span class="section-eyebrow">Notre Savoir-Faire</span>
            <h2 class="section-title">Comment Nous Créons Vos Pièces Uniques</h2>
            <p class="section-subtitle">Chaque commande suit un processus rigoureux pour garantir l'excellence.</p>
        </div>

        <div class="timeline-track" data-aos="fade-up">
            @php
                $processSteps = [
                    ['num' => '01', 'title' => 'Consultation & Mesures', 'desc' => 'Nous nous déplaçons pour analyser votre espace, comprendre vos besoins et prendre les mesures exactes.'],
                    ['num' => '02', 'title' => 'Design & Modélisation', 'desc' => 'Nos designers créent des plans 3D détaillés pour vous permettre de visualiser le projet avant fabrication.'],
                    ['num' => '03', 'title' => 'Fabrication Artisanale', 'desc' => 'Nos artisans sculptent, assemblent et finissent chaque meuble avec une attention méticuleuse portée aux détails.'],
                    ['num' => '04', 'title' => 'Livraison & Pose', 'desc' => 'Nous assurons le transport sécurisé et l\'installation parfaite de vos créations par nos propres équipes.'],
                ];
            @endphp

            @foreach($processSteps as $step)
            <div class="timeline-step">
                <div class="step-marker">{{ $step['num'] }}</div>
                <div class="step-card shadow-sm border-0">
                    <h4 class="fw-black mb-2">{{ $step['title'] }}</h4>
                    <p class="text-muted mb-0 small lh-base">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =============================================
     REFINED USP SECTION
     ============================================= --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4" data-aos="fade-up">
                <div class="p-4">
                    <i class="fas fa-award fa-3x mb-3" style="color: var(--accent);"></i>
                    <h4 class="fw-black mb-2">Qualité Héritage</h4>
                    <p class="text-muted small">Des matériaux sélectionnés pour traverser les générations.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="p-4">
                    <i class="fas fa-pencil-ruler fa-3x mb-3" style="color: var(--accent);"></i>
                    <h4 class="fw-black mb-2">Design Sur-Mesure</h4>
                    <p class="text-muted small">Chaque détail est adapté à vos envies et votre espace.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="p-4">
                    <i class="fas fa-shipping-fast fa-3x mb-3" style="color: var(--accent);"></i>
                    <h4 class="fw-black mb-2">Service National</h4>
                    <p class="text-muted small">Livraison et installation professionnelles dans tout le Maroc.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     BOLD RED CONTACT CTA
     ============================================= --}}
<section id="contact" class="section-py" style="background: var(--accent); color: #fff;">
    <div class="container py-4">
        <div class="row align-items-center g-5 text-center text-lg-start">
            <div class="col-lg-7" data-aos="fade-right">
                <h2 class="display-4 fw-black mb-4">Donnons Vie à <br> Vos <span class="text-white border-bottom border-3">Projets</span></h2>
                <p class="lead mb-0 opacity-90">Notre équipe est prête à vous accompagner dans la création de votre mobilier d'exception. Contactez-nous dès aujourd'hui pour un devis personnalisé.</p>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="d-flex flex-column gap-4 align-items-center align-items-lg-start mt-4 mt-lg-0">
                    <a href="tel:{{ setting('company_phone') }}" class="text-white text-decoration-none d-flex align-items-center gap-3 h3 fw-black mb-0">
                        <i class="fas fa-phone-alt p-3 bg-white text-danger rounded-circle" style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;"></i>
                        {{ setting('company_phone', '+212 6XX XX XX XX') }}
                    </a>
                    <a href="mailto:{{ setting('company_email') }}" class="text-white text-decoration-none d-flex align-items-center gap-3 h3 fw-black mb-0">
                        <i class="fas fa-envelope p-3 bg-white text-danger rounded-circle" style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;"></i>
                        {{ setting('company_email', 'contact@moubdi3oun.com') }}
                    </a>
                    <div class="text-white d-flex align-items-center gap-3 h5 fw-bold mb-0">
                        <i class="fas fa-map-marker-alt p-3 bg-white text-danger rounded-circle" style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;"></i>
                        {{ setting('company_address', 'Maroc, Casablanca') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     BRANDS STRIP
     ============================================= --}}
<section class="brands-section py-4">
    <div class="container">
        <div class="brands-ticker">
            @php
            $brands = ['Bois Massif', 'Marbre', 'Inox Doré', 'Laiton', 'Velours Premium', 'Cuir Véritable', 'Céramique'];
            @endphp
            @foreach($brands as $brand)
            <div class="brand-chip" style="font-weight: 600; font-size: 1.1rem; margin: 0 20px;">
                <i class="fas fa-gem me-2" style="color: var(--accent);"></i>{{ $brand }}
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
        <div class="section-header" data-aos="fade-up">
            <span class="section-eyebrow">Avis clients</span>
            <h2 class="section-title">Ce que disent nos ateliers</h2>
            <p class="section-subtitle">Des imprimeurs professionnels qui nous font confiance au quotidien</p>
        </div>
        <div class="row g-4">
            @foreach($testimonials->take(3) as $i => $t)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="review-card">
                    <div class="review-stars">
                        @for($s = 0; $s < $t->rating; $s++)<i class="fas fa-star"></i>@endfor
                    </div>
                    <p class="review-text">"{{ Str::limit($t->content, 160) }}"</p>
                    <div class="review-author">
                        <div class="review-avatar">{{ strtoupper(substr($t->name, 0, 1)) }}</div>
                        <div>
                            <div class="review-name">{{ $t->name }}</div>
                            <div class="review-role">Client vérifié</div>
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
     FAQ SECTION
     ============================================= --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4" data-aos="fade-right">
                <span class="section-eyebrow">FAQ</span>
                <h2 class="section-title">Questions fréquentes</h2>
                <p class="section-subtitle text-start">Vous avez d'autres questions ? Notre équipe est disponible 6j/7.</p>
                <a href="tel:{{ setting('company_phone', '+212600000000') }}" class="btn-cta-outline mt-3">
                    <i class="fas fa-phone-alt me-2"></i>Nous appeler
                </a>
            </div>
            <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
                    <div class="faq-list">
                    @php
                    $faqs = [
                        ['q'=>'Modifiez-vous le design pour s\'adapter à nos espaces ?',
                         'a'=>'Absolument. Chaque meuble est conçu pour répondre précisément à vos contraintes d\'espace, vos goûts, et l\'ambiance générale de votre intérieur.'],
                        ['q'=>'Quels types de finition proposez-vous ?',
                         'a'=>'Nous proposons une gamme extrêmement variée: mates, brillantes, laquées, brossées et naturelles, pour les bois comme pour les métaux.'],
                        ['q'=>'Vos tissus sont-ils résistants aux taches ?',
                         'a'=>'Oui, certains de nos tissus premium (type velours et lin traités) bénéficient de traitements antitaches de dernière technologie pour une durabilité maximale.'],
                        ['q'=>'Livrez-vous dans toutes les villes ?',
                         'a'=>'Oui, nous assurons la logistique, l\'acheminement et l\'installation par nos artisans professionnels dans tout le Maroc.']
                    ];
                    @endphp
                    @foreach($faqs as $fi => $faq)
                    <div class="faq-item{{ $fi === 0 ? ' faq-open' : '' }}">
                        <button class="faq-question" onclick="toggleFaq(this)">
                            <span>{{ $faq['q'] }}</span>
                            <i class="fas fa-chevron-down faq-chevron"></i>
                        </button>
                        <div class="faq-answer" style="{{ $fi === 0 ? '' : 'display:none;' }}">
                            <p>{{ $faq['a'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     WHATSAPP FLOATING BUTTON
     ============================================= --}}
@php
    $waNumber = setting('social_whatsapp', '');
    // Strip to digits only for the wa.me link
    $waLink = $waNumber ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $waNumber) : '';
@endphp

@if($waLink)
<a href="{{ $waLink }}?text=Bonjour%2C%20je%20suis%20intéressé%20par%20votre%20mobilier%20sur-mesure." 
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
