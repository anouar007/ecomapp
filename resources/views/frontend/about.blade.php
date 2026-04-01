@extends('layouts.frontend')

@section('meta_title', 'L\'Esprit Moubdi3oun — Artisanat de Luxe au Maroc')

@section('content')
<!-- Hero Section -->
<section class="section-py bg-dark text-white position-relative overflow-hidden" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-50">
        <img src="https://images.unsplash.com/photo-1581539250439-c96689b516dd?auto=format&fit=crop&q=80" alt="Atelier" class="w-100 h-100 object-fit-cover">
    </div>
    <div class="container position-relative z-1">
        <div class="row">
            <div class="col-lg-8">
                <span class="badge-new mb-3 d-inline-block">Notre Héritage</span>
                <h1 class="fw-black display-3 text-uppercase ls-1 mb-4">L'Art de Créer <br><span style="color: var(--accent);">l'Exceptionnel</span></h1>
                <p class="lead opacity-75 mb-0" style="max-width: 600px;">
                    Fondée sur une passion pour les matériaux nobles et le design intemporel, Moubdi3oun repousse les limites de l'artisanat marocain moderne.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Zig-Zag -->
<section class="section-py bg-white">
    <div class="container">
        <!-- Story 1 -->
        <div class="row align-items-center g-5 mb-5 pb-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="ps-lg-5">
                    <h2 class="fw-black text-uppercase ls-1 mb-4">La Vision</h2>
                    <p class="text-muted lh-lg mb-4">
                        Chez **Moubdi3oun**, nous croyons que chaque pièce de mobilier doit raconter une histoire unique. Notre vision fusionne l'héritage ancestral de la menuiserie marocaine avec les lignes minimalistes du design contemporain.
                    </p>
                    <p class="text-muted lh-lg">
                        Chaque projet commence par une feuille blanche et une écoute attentive des désirs de nos clients, pour transformer un espace en une œuvre d'art vivante.
                    </p>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="rounded-4 overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1594026112284-02bb6f3352fe?auto=format&fit=crop&q=80" alt="Design Process" class="w-100">
                </div>
            </div>
        </div>

        <!-- Story 2 -->
        <div class="row align-items-center g-5 flex-row-reverse mt-5">
            <div class="col-lg-6" data-aos="fade-left">
                <div class="pe-lg-5">
                    <h2 class="fw-black text-uppercase ls-1 mb-4">Le Savoir-Faire</h2>
                    <p class="text-muted lh-lg mb-4">
                        Notre atelier réunit les meilleurs maîtres artisans : menuisiers, métallurgistes et tapissiers de luxe. Ensemble, ils travaillent le bois massif, l'acier brossé et les tissus les plus fins pour garantir une finition impeccable.
                    </p>
                    <div class="d-flex gap-4 mt-5">
                        <div class="text-center">
                            <h3 class="fw-black mb-0">15+</h3>
                            <span class="small text-muted text-uppercase fw-bold">Artisans</span>
                        </div>
                        <div class="border-start ps-4 text-center">
                            <h3 class="fw-black mb-0">500+</h3>
                            <span class="small text-muted text-uppercase fw-bold">Projets</span>
                        </div>
                        <div class="border-start ps-4 text-center">
                            <h3 class="fw-black mb-0">100%</h3>
                            <span class="small text-muted text-uppercase fw-bold">Sur-mesure</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-right">
                <div class="rounded-4 overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80" alt="Artisan work" class="w-100">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="section-py bg-light">
    <div class="container text-center">
        <h2 class="fw-black text-uppercase ls-1 mb-5">Nos Valeurs Élémentaires</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-5 bg-white h-100 rounded-4 shadow-sm border-0" data-aos="zoom-in" data-aos-delay="100">
                    <i class="fas fa-gem fs-1 mb-4" style="color: var(--accent);"></i>
                    <h4 class="fw-black text-uppercase ls-1 h5 mb-3">Qualité Absolue</h4>
                    <p class="small text-muted mb-0">Sélection rigoureuse des matériaux et attention obsessionnelle aux détails.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-5 bg-white h-100 rounded-4 shadow-sm border-0" data-aos="zoom-in" data-aos-delay="200">
                    <i class="fas fa-pencil-ruler fs-1 mb-4" style="color: var(--accent);"></i>
                    <h4 class="fw-black text-uppercase ls-1 h5 mb-3">Authenticité</h4>
                    <p class="small text-muted mb-0">Des créations originales qui respectent l'âme des matériaux naturels.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-5 bg-white h-100 rounded-4 shadow-sm border-0" data-aos="zoom-in" data-aos-delay="300">
                    <i class="fas fa-leaf fs-1 mb-4" style="color: var(--accent);"></i>
                    <h4 class="fw-black text-uppercase ls-1 h5 mb-3">Durabilité</h4>
                    <p class="small text-muted mb-0">Conçu pour durer des générations, loin de la consommation éphémère.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section-py bg-dark text-white text-center">
    <div class="container">
        <h2 class="fw-black text-uppercase ls-1 mb-4">Prêt à transformer votre espace ?</h2>
        <a href="{{ url('/contact') }}" class="btn btn-outline-light rounded-pill px-5 py-3 fw-black text-uppercase ls-1">Contactez Notre Atelier</a>
    </div>
</section>
@endsection
