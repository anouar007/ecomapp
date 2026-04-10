<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ setting('text_direction', 'ltr') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('maintenance_title', __('We\'ll be back soon!')) }} | {{ setting('app_name', 'E-commerce') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: {{ setting('primary_color', '#3b82f6') }};
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--text-main);
        }

        .maintenance-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: url('{{ setting('maintenance_image') ? asset('storage/' . setting('maintenance_image')) : 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2084&auto=format&fit=crop' }}');
            background-size: cover;
            background-position: center;
            filter: brightness(0.6) blur(2px);
            transition: all 0.5s ease;
        }

        .maintenance-container {
            max-width: 600px;
            width: 90%;
            padding: 48px;
            background: rgb(0 0 0 / 70%);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            max-width: 180px;
            margin-bottom: 32px;
        }

        .maintenance-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 16px;
            background: #fae983;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .maintenance-message {
            font-size: 18px;
            color: white;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid #e2e8f0;
        }

        .social-link {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f1f5f9;
            color: var(--text-main);
            text-decoration: none;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
        }

        .contact-info {
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-muted);
        }

        @media (max-width: 480px) {
            .maintenance-container {
                padding: 32px 24px;
            }
            .maintenance-title {
                font-size: 28px;
            }
            .maintenance-message {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-bg"></div>

    <div class="maintenance-container">
        @if(setting('app_logo'))
            <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="{{ setting('app_name') }}" class="logo">
        @else
            <h2 style="margin-bottom: 24px;">{{ setting('app_name', 'Store Name') }}</h2>
        @endif

        <h1 class="maintenance-title">{{ setting('maintenance_title', __('We\'ll be back soon!')) }}</h1>
        <p class="maintenance-message">
            {{ setting('maintenance_message', __('The store is currently undergoing maintenance. Please check back later.' )) }}
        </p>

        <div class="social-links">
            @if(setting('social_facebook') && setting('social_facebook') !== '#')
                <a href="{{ setting('social_facebook') }}" class="social-link" target="_blank"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if(setting('social_instagram') && setting('social_instagram') !== '#')
                <a href="{{ setting('social_instagram') }}" class="social-link" target="_blank"><i class="fab fa-instagram"></i></a>
            @endif
            @if(setting('social_whatsapp'))
                <a href="https://wa.me/{{ setting('social_whatsapp') }}" class="social-link" target="_blank"><i class="fab fa-whatsapp"></i></a>
            @endif
        </div>

        <div class="contact-info">
            @if(setting('company_email'))
                <p><i class="far fa-envelope"></i> {{ setting('company_email') }}</p>
            @endif
        </div>
    </div>
</body>
</html>
