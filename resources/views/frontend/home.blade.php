@extends('layouts.frontend')

@section('meta_title', 'أناقة الأميرة — متجر العبايات والخمارات الفاخرة')
@section('meta_description', 'اكتشفي تشكيلتنا الحصرية من العبايات الفاخرة والخمارات الأنيقة. جودة عالية وتوصيل لكل مدن المغرب.')

@section('json_ld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "{{ url('/') }}/#organization",
      "name": "{{ setting('app_name', 'Hijab Princesses — أناقة الأميرة') }}",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/logo.png') }}"
      },
      "sameAs": [
        "https://www.facebook.com/hijabprincesses",
        "https://www.instagram.com/hijabprincesses",
        "https://www.tiktok.com/@hijabprincesses"
      ],
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{{ setting('app_phone', '+212-000-000000') }}",
        "contactType": "customer service",
        "areaServed": "MA",
        "availableLanguage": ["Arabic", "French", "English"]
      }
    },
    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}/#website",
      "url": "{{ url('/') }}",
      "name": "Hijab Princesses",
      "publisher": { "@id": "{{ url('/') }}/#organization" },
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/shop?search={search_term_string}') }}",
        "query-input": "required name=search_term_string"
      }
    }
  ]
}
</script>
@endsection

@section('content')

{{-- =============================================
     IMMERSIVE FASHION HERO
     ============================================= --}}
<section class="hero-immersive bg-brand-fashion bg-brand-overlay d-flex align-items-center position-relative" style="min-height: 95vh; overflow: hidden;">
    {{-- Fluid Silk Overlay --}}
    <div class="silk-mist-overlay"></div>
    
    <div class="container px-xl-5 position-relative" style="z-index: 2;">
        <div class="hero-content text-center py-5" data-aos="zoom-out" data-aos-duration="1500">
            <div class="glass-capsule-dark mb-4 mx-auto" style="max-width: 850px;">
                <span class="text-uppercase tracking-widest text-gold fw-bold mb-3 d-block small" style="letter-spacing: 4px;">المجموعة الجديدة</span>
                <h1 class="display-2 fw-bold mb-4 text-gold brand-heading font-corsiva" style="line-height:1.1;">
                    Hijab Princesses<br>
                    <span class="fs-2 d-block mt-2 opacity-90 text-white">تألقي بلمسة راقية</span>
                </h1>
                <p class="lead mb-5 text-white opacity-90 mx-auto font-body" style="max-width: 650px; font-size: 1.15rem;">
                    اكتشفي تشكيلتنا الحصرية التي تمزج بين الأصالة المغربية واللمسة العصرية لأجمل مناسباتكِ.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="#catalog" class="btn-brand-primary px-5 py-3 text-decoration-none shadow-lg">
                        تسوقي الآن
                        <i class="fas fa-shopping-bag ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── SHOP HERO & VISUAL CATEGORY NAVIGATOR ──────────────────── --}}
<section id="catalog" class="shop-hero py-4 py-lg-5 bg-white">
    <div class="container px-xl-5 text-center">
        <h2 class="display-6 brand-heading mb-2 text-dark soft-glow-text font-corsiva" data-aos="fade-down">
            اكتشفي تشكيلة <span class="text-gold">Hijab Princesses</span>
        </h2>
        
        {{-- Visual Category Navigator (Story Pills) --}}
        <div class="category-story-track d-flex justify-content-lg-center" data-aos="fade-up">
            {{-- All (Story Pill) --}}
            <a href="#catalog" class="category-story-pill d-none" data-slug="" style="display: none !important;">
                <div class="category-story-img-wrapper">
                    <div class="category-story-img d-flex align-items-center justify-content-center bg-white border border-gold-light" style="font-size: 1.25rem;">
                       <i class="fas fa-border-all text-gold"></i>
                    </div>
                </div>
                <span class="category-story-label">عرض الكل</span>
            </a>

            @foreach($allCategories as $cat)
                <a href="#category-{{ $cat->slug }}" 
                   class="category-story-pill"
                   data-slug="{{ $cat->slug }}">
                    <div class="category-story-img-wrapper">
                        <img src="{{ $cat->image ? (Str::startsWith($cat->image, 'http') ? $cat->image : Storage::url($cat->image)) : asset('images/placeholder-cat.jpg') }}" 
                             class="category-story-img" alt="{{ $cat->translated_name }}">
                    </div>
                    <span class="category-story-label">{{ $cat->translated_name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ── PRODUCT CATALOG ────────────────────────────────────────── --}}
<section class="section-py bg-surface position-relative" style="border-top: 1px solid rgba(0,0,0,0.02);">
    {{-- Loading Overlay --}}
    <div id="catalog-loader" class="position-absolute top-0 start-0 w-100 h-100 bg-white-50 d-none align-items-center justify-content-center" style="z-index: 10; backdrop-filter: blur(2px);">
        <div class="spinner-border text-gold" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container px-xl-5" id="catalog-container">
        @include('frontend.partials.catalog-content')
    </div>
</section>

{{-- =============================================
     BOUTIQUE INTERIOR (The Experience)
     ============================================= --}}
        </div>
    </div>
</section>

{{-- =============================================
     ELITE TESTIMONIALS (Social Proof)
     ============================================= --}}
<section class="section-py testimonial-luxe-section overflow-hidden">
    {{-- Smooth Section Transition --}}
    <div class="section-divider-silk"></div>
    
    <div class="container px-xl-5">
        <div class="section-header mb-5 text-center" data-aos="fade-up">
            <span class="text-gold fw-bold small text-uppercase ls-2 mb-2 d-block">صدى الجمال</span>
            <h2 class="brand-heading mb-0">كلمات من أميراتنا</h2>
            <div class="bg-gold mt-3 rounded mx-auto" style="width: 40px; height: 2px;"></div>
        </div>

        <div class="row g-4 justify-content-center">
            @php
            $testimonials = [
                ['name'=>'سارة ا.', 'city'=>'الرباط', 'text'=>'العباية التي طلبتها تجاوزت توقعاتي. جودة القماش واللمسات النهائية راقية فعلاً.'],
                ['name'=>'مريم ب.', 'city'=>'الدار البيضاء', 'text'=>'توصيل سريع وتغليف الفاخمر جعلني أشعر كأنها هدية لنفسي. شكراً بوتيك الأميرات.'],
                ['name'=>'خديجة م.', 'city'=>'طنجة', 'text'=>'أناقة لا توصف. التصميم يجمع بين الحشمة والعصرنة بشكل فريد جداً.'],
            ];
            @endphp
            @foreach($testimonials as $i => $item)
            @php $initials = mb_substr($item['name'], 0, 1, 'UTF-8'); @endphp
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="testimonial-card-luxe">
                    <div class="tcard-stars mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <blockquote class="font-body mb-4" style="font-size: 0.95rem; line-height: 1.85; color: #475569; border: none; padding: 0;">
                        &ldquo;{{ $item['text'] }}&rdquo;
                    </blockquote>
                    <div class="d-flex align-items-center gap-3">
                        <div class="tcard-avatar">{{ $initials }}</div>
                        <div>
                            <div class="fw-800 text-dark brand-heading" style="font-size: 0.95rem;">{{ $item['name'] }}</div>
                            <div class="small text-muted d-flex align-items-center gap-1">
                                <i class="fas fa-map-marker-alt" style="font-size: 0.65rem; color: #c5a059;"></i>
                                {{ $item['city'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- =============================================
     SHOPPING PROCESS (Tactile Silk Texture)
     ============================================= --}}
{{-- =============================================
     SHOPPING PROCESS (Tactile Silk Texture)
     ============================================= --}}
<section class="section-py bg-silk bg-brand-overlay-light">
    <div class="container text-center px-xl-5">
        <div class="section-header mb-5" data-aos="fade-up">
            <div class="glass-capsule mb-2">
                <h2 class="brand-heading text-dark m-0">كيف تتسوقين كالأميرة؟</h2>
                <div class="bg-gold mt-3 rounded mx-auto" style="width: 50px; height: 3px;"></div>
                <p class="text-muted mt-3 font-body mb-0 fw-bold">خطوات بسيطة تضمن لكِ وصول طلبكِ بكامل أناقته</p>
            </div>
        </div>

        <div class="row g-4 mt-2">
            @php
            $steps = [
                ['icon'=>'fa-shopping-cart', 'title'=>'الطلب من الموقع', 'desc'=>'اختاري قطعك المفضلة وأضيفيها لسلة التسوق ثم أكملي الطلب بسهولة.'],
                ['icon'=>'fa-phone-alt',     'title'=>'مكالمة التأكيد', 'desc'=>'سيقوم فريقنا بالاتصال بك لتأكيد المقاسات وتجهيز طلبك بعناية.'],
                ['icon'=>'fa-box-open',      'title'=>'تجهيز الطلب',    'desc'=>'يتم تغليف طلبك بأرقى الأساليب لضمان وصوله إليك كهدية فاخرة.'],
                ['icon'=>'fa-truck',         'title'=>'التوصيل للمنزل', 'desc'=>'يصلك المندوب حتى باب بيتك، والدفع عند الاستلام بكل أمان.'],
            ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="brand-card p-4 border-0 shadow-premium h-100 bg-white-90 backdrop-blur">
                    <div class="app-icon mx-auto mb-4 bg-gold-light text-gold rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <i class="fas {{ $step['icon'] }}"></i>
                    </div>
                    <h5 class="brand-heading h6 mb-2 text-dark">{{ $step['title'] }}</h5>
                    <p class="small text-muted font-body mb-0" style="font-size: 0.75rem; line-height: 1.6;">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =============================================
     SOCIAL MEDIA INVITATION
     ============================================= --}}
<section class="section-py social-invite-section" data-aos="fade-up">
    <div class="container px-xl-5">
        <div class="text-center mb-5">
            <span class="text-gold fw-bold small text-uppercase" style="letter-spacing: 3px;">تابعينا</span>
            <h2 class="brand-heading mt-2 mb-0">انضمي إلى عالم الأميرات</h2>
            <div class="bg-gold mt-3 rounded mx-auto" style="width: 40px; height: 2px;"></div>
            <p class="text-muted mt-3 font-body mb-0" style="max-width: 500px; margin: auto;">إطلالات حصرية، مجموعات جديدة، وخلف الكواليس — كلها في انتظارك.</p>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Instagram Card --}}
            <div class="col-12 col-md-5">
                <a href="https://www.instagram.com/hijab_.princesses/" target="_blank" rel="noopener noreferrer" class="social-platform-card instagram-card d-block text-decoration-none">
                    <div class="social-platform-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <div class="social-platform-name">Instagram</div>
                    <div class="social-platform-handle">@hijab_.princesses</div>
                </a>
            </div>

            {{-- TikTok Card --}}
            <div class="col-12 col-md-5">
                <a href="https://www.tiktok.com/@hijab_princesses1" target="_blank" rel="noopener noreferrer" class="social-platform-card tiktok-card d-block text-decoration-none">
                    <div class="social-platform-icon">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <div class="social-platform-name">TikTok</div>
                    <div class="social-platform-handle">@hijab_princesses1</div>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .bg-white-50 {
        background: rgba(255,255,255,0.7) !important;
    }
    .hero-immersive {
        background-attachment: fixed;
    }
    .bg-white-90 {
        background: rgba(255,255,255,0.9) !important;
    }
    .backdrop-blur {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .border-gold-subtle {
        border: 1px solid rgba(197, 160, 89, 0.2) !important;
    }
    @media (max-width: 991px) {
        .hero-immersive {
            background-attachment: scroll;
            min-height: 70vh !important;
        }
        .glass-capsule-dark {
            padding: 1.5rem;
        }
        .display-2 { font-size: 2.5rem; }
    }
</style>
@endpush
@push('scripts')
<script>
    // Smooth scroll for category pills
    document.querySelectorAll('.category-story-pill').forEach(pill => {
        pill.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId.startsWith('#')) {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const offset = 100; // Account for header height
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Update active class
                    document.querySelectorAll('.category-story-pill').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                }
            }
        });
    });

    function loadCategory(slug) {
        // This function is now mostly used for search/sort AJAX
        // and can be simplified or used for jump-to logic.
        const targetElement = document.getElementById('category-' + slug);
        if (targetElement) {
            const offset = 100;
            const elementPosition = targetElement.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;
            window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
        }
    }

    // Handle pagination links
    document.addEventListener('click', function(e) {
        if (e.target.closest('.brand-pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const container = document.getElementById('catalog-container');
            const loader = document.getElementById('catalog-loader');

            loader.classList.remove('d-none');
            loader.classList.add('d-flex');

            window.history.pushState({}, '', url);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                container.innerHTML = data.html;
                if (window.AOS) window.AOS.refreshHard();
                document.getElementById('catalog').scrollIntoView({ behavior: 'smooth' });
            })
            .finally(() => {
                loader.classList.add('d-none');
                loader.classList.remove('d-flex');
            });
        }
    });
</script>
@endpush
