@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('content')
<div class="dashboard-container">
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-sitemap"></i>
            </div>
            <div>
                <h1 class="brand-title">Plan Comptable (PCGM)</h1>
                <p class="brand-subtitle">Comptes comptables, classes et soldes actifs de l'entreprise</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <button type="button" class="btn-brand-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                <i class="fas fa-plus me-1.5"></i> Nouveau Compte
            </button>
        </div>
    </div>

    @include('partials.subnav.finance')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-xs mb-3">
            <i class="fas fa-circle-check me-1.5"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="brand-table-card">
        <div class="p-3 p-md-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="accountsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Class</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td class="font-weight-bold">{{ $account->code }}</td>
                            <td>{{ $account->name }}</td>
                            <td>
                                <span class="badge 
                                    @if($account->type == 'Asset') bg-info
                                    @elseif($account->type == 'Liability') bg-warning
                                    @elseif($account->type == 'Equity') bg-primary
                                    @elseif($account->type == 'Revenue') bg-success
                                    @elseif($account->type == 'Expense') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ $account->type }}
                                </span>
                            </td>
                            <td>{{ $account->class }}</td>
                            <td>
                                @if($account->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Create Account Modal -->
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('accounting.accounts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">New Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. 5141">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Banque Populaire">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Asset">Asset</option>
                            <option value="Liability">Liability</option>
                            <option value="Equity">Equity</option>
                            <option value="Revenue">Revenue</option>
                            <option value="Expense">Expense</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class (PCGM)</label>
                        <select name="class" class="form-select" required>
                            <option value="1">1 - Financement Permanent</option>
                            <option value="2">2 - Actif Immobilisé</option>
                            <option value="3">3 - Actif Circulant</option>
                            <option value="4">4 - Passif Circulant</option>
                            <option value="5">5 - Trésorerie</option>
                            <option value="6">6 - Charges</option>
                            <option value="7">7 - Produits</option>
                            <option value="8">8 - Résultat</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
