@extends('layouts.app')

@section('title', __('Inventory Movements'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
    .movement-card {
        border-radius: var(--radius-lg);
        background: white;
        padding: 1rem;
        border: 1px solid var(--border-color);
        margin-bottom: 1rem;
        transition: var(--transition-base);
    }
    .movement-card:hover {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-md);
    }
    .movement-type-badge {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .qty-display {
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1;
    }
    .variant-line {
        font-size: 0.75rem;
        color: var(--text-muted);
        background: #f8fafc;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    @media (max-width: 991px) {
        .filter-collapse {
            display: none;
        }
        .filter-collapse.show {
            display: block;
        }
    }
</style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="brand-header px-1">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-history"></i>
                </div>
                {{ __('Movement History') }}
            </h1>
            <p class="brand-subtitle">{{ __('Complete audit trail of all warehouse activities.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-brand-light d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterCard">
                <i class="fas fa-filter me-1"></i> {{ __('Filter') }}
            </button>
            <a href="{{ route('inventory.index') }}" class="btn btn-brand-primary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Inventory') }}
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="collapse d-lg-block mb-4" id="filterCard">
        <div class="brand-table-card p-4">
            <form method="GET" action="{{ route('inventory.movements') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">{{ __('Movement Type') }}</label>
                    <select name="type" class="form-select custom-select-premium">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>{{ __('Stock In') }}</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>{{ __('Stock Out') }}</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>{{ __('Adjustment') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">{{ __('Date From') }}</label>
                    <input type="date" name="start_date" class="form-control brand-input font-inter" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">{{ __('Date To') }}</label>
                    <input type="date" name="end_date" class="form-control brand-input font-inter" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-brand-primary flex-grow-1">
                        <i class="fas fa-search me-1"></i> {{ __('Search') }}
                    </button>
                    <a href="{{ route('inventory.movements') }}" class="btn btn-brand-light">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Movements List -->
    <div class="brand-table-card overflow-hidden">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="dashboard-card-title m-0">
                <i class="fas fa-list text-primary me-2"></i> {{ __('Activity Log') }}
            </h5>
            <span class="badge bg-light text-primary font-inter">{{ $movements->total() }}</span>
        </div>

        <div class="responsive-table-container">
            <!-- Desktop Table -->
            <table class="brand-table d-none d-lg-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">{{ __('Date & Time') }}</th>
                        <th>{{ __('Product Details') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th class="text-center">{{ __('Change') }}</th>
                        <th class="text-center">{{ __('Result') }}</th>
                        <th>{{ __('Reference/Operator') }}</th>
                    </tr>
                </thead>
                <tbody class="font-inter">
                    @forelse($movements as $movement)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark small">{{ $movement->created_at->format('M d, Y') }}</div>
                            <div class="text-muted" style="font-size: 11px;">{{ $movement->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small mb-1">{{ $movement->product->translated_name }}</div>
                            @if($movement->variant)
                                <div class="variant-line">
                                    @if($movement->variant->color)
                                        <span class="color-dot" style="width: 8px; height: 8px; border-radius: 50%; background: {{ $movement->variant->color }}"></span>
                                    @endif
                                    {{ $movement->variant->size }} · {{ $movement->variant->sku }}
                                </div>
                            @else
                                <div class="text-muted small">SKU: {{ $movement->product->sku }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="movement-type-badge bg-{{ $movement->type_color }} bg-opacity-10 text-{{ $movement->type_color }}">
                                {{ __($movement->type_label) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold fs-6 {{ $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="text-muted small mb-1">{{ __('Final Stock') }}</div>
                            <span class="badge bg-soft-primary text-primary px-3 rounded-pill">{{ $movement->stock_after }}</span>
                        </td>
                        <td>
                            <div class="small text-dark">{{ $movement->reason ?: __('No context provided') }}</div>
                            <div class="text-muted" style="font-size: 10px;">
                                <i class="fas fa-user-circle me-1"></i> {{ $movement->creator ? $movement->creator->name : __('System') }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-history text-muted opacity-25 fs-1 mb-3"></i>
                            <p class="text-muted">{{ __('No inventory movements recorded yet.') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Mobile Card List -->
            <div class="d-lg-none p-3">
                @forelse($movements as $movement)
                <div class="movement-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="min-width-0 pe-3">
                            <div class="fw-bold text-dark small text-truncate">{{ $movement->product->translated_name }}</div>
                            <div class="text-muted mt-1" style="font-size: 11px;">
                                <i class="far fa-clock me-1"></i> {{ $movement->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span class="movement-type-badge bg-{{ $movement->type_color }} bg-opacity-10 text-{{ $movement->type_color }}">
                            {{ __($movement->type_label) }}
                        </span>
                    </div>

                    @if($movement->variant)
                    <div class="mb-3">
                        <div class="variant-line">
                            @if($movement->variant->color)
                                <span class="color-dot" style="width: 8px; height: 8px; border-radius: 50%; background: {{ $movement->variant->color }}"></span>
                            @endif
                            <span class="fw-bold">{{ $movement->variant->size }}</span> · {{ $movement->variant->sku }}
                        </div>
                    </div>
                    @endif

                    <div class="p-3 bg-light border-radius-lg d-flex justify-content-between align-items-center mb-3" style="border-radius: 12px;">
                        <div>
                            <div class="small text-muted mb-1">{{ __('Movement') }}</div>
                            <div class="qty-display {{ $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted mb-1">{{ __('Before → After') }}</div>
                            <div class="fw-bold font-inter text-dark">
                                {{ $movement->stock_before }} <i class="fas fa-arrow-right mx-1 text-muted" style="font-size: 0.7rem;"></i> {{ $movement->stock_after }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="brand-avatar-ring">
                            <div class="brand-avatar" style="width: 24px; height: 24px; font-size: 10px; background: white; border: 1px solid #e2e8f0;">
                                {{ strtoupper(substr($movement->creator ? $movement->creator->name : 'S', 0, 1)) }}
                            </div>
                        </div>
                        <div class="small text-muted text-truncate italic">
                            "{{ $movement->reason ?: __('No context') }}"
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <p class="text-muted">{{ __('No records found.') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        @if($movements->hasPages())
        <div class="px-4 py-3 border-top bg-light bg-opacity-50">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
@endsection
