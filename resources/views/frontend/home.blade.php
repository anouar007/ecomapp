@extends('layouts.frontend')

@section('meta_title', setting('app_name') . ' — ' . setting('app_description', 'Équipements d\'impression grand format au Maroc'))
@section('meta_description', 'Découvrez notre gamme d\'imprimantes éco-solvant, traceurs de découpe, encres et consommables. Livraison partout au Maroc, installation et formation incluses. Devis gratuit !')
@section('meta_keywords', setting('app_name', 'boutique') . ', imprimantes grand format Maroc, traceur de découpe, éco-solvant, encres imprimante, consommables impression, équipement atelier impression Maroc')

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
        "name": "Quelle est la différence entre éco-solvant et UV ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "L'éco-solvant utilise des encres à base de solvant doux, idéal pour les supports souples (bâche, vinyle). L'UV imprime directement sur des supports rigides (bois, verre, métal) et sèche instantanément par lampe ultraviolet."
        }
      },
      {
        "@type": "Question",
        "name": "Livrez-vous dans tout le Maroc ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui. Nous livrons partout au Maroc avec notre propre réseau logistique. Les grandes villes bénéficient d'une livraison J+1 et d'une installation sur site incluse."
        }
      },
      {
        "@type": "Question",
        "name": "La formation est-elle incluse à l'achat ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, chaque machine est accompagnée d'une formation opérateur gratuite (1 à 2 jours selon la complexité) réalisée sur votre lieu de production par nos techniciens certifiés."
        }
      },
      {
        "@type": "Question",
        "name": "Proposez-vous des facilités de paiement ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, nous proposons des solutions de financement personnalisées (paiement en plusieurs fois, leasing) selon votre situation. Contactez-nous pour un devis adapté à votre budget."
        }
      },
      {
        "@type": "Question",
        "name": "Quels types d'encres compatibles sont disponibles ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Nous stockons les encres d'origine pour toutes les marques (Roland, Epson, Mimaki...) ainsi que des encres compatibles certifiées offrant un excellent rapport qualité/prix sans altérer la tête d'impression."
        }
      }
    ]
  },
  {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "{{ setting('app_name', 'Speed Platform') }}",
    "operatingSystem": "Web",
    "applicationCategory": "BusinessApplication, eCommerce",
    "author": {
      "@type": "Organization",
      "name": "Elegant Boost",
      "url": "https://elegantboost.com/"
    }
  }
]
</script>
@endsection

@section('content')

{{-- =============================================
     HERO — Brand Aligned
     ============================================= --}}
<section class="hero-brand-section position-relative scroll-parallax-bg" style="height: 90vh; min-height: 600px; display: flex; align-items: center; background-image: url('https://images.unsplash.com/photo-1544605992-8dbce4206cde?auto=format&fit=crop&q=80&w=1920'); background-size: cover; background-position: center; overflow: hidden;">
    <!-- Subtle Gradient Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}, rgba(28,36,16,0.9) 0%, rgba(71,89,39,0.3) 100%); z-index: 1;"></div>
    
    <div class="container position-relative" style="z-index: 3;">
        <div class="row">
            <div class="col-lg-8 col-xl-6 text-white pt-5 scroll-parallax-element" data-aos="fade-up" data-aos-duration="1200">
                <h1 class="display-3 fw-bold mb-4" style="font-family: var(--font-heading); line-height: 1.2; text-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                    {{ __('ديوان الفن: فضاء فني') }}<br>
                    {{ __('لتعليم فنون الخط') }}
                </h1>
                <p class="lead mb-5" style="font-size: 1.35rem; font-weight: 300; opacity: 0.95;" data-aos="fade-up" data-aos-delay="200">
                    {{ __('تجسيد التجربة الفنية الفريدة في كل قطرة حبر') }}
                </p>
                <div data-aos="fade-up" data-aos-delay="400">
                    <a href="{{ url('/workshops') }}" class="btn px-5 py-3 fw-bold text-white shadow-lg hover-lift" style="background: linear-gradient(135deg, var(--primary), var(--primary-mid)); border-radius: 50px; font-size: 1.15rem; transition: all 0.4s ease;">
                        {{ __('استكشف ورشنا') }} <i class="fas fa-arrow-left ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Curved SVG Divider -->
    <div class="position-absolute bottom-0 start-0 w-100" style="z-index: 2; line-height: 0;">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto;">
            <path d="M0 120H1440V0C1440 0 1140 120 720 120C300 120 0 0 0 0V120Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- =============================================
     MARQUEE ANIMATION
     ============================================= --}}
<div class="marquee-container border-top border-bottom">
    <div class="marquee-content">
        <span class="marquee-item">أكاديمية ديوان الفن •</span>
        <span class="marquee-item">الخط العربي الأصيل •</span>
        <span class="marquee-item">تراث وثقافة •</span>
        <span class="marquee-item">فنون إسلامية •</span>
        <span class="marquee-item">ورشات تكوينية •</span>
        <span class="marquee-item">أبحاث أكاديمية •</span>
        <span class="marquee-item">أكاديمية ديوان الفن •</span>
        <span class="marquee-item">الخط العربي الأصيل •</span>
        <span class="marquee-item">تراث وثقافة •</span>
        <span class="marquee-item">فنون إسلامية •</span>
        <span class="marquee-item">ورشات تكوينية •</span>
        <span class="marquee-item">أبحاث أكاديمية •</span>
    </div>
</div>

{{-- =============================================
     TRUST BAR — Calligraphy & Art Focus
     ============================================= --}}
<section class="trust-bar-section bg-white border-bottom shadow-sm">
    <div class="container my-3 py-2">
        <div class="row g-0 trust-bar-row">
            <div class="col-6 col-md-3">
                <div class="trust-bar-item py-3">
                    <i class="fas fa-pen-fancy text-primary fs-3"></i>
                    <div>
                        <strong>ورش تفاعلية</strong>
                        <span>تعليم حي مع كبار الخطاطين</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-bar-item py-3">
                    <i class="fas fa-scroll text-primary fs-3"></i>
                    <div>
                        <strong>دراسات موثقة</strong>
                        <span>أبحاث وتراجم معتمدة تاريخياً</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-bar-item py-3">
                    <i class="fas fa-award text-primary fs-3"></i>
                    <div>
                        <strong>لوحات أصلية</strong>
                        <span>موقعة وموثقة بشهادات أصالة</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-bar-item py-3 border-end-0">
                    <i class="fas fa-globe text-primary fs-3"></i>
                    <div>
                        <strong>مستلزمات فنية</strong>
                        <span>شحن وتوصيل لكافة أنحاء العالم</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     NUMBER COUNTER ANIMATION
     ============================================= --}}
<section class="counter-section bg-surface py-5 border-bottom">
    <div class="container py-3">
        <div class="row text-center" id="counter-wrap">
            <div class="col-6 col-md-3 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="0">
                <h2 class="display-4 fw-bold text-primary mb-2"><span class="counter-val" data-target="15">0</span>+</h2>
                <p class="text-muted fw-bold mb-0">أساتذة خط</p>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="100">
                <h2 class="display-4 fw-bold text-danger mb-2"><span class="counter-val" data-target="200">0</span>+</h2>
                <p class="text-muted fw-bold mb-0">طالب ومريد</p>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <h2 class="display-4 fw-bold text-primary mb-2"><span class="counter-val" data-target="1200">0</span></h2>
                <p class="text-muted fw-bold mb-0">لوحة موثقة</p>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <h2 class="display-4 fw-bold text-danger mb-2"><span class="counter-val" data-target="10">0</span></h2>
                <p class="text-muted fw-bold mb-0">سنوات من العطاء</p>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     ABOUT THE DIWANE (OVERVIEW)
     ============================================= --}}
<section id="about-overview" class="section-py bg-white position-relative">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 scroll-reveal" data-aos="fade-left">
                <div class="pe-lg-4">
                    <h2 class="section-title mb-4 fw-bold" style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); line-height: 1.2; color: var(--text-dark);">
                        ديوان الفن: فضاء فني وثقافي لتعليم الخط العربي
                    </h2>
                    <p class="lead mb-4" style="line-height: 1.9; font-size: 1.15rem; color: #555;">
                        تأسس "ديوان الفن" ليكون منارة حضارية متكاملة تعنى بإحياء التراث الجمالي العربي والإسلامي. رسالتنا لا تقتصر على تلقين القواعد التقنية فحسب، بل نسعى لترسيخ الفلسفة الإبداعية وعمق الهوية وراء كل حرف وقلم.
                    </p>
                    <p class="mb-5" style="line-height: 1.9; color: #666; font-size: 1.05rem;">
                        ندمج بين أصول وقواعد المدرسة الكلاسيكية الأصيلة في تحسين الحروف وأسرارها، وبين تطبيقات التصميم المعاصر والبحث الأكاديمي الرصين، لنمنح مريدينا تجربة معرفية وحسية ترقى بأذواقهم وتصقل مواهبهم.
                    </p>
                    <div class="d-flex gap-4 mt-4 flex-wrap">
                        <a href="{{ url('/about') }}" class="btn px-4 py-3 text-white fw-bold shadow-sm hover-lift" style="background-color: var(--primary); border-radius: 50px;">
                            اكتشف هويتنا ورسالتنا <i class="fas fa-chevron-left ms-2" style="font-size: 0.8rem;"></i>
                        </a>
                        <a href="{{ url('/workshops') }}" class="btn px-4 py-3 fw-bold hover-lift" style="border: 2px solid var(--primary); color: var(--primary); border-radius: 50px;">
                            تصفح الورش المتاحة
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 scroll-reveal" data-aos="fade-right">
                <div class="position-relative hover-zoom-img rounded-4 shadow-lg" style="border: 8px solid #fcfaf8;">
                    <img src="https://images.unsplash.com/photo-1544605992-8dbce4206cde?auto=format&fit=crop&q=80&w=800" alt="تعليم الخط العربي في ديوان الفن" class="img-fluid rounded-4 position-relative w-100" style="z-index: 2; object-fit: cover; aspect-ratio: 4/5;">
                    <!-- Decorative element -->
                    <div class="position-absolute top-0 end-0 translate-middle" style="z-index: 3; background: var(--diwane-gold); color: white; width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: var(--font-heading); font-weight: bold; text-align: center; font-size: 0.9rem; box-shadow: 0 10px 30px rgba(212,168,67,0.4);">
                        إرث <br>أصيل
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     CORE PILLARS / CALLIGRAPHY LIBRARY
     ============================================= --}}
<section id="pillars" class="section-py bg-white">
    <div class="container">
        <div class="text-center mb-5 max-w-700 mx-auto">
            <h2 class="section-title mb-3" style="font-family: var(--font-heading); color: var(--text-dark);">{{ __('مكتبة الخط العربي: التاريخ والأدوات') }}</h2>
            <p class="text-muted">{{ __('تعلم الفنون الأصيلة من كبار الأساتذة') }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Solid Green Card 1 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 text-center hover-lift" style="background-color: var(--primary); color: white;">
                    <div class="mx-auto mb-3" style="font-size: 2.5rem; color: white;">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="font-size: 1.25rem;">عصور الخط</h4>
                    <p class="small mb-0 opacity-75">دراسة أكاديمية لتاريخ فنون الكتابة</p>
                </div>
            </div>

            <!-- Solid Green Card 2 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 text-center hover-lift" style="background-color: var(--primary); color: white;">
                    <div class="mx-auto mb-3" style="font-size: 2.5rem; color: white;">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="font-size: 1.25rem;">أدوات الاستوديو</h4>
                    <p class="small mb-0 opacity-75">خامات الاستوديو المعاصرة والورق المذهب</p>
                </div>
            </div>

            <!-- Outlined White Card 1 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 shadow-sm rounded-3 p-4 text-center hover-lift" style="background-color: white; border: 2px solid var(--primary); color: var(--primary);">
                    <div class="mx-auto mb-3" style="font-size: 2.5rem; color: var(--primary);">
                        <i class="fas fa-drafting-compass"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="font-size: 1.25rem; color: var(--text-dark);">قواعد التناسب</h4>
                    <p class="text-muted small mb-0">قواعد التناسب الهندسية والنسب الذهبية</p>
                </div>
            </div>

            <!-- Outlined White Card 2 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 shadow-sm rounded-3 p-4 text-center hover-lift" style="background-color: white; border: 2px solid var(--primary); color: var(--primary);">
                    <div class="mx-auto mb-3" style="font-size: 2.5rem; color: var(--primary);">
                        <i class="fas fa-tablet-alt"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="font-size: 1.25rem; color: var(--text-dark);">تطبيقات رقمية</h4>
                    <p class="text-muted small mb-0">أدوات التصميم الجرافيكي المعاصر</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     INSTRUCTOR PROFILES (DIWANE MASTERS OVERVIEW)
     ============================================= --}}
<section id="masters-overview" class="section-py bg-surface" style="background-color: var(--bg-warm);">
    <div class="container pt-5">
        <div class="row g-4 justify-content-center mt-5">
            <!-- Master 1 -->
            <div class="col-md-4 mt-5 scroll-reveal" data-aos="fade-up" data-aos-delay="0">
                <div class="card border-0 shadow-sm rounded-4 text-center h-100 px-3 pb-4 pt-5 position-relative hover-lift">
                    <div class="position-absolute top-0 start-50 translate-middle hover-zoom-img" style="border-radius: 50%;">
                        <img src="{{ asset('images/master_khalid_ahmed.png') }}" alt="أستاذ خالد أحمد" class="rounded-circle border border-4 border-white shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <h5 class="fw-bold mt-3 mb-1" style="color: var(--text-dark);">أستاذ خالد أحمد</h5>
                    <p class="small text-muted mb-3" style="font-family: monospace;">Master of Thuluth and Naskh Scripts</p>
                    <p class="small fw-bold mb-4">"القلم يجد الحقيقة داخل الحرف"</p>
                    <h3 class="mt-auto" style="font-family: var(--font-heading); color: var(--primary);">الحقيقة</h3>
                </div>
            </div>

            <!-- Master 2 -->
            <div class="col-md-4 mt-5 scroll-reveal" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm rounded-4 text-center h-100 px-3 pb-4 pt-5 position-relative hover-lift">
                    <div class="position-absolute top-0 start-50 translate-middle hover-zoom-img" style="border-radius: 50%;">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200" alt="الفنانة أمينة بنت سعيد" class="rounded-circle border border-4 border-white shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <h5 class="fw-bold mt-3 mb-1" style="color: var(--text-dark);">الفنانة أمينة بنت سعيد</h5>
                    <p class="small text-muted mb-3" style="font-family: monospace;">Stylistic Diwani and Kufic Expert</p>
                    <p class="small fw-bold mb-4">"متى تترجم أبعاد النص في تلاوين من الخيال، ندرك النطاق اللامحدود للرؤية البصرية التجريدية"</p>
                    <h3 class="mt-auto" style="font-family: var(--font-heading); color: var(--primary);">الجمال</h3>
                </div>
            </div>

            <!-- Master 3 -->
            <div class="col-md-4 mt-5 scroll-reveal" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm rounded-4 text-center h-100 px-3 pb-4 pt-5 position-relative hover-lift">
                    <div class="position-absolute top-0 start-50 translate-middle hover-zoom-img" style="border-radius: 50%;">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200" alt="الأستاذ راشد العالمي" class="rounded-circle border border-4 border-white shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <h5 class="fw-bold mt-3 mb-1" style="color: var(--text-dark);">الأستاذ راشد العالمي</h5>
                    <p class="small text-muted mb-3" style="font-family: monospace;">History and Philosophy of Calligraphy</p>
                    <p class="small fw-bold mb-4">"من هنا الأدبيات المعنى الخفي في البناء الجمالي الثقافي"</p>
                    <h3 class="mt-auto" style="font-family: var(--font-heading); color: var(--primary);"><i class="fas fa-scroll fs-2"></i></h3>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     STUDENT JOURNEYS (BEFORE & AFTER)
     ============================================= --}}
<section id="student-journeys" class="section-py bg-surface border-top border-bottom" style="background-color: var(--bg-warm);">
    <div class="container pb-5">
        <div class="text-center mb-5 max-w-700 mx-auto">
            <h2 class="section-title mb-4" style="font-family: var(--font-heading); color: var(--text-dark);">{{ __('رحلات الطلاب: من مبتدئ إلى خطاط') }}</h2>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Before -->
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="0">
                <h5 class="mb-3" style="color: #666;">Before</h5>
                <img src="https://images.unsplash.com/photo-1544605992-8dbce4206cde?auto=format&fit=crop&q=80&w=400" alt="Before" class="img-fluid rounded-3 shadow-sm mb-3" style="height: 250px; width: 100%; object-fit: cover;">
                <p class="fw-bold" style="color: var(--text-dark);">من بقع الحبر إلى الفن بالصبر</p>
            </div>

            <!-- After -->
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="100">
                <h5 class="mb-3" style="color: #666;">After</h5>
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=400" alt="After" class="img-fluid rounded-3 shadow-sm border border-5 border-white mb-3" style="height: 250px; width: 100%; object-fit: cover;">
                <p class="fw-bold" style="color: var(--text-dark);">من بقع الحبر إلى الفن بالصبر</p>
            </div>

            <!-- Modern Interpretation -->
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="rounded-3 shadow-sm d-flex flex-column justify-content-center align-items-center mb-3 position-relative overflow-hidden" style="height: 250px; background: linear-gradient(135deg, #e0f7fa 0%, #ffcdd2 100%);">
                    <h2 class="display-4 fw-bold" style="font-family: 'Cairo', sans-serif; color: var(--secondary); text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">تأويل الخيال</h2>
                </div>
                <div class="py-2 rounded-bottom" style="background-color: var(--primary); color: white; margin-top: -20px; z-index: 2; position: relative;">
                    تأويل حديث من الطالبة ليلى
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =============================================
     CREATIVE GALLERY (OVERVIEW)
     ============================================= --}}
<section id="gallery-overview" class="section-py" style="background-color: var(--primary);">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title mb-3" style="font-family: var(--font-heading); color: white;">معرض الإبداع</h2>
            <p style="color: rgba(255,255,255,0.8);">اكتشف جماليات الحرف العربي الأصيل</p>
        </div>

        <div class="row g-3">
            <div class="col-6 col-md-4 hover-zoom-img rounded-2" data-aos="fade-up" data-aos-delay="0">
                <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=600" alt="Gallery" class="img-fluid shadow-sm w-100" style="height: 250px; object-fit: cover;">
            </div>
            <div class="col-6 col-md-4 hover-zoom-img rounded-2" data-aos="fade-up" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&q=80&w=600" alt="Gallery" class="img-fluid shadow-sm w-100" style="height: 250px; object-fit: cover;">
            </div>
            <div class="col-6 col-md-4 hover-zoom-img rounded-2" data-aos="fade-up" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=600" alt="Gallery" class="img-fluid shadow-sm w-100" style="height: 250px; object-fit: cover;">
            </div>
            <div class="col-6 col-md-4 hover-zoom-img rounded-2" data-aos="fade-up" data-aos-delay="300">
                <img src="https://images.unsplash.com/photo-1544605992-8dbce4206cde?auto=format&fit=crop&q=80&w=600" alt="Gallery" class="img-fluid shadow-sm w-100" style="height: 250px; object-fit: cover;">
            </div>
            <div class="col-6 col-md-4 hover-zoom-img rounded-2" data-aos="fade-up" data-aos-delay="400">
                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=600" alt="Gallery" class="img-fluid shadow-sm w-100" style="height: 250px; object-fit: cover;">
            </div>
            <div class="col-6 col-md-4 hover-zoom-img rounded-2" data-aos="fade-up" data-aos-delay="500">
                <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=600" alt="Gallery" class="img-fluid shadow-sm w-100" style="height: 250px; object-fit: cover;">
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ url('/gallery') }}" class="btn px-4 py-2" style="background-color: white; color: var(--primary); border-radius: 4px; font-weight: bold;">تصفح المعرض الكامل</a>
        </div>
    </div>
</section>

{{-- =============================================
     THE ARTISTIC SHOP
     ============================================= --}}
<section id="featured" class="section-py bg-white">
    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="section-title mb-2" style="font-family: var(--font-heading); color: var(--text-dark);">المتجر الفني</h2>
                <p class="text-muted mb-0">The Artistic Shop</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn px-4 py-2 text-white" style="background-color: var(--primary); border-radius: 4px;">{{ __('عرض كل المنتجات') }}</a>
        </div>

        <div class="row g-5">
            <!-- Sidebar Categories -->
            <div class="col-lg-3 d-none d-lg-block">
                <h4 class="mb-4 fw-bold pb-2" style="border-bottom: 2px solid var(--primary); color: var(--text-dark); display: inline-block;">الفئات</h4>
                <ul class="list-unstyled" style="font-size: 1.1rem;">
                    <li class="mb-3"><a href="#" class="text-decoration-none" style="color: var(--primary); font-weight: bold;"><i class="fas fa-angle-left ms-2"></i>الأعمال الأصلية</a></li>
                    <li class="mb-3"><a href="#" class="text-decoration-none text-muted"><i class="fas fa-angle-left ms-2 opacity-50"></i>المطبوعات</a></li>
                    <li class="mb-3"><a href="#" class="text-decoration-none text-muted"><i class="fas fa-angle-left ms-2 opacity-50"></i>الأدوات</a></li>
                    <li class="mb-3"><a href="#" class="text-decoration-none text-muted"><i class="fas fa-angle-left ms-2 opacity-50"></i>الأوراق</a></li>
                </ul>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="row g-4">
                    <!-- Product 1 -->
                    <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="0">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden text-center hover-lift">
                            <img src="https://images.unsplash.com/photo-1544605992-8dbce4206cde?auto=format&fit=crop&q=80&w=400" alt="قطعة ثلث أصلية" class="card-img-top" style="height: 250px; object-fit: cover;">
                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="card-title fw-bold mb-3" style="color: var(--text-dark);">قطعة ثلث أصلية: الرحمن</h5>
                                <p class="card-text fw-bold fs-5 mb-4" style="color: var(--primary);">1500 MAD</p>
                                <button class="btn mt-auto py-2 w-100 text-white shadow-sm" style="background-color: var(--primary); border-radius: 4px;">
                                    أضف للسلة <i class="fas fa-shopping-cart ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden text-center hover-lift">
                            <img src="https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&q=80&w=400" alt="المطبوعات المذهبة" class="card-img-top" style="height: 250px; object-fit: cover;">
                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="card-title fw-bold mb-3" style="color: var(--text-dark);">المطبوعات المذهبة</h5>
                                <p class="card-text fw-bold fs-5 mb-4" style="color: var(--primary);">450 MAD</p>
                                <button class="btn mt-auto py-2 w-100 text-white shadow-sm" style="background-color: var(--primary); border-radius: 4px;">
                                    أضف للسلة <i class="fas fa-shopping-cart ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden text-center hover-lift">
                            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=400" alt="قلم بامبو مقطوع" class="card-img-top" style="height: 250px; object-fit: cover;">
                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="card-title fw-bold mb-3" style="color: var(--text-dark);">قلم بامبو مقطوع بالليزر</h5>
                                <p class="card-text fw-bold fs-5 mb-4" style="color: var(--primary);">120 MAD</p>
                                <button class="btn mt-auto py-2 w-100 text-white shadow-sm" style="background-color: var(--primary); border-radius: 4px;">
                                    أضف للسلة <i class="fas fa-shopping-cart ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
 


{{-- =============================================
     PROMOTIONAL CTA BANNER
     ============================================= --}}
<section class="promo-cta-section">
    <div class="container">
{{-- =============================================
     MASTERS RESEARCH & ARCHIVES
     ============================================= --}}
<section id="research-archives" class="section-py bg-surface border-top" style="background-color: var(--bg-warm);">
    <div class="container pb-5">
        <div class="text-center mb-5">
            <h2 class="section-title mb-3" style="font-family: var(--font-heading); color: var(--text-dark);">البحوث والأساتذة</h2>
            <p class="text-muted">تصفح الأرشيف الأكاديمي والكتب المحققة في تاريخ الخط</p>
        </div>

        <!-- Research Papers -->
        <div class="row g-4 justify-content-center mb-5">
            <!-- Paper 1 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 hover-lift text-end" style="background-color: white;">
                    <div class="mb-3 text-primary fs-3"><i class="fas fa-file-pdf"></i></div>
                    <h5 class="fw-bold mb-3" style="color: var(--text-dark); line-height: 1.5;">أطروحة في الخط الديواني: تاريخ وتطور</h5>
                    <p class="small text-muted mb-4">بحث تفصيلي للأستاذ خالد أحمد حول نشأة الخط الديواني وتطوره في العهد العثماني.</p>
                    <a href="#" class="btn mt-auto py-2 px-4 text-white shadow-sm d-inline-block w-auto" style="background-color: var(--primary); border-radius: 4px;">
                        تحميل PDF <i class="fas fa-download ms-2"></i>
                    </a>
                </div>
            </div>

            <!-- Paper 2 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 hover-lift text-end" style="background-color: white;">
                    <div class="mb-3 text-primary fs-3"><i class="fas fa-file-pdf"></i></div>
                    <h5 class="fw-bold mb-3" style="color: var(--text-dark); line-height: 1.5;">فلسفة الجمال في التجريد الحروفي</h5>
                    <p class="small text-muted mb-4">ورقة بحثية للفنانة أمينة تركز على الأبعاد الفلسفية للخط العربي الحديث.</p>
                    <a href="#" class="btn mt-auto py-2 px-4 text-white shadow-sm d-inline-block w-auto" style="background-color: var(--primary); border-radius: 4px;">
                        تحميل PDF <i class="fas fa-download ms-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- Paper 3 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 hover-lift text-end" style="background-color: white;">
                    <div class="mb-3 text-primary fs-3"><i class="fas fa-file-pdf"></i></div>
                    <h5 class="fw-bold mb-3" style="color: var(--text-dark); line-height: 1.5;">النسبة الذهبية في خط الثلث</h5>
                    <p class="small text-muted mb-4">دراسة هندسية تطبيقية من إعداد الأستاذ راشد لتحليل البنية الهندسية لحروف الثلث.</p>
                    <a href="#" class="btn mt-auto py-2 px-4 text-white shadow-sm d-inline-block w-auto" style="background-color: var(--primary); border-radius: 4px;">
                        تحميل PDF <i class="fas fa-download ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Masters Library Books -->
        <div class="text-center mt-5 mb-4">
            <h3 class="fw-bold" style="font-family: var(--font-heading); color: var(--text-dark);">مكتبة الأساتذة</h3>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="0">
                <img src="https://images.unsplash.com/photo-1544605992-8dbce4206cde?auto=format&fit=crop&q=80&w=300" alt="كتاب 1" class="img-fluid rounded-3 shadow-sm border border-3 border-white">
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&q=80&w=300" alt="كتاب 2" class="img-fluid rounded-3 shadow-sm border border-3 border-white">
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1544605992-8dbce4206cde?auto=format&fit=crop&q=80&w=300" alt="كتاب 3" class="img-fluid rounded-3 shadow-sm border border-3 border-white">
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
                <img src="https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&q=80&w=300" alt="كتاب 4" class="img-fluid rounded-3 shadow-sm border border-3 border-white">
            </div>
        </div>
    </div>
</section>


{{-- =============================================
     TESTIMONIALS
     ============================================= --}}
@if($testimonials->count() > 0)
<section class="section-py bg-surface">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">{{ __('Customer Reviews') }}</h2>
            <p class="text-muted">{{ __('What our customers say about us') }}</p>
        </div>
        <div class="row g-4">
            @foreach($testimonials->take(3) as $i => $t)
            <div class="col-md-4">
                <div class="review-card">
                    <div class="review-stars">
                        @for($s = 0; $s < $t->rating; $s++)<i class="fas fa-star"></i>@endfor
                    </div>
                    <p class="review-text">"{{ Str::limit($t->content, 160) }}"</p>
                    <div class="review-author">
                        <div class="review-avatar">{{ strtoupper(substr($t->name, 0, 1)) }}</div>
                        <div>
                            <div class="review-name">{{ $t->name }}</div>
                            <div class="review-role">{{ __('Verified Customer') }}</div>
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
// Hero Slider
const heroSwiper = new Swiper('.hero-swiper', {
    loop: true,
    autoplay: {
        delay: 6000,
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
        renderBullet: function (index, className) {
            return '<span class="' + className + '"><i></i></span>';
        },
    }
});

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

// Counter Animation Logic
const counters = document.querySelectorAll('.counter-val');
const speed = 200; // The lower the slower

const animateCounters = () => {
    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const inc = target / speed;

            if (count < target) {
                counter.innerText = Math.ceil(count + inc);
                setTimeout(updateCount, 15);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });
};

// Intersection Observer to trigger counter when visible
const counterSection = document.getElementById('counter-wrap');
if (counterSection) {
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            animateCounters();
            observer.disconnect();
        }
    }, { threshold: 0.5 });
    observer.observe(counterSection);
}
</script>
@endpush
