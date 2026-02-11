@extends('layouts.app')

@section('title', 'CPC (Income Statement)')

@section('content')
<div class="brand-header ignore-print">
    <div>
        <h1 class="brand-title">
            <div class="brand-header-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            Compte de Produits et Charges (CPC)
        </h1>
        <p class="brand-subtitle">Période du : <span class="fw-bold text-dark">{{ $startDate }}</span> au <span class="fw-bold text-dark">{{ $endDate }}</span></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('accounting.reports.cpc.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn-brand-primary">
            <i class="fas fa-file-pdf me-2"></i> Download PDF
        </a>
        <button onclick="window.print()" class="btn-brand-light">
            <i class="fas fa-print me-2"></i> Print
        </button>
        <a href="{{ route('accounting.reports') }}" class="btn-brand-outline">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>
</div>

<div class="brand-table-card p-4">
    <div class="table-responsive">
        <table class="brand-table">
            <thead>
                <tr>
                    <th style="padding-left: 1.5rem;">Nature des produits et charges</th>
                    <th class="text-end" style="padding-right: 1.5rem;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <!-- EXPLOITATION -->
                <tr class="bg-light">
                    <td colspan="2" class="fw-bold py-3 text-primary ps-4">I. PRODUITS D'EXPLOITATION</td>
                </tr>
                @foreach($rows['exploitation']['produits'] as $item)
                <tr>
                    <td class="ps-4 text-muted">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                    <td class="text-end fw-bold text-dark pe-4">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="border-top: 1px dashed #cbd5e1;">
                    <td class="text-end text-muted pe-4 small uppercase date-cell">Total Produits d'Exploitation</td>
                    <td class="text-end text-muted pe-4">{{ number_format($exploitation['total_produits'], 2) }}</td>
                </tr>

                <tr class="bg-light">
                    <td colspan="2" class="fw-bold py-3 text-primary ps-4">II. CHARGES D'EXPLOITATION</td>
                </tr>
                @foreach($rows['exploitation']['charges'] as $item)
                <tr>
                    <td class="ps-4 text-muted">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                    <td class="text-end fw-bold text-dark pe-4">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="border-top: 1px dashed #cbd5e1;">
                    <td class="text-end text-muted pe-4 small uppercase date-cell">Total Charges d'Exploitation</td>
                    <td class="text-end text-muted pe-4">{{ number_format($exploitation['total_charges'], 2) }}</td>
                </tr>

                <tr class="table-active" style="background-color: #f0fdf4;">
                    <td class="fw-bold text-success ps-4 py-3">III. RÉSULTAT D'EXPLOITATION (I - II)</td>
                    <td class="text-end fw-bold text-success pe-4 py-3">{{ number_format($resultatExploitation, 2) }}</td>
                </tr>

                <!-- FINANCIER -->
                <tr class="bg-light">
                    <td colspan="2" class="fw-bold py-3 text-info ps-4">IV. PRODUITS FINANCIERS</td>
                </tr>
                @foreach($rows['financier']['produits'] as $item)
                <tr>
                    <td class="ps-4 text-muted">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                    <td class="text-end fw-bold text-dark pe-4">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="border-top: 1px dashed #cbd5e1;">
                    <td class="text-end text-muted pe-4 small uppercase date-cell">Total Produits Financiers</td>
                    <td class="text-end text-muted pe-4">{{ number_format($financier['total_produits'], 2) }}</td>
                </tr>

                <tr class="bg-light">
                    <td colspan="2" class="fw-bold py-3 text-info ps-4">V. CHARGES FINANCIÈRES</td>
                </tr>
                @foreach($rows['financier']['charges'] as $item)
                <tr>
                    <td class="ps-4 text-muted">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                    <td class="text-end fw-bold text-dark pe-4">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="border-top: 1px dashed #cbd5e1;">
                    <td class="text-end text-muted pe-4 small uppercase date-cell">Total Charges Financières</td>
                    <td class="text-end text-muted pe-4">{{ number_format($financier['total_charges'], 2) }}</td>
                </tr>

                <tr class="table-active" style="background-color: #f0fdf4;">
                    <td class="fw-bold text-success ps-4 py-3">VI. RÉSULTAT FINANCIER (IV - V)</td>
                    <td class="text-end fw-bold text-success pe-4 py-3">{{ number_format($resultatFinancier, 2) }}</td>
                </tr>

                <tr style="background-color: #fef3c7;">
                    <td class="fw-bold text-warning ps-4 py-3">VII. RÉSULTAT COURANT (III + VI)</td>
                    <td class="text-end fw-bold text-warning pe-4 py-3">{{ number_format($resultatCourant, 2) }}</td>
                </tr>

                <!-- NON COURANT -->
                <tr class="bg-light">
                    <td colspan="2" class="fw-bold py-3 text-secondary ps-4">VIII. PRODUITS NON COURANTS</td>
                </tr>
                @foreach($rows['non_courant']['produits'] as $item)
                <tr>
                    <td class="ps-4 text-muted">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                    <td class="text-end fw-bold text-dark pe-4">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="border-top: 1px dashed #cbd5e1;">
                    <td class="text-end text-muted pe-4 small uppercase date-cell">Total Produits Non Courants</td>
                    <td class="text-end text-muted pe-4">{{ number_format($nonCourant['total_produits'], 2) }}</td>
                </tr>

                <tr class="bg-light">
                    <td colspan="2" class="fw-bold py-3 text-secondary ps-4">IX. CHARGES NON COURANTES</td>
                </tr>
                @foreach($rows['non_courant']['charges'] as $item)
                <tr>
                    <td class="ps-4 text-muted">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                    <td class="text-end fw-bold text-dark pe-4">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="border-top: 1px dashed #cbd5e1;">
                    <td class="text-end text-muted pe-4 small uppercase date-cell">Total Charges Non Courantes</td>
                    <td class="text-end text-muted pe-4">{{ number_format($nonCourant['total_charges'], 2) }}</td>
                </tr>

                <tr class="table-active" style="background-color: #f0fdf4;">
                    <td class="fw-bold text-success ps-4 py-3">X. RÉSULTAT NON COURANT (VIII - IX)</td>
                    <td class="text-end fw-bold text-success pe-4 py-3">{{ number_format($resultatNonCourant, 2) }}</td>
                </tr>

                <!-- TOTAL -->
                <tr class="bg-primary text-white">
                    <td class="fw-bold ps-4 py-3">XI. RÉSULTAT NET (VII + X)</td>
                    <td class="text-end fw-bold pe-4 py-3">{{ number_format($resultatNet, 2) }}</td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

<style>
    @media print {
        @page { size: landscape; margin: 10mm; }
        .ignore-print { display: none !important; }
        .brand-table-card { box-shadow: none !important; border: none !important; padding: 0 !important; }
        .bg-light { background-color: #f8f9fc !important; -webkit-print-color-adjust: exact; }
        .bg-primary { background-color: #4e73df !important; color: white !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
