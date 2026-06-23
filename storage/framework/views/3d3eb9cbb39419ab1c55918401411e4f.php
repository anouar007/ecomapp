<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('meta_title', setting_trans('app_name', 'Coop Ait Oumdis')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', setting_trans('app_description', 'Natural Products Cooperative')); ?>">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Favicon -->
    <?php if(setting('app_logo')): ?>
        <link rel="icon" type="image/png" href="<?php echo e(Storage::url(setting('app_logo'))); ?>">
        <link rel="apple-touch-icon" href="<?php echo e(Storage::url(setting('app_logo'))); ?>">
    <?php endif; ?>

    <!-- SEO / AEO / GEO -->
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">
    <meta name="author" content="<?php echo e(setting('app_name', 'Coop Ait Oumdis')); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('meta_title', setting_trans('app_name', 'Coop Ait Oumdis')); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta_description', setting_trans('app_description', 'Natural Products Cooperative')); ?>">
    <meta property="og:image" content="<?php echo $__env->yieldContent('meta_image', setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/og-image.jpg')); ?>">
    <meta property="og:site_name" content="<?php echo e(setting('app_name', 'Coop Ait Oumdis')); ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo e(url()->current()); ?>">
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('meta_title', setting_trans('app_name', 'Coop Ait Oumdis')); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('meta_description', setting_trans('app_description', 'Natural Products Cooperative')); ?>">
    <meta name="twitter:image" content="<?php echo $__env->yieldContent('meta_image', setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/og-image.jpg')); ?>">

    <!-- JSON-LD Structured Data (AEO/GEO Optimization) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "<?php echo e(setting('app_name', 'Coop Ait Oumdis')); ?>",
      "url": "<?php echo e(url('/')); ?>",
      "logo": "<?php echo e(setting('app_logo') ? url(Storage::url(setting('app_logo'))) : ''); ?>",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?php echo e(setting('app_phone', '')); ?>",
        "contactType": "customer service",
        "email": "<?php echo e(setting('app_email', '')); ?>"
      },
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Azilal",
        "addressCountry": "MA"
      },
      "sameAs": [
        "<?php echo e(setting('social_facebook', '#')); ?>",
        "<?php echo e(setting('social_instagram', '#')); ?>",
        "<?php echo e(setting('social_twitter', '#')); ?>"
      ]
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "url": "<?php echo e(url('/')); ?>",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo e(url('/shop?search={search_term_string}')); ?>",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <?php if(app()->getLocale() == 'ar'): ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    
    <link rel="stylesheet" href="<?php echo e(asset('css/frontend.css')); ?>?v=<?php echo e(filemtime(public_path('css/frontend.css'))); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/brand.css')); ?>?v=<?php echo e(filemtime(public_path('css/brand.css'))); ?>">
    
    <?php echo $__env->yieldPushContent('styles'); ?>

    <style>
        :root {
            --green: #0e3820;
            --green-dark: #0a2d19;
            --green-light: #f0faf5;
            --primary: #0e3820;
            --accent: #bf8b43;
            --gold: #bf8b43;
            --gold-light: #fcf8f2;
            --brand-primary: #0e3820;
            --brand-primary-dark: #0a2d19;
        }
        
        /* ── Global Resets ── */
        * { box-sizing: border-box; }
        html, body { overflow-x: hidden; width: 100%; position: relative; scroll-behavior: smooth; }
        body { font-family: 'Tajawal', sans-serif; background: #fff; color: #1F2937; -webkit-font-smoothing: antialiased; }
        
        .text-green { color: #0e3820 !important; }
        .text-gold  { color: #bf8b43 !important; }
        .bg-green   { background-color: #0e3820 !important; }
        .bg-green-light { background-color: #f0faf5 !important; }
        .bg-brand-primary { background-color: #0e3820 !important; }
        .border-green { border-color: #0e3820 !important; }
        .x-small { font-size: 0.75rem; }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 700 !important;
            font-size: 0.88rem !important;
            padding: 8px 4px !important;
            position: relative;
            transition: color 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-link-custom:hover, .nav-link-custom.active {
            color: #bf8b43 !important;
        }
        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #bf8b43;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            transform: translateX(-50%);
        }
        .nav-link-custom.active::after, .nav-link-custom:hover::after {
            width: 80%;
        }

        /* ── Header ── */
        .main-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #0e3820 !important;
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .main-header.scrolled { 
            box-shadow: 0 10px 30px rgba(14, 56, 32, 0.15); 
            background: rgba(14, 56, 32, 0.95) !important;
            border-bottom-color: rgba(255, 255, 255, 0.05) !important;
        }

        /* ── Modern Premium Search Container ── */
        .header-search-container {
            width: 170px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 100px;
            padding: 5px 14px;
            display: flex;
            align-items: center;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .header-search-container:focus-within {
            width: 230px;
            background: rgba(255, 255, 255, 0.12);
            border-color: #bf8b43 !important;
            box-shadow: 0 0 14px rgba(191, 139, 67, 0.3);
        }
        .header-search-input {
            outline: none;
            background: transparent;
            border: 0;
            color: #fff;
            font-size: 0.8rem;
            width: 100%;
        }
        .header-search-input::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }

        /* ── Dropdown Upgrades ── */
        .dropdown-menu {
            border-radius: 14px !important;
            padding: 8px !important;
            box-shadow: 0 15px 40px rgba(14, 56, 32, 0.1) !important;
            border: 1px solid rgba(14, 56, 32, 0.05) !important;
            background: #fff;
            transform: translateY(10px);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dropdown-item {
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-weight: 700;
            color: #4B5563;
            transition: all 0.2s ease;
        }
        .dropdown-item:hover {
            background-color: #f0faf5 !important;
            color: #0e3820 !important;
        }

        /* ── Cart Badge Animation ── */
        @keyframes cartPulse {
            0% { transform: translate(-60%, 20%) scale(1); }
            50% { transform: translate(-60%, 20%) scale(1.4); box-shadow: 0 0 15px #bf8b43; }
            100% { transform: translate(-60%, 20%) scale(1); }
        }
        .cart-badge-pulse {
            animation: cartPulse 0.45s ease-out;
        }

        /* ── Buttons ── */
        .btn-brand {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 13px 28px; border-radius: 100px; font-weight: 700;
            font-family: 'Tajawal', sans-serif; border: none; cursor: pointer;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-decoration: none;
        }
        .btn-brand-primary {
            background: #3BB878; color: #fff !important;
            box-shadow: 0 8px 24px rgba(59,184,120,0.25);
        }
        .btn-brand-primary:hover {
            background: #2f9461; transform: translateY(-3px);
            box-shadow: 0 16px 32px rgba(59,184,120,0.35); color: #fff !important;
        }
        .btn-brand-outline {
            background: transparent; border: 2px solid #3BB878; color: #3BB878 !important;
        }
        .btn-brand-outline:hover {
            background: #3BB878; color: #fff !important; transform: translateY(-3px);
        }
        .btn-brand-white {
            background: #fff; color: #1F2937 !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .btn-brand-white:hover {
            background: #f9fafb; transform: translateY(-3px);
        }

        /* ── Section Utilities ── */
        .section-label {
            display: inline-flex; align-items: center; gap: 8px;
            background: #e8f7ef; color: #3BB878; border-radius: 100px;
            padding: 6px 16px; font-size: 0.82rem; font-weight: 700;
            letter-spacing: 0.5px; margin-bottom: 16px;
        }
        .section-label::before {
            content: ''; display: block; width: 6px; height: 6px;
            background: #3BB878; border-radius: 50%;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
        }
        .section-title {
            font-size: clamp(1.8rem, 4vw, 2.5rem); font-weight: 800;
            line-height: 1.15; color: #1F2937; margin-bottom: 16px;
        }
        .section-divider {
            width: 50px; height: 4px; border-radius: 2px;
            background: linear-gradient(135deg, #3BB878, #2f9461); margin: 0 auto;
        }

        /* ── Product Cards ── */
        .product-card {
            background: #fff; border-radius: 20px; overflow: hidden;
            border: 1px solid #f1f5f9;
            transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 20px 50px rgba(59,184,120,0.12); border-color: #c6f0d8; }
        .product-image-wrapper { position: relative; padding-top: 120%; overflow: hidden; }
        .product-image-wrapper img {
            position: absolute; inset: 0; width: 100%; height: 100%;
            object-fit: cover; transition: transform 0.7s cubic-bezier(0.4,0,0.2,1);
        }
        .product-card:hover .product-image-wrapper img { transform: scale(1.08); }
        .product-card-overlay {
            position: absolute; inset: 0; background: rgba(59,184,120,0.12);
            backdrop-filter: blur(3px); opacity: 0;
            transition: opacity 0.35s ease;
            display: flex; align-items: center; justify-content: center;
        }
        .product-card:hover .product-card-overlay { opacity: 1; }
        .btn-add-to-cart {
            width: 44px; height: 44px; border-radius: 50%;
            background: #3BB878; color: #fff; border: none;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 12px rgba(59,184,120,0.35);
        }
        .btn-add-to-cart:hover { transform: scale(1.15) rotate(90deg); background: #2f9461; }

        /* ── Animations ── */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
        @keyframes shimmer { from { transform: translateX(-100%); } to { transform: translateX(200%); } }
        .float-anim { animation: float 4s ease-in-out infinite; }

        /* ── Mini-Cart ── */
        .mc-qty-btn {
            width: 28px; height: 28px; border: none; background: #f1f5f9;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: background 0.2s;
        }
        .mc-qty-btn:hover { background: #e2e8f0; }

        /* ── Footer ── */
        .footer-main { background: #0F1F16; color: rgba(255,255,255,0.75); }
        .footer-link { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.9rem; transition: color 0.25s ease; }
        .footer-link:hover { color: #bf8b43; }
        .footer-social { width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.6); display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
        .footer-social:hover { background: #bf8b43; color: #fff; transform: translateY(-4px) scale(1.05); }

        /* ── Scroll to Top ── */
        #scrollTopBtn {
            position: fixed; bottom: 30px; right: 30px; z-index: 1100;
            width: 54px; height: 54px; border-radius: 16px;
            background: linear-gradient(135deg, #0e3820, #bf8b43); color: #fff; border: 2px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 30px rgba(14, 56, 32, 0.3);
            opacity: 0; transform: translateY(30px) scale(0.8);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); cursor: pointer;
            visibility: hidden;
        }
        #scrollTopBtn.visible { opacity: 1; transform: translateY(0) scale(1); visibility: visible; }
        #scrollTopBtn:hover { 
            transform: translateY(-8px) scale(1.06); 
            box-shadow: 0 15px 40px rgba(191, 139, 67, 0.45); 
        }
        #scrollTopBtn i { font-size: 1.2rem; transition: transform 0.3s; }
        #scrollTopBtn:hover i { transform: translateY(-4px); }

        @keyframes btnPulse {
            0% { box-shadow: 0 0 0 0 rgba(191, 139, 67, 0.45); }
            70% { box-shadow: 0 0 0 15px rgba(191, 139, 67, 0); }
            100% { box-shadow: 0 0 0 0 rgba(191, 139, 67, 0); }
        }
        #scrollTopBtn.visible { animation: btnPulse 2s infinite; }

        @media(max-width: 991px) {
            #scrollTopBtn { bottom: 85px; right: 20px; width: 44px; height: 44px; }
        }

        /* ── Responsive ── */
        @media(max-width: 767px) { 
            .section-title { font-size: 1.6rem; } 
            .main-header .fs-4 { font-size: 1.15rem !important; }
            .main-header .container { padding-left: 12px; padding-right: 12px; }
        }
        @media(max-width: 380px) {
            .main-header .fs-4 { font-size: 1rem !important; }
            .gap-2 { gap: 0.4rem !important; }
        }

        /* ── RTL Fixes ── */
        [dir="rtl"] {
            text-align: right;
            direction: rtl;
        }
        
        /* RTL Helpers for LTR Bootstrap */
        [dir="rtl"] .text-start { text-align: right !important; }
        [dir="rtl"] .text-end { text-align: left !important; }
        [dir="rtl"] .ms-auto { margin-right: auto !important; margin-left: 0 !important; }
        [dir="rtl"] .me-auto { margin-left: auto !important; margin-right: 0 !important; }
        [dir="rtl"] .me-3 { margin-right: 0 !important; margin-left: 1rem !important; }
        [dir="rtl"] .ms-3 { margin-left: 0 !important; margin-right: 1rem !important; }
        [dir="rtl"] .me-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
        [dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
        [dir="rtl"] .me-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
        [dir="rtl"] .ms-2 { margin-left: 0 !important; margin-right: 0.5rem !important; }
        [dir="rtl"] .ps-5 { padding-right: 3rem !important; padding-left: 0.75rem !important; }
        [dir="rtl"] .pe-4 { padding-left: 1.5rem !important; padding-right: 0.75rem !important; }
        [dir="rtl"] .accordion-button::after { margin-right: auto; margin-left: 0; transform: rotate(180deg); }
        [dir="rtl"] .accordion-button:not(.collapsed)::after { transform: rotate(0deg); }
        [dir="rtl"] .accordion-button { text-align: right !important; }
        [dir="rtl"] .position-relative > .position-absolute[style*="left:14px"] { left: auto !important; right: 14px !important; }
        [dir="rtl"] .fa-arrow-right { transform: rotate(180deg); display: inline-block; }
        [dir="rtl"] .fa-arrow-left { transform: rotate(180deg); display: inline-block; }
        
        [dir="rtl"] .pe-5 { padding-left: 3rem !important; padding-right: 0.75rem !important; }
        
        [dir="rtl"] .dropdown-menu-end { left: 0 !important; right: auto !important; }
        
        [dir="rtl"] .end-0 { right: auto !important; left: 0 !important; }
        [dir="rtl"] .start-0 { left: auto !important; right: 0 !important; }
        [dir="rtl"] .me-1, [dir="rtl"] .me-2, [dir="rtl"] .me-3, [dir="rtl"] .me-4, [dir="rtl"] .me-5 { margin-right: 0 !important; }
        [dir="rtl"] .ms-1, [dir="rtl"] .ms-2, [dir="rtl"] .ms-3, [dir="rtl"] .ms-4, [dir="rtl"] .ms-5 { margin-left: 0 !important; }
        
        /* Stop AOS from causing horizontal scroll */
        [data-aos] { pointer-events: none; }
        .aos-animate { pointer-events: auto; }
        
        /* Prevent overflow from rows */
        .row { --bs-gutter-x: 1.5rem; }
        .container { overflow: visible; }
        main { overflow-x: hidden; width: 100%; position: relative; }
        
        /* Header Squashing Fix */
        .main-header .container > div { flex-wrap: nowrap; }
        .main-header .logo-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }
        @media(max-width: 360px) { .main-header .logo-text { max-width: 100px; } }
        
        /* TomSelect RTL */
        [dir="rtl"] .ts-control > input { text-align: right; }
        
        /* Flip directional icons in RTL */
        [dir="rtl"] .fa-arrow-right, 
        [dir="rtl"] .fa-chevron-right,
        [dir="rtl"] .fa-arrow-left,
        [dir="rtl"] .fa-chevron-left {
            transform: scaleX(-1);
        }
        /* ── Mobile Bottom Nav ── */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            display: flex;
            align-items: center;
            justify-content: space-around;
            border-top: 1px solid rgba(0,0,0,0.05);
            z-index: 1050;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.03);
            padding: 0 10px;
        }
        .nav-item-mobile {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: #9CA3AF;
            font-weight: 700;
            font-size: 0.65rem;
            transition: all 0.3s;
            flex: 1;
        }
        .nav-item-mobile.active { color: #3BB878; }
        .nav-item-mobile i { font-size: 1.2rem; }
        .nav-item-mobile .badge {
            position: absolute;
            top: 5px;
            right: 25%;
            padding: 3px 6px;
            font-size: 0.55rem;
        }

        @media (min-width: 992px) {
            .mobile-bottom-nav { display: none; }
        }
        @media (max-width: 991px) {
            body { padding-bottom: 65px; } /* Space for bottom nav */
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="main-header" id="mainHeader">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between py-3">
                <!-- Logo -->
                <a href="<?php echo e(url('/')); ?>" class="text-decoration-none logo-link">
                    <div class="d-flex align-items-center gap-2">
                        <?php if(setting('app_logo')): ?>
                            <img src="<?php echo e(Storage::url(setting('app_logo'))); ?>" alt="<?php echo e(__('Logo')); ?>" style="height: 50px; width: auto; flex-shrink: 0; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.15));">
                        <?php else: ?>
                            <div style="width: 44px; height: 44px; background: #bf8b43; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-leaf text-white fs-5"></i>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex flex-column text-start align-items-start <?php echo e(app()->getLocale() == 'ar' ? 'text-end align-items-end' : ''); ?>">
                            <span class="fs-5 fw-bold text-white lh-1 logo-text" style="font-family: 'Tajawal', sans-serif;"><?php echo e(app()->getLocale() == 'ar' ? 'تعاونية آيت أومديس' : 'Coop Ait Oumdis'); ?></span>
                            <span class="x-small text-white opacity-50 fw-500 mt-1" style="font-size: 0.62rem; font-family: 'Tajawal', sans-serif;"><?php echo e(app()->getLocale() == 'ar' ? 'منتجات طبيعية من قلب الأطلس' : 'Produits Naturels du cœur de l\'Atlas'); ?></span>
                        </div>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <nav class="d-none d-lg-flex align-items-center gap-4">
                    <a href="<?php echo e(url('/')); ?>" class="text-decoration-none nav-link-custom <?php echo e(Request::is('/') ? 'active' : ''); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'الرئيسية' : (app()->getLocale() == 'fr' ? 'Accueil' : 'Home')); ?></a>
                    <a href="<?php echo e(route('shop.index', ['category' => 'honey'])); ?>" class="text-decoration-none nav-link-custom <?php echo e(request('category') == 'honey' ? 'active' : ''); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'العسل' : (app()->getLocale() == 'fr' ? 'Miel' : 'Honey')); ?></a>
                    <a href="<?php echo e(route('shop.index', ['category' => 'amlou'])); ?>" class="text-decoration-none nav-link-custom <?php echo e(request('category') == 'amlou' ? 'active' : ''); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'أملو' : (app()->getLocale() == 'fr' ? 'Amlou' : 'Amlou')); ?></a>
                    <a href="<?php echo e(route('shop.index')); ?>" class="text-decoration-none nav-link-custom <?php echo e(Request::routeIs('shop.index') && !request('category') ? 'active' : ''); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'المنتجات الطبيعية' : (app()->getLocale() == 'fr' ? 'Produits Naturels' : 'Natural Products')); ?></a>
                    <a href="#about" class="text-decoration-none nav-link-custom"><?php echo e(app()->getLocale() == 'ar' ? 'من نحن' : (app()->getLocale() == 'fr' ? 'Qui sommes-nous' : 'About Us')); ?></a>
                    <a href="https://wa.me/<?php echo e(str_replace(['+',' '],'',(setting('app_phone','212600000000')))); ?>" target="_blank" class="text-decoration-none nav-link-custom"><?php echo e(app()->getLocale() == 'ar' ? 'اتصل بنا' : (app()->getLocale() == 'fr' ? 'Contact' : 'Contact Us')); ?></a>
                </nav>

                <!-- Actions -->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <!-- Search Input (as in design image) -->
                    <div class="d-none d-lg-flex align-items-center header-search-container me-2">
                        <i class="fas fa-search text-white opacity-50 me-2 small"></i>
                        <form action="<?php echo e(route('shop.index')); ?>" method="GET" class="m-0 p-0 w-100">
                            <input type="text" name="search" placeholder="<?php echo e(app()->getLocale() == 'ar' ? 'بحث عن منتج...' : (app()->getLocale() == 'fr' ? 'Rechercher...' : 'Search...')); ?>" class="header-search-input opacity-75 small" value="<?php echo e(request('search')); ?>">
                        </form>
                    </div>

                    <!-- Language -->
                    <div class="dropdown">
                        <button class="btn btn-link text-white opacity-75 hover-opacity-100 text-decoration-none p-2 dropdown-toggle small fw-bold" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i> <?php echo e(strtoupper(app()->getLocale())); ?>

                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><a class="dropdown-item small" href="<?php echo e(route('lang.switch', 'ar')); ?>">🇲🇦 العربية</a></li>
                            <li><a class="dropdown-item small" href="<?php echo e(route('lang.switch', 'fr')); ?>">🇫🇷 Français</a></li>
                            <li><a class="dropdown-item small" href="<?php echo e(route('lang.switch', 'en')); ?>">🇬🇧 English</a></li>
                        </ul>
                    </div>

                    <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-link text-white opacity-75 hover-opacity-100 text-decoration-none p-2 d-none d-lg-block" title="<?php echo e(__('Dashboard')); ?>">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                    <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-link text-white opacity-75 hover-opacity-100 text-decoration-none p-2 d-none d-lg-block" title="<?php echo e(__('Login')); ?>">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                    <?php endif; ?>

                    <!-- Cart Toggle -->
                    <button class="position-relative p-2 border-0 rounded-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.1) !important; border: 1px solid rgba(255,255,255,0.15) !important;">
                        <i class="fa-solid fa-bag-shopping fs-6"></i>
                        <span id="header-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark border border-white" style="font-size: 0.55rem; transform: translate(-60%, 20%); background-color: #bf8b43 !important;">
                            <?php echo e(count(session('cart', []))); ?>

                        </span>
                    </button>

                    <!-- Mobile Toggle -->
                    <button class="d-lg-none p-2 border-0 rounded-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.1) !important; border: 1px solid rgba(255,255,255,0.15) !important;">
                        <i class="fa-solid fa-bars-staggered fs-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="footer-main pt-5 pb-4 mt-0">
        <div class="container">
            <div class="row g-5">
                <!-- Brand Column -->
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width: 36px; height: 36px; background: #3BB878; border-radius: 9px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-leaf text-white small"></i>
                        </div>
                        <span class="text-white fw-bold fs-5"><?php echo e(__('Ait')); ?> <span class="text-green"><?php echo e(__('Oumdis')); ?></span></span>
                    </div>
                    <p class="small lh-lg mb-4" style="color: rgba(255,255,255,0.55);">
                        <?php echo e(setting_trans('app_description', __('A Moroccan cooperative dedicated to bringing you the finest natural products directly from the Atlas Mountains. Pure honey, argan oil, saffron, and more — harvested with tradition and love.'))); ?>

                    </p>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(setting('social_instagram', '#')); ?>" class="footer-social" target="_blank"><i class="fab fa-instagram small"></i></a>
                        <a href="<?php echo e(setting('social_facebook', '#')); ?>" class="footer-social" target="_blank"><i class="fab fa-facebook-f small"></i></a>
                        <a href="<?php echo e(setting('social_tiktok', '#')); ?>" class="footer-social" target="_blank"><i class="fab fa-tiktok small"></i></a>
                        <a href="https://wa.me/<?php echo e(str_replace(['+',' '],'',(setting('social_whatsapp', setting('app_phone','212600000000'))))); ?>" class="footer-social" target="_blank"><i class="fab fa-whatsapp small"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-4"><?php echo e(__('Quick Links')); ?></h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li><a href="<?php echo e(url('/')); ?>" class="footer-link"><?php echo e(__('Home')); ?></a></li>
                        <li><a href="<?php echo e(route('shop.index')); ?>" class="footer-link"><?php echo e(__('Shop')); ?></a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-6 col-lg-3">
                    <h6 class="text-white fw-bold mb-4"><?php echo e(__('Contact')); ?></h6>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li class="d-flex align-items-start gap-2 small" style="color: rgba(255,255,255,0.55);">
                            <i class="fas fa-map-marker-alt text-green mt-1"></i>
                            <?php echo e(__('Ait Oumdis, Azilal, Morocco')); ?>

                        </li>
                        <li class="d-flex align-items-center gap-2 small" style="color: rgba(255,255,255,0.55);">
                            <i class="fas fa-phone text-green"></i>
                            <?php echo e(setting('app_phone', '+212 600 000 000')); ?>

                        </li>
                        <li class="d-flex align-items-center gap-2 small" style="color: rgba(255,255,255,0.55);">
                            <i class="fas fa-envelope text-green"></i>
                            <?php echo e(setting('app_email', 'contact@aitoumdis.ma')); ?>

                        </li>
                    </ul>
                </div>

                <!-- Trust Badges -->
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4"><?php echo e(__('Why Trust Us?')); ?></h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-2 small" style="color: rgba(255,255,255,0.55);">
                            <?php echo e(__('100% Certified Organic')); ?>

                        </div>
                        <div class="d-flex align-items-center gap-2 small" style="color: rgba(255,255,255,0.55);">
                            <?php echo e(__('Delivery to all Morocco')); ?>

                        </div>
                        <div class="d-flex align-items-center gap-2 small" style="color: rgba(255,255,255,0.55);">
                            <?php echo e(__('Pay on Delivery')); ?>

                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5" style="border-color: rgba(255,255,255,0.06);">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <p class="small mb-0" style="color: rgba(255,255,255,0.35);">
                    &copy; <?php echo e(date('Y')); ?> <?php echo e(setting_trans('app_name', 'Coop Ait Oumdis')); ?>. <?php echo e(__('All rights reserved.')); ?>

                </p>
                <div class="d-flex gap-3">
                    <span class="x-small" style="color: rgba(255,255,255,0.25);"><?php echo e(__('Crafted with')); ?> ❤️ <?php echo e(__('in Morocco')); ?></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mini-Cart Drawer -->
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="miniCart" style="max-width: 420px; width: 100%;">
        <div class="offcanvas-header" style="background: #f9fafb; border-bottom: 1px solid #f1f5f9;">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 36px; height: 36px; background: #3BB878; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-bag-shopping text-white small"></i>
                </div>
                <h5 class="offcanvas-title fw-bold mb-0"><?php echo e(__('My Cart')); ?></h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <div class="flex-grow-1 overflow-auto p-3" id="mini-cart-items">
                <?php echo $__env->make('frontend.cart.partials.mini-cart-items', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div id="mini-cart-footer" class="border-top p-3" style="background: #f9fafb;">
                <?php echo $__env->make('frontend.cart.partials.mini-cart-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Offcanvas -->
    <div class="offcanvas offcanvas-<?php echo e(app()->getLocale() == 'ar' ? 'end' : 'start'); ?> border-0 shadow-lg" tabindex="-1" id="mobileMenu" style="width: 280px; background: #fff;">
        <div class="offcanvas-header border-bottom py-4">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 32px; height: 32px; background: #3BB878; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-leaf text-white small"></i>
                </div>
                <span class="fw-bold text-dark fs-5"><?php echo e(__('Ait')); ?> <span class="text-green"><?php echo e(__('Oumdis')); ?></span></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush">
                <a href="<?php echo e(url('/')); ?>" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3">
                    <i class="fas fa-home text-muted"></i>
                    <span class="fw-600"><?php echo e(__('Home')); ?></span>
                </a>
                <a href="<?php echo e(route('shop.index')); ?>" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3">
                    <i class="fas fa-shopping-bag text-muted"></i>
                    <span class="fw-600"><?php echo e(__('Shop')); ?></span>
                </a>
                <div class="p-4 mt-auto">
                    <div class="p-4 rounded-4 bg-light border border-light">
                        <div class="fw-bold text-dark mb-2"><?php echo e(__('Contact Support')); ?></div>
                        <div class="small text-muted mb-3"><?php echo e(__('Need help with your order?')); ?></div>
                        <a href="https://wa.me/<?php echo e(str_replace(['+',' '],'',(setting('app_phone','212600000000')))); ?>" class="btn btn-brand btn-brand-primary w-100 py-2 rounded-pill">
                            <i class="fab fa-whatsapp me-1"></i> WhatsApp
                        </a>
                    </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav d-lg-none">
        <a href="<?php echo e(url('/')); ?>" class="nav-item-mobile <?php echo e(Request::is('/') ? 'active' : ''); ?>">
            <i class="fas fa-home"></i>
            <span><?php echo e(__('Home')); ?></span>
        </a>
        <a href="<?php echo e(route('shop.index')); ?>" class="nav-item-mobile <?php echo e(Request::routeIs('shop.*') ? 'active' : ''); ?>">
            <i class="fas fa-shopping-bag"></i>
            <span><?php echo e(__('Shop')); ?></span>
        </a>
        <a href="javascript:void(0)" class="nav-item-mobile position-relative" data-bs-toggle="offcanvas" data-bs-target="#miniCart">
            <i class="fas fa-cart-shopping"></i>
            <span class="badge rounded-pill bg-green"><?php echo e(count(session('cart', []))); ?></span>
            <span><?php echo e(__('Cart')); ?></span>
        </a>
        <a href="https://wa.me/<?php echo e(str_replace(['+',' '],'',(setting('app_phone','212600000000')))); ?>" class="nav-item-mobile">
            <i class="fab fa-whatsapp"></i>
            <span><?php echo e(__('Contact')); ?></span>
        </a>
    </div>

    <!-- Scroll to Top -->
    <button id="scrollTopBtn" onclick="window.scrollTo({top:0, behavior:'smooth'})">
        <i class="fas fa-arrow-up small"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // Init AOS
        AOS.init({ duration: 700, once: true, offset: 60, easing: 'ease-out-cubic' });

        // Sticky Header shadow
        window.addEventListener('scroll', () => {
            document.getElementById('mainHeader')?.classList.toggle('scrolled', window.scrollY > 20);
            const btn = document.getElementById('scrollTopBtn');
            if(btn) btn.classList.toggle('visible', window.scrollY > 200);
        });

        // Cart update
        function updateQty(id, qty) {
            if(qty < 1) { removeItem(id); return; }
            fetch('<?php echo e(route('cart.update')); ?>', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
                body: JSON.stringify({ id, quantity: qty })
            }).then(r => r.json()).then(data => {
                if(data.success) {
                    document.getElementById('header-cart-count').innerText = data.cartCount;
                    refreshMiniCart();
                } else {
                    Swal.fire({ icon: 'warning', title: data.message || '<?php echo e(__('Insufficient stock')); ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            });
        }

        function removeItem(id) {
            Swal.fire({
                title: '<?php echo e(__('Remove this item?')); ?>',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#3BB878', cancelButtonText: '<?php echo e(__('Keep')); ?>',
                confirmButtonText: '<?php echo e(__('Remove')); ?>'
            }).then((r) => {
                if (r.isConfirmed) {
                    fetch('<?php echo e(route('cart.remove')); ?>', {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
                        body: JSON.stringify({ id })
                    }).then(r => r.json()).then(data => {
                        document.getElementById('header-cart-count').innerText = data.cartCount;
                        refreshMiniCart();
                    });
                }
            });
        }

        function refreshMiniCart() {
            fetch('<?php echo e(route('cart.mini')); ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text()).then(html => document.getElementById('mini-cart-items').innerHTML = html);
            fetch('<?php echo e(route('cart.miniFooter')); ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text()).then(html => document.getElementById('mini-cart-footer').innerHTML = html);
        }

        function addToCart(productId, variantId = null) {
            const btn = event?.currentTarget;
            const orig = btn?.innerHTML;
            if(btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }

            fetch(`<?php echo e(url('/cart/add')); ?>/${productId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ quantity: 1, variant_id: variantId })
            }).then(r => r.json()).then(data => {
                if(btn) { btn.disabled = false; btn.innerHTML = orig; }
                if(data.success) {
                    Swal.fire({ icon: 'success', title: '<?php echo e(__('Added to cart!')); ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    const badge = document.getElementById('header-cart-count');
                    if (badge) {
                        badge.innerText = data.cartCount;
                        badge.classList.remove('cart-badge-pulse');
                        void badge.offsetWidth; // trigger reflow
                        badge.classList.add('cart-badge-pulse');
                    }
                    refreshMiniCart();
                    new bootstrap.Offcanvas(document.getElementById('miniCart')).show();
                } else {
                    Swal.fire({ icon: 'error', title: '<?php echo e(__('Error')); ?>', text: data.message });
                }
            }).catch(() => { if(btn) { btn.disabled = false; btn.innerHTML = orig; }});
        }

        function selectProductSize(productId, variantId, formattedPrice, formattedOriginalPrice, isInStock) {
            const sizeList = document.querySelector(`.size-list-${productId}`);
            if (sizeList) {
                sizeList.querySelectorAll('.size-pill-btn').forEach(btn => {
                    btn.classList.remove('border-green', 'bg-green-light', 'text-green', 'fw-700');
                    btn.classList.add('border-light', 'bg-light', 'text-muted');
                });
            }
            const clickedBtn = document.getElementById(`size-pill-${productId}-${variantId}`);
            if (clickedBtn) {
                clickedBtn.classList.remove('border-light', 'bg-light', 'text-muted');
                clickedBtn.classList.add('border-green', 'bg-green-light', 'text-green', 'fw-700');
            }

            const priceContainer = document.querySelector(`.price-container-${productId}`);
            if (priceContainer) {
                const mainPrice = priceContainer.querySelector('.main-price');
                const origPrice = priceContainer.querySelector('.original-price');
                if (mainPrice) mainPrice.innerText = formattedPrice;
                if (origPrice) {
                    if (formattedOriginalPrice) {
                        origPrice.innerText = formattedOriginalPrice;
                        origPrice.classList.remove('d-none');
                    } else {
                        origPrice.classList.add('d-none');
                    }
                }
            }

            const addBtn = document.getElementById(`add-cart-btn-${productId}`);
            if (addBtn) {
                if (isInStock) {
                    addBtn.removeAttribute('disabled');
                    addBtn.style.opacity = '1';
                    addBtn.onclick = function(e) {
                        addToCart(productId, variantId);
                    };
                } else {
                    addBtn.setAttribute('disabled', 'disabled');
                    addBtn.style.opacity = '0.5';
                    addBtn.onclick = null;
                }
            }
        }

        function toggleWishlist(productId) {
            const heart = document.getElementById(`heart-icon-${productId}`);
            if (heart) {
                const isHearted = heart.classList.contains('fa-solid');
                if (isHearted) {
                    heart.classList.remove('fa-solid', 'text-danger');
                    heart.classList.add('fa-regular', 'text-muted');
                    Swal.fire({
                        icon: 'success',
                        title: '<?php echo e(__("Removed from wishlist")); ?>',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    heart.classList.remove('fa-regular', 'text-muted');
                    heart.classList.add('fa-solid', 'text-danger');
                    Swal.fire({
                        icon: 'success',
                        title: '<?php echo e(__("Added to wishlist!")); ?>',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            }
        }
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/layouts/frontend.blade.php ENDPATH**/ ?>