@extends('layouts.frontend')

@section('meta_title', 'Contact & Showroom — ' . setting('app_name', 'Full Frame House') . ' | Casablanca')
@section('meta_description', 'Contactez nos conseillers en matériel cinéma et vidéo ou visitez notre showroom à Casablanca. Devis entreprise avec ICE sous 2h, tests et livraison partout au Maroc.')
@section('meta_keywords', 'contact ' . setting('app_name', 'Full Frame House') . ', showroom caméra Casablanca, devis caméra cinéma Maroc, adresse showroom, téléphone matériel vidéo')

@section('json_ld')
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "ContactPage",
    "name": "Contact & Showroom {{ addslashes(setting('app_name', 'Full Frame House')) }}",
    "url": "{{ route('contact') }}",
    "description": "Contactez nos experts cinéma et visitez notre showroom à Casablanca, Maroc."
  },
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "{{ addslashes(setting('company_name', setting('app_name', 'Full Frame House'))) }}",
    "url": "{{ url('/') }}",
    "logo": "{{ setting('app_logo') ? asset('storage/' . setting('app_logo')) : asset('images/camera/logo.png') }}",
    "image": "{{ asset('images/camera/about_showroom.jpg') }}",
    "telephone": "{{ setting('company_phone', '+212661987654') }}",
    "email": "{{ setting('company_email', 'contact@aitoumdis.com') }}",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "{{ setting('company_address', '45 Boulevard d\'Anfa, Quartier Racine') }}",
      "addressLocality": "{{ setting('company_city', 'Casablanca') }}",
      "postalCode": "20050",
      "addressCountry": "MA"
    },
    "openingHoursSpecification": [
      {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
        "opens": "09:00",
        "closes": "19:00"
      },
      {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Saturday"],
        "opens": "10:00",
        "closes": "17:00"
      }
    ],
    "priceRange": "$$$$"
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
        "name": "Contact",
        "item": "{{ route('contact') }}"
      }
    ]
  }
]
</script>
@endsection

@section('content')

{{-- =============================================
     HERO STRIP (Light Theme)
     ============================================= --}}
<section class="contact-hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <div class="section-eyebrow-cine justify-content-center mb-3">
                    <span class="tally-dot"></span>
                    <span>SHOWROOM & SERVICE CLIENT</span>
                </div>
                <h1 class="contact-hero-title mb-3">
                    Échangez avec Nos <span class="text-brand-red">Experts Audiovisuels</span>
                </h1>
                <p class="contact-hero-sub mx-auto">
                    Une question sur la compatibilité d'un boîtier, un projet de tournage ou une demande de devis entreprise ? Notre équipe technique vous répond sous 2h ouvrées.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     MAIN CONTACT FORM & DETAILS
     ============================================= --}}
<section class="section-py bg-surface">
    <div class="container">
        <div class="row g-5">

            {{-- ── LEFT COLUMN: DIRECT CONTACT CARDS ── --}}
            <div class="col-lg-5" data-aos="fade-right">
                <div class="d-flex flex-column gap-4">

                    {{-- Showroom Card --}}
                    <div class="contact-info-card">
                        <div class="contact-icon-box">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div>
                            <h5 class="contact-card-title">Showroom & Atelier Technique</h5>
                            <p class="contact-card-desc mb-2">
                                45 Boulevard d'Anfa, Quartier Racine<br>
                                Casablanca 20050, Maroc
                            </p>
                            <span class="contact-badge-sub">Prise en main & Tests sur place</span>
                        </div>
                    </div>

                    {{-- Phone & WhatsApp Card --}}
                    <div class="contact-info-card">
                        <div class="contact-icon-box">
                            <i class="fas fa-phone-volume"></i>
                        </div>
                        <div>
                            <h5 class="contact-card-title">Assistance Téléphonique & Devis</h5>
                            <p class="contact-card-desc mb-2">
                                Lundi au Samedi : 09h00 – 19h00
                            </p>
                            <a href="tel:{{ setting('company_phone', '+212661987654') }}" class="contact-link-bold">
                                {{ setting('company_phone', '+212 661 98 76 54') }}
                            </a>
                        </div>
                    </div>

                    {{-- Email Card --}}
                    <div class="contact-info-card">
                        <div class="contact-icon-box">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <div>
                            <h5 class="contact-card-title">Courrier Électronique</h5>
                            <p class="contact-card-desc mb-2">
                                Devis entreprise, facturation & partenariats
                            </p>
                            <a href="mailto:{{ setting('company_email', 'contact@lumina-optics.ma') }}" class="contact-link-bold">
                                {{ setting('company_email', 'contact@lumina-optics.ma') }}
                            </a>
                        </div>
                    </div>

                    {{-- WhatsApp Direct Banner --}}
                    @php
                        $waNum = setting('social_whatsapp', '+212661987654');
                        $waLink = $waNum ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $waNum) : '#';
                    @endphp
                    <div class="contact-wa-card">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="wa-icon-circle">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1">WhatsApp Conseiller Studio</h6>
                                    <span class="small text-white-50">Réponse instantanée en journée</span>
                                </div>
                            </div>
                            <a href="{{ $waLink }}?text=Bonjour%20{{ urlencode(setting('app_name', 'notre boutique')) }}%2C%20je%20souhaite%20des%20informations%20sur%20vos%20cam%C3%A9ras." target="_blank" rel="noopener noreferrer" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-success">
                                Discuter <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Company Legal Credentials --}}
                    <div class="contact-legal-box p-3 rounded-4 bg-white border">
                        <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold small text-uppercase ls-1">
                            <i class="fas fa-shield-alt text-brand-red"></i> Informations Société & Facturation
                        </div>
                        <p class="small text-muted mb-0">
                            Société enregistrée au Registre du Commerce de Casablanca. Factures avec mention de l'<strong>ICE</strong>, <strong>IF</strong> et <strong>RC</strong> éligibles pour votre comptabilité d'entreprise et crédits de TVA.
                        </p>
                    </div>

                </div>
            </div>

            {{-- ── RIGHT COLUMN: MESSAGE FORM ── --}}
            <div class="col-lg-7" data-aos="fade-left">
                <div class="contact-form-card">
                    <h3 class="fw-bold font-heading text-dark mb-2">Envoyez-nous un Message</h3>
                    <p class="text-muted mb-4 small">
                        Remplissez le formulaire ci-dessous. Nos experts vous contacteront par téléphone ou email dans un délai maximum de 2 heures.
                    </p>

                    @if(session('success'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle fs-4 me-3 text-success"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Message envoyé avec succès !</h6>
                            <p class="mb-0 small">{{ session('success') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4" role="alert">
                        <ul class="mb-0 small ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" id="contactForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">VOTRE NOM COMPLET <span class="text-brand-red">*</span></label>
                                <input type="text" name="name" class="form-control contact-input" placeholder="ex. Karim El Amrani" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">ADRESSE E-MAIL <span class="text-brand-red">*</span></label>
                                <input type="email" name="email" class="form-control contact-input" placeholder="karim@production.ma" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">NUMÉRO DE TÉLÉPHONE</label>
                                <input type="tel" name="phone" class="form-control contact-input" placeholder="06 XX XX XX XX" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">OBJET DE LA DEMANDE</label>
                                <select name="subject" class="form-select contact-input">
                                    <option value="Demande de devis entreprise (avec ICE)">Demande de devis entreprise (avec ICE)</option>
                                    <option value="Conseil choix caméra ou optique">Conseil choix caméra ou optique</option>
                                    <option value="Prise de rendez-vous Showroom Casa">Prise de rendez-vous Showroom Casa</option>
                                    <option value="Suivi de commande ou livraison">Suivi de commande ou livraison</option>
                                    <option value="Service Après-Vente / Garantie">Service Après-Vente / Garantie</option>
                                    <option value="Autre demande">Autre demande</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">VOTRE MESSAGE / PROJET <span class="text-brand-red">*</span></label>
                                <textarea name="message" rows="5" class="form-control contact-input" placeholder="Détaillez vos besoins (modèle de boîtier, optiques ciblées, dates de tournage, contraintes de livraison)..." required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold w-100 shadow">
                                    <i class="fas fa-paper-plane me-2"></i> Envoyer ma Demande
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- =============================================
     FAQ ACCORDION SECTION
     ============================================= --}}
<section class="section-py bg-white" id="faq">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-eyebrow-cine"><i class="fas fa-question-circle"></i> FAQ</span>
            <h2 class="section-title">Questions Fréquemment Posées</h2>
            <p class="section-desc">Tout ce que vous devez savoir avant de passer commande ou de visiter notre showroom</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion custom-faq-accordion" id="faqAccordion">

                    <div class="accordion-item mb-3 border rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-bold font-heading collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <i class="fas fa-camera text-brand-red me-3"></i> Peut-on tester les caméras et objectifs au showroom ?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg pt-0">
                                Oui, notre showroom de Casablanca dispose d'un espace dédié aux tests. Vous pouvez prendre en main les boîtiers hybrides et cinéma, monter vos optiques, tester l'ergonomie des gimbals DJI et calibrer les moniteurs vidéo en conditions réelles.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button fw-bold font-heading collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="fas fa-file-invoice text-brand-red me-3"></i> Délivrez-vous des factures officielles avec ICE pour les sociétés ?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg pt-0">
                                Absolument. Toutes nos ventes font l'objet d'une facture légale mentionnant l'ICE, l'IF, le RC et le détail de la TVA (20%). Vous pouvez renseigner votre ICE directement lors de la commande en ligne ou dans notre formulaire de contact.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button fw-bold font-heading collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fas fa-truck-fast text-brand-red me-3"></i> Quels sont les délais et modalités de livraison partout au Maroc ?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg pt-0">
                                Nous expédions en 24h ouvrées sur l'axe Casablanca – Rabat – Marrakech, et sous 48h dans toutes les autres villes du Royaume (Tanger, Agadir, Fès, Oujda, Laâyoune). Tous les colis sont scellés et assurés à 100% de leur valeur marchande.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button fw-bold font-heading collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                <i class="fas fa-shield-alt text-brand-red me-3"></i> Comment fonctionne la garantie constructeur de 2 ans ?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg pt-0">
                                En tant que revendeur agréé, nos produits bénéficient de la garantie constructeur officielle. En cas d'anomalie matérielle, notre SAV prend en charge l'appareil avec diagnostic gratuit et pièces d'origine certifiées.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
