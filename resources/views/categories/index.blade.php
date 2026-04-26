@extends('layouts.app')

@section('title', __('Categories Management'))

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-folder-tree"></i>
                </div>
                {{ __('Categories Management') }}
            </h1>
            <p class="brand-subtitle">{{ __('Organize and manage your product hierarchy and taxonomy') }}</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> {{ __('Create Category') }}
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="brand-filter-bar px-3 py-2">
        <form action="{{ route('categories.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-8 col-lg-10">
                <div class="brand-search-wrapper w-100">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" 
                           placeholder="{{ __('Search...') }}">
                </div>
            </div>
            
            <div class="col-4 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-brand-primary w-100 py-2 px-0">
                    <i class="fas fa-filter"></i>
                </button>
                
                @if(request('search'))
                    <a href="{{ route('categories.index') }}" class="btn btn-brand-light py-2 px-3" title="{{ __('Clear') }}">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Mobile Categories List -->
    <div class="d-lg-none mt-3 px-1">
        @forelse($categories->where('parent_id', null) as $category)
            @include('categories.partials.category-card', ['category' => $category, 'level' => 0])
            
            @if(!request('search'))
                @foreach($category->children as $child)
                    @include('categories.partials.category-card', ['category' => $child, 'level' => 1])
                    
                    @foreach($child->children as $grandchild)
                        @include('categories.partials.category-card', ['category' => $grandchild, 'level' => 2])
                    @endforeach
                @endforeach
            @endif
        @empty
            <div class="glass-card p-5 text-center">
                <i class="fas fa-folder-open opacity-25 mb-3" style="font-size: 48px;"></i>
                <h5 class="fw-bold">{{ __('No categories found') }}</h5>
            </div>
        @endforelse
    </div>

    <!-- Categories Table (Desktop Only) -->
    <div class="brand-table-card d-none d-lg-block mt-4">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">{{ __('Category Hierarchy') }}</th>
                        <th>{{ __('Slug / Identifier') }}</th>
                        <th class="text-center">{{ __('Assigned Products') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end" style="padding-right: 1.5rem;">{{ __('Actions') }}</th>
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
                                <h5 class="fw-bold text-dark">{{ __('No categories found') }}</h5>
                                <p class="text-muted">{{ __('Start by creating your first product category.') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <a href="{{ route('categories.create') }}" class="mobile-fab d-lg-none">
        <i class="fas fa-plus"></i>
    </a>
@endsection
