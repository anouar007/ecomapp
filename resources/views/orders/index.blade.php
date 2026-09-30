@extends('layouts.app')

@section('title', 'Gestion des commandes')

@section('content')
    <!-- En-tête de page -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                Gestion des commandes
            </h1>
            <p class="brand-subtitle">Suivez et gérez les commandes clients, l'état de livraison et les paiements</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Créer une nouvelle commande
        </a>
    </div>

    <!-- Barre de filtres -->
    <div class="brand-filter-bar">
        <form method="GET" action="{{ route('orders.index') }}" class="d-flex align-items-end gap-3 flex-wrap">
            <div class="brand-search-wrapper flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="N° de commande, nom ou e-mail...">
            </div>
            
            <div style="min-width: 150px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de livraison</label>
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    @php
                        $statusNames = [
                            'pending' => 'En attente',
                            'processing' => 'En cours',
                            'shipped' => 'Expédiée',
                            'delivered' => 'Livrée',
                            'cancelled' => 'Annulée'
                        ];
                    @endphp
                    @foreach($statusNames as $stKey => $stLabel)
                        <option value="{{ $stKey }}" {{ request('status') == $stKey ? 'selected' : '' }}>{{ $stLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 150px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Paiement</label>
                <select name="payment_status" class="form-select">
                    <option value="">Tous les paiements</option>
                    @php
                        $paymentStatusNames = [
                            'pending' => 'En attente',
                            'paid' => 'Payé',
                            'failed' => 'Échoué',
                            'refunded' => 'Remboursé'
                        ];
                    @endphp
                    @foreach($paymentStatusNames as $pstKey => $pstLabel)
                        <option value="{{ $pstKey }}" {{ request('payment_status') == $pstKey ? 'selected' : '' }}>{{ $pstLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                <a href="{{ route('orders.index') }}" class="btn-brand-light" title="Réinitialiser">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des commandes -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">N° Commande</th>
                        <th>Client</th>
                        <th class="text-center">Articles</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Livraison</th>
                        <th class="text-center">Paiement</th>
                        <th>Date</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary text-decoration-none">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $order->customer_name }}</div>
                            <div class="text-muted small">{{ $order->customer_email }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-secondary px-3 py-1" style="border-radius: 6px;">
                                {{ $order->items->count() }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6">
                            {{ $order->formatted_total }}
                        </td>
                        <td class="text-center">
                            @php
                                $statusClass = match(strtolower($order->status)) {
                                    'delivered', 'livrée' => 'success',
                                    'cancelled', 'annulée' => 'danger',
                                    'shipped', 'expédiée' => 'info',
                                    'processing', 'en cours' => 'primary',
                                    default => 'warning'
                                };
                                $statusText = match(strtolower($order->status)) {
                                    'delivered' => 'Livrée',
                                    'shipped' => 'Expédiée',
                                    'processing' => 'En cours',
                                    'pending' => 'En attente',
                                    'cancelled' => 'Annulée',
                                    default => ucfirst($order->status)
                                };
                            @endphp
                            <span class="brand-badge {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $pClass = match(strtolower($order->payment_status)) {
                                    'paid', 'payé' => 'success',
                                    'failed', 'échoué' => 'danger',
                                    'refunded', 'remboursé' => 'info',
                                    default => 'warning'
                                };
                                $pText = match(strtolower($order->payment_status)) {
                                    'paid' => 'Payé',
                                    'failed' => 'Échoué',
                                    'refunded' => 'Remboursé',
                                    'pending' => 'En attente',
                                    default => ucfirst($order->payment_status)
                                };
                            @endphp
                            <span class="brand-badge {{ $pClass }}">
                                {{ $pText }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $order->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="btn-action-icon" title="Voir la commande">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="btn-action-icon" title="Modifier la commande">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(in_array($order->status, ['pending', 'cancelled']))
                                    <form method="POST" 
                                          action="{{ route('orders.destroy', $order->id) }}" 
                                          style="display: inline;"
                                          data-confirm-delete="true"
                                          data-item-type="order"
                                          data-item-name="Commande #{{ $order->order_number }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon danger" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-shopping-cart text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucune commande trouvée</h5>
                                <p class="text-muted">Aucun enregistrement ne correspond à vos critères de recherche.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
@endsection
