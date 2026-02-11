@extends('layouts.app')

@section('title', 'Edit Coupon')

@section('content')
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-edit"></i>
                </div>
                Edit Coupon
            </h1>
            <p class="brand-subtitle">Update coupon details for {{ $coupon->code }}</p>
        </div>
        <div>
            <a href="{{ route('coupons.show', $coupon) }}" class="btn-brand-light">
                <i class="fas fa-times me-2"></i> Cancel
            </a>
        </div>
    </div>

    <form action="{{ route('coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Info -->
            <div class="col-lg-8">
                <div class="brand-card mb-4">
                    <div class="brand-card-header">
                        <h5 class="brand-card-title">Coupon Details</h5>
                    </div>
                    <div class="brand-card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Coupon Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control font-monospace" value="{{ old('code', $coupon->code) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $coupon->name) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $coupon->description) }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" id="couponType" required>
                                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Percentage Discount</option>
                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    <option value="free_shipping" {{ old('type', $coupon->type) == 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                                    <option value="buy_x_get_y" {{ old('type', $coupon->type) == 'buy_x_get_y' ? 'selected' : '' }}>Buy X Get Y</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="valueField">
                                <label class="form-label fw-bold small text-uppercase">Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="valuePrefix">$</span>
                                    <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $coupon->value) }}">
                                    <span class="input-group-text" id="valueSuffix" style="display:none">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Buy X Get Y Section -->
                        <div class="row mb-3 p-3 bg-light rounded" id="buyXGetYFields" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Buy Quantity (X) <span class="text-danger">*</span></label>
                                <input type="number" name="buy_quantity" class="form-control" value="{{ old('buy_quantity', $coupon->buy_quantity) }}" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Get Quantity (Y) <span class="text-danger">*</span></label>
                                <input type="number" name="get_quantity" class="form-control" value="{{ old('get_quantity', $coupon->get_quantity) }}" min="1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Valid From</label>
                                <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from', optional($coupon->valid_from)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Valid To</label>
                                <input type="date" name="valid_to" class="form-control" value="{{ old('valid_to', optional($coupon->valid_to)->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="brand-card">
                    <div class="brand-card-header">
                        <h5 class="brand-card-title">Usage Restrictions</h5>
                    </div>
                    <div class="brand-card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Minimum Order Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $coupon->min_order_amount) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Max Discount Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Utilization Limit (Total)</label>
                                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Limit Per Customer</label>
                                <input type="number" name="per_customer_limit" class="form-control" value="{{ old('per_customer_limit', $coupon->per_customer_limit) }}" min="1">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="firstOrderOnly" name="first_order_only" value="1" {{ old('first_order_only', $coupon->first_order_only) ? 'checked' : '' }}>
                                <label class="form-check-label" for="firstOrderOnly">First order only</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="brand-card mb-4">
                    <div class="brand-card-header">
                        <h5 class="brand-card-title">Status</h5>
                    </div>
                    <div class="brand-card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Active Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $coupon->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $coupon->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="expired" {{ old('status', $coupon->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-brand-primary w-100">
                            Update Coupon
                        </button>
                    </div>
                </div>

                <div class="brand-card">
                    <div class="brand-card-header">
                        <h5 class="brand-card-title">Applicable To</h5>
                    </div>
                    <div class="brand-card-body">
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="applicable_to" id="applyAll" value="all" {{ old('applicable_to', $coupon->applicable_to) == 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="applyAll">All Products</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="applicable_to" id="applyProducts" value="specific_products" {{ old('applicable_to', $coupon->applicable_to) == 'specific_products' ? 'checked' : '' }}>
                                <label class="form-check-label" for="applyProducts">Specific Products</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="applicable_to" id="applyCategories" value="specific_categories" {{ old('applicable_to', $coupon->applicable_to) == 'specific_categories' ? 'checked' : '' }}>
                                <label class="form-check-label" for="applyCategories">Specific Categories</label>
                            </div>
                        </div>

                        <div id="productSelect" style="display: none;" class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Select Products</label>
                            <select name="applicable_ids[]" class="form-select" multiple size="5">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ in_array($product->id, $coupon->applicable_ids ?? []) ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
                        </div>

                        <div id="categorySelect" style="display: none;" class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Select Categories</label>
                            <select name="applicable_ids[]" class="form-select" multiple size="5">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, $coupon->applicable_ids ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('couponType');
            const valueField = document.getElementById('valueField');
            const valuePrefix = document.getElementById('valuePrefix');
            const valueSuffix = document.getElementById('valueSuffix');
            const buyXGetYFields = document.getElementById('buyXGetYFields');
            
            function updateType() {
                const type = typeSelect.value;
                
                if (type === 'free_shipping') {
                    valueField.style.display = 'none';
                    buyXGetYFields.style.display = 'none';
                } else if (type === 'buy_x_get_y') {
                    valueField.style.display = 'none';
                    buyXGetYFields.style.display = 'flex';
                } else {
                    valueField.style.display = 'block';
                    buyXGetYFields.style.display = 'none';
                    
                    if (type === 'percentage') {
                        valuePrefix.style.display = 'none';
                        valueSuffix.style.display = 'block';
                    } else {
                        valuePrefix.style.display = 'block';
                        valueSuffix.style.display = 'none';
                    }
                }
            }
            
            typeSelect.addEventListener('change', updateType);
            updateType(); // Initial call

            // Applicability Logic
            const applyAll = document.getElementById('applyAll');
            const applyProducts = document.getElementById('applyProducts');
            const applyCategories = document.getElementById('applyCategories');
            const productSelect = document.getElementById('productSelect');
            const categorySelect = document.getElementById('categorySelect');

            function updateApplicability() {
                if (applyProducts.checked) {
                    productSelect.style.display = 'block';
                    categorySelect.style.display = 'none';
                    productSelect.querySelector('select').disabled = false;
                    categorySelect.querySelector('select').disabled = true;
                } else if (applyCategories.checked) {
                    productSelect.style.display = 'none';
                    categorySelect.style.display = 'block';
                    productSelect.querySelector('select').disabled = true;
                    categorySelect.querySelector('select').disabled = false;
                } else {
                    productSelect.style.display = 'none';
                    categorySelect.style.display = 'none';
                    productSelect.querySelector('select').disabled = true;
                    categorySelect.querySelector('select').disabled = true;
                }
            }

            [applyAll, applyProducts, applyCategories].forEach(el => el.addEventListener('change', updateApplicability));
            updateApplicability();
        });
    </script>
@endsection
