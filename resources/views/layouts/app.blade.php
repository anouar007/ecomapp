<!DOCTYPE html>
<html lang="{{ setting('language', 'en') }}" dir="{{ setting('text_direction', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') - {{ setting('app_name', 'E-commerce') }}</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ site_logo_url('favicon') }}" type="image/png">
    <link rel="icon" href="{{ site_logo_url('favicon') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ site_logo_url('favicon') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ site_logo_url('favicon') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ site_logo_url('apple_touch') }}">
    <link rel="shortcut icon" href="{{ site_logo_url('favicon') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ file_exists(public_path('css/dashboard.css')) ? filemtime(public_path('css/dashboard.css')) : time() }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
    @stack('styles')
    
    <!-- Winashop Theme Variables & Global Typography -->
    <style>
        :root {
            --primary-color: #dc2626;
            --primary-dark: #b91c1c;
            --secondary-color: #0f172a;
            --accent-color: #dc2626;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #dc2626;
            --font-family: "Roboto", sans-serif;
            --font-heading: "Roboto", sans-serif;
            --font-size-base: 14px;
            --border-radius: 8px !important;
            --bs-border-radius: 8px !important;
            --bs-border-radius-sm: 8px !important;
            --bs-border-radius-lg: 8px !important;
            --bs-border-radius-xl: 8px !important;
            --bs-border-radius-pill: 8px !important;
        }
        
        body, input, button, select, textarea, .brand-title, .page-title, .navbar-brand, table, th, td, p, span:not([class*="fa"]):not([class*="icon"]):not(.fas):not(.far):not(.fab):not(.fa-solid):not(.fa-regular):not(.fa-brands), a:not([class*="fa"]):not(.fas):not(.far):not(.fab), label, li, small, dt, dd {
            font-family: "Roboto", sans-serif !important;
        }

        body {
            font-size: var(--font-size-base);
            background-color: #f8fafc;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }

        /* Allow bold tags to be bold (descriptions, rich content, emphasis) */
        b, strong, .smart-visual-editor b, .smart-visual-editor strong, .smart-preview-container b, .smart-preview-container strong {
            font-weight: 700 !important;
        }

        /* Standardize headings, badges, and buttons to clean modern weight */
        .fw-bolder, .fw-extrabold, h1, h2, h3, h4, h5, h6, .brand-title, .page-title, .navbar-brand, th, dt, .btn, .nav-link, .stat-value-pro, .order-ref-badge, .font-heading, .brand-subtitle strong, .stat-kpi-value, .stat-sub-text strong, .card-title, .stat-title, .badge {
            font-weight: 400 !important;
        }

        /* Protect FontAwesome icon font and solid weights */
        .fa, .fas, .fa-solid {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
        }
        .far, .fa-regular {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 400 !important;
        }
        .fab, .fa-brands {
            font-family: "Font Awesome 6 Brands" !important;
            font-weight: 400 !important;
        }

        /* Universal 8px Border Radius System */
        [class*="rounded"],
        .rounded,
        .rounded-1,
        .rounded-2,
        .rounded-3,
        .rounded-4,
        .rounded-5,
        .rounded-pill,
        .btn,
        .badge,
        .card,
        .card-header,
        .card-footer,
        .modal-content,
        .dropdown-menu,
        .alert,
        input,
        select,
        textarea,
        .form-control,
        .form-select,
        .input-group,
        .input-group-text,
        .page-link,
        .nav-pills .nav-link,
        .stat-card-pro,
        .table-responsive {
            border-radius: 8px !important;
        }

        .badge-dot,
        .status-dot,
        .live-dot {
            border-radius: 50% !important;
        }

        /* Custom Product Tag Badge System (Proportional to Card Size via cqw) */
        .product-card-tag-container {
            container-type: inline-size;
        }
        .product-card-tag-badge {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: calc(clamp(2.5px, 1.3cqw, 5px) * var(--tag-scale, 1)) !important;
            padding: calc(clamp(2.5px, 1.4cqw, 5.5px) * var(--tag-scale, 1)) calc(clamp(6px, 3.2cqw, 11px) * var(--tag-scale, 1)) !important;
            font-size: calc(clamp(0.54rem, 3.9cqw, 0.78rem) * var(--tag-scale, 1)) !important;
            font-weight: var(--tag-weight, 800) !important;
            text-transform: var(--tag-transform, uppercase) !important;
            letter-spacing: 0.04em !important;
            line-height: 1.15 !important;
            background: var(--tag-bg, #dc2626) !important;
            color: var(--tag-color, #ffffff) !important;
            border: var(--tag-border, none) !important;
            border-radius: var(--tag-radius, 6px) !important;
            box-shadow: none !important;
            backdrop-filter: var(--tag-backdrop, none) !important;
            -webkit-backdrop-filter: var(--tag-backdrop, none) !important;
            white-space: nowrap !important;
            user-select: none !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }
        .product-card-tag-badge i {
            font-size: 0.92em !important;
            line-height: 1 !important;
        }
        .product-card-tag-badge.tag-pulse {
            animation: productTagPulse 2.2s infinite ease-in-out;
        }
        @keyframes productTagPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* ── High-Priority Navbar Action Buttons (Top Bar) ── */
        .navbar-user {
            margin-left: auto !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            position: relative !important;
        }

        .nav-btn-pro {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 7px !important;
            height: 36px !important;
            padding: 0 14px !important;
            border-radius: 9999px !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #334155 !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05) !important;
            cursor: pointer !important;
            line-height: 1 !important;
            outline: none !important;
            user-select: none;
            white-space: nowrap !important;
            margin: 0 !important;
        }

        .nav-btn-pro:hover {
            color: #0f172a !important;
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.08) !important;
        }

        .nav-btn-shop {
            border-color: #cbd5e1 !important;
        }

        .nav-btn-shop .nav-live-pulse {
            width: 8px !important;
            height: 8px !important;
            border-radius: 50% !important;
            background: #10b981 !important;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: navLivePulse 2s infinite ease-out;
            display: inline-block !important;
            flex-shrink: 0 !important;
        }

        @keyframes navLivePulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .nav-btn-shop i {
            font-size: 11px !important;
            color: #64748b !important;
            transition: transform 0.15s, color 0.15s;
        }

        .nav-btn-shop:hover i {
            color: #dc2626 !important;
            transform: translate(1px, -1px);
        }

        .nav-btn-guide {
            background: #ffffff !important;
            color: #334155 !important;
            border-color: #cbd5e1 !important;
        }

        .nav-btn-guide i {
            font-size: 13px !important;
            color: #dc2626 !important;
            transition: transform 0.15s;
        }

        .nav-btn-guide:hover i {
            transform: scale(1.15);
        }

        .navbar-user-dropdown {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        .user-avatar {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            border: 2px solid rgba(220, 38, 38, 0.4) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12) !important;
            padding: 0 !important;
            margin: 0 !important;
            outline: none !important;
            line-height: 1 !important;
        }

        .user-avatar:hover {
            transform: translateY(-1px);
            border-color: #dc2626 !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25) !important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <button class="navbar-toggler" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            @php
                $winaLogo = site_logo_url('admin');
                $adminLogoHeight = max(24, min(80, (int) setting('admin_logo_height', 48)));
            @endphp
            @if($winaLogo)
                <img src="{{ $winaLogo }}" alt="{{ setting('app_name', 'WINA SHOP') }}" width="110" height="{{ $adminLogoHeight }}" style="height: {{ $adminLogoHeight }}px; max-width: 180px; object-fit: contain; margin-right: 10px;">
            @else
                <i class="fas fa-camera text-danger me-2"></i>
                <span style="font-weight: 800; color: #0f172a;">WINA<span style="color: #dc2626;">SHOP</span></span>
            @endif
        </a>
        
        <div class="navbar-user d-flex align-items-center gap-2">
            <!-- Link to Frontend -->
            <a href="{{ route('home') }}" target="_blank" class="nav-btn-pro nav-btn-shop d-none d-sm-inline-flex align-items-center text-decoration-none" title="Voir la boutique en direct">
                <span class="nav-live-pulse"></span>
                <span>Boutique en direct</span>
                <i class="fas fa-arrow-up-right-from-square"></i>
            </a>

            <!-- Help/Tour Button -->
            <button type="button" onclick="startTour()" class="nav-btn-pro nav-btn-guide d-none d-md-inline-flex align-items-center" title="Guide interactif">
                <i class="fas fa-circle-question"></i>
                <span>Guide</span>
            </button>

            <div class="dropdown navbar-user-dropdown">
                <button type="button" 
                        class="user-avatar" 
                        id="user-menu-trigger" 
                        data-bs-toggle="dropdown" 
                        data-bs-display="static" 
                        data-bs-auto-close="true"
                        aria-expanded="false" 
                        title="{{ auth()->user()?->name ?? 'Compte' }}">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                </button>
                <div class="dropdown-menu dropdown-menu-end user-dropdown" 
                     id="user-dropdown-menu" 
                     aria-labelledby="user-menu-trigger">
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-name">{{ auth()->user()?->name ?? 'Admin' }}</div>
                        <div class="user-dropdown-email text-truncate" style="max-width: 220px;">{{ auth()->user()?->email ?? '' }}</div>
                        @if(auth()->user()?->role)
                        <div class="mt-1">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.72rem; padding: 2px 6px;">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('profile.show') }}" class="user-dropdown-item">
                        <i class="fas fa-user text-muted me-2"></i>
                        <span>Mon Profil</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                        <i class="fas fa-user-pen text-muted me-2"></i>
                        <span>Modifier le profil</span>
                    </a>
                    <div class="dropdown-divider my-1 border-top" style="opacity: 0.15;"></div>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="user-dropdown-item text-danger" style="width: 100%;">
                            <i class="fas fa-arrow-right-from-bracket text-danger me-2"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item">
                <a href="{{ route('dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Tableau de bord</span>
                </a>
            </li>
            @if(auth()->check() && (auth()->user()->can('view_analytics') || auth()->user()->hasRole('Admin')))
            <li class="sidebar-menu-item">
                <a href="{{ route('analytics.views') }}" class="sidebar-menu-link {{ request()->routeIs('analytics.views*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Vues & Recherches</span>
                </a>
            </li>
            @endif
            @can('manage_categories')
            <li class="sidebar-menu-item">
                <a href="{{ route('categories.index') }}" class="sidebar-menu-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-folder-tree"></i>
                    <span>Catégories</span>
                </a>
            </li>
            @endcan
            @can('manage_products')
            <li class="sidebar-menu-item">
                <a href="{{ route('products.index') }}" class="sidebar-menu-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('brands.index') }}" class="sidebar-menu-link {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Marques</span>
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('product-tags.index') }}" class="sidebar-menu-link {{ request()->routeIs('product-tags.*') ? 'active' : '' }}">
                    <i class="fas fa-bolt"></i>
                    <span>Tags & Badges</span>
                </a>
            </li>
            @endcan
            @can('manage_orders')
            <li class="sidebar-menu-item">
                <a href="{{ route('orders.index') }}" class="sidebar-menu-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Commandes</span>
                </a>
            </li>
            @endcan
            @can('manage_invoices')
            <li class="sidebar-menu-item">
                <a href="{{ route('invoices.index') }}" class="sidebar-menu-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Factures & Devis</span>
                </a>
            </li>
            @endcan
            @can('manage_inventory')
            <li class="sidebar-menu-item">
                <a href="{{ route('inventory.index') }}" class="sidebar-menu-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                    <span>Inventaire & Stock</span>
                </a>
            </li>
            @endcan
            @can('manage_customers')
            <li class="sidebar-menu-item">
                <a href="{{ route('customers.index') }}" class="sidebar-menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Clients</span>
                </a>
            </li>
            @endcan
            @can('manage_reviews')
            <li class="sidebar-menu-item">
                <a href="{{ route('reviews.index') }}" class="sidebar-menu-link {{ request()->routeIs('reviews.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Avis clients</span>
                </a>
            </li>
            @endcan

            <li class="sidebar-menu-item">
                <a href="{{ route('banners.index') }}" class="sidebar-menu-link {{ request()->routeIs('banners.*') ? 'active' : '' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Slider & Bannières</span>
                </a>
            </li>

            @can('manage_settings')
            <li class="sidebar-menu-item">
                <a href="{{ route('settings.logos-icons') }}" class="sidebar-menu-link {{ request()->routeIs('settings.logos-icons*') ? 'active' : '' }}">
                    <i class="fas fa-icons"></i>
                    <span>Logos & Icônes</span>
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('settings.index') }}" class="sidebar-menu-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </li>
            @endcan
            
            <!-- Developer Credit -->
            <li class="sidebar-menu-item mt-auto pt-4 pb-2 px-4 text-center">
                <hr class="opacity-25 mb-3">
                <div class="small text-muted" style="font-size: 0.75rem;">
                    Développé par<br>
                    <a href="https://elegantboost.com/" target="_blank" class="text-primary text-decoration-none fw-bold">Elegant Boost</a>
                </div>
            </li>
        </ul>
    </aside>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop"></div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <!-- SweetAlert Helpers -->
    <script src="{{ asset('js/sweetalert-helpers.js') }}"></script>
    <!-- Delete Confirmation Handler -->
    <script src="{{ asset('js/delete-confirmation.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SortableJS for Touch & Mobile Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/driver.js') }}"></script>
    <script src="{{ asset('js/tour.js') }}"></script>
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

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif

        // Initialize Bootstrap dropdown
        document.addEventListener('DOMContentLoaded', function () {
            const trigger = document.getElementById('user-menu-trigger');
            if (trigger && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                bootstrap.Dropdown.getOrCreateInstance(trigger);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
