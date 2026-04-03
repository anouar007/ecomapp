<?php $__env->startSection('meta_title', 'المتجر الرسمي — Hijab Princesses — أناقة الأميرة'); ?>
<?php $__env->startSection('meta_description', 'Hijab Princesses: اكتشفي أرقى تشكيلة من العبايات والخمارات المغربية الراقية. جودة فاخرة وتوصيل سريع لكل مدن المغرب. تسوقي الآن من أناقة الأميرة.'); ?>

<?php $__env->startSection('content'); ?>


<section class="shop-hero py-4 py-lg-5">
    <div class="container px-xl-5 text-center">
        <h1 class="display-5 display-lg-4 brand-heading mb-2 text-dark soft-glow-text font-corsiva" data-aos="fade-down" style="letter-spacing: -0.02em;">
            مجموعة <span class="text-gold">Hijab Princesses</span> الفاخرة
        </h1>
        <p class="text-muted small mb-4 font-body opacity-75 mx-auto" data-aos="fade-up" style="max-width: 600px;">تمتعي بتجربة تسوق استثنائية مع أرقى تصاميم أناقة الأميرة</p>

        
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

        <?php if(request('q') || request('sort')): ?>
            
            <div class="d-flex align-items-center gap-3 mb-5" data-aos="fade-left">
                <div class="bg-gold rounded" style="width: 4px; height: 35px;"></div>
                <div>
                    <h2 class="brand-heading mb-0 h3">نتائج البحث</h2>
                    <p class="text-muted small mb-0 font-body fw-bold">تم العثور على <span class="text-gold"><?php echo e($products->total()); ?></span> قطعة</p>
                </div>
            </div>

            <?php if($products->isEmpty()): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="brand-heading">لم نجد أي منتجات تطابق بحثك</h4>
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

        <?php elseif(request('category')): ?>
            
            <?php $currentCat = $allCategories->where('slug', request('category'))->first(); ?>
            <?php if($currentCat): ?>
                <div class="d-flex align-items-center gap-3 mb-5" data-aos="fade-left">
                    <div class="bg-gold rounded" style="width: 4px; height: 35px;"></div>
                    <div>
                        <h2 class="brand-heading mb-0 h3"><?php echo e($currentCat->translated_name); ?></h2>
                        <p class="text-muted small mb-0 font-body fw-bold">يتوفر <span class="text-gold"><?php echo e($currentCat->products->count()); ?></span> قطعة حصرية</p>
                    </div>
                </div>

                <?php if($currentCat->products->isEmpty()): ?>
                    <div class="text-center py-5 text-muted">لم يتم إضافة منتجات في هذا القسم بعد.</div>
                <?php else: ?>
                    <div class="row g-3 g-lg-4">
                        <?php $__currentLoopData = $currentCat->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                                <?php echo $__env->make('frontend.partials.product_card_v2', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php else: ?>
            
            <?php $__currentLoopData = $allCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($category->products->count() > 0): ?>
                    <div class="category-section-luxe" data-aos="fade-up" id="category-<?php echo e($category->slug); ?>">
                        <div class="section-premium-header">
                            <h2 class="section-premium-title"><?php echo e($category->translated_name); ?></h2>
                            <div class="section-premium-divider">
                                <div class="section-premium-line"></div>
                                <i class="fas fa-crown section-premium-icon"></i>
                                <div class="section-premium-line"></div>
                            </div>
                        </div>
                        
                        <div class="row g-3 g-lg-4">
                            <?php $__currentLoopData = $category->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-6 col-md-4 col-lg-3">
                                    <?php echo $__env->make('frontend.partials.product_card_v2', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/shop/index.blade.php ENDPATH**/ ?>