@extends('layouts.app')

@section('title', __('Financial Reports'))

@section('content')
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                {{ __('Financial Reports') }}
            </h1>
            <p class="brand-subtitle">{{ __('Generate and download standard financial statements and detailed ledgers.') }}</p>
        </div>
    </div>

    <div class="row">
        <!-- Balance Sheet -->
        <div class="col-md-6 mb-4">
            <div class="brand-table-card h-100 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="brand-stat-icon primary me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark m-0">{{ __('Balance Sheet (Bilan)') }}</h5>
                        <div class="text-muted small">{{ __('Assets, Liabilities & Equity') }}</div>
                    </div>
                </div>
                
                <form action="{{ route('accounting.reports.bilan') }}" method="GET" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">{{ __('Situation Date') }}</label>
                        <input type="date" name="date" class="form-control brand-input" value="{{ date('Y-12-31') }}">
                    </div>
                    <button type="submit" class="btn-brand-primary w-100">
                        <i class="fas fa-eye me-2"></i> {{ __('View Report') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Income Statement -->
        <div class="col-md-6 mb-4">
            <div class="brand-table-card h-100 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="brand-stat-icon success me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark m-0">{{ __('Income Statement (CPC)') }}</h5>
                        <div class="text-muted small">{{ __('Revenue, Expenses & Result') }}</div>
                    </div>
                </div>

                <form action="{{ route('accounting.reports.cpc') }}" method="GET" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">{{ __('Period') }}</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control brand-input" value="{{ date('Y-01-01') }}">
                            <span class="input-group-text bg-light border-0">{{ __('to') }}</span>
                            <input type="date" name="end_date" class="form-control brand-input" value="{{ date('Y-12-31') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn-brand-primary w-100">
                        <i class="fas fa-eye me-2"></i> {{ __('View Report') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- General Ledger -->
        <div class="col-md-6 mb-4">
            <div class="brand-table-card h-100 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="brand-stat-icon info me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark m-0">{{ __('General Ledger (Grand Livre)') }}</h5>
                        <div class="text-muted small">{{ __('Detailed Transaction History') }}</div>
                    </div>
                </div>

                <form action="{{ route('accounting.reports.gl') }}" method="GET" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">{{ __('Account (Optional)') }}</label>
                        <select name="account_id" class="form-select brand-select">
                            <option value="">{{ __('All Accounts') }}</option>
                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-muted">{{ __('Date Range') }}</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control brand-input" value="{{ date('Y-01-01') }}">
                            <span class="input-group-text bg-light border-0">{{ __('to') }}</span>
                            <input type="date" name="end_date" class="form-control brand-input" value="{{ date('Y-12-31') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn-brand-primary w-100">
                        <i class="fas fa-eye me-2"></i> {{ __('View Report') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
