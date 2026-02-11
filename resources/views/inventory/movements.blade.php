@extends('layouts.app')

@section('title', 'Inventory Movements')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-history"></i> Inventory Movements</h1>
            <p class="page-subtitle">Complete audit trail of all stock changes</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.movements') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <select name="type" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">All Types</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
            </select>
            
            <input type="text" name="product" class="form-control" style="flex: 1; min-width: 200px;" 
                   placeholder="Product name..." value="{{ request('product') }}">
            
            <input type="date" name="date_from" class="form-control" style="width: auto;" 
                   value="{{ request('date_from') }}" placeholder="From">
            
            <input type="date" name="date_to" class="form-control" style="width: auto;" 
                   value="{{ request('date_to') }}" placeholder="To">
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('inventory.movements') }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
        </form>
    </div>
</div>

<!-- Movements Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Movement History ({{ $movements->total() }})</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Before</th>
                    <th>After</th>
                    <th>Reason</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>
                        <div>{{ $movement->created_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $movement->created_at->format('H:i:s') }}</small>
                    </td>
                    <td>
                        <div>
                            <strong>{{ $movement->product->name }}</strong>
                            <br><small class="text-muted"><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 11px;">{{ $movement->product->sku }}</code></small>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $movement->type_color }}">
                            {{ $movement->type_label }}
                        </span>
                    </td>
                    <td>
                        <strong style="color: {{ $movement->quantity > 0 ? '#10b981' : '#ef4444' }};">
                            {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                        </strong>
                    </td>
                    <td><span class="badge badge-secondary">{{ $movement->stock_before }}</span></td>
                    <td><span class="badge badge-primary">{{ $movement->stock_after }}</span></td>
                    <td>
                        <div>{{ Str::limit($movement->reason ?? 'No reason provided', 50) }}</div>
                        @if($movement->reference_type && $movement->reference_id)
                            <small class="text-muted">
                                Ref: {{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}
                            </small>
                        @endif
                    </td>
                    <td>
                        @if($movement->createdBy)
                            <div>{{ $movement->createdBy->name }}</div>
                            <small class="text-muted">{{ $movement->createdBy->email }}</small>
                        @else
                            <span class="text-muted">System</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="fas fa-history"></i>
                        <p>No movement history found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="card-footer">
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endsection
