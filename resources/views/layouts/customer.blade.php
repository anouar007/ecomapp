@extends('layouts.frontend')

@section('content')
<div class="py-5" style="background-color: #fcfaf8; min-height: 60vh;">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="rounded-4 overflow-hidden" style="box-shadow: 0 4px 20px rgba(28,36,16,0.12);">
                    <!-- Profile Header -->
                    <div class="p-4 text-white text-center" style="background: #1c2410;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold"
                             style="width: 64px; height: 64px; font-size: 22px; background: #475927; color: white; font-family: 'Cairo', sans-serif; box-shadow: 0 4px 12px rgba(71,89,39,0.4);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-1" style="font-family: 'Cairo', sans-serif; color: white;">{{ auth()->user()->name }}</h6>
                        <small style="color: rgba(255,255,255,0.55);">{{ auth()->user()->email }}</small>
                    </div>

                    <!-- Gold divider -->
                    <div style="height: 2px; background: linear-gradient(90deg, #d4a843 0%, #c8922a 100%);"></div>

                    <!-- Nav Links -->
                    <div style="background: #222d14;">
                        <a href="{{ route('customer.dashboard') }}"
                           class="d-flex align-items-center gap-3 p-3 text-decoration-none border-0"
                           style="color: {{ request()->routeIs('customer.dashboard') ? '#d4a843' : 'rgba(255,255,255,0.7)' }};
                                  background: {{ request()->routeIs('customer.dashboard') ? 'rgba(71,89,39,0.5)' : 'transparent' }};
                                  font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 500;
                                  border-bottom: 1px solid rgba(255,255,255,0.06) !important; transition: all 0.2s;">
                            <i class="fas fa-home" style="width: 18px; text-align: center; opacity: 0.75;"></i>
                            <span>{{ __('لوحة القيادة') }}</span>
                        </a>
                        <a href="{{ route('customer.orders') }}"
                           class="d-flex align-items-center gap-3 p-3 text-decoration-none"
                           style="color: {{ request()->routeIs('customer.orders*') ? '#d4a843' : 'rgba(255,255,255,0.7)' }};
                                  background: {{ request()->routeIs('customer.orders*') ? 'rgba(71,89,39,0.5)' : 'transparent' }};
                                  font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 500;
                                  border-bottom: 1px solid rgba(255,255,255,0.06) !important; transition: all 0.2s;">
                            <i class="fas fa-shopping-bag" style="width: 18px; text-align: center; opacity: 0.75;"></i>
                            <span>{{ __('طلباتي') }}</span>
                        </a>
                        <a href="{{ route('customer.profile') }}"
                           class="d-flex align-items-center gap-3 p-3 text-decoration-none"
                           style="color: {{ request()->routeIs('customer.profile') ? '#d4a843' : 'rgba(255,255,255,0.7)' }};
                                  background: {{ request()->routeIs('customer.profile') ? 'rgba(71,89,39,0.5)' : 'transparent' }};
                                  font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 500;
                                  border-bottom: 1px solid rgba(255,255,255,0.06) !important; transition: all 0.2s;">
                            <i class="fas fa-user-cog" style="width: 18px; text-align: center; opacity: 0.75;"></i>
                            <span>{{ __('إعدادات الملف الشخصي') }}</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="w-100 d-flex align-items-center gap-3 p-3 border-0 text-decoration-none"
                                    style="color: #e85d53; background: transparent;
                                           font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 500; cursor: pointer;">
                                <i class="fas fa-sign-out-alt" style="width: 18px; text-align: center; opacity: 0.75;"></i>
                                <span>{{ __('تسجيل الخروج') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                @if(session('success'))
                    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-3"
                         role="alert"
                         style="background: #f0fdf4; border-left: 4px solid #16a34a !important; color: #166534;">
                        <i class="fas fa-check-circle" style="color: #16a34a;"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @yield('dashboard_content')
            </div>
        </div>
    </div>
</div>
@endsection
