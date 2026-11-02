@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Returns Management</h1>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Return #</th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returns as $return)
                        <tr>
                            <td class="td-product-details" data-label="Retour #">
                                <strong class="product-title-text">{{ $return->return_number }}</strong>
                            </td>
                            <td data-label="Commande #">{{ $return->order->order_number ?? 'N/A' }}</td>
                            <td data-label="Client">{{ $return->customer->name ?? 'N/A' }}</td>
                            <td data-label="Motif">{{ $return->reason }}</td>
                            <td data-label="Statut">
                                <span class="badge bg-secondary">{{ ucfirst($return->status) }}</span>
                            </td>
                            <td class="td-actions">
                                <span class="text-muted">—</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
