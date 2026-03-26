<?php $__env->startSection('dashboard_content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
    <div>
        <h3 class="brand-heading h2 mb-1">تفاصيل الطلب #<?php echo e($order->order_number); ?></h3>
        <div class="bg-gold rounded" style="width: 40px; height: 3px;"></div>
    </div>
    <a href="<?php echo e(route('customer.orders')); ?>" class="btn-brand-outline py-1 px-3 small text-decoration-none hvr-backward">
        <i class="fas fa-arrow-right me-1 small"></i> العودة لطلباتي
    </a>
</div>

<div class="row g-4 font-body">
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="brand-card border-0 shadow-sm overflow-hidden mb-4 bg-white">
            <div class="card-header bg-gold-light p-4 border-bottom border-gold-subtle">
                <h5 class="fw-bold m-0 small text-uppercase ls-1">المنتجات المطلوبة</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-bottom border-light">
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-gold-light rounded shadow-sm d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 75px;">
                                        <?php if($item->product && $item->product->main_image): ?>
                                            <img src="<?php echo e(Storage::url($item->product->main_image)); ?>" class="rounded h-100 w-100 object-fit-cover">
                                        <?php else: ?>
                                            <i class="fas fa-crown text-gold opacity-50"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark"><?php echo e($item->product_name); ?></h6>
                                        <div class="small text-muted">
                                            <span>الكمية: <?php echo e($item->quantity); ?></span>
                                            <?php if($item->variant_id): ?>
                                                <?php $v = \App\Models\ProductVariant::find($item->variant_id); ?>
                                                <?php if($v): ?>
                                                    <span class="ms-2">| <?php echo e($v->color); ?> - <?php echo e($v->size); ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end pe-4 fw-bold text-dark">
                                <?php echo e(currency($item->subtotal)); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="bg-gold-light border-top border-gold-subtle">
                        <tr>
                            <td class="text-end pe-4 py-3 border-0 small text-muted">المجموع الفرعي</td>
                            <td class="text-end pe-4 py-3 border-0 fw-bold text-dark"><?php echo e(currency($order->subtotal)); ?></td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4 py-2 border-0 small text-muted">التوصيل</td>
                            <td class="text-end pe-4 py-2 border-0 text-gold fw-bold"><?php echo e(currency($order->shipping_cost)); ?></td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4 py-3 border-0 h5 fw-bold text-dark">الإجمالي النهائي</td>
                            <td class="text-end pe-4 py-3 border-0 h4 fw-bold text-gold"><?php echo e($order->formatted_total); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="brand-card border-0 shadow-sm mb-4 bg-white p-4">
            <h6 class="fw-bold text-muted mb-4 small text-uppercase ls-2">عنوان التوصيل</h6>
            <div class="d-flex gap-3">
                <i class="fas fa-map-marker-alt text-gold mt-1"></i>
                <div>
                    <p class="mb-1 fw-bold text-dark"><?php echo e($order->shipping_address); ?></p>
                    <p class="mb-0 text-muted"><?php echo e($order->shipping_city); ?></p>
                    <p class="mb-0 text-muted"><?php echo e($order->shipping_state); ?> <?php echo e($order->shipping_zip); ?></p>
                </div>
            </div>
        </div>

        <div class="brand-card border-0 shadow-sm bg-white p-4">
            <h6 class="fw-bold text-muted mb-4 small text-uppercase ls-2">معلومات الطلب</h6>
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <span class="text-muted small">حالة الطلب</span>
                <?php
                    $statusMap = [
                        'pending' => ['bg' => 'bg-warning-subtle text-warning', 'text' => 'قيد الانتظار'],
                        'confirmed' => ['bg' => 'bg-info-subtle text-info', 'text' => 'تم التأكيد'],
                        'shipping' => ['bg' => 'bg-primary-subtle text-primary', 'text' => 'جاري الشحن'],
                        'delivered' => ['bg' => 'bg-success-subtle text-success', 'text' => 'تم التوصيل'],
                        'cancelled' => ['bg' => 'bg-danger-subtle text-danger', 'text' => 'ملغى'],
                    ];
                    $s = $statusMap[$order->status] ?? ['bg' => 'bg-secondary-subtle text-secondary', 'text' => $order->status];
                ?>
                <span class="badge <?php echo e($s['bg']); ?> rounded-pill px-3 py-1 fw-normal" style="font-size: 0.7rem;"><?php echo e($s['text']); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <span class="text-muted small">طريقة الدفع</span>
                <span class="badge bg-gold-light text-dark rounded-pill px-3 py-1 fw-normal text-uppercase" style="font-size: 0.7rem;">عند الاستلام</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">تاريخ الطلب</span>
                <span class="fw-bold text-dark small"><?php echo e($order->created_at->format('d M Y')); ?></span>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/dashboard/order-show.blade.php ENDPATH**/ ?>