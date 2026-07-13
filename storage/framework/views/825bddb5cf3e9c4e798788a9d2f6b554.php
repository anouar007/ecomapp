
<div class="shop-filter-card mb-4 p-3 bg-white rounded-4 border shadow-sm">
    <h6 class="fw-bold mb-3 text-dark" style="font-family: var(--font-heading);"><i class="fas fa-search me-2 text-muted"></i>البحث</h6>
    <form id="searchForm">
        <div class="input-group">
            <input type="text" name="q" class="form-control"
                   placeholder="اسم المنتج..." value="<?php echo e(request('q')); ?>" style="border-radius: 5px 0 0 5px;">
            <button type="submit" class="btn btn-outline-secondary" style="border-radius: 0 5px 5px 0; background-color: var(--primary); color: white; border: none;">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>


<div class="shop-filter-card mb-4 p-3 bg-white rounded-4 border shadow-sm">
    <h6 class="fw-bold mb-3 text-dark" style="font-family: var(--font-heading);"><i class="fas fa-th-large me-2 text-muted"></i>التصنيفات</h6>
    <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
        <li>
            <a href="#" class="category-filter text-decoration-none d-flex justify-content-between align-items-center py-1 <?php echo e(!request('category') ? 'fw-bold text-primary' : 'text-dark'); ?>" data-slug="">
                <span>جميع المنتجات</span>
                <span class="badge rounded-pill bg-light text-dark border"><?php echo e(\App\Models\Product::where('status','active')->count()); ?></span>
            </a>
        </li>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li>
            <a href="#" class="category-filter text-decoration-none d-flex justify-content-between align-items-center py-1 <?php echo e(request('category') == $cat->slug ? 'fw-bold text-primary' : 'text-dark'); ?>" data-slug="<?php echo e($cat->slug); ?>">
                <span><?php echo e($cat->name); ?></span>
                <span class="badge rounded-pill bg-light text-dark border"><?php echo e($cat->products_count); ?></span>
            </a>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>


<div class="shop-filter-card mb-4 p-3 bg-white rounded-4 border shadow-sm">
    <h6 class="fw-bold mb-3 text-dark" style="font-family: var(--font-heading);"><i class="fas fa-tag me-2 text-muted"></i>نطاق السعر</h6>
    <form id="priceFilterForm">
        <div class="d-flex align-items-center gap-2">
            <input type="number" name="min_price" class="form-control text-center"
                   placeholder="الأدنى" value="<?php echo e(request('min_price')); ?>" min="0">
            <span class="text-muted">—</span>
            <input type="number" name="max_price" class="form-control text-center"
                   placeholder="الأقصى" value="<?php echo e(request('max_price')); ?>" min="0">
        </div>
        <button type="submit" class="btn text-white w-100 mt-3 fw-bold" style="background-color: var(--primary); border-radius: 5px;">
            <i class="fas fa-filter me-2"></i>تطبيق الفلتر
        </button>
    </form>
</div>


<div class="shop-filter-card p-3 bg-white rounded-4 border shadow-sm">
    <h6 class="fw-bold mb-3 text-dark" style="font-family: var(--font-heading);"><i class="fas fa-bolt me-2 text-muted"></i>روابط سريعة</h6>
    <div class="d-flex flex-column gap-2">
        <a href="<?php echo e(route('shop.index')); ?>?sort=newest" class="text-decoration-none text-dark d-flex align-items-center py-1 hover-gold">
            <i class="fas fa-star me-2" style="color: var(--gold);"></i>المنتجات الجديدة
        </a>
        <a href="<?php echo e(route('shop.index')); ?>?sort=price_asc" class="text-decoration-none text-dark d-flex align-items-center py-1 hover-gold">
            <i class="fas fa-sort-amount-up me-2" style="color: var(--gold);"></i>السعر: من الأقل للأعلى
        </a>
        <a href="<?php echo e(route('shop.index')); ?>?sort=price_desc" class="text-decoration-none text-dark d-flex align-items-center py-1 hover-gold">
            <i class="fas fa-sort-amount-down me-2" style="color: var(--gold);"></i>السعر: من الأعلى للأقل
        </a>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/shop/partials/sidebar-content.blade.php ENDPATH**/ ?>