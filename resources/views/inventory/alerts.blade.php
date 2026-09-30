@extends('layouts.app')

@section('title', 'Alertes de stock')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-exclamation-triangle"></i> Alertes de stock</h1>
            <p class="page-subtitle">Notifications de stock faible et de rupture de stock</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à l'inventaire
            </a>
            @if($stats['unacknowledged'] > 0)
            <form action="{{ route('inventory.bulk-acknowledge') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-double"></i> Tout marquer comme lu ({{ $stats['unacknowledged'] }})
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<!-- Cartes statistiques -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div style="background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%); border-radius: 16px; padding: 24px; border: 1px solid #fde68a;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #92400e; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Non traitées</p>
                <p style="font-size: 28px; font-weight: 700; color: #b45309; margin: 0;">{{ $stats['unacknowledged'] }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #fee2e2 100%); border-radius: 16px; padding: 24px; border: 1px solid #fecaca;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times-circle" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #991b1b; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Rupture de stock</p>
                <p style="font-size: 28px; font-weight: 700; color: #991b1b; margin: 0;">{{ $stats['out_of_stock'] }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #fed7aa 100%); border-radius: 16px; padding: 24px; border: 1px solid #fdba74;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-exclamation-triangle" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #9a3412; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Stock faible</p>
                <p style="font-size: 28px; font-weight: 700; color: #9a3412; margin: 0;">{{ $stats['low_stock'] }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #d1fae5 100%); border-radius: 16px; padding: 24px; border: 1px solid #a7f3d0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #166534; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Traitées / Lues</p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;">{{ $stats['acknowledged'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filtres</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.alerts') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <select name="status" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">Toutes les alertes</option>
                <option value="unacknowledged" {{ request('status') == 'unacknowledged' ? 'selected' : '' }}>Non traitées</option>
                <option value="acknowledged" {{ request('status') == 'acknowledged' ? 'selected' : '' }}>Traitées</option>
            </select>
            
            <select name="type" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">Tous les types</option>
                <option value="out_of_stock" {{ request('type') == 'out_of_stock' ? 'selected' : '' }}>Rupture de stock</option>
                <option value="low_stock" {{ request('type') == 'low_stock' ? 'selected' : '' }}>Stock faible</option>
            </select>
            
            <input type="text" name="product" class="form-control" style="flex: 1; min-width: 200px;" 
                   placeholder="Rechercher un produit..." value="{{ request('product') }}">
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrer</button>
            <a href="{{ route('inventory.alerts') }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Réinitialiser</a>
        </form>
    </div>
</div>

<!-- Tableau des alertes -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-bell"></i> Alertes actives ({{ $alerts->total() }})</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Type d'alerte</th>
                    <th>Stock actuel</th>
                    <th>Seuil minimal</th>
                    <th>Déclenchée</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 38px; height: 38px; border-radius: 6px; overflow: hidden; flex-shrink: 0; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center;">
                                <img src="{{ $alert->product->thumbnail }}" alt="{{ $alert->product->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                            </div>
                            <div>
                                <strong>{{ $alert->product->name }}</strong>
                                <br><small class="text-muted"><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 11px;">{{ $alert->product->sku }}</code></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($alert->alert_type === 'out_of_stock')
                            <span class="badge badge-danger">
                                <i class="fas fa-times-circle"></i> Rupture de stock
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i> Stock faible
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $alert->current_stock <= 0 ? 'badge-danger' : 'badge-warning' }}">
                            {{ $alert->current_stock }}
                        </span>
                    </td>
                    <td>{{ $alert->threshold_value }}</td>
                    <td>
                        <small class="text-muted">{{ $alert->triggered_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        @if($alert->acknowledged_at)
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> Traitée
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> En attente
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if(!$alert->acknowledged_at)
                            <form action="{{ route('inventory.acknowledge-alert', $alert) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-action-edit" title="Marquer comme traitée">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('inventory.adjust', $alert->product) }}" class="btn-action btn-action-view" title="Ajuster le stock">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <p>Aucune alerte active. Tous les niveaux de stock sont optimaux !</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($alerts->hasPages())
    <div class="card-footer">
        {{ $alerts->links() }}
    </div>
    @endif
</div>
@endsection
