@extends('layouts.app')

@section('title', 'Products Management')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-box"></i>
                </div>
                Products Management
            </h1>
            <p class="brand-subtitle">Manage your product catalog, pricing, and availability</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Add New Product
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="brand-filter-bar">
        <form method="GET" action="{{ route('products.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="brand-search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       placeholder="Search products by name, SKU..."
                       value="{{ request('search') }}">
            </div>
            
            <select name="category" class="form-select w-auto">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn-brand-primary">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <a href="{{ route('products.index') }}" class="btn-brand-light" title="Reset">
                <i class="fas fa-redo"></i>
            </a>
            
            @if(request('search') || request('category'))
            <div class="ms-2">
                <span class="badge bg-light text-secondary px-3 py-2" style="border-radius: 8px;">
                    Found {{ $products->total() }} results
                </span>
            </div>
            @endif

            <div class="ms-auto d-flex gap-2">
                <button type="button" class="btn-brand-outline" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-upload me-2" style="color: var(--primary-color)"></i>Import Excel
                </button>
                <a href="{{ route('export.products') }}" class="btn-brand-outline">
                    <i class="fas fa-file-csv me-2" style="color: var(--success-color)"></i>Export CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold" id="importModalLabel">
                        <i class="fas fa-file-upload me-2 text-primary"></i>Import Products from Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Upload Excel File</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Supported formats: .xlsx, .xls, .csv (max 10MB)</div>
                        </div>
                        
                        <div class="alert alert-info border-0 rounded-3 mb-0">
                            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Required Columns</h6>
                            <p class="mb-2 small">Your file must have these column headers:</p>
                            <code class="d-block bg-white p-2 rounded small">name, sku, description, price, cost_price, stock, min_stock, category, status</code>
                            <p class="mb-0 mt-2 small">
                                <a href="{{ route('products.template') }}" class="fw-bold">
                                    <i class="fas fa-download me-1"></i>Download Sample Template
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-brand-primary">
                            <i class="fas fa-upload me-2"></i>Import Products
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulkActionsBar" class="bulk-actions-bar" style="display: none;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <span class="selected-count fw-bold">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="selectedCount">0</span> product(s) selected
                </span>
                <button type="button" class="btn btn-sm btn-light" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Clear
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Stock Amount Input (for increase/decrease) -->
                <div class="input-group input-group-sm" style="width: 140px;" id="stockAmountGroup" style="display: none;">
                    <span class="input-group-text">Qty</span>
                    <input type="number" id="stockAmount" class="form-control" value="10" min="1" max="9999">
                </div>
                
                <!-- Action Buttons -->
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm" onclick="executeBulkAction('increase_stock')" title="Increase Stock">
                        <i class="fas fa-plus me-1"></i>Add Stock
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="executeBulkAction('decrease_stock')" title="Decrease Stock">
                        <i class="fas fa-minus me-1"></i>Remove Stock
                    </button>
                </div>
                <button type="button" class="btn btn-info btn-sm text-white" onclick="executeBulkAction('duplicate')" title="Duplicate">
                    <i class="fas fa-copy me-1"></i>Duplicate
                </button>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="executeBulkAction('activate')" title="Activate">
                        <i class="fas fa-check me-1"></i>Activate
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="executeBulkAction('deactivate')" title="Deactivate">
                        <i class="fas fa-ban me-1"></i>Deactivate
                    </button>
                </div>
                <button type="button" class="btn btn-danger btn-sm" onclick="executeBulkAction('delete')" title="Delete">
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="width: 50px; padding-left: 1.5rem;">
                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll(this)">
                        </th>
                        <th>Product Details</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Pricing</th>
                        <th>Inventory</th>
                        <th>Status</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr data-product-id="{{ $product->id }}">
                        <td style="padding-left: 1.5rem;">
                            <input type="checkbox" class="form-check-input product-checkbox" 
                                   value="{{ $product->id }}" 
                                   onchange="updateSelection()">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="brand-avatar">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="">
                                    @elseif($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="">
                                    @else
                                        <i class="fas fa-image"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    @if($product->description)
                                        <div class="text-muted small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $product->description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.7rem; border: 1px solid #e2e8f0;">
                                {{ $product->sku }}
                            </span>
                        </td>
                        <td>
                            @if($product->category_name)
                                <span class="brand-badge primary">{{ $product->category_name }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ currency($product->price) }}</div>
                            @if($product->cost_price)
                                <div class="text-muted" style="font-size: 0.7rem;">Cost: {{ currency($product->cost_price) }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $stock = $product->stock;
                                $min = $product->min_stock ?? 5;
                                $badgeClass = 'success';
                                $badgeText = 'In Stock: ' . $stock;
                                if ($stock <= 0) {
                                    $badgeClass = 'danger';
                                    $badgeText = 'Out of Stock';
                                } elseif ($stock <= $min) {
                                    $badgeClass = 'warning';
                                    $badgeText = 'Low Stock: ' . $stock;
                                }
                            @endphp
                            <span class="brand-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                        </td>
                        <td>
                            <span class="brand-badge {{ $product->status === 'active' ? 'success' : 'info' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="btn-action-icon" title="Edit Product">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('products.destroy', $product->id) }}" 
                                      style="display: inline;"
                                      data-confirm-delete="true"
                                      data-item-type="product"
                                      data-item-name="{{ $product->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon danger" title="Delete Product">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No products found</h5>
                                <p class="text-muted">Try refining your search or filter to find what you're looking for.</p>
                                @if(request('search') || request('category'))
                                    <a href="{{ route('products.index') }}" class="btn-brand-primary mt-3">
                                        Clear All Filters
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $products->links() }}
        </div>
        @endif
    </div>

@push('styles')
<style>
    .bulk-actions-bar {
        background: linear-gradient(135deg, var(--primary-color, #00BFA6) 0%, var(--secondary-color, #00A896) 100%);
        color: white;
        padding: 14px 24px;
        border-radius: var(--border-radius, 12px);
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 191, 166, 0.25);
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .bulk-actions-bar .selected-count {
        color: white;
        font-size: 0.95rem;
    }
    
    .bulk-actions-bar .btn-light {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .bulk-actions-bar .btn-light:hover {
        background: rgba(255,255,255,0.35);
        color: white;
    }
    
    .bulk-actions-bar .input-group-text {
        background: rgba(255,255,255,0.25);
        border: none;
        color: white;
        font-weight: 500;
        border-radius: 8px 0 0 8px;
    }
    
    .bulk-actions-bar .form-control {
        background: rgba(255,255,255,0.95);
        border: none;
        border-radius: 0 8px 8px 0;
        font-weight: 500;
    }
    
    .bulk-actions-bar .btn {
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.85rem;
        padding: 6px 12px;
        border: none;
    }
    
    .bulk-actions-bar .btn-group .btn {
        border-radius: 0;
    }
    
    .bulk-actions-bar .btn-group .btn:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .bulk-actions-bar .btn-group .btn:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .bulk-actions-bar .btn-success {
        background: #10b981;
    }
    
    .bulk-actions-bar .btn-warning {
        background: #f59e0b;
        color: white;
    }
    
    .bulk-actions-bar .btn-info {
        background: #3b82f6;
    }
    
    .bulk-actions-bar .btn-secondary {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    
    .bulk-actions-bar .btn-secondary:hover {
        background: rgba(255,255,255,0.35);
        color: white;
    }
    
    .bulk-actions-bar .btn-danger {
        background: #ef4444;
    }
    
    .product-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        border-radius: 4px;
    }
    
    .product-checkbox:checked {
        background-color: var(--primary-color, #00BFA6);
        border-color: var(--primary-color, #00BFA6);
    }
    
    tr.selected {
        background-color: rgba(0, 191, 166, 0.08) !important;
    }
    
    #selectAll {
        width: 18px;
        height: 18px;
        cursor: pointer;
        border-radius: 4px;
    }
    
    #selectAll:checked {
        background-color: var(--primary-color, #00BFA6);
        border-color: var(--primary-color, #00BFA6);
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedProducts = [];
    
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateSelection();
    }
    
    function updateSelection() {
        selectedProducts = [];
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        checkboxes.forEach(cb => {
            selectedProducts.push(parseInt(cb.value));
            cb.closest('tr').classList.add('selected');
        });
        
        // Remove selected class from unchecked rows
        document.querySelectorAll('.product-checkbox:not(:checked)').forEach(cb => {
            cb.closest('tr').classList.remove('selected');
        });
        
        // Update count display
        document.getElementById('selectedCount').textContent = selectedProducts.length;
        
        // Show/hide bulk actions bar
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        if (selectedProducts.length > 0) {
            bulkActionsBar.style.display = 'block';
        } else {
            bulkActionsBar.style.display = 'none';
        }
        
        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.product-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        if (allCheckboxes.length > 0 && selectedProducts.length === allCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (selectedProducts.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }
    
    function clearSelection() {
        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        updateSelection();
    }
    
    function executeBulkAction(action) {
        if (selectedProducts.length === 0) {
            Swal.fire('No Selection', 'Please select at least one product.', 'warning');
            return;
        }
        
        const actionLabels = {
            'delete': 'delete',
            'duplicate': 'duplicate',
            'increase_stock': 'increase stock for',
            'decrease_stock': 'decrease stock for',
            'activate': 'activate',
            'deactivate': 'deactivate'
        };
        
        const confirmMessage = `Are you sure you want to ${actionLabels[action]} ${selectedProducts.length} product(s)?`;
        
        Swal.fire({
            title: 'Confirm Action',
            text: confirmMessage,
            icon: action === 'delete' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#dc3545' : '#4F46E5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction(action);
            }
        });
    }
    
    function performBulkAction(action) {
        const stockAmount = document.getElementById('stockAmount').value || 10;
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we process your request.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route('products.bulk-action') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                product_ids: selectedProducts,
                stock_amount: parseInt(stockAmount)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reload the page to reflect changes
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'An error occurred.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        });
    }
</script>
@endpush
@endsection

