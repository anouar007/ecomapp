@extends('layouts.frontend')

@section('meta_title', 'Commande confirmée — Coopérative Aït Oumdis')

@section('content')
<div class="aod-zellige-soft py-5 d-flex align-items-center" style="min-height: 75vh;">
    <div class="aod-container text-center" style="position: relative; z-index: 1;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="rounded-4 p-4 p-md-5" style="background: #ffffff; border: 1px solid #e3ede6; box-shadow: 0 12px 32px rgba(30, 77, 52, 0.06);">
                    <div class="mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 78px; height: 78px; background: #edf6ed; color: #2f8825; border: 2px solid rgba(47, 136, 37, 0.25);">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                    </div>

                    <h1 class="fw-bold mb-2" style="font-family: 'Playfair Display', Georgia, serif; color: #1e4d34; font-size: clamp(1.6rem, 3vw, 2.1rem);">
                        Commande confirmée !
                    </h1>
                    <p class="mb-4" style="color: #5f6f65; font-size: 0.95rem;">
                        Merci pour votre confiance. Nous avons bien reçu votre commande et notre équipe la prépare dès maintenant.
                    </p>

                    <div class="p-4 rounded-4 mb-4 text-start" style="background: #f8fbf9; border: 1px solid #e3ede6;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold small" style="color: #5f6f65;">N° DE COMMANDE</span>
                            <span class="fw-bold" style="color: #1e4d34;">{{ $order->order_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold small" style="color: #5f6f65;">DATE</span>
                            <span class="fw-bold" style="color: #1e4d34;">{{ $order->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 mt-2" style="border-top: 1px solid #dcebe0;">
                            <span class="fw-bold small" style="color: #5f6f65;">TOTAL À PAYER À LA LIVRAISON</span>
                            <span class="fw-bold" style="color: #2f8825; font-size: 1.15rem;">{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    <a href="{{ route('shop.index') }}" class="aod-btn-green w-100">
                        <span>Continuer mes achats</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
