@extends('layouts.app')

@section('title', 'Create Permission')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> Create New Permission</h1>
    <p class="page-subtitle">Define a new system permission</p>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        <strong>Oops! Something went wrong:</strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Permission Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">
                    Permission Name <span class="required">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       value="{{ old('name') }}" 
                       placeholder="e.g., manage_products, view_reports" 
                       required 
                       autofocus>
                <small class="form-help">
                    Use snake_case format (e.g., manage_products, view_reports, create_orders)
                </small>
            </div>

            <div class="form-actions">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Permission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
