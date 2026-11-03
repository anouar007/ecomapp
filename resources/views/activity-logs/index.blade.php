@extends('layouts.app')

@section('title', "Journal d'Activité")

@section('content')
<div class="brand-header mb-3 mb-md-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h1 class="brand-title">Journal d'Activité</h1>
                <p class="brand-subtitle">Suivi des actions utilisateurs et traçabilité des événements système</p>
            </div>
        </div>
        <form action="{{ route('activity-logs.clear') }}" method="POST" onsubmit="return confirm('Supprimer les journaux anciens de plus de 90 jours ?');">
            @csrf
            <input type="hidden" name="older_than_days" value="90">
            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold">
                <i class="fas fa-trash-alt me-1.5"></i> Purger (>90j)
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3 mb-md-4" role="alert">
    <i class="fas fa-check-circle me-1.5"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Statistics Cards -->
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-4">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="min-w-0">
                    <div class="stat-kpi-label text-truncate">Total Logs</div>
                    <div class="stat-kpi-value text-dark fs-4">{{ number_format($stats['total_activities']) }}</div>
                    <div class="stat-kpi-sub text-muted d-none d-sm-block text-truncate">Événements enregistrés</div>
                </div>
                <div class="stat-kpi-icon bg-primary-subtle text-primary">
                    <i class="fas fa-list-ul"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="min-w-0">
                    <div class="stat-kpi-label text-truncate">Aujourd'hui</div>
                    <div class="stat-kpi-value text-success fs-4">{{ number_format($stats['today_activities']) }}</div>
                    <div class="stat-kpi-sub text-muted d-none d-sm-block text-truncate">Activité en direct</div>
                </div>
                <div class="stat-kpi-icon bg-success-subtle text-success">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="min-w-0">
                    <div class="stat-kpi-label text-truncate">Utilisateurs Uniques</div>
                    <div class="stat-kpi-value text-info fs-4">{{ number_format($stats['unique_users']) }}</div>
                    <div class="stat-kpi-sub text-muted d-none d-sm-block text-truncate">Opérateurs actifs</div>
                </div>
                <div class="stat-kpi-icon bg-info-subtle text-info">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3 mb-md-4 border-0 shadow-sm rounded-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Rechercher une action..." value="{{ request('search') }}">
            </div>
            
            <div class="col-6 col-md-3">
                <select name="user_id" class="form-select">
                    <option value="">Tous les utilisateurs</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Date début">
            </div>
            
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i> Filtrer</button>
                @if(request()->hasAny(['search', 'user_id', 'start_date', 'end_date']))
                <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Desktop Table (d-none d-md-block, Zero horizontal scroll) -->
<div class="brand-table-card d-none d-md-block shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white p-3 px-md-4 border-bottom">
        <h5 class="mb-0 fw-bold fs-6 fs-md-5">
            <i class="fas fa-history text-danger me-2"></i>Historique des événements
        </h5>
    </div>
    <div class="table-responsive">
        <table class="brand-table mb-0 align-middle">
            <thead>
                <tr>
                    <th style="padding-left: 1.5rem; width: 22%;">Utilisateur</th>
                    <th style="width: 25%;">Action</th>
                    <th style="width: 18%;">Élément concerné</th>
                    <th style="width: 20%;">Date & Heure</th>
                    <th class="text-end" style="padding-right: 1.5rem; width: 15%;">Détails</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="padding-left: 1.5rem;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs flex-shrink-0" 
                                 style="width: 32px; height: 32px; background: #e2e8f0; color: #475569; font-size: 0.78rem;">
                                {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                            </div>
                            <div class="min-w-0">
                                <span class="fw-semibold text-dark text-truncate d-block small">{{ $log->user->name ?? 'Système' }}</span>
                                <small class="text-muted text-truncate d-block" style="font-size: 0.70rem;">{{ $log->user->email ?? '' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-2.5 py-1 small">
                            {{ $log->description }}
                        </span>
                    </td>
                    <td>
                        @if($log->subject_type)
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5" style="font-size: 0.72rem;">
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="small text-dark">{{ $log->created_at->format('d/m/Y') }}</div>
                        <small class="text-muted font-monospace" style="font-size: 0.70rem;">{{ $log->created_at->format('H:i:s') }}</small>
                    </td>
                    <td style="padding-right: 1.5rem;" class="text-end">
                        <div class="action-buttons-group justify-content-end">
                            <a href="{{ route('activity-logs.show', $log) }}" class="action-btn action-btn-view" title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-50 mb-2">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h6 class="fw-bold text-muted">Aucun journal d'activité trouvé</h6>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
    @endif
</div>

<!-- Mobile Touch Cards (d-block d-md-none, Zero horizontal scroll) -->
<div class="d-block d-md-none mb-4">
    @forelse($logs as $log)
    <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs flex-shrink-0" 
                     style="width: 28px; height: 28px; background: #e2e8f0; color: #475569; font-size: 0.74rem;">
                    {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                </div>
                <span class="fw-bold text-dark text-truncate small">{{ $log->user->name ?? 'Système' }}</span>
            </div>
            <span class="text-muted small" style="font-size: 0.72rem;">
                {{ $log->created_at->format('d/m/Y H:i') }}
            </span>
        </div>

        <div class="mb-2">
            <span class="badge bg-light text-dark border px-2 py-1 text-wrap text-start lh-sm" style="font-size: 0.78rem;">
                {{ $log->description }}
            </span>
        </div>

        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
            <div>
                @if($log->subject_type)
                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5" style="font-size: 0.70rem;">
                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                    </span>
                @else
                    <span class="text-muted small" style="font-size: 0.72rem;">—</span>
                @endif
            </div>
            <a href="{{ route('activity-logs.show', $log) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                <i class="fas fa-eye me-1"></i> Détails
            </a>
        </div>
    </div>
    @empty
    <div class="text-center py-5 bg-white rounded-3 border">
        <i class="fas fa-history fa-3x text-muted opacity-50 mb-3"></i>
        <h6 class="fw-bold text-muted">Aucun journal trouvé</h6>
    </div>
    @endforelse

    @if($logs->hasPages())
    <div class="p-3 d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
