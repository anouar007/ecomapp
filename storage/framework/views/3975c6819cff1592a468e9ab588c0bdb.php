<?php $__env->startSection('meta_title', 'المتجر — أناقة الأميرة'); ?>
<?php $__env->startSection('meta_description', 'تسوقي أرقى العبايات والخمارات المغربية بأفضل الأسعار. توصيل سريع لكل المدن.'); ?>

<?php $__env->startSection('content'); ?>


<section class="shop-hero py-4 py-lg-5">
    <div class="container px-xl-5 text-center">
        <h1 class="display-4 brand-heading mb-2 text-dark soft-glow-text" data-aos="fade-down" style="font-size: 2.8rem; letter-spacing: -0.02em;">
            اكتشفي <span class="text-gold">مجموعتنا</span> الفاخرة
        </h1>
        <p class="text-muted small mb-4 font-body opacity-75" data-aos="fade-up">انعمي بلمسة من الرقي المغربي الأصيل في كل تفصيل</p>

        
        <div class="category-story-track d-flex justify-content-lg-center" data-aos="fade-up">
            
            <a href="<?php echo e(route('shop.index')); ?>" class="category-story-pill <?php echo e(!request('category') ? 'active' : ''); ?>">
                <div class="category-story-img-wrapper">
                    <div class="category-story-img d-flex align-items-center justify-content-center bg-white border border-gold-light" style="font-size: 1.25rem;">
                       <i class="fas fa-border-all text-gold"></i>
                    </div>
                </div>
                <span class="category-story-label">عرض الكل</span>
            </a>

            <?php $__currentLoopData = $allCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('shop.index', ['category' => $cat->slug])); ?>" 
                   class="category-story-pill <?php echo e(request('category') == $cat->slug ? 'active' : ''); ?>">
                    <div class="category-story-img-wrapper">
                        <img src="<?php echo e($cat->image ? (Str::startsWith($cat->image, 'http') ? $cat->image : Storage::url($cat->image)) : asset('images/placeholder-cat.jpg')); ?>" 
                             class="category-story-img" alt="<?php echo e($cat->translated_name); ?>">
                    </div>
                    <span class="category-story-label"><?php echo e($cat->translated_name); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="section-py bg-surface" style="border-top: 1px solid rgba(0,0,0,0.02);">
    <div class="container px-xl-5">
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6" data-aos="fade-left">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-gold rounded" style="width: 4px; height: 35px;"></div>
                    <div>
                        <h2 class="brand-heading mb-0 h3">
                            <?php if(request('category')): ?>
                                <?php $currentCat = $allCategories->where('slug', request('category'))->first(); ?>
                                <?php echo e($currentCat ? $currentCat->translated_name : 'البحث'); ?>

                            <?php else: ?>
                                أرقى الموديلات
                            <?php endif; ?>
                        </h2>
                        <p class="text-muted small mb-0 font-body fw-bold" style="letter-spacing: 0.5px; opacity: 0.8;">
                             يتوفر لدينا <span class="text-gold"><?php echo e($products->total()); ?></span> قطعة حصرية
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end" data-aos="fade-right">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-white btn-sm border-0 shadow-premium px-4 py-2 rounded-pill dropdown-toggle font-body fw-bold text-muted" type="button" data-bs-toggle="dropdown" style="background: #fff;">
                        <i class="fas fa-sort-amount-down-alt me-2 text-gold"></i>
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