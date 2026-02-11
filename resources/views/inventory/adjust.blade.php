@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
<div class="page-header">
    <div class="header-content">
        <div>
            <h1 class="page-title">
                <i class="fas fa-edit text-primary"></i>
                Adjust Stock
            </h1>
            <p class="page-subtitle">{{ $product->name }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Stock Adjustment Form</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory.process-adjustment', $product) }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">Select type...</option>
                                <option value="in">Stock In (Add)</option>
                                <option value="out">Stock Out (Remove)</option>
                                <option value="adjustment">Adjustment (Correction)</option>
                                <option value="transfer">Transfer</option>
                                <option value="return">Customer Return</option>
                            </select>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                            <small class="text-muted">Enter the quantity to add or remove</small>
                            @error('quantity')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Describe the reason for this adjustment..."></textarea>
                        @error('reason')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Reference (Optional)</label>
                        <input type="text" name="reference" class="form-control" placeholder="e.g., PO-12345, Order #123">
                        <small class="text-muted">Purchase order, invoice, or other reference number</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Adjustment
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Product Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Product Information</h6>
            </div>
            <div class="card-body">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3">
                @endif
                
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Product Name:</td>
                        <td class="fw-bold">{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKU:</td>
                        <td><code>{{ $product->sku }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Category:</td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Current Stock:</td>
                        <td>
                            <span class="badge {{ $product->stock_quantity <= 0 ? 'bg-danger' : ($product->stock_quantity <= $product->low_stock_threshold ? 'bg-warning' : 'bg-success') }}">
                                {{ $product->stock_quantity ?? 0 }} units
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Low Stock Alert:</td>
                        <td>{{ $product->low_stock_threshold }} units</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Price:</td>
                        <td>{{ currency($product->price) }}</td>
                    </tr>
                    @if($product->cost_price)
                    <tr>
                        <td class="text-muted">Cost Price:</td>
                        <td>{{ currency($product->cost_price) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Recent Movements -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Recent Movements</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($product->inventoryMovements()->latest()->limit(5)->get() as $movement)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-{{ $movement->type_color }} mb-1">
                                    {{ $movement->type_label }}
                                </span>
                                <div class="small text-muted">
                                    {{ $movement->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold {{ $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </div>
                                <small class="text-muted">{{ $movement->stock_after }} total</small>
                            </div>
                        </div>
                        @if($movement->reason)
                        <div class="small text-muted mt-1">
                            {{ Str::limit($movement->reason, 50) }}
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted py-4">
                        No movements yet
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('inventory.movements') }}?product_id={{ $product->id }}" class="text-decoration-none">
                    View All Movements <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
