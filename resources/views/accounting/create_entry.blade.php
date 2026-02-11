@extends('layouts.app')

@section('title', 'New Journal Entry')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">New Journal Entry</h1>
        <a href="{{ route('accounting.entries') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Entries
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('accounting.entries.store') }}" method="POST" id="entryForm">
        @csrf
        
        <!-- Header Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Entry Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Journal Type</label>
                            <select name="journal_type" class="form-select" required>
                                <option value="GENERAL">General (OD)</option>
                                <option value="SALES">Sales</option>
                                <option value="PURCHASE">Purchases</option>
                                <option value="BANK">Bank</option>
                                <option value="CASH">Cash</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Reference</label>
                            <input type="text" name="reference" class="form-control" placeholder="e.g. INV-001">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Entry description" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lines -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Transaction Lines</h6>
                <button type="button" class="btn btn-sm btn-success" onclick="addLine()">
                    <i class="fas fa-plus me-1"></i> Add Line
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="linesTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 35%">Account</th>
                                <th style="width: 25%">Line Description</th>
                                <th style="width: 15%">Debit</th>
                                <th style="width: 15%">Credit</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="linesBody">
                            <!-- Initial Lines -->
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="2" class="text-end">Total:</td>
                                <td id="totalDebit">0.00</td>
                                <td id="totalCredit">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-end">Balance:</td>
                                <td colspan="2" id="balanceDiff" class="text-center text-success">Balanced</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary" id="submitBtn">Save Entry</button>
            </div>
        </div>
    </form>
</div>

<script>
    const accounts = @json($accounts);
    let lineCount = 0;

    function addLine(data = {}) {
        const index = lineCount++;
        const tr = document.createElement('tr');
        
        let accountOptions = '<option value="">Select Account...</option>';
        accounts.forEach(acc => {
            accountOptions += `<option value="${acc.id}">${acc.code} - ${acc.name}</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="lines[${index}][account_id]" class="form-select select2-account" required>
                    ${accountOptions}
                </select>
            </td>
            <td>
                <input type="text" name="lines[${index}][description]" class="form-control" placeholder="Optional">
            </td>
            <td>
                <input type="number" step="0.01" name="lines[${index}][debit]" class="form-control debit-input" value="0.00" oninput="calculateTotals()">
            </td>
            <td>
                <input type="number" step="0.01" name="lines[${index}][credit]" class="form-control credit-input" value="0.00" oninput="calculateTotals()">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeLine(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        document.getElementById('linesBody').appendChild(tr);
    }

    function removeLine(btn) {
        btn.closest('tr').remove();
        calculateTotals();
    }

    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        document.querySelectorAll('.debit-input').forEach(input => totalDebit += parseFloat(input.value || 0));
        document.querySelectorAll('.credit-input').forEach(input => totalCredit += parseFloat(input.value || 0));

        document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
        document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);

        const diff = Math.abs(totalDebit - totalCredit);
        const diffLabel = document.getElementById('balanceDiff');
        const submitBtn = document.getElementById('submitBtn');

        if (diff < 0.01) {
            diffLabel.textContent = 'Balanced';
            diffLabel.className = 'text-center text-success fw-bold';
            submitBtn.disabled = false;
        } else {
            diffLabel.textContent = `Out of balance: ${diff.toFixed(2)}`;
            diffLabel.className = 'text-center text-danger fw-bold';
            submitBtn.disabled = true; // Prevent submission if not balanced
        }
    }

    // Add 2 initial lines
    document.addEventListener('DOMContentLoaded', () => {
        addLine();
        addLine();
    });
</script>
@endsection
