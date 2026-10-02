@extends('layouts.frontend')

@section('meta_title', __('About') . ' — ' . setting('app_name', 'Full Frame House'))
@section('meta_description', setting('app_description', 'Distributeur agréé de caméras cinéma, objectifs professionnels et matériel de tournage à Casablanca, Maroc.'))

@section('json_ld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "name": "{{ addslashes(setting('app_name', 'Full Frame House')) }}",
  "url": "{{ route('about') }}"
}
</script>
@endsection

@section('content')

{{-- HERO --}}
<section class="about-hero-section">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <div class="section-eyebrow-cine justify-content-center mb-3">
                    <span class="tally-dot"></span>
                    <span>{{ strtoupper(setting('app_name', 'Full Frame House')) }} · {{ __('Since :year', ['year' => '2018']) }}</span>
                </div>
                <h1 class="about-hero-title mb-4">{{ __('Cinematic Excellence for Creators & Directors') }}</h1>
                <p class="about-hero-sub mx-auto">{{ __('Founded by photography directors...') }}</p>
                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                    <a href="{{ route('shop.index') }}" class="btn-primary rounded-pill px-4 py-3 fw-bold text-decoration-none shadow">
                        <i class="fas fa-camera me-2"></i> {{ __('Explore Catalogue') }}
                    </a>
                    <a href="{{ route('contact') }}" class="btn-dark rounded-pill px-4 py-3 fw-bold text-decoration-none shadow">
                        <i class="fas fa-location-dot me-2"></i> {{ __('Visit Our Showroom') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- STORY --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-image-wrapper">
                    <img src="{{ asset('images/camera/about_showroom.jpg') }}" alt="Showroom {{ setting('app_name', 'Full Frame House') }} Casablanca" class="about-main-img img-fluid shadow-lg">
                    <div class="about-badge-card">
                        <div class="about-badge-icon"><i class="fas fa-award"></i></div>
                        <div>
                            <div class="about-badge-title">{{ __('Certified Distributor') }}</div>
                            <div class="about-badge-sub">Sony · Canon · DJI · Blackmagic</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <span class="section-eyebrow-cine"><i class="fas fa-film"></i> {{ __('Our Story & Mission') }}</span>
                <h2 class="section-title mb-4">{{ __('Pushing the Limits of Visual Storytelling') }}</h2>
                <div class="about-text-content">
                    @if(app()->getLocale() === 'ar')
                    <p class="lead text-secondary mb-4">في <strong>{{ setting('app_name', 'Full Frame House') }}</strong>، نؤمن بأن العمل السينمائي العظيم يبدأ بأدوات موثوقة ودقيقة وممتازة بصرياً.</p>
                    <p class="text-muted mb-4">في مواجهة تحديات توفير معدات البث والسينما في المغرب، أنشأنا نظاماً بيئياً متكاملاً: معرض فعلي في الدار البيضاء، ومناضد لاختبار العدسات وورش لموازنة أجهزة الاستقرار وشحن فائق الأمان في جميع أنحاء المملكة خلال 24 إلى 48 ساعة.</p>
                    @else
                    <p class="lead text-secondary mb-4">Chez <strong>{{ setting('app_name', 'Full Frame House') }}</strong>, nous croyons qu'une grande œuvre cinématographique commence par des outils fiables, précis et optiquement impeccables.</p>
                    <p class="text-muted mb-4">Face aux défis d'approvisionnement en matériel broadcast et cinéma au Maroc, nous avons créé un écosystème complet : showroom physique à Casablanca, bancs de test optique, ateliers d'équilibrage de gimbals et expédition ultra-sécurisée partout dans le Royaume en 24 à 48 heures.</p>
                    @endif

                    <div class="row g-3 pt-2">
                        <div class="col-sm-6">
                            <div class="about-check-item">
                                <i class="fas fa-check-circle text-brand-red me-2"></i>
                                <span class="fw-bold text-dark">{{ __('100% New & Sealed Equipment') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-item">
                                <i class="fas fa-check-circle text-brand-red me-2"></i>
                                <span class="fw-bold text-dark">{{ __('2-Year Manufacturer Warranty') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-item">
                                <i class="fas fa-check-circle text-brand-red me-2"></i>
                                <span class="fw-bold text-dark">{{ __('Invoices with ICE & VAT') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-check-item">
                                <i class="fas fa-check-circle text-brand-red me-2"></i>
                                <span class="fw-bold text-dark">{{ __('After-Sales & Casa Support') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PILLARS --}}
<section class="section-py bg-surface" id="garantie">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-eyebrow-cine"><i class="fas fa-gem"></i> {{ __('Our Core Values') }}</span>
            <h2 class="section-title">{{ __('Why Choose') }} {{ setting('app_name', 'Notre Showroom') }}</h2>
            <p class="section-desc">{{ __('A level of demanding standards for audiovisual professionals') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="about-pillar-card">
                    <div class="pillar-icon-box"><i class="fas fa-certificate"></i></div>
                    <h4 class="pillar-title">{{ __('Certified Authenticity') }}</h4>
                    <p class="pillar-desc">{{ __('All our equipment comes exclusively from official channels with traceable serial numbers and official manufacturer warranty.') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="80">
                <div class="about-pillar-card">
                    <div class="pillar-icon-box"><i class="fas fa-sliders-h"></i></div>
                    <h4 class="pillar-title">{{ __('Calibration Workshop') }}</h4>
                    <p class="pillar-desc">{{ __('Test lenses, configure your DJI Ronin gimbals and calibrate your wireless systems in our technical studio.') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="160">
                <div class="about-pillar-card">
                    <div class="pillar-icon-box"><i class="fas fa-shield-alt"></i></div>
                    <h4 class="pillar-title">{{ __('Warranty & Reactive Support') }}</h4>
                    <p class="pillar-desc">{{ __('Express after-sales service in case of failure, replacement equipment loan and assistance from qualified technicians.') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="240">
                <div class="about-pillar-card">
                    <div class="pillar-icon-box"><i class="fas fa-file-invoice"></i></div>
                    <h4 class="pillar-title">{{ __('Business Support') }}</h4>
                    <p class="pillar-desc">{{ __('Instant quotes and legal invoicing with ICE for production companies, agencies and institutions.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="about-stats-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">1 500<span class="text-brand-red">+</span></div>
                    <div class="stat-label">{{ __('Equipment & Bodies Delivered') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">100<span class="text-brand-red">%</span></div>
                    <div class="stat-label">{{ __('Products Under Manufacturer Warranty') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">24<span class="text-brand-red">h</span>/48<span class="text-brand-red">h</span></div>
                    <div class="stat-label">{{ __('Secure Morocco Delivery (24h/48h)') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="about-stat-item">
                    <div class="stat-number">99.4<span class="text-brand-red">%</span></div>
                    <div class="stat-label">{{ __('Studio & Director Satisfaction') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- BRANDS --}}
<section class="section-py bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-eyebrow-cine"><i class="fas fa-handshake"></i> {{ __('Official Partners') }}</span>
            <h3 class="fw-bold text-dark font-heading">{{ __('The World\'s Greatest Brands') }}</h3>
        </div>
        <div class="row align-items-center justify-content-center g-4 text-center">
            <div class="col-4 col-md-2"><div class="partner-badge-pill">SONY CINE</div></div>
            <div class="col-4 col-md-2"><div class="partner-badge-pill">CANON EOS</div></div>
            <div class="col-4 col-md-2"><div class="partner-badge-pill">DJI PRO</div></div>
            <div class="col-4 col-md-2"><div class="partner-badge-pill">BLACKMAGIC</div></div>
            <div class="col-4 col-md-2"><div class="partner-badge-pill">SIGMA CINE</div></div>
            <div class="col-4 col-md-2"><div class="partner-badge-pill">APUTURE</div></div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="promo-cta-section">
    <div class="container">
        <div class="promo-cta-card">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="section-eyebrow-cine"><i class="fas fa-video"></i> {{ __('Planning a shoot?') }}</span>
                    <h2 class="promo-cta-title">{{ __("Let's Configure Your Next Rig Together") }}</h2>
                    <p class="promo-cta-desc">{{ __('Visit our showroom to test cameras and optics or contact our specialists for a personalized quote within 2h.') }}</p>
                </div>
                <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                    <a href="{{ route('shop.index') }}" class="btn-hero-shop me-2">
                        <i class="fas fa-th-large"></i> {{ __('View Catalogue') }}
                    </a>
                    <a href="{{ route('contact') }}" class="btn-hero-outline">
                        <i class="fas fa-envelope"></i> {{ __('Contact Us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
