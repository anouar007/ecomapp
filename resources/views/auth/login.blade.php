<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Sign In') }} - {{ setting('app_name', 'WINA SHOP') }}</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/camera/logo.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        body, input, button, select, textarea, .auth-title, .auth-subtitle, .form-label, a {
            font-family: "Roboto", sans-serif !important;
        }
        b, strong, .fw-bold, .fw-semibold, h1, h2, h3, h4, h5, h6, .auth-title, .form-label, .btn {
            font-weight: 400 !important;
        }
        .fas, .fa-solid {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
        }
    </style>
    @if(app()->getLocale() === 'ar')
    <style>
        .form-check { padding-right: 1.5em; padding-left: 0; }
        .form-check .form-check-input { float: right; margin-right: -1.5em; margin-left: 0; }
    </style>
    @endif
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <a href="{{ url('/') }}" title="{{ setting('app_name', 'WINA SHOP') }}">
                        <img src="{{ asset('images/camera/logo.png') }}" alt="{{ setting('app_name', 'WINA SHOP') }}" style="max-height: 72px; width: auto; object-fit: contain;">
                    </a>
                </div>
                <h1 class="auth-title">{{ __('Welcome Back') }}</h1>
                <p class="auth-subtitle">{{ __('Sign in to your account to continue') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        value="{{ old('email') }}" 
                        placeholder="{{ __('Enter your email') }}"
                        required 
                        autofocus
                        autocomplete="email"
                    >
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="{{ __('Enter your password') }}"
                        required
                        autocomplete="current-password"
                    >
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">{{ __('Remember me') }}</label>
                </div>

                <button type="submit" class="btn-primary">
                    {{ __('Sign In') }}
                </button>
            </form>

            <div class="auth-footer" style="text-align: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
                <p style="color: #64748b; font-size: 14px;">{{ __("Don't have an account?") }} <a href="{{ route('register') }}" style="color: #667eea; text-decoration: none; font-weight: 400;">{{ __('Create one') }}</a></p>
            </div>
        </div>
    </div>
</body>
</html>
