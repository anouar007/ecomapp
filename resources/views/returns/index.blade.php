@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ __('Returns Management') }}</h1>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('Return #') }}</th>
                            <th>{{ __('Order #') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returns as $return)
                        <tr>
                            <td>{{ $return->return_number }}</td>
                            <td>{{ $return->order->order_number }}</td>
                            <td>{{ $return->customer->name }}</td>
                            <td>{{ $return->reason }}</td>
                            <td>{{ __($return->status) }}</td>
                            <td>{{ __('Actions') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
