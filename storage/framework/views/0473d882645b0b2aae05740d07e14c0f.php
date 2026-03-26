<?php $__env->startSection('title', __('Product Reviews')); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/management.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-star"></i> <?php echo e(__('Product Reviews')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('Manage customer feedback and ratings')); ?></p>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-star" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Total Reviews')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;" data-stat="total"><?php echo e(number_format($stats['total_reviews'])); ?></p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%); border-radius: 16px; padding: 24px; border: 1px solid #fde68a;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #92400e; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Pending Reviews')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #b45309; margin: 0;" data-stat="pending"><?php echo e(number_format($stats['pending_reviews'])); ?></p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #d1fae5 100%); border-radius: 16px; padding: 24px; border: 1px solid #a7f3d0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #166534; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Approved Reviews')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;" data-stat="approved"><?php echo e(number_format($stats['approved_reviews'])); ?></p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #ddd6fe 100%); border-radius: 16px; padding: 24px; border: 1px solid #c4b5fd;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #5b21b6; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;"><?php echo e(__('Average Rating')); ?></p>
                <p style="font-size: 28px; font-weight: 700; color: #6d28d9; margin: 0;" data-stat="average"><?php echo e($stats['average_rating']); ?> <span style="font-size: 16px;">/ 5</span></p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> <?php echo e(__('Filters')); ?></h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('reviews.index')); ?>" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" style="flex: 1; min-width: 200px;" 
                   placeholder="<?php echo e(__('Search reviews...')); ?>" value="<?php echo e(request('search')); ?>">
            
            <select name="status" class="form-control" style="width: auto; min-width: 150px;">
                <option value=""><?php echo e(__('All Status')); ?></option>
                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>><?php echo e(__('Pending')); ?></option>
                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>><?php echo e(__('Approved')); ?></option>
                <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>><?php echo e(__('Rejected')); ?></option>
            </select>
            
            <select name="rating" class="form-control" style="width: auto; min-width: 150px;">
                <option value=""><?php echo e(__('All Ratings')); ?></option>
                <option value="5" <?php echo e(request('rating') == '5' ? 'selected' : ''); ?>>★★★★★ (5 <?php echo e(__('stars')); ?>)</option>
                <option value="4" <?php echo e(request('rating') == '4' ? 'selected' : ''); ?>>★★★★☆ (4 <?php echo e(__('stars')); ?>)</option>
                <option value="3" <?php echo e(request('rating') == '3' ? 'selected' : ''); ?>>★★★☆☆ (3 <?php echo e(__('stars')); ?>)</option>
                <option value="2" <?php echo e(request('rating') == '2' ? 'selected' : ''); ?>>★★☆☆☆ (2 <?php echo e(__('stars')); ?>)</option>
                <option value="1" <?php echo e(request('rating') == '1' ? 'selected' : ''); ?>>★☆☆☆☆ (1 <?php echo e(__('star')); ?>)</option>
            </select>
            
            <select name="product_id" class="form-control" style="width: auto; min-width: 200px;">
                <option value=""><?php echo e(__('All Products')); ?></option>
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($product->id); ?>" <?php echo e(request('product_id') == $product->id ? 'selected' : ''); ?>>
                        <?php echo e($product->translated_name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> <?php echo e(__('Filter')); ?></button>
            <a href="<?php echo e(route('reviews.index')); ?>" class="btn btn-secondary"><i class="fas fa-redo"></i> <?php echo e(__('Reset')); ?></a>
        </form>
    </div>
</div>

<!-- Reviews Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> <?php echo e(__('Reviews')); ?> (<?php echo e($reviews->total()); ?>)</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo e(__('Product')); ?></th>
                    <th><?php echo e(__('Customer')); ?></th>
                    <th><?php echo e(__('Rating')); ?></th>
                    <th><?php echo e(__('Review')); ?></th>
                    <th><?php echo e(__('Date')); ?></th>
                    <th><?php echo e(__('Status')); ?></th>
                    <th><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr data-review-id="<?php echo e($review->id); ?>">
                    <td>
                        <strong><?php echo e($review->product->name); ?></strong>
                        <br><small class="text-muted">ID: #<?php echo e($review->product->id); ?></small>
                    </td>
                    <td>
                        <?php echo e($review->customer_name); ?>

                        <br><small class="text-muted"><?php echo e($review->customer_email); ?></small>
                    </td>
                    <td>
                        <div style="color: #f59e0b; font-size: 16px;">
                            <?php echo str_repeat('★', $review->rating); ?><?php echo str_repeat('☆', 5 - $review->rating); ?>

                        </div>
                        <small class="text-muted"><?php echo e($review->rating); ?>/5</small>
                    </td>
                    <td style="max-width: 300px;">
                        <strong><?php echo e($review->title); ?></strong>
                        <br><small><?php echo e(Str::limit($review->comment, 80)); ?></small>
                    </td>
                    <td>
                        <small><?php echo e($review->created_at->format('M d, Y')); ?></small>
                        <br><small class="text-muted"><?php echo e($review->created_at->diffForHumans()); ?></small>
                    </td>
                    <td data-status>
                        <?php if($review->status == 'approved'): ?>
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> <?php echo e(__('Approved')); ?></span>
                        <?php elseif($review->status == 'pending'): ?>
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> <?php echo e(__('Pending')); ?></span>
                        <?php else: ?>
                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> <?php echo e(__('Rejected')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <?php if($review->status == 'pending'): ?>
                            <form action="<?php echo e(route('reviews.approve', $review)); ?>" method="POST" style="display: inline;" onsubmit="event.preventDefault(); approveReview(<?php echo e($review->id); ?>);">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-action btn-action-success" title="<?php echo e(__('Approve Review')); ?>">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="<?php echo e(route('reviews.reject', $review)); ?>" method="POST" style="display: inline;" onsubmit="event.preventDefault(); rejectReview(<?php echo e($review->id); ?>);">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-action btn-action-warning" title="<?php echo e(__('Reject Review')); ?>">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <form action="<?php echo e(route('reviews.destroy', $review)); ?>" method="POST" style="display: inline;" onsubmit="event.preventDefault(); deleteReview(<?php echo e($review->id); ?>);">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-action btn-action-delete" title="<?php echo e(__('Delete Review')); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-star"></i>
                        <p><?php echo e(__('No reviews found')); ?></p>
                        <small class="text-muted"><?php echo e(__('Customer reviews will appear here once submitted')); ?></small>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($reviews->hasPages()): ?>
    <div class="card-footer">
        <?php echo e($reviews->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Update statistics cards
function updateStats(stats) {
    // Update each stat card
    document.querySelector('[data-stat="total"]').textContent = stats.total_reviews.toLocaleString();
    document.querySelector('[data-stat="pending"]').textContent = stats.pending_reviews.toLocaleString();
    document.querySelector('[data-stat="approved"]').textContent = stats.approved_reviews.toLocaleString();
    document.querySelector('[data-stat="average"]').innerHTML = stats.average_rating + ' <span style="font-size: 16px;">/ 5</span>';
}

// Approve review
function approveReview(reviewId) {
    fetch(`/reviews/${reviewId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update statistics
            updateStats(data.stats);
            
            // Update row
            const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
            
            // Update status badge
            const statusCell = row.querySelector('[data-status]');
            statusCell.innerHTML = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> <?php echo e(__('Approved')); ?></span>';
            
            // Update actions - remove approve/reject buttons
            const actionsCell = row.querySelector('.action-buttons');
            actionsCell.querySelector('form[action*="approve"]')?.remove();
            actionsCell.querySelector('form[action*="reject"]')?.remove();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: '<?php echo e(__('Success!')); ?>',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<?php echo e(__('Error!')); ?>',
            text: '<?php echo e(__('Failed to approve review')); ?>',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    });
}

// Reject review
function rejectReview(reviewId) {
    fetch(`/reviews/${reviewId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update statistics
            updateStats(data.stats);
            
            // Update row
            const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
            
            // Update status badge
            const statusCell = row.querySelector('[data-status]');
            statusCell.innerHTML = '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> <?php echo e(__('Rejected')); ?></span>';
            
            // Update actions - remove approve/reject buttons
            const actionsCell = row.querySelector('.action-buttons');
            actionsCell.querySelector('form[action*="approve"]')?.remove();
            actionsCell.querySelector('form[action*="reject"]')?.remove();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: '<?php echo e(__('Success!')); ?>',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<?php echo e(__('Error!')); ?>',
            text: '<?php echo e(__('Failed to reject review')); ?>',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    });
}

// Delete review
function deleteReview(reviewId) {
    Swal.fire({
        title: '<?php echo e(__('Delete Review?')); ?>',
        text: "<?php echo e(__('This action cannot be undone!')); ?>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<?php echo e(__('Yes, delete it!')); ?>',
        cancelButtonText: '<?php echo e(__('Cancel')); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/reviews/${reviewId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update statistics
                    updateStats(data.stats);
                    
                    // Remove row with animation
                    const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        
                        // Check if table is empty
                        const tbody = document.querySelector('table tbody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="empty-state">
                                        <i class="fas fa-star"></i>
                                        <p><?php echo e(__('No reviews found')); ?></p>
                                        <small class="text-muted"><?php echo e(__('Customer reviews will appear here once submitted')); ?></small>
                                    </td>
                                </tr>
                            `;
                        }
                    }, 300);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: '<?php echo e(__('Deleted!')); ?>',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '<?php echo e(__('Error!')); ?>',
                    text: '<?php echo e(__('Failed to delete review')); ?>',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        }
    });
}
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/reviews/index.blade.php ENDPATH**/ ?>