<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', __('Dashboard')); ?> - <?php echo e(setting('app_name', 'E-commerce')); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?php if(app()->getLocale() == 'ar'): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/dashboard.css')); ?>?v=<?php echo e(filemtime(public_path('css/dashboard.css'))); ?>">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css">
    
    <!-- Dynamic Theme CSS Variables -->
    <style>
        :root {
            --primary-color: <?php echo e(setting('primary_color', '#3b82f6')); ?>;
            --secondary-color: <?php echo e(setting('secondary_color', '#10b981')); ?>;
            --accent-color: <?php echo e(setting('accent_color', '#8b5cf6')); ?>;
            --success-color: <?php echo e(setting('success_color', '#10b981')); ?>;
            --warning-color: <?php echo e(setting('warning_color', '#f59e0b')); ?>;
            --danger-color: <?php echo e(setting('danger_color', '#ef4444')); ?>;
            --font-family: <?php echo e(setting('font_family', "'Cairo', 'Inter', system-ui, sans-serif")); ?>;
            --font-size-base: <?php echo e(setting('font_size_base', '14')); ?>px;
            --border-radius: <?php echo e(setting('border_radius', '12')); ?>px;
        }
        
        body {
            font-family: var(--font-family);
            font-size: var(--font-size-base);
        }

        /* Mobile Dropdown Fix */
        @media (max-width: 991px) {
            .mobile-header {
                background: rgba(255, 255, 255, 0.8) !important;
                backdrop-filter: blur(15px) !important;
                -webkit-backdrop-filter: blur(15px) !important;
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
            }
            .user-dropdown {
                top: 60px !important;
                left: 12px !important;
                right: auto !important;
                z-index: 1100 !important;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
            }
        }
    </style>
    

    <!-- OneSignal SDK -->
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
    <script>
      window.OneSignal = window.OneSignal || [];
      OneSignal.push(function() {
        OneSignal.init({
          appId: "<?php echo e(env('ONESIGNAL_APP_ID')); ?>",
          safari_web_id: "web.onesignal.auto.10427e02-4660-449e-b5c6-724f113e6396",
          notifyButton: {
            enable: true,
          },
          allowLocalhostAsSecureOrigin: true,
        });
      });
    </script>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Mobile Header -->
    <header class="mobile-header d-lg-none">
        <button class="sidebar-toggle" id="mobile-sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="mobile-brand">
            <?php echo e(setting('app_name', 'E-commerce')); ?>

        </div>
        <div class="mobile-user" id="user-menu-trigger-mobile">
            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

        </div>
    </header>

    <!-- Navbar (Desktop) -->
    <nav class="navbar d-none d-lg-flex">
        <div class="navbar-container">
            <div class="d-flex align-items-center gap-3">
                <button id="sidebar-toggle-desktop" class="sidebar-toggle d-none d-lg-flex">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="user-avatar" id="user-menu-trigger">
                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

            </div>
        </div>
    </nav>

    <!-- User Dropdown (Common) -->
    <div class="user-dropdown">
        <div class="user-dropdown-header">
            <div class="user-dropdown-name"><?php echo e(auth()->user()->name); ?></div>
            <div class="user-dropdown-email"><?php echo e(auth()->user()->email); ?></div>
        </div>
        <a href="<?php echo e(route('profile.show')); ?>" class="user-dropdown-item">
            <i class="fas fa-user"></i>
            <?php echo e(__('My Profile')); ?>

        </a>
        <a href="<?php echo e(route('profile.edit')); ?>" class="user-dropdown-item">
            <i class="fas fa-edit"></i>
            <?php echo e(__('Edit Profile')); ?>

        </a>
        <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin: 0;">
            <?php echo csrf_field(); ?>
            <button type="submit" class="user-dropdown-item" style="width: 100%;">
                <i class="fas fa-sign-out-alt"></i>
                <?php echo e(__('Logout')); ?>

            </button>
        </form>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-menu-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span><?php echo e(__('Dashboard')); ?></span>
                </a>
            </li>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_products')): ?>
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('products.index')); ?>" class="sidebar-menu-link <?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>">
                    <i class="fas fa-box"></i>
                    <span><?php echo e(__('Products')); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_categories')): ?>
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('categories.index')); ?>" class="sidebar-menu-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
                    <i class="fas fa-folder-tree"></i>
                    <span><?php echo e(__('Categories')); ?></span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_orders')): ?>
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('orders.index')); ?>" class="sidebar-menu-link <?php echo e(request()->routeIs('orders.*') ? 'active' : ''); ?>">
                    <i class="fas fa-cart-shopping"></i>
                    <span><?php echo e(__('Orders')); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_inventory')): ?>
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('inventory.index')); ?>" class="sidebar-menu-link <?php echo e(request()->routeIs('inventory.*') ? 'active' : ''); ?>">
                    <i class="fas fa-boxes-stacked"></i>
                    <span><?php echo e(__('Inventory')); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('settings.maintenance')); ?>" class="sidebar-menu-link <?php echo e(request()->routeIs('settings.maintenance') ? 'active' : ''); ?>">
                    <i class="fas fa-tools "></i>
                    <span><?php echo e(__('Store Status')); ?></span>
                </a>
            </li>
        </ul>
    </aside>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Main Content -->
    <main class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <!-- SweetAlert Helpers -->
    <script src="<?php echo e(asset('js/sweetalert-helpers.js')); ?>"></script>
    <!-- Delete Confirmation Handler -->
    <script src="<?php echo e(asset('js/delete-confirmation.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if(session('success')): ?>
            Toast.fire({
                icon: 'success',
                title: '<?php echo e(session('success')); ?>'
            });
        <?php endif; ?>

        <?php if(session('error')): ?>
            Toast.fire({
                icon: 'error',
                title: '<?php echo e(session('error')); ?>'
            });
        <?php endif; ?>

        // Mobile Sidebar Toggle
        const mobileToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const userMenuDesktop = document.getElementById('user-menu-trigger');
        const userMenuMobile = document.getElementById('user-menu-trigger-mobile');
        const userDropdown = document.querySelector('.user-dropdown');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.add('show');
                overlay.classList.add('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        if (userMenuDesktop) {
            userMenuDesktop.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });
        }

        if (userMenuMobile) {
            userMenuMobile.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });
        }

        // Desktop Sidebar Toggle
        // const sidebar = document.querySelector('.sidebar'); // Already defined above
        const navbar = document.querySelector('.navbar');
        const mainContent = document.querySelector('.main-content');
        const desktopToggle = document.getElementById('sidebar-toggle-desktop');

        if (desktopToggle) {
            desktopToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                navbar.classList.toggle('expanded');
                mainContent.classList.toggle('expanded');
                
                // Save preference
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            });

            // Restore preference
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
                navbar.classList.add('expanded');
                mainContent.classList.add('expanded');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (userDropdown && !userDropdown.contains(e.target)) {
                if (userMenuDesktop && !userMenuDesktop.contains(e.target)) {
                    if (!userMenuMobile || !userMenuMobile.contains(e.target)) {
                        userDropdown.classList.remove('active');
                    }
                }
            }
        });
    </script>
    <script>
        // Real-time Order Notifications
        let lastOrderId = null;
        const notificationSound = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YTdvT18AZB9vH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28fH28f');

        async function checkNewOrders() {
            try {
                const url = '<?php echo e(route('dashboard.check-new-orders')); ?>' + (lastOrderId ? '?last_id=' + lastOrderId : '');
                const response = await fetch(url);
                const data = await response.json();

                if (lastOrderId && data.count > 0) {
                    // Play sound
                    notificationSound.play().catch(e => console.log('Sound blocked by browser policy'));
                    
                    // Show Notification
                    data.orders.forEach(order => {
                        Toast.fire({
                            icon: 'info',
                            title: `🔔 <?php echo e(__('New Order')); ?>: #${order.number}`,
                            text: `<?php echo e(__('Customer')); ?>: ${order.customer} - ${order.total}`,
                            timer: 10000,
                            showConfirmButton: true,
                            confirmButtonText: '<?php echo e(__('View')); ?>',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = order.url;
                            }
                        });
                    });
                }
                
                lastOrderId = data.last_id;
            } catch (error) {
                console.error('Error checking for orders:', error);
            }
        }

        // Start checking (every 60 seconds)
        if (window.location.pathname.includes('/dashboard') || window.location.pathname.includes('/orders')) {
            checkNewOrders(); // Initial check
            setInterval(checkNewOrders, 60000);
        }
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/layouts/app.blade.php ENDPATH**/ ?>