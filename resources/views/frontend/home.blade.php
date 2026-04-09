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
<section class="hero-immersive bg-brand-fashion d-flex align-items-center position-relative" style="min-height: 90vh; overflow: hidden;">
    <div class="container px-xl-5 text-center">
        {{-- Logo Section --}}
        <div class="mb-3">
            @if(setting('app_logo'))
                <img src="{{ Storage::url(setting('app_logo')) }}" alt="{{ setting('app_name', 'Hijab Princesses') }}" class="hero-logo-elegant">
            @else
                <div class="brand-logo-text hero-luxury-text">
                    Hijab <span class="gold-part">Princesses</span>
                </div>
            @endif
        </div>

        {{-- CTA --}}
        <div>
            <a href="#catalog" class="btn-elegant-gold">
                <span>تسوقي الآن</span>
                <i class="fa-solid fa-cart-shopping bold small opacity-1"></i>
            </a>
        </div>
    </div>
</section>

{{-- ── SHOP HERO & VISUAL CATEGORY NAVIGATOR ──────────────────── --}}
<section id="catalog" class="shop-hero py-0 py-lg-5 bg-white">
    {{-- Welcome Header (Moved Below Logo) --}}
    <div class="mb-3 text-center">
        <span class="hero-welcome-label">مرحبا بكم</span>
    </div>
    <div class="container px-xl-5 text-center">
        
        {{-- Visual Category Navigator (Story Pills) --}}
        <div class="category-story-track d-flex justify-content-lg-center" data-aos="fade-up">
            {{-- All (Story Pill) --}}
            <a href="#catalog" class="category-story-pill active" data-slug="">
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
<section class="section-py bg-surface position-relative pt-2" style="border-top: 1px solid rgba(0,0,0,0.02);">
    {{-- Loading Overlay --}}
    <div id="catalog-loader" class="position-absolute top-0 start-0 w-100 h-100 bg-white-50 d-none align-items-center justify-content-center" style="z-index: 10; backdrop-filter: blur(2px);">
        <div class="spinner-border text-gold" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container px-xl-5" id="catalog-container">
        @include('frontend.partials.catalog-content')
    </div>

    {{-- Load More Action --}}
    <div class="text-center mt-5 mb-5 {{ $hasMore ? '' : 'd-none' }}" id="load-more-container" data-aos="fade-up">
        <button id="load-more-btn" class="btn btn-brand-outline px-5 py-3 rounded-pill fw-bold shadow-sm transition-all">
            <span>عرض المزيد من الموديلات</span>
            <i class="fas fa-plus ms-2"></i>
        </button>
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
<section class="section-py bg-silk bg-brand-overlay-light d-none">
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
            min-height: 50vh !important;
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
    let currentCategory = '';
    let currentPage = 1;

    // 1. AJAX Category Filtering
    document.querySelectorAll('.category-story-pill').forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            
            // UI Updates
            document.querySelectorAll('.category-story-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            
            // Scroll to start of catalog
            const catalogTop = document.getElementById('catalog').getBoundingClientRect().top + window.pageYOffset - 100;
            window.scrollTo({ top: catalogTop, behavior: 'smooth' });
            
            // Reset State & Fetch
            currentCategory = this.dataset.slug;
            currentPage = 1;
            fetchProducts(true);
        });
    });

    // 2. AJAX Load More
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            currentPage++;
            fetchProducts(false);
        });
    }

    /**
     * Fetch products via AJAX
     * @param {Boolean} isNewFilter If true, replace container. Otherwise append to grid.
     */
    function fetchProducts(isNewFilter = false) {
        const loader = document.getElementById('catalog-loader');
        const container = document.getElementById('catalog-container');
        const loadMoreContainer = document.getElementById('load-more-container');
        const btn = document.getElementById('load-more-btn');

        if (isNewFilter) {
            loader.classList.remove('d-none');
            loader.classList.add('d-flex');
        } else {
            btn.innerHTML = '<span>جاري التحميل...</span> <i class="fas fa-circle-notch fa-spin ms-2"></i>';
            btn.disabled = true;
        }

        const params = new URLSearchParams({
            page: currentPage,
            category: currentCategory || ''
        });

        const url = `${window.location.origin}${window.location.pathname}?${params.toString()}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (isNewFilter) {
                container.innerHTML = data.html;
            } else {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                const newItems = tempDiv.querySelectorAll('#products-grid > div');
                const grid = document.getElementById('products-grid');
                if (grid) {
                    newItems.forEach(item => grid.appendChild(item));
                }
            }

            // Sync Button State
            if (data.hasMore) {
                loadMoreContainer.classList.remove('d-none');
                btn.innerHTML = '<span>عرض المزيد</span> <i class="fas fa-plus ms-2"></i>';
                btn.disabled = false;
            } else {
                loadMoreContainer.classList.add('d-none');
            }

            // Refresh animations
            if (window.AOS) window.AOS.refreshHard();
        })
        .catch(err => {
            console.error('Fetch error:', err);
            if (!isNewFilter) {
                btn.innerHTML = '<span>حاول مرة أخرى</span> <i class="fas fa-sync ms-2"></i>';
                btn.disabled = false;
            }
        })
        .finally(() => {
            if (isNewFilter) {
                loader.classList.add('d-none');
                loader.classList.remove('d-flex');
            }
        });
    }
</script>
@endpush
