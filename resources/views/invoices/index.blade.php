@extends('layouts.app')

@section('title', 'Invoices Management')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                Invoices Management
            </h1>
            <p class="brand-subtitle">Track billings, manage customer payments, and monitor revenue</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Create Invoice
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid">
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="brand-stat-label">Total Invoices</div>
            <div class="brand-stat-value">{{ $stats['total_invoices'] }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-history"></i> Lifetime generated
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="brand-stat-label">Paid Amount</div>
            <div class="brand-stat-value">{{ currency($stats['paid_amount']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-arrow-up text-success"></i> Successfully collected
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-hourglass-start"></i>
            </div>
            <div class="brand-stat-label">Unpaid Amount</div>
            <div class="brand-stat-value">{{ currency($stats['unpaid_amount']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-exclamation-circle text-warning"></i> Pending collections
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="brand-stat-label">Total Revenue</div>
            <div class="brand-stat-value">{{ currency($stats['total_revenue']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-chart-line"></i> Combined gross value
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="brand-filter-bar">
        <form method="GET" action="{{ route('invoices.index') }}" class="d-flex align-items-end gap-3 flex-wrap">
            <div class="brand-search-wrapper flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Invoice #, customer name...">
            </div>
            
            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="invoice" {{ request('type') === 'invoice' ? 'selected' : '' }}>Invoice</option>
                    <option value="quote" {{ request('type') === 'quote' ? 'selected' : '' }}>Quote</option>
                </select>
            </div>
            
            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Stamp</label>
                <select name="with_stamp" class="form-select">
                    <option value="">All Stamps</option>
                    <option value="1" {{ request('with_stamp') === '1' ? 'selected' : '' }}>With Stamp</option>
                    <option value="0" {{ request('with_stamp') === '0' ? 'selected' : '' }}>Without Stamp</option>
                </select>
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">From Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">To Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('invoices.index') }}" class="btn-brand-light" title="Reset">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Invoice #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Stamp</th>
                        <th>Method</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <a href="{{ route('invoices.show', $invoice) }}" class="fw-bold text-primary text-decoration-none d-flex align-items-center gap-2">
                                {{ $invoice->invoice_number }}
                                @if($invoice->isQuote())
                                    <span style="background: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase;">{{ __('Quote') }}</span>
                                @endif
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $invoice->customer_name }}</div>
                            @if($invoice->customer_email)
                                <div class="text-muted small">{{ $invoice->customer_email }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">{{ $invoice->issued_at->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $invoice->issued_at->format('h:i A') }}</div>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6">
                            {{ $invoice->formatted_total_amount }}
                        </td>
                        <td class="text-center">
                            @php
                                $statusClasses = [
                                    'paid' => 'success',
                                    'unpaid' => 'warning',
                                    'partial' => 'info',
                                    'cancelled' => 'danger',
                                ];
                                $badgeType = $statusClasses[$invoice->payment_status] ?? 'primary';
                            @endphp
                            <span class="brand-badge {{ $badgeType }}">
                                {{ $invoice->status_label }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($invoice->with_stamp)
                                <span style="background: #e0e7ff; color: #4338ca; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-stamp" style="font-size: 9px;"></i> Yes
                                </span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                    No
                                </span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            <span class="text-uppercase" style="letter-spacing: 0.02em;">{{ str_replace('_', ' ', $invoice->payment_method) }}</span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn-action-icon" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- PDF Dropdown -->
                                <div class="dropdown d-inline-block">
                                    <button class="btn-action-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Download PDF Options">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px; border-radius: 10px;">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.download', [$invoice, 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp" style="color: #6366f1; width: 14px;"></i> {{ __('Download With Stamp') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.download', [$invoice, 'with_stamp' => 0]) }}">
                                                <i class="far fa-file-pdf text-muted" style="width: 14px;"></i> {{ __('Download Without Stamp') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Print Dropdown -->
                                <div class="dropdown d-inline-block">
                                    <button class="btn-action-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Print Options">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px; border-radius: 10px;">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" target="_blank" href="{{ route('invoices.print', [$invoice, 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp" style="color: #6366f1; width: 14px;"></i> {{ __('Print With Stamp') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" target="_blank" href="{{ route('invoices.print', [$invoice, 'with_stamp' => 0]) }}">
                                                <i class="fas fa-print text-muted" style="width: 14px;"></i> {{ __('Print Without Stamp') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No invoices found</h5>
                                <p class="text-muted">You haven't generated any invoices matching your search.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
@endsection
