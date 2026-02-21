@extends('layouts.frontend')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="p-4 bg-primary text-white text-center">
                            <div class="avatar-circle bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px; font-weight: bold;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <h6 class="fw-bold mb-0 text-white">{{ auth()->user()->name }}</h6>
                            <small class="opacity-75">{{ auth()->user()->email }}</small>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.dashboard') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-home me-2 opacity-50"></i> Tableau de bord
                            </a>
                            <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.orders*') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-shopping-bag me-2 opacity-50"></i> Mes commandes
                            </a>
                            <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.profile') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-user-cog me-2 opacity-50"></i> Paramètres du profil
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="border-top">
                                @csrf
                                <button type="submit" class="list-group-item list-group-item-action p-3 text-danger">
                                    <i class="fas fa-sign-out-alt me-2 opacity-50"></i> Déconnexion
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
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('dashboard_content')
            </div>
        </div>
    </div>
</div>
@endsection
