@extends('layouts.pdf')

@section('title', 'CPC (Income Statement)')
@section('subtitle', 'Période du : ' . $startDate . ' au ' . $endDate)

@section('content')
    <table class="table">
        <thead>
            <tr>
                <th>Nature des produits et charges</th>
                <th class="text-end" style="width: 20%">Montant</th>
            </tr>
        </thead>
        <tbody>
            <!-- EXPLOITATION -->
            <tr class="section-header"><td colspan="2">I. PRODUITS D'EXPLOITATION</td></tr>
            @foreach($rows['exploitation']['produits'] as $item)
            <tr>
                <td style="padding-left: 20px;">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold bg-light"><td class="text-end">Total Produits d'Exploitation</td><td class="text-end">{{ number_format($exploitation['total_produits'], 2) }}</td></tr>

            <tr class="section-header"><td colspan="2">II. CHARGES D'EXPLOITATION</td></tr>
            @foreach($rows['exploitation']['charges'] as $item)
            <tr>
                <td style="padding-left: 20px;">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold bg-light"><td class="text-end">Total Charges d'Exploitation</td><td class="text-end">{{ number_format($exploitation['total_charges'], 2) }}</td></tr>

            <tr class="fw-bold border-top border-dark" style="background-color: #f1f3f9;">
                <td>III. RÉSULTAT D'EXPLOITATION (I - II)</td>
                <td class="text-end">{{ number_format($resultatExploitation, 2) }}</td>
            </tr>

            <!-- FINANCIER -->
            <tr class="section-header"><td colspan="2">IV. PRODUITS FINANCIERS</td></tr>
            @foreach($rows['financier']['produits'] as $item)
            <tr>
                <td style="padding-left: 20px;">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold bg-light"><td class="text-end">Total Produits Financiers</td><td class="text-end">{{ number_format($financier['total_produits'], 2) }}</td></tr>

            <tr class="section-header"><td colspan="2">V. CHARGES FINANCIÈRES</td></tr>
            @foreach($rows['financier']['charges'] as $item)
            <tr>
                <td style="padding-left: 20px;">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold bg-light"><td class="text-end">Total Charges Financières</td><td class="text-end">{{ number_format($financier['total_charges'], 2) }}</td></tr>

            <tr class="fw-bold border-top border-dark" style="background-color: #f1f3f9;">
                <td>VI. RÉSULTAT FINANCIER (IV - V)</td>
                <td class="text-end">{{ number_format($resultatFinancier, 2) }}</td>
            </tr>

            <tr class="fw-bold border-top border-dark" style="background-color: #eaecf4; font-size: 1.1em;">
                <td>VII. RÉSULTAT COURANT (III + VI)</td>
                <td class="text-end">{{ number_format($resultatCourant, 2) }}</td>
            </tr>

            <!-- NON COURANT -->
            <tr class="section-header"><td colspan="2">VIII. PRODUITS NON COURANTS</td></tr>
            @foreach($rows['non_courant']['produits'] as $item)
            <tr>
                <td style="padding-left: 20px;">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold bg-light"><td class="text-end">Total Produits Non Courants</td><td class="text-end">{{ number_format($nonCourant['total_produits'], 2) }}</td></tr>

            <tr class="section-header"><td colspan="2">IX. CHARGES NON COURANTES</td></tr>
            @foreach($rows['non_courant']['charges'] as $item)
            <tr>
                <td style="padding-left: 20px;">{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold bg-light"><td class="text-end">Total Charges Non Courantes</td><td class="text-end">{{ number_format($nonCourant['total_charges'], 2) }}</td></tr>

            <tr class="fw-bold border-top border-dark" style="background-color: #f1f3f9;">
                <td>X. RÉSULTAT NON COURANT (VIII - IX)</td>
                <td class="text-end">{{ number_format($resultatNonCourant, 2) }}</td>
            </tr>

            <!-- TOTAL -->
            <tr class="bg-primary fw-bold text-white border-top border-dark" style="font-size: 1.2em;">
                <td>XI. RÉSULTAT NET (VII + X)</td>
                <td class="text-end">{{ number_format($resultatNet, 2) }}</td>
            </tr>

        </tbody>
    </table>
@endsection
