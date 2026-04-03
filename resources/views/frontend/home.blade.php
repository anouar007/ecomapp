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
                <h1 class="display-2 fw-bold mb-4 text-white brand-heading font-corsiva" style="line-height:1.1;">
                    Hijab <span class="text-gold">Princesses</span><br>
                    <span class="fs-2 d-block mt-2 opacity-90">تألقي بلمسة راقية</span>
                </h1>
                <p class="lead mb-5 text-white opacity-90 mx-auto font-body" style="max-width: 650px; font-size: 1.15rem;">
                    اكتشفي تشكيلتنا الحصرية التي تمزج بين الأصالة المغربية واللمسة العصرية لأجمل مناسباتكِ.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('shop.index') }}" class="btn-brand-primary px-5 py-3 text-decoration-none shadow-lg">
                        تسوقي الآن
                        <i class="fas fa-shopping-bag ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
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
