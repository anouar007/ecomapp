@extends('layouts.frontend')

@section('meta_title', setting('app_name', 'Full Frame House') . ' — Matériel Cinéma, Caméras 8K & Optiques Pro au Maroc')
@section('meta_description', 'Découvrez notre catalogue de caméras cinéma, boîtiers hybrides plein format, objectifs professionnels, stabilisateurs et éclairage studio au Maroc. Showroom Casablanca, garantie constructeur 2 ans et livraison express.')
@section('meta_keywords', 'caméra cinéma Maroc, Sony Alpha Casablanca, boîtier hybride plein format, objectifs photo Maroc, stabilisateur DJI Ronin, éclairage vidéo, micro sans fil, ' . setting('app_name', 'Full Frame House'))

@section('json_ld')
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "{{ setting('app_name', 'Full Frame House') }}",
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
    "name": "{{ setting('app_name', 'Full Frame House') }}",
    "url": "{{ url('/') }}",
    "logo": "{{ setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/logo.png') }}",
    @if(setting('company_phone'))
    "telephone": "{{ setting('company_phone') }}",
    @endif
    @if(setting('company_email'))
    "email": "{{ setting('company_email') }}",
    @endif
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "{{ setting('company_phone', '+212661987654') }}",
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
        "name": "Les caméras et objectifs bénéficient-ils d'une garantie constructeur officielle ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Absolument. Tout notre matériel (Sony, Canon, DJI, Blackmagic, Sigma) provient des circuits officiels et dispose d'une garantie constructeur de 1 à 2 ans avec service après-vente dédié."
        }
      },
      {
        "@type": "Question",
        "name": "Est-il possible de tester le matériel avant l'achat ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, notre showroom à Casablanca vous accueille sur rendez-vous pour prendre en main les boîtiers, tester les optiques et configurer vos rigs caméra en conditions réelles."
        }
      },
      {
        "@type": "Question",
        "name": "Quels sont les délais de livraison pour le Maroc ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Nous livrons en 24h à Casablanca, Rabat et Marrakech, et sous 48h partout ailleurs au Maroc via transporteur sécurisé avec suivi et assurance intégrale."
        }
      },
      {
        "@type": "Question",
        "name": "Délivrez-vous des factures avec ICE pour les entreprises et productions audiovisuelles ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, toutes nos commandes font l'objet d'une facture légale mentionnant l'ICE, le RC et l'IF, idéale pour votre comptabilité de société de production."
        }
      }
    ]
  }
]
</script>
@endsection

@section('content')

{{-- =============================================
     HERO — Cinematic Viewfinder HUD Slider
     ============================================= --}}
<section class="hero-slider-section">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            @php
            $cameraSlides = [
                [
                    'badge' => 'CINÉMA & BROADCAST PRO',
                    'title' => 'L\'Excellence Cinématographique & Hybride',
                    'desc'  => 'Caméras cinéma 6K/8K, capteurs plein format et rigs professionnels conçus pour les réalisateurs et créateurs de contenu les plus exigeants.',
                    'img'   => asset('images/camera/hero_cinema_rig.jpg'),
                    'tag1'  => 'RAW 8K',
                    'tag2'  => '120 FPS',
                    'tag3'  => 'DUAL ISO',
                ],
                [
                    'badge' => 'OPTIQUES D\'EXCEPTION',
                    'title' => 'Objectifs Prime & Zooms Lumineux',
                    'desc'  => 'Optiques anamorphiques, focales fixes f/1.2 et zooms G-Master pour sculpter la lumière avec un piqué chirurgical et un bokeh cinématographique.',
                    'img'   => asset('images/camera/hero_lens_optics.jpg'),
                    'tag1'  => 'f/1.2 APERTURE',
                    'tag2'  => 'NANO AR',
                    'tag3'  => 'E-MOUNT / RF',
                ],
                [
                    'badge' => 'ÉCOSYSTÈME TOURNAGE',
                    'title' => 'Stabilisateurs, Éclairage & Audio Studio',
                    'desc'  => 'Gimbals 3 axes DJI RS 4 Pro, projecteurs LED COB Aputure et microphones HF 32-bit float pour transformer chaque prise de vue en chef-d\'œuvre.',
                    'img'   => asset('images/camera/hero_studio_crew.jpg'),
                    'tag1'  => '32-BIT FLOAT',
                    'tag2'  => 'CARBON RIG',
                    'tag3'  => 'WIRELESS VIDEO',
                ],
            ];
            @endphp

            @foreach($cameraSlides as $slide)
            <div class="swiper-slide">
                <div class="hero-slide" style="background-image: url('{{ $slide['img'] }}');">
                    <div class="hero-slide-overlay"></div>

                    {{-- Viewfinder HUD Overlays --}}
                    <div class="hud-reticle-top-left"></div>
                    <div class="hud-reticle-top-right"></div>
                    <div class="hud-reticle-bottom-left"></div>
                    <div class="hud-reticle-bottom-right"></div>

                    <div class="hud-status-strip">
                        <span class="hud-badge rec"><span class="tally-dot"></span> REC ●</span>
                        <span class="hud-badge">{{ $slide['tag1'] }}</span>
                        <span class="hud-badge">{{ $slide['tag2'] }}</span>
                        <span class="hud-badge">{{ $slide['tag3'] }}</span>
                    </div>

                    <div class="container position-relative" style="z-index: 4;">
                        <div class="row align-items-center">
                            <div class="col-lg-8 col-xl-7">
                                <div class="hero-slide-content">
                                    <span class="hero-badge-tag">
                                        <i class="fas fa-video"></i>
                                        {{ $slide['badge'] }}
                                    </span>
                                    @if($loop->first)
                                    <h1 class="hero-slide-title">{{ $slide['title'] }}</h1>
                                    @else
                                    <h2 class="hero-slide-title">{{ $slide['title'] }}</h2>
                                    @endif
                                    <p class="hero-slide-desc">{{ $slide['desc'] }}</p>

                                    <div class="hero-actions-wrap d-flex align-items-center flex-wrap gap-2">
                                        <a href="{{ route('shop.index') }}" class="hero-btn-primary">
                                            <i class="fas fa-camera"></i>
                                            <span>Explorer le Catalogue</span>
                                        </a>
                                        <a href="{{ route('shop.index', ['category' => 'cameras-hybrides']) }}" class="hero-btn-secondary">
                                            <i class="fas fa-play-circle"></i>
                                            <span>Boîtiers Pro</span>
                                        </a>
                                    </div>

                                    <div class="hero-trust-bar">
                                        <div class="hero-trust-pill">
                                            <i class="fas fa-shield-halved"></i> Garantie 2 Ans
                                        </div>
                                        <div class="hero-trust-pill">
                                            <i class="fas fa-truck-fast"></i> Livraison Sécurisée Maroc
                                        </div>
                                        <div class="hero-trust-pill">
                                            <i class="fas fa-file-invoice"></i> Facturation ICE Officielle
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Custom Pagination -->
        <div class="hero-pagination-wrap mt-3">
            <div class="container text-center">
                <div class="swiper-pagination hero-dots d-inline-block"></div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     TRUST BAR — Camera & Audiovisual Perks
     ============================================= --}}
<section class="trust-bar-section">
    <div class="container">
        <div class="trust-card-grid">
            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="trust-item-camera">
                        <div class="trust-icon-box">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <div class="trust-title">Garantie 2 Ans</div>
                            <div class="trust-desc">Produits 100% officiels certifiés</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-item-camera">
                        <div class="trust-icon-box">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <div>
                            <div class="trust-title">Livraison Sécurisée</div>
                            <div class="trust-desc">Expédition express 24/48h assurée</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-item-camera">
                        <div class="trust-icon-box">
                            <i class="fas fa-video"></i>
                        </div>
                        <div>
                            <div class="trust-title">Showroom & Démo</div>
                            <div class="trust-desc">Test en conditions réelles à Casa</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-item-camera">
                        <div class="trust-icon-box">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <div class="trust-title">Facturation & Devis</div>
                            <div class="trust-desc">Conforme ICE pour productions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     SHOP BY CATEGORY (Optics & Gear Grid)
     ============================================= --}}
<section id="categories" class="section-py">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="section-eyebrow-cine"><i class="fas fa-sliders"></i> Gamme Professionnelle</span>
                <h2 class="section-title mb-1">Explorer par Catégorie</h2>
                <p class="section-desc">Tout l'écosystème de prise de vue, de l'optique au stabilisateur</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-link-arrow d-none d-md-inline-flex">
                <span>Tous les équipements</span> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        @if($allCategories->count() > 0)
        <div class="row g-3">
            @foreach($allCategories->where('slug', '!=', 'general')->take(6) as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="cat-card-v2">
                    <div class="cat-card-img">
                        <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                    </div>
                    <div class="cat-card-body">
                        <h3 class="cat-card-name">{{ $category->name }}</h3>
                        <span class="cat-card-count">{{ $category->products_count ?? $category->products()->count() }} références</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- =============================================
     FEATURED PRODUCTS (Cine Titanium Cards)
     ============================================= --}}
<section id="featured" class="section-py bg-surface">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="section-eyebrow-cine"><i class="fas fa-star"></i> Sélection Studio</span>
                <h2 class="section-title mb-1">Équipements Phares</h2>
                <p class="section-desc">Boîtiers, optiques et accessoires plébiscités par les chefs opérateurs</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-link-arrow d-none d-md-inline-flex">
                <span>Voir tout le catalogue</span> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="row g-2 g-sm-3 g-lg-4">
            @foreach($featuredProducts as $index => $product)
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 80 }}">
                <div class="product-card-v2">
                    <div class="product-v2-image">
                        <a href="{{ route('shop.show', $product->id) }}" class="product-v2-img-link d-block w-100 h-100">
                            <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                        </a>

                        <div class="product-v2-badges">
                            @if($product->created_at && $product->created_at->diffInDays(now()) < 14)
                                <span class="badge-v2 badge-new">Nouveau</span>
                            @endif
                            @if($product->isOnSale())
                                <span class="badge-v2 badge-sale">-{{ $product->discount_percentage }}%</span>
                            @endif
                        </div>

                        <div class="product-v2-overlay">
                            @if($product->isInStock())
                            <button class="btn-overlay" onclick="addToCart({{ $product->id }})" title="Ajouter au panier">
                                <i class="fas fa-cart-plus"></i> <span>Ajouter</span>
                            </button>
                            @else
                            <span class="btn-overlay" style="background: rgba(255,255,255,0.2) !important; color:#fff !important; cursor:not-allowed;">
                                <i class="fas fa-clock"></i> Sur Commande
                            </span>
                            @endif
                            <a href="{{ route('shop.show', $product->id) }}" class="btn-overlay-icon" title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product-v2-body">
                        @if($product->productCategory)
                            <span class="product-v2-cat text-truncate">{{ $product->productCategory->name }}</span>
                        @endif
                        <h4 class="product-v2-name">
                            <a href="{{ route('shop.show', $product->id) }}" class="product-v2-name-link" title="{{ $product->name }}">
                                {{ Str::limit($product->name, 45) }}
                            </a>
                        </h4>
                        <div class="product-v2-rating">
                            @php $rating = round($product->reviews()->avg('rating') ?? 5); @endphp
                            <div class="stars-row">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star{{ $i < $rating ? '' : ' opacity-25' }}"></i>
                                @endfor
                            </div>
                            <span class="reviews-count">({{ $product->reviews()->count() > 0 ? $product->reviews()->count() : 5 }})</span>
                        </div>
                        <div class="product-v2-price">
                            @if($product->isOnSale())
                                <span class="price-sale text-nowrap">{{ $product->formatted_sale_price }}</span>
                                <span class="price-old text-nowrap">{{ $product->formatted_price }}</span>
                            @else
                                <span class="price-sale text-nowrap">{{ $product->formatted_price }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4 mt-md-5">
            <a href="{{ route('shop.index') }}" class="hero-btn-primary w-sm-auto">
                <i class="fas fa-th-large me-2"></i> Consulter Toutes les Références
            </a>
        </div>
    </div>
</section>

{{-- =============================================
     PROMOTIONAL CTA BANNER (Studio Project)
     ============================================= --}}
<section class="promo-cta-section">
    <div class="container">
        <div class="promo-cta-card">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="section-eyebrow-cine"><i class="fas fa-headset"></i> Support & Configuration Studio</span>
                    <h2 class="promo-cta-title">Vous préparez un tournage ou un projet vidéo ?</h2>
                    <p class="promo-cta-desc">
                        Nos techniciens et chefs opérateurs vous conseillent sur le choix des boîtiers, objectifs de cinéma, éclairages et rigs d'épaule selon votre budget. Devis entreprise sous 2h avec TVA et ICE.
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                    <div class="d-flex flex-column flex-sm-row justify-content-lg-end gap-2">
                        <a href="tel:{{ setting('company_phone', '+212661987654') }}" class="btn-hero-shop">
                            <i class="fas fa-phone-alt"></i> Appeler un Expert
                        </a>
                        <a href="mailto:{{ setting('company_email', 'contact@lumina-optics.ma') }}" class="btn-hero-outline">
                            <i class="fas fa-envelope"></i> Demander un Devis
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     OUR SERVICES — Camera Store Standards
     ============================================= --}}
<section class="section-py">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow-cine"><i class="fas fa-award"></i> Nos Engagements</span>
            <h2 class="section-title">Pourquoi Choisir {{ setting('app_name', 'Notre Boutique') }}</h2>
            <p class="section-desc">Un service pensé par des professionnels de l'image pour les professionnels</p>
        </div>
        <div class="row g-4">
            @php
            $cameraServices = [
                ['icon' => 'fa-certificate',  'title' => 'Matériel 100% Officiel',   'desc' => 'Produits neufs sous scellés certifiés constructeurs avec garantie légale de 2 ans.'],
                ['icon' => 'fa-truck-fast',   'title' => 'Livraison Express Maroc',   'desc' => 'Expédition sécurisée et assurée 24h sur Casablanca/Rabat, 48h dans tout le Maroc.'],
                ['icon' => 'fa-screwdriver-wrench', 'title' => 'Atelier & Réglage Rigs',   'desc' => 'Équilibrage de gimbals, mise à jour firmware et calibrage d\'objectifs offerts.'],
                ['icon' => 'fa-file-invoice-dollar', 'title' => 'Facturation & Devis Pro', 'desc' => 'Délivrance de devis et factures avec ICE pour entreprises et sociétés de production.'],
            ];
            @endphp
            @foreach($cameraServices as $svc)
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
     WHATSAPP FLOATING BUTTON
     ============================================= --}}
@php
    $waNumber = setting('social_whatsapp', '+212661987654');
    $waLink = $waNumber ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $waNumber) : '';
@endphp

@if($waLink)
<a href="{{ $waLink }}?text=Bonjour%2C%20je%20suis%20int%C3%A9ress%C3%A9%20par%20vos%20cam%C3%A9ras%20et%20mat%C3%A9riel%20audiovisuel." 
   class="whatsapp-float" 
   target="_blank" 
   rel="noopener noreferrer"
   title="Contactez nos conseillers sur WhatsApp">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
</a>
@endif

@endsection

@push('scripts')
<script>
// Hero Cinematic Slider
const heroSwiper = new Swiper('.hero-swiper', {
    loop: true,
    autoplay: {
        delay: 5500,
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
    }
});

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
        if (!response.ok) throw new Error((data && data.message) || `Erreur: ${response.status}`);
        
        ['header-cart-count', 'header-cart-count-mobile'].forEach(id => {
            const countEl = document.getElementById(id);
            if(countEl && data.cartCount !== undefined) countEl.textContent = data.cartCount;
        });
        
        if (typeof refreshMiniCart === 'function') {
            refreshMiniCart();
        }

        Swal.fire({ 
            toast: true, 
            position: 'top-end', 
            icon: 'success', 
            title: 'Équipement ajouté au panier !', 
            showConfirmButton: false, 
            timer: 2500, 
            background: '#ffffff', 
            color: '#0f172a',
            iconColor: '#dc2626'
        });
    })
    .catch(error => {
        Swal.fire({ 
            toast: true, 
            position: 'top-end', 
            icon: 'error', 
            title: error.message || 'Erreur', 
            showConfirmButton: false, 
            timer: 3000,
            background: '#ffffff',
            color: '#0f172a',
            iconColor: '#dc2626'
        });
    });
}
</script>
@endpush
