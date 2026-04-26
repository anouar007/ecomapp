@extends('layouts.customer')

@section('dashboard_content')
<div class="d-flex flex-column mb-4" data-aos="fade-up">
    <h3 class="brand-heading h2 mb-2">{{ __('Your Royal Account Settings') }}</h3>
    <div class="bg-gold rounded" style="width: 40px; height: 3px;"></div>
</div>

<div class="brand-card border-0 shadow-sm bg-white" data-aos="fade-up" data-aos-delay="100">
    <div class="card-body p-4 p-lg-5">
        <form action="{{ route('customer.profile.update') }}" method="POST" class="font-body">
            @csrf
            @method('PUT')
            
            <div class="d-flex align-items-center gap-3 mb-5">
                <div class="bg-gold-light p-3 rounded-circle text-gold">
                    <i class="fas fa-user-crown fa-2x"></i>
                </div>
                <div>
                    <h5 class="brand-heading h4 mb-0">{{ __('Personal Information') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Update your details to keep your experience with us perfect') }}</p>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('Full Name') }}</label>
                    <input type="text" name="name" class="form-control border-light py-3 px-4 bg-light shadow-none focus-gold" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('Email') }}</label>
                    <input type="email" name="email" class="form-control border-light py-3 px-4 bg-light shadow-none focus-gold" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="bg-gold-light mb-5" style="height: 1px;"></div>

            <div class="d-flex align-items-center gap-3 mb-5">
                <div class="bg-gold-light p-3 rounded-circle text-gold">
                    <i class="fas fa-lock fa-2x"></i>
                </div>
                <div>
                    <h5 class="brand-heading h4 mb-0">{{ __('Change Password') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Leave fields blank if you do not want to change') }}</p>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-12">
                    <label class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('Current Password') }}</label>
                    <input type="password" name="current_password" class="form-control border-light py-3 px-4 bg-light shadow-none focus-gold" placeholder="{{ __('Current Password') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('New Password') }}</label>
                    <input type="password" name="password" class="form-control border-light py-3 px-4 bg-light shadow-none focus-gold">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('Confirm New Password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control border-light py-3 px-4 bg-light shadow-none focus-gold">
                </div>
            </div>

            <div class="text-start">
                <button type="submit" class="btn-brand-primary px-5 py-3 hvr-grow">
                    {{ __('Update Profile') }} <i class="fas fa-save ms-2 small"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.focus-gold:focus { border-color: var(--brand-gold) !important; background: white !important; }
.ls-1 { letter-spacing: 1px; }
</style>
@endsection
