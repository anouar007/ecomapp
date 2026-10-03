@extends('layouts.frontend')

@section('meta_title', __('Order confirmed') . ' - ' . setting('app_name', 'Speed Platform'))

@section('content')
<div class="bg-light py-5 min-vh-100 d-flex align-items-center">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-5">
                    <div class="mb-4">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <i class="fas fa-check fa-3x"></i>
                        </div>
                    </div>
                    
                    <h1 class="fw-bold mb-3">{{ __('Order confirmed!') }}</h1>
                    <p class="text-muted mb-4 lead">{{ __('Thank you for your order. We have received it and are processing it right away.') }}</p>
                    
                    <div class="bg-light p-4 rounded-4 mb-4 text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold small">{{ __('ORDER NUMBER') }}</span>
                            <span class="fw-bold text-dark">{{ $order->order_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold small">{{ __('DATE') }}</span>
                            <span class="fw-bold text-dark">{{ $order->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 border-top pt-2 mt-2">
                            <span class="text-muted fw-bold small">{{ __('TOTAL') }}</span>
                            <span class="fw-bold text-primary">{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-bold w-100">
                        {{ __('Continue shopping') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
