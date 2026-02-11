@extends('layouts.customer')

@section('dashboard_content')
<h3 class="fw-bold mb-4">Profile Settings</h3>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('customer.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <h5 class="fw-bold mb-4">Personal Information</h5>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">FULL NAME</label>
                    <input type="text" name="name" class="form-control bg-light border-0 py-2" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">EMAIL ADDRESS</label>
                    <input type="email" name="email" class="form-control bg-light border-0 py-2" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <hr class="my-5 opacity-10">

            <h5 class="fw-bold mb-4">Change Password</h5>
            <div class="row g-4 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-bold small text-muted">CURRENT PASSWORD</label>
                    <input type="password" name="current_password" class="form-control bg-light border-0 py-2" placeholder="Leave blank to keep current password">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">NEW PASSWORD</label>
                    <input type="password" name="password" class="form-control bg-light border-0 py-2">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">CONFIRM PASSWORD</label>
                    <input type="password" name="password_confirmation" class="form-control bg-light border-0 py-2">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Update Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection
