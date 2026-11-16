@extends('layouts.frontend')

@section('content')
@php
    $dashboardCategories = \App\Models\Category::withCount(['products' => function ($q) {
        $q->where('status', 'active');
    }])->get();

    $dashboardProducts = \App\Models\Product::with(['productCategory', 'images', 'primaryImage', 'variants'])
        ->where('status', 'active')
        ->orderBy('id', 'asc')
        ->get();
@endphp

<!-- ==========================================================================
     1. CLEAN HERO SECTION WITH SOFT MOROCCAN ZELLIGE PATTERN & LOGO PALETTE
     ========================================================================== -->
<section class="aod-hero aod-zellige-soft">
  <div class="aod-container">
    <div class="aod-hero-inner">
      
      <!-- Left Hero Content -->
      <div>
        <div class="aod-hero-badge">
          <i class="fas fa-leaf"></i>
          <span>COOPÉRATIVE AÏT OUMDIS • 100% NATUREL</span>
        </div>

        <h1 class="aod-hero-title">
          Miels Purs, <span class="text-green">Amlou Artisanal</span> &amp; Trésors du Terroir Marocain
        </h1>

        <p class="aod-hero-desc">
          Découvrez nos miels 100% purs aux vertus thérapeutiques reconnues (Thym, Daghmous, Sidr, Fernan, Caroubier, Oranger, Eucalyptus, Herbes), notre Amlou aux amandes et huile d'Argan, et notre Safran véritable.
        </p>

        <div class="aod-hero-actions">
          <a href="#produits" class="aod-btn-green">
            <span>Découvrir nos produits</span>
            <i class="fas fa-arrow-down"></i>
          </a>
          <a href="{{ route('shop.index') }}" class="aod-btn-outline">
            <span>Catalogue complet</span>
            <i class="fas fa-store"></i>
          </a>
        </div>

        <div class="aod-hero-trust">
          <div class="aod-trust-item">
            <i class="fas fa-check-circle"></i>
            <span>100% Pur &amp; Brut</span>
          </div>
          <div class="aod-trust-item">
            <i class="fas fa-truck"></i>
            <span>Livraison Partout au Maroc</span>
          </div>
          <div class="aod-trust-item">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Paiement à la Livraison</span>
          </div>
        </div>
      </div>

      <!-- Right Hero Medallion with Official Logo -->
      <div class="aod-hero-visual">
        <div class="aod-logo-medallion">
          <img src="{{ asset('assets/images/logo-zellige.jpg') }}" alt="Aït Oumdis Cooperative">
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ==========================================================================
     2. DYNAMIC PRODUCTS SECTION (100% LOADED FROM DASHBOARD)
     ========================================================================== -->
<section class="aod-catalog-section" id="produits">
  <div class="aod-container">
    
    <div class="aod-section-header">
      <div>
        <span class="aod-kicker">NOS PRODUITS NATURELS</span>
        <h2 class="aod-section-title">Catalogue de la Coopérative ({{ $dashboardProducts->count() }})</h2>
      </div>

      <!-- Dynamic Category Filter Tabs from Dashboard -->
      <div class="aod-filter-tabs" id="homeCategoryTabs">
        <button type="button" class="aod-filter-tab active" data-cat="all" onclick="filterHomeProducts('all', this)">
          <span>Tous</span>
          <span>({{ $dashboardProducts->count() }})</span>
        </button>
        @foreach($dashboardCategories as $cat)
          @if($cat->products_count > 0)
          <button type="button" class="aod-filter-tab" data-cat="cat-{{ $cat->id }}" onclick="filterHomeProducts('cat-{{ $cat->id }}', this)">
            <span>{{ $cat->name }}</span>
            <span>({{ $cat->products_count }})</span>
          </button>
          @endif
        @endforeach
      </div>
    </div>

    <!-- Dynamic Products Grid -->
    <div class="aod-products-grid" id="homeProductsGrid">
      @forelse($dashboardProducts as $p)
        @include('frontend.partials.product-card', ['product' => $p])
      @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--aod-sage-bg); border-radius: 20px; border: 1.5px dashed var(--aod-border);">
          <i class="fas fa-box-open fa-3x mb-3" style="color: var(--aod-leaf-green);"></i>
          <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--aod-charcoal);">Aucun produit disponible pour le moment</h3>
          <p style="color: var(--aod-text-muted); margin-bottom: 0;">Ajoutez vos produits depuis le tableau de bord (Dashboard &rarr; Products) pour les afficher ici.</p>
        </div>
      @endforelse
    </div>

  </div>
</section>

<!-- ==========================================================================
     3. GUIDE PAR BESOIN — CHOISIR LE BON PRODUIT NATUREL
     ========================================================================== -->
<section class="aod-guide-section" id="guide-besoins">
  <div class="aod-container">
    <div class="aod-guide-head">
      <span class="aod-kicker">CONSEIL DE LA COOPÉRATIVE</span>
      <h2 class="aod-section-title" style="margin-bottom: 10px;">Quel Trésor Choisir Selon Vos Besoins ?</h2>
      <p style="font-size: 0.94rem; color: var(--aod-text-muted); margin: 0;">
        Chaque miel et produit de notre terroir possède des propriétés naturelles spécifiques pour votre bien-être au quotidien.
      </p>
    </div>

    <div class="aod-guide-grid">
      <div class="aod-guide-card">
        <div>
          <div class="aod-guide-icon"><i class="fas fa-lungs"></i></div>
          <h3 class="aod-guide-title">Immunité &amp; Voies Respiratoires</h3>
          <p class="aod-guide-text">
            Idéal pour apaiser la gorge, soulager la toux, les coups de froid et renforcer les défenses naturelles en toute saison.
          </p>
        </div>
        <a href="{{ route('shop.index', ['q' => 'Thym']) }}" class="aod-guide-rec">
          <span>Miel de Thym &amp; Daghmous</span>
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="aod-guide-card">
        <div>
          <div class="aod-guide-icon"><i class="fas fa-bolt"></i></div>
          <h3 class="aod-guide-title">Énergie &amp; Confort Digestif</h3>
          <p class="aod-guide-text">
            Favorise une digestion saine, apaise l'estomac et apporte une source d'énergie pure et durable pour toute la famille.
          </p>
        </div>
        <a href="{{ route('shop.index', ['q' => 'Caroubier']) }}" class="aod-guide-rec">
          <span>Miel de Caroubier &amp; Herbes</span>
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="aod-guide-card">
        <div>
          <div class="aod-guide-icon"><i class="fas fa-heartbeat"></i></div>
          <h3 class="aod-guide-title">Cœur &amp; Cholestérol</h3>
          <p class="aod-guide-text">
            Riche en bons acides gras et antioxydants naturels pour soutenir la santé cardiovasculaire et équilibrer le cholestérol.
          </p>
        </div>
        <a href="{{ route('shop.index', ['q' => 'Amlou']) }}" class="aod-guide-rec">
          <span>Amlou Argan &amp; Miel Fernan</span>
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="aod-guide-card">
        <div>
          <div class="aod-guide-icon"><i class="fas fa-moon"></i></div>
          <h3 class="aod-guide-title">Apaisement &amp; Sommeil</h3>
          <p class="aod-guide-text">
            Aide à calmer le système nerveux, réduire le stress quotidien et favoriser un sommeil doux et réparateur.
          </p>
        </div>
        <a href="{{ route('shop.index', ['q' => 'Oranger']) }}" class="aod-guide-rec">
          <span>Miel d'Oranger &amp; Safran</span>
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     4. NOTRE SAVOIR-FAIRE & QUESTIONS FRÉQUENTES (FAQ)
     ========================================================================== -->
<section class="aod-story-faq-section" id="a-propos">
  <div class="aod-container">
    <div class="aod-story-faq-grid">
      
      <!-- Left: Cooperative Value & Metrics -->
      <div>
        <span class="aod-kicker">COOPÉRATIVE AÏT OUMDIS</span>
        <h2 class="aod-section-title" style="margin-bottom: 14px;">
          Du Rucher à Votre Table,<br>Sans Intermédiaire
        </h2>
        <p style="font-size: 0.95rem; color: var(--aod-text-muted); line-height: 1.7; margin: 0 0 16px 0;">
          Notre coopérative réunit des apiculteurs et des artisanes engagés pour préserver l'authenticité du terroir marocain. Chaque pot de miel est extrait à froid sans chauffage industriel, et notre Amlou est préparé sur meule traditionnelle avec des amandes grillées et de l'huile d'Argan pure.
        </p>

        <div class="aod-stats-row">
          <div class="aod-stat-box">
            <span class="aod-stat-val">100%</span>
            <span class="aod-stat-lbl">Pur &amp; Sans Sucre Ajouté</span>
          </div>
          <div class="aod-stat-box">
            <span class="aod-stat-val">24–48h</span>
            <span class="aod-stat-lbl">Livraison au Maroc</span>
          </div>
          <div class="aod-stat-box">
            <span class="aod-stat-val">0 DH</span>
            <span class="aod-stat-lbl">Paiement à la Réception</span>
          </div>
        </div>
      </div>

      <!-- Right: Clean FAQ Accordion -->
      <div>
        <span class="aod-kicker">QUESTIONS FRÉQUENTES</span>
        <h3 style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.45rem; font-weight: 700; color: var(--aod-charcoal); margin: 0 0 18px 0;">
          Tout Savoir Avant de Commander
        </h3>

        <div class="aod-faq-list">
          <div class="aod-faq-item is-open">
            <button type="button" class="aod-faq-btn" onclick="toggleAodFaq(this)">
              <span>Comment garantissez-vous que vos miels sont 100% purs ?</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="aod-faq-body">
              Nos miels sont récoltés directement auprès des ruchers de la coopérative à maturité complète, extraits à froid sans pasteurisation ni ajout de sucre ou de sirop. Chaque variété conserve ses enzymes naturelles, son pollen et ses propriétés thérapeutiques.
            </div>
          </div>

          <div class="aod-faq-item">
            <button type="button" class="aod-faq-btn" onclick="toggleAodFaq(this)">
              <span>Comment se passe la livraison et le paiement ?</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="aod-faq-body">
              Nous livrons partout au Maroc en 24 à 48h dans des emballages renforcés. Vous ne payez rien en ligne : le règlement se fait en espèces directement au livreur à la réception de votre colis.
            </div>
          </div>

          <div class="aod-faq-item">
            <button type="button" class="aod-faq-btn" onclick="toggleAodFaq(this)">
              <span>Est-il normal que certains miels cristallisent ?</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="aod-faq-body">
              Oui, la cristallisation est un phénomène 100% naturel qui prouve que le miel est brut et non chauffé. Pour le liquéfier sans altérer ses bienfaits, placez simplement le pot au bain-marie tiède (moins de 40 °C).
            </div>
          </div>

          <div class="aod-faq-item">
            <button type="button" class="aod-faq-btn" onclick="toggleAodFaq(this)">
              <span>Comment conserver l'Amlou et le Safran pur ?</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="aod-faq-body">
              Conservez votre Amlou à température ambiante à l'abri du soleil (remuez légèrement si l'huile d'Argan remonte naturellement en surface). Gardez le Safran dans son pot hermétique à l'abri de la lumière et de l'humidité.
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ==========================================================================
     5. CLEAN 3-PILLAR COOPERATIVE COMMITMENTS
     ========================================================================== -->
<section class="aod-pillars-section">
  <div class="aod-container">
    <div class="aod-pillars-grid">
      
      <div class="aod-pillar-card">
        <div class="aod-pillar-icon">
          <i class="fas fa-leaf"></i>
        </div>
        <div>
          <h3 class="aod-pillar-title">Miels 100% Purs &amp; Bruts</h3>
          <p class="aod-pillar-desc">
            Récoltés à maturité et extraits à froid sans chauffage ni sucre ajouté, préservant toutes leurs vertus thérapeutiques.
          </p>
        </div>
      </div>

      <div class="aod-pillar-card">
        <div class="aod-pillar-icon">
          <i class="fas fa-mortar-pestle"></i>
        </div>
        <div>
          <h3 class="aod-pillar-title">Amlou &amp; Safran Authentiques</h3>
          <p class="aod-pillar-desc">
            Amlou moulu à la meule traditionnelle aux amandes grillées et huile d'Argan pure, et Safran véritable cueilli à la main.
          </p>
        </div>
      </div>

      <div class="aod-pillar-card">
        <div class="aod-pillar-icon">
          <i class="fas fa-shipping-fast"></i>
        </div>
        <div>
          <h3 class="aod-pillar-title">Livraison Partout au Maroc</h3>
          <p class="aod-pillar-desc">
            Emballage soigné et sécurisé avec expédition rapide dans toutes les villes du Maroc et paiement à la livraison.
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ==========================================================================
     4. FLYER-STYLE DIRECT CONTACT & INSTAGRAM SECTION
     ========================================================================== -->
<section class="aod-flyer-contact-section" id="contact-direct">
  <div class="aod-container">
    <div class="aod-flyer-contact-card">
      
      <!-- Left: Flyer Green Contact Card -->
      <div class="aod-green-contact-box">
        <a href="tel:+212706770940" class="aod-contact-row">
          <span class="aod-contact-icon"><i class="fas fa-phone-alt"></i></span>
          <span>07 06 77 09 40</span>
        </a>
        <a href="tel:+212670035202" class="aod-contact-row">
          <span class="aod-contact-icon"><i class="fas fa-phone-alt"></i></span>
          <span>06 70 03 52 02</span>
        </a>
        <a href="https://aitoumdis.com" target="_blank" rel="noopener" class="aod-contact-row">
          <span class="aod-contact-icon"><i class="fas fa-globe"></i></span>
          <span>Aitoumdis.com</span>
        </a>
      </div>

      <!-- Center: Official Logo -->
      <div class="aod-center-logo-box">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Aït Oumdis Cooperative">
      </div>

      <!-- Right: Instagram & WhatsApp Direct -->
      <div class="aod-insta-box">
        <span class="aod-kicker">COMMANDER OU NOUS SUIVRE</span>
        <h3 style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.4rem; font-weight: 700; color: var(--aod-charcoal); margin: 0;">
          Coopérative Aït Oumdis
        </h3>
        <p style="font-size: 0.9rem; color: var(--aod-text-muted); margin: 0 0 8px 0;">
          Commandez directement sur le site ou contactez-nous sur WhatsApp et Instagram <strong>@AIT_OUMDIS</strong>.
        </p>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <a href="https://wa.me/212706770940" target="_blank" rel="noopener" class="aod-btn-green" style="padding: 10px 20px; font-size: 0.86rem;">
            <i class="fab fa-whatsapp"></i>
            <span>WhatsApp</span>
          </a>
          <a href="https://www.instagram.com/ait_oumdis/" target="_blank" rel="noopener" class="aod-btn-outline" style="padding: 9px 18px; font-size: 0.86rem;">
            <i class="fab fa-instagram"></i>
            <span>@AIT_OUMDIS</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

@push('scripts')
<script>
function filterHomeProducts(catKey, btn) {
    document.querySelectorAll('#homeCategoryTabs .aod-filter-tab').forEach(function (el) {
        el.classList.remove('active');
    });
    if (btn) btn.classList.add('active');

    document.querySelectorAll('#homeProductsGrid .aod-product-card').forEach(function (card) {
        if (catKey === 'all' || card.getAttribute('data-category') === catKey) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function toggleAodFaq(btn) {
    var item = btn.closest('.aod-faq-item');
    if (!item) return;
    var wasOpen = item.classList.contains('is-open');
    document.querySelectorAll('.aod-faq-item').forEach(function (el) {
        el.classList.remove('is-open');
    });
    if (!wasOpen) {
        item.classList.add('is-open');
    }
}
</script>
@endpush
@endsection
