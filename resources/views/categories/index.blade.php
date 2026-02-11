@extends('layouts.app')

@section('title', 'Categories Management')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-folder-tree"></i>
                </div>
                Categories Management
            </h1>
            <p class="brand-subtitle">Organize and manage your product hierarchy and taxonomy</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Create Category
        </a>
    </div>

    <!-- Search Bar -->
    <div class="brand-filter-bar">
        <form action="{{ route('categories.index') }}" method="GET" class="d-flex align-items-center gap-3">
            <div class="brand-search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Search categories by name or description...">
            </div>
            
            <button type="submit" class="btn-brand-primary">
                <i class="fas fa-filter me-1"></i> Search
            </button>
            
            @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn-brand-light" title="Clear Search">
                    <i class="fas fa-times me-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Categories Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Category Hierarchy</th>
                        <th>Slug / Identifier</th>
                        <th class="text-center">Assigned Products</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories->where('parent_id', null) as $category)
                        @include('categories.partials.category-row', ['category' => $category, 'level' => 0])
                        
                        @if(!request('search')) {{-- Only show children relationships if not searching --}}
                            @foreach($category->children as $child)
                                @include('categories.partials.category-row', ['category' => $child, 'level' => 1])
                                
                                @foreach($child->children as $grandchild)
                                    @include('categories.partials.category-row', ['category' => $grandchild, 'level' => 2])
                                @endforeach
                            @endforeach
                        @endif
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No categories found</h5>
                                <p class="text-muted">Start by creating your first product category.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
