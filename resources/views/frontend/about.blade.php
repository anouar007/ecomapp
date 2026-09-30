@extends('layouts.frontend')

@section('meta_title', 'À Propos — ' . setting('app_name', 'Full Frame House') . ' | Showroom & Matériel Cinéma au Maroc')
@section('meta_description', 'Découvrez l\'histoire de ' . setting('app_name', 'Full Frame House') . ', distributeur agréé de caméras cinéma, objectifs professionnels et matériel de tournage à Casablanca, Maroc.')
@section('meta_keywords', 'à propos ' . setting('app_name', 'Full Frame House') . ', boutique caméra Casablanca, matériel cinéma Maroc, Sony Cinema Line Maroc, Canon Cinema EOS, DJI Pro Casablanca, équipement audiovisuel')

@section('json_ld')
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "AboutPage",
    "name": "À Propos de {{ addslashes(setting('app_name', 'Full Frame House')) }}",
    "url": "{{ route('about') }}",
    "description": "{{ addslashes(setting('app_description', 'Distributeur agréé de caméras cinéma, objectifs professionnels et matériel de tournage à Casablanca, Maroc.')) }}",
    "mainEntity": {
      "@type": "Organization",
      "name": "{{ addslashes(setting('app_name', 'Full Frame House')) }}",
      "url": "{{ url('/') }}",
      "logo": "{{ setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/logo.png') }}"
    }
  },
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Accueil",
        "item": "{{ url('/') }}"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "À Propos",
        "item": "{{ route('about') }}"
      }
    ]
  }
]
</script>
@endsection

@section('content')

{{-- =============================================
     HERO STRIP (Light Theme with Red & Black Accents)
     ============================================= --}}
<section class="about-hero-section">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <div class="section-eyebrow-cine justify-content-center mb-3">
                    <span class="tally-dot"></span>
                    <span>{{ strtoupper(setting('app_name', 'Full Frame House')) }} · DEPUIS 2018</span>
                </div>
                <h1 class="about-hero-title mb-4">
                    L'Excellence Cinématographique au Service des <span class="text-brand-red">Créateurs & Réalisateurs</span>
                </h1>
                <p class="about-hero-sub mx-auto">
                    Fondé par des passionnés et directeurs de la photographie, <strong>{{ setting('app_name', 'notre équipe') }}</strong> est le premier showroom et distributeur agréé d'équipements audiovisuels, caméras cinéma et optiques broadcast au Maroc.
                </p>
                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                    <a href="{{ route('shop.index') }}" class="btn-primary rounded-pill px-4 py-3 fw-bold text-decoration-none shadow">
                        <i class="fas fa-camera me-2"></i> Explorer le Catalogue
                    </a>
                    <a href="{{ route('contact') }}" class="btn-dark rounded-pill px-4 py-3 fw-bold text-decoration-none shadow">
                        <i class="fas fa-location-dot me-2"></i> Visiter Notre Showroom
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     SHOWROOM SHOWCASE & STORY
     ============================================= --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-image-wrapper">
                    <img src="{{ asset('images/camera/about_showroom.jpg') }}" alt="Showroom {{ setting('app_name', 'Full Frame House') }} Casablanca" class="about-main-img img-fluid shadow-lg">
                    <div class="about-badge-card">
                        <div class="about-badge-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <div>
                            <div class="about-badge-title">Distributeur Agréé</div>
                            <div class="about-badge-sub">Sony · Canon · DJI · Blackmagic</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <span class="section-eyebrow-cine"><i class="fas fa-film"></i> Notre Histoire & Vocation</span>
                <h2 class="section-title mb-4">Pousser les Limites de la Narration Visuelle</h2>
                <div class="about-text-content">
                    <p class="lead text-secondary mb-4">
                        Chez <strong>{{ setting('app_name', 'Full Frame House') }}</strong>, nous croyons qu'une grande œuvre cinématographique commence par des outils fiables, précis et optiquement impeccables.
                    </p>
                    <p class="text-muted mb-4">
                        Face aux défis d'approvisionnement en matériel broadcast et cinéma au Maroc, nous avons créé un écosystème complet : showroom physique à Casablanca, bancs de test optique pour vérifier chaque focale, ateliers d'équilibrage de gimbals et expédition ultra-sécurisée partout dans le Royaume en 24 à 48 heures.
                    </p>
                    <p class="text-muted mb-4">
                        Que vous tourniez un long-métrage, une série télévisée, un spot publicitaire ou des productions pour le web, nos conseillers certifiés vous accompagnent dans le choix de votre capteur, de vos objectifs anamorphiques et de vos rigs complets.
                    </p>

                    <div class="row g-3 pt-2">
                        <div class="col-sm-6">
                            <div class="about-check-item">
                                <i class="fas fa-check-circle text-brand-red me-2"></i>
                                <span class="fw-bold text-dark">Matériel 100% Neuf & Scellé</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-item">
                                <i class="fas fa-check-circle text-brand-red me-2"></i>
                                <span class="fw-bold text-dark">Garantie Constructeur 2 Ans</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-item">
                                <i class="fas fa-check-circle text-brand-red me-2"></i>
                                <span class="fw-bold text-dark">Factures avec ICE & TVA</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-item">
                                <i class="fas fa-check-circle text-brand-red me-2"></i>
                                <span class="fw-bold text-dark">SAV & Support Dédié Casa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     KEY PILLARS / ENGAGEMENTS
     ============================================= --}}
<section class="section-py bg-surface" id="garantie">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-eyebrow-cine"><i class="fas fa-gem"></i> Nos Valeurs Fondamentales</span>
            <h2 class="section-title">Pourquoi Choisir {{ setting('app_name', 'Notre Showroom') }}</h2>
            <p class="section-desc">Un niveau d'exigence sans compromis pour les professionnels de l'audiovisuel</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="about-pillar-card">
                    <div class="pillar-icon-box">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4 class="pillar-title">Authenticité Certifiée</h4>
                    <p class="pillar-desc">
                        Tous nos équipements proviennent exclusivement des circuits officiels avec numéro de série traçable et garantie constructeur officielle.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="80">
                <div class="about-pillar-card">
                    <div class="pillar-icon-box">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <h4 class="pillar-title">Atelier de Calibration</h4>
                    <p class="pillar-desc">
                        Testez les objectifs, configurez vos gimbals DJI Ronin et calibrez vos systèmes HF sans fil directement dans notre studio technique.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="160">
                <div class="about-pillar-card">
                    <div class="pillar-icon-box">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="pillar-title">Garantie & Support Réactif</h4>
                    <p class="pillar-desc">
                        Prise en charge SAV express en cas de panne, prêt de matériel de remplacement selon disponibilité et assistance par des techniciens qualifiés.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="240">
                <div class="about-pillar-card">
                    <div class="pillar-icon-box">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h4 class="pillar-title">Accompagnement Entreprise</h4>
                    <p class="pillar-desc">
                        Devis instantanés et facturation légale avec ICE pour boîtes de production, agences de communication, télévisions et institutions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     KEY NUMBERS / METRICS
     ============================================= --}}
<section class="about-stats-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">1 500<span class="text-brand-red">+</span></div>
                    <div class="stat-label">Équipements & Boîtiers Livrés</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">100<span class="text-brand-red">%</span></div>
                    <div class="stat-label">Produits Sous Garantie Constructeur</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">24<span class="text-brand-red">h</span>/48<span class="text-brand-red">h</span></div>
                    <div class="stat-label">Livraison Sécurisée Tout le Maroc</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">99.4<span class="text-brand-red">%</span></div>
                    <div class="stat-label">Satisfaction Studios & Réalisateurs</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     BRANDS PARTNERS STRIP
     ============================================= --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-eyebrow-cine"><i class="fas fa-handshake"></i> Partenaires Officiels</span>
            <h3 class="fw-bold text-dark font-heading">Les Plus Grandes Marques Mondiales</h3>
        </div>
        <div class="row align-items-center justify-content-center g-4 text-center">
            <div class="col-4 col-md-2">
                <div class="partner-badge-pill">SONY CINE</div>
            </div>
            <div class="col-4 col-md-2">
                <div class="partner-badge-pill">CANON EOS</div>
            </div>
            <div class="col-4 col-md-2">
                <div class="partner-badge-pill">DJI PRO</div>
            </div>
            <div class="col-4 col-md-2">
                <div class="partner-badge-pill">BLACKMAGIC</div>
            </div>
            <div class="col-4 col-md-2">
                <div class="partner-badge-pill">SIGMA CINE</div>
            </div>
            <div class="col-4 col-md-2">
                <div class="partner-badge-pill">APUTURE</div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     CTA SECTION
     ============================================= --}}
<section class="promo-cta-section">
    <div class="container">
        <div class="promo-cta-card">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="section-eyebrow-cine"><i class="fas fa-video"></i> Vous préparez un tournage ?</span>
                    <h2 class="promo-cta-title">Configurons Votre Prochain Rig Ensemble</h2>
                    <p class="promo-cta-desc">
                        Passez à notre showroom pour tester les boîtiers et optiques ou contactez nos spécialistes pour recevoir un devis personnalisé sous 2h.
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                    <a href="{{ route('shop.index') }}" class="btn-hero-shop me-2">
                        <i class="fas fa-th-large"></i> Voir le Catalogue
                    </a>
                    <a href="{{ route('contact') }}" class="btn-hero-outline">
                        <i class="fas fa-envelope"></i> Nous Contacter
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
