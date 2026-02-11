@extends('layouts.app')

@section('title', 'Coupons')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-ticket-alt"></i> Coupons & Discounts</h1>
            <p class="page-subtitle">Manage promotional codes and discount campaigns</p>
        </div>
        <a href="{{ route('coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Coupon
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-ticket-alt" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Total Coupons</p>
                <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">{{ number_format($stats['total_coupons']) }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #d1fae5 100%); border-radius: 16px; padding: 24px; border: 1px solid #a7f3d0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #166534; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Active Coupons</p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;">{{ number_format($stats['active_coupons']) }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #ddd6fe 100%); border-radius: 16px; padding: 24px; border: 1px solid #c4b5fd;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #5b21b6; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Total Usage</p>
                <p style="font-size: 28px; font-weight: 700; color: #6d28d9; margin: 0;">{{ number_format($stats['total_usage']) }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%); border-radius: 16px; padding: 24px; border: 1px solid #fde68a;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-dollar-sign" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #92400e; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Total Savings</p>
                <p style="font-size: 28px; font-weight: 700; color: #b45309; margin: 0;">{{ currency($stats['total_savings']) }}</p>
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
        <form method="GET" action="{{ route('coupons.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" style="flex: 1; min-width: 200px;" 
                   placeholder="Search coupons..." value="{{ request('search') }}">
            
            <select name="status" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            
            <select name="type" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">All Types</option>
                <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                <option value="free_shipping" {{ request('type') == 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                <option value="buy_x_get_y" {{ request('type') == 'buy_x_get_y' ? 'selected' : '' }}>Buy X Get Y</option>
            </select>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('coupons.index') }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
        </form>
    </div>
</div>

<!-- Coupons Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Coupons ({{ $coupons->total() }})</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Usage</th>
                    <th>Valid Period</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                <tr>
                    <td>
                        <code style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; letter-spacing: 0.5px;">{{ $coupon->code }}</code>
                    </td>
                    <td>
                        <strong>{{ $coupon->name }}</strong>
                        @if($coupon->description)
                            <br><small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        @if($coupon->type == 'percentage')
                            <span class="badge badge-primary"><i class="fas fa-percent"></i> Percentage</span>
                        @elseif($coupon->type == 'fixed')
                            <span class="badge badge-success"><i class="fas fa-dollar-sign"></i> Fixed</span>
                        @elseif($coupon->type == 'free_shipping')
                            <span class="badge badge-info"><i class="fas fa-shipping-fast"></i> Free Shipping</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-gift"></i> Buy X Get Y</span>
                        @endif
                    </td>
                    <td>
                        @if($coupon->type == 'percentage')
                            <strong style="color: #3b82f6;">{{ $coupon->value }}%</strong>
                        @elseif($coupon->type == 'fixed')
                            <strong style="color: #10b981;">{{ currency($coupon->value) }}</strong>
                        @elseif($coupon->type == 'free_shipping')
                            <strong style="color: #06b6d4;">Free</strong>
                        @else
                            <strong style="color: #f59e0b;">{{ $coupon->buy_quantity }} + {{ $coupon->get_quantity }}</strong>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <strong>{{ $coupon->usage_count }}</strong>
                            @if($coupon->usage_limit)
                                <span class="text-muted">/ {{ $coupon->usage_limit }}</span>
                            @else
                                <span class="text-muted">/ ∞</span>
                            @endif
                        </div>
                        @if($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit)
                            <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Limit reached</small>
                        @endif
                    </td>
                    <td>
                        @if($coupon->valid_from)
                            <small>From: {{ $coupon->valid_from->format('M d, Y') }}</small><br>
                        @endif
                        @if($coupon->valid_to)
                            <small>To: {{ $coupon->valid_to->format('M d, Y') }}</small>
                        @else
                            <small class="text-muted">No expiry</small>
                        @endif
                    </td>
                    <td>
                        @if($coupon->status == 'active')
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                        @elseif($coupon->status == 'expired')
                            <span class="badge badge-danger"><i class="fas fa-clock"></i> Expired</span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('coupons.show', $coupon) }}" class="btn-action btn-action-view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('coupons.edit', $coupon) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($coupon->usage_count == 0)
                            <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="fas fa-ticket-alt"></i>
                        <p>No coupons found</p>
                        <a href="{{ route('coupons.create') }}" class="btn btn-primary">Create Your First Coupon</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($coupons->hasPages())
    <div class="card-footer">
        {{ $coupons->links() }}
    </div>
    @endif
</div>
@endsection
