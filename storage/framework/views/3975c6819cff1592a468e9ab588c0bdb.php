<?php $__env->startSection('meta_title', 'المتجر — أناقة الأميرة'); ?>
<?php $__env->startSection('meta_description', 'تسوقي أرقى العبايات والخمارات المغربية بأفضل الأسعار. توصيل سريع لكل المدن.'); ?>

<?php $__env->startSection('content'); ?>


<section class="pdp-breadcrumb-bar py-3 bg-white border-bottom">
    <div class="container px-xl-5 small font-body">
        <nav class="pdp-breadcrumb" aria-label="breadcrumb">
            <a href="<?php echo e(url('/')); ?>" class="text-muted text-decoration-none"><i class="fas fa-home"></i></a>
            <span class="mx-2 text-muted opacity-50">/</span>
            <span class="text-gold fw-bold">المتجر</span>
        </nav>

        
        <div class="stylist-pill-track mt-4" data-aos="fade-up">
            <a href="<?php echo e(route('shop.index')); ?>" class="stylist-pill <?php echo e(!request('category') ? 'active' : ''); ?>">الكل</a>
            <?php $__currentLoopData = $allCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('shop.index', ['category' => $cat->slug])); ?>" 
                   class="stylist-pill <?php echo e(request('category') == $cat->slug ? 'active' : ''); ?>">
                    <?php echo e($cat->translated_name); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="section-py bg-surface">
    <div class="container px-xl-5">
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="brand-heading mb-0">
                    <?php if(request('category')): ?>
                        <?php $currentCat = $allCategories->where('slug', request('category'))->first(); ?>
                        <?php echo e($currentCat ? $currentCat->translated_name : 'البحث'); ?>

                    <?php else: ?>
                        كل المنتجات
                    <?php endif; ?>
                </h2>
                <p class="text-muted small mb-0 font-body"><?php echo e($products->total()); ?> قطعة مميزة</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-white btn-sm border px-3 rounded-pill dropdown-toggle font-body" type="button" data-bs-toggle="dropdown">
                        ترتيب حسب
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'latest'])); ?>">الأحدث</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'price_low'])); ?>">السعر: من الأقل</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'price_high'])); ?>">السعر: من الأعلى</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php if($products->isEmpty()): ?>
            <div class="text-center py-5">
                <div class="app-icon mx-auto mb-4 bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-search"></i>
                </div>
                <h4 class="brand-heading">لم نجد أي منتجات</h4>
                <p class="text-muted font-body">حاولي تغيير معايير البحث أو تصفح كل المجموعات</p>
                <a href="<?php echo e(route('shop.index')); ?>" class="btn btn-brand-primary rounded-pill px-4 mt-3">عرض كل المجموعة</a>
            </div>
        <?php else: ?>
            <div class="row g-3 g-lg-4">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                        <?php echo $__env->make('frontend.partials.product_card_v2', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="mt-5 d-flex justify-content-center brand-pagination">
                <?php echo e($products->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/shop/index.blade.php ENDPATH**/ ?>