<!DOCTYPE html>
<html lang="{{ setting('language', 'en') }}" dir="{{ setting('text_direction', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ setting('app_name', 'E-commerce') }}</title>
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
                <span class="d-none d-md-inline">Help & Guide</span>
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
                    My Profile
                </a>
                <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                    <i class="fas fa-edit"></i>
                    Edit Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="user-dropdown-item" style="width: 100%;">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item">
                <a href="{{ route('dashboard') }}" class="sidebar-menu-link active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @can('manage_orders')
            <li class="sidebar-menu-item">
                <a href="{{ route('pos.index') }}" class="sidebar-menu-link" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                    <i class="fas fa-cash-register"></i>
                    <span>POS Terminal</span>
                </a>
            </li>
            @endcan
            @can('manage_products')
            <li class="sidebar-menu-item">
                <a href="{{ route('products.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            @endcan
            @can('manage_categories')
            <li class="sidebar-menu-item">
                <a href="{{ route('categories.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-folder-tree"></i>
                    <span>Categories</span>
                </a>
            </li>
            @endcan
            @can('manage_orders')
            <li class="sidebar-menu-item">
                <a href="{{ route('orders.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            @endcan
            @can('manage_invoices')
            <li class="sidebar-menu-item">
                <a href="{{ route('invoices.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-file-invoice"></i>
                    <span>Invoices</span>
                </a>
            </li>
            @endcan
            @can('manage_customers')
            <li class="sidebar-menu-item">
                <a href="{{ route('customers.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('debtors.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Debtors</span>
                </a>
            </li>
            @endcan
            @can('manage_inventory')
            <li class="sidebar-menu-item">
                <a href="{{ route('inventory.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                </a>
            </li>
            @endcan
            @can('manage_coupons')
            <li class="sidebar-menu-item">
                <a href="{{ route('coupons.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-tags"></i>
                    <span>Coupons</span>
                </a>
            </li>
            @endcan
            
            @can('manage_reviews')
            <li class="sidebar-menu-item">
                <a href="{{ route('reviews.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            @endcan
            
            <!-- Access Control Section (Grouped) -->
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('manage_users') || auth()->user()->can('manage_roles'))
            <li class="sidebar-menu-item sidebar-submenu {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-menu-link" onclick="toggleSubmenu(event)">
                    <i class="fas fa-user-shield"></i>
                    <span>Access Control</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-items">
                    @can('manage_users')
                    <li class="submenu-item">
                        <a href="{{ route('users.index') }}" class="submenu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    @endcan
                    @can('manage_roles')
                    <li class="submenu-item">
                        <a href="{{ route('roles.index') }}" class="submenu-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tag"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                    <li class="submenu-item">
                        <a href="{{ route('permissions.index') }}" class="submenu-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                            <i class="fas fa-key"></i>
                            <span>Permissions</span>
                        </a>
                    </li>
                    @endcan
                    @can('view_activity_logs')
                    <li class="submenu-item">
                        <a href="{{ route('activity-logs.index') }}" class="submenu-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                            <i class="fas fa-history"></i>
                            <span>Activity Logs</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif
            
            @can('view_reports')
            <li class="sidebar-menu-item">
                <a href="{{ route('reports.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Reports</span>
                </a>
            </li>
            @endcan

            @can('manage_accounting')
            <li class="sidebar-menu-item sidebar-submenu {{ request()->is('accounting*') ? 'active' : '' }}">
                <a href="#" class="sidebar-menu-link" onclick="toggleSubmenu(event)">
                    <i class="fas fa-calculator"></i>
                    <span>Accounting</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu-items">
                    <li class="submenu-item">
                        <a href="{{ route('accounting.index') }}" class="submenu-link {{ request()->routeIs('accounting.index') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="submenu-item">
                        <a href="{{ route('accounting.accounts') }}" class="submenu-link {{ request()->routeIs('accounting.accounts') ? 'active' : '' }}">
                            <i class="fas fa-list-ol"></i>
                            <span>Chart of Accounts</span>
                        </a>
                    </li>
                    <li class="submenu-item">
                        <a href="{{ route('accounting.entries') }}" class="submenu-link {{ request()->routeIs('accounting.entries*', 'accounting.entries') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <span>Journal Entries</span>
                        </a>
                    </li>
                    <li class="submenu-item">
                        <a href="{{ route('accounting.reports') }}" class="submenu-link {{ request()->routeIs('accounting.reports*') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            @can('manage_content')
            <li class="sidebar-menu-item">
                <a href="{{ route('pages.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-file-code"></i>
                    <span>Page Manager</span>
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('menus.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-compass"></i>
                    <span>Navigation Menus</span>
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('custom-codes.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-code"></i>
                    <span>Custom Codes</span>
                </a>
            </li>
            @endcan

            @can('manage_settings')
            <li class="sidebar-menu-item">
                <a href="{{ route('settings.index') }}" class="sidebar-menu-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            @endcan
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
