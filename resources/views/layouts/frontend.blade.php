<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', 'Maison Argan Katy — Premium Moroccan Argan Oil')</title>
    <meta name="description" content="@yield('meta_description', 'Pure, organic argan oil crafted with tradition and excellence.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}?v={{ filemtime(public_path('css/frontend.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}?v={{ filemtime(public_path('css/brand.css')) }}">
    
    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.29/dist/lenis.min.js"></script>
    
    <style>
        /* Custom Loader Styles */
        .loader {
            position: fixed;
            inset: 0;
            background: #0A0A0A;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            overflow: hidden;
        }
        @keyframes drawPath {
            to { stroke-dashoffset: 0; }
        }
        @keyframes pulseLogo {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        @keyframes fadeInOutText {
            0%, 100% { opacity: 0.4; }
            50% { opacity: 1; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-black">

    {{-- Loading Screen --}}
    @if(!request()->routeIs('shop.index'))
    <div class="loader" id="loader">
        <div class="loader-content text-center">
            <img src="{{ asset('assets/img/brand/logo.png') }}" alt="Maison Argan Katy" class="loader-logo mb-4 mx-auto" style="width: 150px; height: auto; animation: pulseLogo 2s ease-in-out infinite;">
            <div class="loader-progress mt-4 mx-auto" style="width: 180px; height: 1px; background: rgba(212, 175, 55, 0.2); position: relative; overflow: hidden;">
                <div class="loader-progress-bar" style="position: absolute; top: 0; left: 0; height: 100%; width: 0%; background: #D4AF37; transition: width 0.3s ease;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Custom Cursor --}}
    <div class="cursor d-none d-md-block" id="cursor"></div>
    <div class="cursor-follower d-none d-md-block" id="cursorFollower"></div>

    {{-- Scroll Progress Bar --}}
    <div class="scroll-progress" id="scrollProgress"></div>

    {{-- Particles Canvas --}}
    <canvas id="particles-canvas"></canvas>

    <!-- Dangila Header -->
    <header class="fixed-top nav-dangila py-3" style="z-index:1000;">
        <div class="container-dangila d-flex align-items-center justify-content-between">
            {{-- Navigation --}}
            <nav class="d-none d-lg-flex align-items-center gap-5">
                <a href="{{ url('/') }}" class="nav-link-dangila active">{{ __('Home') }}</a>
                <a href="{{ route('shop.index') }}" class="nav-link-dangila">{{ __('Products') }}</a>
                <a href="#about" class="nav-link-dangila">{{ __('About Us') }}</a>
            </nav>

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center">
                <span class="dangila-heading text-black fs-3" style="letter-spacing: -0.05em; font-weight: 800;">dangila</span>
            </a>

            {{-- Actions --}}
            <div class="d-flex align-items-center gap-3 gap-lg-4">
                <button class="btn p-0 text-black border-0 bg-transparent position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart">
                    <i class="fa-solid fa-cart-shopping fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge-dangila" style="font-size: 0.5rem; padding: 0.25em 0.5em; border: 2px solid var(--color-bg);">0</span>
                </button>
                
                <a href="{{ route('shop.index') }}" class="btn-dangila d-none d-md-flex" style="padding: 10px 24px; font-size: 0.85rem;">{{ __('Shop Now') }}</a>

                {{-- Mobile Menu --}}
                <button class="btn d-lg-none p-0 text-black border-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                    <i class="fa-solid fa-bars-staggered fs-4"></i>
                </button>
            </div>
        </div>
        
        {{-- Mobile Nav --}}
        <div class="collapse d-lg-none bg-surface border-top border-black border-opacity-5 mt-3" id="mobileNav">
            <div class="container py-5 d-flex flex-column align-items-center gap-4">
                <a href="{{ url('/') }}" class="nav-link-dangila fs-5">{{ __('Home') }}</a>
                <a href="{{ route('shop.index') }}" class="nav-link-dangila fs-5">{{ __('Products') }}</a>
                <a href="#about" class="nav-link-dangila fs-5">{{ __('About Us') }}</a>
                <a href="{{ route('shop.index') }}" class="btn-dangila mt-3 w-100">{{ __('Shop Now') }}</a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Dangila Footer -->
    <footer class="bg-surface py-5 border-top border-black border-opacity-5">
        <div class="container-dangila py-5">
            <div class="row gy-5">
                <div class="col-lg-4 text-center text-lg-start">
                    <span class="dangila-heading text-black fs-2 d-block mb-3" style="letter-spacing: -0.05em; font-weight: 800;">dangila</span>
                    <p class="text-espresso opacity-60 small mb-4" style="max-width: 300px;">
                        {{ __('Crafting natural beauty solutions inspired by Moroccan heritage.') }}
                    </p>
                    <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                        <a href="#" class="text-primary fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-primary fs-5"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-primary fs-5"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2 offset-lg-2">
                    <h6 class="dangila-heading fs-6 mb-4">{{ __('Menu') }}</h6>
                    <div class="d-flex flex-column gap-3">
                        <a href="#" class="nav-link-dangila">{{ __('Home') }}</a>
                        <a href="#" class="nav-link-dangila">{{ __('Products') }}</a>
                        <a href="#" class="nav-link-dangila">{{ __('About Us') }}</a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="dangila-heading fs-6 mb-4">{{ __('Support') }}</h6>
                    <div class="d-flex flex-column gap-3">
                        <a href="#" class="nav-link-dangila">{{ __('Contact') }}</a>
                        <a href="#" class="nav-link-dangila">{{ __('Shipping') }}</a>
                        <a href="#" class="nav-link-dangila">{{ __('FAQ') }}</a>
                    </div>
                </div>
                <div class="col-lg-2 text-lg-end">
                    <h6 class="dangila-heading fs-6 mb-4">{{ __('Location') }}</h6>
                    <p class="nav-link-dangila small">
                        Agadir, Morocco <br>
                        Marrakech, Morocco
                    </p>
                </div>
            </div>
            <div class="pt-5 mt-5 border-top border-black border-opacity-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <span class="text-dim x-small">© 2026 dangila. All rights reserved.</span>
                <div class="d-flex gap-4">
                    <a href="#" class="text-dim x-small text-decoration-none">{{ __('Privacy Policy') }}</a>
                    <a href="#" class="text-dim x-small text-decoration-none">{{ __('Terms of Use') }}</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Mini Cart --}}
    <div class="offcanvas offcanvas-end bg-black text-white" id="miniCart" style="border-left: 1px solid rgba(212, 175, 55, 0.2);">
        <div class="offcanvas-header border-bottom border-gold border-opacity-10">
            <h5 class="offcanvas-title font-serif text-gold">{{ __('Collection') }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0" id="mini-cart-items">
             @include('frontend.cart.partials.mini-cart-items')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Smooth Scroll (Lenis) - Hardened initialization
        if (typeof Lenis !== 'undefined') {
            const lenis = new Lenis({ duration: 1.2, easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), smooth: true });
            function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
            requestAnimationFrame(raf);
        }

        // GSAP & ScrollTrigger
        gsap.registerPlugin(ScrollTrigger);

        // Loader Logic
        @if(!request()->routeIs('shop.index'))
        const loader = document.getElementById('loader');
        const progressBar = document.querySelector('.loader-progress-bar');
        
        if (loader) {
            let loadProgress = 0;
            let progressInterval = setInterval(() => {
                loadProgress += Math.random() * 15;
                if (loadProgress > 90) loadProgress = 90; 
                if (progressBar) progressBar.style.width = loadProgress + '%';
            }, 200);

            function hideLoader() {
                if (progressInterval) clearInterval(progressInterval);
                if (progressBar) progressBar.style.width = '100%';
                
                const tl = gsap.timeline();
                tl.to('.loader-content', { opacity: 0, y: -20, duration: 0.6, delay: 0.4, ease: 'power2.inOut' })
                  .to(loader, { yPercent: -100, duration: 0.8, ease: 'expo.inOut' })
                  .set(loader, { display: 'none' });
            }

            window.addEventListener('load', hideLoader);
            
            // Safety timeout - 4s
            setTimeout(() => {
                if (loader.style.display !== 'none') {
                    hideLoader();
                }
            }, 4000);
        }
        @endif

        // Custom Cursor
        const cursor = document.getElementById('cursor');
        const cursorFollower = document.getElementById('cursorFollower');
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;
        let followerX = 0, followerY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX; mouseY = e.clientY;
        });

        function animateCursor() {
            cursorX += (mouseX - cursorX) * 0.2; cursorY += (mouseY - cursorY) * 0.2;
            cursor.style.left = cursorX + 'px'; cursor.style.top = cursorY + 'px';
            followerX += (mouseX - followerX) * 0.1; followerY += (mouseY - followerY) * 0.1;
            cursorFollower.style.left = followerX + 'px'; cursorFollower.style.top = followerY + 'px';
            requestAnimationFrame(animateCursor);
        }
        animateCursor();

        // Particles
        const canvas = document.getElementById('particles-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth; canvas.height = window.innerHeight;
        const particles = [];
        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width; this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 0.5; this.speedX = Math.random() * 1 - 0.5; this.speedY = Math.random() * 1 - 0.5;
                this.opacity = Math.random() * 0.5 + 0.1;
            }
            update() {
                this.x += this.speedX; this.y += this.speedY;
                if (this.x > canvas.width) this.x = 0; if (this.x < 0) this.x = canvas.width;
                if (this.y > canvas.height) this.y = 0; if (this.y < 0) this.y = canvas.height;
            }
            draw() {
                ctx.fillStyle = `rgba(212, 175, 55, ${this.opacity})`; ctx.beginPath(); ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2); ctx.fill();
            }
        }
        for (let i = 0; i < 50; i++) particles.push(new Particle());
        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => { p.update(); p.draw(); });
            requestAnimationFrame(animateParticles);
        }
        animateParticles();

        // Scroll Logic
        window.addEventListener('scroll', () => {
            const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            document.getElementById('scrollProgress').style.width = scrolled + '%';
            
            const nav = document.getElementById('nav');
            if (window.scrollY > 100) {
                nav.classList.add('bg-black', 'py-3');
                nav.style.mixBlendMode = 'normal';
            } else {
                nav.classList.remove('bg-black', 'py-3');
                nav.style.mixBlendMode = 'difference';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
