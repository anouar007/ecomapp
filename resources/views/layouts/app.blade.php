<!DOCTYPE html>
<html lang="{{ setting('language', 'en') }}" dir="{{ setting('text_direction', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') - {{ setting('app_name', 'E-commerce') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css">
    
    <!-- Winashop Theme Variables -->
    <style>
        :root {
            --primary-color: #dc2626;
            --primary-dark: #b91c1c;
            --secondary-color: #0f172a;
            --accent-color: #dc2626;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #dc2626;
            --font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --font-size-base: 14px;
            --border-radius: 12px;
        }
        
        body {
            font-family: var(--font-family);
            font-size: var(--font-size-base);
            background-color: #f8fafc;
            color: #0f172a;
        }

        h1, h2, h3, h4, h5, h6, .brand-title, .page-title, .navbar-brand {
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
        }
    </style>
    
    <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <button class="navbar-toggler" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            @php
                $winaLogo = file_exists(public_path('images/camera/logo.png')) ? asset('images/camera/logo.png') : (setting('app_logo') ? asset('storage/' . setting('app_logo')) : null);
            @endphp
            @if($winaLogo)
                <img src="{{ $winaLogo }}" alt="WINA SHOP" style="height: 38px; max-width: 150px; object-fit: contain; margin-right: 10px;">
            @else
                <i class="fas fa-camera text-danger me-2"></i>
                <span style="font-weight: 800; color: #0f172a;">WINA<span style="color: #dc2626;">SHOP</span></span>
            @endif
        </a>
        
        <div class="navbar-user">
            <!-- Link to Frontend -->
            <a href="{{ route('home') }}" target="_blank" class="nav-btn-pro nav-btn-shop d-none d-sm-inline-flex" title="Voir la boutique en direct">
                <span class="nav-live-pulse"></span>
                <span>Boutique en direct</span>
                <i class="fas fa-arrow-up-right-from-square"></i>
            </a>

            <!-- Help/Tour Button -->
            <button onclick="startTour()" class="nav-btn-pro nav-btn-guide d-none d-md-inline-flex" title="Guide interactif">
                <i class="fas fa-circle-question"></i>
                <span>Guide</span>
            </button>

            <div class="user-avatar" id="user-menu-trigger">
                {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
            </div>
            <div class="user-dropdown">
                <div class="user-dropdown-header">
                    <div class="user-dropdown-name">{{ auth()->user()?->name ?? 'Admin' }}</div>
                    <div class="user-dropdown-email">{{ auth()->user()?->email ?? '' }}</div>
                </div>
                <a href="{{ route('profile.show') }}" class="user-dropdown-item">
                    <i class="fas fa-user"></i>
                    Mon Profil
                </a>
                <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                    <i class="fas fa-edit"></i>
                    Modifier le profil
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="user-dropdown-item" style="width: 100%;">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </button>
                </form>
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
                <a href="{{ route('settings.index') }}" class="sidebar-menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
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
    </script>
    @stack('scripts')
</body>
</html>
