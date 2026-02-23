<!DOCTYPE html>
<html lang="{{ setting('language', 'en') }}" dir="{{ setting('text_direction', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', setting('app_name', 'Speed Platform'))</title>
    <meta name="description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', setting('app_name', 'Speed Platform'))">
    <meta property="og:description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta property="og:image" content="@yield('meta_image', asset('images/og-default.jpg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('meta_title', setting('app_name', 'Speed Platform'))">
    <meta property="twitter:description" content="@yield('meta_description', setting('app_description', 'High performance e-commerce platform.'))">
    <meta property="twitter:image" content="@yield('meta_image', asset('images/og-default.jpg'))">
    
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Custom Head Codes -->
    @php
        $headCodes = \App\Models\CustomCode::where('is_active', true)
            ->where('position', 'head')
            ->orderBy('priority', 'desc')
            ->get();
    @endphp
    @foreach($headCodes as $code)
        @if($code->type == 'css')
            <style>{!! $code->content !!}</style>
        @elseif($code->type == 'js')
            <script>{!! $code->content !!}</script>
        @else
            {!! $code->content !!}
        @endif
    @endforeach
</head>
<body>
    <!-- Custom Body Start Codes -->
    @php
        $bodyStartCodes = \App\Models\CustomCode::where('is_active', true)
            ->where('position', 'body_start')
            ->orderBy('priority', 'desc')
            ->get();
    @endphp
    @foreach($bodyStartCodes as $code)
        {!! $code->content !!}
    @endforeach




    <main>
        @yield('content')
    </main>



    <footer class="site-footer">
        <div class="container">
            <div class="row g-5">

                {{-- Brand column --}}
                <div class="col-lg-4 pe-lg-5">
                    @if(setting('app_logo'))
                        <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="height:64px; max-width:200px; object-fit:contain; margin-bottom:16px; filter:drop-shadow(0 2px 16px rgba(233,164,37,.22));">
                    @else
                        <div class="footer-brand">{{ setting('app_name', 'Jawhara') }}</div>
                    @endif
                    <p class="footer-bio">
                        Votre partenaire en communication visuelle à Casablanca. Impression grand format, petit format, offset et DTF avec un savoir-faire de plus de 10 ans.
                    </p>
                    <div class="f-social">
                        @if(setting('social_facebook'))
                            <a href="{{ setting('social_facebook') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(setting('social_instagram'))
                            <a href="{{ setting('social_instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(setting('social_twitter'))
                            <a href="{{ setting('social_twitter') }}" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if(setting('social_linkedin'))
                            <a href="{{ setting('social_linkedin') }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        @endif
                        <a href="https://wa.me/212700331450" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="col-6 col-lg-2">
                    <h6 class="f-head">Navigation</h6>
                    <a href="#home" onclick="smoothTo('home'); return false;" class="f-link">Accueil</a>
                    <a href="#about" onclick="smoothTo('about'); return false;" class="f-link">&Agrave; Propos</a>
                    <a href="#services" onclick="smoothTo('services'); return false;" class="f-link">Services</a>
                    <a href="#projects" onclick="smoothTo('projects'); return false;" class="f-link">Projets</a>
                    <a href="#clients" onclick="smoothTo('clients'); return false;" class="f-link">Clients</a>
                </div>

                {{-- Services list --}}
                <div class="col-6 col-lg-2">
                    <h6 class="f-head">Expertises</h6>
                    <span class="f-link">Grand Format</span>
                    <span class="f-link">Petit Format</span>
                    <span class="f-link">Offset</span>
                    <span class="f-link">DTF Textile</span>
                </div>

                {{-- Contact --}}
                <div class="col-lg-4">
                    <h6 class="f-head">Contact</h6>
                    <div class="f-contact-row">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>N 28 GH 1, Lotissement Essalam, Casablanca</span>
                    </div>
                    <div class="f-contact-row">
                        <i class="fas fa-phone-alt"></i>
                        <a href="tel:0700331450">0700-331450</a>
                    </div>
                    <div class="f-contact-row">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:contact@jawharaproprint.com">contact@jawharaproprint.com</a>
                    </div>
                    <div class="f-contact-row">
                        <i class="fas fa-clock"></i>
                        <span>Lun – Sam · 9h00 – 18h00</span>
                    </div>
                </div>

            </div>

            <hr class="footer-hr">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0 footer-copy">
                    &copy; {{ date('Y') }} <span>{{ setting('app_name', 'Jawhara Pro Print') }}</span>. Tous droits réservés.
                </div>
                <div class="col-md-6 text-center text-md-end footer-copy">
                    Casablanca · Impression professionnelle
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to top -->
    <button class="back-top" aria-label="Retour en haut"><i class="fas fa-chevron-up"></i></button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
    /* ═══════════════════════════════════════════════
       SCROLL ANIMATION ENGINE — Jawhara Pro Print
       Includes: AOS · progress bar · parallax ·
                 sr-reveals · eyebrow trigger · spy
    ═══════════════════════════════════════════════ */

    /* ── AOS (Animate On Scroll) ─────────────────── */
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-quart',
            once: true,
            offset: 70,
            delay: 0
        });
    }

    /* ── Scroll progress bar ─────────────────────── */
    (function () {
        var bar = document.createElement('div');
        bar.className = 'scroll-progress';
        document.body.prepend(bar);

        window.addEventListener('scroll', function () {
            var h   = document.documentElement;
            var pct = (h.scrollTop / (h.scrollHeight - h.clientHeight)) * 100;
            bar.style.width = pct.toFixed(2) + '%';
        }, { passive: true });
    })();

    document.addEventListener('DOMContentLoaded', function () {

        /* ── Smooth-scroll helper ───────────────── */
        window.smoothTo = window.smoothTo || function (id) {
            var el = document.getElementById(id);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        /* ── Header compact on scroll ───────────── */
        var header = document.querySelector('.site-header');
        window.addEventListener('scroll', function () {
            if (header) header.classList.toggle('scrolled', window.scrollY > 60);
        }, { passive: true });

        /* ── Back-to-top button ─────────────────── */
        var backBtn = document.querySelector('.back-top');
        if (backBtn) {
            window.addEventListener('scroll', function () {
                backBtn.classList.toggle('show', window.scrollY > 400);
            }, { passive: true });
            backBtn.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        /* ── ScrollSpy (IntersectionObserver) ───────*/
        var navLinks = document.querySelectorAll('.nav-link');
        var spy = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    navLinks.forEach(function (l) {
                        l.classList.remove('active');
                        if (l.getAttribute('href') === '#' + entry.target.id) {
                            l.classList.add('active');
                        }
                    });
                }
            });
        }, { threshold: 0.3, rootMargin: '-80px 0px -45% 0px' });
        document.querySelectorAll('section[id]').forEach(function (s) { spy.observe(s); });

        /* ── .sr scroll-reveals ─────────────────── */
        var srEls = document.querySelectorAll('.sr');
        if (srEls.length) {
            var srObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in');
                        srObs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.18, rootMargin: '-30px 0px' });
            srEls.forEach(function (el) { srObs.observe(el); });
        }

        /* ── Eyebrow underline trigger ──────────── */
        var eyebrows = document.querySelectorAll('.eyebrow');
        if (eyebrows.length) {
            var eyeObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        eyeObs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.6 });
            eyebrows.forEach(function (e) { eyeObs.observe(e); });
        }

        /* ── Section divider trigger ────────────── */
        var dividers = document.querySelectorAll('.rule, .section-divider, .divider');
        if (dividers.length) {
            var divObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        divObs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.8 });
            dividers.forEach(function (d) { divObs.observe(d); });
        }

        /* ── Parallax on hero orbs ──────────────── */
        var orbs = document.querySelectorAll('.hero-orb');
        if (orbs.length) {
            window.addEventListener('scroll', function () {
                var y = window.scrollY;
                orbs.forEach(function (orb, i) {
                    var speed = (i % 2 === 0) ? 0.08 : -0.05;
                    orb.style.transform = 'translateY(' + (y * speed) + 'px)';
                });
            }, { passive: true });
        }

        /* ── Parallax on hero image ─────────────── */
        var heroImg = document.querySelector('.hero-img img');
        if (heroImg) {
            window.addEventListener('scroll', function () {
                var y = window.scrollY;
                if (y < window.innerHeight * 1.3) {
                    heroImg.style.transform = 'translateY(' + (y * 0.12) + 'px)';
                }
            }, { passive: true });
        }

        /* ── Stagger child cards on enter ───────── */
        var staggerGroups = document.querySelectorAll('[data-stagger]');
        if (staggerGroups.length) {
            var stObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    var children = entry.target.querySelectorAll('.svc-card, .project-card, .clt-box, .ftab');
                    children.forEach(function (child, idx) {
                        child.style.transitionDelay = (idx * 80) + 'ms';
                        child.style.opacity    = '0';
                        child.style.transform  = 'translateY(30px)';
                        setTimeout(function () {
                            child.style.transition = 'opacity 0.6s cubic-bezier(0.22,1,0.36,1), transform 0.6s cubic-bezier(0.22,1,0.36,1)';
                            child.style.opacity    = '1';
                            child.style.transform  = '';
                        }, 60 + idx * 80);
                    });
                    stObs.unobserve(entry.target);
                });
            }, { threshold: 0.12 });
            staggerGroups.forEach(function (g) { stObs.observe(g); });
        }

        /* ── Float card entrance ────────────────── */
        var floatCards = document.querySelectorAll('.float-card');
        floatCards.forEach(function (card, i) {
            card.style.opacity   = '0';
            card.style.transform = 'translateY(24px)';
            card.style.transition= 'opacity 0.8s ease, transform 0.8s ease';
            setTimeout(function () {
                card.style.opacity   = '1';
                card.style.transform = '';
            }, 1000 + i * 300);
        });

    }); /* end DOMContentLoaded */
    </script>
    @stack('scripts')

    <!-- Custom Body End Codes -->
    @php
        $bodyEndCodes = \App\Models\CustomCode::where('is_active', true)
            ->where('position', 'body_end')
            ->orderBy('priority', 'desc')
            ->get();
    @endphp
    @foreach($bodyEndCodes as $code)
        @if($code->type == 'css')
            <style>{!! $code->content !!}</style>
        @elseif($code->type == 'js')
            <script>{!! $code->content !!}</script>
        @else
            {!! $code->content !!}
        @endif
    @endforeach
</body>
</html>
