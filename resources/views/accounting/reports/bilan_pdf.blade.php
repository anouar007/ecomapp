@extends('layouts.pdf')

@section('title', 'Bilan')
@section('subtitle', 'Situation au : ' . $date)

@section('content')
    <table style="width: 100%; border: none; margin-bottom: 0;">
        <tr>
            <td style="width: 48%; vertical-align: top; padding-right: 1%; border: none;">
                <h3 class="text-center bg-light" style="padding: 5px; border: 1px solid #e3e6f0; margin-top: 0;">ACTIF</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Eléments</th>
                            <th class="text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actif as $key => $section)
                            @if(count($section['accounts']) > 0 || $section['total'] != 0)
                            <tr class="section-header">
                                <td colspan="2">{{ $section['name'] }}</td>
                            </tr>
                            @foreach($section['accounts'] as $item)
                            <tr>
                                <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td class="text-end">Total {{ $section['name'] }}</td>
                                <td class="text-end">{{ number_format($section['total'], 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                         <tr class="bg-primary fw-bold" style="color: white;">
                             <td>TOTAL ACTIF</td>
                             <td class="text-end">{{ number_format($totalActif, 2) }}</td>
                         </tr>
                    </tfoot>
                </table>
            </td>
            <td style="width: 48%; vertical-align: top; padding-left: 1%; border: none;">
                <h3 class="text-center bg-light" style="padding: 5px; border: 1px solid #e3e6f0; margin-top: 0;">PASSIF</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Eléments</th>
                            <th class="text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($passif as $key => $section)
                            @if(count($section['accounts']) > 0 || $section['total'] != 0)
                            <tr class="section-header">
                                <td colspan="2">{{ $section['name'] }}</td>
                            </tr>
                            @foreach($section['accounts'] as $item)
                            <tr>
                                <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td class="text-end">Total {{ $section['name'] }}</td>
                                <td class="text-end">{{ number_format($section['total'], 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                         <tr class="bg-primary fw-bold" style="color: white;">
                             <td>TOTAL PASSIF</td>
                             <td class="text-end">{{ number_format($totalPassif, 2) }}</td>
                         </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
@endsection
