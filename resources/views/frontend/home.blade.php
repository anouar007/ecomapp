@extends('layouts.frontend')

@section('meta_title', setting('app_name', 'Coopérative Ait Oumdis') . ' — ' . 'Produits du terroir & Santé naturelle')
@section('meta_description', 'Découvrez les trésors de la province d\'Azilal : miel pur, huile d\'argan, amlou artisanal et recettes naturelles. Tradition marocaine et qualité certifiée.')
@section('meta_keywords', 'coopérative Ait Oumdis, miel pur Maroc, huile argan bio, amlou artisanal, produits terroir Azilal, santé naturelle marocaine')

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
        "name": "D'où proviennent vos produits ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Tous nos produits sont issus de la province d'Azilal, au cœur des montagnes de l'Atlas, où nous pratiquons une récolte traditionnelle respectueuse de l'environnement."
        }
      },
      {
        "@type": "Question",
        "name": "Vos miels sont-ils 100% naturels ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, nos miels sont garantis sans additifs ni sucres ajoutés. Chaque récolte est analysée pour garantir sa pureté et ses propriétés thérapeutiques."
        }
      },
      {
        "@type": "Question",
        "name": "Livrez-vous dans tout le Maroc ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Absolument. Nous livrons nos produits du terroir dans toutes les villes du Maroc, avec un emballage soigné pour préserver la qualité des produits."
        }
      },
      {
        "@type": "Question",
        "name": "Comment commander en gros pour une boutique ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Nous collaborons avec de nombreux revendeurs. Contactez-nous via WhatsApp ou par email pour recevoir notre catalogue professionnel et nos conditions tarifaires."
        }
      }
    ]
  }
]
</script>
@endsection

@section('content')

{{-- =============================================
     HERO — Full-Width Cinematic Hero
     ============================================= --}}
<section class="print-hero-v2">
    <div class="aurora-bg"></div>
    <div class="hero-text-overlay" style="position: absolute; inset: 0; background: linear-gradient(60deg, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 60%); pointer-events: none;"></div>

    <div class="container position-relative py-0">
        <div class="row align-items-center min-vh-90">
            <div class="col-lg-6 pe-lg-5" data-aos="fade-right" data-aos-duration="900">

                <div class="hero-eyebrow">
                    <span class="hero-eyebrow-dot"></span>
                    Le meilleur pour votre santé
                </div>

                <h1 class="display-hero">
                    Découvrez les<br>
                    <span class="text-gradient-primary">trésors naturels</span><br>
                    du terroir
                </h1>

                <p class="hero-lead">
                    Produits artisanaux de la <strong>Coopérative Ait Oumdis</strong>. Miel pur, Huile d'Argan, et remèdes naturels issus de la province d'Azilal.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('shop.index') }}" class="btn-hero-primary btn-magnetic">
                        <i class="fas fa-leaf"></i>
                        <span>Nos Produits</span>
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('social_whatsapp', '')) }}?text={{ urlencode('Bonjour, je souhaite commander des produits de la Coopérative Ait Oumdis.') }}" class="btn-hero-ghost btn-magnetic">
                        <i class="fab fa-whatsapp"></i>
                        <span>Commander</span>
                    </a>
                </div>

                {{-- Trust pills --}}
                <div class="hero-trust-pills pt-4">
                    <div class="trust-pill"><i class="fas fa-check-circle"></i> 100% Naturel</div>
                    <div class="trust-pill"><i class="fas fa-certificate"></i> Qualité Certifiée</div>
                    <div class="trust-pill"><i class="fas fa-mountain"></i> Origine Azilal</div>
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center" data-aos="zoom-in-up" data-aos-duration="1200" data-aos-delay="150" style="position: relative; z-index: 2;">
                <div class="hero-image-stack">
                    {{-- Main image --}}
                    <div class="hero-img-main arch-shape" style="position: relative;">
                        @if(isset($heroSlides) && $heroSlides->count() > 0)
                            <img src="{{ $heroSlides[0]->image_url ?? Storage::url($heroSlides[0]->main_image) }}" alt="Produits naturels Atlas" class="img-fluid" style="width: 100%; height: 600px; object-fit: cover;">
                        @else
                            <img src="https://images.unsplash.com/photo-1589733975941-57a51e859ec1?auto=format&fit=crop&q=80&w=900" alt="Miel et Argan" class="img-fluid" style="width: 100%; height: 600px; object-fit: cover;">
                        @endif
                    </div>

                    {{-- Floating cards --}}
                    <div class="hero-float-card hero-float-top">
                        <div class="float-icon-wrap"><i class="fas fa-sun"></i></div>
                        <div>
                            <div class="float-label">Récolte</div>
                            <div class="float-value">Traditionnelle</div>
                        </div>
                    </div>
                    <div class="hero-float-card hero-float-bottom">
                        <div class="float-icon-wrap float-icon-green"><i class="fas fa-heart"></i></div>
                        <div>
                            <div class="float-label">Santé</div>
                            <div class="float-value">100% Pur</div>
                        </div>
                    </div>
                    <div class="hero-float-badge">
                        <i class="fas fa-award me-1"></i>Issu de l'Atlas
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom wave separator --}}
    <div class="hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z" fill="var(--bg-surface)"/></svg>
    </div>
</section>

{{-- =============================================
     KEY METRICS STRIP
     ============================================= --}}
<section class="metrics-strip py-5 border-top border-bottom">
    <div class="container">
        <div class="row g-0 metrics-row">
            <div class="col-6 col-md-3">
                <div class="metric-item border-end" style="border-right-color: var(--border-color) !important;">
                    <div class="metric-icon" style="color: var(--primary);"><i class="fas fa-users"></i></div>
                    <div class="metric-number text-dark">120<span>+</span></div>
                    <div class="metric-label text-muted">Membres Actifs</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item border-end" style="border-right-color: var(--border-color) !important;">
                    <div class="metric-icon" style="color: var(--primary);"><i class="fas fa-leaf"></i></div>
                    <div class="metric-number text-dark">40<span>+</span></div>
                    <div class="metric-label text-muted">Produits Bio</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item border-end" style="border-right-color: var(--border-color) !important;">
                    <div class="metric-icon" style="color: var(--primary);"><i class="fas fa-history"></i></div>
                    <div class="metric-number text-dark">20<span> ans</span></div>
                    <div class="metric-label text-muted">De Savoir-faire</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item">
                    <div class="metric-icon" style="color: var(--primary);"><i class="fas fa-mountain"></i></div>
                    <div class="metric-number text-dark">2500<span>m</span></div>
                    <div class="metric-label text-muted">Altitude Culture</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     CATEGORIES — Print Industry
     ============================================= --}}
<section id="categories" class="section-py bg-surface py-5">
    <div class="container py-4">
        <div class="section-header" data-aos="fade-up">
            <span class="section-eyebrow">Nos Trésors</span>
            <h2 class="section-title">Le meilleur du terroir</h2>
            <p class="section-subtitle">Des produits authentiques, récoltés avec amour et respect pour la nature, directement de nos montagnes.</p>
        </div>

        @if($allCategories->count() > 0)
        <div class="row g-4">
            @foreach($allCategories->take(4) as $index => $category)
            <div class="col-6 col-lg-3" data-aos="zoom-in" data-aos-delay="{{ $index * 80 }}">
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="cat-card-v2 hover-scale">
                    <div class="cat-card-img img-zoom-hover">
                        @if($category->image)
                            @if(Str::startsWith($category->image, ['http://', 'https://']))
                                <img src="{{ $category->image }}" alt="{{ $category->name }}">
                            @else
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}">
                            @endif
                        @else
                            @php $catIcons = ['fa-print','fa-cut','fa-fill-drip','fa-scroll']; @endphp
                            <div class="cat-icon-placeholder cat-icon-{{ $index }}">
                                <i class="fas {{ $catIcons[$index % 4] }}"></i>
                            </div>
                        @endif
                    </div>
                    <div class="cat-card-body">
                        <h3 class="cat-card-name">{{ $category->name }}</h3>
                        <span class="cat-card-count">{{ $category->products_count ?? $category->products()->count() }} produits</span>
                        <div class="cat-card-arrow"><i class="fas fa-arrow-right"></i></div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        {{-- Fallback static industry categories --}}
        @php
        $staticCats = [
            ['icon'=>'fa-honey-pot', 'color'=>'#cc6600', 'bg'=>'rgba(204,102,0,0.12)',  'name'=>'Miels de Montagne', 'sub'=>'Pur · Naturel · Thérapeutique'],
            ['icon'=>'fa-tint',      'color'=>'#00b878', 'bg'=>'rgba(0,184,120,0.12)',  'name'=>'Huiles Essentielles', 'sub'=>'Extraction à froid · Bio'],
            ['icon'=>'fa-seedling',  'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,.12)',  'name'=>'Plantes Médicinales', 'sub'=>'Récolte sauvage · Azilal'],
            ['icon'=>'fa-mortar-pestle','color'=>'#8b5cf6', 'bg'=>'rgba(139,92,246,.12)', 'name'=>'Recettes Traditionnelles', 'sub'=>'Amlou · Épices · Savoir-faire'],
        ];
        @endphp
        <div class="row g-4">
            @foreach($staticCats as $i => $cat)
            <div class="col-6 col-lg-3" data-aos="zoom-in" data-aos-delay="{{ $i * 80 }}">
                <a href="{{ route('shop.index') }}" class="cat-card-v2 hover-scale">
                    <div class="cat-card-img img-zoom-hover">
                        <div class="cat-icon-placeholder" style="background: {{ $cat['bg'] }}; color: {{ $cat['color'] }};">
                            <i class="fas {{ $cat['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="cat-card-body">
                        <h3 class="cat-card-name">{{ $cat['name'] }}</h3>
                        <span class="cat-card-count">{{ $cat['sub'] }}</span>
                        <div class="cat-card-arrow"><i class="fas fa-arrow-right"></i></div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- =============================================
     FEATURED MACHINES — Product Grid
     ============================================= --}}
<section id="featured" class="section-py bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
            <div>
                <span class="section-eyebrow">Sélection</span>
                <h2 class="section-title mb-0">Coups de cœur</h2>
                <p class="section-subtitle mb-0 mt-2">Découvrez nos produits les plus appréciés pour leurs bienfaits et leur goût unique.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-link-arrow d-none d-md-inline-flex">
                Voir tout <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $index => $product)
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 80 }}">
                <div class="product-card-v2 hover-scale">
                    <div class="product-v2-image img-zoom-hover">
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

        <div class="text-center mt-5">
            <a href="{{ route('shop.index') }}" class="btn-cta-outline">
                Découvrir toute la boutique <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- =============================================
     APPLICATIONS — What Can You Print?
     ============================================= --}}
<section class="section-py bg-dark-gradient position-relative overflow-hidden py-5">
    {{-- Particles removed --}}
    <div class="container">
        <div class="section-header section-header-light" data-aos="fade-up">
            <span class="section-eyebrow eyebrow-light">Notre Héritage</span>
            <h2 class="section-title text-white">Le Savoir-faire des Montagnes</h2>
            <p class="section-subtitle" style="color: rgba(255,255,255,.65);">Une tradition ancestrale perpétuée par notre coopérative</p>
        </div>
        <div class="row g-3 mt-2">
            @php
            $apps = [
                ['icon'=>'fa-mountain',      'title'=>'Origine Certifiée', 'desc'=>'Tous nos produits proviennent exclusivement des montagnes de l\'Atlas.'],
                ['icon'=>'fa-hand-holding-heart', 'title'=>'Production Artisanale', 'desc'=>'Récolte et transformation manuelle pour préserver les nutriments.'],
                ['icon'=>'fa-leaf',          'title'=>'Respect Nature',    'desc'=>'Pratiques agricoles éco-responsables et durables.'],
                ['icon'=>'fa-shield-alt',    'title'=>'Qualité Supérieure', 'desc'=>'Analyses régulières pour garantir la pureté de nos miels et huiles.'],
                ['icon'=>'fa-users',         'title'=>'Impact Social',      'desc'=>'Soutien direct aux familles et artisans de la région d\'Azilal.'],
                ['icon'=>'fa-vial',          'title'=>'Richesse Nutritionnelle', 'desc'=>'Des produits denses en vitamines et minéraux, sans additifs.'],
            ];
            @endphp
            @foreach($apps as $i => $app)
            <div class="col-6 col-md-4" data-aos="zoom-in" data-aos-delay="{{ ($i % 3) * 80 }}">
                <div class="app-card glass-tilt">
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
     HOW IT WORKS — 4-Step Process
     ============================================= --}}
<section class="section-py bg-surface">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-eyebrow">Notre Processus</span>
            <h2 class="section-title">De la ruche à votre table</h2>
            <p class="section-subtitle">Chaque produit suit un parcours rigoureux pour garantir sa qualité et son authenticité.</p>
        </div>

        <div class="process-track" data-aos="fade-up" data-aos-delay="100">
            <div class="process-line"></div>
            <div class="row g-4 position-relative">
                @php
                $steps = [
                    ['num'=>'01', 'icon'=>'fa-mountain', 'title'=>'Récolte en Altitude', 'desc'=>'Nos membres récoltent les produits au cœur de l\'Atlas, loin de toute pollution.'],
                    ['num'=>'02', 'icon'=>'fa-flask',    'title'=>'Contrôle Qualité', 'desc'=>'Chaque lot est testé pour vérifier sa pureté et son absence d\'additifs.'],
                    ['num'=>'03', 'icon'=>'fa-box',      'title'=>'Conditionnement', 'desc'=>'Mise en pot manuelle dans le respect des normes d\'hygiène les plus strictes.'],
                    ['num'=>'04', 'icon'=>'fa-truck',    'title'=>'Livraison Directe', 'desc'=>'Nous expédions vos produits dans tout le Maroc, de notre coopérative à chez vous.'],
                ];
                @endphp
                @foreach($steps as $i => $step)
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                    <div class="process-step{{ $i == 1 ? ' process-step-accent' : '' }}">
                        <div class="process-num">{{ $step['num'] }}</div>
                        <div class="process-icon-wrap">
                            <i class="fas {{ $step['icon'] }}"></i>
                        </div>
                        <h5 class="process-title">{{ $step['title'] }}</h5>
                        <p class="process-desc">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     WHY CHOOSE US — Expert USPs
     ============================================= --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <span class="section-eyebrow">Pourquoi nous</span>
                <h2 class="section-title">Engagés pour votre bien-être</h2>
                <p class="section-subtitle text-start">
                    Nous ne vendons pas juste des produits — nous partageons une passion pour notre terre et un engagement pour votre santé.
                    Commerce équitable, qualité premium et authenticité garantie.
                </p>
                <a href="{{ route('shop.index') }}" class="btn-cta-primary btn-magnetic mt-3">
                    Voir notre catalogue <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-7" data-aos="fade-left" data-aos-delay="100">
                <div class="row g-3">
                    @php
                    $usps = [
                        ['icon'=>'fa-certificate',   'color'=>'#00b878', 'title'=>'100% Organique',      'desc'=>'Aucun produit chimique, insecticide ou additif durant tout le processus.'],
                        ['icon'=>'fa-handshake',     'color'=>'#cc6600', 'title'=>'Commerce Équitable',  'desc'=>'Soutien direct aux petits producteurs et coopératives locales d\'Azilal.'],
                        ['icon'=>'fa-shield-virus',  'color'=>'#f59e0b', 'title'=>'Immunité Naturelle',  'desc'=>'Nos miels sont sélectionnés pour leurs vertus thérapeutiques exceptionnelles.'],
                        ['icon'=>'fa-mountain',      'color'=>'#00b878', 'title'=>'Direct Montagne',      'desc'=>'Vente directe de la coopérative, garantissant fraîcheur et prix juste.'],
                        ['icon'=>'fa-truck-loading', 'color'=>'#8b5cf6', 'title'=>'Livraison Soignée',   'desc'=>'Emballage premium pour préserver l\'intégrité de vos produits du terroir.'],
                        ['icon'=>'fa-award',         'color'=>'#06b6d4', 'title'=>'Produit Certifié',    'desc'=>'Conformité totale avec les normes sanitaires et de qualité de l\'ONSSA.'],
                    ];
                    @endphp
                    @foreach($usps as $i => $u)
                    <div class="col-6" data-aos="zoom-in" data-aos-delay="{{ $i * 60 }}">
                        <div class="usp-mini-card glass-tilt">
                            <div class="usp-mini-icon" style="background: {{ $u['color'] }}20; color: {{ $u['color'] }};"><i class="fas {{ $u['icon'] }}"></i></div>
                            <h6 class="usp-mini-title">{{ $u['title'] }}</h6>
                            <p class="usp-mini-desc">{{ $u['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     PROMO BANNER — Quote CTA
     ============================================= --}}
<section class="quote-banner" data-aos="fade-up">
    <div class="quote-banner-bg"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="quote-eyebrow">
                    <i class="fab fa-whatsapp me-2"></i>Conseil & Commande Rapide
                </span>
                <h2 class="quote-title">Besoin d'un conseil personnalisé ?</h2>
                <p class="quote-sub">Nos experts vous guident dans le choix des produits adaptés à vos besoins de santé. Miel, huiles ou infusions — trouvez le remède idéal.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="quote-actions">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('social_whatsapp', '')) }}?text={{ urlencode('Bonjour, j\'ai besoin d\'un conseil sur vos produits naturels Ait Oumdis.') }}" class="btn-quote-primary btn-magnetic">
                        <i class="fab fa-whatsapp me-2"></i>Contactez un expert
                    </a>
                    <a href="tel:{{ setting('company_phone', '+212600000000') }}" class="btn-quote-phone">
                        <i class="fas fa-phone-alt"></i>
                        <div class="btn-quote-text">
                            <small>Appelez-nous</small>
                            <strong>{{ setting('company_phone', '+212 6XX XX XX XX') }}</strong>
                        </div>
                    </a>
                    <a href="mailto:{{ setting('company_email', 'contact@speedprint.ma') }}" class="btn-quote-email">
                        <i class="fas fa-envelope"></i> Envoyer un email
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     BRANDS STRIP
     ============================================= --}}
<section class="brands-section">
    <div class="container">
        <p class="brands-label">Nos régions & spécialités</p>
        <div class="brands-ticker">
            @php
            $brands = ['Azilal', 'Atlas Mountains', 'Dades', 'Ourika', 'Miel Pur', 'Huile Argan', 'Safran', 'Herbes Bio'];
            @endphp
            @foreach($brands as $brand)
            <div class="brand-chip">
                <i class="fas fa-print me-2 opacity-50"></i>{{ $brand }}
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
            <h2 class="section-title">Ils nous font confiance</h2>
            <p class="section-subtitle">Découvrez les témoignages de ceux qui ont intégré nos produits dans leur quotidien.</p>
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
                <p class="section-subtitle text-start">Tout ce que vous devez savoir sur la qualité de nos produits du terroir.</p>
                <a href="tel:{{ setting('company_phone', '+212600000000') }}" class="btn-cta-outline mt-3">
                    <i class="fas fa-phone-alt me-2"></i>Nous appeler
                </a>
            </div>
            <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
                <div class="faq-list">
                    @php
                    $faqs = [
                        ['q'=>'Comment vérifier l\'authenticité de votre miel ?',
                         'a'=>'Chaque pot possède un numéro de lot permettant la traçabilité. Nos miels cristallisent naturellement, ce qui est un signe de pureté. Nous fournissons également les certificats d\'analyse sur demande.'],
                        ['q'=>'D\'où proviennent vos produits ?',
                         'a'=>'Tous nos produits sont récoltés dans la province d\'Azilal et les régions montagneuses environnantes par les membres de notre coopérative.'],
                        ['q'=>'Peut-on commander de l\'étranger ?',
                         'a'=>'Oui, nous expédions à l\'international. Les frais de port et les délais varient selon la destination. Contactez-nous pour une estimation.'],
                        ['q'=>'Vos huiles sont-elles bio ?',
                         'a'=>'Oui, nos huiles (Argan, Figue de Barbarie) sont extraites à froid de fruits sauvages ou cultivés sans pesticides, certifiant leur caractère bio.'],
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
