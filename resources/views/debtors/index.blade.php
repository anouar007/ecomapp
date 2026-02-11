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
<div class="debtors-hero">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                <i class="fas fa-chart-line me-2"></i> Debtors Management
            </h1>
            <p style="opacity: 0.9; font-size: 1rem; margin: 0;">
                Track, manage, and collect outstanding payments efficiently
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('debtors.download') }}" class="btn btn-light">
                <i class="fas fa-download me-2"></i> Export PDF
            </a>
            <button class="btn btn-light" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Print
            </button>
        </div>
    </div>
</div>

<!-- Enhanced Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card-pro" style="border-left-color: #ef4444;">
            <div class="stat-icon-pro" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <i class="fas fa-users"></i>
            </div>
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Total Debtors</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['total_debtors'] }}</div>
            <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">
                <i class="fas fa-info-circle"></i> Customers with outstanding balance
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card-pro" style="border-left-color: #f59e0b;">
            <div class="stat-icon-pro" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="fas fa-coins"></i>
            </div>
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Total Outstanding</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ currency($stats['total_outstanding']) }}</div>
            <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">
                <i class="fas fa-trending-up"></i> Amount to collect
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card-pro" style="border-left-color: #dc2626;">
            <div class="stat-icon-pro" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Over Credit Limit</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['over_limit_count'] }}</div>
            <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">
                <i class="fas fa-ban"></i> Requires immediate action
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card-pro" style="border-left-color: #667eea;">
            <div class="stat-icon-pro" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                <i class="fas fa-percentage"></i>
            </div>
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Collection Rate</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">
                {{ $stats['total_debtors'] > 0 ? number_format(($stats['total_debtors'] - $stats['over_limit_count']) / $stats['total_debtors'] * 100, 1) : 0 }}%
            </div>
            <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">
                <i class="fas fa-chart-pie"></i> Within credit limits
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

<!-- Debtors Table -->
<div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div class="card-header bg-white border-bottom" style="padding: 1.25rem 1.5rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Outstanding Accounts ({{ $debtors->total() }})</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="fas fa-check-square"></i> Select All
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="bulkEmail()">
                    <i class="fas fa-envelope"></i> Bulk Email
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 40px; padding-left: 1.5rem;">
                        <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                    </th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th class="text-end">Credit Limit</th>
                    <th class="text-end">Outstanding</th>
                    <th class="text-center">Utilization</th>
                    <th class="text-center">Risk</th>
                    <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debtors as $debtor)
                @php
                    $utilization = $debtor->credit_limit > 0 ? ($debtor->current_balance / $debtor->credit_limit * 100) : 0;
                    $riskLevel = $debtor->hasReachedCreditLimit() ? 'high' : ($utilization > 75 ? 'medium' : 'low');
                    $riskClass = ['high' => 'risk-high', 'medium' => 'risk-medium', 'low' => 'risk-low'][$riskLevel];
                @endphp
                <tr class="debtor-row">
                    <td style="padding-left: 1.5rem;">
                        <input type="checkbox" class="form-check-input debtor-checkbox" value="{{ $debtor->id }}">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600;">
                                    {{ substr($debtor->name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $debtor->name }}</div>
                                <div class="small text-muted">{{ $debtor->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small">
                            @if($debtor->phone)
                                <i class="fas fa-phone text-muted me-1"></i> {{ $debtor->phone }}
                            @else
                                <span class="text-muted">No phone</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-end">
                        @if($debtor->credit_limit > 0)
                            <span class="fw-semibold">{{ currency($debtor->credit_limit) }}</span>
                        @else
                            <span class="badge bg-secondary">Unlimited</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <span class="fw-bold" style="color: #ef4444; font-size: 1.1rem;">
                            {{ currency($debtor->current_balance) }}
                        </span>
                    </td>
                    <td>
                        <div style="min-width: 120px;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">{{ number_format($utilization, 0) }}%</span>
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" 
                                     style="width: {{ min($utilization, 100) }}%; background: {{ $utilization > 90 ? '#ef4444' : ($utilization > 75 ? '#f59e0b' : '#10b981') }};"></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="risk-badge {{ $riskClass }}">
                            <i class="fas fa-{{ $riskLevel == 'high' ? 'exclamation-circle' : ($riskLevel == 'medium' ? 'exclamation-triangle' : 'check-circle') }}"></i>
                            {{ ucfirst($riskLevel) }}
                        </span>
                    </td>
                    <td style="padding-right: 1.5rem;">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('customers.show', $debtor) }}" 
                               class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="mailto:{{ $debtor->email }}" 
                               class="btn btn-sm btn-outline-info" title="Send Email">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <a href="{{ route('invoices.create') }}?customer_email={{ $debtor->email }}" 
                               class="btn btn-sm btn-outline-success" title="Create Invoice">
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
                            <h5 class="text-muted">No Debtors Found</h5>
                            <p class="text-muted small">All customers have cleared their balances!</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
