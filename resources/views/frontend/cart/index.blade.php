@extends('layouts.frontend')

@section('meta_title', __('My Cart') . ' — ' . setting('app_name', 'LUMINA Cine & Optics'))

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <h1 class="fw-bold mb-4 font-heading">{{ __('My Cart') }}</h1>

        @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light border-bottom">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 text-muted small text-uppercase fw-bold ls-1">{{ __('Product') }}</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-uppercase text-center fw-bold ls-1">{{ __('Price') }}</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-uppercase text-center fw-bold ls-1" style="width: 150px;">{{ __('Quantity') }}</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-uppercase text-end fw-bold ls-1">{{ __('Total') }}</th>
                                        <th scope="col" class="py-3 px-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach(session('cart') as $id => $details)
                                    @php $total += $details['price'] * $details['quantity']; @endphp
                                    <tr class="border-bottom transition-all hover-bg-light" id="cart-row-{{ $id }}">
                                        <td class="py-4 px-4">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    @php
                                                        $cartImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/')))
                                                            ? asset(ltrim($details['image'], '/'))
                                                            : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                                                    @endphp
                                                    <img src="{{ $cartImg }}" alt="{{ $details['name'] }}" class="rounded-3 shadow-sm object-fit-cover" style="width: 70px; height: 70px;">
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1 ml-3"><a href="{{ route('shop.show', $id) }}" class="text-decoration-none text-dark">{{ $details['name'] }}</a></h6>
                                                    <p class="text-muted small mb-0 ml-3">{{ $details['category_name'] ?? __('Product') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-4 px-4 fw-bold">{{ currency($details['price']) }}</td>
                                        <td class="text-center py-4 px-4">
                                            <div class="quantity-control bg-light rounded-pill d-flex align-items-center px-2 py-1 border mx-auto" style="width: 105px;">
                                                <button class="btn btn-sm btn-link text-dark text-decoration-none p-0 w-100" type="button" onclick="changeCartQty({{ $id }}, -1)" aria-label="-">
                                                    <i class="fas fa-minus small"></i>
                                                </button>
                                                <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" id="cart-item-qty-{{ $id }}" value="{{ $details['quantity'] }}" readonly>
                                                <button class="btn btn-sm btn-link text-dark text-decoration-none p-0 w-100" type="button" onclick="changeCartQty({{ $id }}, 1)" aria-label="+">
                                                    <i class="fas fa-plus small"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-end py-4 px-4 fw-bold text-primary h5 mb-0" id="cart-item-total-{{ $id }}">{{ currency($details['price'] * $details['quantity']) }}</td>
                                        <td class="text-end py-4 px-4">
                                            <button class="btn btn-link text-danger p-2 opacity-50 hover-opacity-100 rounded-circle hover-bg-danger-light transition-all" onclick="removeItem({{ $id }})" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 1;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 font-heading">{{ __('Order Summary') }}</h5>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="fw-bold text-dark" id="cart-summary-subtotal">{{ currency($total) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>{{ __('Shipping') }}</span>
                            <span class="text-dark fw-bold">
                                @if(app()->getLocale() === 'ar')
                                    من 20 درهم <small class="text-muted fw-normal">(حسب المدينة)</small>
                                @else
                                    Dès 20 DH <small class="text-muted fw-normal">(selon ville)</small>
                                @endif
                            </span>
                        </div>
                        <hr class="my-4 opacity-10">
                        <div class="d-flex justify-content-between mb-4 align-items-center">
                            <span class="h5 fw-bold mb-0">{{ __('Total') }}</span>
                            <span class="h4 fw-bold text-primary mb-0" id="cart-summary-total">{{ currency($total) }}</span>
                        </div>
                        <button class="btn btn-dark w-100 py-3 rounded-pill fw-bold mb-3 shadow-lg hover-scale-sm transition-transform" onclick="location.href='{{ route('checkout.index') }}'">
                            {{ __('Place Order') }} <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left ms-2' : 'fa-arrow-right ms-2' }}"></i>
                        </button>
                        <a href="{{ route('shop.index') }}" class="btn btn-link text-muted w-100 text-decoration-none small">
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right me-1' : 'fa-arrow-left me-1' }}"></i> {{ __('Continue Shopping') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5 mt-4">
            <div class="mb-4 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 120px; height: 120px;">
                <i class="fas fa-shopping-basket fa-4x text-muted opacity-25"></i>
            </div>
            <h3 class="fw-bold text-dark mb-3">{{ __('Your cart is empty') }}</h3>
            <p class="text-muted mb-4">{{ __("You haven't added anything yet.") }}</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm hover-scale-sm transition-transform">{{ __('Start Shopping') }}</a>
        </div>
        @endif
    </div>
</div>
@endsection
