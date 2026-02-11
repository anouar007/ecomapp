@extends('layouts.app')

@section('title', 'Activity Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-history"></i> Activity Logs</h1>
            <p class="page-subtitle">Track user actions and system events</p>
        </div>
        <div>
            <form action="{{ route('activity-logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete old logs? This action cannot be undone.');" style="display: inline;">
                @csrf
                <input type="hidden" name="older_than_days" value="90">
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash-alt"></i> Clear Old Logs (>90 days)
                </button>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <!-- Total Activities -->
    <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-list-ul" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Total Logs</p>
                <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">{{ number_format($stats['total_activities']) }}</p>
            </div>
        </div>
    </div>

    <!-- Today's Activities -->
    <div style="background: linear-gradient(135deg, #ffffff 0%, #d1fae5 100%); border-radius: 16px; padding: 24px; border: 1px solid #a7f3d0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #166534; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Today's Activity</p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;">{{ number_format($stats['today_activities']) }}</p>
            </div>
        </div>
    </div>

    <!-- Active Users -->
    <div style="background: linear-gradient(135deg, #ffffff 0%, #ede9fe 100%); border-radius: 16px; padding: 24px; border: 1px solid #ddd6fe;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #5b21b6; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Unique Users</p>
                <p style="font-size: 28px; font-weight: 700; color: #6d28d9; margin: 0;">{{ number_format($stats['unique_users']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filter Logs</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('activity-logs.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control" placeholder="Search description..." value="{{ request('search') }}">
            </div>
            
            <div style="width: auto; min-width: 150px;">
                <select name="user_id" class="form-control">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="width: auto; min-width: 150px;">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
            </div>

            <div style="width: auto; min-width: 150px;">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Activity History</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Subject</th>
                    <th>Changes</th>
                    <th>Date & Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #475569;">
                                {{ $log->user ? substr($log->user->name, 0, 1) : '?' }}
                            </div>
                            <div>
                                <span style="font-weight: 600; color: #334155;">{{ $log->user->name ?? 'System' }}</span>
                                <br>
                                <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-light" style="font-weight: normal; font-size: 13px; color: #334155;">
                            {{ $log->description }}
                        </span>
                    </td>
                    <td>
                        @if($log->subject_type)
                            <span class="badge badge-secondary" style="font-size: 11px;">
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if(!empty($log->properties))
                            @php
                                $props = is_array($log->properties) ? $log->properties : json_decode($log->properties, true);
                            @endphp
                            @if(isset($props['attributes']))
                                <div style="font-size: 11px; font-family: monospace; color: #64748b; max-width: 300px; white-space: pre-wrap;">{{ Str::limit(json_encode($props['attributes']), 100) }}</div>
                            @elseif(count($props) > 0)
                                <div style="font-size: 11px; font-family: monospace; color: #64748b; max-width: 300px; white-space: pre-wrap;">{{ Str::limit(json_encode($props), 100) }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span style="color: #475569; font-size: 13px;">
                            {{ $log->created_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                        </span>
                    </td>
                    <td>
                       <a href="{{ route('activity-logs.show', $log) }}" class="btn btn-sm btn-icon btn-light" title="View Details">
                           <i class="fas fa-eye"></i>
                       </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fas fa-history"></i>
                        <p>No activity logs found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
