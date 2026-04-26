@extends('layouts.app')

@section('title', __('Edit Order'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-edit"></i> {{ __('Edit Order') }}: {{ $order->order_number }}</h1>
    <p class="page-subtitle">{{ __('Update order status and payment information') }}</p>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        <strong>{{ __('Oops! Something went wrong:') }}</strong>
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
        <h3 class="card-title"><i class="fas fa-file-alt"></i> {{ __('Order Information') }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="status" class="form-label">
                        {{ __('Order Status') }} <span class="required">*</span>
                    </label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                        <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                        <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_status" class="form-label">
                        {{ __('Payment Status') }} <span class="required">*</span>
                    </label>
                    <select id="payment_status" name="payment_status" class="form-control" required>
                        <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                        <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                        <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_method" class="form-label">{{ __('Payment Method') }}</label>
                    <input type="text" 
                           id="payment_method" 
                           name="payment_method" 
                           class="form-control" 
                           value="{{ old('payment_method', $order->payment_method) }}" 
                           placeholder="{{ __('e.g., Credit Card') }}">
                </div>

                <div class="form-group">
                    <label for="transaction_id" class="form-label">{{ __('Transaction ID') }}</label>
                    <input type="text" 
                           id="transaction_id" 
                           name="transaction_id" 
                           class="form-control" 
                           value="{{ old('transaction_id', $order->transaction_id) }}" 
                           placeholder="{{ __('Transaction ID') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">{{ __('Notes') }}</label>
                <textarea id="notes" 
                          name="notes" 
                          class="form-control" 
                          rows="4" 
                          placeholder="{{ __('Order notes...') }}">{{ old('notes', $order->notes) }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('Update Order') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
