@extends('layouts.app')

@section('title', 'Customer Management')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-users"></i>
                </div>
                Customer Management
            </h1>
            <p class="brand-subtitle">Manage your customer relationships, groups, and revenue tracking</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Add Customer
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid">
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="brand-stat-label">Total Customers</div>
            <div class="brand-stat-value">{{ number_format($stats['total_customers']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-info-circle"></i> Registered in system
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="brand-stat-label">Active Customers</div>
            <div class="brand-stat-value">{{ number_format($stats['active_customers']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-toggle-on"></i> Currently active accounts
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="brand-stat-label">Total Revenue</div>
            <div class="brand-stat-value">{{ currency($stats['total_revenue']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-chart-line"></i> Total sales from customers
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="brand-stat-label">Avg Order Value</div>
            <div class="brand-stat-value">{{ currency($stats['avg_order_value'] ?? 0) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-calculator"></i> Average spend per order
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="brand-filter-bar">
        <form method="GET" action="{{ route('customers.index') }}" class="d-flex align-items-end gap-3 flex-wrap">
            <div class="brand-search-wrapper flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Search name, email, phone, code...">
            </div>
            
            <div style="min-width: 160px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
            </div>

            <div style="min-width: 160px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Customer Group</label>
                <select name="customer_group_id" class="form-select">
                    <option value="">All Groups</option>
                    @foreach($customerGroups as $group)
                        <option value="{{ $group->id }}" {{ request('customer_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('customers.index') }}" class="btn-brand-light" title="Reset">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Customer</th>
                        <th>Contact info</th>
                        <th>Group</th>
                        <th class="text-center">Orders</th>
                        <th class="text-end">Total Spent</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $customer->name }}</div>
                                <div class="text-muted small font-monospace mt-1">{{ $customer->customer_code }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark small">{{ $customer->email }}</div>
                            @if($customer->phone)
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $customer->phone }}</div>
                            @endif
                        </td>
                        <td>
                            @if($customer->customerGroup)
                                <span class="brand-badge info" style="background: {{ $customer->customerGroup->color }}15; color: {{ $customer->customerGroup->color }};">
                                    {{ $customer->customerGroup->name }}
                                </span>
                            @else
                                <span class="text-muted small">No Group</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-dark">{{ $customer->total_orders }}</span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-dark fs-6">{{ $customer->formatted_total_spent }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $statusClasses = [
                                    'active' => 'success',
                                    'inactive' => 'warning',
                                    'blocked' => 'danger',
                                ];
                                $badgeType = $statusClasses[$customer->status] ?? 'primary';
                            @endphp
                            <span class="brand-badge {{ $badgeType }}">
                                {{ $customer->status_label }}
                            </span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('customers.show', $customer) }}" class="btn-action-icon" title="View Customer">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn-action-icon" title="Edit Customer">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon text-danger" title="Delete Customer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-users-slash"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No customers found</h5>
                                <p class="text-muted">No customer records matching your current filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
@endsection
