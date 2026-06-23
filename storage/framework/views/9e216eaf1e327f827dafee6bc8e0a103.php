<?php $__env->startSection('meta_title', __('Coop Ait Oumdis — Natural Moroccan Products')); ?>

<?php $__env->startSection('content'); ?>


<section class="hero-section overflow-hidden" 
         style="background: url('<?php echo e(asset('images/hero_flatlay.png')); ?>') no-repeat center center;">
    
    <div class="container position-relative" style="z-index: 3;">
        <div class="row align-items-center">
            
            <div class="col-lg-7 text-white text-start" 
                 style="margin-left: auto !important; margin-right: 0 !important;"
                 data-aos="fade-up" data-aos-duration="1000">
                
                
                <h1 class="fw-900 lh-sm mb-4 text-white font-title" style="font-size: clamp(2rem, 5vw, 3.5rem); text-shadow: 0 4px 15px rgba(0,0,0,0.3); font-family: 'Tajawal', sans-serif;">
                    <?php echo e(__('From the Atlas Mountains...')); ?><br>
                    <span class="text-white"><?php echo e(__('To every home in the world')); ?></span>
                </h1>

                
                <p class="mb-5 lh-lg text-white text-opacity-90" style="font-size: 1.1rem; max-width: 600px; text-shadow: 0 2px 8px rgba(0,0,0,0.2); font-family: 'Tajawal', sans-serif;">
                    <?php echo e(__('Hero Description Text')); ?>

                </p>

                
                <div class="d-flex flex-wrap gap-3 mb-4 justify-content-start">
                    <a href="#catalog" class="hero-cta-btn text-decoration-none d-inline-flex align-items-center gap-2">
                        <?php echo e(__('Acheter maintenant')); ?> <i class="fas <?php echo e(app()->getLocale() == 'ar' ? 'fa-chevron-left' : 'fa-chevron-right'); ?> small"></i>
                    </a>
                </div>

                
                <div class="d-flex flex-wrap align-items-center gap-3 mt-4 justify-content-start" style="font-family: 'Tajawal', sans-serif;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-leaf text-white"></i>
                        <span class="text-white small fw-bold"><?php echo e(__('100% Naturel')); ?></span>
                    </div>
                    <span class="text-white opacity-50">|</span>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-mountain text-white"></i>
                        <span class="text-white small fw-bold"><?php echo e(__('Au cœur de l\'Atlas')); ?></span>
                    </div>
                    <span class="text-white opacity-50">|</span>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-users text-white"></i>
                        <span class="text-white small fw-bold"><?php echo e(__('Coopératives féminines')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="trust-strip-container">
    <div class="container">
        <div class="row g-4 align-items-center justify-content-around text-center">
            <div class="col-md-3 trust-item-box">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fas fa-certificate fs-3"></i>
                    <div class="text-start">
                        <div class="fw-bold text-dark" style="font-size: 0.95rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('Coopérative Agréée')); ?></div>
                        <div class="x-small text-muted" style="font-family: 'Tajawal', sans-serif;"><?php echo e(__('Certifiée par l\'État')); ?></div>
                    </div>
                </div>
            </div>
            <div class="d-none d-md-block" style="width: 1px; background-color: rgba(14, 56, 32, 0.08); height: 40px;"></div>
            <div class="col-md-3 trust-item-box">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fas fa-truck-fast fs-3"></i>
                    <div class="text-start">
                        <div class="fw-bold text-dark" style="font-size: 0.95rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('Livraison Express')); ?></div>
                        <div class="x-small text-muted" style="font-family: 'Tajawal', sans-serif;"><?php echo e(__('Partout au Maroc')); ?></div>
                    </div>
                </div>
            </div>
            <div class="d-none d-md-block" style="width: 1px; background-color: rgba(14, 56, 32, 0.08); height: 40px;"></div>
            <div class="col-md-3 trust-item-box">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fas fa-lock fs-3"></i>
                    <div class="text-start">
                        <div class="fw-bold text-dark" style="font-size: 0.95rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('Paiement Sécurisé')); ?></div>
                        <div class="x-small text-muted" style="font-family: 'Tajawal', sans-serif;"><?php echo e(__('à la Livraison')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5" id="catalog" style="background: #fbfbfb; position: relative;">
    <div id="catalog-loader" class="position-absolute inset-0 d-none align-items-center justify-content-center" style="inset:0; background:rgba(255,255,255,.8); backdrop-filter:blur(4px); z-index:10;">
        <div class="spinner-border text-green"></div>
    </div>
    
    <div class="container py-3">
        <div class="row g-4 lg-g-5">
            
            <div class="col-lg-8" data-aos="fade-up">
                
                
                <div class="d-flex align-items-center justify-content-start mb-4">
                    <a href="<?php echo e(route('shop.index')); ?>" class="text-decoration-none text-dark fw-bold d-flex align-items-center gap-2" style="font-family: 'Tajawal', sans-serif; font-size: 1.15rem;">
                        <span><?php echo e(__('Voir tout le catalogue')); ?></span>
                        <i class="fas <?php echo e(app()->getLocale() == 'ar' ? 'fa-chevron-left' : 'fa-chevron-right'); ?> small" style="font-size: 0.8rem; margin-top: 3px;"></i>
                    </a>
                </div>

                
                <div id="catalog-container">
                    <?php echo $__env->make('frontend.partials.catalog-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                
                <div class="text-center mt-5 <?php echo e($hasMore ? '' : 'd-none'); ?>" id="load-more-container">
                    <button id="load-more-btn" class="btn-brand px-5 py-2.5 rounded-pill shadow-sm text-white border-0" style="background: #0e3820; font-size: 0.95rem; font-family: 'Tajawal', sans-serif;">
                        <?php echo e(__('Charger Plus')); ?> <i class="fas fa-plus small ms-1"></i>
                    </button>
                </div>
            </div>

            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="sticky-sidebar" style="position: sticky; top: 100px;">
                    
                    
                    <div class="story-card rounded-4 mb-4 overflow-hidden shadow-sm">
                        <div class="p-4 p-lg-4 text-center position-relative">
                            <h4 class="fw-bold text-center text-dark mb-4" style="font-family: 'Tajawal', sans-serif; font-size: 1.4rem;"><?php echo e(__('Notre Histoire')); ?></h4>
                            <p class="text-muted text-center lh-lg mb-4" style="font-family: 'Tajawal', sans-serif; font-size: 0.95rem; color: #666 !important;">
                                <?php echo e(__('Our Story Description')); ?>

                            </p>
                            <a href="#" class="btn px-4 py-2 text-white rounded-pill mb-2">
                                <?php echo e(__('En savoir plus')); ?>

                            </a>
                        </div>
                        <div class="cooperative-image-wrapper">
                            <img src="<?php echo e(asset('images/cooperative_story.png')); ?>" alt="Coop Story" class="w-100 h-auto object-fit-cover" style="display: block;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-white border-top border-light" id="how">
    <div class="container py-3">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label mx-auto" style="background: #fcf8f2; color: #bf8b43;"><?php echo e(__('Simple Process')); ?></div>
            <h2 class="section-title fw-bold mt-2" style="font-size: 1.85rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('How It Works Title')); ?></h2>
            <div class="section-divider mx-auto" style="background: #bf8b43;"></div>
        </div>
        <div class="row g-4 how-it-works-row">
            <?php $__currentLoopData = [
                ['1','fas fa-search','#fcf8f2','#bf8b43',__('how_step_1_title'),__('how_step_1_desc')],
                ['2','fas fa-cart-plus','#fcf8f2','#bf8b43',__('how_step_2_title'),__('how_step_2_desc')],
                ['3','fas fa-phone','#fcf8f2','#bf8b43',__('how_step_3_title'),__('how_step_3_desc')],
                ['4','fas fa-home','#fcf8f2','#bf8b43',__('how_step_4_title'),__('how_step_4_desc')],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo e($i * 100); ?>">
                <div class="step-item-card h-100">
                    <div class="step-number"><?php echo e($s[0]); ?></div>
                    <div class="step-icon-container mb-4" style="background:<?php echo e($s[2]); ?>;">
                        <i class="fas <?php echo e($s[1]); ?>" style="color:<?php echo e($s[3]); ?>; font-size:1.2rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.95rem;"><?php echo e($s[4]); ?></h6>
                    <p class="small text-muted mb-0 lh-lg" style="font-size: 0.82rem;"><?php echo e($s[5]); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-5" style="background:#fcfcfc;">
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5" data-aos="fade-up">
                    <div class="section-label mx-auto"><?php echo e(__('FAQ Label')); ?></div>
                    <h2 class="section-title fw-bold mt-2" style="font-size: 1.85rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('FAQ Title')); ?></h2>
                    <div class="section-divider mx-auto"></div>
                </div>
                <div class="accordion accordion-flush" id="faqAccordion">
                    <?php $__currentLoopData = [
                        [__('faq_1_question'),__('faq_1_answer')],
                        [__('faq_2_question'),__('faq_2_answer')],
                        [__('faq_3_question'),__('faq_3_answer')],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-3" data-aos="fade-up" data-aos-delay="<?php echo e($i * 60); ?>">
                        <div class="faq-accordion-item">
                            <button class="faq-accordion-btn <?php echo e($i > 0 ? 'collapsed' : ''); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo e($i); ?>">
                                <span class="d-flex align-items-center">
                                    <span class="me-3 text-green fw-900"><?php echo e(str_pad($i+1,'2','0',STR_PAD_LEFT)); ?>.</span>
                                    <span><?php echo e($faq[0]); ?></span>
                                </span>
                                <span class="faq-icon"><i class="fas fa-plus"></i></span>
                            </button>
                            <div id="faq<?php echo e($i); ?>" class="accordion-collapse collapse <?php echo e($i === 0 ? 'show' : ''); ?>" data-bs-parent="#faqAccordion">
                                <div class="faq-body"><?php echo e($faq[1]); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-10">
                <div class="support-banner-card text-center" data-aos="zoom-in">
                    <div class="mb-3 text-green" style="font-size:2.8rem;">
                        <i class="fas fa-comment-dots text-gold"></i>
                    </div>
                    <h3 class="fw-bold mb-3" style="font-family: 'Tajawal', sans-serif;"><?php echo e(__('Still have questions?')); ?></h3>
                    <p class="mb-4 mx-auto" style="max-width:480px; font-size: 0.92rem;">
                        <?php echo e(__('Support Description')); ?>

                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="https://wa.me/<?php echo e(str_replace(['+','  ',' '],'',(setting('app_phone','212600000000')))); ?>" target="_blank" class="btn-brand wa-pulse-btn text-decoration-none">
                            <i class="fab fa-whatsapp"></i> <?php echo e(__('Chat on WhatsApp')); ?>

                        </a>
                        <a href="tel:<?php echo e(setting('app_phone','+212600000000')); ?>" class="btn-brand phone-outline-btn text-decoration-none">
                            <i class="fas fa-phone"></i> <?php echo e(__('Call Us')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5" style="background: #fff; border-top: 1px solid #f1f5f9;">
    <div class="container py-2">
        <div class="row g-4 justify-content-center text-center">
            <div class="col-6 col-md-3 cert-col">
                <div class="d-flex flex-column align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-seedling text-gold" style="font-size: 2rem;"></i>
                    </span>
                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('Produits 100% Naturels')); ?></h6>
                </div>
            </div>
            <div class="col-6 col-md-3 cert-col">
                <div class="d-flex flex-column align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-flask-vial text-gold" style="font-size: 2rem;"></i>
                    </span>
                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('Sans Conservateurs')); ?></h6>
                </div>
            </div>
            <div class="col-6 col-md-3 cert-col">
                <div class="d-flex flex-column align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-hands-holding-child text-gold" style="font-size: 2rem;"></i>
                    </span>
                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('Fait à la Main')); ?></h6>
                </div>
            </div>
            <div class="col-6 col-md-3 cert-col">
                <div class="d-flex flex-column align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-users-viewfinder text-gold" style="font-size: 2rem;"></i>
                    </span>
                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem; font-family: 'Tajawal', sans-serif;"><?php echo e(__('Soutien aux Coopératives')); ?></h6>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentPage = 1;

document.getElementById('load-more-btn')?.addEventListener('click', () => { 
    currentPage++; 
    fetchProducts(); 
});

function fetchProducts() {
    const loader = document.getElementById('catalog-loader');
    const btn = document.getElementById('load-more-btn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; 
    btn.disabled = true; 

    const params = new URLSearchParams({ page: currentPage });
    fetch(`${location.origin}${location.pathname}?${params}`, { headers: {'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json()).then(data => {
        const container = document.getElementById('catalog-container');
        const tmp = document.createElement('div'); 
        tmp.innerHTML = data.html;
        const grid = document.getElementById('products-grid');
        if(grid) {
            tmp.querySelectorAll('#products-grid > div').forEach(el => grid.appendChild(el));
        }
        
        const lmc = document.getElementById('load-more-container');
        if(data.hasMore) { 
            lmc.classList.remove('d-none'); 
            btn.innerHTML = '<?php echo e(__("Charger Plus")); ?> <i class="fas fa-plus small ms-1"></i>'; 
            btn.disabled = false; 
        } else {
            lmc.classList.add('d-none');
        }
        if(window.AOS) AOS.refreshHard();
    }).finally(() => { 
        loader.classList.add('d-none'); 
        loader.classList.remove('d-flex'); 
    });
}
</script>

<style>
.hover-translate-y:hover { transform: translateY(-5px); }
.transition-all { transition: all .35s ease; }
.fw-800 { font-weight:800; }
.fw-900 { font-weight:900; }
.inset-0 { inset:0; }

.hover-scale:hover {
    transform: scale(1.04);
}
.active-pill {
    background-color: #0e3820 !important;
    border-color: #0e3820 !important;
    color: #fff !important;
}

.scrollbar-hidden::-webkit-scrollbar {
    display: none;
}
.scrollbar-hidden {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
.text-gold {
    color: #bf8b43 !important;
}
.bg-green-light {
    background-color: #f0faf5 !important;
}
.text-green {
    color: #0e3820 !important;
}
.bg-green {
    background-color: #0e3820 !important;
}

/* ── Hero Section Styles ── */
.hero-section {
    min-height: 80vh;
    background-size: cover !important;
    background-position: center center !important;
    position: relative;
    display: flex;
    align-items: center;
    padding: 120px 0 140px 0;
}
.hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(14, 56, 32, 0.15) 0%, rgba(14, 56, 32, 0.8) 55%, rgba(14, 56, 32, 0.96) 100%);
    z-index: 1;
}
[dir="rtl"] .hero-section::before {
    background: linear-gradient(270deg, rgba(14, 56, 32, 0.15) 0%, rgba(14, 56, 32, 0.8) 55%, rgba(14, 56, 32, 0.96) 100%);
}
.hero-cta-btn {
    background: #bf8b43;
    color: #fff !important;
    font-weight: 700;
    font-family: 'Tajawal', sans-serif;
    padding: 14px 34px;
    border-radius: 100px;
    border: none;
    box-shadow: 0 8px 24px rgba(191, 139, 67, 0.35);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 0.95rem;
    position: relative;
    z-index: 2;
}
.hero-cta-btn:hover {
    background: #a97532;
    transform: translateY(-3px);
    box-shadow: 0 16px 32px rgba(191, 139, 67, 0.45);
    color: #fff !important;
}
.hero-cta-btn i {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.hero-cta-btn:hover i {
    transform: translateX(4px);
}
[dir="rtl"] .hero-cta-btn:hover i {
    transform: translateX(-4px);
}

/* ── Trust Strip Styles ── */
.trust-strip-container {
    background: rgba(255, 255, 255, 0.85) !important;
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 1px solid rgba(14, 56, 32, 0.08);
    box-shadow: 0 20px 40px rgba(14, 56, 32, 0.05);
    border-radius: 20px;
    margin-top: -50px;
    z-index: 10;
    position: relative;
    padding: 24px 15px !important;
    max-width: 1140px;
    margin-left: auto;
    margin-right: auto;
}
.trust-item-box {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.trust-item-box:hover {
    transform: translateY(-5px);
}
.trust-item-box i {
    color: #bf8b43 !important;
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.trust-item-box:hover i {
    transform: scale(1.2) rotate(8deg);
}
.trust-item-box .fw-bold {
    transition: color 0.3s ease;
}
.trust-item-box:hover .fw-bold {
    color: #0e3820 !important;
}

/* ── Story Card Styles ── */
.story-card {
    background: #FDFBF7 !important;
    border: 1px solid rgba(191, 139, 67, 0.15) !important;
    box-shadow: 0 15px 35px rgba(14, 56, 32, 0.04) !important;
    position: relative;
    transition: all 0.4s ease;
}
.story-card::before {
    content: '';
    position: absolute;
    top: 12px;
    bottom: 12px;
    left: 12px;
    right: 12px;
    border: 1px dashed rgba(191, 139, 67, 0.3);
    border-radius: 12px;
    pointer-events: none;
    z-index: 1;
}
.story-card .p-4 {
    position: relative;
    z-index: 2;
}
.story-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(14, 56, 32, 0.08) !important;
    border-color: rgba(191, 139, 67, 0.3) !important;
}
.story-card .btn {
    background: #0e3820;
    color: #fff !important;
    border: 1px solid #bf8b43 !important;
    box-shadow: 0 4px 12px rgba(14, 56, 32, 0.2);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    font-weight: 700;
}
.story-card .btn:hover {
    background: #bf8b43;
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(191, 139, 67, 0.3);
}

/* ── How It Works Process Styles ── */
@media (min-width: 992px) {
    .how-it-works-row {
        position: relative;
    }
    .how-it-works-row::before {
        content: '';
        position: absolute;
        top: 0%;
        left: 12%;
        right: 12%;
        height: 2px;
        border-top: 2px dashed rgba(191, 139, 67, 0.25);
        z-index: 1;
    }
}
.step-item-card {
    background: #fff;
    border-radius: 18px !important;
    padding: 30px 24px !important;
    border: 1px solid #f1f5f9 !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02) !important;
    position: relative;
    z-index: 2;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.step-item-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(191, 139, 67, 0.08) !important;
    border-color: rgba(191, 139, 67, 0.3) !important;
}
.step-number {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 3rem;
    font-weight: 900;
    color: #bf8b43;
    opacity: 0.08;
    transition: all 0.4s ease;
}
[dir="rtl"] .step-number {
    right: auto;
    left: 20px;
}
.step-item-card:hover .step-number {
    opacity: 0.18;
    transform: scale(1.1);
}
.step-icon-container {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.step-item-card:hover .step-icon-container {
    background-color: #0e3820 !important;
}
.step-item-card:hover .step-icon-container i {
    color: #fff !important;
    transform: scale(1.1) rotate(5deg);
}

/* ── FAQ Accordion Styles ── */
.faq-accordion-item {
    background: #fff !important;
    border-radius: 14px !important;
    border: 1px solid rgba(0, 0, 0, 0.03) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01) !important;
    transition: all 0.3s ease;
    overflow: hidden;
}
.faq-accordion-item:hover {
    box-shadow: 0 10px 30px rgba(14, 56, 32, 0.04) !important;
    border-color: rgba(14, 56, 32, 0.05) !important;
}
.faq-accordion-btn {
    font-weight: 700 !important;
    color: #1F2937 !important;
    background: #fff !important;
    border: none !important;
    padding: 20px 24px !important;
    font-size: 1rem !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    transition: all 0.3s ease;
}
.faq-accordion-btn::after {
    display: none !important;
}
.faq-icon {
    font-size: 0.9rem;
    color: #bf8b43;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    display: inline-block;
}
.faq-accordion-btn:not(.collapsed) .faq-icon {
    transform: rotate(135deg);
}
.faq-body {
    padding: 0 24px 20px 24px !important;
    color: #4B5563 !important;
    line-height: 1.8;
}

/* ── Support Card Styles ── */
.support-banner-card {
    background: radial-gradient(circle at top right, rgba(191, 139, 67, 0.15), transparent 60%), 
                radial-gradient(circle at bottom left, rgba(191, 139, 67, 0.08), transparent 50%),
                #0e3820 !important;
    border: 1px solid rgba(191, 139, 67, 0.2) !important;
    color: #fff !important;
    box-shadow: 0 20px 50px rgba(14, 56, 32, 0.2) !important;
    border-radius: 28px !important;
    padding: 55px 40px !important;
}
.support-banner-card h3 {
    color: #fff !important;
}
.support-banner-card p {
    color: rgba(255, 255, 255, 0.8) !important;
}
.wa-pulse-btn {
    background: #25d366 !important;
    color: #fff !important;
    position: relative;
    overflow: visible;
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.35) !important;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 30px;
    border-radius: 100px;
    font-weight: 700;
}
.wa-pulse-btn::before {
    content: '';
    position: absolute;
    inset: -6px;
    border: 2px solid #25d366;
    border-radius: 100px;
    animation: waPulse 1.8s infinite;
    opacity: 0;
}
@keyframes waPulse {
    0% { transform: scale(0.95); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.5; }
    100% { transform: scale(1.2); opacity: 0; }
}
.phone-outline-btn {
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
    color: #fff !important;
    transition: all 0.3s ease !important;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 28px;
    border-radius: 100px;
    font-weight: 700;
}
.phone-outline-btn:hover {
    border-color: #bf8b43 !important;
    background: rgba(255, 255, 255, 0.05) !important;
}

/* ── Certification Styles ── */
.cert-col {
    transition: all 0.3s ease;
}
.cert-col i {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.cert-col:hover {
    transform: translateY(-3px);
}
.cert-col:hover i {
    transform: scale(1.2) rotate(12deg);
    color: #0e3820 !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/home.blade.php ENDPATH**/ ?>