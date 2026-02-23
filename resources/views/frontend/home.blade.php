@extends('layouts.frontend')

@section('content')

{{-- ══════════════════════════════════════════════
         HERO
══════════════════════════════════════════════ --}}
<section id="home" class="hero">

    {{-- Background elements --}}
    <div class="hero-panel"></div>
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="hero-orb hero-orb-3"></div>

    <div class="container position-relative z-1">




        {{-- ── HERO COPY + IMAGE ─────────────────────────── --}}
        <div class="row align-items-center gy-5" style="margin-top:48px;">

            {{-- LEFT: Copy --}}
            <div class="col-lg-6">

                <p class="hero-tag" data-aos="fade-down" data-aos-duration="700">
                    <span class="hero-tag-dot"></span>
                    Agence d'Impression · Casablanca
                </p>

                <h1 class="hero-h1" data-aos="fade-up" data-aos-delay="80">
                    L'Impression,<br><em>Réinventée.</em>
                </h1>

                <p class="hero-sub" data-aos="fade-up" data-aos-delay="160">
                    Jawhara Pro Print est votre partenaire stratégique en communication visuelle. Grand format, petit format, offset ou DTF — nous créons des impressions qui marquent durablement.
                </p>

                <div class="hero-cta" data-aos="fade-up" data-aos-delay="240">
                    <a href="#projects" onclick="smoothTo('projects'); return false;" class="btn-gold">
                        <i class="fas fa-images"></i> Voir nos réalisations
                    </a>
                    <a href="#contact" onclick="smoothTo('contact'); return false;" class="btn-ghost">
                        <i class="fas fa-phone-alt"></i> Devis gratuit
                    </a>
                </div>

                <div class="hero-trust" data-aos="fade-up" data-aos-delay="320">
                    <div class="trust-item"><i class="fas fa-medal"></i><span>+10 ans d'expertise</span></div>
                    <div class="trust-item"><i class="fas fa-print"></i><span>Impression HD</span></div>
                    <div class="trust-item"><i class="fas fa-shipping-fast"></i><span>Livraison rapide</span></div>
                </div>
            </div>

            {{-- RIGHT: Animated Logo --}}
            <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center"
                 data-aos="fade-left" data-aos-delay="200" data-aos-duration="900">
                <div class="hero-logo-visual">

                    {{-- Outer glow ring --}}
                    <div class="hlv-ring hlv-ring-1"></div>
                    <div class="hlv-ring hlv-ring-2"></div>

                    {{-- Logo container --}}
                    <div class="hlv-logo">
                        @if(setting('app_logo'))
                            <img src="{{ asset('storage/' . setting('app_logo')) }}"
                                 alt="{{ setting('app_name', 'Jawhara Pro Print') }}"
                                 class="hlv-logo-img">
                        @else
                            <span class="hero-logo-text">{{ setting('app_name', 'Jawhara') }}</span>
                        @endif
                    </div>

                    {{-- Floating stat cards --}}
                    <div class="float-card" style="top:20px; left:-60px;">
                        <span class="fc-num">500+</span>
                        <span class="fc-lbl">Projets livrés</span>
                    </div>
                    <div class="float-card" style="bottom:20px; right:-60px;">
                        <span class="fc-num">200+</span>
                        <span class="fc-lbl">Clients satisfaits</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
         STATS BAND
══════════════════════════════════════════════ --}}
<div class="stats-band">
    <div class="container-fluid px-0">
        <div class="row g-0">

            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="50">
                <div class="stat-cell">
                    <div style="display:flex;align-items:baseline;justify-content:center;gap:2px;">
                        <span class="stat-val js-count" data-target="500">0</span>
                        <span class="stat-val" style="font-size:2rem;padding-bottom:2px;">+</span>
                    </div>
                    <div class="stat-lbl">Projets livrés</div>
                </div>
            </div>

            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="130">
                <div class="stat-cell">
                    <div style="display:flex;align-items:baseline;justify-content:center;gap:2px;">
                        <span class="stat-val js-count" data-target="200">0</span>
                        <span class="stat-val" style="font-size:2rem;padding-bottom:2px;">+</span>
                    </div>
                    <div class="stat-lbl">Clients satisfaits</div>
                </div>
            </div>

            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="210">
                <div class="stat-cell">
                    <div style="display:flex;align-items:baseline;justify-content:center;gap:2px;">
                        <span class="stat-val js-count" data-target="4">0</span>
                    </div>
                    <div class="stat-lbl">Techniques d'impression</div>
                </div>
            </div>

            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="290">
                <div class="stat-cell">
                    <div style="display:flex;align-items:baseline;justify-content:center;gap:2px;">
                        <span class="stat-val js-count" data-target="10">0</span>
                        <span class="stat-val" style="font-size:2rem;padding-bottom:2px;">+</span>
                    </div>
                    <div class="stat-lbl">Années d'expérience</div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
         ABOUT
══════════════════════════════════════════════ --}}
<section id="about" class="about-section section-py">
    <div class="container">
        <div class="row align-items-center gy-5 g-lg-5">

            {{-- Images --}}
            <div class="col-lg-5" data-aos="fade-right" data-aos-duration="900">
                <div class="img-stack">
                    <div class="years-badge"><span class="y-n">10</span><span class="y-l">Années</span></div>
                    <img class="img-main"
                         src="https://images.unsplash.com/photo-1543269664-7eef42226a21?auto=format&fit=crop&q=80&w=800"
                         alt="Notre savoir-faire">
                    <img class="img-accent"
                         src="https://images.unsplash.com/photo-1600132806370-bf17e65e942f?auto=format&fit=crop&q=80&w=600"
                         alt="Équipe Jawhara"
                         data-aos="zoom-in" data-aos-delay="250">
                </div>
            </div>

            {{-- Text --}}
            <div class="col-lg-7" data-aos="fade-left" data-aos-delay="120" data-aos-duration="900">

                <p class="eyebrow">Notre histoire</p>
                <h2 class="heading-xl">
                    Le Partenaire Créatif<br>
                    <span class="text-grad">de votre Image</span>
                </h2>
                <span class="rule"></span>

                <p class="body-text mb-4">
                    Depuis plus de 10 ans, Jawhara Pro Print accompagne les entreprises et particuliers casablancais dans leurs projets de communication visuelle. Notre expertise couvre l'ensemble du spectre de l'impression — du grand format impactant aux supports premium raffinés.
                </p>
                <p class="body-text mb-5">
                    Notre engagement : comprendre votre vision, proposer la solution technique optimale et livrer un résultat irréprochable — dans les délais, à chaque fois, sans exception.
                </p>

                <ul class="feat-list">
                    <li data-aos="fade-up" data-aos-delay="80">
                        <div class="feat-icon"><i class="fas fa-check"></i></div>
                        <div>
                            <strong>Équipement professionnel haut de gamme</strong>
                            <span>Technologies d'impression dernière génération pour des résultats sur grand et petit format.</span>
                        </div>
                    </li>
                    <li data-aos="fade-up" data-aos-delay="160">
                        <div class="feat-icon"><i class="fas fa-check"></i></div>
                        <div>
                            <strong>Accompagnement de A à Z</strong>
                            <span>De la conception graphique jusqu'à la pose finale, notre équipe vous guide à chaque étape.</span>
                        </div>
                    </li>
                    <li data-aos="fade-up" data-aos-delay="240">
                        <div class="feat-icon"><i class="fas fa-check"></i></div>
                        <div>
                            <strong>Qualité garantie, livraison assurée</strong>
                            <span>Matériaux certifiés, encres résistantes UV et contrôle qualité rigoureux sur chaque commande.</span>
                        </div>
                    </li>
                </ul>

                <a href="#contact" onclick="smoothTo('contact'); return false;" class="btn-dark">
                    <i class="fas fa-arrow-right"></i> Demander un devis
                </a>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
         SERVICES
══════════════════════════════════════════════ --}}
<section id="services" class="services-section section-py">
    <div class="container">

        <div class="text-center mb-5" data-aos="fade-up">
            <p class="eyebrow" style="justify-content:center;">Ce que nous faisons</p>
            <h2 class="heading-lg heading-dark">
                Nos <span class="text-grad">Expertises</span>
            </h2>
            <span class="rule center"></span>
            <p class="body-text" style="color:rgba(255,255,255,.4);max-width:500px;margin:0 auto;">
                Quatre spécialités maîtrisées, un seul objectif : décupler l'impact visuel de votre marque.
            </p>
        </div>

        <div class="row g-4" data-stagger>

            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="60">
                <div class="svc-card">
                    <span class="svc-num">01</span>
                    <div class="svc-icon" style="background:rgba(233,164,37,.08);">
                        <i class="fas fa-ruler-combined" style="color:var(--gold);"></i>
                    </div>
                    <h4>Grand Format</h4>
                    <p>Bâches PVC, vinyles, affiches XL, kakémonos et habillage complet de véhicules ou vitrines en haute résolution durable.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="140">
                <div class="svc-card">
                    <span class="svc-num">02</span>
                    <div class="svc-icon" style="background:rgba(13,185,215,.08);">
                        <i class="fas fa-layer-group" style="color:var(--cyan);"></i>
                    </div>
                    <h4>Petit Format</h4>
                    <p>Cartes de visite premium, flyers, brochures, dépliants et catalogues avec des finitions mat, brillant ou soft-touch.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="220">
                <div class="svc-card">
                    <span class="svc-num">03</span>
                    <div class="svc-icon" style="background:rgba(196,26,179,.08);">
                        <i class="fas fa-industry" style="color:var(--magenta);"></i>
                    </div>
                    <h4>Offset</h4>
                    <p>Impression offset pour les grandes séries : magazines, catalogues institutionnels, têtes de lettre, enveloppes et journaux.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="300">
                <div class="svc-card">
                    <span class="svc-num">04</span>
                    <div class="svc-icon" style="background:rgba(233,164,37,.08);">
                        <i class="fas fa-mitten" style="color:var(--gold);"></i>
                    </div>
                    <h4>DTF Textile</h4>
                    <p>Direct to Film pour personnaliser t-shirts, polos, blouses et vêtements de travail avec un rendu couleurs exceptionnel.</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
         PROJECTS
══════════════════════════════════════════════ --}}
<section id="projects" class="projects-section section-py">
    <div class="container">

        <div class="text-center mb-5" data-aos="fade-up">
            <p class="eyebrow" style="justify-content:center;">Portfolio</p>
            <h2 class="heading-lg">Nos <span class="text-grad">Réalisations</span></h2>
            <span class="rule center"></span>
            <p class="body-text" style="max-width:460px;margin:0 auto;">
                Chaque projet est la preuve de notre engagement pour l'excellence visuelle.
            </p>
        </div>

        {{-- Filters --}}
        <div class="filter-wrap" data-aos="fade-up" data-aos-delay="80">
            <button class="ftab active" data-filter="all">Tous</button>
            <button class="ftab" data-filter="grand-format">Grand Format</button>
            <button class="ftab" data-filter="petit-format">Petit Format</button>
            <button class="ftab" data-filter="offset">Offset</button>
            <button class="ftab" data-filter="dtf">DTF Textile</button>
        </div>

        {{-- Grid --}}
        <div class="row g-4" id="gallery" data-stagger>

            <div class="col-sm-6 col-lg-4 g-item" data-cat="grand-format" data-aos="fade-up" data-aos-delay="50">
                <a class="project-card" href="#">
                    <span class="project-marker">Grand Format</span>
                    <img src="https://images.unsplash.com/photo-1542384557-0824d90731ee?auto=format&fit=crop&q=80&w=800" alt="Habillage vitrine">
                    <div class="project-overlay">
                        <span class="project-cat">Grand Format</span>
                        <h5>Habillage Vitrine Boutique</h5>
                        <p>Vinyle adhésif HD · Casablanca</p>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-4 g-item" data-cat="petit-format" data-aos="fade-up" data-aos-delay="110">
                <a class="project-card" href="#">
                    <span class="project-marker">Petit Format</span>
                    <img src="https://images.unsplash.com/photo-1611532736597-de2d4265fba3?auto=format&fit=crop&q=80&w=800" alt="Cartes de visite">
                    <div class="project-overlay">
                        <span class="project-cat">Petit Format</span>
                        <h5>Cartes de Visite Premium</h5>
                        <p>Pelliculage mat · Vernis UV sélectif</p>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-4 g-item" data-cat="offset" data-aos="fade-up" data-aos-delay="170">
                <a class="project-card" href="#">
                    <span class="project-marker">Offset</span>
                    <img src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?auto=format&fit=crop&q=80&w=800" alt="Catalogue">
                    <div class="project-overlay">
                        <span class="project-cat">Offset</span>
                        <h5>Catalogue Produits 64 pages</h5>
                        <p>Couché mat 170g · Soft-touch cover</p>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-4 g-item" data-cat="dtf" data-aos="fade-up" data-aos-delay="50">
                <a class="project-card" href="#">
                    <span class="project-marker">DTF Textile</span>
                    <img src="https://images.unsplash.com/photo-1604176354204-9268737828e4?auto=format&fit=crop&q=80&w=800" alt="Textile personnalisé">
                    <div class="project-overlay">
                        <span class="project-cat">DTF Textile</span>
                        <h5>Uniformes Personnalisés</h5>
                        <p>50 polos · DTF couleurs vives</p>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-4 g-item" data-cat="grand-format" data-aos="fade-up" data-aos-delay="110">
                <a class="project-card" href="#">
                    <span class="project-marker">Grand Format</span>
                    <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=800" alt="Bâche salon">
                    <div class="project-overlay">
                        <span class="project-cat">Grand Format</span>
                        <h5>Bâche Salon Professionnel</h5>
                        <p>6 × 3 m · Impression HD · Anti-UV</p>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-4 g-item" data-cat="petit-format" data-aos="fade-up" data-aos-delay="170">
                <a class="project-card" href="#">
                    <span class="project-marker">Petit Format</span>
                    <img src="https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&q=80&w=800" alt="Brochure">
                    <div class="project-overlay">
                        <span class="project-cat">Petit Format</span>
                        <h5>Brochure Institutionnelle</h5>
                        <p>6 volets · Satiné 135g · 2 000 ex.</p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
         CLIENTS
══════════════════════════════════════════════ --}}
<section id="clients" class="clients-section section-py">
    <div class="container">

        <div class="text-center mb-5" data-aos="fade-up">
            <p class="eyebrow" style="justify-content:center;">Ils nous font confiance</p>
            <h2 class="heading-lg heading-dark">
                Nos <span class="text-grad">Partenaires</span>
            </h2>
            <span class="rule center"></span>
        </div>

        <div class="row g-3 justify-content-center">
            @foreach([
                ['icon'=>'fab fa-apple',     'delay'=>60],
                ['icon'=>'fab fa-google',    'delay'=>100],
                ['icon'=>'fab fa-microsoft', 'delay'=>140],
                ['icon'=>'fab fa-amazon',    'delay'=>60],
                ['icon'=>'fab fa-shopify',   'delay'=>100],
                ['icon'=>'fab fa-airbnb',    'delay'=>140],
                ['icon'=>'fab fa-spotify',   'delay'=>60],
                ['icon'=>'fab fa-slack',     'delay'=>100],
            ] as $cl)
            <div class="col-6 col-md-3 col-lg-3" data-aos="zoom-in" data-aos-delay="{{ $cl['delay'] }}">
                <div class="clt-box"><i class="{{ $cl['icon'] }}"></i></div>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════
         CONTACT
══════════════════════════════════════════════ --}}
<section id="contact" class="contact-section section-py">
    <div class="container">




        <div class="text-center mb-5" data-aos="fade-up">
            <p class="eyebrow" style="justify-content:center;">Contactez-nous</p>
            <h2 class="heading-lg">Démarrons votre <span class="text-grad">projet</span></h2>
            <span class="rule center"></span>
        </div>

        <div class="contact-wrap" data-aos="fade-up" data-aos-duration="800">
            <div class="row g-0">

                {{-- Left: details --}}
                <div class="col-lg-6 contact-l">
                    <p class="eyebrow mb-4">Prêt à commencer ?</p>
                    <h2>Parlez-nous de<br>votre vision</h2>
                    <p class="mt-3 mb-5" style="color:rgba(255,255,255,.42);font-size:.95rem;line-height:1.85;">
                        Notre équipe est disponible pour vous conseiller, vous accompagner et établir votre devis gratuitement — que ce soit pour une simple carte de visite ou un projet grand format complet.
                    </p>

                    <div class="ci-row">
                        <div class="ci-icon" style="background:rgba(233,164,37,.1);">
                            <i class="fas fa-map-marker-alt" style="color:var(--gold);"></i>
                        </div>
                        <div>
                            <div class="ci-lbl">Adresse</div>
                            <div class="ci-val">N 28 GH 1, Lotissement Essalam Imm 2<br>Casablanca, Maroc</div>
                        </div>
                    </div>

                    <div class="ci-row">
                        <div class="ci-icon" style="background:rgba(13,185,215,.1);">
                            <i class="fas fa-phone-alt" style="color:var(--cyan);"></i>
                        </div>
                        <div>
                            <div class="ci-lbl">Téléphone</div>
                            <div class="ci-val"><a href="tel:0700331450">0700-331450</a></div>
                        </div>
                    </div>

                    <div class="ci-row">
                        <div class="ci-icon" style="background:rgba(196,26,179,.1);">
                            <i class="fas fa-envelope" style="color:var(--magenta);"></i>
                        </div>
                        <div>
                            <div class="ci-lbl">Email</div>
                            <div class="ci-val"><a href="mailto:contact@jawharaproprint.com">contact@jawharaproprint.com</a></div>
                        </div>
                    </div>

                    <div class="ci-row">
                        <div class="ci-icon" style="background:rgba(233,164,37,.1);">
                            <i class="fas fa-clock" style="color:var(--gold);"></i>
                        </div>
                        <div>
                            <div class="ci-lbl">Horaires d'ouverture</div>
                            <div class="ci-val">Lundi – Samedi · 9h00 – 18h00</div>
                        </div>
                    </div>

                </div>

                {{-- Right: Actions --}}
                <div class="col-lg-6 contact-r">
                    <p class="eyebrow mb-4" style="color:var(--gold);">Réponse sous 24h garantie</p>

                    <div class="contact-action">

                        <a href="tel:0700331450" class="ca-btn">
                            <div class="ca-icon" style="background:rgba(233,164,37,.1);">
                                <i class="fas fa-phone-alt" style="color:var(--gold);"></i>
                            </div>
                            <div>
                                <span class="ca-title">Appeler maintenant</span>
                                <span class="ca-sub">0700-331450 · Lun–Sam 9h–18h</span>
                            </div>
                            <i class="fas fa-chevron-right ca-arrow"></i>
                        </a>

                        <a href="mailto:contact@jawharaproprint.com" class="ca-btn">
                            <div class="ca-icon" style="background:rgba(13,185,215,.1);">
                                <i class="fas fa-envelope" style="color:var(--cyan);"></i>
                            </div>
                            <div>
                                <span class="ca-title">Envoyer un email</span>
                                <span class="ca-sub">contact@jawharaproprint.com</span>
                            </div>
                            <i class="fas fa-chevron-right ca-arrow"></i>
                        </a>

                        <a href="https://wa.me/212700331450" target="_blank" rel="noopener" class="ca-btn">
                            <div class="ca-icon" style="background:rgba(37,211,102,.1);">
                                <i class="fab fa-whatsapp" style="color:#25D366;"></i>
                            </div>
                            <div>
                                <span class="ca-title">WhatsApp</span>
                                <span class="ca-sub">Réponse instantanée</span>
                            </div>
                            <i class="fas fa-chevron-right ca-arrow"></i>
                        </a>

                    </div>

                    <p class="mt-4" style="color:rgba(255,255,255,.22);font-size:.78rem;line-height:1.7;">
                        <i class="fas fa-shield-alt me-2" style="color:var(--gold);"></i>
                        Devis entièrement gratuit et sans engagement · Conseils personnalisés · Livraison Casablanca & alentours
                    </p>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {

    /* ── smooth scroll helper ─────────────────── */
    window.smoothTo = function (id) {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    document.addEventListener('DOMContentLoaded', function () {

        /* ── gallery filter ───────────────────── */
        const tabs  = document.querySelectorAll('.ftab');
        const items = document.querySelectorAll('.g-item');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.remove('active'); });
                this.classList.add('active');

                const f = this.dataset.filter;
                items.forEach(function (item) {
                    const show = f === 'all' || item.dataset.cat === f;
                    item.style.transition = 'opacity .4s, transform .4s';
                    if (show) {
                        item.style.display       = '';
                        item.style.opacity       = '1';
                        item.style.transform     = 'scale(1)';
                        item.style.pointerEvents = '';
                    } else {
                        item.style.opacity       = '0';
                        item.style.transform     = 'scale(.95)';
                        item.style.pointerEvents = 'none';
                        setTimeout(function () { item.style.display = 'none'; }, 400);
                    }
                });
            });
        });

        /* ── ScrollSpy ────────────────────────── */
        const links = document.querySelectorAll('.nav-link');
        const spy   = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    links.forEach(function (l) {
                        l.classList.remove('active');
                        if (l.getAttribute('href') === '#' + entry.target.id) {
                            l.classList.add('active');
                        }
                    });
                }
            });
        }, { threshold: 0.35, rootMargin: '-80px 0px -45% 0px' });

        document.querySelectorAll('section[id]').forEach(function (s) { spy.observe(s); });

        /* ── Header compact ───────────────────── */
        var header = document.querySelector('.site-header');
        window.addEventListener('scroll', function () {
            if (header) header.classList.toggle('scrolled', window.scrollY > 60);
        }, { passive: true });

        /* ── Back-to-top ──────────────────────── */
        var backBtn = document.querySelector('.back-top');
        if (backBtn) {
            window.addEventListener('scroll', function () {
                backBtn.classList.toggle('show', window.scrollY > 400);
            }, { passive: true });
            backBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        /* ── Number counters ──────────────────── */
        var counters = document.querySelectorAll('.js-count');
        var cob = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var el     = entry.target;
                var target = parseInt(el.dataset.target, 10);
                var step   = Math.max(1, Math.ceil(target / 60));
                var val    = 0;
                var timer  = setInterval(function () {
                    val += step;
                    if (val >= target) { val = target; clearInterval(timer); }
                    el.textContent = val;
                }, 20);
                cob.unobserve(el);
            });
        }, { threshold: 0.6 });

        counters.forEach(function (c) { cob.observe(c); });

    });
}());
</script>
@endpush
