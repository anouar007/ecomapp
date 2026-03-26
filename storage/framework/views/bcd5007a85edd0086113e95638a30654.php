<?php $__env->startSection('title', 'Stock Adjustment'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="header-content">
        <div>
            <h1 class="page-title">
                <i class="fas fa-edit text-primary"></i>
                Adjust Stock
            </h1>
            <p class="page-subtitle"><?php echo e($product->translated_name); ?></p>
        </div>
        <div class="header-actions">
            <a href="<?php echo e(route('inventory.index')); ?>" class="btn btn-secondary">
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
                <form action="<?php echo e(route('inventory.process-adjustment', $product)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
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
                            <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                            <small class="text-muted">Enter the quantity to add or remove</small>
                            <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Describe the reason for this adjustment..."></textarea>
                        <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                        <a href="<?php echo e(route('inventory.index')); ?>" class="btn btn-secondary">
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
                <?php if($product->image): ?>
                    <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->translated_name); ?>" class="img-fluid rounded mb-3">
                <?php endif; ?>
                
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Product Name:</td>
                        <td class="fw-bold"><?php echo e($product->translated_name); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKU:</td>
                        <td><code><?php echo e($product->sku); ?></code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Category:</td>
                        <td><?php echo e($product->category->name ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Current Stock:</td>
                        <td>
                            <span class="badge <?php echo e($product->stock_quantity <= 0 ? 'bg-danger' : ($product->stock_quantity <= $product->low_stock_threshold ? 'bg-warning' : 'bg-success')); ?>">
                                <?php echo e($product->stock_quantity ?? 0); ?> units
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Low Stock Alert:</td>
                        <td><?php echo e($product->low_stock_threshold); ?> units</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Price:</td>
                        <td><?php echo e(currency($product->price)); ?></td>
                    </tr>
                    <?php if($product->cost_price): ?>
                    <tr>
                        <td class="text-muted">Cost Price:</td>
                        <td><?php echo e(currency($product->cost_price)); ?></td>
                    </tr>
                    <?php endif; ?>
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
                    <?php $__empty_1 = true; $__currentLoopData = $product->inventoryMovements()->latest()->limit(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-<?php echo e($movement->type_color); ?> mb-1">
                                    <?php echo e($movement->type_label); ?>

                                </span>
                                <div class="small text-muted">
                                    <?php echo e($movement->created_at->format('M d, Y H:i')); ?>

                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold <?php echo e($movement->quantity > 0 ? 'text-success' : 'text-danger'); ?>">
                                    <?php echo e($movement->quantity > 0 ? '+' : ''); ?><?php echo e($movement->quantity); ?>

                                </div>
                                <small class="text-muted"><?php echo e($movement->stock_after); ?> total</small>
                            </div>
                        </div>
                        <?php if($movement->reason): ?>
                        <div class="small text-muted mt-1">
                            <?php echo e(Str::limit($movement->reason, 50)); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="list-group-item text-center text-muted py-4">
                        No movements yet
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="<?php echo e(route('inventory.movements')); ?>?product_id=<?php echo e($product->id); ?>" class="text-decoration-none">
                    View All Movements <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/inventory/adjust.blade.php ENDPATH**/ ?>