@extends('layouts.app')

@section('title', "Détail de l'Activité #" . $activityLog->id)

@push('styles')
<style>
.diff-old {
    background-color: #fff1f2 !important;
    color: #be123c !important;
    font-family: monospace;
    font-size: 0.85rem;
    border-left: 3px solid #f43f5e;
}
.diff-new {
    background-color: #f0fdf4 !important;
    color: #15803d !important;
    font-family: monospace;
    font-size: 0.85rem;
    border-left: 3px solid #22c55e;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-2 py-md-3">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-light rounded-circle shadow-xs border p-2" title="Retour">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h5 fw-bold mb-0 text-dark">Activité #{{ $activityLog->id }}</h1>
                <p class="text-muted small mb-0">{{ $activityLog->description }}</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {!! $activityLog->event_badge !!}
            @if($activityLog->source_url)
                <a href="{{ $activityLog->source_url }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                    <i class="fas fa-arrow-up-right-from-square me-1"></i> Fiche source
                </a>
            @endif
        </div>
    </div>

    <!-- Metadata Card -->
    <div class="card border-0 rounded-4 shadow-xs bg-white p-3.5 mb-3">
        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-info-circle text-primary"></i> Métadonnées de l'Événement
        </h6>

        <div class="row g-3">
            <div class="col-12 col-sm-6 col-md-3">
                <span class="text-muted small d-block mb-1">Opérateur :</span>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0" 
                         style="width: 32px; height: 32px; background: linear-gradient(135deg, #334155 0%, #0f172a 100%); font-size: 0.8rem;">
                        {{ strtoupper(substr($activityLog->user ? $activityLog->user->name : ($activityLog->properties['user_name'] ?? 'S'), 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <strong class="text-dark d-block text-truncate small">{{ $activityLog->user ? $activityLog->user->name : ($activityLog->properties['user_name'] ?? 'Système') }}</strong>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">{{ $activityLog->user ? $activityLog->user->email : ($activityLog->properties['user_email'] ?? '') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <span class="text-muted small d-block mb-1">Date & Heure :</span>
                <strong class="text-dark d-block small">{{ $activityLog->created_at->format('d/m/Y à H:i:s') }}</strong>
                <small class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $activityLog->created_at->diffForHumans() }}</small>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <span class="text-muted small d-block mb-1">Élément concerné :</span>
                <strong class="text-dark d-block small font-monospace">{{ $activityLog->subject_label }}</strong>
                <small class="text-muted" style="font-size: 0.72rem;">{{ $activityLog->subject_type ?? 'N/A' }}</small>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <span class="text-muted small d-block mb-1">Adresse IP & Réseau :</span>
                <span class="badge bg-light text-dark border font-monospace">{{ $activityLog->ip_address ?? '—' }}</span>
                @if($activityLog->user_agent)
                    <div class="text-muted small text-truncate mt-1" style="font-size: 0.72rem;" title="{{ $activityLog->user_agent }}">
                        <i class="fas fa-desktop me-1"></i>{{ $activityLog->user_agent_summary }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Changes Card (Diff: Old Data vs New Data) -->
    <div class="card border-0 rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark fs-6">
                <i class="fas fa-code-compare text-primary me-2"></i>Comparatif des Valeurs (Avant ➔ Après)
            </h6>
            <span class="badge bg-light text-secondary border rounded-pill">
                {{ count($activityLog->changes) }} attributs modifiés
            </span>
        </div>

        @if(count($activityLog->changes) > 0)
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; width: 25%;">Champ / Attribut</th>
                        <th class="py-3 text-danger text-uppercase fw-semibold" style="font-size: 0.74rem; width: 37.5%;">— Ancienne Valeur (Avant)</th>
                        <th class="pe-4 py-3 text-success text-uppercase fw-semibold" style="font-size: 0.74rem; width: 37.5%;">+ Nouvelle Valeur (Après)</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($activityLog->changes as $attribute => $values)
                    <tr>
                        <td class="ps-4 py-3 fw-bold text-dark font-monospace small">
                            {{ $attribute }}
                        </td>
                        <td class="py-3 diff-old">
                            @if($values['old'] !== null)
                                {{ is_array($values['old']) ? json_encode($values['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $values['old'] }}
                            @else
                                <span class="text-muted fst-italic">Vide / Initial</span>
                            @endif
                        </td>
                        <td class="pe-4 py-3 diff-new">
                            @if($values['new'] !== null)
                                {{ is_array($values['new']) ? json_encode($values['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $values['new'] }}
                            @else
                                <span class="text-muted fst-italic">Supprimé / Nul</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-4 text-center text-muted">
            <i class="fas fa-circle-info fa-2x mb-2 opacity-50"></i>
            <p class="mb-0 small">Aucune modification spécifique avant/après enregistrée pour cet événement.</p>
        </div>
        @endif
    </div>

    @php
        $extraProps = collect($activityLog->properties ?? [])->except(['old', 'new'])->toArray();
    @endphp
    @if(!empty($extraProps))
    <div class="card border-0 rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark fs-6">
                <i class="fas fa-brackets-curly text-secondary me-2"></i>Propriétés & Contexte Enregistré
            </h6>
            <span class="badge bg-light text-secondary border rounded-pill">{{ count($extraProps) }} clé(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <tbody class="divide-y">
                    @foreach($extraProps as $propKey => $propValue)
                    <tr>
                        <td class="ps-4 py-2.5 fw-bold text-dark font-monospace small" style="width: 28%;">{{ $propKey }}</td>
                        <td class="pe-4 py-2.5 font-monospace small text-secondary">
                            @if(is_array($propValue))
                                <pre class="mb-0 bg-light p-2 rounded-2 border" style="font-size: 0.78rem; white-space: pre-wrap;">{{ json_encode($propValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @elseif(is_bool($propValue))
                                <span class="badge {{ $propValue ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $propValue ? 'true' : 'false' }}</span>
                            @else
                                {{ $propValue ?? '—' }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
