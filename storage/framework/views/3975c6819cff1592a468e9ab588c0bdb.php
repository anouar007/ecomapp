<?php
    $activeCategory = $categories->where('slug', request('category'))->first();
    $pageTitle = $activeCategory
        ? ($activeCategory->translated_name . ' — ' . setting('app_name', 'Hijab Princesses'))
        : (request('q') ? 'نتائج البحث عن "' . request('q') . '" — ' . setting('app_name') : 'المتجر — ' . setting('app_name', 'Hijab Princesses'));
?>

<?php $__env->startSection('meta_title', $pageTitle); ?>

<?php $__env->startSection('content'); ?>


<section class="bg-brand-overlay-light bg-silk border-bottom shadow-sm">
    <div class="container px-xl-5">
        <div class="category-circle-grid py-3" style="gap: 20px;">
            <a href="<?php echo e(route('shop.index')); ?>" 
               class="category-ajax-link category-circle-item text-decoration-none <?php echo e(!request('category') ? 'active' : ''); ?>" 
               data-category=""
               style="width: 70px;">
                <div class="category-circle-img-wrap" style="width: 60px; height: 60px; <?php echo e(!request('category') ? 'border-color: var(--brand-gold);' : ''); ?>">
                    <img src="https://img.icons8.com/ios/100/c5a059/infinity.png" alt="الكل" style="padding: 10px;">
                </div>
                <span class="category-circle-name small font-body">الكل</span>
            </a>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('shop.index', ['category' => $cat->slug])); ?>" 
               class="category-ajax-link category-circle-item text-decoration-none <?php echo e(request('category') == $cat->slug ? 'active' : ''); ?>" 
               data-category="<?php echo e($cat->slug); ?>"
               style="width: 70px;">
                <div class="category-circle-img-wrap" style="width: 60px; height: 60px; <?php echo e(request('category') == $cat->slug ? 'border-color: var(--brand-gold);' : ''); ?>">
                    <img src="<?php echo e($cat->image ? (Str::startsWith($cat->image, 'http') ? $cat->image : Storage::url($cat->image)) : asset('images/placeholder-cat.jpg')); ?>" alt="<?php echo e($cat->translated_name); ?>">
                </div>
                <span class="category-circle-name small font-body"><?php echo e($cat->translated_name); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="section-py bg-surface min-vh-100">
    <div class="container px-xl-5" id="shopMainContainer">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4" data-aos="fade-up">
            <div>
                <h2 class="brand-heading h4 m-0" id="categoryTitle">
                    <?php echo e($activeCategory ? $activeCategory->translated_name : 'جميع المنتجات'); ?>

                    <span class="text-muted small ms-2 fw-normal" id="productCount" style="font-family: var(--font-body);">(<?php echo e($products->total()); ?> قطعة)</span>
                </h2>
                <div class="bg-gold mt-2 rounded" style="width: 40px; height: 3px;"></div>
            </div>
            
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                <span class="small text-muted d-none d-md-inline">ترتيب:</span>
                <select class="form-select form-select-sm brand-card border-0 shadow-sm py-2 px-3 flex-grow-1" id="sortSelect" style="min-width: 160px;">
                    <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>✨ الأحدث تقديماً</option>
                    <option value="price_asc" <?php echo e(request('sort') == 'price_asc' ? 'selected' : ''); ?>>💰 السعر (من الأقل)</option>
                    <option value="price_desc" <?php echo e(request('sort') == 'price_desc' ? 'selected' : ''); ?>>💎 السعر (من الأعلى)</option>
                </select>
            </div>
        </div>

        <div id="productGridWrapper" class="transition-300">
            <?php echo $__env->make('frontend.shop.partials.product-grid', ['products' => $products], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentCategory = "<?php echo e(request('category', '')); ?>";
let currentSort = "<?php echo e(request('sort', 'newest')); ?>";
let currentSearch = "<?php echo e(request('q', '')); ?>";

document.addEventListener('DOMContentLoaded', () => {
    // Intercept Category Clicks
    document.querySelectorAll('.category-ajax-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            currentCategory = this.dataset.category;
            
            // Update UI State
            document.querySelectorAll('.category-ajax-link').forEach(l => {
                l.classList.remove('active');
                l.querySelector('.category-circle-img-wrap').style.borderColor = 'var(--brand-gold-light)';
            });
            this.classList.add('active');
            this.querySelector('.category-circle-img-wrap').style.borderColor = 'var(--brand-gold)';
            
            fetchProducts(true);
        });
    });

    // Handle Sorting
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentSort = this.value;
            fetchProducts(true);
        });
    }

    // Handle Pagination (Delegated)
    document.getElementById('productGridWrapper').addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();
            fetchProducts(false, link.href);
            window.scrollTo({ top: document.getElementById('shopMainContainer').offsetTop - 100, behavior: 'smooth' });
        }
    });
});

function fetchProducts(resetPage = true, customUrl = null) {
    const wrapper = document.getElementById('productGridWrapper');
    wrapper.style.opacity = '0.5';
    wrapper.style.pointerEvents = 'none';

    let url = customUrl || "<?php echo e(route('shop.index')); ?>";
    const params = new URLSearchParams();
    
    if (currentCategory) params.append('category', currentCategory);
    if (currentSort)     params.append('sort',     currentSort);
    if (currentSearch)   params.append('q',        currentSearch);
    
    // If not using a custom pagination URL, add params to the base URL
    if (!customUrl) {
        url = url + '?' + params.toString();
    } else {
        // If it's a pagination URL, ensure other filters are preserved
        let pUrl = new URL(customUrl);
        if (currentCategory) pUrl.searchParams.set('category', currentCategory);
        if (currentSort)     pUrl.searchParams.set('sort',     currentSort);
        if (currentSearch)   pUrl.searchParams.set('q',        currentSearch);
        url = pUrl.toString();
    }

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        wrapper.innerHTML = data.grid_html;
        document.getElementById('categoryTitle').innerHTML = `${data.category_name} <span class="text-muted small ms-2 fw-normal" id="productCount" style="font-family: var(--font-body);">(${data.total_count} قطعة)</span>`;
        
        // Update URL
        history.pushState(null, null, url);
        
        wrapper.style.opacity = '1';
        wrapper.style.pointerEvents = 'all';
        
        // Re-init AOS if needed
        if (window.AOS) AOS.refresh();
    })
    .catch(err => {
        console.error(err);
        wrapper.style.opacity = '1';
        wrapper.style.pointerEvents = 'all';
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/shop/index.blade.php ENDPATH**/ ?>