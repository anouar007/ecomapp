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
                    HMC — Votre univers douceur
                </div>

                <h1 class="display-hero">
                    Parce que votre<br>
                    <span class="text-gradient-primary">confort est féminin</span><br>
                    ✨
                </h1>

                <p class="hero-lead">
                    Découvrez notre collection raffinée de <strong>Pyjamas</strong> élégants, articles de <strong>Décoration</strong> chaleureux, et produits de <strong>Soins</strong> de beauté pour sublimer votre quotidien.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('shop.index') }}" class="btn-hero-primary">
                        <i class="fas fa-heart"></i>
                        <span>Découvrir la collection</span>
                    </a>
                    <a href="https://wa.me/212680631919" target="_blank" class="btn-hero-ghost">
                        <i class="fab fa-whatsapp"></i>
                        <span>Commandez par message</span>
                    </a>
                </div>

                {{-- Trust pills --}}
                <div class="hero-trust-pills">
                    <div class="trust-pill"><i class="fas fa-truck-pickup"></i> Livraison toutes villes</div>
                    <div class="trust-pill"><i class="fas fa-gem"></i> Qualité premium</div>
                    <div class="trust-pill"><i class="fas fa-box-open"></i> Emballage soigné</div>
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center" data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
                <div class="hero-image-stack">
                    {{-- Main image --}}
                    <div class="hero-img-main">
                        @if(isset($heroSlides) && $heroSlides->count() > 0)
                            <img src="{{ $heroSlides[0]->image_url ?? Storage::url($heroSlides[0]->main_image) }}" alt="HM Collection Store" class="img-fluid">
                        @else
                            <img src="{{ asset('images/hmc_hero.png') }}" alt="Pyjamas et Soins HM Collection" class="img-fluid">
                        @endif
                    </div>

                    {{-- Floating cards --}}
                    <div class="hero-float-card hero-float-top">
                        <div class="float-icon-wrap"><i class="fas fa-moon"></i></div>
                        <div>
                            <div class="float-label">Nuits douces</div>
                            <div class="float-value">Pyjamas Soie & Coton</div>
                        </div>
                    </div>
                    <div class="hero-float-card hero-float-bottom">
                        <div class="float-icon-wrap float-icon-green"><i class="fas fa-spa"></i></div>
                        <div>
                            <div class="float-label">Bien-être</div>
                            <div class="float-value">Soins Naturels</div>
                        </div>
                    </div>
                    <div class="hero-float-badge">
                        <i class="fas fa-star me-1"></i>Nouveautés Disponibles
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom wave separator --}}
    <div class="hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z" fill="#fdf6f7"/></svg>
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
                    <div class="metric-icon"><i class="fas fa-box-heart"></i></div>
                    <div class="metric-number">Tous<span> types</span></div>
                    <div class="metric-label">De morphologies</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item">
                    <div class="metric-icon"><i class="fas fa-truck"></i></div>
                    <div class="metric-number">24h<span>-48h</span></div>
                    <div class="metric-label">Livraison rapide</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item">
                    <div class="metric-icon"><i class="fas fa-star"></i></div>
                    <div class="metric-number">100<span>%</span></div>
                    <div class="metric-label">Qualité garantie</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-item border-end-0">
                    <div class="metric-icon"><i class="fab fa-whatsapp"></i></div>
                    <div class="metric-number">7<span>j/7</span></div>
                    <div class="metric-label">Support & Commandes</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     CATEGORIES — HMC Collections
     ============================================= --}}
<section id="categories" class="section-py bg-surface">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-eyebrow">Nos Collections</span>
            <h2 class="section-title">L'univers HM Collection</h2>
            <p class="section-subtitle">Découvrez nos gammes conçues pour votre confort et bien-être, de jour comme de nuit.</p>
        </div>

        @if($allCategories->count() > 0)
        <div class="row g-4 align-items-center justify-content-center">
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
                            @php $catIcons = ['fa-bed','fa-spa','fa-couch','fa-gem']; @endphp
                            <div class="cat-icon-placeholder cat-icon-{{ $index }}">
                                <i class="fas {{ $catIcons[$index % 4] }}"></i>
                            </div>
                        @endif
                    </div>
                    <div class="cat-card-body">
                        <h3 class="cat-card-name">{{ $category->name }}</h3>
                        <span class="cat-card-count">{{ $category->products_count ?? $category->products()->count() }} produits</span>
                        <div class="cat-card-arrow"><i class="fas fa-chevron-right"></i></div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        {{-- Fallback static lifestyle categories --}}
        @php
        $staticCats = [
            ['icon'=>'fa-bed',     'color'=>'#c97b8a', 'bg'=>'rgba(201,123,138,.12)',  'name'=>'Pyjamas', 'sub'=>'Chic & Confortables'],
            ['icon'=>'fa-couch',   'color'=>'#b07aba', 'bg'=>'rgba(176,122,186,.12)',  'name'=>'Décoration', 'sub'=>'Bougies & Intérieurs'],
            ['icon'=>'fa-spa',     'color'=>'#e8a87c', 'bg'=>'rgba(232,168,124,.12)',  'name'=>'Soins',    'sub'=>'Sérums & Beauté'],
            ['icon'=>'fa-gift',    'color'=>'#e8799a', 'bg'=>'rgba(232,121,154,.12)',  'name'=>'Packs & Cadeaux','sub'=>'L\'idée parfaite'],
        ];
        @endphp
        <div class="row g-4 justify-content-center">
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
                        <div class="cat-card-arrow"><i class="fas fa-chevron-right"></i></div>
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
                <span class="section-eyebrow">Coups de cœur</span>
                <h2 class="section-title mb-0">Nos pépites du moment</h2>
                <p class="section-subtitle mb-0 mt-2">Les vêtements et accessoires préférés de nos clientes</p>
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
                            <a href="{{ route('shop.show', $product->id) }}" class="btn-overlay-icon" title="Voir l'article">
                                <i class="fas fa-heart"></i>
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
                        <div class="out-of-stock-label"><i class="fas fa-exclamation-circle me-1"></i>En réapprovisionnement</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('shop.index') }}" class="btn-cta-outline">
                Explorer toute la boutique <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- =============================================
     APPLICATIONS — Moments de vie
     ============================================= --}}
<section class="section-py bg-dark-gradient">
    <div class="container">
        <div class="section-header section-header-light" data-aos="fade-up">
            <span class="section-eyebrow eyebrow-light">Inspirations</span>
            <h2 class="section-title text-white">Vos moments de détente</h2>
            <p class="section-subtitle" style="color: rgba(255,255,255,.65);">L'élégance s'invite dans chacune de vos routines</p>
        </div>
        <div class="row g-3 mt-2">
            @php
            $apps = [
                ['icon'=>'fa-mug-hot',       'title'=>'Dimanche cocooning',   'desc'=>'Le confort ultime pour vos journées détente à la maison en pyjama soie.'],
                ['icon'=>'fa-moon',          'title'=>'Nuits réparatrices',   'desc'=>'Des matières douces et fluides pour un sommeil profond et élégant.'],
                ['icon'=>'fa-spray-can',     'title'=>'Routine beauté',       'desc'=>'Prenez soin de vous avec nos sérums et crèmes de qualité premium.'],
                ['icon'=>'fa-home',          'title'=>'Atmosphère zen',       'desc'=>'Bougies et diffuseurs pour créer une ambiance chaleureuse et apaisante.'],
                ['icon'=>'fa-gifts',         'title'=>'Le cadeau idéal',      'desc'=>'Packs soins et pyjamas emballés avec soin pour offrir du bonheur.'],
                ['icon'=>'fa-camera',        'title'=>'Soirée pyjama',        'desc'=>'Des ensembles matchy pour des moments inoubliables entre amies.'],
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
            <span class="section-eyebrow">Délai express</span>
            <h2 class="section-title">Comment commander ?</h2>
            <p class="section-subtitle">Un processus simple par message pour un service 100% personnalisé</p>
        </div>

        <div class="process-track" data-aos="fade-up" data-aos-delay="100">
            <div class="process-line"></div>
            <div class="row g-4 position-relative">
                @php
                $steps = [
                    ['num'=>'01', 'icon'=>'fa-heart', 'title'=>'Choisissez', 'desc'=>'Explorez nos collections Pyjamas, Déco ou Soins sur le site et faites votre choix.'],
                    ['num'=>'02', 'icon'=>'fa-comment-dots',  'title'=>'Contactez-nous', 'desc'=>'Envoyez-nous un message WhatsApp au 0680631919 avec vos de demande de tailles ou infos.'],
                    ['num'=>'03', 'icon'=>'fa-box',  'title'=>'Confirmation', 'desc'=>'Notre équipe valide avec vous la taille, la couleur et les délais de livraison en temps réel.'],
                    ['num'=>'04', 'icon'=>'fa-gift','title'=>'Livraison & Plaisir', 'desc'=>'Recevez votre commande joliment emballée chez vous, paiement à la livraison.'],
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
     WHY CHOOSE US — HMC USPs
     ============================================= --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <span class="section-eyebrow">Notre promesse</span>
                <h2 class="section-title">L'excellence au service de votre bien-être</h2>
                <p class="section-subtitle text-start">
                    HM Collection sélectionne avec amour chaque pyjama, crème et accessoire déco. Notre priorité : la qualité des matières, des finitions parfaites, et un service irréprochable.
                </p>
                <a href="{{ route('shop.index') }}" class="btn-cta-primary mt-3">
                    Voir la nouvelle collection <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-7" data-aos="fade-left" data-aos-delay="100">
                <div class="row g-3">
                    @php
                    $usps = [
                        ['icon'=>'fa-gem',           'color'=>'#c97b8a', 'title'=>'Qualité premium',      'desc'=>'Soie, satin, et coton doux. Nos tissus sont rigoureusement sélectionnés pour durer.'],
                        ['icon'=>'fa-boxes',         'color'=>'#b07aba', 'title'=>'Collections uniques',  'desc'=>'Des coupes raffinées et des collections qui se renouvellent constamment suivant les tendances.'],
                        ['icon'=>'fa-shipping-fast', 'color'=>'#e8a87c', 'title'=>'Livraison express',    'desc'=>'Recevez votre colis de bonheur en 24h/48h dans toutes les villes du Maroc.'],
                        ['icon'=>'fa-leaf',          'color'=>'#e8799a', 'title'=>'Soins naturels',       'desc'=>'Nos gammes de soin visage et corps privilégient des ingrédients sûrs et testés dermatologiquement.'],
                        ['icon'=>'fa-gift',          'color'=>'#7c5cbf', 'title'=>'Emballage cadeau',     'desc'=>'Nous emballons vos cadeaux avec un soin particulier, prêts à être offerts.'],
                        ['icon'=>'fa-headset',       'color'=>'#f0a8c2', 'title'=>'Service client 7j/7',  'desc'=>'Une équipe à l\'écoute pour vous conseiller sur les tailles ou suivre vos colis.'],
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
     PROMO BANNER — WhatsApp CTA
     ============================================= --}}
<section class="quote-banner" data-aos="fade-up">
    <div class="quote-banner-bg"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="quote-eyebrow">
                    <i class="fab fa-whatsapp me-2"></i>Commande direct depuis WhatsApp
                </span>
                <h2 class="quote-title">Vous avez un coup de cœur ?</h2>
                <p class="quote-sub">Commandez vos pyjamas et articles favoris dès maintenant par message. Prenez un screen de l'article et envoyez le nous sur WhatsApp !</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="quote-actions">
                    <a href="https://wa.me/212680631919" target="_blank" class="btn-quote-phone">
                        <i class="fab fa-whatsapp" style="color: #25d366;"></i>
                        <div class="btn-quote-text">
                            <small>Réponse instantanée</small>
                            <strong>06 80 63 19 19</strong>
                        </div>
                    </a>
                    <a href="https://instagram.com" target="_blank" class="btn-quote-email">
                        <i class="fab fa-instagram"></i> Suivez-nous sur Instagram
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
        <p class="brands-label">Collections HM Collection</p>
        <div class="brands-ticker">
            @php
            $brands = ['Pyjamas Soie', 'Pyjamas Coton', 'Sérums Visage', 'Soins Corps', 'Bougies Parfumées', 'Déco Minimaliste', 'Packs Cadeaux'];
            @endphp
            @foreach($brands as $brand)
            <div class="brand-chip">
                <i class="fas fa-heart me-2 opacity-50" style="color:var(--accent);"></i>{{ $brand }}
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
            <span class="section-eyebrow">Avis clientes</span>
            <h2 class="section-title">Ce que disent nos clientes</h2>
            <p class="section-subtitle">Votre satisfaction est notre plus belle réussite. Découvrez leurs retours.</p>
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
                            <div class="review-role">Cliente vérifiée</div>
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
                <p class="section-subtitle text-start">Vous avez d'autres questions ? Notre équipe répond très vite sur WhatsApp.</p>
                <a href="https://wa.me/212680631919" target="_blank" class="btn-cta-outline mt-3">
                    <i class="fab fa-whatsapp me-2"></i>Nous écrire
                </a>
            </div>
            <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
                <div class="faq-list">
                    @php
                    $faqs = [
                        ['q'=>'Comment choisir ma taille de pyjama ?',
                         'a'=>'Nos coupes taillent normalement. Vous pouvez consulter notre guide des tailles sur WhatsApp ou nous communiquer vos mensurations pour vous orienter vers la taille parfaite.'],
                        ['q'=>'Quels sont vos délais de livraison ?',
                         'a'=>'Toutes les commandes sont expédiées le jour même de la confirmation. Comptez 24h à 48h maximum partout au Maroc.'],
                        ['q'=>'Puis-je échanger un article ?',
                         'a'=>'Absolument. Si la taille ne convient pas, l\'échange est possible sous 3 jours (sous conditions d\'essayage). Les frais de retour sont à la charge du client.'],
                        ['q'=>'Quels sont les modes de paiement ?',
                         'a'=>'Le paiement s\'effectue en toute sécurité à la livraison avec notre livreur partenaire, ou par virement bancaire sur demande.'],
                        ['q'=>'Comment commander plusieurs articles en pack cadeau ?',
                         'a'=>'Il suffit de nous envoyer la liste des articles sur WhatsApp. Nous composerons votre box sur-mesure et vous enverrons une photo du coffret avant l\'expédition !'],
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

@if($waLink || true)
<a href="https://wa.me/212680631919?text=Bonjour%2C%20je%20suis%20intéressée%20par%20vos%20collections." 
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
