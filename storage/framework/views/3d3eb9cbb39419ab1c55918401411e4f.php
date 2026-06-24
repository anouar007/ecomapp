<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('meta_title', setting_trans('app_name', 'Coop Ait Oumdis')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', setting_trans('app_description', 'Natural Products Cooperative')); ?>">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    
    <?php if(setting('app_logo')): ?>
        <link rel="icon" type="image/png" href="<?php echo e(Storage::url(setting('app_logo'))); ?>">
        <link rel="apple-touch-icon" href="<?php echo e(Storage::url(setting('app_logo'))); ?>">
    <?php endif; ?>

    
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">
    <meta name="author" content="<?php echo e(setting('app_name', 'Coop Ait Oumdis')); ?>">

    
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?php echo e(url()->current()); ?>">
    <meta property="og:title"       content="<?php echo $__env->yieldContent('meta_title', setting_trans('app_name', 'Coop Ait Oumdis')); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta_description', setting_trans('app_description', 'Natural Products Cooperative')); ?>">
    <meta property="og:image"       content="<?php echo $__env->yieldContent('meta_image', setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/og-image.jpg')); ?>">
    <meta property="og:site_name"   content="<?php echo e(setting('app_name', 'Coop Ait Oumdis')); ?>">

    
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:url"         content="<?php echo e(url()->current()); ?>">
    <meta name="twitter:title"       content="<?php echo $__env->yieldContent('meta_title', setting_trans('app_name', 'Coop Ait Oumdis')); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('meta_description', setting_trans('app_description', 'Natural Products Cooperative')); ?>">
    <meta name="twitter:image"       content="<?php echo $__env->yieldContent('meta_image', setting('app_logo') ? url(Storage::url(setting('app_logo'))) : asset('images/og-image.jpg')); ?>">

    
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

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    
    <?php if(app()->getLocale() == 'ar'): ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <?php endif; ?>

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/frontend.css']); ?>

    
    <?php if(file_exists(public_path('css/brand.css'))): ?>
        <link rel="stylesheet" href="<?php echo e(asset('css/brand.css')); ?>?v=<?php echo e(filemtime(public_path('css/brand.css'))); ?>">
    <?php endif; ?>

    <?php echo $__env->yieldPushContent('styles'); ?>

</head>
<body>

    
    <header class="main-header" id="mainHeader">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between py-3">

                
                <a href="<?php echo e(url('/')); ?>" class="text-decoration-none logo-link">
                    <div class="d-flex align-items-center gap-2">
                        <?php if(setting('app_logo')): ?>
                            <img src="<?php echo e(Storage::url(setting('app_logo'))); ?>"
                                 alt="<?php echo e(__('Logo')); ?>"
                                 style="height:50px;width:auto;flex-shrink:0;filter:drop-shadow(0 2px 8px rgba(0,0,0,.15));">
                        <?php else: ?>
                            <div style="width:44px;height:44px;background:#bf8b43;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-leaf text-white fs-5"></i>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex flex-column text-start align-items-start <?php echo e(app()->getLocale() == 'ar' ? 'text-end align-items-end' : ''); ?>">
                            <span class="fs-5 fw-bold text-white lh-1 logo-text font-tajawal">
                                <?php echo e(app()->getLocale() == 'ar' ? 'تعاونية آيت أومديس' : 'Coop Ait Oumdis'); ?>

                            </span>
                            <span class="x-small text-white opacity-50 mt-1 font-tajawal" style="font-size:0.62rem;">
                                <?php echo e(app()->getLocale() == 'ar' ? 'منتجات طبيعية من قلب الأطلس' : 'Produits Naturels du cœur de l\'Atlas'); ?>

                            </span>
                        </div>
                    </div>
                </a>

                
                <nav class="d-none d-lg-flex align-items-center gap-4">
                    <a href="<?php echo e(url('/')); ?>"                                       class="text-decoration-none nav-link-custom <?php echo e(Request::is('/') ? 'active' : ''); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'الرئيسية' : (app()->getLocale() == 'fr' ? 'Accueil' : 'Home')); ?></a>
                    <a href="<?php echo e(route('shop.index', ['category' => 'honey'])); ?>"   class="text-decoration-none nav-link-custom <?php echo e(request('category') == 'honey' ? 'active' : ''); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'العسل' : (app()->getLocale() == 'fr' ? 'Miel' : 'Honey')); ?></a>
                    <a href="<?php echo e(route('shop.index', ['category' => 'amlou'])); ?>"  class="text-decoration-none nav-link-custom <?php echo e(request('category') == 'amlou' ? 'active' : ''); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'أملو' : (app()->getLocale() == 'fr' ? 'Amlou' : 'Amlou')); ?></a>
                    <a href="<?php echo e(route('shop.index')); ?>"                            class="text-decoration-none nav-link-custom <?php echo e(Request::routeIs('shop.index') && !request('category') ? 'active' : ''); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'المنتجات الطبيعية' : (app()->getLocale() == 'fr' ? 'Produits Naturels' : 'Natural Products')); ?></a>
                    <a href="https://wa.me/<?php echo e(str_replace(['+',' '],'',(setting('app_phone','212600000000')))); ?>" target="_blank" class="text-decoration-none nav-link-custom"><?php echo e(app()->getLocale() == 'ar' ? 'اتصل بنا' : (app()->getLocale() == 'fr' ? 'Contact' : 'Contact Us')); ?></a>
                </nav>

                
                <div class="d-flex align-items-center gap-2 gap-lg-3">

                    
                    <div class="d-none d-lg-flex align-items-center header-search-container me-2">
                        <i class="fas fa-search text-white opacity-50 me-2 small"></i>
                        <form action="<?php echo e(route('shop.index')); ?>" method="GET" class="m-0 p-0 w-100">
                            <input type="text" name="search"
                                   placeholder="<?php echo e(app()->getLocale() == 'ar' ? 'بحث عن منتج...' : (app()->getLocale() == 'fr' ? 'Rechercher...' : 'Search...')); ?>"
                                   class="header-search-input opacity-75 small"
                                   value="<?php echo e(request('search')); ?>">
                        </form>
                    </div>

                    
                    <div class="dropdown">
                        <button class="btn btn-link text-white opacity-75 text-decoration-none p-2 dropdown-toggle small fw-bold" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i> <?php echo e(strtoupper(app()->getLocale())); ?>

                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><a class="dropdown-item small" href="<?php echo e(route('lang.switch', 'ar')); ?>">🇲🇦 العربية</a></li>
                            <li><a class="dropdown-item small" href="<?php echo e(route('lang.switch', 'fr')); ?>">🇫🇷 Français</a></li>
                            <li><a class="dropdown-item small" href="<?php echo e(route('lang.switch', 'en')); ?>">🇬🇧 English</a></li>
                        </ul>
                    </div>

                    
                    <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-link text-white opacity-75 text-decoration-none p-2 d-none d-lg-block" title="<?php echo e(__('Dashboard')); ?>">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                    <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-link text-white opacity-75 text-decoration-none p-2 d-none d-lg-block" title="<?php echo e(__('Login')); ?>">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                    <?php endif; ?>

                    
                    <button class="position-relative p-2 border-0 rounded-3 text-white"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#miniCart"
                            style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;background-color:rgba(255,255,255,0.1)!important;border:1px solid rgba(255,255,255,0.15)!important;">
                        <i class="fa-solid fa-bag-shopping fs-6"></i>
                        
                        <span data-cart-count
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill border border-white"
                              style="font-size:0.55rem;transform:translate(-60%,20%);background-color:#bf8b43!important;">
                            <?php echo e(count(session('cart', []))); ?>

                        </span>
                    </button>

                    
                    <button class="d-lg-none p-2 border-0 rounded-3 text-white"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#mobileMenu"
                            style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;background-color:rgba(255,255,255,0.1)!important;border:1px solid rgba(255,255,255,0.15)!important;">
                        <i class="fa-solid fa-bars-staggered fs-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <footer class="footer-main pt-5 pb-4 mt-0">
        <div class="container">
            <div class="row g-5">

                
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:#2E993B;border-radius:9px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-leaf text-white small"></i>
                        </div>
                        <span class="text-white fw-bold fs-5 font-tajawal"><?php echo e(__('Ait')); ?> <span class="text-green-mid"><?php echo e(__('Oumdis')); ?></span></span>
                    </div>
                    <p class="small lh-lg mb-4 font-tajawal" style="color:rgba(255,255,255,0.55);">
                        <?php echo e(setting_trans('app_description', __('A Moroccan cooperative dedicated to bringing you the finest natural products directly from the Atlas Mountains. Pure honey, argan oil, saffron, and more — harvested with tradition and love.'))); ?>

                    </p>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(setting('social_instagram', '#')); ?>" class="footer-social" target="_blank"><i class="fab fa-instagram small"></i></a>
                        <a href="<?php echo e(setting('social_facebook', '#')); ?>"  class="footer-social" target="_blank"><i class="fab fa-facebook-f small"></i></a>
                        <a href="<?php echo e(setting('social_tiktok', '#')); ?>"    class="footer-social" target="_blank"><i class="fab fa-tiktok small"></i></a>
                        <a href="https://wa.me/<?php echo e(str_replace(['+',' '],'',(setting('social_whatsapp', setting('app_phone','212600000000'))))); ?>" class="footer-social" target="_blank"><i class="fab fa-whatsapp small"></i></a>
                    </div>
                </div>

                
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-4"><?php echo e(__('Quick Links')); ?></h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li><a href="<?php echo e(url('/')); ?>"          class="footer-link"><?php echo e(__('Home')); ?></a></li>
                        <li><a href="<?php echo e(route('shop.index')); ?>" class="footer-link"><?php echo e(__('Shop')); ?></a></li>
                    </ul>
                </div>

                
                <div class="col-6 col-lg-3">
                    <h6 class="text-white fw-bold mb-4"><?php echo e(__('Contact')); ?></h6>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li class="d-flex align-items-start gap-2 small" style="color:rgba(255,255,255,0.55);">
                            <i class="fas fa-map-marker-alt text-green-mid mt-1"></i>
                            <?php echo e(__('Ait Oumdis, Azilal, Morocco')); ?>

                        </li>
                        <li class="d-flex align-items-center gap-2 small" style="color:rgba(255,255,255,0.55);">
                            <i class="fas fa-phone text-green-mid"></i>
                            <?php echo e(setting('app_phone', '+212 600 000 000')); ?>

                        </li>
                        <li class="d-flex align-items-center gap-2 small" style="color:rgba(255,255,255,0.55);">
                            <i class="fas fa-envelope text-green-mid"></i>
                            <?php echo e(setting('app_email', 'contact@aitoumdis.ma')); ?>

                        </li>
                    </ul>
                </div>

                
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4"><?php echo e(__('Why Trust Us?')); ?></h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="footer-trust-item">
                            <i class="fas fa-certificate"></i>
                            <span><?php echo e(__('100% Certified Organic')); ?></span>
                        </div>
                        <div class="footer-trust-item">
                            <i class="fas fa-truck-fast"></i>
                            <span><?php echo e(__('Delivery to all Morocco')); ?></span>
                        </div>
                        <div class="footer-trust-item">
                            <i class="fas fa-hand-holding-dollar"></i>
                            <span><?php echo e(__('Pay on Delivery')); ?></span>
                        </div>
                        <div class="footer-trust-item">
                            <i class="fas fa-shield-halved"></i>
                            <span><?php echo e(__('Satisfaction Guaranteed')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5" style="border-color:rgba(255,255,255,0.06);">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <p class="small mb-0" style="color:rgba(255,255,255,0.35);">
                    &copy; <?php echo e(date('Y')); ?> <?php echo e(setting_trans('app_name', 'Coop Ait Oumdis')); ?>. <?php echo e(__('All rights reserved.')); ?>

                </p>
                <div class="d-flex gap-3">
                    <span class="x-small" style="color:rgba(255,255,255,0.25);"><?php echo e(__('Crafted with')); ?> ❤️ <?php echo e(__('in Morocco')); ?></span>
                </div>
            </div>
        </div>
    </footer>

    
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="miniCart" style="max-width:420px;width:100%;">
        <div class="offcanvas-header" style="background:#f9fafb;border-bottom:1px solid #f1f5f9;">
            <div class="d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;background:#2E993B;border-radius:10px;display:flex;align-items:center;justify-content:center;">
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
            <div id="mini-cart-footer" class="border-top p-3" style="background:#f9fafb;">
                <?php echo $__env->make('frontend.cart.partials.mini-cart-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>

    
    <div class="offcanvas offcanvas-<?php echo e(app()->getLocale() == 'ar' ? 'end' : 'start'); ?> border-0 shadow-lg"
         tabindex="-1" id="mobileMenu" style="width:300px;background:#fff;">
        <div class="offcanvas-header border-bottom py-4">
            <div class="d-flex align-items-center gap-2">
                <div style="width:32px;height:32px;background:#2E993B;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-leaf text-white small"></i>
                </div>
                <span class="fw-bold text-dark fs-5 font-tajawal"><?php echo e(__('Ait')); ?> <span class="text-green-mid"><?php echo e(__('Oumdis')); ?></span></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            
            <div class="list-group list-group-flush">
                <a href="<?php echo e(url('/')); ?>"                                       class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3 font-tajawal <?php echo e(Request::is('/') ? 'text-green fw-bold' : ''); ?>">
                    <i class="fas fa-home <?php echo e(Request::is('/') ? 'text-green-mid' : 'text-muted'); ?>"></i>
                    <span><?php echo e(app()->getLocale() == 'ar' ? 'الرئيسية' : (app()->getLocale() == 'fr' ? 'Accueil' : 'Home')); ?></span>
                </a>
                <a href="<?php echo e(route('shop.index')); ?>"                            class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3 font-tajawal <?php echo e(Request::routeIs('shop.*') ? 'text-green fw-bold' : ''); ?>">
                    <i class="fas fa-shopping-bag <?php echo e(Request::routeIs('shop.*') ? 'text-green-mid' : 'text-muted'); ?>"></i>
                    <span><?php echo e(app()->getLocale() == 'ar' ? 'المتجر' : (app()->getLocale() == 'fr' ? 'Boutique' : 'Shop')); ?></span>
                </a>
                <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3 font-tajawal">
                    <i class="fa-regular fa-user text-muted"></i>
                    <span><?php echo e(__('Dashboard')); ?></span>
                </a>
                <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="list-group-item list-group-item-action py-3 px-4 border-0 d-flex align-items-center gap-3 font-tajawal">
                    <i class="fa-regular fa-user text-muted"></i>
                    <span><?php echo e(__('Login')); ?></span>
                </a>
                <?php endif; ?>
            </div>

            
            <div class="px-4 py-3 border-top mt-auto">
                <p class="x-small fw-bold text-muted text-uppercase mb-2" style="letter-spacing:1px;"><?php echo e(__('Language')); ?></p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?php echo e(route('lang.switch', 'ar')); ?>"
                       class="btn btn-sm rounded-pill fw-bold <?php echo e(app()->getLocale() == 'ar' ? 'btn-dark' : 'btn-outline-secondary'); ?>"
                       style="font-size:0.75rem;">🇲🇦 العربية</a>
                    <a href="<?php echo e(route('lang.switch', 'fr')); ?>"
                       class="btn btn-sm rounded-pill fw-bold <?php echo e(app()->getLocale() == 'fr' ? 'btn-dark' : 'btn-outline-secondary'); ?>"
                       style="font-size:0.75rem;">🇫🇷 Français</a>
                    <a href="<?php echo e(route('lang.switch', 'en')); ?>"
                       class="btn btn-sm rounded-pill fw-bold <?php echo e(app()->getLocale() == 'en' ? 'btn-dark' : 'btn-outline-secondary'); ?>"
                       style="font-size:0.75rem;">🇬🇧 English</a>
                </div>
            </div>

            
            <div class="p-4">
                <div class="p-4 rounded-4 bg-light border border-light">
                    <div class="fw-bold text-dark mb-1 font-tajawal"><?php echo e(__('Contact Support')); ?></div>
                    <div class="small text-muted mb-3 font-tajawal"><?php echo e(__('Need help with your order?')); ?></div>
                    <a href="https://wa.me/<?php echo e(str_replace(['+',' '],'',(setting('app_phone','212600000000')))); ?>"
                       class="btn btn-brand btn-brand-primary w-100 py-2 rounded-pill text-decoration-none">
                        <i class="fab fa-whatsapp me-1"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>


    
    <button id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fas fa-arrow-up small"></i>
    </button>

    
    <script>
        window.__csrfToken       = '<?php echo e(csrf_token()); ?>';
        window.__baseUrl         = '<?php echo e(url('')); ?>';
        window.__cartMiniUrl     = '<?php echo e(route('cart.mini')); ?>';
        window.__cartMiniFooterUrl = '<?php echo e(route('cart.miniFooter')); ?>';
        window.__cartUpdateUrl   = '<?php echo e(route('cart.update')); ?>';
        window.__cartRemoveUrl   = '<?php echo e(route('cart.remove')); ?>';
        window.__i18n = {
            addedToCart:  '<?php echo e(__('Added to cart!')); ?>',
            outOfStock:   '<?php echo e(__('Insufficient stock')); ?>',
            removeItem:   '<?php echo e(__('Remove this item?')); ?>',
            keep:         '<?php echo e(__('Keep')); ?>',
            remove:       '<?php echo e(__('Remove')); ?>',
            removedFromWishlist: '<?php echo e(__('Removed from wishlist')); ?>',
            addedToWishlist:     '<?php echo e(__('Added to wishlist!')); ?>',
            error:        '<?php echo e(__('Error')); ?>',
        };
    </script>

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/frontend.js']); ?>

    
    <script>
        window.toggleWishlist = function (productId) {
            const heart = document.getElementById(`heart-icon-${productId}`);
            if (!heart) return;
            const isHearted = heart.classList.contains('fa-solid');
            if (isHearted) {
                heart.classList.replace('fa-solid', 'fa-regular');
                heart.classList.remove('text-danger');
                heart.classList.add('text-muted');
                Swal.fire({ icon:'success', title: window.__i18n.removedFromWishlist, toast:true, position:'top-end', showConfirmButton:false, timer:2000 });
            } else {
                heart.classList.replace('fa-regular', 'fa-solid');
                heart.classList.remove('text-muted');
                heart.classList.add('text-danger');
                Swal.fire({ icon:'success', title: window.__i18n.addedToWishlist, toast:true, position:'top-end', showConfirmButton:false, timer:2000 });
            }
        };
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/layouts/frontend.blade.php ENDPATH**/ ?>