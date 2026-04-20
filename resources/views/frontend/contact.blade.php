@extends('layouts.frontend')

@section('meta_title', 'Contactez Moubdi3oun — Votre Mobilier Sur Mesure')

@section('content')
<!-- Header -->
<div class="bg-light section-py pt-5 pb-4">
    <div class="container text-center">
        <span class="badge-new mb-3 d-inline-block">{{ __('Contact Header Eyebrow') }}</span>
        <h1 class="fw-black text-uppercase ls-1 display-5 mb-0">{{ __('Contact Header Title 1') }} <span style="color: var(--accent);">{{ __('Contact Header Title 2') }}</span></h1>
    </div>
</div>

<section class="section-py bg-white">
    <div class="container">
        <div class="row g-5">
            <!-- Info Sidebar -->
            <div class="col-lg-4" data-aos="fade-right">
                <div class="p-5 bg-dark text-white rounded-5 h-100 shadow-lg">
                    <h3 class="fw-black text-uppercase ls-1 h5 mb-5 border-bottom border-secondary-subtle pb-3">{{ __('Our Coordinates') }}</h3>
                    
                    <div class="d-flex mb-5">
                        <div class="me-4 fs-3 opacity-50"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <span class="d-block text-uppercase fw-bold ls-1 mb-2">{{ __('Moroccan Craftsmanship') }}</span>
                            <p class="mb-0 text-white-50 small">{{ setting('company_address', 'Casablanca, Maroc') }}</p>
                        </div>
                    </div>

                    <div class="d-flex mb-5">
                        <div class="me-4 fs-3 opacity-50"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <span class="d-block text-uppercase fw-bold ls-1 mb-2">{{ __('Phone / WhatsApp') }}</span>
                            <p class="mb-0 text-white-50">{{ setting('company_phone') }}</p>
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('social_whatsapp')) }}" class="btn btn-sm btn-success rounded-pill mt-3 px-3 py-1 fw-bold border-0 shadow" style="background-image: linear-gradient(to right, #25D366, #128C7E);">
                                <i class="fab fa-whatsapp me-2"></i> {{ __('Chat Live') }}
                            </a>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="me-4 fs-3 opacity-50"><i class="fas fa-envelope"></i></div>
                        <div>
                            <span class="d-block text-uppercase fw-bold ls-1 mb-2">Email</span>
                            <p class="mb-0 text-white-50">contact@moubdi3oun.com</p>
                        </div>
                    </div>

                    <div class="mt-5 pt-5 opacity-50 border-top border-secondary-subtle">
                        <h6 class="text-uppercase fw-bold small ls-1 mb-4">{{ __('Follow Our Work') }}</h6>
                        <div class="d-flex gap-4 fs-5">
                            <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-pinterest-p"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8" data-aos="fade-left">
                <div class="p-5 border rounded-5 shadow-sm bg-white h-100">
                    <h3 class="fw-black text-uppercase ls-1 h5 mb-3">{{ __('Send Us Message') }}</h3>
                    <p class="text-muted mb-5">{{ __('Contact Subtitle') }}</p>
                    
                    <form action="#" method="POST" class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">{{ __('Full Name') }}</label>
                            <input type="text" class="form-control py-3 bg-light border-0 rounded-4" placeholder="{{ __('FullName Placeholder') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">{{ __('Email Label') }}</label>
                            <input type="email" class="form-control py-3 bg-light border-0 rounded-4" placeholder="{{ __('Email Placeholder Input') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">{{ __('Phone Label') }}</label>
                            <input type="tel" class="form-control py-3 bg-light border-0 rounded-4" placeholder="{{ __('Phone Placeholder') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">{{ __('Subject Label') }}</label>
                            <select class="form-select py-3 bg-light border-0 rounded-4">
                                <option>{{ __('Subject Option 1') }}</option>
                                <option>{{ __('Subject Option 2') }}</option>
                                <option>{{ __('Subject Option 3') }}</option>
                                <option>{{ __('Subject Option 4') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">{{ __('Your Message') }}</label>
                            <textarea class="form-control bg-light border-0 rounded-4 p-4" rows="5" placeholder="{{ __('Message Placeholder') }}"></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-dark w-100 py-3 rounded-pill fw-black text-uppercase ls-1 shadow mt-3">{{ __('Send Message') }} <i class="fas fa-paper-plane ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="mt-5 pt-5" data-aos="fade-up">
            <div class="rounded-5 overflow-hidden shadow-lg" style="height: 450px;">
                <!-- Placeholder for Google Map Embed -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d106399.76451634676!2d-7.666196232386229!3d33.55353594056262!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xda7cc6518134707%3A0xc3f1737e8c33979!2sSidi%20Ma%C3%A2rouf%2C%20Casablanca!5e0!3m2!1sfr!2sma!4v1711124567890!5m2!1sfr!2sma" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</section>

<!-- Footer Trust Cards -->
<section class="section-py bg-light pb-5 border-top">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                 <div class="d-flex align-items-center justify-content-center gap-3">
                     <i class="fas fa-truck-fade fs-4 opacity-50"></i>
                     <span class="small fw-black text-uppercase ls-1">{{ __('Shipping Morocco') }}</span>
                 </div>
            </div>
            <div class="col-md-4">
                 <div class="d-flex align-items-center justify-content-center gap-3">
                     <i class="fas fa-shield-alt fs-4 opacity-50"></i>
                     <span class="small fw-black text-uppercase ls-1">{{ __('Secure Payment') }}</span>
                 </div>
            </div>
            <div class="col-md-4">
                 <div class="d-flex align-items-center justify-content-center gap-3">
                     <i class="fas fa-gem fs-4 opacity-50"></i>
                     <span class="small fw-black text-uppercase ls-1">{{ __('Trust Card 3 Title') }}</span>
                 </div>
            </div>
        </div>
    </div>
</section>
@endsection
