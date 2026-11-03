@extends('layouts.app')

@section('title', 'Gestion des Retours')

@section('content')
    <!-- En-tête Brand Pro -->
    <div class="brand-header mb-3 mb-md-4">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-rotate-left"></i>
            </div>
            <div>
                <h1 class="brand-title">Gestion des Retours</h1>
                <p class="brand-subtitle">Traitez les demandes de retour, vérifiez les motifs et gérez les remboursements</p>
            </div>
        </div>
    </div>

    <!-- Alertes Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3 mb-md-4" role="alert">
            <i class="fas fa-check-circle me-1.5"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3 mb-md-4" role="alert">
            <i class="fas fa-exclamation-circle me-1.5"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cartes KPI Métriques (2 cols sur mobile, 4 sur desktop) -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('returns.index') }}" class="stat-kpi-card text-decoration-none {{ !request('status') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-w-0">
                        <div class="stat-kpi-label text-truncate">Total Retours</div>
                        <div class="stat-kpi-value text-dark fs-4">{{ $stats['total_returns'] }}</div>
                        <div class="stat-kpi-sub text-muted d-none d-sm-block text-truncate">
                            <span class="badge bg-light text-secondary border">Toutes demandes</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-boxes-packing"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('returns.index', ['status' => 'pending']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'pending' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-w-0">
                        <div class="stat-kpi-label text-truncate">En Attente</div>
                        <div class="stat-kpi-value text-warning fs-4">{{ $stats['pending_returns'] }}</div>
                        <div class="stat-kpi-sub text-muted d-none d-sm-block text-truncate">
                            <i class="fas fa-clock text-warning me-1"></i>À traiter
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('returns.index', ['status' => 'approved']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'approved' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-w-0">
                        <div class="stat-kpi-label text-truncate">Approuvés</div>
                        <div class="stat-kpi-value text-info fs-4">{{ $stats['approved_returns'] }}</div>
                        <div class="stat-kpi-sub text-muted d-none d-sm-block text-truncate">
                            <i class="fas fa-check text-info me-1"></i>En réception
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-dolly"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-w-0">
                        <div class="stat-kpi-label text-truncate">Total Remboursé</div>
                        <div class="stat-kpi-value text-success fs-5 text-truncate">{{ currency($stats['total_refunded'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted d-none d-sm-block text-truncate">
                            <i class="fas fa-hand-holding-dollar text-success me-1"></i>Dossiers clôturés
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de Filtres -->
    <div class="brand-filter-bar mb-3 p-3 bg-white rounded-3 border shadow-xs">
        <form method="GET" action="{{ route('returns.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Rechercher par retour #, commande, client..." 
                           value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-6 col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvé</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeté</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complété / Remboursé</option>
                </select>
            </div>

            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                    <i class="fas fa-redo"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tableau Desktop (d-none d-md-block, Zero horizontal scroll) -->
    <div class="brand-table-card d-none d-md-block shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="table-responsive">
            <table class="brand-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem; width: 18%;">Retour #</th>
                        <th style="width: 18%;">Commande</th>
                        <th style="width: 22%;">Client</th>
                        <th style="width: 22%;">Motif du retour</th>
                        <th class="text-center" style="width: 10%;">Statut</th>
                        <th class="text-end" style="padding-right: 1.5rem; width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    @php
                        $st = strtolower($return->status);
                        $statusClass = match($st) {
                            'approved' => 'info',
                            'completed' => 'success',
                            'rejected' => 'danger',
                            default => 'warning'
                        };
                        $statusLabel = match($st) {
                            'approved' => 'Approuvé',
                            'completed' => 'Complété',
                            'rejected' => 'Rejeté',
                            default => 'En attente'
                        };
                    @endphp
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <span class="order-ref-badge fw-bold font-monospace">
                                #{{ $return->return_number }}
                            </span>
                            <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                {{ $return->created_at->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td>
                            @if($return->order)
                            <a href="{{ route('orders.show', $return->order) }}" class="text-dark fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
                                <i class="fas fa-receipt text-danger opacity-75"></i>
                                <span>#{{ $return->order->order_number }}</span>
                            </a>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="brand-avatar flex-shrink-0" style="width: 32px; height: 32px; background: #0f172a; color: white; font-size: 0.78rem; font-weight: 700; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($return->customer->name ?? 'C', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-dark text-truncate">{{ $return->customer->name ?? 'Client inconnu' }}</div>
                                    <div class="text-muted small text-truncate">{{ $return->customer->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 260px;" title="{{ $return->reason }}">
                                {{ $return->reason }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="brand-badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td style="padding-right: 1.5rem;" class="text-end">
                            <div class="action-buttons-group justify-content-end">
                                <a href="{{ route('returns.show', $return) }}" class="action-btn action-btn-view" title="Voir le retour">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($return->status === 'pending')
                                <form action="{{ route('returns.approve', $return) }}" method="POST" class="d-inline" onsubmit="return confirm('Approuver cette demande de retour ?');">
                                    @csrf
                                    <button type="submit" class="action-btn action-btn-success" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('returns.reject', $return) }}" method="POST" class="d-inline" onsubmit="return confirm('Rejeter cette demande de retour ?');">
                                    @csrf
                                    <button type="submit" class="action-btn action-btn-delete" title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="opacity-50 mb-2">
                                <i class="fas fa-rotate-left fa-3x text-muted mb-3"></i>
                                <h6 class="fw-bold text-muted">Aucun retour enregistré</h6>
                                <p class="text-muted small">Toutes les demandes de retour apparaîtront ici.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $returns->links() }}
        </div>
        @endif
    </div>

    <!-- Cartes Tactiles Mobile (d-block d-md-none, Zero horizontal scroll) -->
    <div class="d-block d-md-none mb-4">
        @forelse($returns as $return)
        @php
            $st = strtolower($return->status);
            $statusClass = match($st) {
                'approved' => 'info',
                'completed' => 'success',
                'rejected' => 'danger',
                default => 'warning'
            };
            $statusLabel = match($st) {
                'approved' => 'Approuvé',
                'completed' => 'Complété',
                'rejected' => 'Rejeté',
                default => 'En attente'
            };
        @endphp
        <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="order-ref-badge fw-bold font-monospace" style="font-size: 0.88rem;">
                    #{{ $return->return_number }}
                </span>
                <span class="brand-badge {{ $statusClass }}" style="font-size: 0.72rem;">
                    {{ $statusLabel }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-2 text-muted small">
                <span>
                    <i class="far fa-calendar-alt me-1 text-muted"></i>{{ $return->created_at->format('d/m/Y H:i') }}
                </span>
                @if($return->order)
                <a href="{{ route('orders.show', $return->order) }}" class="text-danger fw-semibold text-decoration-none">
                    Cmd #{{ $return->order->order_number }}
                </a>
                @endif
            </div>

            <!-- Client & Motif -->
            <div class="bg-light rounded-3 p-2.5 mb-2.5 border">
                <div class="d-flex align-items-center gap-2 mb-1.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs" 
                         style="width: 26px; height: 26px; background: #0f172a; color: white; font-size: 0.70rem;">
                        {{ substr($return->customer->name ?? 'C', 0, 1) }}
                    </div>
                    <span class="fw-semibold text-dark text-truncate small">{{ $return->customer->name ?? 'Client' }}</span>
                </div>
                <div class="small text-secondary lh-sm">
                    <strong class="text-dark">Motif :</strong> {{ $return->reason }}
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="d-flex align-items-center justify-content-between gap-1.5 pt-1 border-top">
                <a href="{{ route('returns.show', $return) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 fw-semibold text-dark d-inline-flex align-items-center gap-1">
                    <i class="fas fa-eye text-primary"></i>
                    <span>Détails</span>
                </a>

                @if($return->status === 'pending')
                <div class="d-flex gap-1">
                    <form action="{{ route('returns.approve', $return) }}" method="POST" class="d-inline" onsubmit="return confirm('Approuver ce retour ?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1" style="font-size: 0.76rem;">
                            <i class="fas fa-check me-1"></i> Approuver
                        </button>
                    </form>
                    <form action="{{ route('returns.reject', $return) }}" method="POST" class="d-inline" onsubmit="return confirm('Rejeter ce retour ?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 0.76rem;">
                            <i class="fas fa-times me-1"></i> Rejeter
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded-3 border">
            <i class="fas fa-rotate-left fa-3x text-muted opacity-50 mb-3"></i>
            <h6 class="fw-bold text-muted">Aucun retour enregistré</h6>
            <p class="text-muted small">Toutes les demandes de retour apparaîtront ici.</p>
        </div>
        @endforelse

        @if($returns->hasPages())
        <div class="p-3 d-flex justify-content-center">
            {{ $returns->links() }}
        </div>
        @endif
    </div>
@endsection
