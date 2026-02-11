@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Activity Log Details</h1>
        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Log Information</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Date:</div>
                <div class="col-md-9">{{ $activityLog->created_at->format('Y-m-d H:i:s') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">User:</div>
                <div class="col-md-9">{{ $activityLog->user ? $activityLog->user->name : 'System' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Event:</div>
                <div class="col-md-9"><span class="badge bg-{{ $activityLog->event_color }}">{{ ucfirst($activityLog->event) }}</span></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Description:</div>
                <div class="col-md-9">{{ $activityLog->description }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Subject:</div>
                <div class="col-md-9">
                    @if($activityLog->subject)
                        {{ class_basename($activityLog->subject_type) }} #{{ $activityLog->subject_id }}
                    @else
                        N/A
                    @endif
                </div>
            </div>
            
            <hr>
            
            <h5 class="mb-3">Changes</h5>
            @if(count($activityLog->changes) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Attribute</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLog->changes as $attribute => $values)
                                <tr>
                                    <td>{{ ucfirst($attribute) }}</td>
                                    <td class="text-danger">{{ is_array($values['old']) ? json_encode($values['old']) : $values['old'] }}</td>
                                    <td class="text-success">{{ is_array($values['new']) ? json_encode($values['new']) : $values['new'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No specific changes recorded or available.</p>
            @endif
        </div>
    </div>
</div>
@endsection
