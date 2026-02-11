@extends('layouts.app')

@section('title', 'Bilan')

@section('content')
<div class="brand-header ignore-print">
    <div>
        <h1 class="brand-title">
            <div class="brand-header-icon"><i class="fas fa-balance-scale"></i></div>
            Bilan (Balance Sheet)
        </h1>
        <p class="brand-subtitle">Situation au : <span class="fw-bold text-dark">{{ $date }}</span></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('accounting.reports.bilan.pdf', ['date' => $date]) }}" class="btn-brand-primary">
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

<div class="brand-table-card p-4" id="printable-area">
    <div class="row">
        <!-- ACTIF -->
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3 border">
                <div class="brand-stat-icon primary me-3" style="width: 32px; height: 32px; font-size: 1rem;"><i class="fas fa-plus"></i></div>
                <h5 class="fw-bold m-0 text-dark">ACTIF</h5>
            </div>
            
            <div class="table-responsive">
                <table class="brand-table">
                    <thead>
                        <tr>
                            <th style="padding-left: 1.5rem;">Eléments</th>
                            <th class="text-end" style="width: 150px; padding-right: 1.5rem;">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actif as $key => $section)
                            @if(count($section['accounts']) > 0 || $section['total'] != 0)
                            <tr class="bg-light">
                                <td colspan="2" class="fw-bold py-2 px-4">{{ $section['name'] }}</td>
                            </tr>
                            @foreach($section['accounts'] as $item)
                            <tr>
                                <td class="ps-4 text-muted">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                <td class="text-end fw-bold text-dark pe-4">{{ number_format($item['balance'], 2) }}</td>
                            </tr>
                            @endforeach
                            <tr style="border-top: 2px solid #e2e8f0;">
                                <td class="text-end fw-bold pe-4 text-primary">Total {{ $section['name'] }}</td>
                                <td class="text-end fw-bold pe-4 text-primary">{{ number_format($section['total'], 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                         <tr class="bg-primary text-white">
                             <td class="fw-bold py-3 ps-4">TOTAL ACTIF</td>
                             <td class="text-end fw-bold py-3 pe-4">{{ number_format($totalActif, 2) }}</td>
                         </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- PASSIF -->
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3 border">
                <div class="brand-stat-icon success me-3" style="width: 32px; height: 32px; font-size: 1rem;"><i class="fas fa-minus"></i></div>
                <h5 class="fw-bold m-0 text-dark">PASSIF</h5>
            </div>
            
            <div class="table-responsive">
                <table class="brand-table">
                    <thead>
                        <tr>
                            <th style="padding-left: 1.5rem;">Eléments</th>
                            <th class="text-end" style="width: 150px; padding-right: 1.5rem;">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($passif as $key => $section)
                            @if(count($section['accounts']) > 0 || $section['total'] != 0)
                            <tr class="bg-light">
                                <td colspan="2" class="fw-bold py-2 px-4">{{ $section['name'] }}</td>
                            </tr>
                            @foreach($section['accounts'] as $item)
                            <tr>
                                <td class="ps-4 text-muted">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                <td class="text-end fw-bold text-dark pe-4">{{ number_format($item['balance'], 2) }}</td>
                            </tr>
                            @endforeach
                            <tr style="border-top: 2px solid #e2e8f0;">
                                <td class="text-end fw-bold pe-4 text-success">Total {{ $section['name'] }}</td>
                                <td class="text-end fw-bold pe-4 text-success">{{ number_format($section['total'], 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                         <tr class="bg-success text-white">
                             <td class="fw-bold py-3 ps-4">TOTAL PASSIF</td>
                             <td class="text-end fw-bold py-3 pe-4">{{ number_format($totalPassif, 2) }}</td>
                         </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: landscape; margin: 10mm; }
        .ignore-print { display: none !important; }
        .brand-table-card { box-shadow: none !important; border: none !important; padding: 0 !important; }
        .col-md-6 { width: 48% !important; float: left; margin-right: 2%; }
        .bg-primary { background-color: #4e73df !important; color: white !important; -webkit-print-color-adjust: exact; }
        .bg-success { background-color: #1cc88a !important; color: white !important; -webkit-print-color-adjust: exact; }
        .bg-light { background-color: #f8f9fc !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
