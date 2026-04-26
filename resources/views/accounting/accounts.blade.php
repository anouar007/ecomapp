@extends('layouts.app')

@section('title', __('Chart of Accounts'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Chart of Accounts') }}</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
            <i class="fas fa-plus me-2"></i> {{ __('New Account') }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="accountsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Class') }}</th>
                            <th>{{ __('Status') }}</th>
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
                                    {{ __($account->type) }}
                                </span>
                            </td>
                            <td>{{ $account->class }}</td>
                            <td>
                                @if($account->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
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
                    <h5 class="modal-title">{{ __('New Account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Code') }}</label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. 5141">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Banque Populaire">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Type') }}</label>
                        <select name="type" class="form-select" required>
                            <option value="Asset">{{ __('Asset') }}</option>
                            <option value="Liability">{{ __('Liability') }}</option>
                            <option value="Equity">{{ __('Equity') }}</option>
                            <option value="Revenue">{{ __('Revenue') }}</option>
                            <option value="Expense">{{ __('Expense') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Class (PCGM)') }}</label>
                        <select name="class" class="form-select" required>
                            <option value="1">1 - {{ __('Financement Permanent') }}</option>
                            <option value="2">2 - {{ __('Actif Immobilisé') }}</option>
                            <option value="3">3 - {{ __('Actif Circulant') }}</option>
                            <option value="4">4 - {{ __('Passif Circulant') }}</option>
                            <option value="5">5 - {{ __('Trésorerie') }}</option>
                            <option value="6">6 - {{ __('Charges') }}</option>
                            <option value="7">7 - {{ __('Produits') }}</option>
                            <option value="8">8 - {{ __('Résultat') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
