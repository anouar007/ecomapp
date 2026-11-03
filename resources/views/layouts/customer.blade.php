@extends('layouts.frontend')

@section('content')
<div class="bg-light py-3 py-md-5">
    <div class="container">
        <!-- Mobile Customer Profile & Nav Bar (d-lg-none) -->
        <div class="d-lg-none mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <h6 class="fw-bold mb-0 text-dark text-truncate">{{ auth()->user()->name }}</h6>
                            <small class="text-muted text-truncate d-block" style="font-size: 0.76rem;">{{ auth()->user()->email }}</small>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" title="{{ __('Logout') }}">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="d-none d-sm-inline">{{ __('Logout') }}</span>
                        </button>
                    </form>
                </div>

                <!-- Touch Horizontal Navigation Pills -->
                <div class="d-flex gap-2 overflow-x-auto pb-1 customer-mobile-tabs" style="-webkit-overflow-scrolling: touch; scrollbar-width: none;">
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-sm rounded-pill text-nowrap px-3 py-2 flex-shrink-0 {{ request()->routeIs('customer.dashboard') ? 'btn-primary shadow-xs' : 'btn-light text-dark' }}">
                        <i class="fas fa-home me-1.5 opacity-75"></i> {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('customer.orders') }}" class="btn btn-sm rounded-pill text-nowrap px-3 py-2 flex-shrink-0 {{ request()->routeIs('customer.orders*') ? 'btn-primary shadow-xs' : 'btn-light text-dark' }}">
                        <i class="fas fa-shopping-bag me-1.5 opacity-75"></i> {{ __('My Orders') }}
                    </a>
                    <a href="{{ route('customer.profile') }}" class="btn btn-sm rounded-pill text-nowrap px-3 py-2 flex-shrink-0 {{ request()->routeIs('customer.profile') ? 'btn-primary shadow-xs' : 'btn-light text-dark' }}">
                        <i class="fas fa-user-cog me-1.5 opacity-75"></i> {{ __('Profile Settings') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Desktop Sidebar (d-none d-lg-block) -->
            <div class="col-lg-3 mb-4 d-none d-lg-block">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 20px;">
                    <div class="card-body p-0">
                        <div class="p-4 bg-primary text-white text-center">
                            <div class="avatar-circle bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 64px; height: 64px; font-size: 24px; font-weight: bold;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <h6 class="fw-bold mb-0 text-white">{{ auth()->user()->name }}</h6>
                            <small class="opacity-75">{{ auth()->user()->email }}</small>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.dashboard') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-home me-2 opacity-50"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.orders*') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-shopping-bag me-2 opacity-50"></i> {{ __('My Orders') }}
                            </a>
                            <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.profile') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-user-cog me-2 opacity-50"></i> {{ __('Profile Settings') }}
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="border-top m-0">
                                @csrf
                                <button type="submit" class="list-group-item list-group-item-action p-3 text-danger w-100 text-start border-0 bg-transparent">
                                    <i class="fas fa-sign-out-alt me-2 opacity-50"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="fas fa-check-circle me-1.5"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('dashboard_content')
            </div>
        </div>
    </div>
</div>
@endsection
