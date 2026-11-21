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
    <script>
        (function () {
            try {
                if (window.innerWidth >= 992 && localStorage.getItem('sidebarCollapsed') === 'true') {
                    document.documentElement.classList.add('sidebar-precollapsed');
                }
            } catch (e) {}
        })();
    </script>
    <div id="spa-page-styles">
        @stack('styles')
    </div>
    
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

        /* Zero Horizontal Scroll Guarantee */
        html {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }
        body {
            font-size: var(--font-size-base);
            background-color: #f8fafc;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden !important;
            max-width: 100% !important;
            width: 100% !important;
            position: relative;
        }
        .main-content {
            overflow-x: hidden !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Allow bold tags to be bold (descriptions, rich content, emphasis) */
        b, strong, .smart-visual-editor b, .smart-visual-editor strong, .smart-preview-container b, .smart-preview-container strong {
            font-weight: 700 !important;
        }

        /* Standardize headings, badges, and text to clean modern weight, preserve button weights */
        .fw-bolder, .fw-extrabold, h1, h2, h3, h4, h5, h6, .brand-title, .page-title, .navbar-brand, th, dt, .nav-link, .stat-value-pro, .order-ref-badge, .font-heading, .brand-subtitle strong, .stat-kpi-value, .stat-sub-text strong, .card-title, .stat-title, .badge {
            font-weight: 400 !important;
        }

        .btn-brand-primary,
        .btn-primary {
            font-weight: 600 !important;
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

        /* Pro Executive Sidebar Section Headers (Collapsible) */
        .sidebar-section-header {
            font-size: 0.64rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
            color: #94a3b8 !important;
            padding: 0.68rem 0.65rem 0.3rem 0.65rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.45rem !important;
            user-select: none !important;
            margin: 0 !important;
            cursor: pointer !important;
            border-radius: 6px !important;
            transition: color 0.15s ease, background-color 0.15s ease !important;
        }
        .sidebar-section-header:hover {
            color: #475569 !important;
            background: rgba(15, 23, 42, 0.03) !important;
        }
        .sidebar-section-header::after {
            content: '' !important;
            flex: 1 !important;
            height: 1px !important;
            background: #e2e8f0 !important;
        }
        .sidebar-section-chevron {
            font-size: 0.58rem !important;
            color: #94a3b8 !important;
            transition: transform 0.2s ease !important;
            order: 2 !important;
            flex-shrink: 0 !important;
        }
        .sidebar-section-header.is-collapsed .sidebar-section-chevron {
            transform: rotate(-90deg) !important;
        }
        .sidebar-section-header.is-search-hidden {
            display: none !important;
        }
        @media (min-width: 992px) {
            html.sidebar-precollapsed .sidebar .sidebar-section-header,
            .sidebar.collapsed .sidebar-section-header {
                font-size: 0 !important;
                height: 1px !important;
                padding: 0 !important;
                margin: 8px 16px !important;
                background: #e2e8f0 !important;
                pointer-events: none !important;
            }
            html.sidebar-precollapsed .sidebar .sidebar-section-header::after,
            .sidebar.collapsed .sidebar-section-header::after,
            html.sidebar-precollapsed .sidebar .sidebar-section-chevron,
            .sidebar.collapsed .sidebar-section-chevron {
                display: none !important;
            }
        }

        /* Global Synchronized Subnav Switcher Pills */
        .subnav-pills-bar {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }
        .subnav-pill {
            white-space: nowrap;
            border-radius: 9999px !important;
            padding: 0.38rem 0.9rem;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            flex-shrink: 0;
        }
        .subnav-pill:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .subnav-pill.active {
            background: #0f172a;
            color: #ffffff !important;
            border-color: #0f172a;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
        }
        .subnav-pill.active i {
            color: #ffffff !important;
        }
        .subnav-pill.active .badge {
            background: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
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
        .navbar {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .navbar-user {
            margin-left: auto !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 10px !important;
            position: relative !important;
            flex-shrink: 0 !important;
        }

        .nav-btn-pro {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 36px;
            padding: 0 14px;
            border-radius: 9999px;
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            cursor: pointer;
            line-height: 1;
            outline: none;
            user-select: none;
            white-space: nowrap;
            margin: 0;
        }

        .nav-btn-pro:hover {
            color: #0f172a;
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.08);
        }

        .nav-btn-shop {
            border-color: #cbd5e1;
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
            background: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }

        .nav-btn-guide i {
            font-size: 13px !important;
            color: #dc2626 !important;
            transition: transform 0.15s;
        }

        .nav-btn-guide:hover i {
            transform: scale(1.15);
        }

        .nav-btn-pos {
            background: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }

        .nav-btn-pos i {
            font-size: 13px !important;
            color: #dc2626 !important;
            transition: transform 0.15s;
        }

        .nav-btn-pos:hover i {
            transform: scale(1.12);
        }

        .nav-btn-pos.active {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
        }

        .nav-btn-pos.active i {
            color: #ffffff !important;
        }

        .nav-btn-pos.active:hover {
            background: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        .navbar-user-dropdown {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
            flex-shrink: 0 !important;
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
            flex-shrink: 0 !important;
        }

        .user-avatar:hover {
            transform: translateY(-1px);
            border-color: #dc2626 !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25) !important;
        }

        /* ── Mobile Navbar Executive Polish (Touch-First) ── */
        .nav-btn-shop-mobile {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            padding: 0 !important;
            border-radius: 10px !important;
            position: relative !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            display: inline-flex;
            align-items: center !important;
            justify-content: center !important;
            text-decoration: none !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            flex-shrink: 0 !important;
            margin: 0 !important;
        }

        .nav-btn-shop-mobile i {
            font-size: 14px !important;
            color: #475569 !important;
            transition: transform 0.15s, color 0.15s;
        }

        .nav-btn-shop-mobile:active {
            transform: scale(0.95);
            background: #f8fafc !important;
        }

        .nav-btn-shop-mobile .nav-live-pulse {
            width: 7px !important;
            height: 7px !important;
            border-radius: 50% !important;
            background: #10b981 !important;
            position: absolute !important;
            top: 5px !important;
            right: 5px !important;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: navLivePulse 2s infinite ease-out;
            display: inline-block !important;
        }

        /* ── Strict Responsive Visibility: Phone vs Desktop ── */
        @media (max-width: 991.98px) {
            .navbar-brand {
                display: none !important;
            }
            .nav-btn-shop,
            .nav-btn-guide {
                display: none !important;
            }
            .nav-btn-shop-mobile {
                display: inline-flex !important;
            }
        }

        @media (min-width: 992px) {
            .navbar-brand {
                display: inline-flex !important;
            }
            .nav-btn-shop-mobile {
                display: none !important;
            }
            .nav-btn-shop {
                display: inline-flex !important;
            }
            .nav-btn-guide {
                display: inline-flex !important;
            }
        }

        @media (max-width: 991.98px) {
            :root {
                --navbar-height: 60px !important;
            }

            .navbar {
                height: 60px !important;
                padding: 0 12px !important;
                background: rgba(255, 255, 255, 0.98) !important;
                backdrop-filter: blur(12px) !important;
                -webkit-backdrop-filter: blur(12px) !important;
                border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05) !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 1000 !important;
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                justify-content: space-between !important;
                overflow: visible !important;
            }

            .navbar-toggler {
                width: 38px !important;
                height: 38px !important;
                min-width: 38px !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                background: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 10px !important;
                color: #1e293b !important;
                font-size: 15px !important;
                margin-right: 8px !important;
                transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
                flex-shrink: 0 !important;
            }

            .navbar-toggler:active,
            body.sidebar-open .navbar-toggler {
                background: #fee2e2 !important;
                border-color: #fca5a5 !important;
                color: #dc2626 !important;
                transform: scale(0.95);
            }

            /* Clean mobile navbar: logo is intentionally hidden from top bar on phones */
            .navbar-brand {
                display: none !important;
            }

            .navbar-user {
                gap: 8px !important;
                margin-left: auto !important;
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                flex-shrink: 0 !important;
            }

            .user-avatar {
                width: 36px !important;
                height: 36px !important;
                font-size: 13px !important;
                border-radius: 10px !important;
            }

            .dropdown-menu.user-dropdown {
                position: absolute !important;
                top: calc(100% + 8px) !important;
                right: 0 !important;
                left: auto !important;
                width: 260px !important;
                max-width: calc(100vw - 20px) !important;
                border-radius: 14px !important;
                border: 1px solid rgba(226, 232, 240, 0.9) !important;
                box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.16) !important;
                overflow: hidden !important;
            }

            .sidebar {
                top: 60px !important;
                height: calc(100dvh - 60px) !important;
                width: 280px !important;
                max-width: 85vw !important;
                box-shadow: 4px 0 24px rgba(15, 23, 42, 0.15) !important;
            }

            .sidebar-backdrop {
                top: 60px !important;
            }

            .main-content {
                margin-top: 60px !important;
            }
        }

        /* ── Admin Notification Bell & Popover Center ── */
        .navbar-notif-dropdown {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
            flex-shrink: 0 !important;
        }

        .nav-notif-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            padding: 0;
        }

        .nav-notif-btn:hover {
            border-color: #94a3b8;
            background: #f8fafc;
            color: #0f172a;
            transform: translateY(-1px);
        }

        .nav-notif-btn.has-critical {
            border-color: #fecaca;
            background: #fef2f2;
            color: #dc2626;
        }

        .nav-notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 19px;
            height: 19px;
            padding: 0 5px;
            border-radius: 999px;
            background: #dc2626;
            color: #ffffff;
            border: 2px solid #ffffff;
            font-size: 0.65rem;
            font-weight: 800;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35);
        }

        .nav-notif-btn.has-critical .nav-notif-badge {
            animation: notifPulse 2s infinite;
        }

        @keyframes notifPulse {
            0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.6); }
            70% { box-shadow: 0 0 0 7px rgba(220, 38, 38, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
        }

        .dropdown-menu.nav-notif-dropdown-menu {
            width: 400px !important;
            max-width: calc(100vw - 24px) !important;
            padding: 0 !important;
            margin-top: 10px !important;
            border-radius: 16px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.22) !important;
            overflow: hidden !important;
            background: #ffffff !important;
        }

        .notif-drop-header {
            padding: 13px 16px 10px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
        }

        .notif-tabs-bar {
            display: flex;
            gap: 5px;
            padding: 8px 12px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .notif-tabs-bar::-webkit-scrollbar {
            display: none;
        }

        .notif-tab-btn {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.72rem;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .notif-tab-btn:hover {
            border-color: #94a3b8;
            color: #0f172a;
        }

        .notif-tab-btn.active {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
        }

        .notif-drop-list {
            max-height: 390px;
            overflow-y: auto;
            padding: 8px;
            background: #ffffff;
        }

        .notif-drop-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px;
            border-radius: 12px;
            border: 1px solid transparent;
            margin-bottom: 6px;
            background: #ffffff;
            transition: all 0.15s ease;
        }

        .notif-drop-item.is-unread {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .notif-drop-item.priority-critical {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .notif-drop-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }

        .notif-icon-box {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.85rem;
        }

        .notif-mini-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            font-size: 0.68rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .notif-mini-action:hover {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
        }

        .notif-mini-action.is-primary {
            background: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
        }

        .notif-mini-action.is-primary:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        @media (max-width: 575.98px) {
            .nav-notif-btn {
                width: 36px;
                height: 36px;
            }
            .dropdown-menu.nav-notif-dropdown-menu {
                position: fixed !important;
                left: 10px !important;
                right: 10px !important;
                top: 64px !important;
                width: auto !important;
                max-width: none !important;
                margin-top: 0 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="d-flex align-items-center gap-2 flex-nowrap flex-shrink-0">
            <button type="button" class="navbar-toggler" aria-label="Basculer le menu" aria-controls="admin-sidebar" aria-expanded="true" id="navbar-menu-toggler" title="Réduire / Ouvrir le menu (Ctrl+B)">
                <i class="fas fa-bars" id="navbar-toggler-icon"></i>
            </button>
            <!-- Desktop Brand Logo (Hidden on mobile <992px to keep mobile navbar clean) -->
            <a href="{{ route('dashboard') }}" class="navbar-brand d-none d-lg-inline-flex align-items-center ms-2 text-decoration-none">
                @php
                    $winaLogo = site_logo_url('admin');
                    $adminLogoHeight = max(24, min(80, (int) setting('admin_logo_height', 48)));
                @endphp
                @if($winaLogo)
                    <img src="{{ $winaLogo }}" alt="{{ setting('app_name', 'Wina Shop') }}" height="{{ $adminLogoHeight }}" style="height: {{ $adminLogoHeight }}px; max-width: 160px; object-fit: contain;" onerror="this.onerror=null; this.src='{{ asset('images/camera/logo.png') }}';">
                @else
                    <i class="fas fa-camera text-danger me-2"></i>
                    <span style="font-weight: 800; color: #0f172a; font-size: 1.25rem;">WINA<span style="color: #dc2626;">SHOP</span></span>
                @endif
            </a>
        </div>
        
        <div class="navbar-user d-flex align-items-center gap-2 flex-nowrap" style="flex-shrink: 0;">
            @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->can('access_pos') || auth()->user()->can('manage_orders')))
            <!-- Mobile POS Button (visible on <992px) -->
            <a href="{{ route('pos.index') }}" class="nav-btn-shop-mobile d-inline-flex d-lg-none {{ request()->routeIs('pos.*') ? 'text-white bg-danger border-danger' : '' }}" title="Caisse POS">
                <i class="fas fa-cash-register {{ request()->routeIs('pos.*') ? 'text-white' : '' }}"></i>
            </a>

            <!-- Desktop POS Button (visible on >=992px) -->
            <a href="{{ route('pos.index') }}" class="nav-btn-pro nav-btn-pos d-none d-lg-inline-flex {{ request()->routeIs('pos.*') ? 'active' : '' }}" title="Ouvrir la caisse POS">
                <i class="fas fa-cash-register"></i>
                <span>POS</span>
            </a>
            @endif

            <!-- Mobile Store Button: Icon + Pulse (visible on <992px) -->
            <a href="{{ route('home') }}" target="_blank" class="nav-btn-shop-mobile d-inline-flex d-lg-none" title="Voir la boutique en direct">
                <span class="nav-live-pulse"></span>
                <i class="fas fa-store"></i>
            </a>

            <!-- Desktop Store Button: Full Pill (visible on >=992px) -->
            <a href="{{ route('home') }}" target="_blank" class="nav-btn-pro nav-btn-shop d-none d-lg-inline-flex" title="Voir la boutique en direct">
                <span class="nav-live-pulse"></span>
                <span>Boutique en direct</span>
                <i class="fas fa-arrow-up-right-from-square"></i>
            </a>

            <!-- Desktop Help/Tour Button (visible on >=992px) -->
            <button type="button" onclick="startTour()" class="nav-btn-pro nav-btn-guide d-none d-lg-inline-flex" title="Guide interactif">
                <i class="fas fa-circle-question"></i>
                <span>Guide</span>
            </button>

            @php
                $canSeeAdminNotifs = auth()->check() && app(\App\Services\AdminNotificationService::class)->canAccessAdminNotifications(auth()->user());
            @endphp
            @if($canSeeAdminNotifs)
            <div class="dropdown navbar-notif-dropdown" id="navbar-notif-dropdown">
                <button type="button"
                        class="nav-notif-btn"
                        id="notif-menu-trigger"
                        data-bs-toggle="dropdown"
                        data-bs-display="static"
                        data-bs-auto-close="outside"
                        aria-expanded="false"
                        aria-label="Notifications et rappels"
                        title="Notifications & Rappels">
                    <i class="fas fa-bell"></i>
                    <span class="nav-notif-badge d-none" id="nav-notif-badge">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end nav-notif-dropdown-menu"
                     id="notif-dropdown-menu"
                     aria-labelledby="notif-menu-trigger">
                    <div class="notif-drop-header">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <span class="fw-bold" style="font-size: 0.9rem;">Notifications & Rappels</span>
                                <span class="badge rounded-pill bg-danger" id="nav-notif-header-pill" style="font-size: 0.66rem;">0</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                <button type="button"
                                        class="btn btn-sm btn-danger rounded-pill px-2 py-0.5 fw-semibold d-inline-flex align-items-center gap-1"
                                        style="font-size: 0.69rem;"
                                        onclick="window.WinaNotifications?.openReminderModal()">
                                    <i class="fas fa-plus"></i><span>Rappel</span>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-light rounded-pill px-2 py-0.5 fw-semibold"
                                        style="font-size: 0.69rem;"
                                        onclick="window.WinaNotifications?.markAllRead()">
                                    Tout lu
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="notif-tabs-bar" id="nav-notif-tabs">
                        <button type="button" class="notif-tab-btn active" data-tab="all" onclick="window.WinaNotifications?.setTab('all', this)">Tout</button>
                        <button type="button" class="notif-tab-btn" data-tab="credit" onclick="window.WinaNotifications?.setTab('credit', this)">Crédits</button>
                        <button type="button" class="notif-tab-btn" data-tab="orders" onclick="window.WinaNotifications?.setTab('orders', this)">Commandes</button>
                        <button type="button" class="notif-tab-btn" data-tab="reminders" onclick="window.WinaNotifications?.setTab('reminders', this)">Rappels</button>
                        <button type="button" class="notif-tab-btn" data-tab="other" onclick="window.WinaNotifications?.setTab('other', this)">Autres</button>
                    </div>
                    <div class="notif-drop-list" id="nav-notif-list">
                        <div class="text-center text-muted py-4 small">
                            <i class="fas fa-spinner fa-spin me-1"></i> Chargement...
                        </div>
                    </div>
                    <div class="border-top bg-light px-3 py-2 d-flex align-items-center justify-content-between">
                        <span class="text-muted" id="nav-notif-footer-meta" style="font-size: 0.72rem;">Suivi en temps réel</span>
                        <a href="{{ route('notifications.index') }}" class="text-danger fw-bold text-decoration-none" style="font-size: 0.76rem;" onclick="window.WinaNotifications?.closeDropdown()">
                            Centre complet <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

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
                    <!-- Guide link for mobile screen users -->
                    <button type="button" onclick="startTour()" class="user-dropdown-item d-lg-none border-0 bg-transparent text-start w-100" style="cursor: pointer;">
                        <i class="fas fa-circle-question text-muted me-2"></i>
                        <span>Guide interactif</span>
                    </button>
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
    <aside class="sidebar" id="admin-sidebar" aria-label="Menu principal">
        @php
            $winaLogo = site_logo_url('admin');
        @endphp
        <!-- Mobile Sidebar Drawer Header -->
        <div class="sidebar-mobile-header d-lg-none d-flex align-items-center justify-content-between p-3 border-bottom bg-light flex-shrink-0">
            <div class="d-flex align-items-center gap-2">
                @if($winaLogo)
                    <img src="{{ $winaLogo }}" alt="{{ setting('app_name', 'WINA SHOP') }}" style="height: 28px; width: auto; object-fit: contain;">
                @else
                    <span class="fw-bold text-dark"><i class="fas fa-camera text-danger me-1.5"></i>WINA<span class="text-danger">SHOP</span></span>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-light rounded-circle border" id="sidebar-close-btn" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" aria-label="Fermer le menu">
                <i class="fas fa-xmark text-muted"></i>
            </button>
        </div>

        <!-- Quick Search Filter -->
        <div class="sidebar-search-wrap">
            <div class="sidebar-search-box" id="sidebar-search-box">
                <i class="fas fa-magnifying-glass sidebar-search-icon" aria-hidden="true"></i>
                <input type="text"
                       id="sidebar-quick-search"
                       class="sidebar-search-input"
                       placeholder="Rechercher un menu..."
                       autocomplete="off"
                       aria-label="Rechercher un menu dans la barre latérale">
                <kbd class="sidebar-search-kbd" title="Raccourci clavier">/</kbd>
                <button type="button" id="sidebar-search-clear" class="sidebar-search-clear" aria-label="Effacer la recherche" title="Effacer (Échap)">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <button type="button" id="sidebar-search-trigger" class="sidebar-search-trigger" title="Rechercher un menu (/)" aria-label="Rechercher un menu">
                <i class="fas fa-magnifying-glass"></i>
            </button>
        </div>

        <div class="sidebar-nav-scroll" id="sidebar-nav-scroll">
        <ul class="sidebar-menu" id="sidebar-menu-list">
            <!-- 1. VUE D'ENSEMBLE -->
            <li class="sidebar-section-header" data-section-key="overview" role="button" tabindex="0" aria-expanded="true">
                <span>Vue d'ensemble</span>
                <i class="fas fa-chevron-down sidebar-section-chevron" aria-hidden="true"></i>
            </li>
            @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->can('view_dashboard')))
            <li class="sidebar-menu-item" data-section="overview">
                <a href="{{ route('dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-tooltip="Tableau de bord">
                    <i class="fas fa-home"></i>
                    <span>Tableau de bord</span>
                </a>
            </li>
            @endif
            @if(!empty($canSeeAdminNotifs) || (auth()->check() && app(\App\Services\AdminNotificationService::class)->canAccessAdminNotifications(auth()->user())))
            <li class="sidebar-menu-item" data-section="overview">
                <a href="{{ route('notifications.index') }}" class="sidebar-menu-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" data-tooltip="Notifications & Rappels">
                    <i class="fas fa-bell"></i>
                    <span>Notifications & Rappels</span>
                    <span id="sidebar-notif-badge" class="badge rounded-pill bg-danger ms-auto d-none" style="font-size: 0.65rem;">0</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->canAny(['view_analytics', 'view_analytics_views', 'view_analytics_sales', 'view_analytics_insights', 'view_reports']) || auth()->user()->hasAnyRole(['Admin', 'Super Admin'])))
            <li class="sidebar-menu-item" data-section="overview">
                @php
                    $analyticsRoute = auth()->user()->canAny(['view_analytics', 'view_analytics_views']) || auth()->user()->hasAnyRole(['Admin', 'Super Admin'])
                        ? route('analytics.views')
                        : (auth()->user()->can('view_analytics_sales') ? route('analytics.index')
                        : (auth()->user()->can('view_analytics_insights') ? route('analytics.insights') : route('reports.index')));
                @endphp
                <a href="{{ $analyticsRoute }}" class="sidebar-menu-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}" data-tooltip="Vues & Recherches">
                    <i class="fas fa-chart-line"></i>
                    <span>Vues & Recherches</span>
                </a>
            </li>
            @endif

            <!-- 2. VENTES & CLIENTS -->
            <li class="sidebar-section-header" data-section-key="sales" role="button" tabindex="0" aria-expanded="true">
                <span>Ventes & Clients</span>
                <i class="fas fa-chevron-down sidebar-section-chevron" aria-hidden="true"></i>
            </li>
            @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->canAny(['manage_orders', 'view_orders', 'create_orders', 'edit_orders'])))
            <li class="sidebar-menu-item" data-section="sales">
                <a href="{{ route('orders.index') }}" class="sidebar-menu-link {{ request()->routeIs('orders.*') && !request()->routeIs('orders.pos', 'orders.staff-stats') ? 'active' : '' }}" data-tooltip="Commandes Site Web">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Commandes</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->canAny(['access_pos', 'manage_orders', 'view_orders'])))
            <li class="sidebar-menu-item" data-section="sales">
                <a href="{{ route('orders.pos') }}" class="sidebar-menu-link {{ request()->routeIs('orders.pos') ? 'active' : '' }}" data-tooltip="Commandes POS">
                    <i class="fas fa-receipt"></i>
                    <span>Commandes POS</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->canAny(['access_pos', 'manage_orders', 'view_orders', 'create_orders', 'edit_orders'])))
            <li class="sidebar-menu-item" data-section="sales">
                <a href="{{ route('orders.staff-stats') }}" class="sidebar-menu-link {{ request()->routeIs('orders.staff-stats') ? 'active' : '' }}" data-tooltip="Stats Équipe">
                    <i class="fas fa-user-check"></i>
                    <span>Stats Équipe</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->can('access_pos') || auth()->user()->can('manage_orders')))
            <li class="sidebar-menu-item" data-section="sales">
                <a href="{{ route('pos.index') }}" class="sidebar-menu-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" data-tooltip="Caisse POS">
                    <i class="fas fa-cash-register"></i>
                    <span>Caisse POS</span>
                </a>
            </li>
            @endif
            @can('manage_invoices')
            <li class="sidebar-menu-item" data-section="sales">
                <a href="{{ route('invoices.index') }}" class="sidebar-menu-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" data-tooltip="Factures & Devis">
                    <i class="fas fa-file-invoice"></i>
                    <span>Factures & Devis</span>
                </a>
            </li>
            @endcan
            @can('manage_customers')
            <li class="sidebar-menu-item" data-section="sales">
                <a href="{{ route('customers.index') }}" class="sidebar-menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" data-tooltip="Clients">
                    <i class="fas fa-users"></i>
                    <span>Clients</span>
                </a>
            </li>
            @endcan
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_customers') || auth()->user()->can('manage_client_credits')))
            <li class="sidebar-menu-item" data-section="sales">
                <a href="{{ route('client-credits.index') }}" class="sidebar-menu-link {{ request()->routeIs('client-credits.*') || request()->routeIs('debtors.*') ? 'active' : '' }}" data-tooltip="Crédits & Règlements">
                    <i class="fas fa-wallet"></i>
                    <span>Crédits & Règlements</span>
                </a>
            </li>
            @endif

            <!-- 3. CATALOGUE & STOCKS -->
            <li class="sidebar-section-header" data-section-key="catalog" role="button" tabindex="0" aria-expanded="true">
                <span>Catalogue & Stocks</span>
                <i class="fas fa-chevron-down sidebar-section-chevron" aria-hidden="true"></i>
            </li>
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_products') || auth()->user()->can('view_products')))
            <li class="sidebar-menu-item" data-section="catalog">
                <a href="{{ route('products.index') }}" class="sidebar-menu-link {{ request()->routeIs('products.*') ? 'active' : '' }}" data-tooltip="Produits">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
            </li>
            <li class="sidebar-menu-item" data-section="catalog">
                <a href="{{ route('brands.index') }}" class="sidebar-menu-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" data-tooltip="Marques">
                    <i class="fas fa-tags"></i>
                    <span>Marques</span>
                </a>
            </li>
            <li class="sidebar-menu-item" data-section="catalog">
                <a href="{{ route('product-tags.index') }}" class="sidebar-menu-link {{ request()->routeIs('product-tags.*') ? 'active' : '' }}" data-tooltip="Tags & Badges">
                    <i class="fas fa-bolt"></i>
                    <span>Tags & Badges</span>
                </a>
            </li>
            @endif
            @can('manage_categories')
            <li class="sidebar-menu-item" data-section="catalog">
                <a href="{{ route('categories.index') }}" class="sidebar-menu-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" data-tooltip="Catégories">
                    <i class="fas fa-folder-tree"></i>
                    <span>Catégories</span>
                </a>
            </li>
            @endcan
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_inventory') || auth()->user()->can('view_inventory')))
            <li class="sidebar-menu-item" data-section="catalog">
                <a href="{{ route('inventory.index') }}" class="sidebar-menu-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" data-tooltip="Inventaire & Stock">
                    <i class="fas fa-boxes"></i>
                    <span>Inventaire & Stock</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_inventory') || auth()->user()->can('view_inventory') || auth()->user()->can('manage_stores')))
            <li class="sidebar-menu-item" data-section="catalog">
                <a href="{{ route('stores.index') }}" class="sidebar-menu-link {{ request()->routeIs('stores.*') ? 'active' : '' }}" data-tooltip="Magasins & Dépôts">
                    <i class="fas fa-warehouse"></i>
                    <span>Magasins & Dépôts</span>
                </a>
            </li>
            @endif

            <!-- 4. FINANCE & GESTION -->
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_accounting') || auth()->user()->can('manage_expenses') || auth()->user()->can('view_profitability') || auth()->user()->can('view_reports')))
            <li class="sidebar-section-header" data-section-key="finance" role="button" tabindex="0" aria-expanded="true">
                <span>Finance & Gestion</span>
                <i class="fas fa-chevron-down sidebar-section-chevron" aria-hidden="true"></i>
            </li>
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('manage_accounting') || auth()->user()->can('manage_expenses') || auth()->user()->can('view_reports'))
            <li class="sidebar-menu-item" data-section="finance">
                <a href="{{ route('expenses.index') }}" class="sidebar-menu-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" data-tooltip="Dépenses & Charges">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Dépenses & Charges</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('view_profitability') || auth()->user()->can('view_reports'))
            <li class="sidebar-menu-item" data-section="finance">
                <a href="{{ route('reports.profitability') }}" class="sidebar-menu-link {{ request()->routeIs('reports.profitability*') || request()->routeIs('shipments.*') || request()->routeIs('accounting.*') ? 'active' : '' }}" data-tooltip="Bénéfices & Marges" data-tooltip-badge="Admin">
                    <i class="fas fa-chart-pie"></i>
                    <span>Bénéfices & Marges</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill ms-auto" style="font-size: 0.62rem;">Admin</span>
                </a>
            </li>
            @endif
            @endif

            <!-- 5. MARKETING & VITRINE -->
            <li class="sidebar-section-header" data-section-key="marketing" role="button" tabindex="0" aria-expanded="true">
                <span>Marketing & Vitrine</span>
                <i class="fas fa-chevron-down sidebar-section-chevron" aria-hidden="true"></i>
            </li>
            @if(auth()->check() && (auth()->user()->can('manage_coupons') || auth()->user()->hasRole('Admin')))
            <li class="sidebar-menu-item" data-section="marketing">
                <a href="{{ route('coupons.index') }}" class="sidebar-menu-link {{ request()->routeIs('coupons.*') ? 'active' : '' }}" data-tooltip="Codes Promo & Affiliation">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Codes Promo & Affiliation</span>
                </a>
            </li>
            @endif
            @can('manage_reviews')
            <li class="sidebar-menu-item" data-section="marketing">
                <a href="{{ route('reviews.index') }}" class="sidebar-menu-link {{ request()->routeIs('reviews.*') ? 'active' : '' }}" data-tooltip="Avis clients">
                    <i class="fas fa-star"></i>
                    <span>Avis clients</span>
                </a>
            </li>
            @endcan
            <li class="sidebar-menu-item" data-section="marketing">
                <a href="{{ route('banners.index') }}" class="sidebar-menu-link {{ request()->routeIs('banners.*') ? 'active' : '' }}" data-tooltip="Slider & Bannières">
                    <i class="fas fa-sliders-h"></i>
                    <span>Slider & Bannières</span>
                </a>
            </li>
            <li class="sidebar-menu-item" data-section="marketing">
                <a href="{{ route('home-sections.index') }}" class="sidebar-menu-link {{ request()->routeIs('home-sections.*') ? 'active' : '' }}" data-tooltip="Sections Accueil">
                    <i class="fas fa-layer-group"></i>
                    <span>Sections Accueil</span>
                </a>
            </li>

            <!-- 6. SYSTÈME & AUDIT -->
            <li class="sidebar-section-header" data-section-key="system" role="button" tabindex="0" aria-expanded="true">
                <span>Système & Audit</span>
                <i class="fas fa-chevron-down sidebar-section-chevron" aria-hidden="true"></i>
            </li>
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_users') || auth()->user()->can('manage_roles')))
            <li class="sidebar-menu-item" data-section="system">
                <a href="{{ route('users.index') }}" class="sidebar-menu-link {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'active' : '' }}" data-tooltip="Équipe & Rôles">
                    <i class="fas fa-users-gear"></i>
                    <span>Équipe & Rôles</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('view_activity_logs')))
            <li class="sidebar-menu-item" data-section="system">
                <a href="{{ route('activity-logs.index') }}" class="sidebar-menu-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" data-tooltip="Journal d'Activité">
                    <i class="fas fa-history"></i>
                    <span>Journal d'Activité</span>
                </a>
            </li>
            @endif
            @can('manage_settings')
            <li class="sidebar-menu-item" data-section="system">
                <a href="{{ route('settings.logos-icons') }}" class="sidebar-menu-link {{ request()->routeIs('settings.logos-icons*') ? 'active' : '' }}" data-tooltip="Logos & Icônes">
                    <i class="fas fa-icons"></i>
                    <span>Logos & Icônes</span>
                </a>
            </li>
            <li class="sidebar-menu-item" data-section="system">
                <a href="{{ route('settings.index') }}" class="sidebar-menu-link {{ request()->routeIs('settings.index') ? 'active' : '' }}" data-tooltip="Paramètres">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </li>
            @endcan
        </ul>
        <div class="sidebar-search-empty" id="sidebar-search-empty">
            <i class="fas fa-search text-muted mb-1 d-block"></i>
            <span>Aucun menu trouvé</span>
        </div>
        </div>

        <!-- Sticky Footer Controls & Credit -->
        <div class="sidebar-footer">
            <button type="button" id="sidebar-collapse-btn" class="sidebar-collapse-btn" title="Réduire / Agrandir le menu (Ctrl+B)" aria-label="Réduire ou agrandir le menu latéral">
                <i class="fas fa-angles-left" aria-hidden="true"></i>
                <span>Réduire le menu</span>
            </button>
            <div class="sidebar-footer-credit">
                Développé par <a href="https://elegantboost.com/" target="_blank" class="text-primary text-decoration-none fw-bold">Elegant Boost</a>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop"></div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    @if(auth()->check() && app(\App\Services\AdminNotificationService::class)->canAccessAdminNotifications(auth()->user()))
    <!-- Global Quick Reminder Modal -->
    <div class="modal fade" id="globalReminderModal" tabindex="-1" aria-labelledby="globalReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 text-white py-3 px-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-danger text-white" style="width: 32px; height: 32px;">
                            <i class="fas fa-bell"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="globalReminderModalLabel" style="font-size: 1rem;">Nouveau Rappel / Échéance</h5>
                            <div class="text-white-50" style="font-size: 0.72rem;">Crédit client, commande à suivre ou tâche importante</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form id="globalReminderForm" onsubmit="return window.WinaNotifications?.submitReminder(event);">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="gr_title" class="form-label fw-semibold small text-dark mb-1">Titre du rappel <span class="text-danger">*</span></label>
                            <input type="text" id="gr_title" name="title" class="form-control" maxlength="180" required placeholder="Ex: Relancer client Ahmed pour crédit 2 500 DH">
                        </div>
                        <div class="row g-2.5 mb-3">
                            <div class="col-12 col-sm-6">
                                <label for="gr_category" class="form-label fw-semibold small text-dark mb-1">Catégorie</label>
                                <select id="gr_category" name="category" class="form-select">
                                    <option value="credit">Crédit / Impayé</option>
                                    <option value="order">Commande</option>
                                    <option value="finance">Charge / Facture</option>
                                    <option value="stock">Stock / Arrivage</option>
                                    <option value="general" selected>Général</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="gr_priority" class="form-label fw-semibold small text-dark mb-1">Priorité</label>
                                <select id="gr_priority" name="priority" class="form-select">
                                    <option value="critical">Critique / Urgent</option>
                                    <option value="high" selected>Haute</option>
                                    <option value="medium">Moyenne</option>
                                    <option value="low">Basse</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="gr_due_at" class="form-label fw-semibold small text-dark mb-1">Date & heure de rappel</label>
                            <input type="datetime-local" id="gr_due_at" name="due_at" class="form-control mb-2">
                            <div class="d-flex flex-wrap gap-1.5">
                                <button type="button" class="btn btn-xs btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;" onclick="window.WinaNotifications?.setReminderPreset('today17')">Aujourd'hui 17h</button>
                                <button type="button" class="btn btn-xs btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;" onclick="window.WinaNotifications?.setReminderPreset('tomorrow9')">Demain 09h</button>
                                <button type="button" class="btn btn-xs btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;" onclick="window.WinaNotifications?.setReminderPreset('3days')">Dans 3 jours</button>
                                <button type="button" class="btn btn-xs btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;" onclick="window.WinaNotifications?.setReminderPreset('7days')">Dans 7 jours</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="gr_notes" class="form-label fw-semibold small text-dark mb-1">Notes complémentaires</label>
                            <textarea id="gr_notes" name="notes" rows="2" class="form-control" maxlength="1000" placeholder="Téléphone client, montant exact, détails..."></textarea>
                        </div>
                        <div>
                            <label for="gr_related_url" class="form-label fw-semibold small text-dark mb-1">Lien associé (optionnel)</label>
                            <input type="text" id="gr_related_url" name="related_url" class="form-control form-control-sm" maxlength="255" placeholder="Ex: /orders/123 ou /client-credits">
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light px-4 py-3">
                        <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" id="gr_submit_btn" class="btn btn-danger rounded-pill px-4 fw-semibold">
                            <i class="fas fa-check me-1"></i> Enregistrer le rappel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <!-- SweetAlert Helpers -->
    <script src="{{ asset('js/sweetalert-helpers.js') }}"></script>
    <!-- Delete Confirmation Handler -->
    <script src="{{ asset('js/delete-confirmation.js') }}?v={{ file_exists(public_path('js/delete-confirmation.js')) ? filemtime(public_path('js/delete-confirmation.js')) : time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SortableJS for Touch & Mobile Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="{{ asset('js/app.js') }}?v={{ file_exists(public_path('js/app.js')) ? filemtime(public_path('js/app.js')) : time() }}"></script>
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

        // Initialize Bootstrap dropdown once
        document.addEventListener('DOMContentLoaded', function () {
            if (window.__winaDropdownBooted) return;
            window.__winaDropdownBooted = true;
            const trigger = document.getElementById('user-menu-trigger');
            if (trigger && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                bootstrap.Dropdown.getOrCreateInstance(trigger);
            }
        });
    </script>

    <!-- Real-Time Presence Heartbeat for Dashboard Users -->
    <script>
    (function() {
        var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        var heartbeatUrl = '{{ route("presence.heartbeat") }}';
        var leaveUrl = '{{ route("presence.leave") }}';

        function sendPresence(isLeave) {
            var url = isLeave ? leaveUrl : heartbeatUrl;
            var data = JSON.stringify({
                _token: token,
                url: window.location.href,
                title: document.title
            });

            if (isLeave && navigator.sendBeacon) {
                var blob = new Blob([data], { type: 'application/json' });
                navigator.sendBeacon(url, blob);
                return;
            }

            try {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: data,
                    keepalive: isLeave
                }).catch(function() {});
            } catch(e) {}
        }

        sendPresence(false);

        setInterval(function() {
            if (!document.hidden) {
                sendPresence(false);
            }
        }, 20000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                sendPresence(false);
            }
        });

        window.addEventListener('pagehide', function() {
            sendPresence(true);
        });
        window.addEventListener('beforeunload', function() {
            sendPresence(true);
        });
    })();
    </script>

    @if(auth()->check() && app(\App\Services\AdminNotificationService::class)->canAccessAdminNotifications(auth()->user()))
    <!-- Admin Real-Time Notifications & Reminders Engine -->
    <script>
    (function() {
        if (window.WinaNotifications) return;

        const ROUTES = {
            feed: @json(route('notifications.feed')),
            markRead: @json(route('notifications.read')),
            markAllRead: @json(route('notifications.read-all')),
            snooze: @json(route('notifications.snooze')),
            dismiss: @json(route('notifications.dismiss')),
            restore: @json(route('notifications.restore')),
            remindersStore: @json(route('notifications.reminders.store')),
            completeReminderBase: @json(url('/notifications/reminders')),
            contactReadBase: @json(url('/notifications/contact')),
            stockAckBase: @json(url('/notifications/stock-alert'))
        };

        const state = {
            items: [],
            counts: { unread: 0, critical: 0, total_active: 0 },
            kpis: {},
            activeTab: 'all',
            loading: false,
            toastShownKeys: new Set()
        };

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function escHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        async function postJson(url, payload = {}, method = 'POST') {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: method === 'DELETE' ? null : JSON.stringify(payload)
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return await res.json();
        }

        function updateBadges(counts) {
            state.counts = counts || state.counts;
            const unread = parseInt(state.counts.unread || 0, 10);
            const critical = parseInt(state.counts.critical || 0, 10);

            const navBtn = document.getElementById('notif-menu-trigger');
            const navBadge = document.getElementById('nav-notif-badge');
            const headerPill = document.getElementById('nav-notif-header-pill');
            const sidebarBadge = document.getElementById('sidebar-notif-badge');
            const footerMeta = document.getElementById('nav-notif-footer-meta');

            if (navBtn) {
                navBtn.classList.toggle('has-critical', critical > 0);
            }
            if (navBadge) {
                navBadge.textContent = unread > 99 ? '99+' : String(unread);
                navBadge.classList.toggle('d-none', unread <= 0);
            }
            if (headerPill) {
                headerPill.textContent = `${unread} non lu(s)` + (critical > 0 ? ` • ${critical} urgent(s)` : '');
            }
            if (sidebarBadge) {
                sidebarBadge.textContent = unread > 99 ? '99+' : String(unread);
                sidebarBadge.classList.toggle('d-none', unread <= 0);
            }
            if (footerMeta) {
                const totalActive = parseInt(state.counts.total_active || 0, 10);
                footerMeta.textContent = `${totalActive} alerte(s) active(s)`;
            }
        }

        function filterByTab(items) {
            if (state.activeTab === 'all') return items;
            if (state.activeTab === 'other') {
                return items.filter(i => !['credit', 'orders', 'reminders'].includes(i.category));
            }
            return items.filter(i => i.category === state.activeTab);
        }

        function renderDropdownList() {
            const listEl = document.getElementById('nav-notif-list');
            if (!listEl) return;

            const visible = filterByTab(state.items).slice(0, 18);
            if (!visible.length) {
                listEl.innerHTML = `
                    <div class="text-center py-4 px-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success mb-2" style="width: 44px; height: 44px;">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="fw-bold text-dark small">Tout est à jour !</div>
                        <div class="text-muted" style="font-size: 0.74rem;">Aucune alerte active dans cette catégorie.</div>
                    </div>
                `;
                return;
            }

            listEl.innerHTML = visible.map(item => {
                const bgIcon = `${item.color}18`;
                const prioBadge = item.priority === 'critical'
                    ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.62rem;">URGENT</span>`
                    : (item.due_label ? `<span class="badge bg-light text-dark border" style="font-size: 0.62rem;">${escHtml(item.due_label)}</span>` : '');
                const safeKey = escHtml(item.key);
                let quickBtn = '';
                if (item.quick_action === 'complete_reminder' && item.reminder_id) {
                    quickBtn = `<button type="button" class="notif-mini-action" onclick="window.WinaNotifications.completeReminder(${item.reminder_id})"><i class="fas fa-check"></i> Terminer</button>`;
                } else if (item.quick_action === 'mark_contact_read' && item.target_id) {
                    quickBtn = `<button type="button" class="notif-mini-action" onclick="window.WinaNotifications.markContactRead(${item.target_id}, '${safeKey}')"><i class="fas fa-envelope-open"></i> Lu</button>`;
                } else if (item.quick_action === 'ack_stock_alert' && item.target_id) {
                    quickBtn = `<button type="button" class="notif-mini-action" onclick="window.WinaNotifications.ackStockAlert(${item.target_id}, '${safeKey}')"><i class="fas fa-check"></i> Acquitter</button>`;
                } else if (!item.is_read) {
                    quickBtn = `<button type="button" class="notif-mini-action" onclick="window.WinaNotifications.markRead('${safeKey}', true)"><i class="fas fa-check"></i> Lu</button>`;
                }

                const linkHtml = item.url
                    ? `<a href="${escHtml(item.url)}" class="notif-mini-action is-primary" onclick="window.WinaNotifications.openAlert('${safeKey}')">${escHtml(item.action_label || 'Voir')} →</a>`
                    : '';

                return `
                    <div class="notif-drop-item ${item.is_read ? '' : 'is-unread'} priority-${escHtml(item.priority)}">
                        <div class="notif-icon-box" style="background: ${escHtml(bgIcon)}; color: ${escHtml(item.color)};">
                            <i class="${escHtml(item.icon)}"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center justify-content-between gap-1 mb-0.5">
                                <span class="text-truncate fw-bold text-dark" style="font-size: 0.78rem;" title="${escHtml(item.title)}">${escHtml(item.title)}</span>
                                ${prioBadge}
                            </div>
                            <div class="text-secondary" style="font-size: 0.72rem; line-height: 1.35;">${escHtml(item.message)}</div>
                            <div class="d-flex flex-wrap align-items-center gap-1 mt-1.5">
                                ${linkHtml}
                                ${quickBtn}
                                <button type="button" class="notif-mini-action" onclick="window.WinaNotifications.snooze('${safeKey}', 'tomorrow')" title="Reporter à demain 09h">
                                    <i class="far fa-clock"></i> Demain
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function maybeNotifyCritical(items) {
            let newCritical = null;
            try {
                const seen = JSON.parse(sessionStorage.getItem('wina_seen_critical') || '[]');
                seen.forEach(k => state.toastShownKeys.add(k));
            } catch (e) {}

            for (const item of items) {
                if (item.priority === 'critical' && !item.is_read && !state.toastShownKeys.has(item.key)) {
                    state.toastShownKeys.add(item.key);
                    if (!newCritical) newCritical = item;
                }
            }

            try {
                sessionStorage.setItem('wina_seen_critical', JSON.stringify(Array.from(state.toastShownKeys).slice(-60)));
            } catch (e) {}

            if (newCritical && typeof Toast !== 'undefined') {
                Toast.fire({
                    icon: 'warning',
                    title: `${newCritical.title} (${newCritical.due_label || 'Urgent'})`
                });
            }
        }

        async function refresh(silent = false) {
            if (state.loading) return;
            state.loading = true;
            try {
                const res = await fetch(ROUTES.feed, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const data = await res.json();
                state.items = Array.isArray(data.items) ? data.items : [];
                state.kpis = data.kpis || {};
                updateBadges(data.counts);
                renderDropdownList();
                if (!silent) {
                    maybeNotifyCritical(state.items);
                }
                document.dispatchEvent(new CustomEvent('wina:notifications-updated', { detail: data }));
            } catch (e) {
                // Keep quiet on transient network failures
            } finally {
                state.loading = false;
            }
        }

        function closeDropdown() {
            const trigger = document.getElementById('notif-menu-trigger');
            if (trigger && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                bootstrap.Dropdown.getInstance(trigger)?.hide();
            }
        }

        function formatLocalDatetimeInput(dt) {
            const pad = n => String(n).padStart(2, '0');
            return `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())}T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
        }

        window.WinaNotifications = {
            refresh,
            closeDropdown,
            setTab(tab, btn) {
                state.activeTab = tab;
                document.querySelectorAll('#nav-notif-tabs .notif-tab-btn').forEach(b => b.classList.remove('active'));
                if (btn) btn.classList.add('active');
                renderDropdownList();
            },
            openAlert(alertKey) {
                postJson(ROUTES.markRead, { alert_key: alertKey, read: true }).catch(() => {});
                closeDropdown();
            },
            async markRead(alertKey, read = true) {
                try {
                    const data = await postJson(ROUTES.markRead, { alert_key: alertKey, read: read });
                    updateBadges(data.counts);
                    await refresh(true);
                } catch (e) {}
            },
            async markAllRead() {
                try {
                    const data = await postJson(ROUTES.markAllRead, {});
                    updateBadges(data.counts);
                    await refresh(true);
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: 'Toutes les notifications marquées lues' });
                } catch (e) {}
            },
            async snooze(alertKey, preset, customDate = null) {
                try {
                    const data = await postJson(ROUTES.snooze, { alert_key: alertKey, preset: preset, snoozed_until: customDate });
                    updateBadges(data.counts);
                    await refresh(true);
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'info', title: data.message || 'Rappel reporté' });
                } catch (e) {}
            },
            async dismiss(alertKey) {
                try {
                    const data = await postJson(ROUTES.dismiss, { alert_key: alertKey });
                    updateBadges(data.counts);
                    await refresh(true);
                } catch (e) {}
            },
            async restore(alertKey) {
                try {
                    const data = await postJson(ROUTES.restore, { alert_key: alertKey });
                    updateBadges(data.counts);
                    await refresh(true);
                } catch (e) {}
            },
            async markContactRead(id, alertKey) {
                try {
                    const data = await postJson(`${ROUTES.contactReadBase}/${id}/read`, {});
                    updateBadges(data.counts);
                    await refresh(true);
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: 'Message marqué comme lu' });
                } catch (e) {}
            },
            async ackStockAlert(id, alertKey) {
                try {
                    const data = await postJson(`${ROUTES.stockAckBase}/${id}/acknowledge`, {});
                    updateBadges(data.counts);
                    await refresh(true);
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: 'Alerte stock acquittée' });
                } catch (e) {}
            },
            async completeReminder(id, completed = true) {
                try {
                    const data = await postJson(`${ROUTES.completeReminderBase}/${id}/complete`, { completed: completed });
                    updateBadges(data.counts);
                    await refresh(true);
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: data.message || 'Rappel mis à jour' });
                } catch (e) {}
            },
            async destroyReminder(id) {
                try {
                    const data = await postJson(`${ROUTES.completeReminderBase}/${id}`, {}, 'DELETE');
                    updateBadges(data.counts);
                    await refresh(true);
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: 'Rappel supprimé' });
                } catch (e) {}
            },
            openReminderModal(prefill = {}) {
                closeDropdown();
                const form = document.getElementById('globalReminderForm');
                if (form) form.reset();
                if (prefill.title && document.getElementById('gr_title')) document.getElementById('gr_title').value = prefill.title;
                if (prefill.category && document.getElementById('gr_category')) document.getElementById('gr_category').value = prefill.category;
                if (prefill.priority && document.getElementById('gr_priority')) document.getElementById('gr_priority').value = prefill.priority;
                if (prefill.related_url && document.getElementById('gr_related_url')) document.getElementById('gr_related_url').value = prefill.related_url;

                const modalEl = document.getElementById('globalReminderModal');
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            },
            setReminderPreset(preset) {
                const input = document.getElementById('gr_due_at');
                if (!input) return;
                const d = new Date();
                if (preset === 'today17') {
                    d.setHours(17, 0, 0, 0);
                } else if (preset === 'tomorrow9') {
                    d.setDate(d.getDate() + 1);
                    d.setHours(9, 0, 0, 0);
                } else if (preset === '3days') {
                    d.setDate(d.getDate() + 3);
                    d.setHours(9, 0, 0, 0);
                } else if (preset === '7days') {
                    d.setDate(d.getDate() + 7);
                    d.setHours(9, 0, 0, 0);
                }
                input.value = formatLocalDatetimeInput(d);
            },
            async submitReminder(e) {
                if (e) e.preventDefault();
                const form = document.getElementById('globalReminderForm');
                const btn = document.getElementById('gr_submit_btn');
                if (!form) return false;
                if (btn) btn.disabled = true;

                try {
                    const payload = {
                        title: document.getElementById('gr_title')?.value?.trim() || '',
                        category: document.getElementById('gr_category')?.value || 'general',
                        priority: document.getElementById('gr_priority')?.value || 'high',
                        due_at: document.getElementById('gr_due_at')?.value || null,
                        notes: document.getElementById('gr_notes')?.value?.trim() || null,
                        related_url: document.getElementById('gr_related_url')?.value?.trim() || null
                    };
                    const data = await postJson(ROUTES.remindersStore, payload);
                    updateBadges(data.counts);
                    form.reset();
                    const modalEl = document.getElementById('globalReminderModal');
                    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                    }
                    await refresh(true);
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: data.message || 'Rappel ajouté !' });
                    if (window.location.pathname.startsWith('/notifications')) {
                        window.location.reload();
                    }
                } catch (err) {
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'error', title: 'Impossible d\'enregistrer le rappel.' });
                } finally {
                    if (btn) btn.disabled = false;
                }
                return false;
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            if (!window.__winaNotifBooted) {
                window.__winaNotifBooted = true;
                const notifTrigger = document.getElementById('notif-menu-trigger');
                if (notifTrigger && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                    bootstrap.Dropdown.getOrCreateInstance(notifTrigger);
                }
                refresh(false);
                setInterval(function() {
                    if (!document.hidden) refresh(true);
                }, 60000);
            }
        });

        document.addEventListener('spa:navigated', function() {
            closeDropdown();
            refresh(true);
        });
    })();
    </script>
    @endif
    <div id="spa-page-scripts">
        @stack('scripts')
    </div>

    <!-- Zero-Refresh High-Performance SPA Navigation Engine -->
    <script src="{{ asset('js/spa-navigation.js') }}?v={{ file_exists(public_path('js/spa-navigation.js')) ? filemtime(public_path('js/spa-navigation.js')) : time() }}"></script>
</body>
</html>
