@extends('layouts.app')

@section('title', 'Add Customer')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-plus"></i> Add New Customer</h1>
    <p class="page-subtitle">Create a new customer profile</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Customer Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label for="customer_group_id" class="form-label">Customer Group</label>
                    <select id="customer_group_id" name="customer_group_id" class="form-control">
                        <option value="">No Group</option>
                        @foreach($customerGroups as $group)
                        <option value="{{ $group->id }}" {{ old('customer_group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status <span class="required">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                </div>

                <div class="form-group">
                    <label for="credit_limit" class="form-label">Credit Limit</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" id="credit_limit" name="credit_limit" class="form-control" value="{{ old('credit_limit', 0) }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <textarea id="address" name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" value="{{ old('city') }}">
                </div>

                <div class="form-group">
                    <label for="state" class="form-label">State/Province</label>
                    <input type="text" id="state" name="state" class="form-control" value="{{ old('state') }}">
                </div>

                <div class="form-group">
                    <label for="zip" class="form-label">ZIP Code</label>
                    <input type="text" id="zip" name="zip" class="form-control" value="{{ old('zip') }}">
                </div>

                <div class="form-group">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" id="country" name="country" class="form-control" value="{{ old('country') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Notes</label>
                <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
