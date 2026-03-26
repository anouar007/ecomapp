<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

  
  <url>
    <loc><?php echo e(url('/')); ?></loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
    <lastmod><?php echo e(now()->toAtomString()); ?></lastmod>
  </url>

  
  <url>
    <loc><?php echo e(route('shop.index')); ?></loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
    <lastmod><?php echo e(now()->toAtomString()); ?></lastmod>
  </url>

  
  <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php if($category->slug): ?>
  <url>
    <loc><?php echo e(route('shop.index', ['category' => $category->slug])); ?></loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <lastmod><?php echo e($category->updated_at->toAtomString()); ?></lastmod>
  </url>
  <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  
  <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <url>
    <loc><?php echo e(route('shop.show', $product->id)); ?></loc>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
    <lastmod><?php echo e($product->updated_at->toAtomString()); ?></lastmod>
  </url>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</urlset>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/sitemap.blade.php ENDPATH**/ ?>