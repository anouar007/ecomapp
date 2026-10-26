<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>{{ setting('app_name', 'Full Frame House') }} &mdash; {{ __('Ouverture Prochaine | Matériel Cinéma & Photo') }}</title>
    <meta name="description" content="La référence du matériel vidéo, cinéma et photographie au Maroc prépare son lancement officiel. Bientôt disponible à Casablanca et en livraison express dans tout le Royaume.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ setting('site_favicon') ? asset('storage/' . setting('site_favicon')) : asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Pro Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --cs-bg-dark: #08090d;
            --cs-bg-card: rgba(18, 20, 29, 0.75);
            --cs-border: rgba(255, 255, 255, 0.08);
            --cs-border-glow: rgba(239, 68, 68, 0.35);
            --cs-red: #ef4444;
            --cs-red-hover: #dc2626;
            --cs-text-light: #f8fafc;
            --cs-text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--cs-bg-dark);
            color: var(--cs-text-light);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Ambient Cinema Background Lights */
        .ambient-glow-1 {
            position: fixed;
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 500px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.18) 0%, rgba(220, 38, 38, 0.04) 50%, transparent 75%);
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }

        .ambient-glow-2 {
            position: fixed;
            bottom: -200px;
            right: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.10) 0%, transparent 70%);
            filter: blur(90px);
            z-index: 0;
            pointer-events: none;
        }

        .cinema-grid-overlay {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
            pointer-events: none;
        }

        .page-content-wrapper {
            position: relative;
            z-index: 1;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        /* Brand Navbar */
        .cs-navbar {
            padding: 24px 0;
        }

        .cs-brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--cs-border);
            border-radius: 9999px;
            font-size: 0.82rem;
            color: var(--cs-text-muted);
            backdrop-filter: blur(12px);
        }

        .pulse-led {
            width: 8px;
            height: 8px;
            background-color: var(--cs-red);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 12px var(--cs-red);
            animation: pulseLed 2s infinite ease-in-out;
        }

        @keyframes pulseLed {
            0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 10px var(--cs-red); }
            50% { opacity: 0.4; transform: scale(0.85); box-shadow: 0 0 4px var(--cs-red); }
        }

        /* Hero Typography */
        .hero-title {
            font-size: clamp(2.3rem, 5.5vw, 4.2rem);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -0.02em;
            margin-bottom: 20px;
        }

        .text-gradient-red {
            background: linear-gradient(135deg, #ffffff 30%, #fca5a5 70%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: var(--cs-text-muted);
            max-width: 680px;
            margin: 0 auto 36px;
            line-height: 1.6;
        }

        /* Countdown Cards */
        .countdown-grid {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .countdown-item {
            background: var(--cs-bg-card);
            border: 1px solid var(--cs-border);
            border-radius: 18px;
            padding: 16px 20px;
            min-width: 95px;
            text-align: center;
            backdrop-filter: blur(16px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            transition: transform 0.25s ease, border-color 0.25s ease;
        }

        .countdown-item:hover {
            transform: translateY(-3px);
            border-color: var(--cs-border-glow);
        }

        .countdown-num {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 800;
            line-height: 1;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .countdown-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--cs-text-muted);
            font-weight: 600;
        }

        /* Notification Card Form */
        .vip-notify-card {
            background: linear-gradient(180deg, rgba(24, 27, 38, 0.9) 0%, rgba(14, 16, 24, 0.95) 100%);
            border: 1px solid var(--cs-border);
            border-radius: 24px;
            padding: 32px;
            max-width: 600px;
            margin: 0 auto 48px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .cs-input-group {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50px;
            padding: 6px 8px 6px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .cs-input-group:focus-within {
            border-color: var(--cs-red);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.18);
        }

        .cs-input-field {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 0.95rem;
            width: 100%;
            outline: none;
        }

        .cs-input-field::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }

        .btn-cs-notify {
            background: linear-gradient(135deg, var(--cs-red) 0%, #b91c1c 100%);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            padding: 12px 26px;
            font-weight: 600;
            font-size: 0.92rem;
            white-space: nowrap;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-cs-notify:hover {
            background: linear-gradient(135deg, #f87171 0%, var(--cs-red) 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.55);
            color: #ffffff;
        }

        /* Pillars / Features Grid */
        .pillar-card {
            background: rgba(18, 20, 29, 0.5);
            border: 1px solid var(--cs-border);
            border-radius: 20px;
            padding: 24px;
            height: 100%;
            text-align: left;
            transition: all 0.3s ease;
            backdrop-filter: blur(12px);
        }

        .pillar-card:hover {
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(24, 27, 39, 0.7);
            transform: translateY(-4px);
        }

        .pillar-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: var(--cs-red);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 16px;
        }

        .pillar-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #ffffff;
        }

        .pillar-desc {
            font-size: 0.85rem;
            color: var(--cs-text-muted);
            line-height: 1.5;
            margin-bottom: 0;
        }

        /* WhatsApp Urgent Button */
        .btn-cs-whatsapp {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #ffffff !important;
            border-radius: 50px;
            padding: 12px 28px;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.35);
            transition: all 0.3s ease;
        }

        .btn-cs-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(34, 197, 94, 0.5);
            color: #ffffff;
        }

        /* Footer */
        .cs-footer {
            padding: 32px 0;
            border-top: 1px solid var(--cs-border);
            font-size: 0.85rem;
            color: var(--cs-text-muted);
        }

        .cs-admin-link {
            color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            font-size: 0.78rem;
            transition: color 0.2s ease;
        }

        .cs-admin-link:hover {
            color: var(--cs-red);
        }
    </style>
</head>
<body>
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>
    <div class="cinema-grid-overlay"></div>

    <div class="page-content-wrapper">
        <!-- HEADER -->
        <header class="cs-navbar">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        @if(app_logo_url())
                            <img src="{{ app_logo_url() }}" alt="{{ $appName }}" height="42" class="img-fluid">
                        @else
                            <div class="d-flex align-items-center gap-2 text-white text-decoration-none">
                                <span class="d-inline-flex align-items-center justify-content-center bg-danger rounded-3 text-white" style="width: 38px; height: 38px;">
                                    <i class="fas fa-camera fs-5"></i>
                                </span>
                                <span class="fs-4 fw-bold font-heading tracking-tight">{{ $appName }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="cs-brand-badge">
                        <span class="pulse-led"></span>
                        <span class="fw-semibold text-white">Lancement Prochain</span>
                        <span class="opacity-50">&bull;</span>
                        <span class="d-none d-sm-inline">Casablanca, Maroc</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- HERO SECTION -->
        <main class="py-5 text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        
                        <div class="mb-3">
                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 fw-semibold" style="font-size: 0.84rem;">
                                <i class="fas fa-video me-1.5"></i>Cinéma &bull; Hybrides &bull; Optiques &bull; Audio Pro
                            </span>
                        </div>

                        <h1 class="hero-title text-gradient-red">
                            Le temple du matériel Cinéma & Photo au Maroc se prépare.
                        </h1>

                        <p class="hero-subtitle">
                            Notre nouvelle boutique en ligne dédiée aux réalisateurs, vidéastes et créateurs de contenu est actuellement en phase finale de configuration et d'enrichissement de stock.
                        </p>

                        <!-- COUNTDOWN -->
                        <div class="countdown-grid" id="comingSoonCountdown" data-target="{{ $launchDate }}">
                            <div class="countdown-item">
                                <div class="countdown-num" id="cd-days">07</div>
                                <div class="countdown-label">Jours</div>
                            </div>
                            <div class="countdown-item">
                                <div class="countdown-num" id="cd-hours">14</div>
                                <div class="countdown-label">Heures</div>
                            </div>
                            <div class="countdown-item">
                                <div class="countdown-num" id="cd-minutes">32</div>
                                <div class="countdown-label">Minutes</div>
                            </div>
                            <div class="countdown-item">
                                <div class="countdown-num" id="cd-seconds">48</div>
                                <div class="countdown-label">Secondes</div>
                            </div>
                        </div>

                        <!-- VIP NOTIFICATION CARD -->
                        <div class="vip-notify-card text-center">
                            <h3 class="fs-5 fw-bold text-white mb-2 font-heading">
                                <i class="fas fa-bell text-danger me-2"></i>Soyez le premier informé de l'ouverture
                            </h3>
                            <p class="small text-muted mb-4">
                                Laissez votre adresse e-mail ou WhatsApp pour recevoir un coupon VIP exclusif de bienvenue lors du lancement officiel.
                            </p>

                            <form id="csNotifyForm" action="{{ route('newsletter.subscribe') }}" method="POST">
                                @csrf
                                <div class="cs-input-group mb-2">
                                    <i class="fas fa-envelope text-muted me-2 ms-1"></i>
                                    <input type="email" name="email" id="csEmailInput" class="cs-input-field" placeholder="Votre adresse e-mail professionnelle..." required autocomplete="email">
                                    <button type="submit" id="csSubmitBtn" class="btn-cs-notify">
                                        <span>M'alerter</span> <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                                <div id="csFeedback" class="small mt-2" style="display: none;"></div>
                            </form>
                        </div>

                        <!-- URGENT ORDER / WHATSAPP CALLOUT -->
                        <div class="mb-5 pb-3">
                            <div class="p-3 d-inline-block rounded-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--cs-border);">
                                <div class="d-flex flex-column flex-sm-row align-items-center gap-3">
                                    <div class="text-sm-start">
                                        <div class="text-white fw-bold mb-0.5">Tournage ou projet urgent en cours ?</div>
                                        <div class="small text-muted">Notre service commercial traite directement vos devis et commandes prioritaires.</div>
                                    </div>
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $companyPhone);
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '212' . substr($cleanPhone, 1);
                                        }
                                        $waText = urlencode("Bonjour {$appName}, je souhaite passer une commande urgente pour un tournage.");
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" rel="noopener" class="btn-cs-whatsapp flex-shrink-0">
                                        <i class="fab fa-whatsapp fs-5"></i>
                                        <span>Commander sur WhatsApp</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 4 PILLARS GRID -->
                        <div class="row g-3 text-start">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="pillar-card">
                                    <div class="pillar-icon-box">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <div class="pillar-title">Caméras & Cinéma</div>
                                    <p class="pillar-desc">Sony FX3, FX30, Canon Cinema, Blackmagic, rigs complets et monitoring broadcast.</p>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="pillar-card">
                                    <div class="pillar-icon-box">
                                        <i class="fas fa-circle-notch"></i>
                                    </div>
                                    <div class="pillar-title">Optiques & Filtres</div>
                                    <p class="pillar-desc">Objectifs anamorphiques, focales fixes ciné, filtres VND, Matte Box légères et accessoires.</p>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="pillar-card">
                                    <div class="pillar-icon-box">
                                        <i class="fas fa-broadcast-tower"></i>
                                    </div>
                                    <div class="pillar-title">Son & Transmission</div>
                                    <p class="pillar-desc">Micros HF, enregistreurs 32-bit float, transmetteurs HDMI/SDI sans fil longue portée.</p>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="pillar-card">
                                    <div class="pillar-icon-box">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div class="pillar-title">Facture ICE & Garantie</div>
                                    <p class="pillar-desc">Facturation officielle avec ICE pour entreprises, matériel certifié neuf et support local.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="cs-footer">
            <div class="container">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-center text-md-start">
                    <div>
                        <div class="text-white fw-semibold mb-1">&copy; {{ date('Y') }} {{ $appName }} &bull; Tous droits réservés.</div>
                        <div class="text-muted small">Showroom Casablanca &bull; Livraison express partout au Maroc.</div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <a href="mailto:{{ $companyEmail }}" class="text-muted text-decoration-none small hover-white">
                            <i class="fas fa-envelope me-1"></i>{{ $companyEmail }}
                        </a>
                        <span class="opacity-25">&bull;</span>
                        <a href="{{ route('login') }}" class="cs-admin-link">
                            <i class="fas fa-lock me-1"></i>Accès Administrateur
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Countdown & Form Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Live Countdown
            const countdownEl = document.getElementById('comingSoonCountdown');
            if (countdownEl) {
                const targetAttr = countdownEl.getAttribute('data-target');
                let targetDate = new Date(targetAttr);
                
                // Fallback to 7 days if invalid date
                if (isNaN(targetDate.getTime()) || targetDate <= new Date()) {
                    targetDate = new Date();
                    targetDate.setDate(targetDate.getDate() + 7);
                }

                function updateCountdown() {
                    const now = new Date().getTime();
                    const diff = targetDate.getTime() - now;

                    if (diff <= 0) {
                        document.getElementById('cd-days').textContent = '00';
                        document.getElementById('cd-hours').textContent = '00';
                        document.getElementById('cd-minutes').textContent = '00';
                        document.getElementById('cd-seconds').textContent = '00';
                        return;
                    }

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    document.getElementById('cd-days').textContent = String(days).padStart(2, '0');
                    document.getElementById('cd-hours').textContent = String(hours).padStart(2, '0');
                    document.getElementById('cd-minutes').textContent = String(minutes).padStart(2, '0');
                    document.getElementById('cd-seconds').textContent = String(seconds).padStart(2, '0');
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            }

            // 2. VIP Notification AJAX Form
            const form = document.getElementById('csNotifyForm');
            const feedback = document.getElementById('csFeedback');
            const submitBtn = document.getElementById('csSubmitBtn');

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const email = document.getElementById('csEmailInput').value.trim();
                    if (!email) return;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: email })
                    })
                    .then(response => response.json().catch(() => ({})))
                    .then(data => {
                        feedback.style.display = 'block';
                        feedback.className = 'small mt-2 text-success fw-semibold';
                        feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i> Merci ! Vous serez le premier prévenu avec votre coupon de bienvenue.';
                        form.reset();
                    })
                    .catch(() => {
                        feedback.style.display = 'block';
                        feedback.className = 'small mt-2 text-success fw-semibold';
                        feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i> Merci ! Votre inscription VIP a été enregistrée.';
                        form.reset();
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span>M\'alerter</span> <i class="fas fa-arrow-right ms-1"></i>';
                    });
                });
            }
        });
    </script>
</body>
</html>
