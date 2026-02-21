@extends('layouts.frontend')

@section('content')

{{-- =============================================
     HERO — Full-Width Cinematic Hero
     ============================================= --}}
<section class="print-hero-v2">
    <div class="print-hero-backdrop"></div>
    <div class="print-hero-grid-overlay"></div>

    <div class="container position-relative py-0">
        <div class="row align-items-center min-vh-90">
            <div class="col-lg-6 pe-lg-5" data-aos="fade-right" data-aos-duration="900">

                <div class="hero-eyebrow">
                    <span class="hero-eyebrow-dot"></span>
                    Spécialiste Solutions d'Impression
                </div>

                <h1 class="display-hero">
                    Équipez votre<br>
                    <span class="text-gradient-primary">atelier d'impression</span><br>
                    pro
                </h1>

                <p class="hero-lead">
                    Imprimantes grand format <strong>éco-solvant &amp; UV</strong>, traceurs de découpe,
                    encres certifiées et consommables — livrés partout au Maroc avec installation et formation.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('shop.index') }}" class="btn-hero-primary">
                        <i class="fas fa-print"></i>
                        <span>Voir les machines</span>
                    </a>
                    <a href="mailto:{{ setting('company_email', 'contact@speedprint.ma') }}" class="btn-hero-ghost">
                        <i class="fas fa-file-invoice"></i>
                        <span>Demander un devis</span>
                    </a>
                </div>

                {{-- Trust pills --}}
                <div class="hero-trust-pills">
                    <div class="trust-pill"><i class="fas fa-shipping-fast"></i> Livraison nationale</div>
                    <div class="trust-pill"><i class="fas fa-tools"></i> SAV technique</div>
                    <div class="trust-pill"><i class="fas fa-shield-alt"></i> Pièces d'origine</div>
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center" data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
                <div class="hero-image-stack">
                    {{-- Main image --}}
                    <div class="hero-img-main">
                        @if(isset($heroSlides) && $heroSlides->count() > 0)
                            <img src="{{ $heroSlides[0]->image_url ?? Storage::url($heroSlides[0]->main_image) }}" alt="Imprimante grand format" class="img-fluid">
                        @else
                            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&q=80&w=900" alt="Imprimante grand format éco-solvant" class="img-fluid">
                        @endif
                    </div>

                    {{-- Floating cards --}}
                    <div class="hero-float-card hero-float-top">
                        <div class="float-icon-wrap"><i class="fas fa-tachometer-alt"></i></div>
                        <div>
                            <div class="float-label">Vitesse max</div>
                            <div class="float-value">42 m²/h</div>
                        </div>
                    </div>
                    <div class="hero-float-card hero-float-bottom">
                        <div class="float-icon-wrap float-icon-green"><i class="fas fa-expand-arrows-alt"></i></div>
                        <div>
                            <div class="float-label">Largeur max</div>
                            <div class="float-value">3.2 mètres</div>
                        </div>
                    </div>
                    <div class="hero-float-badge">
                        <i class="fas fa-star me-1"></i>Résolution 2400 dpi
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom wave separator --}}
    <div class="hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z" fill="#ffffff"/></svg>
    </div>
</section>

{{-- =============================================
     KEY METRICS STRIP
     ============================================= --}}
<section class="metrics-strip">
    <div class="container">
        <div class="row g-0 metrics-row">
            <div class="col-6 col-md-3">
                <div class="metric-item">
                    <div class="metric-icon"><i class="fas fa-users"></i></div>
                    <div class="metric-number">500<span>+</span></div>
                    <div class="metric-label">Clients actifs</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item">
                    <div class="metric-icon"><i class="fas fa-print"></i></div>
                    <div class="metric-number">50<span>+</span></div>
                    <div class="metric-label">Modèles disponibles</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item">
                    <div class="metric-icon"><i class="fas fa-history"></i></div>
                    <div class="metric-number">15<span> ans</span></div>
                    <div class="metric-label">D'expérience</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item border-end-0">
                    <div class="metric-icon"><i class="fas fa-headset"></i></div>
                    <div class="metric-number">24<span>/7</span></div>
                    <div class="metric-label">Support technique</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     CATEGORIES — Print Industry
     ============================================= --}}
<section id="categories" class="section-py bg-surface">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-eyebrow">Notre offre</span>
            <h2 class="section-title">Tout pour votre production</h2>
            <p class="section-subtitle">Des équipements professionnels, des consommables d'origine, et un support expert — en un seul endroit</p>
        </div>

        @if($allCategories->count() > 0)
        <div class="row g-4">
            @foreach($allCategories->take(4) as $index => $category)
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="cat-card-v2">
                    <div class="cat-card-img">
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
            ['icon'=>'fa-print',     'color'=>'#e94560', 'bg'=>'rgba(233,69,96,.12)',    'name'=>'Imprimantes Éco-Solvant', 'sub'=>'Grand format · UV · Sublimation'],
            ['icon'=>'fa-cut',       'color'=>'#0077ff', 'bg'=>'rgba(0,119,255,.12)',    'name'=>'Traceurs de découpe',      'sub'=>'Vinyle · Flex · Autocollant'],
            ['icon'=>'fa-fill-drip', 'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,.12)',  'name'=>'Encres & consommables',    'sub'=>'Toutes marques · Origine'],
            ['icon'=>'fa-scroll',    'color'=>'#10b981', 'bg'=>'rgba(16,185,129,.12)',   'name'=>'Médias & supports',        'sub'=>'Bâche · PVC · Bannière'],
        ];
        @endphp
        <div class="row g-4">
            @foreach($staticCats as $i => $cat)
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                <a href="{{ route('shop.index') }}" class="cat-card-v2">
                    <div class="cat-card-img">
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
                <h2 class="section-title mb-0">Nos machines phares</h2>
                <p class="section-subtitle mb-0 mt-2">Équipements testés, certifiés et plébiscités par nos ateliers partenaires</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-link-arrow d-none d-md-inline-flex">
                Voir tout <i class="fas fa-arrow-right ms-2"></i>
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

        <div class="text-center mt-5">
            <a href="{{ route('shop.index') }}" class="btn-cta-outline">
                Voir tous nos équipements <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- =============================================
     APPLICATIONS — What Can You Print?
     ============================================= --}}
<section class="section-py bg-dark-gradient">
    <div class="container">
        <div class="section-header section-header-light" data-aos="fade-up">
            <span class="section-eyebrow eyebrow-light">Applications</span>
            <h2 class="section-title text-white">Ce que vous pouvez réaliser</h2>
            <p class="section-subtitle" style="color: rgba(255,255,255,.65);">Des machines polyvalentes pour tous les secteurs de l'impression</p>
        </div>
        <div class="row g-3 mt-2">
            @php
            $apps = [
                ['icon'=>'fa-store',         'title'=>'Enseignes & signalétique', 'desc'=>'Bannières, roll-ups, panneaux PVC, signalétique intérieure/extérieure'],
                ['icon'=>'fa-car',           'title'=>'Covering véhicule',        'desc'=>'Films adhésifs, covering complet, décoration de flotte'],
                ['icon'=>'fa-tshirt',        'title'=>'Textile & habillement',    'desc'=>'Impression sur tissu, flex thermocollant, sublimation textile'],
                ['icon'=>'fa-building',      'title'=>'Décoration intérieure',    'desc'=>'Papier peint, toile tendue, stickers déco, vitrophanie'],
                ['icon'=>'fa-image',         'title'=>'Photo & fine art',         'desc'=>'Tirages haute résolution sur papier, toile, aluminium'],
                ['icon'=>'fa-box-open',      'title'=>'Packaging & étiquettes',   'desc'=>'Étiquettes autocollantes, emballages personnalisés, codage couleur'],
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
     HOW IT WORKS — 4-Step Process
     ============================================= --}}
<section class="section-py bg-surface">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-eyebrow">Processus</span>
            <h2 class="section-title">Comment ça marche ?</h2>
            <p class="section-subtitle">De la commande à la mise en production, nous vous accompagnons à chaque étape</p>
        </div>

        <div class="process-track" data-aos="fade-up" data-aos-delay="100">
            <div class="process-line"></div>
            <div class="row g-4 position-relative">
                @php
                $steps = [
                    ['num'=>'01', 'icon'=>'fa-search', 'title'=>'Choisissez votre machine', 'desc'=>'Consultez notre catalogue, comparez les modèles ou appelez nos experts pour un conseil personnalisé.'],
                    ['num'=>'02', 'icon'=>'fa-file-invoice',  'title'=>'Recevez votre devis', 'desc'=>'Devis détaillé sous 24h incluant machine, installation, formation et garantie.'],
                    ['num'=>'03', 'icon'=>'fa-truck-moving',  'title'=>'Livraison & installation', 'desc'=>'Nos techniciens livrent, installent et configurent votre équipement sur site.'],
                    ['num'=>'04', 'icon'=>'fa-graduation-cap','title'=>'Formation & suivi', 'desc'=>'Formation opérateur incluse. SAV réactif et stock de pièces en permanence.'],
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
                <h2 class="section-title">Votre partenaire de confiance depuis 15 ans</h2>
                <p class="section-subtitle text-start">
                    Nous ne vendons pas juste des machines — nous construisons des relations à long terme avec nos clients. 
                    Stock permanent, SAV réactif, et expertise technique inégalée.
                </p>
                <a href="{{ route('shop.index') }}" class="btn-cta-primary mt-3">
                    Découvrir notre catalogue <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-7" data-aos="fade-left" data-aos-delay="100">
                <div class="row g-3">
                    @php
                    $usps = [
                        ['icon'=>'fa-user-tie',      'color'=>'#e94560', 'title'=>'Experts certifiés',      'desc'=>'Nos conseillers maîtrisent chaque technologie: éco-solvant, UV, sublimation, découpe.'],
                        ['icon'=>'fa-warehouse',     'color'=>'#0077ff', 'title'=>'Stock permanent',         'desc'=>'Pièces de rechange, têtes d\'impression, encres et médias disponibles immédiatement.'],
                        ['icon'=>'fa-tools',         'color'=>'#f59e0b', 'title'=>'SAV en 24h',              'desc'=>'Intervention sur site sous 24h dans les grandes villes, téléassistance immédiate.'],
                        ['icon'=>'fa-graduation-cap','color'=>'#10b981', 'title'=>'Formation incluse',       'desc'=>'Chaque machine est livrée avec une formation opérateur complète pour votre équipe.'],
                        ['icon'=>'fa-tags',          'color'=>'#8b5cf6', 'title'=>'Meilleurs prix garantis', 'desc'=>'Tarifs compétitifs, financement disponible, devis personnalisé selon votre volume.'],
                        ['icon'=>'fa-leaf',          'color'=>'#06b6d4', 'title'=>'Encres certifiées',       'desc'=>'Encres éco-solvant conformes aux normes environnementales, longue durée de vie.'],
                    ];
                    @endphp
                    @foreach($usps as $i => $u)
                    <div class="col-6" data-aos="zoom-in" data-aos-delay="{{ $i * 60 }}">
                        <div class="usp-mini-card">
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
                    <i class="fas fa-file-invoice me-2"></i>Devis gratuit & sans engagement
                </span>
                <h2 class="quote-title">Besoin d'un équipement sur mesure ?</h2>
                <p class="quote-sub">Dites-nous vos besoins (format, vitesse, budget) et nous vous proposons la machine idéale avec installation, formation et garantie.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="quote-actions">
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
        <p class="brands-label">Marques et équipements disponibles</p>
        <div class="brands-ticker">
            @php
            $brands = ['Epson', 'Roland', 'Mimaki', 'Mutoh', 'Graphtec', 'Summa', 'HP Latex', 'Flora', 'Allwin'];
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
                        ['q'=>'Quelle est la différence entre échosolvant et UV ?',
                         'a'=>'L\'éco-solvant utilise des encres à base de solvant doux, idéal pour les supports souples (bâche, vinyle). L\'UV imprime directement sur des supports rigides (bois, verre, métal) et sèche instantanément par lampe ultraviolet.'],
                        ['q'=>'Livrez-vous dans tout le Maroc ?',
                         'a'=>'Oui. Nous livrons partout au Maroc avec notre propre réseau logistique. Les grandes villes bénéficient d\'une livraison J+1 et d\'une installation sur site incluse.'],
                        ['q'=>'La formation est-elle incluse à l\'achat ?',
                         'a'=>'Oui, chaque machine est accompagnée d\'une formation opérateur gratuite (1 à 2 jours selon la complexité) réalisée sur votre lieu de production par nos techniciens certifiés.'],
                        ['q'=>'Proposez-vous des facilités de paiement ?',
                         'a'=>'Oui, nous proposons des solutions de financement personnalisées (paiement en plusieurs fois, leasing) selon votre situation. Contactez-nous pour un devis adapté à votre budget.'],
                        ['q'=>'Quels types d\'encres compatibles sont disponibles ?',
                         'a'=>'Nous stockons les encres d\'origine pour toutes les marques (Roland, Epson, Mimaki...) ainsi que des encres compatibles certifiées offrant un excellent rapport qualité/prix sans altérer la tête d\'impression.'],
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
