@extends('layouts.frontend')

@section('title', 'Aït Oumdis Coopérative | Soins Naturels & Solaires Bio du Haut Atlas')
@section('meta_description', 'Découvrez les soins bio Aït Oumdis : Shampooing Naturel, Huile Capillaire Anti-Chute, Spray Tonifiant et Écran Solaire Éclat SPF +50. Rituels 100% naturels du Haut Atlas.')

@section('content')

  <!-- ==========================================================================
       1. HERO SECTION (SUNPURE CRÈME SOLAIRE INVISIBLE FPS 50+)
       ========================================================================== -->
  <section class="sp-hero" id="hero" style="background-image: linear-gradient(90deg, rgba(8, 26, 19, 0.90) 0%, rgba(8, 26, 19, 0.68) 38%, rgba(8, 26, 19, 0.20) 62%, transparent 85%), url('{{ asset('assets/images/home-hero.jpg') }}'); background-size: cover; background-position: center right; background-repeat: no-repeat;">
    <div class="sp-hero-container">
      <div class="sp-hero-grid">
        
        <!-- Left Content -->
        <div class="sp-hero-content">
          <span class="sp-hero-kicker">PROTECTION NATURELLE, PEAU RAYONNANTE</span>
          <h1 class="sp-hero-title">
            <span class="sp-sun">Sun</span><span class="sp-pure">Pure</span>
          </h1>
          <h2 class="sp-hero-subtitle">
            Écran Solaire Éclat SPF +50 (50ml)
          </h2>
          <p class="sp-hero-desc">
            La puissance de la nature pour une protection minérale optimale.<br class="sp-desktop-br">Disponible à l'unité ou dans notre rituel complet.
          </p>
          <a href="#produits" class="sp-btn-gold" id="heroCtaBtn">
            <span>Découvrir le produit</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

        <!-- Right Side Handwritten Script Annotation -->
        <div class="sp-hero-right-side">
          <div class="sp-hero-handwritten">
            <span>La nature</span>
            <span>prend soin</span>
            <span>de votre peau</span>
            <span class="sp-script-line"></span>
          </div>
        </div>

      </div>

      <!-- Bottom Feature Badges Bar (4 Pills avec séparateurs) -->
      <div class="sp-hero-features-bar">
        <!-- 100% Naturel -->
        <div class="sp-feature-item">
          <div class="sp-feature-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
          </div>
          <div class="sp-feature-text">
            <span class="sp-feature-title">100% Naturel</span>
            <span class="sp-feature-sub">Sans ingrédients nocifs</span>
          </div>
        </div>

        <!-- FPS 50+ -->
        <div class="sp-feature-item">
          <div class="sp-feature-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
          </div>
          <div class="sp-feature-text">
            <span class="sp-feature-title">FPS 50+</span>
            <span class="sp-feature-sub">Haute protection UVA/UVB</span>
          </div>
        </div>

        <!-- Convient à tous les types -->
        <div class="sp-feature-item">
          <div class="sp-feature-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          </div>
          <div class="sp-feature-text">
            <span class="sp-feature-title">Convient à tous les types</span>
            <span class="sp-feature-sub">de peau</span>
          </div>
        </div>

        <!-- Fabriqué au Maroc -->
        <div class="sp-feature-item">
          <div class="sp-feature-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3c-1.2 2.5-3 4-5 4.5 2.5 1 4 3 4.5 5 .5-2 2-4 4.5-5-2-.5-3.8-2-4-4.5Z"/><path d="M5 14c2 .5 3.5 2 4 4-2-.5-3.5-2-4-4Z"/><path d="M19 14c-2 .5-3.5 2-4 4 2-.5 3.5-2 4-4Z"/></svg>
          </div>
          <div class="sp-feature-text">
            <span class="sp-feature-title">Fabriqué au Maroc</span>
            <span class="sp-feature-sub">Savoir-faire local</span>
          </div>
        </div>

      </div>
    </div>

    <!-- Transition Vague Convexe Vers Section Crème -->
    <div class="sp-hero-wave-bottom">
      <svg viewBox="0 0 1440 46" preserveAspectRatio="none" fill="var(--sp-cream-bg)">
        <path d="M0,24 C360,46 880,46 1440,12 L1440,46 L0,46 Z"></path>
      </svg>
    </div>
  </section>

  <!-- ==========================================================================
       2. SECTION NOS PRODUITS (UNE GAMME NATURELLE ET EFFICACE)
       ========================================================================== -->
  <section class="sp-products-section" id="produits" style="background-color: var(--sp-cream-bg); background-image: url('{{ asset('assets/images/botanical_pattern_bg.jpg') }}'); background-size: 900px auto; background-repeat: repeat; background-position: center top;">
    <!-- Branche Botanique Décorative Gauche (Fidèle à la bordure gauche du mockup) -->
    <svg class="sp-botanical-branch-left" viewBox="0 0 110 380" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path d="M10 370 C15 320, 22 270, 30 220 C36 170, 42 120, 48 70 C52 40, 56 15, 60 5" stroke="#b89345" stroke-width="1.4" stroke-dasharray="4 3" opacity="0.75"/>
      <path d="M60 5 C66 0, 76 2, 82 12 C85 22, 75 27, 65 25 C61 24, 60 14, 60 5 Z" fill="rgba(226, 173, 80, 0.12)" stroke="#b89345" stroke-width="1.3"/>
      <path d="M54 30 C46 25, 32 28, 26 38 C22 46, 32 52, 42 48 C49 46, 52 37, 54 30 Z" fill="rgba(226, 173, 80, 0.1)" stroke="#b89345" stroke-width="1.2"/>
      <path d="M48 70 C58 60, 78 62, 90 75 C94 86, 82 95, 68 91 C56 88, 51 78, 48 70 Z" fill="rgba(226, 173, 80, 0.12)" stroke="#b89345" stroke-width="1.3"/>
      <path d="M42 90 C32 84, 18 90, 10 102 C6 112, 17 120, 30 114 C39 110, 42 99, 42 90 Z" fill="rgba(226, 173, 80, 0.1)" stroke="#b89345" stroke-width="1.2"/>
      <path d="M38 150 C50 138, 74 140, 88 156 C92 168, 78 178, 62 174 C48 170, 41 160, 38 150 Z" fill="rgba(226, 173, 80, 0.12)" stroke="#b89345" stroke-width="1.3"/>
      <path d="M34 170 C22 164, 8 172, 2 186 C-3 198, 10 206, 24 200 C32 194, 34 182, 34 170 Z" fill="rgba(226, 173, 80, 0.1)" stroke="#b89345" stroke-width="1.2"/>
      <path d="M30 220 C45 208, 68 210, 82 228 C86 240, 72 250, 56 246 C42 242, 34 230, 30 220 Z" fill="rgba(226, 173, 80, 0.12)" stroke="#b89345" stroke-width="1.3"/>
      <path d="M26 250 C14 244, 2 252, 0 266 C-2 278, 12 284, 24 278 C30 272, 28 258, 26 250 Z" fill="rgba(226, 173, 80, 0.1)" stroke="#b89345" stroke-width="1.2"/>
      <path d="M22 300 C38 288, 62 292, 76 310 C80 322, 66 332, 50 326 C36 320, 26 310, 22 300 Z" fill="rgba(226, 173, 80, 0.12)" stroke="#b89345" stroke-width="1.3"/>
    </svg>

    <div class="sp-products-container">
      <div class="sp-products-layout">
        
        <!-- Left Intro Block -->
        <div class="sp-products-intro">
          <span class="sp-section-kicker">NOS SOINS SIGNATURES</span>
          <h2 class="sp-products-title">
            Soins Botaniques du Haut Atlas<br>100% Biologiques
          </h2>
          <!-- Golden Wave Squiggle -->
          <div class="sp-wave-squiggle">
            <svg width="38" height="6" viewBox="0 0 38 6" fill="none">
              <path d="M1 3 C6 0.5, 12 5.5, 18 3 C24 0.5, 30 5.5, 37 3" stroke="var(--sp-gold-dark)" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
          <p class="sp-products-desc">
            Nos soins d'exception 100% naturels du Haut Atlas sont formulés artisanalement à base d'extraits botaniques purs cueillis à la main à 1 800 m d'altitude pour sublimer vos cheveux et protéger votre peau au quotidien.
          </p>
          <div class="sp-gold-dash"></div>
          <a href="{{ route('shop.index') }}" class="sp-btn-dark-green">
            <span>Explorer la boutique</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

        <!-- Products Cards Grid -->
        <div class="sp-cards-grid">
          @php
            $displayProducts = \App\Models\Product::where('status', 'active')->orderBy('id', 'asc')->get();
          @endphp
          @forelse($displayProducts as $p)
            <div class="sp-product-card" data-product-id="{{ $p->id }}" onclick="if (!event.target.closest('.sp-card-add-btn')) window.location.href='{{ route('shop.show', $p->id) }}';" style="cursor: pointer;">
              @if($p->badge)
                <span class="sp-card-badge">{{ $p->badge }}</span>
              @elseif($loop->first)
                <span class="sp-card-badge">100% Bio</span>
              @endif

              <div class="sp-card-media" style="cursor: pointer;" title="Voir le détail de {{ $p->name }}">
                <a href="{{ route('shop.show', $p->id) }}" style="display: block; width: 100%; height: 100%;" aria-label="Découvrir {{ $p->name }}">
                  <img src="{{ $p->main_image ? Storage::url($p->main_image) : asset('assets/images/pack-rituel.jpg') }}" alt="{{ $p->name }}" class="sp-card-img" loading="lazy">
                </a>
              </div>

              <div class="sp-card-body">
                <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 6px; margin-bottom: 4px;">
                  <h3 class="sp-card-title" style="margin: 0;">
                    <a href="{{ route('shop.show', $p->id) }}">{{ $p->name }}</a>
                  </h3>
                  @if($p->volume)
                    <span style="font-size: 0.74rem; font-weight: 700; color: #c28d32; background: rgba(226,173,80,0.12); padding: 2px 7px; border-radius: 9999px; white-space: nowrap; flex-shrink: 0;">{{ $p->volume }}</span>
                  @endif
                </div>
                <p class="sp-card-subtitle">
                  {{ Str::limit(strip_tags($p->description), 90) }}
                </p>
              </div>

              <div class="sp-card-footer">
                <span class="sp-card-price" >
                  {{ currency($p->price) }}
                </span>
                <button class="sp-card-add-btn" onclick="event.stopPropagation(); addToCart({{ $p->id }}, 1, this)" aria-label="Ajouter {{ $p->name }} au panier" title="Ajouter au panier">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </button>
              </div>
            </div>
          @empty
            <p>Chargement des trésors de la coopérative...</p>
          @endforelse
        </div>

      </div>

      <!-- Pack Spotlight Banner (Vendu en Pack) -->
      @if(isset($bundlePack) && $bundlePack)
        <div class="sp-pack-spotlight" style="margin-top: 48px; background: linear-gradient(135deg, #0c261e 0%, #153a2f 100%); border-radius: 24px; padding: 36px 40px; color: #ffffff; border: 1.5px solid rgba(226, 173, 80, 0.4); box-shadow: 0 16px 40px rgba(12, 38, 30, 0.18); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 30px; position: relative; overflow: hidden;">
          <div style="flex: 1; min-width: 290px; z-index: 2;">
            <span style="display: inline-block; background: rgba(226, 173, 80, 0.2); color: #e2ad50; border: 1px solid rgba(226, 173, 80, 0.4); font-size: 0.76rem; font-weight: 700; padding: 4px 14px; border-radius: 9999px; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 12px;">
              ✨ RITUEL COMPLET • VENDU EN PACK
            </span>
            <h3 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.6rem, 2.5vw, 2.2rem); color: #ffffff; margin: 0 0 10px 0; font-weight: 600;">
              {{ $bundlePack->name }}
            </h3>
            <p style="font-size: 0.95rem; line-height: 1.65; color: rgba(255, 255, 255, 0.85); margin: 0 0 18px 0; max-width: 600px;">
              Retrouvez notre sélection de soins réunis dans un coffret rituel artisanal : <strong>CapiNova (200ml)</strong> + <strong>CapiPure (50ml)</strong> + <strong>CapiVital (125ml)</strong> + <strong>SunPure (50ml)</strong>.
            </p>
            <div style="display: flex; align-items: baseline; gap: 14px; margin-bottom: 22px; flex-wrap: wrap;">
              <span style="font-size: 1.9rem; font-weight: 800; color: #e2ad50; font-family: 'Playfair Display', Georgia, serif;">
                {{ round($bundlePack->price_mad) }} DH
              </span>
              <span style="font-size: 1.15rem; color: rgba(255,255,255,0.45); text-decoration: line-through;">
                {{ round($bundlePack->old_price_mad) }} DH
              </span>
              <span style="background: #e2ad50; color: #0c261e; font-size: 0.78rem; font-weight: 800; padding: 3px 12px; border-radius: 9999px;">
                Économisez 80 DH
              </span>
              <span style="color: rgba(255,255,255,0.75); font-size: 0.84rem;">• Livraison Gratuite au Maroc</span>
            </div>
            <div style="display: flex; gap: 14px; flex-wrap: wrap;">
              <button onclick="addToCart({{ $bundlePack->id }}, 1, this)" class="sp-btn-gold" style="padding: 12px 28px; font-size: 0.92rem; border: none; cursor: pointer; border-radius: 9999px; display: inline-flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span>Commander le Pack (385 DH)</span>
              </button>
              <a href="{{ route('products.show', $bundlePack->slug) }}" class="sp-btn-outline" style="border: 1.5px solid rgba(226,173,80,0.5); color: #ffffff; padding: 11px 22px; text-decoration: none; border-radius: 9999px; font-size: 0.88rem; font-weight: 600;">
                Détails du coffret
              </a>
            </div>
          </div>
          <div style="flex-shrink: 0; width: 220px; height: 220px; border-radius: 20px; overflow: hidden; border: 2px solid rgba(226,173,80,0.4); box-shadow: 0 12px 30px rgba(0,0,0,0.35); z-index: 2; cursor: pointer;">
            <a href="{{ route('products.show', $bundlePack->slug) }}" style="display: block; width: 100%; height: 100%;" aria-label="Découvrir {{ $bundlePack->name }}">
              <img src="{{ asset($bundlePack->image) }}" alt="{{ $bundlePack->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            </a>
          </div>
        </div>
      @endif

    </div>
  </section>

  <!-- ==========================================================================
       SECTION ENGAGEMENT : 100% NATUREL & CERTIFIÉ BIO
       ========================================================================== -->
  <style>
    /* Scoped Styles for 100% Naturel & Bio Section */
    .sp-bio-section {
      background: linear-gradient(180deg, #ffffff 0%, #faf8f3 50%, #f7f4ed 100%) !important;
      padding: 90px 0 100px 0 !important;
      position: relative !important;
      overflow: hidden !important;
      scroll-margin-top: 100px !important;
    }
    .sp-bio-container {
      max-width: 1280px !important;
      margin: 0 auto !important;
      padding: 0 24px !important;
      position: relative !important;
      z-index: 2 !important;
    }
    .sp-bio-header {
      text-align: center !important;
      max-width: 800px !important;
      margin: 0 auto 52px auto !important;
    }
    .sp-bio-kicker {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
      font-size: 0.78rem !important;
      font-weight: 700 !important;
      letter-spacing: 0.18em !important;
      text-transform: uppercase !important;
      color: #c28d32 !important;
      margin-bottom: 12px !important;
      display: inline-block !important;
    }
    .sp-bio-title {
      font-family: 'Playfair Display', Georgia, serif !important;
      font-size: clamp(2rem, 3.4vw, 2.8rem) !important;
      font-weight: 600 !important;
      color: #0c261e !important;
      line-height: 1.2 !important;
      margin: 0 0 16px 0 !important;
    }
    .sp-bio-desc {
      font-size: 1.02rem !important;
      line-height: 1.72 !important;
      color: #63756e !important;
      margin: 0 auto !important;
    }
    .sp-bio-grid {
      display: grid !important;
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 22px !important;
      margin-bottom: 50px !important;
    }
    .sp-bio-card {
      background: #ffffff !important;
      border-radius: 20px !important;
      padding: 32px 24px !important;
      border: 1px solid rgba(226, 173, 80, 0.28) !important;
      box-shadow: 0 6px 24px rgba(12, 38, 30, 0.05) !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: space-between !important;
      transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
      position: relative !important;
      overflow: hidden !important;
    }
    .sp-bio-card:hover {
      transform: translateY(-6px) !important;
      border-color: #e2ad50 !important;
      box-shadow: 0 18px 40px rgba(12, 38, 30, 0.12) !important;
    }
    .sp-bio-icon {
      width: 54px !important;
      height: 54px !important;
      border-radius: 50% !important;
      background: linear-gradient(135deg, rgba(226, 173, 80, 0.2) 0%, rgba(12, 38, 30, 0.05) 100%) !important;
      border: 1px solid rgba(226, 173, 80, 0.4) !important;
      color: #c28d32 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      margin-bottom: 18px !important;
    }
    .sp-bio-card-title {
      font-family: 'Playfair Display', Georgia, serif !important;
      font-size: 1.28rem !important;
      font-weight: 600 !important;
      color: #0c261e !important;
      margin: 0 0 10px 0 !important;
      line-height: 1.3 !important;
    }
    .sp-bio-card-text {
      font-size: 0.89rem !important;
      line-height: 1.65 !important;
      color: #63756e !important;
      margin: 0 0 18px 0 !important;
      flex-grow: 1 !important;
    }
    .sp-bio-badge {
      display: inline-flex !important;
      align-items: center !important;
      gap: 6px !important;
      padding: 5px 14px !important;
      border-radius: 9999px !important;
      font-size: 0.76rem !important;
      font-weight: 700 !important;
      background: rgba(226, 173, 80, 0.12) !important;
      color: #c28d32 !important;
      border: 1px solid rgba(226, 173, 80, 0.35) !important;
      align-self: flex-start !important;
    }
    .sp-bio-showcase {
      background: #0c261e !important;
      background-image: radial-gradient(circle at 85% 15%, rgba(226, 173, 80, 0.16) 0%, transparent 60%) !important;
      border-radius: 24px !important;
      padding: 44px 38px !important;
      color: #ffffff !important;
      border: 1px solid rgba(226, 173, 80, 0.3) !important;
      box-shadow: 0 18px 44px rgba(12, 38, 30, 0.22) !important;
      position: relative !important;
      overflow: hidden !important;
      margin-bottom: 36px !important;
    }
    .sp-bio-showcase-header {
      display: flex !important;
      justify-content: space-between !important;
      align-items: flex-end !important;
      flex-wrap: wrap !important;
      gap: 20px !important;
      margin-bottom: 32px !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
      padding-bottom: 20px !important;
    }
    .sp-bio-showcase-title {
      font-family: 'Playfair Display', Georgia, serif !important;
      font-size: clamp(1.6rem, 2.5vw, 2.15rem) !important;
      font-weight: 600 !important;
      color: #ffffff !important;
      margin: 6px 0 0 0 !important;
    }
    .sp-bio-ing-grid {
      display: grid !important;
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 20px !important;
    }
    .sp-bio-ing-card {
      background: rgba(255, 255, 255, 0.06) !important;
      backdrop-filter: blur(10px) !important;
      -webkit-backdrop-filter: blur(10px) !important;
      border: 1px solid rgba(255, 255, 255, 0.14) !important;
      border-radius: 18px !important;
      padding: 22px !important;
      transition: all 0.3s ease !important;
      display: flex !important;
      flex-direction: column !important;
    }
    .sp-bio-ing-card:hover {
      background: rgba(255, 255, 255, 0.1) !important;
      border-color: #e2ad50 !important;
      transform: translateY(-4px) !important;
    }
    .sp-bio-ing-img-wrap {
      width: 68px !important;
      height: 68px !important;
      border-radius: 50% !important;
      overflow: hidden !important;
      border: 2.5px solid #e2ad50 !important;
      margin-bottom: 14px !important;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3) !important;
      flex-shrink: 0 !important;
    }
    .sp-bio-ing-img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      display: block !important;
    }
    .sp-bio-ing-name {
      font-family: 'Playfair Display', Georgia, serif !important;
      font-size: 1.18rem !important;
      font-weight: 600 !important;
      color: #ffffff !important;
      margin: 0 0 4px 0 !important;
    }
    .sp-bio-ing-latin {
      font-family: 'Playfair Display', Georgia, serif !important;
      font-style: italic !important;
      font-size: 0.82rem !important;
      color: #e2ad50 !important;
      margin-bottom: 10px !important;
      display: block !important;
    }
    .sp-bio-ing-desc {
      font-size: 0.86rem !important;
      line-height: 1.6 !important;
      color: rgba(255, 255, 255, 0.82) !important;
      margin: 0 !important;
    }
    .sp-commitments-section-wrap {
      margin-top: 50px !important;
      background: #ffffff !important;
      border-radius: 24px !important;
      padding: 38px 32px !important;
      border: 1px solid rgba(226, 173, 80, 0.28) !important;
      box-shadow: 0 10px 32px rgba(12, 38, 30, 0.05) !important;
      position: relative !important;
    }
    .sp-commitments-head {
      text-align: center !important;
      max-width: 720px !important;
      margin: 0 auto 36px auto !important;
    }
    .sp-commitments-kicker {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
      font-size: 0.76rem !important;
      font-weight: 700 !important;
      letter-spacing: 0.18em !important;
      text-transform: uppercase !important;
      color: #c28d32 !important;
      display: inline-block !important;
      margin-bottom: 8px !important;
    }
    .sp-commitments-title {
      font-family: 'Playfair Display', Georgia, serif !important;
      font-size: clamp(1.4rem, 2.2vw, 1.85rem) !important;
      font-weight: 600 !important;
      color: #0c261e !important;
      margin: 0 0 10px 0 !important;
      line-height: 1.25 !important;
    }
    .sp-commitments-desc {
      font-size: 0.94rem !important;
      line-height: 1.6 !important;
      color: #63756e !important;
      margin: 0 auto !important;
    }
    .sp-commitments-grid {
      display: grid !important;
      grid-template-columns: repeat(5, 1fr) !important;
      gap: 18px !important;
    }
    .sp-commitment-card {
      background: #faf8f3 !important;
      border-radius: 18px !important;
      padding: 26px 20px !important;
      border: 1px solid rgba(226, 173, 80, 0.25) !important;
      box-shadow: 0 4px 16px rgba(12, 38, 30, 0.03) !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: space-between !important;
      transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
      position: relative !important;
    }
    .sp-commitment-card:hover {
      background: #ffffff !important;
      transform: translateY(-6px) !important;
      border-color: #e2ad50 !important;
      box-shadow: 0 14px 34px rgba(12, 38, 30, 0.1) !important;
    }
    .sp-commitment-card:hover .sp-commitment-icon {
      transform: scale(1.08) !important;
      background: linear-gradient(135deg, rgba(226, 173, 80, 0.3) 0%, rgba(12, 38, 30, 0.08) 100%) !important;
      border-color: #e2ad50 !important;
    }
    .sp-commitment-top {
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      margin-bottom: 16px !important;
    }
    .sp-commitment-icon {
      width: 50px !important;
      height: 50px !important;
      border-radius: 50% !important;
      background: linear-gradient(135deg, rgba(226, 173, 80, 0.18) 0%, rgba(12, 38, 30, 0.04) 100%) !important;
      border: 1.5px solid rgba(226, 173, 80, 0.45) !important;
      color: #c28d32 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      transition: all 0.3s ease !important;
      flex-shrink: 0 !important;
    }
    .sp-commitment-num {
      font-family: 'Playfair Display', Georgia, serif !important;
      font-size: 1.25rem !important;
      font-weight: 700 !important;
      color: rgba(194, 141, 50, 0.4) !important;
    }
    .sp-commitment-title {
      font-family: 'Playfair Display', Georgia, serif !important;
      font-size: 1.06rem !important;
      font-weight: 600 !important;
      color: #0c261e !important;
      margin: 0 0 8px 0 !important;
      line-height: 1.3 !important;
    }
    .sp-commitment-desc {
      font-size: 0.83rem !important;
      line-height: 1.6 !important;
      color: #63756e !important;
      margin: 0 0 16px 0 !important;
      flex-grow: 1 !important;
    }
    .sp-commitment-tag {
      display: inline-flex !important;
      align-items: center !important;
      gap: 5px !important;
      font-size: 0.72rem !important;
      font-weight: 700 !important;
      color: #c28d32 !important;
      background: rgba(226, 173, 80, 0.1) !important;
      border: 1px solid rgba(226, 173, 80, 0.3) !important;
      padding: 3px 10px !important;
      border-radius: 9999px !important;
      align-self: flex-start !important;
    }
    .sp-bio-showcase-btn:hover {
      background: #e2ad50 !important;
      color: #0c261e !important;
      border-color: #e2ad50 !important;
      transform: translateY(-2px) !important;
    }
    @media (max-width: 1100px) {
      .sp-commitments-grid { grid-template-columns: repeat(3, 1fr) !important; }
    }
    @media (max-width: 1024px) {
      .sp-bio-grid { grid-template-columns: repeat(2, 1fr) !important; }
      .sp-bio-ing-grid { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 768px) {
      .sp-commitments-grid { grid-template-columns: repeat(2, 1fr) !important; }
      .sp-commitments-section-wrap { padding: 26px 18px !important; }
    }
    @media (max-width: 640px) {
      .sp-bio-grid { grid-template-columns: 1fr !important; }
      .sp-bio-ing-grid { grid-template-columns: 1fr !important; }
      .sp-bio-showcase { padding: 26px 20px !important; }
    }
    @media (max-width: 480px) {
      .sp-commitments-grid { grid-template-columns: 1fr !important; }
    }
  </style>

  <section class="sp-bio-section" id="engagements-bio" style="scroll-margin-top: 100px; background: linear-gradient(180deg, #ffffff 0%, #faf8f3 50%, #f7f4ed 100%); padding: 90px 0 100px 0; position: relative; overflow: hidden;">
    <!-- Top Wave Transition -->
    <div class="sp-bio-wave-top" style="position: absolute; top: 0; left: 0; width: 100%; line-height: 0; z-index: 2;">
      <svg viewBox="0 0 1440 36" preserveAspectRatio="none" fill="#ffffff" style="width: 100%; height: 36px; display: block;">
        <path d="M0,0 C480,36 960,36 1440,0 L1440,36 L0,36 Z"></path>
      </svg>
    </div>

    <!-- Decorative Botanical SVGs -->
    <svg class="sp-bio-watermark-left" viewBox="0 0 120 360" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position: absolute; top: 40px; left: -20px; width: 140px; height: 380px; pointer-events: none; opacity: 0.25; z-index: 1;">
      <path d="M10 350 C20 280, 40 200, 60 140 C75 95, 95 40, 110 10" stroke="#e2ad50" stroke-width="1.6" stroke-dasharray="5 3"/>
      <circle cx="60" cy="140" r="18" stroke="#e2ad50" stroke-width="1.2" fill="rgba(226,173,80,0.08)"/>
      <circle cx="110" cy="10" r="10" stroke="#e2ad50" stroke-width="1.2" fill="rgba(226,173,80,0.12)"/>
    </svg>

    <div class="sp-bio-container" style="max-width: 1280px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 2;">
      
      <!-- 1. En-tête de Section -->
      <div class="sp-bio-header" style="text-align: center; max-width: 800px; margin: 0 auto 52px auto;">
        <span class="sp-bio-kicker" style="font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-size: 0.78rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: #c28d32; margin-bottom: 12px; display: inline-block;">ENGAGEMENT DE PURETÉ BOTANIQUE</span>
        <h2 class="sp-bio-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(2rem, 3.4vw, 2.8rem); font-weight: 600; color: #0c261e; line-height: 1.2; margin: 0 0 16px 0;">
          Des Soins 100% Naturels &amp; Bio,<br>Puissance Pure du Haut Atlas
        </h2>
        <div class="sp-wave-squiggle" style="margin: 14px auto 18px auto; text-align: center;">
          <svg width="42" height="6" viewBox="0 0 38 6" fill="none" style="display: inline-block;">
            <path d="M1 3 C6 0.5, 12 5.5, 18 3 C24 0.5, 30 5.5, 37 3" stroke="#c28d32" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <p class="sp-bio-desc" style="font-size: 1.02rem; line-height: 1.72; color: #63756e; margin: 0 auto;">
          À la <strong>Coopérative Aït Oumdis</strong>, nous refusons tout compromis chimique. Chaque formule est élaborée à partir d’actifs botaniques sauvages cueillis à la main à 1 800 mètres d'altitude, sans pesticides, sans conservateurs nocifs ni silicones. Une alliance parfaite entre tradition berbère ancestrale et haute exigence biologique.
        </p>
      </div>

      <!-- 2. Les 4 Piliers de l'Excellence 100% Bio & Naturelle -->
      <div class="sp-bio-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 22px; margin-bottom: 50px;">
        
        <!-- Pilier 1 : Cueillette Sauvage d'Altitude -->
        <div class="sp-bio-card" style="background: #ffffff; border-radius: 20px; padding: 32px 24px; border: 1px solid rgba(226, 173, 80, 0.28); box-shadow: 0 6px 24px rgba(12, 38, 30, 0.05); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div class="sp-bio-icon" style="width: 54px; height: 54px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.2) 0%, rgba(12, 38, 30, 0.05) 100%); border: 1px solid rgba(226, 173, 80, 0.4); color: #c28d32; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
            </div>
            <h3 class="sp-bio-card-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.28rem; font-weight: 600; color: #0c261e; margin: 0 0 10px 0; line-height: 1.3;">Flore Sauvage d'Altitude</h3>
            <p class="sp-bio-card-text" style="font-size: 0.89rem; line-height: 1.65; color: #63756e; margin: 0 0 18px 0;">
              Nos plantes médicinales (romarin sauvage, thym de montagne) s'épanouissent naturellement à 1 800m d'altitude dans la province d'Azilal, loin de toute pollution urbaine ou industrielle. Leur concentration en polyphénols protecteurs est 3 fois supérieure.
            </p>
          </div>
          <span class="sp-bio-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 9999px; font-size: 0.76rem; font-weight: 700; background: rgba(226, 173, 80, 0.12); color: #c28d32; border: 1px solid rgba(226, 173, 80, 0.35); align-self: flex-start;">🌱 100% Sauvage &amp; Sans Pesticide</span>
        </div>

        <!-- Pilier 2 : Extraction Noble à Froid -->
        <div class="sp-bio-card" style="background: #ffffff; border-radius: 20px; padding: 32px 24px; border: 1px solid rgba(226, 173, 80, 0.28); box-shadow: 0 6px 24px rgba(12, 38, 30, 0.05); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div class="sp-bio-icon" style="width: 54px; height: 54px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.2) 0%, rgba(12, 38, 30, 0.05) 100%); border: 1px solid rgba(226, 173, 80, 0.4); color: #c28d32; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>
            </div>
            <h3 class="sp-bio-card-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.28rem; font-weight: 600; color: #0c261e; margin: 0 0 10px 0; line-height: 1.3;">Première Pression à Froid</h3>
            <p class="sp-bio-card-text" style="font-size: 0.89rem; line-height: 1.65; color: #63756e; margin: 0 0 18px 0;">
              Nos huiles précieuses d’argan sont pressées mécaniquement à froid sans solvant chimique. Nos hydrolats sont distillés lentement à la vapeur d'eau de source. Zéro raffinage, préservant ainsi 100% des antioxydants, omégas et vitamines E actives.
            </p>
          </div>
          <span class="sp-bio-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 9999px; font-size: 0.76rem; font-weight: 700; background: rgba(226, 173, 80, 0.12); color: #c28d32; border: 1px solid rgba(226, 173, 80, 0.35); align-self: flex-start;">✨ Actifs Vivants Intacts</span>
        </div>

        <!-- Pilier 3 : Formule Propre 0% Toxique -->
        <div class="sp-bio-card" style="background: #ffffff; border-radius: 20px; padding: 32px 24px; border: 1px solid rgba(226, 173, 80, 0.28); box-shadow: 0 6px 24px rgba(12, 38, 30, 0.05); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div class="sp-bio-icon" style="width: 54px; height: 54px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.2) 0%, rgba(12, 38, 30, 0.05) 100%); border: 1px solid rgba(226, 173, 80, 0.4); color: #c28d32; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3 class="sp-bio-card-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.28rem; font-weight: 600; color: #0c261e; margin: 0 0 10px 0; line-height: 1.3;">Charte Clean Beauty 0%</h3>
            <p class="sp-bio-card-text" style="font-size: 0.89rem; line-height: 1.65; color: #63756e; margin: 0 0 18px 0;">
              Absence totale de sulfates (SLS/SLES), parabènes, silicones occlusifs, huiles minérales issues du pétrole et perturbateurs endocriniens. Nos soins respectent le microbiome naturel de votre peau et conviennent aux peaux ultra-sensibles.
            </p>
          </div>
          <span class="sp-bio-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 9999px; font-size: 0.76rem; font-weight: 700; background: rgba(226, 173, 80, 0.12); color: #c28d32; border: 1px solid rgba(226, 173, 80, 0.35); align-self: flex-start;">🛡️ Zéro Perturbateur Endocrinien</span>
        </div>

        <!-- Pilier 4 : Filtres Minéraux & Éco-Conception -->
        <div class="sp-bio-card" style="background: #ffffff; border-radius: 20px; padding: 32px 24px; border: 1px solid rgba(226, 173, 80, 0.28); box-shadow: 0 6px 24px rgba(12, 38, 30, 0.05); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div class="sp-bio-icon" style="width: 54px; height: 54px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.2) 0%, rgba(12, 38, 30, 0.05) 100%); border: 1px solid rgba(226, 173, 80, 0.4); color: #c28d32; display: flex; align-items: center; justify-content: center; margin-bottom: 18px;">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
            </div>
            <h3 class="sp-bio-card-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.28rem; font-weight: 600; color: #0c261e; margin: 0 0 10px 0; line-height: 1.3;">Minéraux &amp; Respect Océans</h3>
            <p class="sp-bio-card-text" style="font-size: 0.89rem; line-height: 1.65; color: #63756e; margin: 0 0 18px 0;">
              Notre écran solaire SunPure intègre exclusivement des filtres minéraux d'origine naturelle (oxyde de zinc pur non-nano). Ils forment un bouclier physique réflecteur d’UV sans pénétrer le sang et sans dégrader les barrières coralliennes.
            </p>
          </div>
          <span class="sp-bio-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 9999px; font-size: 0.76rem; font-weight: 700; background: rgba(226, 173, 80, 0.12); color: #c28d32; border: 1px solid rgba(226, 173, 80, 0.35); align-self: flex-start;">🌊 Ocean-Friendly &amp; Biodégradable</span>
        </div>

      </div>

      <!-- 3. Vitrine Ingrédients Phares 100% Naturels de l'Atlas -->
      <div class="sp-bio-showcase" style="background: #0c261e; background-image: radial-gradient(circle at 85% 15%, rgba(226, 173, 80, 0.16) 0%, transparent 60%); border-radius: 24px; padding: 44px 38px; color: #ffffff; border: 1px solid rgba(226, 173, 80, 0.3); box-shadow: 0 18px 44px rgba(12, 38, 30, 0.22); margin-bottom: 36px; position: relative; overflow: hidden;">
        <div class="sp-bio-showcase-header" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; margin-bottom: 32px; border-bottom: 1px solid rgba(255, 255, 255, 0.12); padding-bottom: 20px;">
          <div>
            <span class="sp-section-kicker" style="color: #e2ad50; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.78rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; display: block; margin-bottom: 6px;">CŒUR DE FORMULATION</span>
            <h3 class="sp-bio-showcase-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.6rem, 2.5vw, 2.15rem); font-weight: 600; color: #ffffff; margin: 0;">Les Trésors Botaniques au Cœur de Nos Soins</h3>
          </div>
          <a href="{{ route('shop.index') }}" class="sp-btn-outline sp-bio-showcase-btn" style="border: 1.5px solid #e2ad50; background: rgba(226, 173, 80, 0.15); color: #ffffff; padding: 10px 22px; border-radius: 9999px; text-decoration: none; font-size: 0.86rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
            <span>Explorer la gamme bio</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

        <div class="sp-bio-ing-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px;">
          
          <!-- Argan Bio -->
          <div class="sp-bio-ing-card" style="background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.14); border-radius: 18px; padding: 22px; display: flex; flex-direction: column;">
            <div class="sp-bio-ing-img-wrap" style="width: 68px; height: 68px; border-radius: 50%; overflow: hidden; border: 2.5px solid #e2ad50; margin-bottom: 14px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3); flex-shrink: 0;">
              <img src="{{ asset('assets/images/product_argan_hd.jpg') }}" alt="Huile d'Argan Bio" class="sp-bio-ing-img" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            <h4 class="sp-bio-ing-name" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.18rem; font-weight: 600; color: #ffffff; margin: 0 0 4px 0;">Huile d'Argan Pure Bio</h4>
            <span class="sp-bio-ing-latin" style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-size: 0.82rem; color: #e2ad50; margin-bottom: 10px; display: block;">Argania Spinosa Kernel Oil</span>
            <p class="sp-bio-ing-desc" style="font-size: 0.86rem; line-height: 1.6; color: rgba(255, 255, 255, 0.82); margin: 0;">
              Riche en omégas 6, 9 et en vitamine E naturelle. Régénère la barrière lipidique cutanée, nourrit la fibre capillaire en profondeur et prévient le photovieillissement cellulaire.
            </p>
          </div>

          <!-- Romarin Sauvage -->
          <div class="sp-bio-ing-card" style="background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.14); border-radius: 18px; padding: 22px; display: flex; flex-direction: column;">
            <div class="sp-bio-ing-img-wrap" style="width: 68px; height: 68px; border-radius: 50%; overflow: hidden; border: 2.5px solid #e2ad50; margin-bottom: 14px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3); flex-shrink: 0;">
              <img src="{{ asset('assets/images/huile.jpg') }}" alt="Romarin Sauvage de l'Atlas" class="sp-bio-ing-img" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            <h4 class="sp-bio-ing-name" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.18rem; font-weight: 600; color: #ffffff; margin: 0 0 4px 0;">Romarin Sauvage d'Altitude</h4>
            <span class="sp-bio-ing-latin" style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-size: 0.82rem; color: #e2ad50; margin-bottom: 10px; display: block;">Rosmarinus Officinalis</span>
            <p class="sp-bio-ing-desc" style="font-size: 0.86rem; line-height: 1.6; color: rgba(255, 255, 255, 0.82); margin: 0;">
              Récolté sur les crêtes rocheuses d’Azilal. Puissant stimulant circulatoire qui fortifie le bulbe pileux, freine la chute des cheveux et assainit le cuir chevelu en douceur.
            </p>
          </div>

          <!-- Cèdre Sauvage de l'Atlas -->
          <div class="sp-bio-ing-card" style="background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.14); border-radius: 18px; padding: 22px; display: flex; flex-direction: column;">
            <div class="sp-bio-ing-img-wrap" style="width: 68px; height: 68px; border-radius: 50%; overflow: hidden; border: 2.5px solid #e2ad50; margin-bottom: 14px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3); flex-shrink: 0;">
              <img src="{{ asset('assets/images/spray.jpg') }}" alt="Cèdre Sauvage de l'Atlas" class="sp-bio-ing-img" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            <h4 class="sp-bio-ing-name" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.18rem; font-weight: 600; color: #ffffff; margin: 0 0 4px 0;">Cèdre Sauvage de l'Atlas</h4>
            <span class="sp-bio-ing-latin" style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-size: 0.82rem; color: #e2ad50; margin-bottom: 10px; display: block;">Cedrus Atlantica Bark</span>
            <p class="sp-bio-ing-desc" style="font-size: 0.86rem; line-height: 1.6; color: rgba(255, 255, 255, 0.82); margin: 0;">
              Issu des massifs préservés d'Azilal. Un élixir végétal d'exception qui tonifie le cuir chevelu, régule la production de sébum et stimule activement la repousse des follicules pileux.
            </p>
          </div>

          <!-- Filtres Minéraux Purs -->
          <div class="sp-bio-ing-card" style="background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.14); border-radius: 18px; padding: 22px; display: flex; flex-direction: column;">
            <div class="sp-bio-ing-img-wrap" style="width: 68px; height: 68px; border-radius: 50%; overflow: hidden; border: 2.5px solid #e2ad50; margin-bottom: 14px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3); flex-shrink: 0;">
              <img src="{{ asset('assets/images/ecran.jpg') }}" alt="Écran Solaire Minéral" class="sp-bio-ing-img" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            <h4 class="sp-bio-ing-name" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.18rem; font-weight: 600; color: #ffffff; margin: 0 0 4px 0;">Filtres Minéraux Purs</h4>
            <span class="sp-bio-ing-latin" style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-size: 0.82rem; color: #e2ad50; margin-bottom: 10px; display: block;">Non-Nano Zinc Oxide</span>
            <p class="sp-bio-ing-desc" style="font-size: 0.86rem; line-height: 1.6; color: rgba(255, 255, 255, 0.82); margin: 0;">
              Barrière minérale naturelle réfléchissant 98% des rayons UVA/UVB sans absorption cutanée. Texture invisible et soyeuse qui ne blanchit pas la peau et respecte les océans.
            </p>
          </div>

        </div>
      </div>

      <!-- 4. Charte de Pureté & Engagements Inaltérables (5 Piliers d'Excellence) -->
      <div class="sp-commitments-section-wrap" style="margin-top: 50px; background: #ffffff; border-radius: 24px; padding: 40px 34px; border: 1px solid rgba(226, 173, 80, 0.28); box-shadow: 0 10px 32px rgba(12, 38, 30, 0.05); position: relative;">
        
        <!-- En-tête de la Charte -->
        <div class="sp-commitments-head" style="text-align: center; max-width: 760px; margin: 0 auto 38px auto;">
          <span class="sp-commitments-kicker" style="font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: #c28d32; display: inline-block; margin-bottom: 8px;">
            CHARTE DE CONFIANCE &amp; DE PURETÉ BOTANIQUE
          </span>
          <h3 class="sp-commitments-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.45rem, 2.3vw, 1.95rem); font-weight: 600; color: #0c261e; margin: 0 0 10px 0; line-height: 1.25;">
            Nos 5 Engagements Inaltérables pour Votre Beauté
          </h3>
          <p class="sp-commitments-desc" style="font-size: 0.94rem; line-height: 1.62; color: #63756e; margin: 0 auto;">
            Chaque formule élaborée au cœur du Haut Atlas honore ces 5 piliers éthiques, sans aucun compromis chimique ni concession sur l'efficacité.
          </p>
        </div>

        <!-- Grille des 5 Cartes d'Engagement -->
        <div class="sp-commitments-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 18px;">

          <!-- Pilier 1 : 100% Ingrédients Naturels -->
          <div class="sp-commitment-card" style="background: #faf8f3; border-radius: 18px; padding: 26px 20px; border: 1px solid rgba(226, 173, 80, 0.25); box-shadow: 0 4px 16px rgba(12, 38, 30, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.35s ease;">
            <div>
              <div class="sp-commitment-top" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div class="sp-commitment-icon" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.18) 0%, rgba(12, 38, 30, 0.04) 100%); border: 1.5px solid rgba(226, 173, 80, 0.45); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                    <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                  </svg>
                </div>
                <span class="sp-commitment-num" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.25rem; font-weight: 700; color: rgba(194, 141, 50, 0.4);">01</span>
              </div>
              <h4 class="sp-commitment-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.06rem; font-weight: 600; color: #0c261e; margin: 0 0 8px 0; line-height: 1.3;">100% Origine Naturelle</h4>
              <p class="sp-commitment-desc" style="font-size: 0.83rem; line-height: 1.6; color: #63756e; margin: 0 0 16px 0;">
                Actifs botaniques sauvages et huiles végétales pures pressées à froid. Zéro ingrédient pétrochimique ni molécule synthétique.
              </p>
            </div>
            <span class="sp-commitment-tag" style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.72rem; font-weight: 700; color: #c28d32; background: rgba(226, 173, 80, 0.1); border: 1px solid rgba(226, 173, 80, 0.3); padding: 3px 10px; border-radius: 9999px; align-self: flex-start;">🌿 Pureté Vivante</span>
          </div>

          <!-- Pilier 2 : 0% Sulfates, Silicones & Parabènes -->
          <div class="sp-commitment-card" style="background: #faf8f3; border-radius: 18px; padding: 26px 20px; border: 1px solid rgba(226, 173, 80, 0.25); box-shadow: 0 4px 16px rgba(12, 38, 30, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.35s ease;">
            <div>
              <div class="sp-commitment-top" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div class="sp-commitment-icon" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.18) 0%, rgba(12, 38, 30, 0.04) 100%); border: 1.5px solid rgba(226, 173, 80, 0.45); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <line x1="9.5" y1="9.5" x2="14.5" y2="14.5"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </div>
                <span class="sp-commitment-num" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.25rem; font-weight: 700; color: rgba(194, 141, 50, 0.4);">02</span>
              </div>
              <h4 class="sp-commitment-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.06rem; font-weight: 600; color: #0c261e; margin: 0 0 8px 0; line-height: 1.3;">0% Sulfates &amp; Silicones</h4>
              <p class="sp-commitment-desc" style="font-size: 0.83rem; line-height: 1.6; color: #63756e; margin: 0 0 16px 0;">
                Absence totale de sulfates (SLS), parabènes, silicones occlusifs et perturbateurs endocriniens. Respecte le microbiome.
              </p>
            </div>
            <span class="sp-commitment-tag" style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.72rem; font-weight: 700; color: #c28d32; background: rgba(226, 173, 80, 0.1); border: 1px solid rgba(226, 173, 80, 0.3); padding: 3px 10px; border-radius: 9999px; align-self: flex-start;">🛡️ Clean Beauty 0%</span>
          </div>

          <!-- Pilier 3 : Terroir Préservé d'Azilal (1 800m) -->
          <div class="sp-commitment-card" style="background: #faf8f3; border-radius: 18px; padding: 26px 20px; border: 1px solid rgba(226, 173, 80, 0.25); box-shadow: 0 4px 16px rgba(12, 38, 30, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.35s ease;">
            <div>
              <div class="sp-commitment-top" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div class="sp-commitment-icon" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.18) 0%, rgba(12, 38, 30, 0.04) 100%); border: 1.5px solid rgba(226, 173, 80, 0.45); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m8 3 4 8 5-5 5 15H2L8 3z"/>
                    <circle cx="18" cy="5" r="2.5" fill="rgba(226,173,80,0.3)"/>
                  </svg>
                </div>
                <span class="sp-commitment-num" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.25rem; font-weight: 700; color: rgba(194, 141, 50, 0.4);">03</span>
              </div>
              <h4 class="sp-commitment-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.06rem; font-weight: 600; color: #0c261e; margin: 0 0 8px 0; line-height: 1.3;">Terroir d'Azilal (1 800m)</h4>
              <p class="sp-commitment-desc" style="font-size: 0.83rem; line-height: 1.6; color: #63756e; margin: 0 0 16px 0;">
                Plantes sauvages récoltées à la main sur les crêtes rocheuses du Haut Atlas. Concentration en antioxydants 3x supérieure.
              </p>
            </div>
            <span class="sp-commitment-tag" style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.72rem; font-weight: 700; color: #c28d32; background: rgba(226, 173, 80, 0.1); border: 1px solid rgba(226, 173, 80, 0.3); padding: 3px 10px; border-radius: 9999px; align-self: flex-start;">🏔️ Altitude Pure</span>
          </div>

          <!-- Pilier 4 : Coopérative Équitable & Solidaire -->
          <div class="sp-commitment-card" style="background: #faf8f3; border-radius: 18px; padding: 26px 20px; border: 1px solid rgba(226, 173, 80, 0.25); box-shadow: 0 4px 16px rgba(12, 38, 30, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.35s ease;">
            <div>
              <div class="sp-commitment-top" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div class="sp-commitment-icon" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.18) 0%, rgba(12, 38, 30, 0.04) 100%); border: 1.5px solid rgba(226, 173, 80, 0.45); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </div>
                <span class="sp-commitment-num" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.25rem; font-weight: 700; color: rgba(194, 141, 50, 0.4);">04</span>
              </div>
              <h4 class="sp-commitment-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.06rem; font-weight: 600; color: #0c261e; margin: 0 0 8px 0; line-height: 1.3;">Coopérative Équitable</h4>
              <p class="sp-commitment-desc" style="font-size: 0.83rem; line-height: 1.6; color: #63756e; margin: 0 0 16px 0;">
                Valorisation directe du travail des femmes et familles rurales d'Aït Oumdis. Rémunération juste et autonomisation durable.
              </p>
            </div>
            <span class="sp-commitment-tag" style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.72rem; font-weight: 700; color: #c28d32; background: rgba(226, 173, 80, 0.1); border: 1px solid rgba(226, 173, 80, 0.3); padding: 3px 10px; border-radius: 9999px; align-self: flex-start;">🤝 Éthique Berbère</span>
          </div>

          <!-- Pilier 5 : Cruelty-Free & Formule Clean -->
          <div class="sp-commitment-card" style="background: #faf8f3; border-radius: 18px; padding: 26px 20px; border: 1px solid rgba(226, 173, 80, 0.25); box-shadow: 0 4px 16px rgba(12, 38, 30, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.35s ease;">
            <div>
              <div class="sp-commitment-top" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div class="sp-commitment-icon" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, rgba(226, 173, 80, 0.18) 0%, rgba(12, 38, 30, 0.04) 100%); border: 1.5px solid rgba(226, 173, 80, 0.45); color: #c28d32; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                    <circle cx="12" cy="11" r="1.5" fill="currentColor"/>
                  </svg>
                </div>
                <span class="sp-commitment-num" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.25rem; font-weight: 700; color: rgba(194, 141, 50, 0.4);">05</span>
              </div>
              <h4 class="sp-commitment-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.06rem; font-weight: 600; color: #0c261e; margin: 0 0 8px 0; line-height: 1.3;">Cruelty-Free &amp; Éco-Conçu</h4>
              <p class="sp-commitment-desc" style="font-size: 0.83rem; line-height: 1.6; color: #63756e; margin: 0 0 16px 0;">
                Aucun test sur les animaux. Filtres minéraux et ingrédients biodégradables préservant la vie marine et les récifs coralliens.
              </p>
            </div>
            <span class="sp-commitment-tag" style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.72rem; font-weight: 700; color: #c28d32; background: rgba(226, 173, 80, 0.1); border: 1px solid rgba(226, 173, 80, 0.3); padding: 3px 10px; border-radius: 9999px; align-self: flex-start;">🐰 100% Éco-Responsable</span>
          </div>

        </div>
      </div>

    </div>
  </section>

  <!-- ==========================================================================
       3. SECTION À PROPOS DE NOUS (AIT OUMDIS, PLUS QU'UNE COOPÉRATIVE)
       ========================================================================== -->
  <section class="sp-about-section" id="a-propos" style="background-color: #0c261e;">
    <!-- Top Wave Curve -->
    <div class="sp-about-wave-top">
      <svg viewBox="0 0 1440 38" preserveAspectRatio="none" fill="var(--sp-cream-bg)">
        <path d="M0,0 L1440,0 C1080,26 620,38 0,18 Z"></path>
      </svg>
    </div>

    <div class="sp-about-overlay"></div>

    <div class="sp-about-container">
      
      <!-- Left Content Floating Seamlessly over Mountain Mist -->
      <div class="sp-about-content-wrap">
        <span class="sp-section-kicker">À PROPOS DE NOUS</span>
        <h2 class="sp-about-title">
          Ait Oumdis,<br>plus qu'une coopérative
        </h2>
        <p class="sp-about-text">
          Fondée en 2024, la coopérative Ait Oumdis regroupe des femmes et des hommes passionnés par la richesse de notre terroir. Nous valorisons les trésors naturels du Maroc à travers des produits authentiques, sains et <strong>respectueux de l'environnement</strong>.
        </p>
        <a href="{{ route('home') . '#a-propos' }}" class="sp-btn-outline">
          <span>Notre histoire</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>

      <!-- Right Floating Terroir Tab over Beekeeper Sleeve -->
      <div class="sp-terroir-badge">
        <div class="sp-terroir-icon">
          <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="10" stroke="var(--sp-gold)"/>
            <path d="M12 7c-1.5 2.5-3.5 3.5-5 4 2 1 3.5 2.5 4 4.5.5-2 2-3.5 4-4.5-1.5-.5-3.5-1.5-5-4Z" fill="rgba(226, 173, 80, 0.25)" stroke="var(--sp-gold)"/>
          </svg>
        </div>
        <div class="sp-terroir-text">
          Des produits du terroir, pour un avenir durable
        </div>
        <span class="sp-terroir-line"></span>
      </div>

    </div>
  </section>

  <!-- ==========================================================================
       4. SECTION POURQUOI CHOISIR SUNPURE ?
       ========================================================================== -->
  <section class="sp-why-section" id="pourquoi-sunpure" style="background-image: linear-gradient(180deg, rgba(10, 34, 27, 0.88) 0%, rgba(10, 34, 27, 0.78) 50%, rgba(10, 34, 27, 0.92) 100%), url('{{ asset('assets/images/botanical_dark_pattern.jpg') }}'); background-size: 800px auto; background-repeat: repeat; background-position: center;">
    <!-- Top Wave Curve -->
    <div class="sp-why-wave-top">
      <svg viewBox="0 0 1440 38" preserveAspectRatio="none" fill="var(--sp-deep-green)">
        <path d="M0,18 C460,38 980,12 1440,28 L1440,38 L0,38 Z"></path>
      </svg>
    </div>

    <!-- Right Botanical Leaf Decoration -->
    <svg class="sp-why-botanical-right" viewBox="0 0 90 220" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path d="M80 210 C75 160, 68 120, 60 80 C55 50, 48 20, 42 5" stroke="var(--sp-gold)" stroke-width="1.4" opacity="0.35"/>
      <path d="M42 5 C35 15, 32 30, 38 42 C45 36, 46 22, 42 5 Z" fill="rgba(226, 173, 80, 0.12)" stroke="var(--sp-gold)" stroke-width="1.2" opacity="0.45"/>
      <path d="M60 80 C48 70, 30 75, 20 88 C32 94, 48 88, 60 80 Z" fill="rgba(226, 173, 80, 0.12)" stroke="var(--sp-gold)" stroke-width="1.2" opacity="0.45"/>
      <path d="M68 120 C54 115, 40 124, 34 138 C46 142, 60 132, 68 120 Z" fill="rgba(226, 173, 80, 0.12)" stroke="var(--sp-gold)" stroke-width="1.2" opacity="0.45"/>
      <path d="M75 160 C62 154, 48 162, 42 176 C54 180, 68 172, 75 160 Z" fill="rgba(226, 173, 80, 0.12)" stroke="var(--sp-gold)" stroke-width="1.2" opacity="0.45"/>
    </svg>

    <div class="sp-why-container">
      <div class="sp-why-grid">
        
        <!-- Left Title -->
        <div class="sp-why-left">
          <span class="sp-why-kicker">POURQUOI CHOISIR SUNPURE ?</span>
          <h2 class="sp-why-title">
            Une protection naturelle,<br>une différence visible
          </h2>
        </div>

        <!-- Right 4 Icon Pillars avec séparateurs fins -->
        <div class="sp-why-items">
          <!-- 1. Haute protection -->
          <div class="sp-why-col">
            <div class="sp-why-icon-wrap">
              <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
            </div>
            <h4 class="sp-why-heading">Haute protection</h4>
            <span class="sp-why-sub">FPS 50+</span>
          </div>

          <!-- 2. Formule naturelle -->
          <div class="sp-why-col">
            <div class="sp-why-icon-wrap">
              <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>
            </div>
            <h4 class="sp-why-heading">Formule naturelle</h4>
            <span class="sp-why-sub">et douce</span>
          </div>

          <!-- 3. Texture légère -->
          <div class="sp-why-col">
            <div class="sp-why-icon-wrap">
              <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <path d="M8 4 C9 7, 7 10, 8 13 C9 16, 8 19, 8 20" stroke-linecap="round"/>
                <path d="M12 3 C13 6, 11 9, 12 12 C13 15, 12 18, 12 21" stroke-linecap="round"/>
                <path d="M16 4 C17 7, 15 10, 16 13 C17 16, 16 19, 16 20" stroke-linecap="round"/>
              </svg>
            </div>
            <h4 class="sp-why-heading">Texture légère</h4>
            <span class="sp-why-sub">et invisible</span>
          </div>

          <!-- 4. Respecte votre peau -->
          <div class="sp-why-col">
            <div class="sp-why-icon-wrap">
              <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            </div>
            <h4 class="sp-why-heading">Respecte votre peau</h4>
            <span class="sp-why-sub">et l'environnement</span>
          </div>
        </div>

      </div>
    </div>
  </section>
@endsection
