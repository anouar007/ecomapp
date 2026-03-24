@extends('layouts.app')

@section('title', __('Navigation Management'))

@section('content')
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-compass"></i>
                </div>
                {{ __('Navigation Menus') }}
            </h1>
            <p class="brand-subtitle">{{ __("Manage your website's header and footer navigation") }}</p>
        </div>
        <button type="button" class="btn-brand-primary" data-bs-toggle="modal" data-bs-target="#createMenuModal">
            <i class="fas fa-plus me-2"></i> {{ __('Create New Menu') }}
        </button>
    </div>

    <div class="row g-4">
        @foreach($menus as $menu)
        <div class="col-lg-6">
            <div class="brand-table-card">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $menu->name }}</h5>
                        <code class="small text-primary">{{ $menu->location }}</code>
                    </div>
                    <form action="{{ route('menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('{{ __('Delete this menu?') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger border-0">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
                
                <form action="{{ route('menus.items.update', $menu) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-4" id="menu-items-{{ $menu->id }}">
                        <div class="menu-items-list">
                            @foreach($menu->items as $index => $item)
                                <div class="row g-2 mb-3 align-items-center menu-item-row">
                                    <div class="col-md-5">
                                        <input type="text" name="items[{{ $index }}][label]" class="form-control form-control-sm" value="{{ $item->label }}" placeholder="{{ __('Label') }}">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="items[{{ $index }}][link]" class="form-control form-control-sm" value="{{ $item->link }}" placeholder="{{ __('URL') }} (e.g. /about or https://...)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-light text-danger w-100" onclick="this.closest('.menu-item-row').remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addMenuItem({{ $menu->id }})">
                            <i class="fas fa-plus me-1"></i> {{ __('Add Link') }}
                        </button>
                    </div>
                    
                    <div class="p-4 bg-light border-top text-end">
                        <button type="submit" class="btn-brand-primary btn-sm">
                            <i class="fas fa-save me-1"></i> {{ __('Save Items') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Create Menu Modal -->
    <div class="modal fade" id="createMenuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form action="{{ route('menus.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-bold">{{ __('New Navigation Menu') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">{{ __('MENU NAME') }}</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Main Navigation" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">{{ __('LOCATION IDENTIFIER') }}</label>
                            <select name="location" class="form-select" required>
                                <option value="header">{{ __('Header') }}</option>
                                <option value="footer_main">{{ __('Footer Main') }}</option>
                                <option value="footer_links">{{ __('Footer Quick Links') }}</option>
                                <option value="social_sidebar">{{ __('Social Sidebar') }}</option>
                            </select>
                            <div class="form-text small">{{ __('This ID is used to fetch the menu in the frontend code.') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Create Menu') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function addMenuItem(menuId) {
            const container = document.querySelector(`#menu-items-${menuId} .menu-items-list`);
            const index = container.querySelectorAll('.menu-item-row').length;
            
            const html = `
                <div class="row g-2 mb-3 align-items-center menu-item-row">
                    <div class="col-md-5">
                        <input type="text" name="items[${index}][label]" class="form-control form-control-sm" placeholder="{{ __('Label') }}">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="items[${index}][link]" class="form-control form-control-sm" placeholder="{{ __('URL') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-light text-danger w-100" onclick="this.closest('.menu-item-row').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
    @endpush
@endsection
