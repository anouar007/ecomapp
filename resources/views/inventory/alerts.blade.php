@extends('layouts.app')

@section('title', 'Stock Alerts')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-exclamation-triangle"></i> Stock Alerts</h1>
            <p class="page-subtitle">Low stock and out of stock notifications</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
            @if($stats['unacknowledged'] > 0)
            <form action="{{ route('inventory.bulk-acknowledge') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-double"></i> Acknowledge All ({{ $stats['unacknowledged'] }})
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

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div style="background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%); border-radius: 16px; padding: 24px; border: 1px solid #fde68a;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #92400e; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Unacknowledged</p>
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
                <p style="color: #991b1b; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Out of Stock</p>
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
                <p style="color: #9a3412; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Low Stock</p>
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
                <p style="color: #166534; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Acknowledged</p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;">{{ $stats['acknowledged'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.alerts') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <select name="status" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">All Alerts</option>
                <option value="unacknowledged" {{ request('status') == 'unacknowledged' ? 'selected' : '' }}>Unacknowledged</option>
                <option value="acknowledged" {{ request('status') == 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
            </select>
            
            <select name="type" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">All Types</option>
                <option value="out_of_stock" {{ request('type') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                <option value="low_stock" {{ request('type') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
            </select>
            
            <input type="text" name="product" class="form-control" style="flex: 1; min-width: 200px;" 
                   placeholder="Search product..." value="{{ request('product') }}">
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('inventory.alerts') }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
        </form>
    </div>
</div>

<!-- Alerts Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-bell"></i> Active Alerts ({{ $alerts->total() }})</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Alert Type</th>
                    <th>Current Stock</th>
                    <th>Threshold</th>
                    <th>Triggered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr>
                    <td>
                        <div>
                            <strong>{{ $alert->product->name }}</strong>
                            <br><small class="text-muted"><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 11px;">{{ $alert->product->sku }}</code></small>
                        </div>
                    </td>
                    <td>
                        @if($alert->alert_type === 'out_of_stock')
                            <span class="badge badge-danger">
                                <i class="fas fa-times-circle"></i> Out of Stock
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i> Low Stock
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
                                <i class="fas fa-check"></i> Acknowledged
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if(!$alert->acknowledged_at)
                            <form action="{{ route('inventory.acknowledge-alert', $alert) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-action-edit" title="Acknowledge">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('inventory.adjust', $alert->product) }}" class="btn-action btn-action-view" title="Adjust Stock">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <p>No active alerts. All inventory levels are good!</p>
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
