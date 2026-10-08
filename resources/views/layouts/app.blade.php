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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css">
    
    <!-- Dynamic Theme CSS Variables -->
    <style>
        :root {
            --primary-color: {{ setting('primary_color', '#3b82f6') }};
            --secondary-color: {{ setting('secondary_color', '#10b981') }};
            --accent-color: {{ setting('accent_color', '#8b5cf6') }};
            --success-color: {{ setting('success_color', '#10b981') }};
            --warning-color: {{ setting('warning_color', '#f59e0b') }};
            --danger-color: {{ setting('danger_color', '#ef4444') }};
            --font-family: {{ setting('font_family', 'Inter, system-ui, sans-serif') }};
            --font-size-base: {{ setting('font_size_base', '14') }}px;
            --border-radius: {{ setting('border_radius', '12') }}px;
        }
        
        body {
            font-family: var(--font-family);
            font-size: var(--font-size-base);
        }
    </style>
    
    <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <button class="navbar-toggler">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            @if(setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="{{ setting('app_name') }}" style="height: 32px; margin-right: 8px;">
            @else
                <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i>
            @endif
            {{ setting('app_name', 'E-commerce') }}
        </a>
        
        <div class="navbar-user">
            <!-- Help/Tour Button -->
            <button onclick="startTour()" class="btn btn-sm btn-outline-primary" style="margin-right: 12px; border-radius: 20px; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-question-circle"></i>
                <span class="d-none d-md-inline">Aide & Guide</span>
            </button>

            <div class="user-avatar" id="user-menu-trigger">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="user-dropdown">
                <div class="user-dropdown-header">
                    <div class="user-dropdown-name">{{ auth()->user()->name }}</div>
                    <div class="user-dropdown-email">{{ auth()->user()->email }}</div>
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
