@extends('layouts.frontend')

@section('meta_title', __('Contact') . ' & Showroom — ' . setting('app_name', 'WINA SHOP') . ' | Casablanca')
@section('meta_description', __('Talk to Our Audiovisual Experts'))

@section('json_ld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact {{ addslashes(setting('app_name', 'WINA SHOP')) }}",
  "url": "{{ route('contact') }}"
}
</script>
@endsection

@section('content')

{{-- HERO --}}
<section class="contact-hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <div class="section-eyebrow-cine justify-content-center mb-3">
                    <span class="tally-dot"></span>
                    <span>{{ strtoupper(__('SHOWROOM & CUSTOMER SERVICE')) }}</span>
                </div>
                <h1 class="contact-hero-title mb-3">{{ __('Talk to Our Audiovisual Experts') }}</h1>
                <p class="contact-hero-sub mx-auto">{{ __('A question about compatibility, a shoot project or a quote request? Our team responds within 2 business hours.') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- CONTACT BODY --}}
<section class="section-py bg-surface">
    <div class="container">
        <div class="row g-5">

            {{-- LEFT: CONTACT CARDS --}}
            <div class="col-lg-5" data-aos="fade-right">
                <div class="d-flex flex-column gap-4">

                    <div class="contact-info-card">
                        <div class="contact-icon-box"><i class="fas fa-location-dot"></i></div>
                        <div>
                            <h5 class="contact-card-title">{{ __('Showroom & Technical Workshop') }}</h5>
                            <p class="contact-card-desc mb-2">
                                {{ setting('company_address', '01 Rue 102, Al Oulfa – Hay Wiam') }}<br>
                                {{ setting('company_city', 'Casablanca') }}, Maroc
                            </p>
                            <span class="contact-badge-sub">{{ __('Hands-on & On-site Tests') }}</span>
                        </div>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-icon-box"><i class="fas fa-phone-volume"></i></div>
                        <div>
                            <h5 class="contact-card-title">{{ __('Phone Support & Quotes') }}</h5>
                            <p class="contact-card-desc mb-2">{{ __('Monday to Saturday: 09:00 – 19:00') }}</p>
                            <a href="tel:{{ setting('company_phone', '+212629035777') }}" class="contact-link-bold">
                                {{ setting('company_phone', '06 29 03 57 77') }}
                            </a>
                        </div>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-icon-box"><i class="fas fa-envelope-open-text"></i></div>
                        <div>
                            <h5 class="contact-card-title">{{ __('Email') }}</h5>
                            <p class="contact-card-desc mb-2">{{ __('Corporate quotes, billing & partnerships') }}</p>
                            <a href="mailto:{{ setting('company_email', 'contact@winashop.com') }}" class="contact-link-bold">
                                {{ setting('company_email', 'contact@winashop.com') }}
                            </a>
                        </div>
                    </div>

                    @php
                        $waNum = setting('social_whatsapp', '+212629035777');
                        $waLink = $waNum ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $waNum) : 'https://wa.me/212629035777';
                        $waText = app()->getLocale() === 'ar'
                            ? urlencode('مرحباً Wina Shop، أريد الاستفسار عن معداتكم.')
                            : urlencode('Bonjour Wina Shop, je souhaite des informations sur vos équipements.');
                    @endphp
                    <div class="contact-wa-card">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="wa-icon-circle"><i class="fab fa-whatsapp"></i></div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1">{{ __('WhatsApp Studio Advisor') }}</h6>
                                    <span class="small text-white-50">{{ __('Instant reply during business hours') }}</span>
                                </div>
                            </div>
                            <a href="{{ $waLink }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-success">
                                {{ __('Chat') }} <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left ms-1' : 'fa-arrow-right ms-1' }}"></i>
                            </a>
                        </div>
                    </div>

                    <div class="contact-legal-box p-3 rounded-4 bg-white border">
                        <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold small text-uppercase ls-1">
                            <i class="fas fa-shield-alt text-brand-red"></i> {{ __('Company & Billing Information') }}
                        </div>
                        <p class="small text-muted mb-0">
                            @if(app()->getLocale() === 'ar')
                                شركة مسجلة في السجل التجاري للدار البيضاء. الفواتير تتضمن رقم الموحد للمقاولة (<strong>ICE</strong>) وعدد التعريف الضريبي (<strong>IF</strong>) والسجل التجاري (<strong>RC</strong>) وتفاصيل ضريبة القيمة المضافة.
                            @else
                                Société enregistrée au Registre du Commerce de Casablanca. Factures avec mention de l'<strong>ICE</strong>, <strong>IF</strong> et <strong>RC</strong> éligibles pour votre comptabilité d'entreprise et crédits de TVA.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- RIGHT: FORM --}}
            <div class="col-lg-7" data-aos="fade-left">
                <div class="contact-form-card">
                    <h3 class="fw-bold font-heading text-dark mb-2">{{ __('Send us a Message') }}</h3>
                    <p class="text-muted mb-4 small">{{ __('Fill in the form below. Our experts will contact you within 2 hours.') }}</p>

                    @if(session('success'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle fs-4 me-3 text-success"></i>
                        <div>
                            <h6 class="fw-bold mb-1">{{ __('Message sent successfully!') }}</h6>
                            <p class="mb-0 small">{{ session('success') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4" role="alert">
                        <ul class="mb-0 small ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" id="contactForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">{{ strtoupper(__('YOUR FULL NAME')) }} <span class="text-brand-red">*</span></label>
                                <input type="text" name="name" class="form-control contact-input" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: كريم الأمراني' : 'ex. Karim El Amrani' }}" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">{{ strtoupper(__('EMAIL ADDRESS')) }} <span class="text-brand-red">*</span></label>
                                <input type="email" name="email" class="form-control contact-input" placeholder="karim@production.ma" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">{{ strtoupper(__('PHONE NUMBER')) }}</label>
                                <input type="tel" name="phone" class="form-control contact-input" placeholder="06 XX XX XX XX" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">{{ strtoupper(__('SUBJECT')) }}</label>
                                <select name="subject" class="form-select contact-input">
                                    <option value="corporate_quote">{{ __('Corporate quote request (with ICE)') }}</option>
                                    <option value="camera_advice">{{ __('Camera or optic selection advice') }}</option>
                                    <option value="showroom_appointment">{{ __('Showroom appointment') }}</option>
                                    <option value="order_tracking">{{ __('Order or delivery tracking') }}</option>
                                    <option value="after_sales">{{ __('After-sales service / Warranty') }}</option>
                                    <option value="other">{{ __('Other request') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">{{ strtoupper(__('YOUR MESSAGE / PROJECT')) }} <span class="text-brand-red">*</span></label>
                                <textarea name="message" rows="5" class="form-control contact-input" placeholder="{{ __('Describe your needs (camera model, targeted optics, shoot dates, delivery constraints)...') }}" required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold w-100 shadow">
                                    <i class="fas fa-paper-plane me-2"></i> {{ __('Send My Request') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="section-py bg-white" id="faq">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-eyebrow-cine"><i class="fas fa-question-circle"></i> {{ __('FAQ') }}</span>
            <h2 class="section-title">{{ __('Frequently Asked Questions') }}</h2>
            <p class="section-desc">{{ __('Everything you need to know before ordering or visiting our showroom') }}</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion custom-faq-accordion" id="faqAccordion">

                    <div class="accordion-item mb-3 border rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-bold font-heading collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false">
                                <i class="fas fa-camera text-brand-red me-3"></i> {{ __('Can we test cameras and lenses at the showroom?') }}
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg pt-0">{{ __('Yes, our Casablanca showroom has a dedicated testing area...') }}</div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button fw-bold font-heading collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false">
                                <i class="fas fa-file-invoice text-brand-red me-3"></i> {{ __('Do you issue official ICE invoices for companies?') }}
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg pt-0">{{ __('Absolutely. All our sales include a legal invoice with ICE, IF, RC...') }}</div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button fw-bold font-heading collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false">
                                <i class="fas fa-truck-fast text-brand-red me-3"></i> {{ __('What are the delivery times and terms across Morocco?') }}
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg pt-0">{{ __('We ship in 24h on the Casablanca-Rabat-Marrakech axis...') }}</div>
                        </div>
                    </div>

                    <div class="accordion-item border rounded-4 overflow-hidden">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button fw-bold font-heading collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false">
                                <i class="fas fa-shield-alt text-brand-red me-3"></i> {{ __('How does the 2-year manufacturer warranty work?') }}
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary lh-lg pt-0">{{ __('As an authorized reseller, our products benefit from the official manufacturer warranty...') }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
