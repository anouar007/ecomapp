@extends('layouts.app')

@section('title', 'Créer un Tag / Badge Produit')

@section('content')
<div class="categories-container">
    <!-- En-tête Studio Pro -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ route('product-tags.index') }}" class="text-decoration-none text-muted">Tags & Badges</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-dark fw-semibold">Nouveau Badge</span>
                </div>
                <h1 class="brand-title">Studio de Création de Badge</h1>
                <p class="brand-subtitle">Concevez un badge dynamique avec mise à l'échelle proportionnelle selon la taille de la carte produit</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('product-tags.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center text-decoration-none">
                <i class="fas fa-arrow-left me-1.5"></i> <span>Retour aux tags</span>
            </a>
            <button type="submit" form="tagStudioForm" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3.5 py-1.5 shadow-sm rounded-pill" style="font-size: 0.84rem;">
                <i class="fas fa-check-circle me-1.5"></i> <span>Créer le badge</span>
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-3">
            <div class="d-flex align-items-center gap-2 fw-bold mb-1">
                <i class="fas fa-exclamation-triangle"></i> Veuillez corriger les champs suivants :
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('product-tags.store') }}" method="POST" id="tagStudioForm">
        @csrf
        @include('product-tags._form')
    </form>
</div>
@endsection
