<?php $__env->startSection('title', 'مشاهدات المنتجات'); ?>

<?php $__env->startSection('content'); ?>
<div class="brand-header px-2 px-md-4 mb-3">
    <div>
        <h1 class="brand-title d-flex align-items-center gap-2 m-0" style="font-size: 1.25rem;">
            <div class="brand-header-icon p-2 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #c5a059 0%, #a88241 100%); color: white;">
                <i class="fas fa-eye"></i>
            </div>
            مشاهدات المنتجات
        </h1>
        <p class="brand-subtitle text-muted mt-1 small mb-0">عرض تفصيلي لمشاهدات المنتجات (اليوم مقابل الأمس)</p>
    </div>
</div>

<div class="px-2 px-md-4 mb-3">
    <form action="<?php echo e(route('dashboard.product-views')); ?>" method="GET" class="d-flex flex-wrap gap-2">
        <div class="flex-grow-1" style="min-width: 200px;">
            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                <span class="input-group-text bg-white border-0 text-muted"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control border-0 ps-0 shadow-none" placeholder="ابحث عن منتج..." value="<?php echo e(request('search')); ?>">
            </div>
        </div>
        <div class="" style="min-width: 150px;">
            <select name="category" class="form-select shadow-sm border-0 rounded-3 text-muted">
                <option value="">جميع التصنيفات</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->translated_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary shadow-sm rounded-3 px-4" style="background: linear-gradient(135deg, #c5a059 0%, #a88241 100%); border: none;">
            <i class="fas fa-filter"></i>
        </button>
        <?php if(request('search') || request('category')): ?>
            <a href="<?php echo e(route('dashboard.product-views')); ?>" class="btn btn-light shadow-sm rounded-3 border-0 text-danger px-3">
                <i class="fas fa-times"></i>
            </a>
        <?php endif; ?>
    </form>
</div>

<div class="px-2 px-md-4 pb-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="list-group list-group-flush">
            <?php $__empty_1 = true; $__currentLoopData = $productViews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="list-group-item d-flex align-items-center p-3 gap-3 hover-bg-light transition-300 border-bottom border-light">
                
                <!-- Image -->
                <div class="flex-shrink-0" style="width: 45px; height: 45px;">
                    <?php if($item['product'] && $item['product']->main_image): ?>
                        <img src="<?php echo e(asset('storage/' . $item['product']->main_image)); ?>" alt="" class="rounded-3 shadow-sm" style="width:100%; height:100%; object-fit:cover; border: 1px solid #f8f9fa;">
                    <?php else: ?>
                        <div class="rounded-3 shadow-sm bg-light d-flex align-items-center justify-content-center" style="width:100%; height:100%; border: 1px solid #f8f9fa;">
                            <i class="fas fa-box text-muted fs-6"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Name -->
                <div class="flex-grow-1 min-width-0">
                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 14px;">
                        <?php echo e($item['product'] ? $item['product']->translated_name : __('Unknown')); ?>

                    </h6>
                    <div class="text-muted mt-1" style="font-size: 11px;">
                        <?php echo e($item['product'] && $item['product']->category ? $item['product']->category->translated_name : 'بدون تصنيف'); ?>

                    </div>
                </div>

                <!-- Stats (Right Side) -->
                <div class="d-flex flex-column align-items-end flex-shrink-0 gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-success small" style="font-size: 11px;">اليوم</span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 font-inter" style="min-width: 40px; text-align: center;">
                            <?php echo e($item['today_views']); ?>

                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small" style="font-size: 11px;">الأمس</span>
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 font-inter" style="min-width: 40px; text-align: center;">
                            <?php echo e($item['yesterday_views']); ?>

                        </span>
                    </div>
                </div>

            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-5 text-center bg-white">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-chart-bar text-muted fa-lg"></i>
                </div>
                <h6 class="text-dark fw-bold mb-1">لا توجد بيانات مشاهدات حتى الآن</h6>
                <p class="text-muted small mb-0">بمجرد زيارة العملاء لمنتجاتك، ستظهر الإحصائيات هنا.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($productViews->hasPages()): ?>
    <div class="mt-4">
        <?php echo e($productViews->links()); ?>

    </div>
    <?php endif; ?>
</div>

<style>
.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}
.transition-300 {
    transition: all 0.2s ease;
}
.min-width-0 {
    min-width: 0;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/dashboard/product-views.blade.php ENDPATH**/ ?>