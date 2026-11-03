@extends('layouts.app')

@section('title', 'Debtors Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
.debtors-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2.5rem;
    border-radius: 16px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.stat-card-pro {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid;
    position: relative;
    overflow: hidden;
}

.stat-card-pro:hover {
    transform: translateY(-4px);
    shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.stat-card-pro::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    opacity: 0.1;
    background: currentColor;
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.stat-icon-pro {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 1rem;
}

.filter-pills {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin: 1rem 0;
}

.filter-pill {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    background: #f8f9fa;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.875rem;
    font-weight: 500;
}

.filter-pill:hover {
    border-color: #667eea;
    background: #f0f3ff;
}

.filter-pill.active {
    background: #667eea;
    color: white;
}

.debtor-row {
    transition: all 0.2s;
}

.debtor-row:hover {
    background: #f8f9fa;
}

.progress-bar-custom {
    height: 6px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 8px;
}

.progress-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.action-menu {
    position: relative;
}

.action-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 160px;
    z-index: 1000;
    display: none;
}

.action-dropdown.show {
    display: block;
}

.action-dropdown-item {
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s;
}

.action-dropdown-item:hover {
    background: #f8f9fa;
    color: #667eea;
}

.risk-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.risk-high { background: #fee2e2; color: #991b1b; }
.risk-medium { background: #fef3c7; color: #92400e; }
.risk-low { background: #dcfce7; color: #166534; }
</style>
@endpush

@section('content')
<!-- Hero Header -->
<div class="debtors-hero mb-3 mb-md-4 p-3 p-md-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1 text-white d-flex align-items-center gap-2">
                <i class="fas fa-chart-line"></i>
                <span>Créances & Débiteurs</span>
            </h1>
            <p class="mb-0 text-white-50 small">
                Suivez, gérez et recouvrez efficacement les paiements impayés
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('debtors.download') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold">
                <i class="fas fa-download me-1.5 text-primary"></i> Export PDF
            </a>
            <button class="btn btn-light btn-sm rounded-pill px-3 fw-semibold" onclick="window.print()">
                <i class="fas fa-print me-1.5 text-secondary"></i> Imprimer
            </button>
        </div>
    </div>
</div>

<!-- Enhanced Statistics -->
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-3">
        <div class="stat-card-pro p-3" style="border-left-color: #ef4444;">
            <div class="stat-icon-pro" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; width: 34px; height: 34px; font-size: 0.85rem;">
                <i class="fas fa-users"></i>
            </div>
            <div class="small text-muted mb-1 text-truncate">Total Débiteurs</div>
            <div class="fw-bold text-dark fs-4">{{ $stats['total_debtors'] }}</div>
            <div class="small text-muted text-truncate d-none d-sm-block mt-1" style="font-size: 0.72rem;">
                <i class="fas fa-info-circle me-1"></i>Clients avec solde
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-3">
        <div class="stat-card-pro p-3" style="border-left-color: #f59e0b;">
            <div class="stat-icon-pro" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; width: 34px; height: 34px; font-size: 0.85rem;">
                <i class="fas fa-coins"></i>
            </div>
            <div class="small text-muted mb-1 text-truncate">Encours Total</div>
            <div class="fw-bold text-dark fs-5 text-truncate">{{ currency($stats['total_outstanding']) }}</div>
            <div class="small text-muted text-truncate d-none d-sm-block mt-1" style="font-size: 0.72rem;">
                <i class="fas fa-trending-up me-1"></i>Montant à recouvrer
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-3">
        <div class="stat-card-pro p-3" style="border-left-color: #dc2626;">
            <div class="stat-icon-pro" style="background: rgba(220, 38, 38, 0.1); color: #dc2626; width: 34px; height: 34px; font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="small text-muted mb-1 text-truncate">Plafond Dépassé</div>
            <div class="fw-bold text-danger fs-4">{{ $stats['over_limit_count'] }}</div>
            <div class="small text-muted text-truncate d-none d-sm-block mt-1" style="font-size: 0.72rem;">
                <i class="fas fa-ban me-1"></i>Action immédiate
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-3">
        <div class="stat-card-pro p-3" style="border-left-color: #667eea;">
            <div class="stat-icon-pro" style="background: rgba(102, 126, 234, 0.1); color: #667eea; width: 34px; height: 34px; font-size: 0.85rem;">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="small text-muted mb-1 text-truncate">Taux de Maîtrise</div>
            <div class="fw-bold text-dark fs-5">
                {{ $stats['total_debtors'] > 0 ? number_format(($stats['total_debtors'] - $stats['over_limit_count']) / $stats['total_debtors'] * 100, 1) : 0 }}%
            </div>
            <div class="small text-muted text-truncate d-none d-sm-block mt-1" style="font-size: 0.72rem;">
                <i class="fas fa-chart-pie me-1"></i>Dans les plafonds
            </div>
        </div>
    </div>
</div>

<!-- Advanced Filters -->
<div class="card mb-4" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div class="card-body">
        <form action="{{ route('debtors.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Name, email, phone..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Balance Range</label>
                    <select name="balance_range" class="form-select">
                        <option value="">All Amounts</option>
                        <option value="0-500" {{ request('balance_range') == '0-500' ? 'selected' : '' }}>$0 - $500</option>
                        <option value="500-1000" {{ request('balance_range') == '500-1000' ? 'selected' : '' }}>$500 - $1,000</option>
                        <option value="1000-5000" {{ request('balance_range') == '1000-5000' ? 'selected' : '' }}>$1,000 - $5,000</option>
                        <option value="5000+" {{ request('balance_range') == '5000+' ? 'selected' : '' }}>$5,000+</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Risk Level</label>
                    <select name="risk" class="form-select">
                        <option value="">All Risks</option>
                        <option value="high" {{ request('risk') == 'high' ? 'selected' : '' }}>High Risk</option>
                        <option value="medium" {{ request('risk') == 'medium' ? 'selected' : '' }}>Medium Risk</option>
                        <option value="low" {{ request('risk') == 'low' ? 'selected' : '' }}>Low Risk</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="balance_desc" {{ request('sort') == 'balance_desc' ? 'selected' : '' }}>Balance: High to Low</option>
                        <option value="balance_asc" {{ request('sort') == 'balance_asc' ? 'selected' : '' }}>Balance: Low to High</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('debtors.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Debtors Table & Mobile Cards -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom p-3 px-md-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold fs-6 fs-md-5">
                <i class="fas fa-users-viewfinder text-danger me-2"></i>Comptes Débiteurs ({{ $debtors->total() }})
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="selectAll()">
                    <i class="fas fa-check-square me-1"></i> Tout cocher
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="bulkEmail()">
                    <i class="fas fa-envelope me-1"></i> Email groupé
                </button>
            </div>
        </div>
    </div>

    {{-- Desktop Table (d-none d-md-block) --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 40px; padding-left: 1.5rem;">
                        <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                    </th>
                    <th>Client</th>
                    <th>Contact</th>
                    <th class="text-end">Plafond Crédit</th>
                    <th class="text-end">Encours Dû</th>
                    <th class="text-center" style="width: 140px;">Utilisation</th>
                    <th class="text-center">Risque</th>
                    <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debtors as $debtor)
                @php
                    $utilization = $debtor->credit_limit > 0 ? ($debtor->current_balance / $debtor->credit_limit * 100) : 0;
                    $riskLevel = $debtor->hasReachedCreditLimit() ? 'high' : ($utilization > 75 ? 'medium' : 'low');
                    $riskClass = ['high' => 'risk-high', 'medium' => 'risk-medium', 'low' => 'risk-low'][$riskLevel];
                    $riskLabel = ['high' => 'Élevé', 'medium' => 'Moyen', 'low' => 'Faible'][$riskLevel];
                @endphp
                <tr class="debtor-row">
                    <td style="padding-left: 1.5rem;">
                        <input type="checkbox" class="form-check-input debtor-checkbox" value="{{ $debtor->id }}">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs" 
                                     style="width: 36px; height: 36px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; font-size: 0.82rem;">
                                    {{ substr($debtor->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-semibold text-dark text-truncate">{{ $debtor->name }}</div>
                                <div class="small text-muted text-truncate">{{ $debtor->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small">
                            @if($debtor->phone)
                                <a href="tel:{{ $debtor->phone }}" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                                    <i class="fas fa-phone text-muted"></i> {{ $debtor->phone }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-end">
                        @if($debtor->credit_limit > 0)
                            <span class="fw-semibold">{{ currency($debtor->credit_limit) }}</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Illimité</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <span class="fw-bold text-danger fs-6">
                            {{ currency($debtor->current_balance) }}
                        </span>
                    </td>
                    <td>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted" style="font-size: 0.74rem;">{{ number_format($utilization, 0) }}%</span>
                            </div>
                            <div class="progress-bar-custom" style="height: 6px;">
                                <div class="progress-fill" 
                                     style="width: {{ min($utilization, 100) }}%; height: 100%; border-radius: 9999px; background: {{ $utilization > 90 ? '#ef4444' : ($utilization > 75 ? '#f59e0b' : '#10b981') }};"></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="risk-badge {{ $riskClass }} px-2 py-0.5 rounded-pill small">
                            <i class="fas fa-{{ $riskLevel == 'high' ? 'exclamation-circle' : ($riskLevel == 'medium' ? 'exclamation-triangle' : 'check-circle') }} me-1"></i>
                            {{ $riskLabel }}
                        </span>
                    </td>
                    <td style="padding-right: 1.5rem;" class="text-end">
                        <div class="action-buttons-group justify-content-end">
                            <a href="{{ route('customers.show', $debtor) }}" 
                               class="action-btn action-btn-view" title="Voir le compte">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="mailto:{{ $debtor->email }}" 
                               class="action-btn action-btn-email" title="Envoyer un email">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <a href="{{ route('invoices.create') }}?customer_email={{ $debtor->email }}" 
                               class="action-btn action-btn-success" title="Créer une facture">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div style="opacity: 0.5;">
                            <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                            <h5 class="text-muted">Aucun débiteur trouvé</h5>
                            <p class="text-muted small">Tous les clients ont réglé leurs soldes !</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Touch Cards (d-block d-md-none, zero horizontal scroll) --}}
    <div class="d-block d-md-none p-2.5">
        @forelse($debtors as $debtor)
        @php
            $utilization = $debtor->credit_limit > 0 ? ($debtor->current_balance / $debtor->credit_limit * 100) : 0;
            $riskLevel = $debtor->hasReachedCreditLimit() ? 'high' : ($utilization > 75 ? 'medium' : 'low');
            $riskClass = ['high' => 'risk-high', 'medium' => 'risk-medium', 'low' => 'risk-low'][$riskLevel];
            $riskLabel = ['high' => 'Risque Élevé', 'medium' => 'Risque Moyen', 'low' => 'Risque Faible'][$riskLevel];
        @endphp
        <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" class="form-check-input debtor-checkbox m-0" value="{{ $debtor->id }}" style="width: 18px; height: 18px;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs" 
                         style="width: 32px; height: 32px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; font-size: 0.78rem;">
                        {{ substr($debtor->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <h6 class="fw-bold mb-0 text-dark text-truncate" style="font-size: 0.92rem;">{{ $debtor->name }}</h6>
                    </div>
                </div>
                <span class="risk-badge {{ $riskClass }} px-2 py-0.5 rounded-pill" style="font-size: 0.70rem;">
                    <i class="fas fa-{{ $riskLevel == 'high' ? 'exclamation-circle' : ($riskLevel == 'medium' ? 'exclamation-triangle' : 'check-circle') }} me-1"></i>
                    {{ $riskLabel }}
                </span>
            </div>

            <!-- Balances Row -->
            <div class="bg-light rounded-3 p-2.5 mb-2.5 border">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small">Solde Dû :</span>
                    <span class="fw-bold text-danger fs-6">{{ currency($debtor->current_balance) }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between small text-muted mb-1.5">
                    <span>Plafond : {{ $debtor->credit_limit > 0 ? currency($debtor->credit_limit) : 'Illimité' }}</span>
                    <span>{{ number_format($utilization, 0) }}% utilisé</span>
                </div>
                <div class="progress" style="height: 5px; border-radius: 9999px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ min($utilization, 100) }}%; background: {{ $utilization > 90 ? '#ef4444' : ($utilization > 75 ? '#f59e0b' : '#10b981') }};"></div>
                </div>
            </div>

            <!-- Contact & Actions -->
            <div class="d-flex align-items-center justify-content-between gap-1 pt-1 border-top">
                <div class="d-flex gap-1.5">
                    @if($debtor->phone)
                    <a href="tel:{{ $debtor->phone }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" style="font-size: 0.76rem;" title="Appeler">
                        <i class="fas fa-phone text-success me-1"></i> Appeler
                    </a>
                    @endif
                    @if($debtor->email)
                    <a href="mailto:{{ $debtor->email }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" style="font-size: 0.76rem;" title="Email">
                        <i class="fas fa-envelope text-info me-1"></i> Email
                    </a>
                    @endif
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('customers.show', $debtor) }}" class="btn btn-sm btn-light rounded-circle border" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Détails">
                        <i class="fas fa-eye text-primary"></i>
                    </a>
                    <a href="{{ route('invoices.create') }}?customer_email={{ $debtor->email }}" class="btn btn-sm btn-light rounded-circle border" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Facturer">
                        <i class="fas fa-file-invoice text-success"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50"></i>
            <h6 class="fw-bold text-muted">Aucun débiteur trouvé</h6>
            <p class="text-muted small">Tous les clients ont réglé leurs soldes !</p>
        </div>
        @endforelse
    </div>
    @if($debtors->hasPages())
    <div class="card-footer bg-white border-top">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $debtors->firstItem() }} to {{ $debtors->lastItem() }} of {{ $debtors->total() }} debtors
            </div>
            {{ $debtors->links() }}
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('select-all-checkbox')?.addEventListener('change', function() {
    document.querySelectorAll('.debtor-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function selectAll() {
    document.querySelectorAll('.debtor-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function bulkEmail() {
    const selected = Array.from(document.querySelectorAll('.debtor-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Please select at least one debtor');
        return;
    }
    
    alert(`Bulk email feature coming soon! Selected: ${selected.length} debtors`);
}
</script>
@endpush
@endsection
