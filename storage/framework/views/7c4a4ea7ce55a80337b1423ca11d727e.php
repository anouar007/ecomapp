<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['block']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['block']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="page-block-wrapper <?php echo e($block['class'] ?? ''); ?>" 
     <?php if(!empty($block['id'])): ?> id="<?php echo e($block['id']); ?>" <?php endif; ?>
     style="
        background-color: <?php echo e($block['bg_color'] ?? 'transparent'); ?>; 
        color: <?php echo e($block['text_color'] ?? 'inherit'); ?>;
        <?php if(!empty($block['width'])): ?> width: <?php echo e($block['width']); ?>; <?php endif; ?>
        <?php if(!empty($block['height'])): ?> height: <?php echo e($block['height']); ?>; <?php endif; ?>
        <?php if(!empty($block['position'])): ?> position: <?php echo e($block['position']); ?>; <?php endif; ?>
        <?php if(!empty($block['top'])): ?> top: <?php echo e($block['top']); ?>; <?php endif; ?>
        <?php if(!empty($block['bottom'])): ?> bottom: <?php echo e($block['bottom']); ?>; <?php endif; ?>
        <?php if(!empty($block['left'])): ?> left: <?php echo e($block['left']); ?>; <?php endif; ?>
        <?php if(!empty($block['right'])): ?> right: <?php echo e($block['right']); ?>; <?php endif; ?>
        <?php if(!empty($block['z_index'])): ?> z-index: <?php echo e($block['z_index']); ?>; <?php endif; ?>
        
        
        <?php if(($block['type'] ?? '') === 'group'): ?>
            display: flex;
            flex-direction: <?php echo e($block['flex_dir'] ?? 'row'); ?>;
            justify-content: <?php echo e($block['justify_content'] ?? 'flex-start'); ?>;
            align-items: <?php echo e($block['align_items'] ?? 'stretch'); ?>;
            gap: <?php echo e(($block['gap'] ?? '0') . 'px'); ?>;
        <?php endif; ?>

        <?php if(!empty($block['custom_css'])): ?> <?php echo e($block['custom_css']); ?> <?php endif; ?>
     "
     <?php if(!empty($block['animation']) && $block['animation'] !== 'none'): ?> data-aos="<?php echo e($block['animation']); ?>" <?php endif; ?>>

    <?php switch($block['type']):
        case ('group'): ?>
            
            <?php if(!empty($block['children']) && is_array($block['children'])): ?>
                <?php $__currentLoopData = $block['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('partials.page-builder-block', ['block' => $child], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <?php break; ?>

        <?php case ('hero'): ?>
            <div class="hero-section text-center w-100" style="<?php if(!empty($block['bg_image'])): ?> background-image: url('<?php echo e($block['bg_image']); ?>'); background-size: cover; background-position: center; <?php endif; ?>">
                <div class="container py-5">
                    <h1 class="display-3 fw-bold mb-4"><?php echo e($block['body'] ?? 'Hero Title'); ?></h1>
                    <?php if(!empty($block['subtitle'])): ?><p class="lead mb-5 opacity-75"><?php echo e($block['subtitle']); ?></p><?php endif; ?>
                    <div class="d-flex justify-content-center gap-3">
                         <?php if(!empty($block['cta_text'])): ?>
                            <a href="<?php echo e($block['cta_link'] ?? '#'); ?>" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold"><?php echo e($block['cta_text']); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php break; ?>

        <?php case ('image'): ?>
            <div class="text-center w-100">
                <?php if(!empty($block['image_url'])): ?>
                    <img src="<?php echo e($block['image_url']); ?>" class="img-fluid rounded" alt="<?php echo e($block['body'] ?? 'Image'); ?>" style="max-width: 100%;">
                <?php endif; ?>
                <?php if(!empty($block['body'])): ?>
                    <p class="text-muted small mt-2"><?php echo e($block['body']); ?></p>
                <?php endif; ?>
            </div>
            <?php break; ?>

        <?php case ('heading'): ?>
            <div class="w-100">
                 <<?php echo e($block['level'] ?? 'h2'); ?> class="<?php echo e($block['align'] ?? 'text-start'); ?>" style="font-weight: bold;">
                    <?php echo e($block['body'] ?? 'Heading Text'); ?>

                </<?php echo e($block['level'] ?? 'h2'); ?>>
            </div>
            <?php break; ?>
            
        <?php case ('content'): ?>
            <div class="container">
                <div class="lead" style="line-height: 2;">
                    <?php echo nl2br(e($block['body'])); ?>

                </div>
            </div>
            <?php break; ?>

        <?php case ('features'): ?>
            <div class="container">
                <div class="row g-4 justify-content-center">
                    <?php $features = explode("\n", $block['body'] ?? "Feature 1\nFeature 2"); ?>
                    <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4">
                        <div class="feature-box text-center">
                            <div class="brand-avatar bg-primary text-white mx-auto mb-4" style="width: 70px; height: 70px; font-size: 1.5rem; border-radius: 20px;">
                                <i class="fas fa-check"></i>
                            </div>
                            <h4 class="fw-bold mb-3"><?php echo e(trim($feature)); ?></h4>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php break; ?>

        <?php case ('cta'): ?>
            <div class="py-5 w-100">
                 <div class="container text-center">
                    <div class="p-5 rounded-5 shadow-lg bg-primary text-white" style="<?php if(!empty($block['bg_image'])): ?> background-image: url('<?php echo e($block['bg_image']); ?>'); background-size: cover; <?php endif; ?>; background: var(--gradient-primary, #475927);">
                        <h2 class="display-5 fw-bold mb-4"><?php echo e($block['body'] ?? 'Ready to Grow?'); ?></h2>
                        <?php if(!empty($block['cta_text'])): ?>
                            <a href="<?php echo e($block['cta_link'] ?? '#'); ?>" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold shadow"><?php echo e($block['cta_text']); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php break; ?>

        <?php case ('faq'): ?>
            <div class="content-section">
                <div class="container">
                    <div class="col-lg-7 mx-auto">
                        <h2 class="text-center fw-bold mb-5">Frequently Asked Questions</h2>
                        <div class="accordion" id="faqAccordion-<?php echo e($block['id'] ?? uniqid()); ?>">
                            <?php $faqs = explode("\n", $block['body'] ?? "Q: How to start?\nA: Just sign up!\nQ: Is it free?\nA: We have a free trial."); ?>
                            <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faqLine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(str_contains($faqLine, ':')): ?>
                                    <?php [$q, $a] = explode(':', $faqLine, 2); ?>
                                    <div class="accordion-item mb-3 border-0 rounded-4 overflow-hidden shadow-sm">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold py-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo e($block['id'] ?? ''); ?>-<?php echo e($i); ?>">
                                                <?php echo e(trim($q)); ?>?
                                            </button>
                                        </h2>
                                        <div id="collapse-<?php echo e($block['id'] ?? ''); ?>-<?php echo e($i); ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion-<?php echo e($block['id'] ?? uniqid()); ?>">
                                            <div class="accordion-body bg-white opacity-75 py-3 px-4">
                                                <?php echo e(trim($a)); ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php break; ?>

        <?php case ('testimonials'): ?>
            <div class="content-section shadow-sm" style="background: rgba(0,0,0,0.02)">
                <div class="container">
                    <h2 class="text-center fw-bold mb-5">What Our Clients Say</h2>
                    <div class="row g-4">
                        <?php $testimonials = explode("\n", $block['body'] ?? "John Doe: Speed is amazing!\nJane Smith: Handled all our orders perfectly."); ?>
                        <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(str_contains($testi, ':')): ?>
                                <?php [$name, $text] = explode(':', $testi, 2); ?>
                                <div class="col-md-6">
                                    <div class="p-4 bg-white rounded-4 border">
                                        <div class="text-warning mb-3">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                        </div>
                                        <p class="fst-italic opacity-75 mb-4">"<?php echo e(trim($text)); ?>"</p>
                                        <div class="d-flex align-items-center">
                                            <div class="brand-avatar me-3" style="width: 40px; height: 40px; border-radius: 50%;"><?php echo e(substr($name, 0, 1)); ?></div>
                                            <div class="fw-bold"><?php echo e(trim($name)); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php break; ?>

        <?php case ('contact'): ?>
            <div class="content-section">
                <div class="container">
                    <div class="row g-5 align-items-center">
                        <div class="col-lg-5">
                            <h2 class="fw-bold mb-4">Get in Touch</h2>
                            <p class="opacity-75 mb-5"><?php echo e($block['body'] ?: 'Have questions or need support? Our team is available 24/7 to help you succeed.'); ?></p>
                        </div>
                        <div class="col-lg-7">
                            <div class="p-5 bg-white rounded-5 shadow-sm border">
                                <form> <!-- Placeholder form -->
                                    <div class="row g-4">
                                        <div class="col-md-6"><input type="text" class="form-control bg-light border-0 py-3 px-4" placeholder="Full Name"></div>
                                        <div class="col-md-6"><input type="email" class="form-control bg-light border-0 py-3 px-4" placeholder="Email Address"></div>
                                        <div class="col-12"><textarea class="form-control bg-light border-0 py-3 px-4" rows="5" placeholder="Message"></textarea></div>
                                        <div class="col-12"><button class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold">Send Message</button></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php break; ?>

         <?php case ('product-grid'): ?>
            <div class="container py-5">
                <h2 class="text-center fw-bold mb-5"><?php echo e($block['body'] ?: 'Latest Products'); ?></h2>
                <div class="row g-4">
                    <?php
                        $latestProducts = \App\Models\Product::where('status', 'active')->latest()->take(4)->get();
                    ?>
                    <?php $__currentLoopData = $latestProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm rounded-4 product-card overflow-hidden">
                            <a href="<?php echo e(route('shop.show', $prod->id)); ?>" class="position-relative d-block bg-white p-3 text-center">
                                <?php if($prod->main_image): ?>
                                <img src="<?php echo e(Storage::url($prod->main_image)); ?>" class="img-fluid" alt="<?php echo e($prod->name); ?>" style="height: 180px; object-fit: contain;">
                                <?php else: ?>
                                <i class="fas fa-image fa-3x text-muted opacity-25 my-4"></i>
                                <?php endif; ?>
                            </a>
                            <div class="card-body p-3 text-center">
                                <h6 class="fw-bold mb-1"><a href="<?php echo e(route('shop.show', $prod->id)); ?>" class="text-decoration-none text-dark"><?php echo e($prod->name); ?></a></h6>
                                <p class="text-primary fw-bold mb-0"><?php echo e($prod->formatted_price); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php break; ?>

        <?php case ('category-grid'): ?>
            <div class="content-section bg-light">
                <div class="container">
                    <h2 class="text-center fw-bold mb-5"><?php echo e($block['body'] ?: 'Shop by Category'); ?></h2>
                    <div class="row g-4 justify-content-center">
                        <?php
                            $cats = \App\Models\Category::withCount('products')->take(4)->get();
                        ?>
                        <?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3">
                            <a href="<?php echo e(route('shop.index', ['category' => $cat->slug])); ?>" class="card h-100 border-0 shadow-sm rounded-4 text-decoration-none text-dark hover-lift">
                                <div class="card-body p-4 text-center">
                                    <div class="brand-avatar bg-primary text-white mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem; border-radius: 50%;"><i class="fas fa-tag"></i></div>
                                    <h5 class="fw-bold mb-1"><?php echo e($cat->name); ?></h5>
                                    <p class="text-muted small mb-0"><?php echo e($cat->products_count); ?> Products</p>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php break; ?>
        
        <?php case ('list'): ?>
            <div class="container py-3">
                <?php $items = explode("\n", $block['body'] ?? ''); ?>
                <?php if(($block['style'] ?? 'ul') === 'ol'): ?>
                    <ol class="list-group list-group-numbered">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if(trim($item)): ?> <li class="list-group-item"><?php echo e(trim($item)); ?></li> <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ol>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if(trim($item)): ?> <li class="list-group-item"><i class="fas fa-check text-primary me-2"></i> <?php echo e(trim($item)); ?></li> <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php break; ?>

        <?php case ('table'): ?>
            <div class="container py-4"><div class="table-responsive"><table class="table table-hover table-bordered"><tbody><tr><td>Table Content Placeholder</td></tr></tbody></table></div></div>
            <?php break; ?>
        
        <?php case ('code'): ?>
            <div class="container py-3"><?php echo $block['body'] ?? ''; ?></div>
            <?php break; ?>

        <?php default: ?>
            <div class="p-4 border border-dashed text-muted text-center">
                Unknown Block Type: <?php echo e($block['type']); ?>

            </div>
    <?php endswitch; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/partials/page-builder-block.blade.php ENDPATH**/ ?>