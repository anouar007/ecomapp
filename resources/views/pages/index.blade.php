@extends('layouts.app')

@section('title', 'Page Management')

@section('content')
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                Page Management
            </h1>
            <p class="brand-subtitle">Create and manage your client-facing website pages</p>
        </div>
        <a href="{{ route('pages.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Create New Page
        </a>
    </div>

    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Page Title</th>
                        <th>Slug</th>
                        <th class="text-center">Status</th>
                        <th>Last Updated</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <div class="fw-bold text-dark">{{ $page->title }}</div>
                            <div class="text-muted small">Layout: {{ ucfirst($page->layout) }}</div>
                        </td>
                        <td>
                            <a href="{{ url($page->slug) }}" target="_blank" class="text-primary text-decoration-none">
                                /{{ $page->slug }} <i class="fas fa-external-link-alt ms-1 small"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="brand-badge {{ $page->is_published ? 'success' : 'warning' }}">
                                {{ $page->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $page->updated_at->format('M d, Y H:i') }}</div>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('pages.edit', $page) }}" class="btn-action-icon" title="Edit Page">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('pages.destroy', $page) }}" 
                                      style="display: inline;"
                                      data-confirm-delete="true"
                                      data-item-type="page"
                                      data-item-name="{{ $page->title }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon danger" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-file-code text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No pages created yet</h5>
                                <p class="text-muted">Start by creating your first client-facing page.</p>
                                <a href="{{ route('pages.create') }}" class="btn-brand-primary mt-2">
                                    Create First Page
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pages->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $pages->links() }}
        </div>
        @endif
    </div>
@endsection
