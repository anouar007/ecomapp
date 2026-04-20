<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Create Account Title') }} - {{ setting('app_name', 'Moubdi3oun') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .auth-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }
        .auth-footer p {
            color: #64748b;
            font-size: 14px;
        }
        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .auth-footer a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .password-requirements {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="auth-title">{{ __('Create Account Title') }}</h1>
                <p class="auth-subtitle">{{ __('Join Moubdi3oun') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">{{ __('Full Name') }}</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name') }}" 
                        placeholder="{{ __('Name and Surname') }}"
                        required 
                        autofocus
                        autocomplete="name"
                    >
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">{{ __('Email Label') }}</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        value="{{ old('email') }}" 
                        placeholder="{{ __('Email Placeholder Input') }}"
                        required
                        autocomplete="email"
                    >
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="{{ __('Password Placeholder') }}"
                        required
                        autocomplete="new-password"
                    >
                    <p class="password-requirements">{{ __('Password Requirements') }}</p>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password Label') }}</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-control" 
                        placeholder="{{ __('Confirm Password Label') }}"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn-primary">
                    {{ __('Create Account Link') }}
                </button>
            </form>

            <div class="auth-footer">
                <p>{{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Sign In') }}</a></p>
            </div>
        </div>
    </div>
</body>
</html>
