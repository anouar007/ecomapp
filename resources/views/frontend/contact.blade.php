@extends('layouts.frontend')

@section('meta_title', __('Contact') . ' & Showroom — ' . setting('app_name', 'Winashop') . ' | Casablanca')
@section('meta_description', setting('app_description', 'Contactez Winashop Casablanca : showroom matériel photo et vidéo au Maroc, assistance technique, devis proforma avec ICE et support WhatsApp 06 29 03 57 77.'))

@section('json_ld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "{{ addslashes(setting('app_name', 'Winashop')) }} — Contact & Showroom Casablanca",
  "url": "{{ route('contact') }}",
  "description": "Contactez nos spécialistes photo et vidéo à Casablanca. Showroom ouvert, devis proforma avec ICE et support technique express.",
  "mainEntity": {
    "@type": "Store",
    "name": "{{ addslashes(setting('app_name', 'Winashop')) }}",
    "image": "{{ asset('images/camera/hero_winashop.jpg') }}",
    "telephone": "+212629035777",
    "email": "contact@winashop.com",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "01 Rue 102, Hay Wiam, Al Oulfa",
      "addressLocality": "Casablanca",
      "postalCode": "20220",
      "addressCountry": "MA"
    },
    "openingHoursSpecification": {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
      "opens": "09:30",
      "closes": "19:30"
    }
  }
}
</script>
@endsection

@section('content')

@php
    $waNum = setting('social_whatsapp', '+212629035777');
    $waClean = $waNum ? preg_replace('/[^0-9]/', '', $waNum) : '212629035777';
    $waLink = 'https://wa.me/' . $waClean;
    $waText = app()->getLocale() === 'ar'
        ? urlencode('مرحباً Winashop، أريد الاستفسار عن معداتكم وحجز موعد في المعرض.')
        : urlencode('Bonjour Winashop, je souhaite me renseigner sur vos équipements photo/vidéo.');
@endphp

{{-- ═════════════════════════════════════════════════════════════════
     1. CINEMATIC HERO SECTION
     ═════════════════════════════════════════════════════════════════ --}}
<section class="contact-hero-section position-relative overflow-hidden">
    <div class="contact-hero-glow"></div>
    <div class="container position-relative py-4 py-lg-5">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-xl-9 col-lg-10 mx-auto" data-aos="fade-up">
                {{-- Eyebrow Badge --}}
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3 mb-md-4 contact-hero-pill">
                    <span class="about-pulse-dot"></span>
                    <span class="small fw-bold text-uppercase ls-1">
                        {{ setting('app_name', 'Winashop') }} · {{ __('Service Client & Showroom Casablanca') }}
                    </span>
                </div>

                {{-- Main Headline --}}
                <h1 class="contact-hero-title mb-3 mb-md-4 font-heading">
                    @if(app()->getLocale() === 'ar')
                        تواصلوا مع خبرائنا في <span class="text-brand-red">التصوير والإنتاج</span>
                    @else
                        Échangez avec nos experts en <span class="text-brand-red">matériel photo & vidéo</span>
                    @endif
                </h1>

                {{-- Subtitle --}}
                <p class="contact-hero-sub mx-auto mb-4 lead">
                    @if(app()->getLocale() === 'ar')
                        <strong>فريقنا الاستشاري رهن إشارتكم.</strong>
                        سواء كنتم بحاجة لاختيار الكاميرات والعدسات الملائمة، ضبط إعدادات استوديو التصوير، أو طلب عرض أسعار رسمي للشركات مع الفاتورة القانونية (ICE)، نجيبكم خلال ساعتين ونرحب بكم في صالة العرض بالدار البيضاء.
                    @else
                        <strong>Une question de compatibilité, un projet de tournage ou un devis pro avec ICE ?</strong>
                        Nos conseillers spécialisés vous répondent rapidement durant les jours ouvrables et vous accueillent dans notre showroom pour tester et configurer votre matériel de prise de vue.
                    @endif
                </p>

                {{-- Key Trust Badges Pills --}}
                <div class="d-flex flex-wrap justify-content-center gap-2 gap-md-3 mb-4 mb-md-5">
                    <div class="about-feature-chip">
                        <i class="fas fa-store text-brand-red"></i>
                        <span>{{ __('Showroom Al Oulfa Casablanca') }}</span>
                    </div>
                    <div class="about-feature-chip">
                        <i class="fas fa-file-invoice text-brand-red"></i>
                        <span>{{ __('Facturation avec ICE & TVA') }}</span>
                    </div>
                    <div class="about-feature-chip">
                        <i class="fab fa-whatsapp text-success"></i>
                        <span>06 29 03 57 77 · {{ __('WhatsApp Direct') }}</span>
                    </div>
                </div>

                {{-- Call To Action Buttons --}}
                <div class="d-flex justify-content-center gap-2 gap-sm-3 flex-wrap">
                    <a href="#contactForm" class="btn btn-danger rounded-pill px-4 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fas fa-paper-plane"></i>
                        <span>{{ __('Envoyer un Message') }}</span>
                    </a>
                    <a href="{{ $waLink }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="btn btn-success rounded-pill px-4 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fab fa-whatsapp"></i>
                        <span>{{ __('Discuter sur WhatsApp') }}</span>
                    </a>
                    <a href="#showroom-directions" class="btn btn-outline-dark rounded-pill px-4 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fas fa-location-dot"></i>
                        <span>{{ __('Localiser le Showroom') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     2. CONTACT CHANNELS & INTERACTIVE FORM GRID
     ═════════════════════════════════════════════════════════════════ --}}
<section class="section-py bg-surface" id="contactFormSection">
    <h2 class="visually-hidden">{{ __('Canaux de Contact Direct et Devis') }}</h2>
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-start">

            {{-- LEFT COLUMN: DIRECT CONTACT DETAILS --}}
            <div class="col-lg-5" data-aos="fade-right">
                <div class="d-flex flex-column gap-3.5">

                    {{-- Showroom Card --}}
                    <div class="contact-info-card my-2">
                        <div class="contact-icon-box bg-danger-subtle text-danger">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h3 class="contact-card-title mb-0 fs-6">{{ __('Showroom & Espace Test Matériel') }}</h3>
                            </div>
                            <p class="contact-card-desc mb-2 text-dark fw-semibold">
                                01 Rue 102, Hay Wiam, Al Oulfa, Casablanca 20220
                            </p>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="contact-badge-sub">{{ __('Essais sur place & Prise en main') }}</span>
                                <a href="#showroom-directions" class="small text-danger fw-bold text-decoration-none">
                                    {{ __('Plan d\'accès') }} <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} small"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Phone Support Card --}}
                    <div class="contact-info-card my-2">
                        <div class="contact-icon-box bg-dark text-white">
                            <i class="fas fa-phone-volume"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="contact-card-title fs-6">{{ __('Assistance Téléphonique & Conseil') }}</h3>
                            <p class="contact-card-desc mb-2">
                                {{ __('Lundi au Samedi : 09h30 – 19h30') }}
                            </p>
                            <a href="tel:0629035777" class="contact-link-bold d-inline-flex align-items-center gap-2">
                                <span>06 29 03 57 77</span>
                                <span class="badge bg-danger-subtle text-danger font-mono small rounded-pill">{{ __('Direct') }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Email / Pro Quotes Card --}}
                    <div class="contact-info-card my-2">
                        <div class="contact-icon-box bg-danger-subtle text-danger">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="contact-card-title fs-6">{{ __('Email Commercial & Devis Pro') }}</h3>
                            <p class="contact-card-desc mb-2">
                                {{ __('Devis entreprises, facturation proforma & partenariats') }}
                            </p>
                            <a href="mailto:contact@winashop.com" class="contact-link-bold">
                                contact@winashop.com
                            </a>
                        </div>
                    </div>

                    {{-- WhatsApp Studio Box --}}
                    <div class="contact-wa-card my-2">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="wa-icon-circle">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h3 class="fw-bold text-white mb-0 fs-6">{{ __('Conseiller Studio WhatsApp') }}</h3>
                                        <span class="about-pulse-dot"></span>
                                    </div>
                                    <span class="small text-white-50">{{ __('Réponse instantanée durant les heures d\'ouverture') }}</span>
                                </div>
                            </div>
                            <a href="{{ $waLink }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-success d-inline-flex align-items-center gap-1.5 shadow-sm">
                                <span>{{ __('Lancer le Chat') }}</span>
                                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} small"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Billing & ICE Notice Box --}}
                    <div class="contact-legal-box my-2">
                        <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold small text-uppercase ls-1">
                            <i class="fas fa-shield-halved text-brand-red"></i>
                            <span>{{ __('Facturation Officielle & Conformité Entreprise') }}</span>
                        </div>
                        <p class="small text-muted mb-0 lh-base">
                            @if(app()->getLocale() === 'ar')
                                شركة مسجلة بالدار البيضاء. الفواتير تتضمن رقم التعريف الموحد للمقاولة (<strong>ICE</strong>) ورقم التعريف الضريبي (<strong>IF</strong>) والسجل التجاري (<strong>RC</strong>) مع تفاصيل الضريبة على القيمة المضافة القابلة للاسترداد لكافة إنتاجاتكم.
                            @else
                                Société commerciale basée à Casablanca. Toutes nos ventes disposent de factures conformes avec mention de l'<strong>ICE</strong>, <strong>IF</strong> et <strong>RC</strong>, parfaitement éligibles pour vos déductions comptables et récupération de TVA.
                            @endif
                        </p>
                    </div>

                </div>
            </div>

            {{-- RIGHT COLUMN: MODERN FORM --}}
            <div class="col-lg-7" data-aos="fade-left">
                <div class="contact-form-card" id="contactForm">
                    <div class="section-eyebrow-cine mb-2">
                        <span class="tally-dot"></span>
                        <span>{{ __('Formulaire Direct') }}</span>
                    </div>
                    <h3 class="fw-bold font-heading text-dark mb-2">
                        @if(app()->getLocale() === 'ar')
                            أرسلوا لنا رسالتكم أو تفاصيل مشروعكم
                        @else
                            Transmettez-nous votre demande
                        @endif
                    </h3>
                    <p class="text-muted mb-4 small">
                        @if(app()->getLocale() === 'ar')
                            املأوا الاستمارة أسفله وسيتواصل معكم خبراؤنا التقنيون في أقل من ساعتين لتقديم الاستشارة المطلوبة أو إرسال عرض أسعار رسمي.
                        @else
                            Remplissez ce formulaire. Nos spécialistes de l'image vous répondront sous 2 heures avec des conseils techniques adaptés ou un devis chiffré.
                        @endif
                    </p>

                    {{-- Success Alert --}}
                    @if(session('success'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center p-3" role="alert">
                        <i class="fas fa-check-circle fs-3 me-3 text-success flex-shrink-0"></i>
                        <div>
                            <h6 class="fw-bold mb-1">{{ __('Message transmis avec succès !') }}</h6>
                            <p class="mb-0 small">{{ session('success') }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Error Alerts --}}
                    @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-3" role="alert">
                        <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                            <i class="fas fa-triangle-exclamation"></i>
                            <span>{{ __('Veuillez corriger les champs suivants :') }}</span>
                        </div>
                        <ul class="mb-0 small ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            {{-- Full Name --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    {{ __('Nom et Prénom') }} <span class="text-brand-red">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="text" name="name" class="form-control contact-input" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: كريم الأمراني' : 'ex. Karim El Amrani' }}" value="{{ old('name') }}" required>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    {{ __('Adresse Email') }} <span class="text-brand-red">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="email" name="email" class="form-control contact-input" placeholder="karim@production.ma" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    {{ __('Numéro de Téléphone / WhatsApp') }}
                                </label>
                                <div class="position-relative">
                                    <input type="tel" name="phone" class="form-control contact-input" placeholder="06 29 03 57 77" value="{{ old('phone') }}">
                                </div>
                            </div>

                            {{-- Subject --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    {{ __('Objet de la Demande') }}
                                </label>
                                <select name="subject" class="form-select contact-input">
                                    <option value="corporate_quote" {{ old('subject') === 'corporate_quote' ? 'selected' : '' }}>
                                        {{ __('Devis entreprise avec ICE & Facture') }}
                                    </option>
                                    <option value="camera_advice" {{ old('subject') === 'camera_advice' ? 'selected' : '' }}>
                                        {{ __('Conseil choix de caméra & optiques') }}
                                    </option>
                                    <option value="showroom_appointment" {{ old('subject') === 'showroom_appointment' ? 'selected' : '' }}>
                                        {{ __('Rendez-vous pour essais au showroom') }}
                                    </option>
                                    <option value="order_tracking" {{ old('subject') === 'order_tracking' ? 'selected' : '' }}>
                                        {{ __('Suivi de commande & livraison') }}
                                    </option>
                                    <option value="after_sales" {{ old('subject') === 'after_sales' ? 'selected' : '' }}>
                                        {{ __('Service Après-Vente (SAV) & Garantie') }}
                                    </option>
                                    <option value="other" {{ old('subject') === 'other' ? 'selected' : '' }}>
                                        {{ __('Autre demande technique') }}
                                    </option>
                                </select>
                            </div>

                            {{-- Message --}}
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    {{ __('Votre Message ou Cahier des Charges') }} <span class="text-brand-red">*</span>
                                </label>
                                <textarea name="message" rows="5" class="form-control contact-input" placeholder="{{ __('Précisez vos besoins : modèle recherché (ex. Sony FX3, A7 IV, DJI RS 4), délais de votre tournage, contraintes de livraison...') }}" required>{{ old('message') }}</textarea>
                            </div>

                            {{-- Submit CTA --}}
                            <div class="col-12 mt-3 pt-2">
                                <button type="submit" class="btn btn-danger btn-lg rounded-pill px-5 py-3 fw-bold w-100 shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>{{ __('Envoyer Ma Demande') }}</span>
                                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} small"></i>
                                </button>
                                <p class="text-center text-muted small mt-2 mb-0">
                                    <i class="fas fa-lock text-brand-red me-1"></i>
                                    {{ __('Vos données sont protégées et ne seront jamais partagées.') }}
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     3. SHOWROOM EXPERIENCE & DIRECTIONS
     ═════════════════════════════════════════════════════════════════ --}}
<section class="section-py bg-white border-top border-bottom" id="showroom-directions">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            {{-- Information Column --}}
            <div class="col-lg-6" data-aos="fade-right">
                <div class="section-eyebrow-cine mb-2">
                    <span class="tally-dot"></span>
                    <span>{{ __('Accès & Essais sur Place') }}</span>
                </div>
                <h2 class="section-title mb-3 font-heading">
                    @if(app()->getLocale() === 'ar')
                        تفضلوا بزيارة معرضنا بالدار البيضاء
                    @else
                        Venez vivre l'expérience dans notre showroom
                    @endif
                </h2>
                <p class="text-muted mb-4 lead fs-6">
                    @if(app()->getLocale() === 'ar')
                        نرحب بصناع المحتوى والمخرجين والمصورين في فضاءنا المتخصص بحي الوئام / الألفة بالدار البيضاء. مساحة مخصصة تتيح لكم تجربة الكاميرات واختبار عدسات السينما وتكوين استوديوهاتكم مباشرة قبل الشراء.
                    @else
                        Situé à Casablanca (Al Oulfa – Hay Wiam), notre showroom est conçu pour les professionnels de l’image. Venez manipuler les boîtiers, tester les objectifs et calibrez vos rigs et éclairages en conditions réelles.
                    @endif
                </p>

                {{-- Access Highlights --}}
                <div class="row g-2 g-md-3 mb-4">
                    <div class="col-sm-6">
                        <div class="about-check-card">
                            <i class="fas fa-square-parking text-danger fs-5 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold text-dark small">{{ __('Stationnement Facile') }}</div>
                                <div class="text-muted text-2xs">{{ __('Places disponibles devant le magasin') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-check-card">
                            <i class="fas fa-train-subway text-danger fs-5 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold text-dark small">{{ __('Transports en Commun') }}</div>
                                <div class="text-muted text-2xs">{{ __('À proximité de la ligne Al Oulfa') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-check-card">
                            <i class="fas fa-hand-holding-hand text-danger fs-5 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold text-dark small">{{ __('Essais Libres Gratuits') }}</div>
                                <div class="text-muted text-2xs">{{ __('Testez boîtiers, gimbals et micros') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-check-card">
                            <i class="fas fa-box-open text-danger fs-5 flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold text-dark small">{{ __('Click & Collect') }}</div>
                                <div class="text-muted text-2xs">{{ __('Retrait immédiat de vos commandes') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Action Buttons --}}
                <div class="d-flex flex-wrap gap-2 gap-sm-3">
                    <a href="https://maps.google.com/?q=01+Rue+102+Al+Oulfa+Casablanca+Maroc" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fas fa-directions"></i>
                        <span>{{ __('Ouvrir dans Google Maps') }}</span>
                    </a>
                    <a href="{{ $waLink }}?text={{ urlencode('Bonjour Winashop, je souhaite planifier une visite au showroom.') }}" target="_blank" rel="noopener noreferrer" class="btn btn-success rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fab fa-whatsapp"></i>
                        <span>{{ __('Confirmer Ma Visite sur WhatsApp') }}</span>
                    </a>
                </div>
            </div>

            {{-- Visual Showcase Column --}}
            <div class="col-lg-6" data-aos="fade-left">
                <div class="about-showroom-visual shadow-xl rounded-4 overflow-hidden position-relative">
                    <img src="{{ asset('images/camera/hero_winashop.jpg') }}" alt="Showroom Winashop Equipements Casablanca" width="600" height="420" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/camera/about_showroom.jpg') }}';">
                    <div class="about-showroom-badge">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="about-pulse-dot"></span>
                                <span class="fw-bold text-white small">{{ __('Showroom Ouvert : Lun–Sam 09h30–19h30') }}</span>
                            </div>
                            <span class="badge bg-danger text-white font-mono small rounded-pill">{{ __('Casablanca') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     4. FAQ SECTION (Interactive Accordion)
     ═════════════════════════════════════════════════════════════════ --}}
<section class="section-py bg-surface" id="faq">
    <div class="container">
        <div class="text-center max-w-750 mx-auto mb-4 mb-md-5" data-aos="fade-up">
            <div class="section-eyebrow-cine justify-content-center mb-2">
                <span class="tally-dot"></span>
                <span>{{ __('Questions Fréquentes') }}</span>
            </div>
            <h2 class="section-title mb-3 font-heading">
                @if(app()->getLocale() === 'ar')
                    كل ما تحتاجون معرفته قبل زيارة المعرض أو الطلب
                @else
                    Tout savoir avant de commander ou visiter notre showroom
                @endif
            </h2>
            <p class="text-muted lead fs-6">
                {{ __('Retrouvez les réponses aux questions les plus posées par les vidéastes et entreprises de production au Maroc.') }}
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion custom-faq-accordion" id="faqAccordion">

                    {{-- Q1 --}}
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <i class="fas fa-camera text-brand-red me-3"></i>
                                <span>{{ __('Peut-on tester les appareils photo et caméras directement au showroom ?') }}</span>
                            </button>
                        </h3>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('Oui, absolument. Notre showroom de Casablanca dispose d’une zone d’essais équipée. Vous pouvez manipuler les boîtiers (Sony, Canon, Nikon, Fuji), monter différentes optiques, vérifier l’ergonomie avec vos propres accessoires et essayer les gimbals DJI avant d\'arrêter votre décision.') }}
                            </div>
                        </div>
                    </div>

                    {{-- Q2 --}}
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="fas fa-file-invoice text-brand-red me-3"></i>
                                <span>{{ __('Délivrez-vous des factures officielles avec ICE pour les sociétés ?') }}</span>
                            </button>
                        </h3>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('Tout à fait. Toutes nos commandes peuvent faire l\'objet d’une facture commerciale officielle avec mention de l’ICE, de l’IF, du RC et de la TVA. Indiquez simplement vos coordonnées de facturation ou cochez la case devis société pour recevoir une proforma sous 2h.') }}
                            </div>
                        </div>
                    </div>

                    {{-- Q3 --}}
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fas fa-truck-fast text-brand-red me-3"></i>
                                <span>{{ __('Quels sont les délais et modalités de livraison partout au Maroc ?') }}</span>
                            </button>
                        </h3>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('Nous assurons une expédition sécurisée en 24h ouvrées sur Casablanca, Rabat, Marrakech, Tanger, Fès, Agadir et sous 48h dans le reste du royaume. Tous les colis sont conditionnés dans des emballages renforcés haute protection avec numéro de suivi en temps réel.') }}
                            </div>
                        </div>
                    </div>

                    {{-- Q4 --}}
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                <i class="fas fa-shield-halved text-brand-red me-3"></i>
                                <span>{{ __('Comment fonctionne la garantie constructeur 2 ans et le SAV à Casablanca ?') }}</span>
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('Tous nos matériels neufs proviennent des circuits officiels des marques et bénéficient d’une garantie constructeur de 2 ans. En cas de dysfonctionnement, notre équipe technique à Casablanca prend en charge le diagnostic et le suivi SAV pour un retour rapide.') }}
                            </div>
                        </div>
                    </div>

                    {{-- Q5 --}}
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <i class="fas fa-wallet text-brand-red me-3"></i>
                                <span>{{ __('Quels sont les moyens de paiement acceptés ?') }}</span>
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('Vous pouvez régler vos achats en espèces à la livraison (Cash on Delivery), par virement bancaire pour les professionnels, ou par carte bancaire et terminal TPE directement dans notre showroom à Casablanca.') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     5. BOTTOM CTA BANNER (Quote & Studio Consultation)
     ═════════════════════════════════════════════════════════════════ --}}
<section class="promo-cta-section py-5">
    <div class="container">
        <div class="promo-cta-card">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="section-eyebrow-cine mb-2 text-white">
                        <i class="fas fa-video me-1"></i> {{ __('Préparez votre prochain tournage') }}
                    </div>
                    <h2 class="promo-cta-title text-white mb-2 font-heading">
                        @if(app()->getLocale() === 'ar')
                            دعنا نضبط إعدادات كاميراتك ومعداتك معاً
                        @else
                            Configurons ensemble votre prochain setup de tournage
                        @endif
                    </h2>
                    <p class="promo-cta-desc text-white-50 mb-0">
                        {{ __('Visitez notre showroom pour tester le matériel ou contactez nos spécialistes pour recevoir un devis personnalisé sous 2h avec facture proforma.') }}
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-2 gap-sm-3">
                        <a href="{{ route('shop.index') }}" class="btn btn-danger rounded-pill px-4 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                            <i class="fas fa-th-large"></i>
                            <span>{{ __('Voir le Catalogue') }}</span>
                        </a>
                        <a href="{{ $waLink }}?text={{ urlencode('Bonjour Winashop, je souhaite un devis pour un projet audiovisuel.') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                            <i class="fab fa-whatsapp"></i>
                            <span>{{ __('Devis Express WhatsApp') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
