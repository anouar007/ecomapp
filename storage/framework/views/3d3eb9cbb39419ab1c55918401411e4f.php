<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('meta_title', setting('app_name', 'Speed Platform')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', setting('app_description', 'High performance e-commerce platform.')); ?>">
    <meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', setting('app_name', 'boutique') . ', e-commerce, Maroc, acheter en ligne, livraison Maroc'); ?>">
    <meta name="robots" content="<?php echo $__env->yieldContent('meta_robots', 'index, follow'); ?>">
    <meta name="author" content="<?php echo e(setting('app_name', 'Hijab Princesses')); ?>">
    <meta name="theme-color" content="#D4AF37">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">

    <!-- Preconnect to external resources for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Favicon -->
    <?php if(setting('app_logo')): ?>
        <link rel="icon" href="<?php echo e(asset('storage/' . setting('app_logo'))); ?>" type="image/x-icon">
        <link rel="apple-touch-icon" href="<?php echo e(asset('storage/' . setting('app_logo'))); ?>">
    <?php else: ?>
        <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" type="image/x-icon">
    <?php endif; ?>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $__env->yieldContent('meta_type', 'website'); ?>">
    <meta property="og:site_name" content="<?php echo e(setting('app_name', 'Hijab Princesses')); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('meta_title', setting('app_name', 'Hijab Princesses')); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta_description', setting('app_description', 'أناقة الأميرة — متجر العبايات والخمارات الفاخرة بالمغرب.')); ?>">
    <meta property="og:image" content="<?php echo $__env->yieldContent('meta_image', setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/og-default.jpg')); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="ar_MA">
    <meta property="og:updated_time" content="<?php echo e(now()->toIso8601String()); ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo e(url()->current()); ?>">
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('meta_title', setting('app_name', 'Hijab Princesses')); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('meta_description', setting('app_description', 'أناقة الأميرة — متجر العبايات والخمارات الفاخرة بالمغرب.')); ?>">
    <meta name="twitter:image" content="<?php echo $__env->yieldContent('meta_image', setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/og-default.jpg')); ?>">    
    <meta name="twitter:site" content="<?php echo $__env->yieldContent('twitter_site', '@HijabPrincesses'); ?>">
    
    <!-- JSON-LD Structured Data Schema -->
    <?php echo $__env->yieldContent('json_ld'); ?>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- Dynamic Theme CSS Variables -->
    <style>
        :root {
            --primary: <?php echo e(setting('primary_color', '#000000')); ?>;
            --accent: <?php echo e(setting('accent_color', '#D4AF37')); ?>;
            --accent-hover: <?php echo e(setting('accent_hover_color', '#C5A028')); ?>;
            --accent-light: <?php echo e(setting('accent_light_color', 'rgba(212,175,55,.12)')); ?>;
        }
        /* Mobile Viewport Lock */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* High-Contrast Variant Highlights */
        .variant-option.active[data-color], .pcard-color-dot.active {
            outline: 2px solid var(--accent) !important;
            outline-offset: 3px !important;
            border-color: var(--accent) !important;
        }
        .variant-option.active[data-size], .pcard-size-pill.active {
            background-color: var(--accent) !important;
            color: #fff !important;
            border-color: var(--accent) !important;
            font-weight: 800 !important;
        }
    </style>
    <link rel="stylesheet" href="<?php echo e(asset('css/frontend.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/brand.css')); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <!-- Custom Head Codes -->
    <?php
        $headCodes = \App\Models\CustomCode::where('is_active', true)
            ->where('position', 'head')
            ->orderBy('priority', 'desc')
            ->get();
    ?>
    <?php $__currentLoopData = $headCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($code->type == 'css'): ?>
            <style><?php echo $code->content; ?></style>
        <?php elseif($code->type == 'js'): ?>
            <script><?php echo $code->content; ?></script>
        <?php else: ?>
            <?php echo $code->content; ?>

        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Custom Body Start Codes -->
    <?php
        $bodyStartCodes = \App\Models\CustomCode::where('is_active', true)
            ->where('position', 'body_start')
            ->orderBy('priority', 'desc')
            ->get();
    ?>
    <?php $__currentLoopData = $bodyStartCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $code->content; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



    <!-- ── Main Header ────────────────────────────────────────────── -->
    <header class="main-header shadow-sm">
        <div class="container px-xl-5">
            <div class="d-flex align-items-center justify-content-between py-2 py-lg-3">
                
                <!-- Left: Burger Menu (Mobile) / Nav (Desktop) -->
                <div class="d-flex align-items-center flex-1">
                    <button class="header-action-btn d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-expanded="false" aria-label="Menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <nav class="d-none d-lg-flex align-items-center gap-4">
                        <a href="<?php echo e(route('home')); ?>" class="nav-link-custom text-decoration-none <?php echo e(request()->is('/') ? 'active' : ''); ?>">الرئيسية</a>
                        <a href="<?php echo e(route('shop.index')); ?>" class="nav-link-custom text-decoration-none <?php echo e(request()->is('shop*') ? 'active' : ''); ?>">المتجر</a>
                    </nav>
                </div>

                <!-- Center: Brand Logo -->
                <div class="header-logo-container">
                    <a href="<?php echo e(url('/')); ?>" class="text-decoration-none">
                        <?php if(setting('app_logo')): ?>
                            <img src="<?php echo e(asset('storage/' . setting('app_logo'))); ?>" alt="<?php echo e(setting('app_name')); ?>" class="rounded-circle shadow-sm" style="height: 44px; width: 44px; object-fit: cover; border: 1px solid rgba(0,0,0,0.05);">
                        <?php else: ?>
                            <div class="brand-logo-text">
                                Hijab <span class="gold-part">Princesses</span>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Right: Actions -->
                <div class="d-flex align-items-center justify-content-end gap-1 gap-lg-3 flex-1">
                    <!-- Search Toggle -->
                    <button class="header-action-btn" type="button" data-bs-toggle="collapse" data-bs-target="#searchCollapse">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Account (Universal) -->
                    <a href="<?php echo e(auth()->check() ? route('dashboard') : route('login')); ?>" class="header-action-btn text-decoration-none" title="<?php echo e(auth()->check() ? 'حسابي' : 'تسجيل الدخول'); ?>">
                        <i class="fas fa-user-circle"></i>
                    </a>

                    <!-- Cart -->
                    <div class="position-relative">
                        <button class="header-action-btn position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart">
                            <i class="fas fa-shopping-bag"></i>
                            <span id="header-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem; transform: translate(-35%, 25%);">
                                <?php echo e(count(session('cart', []))); ?>

                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Search -->
            <div class="collapse" id="searchCollapse">
                <div class="pb-3 px-1">
                    <form action="<?php echo e(route('shop.index')); ?>" method="GET" class="w-100" style="max-width: 600px; margin: 0 auto;">
                        <div class="input-group brand-card border-0">
                            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                            <input class="form-control border-0 ps-0 text-muted py-2" type="search" name="q" placeholder="ماذا تبحثين عنه اليوم؟" value="<?php echo e(request('q')); ?>">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Menu Expandable (Navbar Collapse) -->
            <div class="collapse d-lg-none" id="navbarMain">
                <div class="py-3 border-top mt-1 nav-mobile-v2">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 fs-5 fw-bold">
                        <li><a href="<?php echo e(route('home')); ?>" class="text-dark text-decoration-none d-block py-1">الرئيسية</a></li>
                        <li><a href="<?php echo e(route('shop.index')); ?>" class="text-dark text-decoration-none d-block py-1">المتجر</a></li>
                        <?php if(auth()->guard()->check()): ?>
                            <li><a href="<?php echo e(route('dashboard')); ?>" class="text-dark text-decoration-none d-block py-1">حسابي</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo e(route('login')); ?>" class="text-dark text-decoration-none d-block py-1">تسجيل الدخول</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </header>


    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>


    <!-- ── Mini-Cart Drawer ─────────────────────────────────────────── -->
    <style>
    /* Mini-cart drawer */
    #miniCart {
        width: 100vw !important;
        max-width: 420px;
        background: #fafafa;
        border: none;
    }
    .mc-header {
        background: #fff;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f0f0f0;
    }
    .mc-header-title {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 800;
        font-size: 1.05rem;
        color: #1e293b;
    }
    .mc-header-icon {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, #c5a059, #a07840);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 0.9rem;
    }
    .mc-close {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: #f1f5f9;
        border: none;
        display: flex; align-items: center; justify-content: center;
        color: #64748b;
        cursor: pointer;
        transition: background 0.15s;
    }
    .mc-close:hover { background: #e2e8f0; }
    /* Items */
    .mc-items {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        -webkit-overflow-scrolling: touch;
    }
    .mc-item {
        background: #fff;
        border-radius: 1rem;
        padding: 0.875rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
        position: relative;
        box-shadow: 0 1px 6px rgba(0,0,0,0.05);
        transition: box-shadow 0.2s;
    }
    .mc-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
    .mc-item-img {
        width: 72px; height: 88px;
        object-fit: cover;
        border-radius: 0.75rem;
        flex-shrink: 0;
        background: #f1f5f9;
    }
    .mc-item-info { flex: 1; min-width: 0; }
    .mc-item-name {
        font-weight: 700;
        font-size: 0.9rem;
        color: #1e293b;
        line-height: 1.3;
        margin-bottom: 0.35rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        padding-left: 1.5rem;
    }
    .mc-tags {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
    .mc-tag {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 100px;
    }
    .mc-item-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.4rem;
    }
    .mc-price {
        font-weight: 800;
        color: #c5a059;
        font-size: 0.95rem;
    }
    /* Qty stepper */
    .mc-qty {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 100px;
        overflow: hidden;
    }
    .mc-qty-btn {
        width: 30px; height: 30px;
        border: none;
        background: transparent;
        color: #1e293b;
        font-size: 0.75rem;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.15s;
    }
    .mc-qty-btn:hover { background: #e2e8f0; }
    .mc-qty-val {
        width: 28px;
        text-align: center;
        font-weight: 800;
        font-size: 0.85rem;
        color: #1e293b;
        border: none;
        background: transparent;
        pointer-events: none;
    }
    /* Delete — always visible on mobile */
    .mc-delete {
        position: absolute;
        top: 0.75rem;
        left: 0.75rem;
        width: 24px; height: 24px;
        border-radius: 50%;
        background: #fff0f0;
        border: none;
        color: #ef4444;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.65rem;
        cursor: pointer;
        opacity: 0.75;
        transition: opacity 0.15s, background 0.15s;
    }
    .mc-delete:hover { opacity: 1; background: #ffe4e4; }
    /* Footer */
    .mc-footer {
        background: #fff;
        border-top: 1px solid #f0f0f0;
        padding: 1rem 1.25rem;
        padding-bottom: max(1.25rem, env(safe-area-inset-bottom));
    }
    .mc-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .mc-total-label { font-size: 0.8rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
    .mc-total-val { font-size: 1.35rem; font-weight: 900; color: #1e293b; }
    .mc-checkout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        background: linear-gradient(135deg, #c5a059, #a07840);
        color: #fff;
        border: none;
        border-radius: 0.875rem;
        padding: 0.9rem 1.25rem;
        font-weight: 700;
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(197,160,89,0.4);
        transition: transform 0.15s, box-shadow 0.15s;
        margin-bottom: 0.6rem;
    }
    .mc-checkout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(197,160,89,0.45);
        color: #fff;
    }
    .mc-view-btn {
        display: block;
        text-align: center;
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        padding: 0.4rem;
    }
    .mc-view-btn:hover { color: #64748b; }
    /* Free shipping badge */
    .mc-shipping {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
        font-size: 0.75rem;
        font-weight: 700;
        text-align: center;
        padding: 0.4rem 0.75rem;
        border-radius: 0.5rem;
        margin-bottom: 0.875rem;
    }
    /* Empty state */
    .mc-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.5rem;
        text-align: center;
    }
    .mc-empty-icon {
        width: 90px; height: 90px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: #cbd5e1;
    }
    .mc-empty h5 { font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; }
    .mc-empty p { color: #94a3b8; font-size: 0.875rem; margin-bottom: 1.5rem; }
    .mc-shop-btn {
        background: linear-gradient(135deg, #c5a059, #a07840);
        color: #fff;
        border: none;
        border-radius: 100px;
        padding: 0.75rem 2rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }
    </style>

    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="miniCart" aria-labelledby="miniCartLabel">
        <!-- Header -->
        <div class="mc-header">
            <div class="mc-header-title">
                <div class="mc-header-icon"><i class="fas fa-shopping-bag"></i></div>
                <span id="miniCartLabel">سلتي</span>
            </div>
            <button class="mc-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="offcanvas-body p-0 d-flex flex-column" style="height: calc(100% - 65px);">

            <!-- Items -->
            <div class="mc-items" id="mini-cart-items">
                <?php echo $__env->make('frontend.cart.partials.mini-cart-items', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <!-- Footer -->
            <div id="mini-cart-footer">
                <?php echo $__env->make('frontend.cart.partials.mini-cart-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

        </div>
    </div>



    <footer class="footer-modern">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <h5 class="brand-heading text-white mb-4 text-uppercase ls-1" style="font-size: 1.5rem;">Hijab <span class="text-gold">Princesses</span></h5>
                    <p class="small lh-lg mb-4">
                        وجهتكم الفاخرة لكل ما يخص الأناقة المحتشمة في المغرب. عبايات راقية، خمارات متميزة، ومجموعات حصرية — مصممة للأميرة العصرية.
                    </p>
                    <?php
                        $sfb  = setting('social_facebook',  '');
                        $stw  = setting('social_twitter',   '');
                        $sig  = setting('social_instagram', '');
                        $sli  = setting('social_linkedin',  '');
                        $swa  = setting('social_whatsapp',  '');
                        // Only treat as valid if it's a real URL (not empty or bare '#')
                        $validUrl = fn($v) => $v && $v !== '#' && $v !== '/#';
                    ?>
                    <div class="d-flex gap-3 flex-wrap">
                        <?php if($validUrl($sfb)): ?>
                        <a href="<?php echo e($sfb); ?>" target="_blank" rel="noopener" class="footer-social-btn" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        <?php if($validUrl($stw)): ?>
                        <a href="<?php echo e($stw); ?>" target="_blank" rel="noopener" class="footer-social-btn" title="Twitter / X">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        <?php if($validUrl($sig)): ?>
                        <a href="<?php echo e($sig); ?>" target="_blank" rel="noopener" class="footer-social-btn" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                        <?php if($validUrl($sli)): ?>
                        <a href="<?php echo e($sli); ?>" target="_blank" rel="noopener" class="footer-social-btn" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php endif; ?>
                        <?php if($validUrl($swa)): ?>
                        <?php
                            $waFooterLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $swa);
                        ?>
                        <a href="<?php echo e($waFooterLink); ?>" target="_blank" rel="noopener" class="footer-social-btn footer-social-btn--wa" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if(!$validUrl($sfb) && !$validUrl($stw) && !$validUrl($sig) && !$validUrl($sli) && !$validUrl($swa)): ?>
                        <span class="text-muted small fst-italic"><?php echo e(__('Réseaux sociaux bientôt disponibles')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase ls-1">المتجر</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo e(route('shop.index')); ?>" class="footer-link small">جميع المنتجات</a></li>
                        <li><a href="#" class="footer-link small">وصل حديثاً</a></li>
                        <li><a href="#" class="footer-link small">الأكثر مبيعاً</a></li>
                        <li><a href="#" class="footer-link small">تخفيضات</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase ls-1">الدعم والمساعدة</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link small">مركز المساعدة</a></li>
                        <li><a href="#" class="footer-link small">تتبع طلبي</a></li>
                        <li><a href="#" class="footer-link small">سياسة الاسترجاع</a></li>
                        <li><a href="#" class="footer-link small">الضمان</a></li>
                    </ul>
                </div>

            </div>
            
            <hr class="border-secondary opacity-25 my-5">
            
            <div class="row align-items-center">
                <div class="col-md-12 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-center mb-0">&copy; <?php echo e(date('Y')); ?> <?php echo e(setting('app_name', 'Hijab Princesses')); ?>. جميع الحقوق محفوظة.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        <?php if(setting('frontend_enable_animations')): ?>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        <?php endif; ?>

        // Mini Cart Functions
        function updateQty(id, qty) {
            if(qty < 1) {
                removeItem(id);
                return;
            }
            
            fetch('<?php echo e(route('cart.update')); ?>', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id, quantity: qty })
            })
            .then(response => response.json())
            .then(data => {
                // Update both desktop and mobile cart count badges
                const updateCartBadges = (count) => {
                    const el = document.getElementById('header-cart-count');
                    if (el && count !== undefined) el.textContent = count;
                };
                updateCartBadges(data.cartCount);
                // Refresh mini-cart content
                refreshMiniCart();
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '<?php echo e(__('Erreur lors de la mise à jour du panier')); ?>',
                    showConfirmButton: false,
                    timer: 2500
                });
            });
        }

        function removeItem(id) {
            Swal.fire({
                title: '<?php echo e(__('Retirer du panier ?')); ?>',
                text: "<?php echo e(__('Voulez-vous supprimer cet article ?')); ?>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<?php echo e(__('Oui, supprimer !')); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('<?php echo e(route('cart.remove')); ?>', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const el = document.getElementById('header-cart-count');
                        if (el && data.cartCount !== undefined) el.textContent = data.cartCount;
                        
                        // Refresh mini-cart content
                        refreshMiniCart();
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '<?php echo e(__('Article supprimé !')); ?>',
                            showConfirmButton: false,
                            timer: 2000,
                            background: '#1a1a2e',
                            color: '#fff'
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        }

        // Refresh mini-cart content dynamically
        function refreshMiniCart() {
            // Refresh Items
            fetch('<?php echo e(route('cart.mini')); ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const miniCartContainer = document.getElementById('mini-cart-items');
                if(miniCartContainer) miniCartContainer.innerHTML = html;
            })
            .catch(console.error);

            // Refresh Footer (Total & Checkout Button)
            fetch('<?php echo e(route('cart.miniFooter')); ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const miniCartFooter = document.getElementById('mini-cart-footer');
                if(miniCartFooter) {
                    miniCartFooter.innerHTML = html;
                    miniCartFooter.style.display = html.trim() === '' ? 'none' : 'block';
                }
            })
            .catch(console.error);
        }

        // Global Variation Selection for Product Cards
        function selectCardVariant(productId, type, value, el, isSlider = false) {
            const cardId = isSlider ? `slider-${productId}` : productId;
            const cardEl = el.closest(isSlider ? '.product-card' : '.pcard');
            
            // Initialize storage if needed
            if (!window.selectedCardOptions) window.selectedCardOptions = {};
            if (!window.selectedCardOptions[cardId]) window.selectedCardOptions[cardId] = {};
            const selection = window.selectedCardOptions[cardId];

            // Update active class for the specific row
            const row = el.closest('.pcard-variant-row');
            if (el.classList.contains('active')) {
                el.classList.remove('active');
                selection[type] = null;
            } else {
                const siblings = row.querySelectorAll(el.classList.contains('pcard-color-dot') ? '.pcard-color-dot' : '.pcard-size-pill');
                siblings.forEach(s => s.classList.remove('active'));
                el.classList.add('active');
                selection[type] = value;
            }

            // Find matching variant
            const variants = window.cardVariants[productId];
            
            // Check if both are needed
            const hasColor = cardEl.querySelector('.pcard-color-dot');
            const hasSize = cardEl.querySelector('.pcard-size-pill');
            
            let match = null;
            if (hasColor && hasSize) {
                if (selection.color && selection.size) {
                    match = variants.find(v => v.color == selection.color && v.size == selection.size);
                }
            } else if (hasColor) {
                if (selection.color) {
                    match = variants.find(v => v.color == selection.color);
                }
            } else if (hasSize) {
                if (selection.size) {
                    match = variants.find(v => v.size == selection.size);
                }
            }

            // Update UI based on match or lack thereof
            const priceEl = document.getElementById(isSlider ? `pcard-price-slider-${productId}` : `pcard-price-${productId}`);
            const inputId = isSlider ? `card-selected-variant-slider-${productId}` : `card-selected-variant-${productId}`;
            const input = document.getElementById(inputId);

            if (match) {
                if (priceEl) priceEl.innerText = match.formatted_price;
                if (input) input.value = match.id;
            } else {
                if (input) input.value = "";
            }

            // Update availability of other pills in this card
            updateCardAvailability(productId, cardEl, selection);
        }

        function updateCardAvailability(productId, cardEl, selection) {
            const variants = window.cardVariants[productId];
            if (!variants) return;

            // Update Size Pills
            cardEl.querySelectorAll('.pcard-size-pill').forEach(pill => {
                const size = pill.innerText.trim();
                let isAvailable = false;
                if (selection.color) {
                    isAvailable = variants.some(v => v.color == selection.color && v.size == size && v.stock > 0);
                } else {
                    isAvailable = variants.some(v => v.size == size && v.stock > 0);
                }
                pill.classList.toggle('disabled', !isAvailable);
            });

            // Update Color Dots
            cardEl.querySelectorAll('.pcard-color-dot').forEach(dot => {
                const color = dot.title;
                let isAvailable = false;
                if (selection.size) {
                    isAvailable = variants.some(v => v.size == selection.size && v.color == color && v.stock > 0);
                } else {
                    isAvailable = variants.some(v => v.color == color && v.stock > 0);
                }
                dot.classList.toggle('disabled', !isAvailable);
            });
        }

        function addToCart(productId, isSlider = false) {
            const cardId = isSlider ? `slider-${productId}` : productId;
            const inputId = isSlider ? `card-selected-variant-slider-${productId}` : `card-selected-variant-${productId}`;
            const variantInput = document.getElementById(inputId);
            let variantId = (variantInput && variantInput.value) ? variantInput.value : null;

            if (!variantId && window.cardVariants && window.cardVariants[productId] && window.cardVariants[productId].length > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'يرجى اختيار المقاس واللون',
                    text: 'الرجاء اختيار الخيارات المفضلة قبل الإضافة إلى السلة.',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#c5a059'
                });
                return;
            }

            const btn = event ? event.currentTarget : null;
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn && btn.tagName === 'BUTTON') {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch(`<?php echo e(url('/cart/add')); ?>/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    quantity: 1,
                    variant_id: variantId 
                })
            })
            .then(r => r.json())
            .then(data => {
                if (btn && btn.tagName === 'BUTTON') {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'تمت الإضافة إلى السلة!',
                        showConfirmButton: false, timer: 2500, background: '#1a1a2e', color: '#fff'
                    });
                    const badge = document.getElementById('header-cart-count');
                    if (badge && data.cartCount !== undefined) badge.innerText = data.cartCount;
                    if (typeof refreshMiniCart === 'function') refreshMiniCart();
                } else {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'حدث خطأ ما' });
                }
            })
            .catch(err => {
                console.error(err);
                if (btn && btn.tagName === 'BUTTON') {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        }
        // Initialize all product cards availability on load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.pcard').forEach(card => {
                const productId = card.dataset.productId;
                if (!window.selectedCardOptions) window.selectedCardOptions = {};
                if (!window.selectedCardOptions[productId]) window.selectedCardOptions[productId] = {};
                updateCardAvailability(productId, card, window.selectedCardOptions[productId]);
            });
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/layouts/frontend.blade.php ENDPATH**/ ?>