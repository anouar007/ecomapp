@extends('layouts.frontend')

@section('meta_title', __('About') . ' — ' . setting('app_name', 'Winashop'))
@section('meta_description', setting('app_description', 'Winashop Casablanca : La technologie au service de la créativité. Votre expert en matériel photo, vidéo, caméras, objectifs, éclairage studio et accessoires au Maroc.'))

@section('json_ld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "name": "{{ addslashes(setting('app_name', 'Winashop')) }}",
  "description": "La technologie au service de la créativité. Votre expert en équipements photo et vidéo à Casablanca.",
  "url": "{{ route('about') }}",
  "mainEntity": {
    "@type": "Store",
    "name": "{{ addslashes(setting('app_name', 'Winashop')) }}",
    "image": "{{ asset('images/camera/about_showroom.jpg') }}",
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

{{-- ═════════════════════════════════════════════════════════════════
     1. CINEMATIC HERO SECTION
     ═════════════════════════════════════════════════════════════════ --}}
<section class="about-hero-section position-relative overflow-hidden">
    <div class="about-hero-glow"></div>
    <div class="container position-relative py-4 py-lg-5">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-xl-9 col-lg-10 mx-auto" data-aos="fade-up">
                {{-- Eyebrow Badge --}}
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3 mb-md-4 about-hero-pill">
                    <span class="about-pulse-dot"></span>
                    <span class="small fw-bold text-uppercase ls-1">
                        {{ setting('app_name', 'Winashop') }} · {{ __('Casablanca · Maroc') }}
                    </span>
                </div>

                {{-- Main Headline directly from winashop.com/a-propos/ --}}
                <h1 class="about-hero-title mb-3 mb-md-4 font-heading">
                    @if(app()->getLocale() === 'ar')
                        التكنولوجيا في خدمة <span class="text-brand-red">الإبداع</span>
                    @else
                        La technologie au service de la <span class="text-brand-red">créativité</span>
                    @endif
                </h1>

                {{-- Subtitle directly from winashop.com/a-propos/ --}}
                <p class="about-hero-sub mx-auto mb-4 lead">
                    @if(app()->getLocale() === 'ar')
                        <strong>خبيركم المعتمد في معدات التصوير الفوتوغرافي والفيديو في الدار البيضاء.</strong>
                        فضاء استثنائي يلتقي فيه الابتكار والأداء والخبرة الفنية لمواكبة صناع المحتوى والمصورين المحترفين في كافة أنحاء المغرب.
                    @else
                        <strong>Votre expert en équipements photo et vidéo à Casablanca.</strong>
                        Un espace d'exception où innovation, performance et expertise se rencontrent pour propulser les créateurs passionnés et les cinéastes professionnels à travers tout le Maroc.
                    @endif
                </p>

                {{-- Key Trust Badges Pills --}}
                <div class="d-flex flex-wrap justify-content-center gap-2 gap-md-3 mb-4 mb-md-5">
                    <div class="about-feature-chip">
                        <i class="fas fa-shield-halved text-brand-red"></i>
                        <span>{{ __('100% Neuf & Garanti') }}</span>
                    </div>
                    <div class="about-feature-chip">
                        <i class="fas fa-store text-brand-red"></i>
                        <span>{{ __('Showroom & Essais à Casablanca') }}</span>
                    </div>
                    <div class="about-feature-chip">
                        <i class="fas fa-truck-fast text-brand-red"></i>
                        <span>{{ __('Livraison Express 24h/48h au Maroc') }}</span>
                    </div>
                    <div class="about-feature-chip">
                        <i class="fas fa-file-invoice text-brand-red"></i>
                        <span>{{ __('Facturation avec ICE & TVA') }}</span>
                    </div>
                </div>

                {{-- Call To Action Buttons --}}
                <div class="d-flex justify-content-center gap-2 gap-sm-3 flex-wrap">
                    <a href="{{ route('shop.index') }}" class="btn btn-danger rounded-pill px-4 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fas fa-camera"></i>
                        <span>{{ __('Explorer le Catalogue') }}</span>
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} small"></i>
                    </a>
                    <a href="#showroom" class="btn btn-outline-dark rounded-pill px-4 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fas fa-location-dot"></i>
                        <span>{{ __('Visiter Notre Showroom') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     2. NOTRE ENGAGEMENT (Directly from winashop.com/a-propos/)
     ═════════════════════════════════════════════════════════════════ --}}
<section class="section-py bg-white border-bottom position-relative">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            {{-- Visual Showcase Column --}}
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-showcase-box position-relative">
                    <div class="about-img-frame shadow-lg rounded-4 overflow-hidden position-relative">
                        <img src="{{ asset('images/camera/about_showroom.jpg') }}" alt="Showroom {{ setting('app_name', 'Winashop') }} Casablanca" width="600" height="400" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        <div class="about-img-overlay"></div>
                    </div>

                    {{-- Floating Glass Accreditation Card --}}
                    <div class="about-float-card about-float-card--top shadow-md">
                        <div class="about-float-icon bg-danger-subtle text-danger">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ __('Boutique Spécialisée Agréée') }}</div>
                            <div class="small text-muted">Sony · Canon · Nikon · DJI · Amaran</div>
                        </div>
                    </div>

                    {{-- Floating Location Card --}}
                    <div class="about-float-card about-float-card--bottom shadow-md">
                        <div class="about-float-icon bg-dark text-white">
                            <i class="fas fa-map-pin"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ __('Showroom Casablanca') }}</div>
                            <div class="small text-muted">{{ __('01 Rue 102, Hay Wiam (Al Oulfa)') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Text & Mission Column --}}
            <div class="col-lg-6" data-aos="fade-left">
                <div class="section-eyebrow-cine mb-2">
                    <span class="tally-dot"></span>
                    <span>{{ __('Notre Engagement') }}</span>
                </div>
                
                <h2 class="section-title mb-3 font-heading">
                    @if(app()->getLocale() === 'ar')
                        أدوات دقيقة لمشاريع استثنائية
                    @else
                        Des équipements de précision pour des projets percutants
                    @endif
                </h2>

                <div class="about-text-narrative text-muted">
                    @if(app()->getLocale() === 'ar')
                        <p class="lead text-dark fw-semibold mb-3">
                            في <strong>{{ setting('app_name', 'Winashop') }}</strong>، نلتزم بتقديم تشكيلة مختارة بعناية فائقة من معدات التصوير الفوتوغرافي والفيديو عالية الجودة، المصممة لتلبية أعلى معايير الإنتاج الاحترافي.
                        </p>
                        <p class="mb-3">
                            سواء كنتم من الهواة الشغوفين أو المحترفين المتمرسين، تضم مجموعتنا المتنوعة أفضل الأدوات لالتقاط صور استثنائية أو إنتاج مقاطع فيديو سينمائية مؤثرة واحترافية. نوفر أحدث الكاميرات الرقمية، والعدسات الدقيقة، والإضاءة المتطورة، وكافة الملحقات الأساسية لإثراء تجربتكم الإبداعية.
                        </p>
                        <p class="mb-4">
                            يقع متجرنا في قلب <strong>الدار البيضاء (حي الوئام / الألفة)</strong>، وهو أكثر من مجرد نقطة بيع عادية. إنه فضاء يلتقي فيه الابتكار والأداء والخبرة لمرافقتكم تقنياً في تحقيق طموحاتكم الفنية بأفضل الأسعار وضمان رسمي.
                        </p>
                    @else
                        <p class="lead text-dark fw-semibold mb-3">
                            Chez <strong>{{ setting('app_name', 'Winashop') }}</strong>, nous nous engageons à vous offrir une sélection soigneusement choisie d’équipements photo et vidéo haut de gamme, répondant aux standards les plus exigeants.
                        </p>
                        <p class="mb-3">
                            Que vous soyez amateur passionné ou professionnel chevronné, notre gamme variée comprend les meilleurs outils pour capturer des images d’une qualité exceptionnelle ou produire des vidéos percutantes et professionnelles. Nous proposons des appareils photo dernier cri, des caméras performantes, des objectifs de précision, ainsi que des accessoires indispensables pour enrichir votre expérience créative.
                        </p>
                        <p class="mb-4">
                            Située en plein cœur de <strong>Casablanca (01 Rue 102, Hay Wiam – Al Oulfa)</strong>, notre boutique est bien plus qu’un simple magasin. C'est un espace où innovation, performance et expertise se rencontrent pour satisfaire toutes vos attentes créatives.
                        </p>
                    @endif

                    {{-- Tags Section --}}
                    <div class="d-flex flex-wrap gap-2 pt-1 mb-4">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-mono small">#PhotoÉquipement</span>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-mono small">#VidéoPro</span>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-mono small">#MatérielPhoto</span>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-mono small">#AccessoiresPro</span>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-mono small">#Casablanca</span>
                    </div>

                    {{-- 4 Value Checks Grid --}}
                    <div class="row g-2 g-md-3">
                        <div class="col-sm-6">
                            <div class="about-check-card">
                                <i class="fas fa-check-circle text-danger fs-5 flex-shrink-0"></i>
                                <span class="small fw-bold text-dark">{{ __('Matériel Neuf & Scellé 100%') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-card">
                                <i class="fas fa-check-circle text-danger fs-5 flex-shrink-0"></i>
                                <span class="small fw-bold text-dark">{{ __('Garantie Constructeur 2 Ans') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-card">
                                <i class="fas fa-check-circle text-danger fs-5 flex-shrink-0"></i>
                                <span class="small fw-bold text-dark">{{ __('Facturation Conforme ICE & TVA') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-card">
                                <i class="fas fa-check-circle text-danger fs-5 flex-shrink-0"></i>
                                <span class="small fw-bold text-dark">{{ __('Support Technique & SAV Casablanca') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     3. UN SERVICE SUR MESURE (Directly from winashop.com/a-propos/)
     ═════════════════════════════════════════════════════════════════ --}}
<section class="section-py bg-surface" id="garantie">
    <div class="container">
        <div class="text-center max-w-750 mx-auto mb-5" data-aos="fade-up">
            <div class="section-eyebrow-cine justify-content-center mb-2">
                <span class="tally-dot"></span>
                <span>{{ __('Expertise & Accompagnement') }}</span>
            </div>
            <h2 class="section-title mb-3 font-heading">
                @if(app()->getLocale() === 'ar')
                    خدمة مصممة خصيصاً لمشاريعكم الإبداعية
                @else
                    Un service sur mesure pour vos projets créatifs
                @endif
            </h2>
            <p class="text-muted lead fs-6">
                @if(app()->getLocale() === 'ar')
                    أولويتنا هي تقديم خدمة مخصصة تلبي متطلباتكم التقنية والميزانيات المحددة لمساعدتكم على تحقيق أرقى النتائج البصرية.
                @else
                    Notre priorité est de vous offrir un service personnalisé. Avec l’expertise de nos conseillers, vous bénéficiez d’un accompagnement à chaque étape de votre projet.
                @endif
            </p>
        </div>

        <div class="row g-4">
            {{-- Pillar 1 --}}
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="about-pillar-card-pro h-100">
                    <div class="pillar-icon-box-pro bg-danger-subtle text-danger">
                        <i class="fas fa-user-astronaut"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2 font-heading fs-5">{{ __('Conseil & Orientation') }}</h3>
                    <p class="small text-muted mb-0 lh-base">
                        {{ __('Nous vous guidons dans vos choix d’équipements en fonction de vos besoins spécifiques, pour composer le setup parfait.') }}
                    </p>
                </div>
            </div>

            {{-- Pillar 2 --}}
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="80">
                <div class="about-pillar-card-pro h-100">
                    <div class="pillar-icon-box-pro bg-dark text-white">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2 font-heading fs-5">{{ __('Espace Test Showroom') }}</h3>
                    <p class="small text-muted mb-0 lh-base">
                        {{ __('Testez les optiques, configurez vos gimbals DJI et calibrez vos kits d’éclairage directement dans notre showroom à Casablanca.') }}
                    </p>
                </div>
            </div>

            {{-- Pillar 3 --}}
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="160">
                <div class="about-pillar-card-pro h-100">
                    <div class="pillar-icon-box-pro bg-danger-subtle text-danger">
                        <i class="fas fa-boxes-packing"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2 font-heading fs-5">{{ __('Neuf & Occasion Vérifiée') }}</h3>
                    <p class="small text-muted mb-0 lh-base">
                        {{ __('Du matériel neuf scellé avec garantie constructeur, ainsi qu’un parc d’occasion inspecté et testé minutieusement par nos techniciens.') }}
                    </p>
                </div>
            </div>

            {{-- Pillar 4 --}}
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="240">
                <div class="about-pillar-card-pro h-100">
                    <div class="pillar-icon-box-pro bg-dark text-white">
                        <i class="fas fa-truck-ramp-box"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2 font-heading fs-5">{{ __('Expédition Sécurisée Maroc') }}</h3>
                    <p class="small text-muted mb-0 lh-base">
                        {{ __('Livraison rapide partout au Maroc (24h/48h) dans des emballages renforcés haute protection, avec livraison express à Casablanca.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     4. LES UNIVERS D'ÉQUIPEMENTS WINASHOP (Interactive Visual Grid)
     ═════════════════════════════════════════════════════════════════ --}}
<section class="section-py bg-white border-top border-bottom">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 mb-md-5">
            <div>
                <div class="section-eyebrow-cine mb-2">
                    <span class="tally-dot"></span>
                    <span>{{ __('Notre Catalogue Spécialisé') }}</span>
                </div>
                <h2 class="section-title mb-0 font-heading">{{ __('Tout le matériel pour réussir vos créations') }}</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 mt-3 mt-md-0 fw-bold small align-self-start align-self-md-auto">
                <span>{{ __('Voir Toutes les Catégories') }}</span>
                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} ms-1"></i>
            </a>
        </div>

        <div class="row g-3 g-md-4">
            {{-- Category 1: Cameras --}}
            <div class="col-md-6 col-lg-4">
                <div class="about-category-card">
                    <div class="about-cat-img-wrap">
                        <img src="{{ asset('images/camera/cat_cameras.jpg') }}" alt="Caméras et Boîtiers" width="400" height="180" class="about-cat-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/hero_winashop.jpg') }}';">
                        <span class="about-cat-badge">Sony · Canon · Nikon · Fuji</span>
                    </div>
                    <div class="about-cat-body">
                        <div>
                            <h3 class="fw-bold text-dark mb-1.5 font-heading fs-6">{{ __('Appareils Photo & Caméras') }}</h3>
                            <p class="small text-muted mb-3">{{ __('Boîtiers plein format, Cinema Line, caméras vidéo 4K/8K et compacts experts.') }}</p>
                        </div>
                        <a href="{{ route('shop.index', ['category' => 'camera']) }}" class="about-cat-link">
                            <span>{{ __('Découvrir les boîtiers') }}</span>
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Category 2: Lenses & Filters --}}
            <div class="col-md-6 col-lg-4">
                <div class="about-category-card">
                    <div class="about-cat-img-wrap">
                        <img src="{{ asset('images/camera/hero_lens_optics.jpg') }}" alt="Objectifs & Optiques" width="400" height="180" class="about-cat-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        <span class="about-cat-badge">Primes · Zooms · Filtres ND</span>
                    </div>
                    <div class="about-cat-body">
                        <div>
                            <h3 class="fw-bold text-dark mb-1.5 font-heading fs-6">{{ __('Objectifs & Optiques Cinéma') }}</h3>
                            <p class="small text-muted mb-3">{{ __('Focales fixes ultra-lumineuses, téléobjectifs, filtres CPL/ND et matte boxes.') }}</p>
                        </div>
                        <a href="{{ route('shop.index', ['category' => 'objectifs']) }}" class="about-cat-link">
                            <span>{{ __('Explorer les optiques') }}</span>
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Category 3: Lighting & Studio --}}
            <div class="col-md-6 col-lg-4">
                <div class="about-category-card">
                    <div class="about-cat-img-wrap">
                        <img src="{{ asset('images/camera/cat_lighting.jpg') }}" alt="Éclairage et Studio" width="400" height="180" class="about-cat-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/banner_studio_lighting.jpg') }}';">
                        <span class="about-cat-badge">Amaran · Nicefoto · Softboxes</span>
                    </div>
                    <div class="about-cat-body">
                        <div>
                            <h3 class="fw-bold text-dark mb-1.5 font-heading fs-6">{{ __('Lumières & Matériel de Studio') }}</h3>
                            <p class="small text-muted mb-3">{{ __('Projecteurs LED COB, panneaux RGB bicolores, softboxes paraboliques et pieds.') }}</p>
                        </div>
                        <a href="{{ route('shop.index', ['category' => 'lumieres-et-materiel-de-studio']) }}" class="about-cat-link">
                            <span>{{ __('Voir le matériel studio') }}</span>
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Category 4: Sound & Podcast --}}
            <div class="col-md-6 col-lg-4">
                <div class="about-category-card">
                    <div class="about-cat-img-wrap">
                        <img src="{{ asset('images/camera/cat_audio.jpg') }}" alt="Son et Podcast" width="400" height="180" class="about-cat-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/banner_mic_sound.jpg') }}';">
                        <span class="about-cat-badge">Micros HF · Podcast · Studio</span>
                    </div>
                    <div class="about-cat-body">
                        <div>
                            <h3 class="fw-bold text-dark mb-1.5 font-heading fs-6">{{ __('Prise de Son & Podcast') }}</h3>
                            <p class="small text-muted mb-3">{{ __('Micros HF sans fil, micros canon directionnels, enregistreurs et kits podcast.') }}</p>
                        </div>
                        <a href="{{ route('shop.index', ['category' => 'son']) }}" class="about-cat-link">
                            <span>{{ __('Découvrir le matériel audio') }}</span>
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Category 5: Gimbals & Tripods --}}
            <div class="col-md-6 col-lg-4">
                <div class="about-category-card">
                    <div class="about-cat-img-wrap">
                        <img src="{{ asset('images/camera/cat_gimbals.jpg') }}" alt="Stabilisateurs & Trépieds" width="400" height="180" class="about-cat-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/hero_cinema_rig.jpg') }}';">
                        <span class="about-cat-badge">DJI Ronin · Manbily · Carbone</span>
                    </div>
                    <div class="about-cat-body">
                        <div>
                            <h3 class="fw-bold text-dark mb-1.5 font-heading fs-6">{{ __('Stabilisateurs & Trépieds Pro') }}</h3>
                            <p class="small text-muted mb-3">{{ __('Gimbals 3 axes DJI, trépieds vidéo carbone Manbily, rotules fluides et monopodes.') }}</p>
                        </div>
                        <a href="{{ route('shop.index', ['category' => 'stabilisateurs']) }}" class="about-cat-link">
                            <span>{{ __('Explorer la stabilisation') }}</span>
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Category 6: Accessories & Occasions --}}
            <div class="col-md-6 col-lg-4">
                <div class="about-category-card">
                    <div class="about-cat-img-wrap">
                        <img src="{{ asset('images/camera/shop_hero_banner.jpg') }}" alt="Accessoires & Occasion" width="400" height="180" class="about-cat-img" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        <span class="about-cat-badge">V-Mount · SD V90 · Occasion</span>
                    </div>
                    <div class="about-cat-body">
                        <div>
                            <h3 class="fw-bold text-dark mb-1.5 font-heading fs-6">{{ __('Accessoires & Parc d\'Occasion') }}</h3>
                            <p class="small text-muted mb-3">{{ __('Batteries, cartes mémoire haute vitesse, sacs renforcés et matériel d\'occasion certifié.') }}</p>
                        </div>
                        <a href="{{ route('shop.index', ['category' => 'accessoires']) }}" class="about-cat-link">
                            <span>{{ __('Voir les accessoires') }}</span>
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     5. STATS D'IMPACT (Cinematic Dark Section)
     ═════════════════════════════════════════════════════════════════ --}}
<section class="about-stats-section py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">5 000<span class="text-brand-red">+</span></div>
                    <div class="stat-label">{{ __('Créateurs & Studios Équipés') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">100<span class="text-brand-red">%</span></div>
                    <div class="stat-label">{{ __('Produits Sous Garantie Constructeur') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">24<span class="text-brand-red">h</span>/48<span class="text-brand-red">h</span></div>
                    <div class="stat-label">{{ __('Livraison Rapide Partout au Maroc') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">4.9<span class="text-brand-red">/5</span></div>
                    <div class="stat-label">{{ __('Satisfaction Clients & Productions') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     6. LES MARQUES PARTENAIRES OFFICIELLES (winashop.com brands)
     ═════════════════════════════════════════════════════════════════ --}}
<section class="section-py bg-white border-bottom">
    <div class="container">
        <div class="text-center mb-4 mb-md-5">
            <div class="section-eyebrow-cine justify-content-center mb-2">
                <span class="tally-dot"></span>
                <span>{{ __('Écosystème & Partenaires') }}</span>
            </div>
            <h3 class="fw-bold text-dark font-heading">{{ __('Les Plus Grandes Marques Mondiales') }}</h3>
            <p class="text-muted small mx-auto max-w-600 mb-0">
                {{ __('Distributeur des leaders de l’industrie audiovisuelle avec numéros de série traçables et garantie constructeur.') }}
            </p>
        </div>

        <div class="row align-items-center justify-content-center g-3 text-center">
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">SONY</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">CANON</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">NIKON</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">FUJIFILM</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">DJI PRO</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">AMARAN</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">APUTURE</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">MANBILY</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">FEELWORLD</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">NICEFOTO</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">AMBITFUL</div></div>
            <div class="col-4 col-sm-3 col-md-2"><div class="partner-badge-pill">NEEWER</div></div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     7. VISITE SHOWROOM CASABLANCA & CONTACT DIRECT
     ═════════════════════════════════════════════════════════════════ --}}
<section class="section-py bg-surface" id="showroom">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="section-eyebrow-cine mb-2">
                    <span class="tally-dot"></span>
                    <span>{{ __('Venez Vivre l\'Expérience') }}</span>
                </div>
                <h2 class="section-title mb-3 font-heading">
                    @if(app()->getLocale() === 'ar')
                        معرضنا في قلب الدار البيضاء
                    @else
                        Notre Showroom à Casablanca
                    @endif
                </h2>
                <p class="text-muted mb-4 lead fs-6">
                    @if(app()->getLocale() === 'ar')
                        نرحب بكم في صالة العرض الخاصة بنا بحي الوئام / الألفة في الدار البيضاء. يمكنكم اختبار الكاميرات والعدسات والمعدات على أرض الواقع واستشارة فريقنا من الخبراء قبل تأكيد اختياركم.
                    @else
                        Bienvenue dans notre espace dédié aux créateurs de contenu et professionnels de l’image. Venez manipuler les boîtiers, tester les optiques sur place et échanger avec nos conseillers techniques.
                    @endif
                </p>

                {{-- Contact Details Cards --}}
                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-white border shadow-xs">
                        <div class="about-contact-icon bg-danger-subtle text-danger">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-bold ls-1">{{ __('Adresse du Magasin') }}</div>
                            <div class="fw-bold text-dark">01 Rue 102, Hay Wiam, Al Oulfa, Casablanca 20220</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-white border shadow-xs">
                        <div class="about-contact-icon bg-dark text-white">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-bold ls-1">{{ __('Horaires d\'Ouverture') }}</div>
                            <div class="fw-bold text-dark">{{ __('Lundi au Samedi : 09h30 – 19h30') }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-white border shadow-xs">
                        <div class="about-contact-icon bg-success-subtle text-success">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-bold ls-1">{{ __('Assistance & WhatsApp') }}</div>
                            <div class="fw-bold text-dark">06 29 03 57 77 · contact@winashop.com</div>
                        </div>
                    </div>
                </div>

                {{-- Interactive Action Buttons --}}
                <div class="d-flex flex-wrap gap-2 gap-sm-3">
                    <a href="https://wa.me/212629035777?text={{ urlencode('Bonjour Winashop, je souhaite me renseigner sur vos équipements.') }}" target="_blank" rel="noopener noreferrer" class="btn btn-success rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fab fa-whatsapp"></i>
                        <span>{{ __('Échanger sur WhatsApp') }}</span>
                    </a>
                    <a href="https://maps.google.com/?q=01+Rue+102+Al+Oulfa+Casablanca+Maroc" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                        <i class="fas fa-directions"></i>
                        <span>{{ __('Itinéraire Google Maps') }}</span>
                    </a>
                </div>
            </div>

            {{-- Showroom Photo Grid with Map Pin Badge --}}
            <div class="col-lg-6" data-aos="fade-left">
                <div class="about-showroom-visual shadow-xl rounded-4 overflow-hidden position-relative">
                    <img src="{{ asset('images/camera/hero_winashop.jpg') }}" alt="Showroom Winashop Equipements" width="600" height="420" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('images/camera/about_showroom.jpg') }}';">
                    <div class="about-showroom-badge">
                        <div class="d-flex align-items-center gap-2">
                            <span class="about-pulse-dot"></span>
                            <span class="fw-bold text-white small">{{ __('Espace Showroom Ouvert aux Créateurs') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════
     8. CTA BANNER (Project Rig / Quote Consultation)
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
                        <a href="{{ route('contact') }}" class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold shadow-sm hover-scale-sm transition-transform d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                            <i class="fas fa-envelope"></i>
                            <span>{{ __('Demander un Devis Pro') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
