@extends('layouts.app')

@section('title', 'Costom Code Manager')

@section('content')
@extends('layouts.app')

@section('title', 'Custom Code Manager')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-code"></i>
                </div>
                Custom Code Manager
            </h1>
            <p class="brand-subtitle">Manage custom CSS, JS, and HTML snippets</p>
        </div>
        <a href="{{ route('custom-codes.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Add New Snippet
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Codes Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Snippet Details</th>
                        <th>Type</th>
                        <th>Position</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($codes as $code)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <div class="fw-bold text-dark">{{ $code->title }}</div>
                            <div class="text-muted small">Updated {{ $code->updated_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            @php
                                $typeColor = match($code->type) {
                                    'css' => 'info',
                                    'js' => 'warning',
                                    'html' => 'secondary',
                                    default => 'primary'
                                };
                            @endphp
                            <span class="brand-badge {{ $typeColor }}">
                                {{ strtoupper($code->type) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ ucwords(str_replace('_', ' ', $code->position)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary font-monospace" style="border: 1px solid #e2e8f0;">
                                {{ $code->priority }}
                            </span>
                        </td>
                        <td>
                            <span class="brand-badge {{ $code->is_active ? 'success' : 'danger' }}">
                                {{ $code->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('custom-codes.edit', $code) }}" class="btn-action-icon" title="Edit Snippet">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('custom-codes.destroy', $code) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon danger" onclick="return confirm('Are you sure you want to delete this snippet?')" title="Delete Snippet">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-code"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No custom codes found</h5>
                                <p class="text-muted">Start by adding your first custom snippet.</p>
                                <a href="{{ route('custom-codes.create') }}" class="btn-brand-primary mt-3">
                                    Add New Snippet
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($codes->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $codes->links() }}
        </div>
        @endif
    </div>
@endsection
