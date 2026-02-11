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
                            <td>{{ $return->return_number }}</td>
                            <td>{{ $return->order->order_number }}</td>
                            <td>{{ $return->customer->name }}</td>
                            <td>{{ $return->reason }}</td>
                            <td>{{ ucfirst($return->status) }}</td>
                            <td>Actions</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
