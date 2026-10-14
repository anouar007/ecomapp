@extends('layouts.app')

@section('title', 'Gestionnaire de codes personnalisés')

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-code"></i>
            </div>
            <div>
                <h1 class="brand-title">Codes & Scripts Personnalisés</h1>
                <p class="brand-subtitle">Injectez vos balises de tracking, feuilles de style CSS et scripts JavaScript</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-cog"></i> <span>Paramètres</span>
            </a>
            <a href="{{ route('custom-codes.create') }}" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3.5 py-2 shadow-sm rounded-pill" style="font-size: 0.84rem;">
                <i class="fas fa-plus me-1.5"></i> <span class="d-none d-sm-inline">Nouvel extrait</span><span class="d-sm-none">Ajouter</span>
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KPI Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Total Extraits --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Extraits</div>
                        <div class="stat-kpi-value text-dark">{{ $codes->total() ?? $codes->count() }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">Scripts enregistrés</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scripts Actifs --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Scripts Actifs</div>
                        <div class="stat-kpi-value text-success">{{ $codes->where('is_active', true)->count() }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-circle-check text-success me-1"></i>Exécutés en vitrine
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- En-tête (Head) --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Balises Head</div>
                        <div class="stat-kpi-value text-info">{{ $codes->where('position', 'head')->count() }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-heading text-info me-1"></i>Dans &lt;head&gt;
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-heading"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pied de page (Body) --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Balises Body</div>
                        <div class="stat-kpi-value text-warning">{{ $codes->whereIn('position', ['body_start', 'body_end'])->count() }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-shoe-prints text-warning me-1"></i>Dans &lt;body&gt;
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Desktop Table View --}}
    <div class="brand-table-card d-none d-md-block mb-4">
        <div class="table-responsive">
            <table class="brand-table mb-0">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Détails de l'extrait</th>
                        <th>Type</th>
                        <th>Emplacement</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($codes as $code)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <div class="fw-bold text-dark">{{ $code->title }}</div>
                            <div class="text-muted small">Mis à jour {{ $code->updated_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            @php
                                $typeBadge = match($code->type) {
                                    'css' => 'bg-info-subtle text-info border border-info-subtle',
                                    'js' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    'html' => 'bg-secondary-subtle text-secondary border',
                                    default => 'bg-primary-subtle text-primary'
                                };
                            @endphp
                            <span class="badge rounded-pill px-2.5 py-1 font-monospace {{ $typeBadge }}" style="font-size: 0.75rem;">
                                {{ strtoupper($code->type) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                @php
                                    $pos = match($code->position) {
                                        'head', 'header' => 'En-tête (<head>)',
                                        'body_end', 'footer' => 'Pied de page (Fin <body>)',
                                        'body_start' => 'Début du Body (<body>)',
                                        default => ucwords(str_replace('_', ' ', $code->position))
                                    };
                                @endphp
                                {{ $pos }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-monospace border px-2 py-1">
                                #{{ $code->priority }}
                            </span>
                        </td>
                        <td>
                            @if($code->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                    <i class="fas fa-check-circle me-1"></i> Actif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1">
                                    <i class="fas fa-eye-slash me-1"></i> Inactif
                                </span>
                            @endif
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-1.5">
                                <a href="{{ route('custom-codes.edit', $code) }}" class="btn btn-sm btn-brand-soft-primary rounded-pill px-2.5 py-1" title="Modifier l'extrait">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('custom-codes.destroy', $code) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-brand-soft-danger rounded-pill px-2.5 py-1" onclick="return confirm('Voulez-vous vraiment supprimer cet extrait de code ?')" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5">
                                <div class="rounded-circle bg-light text-muted mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-code"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucun extrait de code trouvé</h5>
                                <p class="text-muted small">Commencez par ajouter votre premier extrait CSS, JS ou HTML.</p>
                                <a href="{{ route('custom-codes.create') }}" class="btn btn-brand-primary rounded-pill px-4 py-2 mt-2">
                                    <i class="fas fa-plus me-1.5"></i> Ajouter un extrait
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($codes->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $codes->links() }}
        </div>
        @endif
    </div>

    {{-- Mobile Touch Cards View (Zero horizontal scroll) --}}
    <div class="d-md-none mb-4">
        @forelse($codes as $code)
        <div class="mobile-custom-code-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    @php
                        $typeBadge = match($code->type) {
                            'css' => 'bg-info-subtle text-info border border-info-subtle',
                            'js' => 'bg-warning-subtle text-warning border border-warning-subtle',
                            'html' => 'bg-secondary-subtle text-secondary border',
                            default => 'bg-primary-subtle text-primary'
                        };
                    @endphp
                    <span class="badge rounded-pill px-2 py-0.5 font-monospace {{ $typeBadge }}" style="font-size: 0.72rem;">
                        {{ strtoupper($code->type) }}
                    </span>
                    <span class="badge bg-light text-muted border px-1.5 py-0.5 font-monospace" style="font-size: 0.72rem;">
                        #{{ $code->priority }}
                    </span>
                </div>
                @if($code->is_active)
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 small">
                        Actif
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-0.5 small">
                        Inactif
                    </span>
                @endif
            </div>

            <h6 class="fw-bold text-dark mb-1">{{ $code->title }}</h6>
            <div class="small text-muted mb-2.5">
                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                @php
                    $pos = match($code->position) {
                        'head', 'header' => 'En-tête (<head>)',
                        'body_end', 'footer' => 'Pied de page (Fin <body>)',
                        'body_start' => 'Début du Body (<body>)',
                        default => ucwords(str_replace('_', ' ', $code->position))
                    };
                @endphp
                {{ $pos }}
                <span class="mx-1">&bull;</span>
                {{ $code->updated_at->diffForHumans() }}
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2 border-top pt-2">
                <a href="{{ route('custom-codes.edit', $code) }}" class="btn btn-sm btn-brand-soft-primary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1">
                    <i class="fas fa-edit"></i>
                    <span>Modifier</span>
                </a>
                <form action="{{ route('custom-codes.destroy', $code) }}" method="POST" class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-brand-soft-danger rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1" onclick="return confirm('Voulez-vous vraiment supprimer cet extrait de code ?')">
                        <i class="fas fa-trash-alt"></i>
                        <span>Supprimer</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <div class="rounded-circle bg-light text-muted mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 22px;">
                <i class="fas fa-code"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Aucun extrait de code</h6>
            <p class="text-muted small mb-3">Ajoutez un extrait pour injecter du code sur votre boutique.</p>
            <div>
                <a href="{{ route('custom-codes.create') }}" class="btn btn-brand-primary rounded-pill px-4 py-2">
                    <i class="fas fa-plus me-1.5"></i> Ajouter un extrait
                </a>
            </div>
        </div>
        @endforelse

        @if($codes->hasPages())
        <div class="py-2">
            {{ $codes->links() }}
        </div>
        @endif
    </div>
@endsection
